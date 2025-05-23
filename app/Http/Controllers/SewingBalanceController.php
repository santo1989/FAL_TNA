<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\SewingBalance;
use App\Models\SewingPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SewingBalanceController extends Controller
{

    public function index()
    {
        $sewing_balance = SewingBalance::select(
            'job_no',
            'color',
            'size',
            DB::raw('SUM(sewing_balance) as total_sewing_balance'),
            DB::raw('SUM(production_min_balance) as total_production_min_balance')
        )
            ->groupBy('job_no', 'color', 'size')
            ->get();
        // dd($sewing_balance);
        //push buyer, style, po_no, item,
        return view('backend.OMS.sewing_balances.index', compact('sewing_balance'));
    }
    public function create_sewing_balances(Request $request, $job_no)
    {
        $plans = SewingPlan::where('job_no', $job_no)
            ->where('color_quantity', '>', 0)
            ->when($request->filled('production_plan'), function ($q) use ($request) {
                $q->where('production_plan', $request->production_plan);
            })
            ->get();

        $basic = Job::where('job_no', $job_no)->firstOrFail();

        $color_sizes_qties = $plans;

        $oldBalances = SewingBalance::where('job_no', $job_no)
            ->when($request->filled('production_plan'), function ($q) use ($request) {
                $q->where('production_plan', $request->production_plan);
            })->get();

        return view('backend.OMS.sewing_balances.create', [
            'basic_info' => $basic,
            'color_sizes_qties' => $color_sizes_qties,
            'old_sewing_balances' => $oldBalances,
            'jobs_no' => $job_no,
        ]);
    }

    public function getColorSizesQties(Request $request, $job_no)
    {
        $request->validate(['production_plan' => 'required|date_format:Y-m']);

        $plans = SewingPlan::where('job_no', $job_no)
            ->where('production_plan', $request->production_plan)
            ->where('color_quantity', '>', 0)
            ->get()
            ->map(function ($row) {
                return [
                    'id' => $row->id,
                    'color' => $row->color,
                    'size' => $row->size,
                    'color_quantity' => $row->color_quantity,
                ];
            });

        return response()->json([
            'color_sizes_qties' => $plans,
        ]);
    }

    public function store(Request $r, $job_no)
    {
        // dd($r->all());
        foreach ($r->input('sewing_quantity', []) as $i => $qty) {
            SewingBalance::create([
                'job_no' => $job_no,
                'sewing_date' => now(),
                'color' => $r->input('color')[$i],
                'size' => $r->input('size')[$i],
                'sewing_balance' => $qty,
                'production_plan' => $r->input('production_plan'),
                'production_min_balance' => $r->input('production_min_balance'),
                // 'created_by' => $r->created_by,
                // 'division_id' => $r->division_id,
                // 'division_name' => $r->division_name,
                // 'company_id' => $r->company_id,
                // 'company_name' => $r->company_name,
            ]);
        }

        return redirect()->route('jobs.index')
            ->with('message', 'Sewing balances updated successfully.');
    }
    // In SewingBalanceController.php

    public function get_buyer_po_styles(Request $request)
    {
        $buyerId = $request->buyer_id;
        $pos = Job::where('buyer_id', $buyerId)->pluck('po')->unique()->values();
        $styles = Job::where('buyer_id', $buyerId)->pluck('style')->unique()->values();

        return response()->json([
            'success' => true,
            'data' => [
                'pos' => $pos,
                'styles' => $styles,
            ]
        ]);
    }

    public function get_color_sizes_qties(Request $request)
    {
        $jobs = Job::where('buyer_id', $request->buyer_id)
            ->where('po', $request->po)
            ->where('style', $request->style_id)
            ->whereBetween('delivery_date', [$request->shipment_start_date, $request->shipment_end_date])
            ->get();

        $colorSizesQties = [];
        foreach ($jobs as $job) {
            $totalSewing = SewingPlan::where('job_no', $job->job_no)
                ->where('color', $job->color)
                ->where('size', $job->size)
                ->sum('color_quantity');

            $remaining = $job->color_quantity - $totalSewing;
            if ($remaining > 0) {
                $colorSizesQties[] = [
                    'id' => $job->id,
                    'job_no' => $job->job_no,
                    'color' => $job->color,
                    'size' => $job->size,
                    'color_quantity' => $job->color_quantity,
                    'total_sewing_quantity' => $totalSewing,
                    'remaining_quantity' => $remaining,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => $colorSizesQties
        ]);
    }



    //     public function store(Request $request)
    //     {
    //         // dd($request->all());
    //         // Validate the request data
    //         $request->validate([
    //             'job_no' => 'required|string|max:255',
    //             'production_plan' => 'required|date_format:Y-m',
    //             'production_min_balance' => 'required|numeric',
    //             'color_id' => 'required|array',
    //             'color_id.*' => 'required|integer',
    //             'color' => 'required|array',
    //             'color.*' => 'required|string',
    //             'size' => 'required|array',
    //             'size.*' => 'required|string',
    //             'sewing_quantity' => 'required|array',
    //             'sewing_quantity.*' => 'required|integer',
    //         ]);
    // // dd($request->all());
    //         // Iterate over the color and size arrays and save each combination
    //         foreach ($request->color_id as $key => $value) {
    //             dd($value);
    //             // Find the job
    //             $sewing_plan = SewingPlan::findOrFail($value);
    //             $job = Job::findOrFail($sewing_plan->job_id);

    //             if (!$job) {
    //                 return redirect()->back()->withError('Job not found.');
    //             }

    //             // dd($job);

    //             // Create a new sewing balance
    //             $sewing_balance = SewingBalance::create([
    //                 'job_id' => $job->id,
    //                 'sewing_plan_id' => $sewing_plan->id,
    //                 'job_no' => $request->job_no,
    //                 'color' => $request->color[$key],
    //                 'size' => $request->size[$key],
    //                 'sewing_balance' => $request->sewing_quantity[$key],
    //                 'production_plan' => $request->production_plan,
    //                 'production_min_balance' => $request->production_min_balance,
    //             ]);
    //         }

    //         // Redirect back with a success message
    //         return redirect()->route('jobs.index')->withMessage('Sewing balances saved successfully.');
    //     }


    // public function store(Request $request)
    // {
    //     // Validate the request data
    //     $request->validate([
    //         'job_no' => 'required|string|max:255',
    //         'production_plan' => 'required|date_format:Y-m',
    //         'production_min_balance' => 'required|numeric',
    //         'color_id' => 'required|array',
    //         'color_id.*' => 'required|integer',
    //         'color' => 'required|array',
    //         'color.*' => 'required|string',
    //         'size' => 'required|array',
    //         'size.*' => 'required|string',
    //         'sewing_quantity' => 'required|array',
    //         'sewing_quantity.*' => 'required|integer|min:0', // Ensure sewing quantity is non-negative
    //     ]);

    //     // Start a database transaction to ensure data consistency
    //     DB::beginTransaction();

    //     try {
    //         // Iterate over the color and size arrays and save each combination
    //         foreach ($request->color_id as $key => $colorId) {
    //             // Find the sewing plan
    //             $sewingPlan = SewingPlan::find($colorId);

    //             if (!$sewingPlan) {
    //                 throw new \Exception("Sewing plan not found for color ID: {$colorId}");
    //             }

    //             // Find the job associated with the sewing plan
    //             $job = Job::find($sewingPlan->job_id);

    //             if (!$job) {
    //                 throw new \Exception("Job not found for sewing plan ID: {$sewingPlan->id}");
    //             }

    //             // Check if the sewing quantity exceeds the remaining quantity
    //             $remainingQuantity = $sewingPlan->color_quantity - $sewingPlan->total_sewing_quantity;
    //             if ($request->sewing_quantity[$key] > $remainingQuantity) {
    //                 throw new \Exception("Sewing quantity exceeds remaining quantity for color: {$request->color[$key]} and size: {$request->size[$key]}");
    //             }

    //             // Create a new sewing balance record
    //             SewingBalance::create([
    //                 'job_id' => $job->id,
    //                 'sewing_plan_id' => $sewingPlan->id,
    //                 'job_no' => $request->job_no,
    //                 'color' => $request->color[$key],
    //                 'size' => $request->size[$key],
    //                 'sewing_balance' => $request->sewing_quantity[$key],
    //                 'production_plan' => $request->production_plan,
    //                 'production_min_balance' => $request->production_min_balance,
    //             ]);

    //             // Update the total sewing quantity in the sewing plan
    //             $sewingPlan->total_sewing_quantity += $request->sewing_quantity[$key];
    //             $sewingPlan->save();
    //         }

    //         // Commit the transaction
    //         DB::commit();

    //         // Redirect back with a success message
    //         return redirect()->route('jobs.index')->with('message', 'Sewing balances saved successfully.');
    //     } catch (\Exception $e) {
    //         // Rollback the transaction in case of an error
    //         DB::rollBack();

    //         // Redirect back with an error message
    //         return redirect()->back()->with('error', $e->getMessage());
    //     }
    // }

public function show(Request $request, $job_no)
    {
        $basic_info = Job::where('job_no', $job_no)->first();
        $jobs_no = $basic_info->job_no;
        $old_sewing_balances = SewingBalance::where('job_no', $job_no)->get();
        //push buyer, style, po_no, item, in $old_sewing_balances array
        foreach ($old_sewing_balances as $key => $value) {
            $old_sewing_balances[$key]['buyer'] = $basic_info->buyer;
            $old_sewing_balances[$key]['style'] = $basic_info->style;
            $old_sewing_balances[$key]['po'] = $basic_info->po;
            $old_sewing_balances[$key]['item'] = $basic_info->item;
        }



        return view('backend.OMS.sewing_balances.show', compact('basic_info', 'old_sewing_balances', 'jobs_no'));
    }


    public function edit(Request $request, $job_no)
    {
        // dd($job_no); 

        $old_sewing_balances = SewingBalance::where('id', $job_no)->get();
        $old_sewing_basic_info = SewingBalance::where('id', $job_no)->first();
        $color_sizes_qties = Job::where('job_no', $old_sewing_basic_info->job_no)->get();

        $basic_info = Job::where('job_no', $old_sewing_basic_info->job_no)->first();
        $jobs_no = $basic_info->job_no;

        // dd($color_sizes_qties, $basic_info, $old_sewing_balances, $jobs_no); 

        return view('backend.OMS.sewing_balances.edit', compact('color_sizes_qties', 'basic_info', 'old_sewing_balances', 'jobs_no', 'old_sewing_basic_info'));
    }

    
    public function update(Request $request, SewingBalance $SewingBalance)
    {
       dd($request->all());
    }

    
    public function destroy(SewingBalance $SewingBalance)
    {
        //
    }
}
