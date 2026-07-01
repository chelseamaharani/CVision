"""
Pydantic schemas for request/response validation in FastAPI endpoints.
"""

from pydantic import BaseModel, Field
from typing import Any


class AnalyzeCVResponse(BaseModel):
    """Response schema for CV analysis endpoint."""
    tfidf_score: float = Field(..., ge=0.0, le=1.0, description="TF-IDF cosine similarity score")
    sbert_score: float = Field(..., ge=0.0, le=1.0, description="SBERT semantic similarity score")
    hybrid_score: float = Field(..., ge=0.0, le=1.0, description="Weighted hybrid score")
    match_percentage: float = Field(..., ge=0.0, le=100.0, description="Match percentage")
    recommendation: dict[str, Any] = Field(default_factory=dict, description="Gemini job recommendations")
    skill_gap: dict[str, Any] | None = Field(default=None, description="Skill gap analysis")
    experience_years: float = Field(default=0.0, description="Estimated years of experience")
    education_level: str = Field(default="Unknown", description="Highest education level detected")


class HealthResponse(BaseModel):
    """Response schema for health check endpoint."""
    status: str = Field(..., description="Service status")
    model_loaded: bool = Field(..., description="Whether SBERT model is loaded")


class ErrorResponse(BaseModel):
    """Response schema for error cases."""
    detail: str = Field(..., description="Error description")
    error_code: str | None = Field(default=None, description="Optional error code")