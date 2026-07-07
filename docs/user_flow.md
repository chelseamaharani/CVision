# Alur Penggunaan Website CVision

## Pendahuluan

Website CVision memiliki dua peran pengguna utama: **Pelamar (Job Seeker)** dan **HRD (Recruiter/Admin)**. Sistem dirancang sebagai two-sided platform di mana pelamar mengunggah CV dan HRD membuat lowongan pekerjaan, kemudian AI Engine secara otomatis melakukan pencocokan (matching) antara keduanya.

Berdasarkan analisis `routes/web.php` dan `routes/api.php`, alur penggunaan dijelaskan sebagai berikut.

---

## 1. Sisi Pelamar (Job Seeker)

Pelamar adalah pencari kerja yang ingin mengetahui seberapa cocok CV mereka dengan lowongan yang tersedia.

### 1.1 Akses dan Autentikasi

| Step | Aksi | Route | Controller |
|------|------|-------|-----------|
| 1 | Buka beranda (landing page) | `GET /` | `LandingPagePelamarController@index` |
| 2 | Login | `POST /login` | `LoginController@login` |
| 3 | Register (jika belum punya akun) | `POST /register` | `RegisterController@register` |

**Penjelasan:** Landing page dapat diakses tanpa login (publik) untuk melihat daftar lowongan. Namun, untuk mengunggah CV, pelamar wajib login terlebih dahulu (dilindungi `auth` middleware).

### 1.2 Upload dan Pemrosesan CV

| Step | Aksi | Route | Controller |
|------|------|-------|-----------|
| 4 | Upload CV (PDF) | `POST /upload-cv` | `LandingPagePelamarController@store` |
| 5 | Hapus CV | `DELETE /upload-cv/{id}` | `LandingPagePelamarController@destroy` |

**Proses di Balik Layar (Background):**
1. File PDF disimpan ke storage lokal
2. `CVExtractionService` mengekstrak teks dari PDF (PyMuPDF → fallback PHP → fallback Python)
3. `CVScoreService` memanggil FastAPI AI Engine (`/api/cv/analyze`) dengan CV text + job description
4. AI Engine menghitung: TF-IDF Score → SBERT Score → Hybrid Score → Match Percentage
5. Gemini menghasilkan job recommendation + skill gap analysis
6. `ResumeParsingService` (rule-based) mengekstrak resume terstruktur
7. Hasil disimpan ke tabel `matching_results` + perhitungan rank relatif

### 1.3 Melihat Hasil

Pelamar dapat melihat hasil analisis melalui dashboard pelamar:
- **Match Percentage**: persentase kecocokan CV dengan masing-masing job
- **Resume Terstruktur**: nama, email, pengalaman, pendidikan, skills (hasil parse)
- **Rekomendasi**: saran posisi dari Gemini

### 1.4 Ringkasan Alur Pelamar

```
[Beranda] → [Login] → [Upload CV] → [Sistem AI Proses] → [Lihat Match % + Resume]
                ↑                                              |
                └──────────────────────────────────────────────┘
                        (bisa upload CV baru / hapus)
```

---

## 2. Sisi HRD (Recruiter / Admin)

HRD adalah perekrut yang membuat lowongan dan menyeleksi kandidat.

### 2.1 Akses dan Autentikasi

| Step | Aksi | Route | Controller |
|------|------|-------|-----------|
| 1 | Login | `POST /login` | `LoginController@login` |
| 2 | Dashboard | `GET /dashboard` | `DashboardController@index` |

**Penjelasan:** Semua fitur HRD dilindungi `auth` middleware (wajib login).

### 2.2 Manajemen Lowongan

| Step | Aksi | Route | Controller |
|------|------|-------|-----------|
| 3 | Form buat lowongan | `GET /job_listing/create` | `JobListingController@create` |
| 4 | Simpan lowongan | `POST /job_listing` | `JobListingController@store` |
| 5 | Lihat daftar lowongan | `GET /job_listing` | `JobListingController@index` |

**Penjelasan:** HRD mengisi job description, required skills, dan metadata lowongan lainnya. Data ini akan menjadi acuan pencocokan AI.

### 2.3 Screening dan Matching

| Step | Aksi | Route | Controller |
|------|------|-------|-----------|
| 6 | Screen semua CV untuk 1 job | `POST /job_listing/{jobId}/screen` | `JobListingController@screen` |
| 7 | Screen 1 CV manual | `POST /screening/{cvId}/screen` | `ScreeningController@screenSingle` |
| 8 | Screen semua CV | `POST /screening/{jobId}/screen-all` | `ScreeningController@screenAll` |
| 9 | Lihat hasil matching | `GET /matching_results` | `MatchingController@results` |
| 10 | Detail kandidat | `GET /candidate/{id}` | `CandidateResumeController@show` |

**Penjelasan:**
- **Screening** memicu pemrosesan AI untuk CV yang belum dianalisis
- **Matching Results** menampilkan tabel: match %, rank, skills matched, skill gap, education level
- **Candidate Detail** menampilkan resume terstruktur + skor AI + rekomendasi Gemini + skill gap

### 2.4 Manajemen Kandidat & Pengaturan

| Step | Aksi | Route | Controller |
|------|------|-------|-----------|
| 11 | Lihat semua kandidat | `GET /candidates` | `CandidatesController@index` |
| 12 | Hapus kandidat | `DELETE /candidates/{id}` | `CandidatesController@destroy` |
| 13 | Pengaturan | `GET /settings`, `PUT /settings` | `SettingsController` |

### 2.5 Ringkasan Alur HRD

```
[Login] → [Dashboard] → [Buat Lowongan] → [Screen CV] → [Lihat Hasil Matching]
                                                            ↓
                                                   [Detail Kandidat]
                                                            ↓
                                              [Shortlist / Hapus Kandidat]
```

---

## 3. Interaksi Antara Pelamar dan HRD

Kedua peran bertemu di **Sistem AI Engine** yang berjalan di background:

```
[Pelamar]                    [Sistem AI Engine]              [HRD]
   |                              |                            |
   |-- Upload CV ---------------->|                            |
   |                              |-- Extract Text             |
   |                              |-- TF-IDF + SBERT           |
   |                              |-- Hybrid Score + Rank      |
   |<-- Lihat Match % -----------|                            |
   |                              |                            |
   |                              |<-- Buat Job Description ---|
   |                              |-- Auto-Match CV ↔ Job      |
   |                              |                            |
   |                              |<-- Trigger Screening ------|
   |                              |-- Generate Report          |
   |                              |------------> Lihat Results |
   |                              |------------> Detail Candidate
```

**Penjelasan:**
- Pelamar mengisi "supply" (CV), HRD mengisi "demand" (Job)
- AI Engine melakukan matching otomatis
- Hasilnya dapat dilihat oleh kedua pihak dalam perspektif masing-masing

---

## 4. Perbandingan Alur Pelamar vs HRD

| Aspek | Pelamar | HRD |
|-------|---------|-----|
| Tujuan Utama | Mencari job yang cocok | Mencari kandidat terbaik |
| Aksi Utama | Upload CV | Buat job + screen CV |
| Output yang Dilihat | Match % CV vs job | Rank kandidat + skill gap |
| Akses Halaman | Landing (publik) + login | Login wajib (dashboard) |
| AI yang Dialami | Resume parse + recommendation | Matching + screening + gap analysis |
| Middleware | `auth` untuk upload | `auth` untuk semua fitur |

---

## 5. Kesimpulan untuk Laporan Akhir

Alur penggunaan CVision dirancang dengan prinsip **separation of concerns**:
- **Pelamar** fokus pada input (upload CV) dan feedback individu (match %)
- **HRD** fokus pada manajemen (job creation) dan evaluasi kolektif (ranking, screening)
- **AI Engine** menangani semua komputasi berat secara transparan di background

Sistem ini meminimalkan intervensi manual dalam proses seleksi CV, mempercepat waktu hiring, dan memberikan objektivitas melalui metrik AI (TF-IDF, SBERT, Hybrid, Gemini).
</parameter2_name>
<task_progress>
- [x] Analisis routes dan controllers untuk alur HRD
- [x] Analisis routes dan controllers untuk alur pelamar
- [x] Buat dokumentasi MD lengkap untuk laporan
</task_progress>
