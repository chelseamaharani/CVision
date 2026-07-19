# CVision AI Integration - Fix Plan

## All Issues Identified (17 Total)

### Critical Issues (Phase 1)
1. ✅ Model Gemini tidak valid (`gemini-3.1-flash-lite` → `gemini-2.0-flash-lite`)
2. ✅ Endpoint `/api/cv/analyze` tidak menerima job requirements
3. ✅ PDF extraction loop tidak berguna di CVExtractionService

### Major Issues (Phase 2)
4. ✅ Duplikasi resume parsing (Python vs PHP)
5. ✅ Field `skills_total` / `skills_count` tidak diisi
6. ✅ Cache key tidak include job description update
7. ✅ Rank tidak konsisten pada concurrent requests
8. ✅ UTF-8 cleaning tidak efektif

### Performance & UX Issues (Phase 3)
9. ✅ Rate limiting terlalu ketat (2s → 0.5s)
10. ✅ Tidak ada monitoring queue worker
11. ✅ Python subprocess fallback untuk PDF extraction di Windows
12. ✅ Tidak ada feedback real-time untuk proses screening
13. ✅ Google Auth route tidak fungsional
14. ✅ Tidak ada loading state untuk upload

## Changes Made

### Phase 1 - Critical Fixes
- `python/services/gemini_client.py` - Model name updated
- `python/models/schemas.py` - Added `min_experience` and `required_education` fields
- `python/main.py` - Synchronized endpoint parameters
- `app/Services/CVExtractionService.php` - Removed dead code

### Phase 2 - Data Integrity
- `app/Services/CVScoreService.php` - Added skills_total/count calculation, improved cache key, fixed rank calculation
- `app/DTOs/CVScoreResult.php` - Fixed UTF-8 cleaning

### Phase 3 - Performance & UX
- `python/services/gemini_client.py` - Reduced rate limit delay
- `app/Http/Controllers/LandingPagePelamarController.php` - Added queue fallback
- `resources/views/pages/landing_page_pelamar.blade.php` - Added loading state

### Phase 4 - Code Quality
- `python/services/resume_generator.py` - Removed duplicate parser
- `app/Services/CVExtractionService.php` - Added Python path auto-detection
- `routes/web.php` - Removed non-functional Google Auth route

## Testing Instructions

1. Clear cache:
```bash
php artisan optimize:clear
```

2. Restart Python AI Engine:
```bash
cd python
uvicorn main:app --reload --port 8000
```

3. Upload CV and verify:
   - Model Gemini responds correctly
   - Job requirements are passed to AI service
   - Skills total/count are populated
   - Rank is calculated correctly
   - Loading state appears during upload
