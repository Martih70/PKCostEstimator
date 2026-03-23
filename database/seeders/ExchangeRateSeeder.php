<?php

namespace Database\Seeders;

use App\Models\ExchangeRate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExchangeRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ExchangeRate::firstOrCreate(['currency_code' => 'USD'], ['rate_to_pkr' => 277.00, 'effective_date' => '2025-01-01']);
        ExchangeRate::firstOrCreate(['currency_code' => 'GBP'], ['rate_to_pkr' => 350.00, 'effective_date' => '2025-01-01']);
    }
}
