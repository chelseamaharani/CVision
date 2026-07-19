# Troubleshooting Guide - CVision

## Alur Aplikasi

### User Flow (Pelamar)
```
1. User buka landing page (/) — TANPA login
2. User upload CV + pilih job
3. Sistem extract PDF → analyze dengan AI Engine
4. Hasil: Match % + Job Recommendations + Skill Gap
5. User lihat history CV di tab "History"
```

### HRD Flow
```
1. HRD login ke dashboard
2. HRD create job posting
3. HRD screening CVs
4. Sistem tampilkan: TF-IDF, SBERT, Hybrid scores + Rank
5. HRD lihat candidate detail + resume terstruktur
```

### Technical Flow
```
Laravel (PHP)                    Python AI Engine (FastAPI)
    │                                    │
    │  POST /api/cv/analyze             │
    │  {cv_file, job_description}       │
    ├───────────────────────────────────>│
    │                                    │ 1. Extract PDF text
    │                                    │ 2. Calculate TF-IDF
    │                                    │ 3. Calculate SBERT
    │                                    │ 4. Calculate Hybrid
    │                                    │ 5. Gemini recommendations
    │                                    │ 6. Return JSON
    │<───────────────────────────────────┤
    │  {scores, recommendations}         │
    │                                    │
    │  Save to database                  │
    │  Calculate rank                    │
    │                                    │
    ▼                                    ▼
Display to user
```

---

## Common Errors & Solutions

### ❌ Error 1: UnicodeEncodeError

**Error Message:**
```
UnicodeEncodeError: 'charmap' codec can't encode character '\uf0b7' in position 1137
```

**Penyebab:**
- CV PDF mengandung karakter Unicode (bullet points, special chars)
- Python logging mencoba print ke Windows console
- Windows console default encoding **cp1252** (bukan UTF-8)
- cp1252 tidak bisa handle karakter di Private Use Area

**Solusi:**
1. **Gunakan batch file:**
   ```bash
   python/run_with_utf8.bat
   ```

2. **Atau set environment variable:**
   ```bash
   # PowerShell
   $env:PYTHONIOENCODING="utf-8"
   cd python
   uvicorn main:app --reload --port 8000
   ```

3. **Atau ganti code page:**
   ```bash
   chcp 65001
   uvicorn main:app --reload --port 8000
   ```

**Verifikasi:**
- ✅ Tidak ada UnicodeEncodeError di console
- ✅ Upload CV berhasil
- ✅ Scores muncul (TF-IDF, SBERT, Hybrid)

---

### ❌ Error 2: RouteNotFoundException

**Error Message:**
```
Symfony\Component\Routing\Exception\RouteNotFoundException
```

**Penyebab:**
- View menggunakan route yang tidak ada di `routes/web.php`
- Contoh: `route('auth.google')` — route ini sudah dihapus

**Solusi:**
1. Cek view yang error (lihat error message)
2. Cek `routes/web.php` — pastikan route exists
3. Jika route sudah dihapus, hapus juga dari view

**Yang sudah diperbaiki:**
- ✅ `login.blade.php` — tombol Google Auth dihapus
- ✅ `register.blade.php` — tombol Google Auth dihapus

**Verifikasi:**
```bash
php artisan route:clear
php artisan view:clear
```

---

### ❌ Error 3: Gemini API Gagal

**Error Message:**
```
Gemini API call failed after 3 attempts: 503 UNAVAILABLE
```

**Penyebab:**
- API key salah atau expired
- Quota Gemini habis
- Internet connection issues

**Solusi:**
1. Cek `.env`:
   ```env
   GEMINI_API_KEY=your_api_key_here
   ```

2. Cek quota di [Google AI Studio](https://aistudio.google.com/)

3. Test koneksi:
   ```bash
   curl http://127.0.0.1:8000/health
   ```

**Verifikasi:**
- ✅ Health check returns `{"status": "healthy"}`
- ✅ Gemini API key valid
- ✅ Internet connection stabil

---

### ❌ Error 4: Queue Worker Tidak Memproses CV

**Error Message:**
```
CV uploaded successfully! AI analysis is in progress.
(Tapi tidak ada hasil setelah lama)
```

**Penyebab:**
- Queue worker tidak berjalan: `php artisan queue:work`
- Atau queue connection salah di `.env`

**Solusi:**
1. **Jalankan queue worker:**
   ```bash
   php artisan queue:work
   ```

2. **Atau gunakan fallback synchronous** (sudah diimplementasikan):
   - Jika queue gagal, otomatis proses synchronous
   - Tidak perlu queue worker untuk development

**Verifikasi:**
- ✅ Cek `storage/logs/laravel.log` — ada log "CV analysis complete"
- ✅ Database `matching_results` terisi

---

### ❌ Error 5: PDF Extraction Gagal

**Error Message:**
```
Failed to extract text from PDF
```

**Penyebab:**
- PyMuPDF tidak terinstall
- Atau PHP PDF Parser tidak terinstall

**Solusi:**
1. **Install PyMuPDF (Python):**
   ```bash
   cd python
   pip install pymupdf
   ```

2. **Atau install PHP PDF Parser:**
   ```bash
   composer require smalot/pdfparser
   ```

**Verifikasi:**
- ✅ Upload PDF berhasil
- ✅ Teks CV ter-extract dengan benar

---

## Testing Checklist

### Pre-Testing
- [ ] Python AI Engine running di port 8000
- [ ] Laravel running di port 80/8000
- [ ] Database migrated: `php artisan migrate`
- [ ] Test data seeded: `php artisan db:seed --class=TestDataSeeder`
- [ ] `.env` configured (GEMINI_API_KEY, AI_ENGINE_URL)

### User Flow Testing
- [ ] Buka landing page (`/`) — tanpa login
- [ ] Upload CV + pilih job
- [ ] Loading spinner muncul
- [ ] Setelah 5-10 detik, redirect ke history
- [ ] Match % muncul
- [ ] Job recommendations muncul (TOP 5)
- [ ] Skill gap analysis muncul

### HRD Flow Testing
- [ ] Login sebagai admin (`admin@cvision.test` / `password`)
- [ ] Buka dashboard
- [ ] Create job posting
- [ ] Buka screening page
- [ ] Upload CV untuk screening
- [ ] TF-IDF score muncul
- [ ] SBERT score muncul
- [ ] Hybrid score muncul
- [ ] Rank terisi (1, 2, 3, ...)
- [ ] Skills total/count terisi

### Error Monitoring
- [ ] Cek `storage/logs/laravel.log` — tidak ada error
- [ ] Cek Python console — tidak ada UnicodeEncodeError
- [ ] Cek database — data terisi dengan benar

---

## Debugging Commands

### Laravel
```bash
# Clear all cache
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Check routes
php artisan route:list

# Check logs
tail -f storage/logs/laravel.log

# Database
php artisan tinker
>>> App\Models\MatchingResult::with('cv', 'uploadJob')->get()
```

### Python
```bash
# Health check
curl http://127.0.0.1:8000/health

# Test analyze endpoint
curl -X POST http://127.0.0.1:8000/api/cv/analyze-text \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "cv_text=Test CV text" \
  -d "job_description=Test job description"

# Run with UTF-8
python/run_with_utf8.bat
```

---

## Known Issues

1. **Queue worker harus berjalan** untuk processing async
   - Atau gunakan fallback synchronous (sudah diimplementasikan)

2. **Gemini API key harus di-set** di `.env`
   - Gratis quota: 60 requests/minute
   - Jika habis, tunggu 1 menit atau upgrade ke paid

3. **PDF extraction memerlukan PyMuPDF** atau PHP PDF Parser
   - PyMuPDF lebih akurat
   - PHP PDF Parser sebagai fallback

4. **Windows console encoding** — gunakan `run_with_utf8.bat`

---

## Performance Tips

1. **SBERT model loaded once** at startup — tidak di-load setiap request
2. **Cache TTL 1 hour** — upload CV yang sama = hasil instan
3. **Rate limiting Gemini** — 0.5s delay antar request
4. **Queue fallback** — jika queue gagal, proses synchronous

---

## Support

Jika menemukan masalah baru:
1. Cek `docs/identifikasi_masalah.md` — daftar masalah yang sudah diperbaiki
2. Cek `docs/solusi_masalah.md` — solusi untuk setiap masalah
3. Cek `docs/next_steps.md` — langkah selanjutnya

**Last Updated:** 2026-01-12
**Status:** Production Ready (setelah fix UnicodeEncodeError)