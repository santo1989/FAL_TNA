<?php

namespace App\Http\Controllers;

use App\Models\CapacityPlan;
use App\Models\Job;
use App\Models\SewingBalance;
use App\Models\SewingPlan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SewingPlanController extends Controller
{

    public function index()
    {
        $sewing_plan = SewingPlan::select(
            'job_no',
            'color',
            'size',
            'production_plan',
            DB::raw('SUM(color_quantity) as total_sewing_quantity')
        )
            ->groupBy('job_no', 'color', 'size', 'production_plan')
            ->orderBy('production_plan', 'desc')
            ->get();

        // dd($sewing_plan);

        return view('backend.OMS.sewing_plans.index', compact('sewing_plan'));
    }

    public function create()
    {
        // Use DB transaction to prevent null/empty values or deadlocks
        DB::beginTransaction();

        try {
            $buyers = Job::select('buyer','buyer_id')->distinct()->get();

            //if production plan is set, then fetch the sewing plans for that production plan
            if (request()->has('production_plan')) {
                $productionPlan = request()->production_plan;

                //convert the string to date
                $productionPlan = Carbon::parse($productionPlan)->format('Y-m');

                //production_plan column convert the string to date format in the database and check if it exists

                $db_production_plan = CapacityPlan::where('production_plan', $productionPlan)->first();

            } else {
               //set no production_plan if production plan is not set
                $db_production_plan = null;
            }
            
            //buyer_id, style, po, shipment_start_date, shipment_end_date wise check and fetch the data to add $color_sizes_qties if not exist in sewing plan
            $buyer_id_filter = request()->buyer_id ?? null;
            $style_filter = request()->style ?? null;
            $po_filter = request()->po ?? null;
            $shipment_start_date = request()->shipment_start_date ?? null;
            $shipment_end_date = request()->shipment_end_date ?? null;

            //fitler the data from jobs table
            $color_sizes_qties = Job::select('id', 'job_no', 'color', 'size', 'color_quantity')
                ->whereNull('buyer_hold_shipment')
                ->whereNull('buyer_cancel_shipment')
                ->whereNull('order_close')
                ->when($buyer_id_filter, function ($query) use ($buyer_id_filter) {
                    return $query->where('buyer_id', $buyer_id_filter);
                })
                ->when($style_filter, function ($query) use ($style_filter) {
                    return $query->where('style', $style_filter);
                })
                ->when($po_filter, function ($query) use ($po_filter) {
                    return $query->where('po', $po_filter);
                })
                ->when($shipment_start_date, function ($query) use ($shipment_start_date) {
                    return $query->where('shipment_start_date', $shipment_start_date);
                })
                ->when($shipment_end_date, function ($query) use ($shipment_end_date) {
                    return $query->where('shipment_end_date', $shipment_end_date);
                })
                ->get();





            // // Fetch color, size, and quantity details from the jobs table
            // $color_sizes_qties = Job::select('id', 'job_no', 'color', 'size', 'color_quantity')
            // ->whereNull('buyer_hold_shipment')
            // ->whereNull('buyer_cancel_shipment')
            // ->whereNull('order_close')
            // ->get();

            // Fetch sewing balance entries
            $old_sewing_balances = SewingBalance::select('job_no', 'color', 'size', 'sewing_balance')->get();

            // Fetch sewing plan entries
            $old_sewing_plans = SewingPlan::select('job_no', 'color', 'size', 'color_quantity')->get();

            // Calculate remaining quantity and total sewing quantity for each job entry
            foreach ($color_sizes_qties as $color_size) {
                // Calculate already completed quantity from SewingBalance
                $completed_qty = $old_sewing_balances
                    ->where('job_no', $color_size->job_no)
                    ->where('color', $color_size->color)
                    ->where('size', $color_size->size)
                    ->sum('sewing_balance');

                // Calculate planned quantity from SewingPlan
                $planned_qty = $old_sewing_plans
                    ->where('job_no', $color_size->job_no)
                    ->where('color', $color_size->color)
                    ->where('size', $color_size->size)
                    ->sum('color_quantity');

                // add buyer, style, po to the color_size object from the jobs table
                $job_information = Job::select('buyer', 'style', 'po')
                    ->where('job_no', $color_size->job_no)
                    ->first();
                $color_size->buyer = $job_information->buyer ?? 'N/A';
                $color_size->style = $job_information->style ?? 'N/A';
                $color_size->po = $job_information->po ?? 'N/A';

                // Calculate remaining quantity and total sewing quantity
                $color_size->remaining_quantity = $color_size->color_quantity - $completed_qty - $planned_qty;
                $color_size->total_sewing_quantity = $completed_qty + $planned_qty;

                // Prevent negative remaining quantities
                if ($color_size->remaining_quantity < 0) {
                    $color_size->remaining_quantity = 0;
                }
            }

            // Filter out entries with no remaining quantity
            $color_sizes_qties = $color_sizes_qties->filter(function ($color_size) {
                return isset($color_size->remaining_quantity) && $color_size->remaining_quantity > 0;
            });

            // Commit the transaction
            DB::commit();

            // Return the view with the calculated data
            return view('backend.OMS.sewing_plans.create', compact('color_sizes_qties', 'buyers', 'db_production_plan'));
        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }




    public function sewing_plans_store(Request $request)
    {
        // dd($request->all());
        // Validate the request data
        $request->validate([
            'job_no' => 'required|array',
            'job_no.*' => 'required|string',
            'production_plan' => 'required|date_format:Y-m',
            'color_id' => 'required|array',
            'color_id.*' => 'required|integer',
            'color' => 'required|array',
            'color.*' => 'required|string',
            'size' => 'required|array',
            'size.*' => 'required|string',
            'color_quantity' => 'required|array', // Validate the array itself
            'color_quantity.*' => 'nullable|integer|min:0', // Allow null but enforce integer >= 0
        ]);
        

        // Check if at least one color_quantity is greater than 0
        $hasValidQuantity = collect($request->color_quantity)
            ->filter(fn($qty) => $qty !== null) // Exclude null values
            ->some(fn($qty) => $qty > 0); // Ensure at least one value > 0

        if (!$hasValidQuantity) {
            return redirect()->back()->withErrors([
                'color_quantity' => 'At least one color quantity must be greater than 0.'
            ])->withInput();
        }

        // Iterate over the color and size arrays and save each combination
        foreach ($request->color_id as $key => $value) {
            // Skip if the color_quantity is 0 or null
            if (empty($request->color_quantity[$key]) || $request->color_quantity[$key] == 0) {
                continue;
            }

            // Find the job
            $job = Job::findOrFail($value);

            // Create a new sewing plan
            SewingPlan::create([
                'job_id' => $job->id,
                'job_no' => $request->job_no[$key],
                'color' => $request->color[$key],
                'size' => $request->size[$key],
                'color_quantity' => $request->color_quantity[$key],
                'production_plan' => $request->production_plan,
            ]);
        }

        // Redirect back with a success message
        return redirect()->route('sewing_plans.index')->withMessage('Sewing balances saved successfully.');
    }


    public function show(Request $request, $job_no)
    {
        // dd($job_no);
        $basic_info = Job::where('job_no', $job_no)->first();
        $jobs_no = $basic_info->job_no;
        $old_sewing_balances = SewingPlan::where('job_no', $job_no)->get();
        //push buyer, style, po_no, item, in $old_sewing_balances array
        foreach ($old_sewing_balances as $key => $value) {
            $old_sewing_balances[$key]['buyer'] = $basic_info->buyer;
            $old_sewing_balances[$key]['style'] = $basic_info->style;
            $old_sewing_balances[$key]['po'] = $basic_info->po;
            $old_sewing_balances[$key]['item'] = $basic_info->item;
        }

// dd($basic_info, $old_sewing_balances, $jobs_no);

        return view('backend.OMS.sewing_plans.show', compact('basic_info', 'old_sewing_balances', 'jobs_no'));
    }

    public function edit(Request $request, $job_no)
    {
        // dd($job_no);

        $old_sewing_basic_info = SewingPlan::where('id', $job_no)->first();

        $job_no = $old_sewing_basic_info->job_no;

        // dd($old_sewing_basic_info);

        if (!$old_sewing_basic_info) {
            return redirect()->route('sewing_plans.index')->withErrors(['message' => 'No sewing plan found for this job number.']);
        }

        // Fetch color, size, and quantity details for the selected job
        $color_sizes_qties = Job::where('job_no', $job_no)->get(); // Fetch the color, size, and quantity details from the jobs table

        // Fetch old sewing balance entries
        $old_sewing_balances = SewingBalance::select('job_no', 'color', 'size', 'sewing_balance')->where('job_no', $job_no)->get();

        // Fetch sewing plan entries
        $old_sewing_plans = SewingPlan::select('job_no', 'color', 'size', 'color_quantity')->where('job_no', $job_no)->get();

        // dd($old_sewing_balances, $old_sewing_plans);

        // Merge sewing plans into old sewing balances if not already present
        foreach ($old_sewing_plans as $sewing_plan) {
            $matching_balance = $old_sewing_balances->firstWhere(function ($balance) use ($sewing_plan) {
                return $balance->job_no === $sewing_plan->job_no &&
                    $balance->color === $sewing_plan->color &&
                    $balance->size === $sewing_plan->size;
            });

            if (!$matching_balance) {
                // Add the sewing plan as a new balance
                $old_sewing_balances->push((object)[
                    'job_no' => $sewing_plan->job_no,
                    'color' => $sewing_plan->color,
                    'size' => $sewing_plan->size,
                    'sewing_balance' => $sewing_plan->color_quantity,
                ]);
            }
        }

        // dd($old_sewing_balances);

        // Loop through each job entry and calculate the remaining and total sewing quantities
        foreach ($color_sizes_qties as $color_size) {
            $completed_qty = $old_sewing_balances
                ->where('job_no', $color_size->job_no)
                ->where('color', $color_size->color)
                ->where('size', $color_size->size)
                ->sum('sewing_balance');

            // Calculate remaining quantity and total sewing quantity
            $color_size->remaining_quantity = $color_size->color_quantity - $completed_qty;
            $color_size->total_sewing_quantity = $completed_qty;
        }

        // dd($color_sizes_qties);

        // Filter out jobs with no remaining quantity
        $color_sizes_qties = $color_sizes_qties->filter(function ($color_size) {
            return isset($color_size->remaining_quantity) && $color_size->remaining_quantity > 0;
        });

        // dd($color_sizes_qties);

        // Get basic job information
        $basic_info = Job::where('job_no', $job_no)->first();
        $jobs_no = $basic_info->job_no;
        $production_plan = $old_sewing_basic_info->production_plan;
        $capacity_plan = CapacityPlan::where('production_plan', $production_plan)->first();

        // Pass data to the edit view
        return view('backend.OMS.sewing_plans.edit', compact(
            'color_sizes_qties',
            'basic_info',
            'old_sewing_balances',
            'jobs_no',
            'old_sewing_basic_info',
            'capacity_plan'
        ));
    }

    public function update(Request $request, $job_no)
    {
        // Validate the request data
        $request->validate([
            'job_no' => 'required|array',
            'job_no.*' => 'required|string',
            'production_plan' => 'required|date_format:Y-m',
            'color_id' => 'required|array',
            'color_id.*' => 'required|integer',
            'color' => 'required|array',
            'color.*' => 'required|string',
            'size' => 'required|array',
            'size.*' => 'required|string',
            'color_quantity' => 'required|array',
            'color_quantity.*' => 'nullable|integer|min:0',
        ]);

        // Check if at least one color_quantity is greater than 0
        $hasValidQuantity = collect($request->color_quantity)
            ->filter(fn($qty) => $qty !== null)
            ->some(fn($qty) => $qty > 0);

        if (!$hasValidQuantity) {
            return redirect()->back()->withErrors([
                'color_quantity' => 'At least one color quantity must be greater than 0.'
            ])->withInput();
        }

        // Fetch the existing sewing plans for the job number
        $existing_plans = SewingPlan::where('job_no', $job_no)->get()->keyBy(function ($plan) {
            return $plan->color . '-' . $plan->size;
        });

        // Iterate through the submitted data and update or create sewing plans
        foreach ($request->color_id as $key => $value) {
            // Skip if the color_quantity is 0 or null
            if (empty($request->color_quantity[$key]) || $request->color_quantity[$key] == 0) {
                continue;
            }

            $color = $request->color[$key];
            $size = $request->size[$key];
            $unique_key = $color . '-' . $size;

            // Check if the sewing plan already exists
            if ($existing_plans->has($unique_key)) {
                // Update the existing sewing plan
                $existing_plans[$unique_key]->update([
                    'color_quantity' => $request->color_quantity[$key],
                    'production_plan' => $request->production_plan,
                ]);
            } else {
                // Find the job
                $job = Job::findOrFail($value);

                // Create a new sewing plan
                SewingPlan::create([
                    'job_id' => $job->id,
                    'job_no' => $request->job_no[$key],
                    'color' => $request->color[$key],
                    'size' => $request->size[$key],
                    'color_quantity' => $request->color_quantity[$key],
                    'production_plan' => $request->production_plan,
                ]);
            }
        }

        // Redirect back with a success message
        return redirect()->route('sewing_plans.index')->withMessage('Sewing plan updated successfully.');
    }

    // Controller
    public function sewing_plans_destroy(Request $request, $job_no)
    {
        // dd($request->all(), $job_no);

        // Validate the request data
        $request->validate([
            'job_no' => 'required|string',
            'color' => 'required|string',
            'size' => 'required|string',
            'production_plan' => 'required|date_format:Y-m',
        ]);

        // Find the sewing plan entry to delete
        $sewing_plan = SewingPlan::where('job_no', $job_no)
            ->where('color', $request->color)
            ->where('size', $request->size)
            ->where('production_plan', $request->production_plan)
            ->get();

            // dd($sewing_plan);

        // Delete all the sewing plan entry which are not already in sewing balance
        foreach ($sewing_plan as $plan) {
            // Check if the sewing plan entry exists in the sewing balance
            $exists_in_balance = SewingBalance::where('job_no', $plan->job_no)
                ->where('color', $plan->color)
                ->where('size', $plan->size)
                ->exists();

            // If it doesn't exist in the sewing balance, delete it
            if (!$exists_in_balance) {
                $plan->delete();
            }
        }

    
        return redirect()->route('sewing_plans.index')->with('message', 'Sewing plan deleted successfully.');
    }

    public function sewing_plans_destroy_single($id)
    {
        // Find the sewing plan by ID
        $sewing_plan = SewingPlan::findOrFail($id);

        // Delete the sewing plan
        $sewing_plan->delete();

        // Redirect back with a success message
        return redirect()->route('sewing_plans.index')->withMessage('Sewing plan deleted successfully.');
    }
}
