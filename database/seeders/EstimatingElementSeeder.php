<?php

namespace Database\Seeders;

use App\Models\EstimatingElement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EstimatingElementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EstimatingElement::create(['code' => '60A', 'name' => 'Substructure inc Floor', 'sort_order' => 1]);
        EstimatingElement::create(['code' => '60B-W', 'name' => 'External Walls', 'sort_order' => 2]);
        EstimatingElement::create(['code' => '60B-R', 'name' => 'Roof', 'sort_order' => 3]);
        EstimatingElement::create(['code' => '60CE', 'name' => 'Internal Fit-Out & Finish', 'sort_order' => 4]);
        EstimatingElement::create(['code' => '60D2', 'name' => 'Plumbing & Heating', 'sort_order' => 5]);
        EstimatingElement::create(['code' => '60D3', 'name' => 'HVAC', 'sort_order' => 6]);
        EstimatingElement::create(['code' => '60D5', 'name' => 'Electrical & Lighting', 'sort_order' => 7]);
        EstimatingElement::create(['code' => '60D67', 'name' => 'Audio Video & Alarms', 'sort_order' => 8]);
        EstimatingElement::create(['code' => '60F', 'name' => 'Special', 'sort_order' => 9]);
    }
}
