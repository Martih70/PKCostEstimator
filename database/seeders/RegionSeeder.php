<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Region::firstOrCreate(['slug' => 'islamabad-rawalpindi'], ['name' => 'Islamabad/Rawalpindi']);
        Region::firstOrCreate(['slug' => 'sialkot'],              ['name' => 'Sialkot']);
        Region::firstOrCreate(['slug' => 'lahore'],               ['name' => 'Lahore']);
        Region::firstOrCreate(['slug' => 'karachi'],              ['name' => 'Karachi']);
        Region::firstOrCreate(['slug' => 'motra'],                ['name' => 'Motra']);
        Region::firstOrCreate(['slug' => 'other'],                ['name' => 'Other']);
    }
}
