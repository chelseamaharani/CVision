## 🧠 IDENTIFIKASI AI DI WEBSITE CVision

### Arsitektur AI: Hybrid (Laravel + Python FastAPI)

```
┌─────────────────────────────────────────────────────────────┐
│                    LARAVEL (PHP)                             │
│  ┌──────────────────────────────────────────────────────┐   │
│  │  GeminiAIService.php                                  │   │
│  │  → HTTP Client ke FastAPI Python backend              │   │
│  │  → Mengirim CV text + job description                 │   │
│  │  → Menerima hasil scoring + rekomendasi               │   │
│  └──────────────────────────────────────────────────────┘   │
│  ┌──────────────────────────────────────────────────────┐   │
│  │  CVScoreService.php                                   │   │
│  │  → Orchestrator: extract PDF → AI → cache → save DB  │   │
│  │  → Fallback rekomendasi jika Gemini gagal             │   │
│  └──────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
                              │ HTTP POST
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                 PYTHON FASTAPI (main.py)                     │
│  ┌──────────────────────────────────────────────────────┐   │
│  │  Endpoint: POST /api/cv/analyze-text                  │   │
│  │  Endpoint: POST /api/cv/analyze (upload PDF)         │   │
│  │  Endpoint: POST /api/cv/generate-resume              │   │
│  │  Endpoint: POST /api/cv/generate-resume-text         │   │
│  │  Endpoint: GET  /health                              │   │
│  └──────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
```

---

### 🔬 AI / MODEL YANG DIGUNAKAN (5 Komponen)

| No | Nama AI/Model | Jenis | Fungsi | File |
|----|--------------|-------|--------|------|
| **1** | **TF-IDF Vectorizer** | NLP / Information Retrieval | Menghitung kemiripan kata kunci antara CV dan job description. Menggunakan n-gram (1-2) + sublinear tf. | `python/services/similarity.py` |
| **2** | **SBERT (Sentence-BERT)** | Deep Learning / Semantic | Menghitung kemiripan semantik/konteks kalimat antara CV dan job description. Model: `all-MiniLM-L6-v2` | `python/services/similarity.py` |
| **3** | **Hybrid Score** | Weighted Fusion | Menggabungkan TF-IDF (50%) + SBERT (50%) menjadi satu skor akhir | `python/services/similarity.py` |
| **4** | **Google Gemini AI** | Large Language Model (LLM) | **3 fungsi:** <br>• Rekomendasi 5 pekerjaan teratas dari CV<br>• Analisis skill gap (skills_present vs skills_missing)<br>• Generate resume terstruktur dari teks CV | `python/services/gemini_client.py` |
| **5** | **Rule-based Extraction** | Heuristic | Ekstrak tahun pengalaman & tingkat pendidikan dari teks CV menggunakan regex | `python/services/text_processor.py` |

---

### 📊 ALUR KERJA AI (End-to-End)

```
CV PDF Upload
     │
     ▼
┌─────────────────┐
│ PDF Extractor   │ ← PyMuPDF (fitz)
│ (extract_pdf)   │
└────────┬────────┘
         │ CV Text
         ▼
┌─────────────────┐
│ Text Processor  │ ← lowercase, hapus simbol, hapus spasi ganda
│ (preprocess)    │
└────────┬────────┘
         │ Clean Text
         ▼
┌──────────────────────────────────────────────────────┐
│              SIMILARITY ENGINE                        │
│  ┌────────────┐  ┌────────────┐  ┌────────────────┐ │
│  │  TF-IDF    │  │   SBERT    │  │   HYBRID       │ │
│  │  (sklearn) │  │(MiniLM-L6) │  │ 0.5*TF + 0.5*SB│ │
│  │  score 0-1 │  │  score 0-1 │  │  → percentage  │ │
│  └────────────┘  └────────────┘  └────────────────┘ │
└──────────────────────────────────────────────────────┘
         │
         ▼
┌──────────────────────────────────────────────────────┐
│              GEMINI AI (LLM)                          │
│  ┌────────────────────┐  ┌────────────────────────┐  │
│  │ Job Recommendations│  │   Skill Gap Analysis   │  │
│  │ → 5 rekomendasi    │  │ → skills_present[]     │  │
│  │ → confidence score │  │ → skills_missing[]     │  │
│  │ → reasoning        │  │ → fit_score            │  │
│  └────────────────────┘  │ → recommendation       │  │
│                          └────────────────────────┘  │
│  ┌────────────────────────────────────────────────┐  │
│  │ Resume Generator (Gemini)                      │  │
│  │ → Extract: nama, email, pengalaman, pendidikan │  │
│  │ → Format JSON terstruktur                      │  │
│  └────────────────────────────────────────────────┘  │
└──────────────────────────────────────────────────────┘
         │
         ▼
┌─────────────────┐
│  Save to DB     │ ← matching_results table
│  + Cache (1 jam)│
└─────────────────┘
```

---

### 📦 TEKNOLOGI YANG DIGUNAKAN

| Komponen | Teknologi |
|----------|-----------|
| **Framework Web** | Laravel 13 (PHP 8.3) + FastAPI (Python) |
| **NLP / Text Similarity** | scikit-learn TfidfVectorizer, Sentence-Transformers |
| **LLM** | Google Gemini 2.0 Flash Lite (`gemini-2.0-flash-lite`) |
| **PDF Extraction** | PyMuPDF (fitz) |
| **Deployment** | Railway (Docker) |
| **Database** | MySQL |

---

## 📋 PREDIKSI PERTANYAAN SIDANG PROPOSAL

### A. Pertanyaan Umum & Latar Belakang

1. **"Apa latar belakang masalah yang mendorong Anda membuat CVision?"**
   - Jawab: Proses rekrutmen manual memakan waktu, CV screening tidak efisien, subjektivitas HR.

2. **"Apa perbedaan CVision dengan ATS (Applicant Tracking System) yang sudah ada?"**
   - Jawab: CVision menggunakan AI (TF-IDF + SBERT + Gemini) untuk scoring otomatis, bukan hanya keyword matching sederhana.

3. **"Siapa target user aplikasi ini?"**
   - Jawab: HRD/Admin (untuk screening) dan Pelamar (untuk upload CV dan lihat hasil).

### B. Pertanyaan tentang AI / Algoritma

4. **"Kenapa menggunakan TF-IDF? Apa kelebihan dan kekurangannya?"**
   - Kelebihan: Sederhana, cepat, cocok untuk keyword matching.
   - Kekurangan: Tidak menangkap sinonim atau konteks semantik.

5. **"Kenapa menggunakan SBERT? Apa bedanya dengan TF-IDF?"**
   - SBERT menangkap makna semantik kalimat, bukan hanya kata kunci. Contoh: "Saya ahli Python" dan "Saya menguasai Python" dianggap mirip oleh SBERT, tapi berbeda oleh TF-IDF.

6. **"Kenapa menggunakan hybrid score (TF-IDF + SBERT)? Tidak pakai salah satu saja?"**
   - TF-IDF bagus untuk keyword spesifik, SBERT bagus untuk konteks. Hybrid menggabungkan keduanya dengan bobot 50:50.

7. **"Apa itu model all-MiniLM-L6-v2? Kenapa memilih model itu?"**
   - Model SBERT yang ringan (80MB) dan cepat, cocok untuk production. Trade-off: akurasi sedikit lebih rendah dari model besar.

8. **"Apa fungsi Google Gemini di sini? Kenapa tidak pakai GPT / LLM lain?"**
   - Gemini untuk rekomendasi pekerjaan dan analisis skill gap. Alasan: free tier murah, API key mudah didapat.

9. **"Bagaimana cara Gemini menghasilkan rekomendasi pekerjaan?"**
   - Prompt engineering: CV text dikirim ke Gemini dengan instruksi spesifik untuk output JSON.

10. **"Apa yang terjadi jika Gemini sedang down atau rate limited?"**
    - Ada fallback: rekomendasi dibuat berdasarkan skill matching saja (tanpa LLM).

### C. Pertanyaan tentang Arsitektur

11. **"Kenapa arsitekturnya Laravel + Python FastAPI? Kenapa tidak semua di PHP?"**
    - Python memiliki library NLP yang matang (scikit-learn, sentence-transformers). PHP tidak punya ekosistem ML yang baik.

12. **"Bagaimana komunikasi antara Laravel dan Python?"**
    - HTTP POST via Laravel Http Client ke endpoint FastAPI.

13. **"Bagaimana cara deployment? Apakah Python backend juga di-deploy?"**
    - Satu Docker container berisi Laravel + Nginx + PHP-FPM. Python FastAPI bisa di service terpisah atau di container yang sama.

### D. Pertanyaan tentang Performa & Akurasi

14. **"Bagaimana cara mengukur akurasi sistem ini?"**
    - Bisa menggunakan precision/recall, atau membandingkan hasil AI dengan penilaian HR manual.

15. **"Berapa lama waktu yang dibutuhkan untuk memproses satu CV?"**
    - Tergantung: TF-IDF cepat (<1 detik), SBERT (~2-3 detik), Gemini (~3-5 detik). Total ~5-10 detik per CV.

16. **"Apa yang terjadi jika CV yang diupload bukan PDF?"**
    - Sistem menolak dengan error 400: "Only PDF files are supported".

17. **"Bagaimana jika CV tidak bisa di-extract text-nya (misalnya scan gambar)?"**
    - Sistem mengembalikan error 422: "No text could be extracted from the PDF".

### E. Pertanyaan tentang Database & Data

18. **"Apa saja tabel yang terlibat dalam proses scoring?"**
    - `users`, `cvs`, `upload_jobs`, `matching_results`.

19. **"Bagaimana cara menyimpan hasil analisis AI?"**
    - Disimpan di tabel `matching_results` dengan kolom: tfidf_score, sbert_score, hybrid_score, match_percentage, skills_matched, skill_gap, dll.

20. **"Apakah ada caching? Kenapa?"**
    - Ya, cache 1 jam untuk CV + job yang sama, agar tidak memanggil AI berulang kali.

### F. Pertanyaan tentang Keamanan & Etika

21. **"Bagaimana keamanan data CV pelamar?"**
    - File disimpan di storage, hanya admin yang bisa akses. Ada autentikasi via Laravel.

22. **"Apakah ada bias dalam AI? Bagaimana mengatasinya?"**
    - TF-IDF dan SBERT bisa bias terhadap bahasa/terminologi tertentu. Perlu diversifikasi data training.

23. **"Apakah data CV dikirim ke pihak ketiga (Gemini API)?"**
    - Ya, teks CV dikirim ke Google Gemini API untuk rekomendasi. Ini perlu dipertimbangkan dari sisi privasi data.

### G. Pertanyaan tentang Pengembangan ke Depan

24. **"Apa rencana pengembangan selanjutnya?"**
    - Contoh: Integrasi dengan LinkedIn API, dukungan file DOCX, multiple language, dashboard analitik.

25. **"Apakah bisa diintegrasikan dengan job portal lain?"**
    - Bisa, karena arsitektur modular. Tinggal tambah endpoint/connector.

26. **"Bagaimana jika ingin menggunakan model AI lain selain Gemini?"**
    - Tinggal ganti implementasi di `GeminiClient` atau buat class baru yang implement `AIService` interface.

---

### 💡 Tips Menjawab Sidang

| Poin | Penjelasan |
|------|-----------|
| **TF-IDF** | "TF-IDF menghitung seberapa penting suatu kata dalam CV dibandingkan job description. Semakin sering kata muncul di CV tapi jarang di dokumen lain, semakin tinggi skornya." |
| **SBERT** | "SBERT mengubah kalimat menjadi vektor (embedding) lalu menghitung cosine similarity. Bisa menangkap sinonim dan konteks." |
| **Hybrid** | "Kami menggabungkan keduanya karena TF-IDF bagus untuk keyword, SBERT bagus untuk konteks. Bobot 50:50." |
| **Gemini** | "Gemini digunakan untuk rekomendasi pekerjaan yang lebih natural dan analisis skill gap. Output berupa JSON yang sudah diparsing." |
| **Fallback** | "Jika Gemini gagal, sistem tetap bisa memberikan rekomendasi berbasis skill matching agar tidak gagal total." |


## Langkah-langkah di Railway Dashboard:

1. Buka **Railway Dashboard** → Project **CVision**
2. Klik tab **Deployments**
3. Pilih deployment yang **Active / Running** (bukan queued)
4. Klik tombol **"Shell"** atau **"Console"** (icon `>_` biasanya di kanan atas)
5. Di terminal yang muncul, jalankan:

```bash
php artisan migrate --force
```

6. Tunggu sampai selesai — akan terlihat output:
   - ✅ `2026_07_21_181954_add_google_id_to_users_table ............................ DONE`
   - ✅ `2026_07_22_000001_create_sessions_table ................................. DONE`

7. Setelah selesai, coba akses:
   ```
   https://cvision.up.railway.app/auth/google
   ```

## Yang sudah saya siapkan di codebase (untuk setelah commit queue terproses):
- ✅ **Dockerfile** — `--no-scripts` dihapus, Socialite provider akan terdaftar otomatis
- ✅ **Dockerfile** — `php artisan migrate --force` ditambahkan di startup script
- ✅ **Migration session table** — untuk `SESSION_DRIVER=database` di Railway

Setelah migration berhasil dijalankan, error `Unknown column 'google_id'` akan teratasi dan Google Login seharusnya berfungsi normal.




# 📋 Rencana Deployment CVision ke Railway

# perubahan 1 - !

# ✅ Tahap 1 Selesai: Persiapan File Deployment

Dokumentasi perubahan yang telah dilakukan:

## 📁 File Baru Dibuat

| File | Keterangan |
|---|---|
| **`Dockerfile`** | Multi-stage Docker build untuk Laravel 13 + PHP 8.3 FPM + Nginx. Stage 1 build assets & optimize, Stage 2 production minimal. |
| **`nginx.conf`** | Konfigurasi Nginx untuk Laravel production: static cache, security headers, gzip, deny sensitive files, timeout 300s untuk Gemini API |
| **`python/Dockerfile`** | Docker untuk Python FastAPI + SBERT. Python 3.11 slim, healthcheck dengan 60s start-period (loading model SBERT ~80MB) |
| **`railway.json`** | Konfigurasi Railway Laravel: Dockerfile builder, restart on failure |
| **`python/railway.json`** | Konfigurasi Railway Python: healthcheck path `/health`, timeout 100s |
| **`.dockerignore`** | Filter file tidak perlu ke Docker (.git, vendor, node_modules, .env, docs, dll) |

## ✏️ File Dimodifikasi

| File | Perubahan |
|---|---|
| **`python/main.py`** | 3 perubahan penting: |
| | 1. **Logging dipindah ke atas** — agar `logger` siap sebelum dipakai di blok env loading |
| | 2. **CORS production** — ganti dari `allow_origins=["*"]` menjadi `[LARAVEL_URL, localhost:8080, localhost:3000]` via env `LARAVEL_URL` |
| | 3. **Env loading graceful** — `load_dotenv` hanya jalan jika file `.env` ada, tidak raise error di Railway. GEMINI_API_KEY warning jika tidak ada di production |

## 🔑 APP_KEY Tergenerate

```
base64:sLiOjpnWWxoIYEPFXcyENX4zaWmR5qAZnyb54f6c1Yw=
```

## 📋 Checklist Tahap 1

| Task | Status |
|---|---|
| 1.1 Generate APP_KEY | ✅ `base64:sLiOjpnWWxoIYEPFXcyENX4zaWmR5qAZnyb54f6c1Yw=` |
| 1.2 Buat Dockerfile Laravel | ✅ Multi-stage build, Nginx + PHP 8.3 FPM |
| 1.3 Buat nginx.conf | ✅ Production-ready Nginx config |
| 1.4 Buat Dockerfile Python | ✅ Python 3.11 + FastAPI + SBERT |
| 1.5 Buat railway.json (Laravel) | ✅ Dockerfile builder config |
| 1.6 Buat railway.json (Python) | ✅ With healthcheck |
| 1.7 Buat .dockerignore | ✅ Optimal untuk build |
| 1.8 Update .gitignore | ✅ Tidak ada file deployment yang di-ignore |
| 2.1 Modifikasi CORS Python | ✅ Batasi origin production |
| 2.2 Modifikasi env loading | ✅ Graceful loading tanpa .env file |

**Total**: 8 file baru dibuat + 1 file dimodifikasi ✅

Silakan konfirmasi apakah sudah sesuai. Jika sudah OK, kita lanjut ke **Tahap 2: Setup Railway & Deploy Laravel** (push ke GitHub, login Railway CLI, init project, deploy, set env variables).

# Tahap 2 Perubahan - !

# ✅ Tahap 2 Selesai! Laravel Service Deployed 🎉

## Status Terkini:

| Item | Status | Detail |
|---|---|---|
| **Railway CLI** | ✅ Installed | v5.27.0 |
| **Login Railway** | ✅ Berhasil | Akun chelseamaharani |
| **Project** | ✅ CVision | https://railway.com/project/d6483b18-a1f3-44c9-a109-795d89dec249 |
| **Laravel Service** | ✅ **Online** | https://cvision-production-6a2c.up.railway.app |
| **Dockerfile Fix** | ✅ 3x iteration | Memory limit → Vite native binding → Success |
| **Environment Variables** | ✅ Set | APP_KEY, APP_ENV, APP_DEBUG, SESSION_DRIVER, CACHE_STORE |

## 🔧 Root Cause Build Failure (sebelumnya):

1. **Memory limit** — `docker-php-ext-install -j$(nproc)` pakai semua CPU thread, di-fix dengan `-j1` 
2. **Vite 8 + rolldown** — Flag `--ignore-scripts` mencegah install native binary `@rolldown/binding-linux-x64-musl` untuk Alpine
3. **package-lock.json** — Tidak ada di repo, `npm ci` gagal. Digenerate & di-commit

## 📋 Langkah Selanjutnya — Tahap 3: Setup MySQL Database

Yang akan dilakukan:
1. Add MySQL service dari Railway dashboard
2. Copy credentials ke Laravel env variables
3. Set `DB_CONNECTION=mysql`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
4. Run migration: `railway run php artisan migrate`

**Konfirmasi** apakah ingin lanjut ke **Tahap 3: Setup MySQL Database**?

# Prubahan Tahap 3 - !

Successfully deployed Laravel service to Railway and configured MySQL database connection.

## ✅ Stage 3 Complete: MySQL Database Setup

- [x] MySQL service is online
- [x] Set all DB environment variables (DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD)
- [x] Laravel service is running and ready for database operations

## Ready for Stage 4: Deploy Python AI Engine
1. Create Python service on Railway (connected to GitHub repo)
2. Set root directory to `/python`
3. Configure environment variables (GEMINI_API_KEY, LARAVEL_URL)
4. Deploy and verify health endpoint

Next step is to create the Python service from the Railway dashboard or CLI. Want me to proceed?

# Python service 

## Langkah-langkah Membuat Python Service di Railway

Berikut langkah lengkap untuk membuat dan deploy Python AI Engine:

### 1️⃣ Buka Railway Dashboard

Buka URL berikut di browser:
```
https://railway.com/project/d6483b18-a1f3-44c9-a109-795d89dec249
```

### 2️⃣ Buat Service Baru

1. Klik tombol **"New"** (biasanya di pojok kanan atas)
2. Pilih **"GitHub Repo"**
3. Jika diminta授权, pilih repository **`chelseamaharani/CVision`**
4. Klik **"Connect"** atau **"Add to Project"**

### 3️⃣ Konfigurasi Service

Setelah service dibuat, lakukan konfigurasi:

**Di menu service settings:**

1. **Service Name** (opsional): `cvision-python` atau biarkan default
2. **Root Directory**: Ketik `/python` (penting! agar Railway pakai `python/Dockerfile`)
3. **Builder**: Pastinya **"Dockerfile"** (auto-detect)

### 4️⃣ Set Environment Variables

Setelah service terbuat, klik **"Variables"** tab dan tambahkan:

| Variable | Value | Keterangan |
|---|---|---|
| `GEMINI_API_KEY` | *(API key Google Gemini Anda)* | Wajib untuk AI features |
| `LARAVEL_URL` | `https://cvision-production-6a2c.up.railway.app` | Untuk CORS configuration |

**Cara menambahkan:**
- Klik **"+ New Variable"**
- Masukkan nama variable dan value
- Klik **"Add"**
- Ulangi untuk kedua variable

### 5️⃣ Deploy & Tunggu Build

Railway akan otomatis mulai build dan deploy. Proses ini memakan waktu **5-10 menit** karena:
- Download dependencies Python
- Download SBERT model (~80MB)
- Build Docker image

**Pantau progress:**
- Klik tab **"Deployments"** untuk melihat build log
- Lihat **"Logs"** tab untuk real-time output

### 6️⃣ Verifikasi Health Check

Setelah deploy selesai (status **"Active"**), test health endpoint:

**Dari terminal lokal:**
```bash
railway run --service cvision-python curl http://localhost:8080/health
```

Atau dari browser, buka URL service (akan muncul di dashboard setelah deploy).

### 7️⃣ Link Python URL ke Laravel

Setelah Python service online, copy URL-nya (biasanya format `cvision-python-production-xxx.up.railway.app`), lalu set di Laravel:

```bash
railway variables --set PYTHON_AI_URL=https://cvision-python-production-xxx.up.railway.app --service CVision
```

### 8️⃣ Troubleshooting Jika Build Gagal

**Error: Memory exceeded**
- Klik service → **"Settings"** → **"Resources"**
- Naikkan RAM ke **1GB** (tambah ~$0.17/minggu)

**Error: Port already in use**
- Cek `python/railway.json` sudah ada `"healthcheckPath": "/health"`
- Pastikan `CMD` di Dockerfile pakai `--workers 1`

**Error: SBERT model download timeout**
- Railway otomatis retry, tunggu saja
- Jika gagal terus, cek koneksi internet Railway (biasanya stabil)

### ✅ Checklist Setelah Python Deploy

- [ ] Python service status: **Active**
- [ ] Health endpoint return 200 OK
- [ ] `GEMINI_API_KEY` dan `LARAVEL_URL` sudah diset
- [ ] `PYTHON_AI_URL` sudah di-set di Laravel
- [ ] Test manual: Panggil endpoint `/health` dan `/api/test`

---

**Siap dilanjutkan ke Stage 5 (File Storage Volume) setelah Python service berhasil deploy.**

## 📊 Ringkasan Proyek

| Item | Detail |
|---|---|
| **Nama Proyek** | CVision - CV Matching & Analysis System |
| **Web Framework** | Laravel 13 (PHP 8.3+) + Blade + Tailwind + Vite |
| **AI Engine** | Python FastAPI + SBERT + TF-IDF + Gemini AI |
| **Database** | MySQL (via Railway MySQL add-on) |
| **Target Platform** | Railway.app |
| **Budget** | $5 Free Plan (saldo awal) |
| **Estimasi Pemakaian** | 1 Minggu |

---

## 💰 Kalkulasi Biaya Railway ($5 Free Plan)

### Harga Resource Railway (per 2026)

| Resource | Harga |
|---|---|
| CPU (per vCPU/jam) | $0.002/jam |
| RAM (per GB/jam) | $0.002/jam |
| Disk (per GB/bulan) | $0.10/GB |
| Network Egress | $0.10/GB |
| MySQL Add-on (Starter) | $5/bulan (pro-rata) |

### Skenario Biaya 1 Minggu (168 jam)

#### Opsi A: Laravel + Python + MySQL (Rekomendasi ✅)

| Service | CPU | RAM | Biaya/Minggu |
|---|---|---|---|
| **Laravel Web** | 0.5 vCPU | 512 MB | ~$0.34 |
| **Python AI Engine** | 1 vCPU | 1 GB | ~$1.01 |
| **MySQL Database** | Starter ($5/bln pro-rata) | | ~$1.25 |
| **Disk (2GB)** | | | ~$0.05 |
| **Egress Network** | | | ~$0.10 |
| **TOTAL** | | | **~$2.75** ✅ |

> **Sisa budget**: **$2.25** — masih cukup untuk development/test/traffic tambahan

#### Opsi B: Tanpa MySQL (Pakai SQLite — Tidak Disarankan)

| Service | Biaya/Minggu |
|---|---|
| Laravel Web | ~$0.34 |
| Python AI Engine | ~$1.01 |
| **TOTAL** | **~$1.35** |

> ⚠️ SQLite tidak cocok untuk production karena tidak support concurrent writes dan scaling.

### ⚠️ Catatan Penting: SBERT Model Size

**sentence-transformers (all-MiniLM-L6-v2)** memiliki ukuran ~80MB saat di-download pertama kali. Proses download terjadi saat startup Python service. Ini menyebabkan:

- **Build time lebih lama** (bisa 3-5 menit)
- **Memory spike** saat loading model (~500MB - 1GB RAM)
- **Disk usage** ~200MB untuk model + dependencies

**Solusi**: Alokasikan **1 vCPU + 1GB RAM** untuk Python service agar loading model stabil.

### 💡 Tips Hemat Budget

1. **Matikan service saat tidak dipakai** — Railway tidak charge saat service di-stop
2. **Gunakan Railway CLI** untuk start/stop otomatis
3. **Pantau usage** di dashboard Railway secara berkala
4. **Jika budget menipis**, turunkan Python ke 0.5 vCPU + 512MB (resiko startup failure)

---

## 🏗️ Arsitektur Deployment

```
┌─────────────────────────────────────────────────────────────┐
│                      Railway Platform                        │
│                                                              │
│  ┌─────────────────────┐    ┌─────────────────────┐          │
│  │   Laravel Service   │    │  Python AI Engine   │          │
│  │   (PHP 8.3 + Nginx) │    │  (FastAPI + SBERT)  │          │
│  │   Port: 80          │    │  Port: 8080        │          │
│  │   Domain: cvision-  │    │  Domain: cvision-   │          │
│  │   laravel.railway   │    │  python.railway     │          │
│  └────────┬────────────┘    └──────────┬──────────┘          │
│           │                            │                     │
│           ▼                            ▼                     │
│  ┌─────────────────────────────────────────────┐             │
│  │         MySQL Database (Railway)             │             │
│  │         Port: 3306                           │             │
│  └─────────────────────────────────────────────┘             │
│                                                              │
│  ┌─────────────────────────────────────────────┐             │
│  │         Railway Volume (File Storage)        │             │
│  │         Untuk upload CV files                │             │
│  └─────────────────────────────────────────────┘             │
└─────────────────────────────────────────────────────────────┘
```

---

## 🔧 File-file yang Perlu Dibuat

### 1. `Dockerfile` (Root — Untuk Laravel)

```dockerfile
# ============================================================
# Dockerfile — Laravel 13 + PHP 8.3 + Nginx
# ============================================================

FROM php:8.3-fpm-alpine AS builder

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    git \
    unzip \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    libxml2-dev \
    zip \
    nodejs \
    npm \
    mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        intl \
    && rm -rf /var/cache/apk/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy application files
COPY . .

# Install PHP dependencies (production only)
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-scripts \
    && composer dump-autoload --optimize

# Install & build frontend assets
RUN npm ci --ignore-scripts && npm run build && rm -rf node_modules

# Laravel optimization
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && php artisan event:cache

# Set permissions
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache \
    && chmod -R 775 /app/storage /app/bootstrap/cache

# ============================================================
# Production stage
# ============================================================
FROM php:8.3-fpm-alpine

RUN apk add --no-cache nginx curl && \
    docker-php-ext-install pdo pdo_mysql

WORKDIR /app

# Copy from builder
COPY --from=builder /app /app
COPY --from=builder /usr/bin/composer /usr/bin/composer

# Copy Nginx configuration
COPY nginx.conf /etc/nginx/nginx.conf

# Create storage link
RUN php artisan storage:link || true

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=3s --start-period=10s --retries=3 \
    CMD curl -f http://localhost/health || exit 1

CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]
```

### 2. `nginx.conf` (Root — Untuk Laravel)

```nginx
worker_processes auto;
error_log /dev/stderr warn;
pid /tmp/nginx.pid;

events {
    worker_connections 1024;
    multi_accept on;
}

http {
    include /etc/nginx/mime.types;
    default_type application/octet-stream;

    # Logging
    log_format main '$remote_addr - $remote_user [$time_local] "$request" '
                    '$status $body_bytes_sent "$http_referer" '
                    '"$http_user_agent" "$http_x_forwarded_for"';
    access_log /dev/stdout main;
    error_log /dev/stderr warn;

    # Performance
    sendfile on;
    tcp_nopush on;
    tcp_nodelay on;
    keepalive_timeout 65;
    types_hash_max_size 2048;
    client_max_body_size 20M;

    # Gzip
    gzip on;
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_types text/plain text/css text/xml application/json application/javascript application/xml+rss application/atom+xml image/svg+xml;

    server {
        listen 80;
        server_name _;
        root /app/public;
        index index.php;

        # Security headers
        add_header X-Frame-Options "SAMEORIGIN" always;
        add_header X-Content-Type-Options "nosniff" always;
        add_header X-XSS-Protection "1; mode=block" always;

        # Static files cache
        location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
            expires 1y;
            add_header Cache-Control "public, immutable";
            try_files $uri /index.php?$query_string;
        }

        # Laravel entry point
        location / {
            try_files $uri $uri/ /index.php?$query_string;
        }

        # PHP-FPM
        location ~ \.php$ {
            fastcgi_pass 127.0.0.1:9000;
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
            include fastcgi_params;
            fastcgi_param HTTPS off;
            fastcgi_param APP_ENV production;
            fastcgi_read_timeout 300;
            fastcgi_send_timeout 300;
        }

        # Deny hidden files
        location ~ /\.(?!well-known).* {
            deny all;
            access_log off;
            log_not_found off;
        }

        # Deny sensitive files
        location ~ (\.env|\.git|composer\.json|composer\.lock|artisan)$ {
            deny all;
            access_log off;
            log_not_found off;
        }
    }
}
```

### 3. `Dockerfile` (di folder `/python/` — Untuk Python FastAPI + SBERT)

```dockerfile
# ============================================================
# Dockerfile — Python FastAPI AI Engine
# ============================================================

# Gunakan Python 3.11 slim untuk ukuran lebih kecil
FROM python:3.11-slim

# Set environment variables
ENV PYTHONDONTWRITEBYTECODE=1 \
    PYTHONUNBUFFERED=1 \
    DEBIAN_FRONTEND=noninteractive

# Install system dependencies untuk numpy, scikit-learn, dll
RUN apt-get update && apt-get install -y --no-install-recommends \
    gcc \
    g++ \
    build-essential \
    libffi-dev \
    libssl-dev \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /app

# Copy requirements first (cache layer)
COPY python/requirements.txt .
RUN pip install --no-cache-dir --upgrade pip && \
    pip install --no-cache-dir -r requirements.txt

# Copy Python application code
COPY python/ .

# Expose port
EXPOSE 8080

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=60s --retries=3 \
    CMD curl -f http://localhost:8080/health || exit 1

# Run with uvicorn
# Gunakan --workers 1 karena SBERT model tidak thread-safe
CMD ["uvicorn", "main:app", "--host", "0.0.0.0", "--port", "8080", "--workers", "1", "--timeout-keep-alive", "120"]
```

### 4. `railway.json` (Root)

```json
{
  "$schema": "https://railway.app/railway.schema.json",
  "build": {
    "builder": "DOCKERFILE",
    "dockerfilePath": "Dockerfile"
  },
  "deploy": {
    "numReplicas": 1,
    "restartPolicyType": "ON_FAILURE",
    "restartPolicyMaxRetries": 3
  }
}
```

### 5. `.dockerignore` (Root)

```
# Git
.git
.gitignore
.gitattributes

# Dependencies
node_modules/
vendor/
composer.lock
package-lock.json

# Environment
.env
.env.backup
.env.production
.env.example

# Cache & logs
storage/framework/cache/*
storage/framework/views/*
storage/framework/sessions/*
storage/logs/*
!storage/framework/.gitkeep
!storage/logs/.gitkeep

# Python
__pycache__/
*.py[cod]
*$py.class
*.so
.venv/
venv/
env/
ENV/
*.egg-info/
dist/
build/
*.egg

# Database
*.sqlite
*.db

# Tests
tests/
phpunit.xml
.phpunit.result.cache

# Docs
docs/
*.md
README.md

# IDE
.idea/
.vscode/
*.swp
*.swo

# OS
.DS_Store
Thumbs.db
desktop.ini

# Build artifacts
public/build/*
!public/build/.gitkeep
public/hot
```

### 6. `railway.json` (di folder `/python/` — Untuk Python Service)

```json
{
  "$schema": "https://railway.app/railway.schema.json",
  "build": {
    "builder": "DOCKERFILE",
    "dockerfilePath": "Dockerfile"
  },
  "deploy": {
    "numReplicas": 1,
    "restartPolicyType": "ON_FAILURE",
    "restartPolicyMaxRetries": 3,
    "healthcheckPath": "/health",
    "healthcheckTimeout": 100
  }
}
```

---

## 🔧 Perubahan Code yang Diperlukan

### A. Laravel — Konfigurasi Environment

Pastikan file `config/database.php` sudah support MySQL via environment variable:

```php
// config/database.php (sudah support, hanya perlu setting env)
'mysql' => [
    'driver' => 'mysql',
    'url' => env('DB_URL'),
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '3306'),
    'database' => env('DB_DATABASE', 'laravel'),
    'username' => env('DB_USERNAME', 'root'),
    'password' => env('DB_PASSWORD', ''),
    'unix_socket' => env('DB_SOCKET', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'prefix_indexes' => true,
    'strict' => true,
    'engine' => null,
],
```

### B. Python — Konfigurasi CORS & Environment

File `python/main.py` perlu dimodifikasi untuk:

1. **CORS** — Batasi origin ke domain Laravel di production
2. **Environment loading** — Support Railway env variables (tidak hanya `.env` file)

```python
# Di python/main.py, ubah bagian CORS:
app.add_middleware(
    CORSMiddleware,
    allow_origins=[
        os.getenv("LARAVEL_URL", "http://localhost:8000"),  # Domain Laravel
    ],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Ubah bagian load_dotenv agar tidak crash jika .env tidak ada:
load_dotenv(ROOT_DIR / ".env", override=False)  # Tidak raise error jika file tidak ada
```

### C. File Storage untuk Upload CV

Railway tidak menyediakan persistent filesystem secara default. Untuk upload CV:

**Opsi 1: Railway Volume (Rekomendasi)**
- Buat Volume di Railway dashboard
- Mount ke path `/app/storage/app/public`
- Set `FILESYSTEM_DISK=local` di env

**Opsi 2: External Storage (S3-compatible)**
- Gunakan Backblaze B2, Cloudflare R2, atau AWS S3
- Install package `league/flysystem-aws-s3-v3`
- Set `FILESYSTEM_DISK=s3`

---

## 📝 Langkah Pengerjaan Detail

### ✅ Tahap 1: Persiapan File Deployment

| # | Task | Detail | Command/File |
|---|---|---|---|
| 1.1 | Generate APP_KEY | Generate key untuk Laravel | `php artisan key:generate --show` |
| 1.2 | Buat Dockerfile Laravel | Di root project | `Dockerfile` |
| 1.3 | Buat nginx.conf | Konfigurasi Nginx | `nginx.conf` |
| 1.4 | Buat Dockerfile Python | Di folder `python/` | `python/Dockerfile` |
| 1.5 | Buat railway.json (Laravel) | Di root project | `railway.json` |
| 1.6 | Buat railway.json (Python) | Di folder `python/` | `python/railway.json` |
| 1.7 | Buat .dockerignore | Filter file untuk Docker | `.dockerignore` |
| 1.8 | Update .gitignore | Pastikan file deployment tidak di-ignore | `.gitignore` |

### ✅ Tahap 2: Setup Railway & Deploy Laravel

| # | Task | Detail | Command |
|---|---|---|---|
| 2.1 | Push code ke GitHub | Pastikan semua file sudah di-commit | `git add . && git commit -m "Add deployment files" && git push` |
| 2.2 | Login Railway CLI | Authentikasi dengan akun GitHub | `railway login` |
| 2.3 | Init project Railway | Buat project baru | `railway init` |
| 2.4 | Deploy Laravel service | Deploy ke Railway | `railway up` |
| 2.5 | Set environment variables | Konfigurasi env di dashboard Railway | Lihat tabel env di bawah |
| 2.6 | Test akses Laravel | Cek apakah Laravel bisa diakses | Buka URL `.railway.app` |

### ✅ Tahap 3: Setup MySQL Database

| # | Task | Detail | Command |
|---|---|---|---|
| 3.1 | Add MySQL service | Dari Railway dashboard → New → Database → MySQL | Dashboard UI |
| 3.2 | Copy connection string | Railway akan generate `MYSQL_URL` | Copy ke Laravel env |
| 3.3 | Set DB env variables | Update Laravel env dengan koneksi MySQL | Lihat tabel env |
| 3.4 | Run migration | Jalankan migration dari Railway shell | `railway run php artisan migrate` |

### ✅ Tahap 4: Deploy Python AI Engine

| # | Task | Detail | Command |
|---|---|---|---|
| 4.1 | Create Python service | Dashboard → New Service → GitHub repo | Dashboard UI |
| 4.2 | Set root directory | Set ke `/python` agar pakai Dockerfile di sana | Dashboard UI |
| 4.3 | Set env variables Python | GEMINI_API_KEY, LARAVEL_URL | Dashboard UI |
| 4.4 | Deploy & wait for build | Build akan lebih lama karena download SBERT model | `railway up` atau auto-deploy |
| 4.5 | Test health endpoint | Cek apakah Python service berjalan | `curl https://python-url.railway.app/health` |
| 4.6 | Link Python URL ke Laravel | Set `PYTHON_AI_URL` di Laravel env | Dashboard UI |

### ✅ Tahap 5: Finalisasi & Testing

| # | Task | Detail |
|---|---|---|
| 5.1 | Setup Railway Volume | Buat volume untuk file upload CV |
| 5.2 | Full integration test | Upload CV, matching, resume generation |
| 5.3 | Test error handling | Pastikan error handling berfungsi |
| 5.4 | Monitor logs | Cek Railway logs untuk issues |
| 5.5 | (Opsional) Custom domain | Setup domain jika punya |

---

## 📋 Environment Variables

### Laravel Service

| Variable | Value | Keterangan |
|---|---|---|
| `APP_KEY` | `base64:...` | Generate dengan `php artisan key:generate --show` |
| `APP_ENV` | `production` | Mode production |
| `APP_DEBUG` | `false` | Matikan debug |
| `APP_URL` | `https://cvison-laravel.up.railway.app` | URL Laravel |
| `DB_CONNECTION` | `mysql` | Gunakan MySQL |
| `DB_HOST` | Dari Railway MySQL | Host MySQL |
| `DB_PORT` | `3306` | Port MySQL |
| `DB_DATABASE` | `railway` | Nama database |
| `DB_USERNAME` | Dari Railway MySQL | Username |
| `DB_PASSWORD` | Dari Railway MySQL | Password |
| `GEMINI_API_KEY` | `your-gemini-api-key` | API Key Google Gemini |
| `PYTHON_AI_URL` | `https://cvison-python.up.railway.app` | URL Python service |
| `FILESYSTEM_DISK` | `local` | File storage disk |
| `SESSION_DRIVER` | `database` | Session driver |
| `CACHE_STORE` | `database` | Cache store |

### Python Service

| Variable | Value | Keterangan |
|---|---|---|
| `GEMINI_API_KEY` | `your-gemini-api-key` | API Key Google Gemini |
| `LARAVEL_URL` | `https://cvison-laravel.up.railway.app` | Untuk CORS |

---

## ⚠️ Potensi Masalah & Solusi

| Masalah | Penyebab | Solusi |
|---|---|---|
| **Build timeout** | SBERT model download lama | Naikkan build timeout di Railway settings |
| **Memory error saat startup Python** | SBERT model butuh ~1GB RAM | Alokasikan 1GB RAM untuk Python service |
| **MySQL connection refused** | Service order salah | Pastikan MySQL sudah running sebelum Laravel |
| **File upload gagal** | Storage tidak persistent | Setup Railway Volume atau S3 storage |
| **CORS error** | Origin tidak cocok | Set `LARAVEL_URL` dengan benar di Python env |
| **Session tidak bekerja** | Session driver tidak sesuai | Set `SESSION_DRIVER=database` dan jalankan `php artisan session:table` |
| **Gemini API timeout** | Network latency | Set timeout lebih panjang di Laravel config |
| **500 error setelah deploy** | Cache lama | Jalankan `php artisan optimize:clear` via Railway shell |

---

## 📈 Monitoring & Maintenance

### Railway Dashboard
- **Logs**: Real-time logs untuk setiap service
- **Metrics**: CPU, RAM, Network usage
- **Deployments**: Riwayat deployment & rollback
- **Domains**: Manage custom domains & SSL

### Commands Berguna

```bash
# Railway CLI
railway login                    # Login ke Railway
railway init                     # Init project
railway up                       # Deploy
railway status                   # Cek status
railway logs                     # Lihat logs
railway run "php artisan migrate"  # Run command di service
railway domain                   # Lihat domain
railway volume                   # Manage volumes

# Laravel (via Railway shell)
railway run php artisan migrate        # Run migration
railway run php artisan optimize:clear # Clear cache
railway run php artisan storage:link   # Link storage
```

---

## 🎯 Kesimpulan

✅ **$5 Free Plan MUMPUNI untuk 1 minggu** dengan total estimasi biaya **~$2.75** (termasuk MySQL).

✅ **Sisa budget**: **$2.25** — cukup untuk development, testing, dan traffic tambahan.

✅ **Arsitektur 3 service**: Laravel Web + Python AI Engine + MySQL Database.

✅ **Dockerfile sudah dioptimasi** untuk production dengan multi-stage build.

---

## 📌 Catatan Tambahan

1. **Railway Free Plan** memberikan $5 credit setiap bulan (bukan sekali). Jadi bulan depan akan dapat $5 lagi.
2. **Matikan service** saat tidak digunakan untuk menghemat budget.
3. **Jika build gagal** karena memory, upgrade Python service ke 2GB RAM (tambah ~$0.17/minggu).
4. **Untuk production jangka panjang**, pertimbangkan upgrade ke plan yang lebih tinggi.
5. **Backup database** secara berkala menggunakan Railway backup feature.

---

*Dokumen ini dibuat pada: 19 Juli 2026*
*Versi: 1.0*