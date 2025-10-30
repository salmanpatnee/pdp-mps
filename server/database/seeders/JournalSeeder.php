<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JournalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('journals')->insert([
            [
                'name' => 'International Journal of Artificial Intelligence',
                'issn' => '1234-5678',
                'eissn' => '8765-4321',
                'abbreviation' => 'IJAI',
                'description' => 'Publishes cutting-edge research in artificial intelligence, machine learning, and computational intelligence.',
                'publisher' => 'Springer Nature',
                'email' => 'contact@ijai.org',
                'website_url' => 'https://ijai.springernature.com',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Journal of Cybersecurity and Information Systems',
                'issn' => '2345-6789',
                'eissn' => '9876-5432',
                'abbreviation' => 'JCIS',
                'description' => 'Focuses on cybersecurity, data protection, and the application of secure information systems in business and government.',
                'publisher' => 'Oxford University Press',
                'email' => 'editor@jcis.oxfordjournals.org',
                'website_url' => 'https://jcis.oxfordjournals.org',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Medical Research and Innovations Journal',
                'issn' => '3456-7890',
                'eissn' => '0987-6543',
                'abbreviation' => 'ARIJ',
                'description' => 'Covers clinical medicine, healthcare innovations, and translational biomedical research.',
                'publisher' => 'Elsevier',
                'email' => 'submissions@mrijournal.com',
                'website_url' => 'https://mrijournal.elsevier.com',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
