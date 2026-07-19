# Metodologi AI pada Website CVision

## Ringkasan

Website CVision mengimplementasikan sistem pencocokan CV dan lowongan pekerjaan menggunakan kombinasi beberapa metodologi kecerdasan buatan, mulai dari classical machine learning hingga large language model. Secara arsitektural, sistem dibagi menjadi dua bagian: (1) **Python AI Engine** (FastAPI) yang melakukan komputasi berat seperti embedding dan LLM inference, dan (2) **Laravel Backend** (PHP) yang mengorkestrasi request, menyimpan hasil ke database, dan menghitung peringkat (rank).

Kategori metodologi AI yang digunakan:
1. **Information Retrieval / Lexical**: TF-IDF (Term Frequency-Inverse Document Frequency)
2. **Deep Learning / Embedding**: SBERT (Sentence-BERT, Transformer-based)
3. **Ensemble / Fusion**: Hybrid Weighted Scoring (gabungan TF-IDF + SBERT)
4. **Large Language Model (LLM)**: Google Gemini untuk job recommendation dan skill gap analysis
5. **Rule-Based / Heuristic**: Resume parsing, metadata extraction, dan rank calculation
6. **Document Text Extraction**: PDF text extraction menggunakan PyMuPDF

---

## 1. TF-IDF + Cosine Similarity (Lexical Matching)

**Lokasi Implementasi:** `python/services/similarity.py` - fungsi `calculate_tfidf()`

**Penjelasan:**
TF-IDF (Term Frequency-Inverse Document Frequency) adalah metode classical information retrieval yang mengukur seberapa penting sebuah kata dalam dokumen relatif terhadap korpus. Pada CVision, TF-IDF digunakan untuk mengukur kemiripan kata kunci (lexical similarity) antara teks CV dan teks job description.

**Proses:**
1. Kedua teks (CV dan JD) dipreproses (lowercase, hapus control chars, hapus newline)
2. Dibentuk vektor TF-IDF menggunakan `TfidfVectorizer` dengan konfigurasi:
   - `ngram_range=(1,2)`: menangkap unigram dan bigram
   - `stop_words=None`: membiarkan IDF menangani kata umum
   - `sublinear_tf=True`: log scaling untuk mengurangi dominasi kata frekuen
   - `norm='l2'`: normalisasi panjang vektor
3. Cosine similarity dihitung antara vektor CV dan vektor JD
4. Hasil berupa skor 0.0 - 1.0

**Kelebihan:**
- Cepat dan efisien untuk teks berukuran menengah (CV ~300 kata, JD ~200 kata)
- Interpretable: HRD bisa melihat kata kunci mana yang cocok
- ATS-friendly (Applicant Tracking System konvensional menggunakan pendekatan serupa)

**Kekurangan:**
- Tidak bisa menangkap sinonim ("Web Developer" ≠ "Frontend Engineer")
- Tidak paham konteks ("Java" bahasa pemrograman vs "Java" kopi)

---

## 2. SBERT — Sentence-BERT (Semantic Embedding)

**Lokasi Implementasi:** `python/services/similarity.py` - fungsi `calculate_sbert()`

**Penjelasan:**
SBERT (Sentence-BERT) adalah model deep learning berbasis arsitektur Transformer yang menghasilkan vektor embedding (representasi numerik) dari sebuah teks. Berbeda dengan TF-IDF yang hanya menghitung kemunculan kata, SBERT memahami makna semantik. Dua teks yang maknanya mirip akan memiliki vektor yang berdekatan di ruang embedding, meskipun kata-katanya berbeda.

**Implementasi:**
- Model: `all-MiniLM-L6-v2` (80MB, 384 dimensi)
- CV dan JD di-encode menjadi embedding masing-masing
- Cosine similarity dihitung antara kedua embedding
- Hasil berupa skor 0.0 - 1.0

**Kelebihan:**
- Mampu menangkap sinonim dan konteks ("Backend Developer" ≈ "Python Developer")
- Robust terhadap variasi terminologi
- Memberikan semantic recall yang tinggi

**Kekurangan:**
- Black box: sulit diinterpretasikan
- Lebih lambat dari TF-IDF (tetapi masih acceptable untuk produksi)
- Dapat overestimate similarity untuk teks yang secara makna dekat tapi bukan requirement

---

## 3. Hybrid Weighted Scoring (Ensemble)

**Lokasi Implementasi:** `python/services/similarity.py` - fungsi `calculate_hybrid_score()` dan `calculate_match_percentage()`

**Penjelasan:**
Metode ensemble menggabungkan kekuatan TF-IDF (precision/kata kunci) dan SBERT (recall/semantik) melalui pembobotan. Formula:

```
Hybrid Score = (0.5 × TF-IDF Score) + (0.5 × SBERT Score)
Match Percentage = Hybrid Score × 100
```

Bobot 50/50 dipilih karena merupakan balanced approach yang optimal untuk teks berukuran menengah (100-500 kata), didukung oleh penelitian akademik dan praktik industri (Indeed, Glassdoor).

**Alasan Penggunaan Ensemble:**
- TF-IDF saja: terlalu strict, miss semantic matches
- SBERT saja: terlalu longgar, miss critical keywords
- Hybrid: menyeimbangkan keduanya → F1-Score tertinggi

---

## 4. Google Gemini LLM (Reasoning & Recommendation)

**Lokasi Implementasi:** `python/services/gemini_client.py` - class `GeminiClient`

**Penjelasan:**
Gemini adalah Large Language Model (LLM) dari Google yang digunakan untuk tugas-tugas yang membutuhkan reasoning dan pemahaman konteks tingkat tinggi. Pada CVision, Gemini digunakan untuk:

1. **Job Recommendation**: Memberikan rekomendasi posisi pekerjaan yang cocok berdasarkan CV, lengkap dengan confidence score (0-100) dan alasan.
2. **Skill Gap Analysis**: Menganalisis kekurangan skill antara CV kandidat dan requirement job, menghasilkan fit_score (0-100) dan rekomendasi HIRE / CONSIDER / REJECT.

**Detail Teknis:**
- Model: `gemini-2.0-flash-lite`
- Rate limiting: minimum 0.5 detik antar request (menghindari quota limit)
- Retry logic: exponential backoff hingga 3x percobaan
- Output: di-parse sebagai JSON terstruktur

**Alasan Pemilihan LLM:**
- Tugas recommendation dan skill gap membutuhkan pemahaman nuansa yang sulit dihitung dengan rule-based
- Gemini flash-lite dipilih untuk kecepatan dan efisiensi biaya

---

## 5. Rule-Based Resume Parsing (Heuristic)

**Lokasi Implementasi:**
- Python: `python/services/resume_generator.py` - `_extract_resume_fallback()`
- PHP: `app/Services/ResumeParsingService.php` - `parse()`

**Penjelasan:**
Metode rule-based menggunakan pola regex dan keyword matching untuk mengekstrak informasi terstruktur dari CV (nama, email, phone, experience, education, skills, certifications, languages). Tidak menggunakan AI/LLM sama sekali.

**Alasan Penggunaan Rule-Based (bukan Gemini):**
- Hemat token Gemini (khususkan LLM untuk reasoning)
- Lebih cepat (millisecond, tanpa network call)
- Tidak depend pada API availability (reliabel saat demo)
- Untuk teks terstruktur seperti CV, regex sudah cukup akurat

**Contoh Pola:**
- Email: `\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b`
- Pengalaman: `(\d{4})\s*[-–]\s*(\d{4}|present|now|saat\s*ini)`
- Education: keyword S1/S2/D3/SMA/diploma/bachelor/master

---

## 6. PDF Text Extraction (Document AI)

**Lokasi Implementasi:**
- Python: `python/services/pdf_extractor.py` - `extract_pdf_from_bytes()`
- PHP: `app/Services/CVExtractionService.php` - `extractPdf()`

**Penjelasan:**
Sebelum teks bisa dianalisis oleh AI, PDF harus diekstrak menjadi raw text. CVision menggunakan PyMuPDF (fitz) di sisi Python. Jika Python unavailable, Laravel fallback ke PHP PdfParser (Smalot), dan terakhir ke Python subprocess.

**Alasan:**
- PyMuPDF cepat dan akurat untuk ekstraksi text dari PDF
- Fallback berlapis menjamin sistem tetap berjalan meskipun satu komponen gagal

---

## 7. Rank Calculation (Relative Scoring)

**Lokasi Implementasi:** `app/Services/CVScoreService.php` - `calculateRank()`

**Penjelasan:**
Setelah match percentage dihitung untuk satu CV terhadap satu job, sistem menghitung **rank** (peringkat) CV tersebut relatif terhadap CV lain untuk job yang sama. Rumus: menghitung jumlah CV dengan score lebih tinggi, lalu rank = jumlah tersebut + 1.

**Alasan:**
- HRD butuh peringkat relatif, bukan hanya skor absolut
- Memudahkan shortlisting kandidat terbaik

---

## Arsitektur Alur AI (End-to-End)

```
[PDF Upload]
     ↓
[PyMuPDF] → Extract Raw Text
     ↓
     ├─→ [TF-IDF] ──────┐
     │                  ├─→ [Hybrid Score] → [Match %]
     ├─→ [SBERT] ───────┘
     │
     ├─→ [Gemini LLM] → Job Recommendation + Skill Gap
     │
     └─→ [Rule-Based Parser] → Structured Resume
     
[Laravel] → Save to DB + Calculate Rank
     ↓
[Display to User]
```

---

## Tabel Rekapitulasi Metodologi

| No | Metodologi | Kategori | File | Fungsi Utama |
|----|-----------|---------|------|-------------|
| 1 | TF-IDF | Classical ML / IR | `similarity.py` | Lexical keyword matching |
| 2 | SBERT | Deep Learning | `similarity.py` | Semantic embedding similarity |
| 3 | Hybrid Scoring | Ensemble | `similarity.py` | Fusion TF-IDF + SBERT |
| 4 | Gemini | LLM | `gemini_client.py` | Recommendation + Skill Gap |
| 5 | Rule-Based Parser | Heuristic | `resume_generator.py`, `ResumeParsingService.php` | Extract structured resume |
| 6 | PDF Extraction | Document AI | `pdf_extractor.py`, `CVExtractionService.php` | Extract text from PDF |
| 7 | Rank Calculation | Heuristic | `CVScoreService.php` | Relative ranking |

---

## Kesimpulan untuk Laporan Akhir

Sistem CVision merupakan implementasi nyata dari integrasi multi-metodologi AI:
- **Classical ML** (TF-IDF) untuk efisiensi lexical matching
- **Deep Learning** (SBERT) untuk pemahaman semantik
- **LLM** (Gemini) untuk reasoning tingkat tinggi
- **Rule-Based** untuk ekstraksi terstruktur yang reliable dan murah

Pendekatan hibrida ini memberikan keseimbangan antara akurasi, kecepatan, dan biaya operasional, serta menyediakan transparansi yang dibutuhkan untuk validasi ilmiah dalam tugas akhir.
</parameter2_name>
<task_progress>
- [x] Identifikasi metodologi AI di Python services
- [x] Identifikasi metodologi AI di Laravel services
- [x] Buat dokumentasi MD detail untuk laporan
</task_progress>
