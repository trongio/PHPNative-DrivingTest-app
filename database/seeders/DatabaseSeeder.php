<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            QuestionCategorySeeder::class,
            SignCategorySeeder::class,
            LicenseTypeSeeder::class,
            SignSeeder::class,
            QuestionSeeder::class,
        ]);
    }
}
