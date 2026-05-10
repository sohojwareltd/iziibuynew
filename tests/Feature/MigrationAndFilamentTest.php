<?php

declare(strict_types=1);

use App\Models\User;
use Database\Seeders\RoleSeeder;

beforeEach(function (): void {
    $this->seed(RoleSeeder::class);
});

it('runs migrations successfully', function (): void {
    expect(\Illuminate\Support\Facades\Schema::hasTable('orders'))->toBeTrue();
    expect(\Illuminate\Support\Facades\Schema::hasTable('roles'))->toBeTrue();
});

it('redirects guests from filament panel', function (): void {
    $this->get('/panel')->assertRedirect();
});

it('allows admin user to access filament panel', function (): void {
    $user = User::factory()->create([
        'role_id' => User::ROLES['Admin'],
        'email' => 'filament-admin@test.com',
        'password' => bcrypt('password'),
    ]);
    $user->assignRole('admin');

    $this->actingAs($user)->get('/panel')->assertOk();
});

it('allows admin user to access retailer filament resource', function (): void {
    $user = User::factory()->create([
        'role_id' => User::ROLES['Admin'],
        'email' => 'filament-retailers@test.com',
        'password' => bcrypt('password'),
    ]);
    $user->assignRole('admin');

    $this->actingAs($user)->get('/panel/retailer-metas')->assertOk();
});
