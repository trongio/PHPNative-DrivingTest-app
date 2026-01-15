<?php

namespace Database\Seeders;

use App\Models\QuestionCategory;
use Illuminate\Database\Seeder;

class QuestionCategorySeeder extends Seeder
{
    public function run(): void
    {
        // Create 46 question categories (cat_id 1-46 from JSON)
        for ($i = 1; $i <= 46; $i++) {
            QuestionCategory::create([
                'name' => "Category {$i}",
            ]);
        }
    }
}
