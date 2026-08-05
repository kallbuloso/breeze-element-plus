<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $tenant = Tenant::factory()->create([
            'name' => 'Default Tenant',
        ]);

        User::factory()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'is_owner' => true,
        ]);
    }
}
