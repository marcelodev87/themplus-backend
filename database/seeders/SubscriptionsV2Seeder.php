<?php

namespace Database\Seeders;

use App\Models\Subscription;
use Illuminate\Database\Seeder;

class SubscriptionsV2Seeder extends Seeder
{
    public function run()
    {
        Subscription::whereIn('name', [
            'premium',
            'master',
            'plus',
            'light',
        ])->delete();

        Subscription::where('name', 'basic')
            ->update(['price' => 89]);

        Subscription::updateOrCreate(
            ['name' => 'advanced'],
            ['price' => 129]
        );
    }
}
