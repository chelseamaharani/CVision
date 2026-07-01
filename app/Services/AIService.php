<?php

namespace App\Services;

use App\DTOs\CVScoreResult;

/**
 * Contract for AI-powered CV analysis services.
 * 
 * Implementations can use different backends (FastAPI, direct Python, OpenAI, etc.)
 * as long as they conform to this interface.
 */
interface AIService
{
    /**
     * Analyze a CV against a job description.
     *
     * @param string $cvText         Raw text extracted from the CV
     * @param string $jobDescription Job description text
     * @param array  $requiredSkills List of required skills (optional)
     * @param string $jobTitle       Job title (optional)
     * @return CVScoreResult
     *
     * @throws \App\Exceptions\AIProcessingException
     */
    public function analyzeCV(
        string $cvText,
        string $jobDescription,
        array $requiredSkills = [],
        string $jobTitle = 'Unknown Position',
    ): CVScoreResult;

    /**
     * Check if the AI service is healthy and reachable.
     */
    public function isHealthy(): bool;
}