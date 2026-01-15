<?php

namespace Database\Seeders;

use App\Models\Sign;
use App\Models\SignCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class SignSeeder extends Seeder
{
    public function run(): void
    {
        $jsonPath = database_path('data/signs_utf8.json');
        $signs = json_decode(File::get($jsonPath), true);

        // Build a map of group_number to sign_category_id
        $categoryMap = SignCategory::pluck('id', 'group_number')->toArray();

        foreach ($signs as $signData) {
            $signCategoryId = $categoryMap[$signData['group']] ?? null;

            if (! $signCategoryId) {
                continue;
            }

            Sign::create([
                'sign_category_id' => $signCategoryId,
                'position' => $signData['position'],
                'is_child' => (bool) $signData['child'],
                'image' => $signData['img'],
                'title' => $signData['title'],
                'title_en' => $signData['signTitleEng'] ?? null,
                'description' => $signData['text'] ?? null,
            ]);
        }
    }
}
