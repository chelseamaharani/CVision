# AI Architecture - CVision (3-Layer Architecture)

## Overview

CVision mengimplementasikan arsitektur AI dengan tiga lapis utama yang terstandar di industri:

```
┌─────────────────────────────────────────────────────────────────┐
│                    APPLICATION / MLOPS LAYER                     │
│  (Orchestration, API, Business Logic, Monitoring)               │
├─────────────────────────────────────────────────────────────────┤
│                       MODEL LAYER                               │
│  (AI Models, Algorithms, Inference Engine)                     │
├─────────────────────────────────────────────────────────────────┤
│                        DATA LAYER                                 │
│  (Storage, Processing, Feature Engineering)                     │
└─────────────────────────────────────────────────────────────────┘
```

---

## 1. DATA LAYER (Data Foundation)

### 1.1 Data Sources

| Source | Type | Description |
|--------|------|-------------|
| **PDF Files** | Unstructured | CV kandidat dalam format PDF |
| **Job Descriptions** | Unstructured | Deskripsi pekerjaan dari employer |
| **User Profiles** | Structured | Data user di database (Laravel) |
| **Application History** | Structured | Riwayat aplikasi kerja |

### 1.2 Data Processing Pipeline

```
[PDF Upload]
     ↓
[PDF Extraction] → PyMuPDF (fitz)
     ↓
[Text Preprocessing] → text_processor.py
     ├── Lowercase normalization
     ├── Remove control characters
     ├── Clean UTF-8 encoding
     └── Preserve structure for section detection
     ↓
[Feature Engineering]
     ├── TF-IDF Vectorization (30000 features, n-gram 1-2)
     └── SBERT Embedding (384 dimensions)
```

### 1.3 Data Storage

**Primary Database (MySQL/PostgreSQL)**
- `users` - Data pengguna
- `jobs` - Lowongan pekerjaan
- `applications` - Riwayat aplikasi
- `cv_scores` - Hasil scoring CV
- `resumes` - Data resume terstruktur

**File Storage**
- `storage/app/cv_uploads/` - File PDF CV
- `storage/app/resume_downloads/` - File resume hasil generate

### 1.4 Data Models (Schemas)

```python
# python/models/schemas.py
class AnalyzeCVResponse:
    - tfidf_score: float (0.0 - 1.0)
    - sbert_score: float (0.0 - 1.0)
    - hybrid_score: float (0.0 - 1.0)
    - match_percentage: float (0 - 100)
    - recommendation: dict (Gemini output)
    - skill_gap: dict (Gemini output)
    - experience_years: float
    - education_level: str

class ResumeResponse:
    - success: bool
    - data: dict (structured resume)
    - error: str | None
```

---

## 2. MODEL LAYER (AI/ML Engine)

### 2.1 Classical ML - TF-IDF

**File:** `python/services/similarity.py`

```
Input: CV text + Job Description text
     ↓
TfidfVectorizer(
    ngram_range=(1, 2),
    min_df=1,
    max_df=0.95,
    stop_words=None,
    max_features=30000,
    sublinear_tf=True,
    norm='l2'
)
     ↓
Cosine Similarity
     ↓
Output: TF-IDF Score (0.0 - 1.0)
```

**Karakteristik:**
- Lexical matching (kata kunci)
- Interpretable
- Fast inference
- Cocok untuk ATS-like matching

### 2.2 Deep Learning - SBERT

**File:** `python/services/similarity.py`

```
Input: CV text + Job Description text
     ↓
SentenceTransformer('all-MiniLM-L6-v2')
     ├── Model size: 80MB
     ├── Embedding dim: 384
     └── Pre-trained on NLI + STS
     ↓
Cosine Similarity
     ↓
Output: SBERT Score (0.0 - 1.0)
```

**Karakteristik:**
- Semantic understanding
- Menangkap sinonim
- Context-aware
- Model loaded once at startup

### 2.3 Ensemble - Hybrid Scoring

**Formula:**
```
Hybrid Score = (0.5 × TF-IDF Score) + (0.5 × SBERT Score)
Match Percentage = Hybrid Score × 100
```

**File:** `python/services/similarity.py` - `calculate_hybrid_score()`

### 2.4 Large Language Model - Gemini

**File:** `python/services/gemini_client.py`

```
Input: CV text
     ↓
Gemini API (gemini-2.0-flash-lite)
     ├── Rate limit: 0.5s minimum delay
     ├── Retry: exponential backoff (5s, 10s, 20s)
     └── Max retries: 3x
     ↓
Output:
    ├── Job Recommendations (TOP 5)
    │   ├── job_title
    │   ├── confidence (0-100)
    │   ├── reasoning
    │   └── supporting_skills
    └── Skill Gap Analysis
        ├── skills_present
        ├── skills_missing
        ├── fit_score (0-100)
        └── recommendation (HIRE/CONSIDER/REJECT)
```

### 2.5 Rule-Based Models

**File:** `python/services/resume_generator.py` & `app/Services/ResumeParsingService.php`

```
Input: Raw CV text
     ↓
Regex Pattern Matching
     ├── Email: \b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b
     ├── Phone: (\+?\d[\d\s\-]{7,15})
     ├── Experience: (\d{4})\s*[-–]\s*(\d{4}|present|now)
     ├── Education: S1/S2/D3/SMA/bachelor/master keywords
     └── Skills: Section detection + comma split
     ↓
Output: Structured Resume
    ├── name, email, phone
    ├── experience (list)
    ├── education (list)
    ├── skills (list)
    └── certifications, languages
```

---

## 3. APPLICATION / MLOPS LAYER

### 3.1 API Layer (FastAPI)

**File:** `python/main.py`

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/health` | GET | Health check + model status |
| `/api/cv/analyze` | POST | Full CV analysis (PDF upload) |
| `/api/cv/analyze-text` | POST | CV analysis (text input) |
| `/api/cv/generate-resume` | POST | Generate structured resume |
| `/api/cv/generate-resume-text` | POST | Generate resume text for download |

**Middleware:**
- CORS (allow all origins for dev)
- UTF-8 cleaning
- Error handling

### 3.2 Application Layer (Laravel)

**File:** `app/Services/ResumeParsingService.php`

**Responsibilities:**
- HTTP request handling
- Database operations
- Session management
- User authentication
- File upload management

**Key Services:**
- `CVScoreService.php` - Rank calculation
- `CVExtractionService.php` - PDF extraction coordination
- `ResumeParsingService.php` - Resume parsing orchestration

### 3.3 MLOps - Model Management

```
Model Lifecycle:
┌─────────────────────────────────────┐
│  Model Loading (Startup)              │
│  - SBERT model loaded once           │
│  - Gemini client initialized          │
└─────────────────────────────────────┘
                   ↓
┌─────────────────────────────────────┐
│  Inference (Runtime)                │
│  - TF-IDF: on-demand                 │
│  - SBERT: cached model               │
│  - Gemini: rate-limited API calls     │
└─────────────────────────────────────┘
                   ↓
┌─────────────────────────────────────┐
│  Monitoring & Logging               │
│  - Request logging                    │
│  - Error tracking                     │
│  - Performance metrics                │
└─────────────────────────────────────┘
```

### 3.4 Deployment Architecture

```
                    ┌─────────────────────┐
                    │   Load Balancer     │
                    │   (Nginx/Cloudflare)│
                    └──────────┬──────────┘
                               │
        ┌────────────────────────┼────────────────────────┐
        │                      │                        │
        ▼                      ▼                        ▼
┌───────────────┐    ┌───────────────┐    ┌───────────────┐
│   Laravel     │    │   Python      │    │   Database    │
│   (PHP 8.3)   │    │   (FastAPI)   │    │   (MySQL)     │
│               │    │               │    │               │
│ - Web routes  │    │ - /api/cv/*   │    │ - Users       │
│ - Auth        │    │ - /health     │    │ - Jobs        │
│ - Database    │    │ - Model APIs  │    │ - Scores      │
└───────────────┘    └───────────────┘    └───────────────┘
        │                      │                        │
        └──────────────────────┼────────────────────────┘
                             │
                    ┌────────┴────────┐
                    │   File Storage    │
                    │   (Local/S3)      │
                    └───────────────────┘
```

---

## Model Comparison Matrix

| Model | Category | Speed | Accuracy | Cost | Interpretability |
|-------|----------|-------|----------|------|----------------|
| TF-IDF | Classical ML | ⚡⚡⚡ | ⚡⚡ | Free | ⚡⚡⚡ |
| SBERT | Deep Learning | ⚡⚡ | ⚡⚡⚡ | Free | ⚡ |
| Hybrid | Ensemble | ⚡⚡ | ⚡⚡⚡ | Free | ⚡⚡ |
| Gemini | LLM | ⚡ | ⚡⚡⚡ | Paid (API) | ⚡⚡ |
| Rule-Based | Heuristic | ⚡⚡⚡ | ⚡⚡ | Free | ⚡⚡⚡ |

---

## Data Flow Summary

```
1. User Upload PDF
         ↓
2. Data Layer: PDF → Text (PyMuPDF)
         ↓
3. Model Layer: 
   ├── TF-IDF scoring
   ├── SBERT embedding
   ├── Hybrid fusion
   ├── Gemini recommendation
   └── Rule-based parsing
         ↓
4. Application Layer:
   ├── Save to database
   ├── Calculate rank
   └── Return JSON response
         ↓
5. Frontend: Display results
```

---

## Key Design Decisions

1. **Why Hybrid TF-IDF + SBERT?**
   - TF-IDF alone: terlalu strict, miss semantic matches
   - SBERT alone: terlalu longgar, miss critical keywords
   - Hybrid: F1-Score optimal (50/50 weight)

2. **Why Gemini Flash-Lite?**
   - Kecepatan tinggi untuk production
   - Biaya lebih murah dari Gemini Pro
   - Cukup akurat untuk recommendation

3. **Why Rule-Based Parsing?**
   - Hemat token Gemini
   - Tidak depend pada API availability
   - CV terstruktur sudah cukup akurat dengan regex

4. **Why Two-Tier Architecture?**
   - PHP shared hosting tidak support Python
   - Scalability terpisah untuk masing-masing layer
   - Laravel untuk web, Python untuk AI compute