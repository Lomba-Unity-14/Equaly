<?php

namespace Database\Seeders;

use App\Models\JobVacancyData;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JobVacancyDataSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $csvPath = database_path('jobs_clean.csv');

        if (! file_exists($csvPath)) {
            $this->command->error("CSV file not found: $csvPath");

            return;
        }

        $handle = fopen($csvPath, 'r');
        if (! $handle) {
            $this->command->error('Failed to open CSV file.');

            return;
        }

        $header = fgetcsv($handle, 0, ',');
        if (! $header) {
            fclose($handle);
            $this->command->error('Failed to read CSV header.');

            return;
        }

        $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);

        JobVacancyData::truncate();

        $rows = [];
        $columns = ['job_title', 'company', 'location', 'salary', 'job_detail', 'company_size', 'category', 'company_logo_url', 'job_url'];

        while (($data = fgetcsv($handle, 0, ',')) !== false) {
            if (count($data) < count($columns)) {
                continue;
            }

            $row = [];
            foreach ($columns as $i => $col) {
                $value = $data[$i] ?? null;
                $value = $value === '' ? null : $value;
                $row[$col] = $value;
            }

            if ($row['location'] && stripos($row['location'], 'Jakarta Raya') !== false) {
                $row['location'] = 'Jakarta';
            }

            $row['education_req'] = $this->extractEducation($row['job_detail'] ?? '');
            $row['skill_req'] = $this->extractSkills($row['job_detail'] ?? '', $row['job_title'] ?? '');
            $row['work_type'] = $this->detectWorkType($row['job_detail'] ?? '', $row['job_title'] ?? '', $row['category'] ?? '');
            $row['employment_type'] = $this->detectEmploymentType($row['job_detail'] ?? '', $row['job_title'] ?? '');

            $rows[] = $row;
        }

        fclose($handle);

        foreach (array_chunk($rows, 25) as $chunk) {
            JobVacancyData::insert($chunk);
        }

        $this->command->info('Imported '.count($rows).' records from jobs_clean.csv.');
    }

    protected function extractEducation(?string $detail): ?string
    {
        if (empty($detail)) {
            return null;
        }

        $keywords = ['SD', 'SMP', 'SMA', 'SMK', 'D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3',
            'Diploma', 'Bachelor', 'Master', 'PhD', 'Sarjana', 'Magister', 'Doktor', ];
        $found = [];

        foreach ($keywords as $keyword) {
            if (preg_match('/\b'.preg_quote($keyword, '/').'\b/i', $detail)) {
                $found[] = $keyword;
            }
        }

        if (preg_match('/(?:pendidikan|pend\.?|min\.?|minimal)\s*(?:min\.?|:)?\s*(S[123D]|D[1234]|SMA|SMK|Diploma|Bachelor|Master)/i', $detail, $m)) {
            $found[] = strtoupper($m[1]);
        }

        return ! empty($found) ? implode(', ', array_unique($found)) : null;
    }

    protected function extractSkills(?string $detail, ?string $title): ?string
    {
        if (empty($detail)) {
            return $title;
        }

        $sectionHeaders = 'Kualifikasi|Requirements?|Skill\s*(?:yang\s*Dibutuhkan)?|Keahlian|Persyaratan|Kemampuan|Menguasai|Mahir|Required\s*Skill|Tanggung\s*Jawab|Tugas\s*&\s*Tanggung|Job\s*Desc';
        $stopHeaders = 'Manfaat|Tentang|Deskripsi|Benefit|Fasilitas|Cara\s*Melamar|Gaji|Salary|Kirim|Bergabunglah|Pendidikan|Kualifikasi|Skill';

        $allSkills = [];

        $pattern = '/(?:'.$sectionHeaders.')\s*[:\n]?(.+?)(?=\n\s*\n|(?:(?:'.$stopHeaders.')\s*[:\n]?))/si';

        if (preg_match_all($pattern, $detail, $matches)) {
            foreach ($matches[1] as $content) {
                $content = strip_tags($content);

                $content = preg_replace('/\)(?=[A-Z][a-z])/', ")\n", $content);
                $content = preg_replace('/\.(?=[A-Z][a-z])/', ".\n", $content);

                $lines = preg_split('/\r?\n/', $content);

                foreach ($lines as $line) {
                    $line = trim($line);
                    $line = preg_replace('/^[\s•\-–*\d]+\.?\s*/', '', $line);
                    $line = trim($line);

                    if (strlen($line) < 3) {
                        continue;
                    }

                    preg_match_all('/\b([A-Z][a-zA-Z0-9+#._-]+(?:\s*[A-Z][a-zA-Z0-9+#._-]+)*)\b/', $line, $techMatches);
                    foreach ($techMatches[0] as $tech) {
                        $tech = trim($tech);
                        if (strlen($tech) > 1 && strlen($tech) < 40 && ! $this->isNonSkill($tech)) {
                            $allSkills[] = $tech;
                        }
                    }

                    preg_match_all('/\b(api|css|html|js|json|xml|sql|php|ui|ux|seo|git|rest|soap|http|tcp|ip|dns|dhcp|vpn|ssh|linux|unix|mysql|redis|mongodb|docker|kubernetes|aws|gcp|azure|ci|cd)\b/i', $line, $kwMatches);
                    foreach ($kwMatches[0] as $kw) {
                        $allSkills[] = $kw;
                    }
                }
            }
        }

        $allSkills = array_unique($allSkills);
        natsort($allSkills);

        if (! empty($allSkills)) {
            return implode(', ', array_slice($allSkills, 0, 30));
        }

        preg_match_all('/\b([A-Z][a-zA-Z0-9+#._-]+)\b/', $detail, $m);
        $fallback = array_unique($m[1]);
        $fallback = array_filter($fallback, fn ($t) => strlen($t) > 2 && strlen($t) < 30 && ! $this->isNonSkill($t));

        return ! empty($fallback) ? implode(', ', array_slice($fallback, 0, 15)) : ($title ?? '');
    }

    protected function isNonSkill(string $text): bool
    {
        $lower = strtolower($text);

        $non = [
            'ditempatkan', 'penempatan', 'bersedia', 'kendaraan', 'domisili',
            'pendidikan', 'minimal', 'pengalaman', 'diutamakan',             'persyaratan', 'informatika', 'programmer', 'developer',
            'berkepribadian', 'rapi', 'rapih', 'sopan', 'sabar', 'jujur', 'teliti',
            'tanggung jawab', 'disiplin', 'cepat belajar', 'mau belajar',
            'inisiatif', 'proaktif', 'kreatif', 'inovatif', 'fisik', 'sehat',
            'pria', 'wanita', 'perempuan', 'usia', 'umur',
            'kerjasama', 'kelompok', 'individu', 'mandiri',
            'memahami', 'mengerti', 'mengetahui', 'mampu', 'dapat',
            'memiliki', 'menguasai', 'mengikuti',
            'willing', 'able', 'ability', 'experience', 'minimum', 'preferred',
            'komputer', 'laptop', 'nilai tambah', 'konsep',
            'berbahasa', 'inggris', 'english', 'lisan', 'tulisan', 'lancar', 'aktif', 'pasif',
            'target', 'tekanan', 'deadline', 'shift', 'lembur',
            'berorientasi', 'hasil', 'berkembang',
            'pelayanan', 'melayani',
            'berkendaraan', 'transportasi', 'berpengalaman',
            'mengembang', 'membangun', 'melakukan', 'membuat', 'mengelola',
            'memantau', 'menjaga', 'berkolaborasi', 'bekerja', 'melayani',
            'menangani', 'memberikan', 'menyusun', 'mengawasi', 'mengatur',
            'menyediakan', 'menyiapkan', 'mengintegrasikan',
            'mendesain', 'memelihara', 'menggunakan',
            'staff', 'team',
            'familiarity', 'proficiency', 'proven', 'hands.on', 'hands-on', 'hands on',
            'bachelor', 'degree', 'minimum', 'required', 'plus',
            'ipk', 'gpa', 'information', 'technical', 'system', 'strong', 'solid',
            'bagi', 'dengan', 'paham', 'untuk', 'yang', 'pada',
            'dan', 'atau', 'serta', 'akan', 'dari', 'dalam',
            'kepada', 'adalah', 'terhadap', 'sebagai',
            'hal', 'agar', 'setiap', 'setelah', 'sebelum',
            'telah', 'sudah', 'oleh', 'tanpa',
            'kami', 'anda', 'dia', 'saya', 'mereka',
            'sebuah', 'seorang', 'suatu', 'para', 'lain',
            'dapat', 'bisa', 'pernah', 'belum',
            'mengimplementasikan', 'mengintegrasikan', 'mengaplikasikan',
            'tugas', 'posisi', 'tanggung', 'jawab',
            'the', 'this', 'you', 'your', 'will', 'key', 'note', 'what',
            'responsibilities', 'responsibility', 'requirements', 'qualifications',

        ];

        foreach ($non as $phrase) {
            if (str_contains($lower, $phrase)) {
                return true;
            }
        }

        return false;
    }

    protected function detectWorkType(?string $detail, ?string $title, ?string $category): string
    {
        $text = strtolower(($detail ?? '').' '.($title ?? ''));

        if (preg_match('/\b(remote|wfh|work\s*(from|at)\s*home|kerja\s*dari\s*rumah)\b/i', $text)) {
            return 'Remote';
        }

        if (preg_match('/\b(hybrid)\b/i', $text)) {
            return 'Hybrid';
        }

        $remoteFriendly = ['IT/Programmer', 'IT Programmer', 'IT/', 'Programmer', 'Developer',
            'Software', 'Data', 'Fullstack', 'Full Stack', 'Backend', 'Frontend', ];
        foreach ($remoteFriendly as $kw) {
            if (stripos($category ?? '', $kw) !== false || stripos($title ?? '', $kw) !== false) {
                return 'Hybrid';
            }
        }

        return 'On-site';
    }

    protected function detectEmploymentType(?string $detail, ?string $title): string
    {
        $text = strtolower(($detail ?? '').' '.($title ?? ''));

        if (preg_match('/\b(magang|internship|intern|fresh\s*graduate)\b/i', $text)) {
            return 'Magang (Internship)';
        }

        if (preg_match('/\b(freelance|project\s*(base|based))\b/i', $text)) {
            return 'Freelance';
        }

        if (preg_match('/\b(kontrak|contract|temporer)\b/i', $text)) {
            return 'Kontrak';
        }

        if (preg_match('/\b(paruh\s*waktu|part[- ]?time)\b/i', $text)) {
            return 'Part-Time';
        }

        return 'Penuh Waktu (Full-time)';
    }
}
