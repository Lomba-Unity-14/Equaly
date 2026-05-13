# Sistem AI Matching — Equaly

> **Dokumen acuan**: Cara kerja lengkap AI Matching Equaly — dari trigger, pre-filter, batch processing, AI prompt, scoring, hingga penampilan hasil.  
> **Status**: Final  
> **Tanggal**: 9 Mei 2026  
> **Terkait**:
> - [`tunarungu-employment-research.md`](./tunarungu-employment-research.md) — Riset ketenagakerjaan tunarungu
> - [`onboarding-profiling-redesign.md`](./onboarding-profiling-redesign.md) — Struktur onboarding baru

---

## Daftar Isi

1. [Arsitektur & Flow](#1-arsitektur--flow)
2. [Trigger Points](#2-trigger-points)
3. [State Machine (Cache)](#3-state-machine-cache)
4. [Pre-Filter](#4-pre-filter)
5. [Batch Processing](#5-batch-processing)
6. [AI Service (DeepseekService)](#6-ai-service-deepseekservice)
7. [Prompt Structure](#7-prompt-structure)
8. [5 Dimensi Scoring](#8-5-dimensi-scoring)
9. [Penyimpanan Hasil](#9-penyimpanan-hasil)
10. [Penampilan Hasil](#10-penampilan-hasil)
11. [Edge Cases & Error Handling](#11-edge-cases--error-handling)

---

## 1. Arsitektur & Flow

```
                          ┌─────────────────────────────────┐
                          │         TRIGGER POINTS           │
                          │  Onboarding Save · Rematch ·     │
                          │  Matching Retry · Profile Edit   │
                          └─────────────┬───────────────────┘
                                        │
                                        ▼
                          ┌─────────────────────────────────┐
                          │     Cache: matching_status =     │
                          │         "processing"             │
                          └─────────────┬───────────────────┘
                                        │
                          ┌─────────────▼───────────────────┐
                          │     MatchUserToJobs (Queue)      │
                          │   · Load User + Profile          │
                          │   · Build $userProfile array     │
                          │   · Load all JobVacancyData      │
                          │   · Hapus match lama user        │
                          └─────────────┬───────────────────┘
                                        │
                          ┌─────────────▼───────────────────┐
                          │          PRE-FILTER              │
                          │   3 Dimensi: Work Env · Skills   │
                          │   · Location                    │
                          │   Logika: OR                    │
                          │   Tujuan: Kurangi job → AI       │
                          └─────────────┬───────────────────┘
                                        │
                          ┌─────────────▼───────────────────┐
                          │     Chunk filtered jobs → 10/batch│
                          │   Dispatch MatchBatchJob × N    │
                          │   Cache: matching_total{N} = N   │
                          └─────────────┬───────────────────┘
                                        │
                    ┌───────────────────┼───────────────────┐
                    ▼                   ▼                   ▼
            ┌──────────────┐   ┌──────────────┐   ┌──────────────┐
            │ MatchBatchJob│   │ MatchBatchJob│   │ MatchBatchJob│
            │   Batch 0    │   │   Batch 1    │   │   Batch N    │
            │ (10 jobs)    │   │ (10 jobs)    │   │ (≤10 jobs)   │
            └──────┬───────┘   └──────┬───────┘   └──────┬───────┘
                   │                  │                  │
                   ▼                  ▼                  ▼
            ┌──────────────────────────────────────────────────┐
            │            DeepseekService                       │
            │   · POST https://api.deepseek.com/v1/            │
            │     chat/completions                             │
            │   · buildMatchingPrompt(userProfile, batch)      │
            │   · Parse JSON response → matches[]              │
            └──────────────────────┬───────────────────────────┘
                                   │
                                   ▼
            ┌──────────────────────────────────────────────────┐
            │         JobUserMatch::updateOrCreate()           │
            │   Per job: match_score, is_match,                │
            │   5 sub-scores, match_reason, calculated_at       │
            └──────────────────────┬───────────────────────────┘
                                   │
                                   ▼
            ┌──────────────────────────────────────────────────┐
            │         Cache: matching_done{N}++                │
            │   Jika done{N} >= total{N} → status = "completed" │
            └──────────────────────┬───────────────────────────┘
                                   │
                                   ▼
            ┌──────────────────────────────────────────────────┐
            │         Halaman /matching (polling 2s)           │
            │   Cache "completed" → redirect /beranda          │
            │   Cache "failed" → tombol "Coba Lagi"            │
            │   90s timeout → auto "failed"                    │
            └──────────────────────┬───────────────────────────┘
                                   │
                                   ▼
            ┌──────────────────────────────────────────────────┐
            │              PENAMPILAN HASIL                    │
            │   /beranda     → Top 6 (is_match=true, desc)     │
            │   /lowongan    → Semua + badge skor              │
            │   /lowongan/id → Full breakdown 5 sub-skor       │
            └──────────────────────────────────────────────────┘
```

---

## 2. Trigger Points

AI Matching dipicu dari **4 titik** dalam aplikasi. Dua menggunakan dispatch **async via queue**, dua menggunakan dispatch **sync** (langsung).

### 2.1 Onboarding Save (`app/Livewire/Onboarding.php:98`)

```php
Cache::put('matching_status_' . auth()->id(), 'processing', now()->addMinutes(10));
MatchUserToJobs::dispatch(auth()->id());
$this->redirect(route('matching'), navigate: true);
```

- **Async** — dispatch ke queue, user langsung diredirect ke `/matching`
- **Kapan**: User klik "Simpan & Mulai Pencocokan" di Step 7 onboarding
- **Prasyarat**: Semua 7 step divalidasi sukses, profil tersimpan dengan `onboarding_completed = true`

### 2.2 Profil Rematch (`app/Livewire/Profil.php:148`)

```php
Cache::put('matching_status_' . auth()->id(), 'processing', now()->addMinutes(10));
MatchUserToJobs::dispatch(auth()->id());
$this->redirect(route('matching'), navigate: true);
```

- **Async** — dispatch ke queue
- **Kapan**: User klik "Cocokkan Ulang" di halaman profil setelah mengedit data AI Matching
- **Prasyarat**: `$needsRematch = true` (diset setelah `saveSection()` berhasil)

### 2.3 Matching Retry (`app/Livewire/Matching.php:69`)

```php
Cache::put('matching_status_' . auth()->id(), 'processing', now()->addMinutes(10));
MatchUserToJobs::dispatch(auth()->id());
```

- **Async** — dispatch ke queue
- **Kapan**: User klik "Coba Lagi" setelah status matching `failed` atau timeout 90 detik
- **Prasyarat**: Status cache = `failed`

### 2.4 Matching `mount()` — auto-dispatch (`app/Livewire/Matching.php:37`)

```php
if ($cached !== 'processing') {
    $this->dispatchJob(); // Memanggil MatchUserToJobs::dispatch()
}
```

- **Async** — dispatch ke queue
- **Kapan**: Saat halaman `/matching` di-mount dan tidak ada status `processing` di cache (fallback untuk kasus di mana job belum di-dispatch sebelumnya)
- **Prasyarat**: Cache tidak mengandung `processing` atau `completed`

### Urutan Prioritas Trigger

| Prioritas | Trigger | Method | Async? |
|:---------:|---------|--------|:------:|
| 1 | Onboarding save | `::dispatch()` | ✅ Queue |
| 2 | Profil rematch | `::dispatch()` | ✅ Queue |
| 3 | Matching retry | `::dispatch()` | ✅ Queue |
| 4 | Matching mount auto-dispatch | `::dispatch()` | ✅ Queue |

> **Perubahan dari versi lama**: Sebelumnya Onboarding dan Profil rematch memanggil `->handle()` secara **synchronous** (blocking). Sekarang semua trigger menggunakan async dispatch untuk menghindari timeout HTTP request.

---

## 3. State Machine (Cache)

Proses AI Matching dikelola melalui **3 cache keys** berbasis user ID. Tidak ada state yang disimpan di database.

### 3.1 Cache Keys

| Key | Contoh | Nilai | TTL | Keterangan |
|-----|--------|-------|-----|-----------|
| `matching_status_{uid}` | `matching_status_2` | `processing` / `completed` / `failed` | 10 menit (processing), 24 jam (completed), 1 jam (failed) | Status utama yang di-polling oleh halaman `/matching` |
| `matching_done_{uid}` | `matching_done_2` | Integer counter | 10 menit | Jumlah batch yang sudah selesai diproses |
| `matching_total_{uid}` | `matching_total_2` | Integer | 10 menit | Total batch yang dikirim |

### 3.2 State Transition Diagram

```
                          ┌─────────────┐
                          │  (initial)  │
                          │   no cache  │
                          └──────┬──────┘
                                 │
                    ┌────────────▼────────────┐
                    │      "processing"        │◄──────────────┐
                    │  Cache TTL: 10 menit     │               │
                    │  done = 0, total = N     │               │
                    └────────────┬────────────┘               │
                                 │                             │
                    ┌────────────┼────────────┐               │
                    │            │            │               │
                    ▼            ▼            ▼               │
              ┌──────────┐ ┌──────────┐ ┌──────────┐        │
              │ Batch 0  │ │ Batch 1  │ │ Batch N  │        │
              │  selesai │ │  selesai │ │  selesai │        │
              │ done++   │ │ done++   │ │ done++   │        │
              └────┬─────┘ └────┬─────┘ └────┬─────┘        │
                   │            │            │               │
                   └────────────┼────────────┘               │
                                │                             │
                    ┌───────────▼───────────┐                │
                    │   done >= total ?     │                │
                    └───────────┬───────────┘                │
                                │                             │
                    ┌───────────┼───────────┐                │
                    │ YES       │           │ NO             │
                    ▼           │           ▼                │
            ┌─────────────┐    │    ┌─────────────┐         │
            │ "completed" │    │    │ (wait for   │         │
            │ TTL: 24 jam │    │    │  next batch)│─────────┘
            └──────┬──────┘    │    └─────────────┘
                   │           │
                   ▼           ▼
            ┌──────────┐ ┌──────────┐
            │ Redirect │ │ "failed" │
            │ /beranda │ │ TTL: 1hr │
            └──────────┘ └────┬─────┘
                              │
                    ┌─────────▼─────────┐
                    │  Tombol Retry     │
                    │  Set ke           │
                    │  "processing"     │──────┐
                    └───────────────────┘      │
                                               │
                    ┌──────────────────────────┘
                    │
                    ▼
            ┌───────────────┐
            │ Timeout 90 detik│
            │ (dari mount)   │
            │ → "failed"     │
            └────────────────┘
```

### 3.3 Polling di Halaman `/matching`

```php
// app/Livewire/Matching.php:41
public function checkStatus(): void
{
    $this->progressStep = ($this->progressStep + 1) % 5;  // 5 pesan animasi

    $cached = Cache::get('matching_status_' . auth()->id());

    if ($cached === 'completed') {
        $this->redirect(route('beranda'), navigate: true);  // ✅ Sukses
    }

    if ($cached === 'failed') {
        $this->status = 'failed';  // ❌ Tampilkan tombol retry
    }

    if (time() - $this->startedAt > 90) {
        $this->status = 'failed';  // ⏱ Timeout
    }
}
```

- **Interval**: `wire:poll.2s` — setiap 2 detik
- **Timeout**: 90 detik dari `mount()`
- **Progress messages**: 5 pesan bergantian yang disiklus (`$progressStep % 5`)
- **Konsekuensi timeout**: Status dipaksa `failed`, tapi job di queue tetap berjalan. User bisa klik retry.

---

## 4. Pre-Filter

Pre-filter adalah **filter lokal (tanpa AI)** yang mengurangi jumlah job yang dikirim ke Deepseek API. Tujuannya: **menghemat biaya API call** dan mempercepat proses.

**File**: `app/Jobs/MatchUserToJobs.php:146` (method `preFilter()`)

### 4.1 Tiga Dimensi Filter

| Dimensi | Data User | Data Job | Cara Mencocokkan |
|---------|-----------|----------|-----------------|
| **Work Environment** | `work_environment` (5 opsi: remote, hybrid, onsite_juru_isyarat, onsite_notifikasi_visual, onsite_standar) | `work_type` (Full time, Kontrak/Temporer) | Mapping keyword: remote→Remote/WFH/Work From Home; hybrid→Hybrid; semua onsite→Full time/Kontrak. `stripos()` pada `job.work_type`. |
| **Skills** | `skill_categories` (8 kategori) + `sub_skills` (sub-keahlian) | `skill_req` | Ekstrak kata-kata dari label kategori dan sub-keahlian (min 3 karakter). `str_contains()` pada `job.skill_req` (lowercase). |
| **Location** | `preferred_locations` (13 lokasi Jabodetabek) | `location` | Konversi value lokasi ke label (misal `jakarta_selatan` → `Jakarta Selatan`). `stripos()` pada `job.location`. |

### 4.2 Logika OR

```php
return $workMatch || $skillMatch || $locationMatch;
```

Job lolos jika **minimal SATU** dimensi cocok. Tidak harus semua. Jika user belum mengisi suatu dimensi (array kosong), dimensi itu dianggap `true` (tidak mem-filter).

### 4.3 Work Environment Mapping

| Preferensi User | Mencocokkan Job dengan work_type mengandung |
|-----------------|-------------------------------------------|
| `remote` | `Remote`, `WFH`, `Work From Home`, `Kerja dari rumah` |
| `hybrid` | `Hybrid` |
| `onsite_juru_isyarat` | `On-site`, `On site`, `Full time`, `Full-time`, `Fulltime`, `Kontrak/Temporer` |
| `onsite_notifikasi_visual` | (sama seperti di atas) |
| `onsite_standar` | (sama seperti di atas) |

> **Catatan**: Ketiga opsi on-site memiliki mapping yang sama karena perbedaan akomodasi (juru isyarat vs notifikasi visual) dinilai oleh AI, bukan pre-filter.

### 4.4 Skill Keyword Extraction

Pre-filter mengekstrak kata-kata individual dari label untuk mencocokkan dengan `skill_req`:

```php
// Contoh: kategori "IT & Programming" → keyword: ["IT", "Programming"]
// Sub-keahlian "Web Developer" → keyword: ["Web", "Developer"]
// Sub-keahlian "Backend Developer" → keyword: ["Backend", "Developer"]
```

Kata yang kurang dari 3 karakter diabaikan. Semua keyword dijadikan lowercase dan di-unique.

### 4.5 Lokasi Mapping

```php
// User pilih ['jakarta_selatan', 'depok']
// → Cari job yang location-nya mengandung "Jakarta Selatan" atau "Depok"
```

### 4.6 Statistik Pre-Filter (berdasarkan data testing 93 jobs)

| User | Jobs Sebelum | Jobs Sesudah | Dikurangi |
|------|:-----------:|:------------:|:---------:|
| Tunarungu + IT skills + Jakarta | 93 | ~30-40 | ~60% |

Dengan 93 job, jika user memilih 1-2 lokasi + 1-2 kategori keahlian, biasanya tersisa 20-40 job = 2-4 batch = 2-4 API call.

---

## 5. Batch Processing

### 5.1 Arsitektur: Orchestrator + Worker

```
MatchUserToJobs (Orchestrator)        MatchBatchJob (Worker) × N
┌─────────────────────────────┐       ┌─────────────────────────────┐
│ implements ShouldQueue      │       │ implements ShouldQueue      │
│ tries = 1                   │──▸───│ tries = 3                   │
│ timeout = 600s (10 menit)   │       │ backoff = 3s                │
│                             │       │                             │
│ · Load user + profile       │       │ · Terima $userId,           │
│ · Build $userProfile array  │       │   $userProfile, $jobs[],    │
│ · Load ALL JobVacancyData   │       │   $batchIndex               │
│ · preFilter()               │       │ · Panggil DeepseekService   │
│ · Hapus match lama          │       │ · updateOrCreate()          │
│ · Chunk 10 jobs per batch   │       │ · Cache increment done      │
│ · Dispatch MatchBatchJob    │       │ · Cek done >= total         │
│   untuk setiap batch        │       │                             │
└─────────────────────────────┘       └─────────────────────────────┘
```

### 5.2 MatchUserToJobs (Orchestrator)

**File**: `app/Jobs/MatchUserToJobs.php`

| Atribut | Nilai | Keterangan |
|---------|-------|-----------|
| `tries` | 1 | Jika orchestrator gagal, tidak di-retry (batch individu yang di-retry) |
| `timeout` | 600 detik | 10 menit — cukup untuk dispatch banyak batch |
| `ShouldQueue` | ✅ | Berjalan di queue `database` |

**Yang dilakukan (berurutan)**:

1. Cek user + profile exist → jika tidak, set status `failed`
2. Build `$userProfile` array dari profile + user data (15 field)
3. `JobVacancyData::all()` → semua job
4. `preFilter()` → kurangi job
5. `JobUserMatch::where('user_id', $uid)->delete()` — hapus semua match lama
6. `array_chunk($filteredJobs, 10)` → batch size 10
7. Set cache: `status = processing`, `done = 0`, `total = count(batches)`
8. Foreach batch → `MatchBatchJob::dispatch($uid, $userProfile, $batch, $i)`

### 5.3 MatchBatchJob (Worker)

**File**: `app/Jobs/MatchBatchJob.php`

| Atribut | Nilai | Keterangan |
|---------|-------|-----------|
| `tries` | 2 | Retry 2x jika gagal (API timeout, dll) |
| `backoff` | 1 detik | Jeda antar retry |
| `ShouldQueue` | ✅ | Berjalan di queue `database` |
| DI | `DeepseekService $ai` | Auto-resolved oleh Laravel container |

**Yang dilakukan**:

1. `$ai->matchUserToJobs($this->userProfile, $this->jobs)` → panggil Deepseek API
2. Validasi `$jobIds` terhadap database (hindari menyimpan match untuk job yang sudah dihapus)
3. Foreach result → `JobUserMatch::updateOrCreate()`
4. `Cache::increment("matching_done_{$uid}")` → counter++
5. Jika `done >= total` → `Cache::put("matching_status_{$uid}", "completed")`

### 5.4 Queue Driver

```php
// config/queue.php — default
'default' => env('QUEUE_CONNECTION', 'database'),
```

Jobs tabel: `jobs` (standard Laravel queue table). Queue worker harus dijalankan:

```bash
php artisan queue:work
```

Atau via `composer run dev` yang sudah include queue worker di `package.json` scripts.

---

## 6. AI Service (DeepseekService)

**File**: `app/Services/DeepseekService.php`

### 6.1 Konfigurasi

```php
// config/services.php
'deepseek' => [
    'api_key' => env('DEEPSEEK_API_KEY'),
    'model' => env('DEEPSEEK_MODEL', 'deepseek-chat'),
    'match_threshold' => env('JOB_MATCH_THRESHOLD', 60),
],
```

### 6.2 HTTP Client Configuration

| Parameter | Nilai | Keterangan |
|-----------|-------|-----------|
| **Endpoint** | `POST https://api.deepseek.com/v1/chat/completions` | Deepseek Chat API |
| **Model** | `deepseek-chat` (default) | Bisa diubah ke `deepseek-reasoner` via `.env` |
| **Authentication** | `Bearer {DEEPSEEK_API_KEY}` | Via `->withToken()` |
| **Retry** | 3 kali, jeda 2000ms | Via `Http::retry(3, 2000)` |
| **Timeout** | 45 detik (`deepseek-chat`), 120 detik (`deepseek-reasoner`) | Per model berbeda |
| **temperature** | 0.3 | Rendah = hasil lebih konsisten, kurang kreatif |
| **max_tokens** | 4096 | Cukup untuk 10 job × scores |
| **response_format** | `json_object` | Memaksa AI merespon dalam JSON valid |
| **System prompt** | "You are an expert job matching system for deaf and hard-of-hearing individuals in Indonesia..." | Fokus pada Tunarungu |

### 6.3 Error Handling

```php
// API error (non-2xx)
if (! $response->successful()) {
    Log::error('Deepseek API error', [
        'status' => $response->status(),
        'body' => $response->body(),
    ]);
    return [];  // Kembalikan array kosong → batch ini tidak menyimpan apa pun
}

// JSON response invalid
$result = json_decode($content, true);
if (! is_array($result)) {
    Log::error('Deepseek invalid JSON response', ['content' => $content]);
    return [];
}

return $result['matches'] ?? [];
```

Jika satu batch gagal (API down, invalid response), batch tersebut return array kosong. Batch lain tetap diproses. `MatchBatchJob` punya `tries=3` sehingga akan di-retry 3x sebelum permanent fail.

### 6.4 Method: `matchUserToJobs(array $userProfile, array $jobs): array`

Input: `$userProfile` (15 field) + `$jobs` (maks 10 per batch)

Output: `array` — decoded dari `response.matches[]` JSON

---

## 7. Prompt Structure

Prompt dibangun oleh method `buildMatchingPrompt()` (`DeepseekService.php:76`).

### 7.1 Full Prompt Template

```
You are matching a DEAF/HARD-OF-HEARING candidate to job vacancies in
Indonesia's Jabodetabek area. Evaluate each job carefully.

=== CANDIDATE PROFILE ===
Disability: Tunarungu
Hearing level: Tuli total — Tidak dapat mendengar suara sama sekali...
Communication preference: BISINDO (Bahasa Isyarat Indonesia), Teks tertulis...
Preferred work environment & accommodation: Remote (WFH), On-site dengan juru isyarat
Skill categories: IT & Programming, Desain & Kreatif
Specific sub-skills: Web Developer, Backend Developer, UI/UX Designer
Education: S1 / Sarjana — Teknik Informatika
Preferred job types: Penuh Waktu (Full-time), Kontrak
Preferred locations: Jakarta Selatan, Depok
Age: 24 years old

=== WEIGHTING ===
Match scores (0-100) should use these weights:
- Disability fit (35%): Can this deaf/hard-of-hearing person effectively
  perform this job? Consider: Does the job require extensive verbal phone
  communication? Does it require constant in-person verbal meetings without
  accommodation? Or is it primarily text-based/visual? A job that requires
  heavy verbal communication should score LOW for deaf candidates. A job
  that can be done via text/visual means should score HIGH.
- Skill match (30%): Do the candidate's skill categories and sub-skills
  align with the job's required skills and category?
- Work environment (20%): Does the work type and location match the
  candidate's preferences? Consider: Remote vs on-site, Jabodetabek
  location match, job type, and accommodation needs.
- Communication (10%): Does the job's communication demands match the
  candidate's specific communication methods? BISINDO users match well
  with companies providing sign language interpreters. Text-based users
  match well with remote/async jobs.
- Education (5%): Does the candidate's education level and major match
  the job requirements?

=== SCORING GUIDE ===
0-20: Not compatible at all
21-40: Poor match
41-60: Somewhat compatible
61-80: Good match
81-100: Excellent match

=== RESPONSE FORMAT ===
Return ONLY a JSON object with this exact structure:
{
  "matches": [
    {
      "id": <job_id>,
      "match_score": <0-100>,
      "is_match": <true if score >= 60>,
      "disability_score": <0-100>,
      "skill_score": <0-100>,
      "environment_score": <0-100>,
      "communication_score": <0-100>,
      "education_score": <0-100>,
      "match_reason": "<1-2 sentence summary in Bahasa Indonesia>"
    }
  ]
}

=== JOB LISTINGS ===
Job 0: [id: 42]
- Title: Fullstack Engineer
- Company: PT MNC Teknologi Nusantara
- Category: Developer/Programmer
- Skills required: Laravel, Golang, PostgreSQL, Docker
- Education: S1 Teknik Informatika
- Work type: Full time
- Location: Jakarta Selatan
- Description: Mencari Fullstack Engineer untuk mengembangkan...

Job 1: [id: 43]
...
```

### 7.2 Data Points yang Dikirim ke AI

| # | Data Point | Sumber | Format di Prompt |
|---|-----------|--------|-----------------|
| 1 | Disability | `user_profiles.disability_condition` | "Tunarungu" |
| 2 | Hearing level | `user_profiles.hearing_level` | Label + deskripsi lengkap |
| 3 | Communication | `user_profiles.communication_preference` | Array label (BISINDO, Teks tertulis, ...) |
| 4 | Work environment | `user_profiles.work_environment` | Array label |
| 5 | Skill categories | `user_profiles.skill_categories` | Array label kategori |
| 6 | Sub-skills | `user_profiles.skill_categories[].subs` | Array label spesifik |
| 7 | Education level | `user_profiles.education_level` | Label (S1 / Sarjana) |
| 8 | Education major | `user_profiles.education_major` | String jurusan |
| 9 | Job types | `user_profiles.job_types` | Array label |
| 10 | Locations | `user_profiles.preferred_locations` | Array label |
| 11 | Age | `users.date_of_birth` | Integer tahun |
| 12 | Job ID | `job_vacancy_data.id` | Integer |
| 13 | Job title | `job_vacancy_data.job_title` | String |
| 14 | Company | `job_vacancy_data.company` | String |
| 15 | Job category | `job_vacancy_data.job_category` | String |
| 16 | Skills required | `job_vacancy_data.skill_req` | String |
| 17 | Education required | `job_vacancy_data.education_req` | String |
| 18 | Work type | `job_vacancy_data.work_type` | String |
| 19 | Location | `job_vacancy_data.location` | String |
| 20 | Description | `job_vacancy_data.jobdesk` | String (dipotong 800 karakter) |

### 7.3 Kenapa Prompt Didesain Seperti Ini?

| Keputusan Desain | Alasan |
|-----------------|--------|
| **System prompt spesifik Tunarungu** | AI fokus pada dimensi komunikasi dan akomodasi yang unik untuk tunarungu. Tidak membuang token untuk menilai disabilitas lain. |
| **Hearing level + deskripsi** | AI bisa membedakan: tuli total → skor rendah untuk job verbal; gangguan ringan → masih bisa meeting terbatas. |
| **5 spektrum komunikasi (BISINDO/SIBI/teks/bibir/alat)** | BISINDO → butuh juru isyarat; teks → cocok remote/async; bibir → masih bisa tatap muka. Setiap spektrum punya implikasi scoring berbeda. |
| **Sub-skills spesifik** | "Web Developer" lebih presisi daripada hanya "IT & Programming". AI bisa mencocokkan dengan skill_req yang menyebut "React", "Laravel", dll. |
| **Lokasi + tipe kerja** | AI bisa menilai environment_score: lokasi cocok + tipe kontrak cocok = skor tinggi. |
| **Job description dipotong 800 karakter** | Menghemat token tanpa kehilangan informasi penting (deskripsi job biasanya ringkas di 200-500 karakter pertama). |
| **Temperature 0.0** | Scoring harus konsisten. Temperature 0.0 (fully deterministic) memastikan input yang sama selalu menghasilkan output yang sama — mencegah inkonsistensi antar run. User A dan B dengan profil identik akan mendapat skor identik (fairness). |
| **JSON mode** | Menghindari parsing error. AI dipaksa output JSON valid, bukan teks bebas. |
| **match_reason dalam Bahasa Indonesia** | Ditampilkan langsung ke user Indonesia tanpa perlu translate. |

---

## 8. 5 Dimensi Scoring

Setiap job dinilai dalam **5 dimensi**, masing-masing 0-100. Skor total adalah rata-rata berbobot.

### 8.1 Disability Fit (35%)

**Bobot**: 35% — **dimensi terpenting**

**Yang dinilai AI**:
- Apakah job membutuhkan komunikasi verbal intensif (telepon, meeting lisan tanpa akomodasi)?
- Apakah job bisa dilakukan secara visual/teks?
- Apakah job membutuhkan pendengaran sebagai fungsi utama (contoh: operator telepon, resepsionis)?
- Apakah job menyediakan akomodasi untuk tunarungu?

**Contoh penilaian**:
| Skenario Job | Skor | Alasan |
|-------------|:----:|--------|
| Remote programmer, komunikasi via Slack/GitHub | **85-95** | 100% teks-based, async |
| Barista di kafe yang punya SOP visual dan rekan bisa isyarat | **70-85** | Visual-based, ada akomodasi |
| Admin officer dengan sesekali meeting tim | **50-70** | Ada komponen verbal tapi masih dominan teks |
| Customer service telepon | **5-15** | 100% verbal, tidak cocok sama sekali |
| Resepsionis hotel | **10-25** | Butuh komunikasi verbal konstan dengan tamu |

### 8.2 Skill Match (30%)

**Bobot**: 30%

**Yang dinilai AI**:
- Apakah kategori keahlian user cocok dengan kategori job?
- Apakah sub-keahlian user muncul di `skill_req` job?
- Apakah ada teknologi/tools yang disebut di jobdesk yang relevan dengan skill user?

**Contoh penilaian**:
| Skenario | Skor | Alasan |
|----------|:----:|--------|
| User: IT/Web Developer · Job: Fullstack Engineer (Laravel, React) | **85-95** | Perfect match |
| User: IT/Data Analyst · Job: IT Support | **55-70** | Kategori sama, sub berbeda |
| User: Desain/UI-UX · Job: Graphic Designer | **65-80** | Masih dalam ranah desain |
| User: Kuliner/Barista · Job: IT Programmer | **5-15** | Kategori berbeda total |

### 8.3 Work Environment (20%)

**Bobot**: 20%

**Yang dinilai AI**:
- Apakah `work_type` job cocok dengan preferensi user (remote/hybrid/onsite)?
- Apakah lokasi job berada di Jabodetabek yang dipilih user?
- Apakah tipe kontrak (full-time/part-time/kontrak) cocok dengan preferensi user?
- Apakah akomodasi yang dibutuhkan (juru isyarat, notifikasi visual) tersedia?

**Contoh penilaian**:
| Skenario | Skor | Alasan |
|----------|:----:|--------|
| User prefer Remote · Job: Remote, Jakarta Selatan, Full-time | **90-100** | Semua cocok |
| User prefer Remote · Job: On-site, Jakarta Pusat (beda lokasi) | **40-55** | Tidak remote + lokasi tidak cocok |
| User prefer On-site dgn juru isyarat · Job: On-site | **65-80** | On-site ok, tapi akomodasi isyarat tidak diketahui |
| User prefer Hybrid · Job: Full time (Jakarta) | **60-75** | Hybrid = bisa dinegosiasikan |

### 8.4 Communication (10%)

**Bobot**: 10%

**Yang dinilai AI**:
- Apakah job bisa dikerjakan dengan metode komunikasi yang user kuasai?
- Jika user menggunakan BISINDO: apakah perusahaan mungkin menyediakan juru isyarat?
- Jika user menggunakan teks tertulis: apakah job memungkinkan komunikasi async via chat/email?
- Jika user membaca gerak bibir: apakah job melibatkan cukup banyak interaksi tatap muka?

**Contoh penilaian**:
| Skenario | Skor | Alasan |
|----------|:----:|--------|
| User: BISINDO + Teks · Job: Remote developer (Slack, GitHub) | **90-100** | Async teks, no verbal needed |
| User: BISINDO · Job: On-site retail (bisa gesture + teks) | **55-70** | Tidak perlu verbal kompleks |
| User: Teks + Bibir · Job: Admin dengan meeting mingguan | **60-75** | Bibir membantu, teks untuk sisanya |
| User: BISINDO · Job: Sales lapangan (presentasi ke klien) | **15-30** | Butuh komunikasi verbal intensif |

### 8.5 Education (5%)

**Bobot**: 5% — **dimensi paling ringan**

**Yang dinilai AI**:
- Apakah jenjang pendidikan user memenuhi minimum requirement job?
- Apakah jurusan user relevan dengan bidang job?
- Untuk job yang tidak mensyaratkan pendidikan spesifik, skor otomatis tinggi.

**Contoh penilaian**:
| Skenario | Skor | Alasan |
|----------|:----:|--------|
| User: S1 Teknik Informatika · Job: S1 Teknik Informatika | **95-100** | Perfect match |
| User: S1 Sistem Informasi · Job: S1 Ilmu Komputer | **80-90** | Serumpun |
| User: D3 Multimedia · Job: S1 Desain Grafis | **60-75** | Jenjang berbeda, jurusan dekat |
| User: SMA · Job: Minimal S1 | **10-20** | Tidak memenuhi minimum |

### 8.6 Rumus Skor Total

```
match_score = (disability_score × 0.35)
            + (skill_score × 0.30)
            + (environment_score × 0.20)
            + (communication_score × 0.10)
            + (education_score × 0.05)
```

AI menghitung masing-masing sub-skor lalu mengembalikan `match_score` sebagai integer 0-100.

`is_match = true` jika `match_score >= 60` (threshold dari `config/services.php`).

---

## 9. Penyimpanan Hasil

### 9.1 Tabel `job_user_matches`

**File**: `database/migrations/2026_05_06_000001_create_job_user_matches_table.php`

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id` | bigint (PK) | Auto-increment |
| `job_vacancy_data_id` | FK → `job_vacancy_data.id` | Lowongan yang dinilai |
| `user_id` | FK → `users.id` | User yang dinilai |
| `match_score` | unsigned tinyint (0-100) | Skor total |
| `is_match` | boolean | `true` jika skor ≥ threshold (default 60) |
| `disability_score` | unsigned tinyint (0-100) | Sub-skor disability fit |
| `skill_score` | unsigned tinyint (0-100) | Sub-skor skill match |
| `environment_score` | unsigned tinyint (0-100) | Sub-skor work environment |
| `communication_score` | unsigned tinyint (0-100) | Sub-skor communication |
| `education_score` | unsigned tinyint (0-100) | Sub-skor education |
| `match_reason` | text, nullable | Alasan dalam Bahasa Indonesia |
| `calculated_at` | timestamp, nullable | Waktu perhitungan |
| `created_at` / `updated_at` | timestamps | Laravel default |

**Unique constraint**: `UNIQUE(job_vacancy_data_id, user_id)` — satu user hanya punya satu skor per lowongan.

### 9.2 Query Penyimpanan

```php
// app/Jobs/MatchBatchJob.php:50
JobUserMatch::updateOrCreate(
    [
        'job_vacancy_data_id' => $jobId,
        'user_id' => $uid,
    ],
    [
        'match_score' => (int) ($result['match_score'] ?? 0),
        'is_match' => ! empty($result['is_match']),
        'disability_score' => (int) ($result['disability_score'] ?? 0),
        'skill_score' => (int) ($result['skill_score'] ?? 0),
        'environment_score' => (int) ($result['environment_score'] ?? 0),
        'communication_score' => (int) ($result['communication_score'] ?? 0),
        'education_score' => (int) ($result['education_score'] ?? 0),
        'match_reason' => $result['match_reason'] ?? null,
        'calculated_at' => now(),
    ]
);
```

`updateOrCreate` memastikan:
- Jika match sudah ada (rematch) → update dengan skor baru
- Jika match belum ada → insert baru

### 9.3 Data Dihapus Sebelum Rematch

```php
// app/Jobs/MatchUserToJobs.php:61
JobUserMatch::where('user_id', $uid)->delete();
```

Sebelum memproses batch baru, **semua match lama user dihapus**. Ini memastikan tidak ada data stale (misal lowongan yang sudah tidak relevan).

---

## 10. Penampilan Hasil

### 10.1 Halaman Beranda (`/beranda`)

**File**: `app/Livewire/Beranda.php`

```php
JobUserMatch::with('jobVacancyData')
    ->where('user_id', auth()->id())
    ->where('is_match', true)          // Hanya yang di atas threshold
    ->orderByDesc('match_score')       // Skor tertinggi duluan
    ->take(6)                          // Maksimal 6
    ->get();
```

**Tampilan**: Kartu lowongan dengan badge skor kecocokan (hijau: 81-100, biru: 61-80, kuning: 41-60).

### 10.2 Halaman Lowongan (`/lowongan`)

**File**: `app/Livewire/Lowongan.php`

Menampilkan semua lowongan. Untuk user yang sudah login dan memiliki match:
- Jika `match_score >= 60` → tampilkan badge skor
- Jika `< 60` → tidak tampil badge (tapi data match tetap tersimpan)

### 10.3 Halaman Detail Lowongan (`/lowongan/{id}`)

**File**: `app/Livewire/DetailLowongan.php`

Menampilkan full breakdown:
- **5 sub-skor** dalam bentuk progress bar horizontal (0-100%)
- **match_reason** — penjelasan AI dalam Bahasa Indonesia
- **match_score** total dengan badge warna

```
┌─────────────────────────────────────────┐
│ Kecocokan: 78%           🟢 Cocok       │
├─────────────────────────────────────────┤
│ Kecocokan Disabilitas  ████████████ 85% │
│ Kecocokan Skill        ██████████   75% │
│ Lingkungan Kerja       ████████████ 90% │
│ Komunikasi             ████████     60% │
│ Pendidikan             ███████      55% │
├─────────────────────────────────────────┤
│ "Pekerjaan ini sangat cocok untuk       │
│  tunarungu karena berbasis koding dan   │
│  komunikasi dilakukan via teks..."      │
└─────────────────────────────────────────┘
```

---

## 11. Edge Cases & Error Handling

### 11.1 User atau Profile Tidak Ditemukan

```php
if (! $user || ! $user->profile) {
    Log::warning("MatchUserToJobs: user or profile not found for ID {$uid}");
    Cache::put($statusKey, 'failed', now()->addHours(1));
    return;
}
```

**Dampak**: Status cache menjadi `failed`. User di halaman `/matching` akan melihat tombol "Coba Lagi". Tapi retry tidak akan berhasil karena data tidak ada — perlu dicek secara manual.

### 11.2 Tidak Ada Job di Database

```php
$jobs = JobVacancyData::all();
$filteredJobs = $this->preFilter($userProfile, $jobs);
```

- Jika `JobVacancyData::all()` kosong → `$filteredJobs` kosong
- `$batches` kosong → langsung set status `completed`
- Tidak ada API call yang dilakukan

### 11.3 Semua Job Ter-filter (0 Hasil Pre-Filter)

```php
if (empty($batches)) {
    Cache::put($statusKey, 'completed', now()->addHours(24));
    Cache::forget($doneKey);
    Cache::forget($totalKey);
    return;
}
```

**Dampak**: Status `completed` tanpa batch diproses. User ke beranda tapi tidak ada rekomendasi. Perlu log untuk monitoring.

### 11.4 Deepseek API Error (Non-2xx)

```php
if (! $response->successful()) {
    Log::error('Deepseek API error', [...]);
    return [];
}
```

**Dampak**: Batch ini return array kosong → tidak ada match yang disimpan. `MatchBatchJob` akan retry 3x (backoff 3 detik). Jika tetap gagal → job failed → batch tidak terproses. Batch lain tetap berjalan. Status tetap `processing` → user bisa timeout 90 detik.

### 11.5 Deepseek Mengembalikan JSON Invalid

```php
$result = json_decode($content, true);
if (! is_array($result)) {
    Log::error('Deepseek invalid JSON response', ['content' => $content]);
    return [];
}
```

**Dampak**: Sama seperti API error. Batch ini gagal, tapi batch lain lanjut. Di-retry 3x.

### 11.6 Batch Gagal Sebagian

```php
// MatchBatchJob: jika satu batch gagal, done tetap di-increment
// Karena increment dilakukan di catch block, tapi throw $e akan trigger retry
```

Scenario: 3 batch, batch #1 gagal setelah 3 retry. Batch #2 dan #3 sukses.
- `done` = 2, `total` = 3
- `done < total` → status tetap `processing`
- User timeout 90 detik → status `failed`
- Tapi 2 batch sukses sudah tersimpan di database

### 11.7 Job ID di Hasil AI Tidak Valid

```php
$jobIds = array_column($this->jobs, 'id');
$existingJobs = JobVacancyData::whereIn('id', $jobIds)->pluck('id')->toArray();

foreach ($results as $result) {
    $jobId = (int) ($result['id'] ?? 0);
    if (! $jobId || ! in_array($jobId, $existingJobs)) {
        continue;  // Skip job yang tidak valid
    }
    JobUserMatch::updateOrCreate(...);
}
```

**Proteksi**: Validasi double — cek `$jobIds` (dari input batch) dan `$existingJobs` (dari database). Job yang di-hapus di antara pre-filter dan batch processing tidak akan disimpan.

### 11.8 Timeout Halaman `/matching` (90 Detik)

```php
if ($this->startedAt && (time() - $this->startedAt) > self::TIMEOUT_SECONDS) {
    $this->status = 'failed';
    Cache::put($cacheKey, 'failed', now()->addHours(1));
    return;
}
```

**Dampak**: Status dipaksa `failed`. Tapi job di queue TETAP BERJALAN. Jika job selesai setelah timeout, cache akan tetap di-set ke `completed` oleh batch terakhir. User tidak akan tahu kecuali mereka refresh halaman. Tombol retry akan me-reset counter dan me-dispatch ulang (yang akan menghapus match lama dan mulai dari awal).

### 11.9 Batch Counter Race Condition

```php
$done = Cache::increment($doneKey);
$total = (int) Cache::get($totalKey, 0);

if ($done >= $total) {
    Cache::put($statusKey, 'completed', now()->addHours(24));
}
```

**Potensi masalah**: Jika 2 batch selesai bersamaan, keduanya bisa membaca `done >= total`. Tapi `Cache::put` untuk `completed` bersifat idempotent — tidak ada side effect negatif.

### 11.10 Tidak Ada Queue Worker Berjalan

**Dampak**: Job masuk ke tabel `jobs` tapi tidak pernah diproses. Status cache tetap `processing`. User timeout di halaman `/matching`. Solusi: pastikan queue worker berjalan (`php artisan queue:work` atau via `composer run dev`).

---

## Ringkasan Teknis

| Aspek | Detail |
|-------|--------|
| **Trigger** | 4 titik: Onboarding save, Profil rematch, Matching retry, Matching mount auto-dispatch |
| **Dispatch** | Async via queue (`MatchUserToJobs::dispatch()`) |
| **Queue** | Database driver, tabel `jobs` |
| **Orchestrator** | `MatchUserToJobs` (tries=1, timeout=600s) |
| **Worker** | `MatchBatchJob` (tries=2, backoff=1s) |
| **Batch size** | 10 job per API call (optimal — lebih besar kontraproduktif: 15 job/batch = 28s vs 10 job/batch = 15s per call, efisiensi per-job justru turun 25%) |
| **Pre-filter** | 4 dimensi (work env + skills + location + **education level**), logika: `educationFilter AND (workMatch OR skillMatch OR locationMatch)` |
| **Education pre-filter** | Hard filter: job dengan education_req di atas level user langsung dibuang. Keyword: sd/smp/sma/smk/diploma/s1/sarjana/bachelor/degree/profesi/s2/master/s3/doktor/phd. |
| **Database** | SQLite **WAL mode** + busy_timeout=5000ms (concurrent read/write, minimal lock contention) |
| **AI Model** | Deepseek (`deepseek-chat` default, `deepseek-reasoner` optional) |
| **AI Temperature** | 0.0 (fully deterministic) |
| **AI Timeout** | 45s (chat) / 120s (reasoner) |
| **Scoring** | 5 dimensi berbobot: 35/30/20/10/5 |
| **Threshold** | `match_score >= 60` → `is_match = true` |
| **Hasil** | Tabel `job_user_matches`, unique per user+job |
| **State** | Cache-based (3 keys), polling 2 detik, timeout 90 detik |
| **Response format** | JSON via `response_format: json_object` |

---

> **Dokumen ini adalah panduan komprehensif sistem AI Matching Equaly.**  
> Setiap perubahan pada pipeline matching (prompt, scoring, pre-filter, batch processing) harus dicatat dan di-update di dokumen ini.  
> **Acuan terkait**:
> - [`tunarungu-employment-research.md`](./tunarungu-employment-research.md)
> - [`onboarding-profiling-redesign.md`](./onboarding-profiling-redesign.md)
