<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\SewingBlance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SewingBlanceController extends Controller
{
    
    public function index()
    {
        $sewing_balance = SewingBlance::select(
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
    public function create_sewing_balances(Request  $request, $job_no)
    {
        

        $color_sizes_qties = Job::where('job_no', $job_no)->get();

        $basic_info = Job::where('job_no', $job_no)->first();
         $jobs_no = $basic_info->job_no;
        $old_sewing_balances = SewingBlance::where('job_no', $job_no)->get();
// dd($color_sizes_qties, $basic_info, $old_sewing_balances, $jobs_no);
     
    // Return a response or redirect as needed
        
        return view('backend.OMS.sewing_balances.create', compact('color_sizes_qties', 'basic_info', 'old_sewing_balances', 'jobs_no'));
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
            $sewing_balance = SewingBlance::create([
                'job_id' => $job->id,
                'job_no' => $request->job_no,
                'color' => $request->color[$key],
                'size' => $request->size[$key],
                'sewing_balance' => $request->sewing_quantity[$key],
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
        $old_sewing_balances = SewingBlance::where('job_no', $job_no)->get();
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
        
        $old_sewing_balances = SewingBlance::where('id', $job_no)->get();
        $old_sewing_basic_info = SewingBlance::where('id', $job_no)->first();
        $color_sizes_qties = Job::where('job_no', $old_sewing_basic_info->job_no)->get();

        $basic_info = Job::where('job_no', $old_sewing_basic_info->job_no)->first();
        $jobs_no = $basic_info->job_no;
       
        // dd($color_sizes_qties, $basic_info, $old_sewing_balances, $jobs_no); 

        return view('backend.OMS.sewing_balances.edit', compact('color_sizes_qties', 'basic_info', 'old_sewing_balances', 'jobs_no', 'old_sewing_basic_info'));	
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SewingBlance  $sewingBlance
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SewingBlance $sewingBlance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SewingBlance  $sewingBlance
     * @return \Illuminate\Http\Response
     */
    public function destroy(SewingBlance $sewingBlance)
    {
        //
    }
}
