<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Region;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $regionSialkot = Region::where('slug', 'sialkot')->first();
        $regionIslamabad = Region::where('slug', 'islamabad-rawalpindi')->first();
        $regionKarachi = Region::where('slug', 'karachi')->first();
        $regionLahore = Region::where('slug', 'lahore')->first();
        $regionMoitra = Region::where('slug', 'motra')->first();
        $regionOther = Region::where('slug', 'other')->first();

        Project::create(['project_nr' => '1257', 'name' => 'Sialkot', 'region_id' => $regionSialkot->id, 'gross_floor_area' => 412.00]);
        Project::create(['project_nr' => '1273', 'name' => 'Sialkot', 'region_id' => $regionSialkot->id, 'gross_floor_area' => 412.00]);
        Project::create(['project_nr' => '1455', 'name' => 'Islamabad', 'region_id' => $regionIslamabad->id, 'gross_floor_area' => 236.00]);
        Project::create(['project_nr' => '1542', 'name' => 'Islamabad (Property)', 'region_id' => $regionIslamabad->id, 'gross_floor_area' => null, 'exclude_from_estimator' => true]);
        Project::create(['project_nr' => '1562', 'name' => 'Karachi Korangi', 'region_id' => $regionKarachi->id, 'gross_floor_area' => 252.00]);
        Project::create(['project_nr' => '1637', 'name' => 'Akhtar Colony', 'region_id' => $regionKarachi->id, 'gross_floor_area' => 145.00]);
        Project::create(['project_nr' => '1644', 'name' => 'Baldia Town', 'region_id' => $regionKarachi->id, 'gross_floor_area' => 278.00]);
        Project::create(['project_nr' => '1645', 'name' => 'Drigh Road', 'region_id' => $regionKarachi->id, 'gross_floor_area' => 209.00]);
        Project::create(['project_nr' => '1646', 'name' => 'Hyderabad', 'region_id' => $regionOther->id, 'gross_floor_area' => null]);
        Project::create(['project_nr' => '1703', 'name' => 'Lahore Garhi Shahu', 'region_id' => $regionLahore->id, 'gross_floor_area' => 187.00]);
        Project::create(['project_nr' => '1709', 'name' => 'Islamabad', 'region_id' => $regionIslamabad->id, 'gross_floor_area' => 654.00]);
        Project::create(['project_nr' => '1837', 'name' => 'Motra', 'region_id' => $regionMoitra->id, 'gross_floor_area' => null]);
        Project::create(['project_nr' => '1838', 'name' => 'Rawalpindi', 'region_id' => $regionIslamabad->id, 'gross_floor_area' => 312.00]);
        Project::create(['project_nr' => '1846', 'name' => 'Karachi Akhtar Colony', 'region_id' => $regionKarachi->id, 'gross_floor_area' => null]);
        Project::create(['project_nr' => '1882', 'name' => 'Karachi Korangi', 'region_id' => $regionKarachi->id, 'gross_floor_area' => null]);
        Project::create(['project_nr' => '1243', 'name' => 'Lahore Youhanabad', 'region_id' => $regionLahore->id, 'gross_floor_area' => null]);
        Project::create(['project_nr' => '333', 'name' => 'LDC Support', 'region_id' => null, 'gross_floor_area' => null, 'is_overhead_centre' => true]);
    }
}
