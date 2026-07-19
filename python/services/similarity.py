"""
Similarity Calculation Service
Provides TF-IDF, SBERT, and hybrid scoring for CV-job matching.
"""

import sys
import logging
import numpy as np
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity
from sentence_transformers import SentenceTransformer

from .text_processor import preprocess_text

# Fix for Windows console encoding (cp1252) - enable UTF-8 output
if sys.platform == 'win32':
    sys.stdout.reconfigure(encoding='utf-8')
    sys.stderr.reconfigure(encoding='utf-8')

logger = logging.getLogger(__name__)


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
        try:
            logger.info(f"Loading SBERT model: {sbert_model_name}")
            self.sbert_model = SentenceTransformer(sbert_model_name)
            self._model_loaded = True
            logger.info(f"SBERT model loaded successfully")
        except Exception as e:
            logger.error(f"Failed to load SBERT model: {e}")
            self._model_loaded = False
            self.sbert_model = None

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

        # Use word n-grams (1-2) to capture phrases and individual terms
        # Don't remove stop words - they provide context and improve matching
        # Use sublinear_tf to reduce impact of very frequent terms
        vectorizer = TfidfVectorizer(
            ngram_range=(1, 2),  # Use unigrams and bigrams
            min_df=1,
            max_df=0.95,
            stop_words=None,  # Don't remove stop words - they provide context
            max_features=30000,  # Increase features to capture more terms
            sublinear_tf=True,  # Use log scaling (1 + log(tf)) instead of raw tf
            norm='l2',  # L2 normalization
        )
        documents = [clean_job, clean_cv]
        matrix = vectorizer.fit_transform(documents)
        
        # Check if matrix has any non-zero values
        if matrix.nnz == 0:
            logger.warning("TF-IDF matrix is empty - no matching terms found")
            return 0.0
        
        score = cosine_similarity(matrix[0], matrix[1])[0][0]
        logger.info(f"TF-IDF score: {score:.4f} (cv_words={len(clean_cv.split())}, job_words={len(clean_job.split())})")

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
        logger.info(f"SBERT score: {score:.4f}")

        return float(score)

    def calculate_hybrid_score(
        self,
        tfidf_score: float,
        sbert_score: float,
        tfidf_weight: float = 0.5,
        sbert_weight: float = 0.5
    ) -> float:
        """
        Calculate weighted hybrid score from TF-IDF and SBERT scores.

        Args:
            tfidf_score: TF-IDF similarity score
            sbert_score: SBERT similarity score
            tfidf_weight: Weight for TF-IDF (default 0.5)
            sbert_weight: Weight for SBERT (default 0.5)

        Returns:
            Weighted hybrid score (0.0 to 1.0)
        """
        # Calculate base hybrid score with equal weights
        hybrid = (tfidf_weight * tfidf_score) + (sbert_weight * sbert_score)
        
        return hybrid

    def calculate_match_percentage(self, hybrid_score: float) -> float:
        """
        Convert hybrid score to a match percentage.

        Uses linear scaling for transparency and interpretability.
        Same CV + same job will always produce the same score.

        Args:
            hybrid_score: Hybrid similarity score (0.0 to 1.0)

        Returns:
            Match percentage rounded to 2 decimal places (0 to 100)
        """
        # Linear scaling - transparent and predictable
        # If hybrid score is 0.75, match percentage is 75%
        percentage = hybrid_score * 100
        
        # Clamp to valid range [0, 100]
        percentage = max(0.0, min(100.0, percentage))
        
        # Round to 2 decimal places
        percentage = round(percentage, 2)
        
        return percentage
