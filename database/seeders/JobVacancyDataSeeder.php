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
        $csvPath = database_path('jobstreet_data.csv');

        if (!file_exists($csvPath)) {
            $this->command->error("CSV file not found: $csvPath");
            return;
        }

        $handle = fopen($csvPath, 'r');
        if (!$handle) {
            $this->command->error("Failed to open CSV file.");
            return;
        }

        $header = fgetcsv($handle, 0, ';');

        if (!$header) {
            fclose($handle);
            $this->command->error("Failed to read CSV header.");
            return;
        }

        $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);

        JobVacancyData::truncate();

        $rows = [];
        $columns = [
            'url', 'company', 'location', 'job_title', 'jobdesk',
            'salary', 'job_category', 'skill_req', 'education_req',
            'work_type', 'company_size', 'image_logo_url',
        ];

        while (($data = fgetcsv($handle, 0, ';')) !== false) {
            if (count($data) < count($columns)) {
                continue;
            }

            $row = [];
            foreach ($columns as $i => $col) {
                $value = $data[$i] ?? null;
                $value = $value === 'Tidak Disebutkan' ? null : $value;
                $value = $value === '' ? null : $value;
                $row[$col] = $value;
            }

            $rows[] = $row;
        }

        fclose($handle);

        foreach (array_chunk($rows, 25) as $chunk) {
            JobVacancyData::insert($chunk);
        }

        $this->command->info('Imported ' . count($rows) . ' records from jobstreet_data.csv.');
    }
}
