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
    min_experience: float = Field(default=0.0, description="Minimum required years of experience")
    required_education: str = Field(default="", description="Required education level")


class HealthResponse(BaseModel):
    """Response schema for health check endpoint."""
    model_config = {"protected_namespaces": ()}
    
    status: str = Field(..., description="Service status")
    model_loaded: bool = Field(..., description="Whether SBERT model is loaded")


class ResumeResponse(BaseModel):
    """Response schema for resume generation endpoint."""
    success: bool = Field(..., description="Whether resume generation was successful")
    data: dict[str, Any] | None = Field(default=None, description="Structured resume data")
    error: str | None = Field(default=None, description="Error message if failed")


class ResumeDownloadResponse(BaseModel):
    """Response schema for resume download endpoint."""
    success: bool = Field(..., description="Whether PDF generation was successful")
    filename: str | None = Field(default=None, description="Generated PDF filename")
    content: str | None = Field(default=None, description="Text content for the resume")
    error: str | None = Field(default=None, description="Error message if failed")


class ErrorResponse(BaseModel):
    """Response schema for error cases."""
    detail: str = Field(..., description="Error description")
    error_code: str | None = Field(default=None, description="Optional error code")
