"""
CVision AI Engine — FastAPI Server
Provides REST API endpoints for CV analysis, similarity scoring, and Gemini recommendations.

Run with:
    uvicorn main:app --reload --port 8000
"""

import os
import sys
import logging
from pathlib import Path
from contextlib import asynccontextmanager

# Fix for Windows console encoding (cp1252) - enable UTF-8 output
if sys.platform == 'win32':
    sys.stdout.reconfigure(encoding='utf-8')
    sys.stderr.reconfigure(encoding='utf-8')
    
    # Also fix logging encoding
    for handler in logging.root.handlers:
        if hasattr(handler, 'stream'):
            handler.stream.reconfigure(encoding='utf-8')

from fastapi import FastAPI, UploadFile, File, Form, HTTPException
from fastapi.middleware.cors import CORSMiddleware

from dotenv import load_dotenv

from services.pdf_extractor import extract_pdf_from_bytes
from services.text_processor import (
    extract_years_of_experience,
    extract_education_level,
)
from services.similarity import SimilarityService
from services.gemini_client import GeminiClient
from services.resume_generator import ResumeGenerator
from models.schemas import AnalyzeCVResponse, HealthResponse, ResumeResponse, ResumeDownloadResponse

# ---------------------------------------------------------------------------
# Configuration
# ---------------------------------------------------------------------------

ROOT_DIR = Path(__file__).resolve().parent.parent
load_dotenv(ROOT_DIR / ".env")

GEMINI_API_KEY = os.getenv("GEMINI_API_KEY")
if not GEMINI_API_KEY:
    raise ValueError("GEMINI_API_KEY not found in .env")

# ---------------------------------------------------------------------------
# Global service instances (loaded once at startup)
# ---------------------------------------------------------------------------

similarity_service: SimilarityService | None = None
gemini_client: GeminiClient | None = None
resume_generator: ResumeGenerator | None = None

# Configure logging with UTF-8 encoding for Windows
if sys.platform == 'win32':
    # Remove default handlers
    logging.root.handlers = []
    
    # Create UTF-8 console handler
    console_handler = logging.StreamHandler(sys.stdout)
    console_handler.setLevel(logging.INFO)
    console_handler.setFormatter(logging.Formatter(
        '%(asctime)s - %(name)s - %(levelname)s - %(message)s'
    ))
    
    # Configure root logger
    logging.root.setLevel(logging.INFO)
    logging.root.addHandler(console_handler)

logger = logging.getLogger("cvision.ai")


@asynccontextmanager
async def lifespan(app: FastAPI):
    """Startup and shutdown events."""
    global similarity_service, gemini_client, resume_generator

    # Startup: load models
    logger.info("Loading SBERT model...")
    similarity_service = SimilarityService()
    logger.info("SBERT model loaded successfully.")

    logger.info("Initializing Gemini client...")
    gemini_client = GeminiClient(api_key=GEMINI_API_KEY)
    logger.info("Gemini client initialized.")

    logger.info("Initializing Resume Generator...")
    resume_generator = ResumeGenerator(gemini_client=gemini_client)
    logger.info("Resume Generator initialized.")

    yield

    # Shutdown: cleanup if needed
    logger.info("AI Engine shutting down.")


# ---------------------------------------------------------------------------
# FastAPI app
# ---------------------------------------------------------------------------

app = FastAPI(
    title="CVision AI Engine",
    description="CV matching and analysis engine using TF-IDF, SBERT, and Gemini AI",
    version="2.0.0",
    lifespan=lifespan,
)

# CORS — allow Laravel dev server
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # In production, restrict to your Laravel domain
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)


# ---------------------------------------------------------------------------
# Endpoints
# ---------------------------------------------------------------------------

@app.get("/health", response_model=HealthResponse)
async def health_check():
    """Health check endpoint to verify the service is running."""
    return HealthResponse(
        status="ok",
        model_loaded=similarity_service.is_loaded if similarity_service else False,
    )


@app.post("/api/cv/analyze", response_model=AnalyzeCVResponse)
async def analyze_cv(
    cv_file: UploadFile = File(...),
    job_description: str = Form(...),
    required_skills: str = Form(default=""),
    job_title: str = Form(default="Unknown Position"),
    min_experience: float = Form(default=0.0),
    required_education: str = Form(default=""),
):
    """
    Analyze a CV against a job description.

    - Extracts text from the uploaded PDF
    - Computes TF-IDF, SBERT, and hybrid similarity scores
    - Generates AI-powered job recommendations via Gemini
    - Performs skill gap analysis
    - Extracts experience years and education level
    """
    # Validate inputs
    if not cv_file.filename or not cv_file.filename.lower().endswith(".pdf"):
        raise HTTPException(status_code=400, detail="Only PDF files are supported")

    if not job_description.strip():
        raise HTTPException(status_code=400, detail="Job description is required")

    # Read uploaded file
    pdf_bytes = await cv_file.read()
    if not pdf_bytes:
        raise HTTPException(status_code=400, detail="Uploaded file is empty")

    # Extract text from PDF
    try:
        cv_text = extract_pdf_from_bytes(pdf_bytes)
    except Exception as e:
        logger.error(f"PDF extraction failed: {e}")
        raise HTTPException(status_code=422, detail=f"Failed to extract text from PDF: {str(e)}")

    if not cv_text.strip():
        raise HTTPException(status_code=422, detail="No text could be extracted from the PDF")

    # Clean UTF-8 encoding to prevent json_encode errors
    cv_text = cv_text.encode('utf-8', 'replace').decode('utf-8')

    # Compute similarity scores
    try:
        tfidf_score = similarity_service.calculate_tfidf(cv_text, job_description)
        sbert_score = similarity_service.calculate_sbert(cv_text, job_description)
        hybrid_score = similarity_service.calculate_hybrid_score(tfidf_score, sbert_score)
        match_percentage = similarity_service.calculate_match_percentage(hybrid_score)
    except Exception as e:
        logger.error(f"Similarity calculation failed: {e}")
        raise HTTPException(status_code=500, detail=f"Similarity calculation failed: {str(e)}")

    # Generate AI recommendations
    try:
        recommendation = gemini_client.generate_job_recommendation(cv_text)
    except Exception as e:
        logger.warning(f"Gemini recommendation failed (non-critical): {e}")
        recommendation = {"recommendations": []}

    # Clean recommendation JSON to ensure valid UTF-8
    recommendation = _clean_utf8(recommendation)

    # Skill gap analysis (if skills provided)
    skill_gap = None
    if required_skills.strip():
        skills_list = [s.strip() for s in required_skills.split(",") if s.strip()]
        if skills_list:
            try:
                skill_gap = gemini_client.analyze_skill_gap(cv_text, skills_list, job_title)
                skill_gap = _clean_utf8(skill_gap)
            except Exception as e:
                logger.warning(f"Skill gap analysis failed (non-critical): {e}")

    # Extract metadata
    experience_years = extract_years_of_experience(cv_text)
    education_level = extract_education_level(cv_text)

    return AnalyzeCVResponse(
        tfidf_score=round(tfidf_score, 4),
        sbert_score=round(sbert_score, 4),
        hybrid_score=round(hybrid_score, 4),
        match_percentage=match_percentage,
        recommendation=recommendation,
        skill_gap=skill_gap,
        experience_years=experience_years,
        education_level=education_level,
        min_experience=min_experience,
        required_education=required_education,
    )


@app.post("/api/cv/generate-resume", response_model=ResumeResponse)
async def generate_resume(
    cv_text: str = Form(...),
):
    """
    Generate a structured resume from raw CV text.
    Uses Gemini AI to extract and organize information.
    Falls back to rule-based extraction if Gemini is unavailable.
    """
    if not cv_text.strip():
        raise HTTPException(status_code=400, detail="CV text is required")

    try:
        # Clean UTF-8
        cv_text = cv_text.encode('utf-8', 'replace').decode('utf-8')
        
        # Generate structured resume
        structured_resume = resume_generator.generate_structured_resume(cv_text)
        
        return ResumeResponse(
            success=True,
            data=_clean_utf8(structured_resume),
            error=None,
        )
    except Exception as e:
        logger.error(f"Resume generation failed: {e}")
        return ResumeResponse(
            success=False,
            data=None,
            error=str(e),
        )


@app.post("/api/cv/generate-resume-text", response_model=ResumeDownloadResponse)
async def generate_resume_text(
    cv_text: str = Form(...),
):
    """
    Generate a formatted resume text for download.
    Suitable for displaying in the browser or downloading as .txt file.
    """
    if not cv_text.strip():
        raise HTTPException(status_code=400, detail="CV text is required")

    try:
        # Clean UTF-8
        cv_text = cv_text.encode('utf-8', 'replace').decode('utf-8')
        
        # Generate structured resume
        structured_resume = resume_generator.generate_structured_resume(cv_text)
        
        # Generate formatted text version
        resume_text = resume_generator.generate_resume_text(structured_resume)
        
        # Get filename
        name = structured_resume.get('name', 'Resume').replace(' ', '_')
        filename = f"{name}_Resume.txt"
        
        return ResumeDownloadResponse(
            success=True,
            filename=filename,
            content=resume_text,
            error=None,
        )
    except Exception as e:
        logger.error(f"Resume text generation failed: {e}")
        return ResumeDownloadResponse(
            success=False,
            filename=None,
            content=None,
            error=str(e),
        )


def _clean_utf8(data):
    """
    Recursively clean data to ensure valid UTF-8 encoding.
    Prevents json_encode errors from malformed UTF-8 characters.
    """
    if isinstance(data, dict):
        return {key: _clean_utf8(value) for key, value in data.items()}
    elif isinstance(data, list):
        return [_clean_utf8(item) for item in data]
    elif isinstance(data, str):
        # Remove invalid UTF-8 characters
        cleaned = data.encode('utf-8', 'replace').decode('utf-8')
        # Remove all problematic Unicode characters:
        # - Private Use Area (U+E000-U+F8FF)
        # - Supplementary Private Use Area (U+F0000-U+10FFFF)
        # - Surrogate characters (U+D800-U+DFFF)
        # - Variation Selectors (U+FE00-U+FE0F)
        # - Combining characters that might cause issues
        cleaned = ''.join(
            char for char in cleaned 
            if (ord(char) < 0xE000 or ord(char) > 0xF8FF) and  # Private use area
               (ord(char) < 0xD800 or ord(char) > 0xDFFF) and  # Surrogates
               (ord(char) < 0xFE00 or ord(char) > 0xFE0F)      # Variation selectors
        )
        return cleaned
    return data


@app.post("/api/cv/analyze-text", response_model=AnalyzeCVResponse)
async def analyze_cv_text(
    cv_text: str = Form(...),
    job_description: str = Form(...),
    required_skills: str = Form(default=""),
    job_title: str = Form(default="Unknown Position"),
    min_experience: float = Form(default=0.0),
    required_education: str = Form(default=""),
):
    """
    Analyze CV text directly (without PDF upload).
    Useful for testing or when text is already extracted.
    """
    if not cv_text.strip():
        raise HTTPException(status_code=400, detail="CV text is required")

    if not job_description.strip():
        raise HTTPException(status_code=400, detail="Job description is required")

    # Compute similarity scores
    try:
        tfidf_score = similarity_service.calculate_tfidf(cv_text, job_description)
        sbert_score = similarity_service.calculate_sbert(cv_text, job_description)
        hybrid_score = similarity_service.calculate_hybrid_score(tfidf_score, sbert_score)
        match_percentage = similarity_service.calculate_match_percentage(hybrid_score)
    except Exception as e:
        logger.error(f"Similarity calculation failed: {e}")
        raise HTTPException(status_code=500, detail=f"Similarity calculation failed: {str(e)}")

    # Generate AI recommendations
    try:
        recommendation = gemini_client.generate_job_recommendation(cv_text)
    except Exception as e:
        logger.warning(f"Gemini recommendation failed (non-critical): {e}")
        recommendation = {"recommendations": []}

    # Skill gap analysis
    skill_gap = None
    if required_skills.strip():
        skills_list = [s.strip() for s in required_skills.split(",") if s.strip()]
        if skills_list:
            try:
                skill_gap = gemini_client.analyze_skill_gap(cv_text, skills_list, job_title)
            except Exception as e:
                logger.warning(f"Skill gap analysis failed (non-critical): {e}")

    # Extract metadata
    experience_years = extract_years_of_experience(cv_text)
    education_level = extract_education_level(cv_text)

    return AnalyzeCVResponse(
        tfidf_score=round(tfidf_score, 4),
        sbert_score=round(sbert_score, 4),
        hybrid_score=round(hybrid_score, 4),
        match_percentage=match_percentage,
        recommendation=recommendation,
        skill_gap=skill_gap,
        experience_years=experience_years,
        education_level=education_level,
        min_experience=min_experience,
        required_education=required_education,
    )


# ---------------------------------------------------------------------------
# Run directly
# ---------------------------------------------------------------------------

if __name__ == "__main__":
    import uvicorn
    uvicorn.run("main:app", host="0.0.0.0", port=8000, reload=True)