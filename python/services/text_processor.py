"""
Text Preprocessing Service
Handles text cleaning and normalization for CV and job descriptions.
"""

import re


def preprocess_text(text: str) -> str:
    """
    Clean and normalize text for similarity analysis.
    
    IMPORTANT: This function removes newlines because TF-IDF and SBERT
    need continuous text for proper similarity calculation.
    For resume parsing (section detection), use the raw text instead.

    - Lowercases text
    - Removes newlines (similarity algorithms need continuous text)
    - Keeps non-ASCII characters (for Indonesian/English text)
    - Removes only control characters
    - Normalizes whitespace

    Args:
        text: Raw text to preprocess

    Returns:
        Cleaned and normalized text (single line, no newlines)
    """
    # Normalize common technical terms to preserve them
    text = text.replace('C++', 'cplusplus')
    text = text.replace('C#', 'csharp')
    text = text.replace('Node.js', 'nodejs')
    text = text.replace('.NET', 'dotnet')
    text = text.replace('ASP.NET', 'aspnet')
    
    # Lowercase
    text = text.lower()
    
    # Remove control characters (ASCII 0-31) including newlines
    # TF-IDF and SBERT need continuous text, not structured text
    text = ''.join(char if ord(char) >= 32 else ' ' for char in text)
    
    # Normalize all whitespace to single spaces
    text = re.sub(r'\s+', ' ', text)
    
    return text.strip()


def preprocess_text_for_resume(text: str) -> str:
    """
    Clean text for resume parsing while preserving structure.
    
    This function preserves newlines because resume parsing needs them
    for section detection (EXPERIENCE, EDUCATION, SKILLS sections).

    Args:
        text: Raw CV text

    Returns:
        Cleaned text with newlines preserved
    """
    # Normalize common technical terms
    text = text.replace('C++', 'cplusplus')
    text = text.replace('C#', 'csharp')
    text = text.replace('Node.js', 'nodejs')
    text = text.replace('.NET', 'dotnet')
    text = text.replace('ASP.NET', 'aspnet')
    
    # Lowercase
    text = text.lower()
    
    # Remove ONLY control characters except newline and tab
    text = ''.join(char if (ord(char) >= 32 or char in '\n\t') else ' ' for char in text)
    
    # Normalize spaces per line (don't remove newlines)
    lines = text.split('\n')
    lines = [re.sub(r'[ \t]+', ' ', line).strip() for line in lines]
    text = '\n'.join(lines)
    
    return text


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
    Supports both Indonesian and English education terms.

    Args:
        text: CV text to analyze

    Returns:
        Highest education level found (e.g., 'S1', 'D3', 'SMA', 'SMP')
    """
    text_lower = text.lower()
    
    # Find all matching education levels with their priority
    found_levels = []
    
    # PhD / S3 (highest)
    if any(term in text_lower for term in ['phd', 'doctorate', 'doctoral', 'ph.d', 's3']):
        found_levels.append('S3')
    
    # Master / S2
    if any(term in text_lower for term in ['master', "master's", 'masters', 'm.sc', 'm.a', 'msc', 'ma', 's2']):
        found_levels.append('S2')
    
    # Bachelor / S1
    if any(term in text_lower for term in ['bachelor', "bachelor's", 'bachelors', 'b.sc', 'b.a', 'bsc', 'ba', 's1', 'undergraduate', 'sarjana']):
        found_levels.append('S1')
    
    # D4
    if any(term in text_lower for term in ['d4', 'diploma 4', 'diploma iv']):
        found_levels.append('D4')
    
    # D3
    if any(term in text_lower for term in ['d3', 'diploma 3', 'diploma iii', 'associate degree']):
        found_levels.append('D3')
    
    # SMA/SMK (High School)
    if any(term in text_lower for term in ['sma', 'smk', 'high school', 'secondary school', 'senior high']):
        found_levels.append('SMA/SMK')
    
    # SMP (Junior High School)
    if any(term in text_lower for term in ['smp', 'junior high', 'middle school']):
        found_levels.append('SMP')
    
    # Return the highest level found (first in priority order)
    # Priority: S3 > S2 > S1 > D4 > D3 > SMA/SMK > SMP
    priority_order = ['S3', 'S2', 'S1', 'D4', 'D3', 'SMA/SMK', 'SMP']
    
    for level in priority_order:
        if level in found_levels:
            return level
    
    return 'Unknown'
