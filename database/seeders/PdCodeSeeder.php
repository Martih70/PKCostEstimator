<?php

namespace Database\Seeders;

use App\Models\EstimatingElement;
use App\Models\PdCode;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PdCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch estimating elements
        $elem60A   = EstimatingElement::where('code', '60A')->first();
        $elem60BW  = EstimatingElement::where('code', '60B-W')->first();
        $elem60BR  = EstimatingElement::where('code', '60B-R')->first();
        $elem60CE  = EstimatingElement::where('code', '60CE')->first();
        $elem60D2  = EstimatingElement::where('code', '60D2')->first();
        $elem60D3  = EstimatingElement::where('code', '60D3')->first();
        $elem60D5  = EstimatingElement::where('code', '60D5')->first();
        $elem60D67 = EstimatingElement::where('code', '60D67')->first();
        $elem60F   = EstimatingElement::where('code', '60F')->first();

        // Element 60A — Substructure inc Floor
        PdCode::firstOrCreate(['code' => '60A1010'], ['name' => 'Foundations', 'area_code' => '60A-60C', 'mh_sort_order' => 210, 'estimating_element_id' => $elem60A->id, 'category' => 'construction']);

        // Element 60B-W (External Walls)
        PdCode::firstOrCreate(['code' => '60B2010'], ['name' => 'External Walls',        'area_code' => '60A-60C', 'mh_sort_order' => 260,  'estimating_element_id' => $elem60BW->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '60B2011'], ['name' => 'External Painting',     'area_code' => '60A-60C', 'mh_sort_order' => 270,  'estimating_element_id' => $elem60BW->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '60B2020'], ['name' => 'External Windows',      'area_code' => '60A-60C', 'mh_sort_order' => 280,  'estimating_element_id' => $elem60BW->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '60B2050'], ['name' => 'External Doors',        'area_code' => '60A-60C', 'mh_sort_order' => 290,  'estimating_element_id' => $elem60BW->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '30B2010'], ['name' => 'SP External Walls',     'area_code' => '60A-60C', 'mh_sort_order' => null, 'estimating_element_id' => $elem60BW->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '30B2011'], ['name' => 'SP External Painting',  'area_code' => '60A-60C', 'mh_sort_order' => null, 'estimating_element_id' => $elem60BW->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '30B2020'], ['name' => 'SP External Windows',   'area_code' => '60A-60C', 'mh_sort_order' => null, 'estimating_element_id' => $elem60BW->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '30B2050'], ['name' => 'SP External Doors',     'area_code' => '60A-60C', 'mh_sort_order' => null, 'estimating_element_id' => $elem60BW->id, 'category' => 'construction']);

        // Element 60B-R (Roof)
        PdCode::firstOrCreate(['code' => '60B1011'], ['name' => 'Structural Frame',    'area_code' => '60A-60C', 'mh_sort_order' => 220,  'estimating_element_id' => $elem60BR->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '60B1012'], ['name' => 'Floor Construction',  'area_code' => '60A-60C', 'mh_sort_order' => 230,  'estimating_element_id' => $elem60BR->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '60B1020'], ['name' => 'Roof Structure',      'area_code' => '60A-60C', 'mh_sort_order' => 240,  'estimating_element_id' => $elem60BR->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '60B3010'], ['name' => 'Roof Covering',       'area_code' => '60A-60C', 'mh_sort_order' => 250,  'estimating_element_id' => $elem60BR->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '30B1011'], ['name' => 'SP Structural Frame', 'area_code' => '60A-60C', 'mh_sort_order' => null, 'estimating_element_id' => $elem60BR->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '30B1012'], ['name' => 'SP Floor Construction','area_code' => '60A-60C','mh_sort_order' => null,  'estimating_element_id' => $elem60BR->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '30B1020'], ['name' => 'SP Roof Structure',   'area_code' => '60A-60C', 'mh_sort_order' => null, 'estimating_element_id' => $elem60BR->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '30B3010'], ['name' => 'SP Roof Covering',    'area_code' => '60A-60C', 'mh_sort_order' => null, 'estimating_element_id' => $elem60BR->id, 'category' => 'construction']);

        // Element 60CE (Internal Fit-Out & Finish — C + E areas)
        PdCode::firstOrCreate(['code' => '60C1010'], ['name' => 'Internal Partitions',                'area_code' => '60A-60C', 'mh_sort_order' => 300,  'estimating_element_id' => $elem60CE->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '60C1020'], ['name' => 'Internal Windows',                   'area_code' => '60A-60C', 'mh_sort_order' => 310,  'estimating_element_id' => $elem60CE->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '60C1030'], ['name' => 'Internal Doors',                     'area_code' => '60A-60C', 'mh_sort_order' => 320,  'estimating_element_id' => $elem60CE->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '60C1070'], ['name' => 'Suspended Ceiling',                  'area_code' => '60A-60C', 'mh_sort_order' => 330,  'estimating_element_id' => $elem60CE->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '60C1090'], ['name' => 'Interior Specialities',              'area_code' => '60A-60C', 'mh_sort_order' => 340,  'estimating_element_id' => $elem60CE->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '60C1092'], ['name' => 'Interior Specialities (Plumbing/Sanitary)', 'area_code' => '60A-60C', 'mh_sort_order' => null, 'estimating_element_id' => $elem60CE->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '60C1097'], ['name' => 'Wardrobe & Storage',                 'area_code' => '60A-60C', 'mh_sort_order' => 350,  'estimating_element_id' => $elem60CE->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '60C2010'], ['name' => 'Wall/Ceiling Finishes',              'area_code' => '60A-60C', 'mh_sort_order' => 360,  'estimating_element_id' => $elem60CE->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '60C2011'], ['name' => 'Wall Tiling',                        'area_code' => '60A-60C', 'mh_sort_order' => 370,  'estimating_element_id' => $elem60CE->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '60C2032'], ['name' => 'Floor Tiling',                       'area_code' => '60A-60C', 'mh_sort_order' => 380,  'estimating_element_id' => $elem60CE->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '60C2037'], ['name' => 'Floor Carpet',                       'area_code' => '60A-60C', 'mh_sort_order' => 390,  'estimating_element_id' => $elem60CE->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '60E1060'], ['name' => 'Residential Equipment',              'area_code' => '60E',     'mh_sort_order' => 490,  'estimating_element_id' => $elem60CE->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '60E2012'], ['name' => 'Window Treatments',                  'area_code' => '60E',     'mh_sort_order' => 500,  'estimating_element_id' => $elem60CE->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '60E2013'], ['name' => 'Casework',                           'area_code' => '60E',     'mh_sort_order' => 510,  'estimating_element_id' => $elem60CE->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '60E2050'], ['name' => 'Moveable Furnishings',               'area_code' => '60E',     'mh_sort_order' => 520,  'estimating_element_id' => $elem60CE->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '60E2070'], ['name' => 'Auditorium Seating',                 'area_code' => '60E',     'mh_sort_order' => 530,  'estimating_element_id' => $elem60CE->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '30C1010'], ['name' => 'SP Internal Partitions',             'area_code' => '60A-60C', 'mh_sort_order' => null, 'estimating_element_id' => $elem60CE->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '30C1030'], ['name' => 'SP Internal Doors',                  'area_code' => '60A-60C', 'mh_sort_order' => null, 'estimating_element_id' => $elem60CE->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '30C1090'], ['name' => 'SP Interior Specialities',           'area_code' => '60A-60C', 'mh_sort_order' => null, 'estimating_element_id' => $elem60CE->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '30C1092'], ['name' => 'SP Int. Specialities (Plumbing)',    'area_code' => '60A-60C', 'mh_sort_order' => null, 'estimating_element_id' => $elem60CE->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '30C1097'], ['name' => 'SP Wardrobe & Storage',              'area_code' => '60A-60C', 'mh_sort_order' => null, 'estimating_element_id' => $elem60CE->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '30C2010'], ['name' => 'SP Wall/Ceiling Finishes',           'area_code' => '60A-60C', 'mh_sort_order' => null, 'estimating_element_id' => $elem60CE->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '30C2011'], ['name' => 'SP Wall Tiling',                     'area_code' => '60A-60C', 'mh_sort_order' => null, 'estimating_element_id' => $elem60CE->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '30C2032'], ['name' => 'SP Floor Tiling',                    'area_code' => '60A-60C', 'mh_sort_order' => null, 'estimating_element_id' => $elem60CE->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '30E1060'], ['name' => 'SP Residential Equipment',           'area_code' => '60E',     'mh_sort_order' => null, 'estimating_element_id' => $elem60CE->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '30E2012'], ['name' => 'SP Window Treatments',               'area_code' => '60E',     'mh_sort_order' => null, 'estimating_element_id' => $elem60CE->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '30E2013'], ['name' => 'SP Casework',                        'area_code' => '60E',     'mh_sort_order' => null, 'estimating_element_id' => $elem60CE->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '30E2050'], ['name' => 'SP Moveable Furnishings',            'area_code' => '60E',     'mh_sort_order' => null, 'estimating_element_id' => $elem60CE->id, 'category' => 'construction']);

        // Element 60D2 (Plumbing & Heating)
        PdCode::firstOrCreate(['code' => '60D1010'], ['name' => 'Elevators',   'area_code' => '60D', 'mh_sort_order' => 400,  'estimating_element_id' => $elem60D2->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '60D2000'], ['name' => 'Plumbing',    'area_code' => '60D', 'mh_sort_order' => 410,  'estimating_element_id' => $elem60D2->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '30D2000'], ['name' => 'SP Plumbing', 'area_code' => '60D', 'mh_sort_order' => null, 'estimating_element_id' => $elem60D2->id, 'category' => 'construction']);

        // Element 60D3 (HVAC)
        PdCode::firstOrCreate(['code' => '60D3000'], ['name' => 'HVAC',        'area_code' => '60D', 'mh_sort_order' => 420,  'estimating_element_id' => $elem60D3->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '60D3010'], ['name' => 'Fuel Systems', 'area_code' => '60D', 'mh_sort_order' => 430, 'estimating_element_id' => $elem60D3->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '30D3000'], ['name' => 'SP HVAC',     'area_code' => '60D', 'mh_sort_order' => null, 'estimating_element_id' => $elem60D3->id, 'category' => 'construction']);

        // Element 60D5 (Electrical & Lighting)
        PdCode::firstOrCreate(['code' => '60D4010'], ['name' => 'Fire Suppression', 'area_code' => '60D', 'mh_sort_order' => 440,  'estimating_element_id' => $elem60D5->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '60D5000'], ['name' => 'Electric',         'area_code' => '60D', 'mh_sort_order' => 450,  'estimating_element_id' => $elem60D5->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '60D5040'], ['name' => 'Lighting',         'area_code' => '60D', 'mh_sort_order' => 460,  'estimating_element_id' => $elem60D5->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '30D5000'], ['name' => 'SP Electric',      'area_code' => '60D', 'mh_sort_order' => null, 'estimating_element_id' => $elem60D5->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '30D5040'], ['name' => 'SP Lighting',      'area_code' => '60D', 'mh_sort_order' => null, 'estimating_element_id' => $elem60D5->id, 'category' => 'construction']);

        // Element 60D67 (Audio Video & Alarms)
        PdCode::firstOrCreate(['code' => '60D6030'], ['name' => 'Audio Visual', 'area_code' => '60D', 'mh_sort_order' => 470,  'estimating_element_id' => $elem60D67->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '60D7050'], ['name' => 'Security',     'area_code' => '60D', 'mh_sort_order' => 480,  'estimating_element_id' => $elem60D67->id, 'category' => 'construction']);
        PdCode::firstOrCreate(['code' => '30D7050'], ['name' => 'SP Security',  'area_code' => '60D', 'mh_sort_order' => null, 'estimating_element_id' => $elem60D67->id, 'category' => 'construction']);

        // Element 60F
        PdCode::firstOrCreate(['code' => '60F0100'], ['name' => 'Special Items', 'area_code' => '60F', 'mh_sort_order' => null, 'estimating_element_id' => $elem60F->id, 'category' => 'construction']);

        // Category: externals (Area 20)
        PdCode::firstOrCreate(['code' => '20G1010'], ['name' => 'Site Clearing',          'area_code' => '20', 'mh_sort_order' => 70,   'estimating_element_id' => null, 'category' => 'externals']);
        PdCode::firstOrCreate(['code' => '20G1020'], ['name' => 'Site Demolition',        'area_code' => '20', 'mh_sort_order' => 80,   'estimating_element_id' => null, 'category' => 'externals']);
        PdCode::firstOrCreate(['code' => '20G2020'], ['name' => 'Car Park',               'area_code' => '20', 'mh_sort_order' => 90,   'estimating_element_id' => null, 'category' => 'externals']);
        PdCode::firstOrCreate(['code' => '20G2030'], ['name' => 'Pedestrian Paving',      'area_code' => '20', 'mh_sort_order' => 100,  'estimating_element_id' => null, 'category' => 'externals']);
        PdCode::firstOrCreate(['code' => '20G2060'], ['name' => 'Site Development',       'area_code' => '20', 'mh_sort_order' => 120,  'estimating_element_id' => null, 'category' => 'externals']);
        PdCode::firstOrCreate(['code' => '20G2080'], ['name' => 'Landscaping',            'area_code' => '20', 'mh_sort_order' => 130,  'estimating_element_id' => null, 'category' => 'externals']);
        PdCode::firstOrCreate(['code' => '20G3010'], ['name' => 'Utility - Water',        'area_code' => '20', 'mh_sort_order' => 140,  'estimating_element_id' => null, 'category' => 'externals']);
        PdCode::firstOrCreate(['code' => '20G3020'], ['name' => 'Utility - Foul Drainage','area_code' => '20', 'mh_sort_order' => 150,  'estimating_element_id' => null, 'category' => 'externals']);
        PdCode::firstOrCreate(['code' => '20G3030'], ['name' => 'Utility - Storm Drainage','area_code' => '20','mh_sort_order' => 160,  'estimating_element_id' => null, 'category' => 'externals']);
        PdCode::firstOrCreate(['code' => '20G3060'], ['name' => 'Utility - Gas',          'area_code' => '20', 'mh_sort_order' => 170,  'estimating_element_id' => null, 'category' => 'externals', 'is_contractor' => true]);
        PdCode::firstOrCreate(['code' => '20G4010'], ['name' => 'Utility - Electricity',  'area_code' => '20', 'mh_sort_order' => 180,  'estimating_element_id' => null, 'category' => 'externals', 'is_contractor' => true]);
        PdCode::firstOrCreate(['code' => '20G4050'], ['name' => 'Site Lighting',          'area_code' => '20', 'mh_sort_order' => 200,  'estimating_element_id' => null, 'category' => 'externals']);
        PdCode::firstOrCreate(['code' => '20G5010'], ['name' => 'Utility - Telecoms',     'area_code' => '20', 'mh_sort_order' => 190,  'estimating_element_id' => null, 'category' => 'externals', 'is_contractor' => true]);

        // Category: overhead (Areas 10, 19 + special codes)
        PdCode::firstOrCreate(['code' => '10S1010'],  ['name' => 'Planning Approval',                'area_code' => '10', 'mh_sort_order' => 10,   'estimating_element_id' => null, 'category' => 'overhead']);
        PdCode::firstOrCreate(['code' => '10S1020'],  ['name' => 'Planning Permits',                 'area_code' => '10', 'mh_sort_order' => 20,   'estimating_element_id' => null, 'category' => 'overhead']);
        PdCode::firstOrCreate(['code' => '10S1120'],  ['name' => 'Building Permits',                 'area_code' => '10', 'mh_sort_order' => 30,   'estimating_element_id' => null, 'category' => 'overhead']);
        PdCode::firstOrCreate(['code' => '10S1150'],  ['name' => 'Surveying',                        'area_code' => '10', 'mh_sort_order' => 40,   'estimating_element_id' => null, 'category' => 'overhead']);
        PdCode::firstOrCreate(['code' => '10S1200'],  ['name' => 'General Project Support',          'area_code' => '10', 'mh_sort_order' => 50,   'estimating_element_id' => null, 'category' => 'overhead']);
        PdCode::firstOrCreate(['code' => '10S1240'],  ['name' => 'Travel/Delivery',                  'area_code' => '10', 'mh_sort_order' => null, 'estimating_element_id' => null, 'category' => 'overhead']);
        PdCode::firstOrCreate(['code' => '10S1330'],  ['name' => 'Site Costs',                       'area_code' => '10', 'mh_sort_order' => null, 'estimating_element_id' => null, 'category' => 'overhead']);
        PdCode::firstOrCreate(['code' => '10S1340'],  ['name' => 'Security',                         'area_code' => '10', 'mh_sort_order' => null, 'estimating_element_id' => null, 'category' => 'overhead']);
        PdCode::firstOrCreate(['code' => '10S1360'],  ['name' => 'Project Support - Office Equipment','area_code' => '10','mh_sort_order' => 55,   'estimating_element_id' => null, 'category' => 'overhead']);
        PdCode::firstOrCreate(['code' => '10S1410'],  ['name' => 'Project Support - Hand Tools',     'area_code' => '10', 'mh_sort_order' => 56,   'estimating_element_id' => null, 'category' => 'overhead']);
        PdCode::firstOrCreate(['code' => '10S1430'],  ['name' => 'Hardware/Sundries',                'area_code' => '10', 'mh_sort_order' => null, 'estimating_element_id' => null, 'category' => 'overhead']);
        PdCode::firstOrCreate(['code' => '10S1440'],  ['name' => 'Safety/PPE',                       'area_code' => '10', 'mh_sort_order' => null, 'estimating_element_id' => null, 'category' => 'overhead']);
        PdCode::firstOrCreate(['code' => '10S1450'],  ['name' => 'Plant Hire',                       'area_code' => '10', 'mh_sort_order' => null, 'estimating_element_id' => null, 'category' => 'overhead']);
        PdCode::firstOrCreate(['code' => '10S1460'],  ['name' => 'Project Support - Heavy Equipment','area_code' => '10', 'mh_sort_order' => 58,   'estimating_element_id' => null, 'category' => 'overhead']);
        PdCode::firstOrCreate(['code' => '19S3000'],  ['name' => 'Combined Overhead',                'area_code' => '19', 'mh_sort_order' => 60,   'estimating_element_id' => null, 'category' => 'overhead']);
        PdCode::firstOrCreate(['code' => 'OTH_EXP'],  ['name' => 'Other Expenses',  'area_code' => null, 'mh_sort_order' => null, 'estimating_element_id' => null, 'category' => 'overhead']);
        PdCode::firstOrCreate(['code' => 'VEH_FUEL'], ['name' => 'Vehicle Fuel',    'area_code' => null, 'mh_sort_order' => null, 'estimating_element_id' => null, 'category' => 'overhead']);
        PdCode::firstOrCreate(['code' => 'VEH_REP'],  ['name' => 'Vehicle Repair',  'area_code' => null, 'mh_sort_order' => null, 'estimating_element_id' => null, 'category' => 'overhead']);
        PdCode::firstOrCreate(['code' => 'TRAVEL'],   ['name' => 'Travel',          'area_code' => null, 'mh_sort_order' => null, 'estimating_element_id' => null, 'category' => 'overhead']);

        // Category: excluded (property purchase)
        PdCode::firstOrCreate(['code' => '01P1010'], ['name' => 'Property Purchase',     'area_code' => null, 'mh_sort_order' => null, 'estimating_element_id' => null, 'category' => 'excluded']);
        PdCode::firstOrCreate(['code' => '01P1020'], ['name' => 'Property Purchase',     'area_code' => null, 'mh_sort_order' => null, 'estimating_element_id' => null, 'category' => 'excluded']);
        PdCode::firstOrCreate(['code' => '01P2010'], ['name' => 'Property Purchase',     'area_code' => null, 'mh_sort_order' => null, 'estimating_element_id' => null, 'category' => 'excluded']);
        PdCode::firstOrCreate(['code' => '01P2030'], ['name' => 'Property Purchase Tax', 'area_code' => null, 'mh_sort_order' => null, 'estimating_element_id' => null, 'category' => 'excluded']);
    }
}
