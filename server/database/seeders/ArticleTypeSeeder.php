<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArticleTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            'Book of Abstract',
            'Book review',
            'Case Report',
            'Clinical Trial Study',
            'Commentary',
            'Conference Paper',
            'Cross Sectional Study',
            'Current Frontier',
            'Ebook',
            'Ebook Chapter',
            'Editorial',
            'Guidelines',
            'Highlights',
            'Image Comments',
            'Industry News',
            'Letter article',
            'Letter to the Editor',
            'Meta-Analysis',
            'Mini-review',
            'Opinion Article',
            'Patent News',
            'Perspective',
            'Position Paper',
            'Preface',
            'Research Article',
            'Review Article',
            'Scoping Review',
            'Short Communication',
            'Systematic review',
            'Technical note',
        ];

        foreach ($types as $type) {
            DB::table('article_types')->insert([
                'name' => $type
            ]);
        }
    }
}
