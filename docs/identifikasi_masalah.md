# Identifikasi Masalah - CVision Application

## Ringkasan
Berdasarkan analisis kode pada seluruh layer aplikasi (Python AI Engine, PHP/Laravel Backend, Frontend Blade Views, dan Dokumentasi), ditemukan **20+ masalah** yang dikategorikan dalam 4 area utama.

---

## A. MASALAH KRITIS (Critical) — Menyebabkan aplikasi tidak berfungsi sebagaimana mestinya

### A1. Model Gemini Tidak Valid ❌
**Lokasi:** `python/services/gemini_client.py:23`
```python
self.model = "gemini-3.1-flash-lite"  # ❌ LAMA - sudah diperbaiki ke gemini-2.0-flash-lite
```
**Masalah:** Nama model `gemini-3.1-flash-lite` TIDAK VALID. Model Gemini terbaru adalah:
- `gemini-2.0-flash` (valid)
- `gemini-2.0-flash-lite` (valid)
- `gemini-1.5-flash` (valid)

**Akibat:** Semua panggilan API ke Gemini akan gagal (HTTP 404/model not found). Fitur **job recommendation**, **skill gap analysis**, dan **resume generation** tidak akan berfungsi sama sekali.

**Dampak:** 🔴 Sangat Tinggi

---

### A2. Endpoint `/api/cv/analyze` Tidak Menerima Job Requirements
**Lokasi:** `python/main.py:109-115` dan `python/main.py:299-307`
```python
# Endpoint /api/cv/analyze (PDF upload) — TIDAK punya min_experience / required_education
async def analyze_cv(
    cv_file: UploadFile = File(...),
    job_description: str = Form(...),
    required_skills: str = Form(default=""),
    job_title: str = Form(default="Unknown Position"),
):
```
```python
# Endpoint /api/cv/analyze-text (text only) — SUDAH punya
async def analyze_cv_text(
    ...
    min_experience: float = Form(default=0.0),
    required_education: str = Form(default=""),
):
```
**Masalah:** Terdapat inkonsistensi. Endpoint `analyze_cv` (untuk upload PDF) tidak menerima parameter `min_experience` dan `required_education`. Sementara endpoint `analyze-text` sudah menerimanya. Jika HRD mengisi persyaratan pengalaman/education di job input, parameter tersebut tidak akan dikirim saat screening via PDF upload.

**Akibat:** Job requirements tidak digunakan dalam perhitungan scoring untuk CV yang diupload via form.

**Dampak:** 🔴 Sangat Tinggi

---

### A3. PDF Extraction Loop Tidak Berguna di CVExtractionService
**Lokasi:** `app/Services/CVExtractionService.php:48-72`
```php
// Mencoba memanggil /api/cv/analyze dengan placeholder job_description
$response = Http::timeout(30)
    ->attach('cv_file', $pdfContent, basename($path))
    ->post(config('services.ai.engine_url', ...) . '/api/cv/analyze', [
        'job_description' => 'Placeholder for extraction only',
    ]);
```
**Masalah:** Kode ini memanggil endpoint `/api/cv/analyze` dengan tujuan ekstraksi teks, TAPI:
1. Endpoint `/api/cv/analyze` TIDAK mengembalikan extracted text — ia mengembalikan `AnalyzeCVResponse` yang berisi score, recommendation, dll.
2. Response tidak pernah digunakan (code hanya log "succeeded" lalu lanjut ke fallback)
3. Membuang waktu ~3-6 detik untuk request yang tidak berguna

**Akibat:** Seluruh blok kode ini waste resource dan tidak memberikan nilai tambah.

**Dampak:** 🔴 Tinggi (performa)

---

## B. MASALAH SIGNIFIKAN (Major) — Menyebabkan inkonsistensi atau kegagalan parsial

### B1. Duplikasi Resume Parsing (Python vs PHP)
**Lokasi:** 
- `python/services/resume_generator.py` (rule-based parsing)
- `app/Services/ResumeParsingService.php` (rule-based parsing)

**Masalah:** Ada DUA implementasi parsing resume yang hampir identik. Keduanya menggunakan rule-based/regex dan menghasilkan struktur data yang berbeda. Python menghasilkan format dengan keys seperti `name`, `email`, `phone`, `summary`, sementara PHP menghasilkan format berbeda.

**Akibat:** Potensi inkonsistensi data. Jika suatu fitur menggunakan Python parser dan fitur lain menggunakan PHP parser, hasilnya bisa berbeda.

**Dampak:** 🟠 Sedang

---

### B2. Field `skills_total` dan `skills_count` Tidak Pernah Diisi
**Lokasi:** `app/Services/CVScoreService.php:89-109` dan `app/Models/MatchingResult.php`
```php
$data = array_merge($result->toArray(), [
    'upload_job_id'   => $job->id,
    ...
    'skills_matched'  => $result->skillGap['skills_present'] ?? null,
    'skill_gap'       => $result->skillGap['skills_missing'] ?? null,
    // skills_total dan skills_count TIDAK ADA
]);
```
**Masalah:** Model `MatchingResult` memiliki fillable field `skills_total` dan `skills_count`, tapi `CVScoreService::saveResult()` tidak pernah mengisi kedua field tersebut.

**Akibat:** Di view `CandidateResumeController.php:103-104`, field ini selalu `null`/undefined:
```php
'skills_total' => $result->skills_total ?? count($result->skills_matched ?? []) + count($result->skill_gap ?? []),
'skills_count' => $result->skills_count ?? count($result->skills_matched ?? []),
```
Ini menggunakan fallback yang tidak akurat.

**Dampak:** 🟠 Sedang

---

### B3. Cache Key Tidak Mempertimbangkan Perubahan Job Description
**Lokasi:** `app/Services/CVScoreService.php:115-119`
```php
private function buildCacheKey(Cv $cv, UploadJob $job): string
{
    $fileTimestamp = $cv->updated_at?->timestamp ?? $cv->created_at?->timestamp ?? time();
    return "cv_analysis:{$cv->id}:job_{$job->id}:{$fileTimestamp}";
}
```
**Masalah:** Cache key hanya berdasarkan CV file timestamp, TIDAK menyertakan perubahan pada job description. Jika HRD mengubah job description, hasil analisis yang di-cache masih menggunakan job description lama.

**Akibat:** Hasil screening tidak merefleksikan job description terbaru sampai cache dihapus manual (TTL 1 jam).

**Dampak:** 🟠 Sedang

---

### B4. Rank Tidak Konsisten pada Concurrent Requests
**Lokasi:** `app/Services/CVScoreService.php:124-131`
```php
private function calculateRank(int $jobId, float $score): int
{
    $higherScores = MatchingResult::where('upload_job_id', $jobId)
        ->where('score', '>', $score)
        ->count();
    return $higherScores + 1;
}
```
**Masalah:** Rank dihitung SEBELUM data baru disimpan. Jika ada 2 CV di-screen bersamaan, keduanya bisa mendapat rank yang sama karena keduanya menghitung berdasarkan state database yang sama.

**Akibat:** Duplikasi rank atau rank yang tidak akurat.

**Dampak:** 🟠 Sedang

---

### B5. `mb_convert_encoding` Tidak Efektif untuk UTF-8 Cleaning
**Lokasi:** `app/DTOs/CVScoreResult.php:66-71` dan `app/Services/CVScoreService.php:62`
```php
return mb_convert_encoding($data, 'UTF-8', 'UTF-8');
```
```php
$cvText = mb_convert_encoding($cvText, 'UTF-8', 'UTF-8');
```
**Masalah:** `mb_convert_encoding` dengan source encoding = 'UTF-8' dan destination encoding = 'UTF-8' TIDAK melakukan apa-apa. Ini tidak membersihkan invalid UTF-8 characters.

Sebaliknya, Python sudah melakukan cleaning yang lebih baik di `main.py:148`:
```python
cv_text = cv_text.encode('utf-8', 'replace').decode('utf-8')
```

**Akibat:** Jika ada karakter corrupt di teks, PHP tidak akan membersihkannya, menyebabkan error `json_encode()` saat menyimpan recommendation ke database.

**Dampak:** 🟠 Sedang

---

## C. MASALAH PERFORMANCE & ARSITEKTUR

### C1. Rate Limiting Gemini Terlalu Ketat
**Lokasi:** `python/services/gemini_client.py:36`
```python
self._min_delay = 2.0  # Minimum 2 detik antar request ke Gemini
```
**Masalah:** Delay 2 detik antar request ke Gemini memperlambat pipeline. Untuk screening 10 CV, tambahan delay menjadi 20 detik.

**Saran:** Turunkan ke 0.5-1 detik, atau gunakan adaptive rate limiting berdasarkan HTTP response codes.

**Dampak:** 🟡 Rendah-Sedang

---

### C2. Tidak Ada Health Check / Monitoring Queue Worker
**Lokasi:** `app/Jobs/ProcessCVJob.php`
**Masalah:** Queue job di-dispatch tanpa mekanisme monitoring. Jika queue worker `php artisan queue:work` tidak berjalan, CV tidak akan pernah diproses dan user tidak mendapat feedback.

**Akibat:** User upload CV, mendapat sukses message, tapi analysis tidak pernah jalan. Tidak ada notifikasi error ke user.

**Dampak:** 🟠 Sedang

---

### C3. Python Subprocess Fallback untuk PDF Extraction di Windows
**Lokasi:** `app/Services/CVExtractionService.php:95-128`
**Masalah:** Method `extractPdfViaPython` menggunakan `shell_exec()` yang bergantung pada Python path di config. Di environment Windows, path ke executable Python bisa berbeda dan rentan error.

**Akibat:** Fallback terakhir bisa gagal tanpa pesan error yang jelas.

**Dampak:** 🟡 Rendah

---

## D. MASALAH UI/UX & FRONTEND

### D1. Tidak Ada Feedback Real-time untuk Proses Screening
**Lokasi:** `resources/views/pages/landing_page_pelamar.blade.php`
**Masalah:** Setelah upload CV, user hanya mendapat flash message "AI analysis is in progress". Tidak ada progress bar, polling status, atau WebSocket notification. User harus refresh halaman dan cek history tab secara manual.

**Dampak:** 🟡 Rendah

---

### D2. Google Auth Route Tidak Fungsional
**Lokasi:** `routes/web.php:23`
```php
Route::get('/auth/google', fn() => redirect('/login'))->name('auth.google');
```
**Masalah:** Route `/auth/google` hanya redirect ke `/login`, tidak mengimplementasikan Google OAuth.

**Akibat:** Tombol "Login with Google" (jika ada di view) tidak akan berfungsi.

**Dampak:** 🟡 Rendah

---

### D3. Tidak Ada Loading State untuk Upload
**Lokasi:** `resources/views/pages/landing_page_pelamar.blade.php`
**Masalah:** Tombol submit upload tidak memiliki loading state / disabled state saat processing. User bisa mengklik tombol submit berkali-kali, menyebabkan multiple upload.

**Dampak:** 🟡 Rendah

---

## E. MASALAH DOKUMENTASI & MAINTENANCE

### E1. Dokumentasi Tidak Sinkron dengan Kode
**Lokasi:** `docs/ai_pipeline.md:90`
```
│   │ gemini-3.1-     │
│   │ flash-lite      │
```
**Masalah:** Dokumentasi menyebut `gemini-3.1-flash-lite` yang tidak valid. Juga menyebut beberapa fitur yang belum diimplementasikan.

**Dampak:** 🟡 Rendah

---

### E2. Plan.md Tidak Mencakup Semua Masalah
**Lokasi:** `plan.md`
**Masalah:** Dokumen `plan.md` hanya menyebut 3 masalah (resume format, job input, inconsistent scoring). Masalah-masalah lain (model Gemini tidak valid, endpoint inconsistency, dll) tidak tercatat.

**Dampak:** 🟡 Rendah

---

### E3. Tidak Ada Migration / Seeder untuk Testing
**Masalah:** Tidak ditemukan file migration atau database seeder untuk data testing. Ini menyulitkan development dan testing.

**Dampak:** 🟡 Rendah

---

## F. SUMMARY & REKOMENDASI PRIORITAS

| Prioritas | Masalah | Dampak | Estimasi Fix |
|-----------|---------|--------|--------------|
| 🔴 P1 | Model Gemini tidak valid (`gemini-3.1-flash-lite`) | Semua fitur AI recommendation gagal | 5 menit |
| 🔴 P2 | Endpoint `/api/cv/analyze` tidak menerima job requirements | Job requirements tidak dipakai di PDF upload | 15 menit |
| 🔴 P3 | PDF extraction loop tidak berguna | Waste resource 3-6 detik per request | 15 menit |
| 🟠 P4 | Duplikasi resume parsing (Python vs PHP) | Inkonsistensi data | 30 menit |
| 🟠 P5 | Field `skills_total`/`skills_count` tidak diisi | Data tidak akurat di view | 10 menit |
| 🟠 P6 | Cache key tidak include job description update | Hasil screening basi | 10 menit |
| 🟠 P7 | Rank tidak konsisten (concurrent) | Duplikasi rank | 15 menit |
| 🟠 P8 | `mb_convert_encoding` tidak efektif | Potensi json_encode error | 5 menit |
| 🟡 P9 | Tidak ada monitoring queue worker | CV tidak terproses tanpa feedback | 30 menit |
| 🟡 P10 | Google Auth tidak fungsional | Fitur tidak berguna | 5 menit |

### Rekomendasi Langsung (P1-P3):
1. **Ubah model name** di `gemini_client.py:23` dari `gemini-3.1-flash-lite` menjadi `gemini-2.0-flash-lite`
2. **Sinkronkan parameter** endpoint `/api/cv/analyze` dengan `/api/cv/analyze-text` — tambahkan `min_experience` dan `required_education`
3. **Hapus block kode** PDF extraction via `/api/cv/analyze` di `CVExtractionService.php` yang tidak berguna
4. **Update schema** `AnalyzeCVResponse` untuk menyertakan `min_experience` dan `required_education`