<?php

namespace Database\Seeders;

use App\Models\LicenseType;
use Illuminate\Database\Seeder;

class LicenseTypeSeeder extends Seeder
{
    public function run(): void
    {
        // Parent license types (shown in UI for selection)
        // Children share the same question pool as their parent
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
            // Create parent license type
            $parent = LicenseType::create([
                'code' => $parentCode,
                'name' => $parentData['name'],
                'is_parent' => true,
                'parent_id' => null,
            ]);

            // Create child license types
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
}
