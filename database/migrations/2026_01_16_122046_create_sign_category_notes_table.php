<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sign_category_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sign_category_id')->constrained()->cascadeOnDelete();
            $table->integer('position')->default(0);
            $table->text('content');
            $table->json('sign_ids');
            $table->timestamps();
        });

        // Seed the notes data
        $this->seedNotes();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sign_category_notes');
    }

    /**
     * Seed sign category notes data.
     */
    private function seedNotes(): void
    {
        $categoryMap = DB::table('sign_categories')->pluck('id', 'group_number')->toArray();

        $notesData = [
            // Category 1: მაფრთხილებელი (Warning)
            1 => [
                [
                    'content' => '<p>ნიშნები, რომლებიც დაუსახლებელში მეორდება არანაკლებ 50 მეტრში.</p>',
                    'sign_ids' => [103, 104, 114, 113, 131, 133],
                ],
            ],
            // Category 2: პრიორიტეტი (Priority)
            2 => [
                [
                    'content' => '<p><strong><span>შენიშვნა:</span></strong></p><p style="text-align: justify"><span>ა) 2.3 და 2.4 ნიშნების მოქმედება ვრცელდება სავალი ნაწილების იმ გადაკვეთაზე, რომლის წინაც დადგმულია სათანადო ნიშანი;</span></p>',
                    'sign_ids' => [24, 25],
                ],
                [
                    'content' => '<p><strong>შენიშვნა:</strong></p><p><span>ბ) 2.5 და 2.6 ნიშნები იდგმება უშუალოდ გზის ვიწრო უბნის წინ. 2.5 ნიშანი შეიძლება დაიდგას წინასწარ 8.1.1 დაფით.</span></p>',
                    'sign_ids' => [26, 27],
                ],
            ],
            // Category 3: ამკრძალავი (Prohibiting)
            3 => [
                [
                    'content' => '<p>ნიშნების მოქმედება არ ვრცელდება <span>სამარშრუტო სატრანსპორტო საშუალებაზე;</span></p>',
                    'sign_ids' => [28, 29, 30, 58, 47, 48, 49],
                ],
                [
                    'content' => '<p>ნიშნების მოქმედება არ ვრცელდება <span>სატრანსპორტო საშუალებაზე, რომელიც ემსახურება აღნიშნულ ზონაში მდებარე საწარმოს, ანდა ემსახურება ან ეკუთვნის ამავე ზონაში მცხოვრებ ან მომუშავე პირს. ამ შემთხვევაში სატრანსპორტო საშუალება შედის აღნიშნულ ზონაში და გამოდის იქიდან დანიშნულების ადგილისაკენ უახლოეს გზაჯვარედინზე;</span></p>',
                    'sign_ids' => [29, 30, 31, 32, 33, 34, 35],
                ],
                [
                    'content' => '<p>ნიშნების მოქმედება არ ვრცელდება <span>იმ სატრანსპორტო საშუალებაზე, რომელსაც მნიშვნელოვნად გამოხატული ან მკვეთრად გამოხატული შეზღუდული შესაძლებლობის სტატუსის მქონე პირი მართავს, ან იმ სატრანსპორტო საშუალებაზე, რომელსაც აღნიშნული პირები გადაჰყავს;</span></p>',
                    'sign_ids' => [30, 60, 61, 29, 59],
                ],
                [
                    'content' => '<p><strong>შენიშვნა: </strong></p><p><span>ე) 3.16, 3.20, 3.22, 3.24, 3.26–3.30 ნიშნების მოქმედება ვრცელდება სათანადო ნიშნის შემდეგ, ნიშნის დადგმის ადგილიდან უახლოეს გზაჯვარედინამდე, ხოლო დასახლებულ პუნქტში გზაჯვარედინის არარსებობისას – დასახლებული პუნქტის ბოლომდე.</span></p>',
                    'sign_ids' => [50, 52, 54, 56, 58, 59, 60, 61, 43],
                ],
                [
                    'content' => '<p><span>თ) 3.27 ნიშნის გამოყენება შეიძლება 1.4 მონიშვნასთან, ხოლო 3.28 ნიშნისა – 1.10 მონიშვნასთან ერთად. ამასთანავე, ნიშნების მოქმედების ზონა მონიშვნის ხაზის სიგრძით განისაზღვრება;</span></p>',
                    'sign_ids' => [59, 58],
                ],
                [
                    'content' => '<p><span>დ) 3.18.1 და 3.18.2 ნიშნების მოქმედება ვრცელდება სავალი ნაწილების იმ გადაკვეთაზე, რომლის წინაც დადგმულია სათანადო ნიშანი;</span></p><p><span>ი) 3.10, 3.27–3.30 ნიშნების მოქმედება ვრცელდება მხოლოდ გზის იმ მხარეს, რომელზედაც ისინია დადგმული.</span></p>',
                    'sign_ids' => [58, 59, 61, 60, 47, 48],
                ],
            ],
            // Category 4: მიმთითებელი (Directing)
            4 => [
                [
                    'content' => '<p><strong><span>შენიშვნა:</span></strong></p><p style="text-align: justify"><span>ა) 4.1.1−4.1.6 ნიშნები რთავს მხოლოდ ისრებით ნაჩვენები მიმართულებებით მოძრაობის ნებას, ხოლო ნიშნები, რომლებიც მარცხნივ მოხვევის ნებას რთავს, მობრუნების უფლებასაც იძლევა.</span></p><p style="text-align: justify"><span>ბ) 4.1.1−4.1.6 ნიშნების მოქმედება ვრცელდება სავალი ნაწილების იმ გადაკვეთაზე, რომლის წინაც დადგმულია სათანადო ნიშანი;</span></p><p style="text-align: justify"><span>გ) 4.1.1−4.1.6 ნიშნების მოქმედება არ ვრცელდება სამარშრუტო სატრანსპორტო საშუალებაზე;</span></p><p style="text-align: justify"><span>დ) 4.1.1 ნიშნის მოქმედება, რომელიც გზის მონაკვეთის დასაწყისშია დადგმული, ვრცელდება უახლოეს გზაჯვარედინამდე.</span></p>',
                    'sign_ids' => [6, 7, 8, 9, 10, 11],
                ],
            ],
            // Category 8: დამატებითი ინფორმაცია (Additional)
            8 => [
                [
                    'content' => '<p><strong><span>შენიშვნა:</span></strong></p><p style="text-align: justify"><span>ა) დამატებითი ინფორმაციის ნიშნები (დაფები) თავსდება უშუალოდ იმ ნიშნის ქვემოთ, რომელთანაც ისინი გამოიყენება. 8.2.2–8.2.4 და 8.13 დაფები თავსდება სათანადო ნიშნის გვერდით, თუ ეს ნიშანი განლაგებულია სავალი ნაწილის, გზისპირის ან ტროტუარის ზემოთ;</span></p>',
                    'sign_ids' => [67, 102],
                ],
            ],
            // Category 9: საგზაო მონიშვნები (Road Markings) - Note: group_number is 10 in DB
            10 => [
                [
                    'content' => '<p><span>ჰორიზონტალური მონიშვნა (ხაზი, ისარი, წარწერა და სხვა აღნიშვნა სავალ ნაწილზე) თეთრი ფერისაა, გარდა 1.4, 1.10 და 1.17 ხაზებისა, რომლებიც ყვითელი ფერისაა.</span></p>',
                    'sign_ids' => [217, 224, 233],
                ],
                [
                    'content' => '<p><strong><span>შენიშვნა:</span></strong><span> 1.1–1.11 ჰორიზონტალური მონიშვნების აღსანიშნავად შეიძლება გამოყენებულ იქნეს ტერმინი „გრძივი მონიშვნა", ხოლო 1.1, 1.3, 1.5, 1.6 და 1.11 ჰორიზონტალური მონიშვნების აღსანიშნავად − ტერმინი „ღერძულა ხაზი".</span></p>',
                    'sign_ids' => [214, 215, 216, 217, 219, 220, 221, 222, 223, 224, 225],
                ],
            ],
        ];

        $now = now();

        foreach ($notesData as $groupNumber => $notes) {
            $categoryId = $categoryMap[$groupNumber] ?? null;
            if (! $categoryId) {
                continue;
            }

            foreach ($notes as $position => $noteData) {
                DB::table('sign_category_notes')->insert([
                    'sign_category_id' => $categoryId,
                    'position' => $position,
                    'content' => $noteData['content'],
                    'sign_ids' => json_encode($noteData['sign_ids']),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
};
