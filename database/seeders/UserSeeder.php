<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all tenants
        $tenants = Tenant::all();

        if ($tenants->isEmpty()) {
            $this->command->warn('No tenants found. Please run TenantSeeder first.');
            return;
        }

        // Create super admin (not tied to any tenant - for platform management)
        User::create([
            'tenant_id' => $tenants->first()->id, // Assign to first tenant for now
            'name' => 'Super Admin',
            'email' => 'admin@boxmanager.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->command->info('Super Admin created: admin@boxmanager.com / password');

        // Create admin users for each tenant
        foreach ($tenants as $tenant) {
            // Admin for this tenant
            User::create([
                'tenant_id' => $tenant->id,
                'name' => "{$tenant->name} Admin",
                'email' => "admin@{$tenant->domain}",
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            $this->command->info("Admin created for {$tenant->name}: admin@{$tenant->domain} / password");

            // Manager for this tenant
            User::create([
                'tenant_id' => $tenant->id,
                'name' => "{$tenant->name} Manager",
                'email' => "manager@{$tenant->domain}",
                'password' => Hash::make('password'),
                'role' => 'manager',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            // 2 Employees for this tenant
            for ($i = 1; $i <= 2; $i++) {
                User::create([
                    'tenant_id' => $tenant->id,
                    'name' => "{$tenant->name} Employee {$i}",
                    'email' => "employee{$i}@{$tenant->domain}",
                    'password' => Hash::make('password'),
                    'role' => 'employee',
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]);
            }

            $this->command->info("Manager and 2 employees created for {$tenant->name}");
        }

        $this->command->info('✅ User seeder completed successfully!');
        $this->command->info('Default password for all users: password');
    }
}
