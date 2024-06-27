<?php

namespace Database\Seeders;

use App\Models\SOP;
use Illuminate\Database\Seeder;

class SOPSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SOP::create([
        'Perticulars' => 'Order Receive Date',
        'day' => '0', 
        'status' => '0',
        ]);

        SOP::create([
        'Perticulars' => 'Lab Dip Submission',
        'day' => '5',
        'status' => '0',
        ]);

        SOP::create([
        'Perticulars' => 'Fabric Booking',
        'day' => '5', 
        'status' => '0',
        ]);
        
        SOP::create([
        'Perticulars' => 'Fit Sample Submission',
        'day' => '7',
        'status' => '0',
        ]);
        
        SOP::create([
        'Perticulars' => 'Print Strike Off Submission',
        'day' => '7',
        'status' => '0',
        ]);
        
        SOP::create([
        'Perticulars' => 'Bulk Accessories Booking',
        'day' => '15',
        'status' => '0',
        ]);
        
        SOP::create([
        'Perticulars' => 'Fit Comments',
        'day' => '15',
        'status' => '0',
        ]);
        
        SOP::create([
        'Perticulars' => 'Bulk Yarn Inhouse',
        'day' => '35',
        'status' => '0',
        ]);
        
        SOP::create([
        'Perticulars' => 'Bulk Accessories Inhouse',
        'day' => '35',
        'status' => '0',
        ]);
        
        SOP::create([
        'Perticulars' => 'PP Sample Submission',
        'day' => '35',
        'status' => '0',
        ]);
        
        SOP::create([
        'Perticulars' => 'Bulk Fabric Knitting',
        'day' => '43',
        'status' => '0',
        ]);
        
        SOP::create([
        'Perticulars' => 'PP Comments Receive',
        'day' => '45',
        'status' => '0',
        ]);
        
        SOP::create([
        'Perticulars' => 'Bulk Fabric Dyeing',
        'day' => '63',
        'status' => '0',
        ]);
        
        SOP::create([
        'Perticulars' => 'Bulk Fabric Delivery',
        'day' => '64',
        'status' => '0',
        ]);

        SOP::create([
        'Perticulars' => 'PP Meeting',
        'day' => '65',
        'status' => '0',
        ]);
    }
}

