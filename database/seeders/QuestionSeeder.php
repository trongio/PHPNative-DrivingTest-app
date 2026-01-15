<?php

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\LicenseType;
use App\Models\Question;
use App\Models\Sign;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        $jsonPath = database_path('data/questions_utf8.json');
        $data = json_decode(File::get($jsonPath), true);
        $questions = $data['paginationData']['data'];

        // Build license type map (code => id)
        $licenseTypeMap = LicenseType::pluck('id', 'code')->toArray();

        // Build sign map (image => id) for linking questions to signs
        $signMap = Sign::pluck('id', 'image')->toArray();

        foreach ($questions as $questionData) {
            // Create the question
            $question = Question::create([
                'question_category_id' => $questionData['cat_id'],
                'question' => $questionData['question'],
                'description' => $questionData['description'] ?? null,
                'full_description' => $questionData['fullDescription'] ?? null,
                'image' => $questionData['img'] ?? null,
                'image_custom' => $questionData['img_own'] ?: null,
                'is_short_image' => (bool) $questionData['short_image'],
                'has_small_answers' => (bool) $questionData['small_answers'],
                'is_active' => ! (bool) $questionData['inactive'],
            ]);

            // Create answers (ans1 is always correct)
            $answerFields = ['ans1', 'ans2', 'ans3', 'ans4'];
            foreach ($answerFields as $index => $field) {
                $text = trim($questionData[$field] ?? '');
                if ($text !== '') {
                    Answer::create([
                        'question_id' => $question->id,
                        'text' => $text,
                        'is_correct' => $field === 'ans1',
                        'position' => $index + 1,
                    ]);
                }
            }

            // Parse main_cat and link to license types
            $mainCat = $questionData['main_cat'] ?? '';
            $licenseTypeIds = $this->parseLicenseTypes($mainCat, $licenseTypeMap);
            if (! empty($licenseTypeIds)) {
                $question->licenseTypes()->attach($licenseTypeIds);
            }

            // Link to signs via pivot
            $signs = $questionData['signs'] ?? [];
            $signIds = [];
            foreach ($signs as $sign) {
                $signImage = $sign['img'] ?? null;
                if ($signImage && isset($signMap[$signImage])) {
                    $signIds[] = $signMap[$signImage];
                }
            }
            if (! empty($signIds)) {
                $question->signs()->attach(array_unique($signIds));
            }
        }
    }

    private function parseLicenseTypes(string $mainCat, array $licenseTypeMap): array
    {
        $ids = [];

        // Remove leading/trailing pipes and split by |
        $mainCat = trim($mainCat, '|');
        if ($mainCat === '') {
            return $ids;
        }

        $groups = explode('|', $mainCat);

        foreach ($groups as $group) {
            // Split by comma for combined types like "B, B1" or "T,S"
            $codes = preg_split('/[,\s]+/', $group, -1, PREG_SPLIT_NO_EMPTY);

            foreach ($codes as $code) {
                $code = trim($code);
                // Ignore values in brackets like "(12)"
                $code = preg_replace('/\s*\([^)]*\)/', '', $code);
                $code = trim($code);

                if ($code !== '' && isset($licenseTypeMap[$code])) {
                    $ids[] = $licenseTypeMap[$code];
                }
            }
        }

        return array_unique($ids);
    }
}
