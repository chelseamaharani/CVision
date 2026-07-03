"""
Resume Generator Service
Uses Gemini AI to structure raw CV text into a clean, organized resume format.
Also generates a PDF version for download.
"""

import json
import logging
import re
from datetime import datetime
from typing import Any

logger = logging.getLogger(__name__)


class ResumeGenerator:
    """
    Generates structured resumes from raw CV text using Gemini AI.
    Falls back to rule-based extraction if Gemini is unavailable.
    """

    def __init__(self, gemini_client=None):
        self.gemini_client = gemini_client

    def generate_structured_resume(self, cv_text: str) -> dict[str, Any]:
        """
        Generate a structured resume from raw CV text.
        Uses Gemini if available, otherwise falls back to regex extraction.

        Args:
            cv_text: Raw text extracted from PDF

        Returns:
            Structured resume dictionary
        """
        # Try Gemini first
        if self.gemini_client:
            try:
                result = self.gemini_client.generate_resume_json(cv_text)
                if result and result.get("success"):
                    logger.info("Gemini resume generation successful")
                    return result["data"]
            except Exception as e:
                logger.warning(f"Gemini resume generation failed, using fallback: {e}")

        # Fallback: rule-based extraction
        logger.info("Using fallback resume extraction")
        return self._extract_resume_fallback(cv_text)

    def _extract_resume_fallback(self, cv_text: str) -> dict[str, Any]:
        """
        Rule-based resume extraction as fallback when Gemini is unavailable.

        Args:
            cv_text: Raw CV text

        Returns:
            Structured resume dictionary
        """
        lines = cv_text.split('\n')
        lines = [l.strip() for l in lines if l.strip()]

        # Extract name (usually first non-empty line)
        name = lines[0] if lines else "Unknown"

        # Extract email
        email = ""
        email_pattern = r'\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b'
        for line in lines:
            match = re.search(email_pattern, line)
            if match:
                email = match.group()
                break

        # Extract phone
        phone = ""
        phone_pattern = r'(\+?[\d\s\-\(\)]{7,15})'
        for line in lines:
            match = re.search(phone_pattern, line)
            if match:
                phone = match.group().strip()
                break

        # Extract sections
        sections = {
            "experience": [],
            "education": [],
            "skills": [],
            "projects": [],
            "certifications": []
        }

        current_section = None
        section_keywords = {
            "experience": ["experience", "work history", "employment", "pengalaman"],
            "education": ["education", "pendidikan", "academic"],
            "skills": ["skills", "keahlian", "technical skills", "kompetensi"],
            "projects": ["projects", "project", "proyek"],
            "certifications": ["certification", "certificate", "sertifikat"]
        }

        for line in lines:
            line_lower = line.lower()
            
            # Detect section headers
            for section, keywords in section_keywords.items():
                if any(kw in line_lower for kw in keywords) and len(line) < 50:
                    current_section = section
                    break
            
            # Add content to current section
            if current_section and len(line) > 3:
                if current_section == "skills":
                    # Split comma-separated skills
                    skills = [s.strip() for s in line.split(',') if s.strip()]
                    sections["skills"].extend(skills)
                elif current_section in ["experience", "education"]:
                    if line not in [l.strip() for l in sections[current_section]]:
                        sections[current_section].append(line)

        # Build structured resume
        resume = {
            "name": name,
            "email": email,
            "phone": phone,
            "summary": self._generate_summary(cv_text),
            "experience": self._parse_experience(cv_text),
            "education": self._parse_education(cv_text),
            "skills": list(set(sections["skills"])),
            "raw_text": cv_text[:3000],  # Keep first 3000 chars for display
            "generated_at": datetime.now().isoformat()
        }

        return resume

    def _generate_summary(self, cv_text: str) -> str:
        """Generate a brief professional summary from CV text."""
        # Take first few meaningful lines as summary
        lines = [l.strip() for l in cv_text.split('\n') if l.strip()]
        summary_lines = []
        for line in lines[1:6]:  # Skip name, take next 5 lines
            if len(line) > 20 and not any(kw in line.lower() for kw in 
                ['email', 'phone', 'telp', 'address', 'alamat']):
                summary_lines.append(line)
        
        return ' '.join(summary_lines)[:500] if summary_lines else "Professional with relevant experience."

    def _parse_experience(self, cv_text: str) -> list[dict]:
        """Parse work experience from CV text."""
        experiences = []
        lines = cv_text.split('\n')
        current_exp = {}
        
        # Simple pattern matching for experience blocks
        for i, line in enumerate(lines):
            line = line.strip()
            if not line:
                continue
            
            # Detect date patterns
            date_pattern = r'(\d{4})\s*[-–]\s*(\d{4}|present|now|saat\s*ini)'
            match = re.search(date_pattern, line.lower())
            if match:
                if current_exp.get('title'):
                    experiences.append(current_exp)
                current_exp = {
                    'title': lines[i-1].strip() if i > 0 else '',
                    'period': match.group(),
                    'company': line.replace(match.group(), '').strip() if match.group() in line else line,
                    'description': []
                }
            elif current_exp and len(line) > 30:
                current_exp.setdefault('description', []).append(line)

        if current_exp.get('title'):
            experiences.append(current_exp)

        return experiences if experiences else [{"title": "Work Experience", "period": "", "company": "", "description": ["Details available in CV."]}]

    def _parse_education(self, cv_text: str) -> list[dict]:
        """Parse education from CV text."""
        education = []
        lines = cv_text.split('\n')
        
        edu_keywords = ['university', 'college', 'institute', 'universitas', 'sekolah', 
                       'sma', 'smk', 's1', 's2', 'd3', 'd4', 'bachelor', 'master',
                       'diploma', 'degree', 'gelar']
        
        for i, line in enumerate(lines):
            line_lower = line.lower()
            if any(kw in line_lower for kw in edu_keywords) and len(line) > 10:
                year_pattern = r'(\d{4})\s*[-–]\s*(\d{4}|present|now)'
                year_match = re.search(year_pattern, line_lower)
                
                education.append({
                    'institution': line.strip(),
                    'degree': lines[i-1].strip() if i > 0 else '',
                    'year': year_match.group() if year_match else ''
                })

        return education if education else [{"institution": "Not specified", "degree": "", "year": ""}]

    def generate_resume_text(self, structured_resume: dict) -> str:
        """
        Convert structured resume to formatted text.

        Args:
            structured_resume: Resume dictionary

        Returns:
            Formatted text suitable for display
        """
        lines = []
        lines.append("=" * 60)
        lines.append(f"{'RESUME':^60}")
        lines.append("=" * 60)
        lines.append("")
        
        # Name & Contact
        lines.append(structured_resume.get('name', 'Unknown').upper())
        lines.append("-" * 40)
        if structured_resume.get('email'):
            lines.append(f"Email: {structured_resume['email']}")
        if structured_resume.get('phone'):
            lines.append(f"Phone: {structured_resume['phone']}")
        lines.append("")
        
        # Professional Summary
        summary = structured_resume.get('summary', '')
        if summary:
            lines.append("PROFESSIONAL SUMMARY")
            lines.append("-" * 40)
            lines.append(summary)
            lines.append("")
        
        # Experience
        experience = structured_resume.get('experience', [])
        if experience:
            lines.append("WORK EXPERIENCE")
            lines.append("-" * 40)
            for exp in experience:
                if exp.get('title'):
                    lines.append(f"• {exp['title']}")
                if exp.get('company'):
                    lines.append(f"  {exp['company']}")
                if exp.get('period'):
                    lines.append(f"  {exp['period']}")
                for desc in exp.get('description', []):
                    lines.append(f"  - {desc}")
                lines.append("")
        
        # Education
        education = structured_resume.get('education', [])
        if education:
            lines.append("EDUCATION")
            lines.append("-" * 40)
            for edu in education:
                line_parts = []
                if edu.get('degree'):
                    line_parts.append(edu['degree'])
                if edu.get('institution'):
                    line_parts.append(edu['institution'])
                if edu.get('year'):
                    line_parts.append(f"({edu['year']})")
                if line_parts:
                    lines.append(f"• {' - '.join(line_parts)}")
            lines.append("")
        
        # Skills
        skills = structured_resume.get('skills', [])
        if skills:
            lines.append("SKILLS")
            lines.append("-" * 40)
            lines.append(f"{', '.join(skills)}")
            lines.append("")
        
        lines.append("=" * 60)
        lines.append(f"Generated by CVision AI Engine")
        lines.append(f"Date: {datetime.now().strftime('%Y-%m-%d %H:%M')}")
        lines.append("=" * 60)
        
        return '\n'.join(lines)