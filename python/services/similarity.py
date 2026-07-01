"""
Similarity Calculation Service
Provides TF-IDF, SBERT, and hybrid scoring for CV-job matching.
"""

import numpy as np
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity
from sentence_transformers import SentenceTransformer

from .text_processor import preprocess_text


class SimilarityService:
    """
    Service that computes similarity scores between CV text and job descriptions.
    SBERT model is loaded once at initialization and reused.
    """

    def __init__(self, sbert_model_name: str = "all-MiniLM-L6-v2"):
        """
        Initialize the similarity service and load the SBERT model.

        Args:
            sbert_model_name: Name of the SentenceTransformer model to use
        """
        self.sbert_model = SentenceTransformer(sbert_model_name)
        self._model_loaded = True

    @property
    def is_loaded(self) -> bool:
        """Check if the SBERT model is loaded."""
        return self._model_loaded

    def calculate_tfidf(self, cv_text: str, job_text: str) -> float:
        """
        Calculate TF-IDF cosine similarity between CV and job description.

        Args:
            cv_text: Raw CV text
            job_text: Raw job description text

        Returns:
            TF-IDF similarity score (0.0 to 1.0)
        """
        clean_cv = preprocess_text(cv_text)
        clean_job = preprocess_text(job_text)

        vectorizer = TfidfVectorizer()
        documents = [clean_job, clean_cv]
        matrix = vectorizer.fit_transform(documents)
        score = cosine_similarity(matrix[0], matrix[1])[0][0]

        return float(score)

    def calculate_sbert(self, cv_text: str, job_text: str) -> float:
        """
        Calculate SBERT semantic similarity between CV and job description.

        Args:
            cv_text: Raw CV text
            job_text: Raw job description text

        Returns:
            SBERT similarity score (0.0 to 1.0)
        """
        clean_cv = preprocess_text(cv_text)
        clean_job = preprocess_text(job_text)

        job_embedding = self.sbert_model.encode(clean_job)
        cv_embedding = self.sbert_model.encode(clean_cv)

        score = cosine_similarity([job_embedding], [cv_embedding])[0][0]

        return float(score)

    def calculate_hybrid_score(
        self,
        tfidf_score: float,
        sbert_score: float,
        tfidf_weight: float = 0.4,
        sbert_weight: float = 0.6
    ) -> float:
        """
        Calculate weighted hybrid score from TF-IDF and SBERT scores.

        Args:
            tfidf_score: TF-IDF similarity score
            sbert_score: SBERT similarity score
            tfidf_weight: Weight for TF-IDF (default 0.4)
            sbert_weight: Weight for SBERT (default 0.6)

        Returns:
            Weighted hybrid score
        """
        return (tfidf_weight * tfidf_score) + (sbert_weight * sbert_score)

    def calculate_match_percentage(self, hybrid_score: float) -> float:
        """
        Convert hybrid score to a match percentage.

        Args:
            hybrid_score: Hybrid similarity score

        Returns:
            Match percentage rounded to 2 decimal places
        """
        return round(hybrid_score * 100, 2)