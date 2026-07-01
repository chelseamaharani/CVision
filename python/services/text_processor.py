"""
Text Preprocessing Service
Handles text cleaning and normalization for CV and job descriptions.
"""

import re


def preprocess_text(text: str) -> str:
    """
    Clean and normalize text for similarity analysis.

    - Lowercases text
    - Removes special characters (keeps alphanumeric and spaces)
    - Collapses multiple whitespace into single space
    - Strips leading/trailing whitespace

    Args:
        text: Raw text to preprocess

    Returns:
        Cleaned and normalized text
    """
    text = text.lower()
    text = re.sub(r'[^a-zA-Z0-9\s]', ' ', text)
    text = re.sub(r'\s+', ' ', text)
    return text.strip()


def extract_skills(text: str, skill_list: list[str]) -> list[str]:
    """
    Extract matching skills from text against a known skill list.

    Args:
        text: Text to search for skills
        skill_list: List of skills to look for

    Returns:
        List of matched skills
    """
    text_lower = text.lower()
    matched = []
    for skill in skill_list:
        if skill.lower() in text_lower:
            matched.append(skill)
    return matched


def extract_years_of_experience(text: str) -> float:
    """
    Extract total years of experience from text using regex patterns.

    Looks for patterns like "X years of experience", "X+ years", etc.

    Args:
        text: CV text to analyze

    Returns:
        Estimated years of experience, or 0.0 if not found
    """
    patterns = [
        r'(\d+)\+?\s*years?\s*(?:of\s*)?experience',
        r'experience\s*(?:of\s*)?(\d+)\+?\s*years?',
        r'(\d+)\+?\s*years?\s*(?:of\s*)?(?:work|professional|industry)',
    ]
    for pattern in patterns:
        match = re.search(pattern, text.lower())
        if match:
            return float(match.group(1))
    return 0.0


def extract_education_level(text: str) -> str:
    """
    Extract highest education level from text.

    Args:
        text: CV text to analyze

    Returns:
        Highest education level found (e.g., 'PhD', 'Master', 'Bachelor', 'Diploma', 'High School')
    """
    text_lower = text.lower()
    if any(term in text_lower for term in ['phd', 'doctorate', 'doctoral', 'ph.d']):
        return 'PhD'
    if any(term in text_lower for term in ['master', "master's", 'masters', 'm.sc', 'm.a', 'msc', 'ma']):
        return 'Master'
    if any(term in text_lower for term in ['bachelor', "bachelor's", 'bachelors', 'b.sc', 'b.a', 'bsc', 'ba', 's1', 'undergraduate']):
        return 'Bachelor'
    if any(term in text_lower for term in ['diploma', 'd3', 'd4', 'associate']):
        return 'Diploma'
    if any(term in text_lower for term in ['high school', 'sma', 'smk', 'smp']):
        return 'High School'
    return 'Unknown'