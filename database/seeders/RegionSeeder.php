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
        Region::create(['name' => 'Islamabad/Rawalpindi', 'slug' => 'islamabad-rawalpindi']);
        Region::create(['name' => 'Sialkot', 'slug' => 'sialkot']);
        Region::create(['name' => 'Lahore', 'slug' => 'lahore']);
        Region::create(['name' => 'Karachi', 'slug' => 'karachi']);
        Region::create(['name' => 'Motra', 'slug' => 'motra']);
        Region::create(['name' => 'Other', 'slug' => 'other']);
    }
}
