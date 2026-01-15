<?php

namespace Database\Seeders;

use App\Models\LicenseType;
use Illuminate\Database\Seeder;

class LicenseTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['code' => 'A', 'name' => 'Motorcycle'],
            ['code' => 'A1', 'name' => 'Light Motorcycle'],
            ['code' => 'AM', 'name' => 'Moped'],
            ['code' => 'B', 'name' => 'Car'],
            ['code' => 'B1', 'name' => 'Light Car'],
            ['code' => 'C', 'name' => 'Truck'],
            ['code' => 'C1', 'name' => 'Light Truck'],
            ['code' => 'D', 'name' => 'Bus'],
            ['code' => 'D1', 'name' => 'Minibus'],
            ['code' => 'Mil', 'name' => 'Military'],
            ['code' => 'S', 'name' => 'Special'],
            ['code' => 'T', 'name' => 'Tractor'],
            ['code' => 'Tram', 'name' => 'Tram'],
        ];

        foreach ($types as $type) {
            LicenseType::create($type);
        }
    }
}
