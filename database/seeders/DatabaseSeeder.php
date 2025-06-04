<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            CategorySeeder::class,
            UserSeeder::class,
            SkillSeeder::class,
            TaskSeeder::class,
            BidSeeder::class,
            BookingSeeder::class,
            ReviewSeeder::class,
            MessageSeeder::class,
            NotificationSeeder::class,
            PaymentSeeder::class,
        ]);
    }
}
