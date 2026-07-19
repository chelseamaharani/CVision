# AI Pipeline - CVision

## Visual Pipeline Diagram

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                           DATA LAYER (Input)                                   │
└─────────────────────────────────────────────────────────────────────────────┘
                                    │
                        ┌───────────┼───────────┐
                        ▼           ▼           ▼
              ┌─────────────┐ ┌─────────────┐ ┌─────────────┐
              │  PDF File   │ │ Job Desc    │ │ User Data   │
              │  (Binary)   │ │ (Text)      │ │ (Profile)   │
              └─────────────┘ └─────────────┘ └─────────────┘
                     │              │              │
                     ▼              ▼              ▼
                    
┌─────────────────────────────────────────────────────────────────────────────┐
│                    DATA PROCESSING PIPELINE                                   │
└─────────────────────────────────────────────────────────────────────────────┘
                                    │
                        ┌───────────┼───────────┐
                        ▼           ▼           ▼
              ┌─────────────┐ ┌─────────────┐ ┌─────────────┐
              │ PyMuPDF     │ │ Text        │ │ Database    │
              │ (Extract)   │ │ Preprocessor│ │ Query       │
              │             │ │             │ │             │
              │ - PDF →     │ │ - Lowercase │ │ - User auth │
              │   Text      │ │ - Remove    │ │ - Job data  │
              │ - Clean     │ │   control   │ │ - History   │
              │   encoding  │ │   chars     │ │             │
              └─────────────┘ └─────────────┘ └─────────────┘
                     │              │              │
                     └──────────────┼──────────────┘
                                    ▼
                           ┌─────────────────┐
                           │ Raw CV Text     │
                           │ (Cleaned)       │
                           └─────────────────┘
                                    │
                                    ▼
                                    
┌─────────────────────────────────────────────────────────────────────────────┐
│                           MODEL LAYER (AI Engine)                            │
└─────────────────────────────────────────────────────────────────────────────┘
                                    │
                   ┌────────────────┼────────────────┐
                   ▼                ▼                ▼
         ┌───────────────┐ ┌───────────────┐ ┌───────────────┐
         │   TF-IDF      │ │    SBERT      │ │  Rule-Based   │
         │  (Lexical)    │ │  (Semantic)   │ │   (Parsing)   │
         │               │ │               │ │               │
         │ TfidfVectoriz │ │ SentenceTrans-  │ │ Regex Pattern │
         │ er (30K feat) │ │ former (384d) │ │ Matching      │
         │               │ │               │ │               │
         │ - n-gram 1-2  │ │ - all-MiniLM- │ │ - Email       │
         │ - sublinear   │ │   L6-v2       │ │ - Phone       │
         │ - L2 norm     │ │ - Cosine sim  │ │ - Experience  │
         └───────────────┘ └───────────────┘ │ - Education   │
                   │                │        │ - Skills      │
                   │                │        └───────────────┘
                   │                │                │
                   ▼                ▼                ▼
         ┌───────────────┐ ┌───────────────┐ ┌───────────────┐
         │ TF-IDF Score  │ │ SBERT Score   │ │ Structured    │
         │ (0.0 - 1.0)   │ │ (0.0 - 1.0)   │ │ Resume        │
         └───────────────┘ └───────────────┘ │ (JSON)        │
                   │                │        └───────────────┘
                   └────────┬───────┘                │
                            ▼                        │
                   ┌─────────────────┐                 │
                   │ Hybrid Scoring  │                 │
                   │                 │                 │
                   │ 0.5 × TF-IDF +  │                 │
                   │ 0.5 × SBERT     │                 │
                   └─────────────────┘                 │
                            │                        │
                            ▼                        │
                   ┌─────────────────┐                 │
                   │ Match %         │                 │
                   │ (0 - 100)       │                 │
                   └─────────────────┘                 │
                            │                        │
                            ▼                        │
                   ┌─────────────────┐                 │
                   │   Gemini LLM    │◄────────────────┘
                   │                 │
                   │ gemini-3.1-     │
                   │ flash-lite      │
                   │                 │
                   │ - Job Recom-    │
                   │   mendation     │
                   │ - Skill Gap     │
                   │ - Reasoning     │
                   └─────────────────┘
                            │
                            ▼
                   ┌─────────────────┐
                   │ AI Analysis     │
                   │ (JSON)          │
                   └─────────────────┘
                                    │
                                    ▼
                                    
┌─────────────────────────────────────────────────────────────────────────────┐
│                      APPLICATION / MLOPS LAYER (Output)                        │
└─────────────────────────────────────────────────────────────────────────────┘
                                    │
                   ┌────────────────┼────────────────┐
                   ▼                ▼                ▼
         ┌───────────────┐ ┌───────────────┐ ┌───────────────┐
         │ Save to DB    │ │ Calculate     │ │ Return JSON   │
         │               │ │ Rank          │ │ Response      │
         │ - cv_scores   │ │               │ │               │
         │ - resumes     │ │ - Count       │ │ - match_%     │
         │ - history     │ │   higher      │ │ - tfidf       │
         └───────────────┘ │   scores      │ │ - sbert       │
                           │ - Rank = N+1  │ │ - hybrid      │
                           └───────────────┘ │ - ai_analysis │
                                             └───────────────┘
                                    │
                                    ▼
                           ┌─────────────────┐
                           │ Frontend        │
                           │ (Blade Views)   │
                           └─────────────────┘
```

---

## Detailed Pipeline Flow

### Stage 1: Data Ingestion

```
[User Upload]
     ↓
[File Validation]
     - Check .pdf extension
     - Check file not empty
     ↓
[PDF Extraction] → PyMuPDF
     - Binary → Text
     - Multi-page support
     - UTF-8 encoding
```

### Stage 2: Text Preprocessing

```
[Raw Text]
     ↓
[Text Processor]
     ├── preprocess_text() → For similarity
     │   - Lowercase
     │   - Remove control chars
     │   - Normalize whitespace
     │   - Single line output
     │
     └── preprocess_text_for_resume() → For parsing
         - Preserve newlines
         - Section detection ready
         - Multi-line output
```

### Stage 3: Feature Engineering

```
[Cleaned Text]
     ↓
[TF-IDF Vectorizer]
     - ngram_range=(1,2)
     - max_features=30000
     - sublinear_tf=True
     - norm='l2'
     ↓
[TF-IDF Matrix] (sparse)
     │
     └── Cosine Similarity → TF-IDF Score

[Cleaned Text]
     ↓
[SBERT Encoder]
     - all-MiniLM-L6-v2
     - 384 dimensions
     - Mean pooling
     ↓
[Embeddings] (dense)
     │
     └── Cosine Similarity → SBERT Score
```

### Stage 4: Model Inference

```
[TF-IDF Score] ──┐
                 │
[SBERT Score] ───┼──► [Hybrid Score]
                 │           │
                 │           ▼
                 │    [Match Percentage]
                 │           │
                 │           ▼
                 │    [Rank Calculation]
                 │
[CV Text] ───────┼──► [Gemini LLM]
                 │           │
                 │           ├──► Job Recommendations
                 │           └──► Skill Gap Analysis
                 │
[CV Text] ───────┼──► [Rule-Based Parser]
                           │
                           └──► Structured Resume
```

### Stage 5: Response Assembly

```
[All Results]
     ↓
[Response Builder]
     ├── tfidf_score: float
     ├── sbert_score: float
     ├── hybrid_score: float
     ├── match_percentage: float
     ├── recommendation: dict
     ├── skill_gap: dict
     ├── experience_years: float
     ├── education_level: str
     └── structured_resume: dict
     ↓
[JSON Response]
     ↓
[Frontend Display]
```

---

## MLOps Pipeline

### Model Training & Deployment

```
┌─────────────────────────────────────────────────────────────┐
│                    DEVELOPMENT PHASE                          │
└─────────────────────────────────────────────────────────────┘
                                    │
                        ┌───────────┼───────────┐
                        ▼           ▼           ▼
              ┌─────────────┐ ┌─────────────┐ ┌─────────────┐
              │ Model       │ │ Prompt      │ │ Testing     │
              │ Selection   │ │ Engineering │ │ & Tuning    │
              │             │ │             │ │             │
              │ - TF-IDF    │ │ - Gemini    │ │ - Unit      │
              │ - SBERT     │ │   prompts   │ │   tests     │
              │ - Ensemble  │ │ - Weight    │ │ - Accuracy  │
              │             │ │   tuning    │ │   testing   │
              └─────────────┘ └─────────────┘ └─────────────┘
                                    │
                                    ▼
                                    
┌─────────────────────────────────────────────────────────────┐
│                    DEPLOYMENT PHASE                           │
└─────────────────────────────────────────────────────────────┘
                                    │
                        ┌───────────┼───────────┐
                        ▼           ▼           ▼
              ┌─────────────┐ ┌─────────────┐ ┌─────────────┐
              │ Model       │ │ API         │ │ Monitoring  │
              │ Loading     │ │ Deployment  │ │ & Logging   │
              │             │ │             │ │             │
              │ - SBERT     │ │ - FastAPI   │ │ - Request   │
              │   load      │ │ - CORS      │ │   logging   │
              │ - Gemini    │ │ - Rate      │ │ - Error     │
              │   init      │ │   limiting  │ │   tracking  │
              └─────────────┘ └─────────────┘ └─────────────┘
                                    │
                                    ▼
                                    
┌─────────────────────────────────────────────────────────────┐
│                    RUNTIME PHASE                              │
└─────────────────────────────────────────────────────────────┘
                                    │
                        ┌───────────┼───────────┐
                        ▼           ▼           ▼
              ┌─────────────┐ ┌─────────────┐ ┌─────────────┐
              │ Inference   │ │ Data        │ │ Feedback    │
              │             │ │ Persistence │ │ Loop        │
              │ - TF-IDF    │ │             │ │             │
              │ - SBERT     │ │ - MySQL     │ │ - User      │
              │ - Gemini    │ │ - File      │ │   ratings   │
              │             │ │   storage   │ │ - Model     │
              │             │ │             │ │   updates   │
              └─────────────┘ └─────────────┘ └─────────────┘
```

---

## Performance Metrics

| Stage | Avg Time | Memory | Notes |
|-------|----------|--------|-------|
| PDF Extraction | 0.5s | 10MB | PyMuPDF |
| Text Preprocessing | 0.01s | 1MB | Regex ops |
| TF-IDF Scoring | 0.1s | 50MB | Sparse matrix |
| SBERT Encoding | 0.3s | 200MB | Model inference |
| Gemini API Call | 2-5s | 10MB | Network + rate limit |
| Rule-Based Parse | 0.05s | 5MB | Regex matching |
| **Total** | **3-6s** | **~300MB** | |

---

## Error Handling Pipeline

```
[Input]
     ↓
[Validation]
     ├── File type check
     ├── Text extraction check
     └── Required fields check
     ↓
[Processing]
     ├── Try TF-IDF
     │   └── Fallback: return 0.0 if error
     ├── Try SBERT
     │   └── Fallback: return 0.0 if error
     ├── Try Gemini
     │   └── Fallback: empty recommendations
     └── Try Rule-Based
         └── Fallback: minimal resume structure
     ↓
[Response]
     ├── Always return valid JSON
     ├── Include error messages
     └── Graceful degradation