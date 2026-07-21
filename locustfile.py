"""
CVision Load Testing - Locust
==============================
Target:
  - Concurrent users : 100
  - Ramp-up          : 15 user/detik
  - Avg response     : <= 3 detik
  - Median (P50)     : <= 2.0 detik
  - P95              : <= 5 detik
  - P99              : <= 6.0 detik
  - Failure rate     : <= 2%
  - RPS              : >= 50-80 req/s

Cara jalankan:
  locust -f locustfile.py --host=http://127.0.0.1:8000

Lalu buka browser: http://localhost:8089
Isi:
  - Number of users  : 100
  - Spawn rate       : 15
  - Host             : http://127.0.0.1:8000
"""

import os
import random
from locust import HttpUser, task, between, events
from locust.exception import RescheduleTask


# ── Akun pelamar untuk testing ─────────────────────────────────────────────
# Pastikan akun-akun ini sudah terdaftar di database sebelum menjalankan test.
# Minimal 1 akun sudah cukup, tapi lebih banyak lebih realistis.
TEST_ACCOUNTS = [
    {"email": "helena@gmail.com",   "password": "helena123"},
    {"email": "test1@cvision.com",  "password": "test1234"},
    {"email": "test2@cvision.com",  "password": "test1234"},
    {"email": "test3@cvision.com",  "password": "test1234"},
    {"email": "test4@cvision.com",  "password": "test1234"},
]

# ID job yang tersedia di database (sesuaikan dengan ID di tabel upload_jobs kamu)
JOB_IDS = [1, 2, 3]

# Path file CV dummy untuk upload (bisa pakai file PDF kecil)
CV_FILE_PATH = os.path.join(os.path.dirname(__file__), "dummy_cv.pdf")


def get_csrf_token(response_text: str) -> str:
    """Ambil CSRF token dari HTML response."""
    import re
    match = re.search(r'<meta name="csrf-token" content="([^"]+)"', response_text)
    if match:
        return match.group(1)
    # Coba cari dari input hidden
    match = re.search(r'name="_token"[^>]*value="([^"]+)"', response_text)
    if match:
        return match.group(1)
    return ""


class CVisionUser(HttpUser):
    """
    Simulasi perilaku pengguna CVision:
    1. Login
    2. Upload CV (butuh session yang sudah login)
    """

    wait_time = between(1, 3)  # jeda antar task (1-3 detik) — lebih realistis

    def on_start(self):
        """
        Dipanggil sekali saat setiap virtual user mulai.
        Lakukan login dan simpan session-nya.
        """
        self.account = random.choice(TEST_ACCOUNTS)
        self.logged_in = False
        self.csrf_token = ""
        self._do_login()

    def _do_login(self):
        """Login dan simpan CSRF token dari session cookie."""

        # Step 1: GET halaman login dulu untuk ambil CSRF token
        with self.client.get(
            "/login",
            name="[Setup] GET /login",
            catch_response=True
        ) as resp:
            if resp.status_code != 200:
                resp.failure(f"GET /login gagal: {resp.status_code}")
                return
            self.csrf_token = get_csrf_token(resp.text)

        if not self.csrf_token:
            # Coba ambil dari cookie
            self.csrf_token = self.client.cookies.get("XSRF-TOKEN", "")

        # Step 2: POST login
        with self.client.post(
            "/login",
            data={
                "_token":  self.csrf_token,
                "email":   self.account["email"],
                "password": self.account["password"],
                "login_as": "pelamar",
            },
            headers={"X-CSRF-TOKEN": self.csrf_token},
            name="POST /login",
            allow_redirects=True,
            catch_response=True,
        ) as resp:
            # Laravel redirect ke / setelah login sukses
            if resp.status_code in (200, 302):
                # Cek apakah masih di halaman login (artinya gagal)
                if "/login" in resp.url and "email" in resp.text.lower() and "invalid" in resp.text.lower():
                    resp.failure("Login gagal: kredensial tidak valid")
                    self.logged_in = False
                else:
                    resp.success()
                    self.logged_in = True
                    # Perbarui CSRF token dari halaman setelah login
                    self.csrf_token = get_csrf_token(resp.text) or self.csrf_token
            else:
                resp.failure(f"Login HTTP error: {resp.status_code}")
                self.logged_in = False

    @task(1)
    def login(self):
        """
        Task: Login.
        Bobot 1 — dijalankan 1x dibanding upload_cv.
        Simulasi: user membuka halaman login, lalu submit form.
        """
        # GET halaman login
        with self.client.get(
            "/login",
            name="GET /login",
            catch_response=True
        ) as resp:
            if resp.status_code == 200:
                resp.success()
                token = get_csrf_token(resp.text) or self.csrf_token
            else:
                resp.failure(f"GET /login {resp.status_code}")
                return

        account = random.choice(TEST_ACCOUNTS)

        # POST login
        with self.client.post(
            "/login",
            data={
                "_token":   token,
                "email":    account["email"],
                "password": account["password"],
                "login_as": "pelamar",
            },
            headers={"X-CSRF-TOKEN": token},
            name="POST /login",
            allow_redirects=True,
            catch_response=True,
        ) as resp:
            if resp.status_code in (200, 302):
                if "The provided credentials" in resp.text or "These credentials" in resp.text:
                    resp.failure("Login gagal: invalid credentials")
                else:
                    resp.success()
            else:
                resp.failure(f"POST /login HTTP {resp.status_code}")

    @task(2)
    def upload_cv(self):
        """
        Task: Upload CV.
        Bobot 2 — dijalankan 2x lebih sering dari login.
        Hanya dijalankan bila sudah login (self.logged_in = True).
        """
        if not self.logged_in:
            self._do_login()
            if not self.logged_in:
                raise RescheduleTask()

        # Perbarui CSRF token
        with self.client.get(
            "/",
            name="GET / (refresh token)",
            catch_response=True
        ) as resp:
            if resp.status_code == 200:
                resp.success()
                token = get_csrf_token(resp.text) or self.csrf_token
            else:
                resp.failure(f"GET / {resp.status_code}")
                raise RescheduleTask()

        job_id = random.choice(JOB_IDS)

        # Buat file dummy kalau tidak ada
        if not os.path.exists(CV_FILE_PATH):
            _create_dummy_pdf(CV_FILE_PATH)

        with open(CV_FILE_PATH, "rb") as f:
            cv_bytes = f.read()

        # POST upload-cv
        with self.client.post(
            "/upload-cv",
            files={
                "cv_file": ("dummy_cv.pdf", cv_bytes, "application/pdf"),
            },
            data={
                "_token":        token,
                "upload_job_id": job_id,
            },
            headers={"X-CSRF-TOKEN": token},
            name="POST /upload-cv",
            allow_redirects=True,
            catch_response=True,
        ) as resp:
            if resp.status_code in (200, 302):
                # Cek apakah redirect ke login (session expired)
                if "/login" in resp.url:
                    self.logged_in = False
                    resp.failure("Session expired, diarahkan ke login")
                elif "successfully" in resp.text or resp.status_code == 302:
                    resp.success()
                else:
                    # Bisa jadi validasi error tapi request tetap sampai
                    resp.success()
            elif resp.status_code == 422:
                # Validation error — request sampai tapi data tidak valid
                resp.failure(f"Validation error 422: {resp.text[:200]}")
            elif resp.status_code == 419:
                # CSRF token mismatch
                self.csrf_token = ""
                self.logged_in = False
                resp.failure("CSRF token expired (419)")
            else:
                resp.failure(f"Upload CV HTTP {resp.status_code}")


def _create_dummy_pdf(path: str):
    """
    Buat file PDF minimal valid (tanpa library tambahan).
    Ukuran kecil (~1 KB) agar tidak mempengaruhi network secara signifikan.
    """
    minimal_pdf = b"""%PDF-1.4
1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj
2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj
3 0 obj<</Type/Page/MediaBox[0 0 612 792]/Parent 2 0 R/Resources<<>>>>endobj
xref
0 4
0000000000 65535 f
0000000009 00000 n
0000000058 00000 n
0000000115 00000 n
trailer<</Size 4/Root 1 0 R>>
startxref
204
%%EOF"""
    with open(path, "wb") as f:
        f.write(minimal_pdf)
    print(f"[locust] dummy_cv.pdf dibuat di: {path}")


# ── Event hooks (opsional) ─────────────────────────────────────────────────

@events.test_start.add_listener
def on_test_start(environment, **kwargs):
    print("\n" + "="*60)
    print("CVision Load Test dimulai")
    print(f"Target: 100 users | Ramp-up: 15/s")
    print(f"Endpoint: POST /login, POST /upload-cv")
    print("="*60 + "\n")

    # Buat dummy CV jika belum ada
    if not os.path.exists(CV_FILE_PATH):
        _create_dummy_pdf(CV_FILE_PATH)


@events.test_stop.add_listener
def on_test_stop(environment, **kwargs):
    stats = environment.stats
    total = stats.total
    print("\n" + "="*60)
    print("CVision Load Test selesai — Ringkasan:")
    print(f"  Total requests    : {total.num_requests}")
    print(f"  Total failures    : {total.num_failures}")
    print(f"  Failure rate      : {(total.num_failures / max(total.num_requests, 1) * 100):.2f}%")
    print(f"  Avg response time : {total.avg_response_time:.0f} ms")
    print(f"  RPS               : {total.current_rps:.1f} req/s")
    print("="*60 + "\n")