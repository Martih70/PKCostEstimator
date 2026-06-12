<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@pkcost.test'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $roles = [
            'admin' => 'Admin',
            'cost_manager' => 'Cost Manager',
            'reviewer' => 'Reviewer',
        ];

        foreach ($roles as $role => $label) {
            for ($i = 1; $i <= 3; $i++) {
                User::updateOrCreate(
                    ['email' => "{$role}{$i}@pkcost.test"],
                    [
                        'name' => "{$label} {$i}",
                        'password' => Hash::make('password'),
                        'role' => $role,
                        'email_verified_at' => now(),
                    ]
                );
            }
        }
    }
}
