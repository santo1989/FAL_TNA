<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\SewingBlance;
use App\Models\SewingPlan;
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
            DB::raw('SUM(color_quantity) as total_color_quantity')
        )
            ->groupBy('job_no', 'color', 'size', 'production_plan')
            ->get();
        // dd($sewing_plan);
        //push buyer, style, po_no, item,
        return view('backend.OMS.sewing_plans.index', compact('sewing_plan'));
    }
    public function create()
    {
        // Fetch the color, size, and quantity details from the jobs table
        $color_sizes_qties = Job::select('id', 'job_no', 'color', 'size', 'color_quantity')->get();

        // Fetch old sewing plan entries based on job number (to get existing sewing balances)
        $old_sewing_balances = SewingBlance::select('job_no', 'color', 'size', 'sewing_balance')->get();

        // Loop through each job entry and calculate the remaining quantity
        foreach ($color_sizes_qties as $color_size) {
            $completed_qty = $old_sewing_balances
                ->where('job_no', $color_size->job_no)
                ->where('color', $color_size->color)
                ->where('size', $color_size->size)
                ->sum('sewing_balance');

            // Calculate remaining quantity
            $color_size->remaining_quantity = $color_size->color_quantity - $completed_qty;
            // calculate total sewing quantity
            $color_size->total_sewing_quantity = $completed_qty;
        }

        // if  $color_size->remaining_quantity =< 0, remove it from the collection
        $color_sizes_qties = $color_sizes_qties->filter(function ($color_size) {
            return $color_size->remaining_quantity > 0;
        });

        // Return a response or redirect as needed
        return view('backend.OMS.sewing_plans.create', compact('color_sizes_qties'));

        // // Assuming job_no is passed through the request or you define it here
        // $job_no = request('job_no');

        // // Fetch basic info of the job to display in the form
        // $basic_info = Job::where('job_no', $job_no)->first();
        // $jobs_no = $basic_info->job_no;

        // // Fetch sewing balances of the selected job
        // $old_sewing_balances = SewingPlan::where('job_no', $job_no)->get();

        // // Return the view with the necessary data
        // return view('backend.OMS.sewing_plans.create', compact('color_sizes_qties', 'basic_info', 'old_sewing_balances', 'jobs_no'));
    }



    public function store(Request $request)
    {
        // dd($request->all());
        // Validate the request data
        $request->validate([
            'job_no' => 'required|string|max:255',
            'production_plan' => 'required|date_format:Y-m',
            'production_min_balance' => 'required|numeric',
            'color_id' => 'required|array',
            'color_id.*' => 'required|integer',
            'color' => 'required|array',
            'color.*' => 'required|string',
            'size' => 'required|array',
            'size.*' => 'required|string',
            'sewing_quantity' => 'required|array',
            'sewing_quantity.*' => 'required|integer',
        ]);

        // Iterate over the color and size arrays and save each combination
        foreach ($request->color_id as $key => $value) {
            // Find the job
            $job = Job::findOrFail($value);

            // Create a new sewing balance
            $sewing_plan = SewingPlan::create([
                'job_id' => $job->id,
                'job_no' => $request->job_no,
                'color' => $request->color[$key],
                'size' => $request->size[$key],
                'sewing_plan' => $request->sewing_quantity[$key],
                'production_plan' => $request->production_plan,
                'production_min_balance' => $request->production_min_balance,
            ]);
        }

        // Redirect back with a success message
        return redirect()->route('jobs.index')->withMessage('Sewing balances saved successfully.');
    }

    public function show(Request $request, $job_no)
    {
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



        return view('backend.OMS.sewing_plans.show', compact('basic_info', 'old_sewing_balances', 'jobs_no'));
    }


    public function edit(Request $request, $job_no)
    {
        // dd($job_no); 

        $old_sewing_balances = SewingPlan::where('id', $job_no)->get();
        $old_sewing_basic_info = SewingPlan::where('id', $job_no)->first();
        $color_sizes_qties = Job::where('job_no', $old_sewing_basic_info->job_no)->get();

        $basic_info = Job::where('job_no', $old_sewing_basic_info->job_no)->first();
        $jobs_no = $basic_info->job_no;

        // dd($color_sizes_qties, $basic_info, $old_sewing_balances, $jobs_no); 

        return view('backend.OMS.sewing_plans.edit', compact('color_sizes_qties', 'basic_info', 'old_sewing_balances', 'jobs_no', 'old_sewing_basic_info'));
    }

    public function update(Request $request, SewingPlan $sewingPlan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SewingPlan  $sewingPlan
     * @return \Illuminate\Http\Response
     */
    public function destroy(SewingPlan $sewingPlan)
    {
        //
    }
}
