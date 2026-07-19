# Metodologi Scoring - CVision

## Bobot TF-IDF dan SBERT

### Default Weights:
```
TF-IDF Weight: 0.5 (50%)
SBERT Weight: 0.5 (50%)
```

### Hybrid Score Formula:
```python
hybrid_score = (tfidf_weight * tfidf_score) + (sbert_weight * sbert_score)
```

### Contoh Perhitungan:
```
TF-IDF Score: 0.75
SBERT Score: 0.85

Hybrid Score = (0.5 * 0.75) + (0.5 * 0.85)
             = 0.375 + 0.425
             = 0.80
```

---

## Persentase Kecocokan (Match Percentage)

### Formula:
```python
match_percentage = hybrid_score * 100
```

### Linear Scaling:
- **Transparent dan predictable**
- Same CV + same job = same score
- Tidak ada randomness atau faktor eksternal

### Contoh Perhitungan:
```
Hybrid Score: 0.80

Match Percentage = 0.80 * 100
                 = 80%
```

### Clamping:
```python
percentage = max(0.0, min(100.0, percentage))
```
- Minimum: 0%
- Maximum: 100%

### Rounding:
```python
percentage = round(percentage, 2)
```
- Dibatasi 2 decimal places
- Contoh: 87.654321 → 87.65%

---

## Contoh Lengkap

### Input:
```
CV Text: "Python developer with 3 years experience in Django and Flask"
Job Description: "Looking for Python developer with Django experience"

TF-IDF Score: 0.72
SBERT Score: 0.88
```

### Perhitungan:
```
1. Hybrid Score = (0.5 * 0.72) + (0.5 * 0.88)
                = 0.36 + 0.44
                = 0.80

2. Match Percentage = 0.80 * 100
                    = 80.00%
```

### Output:
```
TF-IDF Score: 0.72 (72%)
SBERT Score: 0.88 (88%)
Hybrid Score: 0.80 (80%)
Match Percentage: 80.00%
```

---

## Parameter yang Dapat Disesuaikan

### Di `python/services/similarity.py`:

```python
def calculate_hybrid_score(
    self,
    tfidf_score: float,
    sbert_score: float,
    tfidf_weight: float = 0.5,  # Bisa diubah (contoh: 0.3, 0.7)
    sbert_weight: float = 0.5   # Bisa diubah (contoh: 0.7, 0.3)
) -> float:
```

### Contoh Bobot Lain:

**Lebih fokus ke lexical matching (TF-IDF):**
```python
tfidf_weight = 0.7
sbert_weight = 0.3
```

**Lebih fokus ke semantic matching (SBERT):**
```python
tfidf_weight = 0.3
sbert_weight = 0.7
```

---

## TF-IDF Configuration

### Parameter:
```python
vectorizer = TfidfVectorizer(
    ngram_range=(1, 2),        # Unigrams + Bigrams
    min_df=1,                  # Minimum document frequency
    max_df=0.95,               # Maximum document frequency (remove common words)
    stop_words=None,           # Don't remove stop words
    max_features=30000,        # Maximum vocabulary size
    sublinear_tf=True,         # Use log scaling (1 + log(tf))
    norm='l2',                 # L2 normalization
)
```

### Alasan:
- **ngram_range=(1, 2)**: Capture phrases dan individual terms
- **stop_words=None**: Keep stop words untuk context
- **sublinear_tf=True**: Reduce impact of very frequent terms
- **norm='l2')**: Cosine similarity friendly

---

## SBERT Configuration

### Model:
```python
sbert_model_name = "all-MiniLM-L6-v2"
```

### Alasan:
- **all-MiniLM-L6-v2**: Lightweight, fast, good accuracy
- Alternative: `all-mpnet-base-v2` (more accurate, slower)

### Embedding:
```python
job_embedding = self.sbert_model.encode(clean_job)
cv_embedding = self.sbert_model.encode(clean_cv)

score = cosine_similarity([job_embedding], [cv_embedding])[0][0]
```

---

## Ringkasan

| Component | Formula | Weight |
|-----------|---------|--------|
| TF-IDF Score | Cosine similarity between TF-IDF vectors | 50% |
| SBERT Score | Cosine similarity between embeddings | 50% |
| Hybrid Score | (0.5 * TF-IDF) + (0.5 * SBERT) | 100% |
| Match Percentage | Hybrid Score * 100 | 0-100% |

---

## Catatan Penting

1. **Deterministic**: Same input = same output
2. **No randomness**: Tidak ada faktor acak
3. **Transparent**: Formula bisa dijelaskan dan diverifikasi
4. **Reproducible**: Hasil bisa direproduksi kapan saja

---

**Last Updated:** 2026-07-12
**Status:** Production Ready