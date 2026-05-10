<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            1 => 'admin',
            2 => 'user',
            3 => 'vendor',
            4 => 'manager',
            5 => 'retailer',
            6 => 'external',
            7 => 'enterprise',
        ];

        foreach ($roles as $id => $name) {
            DB::table('roles')->updateOrInsert(
                ['id' => $id],
                [
                    'name' => $name,
                    'guard_name' => 'web',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE roles AUTO_INCREMENT = 8');
        }

        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@iziibuy.test'],
            [
                'name' => 'Admin',
                'last_name' => 'User',
                'password' => Hash::make('password'),
                'role_id' => 1,
            ]
        );
        $admin->syncRoles(['admin']);
    }
}
