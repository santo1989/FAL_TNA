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
        //Lead Time 90 Days

        SOP::create([
        'lead_time' => '90',
        'Perticulars' => 'Order Receive Date',
        'day' => '0', 
        'status' => '0',
        ]);

        SOP::create([
        'lead_time' => '90',  
        'Perticulars' => 'Lab Dip Submission',
        'day' => '5',
        'status' => '0',
        ]);

        SOP::create([
        'lead_time' => '90',
        'Perticulars' => 'Fabric Booking',
        'day' => '5', 
        'status' => '0',
        ]);
        
        SOP::create([
        'lead_time' => '90',
        'Perticulars' => 'Fit Sample Submission',
        'day' => '7',
        'status' => '0',
        ]);
        
        SOP::create([
        'lead_time' => '90',
        'Perticulars' => 'Print Strike Off Submission',
        'day' => '7',
        'status' => '0',
        ]);
        
        SOP::create([
        'lead_time' => '90',
        'Perticulars' => 'Bulk Accessories Booking',
        'day' => '10',
        'status' => '0',
        ]);
        
        SOP::create([
        'lead_time' => '90',
        'Perticulars' => 'Fit Comments',
        'day' => '15',
        'status' => '0',
        ]);
        
        SOP::create([
        'lead_time' => '90',
        'Perticulars' => 'Bulk Yarn Inhouse',
        'day' => '35',
        'status' => '0',
        ]);
        
        SOP::create([
        'lead_time' => '90',
        'Perticulars' => 'Bulk Accessories Inhouse',
        'day' => '50',
        'status' => '0',
        ]);
        
        SOP::create([
        'lead_time' => '90',
        'Perticulars' => 'PP Sample Submission',
        'day' => '35',
        'status' => '0',
        ]);
        
        SOP::create([
        'lead_time' => '90',
        'Perticulars' => 'Bulk Fabric Knitting',
        'day' => '45',
        'status' => '0',
        ]);
        
        SOP::create([
        'lead_time' => '90',
        'Perticulars' => 'PP Comments Receive',
        'day' => '45',
        'status' => '0',
        ]);
        
        SOP::create([
        'lead_time' => '90',
        'Perticulars' => 'Bulk Fabric Dyeing',
        'day' => '55',
        'status' => '0',
        ]);
        
        SOP::create([
        'lead_time' => '90',
        'Perticulars' => 'Bulk Fabric Delivery',
        'day' => '60',
        'status' => '0',
        ]);

        SOP::create([
        'lead_time' => '90',
        'Perticulars' => 'PP Meeting',
        'day' => '65',
        'status' => '0',
        ]);

    // Lead Time 75 Days

        SOP::create([
        'lead_time' => '75',
        'Perticulars' => 'Order Receive Date',
        'day' => '0', 
        'status' => '0',
        ]);

        SOP::create([
        'lead_time' => '75',  
        'Perticulars' => 'Lab Dip Submission',
        'day' => '5',
        'status' => '0',
        ]);

        SOP::create([
        'lead_time' => '75',
        'Perticulars' => 'Fabric Booking',
        'day' => '3', 
        'status' => '0',
        ]);
        
        SOP::create([
        'lead_time' => '75',
        'Perticulars' => 'Fit Sample Submission',
        'day' => '7',
        'status' => '0',
        ]);
        
        SOP::create([
        'lead_time' => '75',
        'Perticulars' => 'Print Strike Off Submission',
        'day' => '7',
        'status' => '0',
        ]);
        
        SOP::create([
        'lead_time' => '75',
        'Perticulars' => 'Bulk Accessories Booking',
        'day' => '10',
        'status' => '0',
        ]);
        
        SOP::create([
        'lead_time' => '75',
        'Perticulars' => 'Fit Comments',
        'day' => '15',
        'status' => '0',
        ]);
        
        SOP::create([
        'lead_time' => '75',
        'Perticulars' => 'Bulk Yarn Inhouse',
        'day' => '32',
        'status' => '0',
        ]);
        
        SOP::create([
        'lead_time' => '75',
        'Perticulars' => 'Bulk Accessories Inhouse',
        'day' => '40',
        'status' => '0',
        ]);
        
        SOP::create([
        'lead_time' => '75',
        'Perticulars' => 'PP Sample Submission',
        'day' => '35',
        'status' => '0',
        ]);
        
        SOP::create([
        'lead_time' => '75',
        'Perticulars' => 'Bulk Fabric Knitting',
        'day' => '40',
        'status' => '0',
        ]);

        SOP::create([
        'lead_time' => '75',
        'Perticulars' => 'PP Comments Receive',
        'day' => '45',
        'status' => '0',
        ]);

        SOP::create([
        'lead_time' => '75',
        'Perticulars' => 'Bulk Fabric Dyeing',
        'day' => '48',
        'status' => '0',
        ]);

        SOP::create([
        'lead_time' => '75',
        'Perticulars' => 'Bulk Fabric Delivery',
        'day' => '50',
        'status' => '0',
        ]);

        SOP::create([
        'lead_time' => '75',
        'Perticulars' => 'PP Meeting',
        'day' => '55',
        'status' => '0',
        ]);

    // Lead Time 60 Days
    
            SOP::create([
            'lead_time' => '60',
            'Perticulars' => 'Order Receive Date',
            'day' => '0', 
            'status' => '0',
            ]);
    
            SOP::create([
            'lead_time' => '60',  
            'Perticulars' => 'Lab Dip Submission',
            'day' => '5',
            'status' => '0',
            ]);
    
            SOP::create([
            'lead_time' => '60',
            'Perticulars' => 'Fabric Booking',
            'day' => '3', 
            'status' => '0',
            ]);
            
            SOP::create([
            'lead_time' => '60',
            'Perticulars' => 'Fit Sample Submission',
            'day' => '7',
            'status' => '0',
            ]);
            
            SOP::create([
            'lead_time' => '60',
            'Perticulars' => 'Print Strike Off Submission',
            'day' => '5',
            'status' => '0',
            ]);
            
            SOP::create([
            'lead_time' => '60',
            'Perticulars' => 'Bulk Accessories Booking',
            'day' => '7',
            'status' => '0',
            ]);
            
            SOP::create([
            'lead_time' => '60',
            'Perticulars' => 'Fit Comments',
            'day' => '15',
            'status' => '0',
            ]);
            
            SOP::create([
            'lead_time' => '60',
            'Perticulars' => 'Bulk Yarn Inhouse',
            'day' => '24',
            'status' => '0',
            ]);
            
            SOP::create([
            'lead_time' => '60',
            'Perticulars' => 'Bulk Accessories Inhouse',
            'day' => '35',
            'status' => '0',
            ]);
            
            SOP::create([
            'lead_time' => '60',
            'Perticulars' => 'PP Sample Submission',
            'day' => '30',
            'status' => '0',
            ]);
            
            SOP::create([
            'lead_time' => '60',
            'Perticulars' => 'Bulk Fabric Knitting',
            'day' => '31',
            'status' => '0',
            ]);

            SOP::create([
            'lead_time' => '60',
            'Perticulars' => 'PP Comments Receive',
            'day' => '40',
            'status' => '0',
            ]);

            SOP::create([
            'lead_time' => '60',
            'Perticulars' => 'Bulk Fabric Dyeing',
            'day' => '38',
            'status' => '0',
            ]);

            SOP::create([
            'lead_time' => '60',
            'Perticulars' => 'Bulk Fabric Delivery',
            'day' => '40',
            'status' => '0',
            ]);

            SOP::create([
            'lead_time' => '60',
            'Perticulars' => 'PP Meeting',
            'day' => '45',
            'status' => '0',
            ]);


    }
}

