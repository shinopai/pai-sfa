<?php

namespace Database\Seeders;

use App\Models\Deal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DealSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Deal::factory()
            ->count(300)
            ->create();
    }
}
