<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\SewingBlance;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShipmentController extends Controller
{
    public function index()
    {
        $shipments = Shipment::all();
        return view('backend.OMS.shipments.index', compact('shipments'));
    }

    public function create(Request $request, $job_no)
    {
        $color_sizes_qties = SewingBlance::where('job_no', $job_no)
        ->select(
            'job_id',
            'job_no',
            'color',
            'size',
            DB::raw('SUM(sewing_balance) as total_sewing_balance')
        )
            ->groupBy('job_id', 'job_no', 'color', 'size')
            ->get();


        $basic_info = Job::where('job_no', $job_no)->first();
        $jobs_no = $basic_info->job_no;
        $old_shipments_entries = Shipment::where('job_no', $job_no)->get();
        // dd($color_sizes_qties, $basic_info, $old_shipments_entries, $jobs_no);

        // Return a response or redirect as needed

        return view('backend.OMS.shipments.create', compact('color_sizes_qties', 'basic_info', 'old_shipments_entries', 'jobs_no')); 
    }



    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'job_no' => 'required|string|max:255',
            'ex_factory_date' => 'required',
            'total_shipped_qty' => 'required',
            'shipped_value' => 'required',
            'job_id' => 'required|array',
            'job_id.*' => 'required|integer',
            'color' => 'required|array',
            'color.*' => 'required|string',
            'size' => 'required|array',
            'size.*' => 'required|string',
            'shipped_qty' => 'required|array',
            'shipped_qty.*' => 'required|integer',
            'unit_price' => 'required|numeric',
        ]);

        foreach ($request->job_id as $key => $value) {
            // Find the job
            $job = Job::findOrFail($value);

            // Calculate the job's total color quantity
            $job_ways_color_quantity = Job::where('job_no', $request->job_no)
                ->where('color', $request->color[$key])
                ->where('size', $request->size[$key])
                ->sum('color_quantity');

            // Calculate the total shipped quantity for this job, color, and size
            $shipped_qty = Shipment::where('job_no', $request->job_no)
                ->where('color', $request->color[$key])
                ->where('size', $request->size[$key])
                ->sum('shipped_qty');

            $new_shipped_qty = $request->shipped_qty[$key];
            $total_shipped_qty = $shipped_qty + $new_shipped_qty;

            if ($total_shipped_qty <= $job_ways_color_quantity) {
                // Update total_shipped_qty and total_shipped_value
                $total_shipped_value = $total_shipped_qty * $request->unit_price;
                $excess_short_shipment_qty = null;
                $excess_short_shipment_value = null;
            } else {
                // Calculate excess shipment
                $excess_short_shipment_qty = $total_shipped_qty - $job_ways_color_quantity;
                $excess_short_shipment_value = $excess_short_shipment_qty * $request->unit_price;
                $total_shipped_qty = null;
                $total_shipped_value = null;
            }

            // Create a new Shipment record
            Shipment::create([
                'job_id' => $value,
                'job_no' => $request->job_no,
                'color' => $request->color[$key],
                'size' => $request->size[$key],
                'shipped_qty' => $new_shipped_qty,
                'total_shipped_qty' => $total_shipped_qty,
                'total_shipped_value' => $total_shipped_value,
                'excess_short_shipment_qty' => $excess_short_shipment_qty,
                'excess_short_shipment_value' => $excess_short_shipment_value,
                'shipped_value' => round($new_shipped_qty * $request->unit_price, 2),
                'ex_factory_date' => $request->ex_factory_date,
            ]);
        }

        // Redirect back with a success message
        return redirect()->route('jobs.index')->with('message', 'Sewing balances saved successfully.');
    }



   
}
