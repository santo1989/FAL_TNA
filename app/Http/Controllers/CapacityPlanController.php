<?php

namespace App\Http\Controllers;

use App\Models\CapacityPlan;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CapacityPlanController extends Controller
{
    public function index()
    {
        $capacity_plans = CapacityPlan::all();
        return view('backend.OMS.capacity_plans.index', compact('capacity_plans'));
    }

    public function create()
    {
        return view('backend.OMS.capacity_plans.create');
    }

    // public function store(Request $request)
    // {
    //     // dd($request->all());

    //     $request->validate([
    //         'division_id' => 'required',
    //         'division_name' => 'required',
    //         'company_id' => 'required',
    //         'company_name' => 'required',
    //         'production_plan' => 'required',
    //         'running_machines' => 'required',
    //         'helpers' => 'required',
    //         'working_hours' => 'required',
    //         'efficiency' => 'required',
    //         'smv' => 'required',
    //     ]);

    //     $exsisitingCapacityPlan = CapacityPlan::where('production_plan', $request->production_plan)->first();
    //     if ($exsisitingCapacityPlan) {
    //         return redirect()->back()->withErrors( 'Capacity Plan already exists for this month!');
    //     }

    //     $capacity_plan = CapacityPlan::create([
    //         'division_id' => $request->division_id,
    //         'division_name' => $request->division_name,
    //         'company_id' => $request->company_id,
    //         'company_name' => $request->company_name,
    //         'production_plan' => $request->production_plan,
    //         'running_machines' => $request->running_machines,
    //         'helpers' => $request->helpers,
    //         'working_hours' => $request->working_hours,
    //         'efficiency' => $request->efficiency,
    //         'smv' => $request->smv,
    //         'workingDays' => $request->workingDays,
    //         'daily_capacity_minutes' => $request->daily_capacity_minutes,
    //         'weekly_capacity_minutes' => $request->weekly_capacity_minutes,
    //         'monthly_capacity_minutes' => $request->monthly_capacity_minutes,
    //         'monthly_capacity_quantity' => $request->monthly_capacity_quantity,
    //         'monthly_capacity_value' => $request->monthly_capacity_value,
    //     ]);



    //     return redirect()->route('capacity_plans.index')->withMessage( 'Capacity Plan added successfully!');
    // }

    public function store(Request $request)
    {
        $request->validate([
            'division_id' => 'required|integer',
            'division_name' => 'required|string',
            'company_id' => 'required|integer',
            'company_name' => 'required|string',
            'production_plan' => 'required|string',
            'running_machines' => 'required|integer',
            'helpers' => 'required|integer',
            'working_hours' => 'required|integer',
            'efficiency' => 'required|numeric',
            'smv' => 'required|numeric',
        ]);

        $existingCapacityPlan = CapacityPlan::where('production_plan', $request->production_plan)->first();
        if ($existingCapacityPlan) {
            return redirect()->back()->withErrors('Capacity Plan already exists for this month!');
        }

        $capacity_plan = CapacityPlan::create([
            'division_id' => (int)$request->division_id,
            'division_name' => $request->division_name,
            'company_id' => (int)$request->company_id,
            'company_name' => $request->company_name,
            'production_plan' => $request->production_plan,
            'running_machines' => (int)$request->running_machines,
            'helpers' => (int)$request->helpers,
            'working_hours' => (int)$request->working_hours,
            'efficiency' => (float)$request->efficiency,
            'smv' => (float)$request->smv,
            'workingDays' => (int)$request->workingDays,
            'daily_capacity_minutes' => (float)$request->daily_capacity_minutes,
            'weekly_capacity_minutes' => (float)$request->weekly_capacity_minutes,
            'monthly_capacity_minutes' => (float)$request->monthly_capacity_minutes,
            'monthly_capacity_quantity' => (int)$request->monthly_capacity_quantity,
            'monthly_capacity_value' => (float)$request->monthly_capacity_value,
        ]);

        return redirect()->route('capacity_plans.index')->withMessage('Capacity Plan added successfully!');
    }
    public function edit($id)
    {
        $capacity_plan = CapacityPlan::findOrFail($id);
        // dd($capacity_plan);
        return view('backend.OMS.capacity_plans.edit', compact('capacity_plan'));
    }

    public function update(Request $request, $id)
    {
        // dd($request->all(), $id);
        $request->validate([
            'production_plan' => 'required|date_format:Y-m',
            'running_machines' => 'required|integer',
            'helpers' => 'required|integer',
            'working_hours' => 'required|integer',
            'efficiency' => 'required|numeric',
            'smv' => 'required|numeric',
        ]);

        $capacityPlan = CapacityPlan::findOrFail($id);

        // Update only the fields that have a value in the request
        $data = [];

        if ($request->has('production_plan')) {
            $data['production_plan'] = $request->input('production_plan');
        }

        if ($request->has('running_machines')) {
            $data['running_machines'] = $request->input('running_machines');
        }

        if ($request->has('helpers')) {
            $data['helpers'] = $request->input('helpers');
        }

        // if ($request->has('working_hours')) {
        //     $data['working_hours'] = $request->input('working_hours');
        // }

        if ($request->has('efficiency')) {
            $data['efficiency'] = $request->input('efficiency');
        }

        if ($request->has('smv')) {
            $data['smv'] = $request->input('smv');
        }

        if ($request->has('daily_capacity_minutes')) {
            $data['daily_capacity_minutes'] = $request->input('daily_capacity_minutes');
        }

        if ($request->has('weekly_capacity_minutes')) {
            $data['weekly_capacity_minutes'] = $request->input('weekly_capacity_minutes');
        }

        if ($request->has('monthly_capacity_minutes')) {
            $data['monthly_capacity_minutes'] = $request->input('monthly_capacity_minutes');
        }

        if ($request->has('monthly_capacity_quantity')) {
            $data['monthly_capacity_quantity'] = $request->input('monthly_capacity_quantity');
        }

        if ($request->has('monthly_capacity_value')) {
            $data['monthly_capacity_value'] = $request->input('monthly_capacity_value');
        }

       

        // Update only the fields that have been added to $data
        $capacityPlan->update($data);

        return redirect()->route('capacity_plans.index')->withMessage('Capacity plan updated successfully');
    }

    public function show($id)
    {
        $capacity_plan = CapacityPlan::findOrFail($id);
        // dd($capacity_plan);
        return view('backend.OMS.capacity_plans.show', compact('capacity_plan'));
    }

    


    public function destroy($id)
    {
        $capacity_plan = CapacityPlan::findOrFail($id);
        // dd($capacity_plan);

        $capacity_plan->delete();
        return redirect()->route('capacity_plans.index')->withMessage( 'Capacity Plan deleted successfully!');
    }

    //  public function getAvgSMV(Request $request)
    // {
    //     $productionPlan = $request->input('production_plan');
    //     // Use Carbon to generate the start and end dates of the selected month
    //     $startOfMonth = Carbon::parse($productionPlan . '-01')->startOfMonth()->toDateString();
    //     $endOfMonth = Carbon::parse($productionPlan . '-01')->endOfMonth()->toDateString();

    //     // Calculate total color quantity and total production minutes for the selected plan month
    //     // $jobData = Job::whereBetween('delivery_date', [$startOfMonth, $endOfMonth])
    //     //     ->select(
    //     //         DB::raw('SUM(color_quantity) as total_color_quantity'),
    //     //         DB::raw('SUM(color_quantity * target_smv) as total_production_minutes'),
    //     //         DB::raw('SUM(color_quantity * unit_price) as total_value')
    //     //     )
    //     //     ->first();

    //     $jobData = Job::select(
    //         'job_no',
    //         DB::raw('SUM(color_quantity) as total_color_quantity'),
    //         DB::raw('SUM(color_quantity * target_smv) as total_production_minutes'),
    //         DB::raw('SUM(color_quantity * unit_price) as total_value')
    //     )->groupBy('job_no', 'target_smv')
    //         ->get();

    //     // Calculate avg SMV: total production minutes / total color quantity
    //     // $avgSMV = $jobData->total_color_quantity > 0
    //     //     ? $jobData->total_production_minutes / $jobData->total_color_quantity
    //     //     : 0;

    //     $avgSMV = $jobData->total_color_quantity > 0
    //         ? $jobData->sum('target_smv') / $jobData->count('job_no')
    //         : 0;



    //     // avgSMV show only 2 decimal points in ceil
    //     // $avgSMV = ceil($avgSMV * 100) / 100;

    //     // AvgUnitPrice = total_value / total_color_quantity
    //     $avgUnitPrice = $jobData->total_color_quantity > 0 ? $jobData->total_value / $jobData->total_color_quantity : 0;

    //     // json have all the data in different key value pair
    //     $avgSMV = [
    //         'avg_smv' => $avgSMV,
    //         'total_color_quantity' => $jobData->total_color_quantity,
    //         'total_value' => $jobData->total_value,
    //         'total_production_minutes' => $jobData->total_production_minutes,
    //         'avg_unit_price' => $avgUnitPrice,
    //     ];



    //     return response()->json($avgSMV);
    // }

    public function getAvgSMV(Request $request)
    {
        $productionPlan = $request->input('production_plan');

        // Calculate total color quantity, total production minutes, and total value for the selected plan month
        $jobData = Job::select(
            'job_no as job_no',
            DB::raw('MAX(target_smv) as total_target_smv'),
            DB::raw('SUM(color_quantity) as total_color_quantity'),
            DB::raw('SUM(color_quantity * target_smv) as total_production_minutes'),
            DB::raw('SUM(color_quantity * unit_price) as total_value'),
        )
        ->groupBy('job_no')
        ->get();

        // Calculate total target_smv and total unique job_no
        $totalTargetSMV = 0;
        $totalJobNo = 0;

        foreach($jobData as $job){
            $totalTargetSMV += $job->total_target_smv;
            $totalJobNo += 1;
        }

        // Calculate avg SMV: total target_smv / total unique job_no
        $avgSMV = $totalJobNo > 0 ? $totalTargetSMV / $totalJobNo : 0;

        // Calculate total color quantity, total production minutes, and total value
        $totalColorQuantity = $jobData->sum('total_color_quantity');
        $totalProductionMinutes = $jobData->sum('total_production_minutes');
        $totalValue = $jobData->sum('total_value');

        // Calculate avg unit price: total_value / total_color_quantity
        $avgUnitPrice = $totalColorQuantity > 0 ? $totalValue / $totalColorQuantity : 0;

        // Prepare the response data
        $responseData = [
                'avg_smv' => $avgSMV,
                'total_color_quantity' => $totalColorQuantity,
                'total_value' => $totalValue,
                'total_production_minutes' => $totalProductionMinutes,
                'avg_unit_price' => $avgUnitPrice,
                'total_target_smv' => $totalTargetSMV,
                'total_job_no' => $totalJobNo,
                'job_data' => $jobData,

            ];

        return response()->json($responseData);
    }

    public function checkExistingPlan(Request $request)
    {
         
        $productionPlan =
        $request->input('production_plan'); // assuming this is a string like "2024-11"

      //convert the string to date
        $productionPlan = Carbon::parse($productionPlan)->format('Y-m');

        //production_plan column convert the string to date format in the database and check if it exists

        $db_production_plan = CapacityPlan::where('production_plan', $productionPlan)->first();
 
        if ($db_production_plan) {
            return response()->json(['exists' => true,
                'edit_url' => route('capacity_plans.show', $db_production_plan->id),
        ]);
        } else {
            return response()->json(['exists' => false,
                'create_url' => route('capacity_plans.create'),
        ]);
        }


 
    }
 

    public function checkExistingCapacity(Request $request)
    {

        $productionPlan =
            $request->input('production_plan'); // assuming this is a string like "2024-11"

        //convert the string to date
        $productionPlan = Carbon::parse($productionPlan)->format('Y-m');

        //production_plan column convert the string to date format in the database and check if it exists

        $db_production_plan = CapacityPlan::where('production_plan', $productionPlan)->first();

        if ($db_production_plan) {
            return response()->json([
                'exists' => true,
                 'data' => $db_production_plan,
            ]);
        } else {
            return response()->json([
                'exists' => false,
                'create_url' => route('capacity_plans.create'),
            ]);
        }
    }

}
