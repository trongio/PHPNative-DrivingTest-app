<?php

use App\Models\Answer;
use App\Models\LicenseType;
use App\Models\Question;
use App\Models\QuestionCategory;
use App\Models\Sign;
use App\Models\SignCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Seeds all question-related data for NativePHP mobile.
     */
    public function up(): void
    {
        // Skip if data already exists (for re-running migrations)
        if (QuestionCategory::count() > 0) {
            return;
        }

        $this->seedQuestionCategories();
        $this->seedSignCategories();
        $this->seedLicenseTypes();
        $this->seedSigns();
        $this->seedQuestions();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clear all seeded data in reverse order
        DB::table('question_license_type')->truncate();
        DB::table('question_sign')->truncate();
        Answer::query()->delete();
        Question::query()->delete();
        Sign::query()->delete();
        LicenseType::query()->delete();
        SignCategory::query()->delete();
        QuestionCategory::query()->delete();
    }

    private function seedQuestionCategories(): void
    {
        $categories = [
            1 => 'ტერმინები',
            2 => 'მძღოლის უფლებები',
            3 => 'მაფრთხილებელი',
            4 => 'პრიორიტეტი',
            5 => 'მიმთითებელი',
            6 => 'ამკრძალავი',
            7 => 'დამატებითი',
            8 => 'საცნობი ნიშნები',
            9 => 'საინფორმაციო',
            10 => 'რევერსული ზოლი',
            11 => 'გზაზე გასვლა',
            12 => 'სასწავლო',
            13 => 'მონიშვნა',
            14 => 'შუქნიშანი',
            15 => 'გზაჯვარედინი',
            16 => 'ბავშვები',
            17 => 'სპეც-მანქანა',
            18 => 'მარეგულირებელი',
            19 => 'სამუხრუჭე',
            20 => 'მკვეთრი დამუხრუჭება',
            21 => 'ბუქსირება',
            22 => 'ავტომაგისტრალი',
            23 => 'უწესივრობა',
            24 => 'კანონმდებლობა',
            25 => 'ეკო-მართვა',
            26 => 'სიგნალები',
            27 => 'არასაკმარისი ხილვად.',
            28 => 'საავარიო',
            29 => 'სიჩქარე',
            30 => 'მეხრე',
            31 => 'ზოლებში მოძრაობა',
            32 => 'გასწრება',
            33 => 'რკინიგზა',
            34 => 'ქვეითი',
            35 => 'გაჩერება, დგომა',
            36 => 'სამედიცინო',
            37 => 'ჯარიმები',
            38 => 'არ დაგავიწყდეთ!',
            39 => 'დარწმუნდეს',
            40 => 'სამარშრუტო',
            41 => 'მასა',
            42 => 'მოპედი',
            43 => 'მოცურება',
            44 => 'ლოგიკური',
            45 => 'კონვენცია',
            46 => 'გადაზიდვები',
        ];

        foreach ($categories as $id => $name) {
            QuestionCategory::create([
                'id' => $id,
                'name' => $name,
            ]);
        }
    }

    private function seedSignCategories(): void
    {
        $categories = [
            ['name' => 'მაფრთხილებელი', 'group_number' => 1],
            ['name' => 'პრიორიტეტი', 'group_number' => 2],
            ['name' => 'ამკრძალავი', 'group_number' => 3],
            ['name' => 'მიმთითებელი', 'group_number' => 4],
            ['name' => 'განსაკუთრებული მითითების', 'group_number' => 5],
            ['name' => 'სერვისის', 'group_number' => 6],
            ['name' => 'საინფორმაციო', 'group_number' => 7],
            ['name' => 'დამატებითი ინფორმაცია', 'group_number' => 8],
            ['name' => 'შუქნიშანი', 'group_number' => 9],
            ['name' => 'საგზაო მონიშვნები', 'group_number' => 10],
            ['name' => 'თეგეტას ნიშნები', 'group_number' => 11],
        ];

        foreach ($categories as $category) {
            SignCategory::create($category);
        }
    }

    private function seedLicenseTypes(): void
    {
        $parentTypes = [
            'B' => [
                'name' => 'მსუბუქი ავტომობილი',
                'children' => [
                    ['code' => 'B1', 'name' => 'კვადროციკლი, მოპედი'],
                ],
            ],
            'A' => [
                'name' => 'მოტოციკლი',
                'children' => [
                    ['code' => 'A1', 'name' => 'მსუბუქი მოტოციკლი'],
                    ['code' => 'A2', 'name' => 'საშუალო მოტოციკლი'],
                ],
            ],
            'C' => [
                'name' => 'სატვირთო',
                'children' => [
                    ['code' => 'C1', 'name' => 'მსუბუქი სატვირთო'],
                ],
            ],
            'D' => [
                'name' => 'ავტობუსი',
                'children' => [
                    ['code' => 'D1', 'name' => 'მინიბუსი'],
                ],
            ],
            'T' => [
                'name' => 'ტრაქტორი',
                'children' => [
                    ['code' => 'S', 'name' => 'თვითმავალი მანქანა'],
                ],
            ],
        ];

        foreach ($parentTypes as $parentCode => $parentData) {
            $parent = LicenseType::create([
                'code' => $parentCode,
                'name' => $parentData['name'],
                'is_parent' => true,
                'parent_id' => null,
            ]);

            foreach ($parentData['children'] as $child) {
                LicenseType::create([
                    'code' => $child['code'],
                    'name' => $child['name'],
                    'is_parent' => false,
                    'parent_id' => $parent->id,
                ]);
            }
        }
    }

    private function seedSigns(): void
    {
        $jsonPath = database_path('data/signs_utf8.json');

        if (! File::exists($jsonPath)) {
            return;
        }

        $signs = json_decode(File::get($jsonPath), true);
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

    private function seedQuestions(): void
    {
        $jsonPath = database_path('data/questions_utf8.json');

        if (! File::exists($jsonPath)) {
            return;
        }

        $data = json_decode(File::get($jsonPath), true);
        $questions = $data['paginationData']['data'];

        $licenseTypeMap = LicenseType::pluck('id', 'code')->toArray();
        $signMap = Sign::pluck('id', 'image')->toArray();

        foreach ($questions as $questionData) {
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

            // Link to license types
            $mainCat = $questionData['main_cat'] ?? '';
            $licenseTypeIds = $this->parseLicenseTypes($mainCat, $licenseTypeMap);
            if (! empty($licenseTypeIds)) {
                $question->licenseTypes()->attach($licenseTypeIds);
            }

            // Link to signs
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

        $mainCat = trim($mainCat, '|');
        if ($mainCat === '') {
            return $ids;
        }

        $groups = explode('|', $mainCat);

        foreach ($groups as $group) {
            $codes = preg_split('/[,\s]+/', $group, -1, PREG_SPLIT_NO_EMPTY);

            foreach ($codes as $code) {
                $code = trim($code);
                $code = preg_replace('/\s*\([^)]*\)/', '', $code);
                $code = trim($code);

                if ($code !== '' && isset($licenseTypeMap[$code])) {
                    $ids[] = $licenseTypeMap[$code];
                }
            }
        }

        return array_unique($ids);
    }
};
