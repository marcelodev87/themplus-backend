<?php

namespace Database\Seeders;

use App\Models\Subscription;
use Illuminate\Database\Seeder;

class CounterSubscriptionsSeeder extends Seeder
{
    public function run()
    {
        Subscription::updateOrCreate(
            ['name' => 'C5'],
            ['price' => 299.99, 'type' => 'counter', 'client_limit' => 5],
        );

        Subscription::updateOrCreate(
            ['name' => 'C10'],
            ['price' => 499.99, 'type' => 'counter', 'client_limit' => 10],
        );

        Subscription::updateOrCreate(
            ['name' => 'CIlimited'],
            ['price' => 799.99, 'type' => 'counter', 'client_limit' => 20],
        );
    }
}
