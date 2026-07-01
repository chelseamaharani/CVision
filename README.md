# CVision - AI-Powered CV Matching System

CVision adalah sistem rekrutmen cerdas yang menggunakan AI untuk mencocokkan CV kandidat dengan posisi pekerjaan yang tersedia. Sistem ini menggabungkan **TF-IDF**, **SBERT (Semantic Search)**, dan **Gemini AI** untuk memberikan skor kecocokan yang akurat.

## 🎯 Fitur Utama

### Untuk HRD (Recruiter):
- ✅ **Job Listing Management** - Buat dan kelola posisi pekerjaan
- ✅ **Per-CV Screening** - Pilih CV mana yang akan di-analisis (hemat token)
- ✅ **Batch Screening** - Screen semua CV sekaligus dengan jeda otomatis
- ✅ **Real-time AI Scoring** - Lihat skor TF-IDF, SBERT, dan Hybrid score
- ✅ **Skill Gap Analysis** - Identifikasi skill yang kurang dari kandidat
- ✅ **Gemini Recommendations** - Rekomendasi pekerjaan alternatif dari AI
- ✅ **Ranking System** - Kandidat diurutkan berdasarkan skor kecocokan

### Untuk Pelamar (Applicants):
- ✅ **CV Upload** - Upload CV dalam format PDF
- ✅ **Job Matching** - Lihat posisi yang cocok dengan CV mereka
- ✅ **AI Analysis** - Dapatkan insight dari AI tentang kekuatan dan kelemahan CV

---

## 🏗️ Arsitektur Sistem

```
┌─────────────────────────────────────────────────────────────┐
│                    CVision System                            │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  ┌──────────────┐         ┌──────────────┐                 │
│  │   Laravel    │◄───────►│  FastAPI     │                 │
│  │   (PHP)      │  HTTP   │  (Python)    │                 │
│  │              │  POST    │              │                 │
│  │  - Controllers│         │  - TF-IDF    │                 │
│  │  - Services   │         │  - SBERT     │                 │
│  │  - Queue Jobs │         │  - Hybrid    │                 │
│  │  - Blade UI   │         │  - Gemini    │                 │
│  └──────────────┘         └──────────────┘                 │
│         ▲                         ▲                          │
│         │                         │                          │
│  ┌──────┴──────┐          ┌──────┴──────┐                  │
│  │   MySQL     │          │   Gemini    │                  │
│  │  Database   │          │   API       │                  │
│  └─────────────┘          └─────────────┘                  │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### Tech Stack:
- **Backend**: Laravel 10+ (PHP 8.1+)
- **AI Engine**: FastAPI (Python 3.10+)
- **Database**: MySQL
- **Queue**: Laravel Database Queue
- **AI/ML**: 
  - TF-IDF (scikit-learn)
  - SBERT (Sentence-Transformers)
  - Google Gemini API
- **PDF Processing**: PyMuPDF

---

## 📦 Struktur Project

```
CVision/
├── Laravel Application
│   ├── app/
│   │   ├── Http/Controllers/
│   │   │   ├── ScreeningController.php       # Per-CV screening
│   │   │   ├── JobListingController.php      # Job management
│   │   │   ├── MatchingController.php        # Results display
│   │   │   └── CandidateResumeController.php # Candidate detail
│   │   ├── Services/
│   │   │   ├── AIService.php                 # Interface
│   │   │   ├── GeminiAIService.php           # FastAPI client
│   │   │   ├── CVExtractionService.php       # PDF text extraction
│   │   │   └── CVScoreService.php            # Analysis orchestrator
│   │   ├── DTOs/
│   │   │   └── CVScoreResult.php             # Data transfer object
│   │   ├── Exceptions/
│   │   │   └── AIProcessingException.php     # Custom exception
│   │   ├── Jobs/
│   │   │   └── ProcessCVJob.php              # Queue job (retry: 3x)
│   │   ├── Repositories/
│   │   │   └── MatchingResultRepository.php  # Query logic
│   │   └── Models/
│   │       ├── Cv.php
│   │       ├── UploadJob.php
│   │       └── MatchingResult.php            # AI scores storage
│   ├── resources/views/pages/
│   │   ├── screening_cvs.blade.php           # Per-CV screening UI
│   │   ├── job_listing.blade.php             # Job list for HRD
│   │   ├── matching_results.blade.php        # Results with AI scores
│   │   └── candidate_resume.blade.php        # Detailed AI analysis
│   ├── routes/
│   │   ├── web.php                           # Web routes
│   │   └── api.php                           # API routes
│   └── database/migrations/
│       └── 2026_07_01_*.php                  # AI scores columns
│
├── Python AI Engine (FastAPI)
│   ├── main.py                               # FastAPI server
│   ├── services/
│   │   ├── pdf_extractor.py                  # PyMuPDF text extraction
│   │   ├── text_processor.py                 # Text cleaning & extraction
│   │   ├── similarity.py                     # TF-IDF + SBERT + Hybrid
│   │   └── gemini_client.py                  # Gemini API (with retry)
│   ├── models/
│   │   └── schemas.py                        # Pydantic validation
│   └── requirements.txt                      # Python dependencies
│
├── .env                                      # Configuration (NOT in Git)
├── .gitignore                                # Git ignore rules
└── README.md                                 # This file
```

---

## 🚀 Instalasi & Setup

### Prerequisites:
- PHP 8.1+
- Composer
- Node.js & NPM
- Python 3.10+
- MySQL
- Git

### 1. Clone Repository:
```bash
git clone <repository-url>
cd CVision
```

### 2. Setup Laravel:
```bash
# Install dependencies
composer install
npm install

# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Configure database in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cvision
DB_USERNAME=root
DB_PASSWORD=

# Configure AI Engine
AI_ENGINE_URL=http://127.0.0.1:8000
AI_ENGINE_TIMEOUT=120
PYTHON_PATH=c:/laragon/www/CVision/venv/Scripts/python.exe
GEMINI_API_KEY=your_gemini_api_key_here

# Run migrations
php artisan migrate

# Create storage link
php artisan storage:link
```

### 3. Setup Python AI Engine:
```bash
# Navigate to python directory
cd python

# Create virtual environment
python -m venv venv

# Activate virtual environment
# Windows:
venv/Scripts/activate
# Linux/Mac:
source venv/bin/activate

# Install dependencies
pip install -r requirements.txt

# Create .env file in python/ directory
echo "GEMINI_API_KEY=your_gemini_api_key_here" > .env
```

### 4. Start Services:

**Terminal 1 - FastAPI AI Engine:**
```bash
cd python
uvicorn main:app --reload --port 8000
```

**Terminal 2 - Laravel Queue Worker:**
```bash
php artisan queue:work --tries=3
```

**Terminal 3 - Laravel Server:**
```bash
php artisan serve
```

### 5. Access Application:
- **Laravel**: http://localhost:8000
- **FastAPI Docs**: http://localhost:8000/docs
- **FastAPI Health**: http://localhost:8000/health

---

## ⚙️ Konfigurasi

### Environment Variables (.env):

```env
# Application
APP_NAME=CVision
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cvision
DB_USERNAME=root
DB_PASSWORD=

# Queue
QUEUE_CONNECTION=database

# AI Engine (FastAPI)
AI_ENGINE_URL=http://127.0.0.1:8000
AI_ENGINE_TIMEOUT=120
PYTHON_PATH=c:/laragon/www/CVision/venv/Scripts/python.exe

# Gemini API
GEMINI_API_KEY=your_api_key_here
```

### Gemini API Key:
1. Kunjungi [Google AI Studio](https://makersuite.google.com/app/apikey)
2. Buat API key baru
3. Copy ke `.env` file

---

## 📊 Alur Kerja Sistem

### 1. **Job Posting (HRD)**:
```
HRD Login
  ↓
Create Job (title, description, skills, requirements)
  ↓
Job saved to database
```

### 2. **CV Upload (Applicant)**:
```
Applicant Login
  ↓
Upload CV (PDF)
  ↓
CV saved to storage
```

### 3. **Per-CV Screening (HRD)**:
```
HRD buka Job Listing
  ↓
Klik "Screen CVs" untuk job tertentu
  ↓
Pilih CV yang ingin di-screen
  ↓
Klik "Screen" button
  ↓
┌─────────────────────────────────────┐
│ Laravel Queue Job                   │
│  ├─ Extract text from PDF           │
│  ├─ Clean UTF-8 encoding            │
│  ├─ Send to FastAPI (HTTP POST)     │
│  └─ Receive AI analysis             │
└─────────────────────────────────────┘
  ↓
FastAPI Processing:
  ├─ TF-IDF scoring
  ├─ SBERT semantic similarity
  ├─ Hybrid score (40% TF-IDF + 60% SBERT)
  ├─ Gemini job recommendations
  ├─ Skill gap analysis
  └─ Experience/education extraction
  ↓
Save results to matching_results table
  ↓
HRD lihat hasil screening (score, rank, recommendations)
```

### 4. **Batch Screening (HRD)**:
```
HRD klik "Screen All CVs"
  ↓
Loop through all CVs:
  ├─ Process CV #1
  ├─ Wait 3 seconds (rate limiting)
  ├─ Process CV #2
  ├─ Wait 3 seconds
  ├─ Process CV #3
  └─ ... (continue for all CVs)
  ↓
Update ranking based on scores
  ↓
Show completion message
```

---

## 🔌 API Endpoints

### FastAPI (Python):

#### Health Check:
```http
GET /health
```

#### Analyze CV (with PDF upload):
```http
POST /api/cv/analyze
Content-Type: multipart/form-data

{
  "cv_file": "<PDF file>",
  "job_description": "string",
  "required_skills": "string (comma-separated)",
  "job_title": "string"
}
```

#### Analyze CV (with text):
```http
POST /api/cv/analyze-text
Content-Type: application/x-www-form-urlencoded

{
  "cv_text": "string",
  "job_description": "string",
  "required_skills": "string",
  "job_title": "string"
}
```

### Laravel Routes:

#### Web Routes:
```php
// Job Listing
GET  /job_listing                    → JobListingController@index
GET  /job_listing/create             → JobListingController@create
POST /job_listing                    → JobListingController@store

// Per-CV Screening
GET  /screening/{jobId}              → ScreeningController@index
POST /screening/{cvId}/screen        → ScreeningController@screenSingle
POST /screening/{jobId}/screen-all   → ScreeningController@screenAll

// Results
GET  /matching_results               → MatchingController@results
GET  /matching_history               → MatchingController@index
GET  /candidate/{id}                 → CandidateResumeController@show
```

---

## 🧪 Testing

### Test FastAPI:
```bash
# Health check
curl http://127.0.0.1:8000/health

# Test with curl
curl -X POST "http://127.0.0.1:8000/api/cv/analyze-text" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "cv_text=Sample CV text here" \
  -d "job_description=Job description here" \
  -d "required_skills=PHP,Python,JavaScript" \
  -d "job_title=Full Stack Developer"
```

### Test Laravel:
```bash
# Run tests
php artisan test

# Test specific feature
php artisan test --filter=ScreeningTest
```

---

## 🐛 Troubleshooting

### Error: "Python not found"
**Solusi**: Pastikan `PYTHON_PATH` di `.env` mengarah ke Python executable yang benar:
```env
PYTHON_PATH=c:/laragon/www/CVision/venv/Scripts/python.exe
```

### Error: "AI Engine is not running"
**Solusi**: Start FastAPI server:
```bash
cd python
uvicorn main:app --reload --port 8000
```

### Error: "Gemini API 503 UNAVAILABLE"
**Solusi**: 
- Sistem sudah memiliki rate limiting (2 detik antar request)
- Retry logic (3x percobaan dengan exponential backoff)
- Jika masih error, tunggu beberapa menit dan coba lagi

### Error: "Malformed UTF-8 characters"
**Solusi**: Sudah di-handle dengan UTF-8 cleaning di 3 lapis:
1. `CVScoreService.php` - sebelum kirim ke AI
2. `CVScoreResult.php` - sebelum simpan ke DB
3. `python/main.py` - setelah terima dari Laravel

### Error: "HTTP 422 Unprocessable Entity"
**Solusi**: Pastikan menggunakan `->asForm()` di `GeminiAIService.php` untuk mengirim form data, bukan JSON.

---

## 📝 Catatan Penting

1. **Token Management**: 
   - Screening per-CV menghemat token Gemini
   - Batch screening memiliki jeda 3 detik antar CV
   - Rate limiting: 2 detik minimum antar request Gemini

2. **Caching**:
   - Hasil analisis di-cache selama 1 jam
   - Cache key berdasarkan CV ID + Job ID + file timestamp

3. **Queue Jobs**:
   - Retry: 3 kali percobaan
   - Backoff: 10 detik antar retry
   - Timeout: 120 detik

4. **Security**:
   - `.env` tidak di-Git
   - Gemini API key aman di environment variable
   - CORS hanya untuk development (restrict di production)

---

## 🎯 Roadmap

- [ ] Add user authentication (Google OAuth)
- [ ] Email notifications for screening completion
- [ ] Export results to PDF/Excel
- [ ] Advanced filtering and sorting
- [ ] Multi-language support
- [ ] Docker deployment
- [ ] Unit tests & Integration tests
- [ ] CI/CD pipeline

---

## 👨‍💻 Developer

**CVision** - AI-Powered CV Matching System

---

## 📄 License

This project is proprietary software. All rights reserved.

---

## 🙏 Credits

- **Laravel** - PHP Framework
- **FastAPI** - Python API Framework
- **Sentence-Transformers** - SBERT model
- **scikit-learn** - TF-IDF implementation
- **Google Gemini** - AI recommendations
- **PyMuPDF** - PDF text extraction