"""
Gemini AI Client Service
Handles communication with Google Gemini API for job recommendations.
"""

import json
import logging
import time
from typing import Any

from google import genai

logger = logging.getLogger(__name__)


class GeminiClient:
    """
    Client for interacting with Google Gemini API.
    Provides methods for CV analysis and job recommendations.
    Includes retry logic with exponential backoff for rate limiting.
    """

    def __init__(self, api_key: str, model: str = "gemini-2.5-flash"):
        """
        Initialize the Gemini client.

        Args:
            api_key: Google Gemini API key
            model: Gemini model name to use
        """
        if not api_key:
            raise ValueError("GEMINI_API_KEY is required")
        self.client = genai.Client(api_key=api_key)
        self.model = model
        self._last_request_time = 0.0
        self._min_delay = 2.0  # Minimum 2 detik antar request ke Gemini

    def _rate_limit(self):
        """
        Ensure minimum delay between requests to avoid 503 rate limiting.
        """
        elapsed = time.time() - self._last_request_time
        if elapsed < self._min_delay:
            wait = self._min_delay - elapsed
            logger.info(f"Rate limiting: waiting {wait:.1f}s before next Gemini request")
            time.sleep(wait)
        self._last_request_time = time.time()

    def _call_with_retry(self, prompt: str, max_retries: int = 3) -> dict[str, Any]:
        """
        Call Gemini API with retry logic and exponential backoff.

        Args:
            prompt: The prompt to send to Gemini
            max_retries: Maximum number of retry attempts

        Returns:
            Parsed JSON response

        Raises:
            Exception: If all retries fail
        """
        last_error = None

        for attempt in range(1, max_retries + 1):
            try:
                # Rate limit before each attempt
                self._rate_limit()

                response = self.client.models.generate_content(
                    model=self.model,
                    contents=prompt
                )

                cleaned_text = response.text.replace("```json", "").replace("```", "").strip()
                result = json.loads(cleaned_text)

                logger.info(f"Gemini API call succeeded (attempt {attempt})")
                return result

            except json.JSONDecodeError as e:
                logger.error(f"Failed to parse Gemini response as JSON: {e}")
                logger.debug(f"Raw response: {response.text}")
                raise ValueError(f"Invalid JSON from Gemini API: {e}") from e

            except Exception as e:
                error_str = str(e)
                last_error = e

                # Check if it's a rate limit (503) or quota error
                is_rate_limit = "503" in error_str or "UNAVAILABLE" in error_str or "429" in error_str or "RESOURCE_EXHAUSTED" in error_str

                if is_rate_limit and attempt < max_retries:
                    # Exponential backoff: 5s, 10s, 20s
                    wait_time = 5 * (2 ** (attempt - 1))
                    logger.warning(
                        f"Gemini rate limited (attempt {attempt}/{max_retries}). "
                        f"Waiting {wait_time}s before retry..."
                    )
                    time.sleep(wait_time)
                elif attempt < max_retries:
                    # Non-rate-limit error, shorter backoff
                    wait_time = 2 * attempt
                    logger.warning(
                        f"Gemini API error (attempt {attempt}/{max_retries}): {error_str[:100]}. "
                        f"Retrying in {wait_time}s..."
                    )
                    time.sleep(wait_time)
                else:
                    # Last attempt failed
                    logger.error(f"Gemini API call failed after {max_retries} attempts: {error_str}")
                    raise

        # Should not reach here, but just in case
        raise last_error or Exception("Gemini API call failed")

    def generate_job_recommendation(self, cv_text: str) -> dict[str, Any]:
        """
        Generate job recommendations from CV text using Gemini.

        Args:
            cv_text: Full text extracted from CV

        Returns:
            Parsed JSON response with job recommendations

        Raises:
            ValueError: If API response cannot be parsed as JSON
            Exception: If API call fails after retries
        """
        prompt = self._build_recommendation_prompt(cv_text)
        return self._call_with_retry(prompt)

    def analyze_skill_gap(
        self,
        cv_text: str,
        required_skills: list[str],
        job_title: str
    ) -> dict[str, Any]:
        """
        Analyze skill gap between CV and job requirements using Gemini.

        Args:
            cv_text: Full text extracted from CV
            required_skills: List of skills required for the job
            job_title: Title of the job position

        Returns:
            Parsed JSON with skill gap analysis
        """
        skills_str = ", ".join(required_skills)
        prompt = f"""
You are an experienced HR Recruitment Specialist.

Analyze the following CV against the required skills for "{job_title}".

Required Skills: {skills_str}

Tasks:
1. Identify which required skills are PRESENT in the CV
2. Identify which required skills are MISSING from the CV
3. Rate the candidate's overall fit (0-100)
4. Provide a brief recommendation (HIRE / CONSIDER / REJECT)

Return ONLY valid JSON with this structure:
{{
    "skills_present": ["skill1", "skill2"],
    "skills_missing": ["skill3"],
    "fit_score": 75,
    "recommendation": "CONSIDER",
    "reasoning": "Brief explanation"
}}

CV:
{cv_text}
"""
        try:
            response = self.client.models.generate_content(
                model=self.model,
                contents=prompt
            )

            cleaned_text = response.text.replace("```json", "").replace("```", "").strip()
            result = json.loads(cleaned_text)

            logger.info(f"Skill gap analysis generated for {job_title}")
            return result

        except Exception as e:
            logger.error(f"Skill gap analysis failed: {e}")
            return {
                "skills_present": [],
                "skills_missing": required_skills,
                "fit_score": 0,
                "recommendation": "ERROR",
                "reasoning": f"Analysis failed: {str(e)}"
            }

    def _build_recommendation_prompt(self, cv_text: str) -> str:
        """
        Build the prompt for job recommendation generation.

        Args:
            cv_text: CV text to analyze

        Returns:
            Formatted prompt string
        """
        return f"""
You are an experienced HR Recruitment Specialist.

Analyze the following CV.

Tasks:
1. Recommend TOP 5 jobs that best match this candidate
2. Explain why each job is a good fit
3. Mention supporting skills for each recommendation
4. Provide confidence score (0-100) for each recommendation

Return ONLY valid JSON with this structure:
{{
    "recommendations": [
        {{
            "job_title": "Data Scientist",
            "confidence": 85,
            "reasoning": "Strong background in machine learning and Python",
            "supporting_skills": ["Python", "Machine Learning", "Statistics"]
        }}
    ]
}}

CV:
{cv_text}
"""