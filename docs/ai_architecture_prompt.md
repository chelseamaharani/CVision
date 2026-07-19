# AI Architecture Prompt Template

## Prompt untuk Chatbot - Generate AI Architecture Documentation

Berikut adalah prompt yang dapat Anda gunakan untuk chatbot agar dapat menghasilkan dokumentasi arsitektur AI CVision yang sesuai:

---

### **PROMPT UTAMA**

```
Anda adalah seorang AI Architect dan Technical Writer. Buatkan dokumentasi arsitektur AI untuk sistem CVision berdasarkan informasi berikut:

SISTEM: CVision - Sistem pencocokan CV dan lowongan pekerjaan
TIPE: Two-Tier Architecture (Laravel + Python FastAPI)

LAYER 1 - DATA LAYER:
- Input: PDF CV, Job Description (teks), User Profile
- Processing: PyMuPDF untuk ekstraksi teks, text preprocessing
- Storage: MySQL/PostgreSQL database, file storage untuk PDF
- Output: Raw text yang sudah dibersihkan

LAYER 2 - MODEL LAYER:
- TF-IDF: Classical ML untuk lexical matching (ngram 1-2, 30K features)
- SBERT: Deep learning untuk semantic similarity (all-MiniLM-L6-v2, 384 dimensi)
- Hybrid Scoring: Ensemble 50/50 TF-IDF + SBERT
- Gemini LLM: Job recommendation dan skill gap analysis
- Rule-Based: Regex parsing untuk ekstrak data CV

LAYER 3 - APPLICATION/MLOPS LAYER:
- FastAPI endpoints: /health, /api/cv/analyze, /api/cv/generate-resume
- Laravel backend: HTTP handling, database, rank calculation
- MLOps: Model loading, rate limiting, error handling

Buatkan:
1. Diagram arsitektur 3-layer
2. Pipeline flow dari input ke output
3. Tabel perbandingan model
4. Performance metrics
5. Error handling flow
```

---

### **PROMPT DETAIL PIPELINE**

```
Buatkan diagram pipeline AI CVision dengan format berikut:

INPUT:
- [PDF Upload] → [File Validation] → [PyMuPDF Extraction]
- [Job Description] → [Text Preprocessing]
- [User Data] → [Database Query]

PROCESSING:
- [Text Preprocessor]
  ├── preprocess_text() → single line untuk similarity
  └── preprocess_text_for_resume() → multi-line untuk parsing

FEATURE ENGINEERING:
- [TF-IDF Vectorizer]
  - ngram_range=(1,2)
  - max_features=30000
  - sublinear_tf=True
  - norm='l2'
  - Output: TF-IDF Score (0.0-1.0)

- [SBERT Encoder]
  - Model: all-MiniLM-L6-v2
  - Embedding dim: 384
  - Output: SBERT Score (0.0-1.0)

MODEL INFERENCE:
- [Hybrid Scoring]
  - Formula: 0.5 × TF-IDF + 0.5 × SBERT
  - Output: Match Percentage (0-100)

- [Gemini LLM]
  - Model: gemini-2.0-flash-lite
  - Rate limit: 0.5 detik
  - Output: Job recommendations + skill gap analysis

- [Rule-Based Parser]
  - Regex patterns untuk email, phone, experience, education, skills
  - Output: Structured resume JSON

OUTPUT:
- [Response Builder] → [JSON Response] → [Frontend Display]
- [Database Save] → [Rank Calculation]
```

---

### **PROMPT UNTUK GENERATE DIAGRAM**

```
Buatkan diagram arsitektur AI CVision dalam format ASCII art:

Format:
┌─────────────────────────────────────────────────────────────┐
│                    LAYER NAME                              │
└─────────────────────────────────────────────────────────────┘
                                    │
                        ┌───────────┼───────────┐
                        ▼           ▼           ▼
              ┌─────────────┐ ┌─────────────┐ ┌─────────────┐
              │ Component 1 │ │ Component 2 │ │ Component 3 │
              │             │ │             │ │             │
              │ - Detail 1  │ │ - Detail 2  │ │ - Detail 3  │
              └─────────────┘ └─────────────┘ └─────────────┘
```

---

### **PROMPT UNTUK GENERATE TABEL**

```
Buatkan tabel perbandingan model AI CVision:

| Model | Kategori | Kecepatan | Akurasi | Biaya | Interpretasi |
|-------|----------|-----------|---------|-------|--------------|
| TF-IDF | Classical ML | ⚡⚡⚡ | ⚡⚡ | Gratis | ⚡⚡⚡ |
| SBERT | Deep Learning | ⚡⚡ | ⚡⚡⚡ | Gratis | ⚡ |
| Hybrid | Ensemble | ⚡⚡ | ⚡⚡⚡ | Gratis | ⚡⚡ |
| Gemini | LLM | ⚡ | ⚡⚡⚡ | Berbayar | ⚡⚡ |
| Rule-Based | Heuristic | ⚡⚡⚡ | ⚡⚡ | Gratis | ⚡⚡⚡ |
```

---

### **PROMPT UNTUK GENERATE MLOPS**

```
Jelaskan MLOps pipeline CVision dalam 3 fase:

FASE 1 - DEVELOPMENT:
- Model selection (TF-IDF, SBERT, Gemini)
- Prompt engineering untuk LLM
- Testing dan tuning

FASE 2 - DEPLOYMENT:
- Model loading (SBERT di-load sekali)
- API deployment (FastAPI)
- Monitoring setup

FASE 3 - RUNTIME:
- Inference pipeline
- Data persistence
- Feedback loop
```

---

## Contoh Output yang Diharapkan

Chatbot yang menggunakan prompt ini akan menghasilkan:

1. **Diagram 3-layer architecture** dengan visual ASCII art
2. **Pipeline flow chart** dari input ke output
3. **Tabel perbandingan model** dengan metrics
4. **Performance metrics** (waktu, memory, notes)
5. **Error handling flow** dengan fallback mechanism

---

## File Output

Dokumentasi yang dihasilkan akan disimpan di:
- `docs/ai_architecture_3layer.md` - Arsitektur 3 layer
- `docs/ai_pipeline.md` - Pipeline flow
- `docs/ai_methodology_identification.md` - Metodologi AI (sudah ada)