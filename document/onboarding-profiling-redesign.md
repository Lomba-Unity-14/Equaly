# Redesain Onboarding Profiling — Equaly

> **Dokumen acuan**: Rancangan ulang onboarding profiling pasca pemilihan fokus ke penyandang Tunarungu.  
> **Status**: Final (ready to implement)  
> **Tanggal**: 8 Mei 2026  
> **Terkait**: [`tunarungu-employment-research.md`](./tunarungu-employment-research.md)

---

## Daftar Isi

1. [Latar Belakang](#1-latar-belakang)
2. [Prinsip Desain](#2-prinsip-desain)
3. [Struktur 7 Step Onboarding](#3-struktur-7-step-onboarding)
4. [Detail Setiap Step](#4-detail-setiap-step)
   - [Step 1: Kondisi Disabilitas](#step-1-kondisi-disabilitas)
   - [Step 2: Tingkat Pendengaran](#step-2-tingkat-pendengaran)
   - [Step 3: Preferensi Komunikasi](#step-3-preferensi-komunikasi)
   - [Step 4: Lingkungan Kerja + Akomodasi](#step-4-lingkungan-kerja--akomodasi)
   - [Step 5: Kategori & Sub-Keahlian](#step-5-kategori--sub-keahlian)
   - [Step 6: Data Diri & Preferensi Kerja](#step-6-data-diri--preferensi-kerja)
   - [Step 7: Konfirmasi & Submit](#step-7-konfirmasi--submit)
5. [Perubahan Database](#5-perubahan-database)
6. [Daftar File yang Berubah](#6-daftar-file-yang-berubah)
7. [Dampak ke AI Matching](#7-dampak-ke-ai-matching)
8. [Appendix: Data Referensi Lengkap](#8-appendix-data-referensi-lengkap)

---

## 1. Latar Belakang

### Mengapa Redesain?

Onboarding saat ini (4 step) memiliki beberapa kelemahan fundamental:

| Masalah | Dampak |
|---------|--------|
| Step 1 multi-select 4 tipe disabilitas (Tunarungu, Tunadaksa, Netra, Lainnya) | Tidak fokus. AI tidak bisa memberikan rekomendasi spesifik karena profil terlalu generik. |
| Step 2 preferensi komunikasi hanya 3 opsi (Full Teks, Baca bibir, Juru isyarat) | Tidak spesifik untuk tunarungu. Tidak membedakan BISINDO vs SIBI. |
| Step 3 lingkungan kerja hanya 3 opsi (Remote, Hybrid, On-site) | Tidak memasukkan dimensi akomodasi (juru isyarat, notifikasi visual). |
| Step 4 skills input bebas (free text) | **Ambigu!** User bisa input "apa aja bisa", "figma" vs "Figma", singkatan tidak jelas. AI bingung. |
| Tidak ada data pendidikan | AI tidak bisa menghitung `education_score` (5% bobot). |
| Tidak ada data lokasi yang diminati | AI tidak bisa mencocokkan lokasi lowongan dengan preferensi user. |
| Tidak ada tipe pekerjaan (full-time, part-time, dll) | AI tidak bisa membedakan preferensi jenis kontrak. |
| Tidak ada data usia | AI tidak bisa memperkirakan level pengalaman. |

### Tujuan Redesain

1. **Fokus ke Tunarungu** (MVP) dengan kemampuan ekspansi ke disabilitas lain di masa depan
2. **Zero free text input** — semua input terstruktur, predefined, tidak ambigu
3. **Data lebih kaya** — mencakup pendidikan, jurusan, tipe kerja, lokasi, tingkat pendengaran, akomodasi
4. **AI-ready** — setiap data yang dikumpulkan langsung memperkaya prompt AI Matching
5. **Based on data** — setiap opsi didasarkan pada riset ketenagakerjaan tunarungu (lihat [`tunarungu-employment-research.md`](./tunarungu-employment-research.md))

---

## 2. Prinsip Desain

| Prinsip | Implementasi |
|---------|-------------|
| **No ambiguity** | Semua input adalah pilihan terstruktur (single-select, multi-select, date picker). Tidak ada text field bebas. |
| **Fokus Tunarungu** | Step 1 menampilkan semua disabilitas tetapi hanya Tunarungu yang bisa dipilih. Lainnya dikunci (`disabled`) dengan label "Coming Soon". |
| **Expandable** | Arsitektur mendukung penambahan disabilitas lain di masa depan. Setiap disabilitas akan memiliki step spesifiknya sendiri. |
| **Data-driven** | Semua opsi diambil dari riset BPS, ILO, Gerkatin, Kemnaker, studi kasus perusahaan inklusif. |
| **Progressive disclosure** | Opsi yang muncul di step berikutnya bisa bergantung pada pilihan di step sebelumnya (contoh: jurusan difilter berdasarkan kategori keahlian). |
| **AI precision** | Setiap data yang dikumpulkan memiliki korespondensi langsung ke 5 dimensi scoring AI. |

---

## 3. Struktur 7 Step Onboarding

```
┌──────────┐   ┌──────────┐   ┌──────────┐   ┌──────────┐   ┌──────────┐   ┌──────────┐   ┌──────────┐
│  STEP 1  │   │  STEP 2  │   │  STEP 3  │   │  STEP 4  │   │  STEP 5  │   │  STEP 6  │   │  STEP 7  │
│ Disabi-  │ → │ Tingkat  │ → │ Prefer.  │ → │ Lingkung.│ → │ Kategori │ → │ Data Diri│ → │Konfirmasi│
│ litas    │   │Pendengar.│   │Komunikasi│   │+Akomodasi│   │+SubKeahli│   │& Pref.Krj│   │  & Save  │
├──────────┤   ├──────────┤   ├──────────┤   ├──────────┤   ├──────────┤   ├──────────┤   ├──────────┤
│ Single   │   │ Single   │   │ Multi    │   │ Multi    │   │ Multi    │   │ Mixed    │   │ Review   │
│ Select   │   │ Select   │   │ Select   │   │ Select   │   │+Cascading│   │ Form     │   │ Summary  │
│ (locked) │   │ (3 opsi) │   │ (5 opsi) │   │ (5 opsi) │   │(8 kat,   │   │(DOB,     │   │          │
│          │   │          │   │          │   │          │   │7-8 sub)  │   │Pendidikan│   │          │
│          │   │          │   │          │   │          │   │          │   │Jurusan,  │   │          │
│          │   │          │   │          │   │          │   │          │   │Tipe,     │   │          │
│          │   │          │   │          │   │          │   │          │   │Lokasi)   │   │          │
└──────────┘   └──────────┘   └──────────┘   └──────────┘   └──────────┘   └──────────┘   └──────────┘
```

---

## 4. Detail Setiap Step

### Step 1: Kondisi Disabilitas

**Tipe input**: Single select (radio button / card select)  
**Validasi**: `required|string|in:tunarungu`

| Value | Label | Status |
|-------|-------|--------|
| `tunarungu` | Tunarungu (Tuli/Deaf) | ✅ **Bisa dipilih** |
| `tunadaksa` | Tunadaksa (Fisik) | 🔒 Coming Soon |
| `netra` | Netra / Low Vision | 🔒 Coming Soon |
| `lainnya` | Lainnya | 🔒 Coming Soon |

**Catatan UX**:
- Hanya Tunarungu yang bisa diklik. Tiga opsi lainnya berwarna abu-abu (disabled) dengan label "Coming Soon".
- Ada teks disclaimer di atas: *"Equaly saat ini fokus untuk teman Tunarungu. Dukungan untuk disabilitas lain akan hadir di pengembangan selanjutnya."*

---

### Step 2: Tingkat Pendengaran

**Tipe input**: Single select  
**Validasi**: `required|string|in:tuli_total,gangguan_berat,gangguan_ringan`

| Value | Label | Deskripsi (untuk AI) |
|-------|-------|---------------------|
| `tuli_total` | Tuli total | Tidak dapat mendengar suara sama sekali. Sepenuhnya bergantung pada komunikasi visual: bahasa isyarat, teks tertulis, atau notifikasi visual. |
| `gangguan_berat` | Gangguan pendengaran berat | Masih dapat mendengar suara sangat keras. Mungkin menggunakan alat bantu dengar, namun komunikasi verbal sangat terbatas. Lebih mengandalkan teks atau isyarat. |
| `gangguan_ringan` | Gangguan pendengaran ringan/sedang | Kesulitan mendengar suara pelan atau percakapan dalam lingkungan bising. Dapat berkomunikasi verbal dengan bantuan alat bantu dengar. |

**Basis data**: ILO 2022 wage gap — 86,4% disabilitas berat berpenghasilan <Rp2 juta vs 82,7% disabilitas ringan. Tingkat pendengaran mempengaruhi aksesibilitas dan produktivitas.

---

### Step 3: Preferensi Komunikasi

**Tipe input**: Multi select (checkbox / chip toggle)  
**Validasi**: `required|array|min:1`

| Value | Label | Penjelasan |
|-------|-------|-----------|
| `bisindo` | BISINDO (Bahasa Isyarat Indonesia) | Bahasa isyarat alami yang digunakan komunitas Tuli Indonesia. Didukung oleh Gerkatin dan Pusbisindo. |
| `sibi` | SIBI (Sistem Isyarat Bahasa Indonesia) | Sistem isyarat formal yang digunakan di SLB dan institusi pendidikan. Mengikuti struktur tata bahasa Indonesia. |
| `teks_tertulis` | Teks tertulis (chat/email/dokumen) | Komunikasi via WhatsApp, email, Slack, catatan tertulis. Paling universal — bisa digunakan di semua tempat kerja. |
| `bibir` | Membaca gerak bibir | Memahami lawan bicara melalui gerakan bibir dalam percakapan tatap muka. |
| `alat_bantu` | Alat bantu dengar | Menggunakan alat bantu dengar (hearing aid) atau implan koklea untuk membantu pendengaran. |

**Basis data**:
- BISINDO vs SIBI: Gerkatin, Pusbisindo (komunitas tuli lebih memilih BISINDO sebagai bahasa alami)
- 68,2% tunarungu global menyebut hambatan komunikasi sebagai alasan utama pengangguran (Worldmetrics 2026)
- Sunyi Coffee: menggunakan BISINDO + teks + sistem notifikasi visual

**Aturan tambahan (bisa diimplementasikan nanti)**:
- Jika Step 2 memilih `tuli_total` dan Step 3 TIDAK memilih `bisindo`, `sibi`, atau `teks_tertulis` → tampilkan peringatan ringan (bukan blocker).

---

### Step 4: Lingkungan Kerja + Akomodasi

**Tipe input**: Multi select  
**Validasi**: `required|array|min:1`

| Value | Label | Penjelasan |
|-------|-------|-----------|
| `remote` | Remote (WFH) | Bekerja jarak jauh dari rumah. Paling ideal untuk tunarungu karena komunikasi async via teks. |
| `hybrid` | Hybrid | Kombinasi remote dan on-site. Fleksibel dengan beberapa hari di kantor. |
| `onsite_juru_isyarat` | On-site dengan juru bahasa isyarat | Bekerja di kantor yang menyediakan juru bahasa isyarat (BISINDO/SIBI). |
| `onsite_notifikasi_visual` | On-site dengan notifikasi visual | Kantor dengan sistem notifikasi visual (lampu, layar, getar) sebagai pengganti audio. |
| `onsite_standar` | On-site tanpa akomodasi khusus | Kantor standar tanpa akomodasi spesifik untuk tunarungu. |

**Basis data**:
- Sunyi Coffee: menggunakan mesin kopi yang diadaptasi secara visual, sistem notifikasi lampu, alat pemanggil getar (detik.com 2025)
- Worldmetrics 2026: 52,3% pekerja tunarungu memiliki akses ke minimal satu akomodasi tempat kerja; 31,7% punya akses juru isyarat
- Remote/hybrid adalah preferensi utama untuk tunarungu karena komunikasi async teks

---

### Step 5: Kategori & Sub-Keahlian

**Tipe input**: Multi select kategori → cascading sub-keahlian  
**Validasi**: `required|array|min:1`

#### 5a. Kategori Utama (8 opsi)

| Value | Label |
|-------|-------|
| `it_programming` | IT & Programming |
| `desain_kreatif` | Desain & Kreatif |
| `kuliner_tata_boga` | Kuliner & Tata Boga |
| `administrasi_data` | Administrasi & Data |
| `retail_logistik` | Retail & Logistik |
| `otomotif_teknisi` | Otomotif & Teknisi |
| `hospitality` | Hospitality |
| `tekstil_manufaktur` | Tekstil & Manufaktur |

#### 5b. Sub-Keahlian (muncul setelah pilih kategori)

##### IT & Programming
| Value | Label |
|-------|-------|
| `web_developer` | Web Developer |
| `mobile_developer` | Mobile Developer |
| `backend_developer` | Backend Developer |
| `fullstack_developer` | Fullstack Developer |
| `qa_tester` | QA / Software Tester |
| `data_analyst` | Data Analyst |
| `it_support` | IT Support |
| `devops` | DevOps |

##### Desain & Kreatif
| Value | Label |
|-------|-------|
| `ui_ux_designer` | UI/UX Designer |
| `graphic_designer` | Graphic Designer |
| `illustrator` | Illustrator |
| `video_editor` | Video Editor |
| `photographer` | Photographer |
| `content_creator` | Content Creator |
| `motion_designer` | Motion Designer |

##### Kuliner & Tata Boga
| Value | Label |
|-------|-------|
| `barista` | Barista |
| `koki` | Koki / Juru Masak |
| `baker_pastry` | Baker / Pastry |
| `kitchen_staff` | Kitchen Staff |
| `food_preparation` | Food Preparation |

##### Administrasi & Data
| Value | Label |
|-------|-------|
| `data_entry` | Data Entry |
| `admin_officer` | Admin Officer |
| `document_processing` | Document Processing |
| `customer_service_text` | Customer Service (Text-based) |
| `office_assistant` | Office Assistant |

##### Retail & Logistik
| Value | Label |
|-------|-------|
| `kasir` | Kasir |
| `pramuniaga` | Pramuniaga |
| `checker_gudang` | Checker Gudang |
| `warehouse_staff` | Warehouse Staff |
| `inventory` | Inventory |

##### Otomotif & Teknisi
| Value | Label |
|-------|-------|
| `montir` | Montir / Mekanik |
| `teknisi_elektronik` | Teknisi Elektronik |
| `operator_mesin` | Operator Mesin |
| `teknisi_ac` | Teknisi AC |
| `teknisi_hardware` | Teknisi Komputer / Hardware |

##### Hospitality
| Value | Label |
|-------|-------|
| `hotel_staff` | Hotel Staff (Back Office) |
| `laundry_staff` | Laundry Staff |
| `room_attendant` | Room Attendant |
| `kitchen_helper` | Kitchen Helper |

##### Tekstil & Manufaktur
| Value | Label |
|-------|-------|
| `penjahit` | Penjahit / Tailor |
| `operator_produksi` | Operator Produksi |
| `quality_control` | Quality Control |
| `packing` | Packing |

**Basis data**: [tunarungu-employment-research.md](./tunarungu-employment-research.md) Bagian 4 — 8 kategori berdasarkan data lowongan nyata, program pelatihan, dan perusahaan inklusif.

---

### Step 6: Data Diri & Preferensi Kerja

**Tipe input**: Mixed (date picker, single select, cascading select, multi select)

#### 6a. Tanggal Lahir

**Tipe**: Date picker  
**Validasi**: `required|date|before:today`  
**Keterangan**: Digunakan AI untuk memperkirakan level pengalaman. Disimpan di tabel `users`.

#### 6b. Pendidikan Terakhir

**Tipe**: Single select  
**Validasi**: `required|string|in:sd,smp,sma_smk,d1_d4,s1,profesi,s2,s3`

| Value | Label |
|-------|-------|
| `sd` | SD / Sederajat |
| `smp` | SMP / Sederajat |
| `sma_smk` | SMA / SMK / Sederajat |
| `d1_d4` | D1 - D4 |
| `s1` | S1 / Sarjana |
| `profesi` | Pendidikan Profesi |
| `s2` | S2 / Magister |
| `s3` | S3 / Doktor |

#### 6c. Jurusan (kondisional)

**Tipe**: Single select (dropdown, difilter berdasarkan **kategori keahlian di Step 5**)  
**Kondisi muncul**: Jika pendidikan = `s1`, `d1_d4`, `profesi`, `s2`, atau `s3`  
**Validasi**: `required_if:education_level,s1,d1_d4,profesi,s2,s3|string`

Jurusan yang muncul adalah **UNION** dari semua jurusan yang terkait dengan kategori yang dipilih di Step 5. Jika user memilih banyak kategori (misal IT + Desain), jurusan dari kedua kategori digabung.

<details>
<summary><b>Klik untuk melihat mapping lengkap Kategori → Jurusan</b></summary>

**Jika user memilih IT & Programming:**
| Jurusan |
|--------|
| Teknik Informatika |
| Sistem Informasi |
| Ilmu Komputer |
| Teknik Komputer |
| Rekayasa Perangkat Lunak |
| Data Science |
| Teknologi Informasi |

**Jika user memilih Desain & Kreatif:**
| Jurusan |
|--------|
| Desain Komunikasi Visual (DKV) |
| Desain Grafis |
| Desain Produk |
| Multimedia |
| Seni Rupa |
| Fotografi |
| Desain Interior |

**Jika user memilih Kuliner & Tata Boga:**
| Jurusan |
|--------|
| Tata Boga |
| Manajemen Kuliner |
| Gizi |

**Jika user memilih Administrasi & Data:**
| Jurusan |
|--------|
| Manajemen |
| Administrasi Perkantoran |
| Administrasi Bisnis |
| Akuntansi |
| Administrasi Publik |
| Sekretaris |

**Jika user memilih Retail & Logistik:**
| Jurusan |
|--------|
| Manajemen Logistik |
| Manajemen Bisnis |
| Manajemen Ritel |
| Administrasi Bisnis |

**Jika user memilih Otomotif & Teknisi:**
| Jurusan |
|--------|
| Teknik Mesin |
| Teknik Otomotif |
| Teknik Elektro |
| Teknik Industri |

**Jika user memilih Hospitality:**
| Jurusan |
|--------|
| Perhotelan |
| Pariwisata |
| Manajemen Hospitality |

**Jika user memilih Tekstil & Manufaktur:**
| Jurusan |
|--------|
| Teknik Industri |
| Tata Busana |
| Teknik Tekstil |
| Manajemen Operasional |

</details>

##### Jurusan SMK (jika pendidikan = `sma_smk`)

| Jurusan SMK |
|------------|
| Rekayasa Perangkat Lunak |
| Teknik Komputer dan Jaringan |
| Multimedia |
| Desain Grafis |
| Desain Komunikasi Visual |
| Tata Boga |
| Perhotelan |
| Akuntansi |
| Administrasi Perkantoran |
| Pemasaran |
| Teknik Kendaraan Ringan |
| Teknik Sepeda Motor |
| Teknik Elektronika |
| Teknik Mesin |
| Tata Busana |

#### 6d. Tipe Pekerjaan yang Dicari

**Tipe**: Multi select  
**Validasi**: `required|array|min:1`

| Value | Label |
|-------|-------|
| `penuh_waktu` | Penuh Waktu (Full-time) |
| `part_time` | Part-Time |
| `magang` | Magang (Internship) |
| `freelance` | Freelance |
| `kontrak` | Kontrak |

#### 6e. Lokasi yang Diminati

**Tipe**: Multi select search  
**Validasi**: `required|array|min:1`  
**Lingkup**: **Jabodetabek** (13 lokasi)

| # | Value | Label |
|---|-------|-------|
| 1 | `jakarta_pusat` | Jakarta Pusat |
| 2 | `jakarta_selatan` | Jakarta Selatan |
| 3 | `jakarta_timur` | Jakarta Timur |
| 4 | `jakarta_barat` | Jakarta Barat |
| 5 | `jakarta_utara` | Jakarta Utara |
| 6 | `bogor` | Bogor |
| 7 | `kab_bogor` | Kabupaten Bogor |
| 8 | `depok` | Depok |
| 9 | `tangerang` | Tangerang |
| 10 | `kab_tangerang` | Kabupaten Tangerang |
| 11 | `tangerang_selatan` | Tangerang Selatan |
| 12 | `bekasi` | Bekasi |
| 13 | `kab_bekasi` | Kabupaten Bekasi |

**Basis data**: Jabodetabek dipilih sebagai fokus awal karena:
- Area metropolitan terbesar di Indonesia
- Scraping manual lowongan akan difokuskan ke Jabodetabek + 8 kategori keahlian
- 13 lokasi cukup granular tanpa membuat user overwhelmed

---

### Step 7: Konfirmasi & Submit

**Tipe**: Review summary + tombol submit

Menampilkan ringkasan semua data yang akan disimpan:
- Kondisi disabilitas
- Tingkat pendengaran
- Preferensi komunikasi
- Lingkungan kerja + akomodasi
- Kategori & sub-keahlian
- Tanggal lahir, pendidikan, jurusan
- Tipe pekerjaan, lokasi

Tombol: **"Kembali"** / **"Simpan & Mulai Pencocokan"**

Setelah submit:
1. Simpan `UserProfile` dengan `onboarding_completed = true`
2. Set cache `matching_status_{uid} = 'processing'`
3. Jalankan `MatchUserToJobs::handle()` **secara asinkron** (dispatch ke queue)
4. Redirect ke `/matching` (layar loading AI)

> **Perubahan dari versi lama**: Matching akan dijalankan **asinkron via queue** (tidak synchronous seperti sebelumnya) untuk menghindari blocking request.

---

## 5. Perubahan Database

### Migration Baru

**Nama file**: `database/migrations/2026_05_08_000001_add_onboarding_fields.php`

```php
// Tambah kolom ke user_profiles
Schema::table('user_profiles', function (Blueprint $table) {
    $table->string('hearing_level')->nullable()->after('disability_condition');
    $table->string('education_level')->nullable()->after('skills');
    $table->string('education_major')->nullable()->after('education_level');
    $table->json('job_types')->nullable()->after('education_major');
    $table->json('preferred_locations')->nullable()->after('job_types');
    $table->json('skill_categories')->nullable()->after('preferred_locations');
});

// Hapus kolom skills (free text) lama
Schema::table('user_profiles', function (Blueprint $table) {
    $table->dropColumn('skills');
});

// Tambah kolom ke users
Schema::table('users', function (Blueprint $table) {
    $table->date('date_of_birth')->nullable()->after('avatar');
});
```

### Struktur Tabel `user_profiles` Setelah Migration

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id` | integer | Primary key |
| `user_id` | FK | Foreign key ke `users` |
| `disability_condition` | json | Array, isinya `["tunarungu"]` (single element array) |
| `hearing_level` | string | `tuli_total` / `gangguan_berat` / `gangguan_ringan` |
| `communication_preference` | json | Array dari 5 opsi |
| `work_environment` | json | Array dari 5 opsi |
| `skill_categories` | json | Array kategori + sub-keahlian |
| `education_level` | string | `sd` s.d. `s3` |
| `education_major` | string, nullable | Jurusan |
| `job_types` | json | Array tipe pekerjaan |
| `preferred_locations` | json | Array lokasi Jabodetabek |
| `onboarding_completed` | boolean | Flag selesai onboarding |
| `headline` | string, nullable | Headline profil |
| `timestamps` | timestamps | created_at, updated_at |

### Struktur Tabel `users` (Tambahan)

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `date_of_birth` | date, nullable | Tanggal lahir |

---

## 6. Daftar File yang Berubah

| # | File | Aksi | Deskripsi |
|---|------|------|-----------|
| 1 | `database/migrations/2026_05_08_000001_add_onboarding_fields.php` | **CREATE** | Migration baru |
| 2 | `app/Models/User.php` | **EDIT** | Tambah `date_of_birth` ke `#[Fillable]` dan `casts` |
| 3 | `app/Models/UserProfile.php` | **EDIT** | Tambah field baru ke `#[Fillable]` dan `casts`; hapus `skills` |
| 4 | `app/Livewire/Onboarding.php` | **REWRITE** | 7 step, semua opsi predefined |
| 5 | `resources/views/livewire/onboarding.blade.php` | **REWRITE** | UI baru untuk 7 step |
| 6 | `resources/views/layouts/onboarding.blade.php` | **EDIT** (minor) | Sesuaikan step indicator dari 4 ke 7 |
| 7 | `app/Livewire/Profil.php` | **EDIT** | Update `labels()`, ganti section edit, tambah field baru |
| 8 | `resources/views/livewire/profil.blade.php` | **EDIT** | Tampilkan field baru; ganti tampilan skills free text |
| 9 | `app/Jobs/MatchUserToJobs.php` | **EDIT** | Update `$userProfile` array mapping dengan field baru; ubah ke dispatch async |
| 10 | `app/Services/DeepseekService.php` | **EDIT** | Update `buildMatchingPrompt()` dengan field baru dari profil |
| 11 | `config/data.php` atau dedicated class | **CREATE** | Konstanta data: opsi lokasi, kategori, sub-keahlian, jurusan |
| 12 | `app/Http/Middleware/EnsureOnboardingCompleted.php` | **NO CHANGE** | Tetap berfungsi sama |

---

## 7. Dampak ke AI Matching

### Prompt Baru — Lebih Kaya

```
=== CANDIDATE PROFILE ===
Disability condition: Tunarungu
Hearing level: tuli_total (completely deaf — relies entirely on visual communication: sign language and text)
Communication preference: BISINDO (Indonesian Sign Language — natural, community-based), Teks tertulis (text-based: chat, email, documents)
Preferred work environment + accommodation: Remote (WFH), On-site dengan juru bahasa isyarat (on-site with sign language interpreter provided)
Skill categories: IT & Programming [Web Developer, Backend Developer], Desain & Kreatif [UI/UX Designer]
Education: S1 / Sarjana — Teknik Informatika
Job types: Penuh Waktu, Kontrak
Preferred locations: Jakarta Selatan, Depok
Age: 24 tahun (estimated from date_of_birth)
```

### Efek ke 5 Dimensi Scoring

| Dimensi | Bobot | Data Baru | Dampak |
|---------|:-----:|-----------|--------|
| **Disability fit** | 35% | `hearing_level` + `work_environment` + `accommodation` | AI bisa membedakan: tuli total butuh isyarat/teks penuh, gangguan ringan mungkin bisa verbal terbatas. Akomodasi (juru isyarat, notifikasi visual) menjadi faktor diskriminan kuat. |
| **Skill match** | 30% | `skill_categories` + sub-keahlian | AI tidak lagi bingung oleh free text seperti "apa aja bisa". Kategori terstruktur dengan sub-keahlian spesifik ("Web Developer", "UI/UX Designer") memberi sinyal jelas. |
| **Work environment** | 20% | `work_environment` + `job_types` + `preferred_locations` | 4 data point: environment type, accommodation, job contract type, location. AI bisa mencocokkan lokasi, tipe kerja, dan akomodasi. |
| **Communication** | 10% | `communication_preference` (5 spektrum) | BISINDO vs SIBI vs teks vs bibir vs alat bantu — AI bisa mencocokkan dengan kebutuhan komunikasi lowongan. Lowongan dengan requirement "meeting verbal" akan skor rendah untuk pengguna BISINDO/teks. |
| **Education** | 5% | `education_level` + `education_major` | Untuk pertama kalinya AI punya data pendidikan. Bisa mencocokkan jenjang (S1) dan relevansi jurusan (Teknik Informatika vs lowongan IT). |

---

## 8. Appendix: Data Referensi Lengkap

### A. Konstanta Lokasi (Jabodetabek)

```php
const LOCATIONS = [
    'jakarta_pusat' => 'Jakarta Pusat',
    'jakarta_selatan' => 'Jakarta Selatan',
    'jakarta_timur' => 'Jakarta Timur',
    'jakarta_barat' => 'Jakarta Barat',
    'jakarta_utara' => 'Jakarta Utara',
    'bogor' => 'Bogor',
    'kab_bogor' => 'Kabupaten Bogor',
    'depok' => 'Depok',
    'tangerang' => 'Tangerang',
    'kab_tangerang' => 'Kabupaten Tangerang',
    'tangerang_selatan' => 'Tangerang Selatan',
    'bekasi' => 'Bekasi',
    'kab_bekasi' => 'Kabupaten Bekasi',
];
```

### B. Konstanta Kategori & Sub-Keahlian

```php
const SKILL_CATEGORIES = [
    'it_programming' => [
        'label' => 'IT & Programming',
        'subs' => [
            'web_developer' => 'Web Developer',
            'mobile_developer' => 'Mobile Developer',
            'backend_developer' => 'Backend Developer',
            'fullstack_developer' => 'Fullstack Developer',
            'qa_tester' => 'QA / Software Tester',
            'data_analyst' => 'Data Analyst',
            'it_support' => 'IT Support',
            'devops' => 'DevOps',
        ],
    ],
    'desain_kreatif' => [
        'label' => 'Desain & Kreatif',
        'subs' => [
            'ui_ux_designer' => 'UI/UX Designer',
            'graphic_designer' => 'Graphic Designer',
            'illustrator' => 'Illustrator',
            'video_editor' => 'Video Editor',
            'photographer' => 'Photographer',
            'content_creator' => 'Content Creator',
            'motion_designer' => 'Motion Designer',
        ],
    ],
    'kuliner_tata_boga' => [
        'label' => 'Kuliner & Tata Boga',
        'subs' => [
            'barista' => 'Barista',
            'koki' => 'Koki / Juru Masak',
            'baker_pastry' => 'Baker / Pastry',
            'kitchen_staff' => 'Kitchen Staff',
            'food_preparation' => 'Food Preparation',
        ],
    ],
    'administrasi_data' => [
        'label' => 'Administrasi & Data',
        'subs' => [
            'data_entry' => 'Data Entry',
            'admin_officer' => 'Admin Officer',
            'document_processing' => 'Document Processing',
            'customer_service_text' => 'Customer Service (Text-based)',
            'office_assistant' => 'Office Assistant',
        ],
    ],
    'retail_logistik' => [
        'label' => 'Retail & Logistik',
        'subs' => [
            'kasir' => 'Kasir',
            'pramuniaga' => 'Pramuniaga',
            'checker_gudang' => 'Checker Gudang',
            'warehouse_staff' => 'Warehouse Staff',
            'inventory' => 'Inventory',
        ],
    ],
    'otomotif_teknisi' => [
        'label' => 'Otomotif & Teknisi',
        'subs' => [
            'montir' => 'Montir / Mekanik',
            'teknisi_elektronik' => 'Teknisi Elektronik',
            'operator_mesin' => 'Operator Mesin',
            'teknisi_ac' => 'Teknisi AC',
            'teknisi_hardware' => 'Teknisi Komputer / Hardware',
        ],
    ],
    'hospitality' => [
        'label' => 'Hospitality',
        'subs' => [
            'hotel_staff' => 'Hotel Staff (Back Office)',
            'laundry_staff' => 'Laundry Staff',
            'room_attendant' => 'Room Attendant',
            'kitchen_helper' => 'Kitchen Helper',
        ],
    ],
    'tekstil_manufaktur' => [
        'label' => 'Tekstil & Manufaktur',
        'subs' => [
            'penjahit' => 'Penjahit / Tailor',
            'operator_produksi' => 'Operator Produksi',
            'quality_control' => 'Quality Control',
            'packing' => 'Packing',
        ],
    ],
];
```

### C. Mapping Kategori → Jurusan

```php
const CATEGORY_MAJORS = [
    'it_programming' => [
        'Teknik Informatika', 'Sistem Informasi', 'Ilmu Komputer',
        'Teknik Komputer', 'Rekayasa Perangkat Lunak', 'Data Science',
        'Teknologi Informasi',
    ],
    'desain_kreatif' => [
        'Desain Komunikasi Visual (DKV)', 'Desain Grafis', 'Desain Produk',
        'Multimedia', 'Seni Rupa', 'Fotografi', 'Desain Interior',
    ],
    'kuliner_tata_boga' => [
        'Tata Boga', 'Manajemen Kuliner', 'Gizi',
    ],
    'administrasi_data' => [
        'Manajemen', 'Administrasi Perkantoran', 'Administrasi Bisnis',
        'Akuntansi', 'Administrasi Publik', 'Sekretaris',
    ],
    'retail_logistik' => [
        'Manajemen Logistik', 'Manajemen Bisnis', 'Manajemen Ritel',
        'Administrasi Bisnis',
    ],
    'otomotif_teknisi' => [
        'Teknik Mesin', 'Teknik Otomotif', 'Teknik Elektro', 'Teknik Industri',
    ],
    'hospitality' => [
        'Perhotelan', 'Pariwisata', 'Manajemen Hospitality',
    ],
    'tekstil_manufaktur' => [
        'Teknik Industri', 'Tata Busana', 'Teknik Tekstil', 'Manajemen Operasional',
    ],
];

const SMK_MAJORS = [
    'Rekayasa Perangkat Lunak', 'Teknik Komputer dan Jaringan', 'Multimedia',
    'Desain Grafis', 'Desain Komunikasi Visual', 'Tata Boga', 'Perhotelan',
    'Akuntansi', 'Administrasi Perkantoran', 'Pemasaran',
    'Teknik Kendaraan Ringan', 'Teknik Sepeda Motor',
    'Teknik Elektronika', 'Teknik Mesin', 'Tata Busana',
];
```

### D. Konstanta Pendidikan

```php
const EDUCATION_LEVELS = [
    'sd' => 'SD / Sederajat',
    'smp' => 'SMP / Sederajat',
    'sma_smk' => 'SMA / SMK / Sederajat',
    'd1_d4' => 'D1 - D4',
    's1' => 'S1 / Sarjana',
    'profesi' => 'Pendidikan Profesi',
    's2' => 'S2 / Magister',
    's3' => 'S3 / Doktor',
];

// Jenjang yang memunculkan dropdown jurusan
const EDUCATION_HAS_MAJOR = ['d1_d4', 's1', 'profesi', 's2', 's3'];
```

### E. Konstanta Tipe Pekerjaan

```php
const JOB_TYPES = [
    'penuh_waktu' => 'Penuh Waktu (Full-time)',
    'part_time' => 'Part-Time',
    'magang' => 'Magang (Internship)',
    'freelance' => 'Freelance',
    'kontrak' => 'Kontrak',
];
```

### F. Konstanta Komunikasi

```php
const COMMUNICATION_PREFERENCES = [
    'bisindo' => 'BISINDO (Bahasa Isyarat Indonesia)',
    'sibi' => 'SIBI (Sistem Isyarat Bahasa Indonesia)',
    'teks_tertulis' => 'Teks tertulis (chat/email/dokumen)',
    'bibir' => 'Membaca gerak bibir',
    'alat_bantu' => 'Alat bantu dengar',
];
```

### G. Konstanta Lingkungan Kerja + Akomodasi

```php
const WORK_ENVIRONMENTS = [
    'remote' => 'Remote (WFH)',
    'hybrid' => 'Hybrid',
    'onsite_juru_isyarat' => 'On-site dengan juru bahasa isyarat',
    'onsite_notifikasi_visual' => 'On-site dengan notifikasi visual',
    'onsite_standar' => 'On-site tanpa akomodasi khusus',
];
```

### H. Konstanta Tingkat Pendengaran

```php
const HEARING_LEVELS = [
    'tuli_total' => 'Tuli total',
    'gangguan_berat' => 'Gangguan pendengaran berat',
    'gangguan_ringan' => 'Gangguan pendengaran ringan/sedang',
];
```

---

> **Status**: Dokumen ini adalah hasil diskusi dan riset. Belum diimplementasikan.  
> **Langkah selanjutnya**: Setelah dokumen ini disetujui oleh tim dan dosen pembimbing, implementasi coding akan dimulai.  
> **Acuan terkait**:
> - [`tunarungu-employment-research.md`](./tunarungu-employment-research.md) — Riset data ketenagakerjaan tunarungu
> - `app/Livewire/Onboarding.php` — Onboarding saat ini (akan di-rewrite)
> - `app/Services/DeepseekService.php` — AI service (prompt akan di-update)
