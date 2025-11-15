<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call seeders in the correct order (respecting foreign key constraints)
        $this->call([
            TenantSeeder::class,
            SiteSeeder::class,
            BuildingSeeder::class,
            FloorSeeder::class,
            BoxSeeder::class,
            CustomerSeeder::class,
            ContractSeeder::class,
        ]);

        $this->command->info('Database seeding completed successfully!');
        $this->command->info('You can now login and explore the application.');
    }
}
