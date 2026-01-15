<?php

namespace Database\Seeders;

use App\Models\SignCategory;
use Illuminate\Database\Seeder;

class SignCategorySeeder extends Seeder
{
    public function run(): void
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
}
