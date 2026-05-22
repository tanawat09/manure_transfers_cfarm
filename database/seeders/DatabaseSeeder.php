<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Farm;
use App\Models\ManurePile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Users
        User::create([
            'name' => 'ผู้ดูแลระบบ (Admin)',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'เจ้าหน้าที่ปฏิบัติงาน (Staff)',
            'email' => 'staff@example.com',
            'password' => Hash::make('password'),
            'role' => 'staff',
        ]);

        User::create([
            'name' => 'ผู้บริหาร / ผู้ดูรายงาน (Viewer)',
            'email' => 'viewer@example.com',
            'password' => Hash::make('password'),
            'role' => 'viewer',
        ]);

        // 2. Seed Farms (3 sample farms)
        $farms = ['ฟาร์ม A (สาขาใหญ่)', 'ฟาร์ม B (สาขาตะวันตก)', 'ฟาร์ม C (สาขาใต้)'];
        foreach ($farms as $farmName) {
            Farm::create(['name' => $farmName]);
        }

        // 3. Seed Manure Piles (22 piles)
        for ($i = 1; $i <= 22; $i++) {
            ManurePile::create(['name' => "กอง {$i}"]);
        }
    }
}
