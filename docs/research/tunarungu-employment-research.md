# Penelitian Ketenagakerjaan Tunarungu — Equaly

> **Dokumen acuan**: Alasan pemilihan fokus ke penyandang **Tunarungu (Tuli/Deaf)** untuk fitur AI Matching Equaly.  
> **Status**: Final  
> **Tanggal**: 8 Mei 2026  

---

## Daftar Isi

1. [Mengapa Tunarungu? — Perbandingan 3 Disabilitas](#1-mengapa-tunarungu--perbandingan-3-disabilitas)
2. [Statistik Populasi & Ketenagakerjaan Tunarungu](#2-statistik-populasi--ketenagakerjaan-tunarungu)
3. [Preferensi Komunikasi Tunarungu di Tempat Kerja](#3-preferensi-komunikasi-tunarungu-di-tempat-kerja)
4. [Kategori Pekerjaan yang Cocok untuk Tunarungu](#4-kategori-pekerjaan-yang-cocok-untuk-tunarungu)
5. [Perusahaan Inklusif Tunarungu di Indonesia](#5-perusahaan-inklusif-tunarungu-di-indonesia)
6. [Program Pemerintah & Pelatihan](#6-program-pemerintah--pelatihan)
7. [Hambatan Ketenagakerjaan Tunarungu](#7-hambatan-ketenagakerjaan-tunarungu)
8. [Implikasi untuk Onboarding Equaly](#8-implikasi-untuk-onboarding-equaly)
9. [Referensi Akademik & Sumber Data](#9-referensi-akademik--sumber-data)

---

## 1. Mengapa Tunarungu? — Perbandingan 3 Disabilitas

### Ringkasan Perbandingan

| Dimensi | **Tunarungu (Deaf)** | Tunadaksa (Fisik) | Tunanetra (Netra/Low Vision) |
|--------:|:--------------------:|:-----------------:|:----------------------------:|
| **Populasi** | ~1,8 juta (Gerkatin); estimasi lain hingga 4 juta | 584.503 (62,6% dari 933.893 penduduk disabilitas terdata BPS) | ~4 juta (AIDRAN 2023) |
| **Tingkat partisipasi formal** | ~17% | ~17% | **Hanya ~1%** |
| **Pendidikan S1+ (yang bekerja)** | Data minim | Data minim | **76% S1, 22% S2** |
| **Spesifisitas komunikasi** | ✅ **Sangat spesifik** — BISINDO, SIBI, teks/chat, bibir, alat bantu dengar | ❌ Tidak relevan (hambatan fisik) | ✅ Spesifik — NVDA/JAWS, screen reader, audio |
| **Spesifisitas lingkungan kerja** | Notifikasi visual, teks-based tools, juru isyarat | Ramp, lift, toilet aksesibel, meja adjustable | Website WCAG-compatible, screen reader support |
| **Perusahaan inklusif** | ✅ Banyak: Sunyi Coffee, Kopi Tuli, MAP, Superindo, Alfamart, DHL | Transjakarta, manufaktur | ❌ Sangat sedikit |
| **Organisasi komunitas** | ✅ Gerkatin (31 provinsi, 416 kota); Pusbisindo | Beragam | PERTUNI, Mitra Netra |
| **Program pelatihan** | ✅ Barista, konten kreator, tata boga, fotografi, otomotif | Manufaktur, BLK umum | Terbatas |
| **Jurnal akademik** | 6+ publikasi ditemukan | 8+ publikasi ditemukan | **Paling banyak** (riset lintas negara Mitra Netra 2024, AIDRAN) |

### Alasan Memilih Tunarungu

#### 1. Spesifisitas Komunikasi Paling Tajam

Tunarungu memiliki **spektrum komunikasi 5 dimensi** yang tidak dimiliki disabilitas lain:
- **BISINDO** (Bahasa Isyarat Indonesia) — bahasa alami komunitas tuli
- **SIBI** (Sistem Isyarat Bahasa Indonesia) — sistem formal pendidikan
- **Teks/chat tertulis** — komunikasi async via WhatsApp, email, chat
- **Membaca gerak bibir** — oral-visual
- **Alat bantu dengar** — untuk gangguan pendengaran ringan-berat

Dimensi ini membuat AI Matching bisa menilai kecocokan dengan presisi tinggi. Contoh:
- Lowongan dengan juru isyarat BISINDO → skor komunikasi tinggi untuk pengguna BISINDO
- Lowongan remote dengan komunikasi async teks → skor tinggi untuk pengguna teks penuh
- Lowongan dengan meeting verbal intensif → skor rendah untuk tuli total

#### 2. Ekosistem Pekerjaan Sudah Terbukti

Ada perusahaan nyata yang sudah mempekerjakan tunarungu sebagai bukti bahwa model ini **based on real world data**, bukan asumsi:
- **Sunyi Coffee**: 25+ karyawan tuli, 100% deaf staff (Jakarta & Yogyakarta)
- **Kopi Tuli / Deaf Cafe Fingertalk**: pemenang SDGs Action Award Tokyo 2019
- **PT MAP Tbk**: 11 tunarungu ditempatkan via Kemensos (2025)
- **Superindo, Alfamart, Indomaret**: membuka lowongan disabilitas di Job Fair Kemnaker 2025
- **DHL Supply Chain**: lowongan checker/warehouse untuk tunarungu

#### 3. Program Pemerintah Aktif

- **BPVP Padang (Kemnaker, 2026)**: Pelatihan tata kafe khusus tunarungu + tata boga
- **Pemprov DKI (2025)**: Pelatihan barista + content creator untuk disabilitas
- **Kemnaker Job Fair Inklusif (2025)**: 135 lowongan dari 26 perusahaan
- **Kemensos + PT MAP**: Penempatan 11 tunarungu via Sentra Mulya Jaya

#### 4. Minim Perubahan Teknis Aplikasi

Fokus tunarungu hanya membutuhkan perubahan pada:
- Form onboarding (4 step baru)
- AI prompt matching
- Model database (`user_profiles`)
- Tidak perlu rebuild UI untuk screen reader (seperti yang dibutuhkan tunanetra)
- Tidak perlu mengubah database lowongan secara fundamental

#### 5. Bobot AI Scoring Sangat Relevan

AI Matching kita memiliki 5 sub-skor. `communication_score` (10%) dan `disability_score` (35%) adalah dimensi yang **unik dan spesifik** untuk tunarungu. Untuk tunadaksa dan tunanetra, communication bukan faktor pembeda utama.

---

## 2. Statistik Populasi & Ketenagakerjaan Tunarungu

### Data Populasi

| Sumber | Estimasi |
|--------|----------|
| SIMPD Kemensos 2019 (via Gerkatin) | 18+ juta seluruh disabilitas |
| Gerkatin (anggota terdaftar) | 1,8 juta |
| WHO / Springer | ~205.000 (estimasi 1/1.000 populasi) |
| Facebook / GoFundMe (Deaf Mental Health Group) | ~4 juta |

> **Catatan**: Data populasi tunarungu sangat bervariasi karena perbedaan metodologi sensus. BPS Long Form SP2020 adalah sumber primer paling kredibel, namun tidak memisahkan tunarungu secara spesifik.

### Data Ketenagakerjaan (BPS 2024 via Katadata.co.id)

| Indikator | Angka |
|-----------|-------|
| Total pekerja disabilitas | **932.435** orang (0,64% dari 144,64 juta pekerja nasional) |
| Di sektor informal | **83%** (~773.921 orang) |
| Di sektor formal | **17%** (~158.514 orang) |
| Kenaikan dari 2023 ke 2024 | **+22%** |
| Hanya **1,1%** penyandang disabilitas terserap di sektor formal (dari total populasi disabilitas) |

### Kesenjangan Upah (ILO 2022 via Katadata)

| Kelompok Pendapatan | Non-Disabilitas | Disabilitas Ringan | Disabilitas Berat |
|---------------------|:---------------:|:------------------:|:-----------------:|
| < Rp2 juta/bulan | 69,1% | 82,7% | **86,4%** |
| Rp2–10 juta/bulan | 30,1% | 16,8% | 13,5% |
| > Rp10 juta/bulan | 0,8% | 0,5% | 0,1% |

> **Untuk tunarungu berat (tuli total)**: 86,4% berpenghasilan di bawah Rp2 juta/bulan. Hanya 0,1% di atas Rp10 juta.

### Pendidikan

- **13 SLB untuk tunarungu** di Jakarta saja (data 2022)
- Akses ke pendidikan tinggi **sangat terbatas** — jumlah juru isyarat di kampus minim
- Gerkatin aktif mendorong akses pendidikan melalui advokasi dan kerjasama

---

## 3. Preferensi Komunikasi Tunarungu di Tempat Kerja

### BISINDO vs SIBI

| Aspek | BISINDO | SIBI |
|-------|---------|------|
| **Sifat** | Alami, berkembang dari komunitas tuli | Dibuat pemerintah, mengikuti struktur tata bahasa Indonesia |
| **Pengguna** | Komunitas tuli (didukung Gerkatin & Pusbisindo) | SLB (pendidikan formal) |
| **Preferensi** | Dipilih komunitas tuli | Sering dikritik sebagai tidak alami |
| **Digunakan di** | Kehidupan sehari-hari, tempat kerja inklusif | Sekolah luar biasa |

### Spektrum Komunikasi

| Metode | Kapan Digunakan | Relevan untuk Pekerjaan |
|--------|----------------|------------------------|
| **BISINDO** | Komunikasi sesama tuli; dengan rekan kerja yang bisa isyarat | Perusahaan dengan juru isyarat atau staf terlatih |
| **SIBI** | Lingkungan pendidikan formal | Instansi pemerintah, sekolah |
| **Teks/chat tertulis** | WhatsApp, email, Slack, catatan tertulis | **Paling universal** — semua tempat kerja |
| **Membaca gerak bibir** | Percakapan langsung dengan rekan dengar | Lingkungan tatap muka; tidak selalu akurat |
| **Alat bantu dengar** | Untuk gangguan ringan-berat | Lingkungan dengan suara yang tidak terlalu bising |

### Akomodasi Tempat Kerja (Studi Kasus Sunyi Coffee — detik.com 2025)

- Pelatihan bahasa isyarat untuk staf dengar
- Mesin kopi yang diadaptasi (visual-based operation)
- Sistem notifikasi visual (lampu, layar)
- Alat pemanggil getar
- Guiding block, ramp, toilet aksesibel

### Statistik Global (Worldmetrics 2026)

| Indikator | Persentase |
|-----------|:----------:|
| Pekerja tunarungu yang punya akses ke akomodasi tempat kerja | 52,3% |
| Yang punya akses ke juru isyarat | 31,7% |
| Yang menggunakan alat komunikasi berbasis teks | 27,4% |
| **Menyebut hambatan komunikasi sebagai alasan utama pengangguran** | **68,2%** |

---

## 4. Kategori Pekerjaan yang Cocok untuk Tunarungu

Berdasarkan data lowongan nyata, program pelatihan, dan perusahaan inklusif yang ada.

### Kategori 1: IT & Programming

| Pekerjaan | Skill Dibutuhkan | Kenapa Cocok |
|-----------|-----------------|--------------|
| Programmer / Web Developer | PHP, Laravel, JavaScript, Python, React | Komunikasi async via teks, fokus koding individual |
| IT Support (text-based) | Troubleshooting via chat/tiket | Tidak perlu komunikasi verbal |
| QA / Software Tester | Manual & automated testing | Pekerjaan berbasis teks dan tools |
| Mobile Developer | Android, iOS, Flutter | Remote-friendly, teks-based |
| Data Analyst | SQL, Excel, Python | Fokus data, komunikasi via laporan tertulis |

**Sumber data riil**: Kemnaker Job Fair 2025, Kerjabilitas.com

---

### Kategori 2: Desain & Kreatif

| Pekerjaan | Skill Dibutuhkan | Kenapa Cocok |
|-----------|-----------------|--------------|
| Graphic Designer | Adobe Illustrator, Photoshop, Figma | Visual work, brief via teks |
| UI/UX Designer | Figma, prototyping, research | Async communication with team |
| Photographer | Kamera, editing (Lightroom) | Visual medium, non-verbal |
| Video Editor | Premiere Pro, After Effects, CapCut | Visual editing, brief via teks |
| Content Creator | Social media, copywriting, basic design | Async, teks & visual |

**Sumber data riil**: Program Gerkatin Solo (fotografi), Pemprov DKI (content creator)

---

### Kategori 3: Kuliner & Tata Boga

| Pekerjaan | Skill Dibutuhkan | Kenapa Cocok |
|-----------|-----------------|--------------|
| Barista | Espresso machine, latte art | **Bukti nyata**: Sunyi Coffee 25+ karyawan tuli |
| Koki / Juru Masak | Teknik memasak, higiene | Komunikasi via isyarat/teks di dapur |
| Baker / Pastry Chef | Baking, dough handling | Proses terstruktur, minim komunikasi verbal |
| Kitchen Staff | Food prep, cleaning | Instruksi visual, checklist |

**Sumber data riil**: BPVP Padang (pelatihan tata boga + tata kafe, 2026), Dinsos DKI (pelatihan barista, 2025)

---

### Kategori 4: Administrasi & Data

| Pekerjaan | Skill Dibutuhkan | Kenapa Cocok |
|-----------|-----------------|--------------|
| Data Entry | Mengetik cepat, teliti, Excel | 100% teks-based |
| Admin Officer | Dokumen, email, scheduling | Komunikasi via email/chat |
| Customer Service (text) | Live chat, email support | Tidak perlu telepon |
| Document Processing | Scanning, indexing, filing | Pekerjaan individual, visual |

**Sumber data riil**: Mitracomm Ekasarana (BPO), MSIG Life Insurance (via Kemnaker Job Fair)

---

### Kategori 5: Retail & Logistik

| Pekerjaan | Skill Dibutuhkan | Kenapa Cocok |
|-----------|-----------------|--------------|
| Kasir | Mesin kasir, teliti | Transaksi visual; banyak perusahaan retail sudah inklusif |
| Pramuniaga | Product knowledge, display | Komunikasi via gesture/teks dengan pelanggan |
| Checker Gudang | Inventory, scanning | Pekerjaan individual, teks-based |
| Warehouse Staff | Picking, packing | Instruksi via sistem/display |

**Sumber data riil**: Superindo, Alfamart, Indomaret, Lawson (Job Fair Kemnaker 2025), DHL Supply Chain

---

### Kategori 6: Otomotif & Teknisi

| Pekerjaan | Skill Dibutuhkan | Kenapa Cocok |
|-----------|-----------------|--------------|
| Montir / Mekanik | Mesin kendaraan, tools | Pekerjaan hands-on, visual |
| Teknisi Elektronik | Soldering, perbaikan hardware | Fokus individual, instruksi visual |
| Operator Mesin | Mesin produksi | Instruksi visual/display |

**Sumber data riil**: SLB program vokasional, BLK pelatihan otomotif

---

### Kategori 7: Hospitality

| Pekerjaan | Skill Dibutuhkan | Kenapa Cocok |
|-----------|-----------------|--------------|
| Hotel Staff (back office) | Admin hotel, housekeeping | Tugas back-office minim komunikasi tamu |
| Laundry Staff | Mesin cuci, setrika | Prosedural, visual |

**Sumber data riil**: Aston, Favehotel (Job Fair Kemnaker 2025)

---

### Kategori 8: Tekstil & Manufaktur

| Pekerjaan | Skill Dibutuhkan | Kenapa Cocok |
|-----------|-----------------|--------------|
| Penjahit / Tailor | Mesin jahit, pola | Visual, hands-on |
| Operator Produksi | Mesin produksi, SOP | Instruksi visual/SOP tertulis |

**Sumber data riil**: BLK pelatihan menjahit, industri garmen

---

## 5. Perusahaan Inklusif Tunarungu di Indonesia

| Perusahaan | Sektor | Detail | Tahun |
|-----------|--------|--------|-------|
| **Sunyi Coffee** (Sunyi Group) | F&B / Kedai kopi | 25+ karyawan tuli (100% deaf), Jakarta & Yogyakarta, didirikan 2019 oleh Mario Gultom | 2019–sekarang |
| **Kopi Tuli / Deaf Cafe Fingertalk** | F&B / Kedai kopi + cuci mobil | Didirikan Dissa Sakina Ahdanisa, menang SDGs Action Award Tokyo 2019, Depok | Berjalan |
| **Treestori Coffee** | F&B | Mempekerjakan anak berkebutuhan khusus, Jakarta | Berjalan |
| **Kawan Tuli Coffee Space** | F&B | Surakarta | Berjalan |
| **PT MAP Tbk** | Retail (MAP Group) | 11 tunarungu ditempatkan via kerjasama Kemensos + Sentra Mulya Jaya | 2025 |
| **Superindo** | Retail | Membuka lowongan disabilitas | 2025 |
| **Alfamart** | Retail | Membuka lowongan disabilitas | 2025 |
| **Indomaret** | Retail | Membuka lowongan disabilitas | 2025 |
| **Lawson Indonesia** | Retail | Membuka lowongan disabilitas | 2025 |
| **ISS Indonesia** | Facility services | Membuka lowongan disabilitas | 2025 |
| **Aston Hotels** | Hospitality | Membuka lowongan disabilitas | 2025 |
| **Favehotel** | Hospitality | Membuka lowongan disabilitas | 2025 |
| **MSIG Life Insurance** | Asuransi | Membuka lowongan disabilitas | 2025 |
| **Mitracomm Ekasarana** | BPO / Call Center | Text-based customer service | 2025 |
| **DHL Supply Chain** | Logistik | Checker, warehouse staff | 2025 |
| **PT Transjakarta** | Transportasi | Mempekerjakan Zidan (tunarungu, usia 20) | 2025 |

### Platform Lowongan Inklusif

- **Inclusive Job Center BPJS Ketenagakerjaan**: 1.396 lowongan aktif, 659 perusahaan terdaftar, 282.835 pencari kerja — [lokerdisabilitas.bpjsketenagakerjaan.go.id](https://lokerdisabilitas.bpjsketenagakerjaan.go.id/)
- **Kerjabilitas**: Platform pencarian kerja untuk disabilitas — [kerjabilitas.com](https://www.kerjabilitas.com/)
- **Facebook Group**: "Lowongan Pekerjaan Tunarungu & Tuli" — komunitas aktif 100.000+ anggota

---

## 6. Program Pemerintah & Pelatihan

### Regulasi Kunci

| Regulasi | Isi Penting |
|----------|------------|
| **UU No. 8/2016** Pasal 53 | Pemerintah & BUMN/BUMD wajib **minimal 2%** pekerja disabilitas; swasta **minimal 1%** |
| **UU No. 8/2016** Pasal 55 | Pemberi kerja wajib memberi aksesibilitas dan akomodasi yang layak |
| **UU No. 19/2011** | Ratifikasi UN Convention on Rights of Persons with Disabilities (CRPD) |
| **PP No. 60/2020** | Unit Layanan Disabilitas Bidang Ketenagakerjaan |
| **PP No. 70/2019** | Perencanaan, penyelenggaraan, dan evaluasi pemenuhan hak disabilitas |
| **Kepmenaker KEP-205/MEN/1999** | Pelatihan kerja dan penempatan tenaga kerja penyandang cacat |

### Program Aktif

| Program | Lembaga | Detail | Tahun |
|---------|---------|--------|-------|
| Pelatihan Tata Kafe untuk Tunarungu | BPVP Padang (Kemnaker) | Barista + manajemen kafe khusus tunarungu | 2026 |
| Pelatihan Tata Boga | BPVP Padang (Kemnaker) | Kuliner untuk tunarungu & lansia | 2026 |
| Pelatihan Barista | Dinsos DKI di PPKD Jakarta Timur | 25 penyandang disabilitas dilatih barista | 2025 |
| Pelatihan Content Creator + Barista | Pemprov DKI | Jakpreneur Disabilitas | 2025 |
| Penempatan Kerja | Kemensos + PT MAP | 11 tunarungu ditempatkan via Sentra Mulya Jaya | 2025 |
| Job Fair Inklusif | Kemnaker | 135 lowongan dari 26 perusahaan | 2025 |
| Inclusive Job Center | BPJS Ketenagakerjaan | Portal lowongan khusus disabilitas | Berjalan |
| ULD (Unit Layanan Disabilitas) | Kemnaker | 207 ULD di 28 provinsi, 127 kabupaten, 52 kota | 2023 |

### Organisasi Komunitas

- **Gerkatin** (Gerakan untuk Kesejahteraan Tunarungu Indonesia): Berdiri 1981, 31 DPD provinsi, 416 DPC kota, anggota World Federation of the Deaf sejak 1983
- **Pusbisindo** (Pusat Bahasa Isyarat Indonesia): Standardisasi dan pelatihan BISINDO
- **PLJ Indonesia**: Advokasi dan pemberdayaan

---

## 7. Hambatan Ketenagakerjaan Tunarungu

| Hambatan | Detail | Dampak |
|----------|--------|--------|
| **Komunikasi** | 68,2% tunarungu global menyebut ini alasan utama pengangguran (Worldmetrics 2026) | Tidak bisa mengikuti meeting verbal, wawancara lisan |
| **Stigma & diskriminasi** | 33,7% hadapi diskriminasi saat wawancara; 15,3% bias perekrut (global) | Sulit lolos seleksi |
| **Pendidikan** | Akses ke pendidikan tinggi terbatas; minim juru isyarat di kampus | Skill mismatch dengan kebutuhan industri |
| **Aksesibilitas fisik & digital** | Transportasi publik tidak aksesibel (Jakarta); platform lamaran tidak ramah teks | Sulit mengakses lowongan & lokasi kerja |
| **Minim juru isyarat** | Jumlah juru isyarat di institusi pemerintah, RS, sekolah sangat sedikit | Isolasi di lingkungan kerja |
| **Penegakan hukum lemah** | UU 8/2016 mandat 1-2% kuota tapi implementasi buruk | Hanya 1,1% terserap formal |
| **Sensory overload** | Lingkungan bising mengganggu pengguna alat bantu dengar | Tidak cocok di pabrik bising, open office ramai |

---

## 8. Implikasi untuk Onboarding Equaly

Berdasarkan seluruh data riset, onboarding yang akan dibangun untuk fokus Tunarungu:

### Step 1: Tingkat Pendengaran

Menggantikan pemilihan tipe disabilitas (yang sebelumnya multi-select).  
**Basis data**: ILO 2022 wage gap — tunarungu berat (disabilitas berat) memiliki kondisi kerja & upah berbeda dari gangguan ringan.

| Opsi | Deskripsi |
|------|-----------|
| `tuli_total` | Tidak bisa mendengar sama sekali |
| `gangguan_berat` | Gangguan pendengaran berat (mungkin menggunakan alat bantu dengar) |
| `gangguan_ringan` | Gangguan pendengaran ringan-sedang |

### Step 2: Preferensi Komunikasi

**Basis data**: Gerkatin, Pusbisindo, riset Sunyi Coffee (detik.com).  
Multi-select karena satu orang bisa menggunakan beberapa metode.

| Opsi | Konteks Penggunaan |
|------|-------------------|
| `bisindo` | Bahasa Isyarat Indonesia — alami, digunakan komunitas tuli sehari-hari |
| `sibi` | Sistem Isyarat Bahasa Indonesia — formal, digunakan di SLB/pendidikan |
| `teks_tertulis` | Komunikasi via chat, email, WhatsApp, dokumen tertulis |
| `bibir` | Membaca gerak bibir dalam percakapan langsung |
| `alat_bantu_dengar` | Menggunakan alat bantu dengar/cochlear implant |

### Step 3: Lingkungan Kerja + Akomodasi

**Basis data**: Studi kasus Sunyi Coffee, Gerkatin, Worldmetrics 2026.  
Memasukkan dimensi akomodasi yang tidak ada di onboarding sebelumnya.

| Opsi | Deskripsi |
|------|-----------|
| `remote` | Kerja jarak jauh (WFH) — paling ideal, komunikasi async |
| `hybrid` | Campuran remote & on-site |
| `onsite_juru_isyarat` | On-site dengan fasilitas juru bahasa isyarat |
| `onsite_notifikasi_visual` | On-site dengan sistem notifikasi visual (lampu, layar) |
| `onsite_standar` | On-site tanpa akomodasi khusus |

### Step 4: Kategori Keahlian

**Basis data**: 8 kategori pekerjaan dari data riil (lihat [Bagian 4](#4-kategori-pekerjaan-yang-cocok-untuk-tunarungu)).  
Predefined untuk memudahkan AI melakukan matching spesifik.

| Kategori | Sub-Keahlian |
|----------|-------------|
| `it_programming` | Web Dev, Mobile Dev, Backend, QA, Data Analyst |
| `desain_kreatif` | UI/UX, Graphic Design, Fotografi, Video Editing, Content Creator |
| `kuliner_tata_boga` | Barista, Koki, Baker, Kitchen Staff |
| `administrasi_data` | Data Entry, Admin Officer, Customer Service (text), Document Processing |
| `retail_logistik` | Kasir, Pramuniaga, Checker Gudang, Warehouse |
| `otomotif_teknisi` | Montir, Teknisi Elektronik, Operator Mesin |
| `hospitality` | Hotel Staff (back office), Laundry |
| `tekstil_manufaktur` | Penjahit, Operator Produksi |

---

## 9. Referensi Akademik & Sumber Data

### Regulasi

1. **UU No. 8 Tahun 2016** tentang Penyandang Disabilitas. Pasal 5, 11, 53, 55, 124.
2. **UU No. 19 Tahun 2011** — Ratifikasi UN CRPD.
3. **PP No. 60 Tahun 2020** — Unit Layanan Disabilitas Bidang Ketenagakerjaan.
4. **PP No. 70 Tahun 2019** — Perencanaan, Penyelenggaraan, Evaluasi Penghormatan, Pelindungan, dan Pemenuhan Hak Penyandang Disabilitas.
5. **Perpres No. 68 Tahun 2020** — Komisi Nasional Disabilitas.

### Statistik & Laporan

6. **BPS (2024).** *Potret Penyandang Disabilitas di Indonesia: Hasil Long Form SP2020.* Jakarta: Badan Pusat Statistik.  
   URL: [https://www.bps.go.id/id/publication/2024/12/20/43880dc0f8be5ab92199f8b9/](https://www.bps.go.id/id/publication/2024/12/20/43880dc0f8be5ab92199f8b9/)

7. **ILO (2022).** *Mapping Workers with Disabilities in Indonesia.* Gunawan, T. & Rezki, J.F. ISBN: 9789220358191.  
   URL: [https://www.ilo.org/publications/mapping-workers-disabilities-indonesia](https://www.ilo.org/publications/mapping-workers-disabilities-indonesia)

8. **Katadata.co.id (2025).** "Infografik: Penyandang Disabilitas Sulit Cari Kerja dan Diupah Rendah."  
   URL: [https://katadata.co.id/infografik/691aa5bc10be1/infografik-penyandang-disabilitas-sulit-cari-kerja-dan-diupah-rendah](https://katadata.co.id/infografik/691aa5bc10be1/infografik-penyandang-disabilitas-sulit-cari-kerja-dan-diupah-rendah)

9. **Worldmetrics (2026).** *Deaf Employment Statistics.*  
   URL: [https://worldmetrics.org/deaf-employment-statistics/](https://worldmetrics.org/deaf-employment-statistics/)

### Jurnal & Publikasi Akademik

10. **Erissa, D. & Widinarsih, D. (2022).** "Akses Penyandang Disabilitas Terhadap Pekerjaan: Kajian Literatur." *Jurnal Pembangunan Manusia*, Vol. 3, No. 1. Universitas Indonesia. DOI: 10.7454/jpm.v3i1.1027.

11. **Jurnal Pengabdian Pada Masyarakat (JPPM), Universitas Padjadjaran.** "Tanggung Jawab Sosial Perusahaan Di Indonesia Dalam Menciptakan Tempat Kerja Inklusif bagi Penyandang Disabilitas."  
    URL: [https://jurnal.unpad.ac.id/jppm/article/download/46489/pdf](https://jurnal.unpad.ac.id/jppm/article/download/46489/pdf)

12. **ResearchGate (2025).** "Inklusivitas di Tempat Kerja: Upaya Memenuhi Hak dan Fasilitas bagi Penyandang Disabilitas."  
    URL: [https://www.researchgate.net/publication/387897553](https://www.researchgate.net/publication/387897553)

13. **Law Reform, Universitas Diponegoro.** "Pemenuhan Hak Penyandang Disabilitas Dalam Mendapatkan Pekerjaan."  
    URL: [https://ejournal.undip.ac.id/index.php/lawreform/article/download/26181/15939](https://ejournal.undip.ac.id/index.php/lawreform/article/download/26181/15939)

14. **Prospect Publishing (2025).** "Assessing Solo's Inclusiveness for the Deaf Community: A Study of GERKATIN Solo Empowerment Programs."  
    URL: [https://prospectpublishing.id/ojs/index.php/icomsi/article/view/499](https://prospectpublishing.id/ojs/index.php/icomsi/article/view/499)

### Media & Studi Kasus

15. **detik.com (2025).** "Kafe Ini Punya 25 Karyawan Disabilitas, Apa Saja Upaya Adaptasinya?" — Studi kasus Sunyi Coffee.  
    URL: [https://www.detik.com/edu/edutainment/d-8271145/kafe-ini-punya-25-karyawan-disabilitas-apa-saja-upaya-adaptasinya](https://www.detik.com/edu/edutainment/d-8271145/kafe-ini-punya-25-karyawan-disabilitas-apa-saja-upaya-adaptasinya)

16. **Kompas.com (2025).** "Ada 135 Lowongan Kerja untuk Penyandang Disabilitas di Job Fair Kemenaker."  
    URL: [https://money.kompas.com/read/2025/05/22/152445126/ada-135-lowongan-kerja-untuk-penyandang-disabilitas-di-job-fair-kemenaker](https://money.kompas.com/read/2025/05/22/152445126/ada-135-lowongan-kerja-untuk-penyandang-disabilitas-di-job-fair-kemenaker)

17. **Tempo.co (2025).** "Sinergi Kemensos dan MAP Buka Peluang Kerja untuk Penyandang Disabilitas."  
    URL: [https://www.tempo.co/info-tempo/sinergi-kemensos-dan-map-buka-peluang-kerja-untuk-penyandang-disabilitas-2054946](https://www.tempo.co/info-tempo/sinergi-kemensos-dan-map-buka-peluang-kerja-untuk-penyandang-disabilitas-2054946)

18. **UnspokenASL.** "Urbanization and Its Impact on the Deaf Community in Indonesia."  
    URL: [https://www.unspokenasl.com/aslblogs/urbanization-and-its-impact-on-the-deaf-community-in-indonesia/](https://www.unspokenasl.com/aslblogs/urbanization-and-its-impact-on-the-deaf-community-in-indonesia/)

19. **Kemendikdasmen (Vokasi).** "Sunyi Coffee dan Ruang Kerja Setara untuk Pekerja Disabilitas."  
    URL: [https://vokasi.kemendikdasmen.go.id/read/b/sunyi-coffee-dan-ruang-kerja-setara-untuk-pekerja-disabilitas](https://vokasi.kemendikdasmen.go.id/read/b/sunyi-coffee-dan-ruang-kerja-setara-untuk-pekerja-disabilitas)

### Platform & Organisasi

20. **Gerkatin** — [https://www.gerkatin.org](https://www.gerkatin.org)
21. **Inclusive Job Center BPJS Ketenagakerjaan** — [https://lokerdisabilitas.bpjsketenagakerjaan.go.id/](https://lokerdisabilitas.bpjsketenagakerjaan.go.id/)
22. **Kerjabilitas** — [https://www.kerjabilitas.com/](https://www.kerjabilitas.com/)
23. **AIDRAN** (Australia-Indonesia Disability Research and Advocacy Network) — [https://aidran.org](https://aidran.org)
24. **Mitra Netra** — [https://mitranetra.or.id](https://mitranetra.or.id)
25. **PERTUNI** (Persatuan Tunanetra Indonesia) — [https://pertuni.or.id](https://pertuni.or.id)

---

> **Dokumen ini disusun sebagai acuan pengembangan fitur Onboarding dan AI Matching Equaly yang fokus pada penyandang Tunarungu.**  
> Semua keputusan desain dalam dokumen ini berdasarkan data eksternal (BPS, ILO, Gerkatin, jurnal akademik, studi kasus perusahaan), bukan data internal Equaly yang bersifat testing.
