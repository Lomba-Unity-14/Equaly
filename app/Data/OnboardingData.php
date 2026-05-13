<?php

namespace App\Data;

class OnboardingData
{
    public const HEARING_LEVELS = [
        'tuli_total' => 'Tuli total',
        'gangguan_berat' => 'Gangguan pendengaran berat',
        'gangguan_ringan' => 'Gangguan pendengaran ringan/sedang',
    ];

    public const HEARING_DESCRIPTIONS = [
        'tuli_total' => 'Tidak dapat mendengar suara sama sekali. Sepenuhnya bergantung pada komunikasi visual: bahasa isyarat, teks tertulis, atau notifikasi visual.',
        'gangguan_berat' => 'Masih dapat mendengar suara sangat keras. Mungkin menggunakan alat bantu dengar, namun komunikasi verbal sangat terbatas. Lebih mengandalkan teks atau isyarat.',
        'gangguan_ringan' => 'Kesulitan mendengar suara pelan atau percakapan dalam lingkungan bising. Dapat berkomunikasi verbal dengan bantuan alat bantu dengar.',
    ];

    public const COMMUNICATION_PREFERENCES = [
        'bisindo' => 'BISINDO (Bahasa Isyarat Indonesia)',
        'sibi' => 'SIBI (Sistem Isyarat Bahasa Indonesia)',
        'teks_tertulis' => 'Teks tertulis (chat/email/dokumen)',
        'bibir' => 'Membaca gerak bibir',
        'alat_bantu' => 'Alat bantu dengar',
    ];

    public const WORK_ENVIRONMENTS = [
        'remote' => 'Remote (WFH)',
        'hybrid' => 'Hybrid',
        'onsite_juru_isyarat' => 'On-site dengan juru bahasa isyarat',
        'onsite_notifikasi_visual' => 'On-site dengan notifikasi visual',
        'onsite_standar' => 'On-site tanpa akomodasi khusus',
    ];

    public const SKILL_CATEGORIES = [
        'it_programming' => 'IT & Programming',
        'desain_kreatif' => 'Desain & Kreatif',
        'kuliner_tata_boga' => 'Kuliner & Tata Boga',
        'administrasi_data' => 'Administrasi & Data',
        'retail_logistik' => 'Retail & Logistik',
        'otomotif_teknisi' => 'Otomotif & Teknisi',
        'hospitality' => 'Hospitality',
        'tekstil_manufaktur' => 'Tekstil & Manufaktur',
    ];

    public const SKILL_CATEGORY_ICONS = [
        'it_programming' => 'code',
        'desain_kreatif' => 'palette',
        'kuliner_tata_boga' => 'restaurant_menu',
        'administrasi_data' => 'description',
        'retail_logistik' => 'inventory_2',
        'otomotif_teknisi' => 'build',
        'hospitality' => 'room_service',
        'tekstil_manufaktur' => 'manufacturing',
    ];

    public const SKILL_SUBS = [
        'it_programming' => [
            'web_developer' => 'Web Developer',
            'mobile_developer' => 'Mobile Developer',
            'backend_developer' => 'Backend Developer',
            'fullstack_developer' => 'Fullstack Developer',
            'qa_tester' => 'QA / Software Tester',
            'data_analyst' => 'Data Analyst',
            'it_support' => 'IT Support',
            'devops' => 'DevOps',
        ],
        'desain_kreatif' => [
            'ui_ux_designer' => 'UI/UX Designer',
            'graphic_designer' => 'Graphic Designer',
            'illustrator' => 'Illustrator',
            'video_editor' => 'Video Editor',
            'photographer' => 'Photographer',
            'content_creator' => 'Content Creator',
            'motion_designer' => 'Motion Designer',
        ],
        'kuliner_tata_boga' => [
            'barista' => 'Barista',
            'koki' => 'Koki / Juru Masak',
            'baker_pastry' => 'Baker / Pastry',
            'kitchen_staff' => 'Kitchen Staff',
            'food_preparation' => 'Food Preparation',
        ],
        'administrasi_data' => [
            'data_entry' => 'Data Entry',
            'admin_officer' => 'Admin Officer',
            'document_processing' => 'Document Processing',
            'customer_service_text' => 'Customer Service (Text-based)',
            'office_assistant' => 'Office Assistant',
        ],
        'retail_logistik' => [
            'kasir' => 'Kasir',
            'pramuniaga' => 'Pramuniaga',
            'checker_gudang' => 'Checker Gudang',
            'warehouse_staff' => 'Warehouse Staff',
            'inventory' => 'Inventory',
        ],
        'otomotif_teknisi' => [
            'montir' => 'Montir / Mekanik',
            'teknisi_elektronik' => 'Teknisi Elektronik',
            'operator_mesin' => 'Operator Mesin',
            'teknisi_ac' => 'Teknisi AC',
            'teknisi_hardware' => 'Teknisi Komputer / Hardware',
        ],
        'hospitality' => [
            'hotel_staff' => 'Hotel Staff (Back Office)',
            'laundry_staff' => 'Laundry Staff',
            'room_attendant' => 'Room Attendant',
            'kitchen_helper' => 'Kitchen Helper',
        ],
        'tekstil_manufaktur' => [
            'penjahit' => 'Penjahit / Tailor',
            'operator_produksi' => 'Operator Produksi',
            'quality_control' => 'Quality Control',
            'packing' => 'Packing',
        ],
    ];

    public const CATEGORY_MAJORS = [
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

    public const SMK_MAJORS = [
        'Rekayasa Perangkat Lunak', 'Teknik Komputer dan Jaringan', 'Multimedia',
        'Desain Grafis', 'Desain Komunikasi Visual', 'Tata Boga', 'Perhotelan',
        'Akuntansi', 'Administrasi Perkantoran', 'Pemasaran',
        'Teknik Kendaraan Ringan', 'Teknik Sepeda Motor',
        'Teknik Elektronika', 'Teknik Mesin', 'Tata Busana',
    ];

    public const EDUCATION_LEVELS = [
        'sd' => 'SD / Sederajat',
        'smp' => 'SMP / Sederajat',
        'sma_smk' => 'SMA / SMK / Sederajat',
        'd1_d4' => 'D1 - D4',
        's1' => 'S1 / Sarjana',
        'profesi' => 'Pendidikan Profesi',
        's2' => 'S2 / Magister',
        's3' => 'S3 / Doktor',
    ];

    public const EDUCATION_HAS_MAJOR = ['d1_d4', 's1', 'profesi', 's2', 's3'];

    public const JOB_TYPES = [
        'penuh_waktu' => 'Penuh Waktu (Full-time)',
        'part_time' => 'Part-Time',
        'magang' => 'Magang (Internship)',
        'freelance' => 'Freelance',
        'kontrak' => 'Kontrak',
    ];

    public const LOCATIONS = [
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

    public const DISABILITY_CONDITIONS = [
        'tunarungu' => 'Tunarungu (Tuli/Deaf)',
    ];

    public const MONTHS = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
        '04' => 'April', '05' => 'Mei', '06' => 'Juni',
        '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
        '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
    ];

    public const MATCH_TIERS = [
        [
            'min' => 80,
            'label' => 'Sangat Disarankan',
            'descs' => [
                'Kamu berpotensi sesuai dengan lowongan ini',
                'Profil kamu dapat meningkatkan peluang di posisi ini',
                'Skill dan preferensimu selaras dengan kebutuhan lowongan',
            ],
            'icon' => 'check_circle',
            'variant' => 'high',
        ],
        [
            'min' => 60,
            'label' => 'Disarankan',
            'descs' => [
                'Cukup sesuai, pertimbangkan mengasah skill terkait',
                'Peluangmu cukup baik, beberapa skill bisa diperkuat',
                'Profil kamu cukup cocok, tingkatkan di beberapa area',
            ],
            'icon' => 'info',
            'variant' => 'medium',
        ],
        [
            'min' => 40,
            'label' => 'Dipertimbangkan',
            'descs' => [
                'Masih ada gap, disarankan ikut pelatihan terlebih dahulu',
                'Kamu punya dasar, tapi perlu mengembangkan skill lebih lanjut',
                'Kesenjangan skill cukup terasa, academy dapat membantu',
            ],
            'icon' => 'warning',
            'variant' => 'medium',
        ],
        [
            'min' => 0,
            'label' => 'Belum Disarankan',
            'descs' => [
                'Profil kamu belum sesuai dengan lowongan ini',
                'Coba jelajahi lowongan yang lebih sesuai keahlianmu',
                'Kesenjangan cukup besar, lihat rekomendasi pelatihan',
            ],
            'icon' => 'warning',
            'variant' => 'low',
        ],
    ];

    public static function matchTier(int $score, ?int $seed = null): array
    {
        foreach (self::MATCH_TIERS as $tier) {
            if ($score >= $tier['min']) {
                $tier['desc'] = self::pickTierDesc($tier, $seed);

                return $tier;
            }
        }

        $fallback = self::MATCH_TIERS[3];
        $fallback['desc'] = self::pickTierDesc($fallback, $seed);

        return $fallback;
    }

    protected static function pickTierDesc(array $tier, ?int $seed): string
    {
        $descs = $tier['descs'] ?? [];

        if (empty($descs)) {
            return '';
        }

        if ($seed !== null) {
            return $descs[$seed % count($descs)];
        }

        return $descs[array_rand($descs)];
    }

    public static function getMajorsForCategories(array $categories, bool $isSmk = false): array
    {
        if ($isSmk) {
            return array_combine(self::SMK_MAJORS, self::SMK_MAJORS);
        }

        $majors = [];
        foreach ($categories as $category) {
            foreach (self::CATEGORY_MAJORS[$category] ?? [] as $major) {
                $majors[$major] = $major;
            }
        }

        return $majors;
    }

    public static function getSelectedSubSkills(array $selectedCategories, array $selectedSubSkills): array
    {
        $result = [];
        foreach ($selectedCategories as $category) {
            $label = self::SKILL_CATEGORIES[$category] ?? $category;
            $subs = [];
            foreach ($selectedSubSkills as $sub) {
                $subLabel = self::SKILL_SUBS[$category][$sub] ?? null;
                if ($subLabel) {
                    $subs[] = $subLabel;
                }
            }
            $result[$label] = $subs;
        }

        return $result;
    }

    public static function getDisabilityTrivia(?object $companyAgg, ?string $jobDetail): array
    {
        if ($companyAgg && $companyAgg->total > 0) {
            $pct = round(($companyAgg->friendly / $companyAgg->total) * 100);

            if ($pct < 50) {
                return [
                    'text' => 'Lowongan ini kurang ramah disabilitas',
                    'icon' => 'sentiment_dissatisfied',
                    'variant' => 'bad',
                ];
            }

            return [
                'text' => 'Lowongan ini disability friendly!',
                'icon' => 'diversity_3',
                'variant' => 'good',
            ];
        }

        if (self::jobDetailMentionsDisability($jobDetail)) {
            return [
                'text' => 'Lowongan ini menerima disabilitas',
                'icon' => 'info',
                'variant' => 'neutral',
            ];
        }

        return [
            'text' => 'Belum ada informasi menerima disabilitas',
            'icon' => 'help_outline',
            'variant' => 'noinfo',
        ];
    }

    protected static function jobDetailMentionsDisability(?string $jobDetail): bool
    {
        if (empty($jobDetail)) {
            return false;
        }

        $keywords = ['disabilitas', 'disability', 'tunarungu', 'tuli', 'deaf', 'difabel', 'inklusif', 'inklusi', 'penyandang'];
        $lower = strtolower($jobDetail);

        foreach ($keywords as $kw) {
            if (str_contains($lower, $kw)) {
                return true;
            }
        }

        return false;
    }
}
