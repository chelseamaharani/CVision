"""
AI Engine for CV Matching System
By Chelsea Maharani Putri
"""

import os
import json
import fitz
import numpy as np
import pandas as pd

from pathlib import Path
from dotenv import load_dotenv

from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity

from sentence_transformers import SentenceTransformer

from google import genai

# CONFIGURATION

ROOT_DIR = Path(__file__).resolve().parent.parent
load_dotenv(ROOT_DIR / ".env")

GEMINI_API_KEY = os.getenv("GEMINI_API_KEY")

if not GEMINI_API_KEY:
    raise ValueError("GEMINI_API_KEY not found in .env")

client = genai.Client(api_key=GEMINI_API_KEY)

sbert_model = SentenceTransformer("all-MiniLM-L6-v2")

# FUNCTIONS

def extract_pdf(pdf_path):
    """
    Extract text from PDF using PyMuPDF.
    """

    doc = fitz.open(pdf_path)

    text = ""

    for page in doc:
        text += page.get_text()

    doc.close()

    return text


import re

def preprocess_text(text):

    text = text.lower()

    text = re.sub(r'[^a-zA-Z0-9\s]', ' ', text)

    text = re.sub(r'\s+', ' ', text)

    return text.strip()


def calculate_tfidf(cv_text, job_text):

    vectorizer = TfidfVectorizer()

    documents = [
        job_text,
        cv_text
    ]

    matrix = vectorizer.fit_transform(documents)

    score = cosine_similarity(
        matrix[0],
        matrix[1]
    )[0][0]

    return float(score)


def calculate_sbert(cv_text, job_text):

    job_embedding = sbert_model.encode(job_text)

    cv_embedding = sbert_model.encode(cv_text)

    score = cosine_similarity(
        [job_embedding],
        [cv_embedding]
    )[0][0]

    return float(score)


def calculate_hybrid_score(tfidf_score, sbert_score):

    return (
        0.4 * tfidf_score +
        0.6 * sbert_score
    )


def calculate_match_percentage(hybrid_score):

    percentage = hybrid_score * 100

    return round(percentage,2)


def generate_job_recommendation(cv_text):

    prompt = f"""
You are an experienced HR Recruitment Specialist.

Analyze the following CV.

Tasks:

1. Recommend TOP 5 jobs
2. Explain why
3. Mention supporting skills
4. Confidence 0-100

Return ONLY JSON.

CV:

{cv_text}
"""

    response = client.models.generate_content(
        model="gemini-2.5-flash",
        contents=prompt
    )

    text = response.text.replace(
        "```json",""
    ).replace(
        "```",""
    ).strip()

    return json.loads(text)


def process_cv(pdf_path, job_description):

    raw_text = extract_pdf(pdf_path)

    clean_text = preprocess_text(raw_text)

    clean_job = preprocess_text(job_description)

    tfidf = calculate_tfidf(
        clean_text,
        clean_job
    )

    sbert = calculate_sbert(
        clean_text,
        clean_job
    )

    hybrid = calculate_hybrid_score(
        tfidf,
        sbert
    )

    percentage = calculate_match_percentage(
        hybrid
    )

    recommendation = generate_job_recommendation(
        raw_text
    )

    return {

        "tfidf_score": round(tfidf,4),

        "sbert_score": round(sbert,4),

        "hybrid_score": round(hybrid,4),

        "match_percentage": percentage,

        "recommendation": recommendation
    }


# MAIN

import sys

def main():

    pdf = sys.argv[1]

    job = sys.argv[2]

    result = process_cv(
        pdf,
        job
    )

    print(
        json.dumps(
            result,
            indent=4
        )
    )

if __name__ == "__main__":
    main()