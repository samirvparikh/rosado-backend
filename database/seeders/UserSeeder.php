<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Admin', 'email' => 'admin@rosado.test', 'mobile' => '9000000001', 'password' => '123456', 'isAdmin' => true],
            ['name' => 'Samir', 'email' => 'samir@rosado.test', 'mobile' => '9000000002', 'password' => '123456', 'isAdmin' => true],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'mobile' => $user['mobile'],
                    'password' => Hash::make($user['password']),
                    'is_admin' => $user['isAdmin'],
                ],
            );
        }
    }
}
