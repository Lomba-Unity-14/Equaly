<?php

namespace Database\Seeders;

use App\Models\CompanyReview;
use App\Models\JobApplication;
use App\Models\JobUserMatch;
use App\Models\JobVacancyData;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // ── Demo user ──
        $user = User::firstOrCreate(
            ['email' => 'demo@equaly.id'],
            [
                'name' => 'Demo Pengguna',
                'password' => Hash::make('password'),
                'date_of_birth' => '2001-05-15',
            ]
        );

        // ── Profile (onboarding selesai) ──
        $user->profile()->firstOrCreate(
            ['user_id' => $user->id],
            [
                'disability_condition' => ['tunarungu'],
                'hearing_level' => 'tuli_total',
                'communication_preference' => ['bisindo', 'teks_tertulis'],
                'work_environment' => ['onsite_juru_isyarat', 'onsite_standar'],
                'skill_categories' => [
                    ['category' => 'kuliner_tata_boga', 'subs' => ['barista']],
                ],
                'education_level' => 'sma_smk',
                'education_major' => 'Tata Boga',
                'job_types' => ['penuh_waktu', 'kontrak'],
                'preferred_locations' => ['jakarta_selatan', 'jakarta_pusat'],
                'onboarding_completed' => true,
                'headline' => 'Barista | Tunarungu',
            ]
        );

        // ── 6 match untuk job F&B/Barista ──
        if ($user->jobUserMatches()->count() === 0) {
            $jobs = JobVacancyData::where('category', 'Kuliner/FnB/Barista')
                ->inRandomOrder()
                ->take(6)
                ->get();

            $matchConfigs = [
                [88, 90, 85, 80, 85, 95, 'Pekerjaan barista sangat cocok untuk tunarungu karena bersifat visual dan prosedural. Komunikasi dapat dilakukan menggunakan isyarat dan teks, tanpa memerlukan komunikasi verbal intensif.'],
                [84, 85, 82, 78, 80, 95, 'Pekerjaan F&B ini ideal untuk tunarungu karena tugas-tugasnya berbasis visual dan dapat dilakukan dengan panduan SOP tertulis. Lingkungan kerja mendukung komunikasi non-verbal.'],
                [76, 80, 72, 75, 70, 85, 'Lingkungan kerja dapur mendukung komunikasi visual dan gestur. Tugas memasak bersifat prosedural dan dapat diikuti dengan panduan visual.'],
                [72, 75, 68, 72, 68, 85, 'Pekerjaan ini cocok untuk tunarungu karena interaksi utama bersifat visual. Tugas dapat dilakukan dengan panduan SOP dan koordinasi tim via isyarat dasar.'],
                [65, 70, 60, 65, 60, 80, 'Pekerjaan ini cukup cocok dengan preferensi Anda terutama jika perusahaan menyediakan akomodasi komunikasi visual dan teks tertulis.'],
                [60, 62, 58, 60, 55, 80, 'Pekerjaan ini memiliki potensi kecocokan dengan preferensi Anda, terutama dengan dukungan komunikasi teks tertulis dan lingkungan yang mendukung.'],
            ];

            foreach ($jobs as $i => $job) {
                $cfg = $matchConfigs[$i];
                JobUserMatch::create([
                    'job_vacancy_data_id' => $job->id,
                    'user_id' => $user->id,
                    'match_score' => $cfg[0],
                    'is_match' => $cfg[0] >= 60,
                    'disability_score' => $cfg[1],
                    'skill_score' => $cfg[2],
                    'environment_score' => $cfg[3],
                    'communication_score' => $cfg[4],
                    'education_score' => $cfg[5],
                    'match_reason' => $cfg[6],
                    'calculated_at' => now(),
                ]);
            }
        }

        // ── 2 aplikasi + review untuk label "disability friendly" ──
        if ($user->jobApplications()->count() === 0) {
            $jobs = JobVacancyData::where('category', 'Kuliner/FnB/Barista')
                ->inRandomOrder()
                ->take(2)
                ->get();

            foreach ($jobs as $job) {
                $app = JobApplication::create([
                    'user_id' => $user->id,
                    'job_vacancy_data_id' => $job->id,
                    'company_name' => $job->company,
                    'applied_at' => now()->subDays(rand(1, 30)),
                ]);

                CompanyReview::create([
                    'user_id' => $user->id,
                    'job_application_id' => $app->id,
                    'company_name' => $job->company,
                    'is_accepted' => true,
                    'has_disability_employees' => 1,
                    'is_friendly' => true,
                    'experience' => 'Lingkungan kerja sangat mendukung teman tuli. Komunikasi menggunakan BISINDO dan teks tertulis. Rekan kerja ramah dan sabar.',
                ]);
            }
        }
    }
}
