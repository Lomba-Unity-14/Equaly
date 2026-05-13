# AI Matching Performance Bottleneck — Equaly

> **Dokumen**: Analisis bottleneck performa AI Matching setelah upgrade onboarding 7-step.  
> **Status**: Optimized (iteration 1 applied)  
> **Tanggal**: 10 Mei 2026  
> **Terkait**:
> - [`docs/research/ai-matching-system.md`](../research/ai-matching-system.md) — Arsitektur AI Matching
> - [`docs/research/onboarding-profiling-redesign.md`](../research/onboarding-profiling-redesign.md) — Desain onboarding 7-step

---

## Daftar Isi

1. [Timeline & Commit References](#1-timeline--commit-references)
2. [Symptom](#2-symptom)
3. [Architecture Comparison: Old vs New](#3-architecture-comparison-old-vs-new)
4. [Pre-Filter Comparison](#4-pre-filter-comparison)
5. [AI Prompt Comparison](#5-ai-prompt-comparison)
6. [Queue Architecture Comparison](#6-queue-architecture-comparison)
7. [Root Cause Rating](#7-root-cause-rating)
8. [Total Time Calculation](#8-total-time-calculation)
9. [Proposed Optimizations](#9-proposed-optimizations)
10. [Measurement Plan](#10-measurement-plan)
11. [Changelog](#11-changelog)

---

## 1. Timeline & Commit References

| Commit | Deskripsi | Tanggal |
|--------|-----------|---------|
| `cbf7593` | feat: fitur onboarding profiling (4 step) | 4 Mei 2026 |
| `5e7f996` | feat: fitur job matching | 6 Mei 2026 |
| `9cda5d1` | feat: Fitur skoring perusahaan | 6 Mei 2026 |
| `e16efc7` | feat: Update Onboarding agar lebih spesifik ke Tunarungu (7 step) | 9 Mei 2026 |

**Baseline pengukuran**:
- **Old** (`5e7f996`): 25—30 detik (onboarding 4-step + matching)
- **New** (`e16efc7`): 70 detik (onboarding 7-step + matching)

> **Catatan**: Commit `9cda5d1` (skoring perusahaan) ada di antara `5e7f996` dan `e16efc7` tetapi tidak mengubah file matching — `git diff 5e7f996 e16efc7 -- app/Jobs/MatchBatchJob.php` = **no changes**.

---

## 2. Symptom

Setelah upgrade onboarding ke 7 step (commit `e16efc7`):

| Metrik | Old (commit `5e7f996`) | New (commit `e16efc7`) | Delta |
|--------|------------------------|-------------------------|-------|
| **Total waktu** | ~25—30 detik | ~70 detik | +140% |
| **S1 jobs** | Sebagian besar masuk | Terfilter sebagian | OK (education filter works) |
| **S3 jobs** | Beberapa masuk (tidak relevan) | Tidak ada yang masuk | OK (education filter works) |

Pre-filter education sudah benar secara logika (S1 user tidak dapat job S3). Tapi performa memburuk drastis.

---

## 3. Architecture Comparison: Old vs New

### 3.1 Old Architecture (commit `5e7f996`)

```
┌──────────┐     ┌──────────────────────┐     ┌──────────────────┐
│ Onboarding│────▸│ MatchUserToJobs       │────▸│ MatchBatchJob × N │
│   save()  │     │ (SYNC - blocking)     │     │ (QUEUE - async)    │
│           │     │                       │     │                    │
│ Cache:    │     │ 1. Load user+profile  │     │ 1. Deepseek API    │
│ processing│     │ 2. Build userProfile  │     │ 2. updateOrCreate  │
│           │     │ 3. JobVacancy::all()  │     │ 3. Cache increment │
│           │     │ 4. preFilter()        │     │                    │
│           │     │ 5. Delete old matches │     │ ≡ 3 workers parallel│
│           │     │ 6. Chunk 10 → batches │     │                    │
│           │     │ 7. Dispatch batches   │     │                    │
└──────────┘     └──────────────────────┘     └──────────────────┘
     │                    │
     │  Blocking HTTP     │  Instan (pre-filter di request)
     │                    │
     ▼                    ▼
 Redirect: /matching   Queue Workers (3) pick up batches
 (poll 2s sampai       → proses paralel
  cache = completed)
```

**Karakteristik**:
- `MatchUserToJobs` **bukan** `ShouldQueue` — `handle()` dipanggil langsung via `(new MatchUserToJobs($uid))->handle()`
- Pre-filter dijalankan **dalam HTTP request** — near-instant
- Batch langsung di-`dispatch` ke queue table
- 3 queue workers (`queue:listen`) langsung memproses batch begitu job masuk

### 3.2 New Architecture (commit `e16efc7`)

```
┌──────────┐     ┌──────────────────────┐     ┌──────────────────┐
│ Onboarding│────▸│ MatchUserToJobs       │────▸│ MatchBatchJob × N │
│   save()  │     │ (QUEUE - async)       │     │ (QUEUE - async)    │
│           │     │ implements ShouldQueue │     │                    │
│ Cache:    │     │ $tries = 1            │     │ ≡ 3 workers parallel│
│ processing│     │ $timeout = 600s        │     │                    │
│           │     │                       │     │                    │
│ Dispatch  │     │ ⚠️ EXTRA: 1 round-trip│     │                    │
│ to queue  │     │  queue write → poll   │     │                    │
│           │     │  → worker read → exec │     │                    │
└──────────┘     └──────────────────────┘     └──────────────────┘
     │                    │
     │  HTTP return       │  Queue worker #1 picks up
     │  (non-blocking)    │  → pre-filter → dispatch batches
     │                    │
     ▼                    ▼
 Redirect: /matching    Workers #2, #3, #1 pick up batches
 (poll 2s sampai        → proses paralel
  cache = completed)
```

**Perubahan utama**:
- `MatchUserToJobs` sekarang `implements ShouldQueue` — satu extra putaran queue (tulis-jobs-table → poll → read → execute)
- Pre-filter dijalankan **dalam queue worker** (bukan HTTP request)
- User profile dikirim sebagai data serialized dalam queue payload

---

## 4. Pre-Filter Comparison

### 4.1 Logika Filter

| Dimensi | Old (`5e7f996`) | New (`e16efc7`) |
|---------|----------------|-----------------|
| **Work Environment** | `workMatch` | `workMatch` (keys berubah: `onsite` → 3 varian onsite) |
| **Skills** | `skillMatch` | `skillMatch` (sumber data berubah: free-text → structured) |
| **Location** | ❌ Tidak ada | ✅ `locationMatch` (13 lokasi Jabodetabek) |
| **Education** | ❌ Tidak ada | ✅ `passesEducationFilter()` (hard gatekeeper, 28 keywords) |
| **Logika** | `workMatch OR skillMatch` | `educationFilter AND (workMatch OR skillMatch OR locationMatch)` |

### 4.2 Dampak ke Jumlah Batch

**Old**: `work OR skill`
- User dengan 1-2 kategori skill + preferensi remote → ~40-60 jobs lolos → **4-6 batch**

**New**: `education AND (work OR skill OR location)`
- Education filter membuang job dengan requirement di atas level user (misal S1 user → S2/S3 jobs dibuang) → ~5-10% pengurangan
- Location ditambahkan sebagai OR → lebih banyak job lolos dari dimensi ini
- **Net effect**: jumlah batch kemungkinan **sama atau sedikit lebih banyak** dari old

### 4.3 Skill Keyword Extraction

| Aspek | Old | New |
|-------|-----|-----|
| Sumber data | `$profile->skills` (array flat, free text) | `$profile->skill_categories` (structured, categories + subs) |
| Ekstraksi keyword | `array_map(trim) + array_filter(strlen>=3)` | Iterate `OnboardingData::SKILL_CATEGORIES` + nested `OnboardingData::SKILL_SUBS` |
| Kompleksitas | O(n) flat array | O(categories × subs) dengan nested lookup |
| Overhead tambahan | N/A | `OnboardingData::SKILL_SUBS` di-loop untuk setiap sub-skill user (break setelah ketemu) |

**Catatan**: Ekstraksi keyword dilakukan **sekali**, sebelum filter closure. Bukan per-job bottleneck.

### 4.4 Education Filter Overhead

```php
// Dipanggil untuk SETIAP job (93x)
passesEducationFilter(userLevel, jobEducationReq)
  └─ foreach 28 keywords → stripos() per keyword
     Total: 93 × 28 = ~2,600 stripos() calls
```

Overhead: **dapat diabaikan** (< 1ms total). Bukan penyebab bottleneck.

---

## 5. AI Prompt Comparison

### 5.1 Token Estimation

| Bagian Prompt | Old (chars) | Old (est. tokens) | New (chars) | New (est. tokens) |
|---------------|:-----------:|:-----------------:|:-----------:|:-----------------:|
| System message | ~150 | ~35 | ~220 | ~55 |
| Hearing level | N/A | 0 | ~150 | ~40 |
| Communication | ~30 (raw values) | ~8 | ~150 (full labels) | ~40 |
| Work environment | ~30 (raw values) | ~8 | ~200 (accommodation labels) | ~50 |
| Skill categories | N/A | 0 | ~80 | ~20 |
| Sub-skills | ~50 (free text) | ~15 | ~120 | ~30 |
| Education | N/A | 0 | ~80 | ~20 |
| Job types | N/A | 0 | ~80 | ~20 |
| Locations | N/A | 0 | ~120 | ~30 |
| Age | N/A | 0 | ~15 | ~5 |
| **Profile total** | **~110** | **~30** | **~1,215** | **~310** |
| | | | | |
| Weighting section | ~180 | ~45 | ~600 | ~150 |
| Scoring guide | ~120 | ~30 | ~250 | ~65 |
| Response format | ~350 | ~90 | ~450 | ~115 |
| **Static sections total** | **~800** | **~200** | **~2,515** | **~630** |
| | | | | |
| Per job (10 jobs) | ~80/job | ~20/job | ~95/job (+company) | ~24/job |
| **10 jobs sub-total** | **~800** | **~200** | **~950** | **~240** |
| | | | | |
| **GRAND TOTAL** | **~1,600** | **~400-500** | **~3,500** | **~880-980** |

> Estimasi token menggunakan asumsi ~4 karakter = 1 token (bahasa Indonesia).

### 5.2 Prompt Diff (Side-by-Side)

#### System Message

| Old | New |
|-----|-----|
| `You are an expert job matching system for people with disabilities. You assess how well job vacancies match a candidate profile, paying special attention to disability compatibility.` | `You are an expert job matching system for deaf and hard-of-hearing individuals in Indonesia. You assess how well job vacancies match a deaf candidate profile, paying special attention to communication compatibility, disability accommodation, and skill alignment.` |

#### Candidate Profile

| Old | New |
|-----|-----|
| `Disability condition: Tunarungu` | `Disability: Tunarungu` |
| `Skills: web, developer, laravel, php` | `Hearing level: Tuli total — Tidak dapat mendengar suara sama sekali. Sepenuhnya bergantung pada komunikasi visual: bahasa isyarat, teks tertulis, atau notifikasi visual.` |
| `Communication preference: teks_tertulis, bibir` | `Communication preference: BISINDO (Bahasa Isyarat Indonesia), Teks tertulis (chat/email/dokumen)` |
| `Preferred work environment: remote` | `Preferred work environment & accommodation: Remote (WFH), On-site dengan juru bahasa isyarat` |
| _(tidak ada)_ | `Skill categories: IT & Programming, Desain & Kreatif` |
| _(tidak ada)_ | `Specific sub-skills: Web Developer, Backend Developer, UI/UX Designer` |
| _(tidak ada)_ | `Education: S1 / Sarjana — Teknik Informatika` |
| _(tidak ada)_ | `Preferred job types: Penuh Waktu (Full-time), Kontrak` |
| _(tidak ada)_ | `Preferred locations: Jakarta Selatan, Depok` |
| _(tidak ada)_ | `Age: 24 years old` |

#### Weighting

| Old (~45 tokens) | New (~150 tokens) |
|------------------|--------------------|
| `- Disability fit (35%): Can this person with their disability actually perform this job based on the job description?` | `- Disability fit (35%): Can this deaf/hard-of-hearing person effectively perform this job? Consider: Does the job require extensive verbal phone communication? Does it require constant in-person verbal meetings without accommodation? Or is it primarily text-based/visual? A job that requires heavy verbal communication should score LOW for deaf candidates. A job that can be done via text/visual means should score HIGH.` |
| `- Skill match (30%): Do their skills align with the required skills?` | `- Skill match (30%): Do the candidate's skill categories and sub-skills align with the job's required skills and category?` |
| `- Work environment (20%): Does the work type (Remote/Hybrid/On-site/Full-time/Contract) match their preference?` | `- Work environment (20%): Does the work type and location match the candidate's preferences? Consider: Remote vs on-site, Jabodetabek location match, job type (full-time/part-time/contract), and accommodation needs (sign language interpreter, visual notifications).` |
| `- Communication (10%): Does the job's communication demands match their preference (text/verbal/sign language)?` | `- Communication (10%): Does the job's communication demands match the candidate's specific communication methods? For example: a candidate using BISINDO (sign language) would match well with companies that provide sign language interpreters. A candidate preferring text-based communication would match well with remote/async jobs. A candidate who can lip-read may still manage some in-person roles.` |
| `- Education (5%): Does their education level match requirements?` | `- Education (5%): Does the candidate's education level and major match the job requirements?` |

#### Scoring Guide

| Old | New |
|-----|-----|
| `0-20: Not compatible at all` | `0-20: Not compatible at all — job requires extensive verbal communication without accommodation` |
| `21-40: Poor match` | `21-40: Poor match — significant communication or skill gaps` |
| `41-60: Somewhat compatible` | `41-60: Somewhat compatible — possible with accommodations` |
| `61-80: Good match` | `61-80: Good match — candidate can perform well with existing skills and communication methods` |
| `81-100: Excellent match` | `81-100: Excellent match — strong alignment across all dimensions` |

#### match_reason

| Old | New |
|-----|-----|
| `"<1-2 sentence summary in Bahasa Indonesia>"` | `"<1-2 sentence summary in Bahasa Indonesia explaining WHY this job is suitable or not suitable for this deaf candidate. Mention specific communication considerations.>"` |

#### Per-Job Listing

| Old | New |
|-----|-----|
| `- Title:` | `- Title:` |
| _(tidak ada)_ | `- Company:` ⬅️ **NEW** |
| `- Category:` | `- Category:` |
| `- Skills required:` | `- Skills required:` |
| `- Education:` | `- Education:` |
| `- Work type:` | `- Work type:` |
| `- Location:` | `- Location:` |
| `- Description:` | `- Description:` |

### 5.3 Temperature

| | Old | New |
|---|-----|-----|
| **Temperature** | `0.3` | `0.0` |
| **Dampak** | Slightly faster output generation (sampling-based) | Fully deterministic (beam/greedy) — marginally slower |

Temperature `0.0` memastikan output deterministik (fairness antar user) tapi tidak signifikan mempengaruhi total waktu.

---

## 6. Queue Architecture Comparison

### 6.1 Queue Workers

```bash
# composer.json "dev" script — 3 workers
php artisan queue:listen --tries=1 --timeout=0 --sleep=2  # worker-1
php artisan queue:listen --tries=1 --timeout=0 --sleep=2  # worker-2
php artisan queue:listen --tries=1 --timeout=0 --sleep=2  # worker-3
```

| Parameter | Nilai | Keterangan |
|-----------|-------|-----------|
| `--tries=1` | 1 | Job gagal → tidak di-retry oleh worker |
| `--timeout=0` | Unlimited | Worker tidak membatasi waktu eksekusi job |
| `--sleep=2` | 2 detik | Jeda polling saat queue kosong |
| Worker count | 3 | Pemrosesan paralel untuk batch jobs |

### 6.2 Job Lifecycle

#### Old Flow (MatchUserToJobs = SYNC)

```
T=0:    User klik save
T=0:    Cache: matching_status = 'processing'
T=0:    (new MatchUserToJobs($uid))->handle()
          ├─ Load user + profile          (~1ms)
          ├─ Build $userProfile[4 fields] (~1ms)
          ├─ JobVacancyData::all()        (~5ms, 93 rows)
          ├─ preFilter()                  (~5ms)
          ├─ Delete old matches           (~2ms)
          ├─ Chunk 10                     (~1ms)
          └─ Dispatch MatchBatchJob × N   (~5ms per dispatch)
T=0:    HTTP response: redirect /matching
T=~1s:  Worker-1 picks up MatchBatchJob #0 → API call (~10-15s)
T=~1s:  Worker-2 picks up MatchBatchJob #1 → API call (~10-15s)
T=~1s:  Worker-3 picks up MatchBatchJob #2 → API call (~10-15s)
T=~12s: Batch #0 selesai → Cache: done++
T=~12s: Batch #1 selesai → Cache: done++
T=~12s: Batch #2 selesai → Cache: done >= total → status = 'completed'
T=~12s: /matching polling detects 'completed' → redirect /beranda
```

**Total wall time: ~12—25 detik**
(bergantung jumlah batch; jika 6 batch → 2 round pemrosesan paralel)

#### New Flow (MatchUserToJobs = QUEUED)

```
T=0:    User klik save
T=0:    Cache: matching_status = 'processing'
T=0:    MatchUserToJobs::dispatch($uid) → INSERT ke jobs table
T=0:    HTTP response: redirect /matching
T=~2s:  Worker-1 polls, reads MatchUserToJobs dari jobs table
T=~2s:  Worker-1 runs MatchUserToJobs::handle()
          ├─ Load user + profile          (~1ms)
          ├─ Build $userProfile[11 fields] (~2ms)
          ├─ JobVacancyData::all()        (~5ms)
          ├─ preFilter() (+ education)    (~8ms)
          ├─ Delete old matches           (~2ms)
          ├─ Chunk 10                     (~1ms)
          └─ Dispatch MatchBatchJob × N   (~5ms per dispatch → INSERT ke jobs table)
T=~2s:  Worker-1 finishes MatchUserToJobs
T=~3s:  Worker-2 picks up MatchBatchJob #0 → API call (~20-25s with larger prompt)
T=~3s:  Worker-3 picks up MatchBatchJob #1 → API call (~20-25s)
T=~3s:  Worker-1 picks up MatchBatchJob #2 → API call (~20-25s)
T=~23s: Batch #0 selesai
T=~23s: Batch #1 selesai
T=~23s: Batch #2 selesai → done >= total → status = 'completed'
T=~23s: /matching polling detects 'completed' → redirect /beranda
```

**Total wall time: ~25—70 detik**
(3 batch: ~25s; 6 batch → 2 rounds parallel: ~48s; 9 batch → 3 rounds: ~72s)

### 6.3 Extra Overhead (Queued Orchestrator)

| Overhead | Estimasi | 
|----------|----------|
| Serialize MatchUserToJobs ke JSON | ~1ms |
| INSERT ke `jobs` table (SQLite) | ~5ms |
| Queue worker polling (~2s sleep) | 0—2s |
| Worker baca `jobs` table + delete | ~5ms |
| Unserialize MatchUserToJobs | ~1ms |
| **Total round-trip overhead** | **~2—4 detik** |

Overhead ini **tidak signifikan** untuk total waktu 70 detik.

---

## 7. Root Cause Rating

| # | Penyebab | Severity | Dampak Estimasi | 
|---|----------|:--------:|:---------------:|
| **1** | **Prompt size ~2x lebih besar** | 🔴 HIGH | +15—20s per API call |
| **2** | **Pre-filter OR logic bertambah → lebih banyak batch** | 🟡 MEDIUM | +1-3 batch (tergantung user) |
| **3** | **MatchUserToJobs now queued → 1 extra round-trip** | 🟢 LOW | +2-4s |
| **4** | **Temperature 0.3→0.0** | 🟢 LOW | ±1s |
| **5** | **Education filter (28 keywords × 93 jobs)** | 🟢 LOW | < 1ms |
| **6** | **`Company` field in job listing** | 🟢 LOW | +10 tokens per job |

### Root Cause #1: Prompt Size (🔴 PRIMARY)

**Detail**: Prompt baru ~980 tokens vs ~500 tokens lama → ~2x lebih besar.

**Mekanisme dampak**: Deepseek API memproses input tokens sebelum menghasilkan output. Lebih banyak input tokens = lebih banyak komputasi = lebih lama response time.

**Estimasi kuantitatif**:
- Old: ~500 input + ~500 output tokens = ~15 detik
- New: ~980 input + ~500 output tokens = ~20-25 detik
- **Delta per batch: +5-10 detik**
- Dengan 6 batch paralel dalam 2 ronde: **+10-20 detik total**

### Root Cause #2: Pre-Filter OR Logic (🟡 SECONDARY)

**Detail**: Menambahkan `location` sebagai dimensi OR artinya lebih banyak job lolos pre-filter. Lokasi adalah dimensi yang match rate-nya tinggi (13 lokasi Jabodetabek mencakup hampir semua job di dataset).

**Estimasi**: 
- Jika user pilih "Jakarta Selatan", mayoritas job di dataset memiliki lokasi yang mengandung "Jakarta Raya", "Jakarta Selatan", dll → banyak match
- Hasil: jumlah batch bisa 6-8 dari yang sebelumnya 4-6

### Root Cause #3: Queued Orchestrator (🟢 MINOR)

**Detail**: `MatchUserToJobs` berubah dari sync `->handle()` menjadi queued `::dispatch()`.

**Dampak**: 2-4 detik delay queue round-trip. Tidak signifikan untuk total 70 detik.

---

## 8. Total Time Calculation

### 8.1 Old System (25—30 detik)

| Komponen | Waktu |
|----------|:-----:|
| Onboarding save → pre-filter → dispatch batches | < 1s |
| Queue worker picks up batch #0 | ~1s |
| Queue worker picks up batch #1 | ~1s |
| Queue worker picks up batch #2 | ~1s |
| **API call per batch (old prompt)** | **~10—15s** |
| Batch #0-2 parallel (3 workers) | ~12s |
| Batch #3-5 parallel (round 2, if 6 batches) | ~12s |
| Database write (updateOrCreate) | ~1s |
| **TOTAL** | **~25—30s** |

### 8.2 New System (70 detik)

| Komponen | Waktu |
|----------|:-----:|
| Onboarding save → dispatch orchestrator to queue | < 1s |
| Queue worker polls orchestrator | ~2s |
| Orchestrator runs → pre-filter → dispatch batches | ~1s |
| Queue workers pick up batches | ~2s |
| **API call per batch (new prompt, 2x larger)** | **~20—25s** |
| Batch #0-2 parallel (3 workers, round 1) | ~23s |
| Batch #3-5 parallel (round 2, if 6 batches) | ~23s |
| Batch #6-8 parallel (round 3, if 9 batches) | ~23s |
| Database write (updateOrCreate) | ~1s |
| **TOTAL (3 batch)** | **~28s** |
| **TOTAL (6 batch)** | **~51s** |
| **TOTAL (9 batch)** | **~74s** |

### 8.3 Kesimpulan

70 detik terjadi jika:
1. **Prompt 2x lebih besar** → per-batch API call naik dari ~12s ke ~23s
2. **Jumlah batch cukup banyak** (6-9 batch) karena pre-filter OR logic yang lebih lebar

Kombinasi 2 faktor di atas: 3 ronde × 23s per ronde = **~69 detik** ≈ **70 detik**.

---

## 9. Proposed Optimizations

### 9.1 Optimization #1: Slim Prompt (🔴 HIGH IMPACT)

**Target**: Kurangi prompt size dari ~980 token ke ~500-600 token.

**Cara**:
```diff
- Hearing level: Tuli total — Tidak dapat mendengar suara sama sekali. Sepenuhnya
- bergantung pada komunikasi visual: bahasa isyarat, teks tertulis, atau notifikasi visual.
+ Hearing level: Tuli total (completely deaf — visual communication only)

- Communication preference: BISINDO (Bahasa Isyarat Indonesia), Teks tertulis
- (chat/email/dokumen)
+ Communication: BISINDO, Teks tertulis

- Preferred work environment & accommodation: Remote (WFH), On-site dengan
- juru bahasa isyarat
+ Work environment: Remote (WFH), On-site (sign language interpreter)
```

**Weighting section**: Kembalikan ke format concise seperti commit `5e7f996`.

**match_reason**: Hapus instruksi tambahan yang verbose.

**Estimasi penghematan**: ~300-400 token → per-batch API call turun ~5-8 detik.

### 9.2 Optimization #2: Pre-Filter AND Logic (🟡 MEDIUM IMPACT)

**Target**: Kurangi jumlah batch dengan filter yang lebih selektif.

**Usulan**: Ubah pre-filter OR menjadi AND untuk dimensi skill + location:
```
educationFilter AND workMatch AND (skillMatch OR locationMatch)
```
Atau tetap OR tapi dengan bobot: priority matching — prioritaskan job yang match di 2+ dimensi untuk dikirim ke AI.

**Trade-off**: Lebih sedikit job dikirim ke AI = lebih cepat tapi potensi melewatkan good matches.

### 9.3 Optimization #3: Kembalikan MatchUserToJobs ke Sync (🟢 LOW IMPACT)

**Target**: Hapus 1 queue round-trip.

**Cara**: Buat `MatchUserToJobs` non-ShouldQueue kembali, panggil `->handle()` langsung dari Onboarding/Profil/Matching.

**Trade-off**: HTTP request akan block selama pre-filter (~10ms) — tidak masalah. Tapi jika di masa depan pre-filter butuh waktu lama (misal thousands of jobs), ini bisa jadi masalah.

### 9.4 Optimization #4: Batch Size Adjustment (🟡 MEDIUM IMPACT)

**Target**: Eksperimen dengan batch size untuk optimal throughput.

| Batch Size | Jobs/Batch | Batches (40 jobs) | API Calls | Parallel Rounds | Est. Time |
|:----------:|:----------:|:-----------------:|:---------:|:---------------:|:---------:|
| 5 | 5 | 8 | 8 | 3 | 3 × 18s = 54s |
| 10 (current) | 10 | 4 | 4 | 2 | 2 × 23s = 46s |
| 15 | 15 | 3 | 3 | 1 | 1 × 30s = 30s |
| 20 | 20 | 2 | 2 | 1 | 1 × 35s = 35s |

> **Catatan dari research**: Batch size 15 → 28s per call vs 10 → 15s per call. Efisiensi per-job turun 25% pada batch size lebih besar. Tapi throughput total bisa lebih baik karena lebih sedikit round-trip.

### 9.5 Prioritas Optimasi

| Prioritas | Optimasi | Est. Penghematan | Kompleksitas |
|:---------:|----------|:----------------:|:------------:|
| **1** | Slim prompt (#1) | **-15—20s** | Rendah |
| **2** | Pre-filter AND logic (#2) | **-10—25s** (kurangi batch) | Sedang |
| **3** | Batch size 15 (#4) | **-15—20s** | Rendah |
| **4** | Sync orchestrator (#3) | **-2—4s** | Rendah |

**Target setelah optimasi**: Kembali ke 25—30 detik.

---

## 10. Measurement Plan

### 10.1 Cara Mengukur

1. **Tambahkan timing log** di `MatchUserToJobs::handle()`:
   ```php
   $start = microtime(true);
   // ... pre-filter, dispatch batches ...
   Log::info("MatchUserToJobs orchestrator: " . (microtime(true) - $start) . "s");
   ```

2. **Tambahkan timing log** di `MatchBatchJob::handle()`:
   ```php
   $start = microtime(true);
   $results = $ai->matchUserToJobs(...);
   Log::info("MatchBatchJob API call batch {$this->batchIndex}: " . (microtime(true) - $start) . "s");
   ```

3. **Pantau jumlah batch**:
   ```php
   Log::info("MatchUserToJobs: user {$uid} — {$jobs->count()} jobs → " . count($filteredJobs) . " after pre-filter → " . count($batches) . " batches");
   ```

### 10.2 Metrik yang Dipantau

| Metrik | Baseline (Old) | Current (New) | Target |
|--------|:--------------:|:-------------:|:------:|
| Pre-filter time | < 5ms | ~8ms | < 5ms |
| API call per batch | ~10-15s | ~20-25s | ~10-15s |
| Jumlah batch | 4-6 | 6-9 | 3-5 |
| Total wall time | 25-30s | 70s | 25-30s |

---

## 11. Changelog

| Tanggal | Versi | Perubahan | Author |
|---------|:-----:|-----------|--------|
| 10 Mei 2026 | 1.0 | Initial analysis — identifikasi prompt size sebagai primary bottleneck | Yazid |
| 10 Mei 2026 | 1.1 | Iterasi optimasi #1 — slim prompt + batch 15 + sync orchestrator | Yazid |

---

## 12. Iterasi Optimasi #1 (10 Mei 2026)

### Perubahan yang Dilakukan

| # | File | Perubahan | Target Penghematan |
|---|------|-----------|:------------------:|
| 1 | `app/Services/DeepseekService.php` | **Slim prompt**: hapus hearing description (150 chars), kompres weighting ke 1 kalimat per dimensi, scoring guide jadi 1 baris, hapus `Company` dari job listing, system message lebih pendek, `match_reason` dari 1-2 sentence ke 1 sentence | **±15—20s** |
| 2 | `app/Jobs/MatchUserToJobs.php` | **Batch size 10→15**: `array_chunk($filteredJobs, 15)` → lebih sedikit ronde paralel. 30 job: 3 batch (1 ronde) vs 3 batch sebelumnya | **±5—10s** |
| 3 | `app/Jobs/MatchUserToJobs.php` | **Sync orchestrator**: hapus `implements ShouldQueue` + traits queue. Kembalikan `MatchUserToJobs` ke non-queued class. Pre-filter + dispatch batch terjadi langsung di HTTP request | **±2—4s** |
| 4 | `app/Livewire/Onboarding.php` | **Panggil `handle()` langsung**: `(new MatchUserToJobs(auth()->id()))->handle()` | Terkait #3 |
| 5 | `app/Livewire/Profil.php` | **Panggil `handle()` langsung**: `(new MatchUserToJobs(auth()->id()))->handle()` | Terkait #3 |
| 6 | `app/Livewire/Matching.php` | **Panggil `handle()` langsung**: `(new MatchUserToJobs(auth()->id()))->handle()` | Terkait #3 |

### Estimasi Total Penghematan

| Skenario | Sebelum | Sesudah |
|----------|:------:|:------:|
| 30 job filtered (2 batch × 15) | 2 × 23s + 4s overhead = **50s** | 2 × 12s + 0s overhead = **24s** |
| 45 job filtered (3 batch × 15) | 3 × 23s + 4s overhead = **73s** | 3 × 12s + 0s overhead = **36s** |
| 60 job filtered (4 batch × 10 old) | 4 × 12s + 0s old overhead = **48s** | — |

### Cara Verifikasi

```bash
# Jalankan composer run dev (3 queue workers)
composer run dev

# Lakukan onboarding baru dan ukur waktu dari klik "Simpan & Mulai Pencocokan"
# sampai redirect ke /beranda

# Atau pantau log:
tail -f storage/logs/laravel.log | grep Match
```

### Catatan

- **Onboarding akan sedikit lebih lambat** (100—200ms) karena pre-filter berjalan di HTTP request. Tapi tidak signifikan secara UX.
- **Temperature tetap 0.0** — deterministik output penting untuk fairness scoring antar user.
- Pre-filter logic **tidak diubah** — hanya batch size yang dinaikkan.
- Jika hasil scoring berubah (karena prompt lebih ringkas), adjust ulang prompt di iterasi berikutnya.
