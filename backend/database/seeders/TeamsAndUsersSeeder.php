<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TeamsAndUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Create hospital teams
        $teams = [
            ['name' => 'Emergency Department', 'code' => 'ER', 'description' => 'Emergency and urgent care services'],
            ['name' => 'Cardiology', 'code' => 'CARDIO', 'description' => 'Heart and cardiovascular care'],
            ['name' => 'Pediatrics', 'code' => 'PEDS', 'description' => 'Children healthcare services'],
            ['name' => 'Surgery', 'code' => 'SURGERY', 'description' => 'Surgical department'],
            ['name' => 'Radiology', 'code' => 'RAD', 'description' => 'Medical imaging and diagnostics'],
            ['name' => 'Laboratory', 'code' => 'LAB', 'description' => 'Medical laboratory services'],
            ['name' => 'Pharmacy', 'code' => 'PHARMA', 'description' => 'Pharmaceutical services'],
            ['name' => 'Nursing', 'code' => 'NURSING', 'description' => 'Nursing care and support'],
            ['name' => 'Administration', 'code' => 'ADMIN', 'description' => 'Hospital administration'],
            ['name' => 'IT Department', 'code' => 'IT', 'description' => 'Information technology services'],
        ];

        foreach ($teams as $teamData) {
            Team::create($teamData);
        }

        // Create default admin user
        $adminTeam = Team::where('code', 'ADMIN')->first();
        
        User::create([
            'name' => 'System Administrator',
            'email' => 'admin@hospital.local',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'team_id' => $adminTeam->id,
            'is_active' => true,
        ]);

        // Create sample team leaders
        $erTeam = Team::where('code', 'ER')->first();
        User::create([
            'name' => 'Dr. Emergency Leader',
            'email' => 'er.leader@hospital.local',
            'password' => Hash::make('password123'),
            'role' => 'team_leader',
            'team_id' => $erTeam->id,
            'is_active' => true,
        ]);

        $cardioTeam = Team::where('code', 'CARDIO')->first();
        User::create([
            'name' => 'Dr. Cardio Leader',
            'email' => 'cardio.leader@hospital.local',
            'password' => Hash::make('password123'),
            'role' => 'team_leader',
            'team_id' => $cardioTeam->id,
            'is_active' => true,
        ]);

        echo "Teams and users seeded successfully!\n";
        echo "Admin login: admin@hospital.local / admin123\n";
    }
}
