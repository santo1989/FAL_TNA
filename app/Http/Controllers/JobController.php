<?php

namespace App\Http\Controllers;

use App\Models\Buyer;
use App\Models\Job;
use App\Models\SewingBalance;
use App\Models\Shipment;
use App\Models\TNA;
use App\Models\TnaExplanation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\JobsImport; // Add this line to import the JobsImport class
use Illuminate\Support\Facades\File;

class JobController extends Controller
{
    // public function index()
    // {
    //     //if job_no is not empty
    //     $jobs = Job::select('buyer', 'job_no', 'style', 'po', 'department', 'item', 'order_quantity', 'delivery_date', 'order_received_date')->groupBy('buyer', 'job_no', 'style', 'po', 'department', 'item', 'order_quantity', 'delivery_date', 'order_received_date')->get();
    //     // dd($jobs); 
    //     //if job_no is empty
    //     if ($jobs->isEmpty()) {
    //         return view('backend.OMS.jobs.create');
    //     }


    //     return view('backend.OMS.jobs.index', compact('jobs'));
    // }


    public function index()
    {
        $jobs = Job::select([
            'buyer',
            'job_no',
            'style',
            'po',
            'department',
            'item',
            'order_quantity',
            'delivery_date',
            'order_received_date'
        ])
            ->with(['sewingBalances', 'shipments']) // Add eager loading
            ->groupBy([
                'buyer',
                'job_no',
                'style',
                'po',
                'department',
                'item',
                'order_quantity',
                'delivery_date',
                'order_received_date'
            ])
            ->get();

        return view('backend.OMS.jobs.index', compact('jobs'));
    }
    public function tableBody()
    {
        $jobs = Job::select(
            'buyer',
            'job_no',
            'style',
            'po',
            'department',
            'item',
            'order_quantity',
            'delivery_date',
            'order_received_date'
        )
            ->groupBy(
                'buyer',
                'job_no',
                'style',
                'po',
                'department',
                'item',
                'order_quantity',
                'delivery_date',
                'order_received_date'
            )
            ->with(['sewingBalances', 'shipments'])
            ->get();

        return view('backend.OMS.jobs.partials.job_rows', compact('jobs'));
    }

  
    public function sewingData($jobNo)
    {
        $sewingData = SewingBalance::where('job_no', $jobNo)->get();

        return view('backend.OMS.jobs.partials.sewing_data', [
            'sewingData' => $sewingData,
            'jobNo' => $jobNo
        ]);
    }

    public function shipmentData($jobNo)
    {
        $shipmentData = Shipment::where('job_no', $jobNo)->get();

        return view('backend.OMS.jobs.partials.shipment_data', [
            'shipmentData' => $shipmentData,
            'jobNo' => $jobNo
        ]);
    }

    public function create()
    {
        return view('backend.OMS.jobs.create');
    }

        public function store(Request $request)
    {
        // dd($request->all()); // Uncomment this line for debugging purposes only.

        try {
            // Validate the request data if needed.
            $request->validate([
                'created_by' => 'required|integer',
                'division_id' => 'required|integer',
                'company_id' => 'required|integer',
                'job_no' => 'required|string',
                'style' => 'required|string',
                'po' => 'required|string',
                'department' => 'required|string',
                'item' => 'required|string',
                'country' => 'required|string',
                'order_quantity' => 'required|integer',
                // 'ins_date' => 'required|date',
                'print_wash' => 'required|string',
                'delivery_date' => 'required|date',
                'target_smv' => 'required|numeric',
                'production_minutes' => 'required|numeric',
                'unit_price' => 'required|numeric',
                'total_value' => 'required|numeric',
                'cm_pc' => 'required|numeric',
                'total_cm' => 'required|numeric',
                'consumption_dzn' => 'required|numeric',
                'fabric_qnty' => 'required|numeric',
                'fabrication' => 'required|string',
                'aop' => 'required|string',
                'print' => 'required|string',
                'embroidery' => 'required|string',
                'color' => 'required|array',
                'size' => 'required|array',
                'color_quantity' => 'required|array',
            ]);

            // dd($request->all());

            // Calculate total lead time in days
            $poReceiveDate = Carbon::parse($request->order_received_date);
            $shipmentETD = Carbon::parse($request->delivery_date);
            $total_lead_time = $shipmentETD->diffInDays($poReceiveDate);
            $printAndwash = $request->print_wash;

            if ($total_lead_time < 0) {
                return redirect()->back()->withInput()->withErrors(['error' => 'Invalid total lead time.']);
            }

            // Extract the color and color_quantity arrays from the request.
            $colors = $request->input('color');
            $sizes = $request->input('size');
            $colorQuantities = $request->input('color_quantity');

            //dynamically update job_no field from the combination of job_no-buyer(first 3 letter)-style_no-po_no 
            $buyer = Buyer::find($request->input('buyer_id'));
            $style = $request->input('style');
            $po = $request->input('po');

            // Ensure at least 3 characters are taken, or the full string if less than 3
            $buyer_suffix = substr($buyer->name, 0, 4);

            // Ensure at least 3 characters are taken, or the full string if less than 3
            $style_suffix = substr($style, -4); // Takes last 3 characters (or full string if less than 3)
            $po_suffix = substr($po, -4); // Takes last 3 characters (or full string if less than 3)

            $job_no = strtoupper($request->input('job_no')) . '-' .
                strtoupper($buyer_suffix) . '-' .
                strtoupper($style_suffix) . '-' .
                strtoupper($po_suffix);

            // dd($job_no);

            foreach ($colors as $key => $color) {
                Job::create([

                    'company_id' => $request->input('company_id'),
                    'division_id' => $request->input('division_id'),
                    'company_name' => $request->input('company_name'),
                    'division_name' => $request->input('division_name'),
                    'buyer_id' => $request->input('buyer_id'),
                    'buyer' => Buyer::find($request->input('buyer_id'))->name,
                    'job_no' => strtoupper($job_no),
                    'style' => strtoupper($request->input('style')),
                    'po' => strtoupper($request->input('po')),
                    'department' => strtoupper($request->input('department')),
                    'item' => $request->input('item'),
                    'color' => strtoupper($color),
                    'size' => strtoupper($sizes[$key]),
                    'color_quantity' => $colorQuantities[$key],
                    'destination' => $request->input('country'),
                    // save $request->input('order_quantity') as integer
                    'order_quantity' => number_format($request->input('order_quantity'), 0, '.', ''),
                    // 'ins_date' => $request->input('ins_date'),
                    'wash' => $request->input('wash'),
                    'print_wash' => $request->input('print_wash'),
                    'delivery_date' => $request->input('delivery_date'),
                    'target_smv' => $request->input('target_smv'),
                    'production_minutes' => $request->input('production_minutes'),
                    'unit_price' => $request->input('unit_price'),
                    'total_value' => $request->input('total_value'),
                    'cm_pc' => $request->input('cm_pc'),
                    'total_cm' => $request->input('total_cm'),
                    'consumption_dzn' => $request->input('consumption_dzn'),
                    'fabric_qnty' => $request->input('fabric_qnty'),
                    'fabrication' => strtoupper($request->input('fabrication')),
                    'order_received_date' => $request->input('order_received_date'),
                    'aop' => $request->input('aop'),
                    'print' => $request->input('print'),
                    'embroidery' => $request->input('embroidery'),
                    'remarks' => strtoupper($request->input('remarks')),
                ]);
            }

            //retrieve the incerted job information
            $job = Job::where('job_no', $job_no)->first();
            // dd($job);

            //send  "buyer_id", "style", "po", "item", "qty_pcs", "po_receive_date", "shipment_etd", "total_lead_time", "remarks", "print_wash" information to the  TNAController store method to generate TNA 

            //$job->order_quantity value convert to integer 
            $order_quantity = abs($job->order_quantity);

            // In your JobController's store method:

            $tna_data = [
                'job_id' => $job->id,
                'job_no' => $job->job_no,
                'buyer_id' => $job->buyer_id,
                'style' => $job->style,
                'po' => $job->po,
                'item' => $job->item,
                'qty_pcs' => $order_quantity,
                'po_receive_date' => $request->input('order_received_date'),
                'shipment_etd' => $request->input('delivery_date'),
                'total_lead_time' => $request->input('total_lead_time'),
                'remarks' => $job->remarks,
                'print_wash' => $job->print_wash,
            ];

            // Validate the data
            $validator = Validator::make(
                $tna_data,
                [
                    'job_id' => 'required|integer',
                    'job_no' => 'required|string',
                    'buyer_id' => 'required|integer',
                    'style' => 'required|string',
                    'po' => 'required|string',
                    'item' => 'required|string',
                    'qty_pcs' => 'required|integer',
                    'po_receive_date' => 'required|date',
                    'shipment_etd' => 'required|date',
                    'total_lead_time' => 'required|integer',
                    'remarks' => 'nullable|string',
                    'print_wash' => 'nullable|string',
                ]
            );

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            // // Pass data to TNAController
            // $tnaRequest = new Request($tna_data);
            // $tnaController = app(TNAController::class);
            // $tnaController->store($tnaRequest);




            return redirect()->route('jobs.index')->with('message', 'Job created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function show(Request $request, $job_no)
    {
        $jobs = Job::where('job_no', $job_no)->get();
        // dd($jobs);
        return view('backend.OMS.jobs.show', compact('jobs'));
    }


    public function edit_jobs(Request $request, $edit_jobs)
    {
        $job = Job::where('job_no', $edit_jobs)->first();
        $jobs_no = $edit_jobs;
        $colors = Job::where('job_no', $edit_jobs)->get();

        // dd($jobs);
        return view('backend.OMS.jobs.edit_jobs', compact('job', 'jobs_no', 'colors'));
    }


    public function update_edit_jobs(Request $request, $edit_jobs)
    {
        // Retrieve the job(s) using where clause
        $jobs = Job::where('job_no', $edit_jobs)->get();

        // Validate the request data if needed.
        $request->validate([
            'created_by' => 'required|integer',
            'division_id' => 'required|integer',
            'company_id' => 'required|integer',
            'job_no' => 'required|string',
            'style' => 'required|string',
            'po' => 'required|string',
            'department' => 'required|string',
            'item' => 'required|string',
            'country' => 'required|string',
            'order_quantity' => 'required|integer',
            'ins_date' => 'required|date',
            'delivery_date' => 'required|date',
            'target_smv' => 'required|numeric',
            'production_minutes' => 'required|numeric',
            'unit_price' => 'required|numeric',
            'total_value' => 'required|numeric',
            'cm_pc' => 'required|numeric',
            'total_cm' => 'required|numeric',
            'consumption_dzn' => 'required|numeric',
            'fabric_qnty' => 'required|numeric',
            'fabrication' => 'required|string',
            'aop' => 'required|string',
            'print' => 'required|string',
            'embroidery' => 'required|string',
            'color' => 'required|array',
            'size' => 'required|array',
            'color_quantity' => 'required|array',
        ]);

        // Extract the color, size, and color_quantity arrays from the request.
        $colors = $request->input('color');
        $sizes = $request->input('size');
        $colorQuantities = $request->input('color_quantity');

        try {
            // Calculate total lead time in days
            $poReceiveDate = Carbon::parse($request->order_received_date);
            $shipmentETD = Carbon::parse($request->delivery_date);
            $total_lead_time = $shipmentETD->diffInDays($poReceiveDate);
            $printAndwash = $request->print_wash;

            if ($total_lead_time < 0) {
                return redirect()->back()->withInput()->withErrors(['error' => 'Invalid total lead time.']);
            }

            foreach ($jobs as $job) {
                foreach ($colors as $key => $color) {
                    // Update job attributes
                    $job->update([
                        'company_id' => $request->input('company_id'),
                        'division_id' => $request->input('division_id'),
                        'company_name' => $request->input('company_name'),
                        'division_name' => $request->input('division_name'),
                        'buyer_id' => $request->input('buyer_id'),
                        'buyer' => Buyer::find($request->input('buyer_id'))->name,
                        'job_no' => strtoupper($request->input('job_no')),
                        'style' => strtoupper($request->input('style')),
                        'po' => strtoupper($request->input('po')),
                        'department' => strtoupper($request->input('department')),
                        'item' => $request->input('item'),
                        'color' => strtoupper($color),
                        'size' => strtoupper($sizes[$key]),
                        'color_quantity' => $colorQuantities[$key],
                        'destination' => $request->input('country'),
                        'order_quantity' => number_format($request->input('order_quantity'), 0, '.', ''),
                        'ins_date' => $request->input('ins_date'),
                        'wash' => $request->input('wash'),
                        'print_wash' => $request->input('print_wash'),
                        'delivery_date' => $request->input('delivery_date'),
                        'target_smv' => $request->input('target_smv'),
                        'production_minutes' => $request->input('production_minutes'),
                        'unit_price' => $request->input('unit_price'),
                        'total_value' => $request->input('total_value'),
                        'cm_pc' => $request->input('cm_pc'),
                        'total_cm' => $request->input('total_cm'),
                        'consumption_dzn' => $request->input('consumption_dzn'),
                        'fabric_qnty' => $request->input('fabric_qnty'),
                        'fabrication' => strtoupper($request->input('fabrication')),
                        'order_received_date' => $request->input('order_received_date'),
                        'aop' => $request->input('aop'),
                        'print' => $request->input('print'),
                        'embroidery' => $request->input('embroidery'),
                        'remarks' => strtoupper($request->input('remarks')),
                    ]);
                }
            }

            // Retrieve the updated job information
            $job = Job::where('job_no', $request->input('job_no'))->first();

            // Prepare TNA data
            $order_quantity = abs($job->order_quantity);

            $tna_data = [
                'buyer_id' => $job->buyer_id,
                'style' => $job->style,
                'po' => $job->po,
                'item' => $job->item,
                'qty_pcs' => $order_quantity,
                'po_receive_date' => $request->input('order_received_date'),
                'shipment_etd' => $request->input('delivery_date'),
                'total_lead_time' => $total_lead_time,
                'remarks' => $job->remarks,
                'print_wash' => $job->print_wash,
            ];

            // Validate the TNA data
            $validator = Validator::make(
                $tna_data,
                [
                    'buyer_id' => 'required|integer',
                    'style' => 'required|string',
                    'po' => 'required|string',
                    'item' => 'required|string',
                    'qty_pcs' => 'required|integer',
                    'po_receive_date' => 'required|date',
                    'shipment_etd' => 'required|date',
                    'total_lead_time' => 'required|integer',
                    'remarks' => 'nullable|string',
                    'print_wash' => 'nullable|string',
                ]
            );

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            // Pass data to TNAController
            $tnaRequest = new Request($tna_data);
            $tnaController = app(TNAController::class);
            $tnaController->store($tnaRequest);

            return redirect()->route('jobs.index')->with('message', 'Job updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }
    public function destroy($id)
    {
        // Find the job by its ID
        $job = Job::find($id);

        // If the job doesn't exist, return a 404 response or redirect with an error message
        if (!$job) {
            return redirect()->route('jobs.index')->with('error', 'Job not found.');
        }

        // Find related sewings and shipments using the job ID
        $sewings = SewingBalance::where('job_id', $job->id)->get();
        $shipments = Shipment::where('job_id', $job->id)->get();

        // Delete all related sewings
        foreach ($sewings as $sewing) {
            $sewing->delete();
        }

        // Delete all related shipments
        foreach ($shipments as $shipment) {
            $shipment->delete();
        }

        // Delete the job itself
        $job->delete();

        // Redirect to the jobs index with a success message
        return redirect()->route('jobs.index')->with('message', 'Job and related records deleted successfully.');
    }

    public function destroy_all($job_no)
    {
        // Find the job by its ID
        $jobs = Job::where('job_no', $job_no)->get();

        // If the job doesn't exist, return a 404 response or redirect with an error message
        if (!$jobs) {
            return redirect()->route('jobs.index')->with('error', 'Job not found.');
        }

        // Find related sewings and shipments using the job ID
        $sewings = SewingBalance::where('job_no', $job_no)->get();
        $shipments = Shipment::where('job_no', $job_no)->get();

        // Delete all related sewings
        foreach ($sewings as $sewing) {
            $sewing->delete();
        }

        // Delete all related shipments
        foreach ($shipments as $shipment) {
            $shipment->delete();
        }

        // Delete the all job itself and related records
        foreach ($jobs as $job) {
            $job->delete();
        }

        // Redirect to the jobs index with a success message
        return redirect()->route('jobs.index')->with('message', 'Job and related records deleted successfully.');
    }

    // destroy_all_tna
    public function destroy_all_tna($job_no)
    {
        // Find the job by its ID
        $jobs = Job::where('job_no', $job_no)->first();

        // If the job doesn't exist, return a 404 response or redirect with an error message
        if (!$jobs) {
            return redirect()->route('jobs.index')->with('error', 'Job not found.');
        }

        // dd($jobs);

        //convert qty_pcs to integer
        $order_quantity = abs($jobs->order_quantity);

        $tna = TNA::where('buyer_id', $jobs->buyer_id)->where('style', $jobs->style)->where('po', $jobs->po)->where('item', $jobs->item)->where('qty_pcs', $order_quantity)->first();

        if ($tna != null) {
            $tna_explain = TnaExplanation::where('tna_id', $tna->id)->get();
            // dd($tna_explain, $tna, $jobs);

            // Delete all related tna_explain
            foreach ($tna_explain as $tna_explains) {
                $tna_explains->delete();
            }

            // Delete the all tna itself and related records
            $tna->delete();
        }




        $job_no = $jobs->job_no;
        $jobs = Job::where('job_no', $job_no)->get();

        // Find related sewings and shipments using the job ID
        $sewings = SewingBalance::where('job_no', $job_no)->get();
        $shipments = Shipment::where('job_no', $job_no)->get();

        // Delete all related sewings
        foreach ($sewings as $sewing) {
            $sewing->delete();
        }

        // Delete all related shipments
        foreach ($shipments as $shipment) {
            $shipment->delete();
        }

        // Delete the all job itself and related records
        foreach ($jobs as $job) {
            $job->delete();
        }

        // Redirect to the jobs index with a success message
        return redirect()->route('jobs.index')->with('message', 'Job and related records deleted successfully.');
    }
    public function monthlyOrderSummary()
    {
        // Get the total order quantity
        $total_order_qty = Job::sum('order_quantity');

        $buyers = DB::table('jobs')
            ->leftJoin('sewing_balances', 'jobs.id', '=', 'sewing_balances.job_id')
            ->leftJoin('shipments', 'jobs.id', '=', 'shipments.job_id')
            ->select(
                'jobs.buyer',
                DB::raw('COUNT(DISTINCT jobs.job_no) as number_of_orders'),
                DB::raw('SUM(jobs.order_quantity) as order_qty'),
                DB::raw('SUM(sewing_balances.sewing_balance) as sewing_balance'),
                DB::raw('AVG(jobs.target_smv) as avg_smv'),
                DB::raw('SUM(jobs.production_minutes) as produced_min'),
                DB::raw('SUM(sewing_balances.production_min_balance) as production_balance'),
                DB::raw('AVG(jobs.unit_price) as avg_unit_price'),
                DB::raw('SUM(jobs.total_value) as total_value'),
                DB::raw('AVG(jobs.cm_pc) as avg_cm_dzn'),
                DB::raw('SUM(jobs.total_cm) as total_cm'),
                DB::raw('SUM(shipments.shipped_qty) as shipped_qty'),
                DB::raw('SUM(jobs.order_quantity) - SUM(shipments.shipped_qty) as shipment_balance'),
                DB::raw('SUM(shipments.excess_short_shipment_qty) as excess_short_qty'),
                DB::raw('SUM(shipments.shipped_qty * jobs.unit_price) as shipped_value'),
                DB::raw('SUM((jobs.order_quantity - shipments.shipped_qty) * jobs.unit_price) as value_balance'),
                DB::raw('SUM(shipments.excess_short_shipment_value * jobs.unit_price) as excess_short_value')
            )
            ->groupBy('jobs.buyer')
            ->get();

        // Compute booking percentage for each buyer
        foreach ($buyers as $buyer) {
            $buyer->booking_percentage = ($buyer->order_qty / $total_order_qty) * 100;
        }

        return view('backend.OMS.reports.monthly_order_summary', compact('buyers'));
    }

    public function quantityWiseSummary()
    {
        $quantityRanges = [
            '3000 to 5000' => [3000, 5000],
            '5001 to 10000' => [5001, 10000],
            'More than 10000' => [10001, PHP_INT_MAX],
        ];

        $buyers = Job::distinct()->pluck('buyer')->toArray();
        $summary = [];

        foreach ($quantityRanges as $rangeName => $range) {
            foreach ($buyers as $buyer) {
                $data = DB::table('jobs')
                    ->select(
                        DB::raw('COUNT(DISTINCT job_no) as number_of_orders'),
                        DB::raw('SUM(order_quantity) as total_quantity'),
                        DB::raw('SUM(total_value) as total_value'),
                        DB::raw('SUM(production_minutes) as produced_min'),
                        DB::raw('SUM(total_cm) as total_cm')
                    )
                    ->where('buyer', $buyer)
                    ->whereBetween('order_quantity', $range)
                    ->first();

                $summary[$rangeName][$buyer] = [
                    'number_of_orders' => $data->number_of_orders,
                    'total_quantity' => $data->total_quantity,
                    'total_value' => $data->total_value,
                    'produced_min' => $data->produced_min,
                    'total_cm' => $data->total_cm,
                ];
            }
        }

        // Calculate totals for all ranges and buyers
        $totals = [
            'number_of_orders' => 0,
            'total_quantity' => 0,
            'total_value' => 0,
            'produced_min' => 0,
            'total_cm' => 0,
        ];

        foreach ($summary as $ranges) {
            foreach ($ranges as $data) {
                $totals['number_of_orders'] += $data['number_of_orders'];
                $totals['total_quantity'] += $data['total_quantity'];
                $totals['total_value'] += $data['total_value'];
                $totals['produced_min'] += $data['produced_min'];
                $totals['total_cm'] += $data['total_cm'];
            }
        }

        // Calculate percentages
        foreach ($summary as $rangeName => &$ranges) {
            foreach ($ranges as &$data) {
                $data['percentage_orders'] = ($data['number_of_orders'] / $totals['number_of_orders']) * 100;
                $data['percentage_quantity'] = ($data['total_quantity'] / $totals['total_quantity']) * 100;
                $data['percentage_value'] = ($data['total_value'] / $totals['total_value']) * 100;
                $data['percentage_produced_min'] = ($data['produced_min'] / $totals['produced_min']) * 100;
            }
        }

        return view('backend.OMS.reports.quantity_wise_summary', compact('summary', 'totals', 'buyers', 'quantityRanges'));
    }


    public function itemWiseSummary()
    {
        $items = [
            'T-Shirt',
            'Polo Shirt',
            'Romper',
            'Sweat Shirt',
            'Jacket',
            'Hoodie',
            'Jogger',
            'Pant/Bottom',
            'Cargo Pant',
            'Leggings',
            'Ladies/Girls Dress',
            'Others'
        ];

        $buyers = Job::distinct()->pluck('buyer')->toArray();
        $summary = [];

        foreach ($items as $item) {
            foreach ($buyers as $buyer) {
                $data = DB::table('jobs')
                    ->join('shipments', 'jobs.id', '=', 'shipments.job_id')
                    ->join('sewing_balances', 'jobs.id', '=', 'sewing_balances.job_id')
                    ->select(
                        DB::raw('COUNT(DISTINCT jobs.job_no) as number_of_orders'),
                        DB::raw('SUM(shipments.shipped_qty) as total_quantity'),
                        DB::raw('SUM(shipments.total_shipped_value) as total_value'),
                        DB::raw('SUM(sewing_balances.production_min_balance) as produced_min'),
                        DB::raw('SUM(jobs.total_cm) as total_cm')
                    )
                    ->where('jobs.item', $item)
                    ->where('jobs.buyer', $buyer)
                    ->first();

                $summary[$item][$buyer] = [
                    'number_of_orders' => $data->number_of_orders,
                    'total_quantity' => $data->total_quantity,
                    'total_value' => $data->total_value,
                    'produced_min' => $data->produced_min,
                    'total_cm' => $data->total_cm,
                ];
            }
        }

        $totals = [
            'number_of_orders' => 0,
            'total_quantity' => 0,
            'total_value' => 0,
            'produced_min' => 0,
            'total_cm' => 0,
        ];

        foreach ($summary as $item => $buyersData) {
            foreach ($buyersData as $buyer => $data) {
                $totals['number_of_orders'] += $data['number_of_orders'];
                $totals['total_quantity'] += $data['total_quantity'];
                $totals['total_value'] += $data['total_value'];
                $totals['produced_min'] += $data['produced_min'];
                $totals['total_cm'] += $data['total_cm'];
            }
        }

        // Calculate percentages
        foreach ($summary as $item => &$buyersData) {
            foreach ($buyersData as $buyer => &$data) {
                $data['percentage_orders'] = ($totals['number_of_orders'] > 0) ? ($data['number_of_orders'] / $totals['number_of_orders']) * 100 : 0;
                $data['percentage_quantity'] = ($totals['total_quantity'] > 0) ? ($data['total_quantity'] / $totals['total_quantity']) * 100 : 0;
                $data['percentage_value'] = ($totals['total_value'] > 0) ? ($data['total_value'] / $totals['total_value']) * 100 : 0;
                $data['percentage_produced_min'] = ($totals['produced_min'] > 0) ? ($data['produced_min'] / $totals['produced_min']) * 100 : 0;
            }
        }

        return view('backend.OMS.reports.item_wise_summary', compact('summary', 'totals', 'buyers'));
    }





    public function deliverySummary()
    {
        // Define the possible statuses
        $statuses = ['Advance Delivery', 'On-time Delivery', 'Delay Delivery', 'Partial Delivery'];

        // Initialize the summary array
        $summary = [];

        // Get distinct buyers
        $buyers = Job::distinct()->pluck('buyer')->toArray();

        // Get all jobs
        $jobs = DB::table('jobs')->get();

        foreach ($jobs as $job) {
            // Get the total shipped quantity for the current job
            $totalShippedQty = DB::table('shipments')
                ->where('job_id', $job->id)
                ->sum('shipped_qty');

            // Determine the delivery status based on the comparison
            if ($totalShippedQty >= $job->color_quantity) {
                // Check the ex_factory_date for each shipment
                $shipments = DB::table('shipments')
                    ->where('job_id', $job->id)
                    ->get();

                foreach ($shipments as $shipment) {
                    if ($shipment->ex_factory_date == $job->delivery_date) {
                        $deliveryStatus = 'On-time Delivery';
                    } elseif ($shipment->ex_factory_date < $job->delivery_date) {
                        $deliveryStatus = 'Advance Delivery';
                    } else {
                        $deliveryStatus = 'Delay Delivery';
                    }

                    // Update the delivery status in the shipments table
                    DB::table('shipments')
                        ->where('id', $shipment->id)
                        ->update(['delivery_status' => $deliveryStatus]);
                }
            } else {
                $deliveryStatus = 'Partial Delivery';

                // Update the delivery status in the shipments table for partial delivery
                DB::table('shipments')
                    ->where('job_id', $job->id)
                    ->update(['delivery_status' => $deliveryStatus]);
            }
        }

        // Generate the summary for each status and buyer
        foreach ($statuses as $status) {
            foreach ($buyers as $buyer) {
                $data = DB::table('shipments')
                    ->join('jobs', 'shipments.job_id', '=', 'jobs.id')
                    ->select(
                        DB::raw('COUNT(DISTINCT shipments.job_no) as number_of_deliveries'),
                        DB::raw('SUM(shipments.shipped_qty) as total_quantity')
                    )
                    ->where('shipments.delivery_status', $status)
                    ->where('jobs.buyer', $buyer)
                    ->first();

                $summary[$status][$buyer] = [
                    'number_of_deliveries' => $data->number_of_deliveries,
                    'total_quantity' => $data->total_quantity,
                ];
            }
        }

        // Calculate totals for all statuses
        $totals = [
            'number_of_deliveries' => array_sum(array_column($summary, 'number_of_deliveries')),
            'total_quantity' => array_sum(array_column($summary, 'total_quantity')),
        ];

        // Calculate percentages
        foreach ($summary as $status => &$data) {
            foreach ($data as &$buyerData) {
                $buyerData['percentage_deliveries'] = ($totals['number_of_deliveries'] > 0) ? ($buyerData['number_of_deliveries'] / $totals['number_of_deliveries']) * 100 : 0;
                $buyerData['percentage_quantity'] = ($totals['total_quantity'] > 0) ? ($buyerData['total_quantity'] / $totals['total_quantity']) * 100 : 0;
            }
        }

        return view('backend.OMS.reports.delivery_summary', compact('summary', 'totals', 'buyers', 'statuses'));
    }

    public function import(Request $request)
    {
        // $request->validate([
        //     'file' => 'required|mimes:xlsx,xls'
        // ]);

        // try {
        //     // dd($request->file('file'));
        //     // Import the Excel file using the JobsImport class
        //     Excel::import(new JobsImport, $request->file('file'));
        //     return redirect()->back()->withMessage('Jobs imported successfully!');
        // } catch (\Exception $e) {
        //     return redirect()->back()->withErrors( 'Error importing jobs: ' . $e->getMessage());
        // }
        $request->validate(['file' => 'required|mimes:xlsx,xls']);

        try {
            $import = new JobsImport();
            Excel::import($import, $request->file('file'));

            // return response()->json([
            //     'message' => 'Import successful',
            //     'processed' => $import->getProcessedRows(),
            //     'failed' => count($import->getFailedRows()),
            //     'batch_id' => $import->getBatchId(),
            //     'failures' => $import->getFailedRows()
            // ]);
            return redirect()->back()->with('message', 'Jobs imported successfully!')->with([
                'processed' => $import->getProcessedRows(),
                'failed' => count($import->getFailedRows()),
                'batch_id' => $import->getBatchId(),
                'failures' => $import->getFailedRows()
            ]);
        } catch (\Exception $e) {
            // return response()->json([
            //     'error' => 'Import failed: ' . $e->getMessage()
            // ], 500);
            return redirect()->back()->withErrors(['error' => 'Import failed: ' . $e->getMessage()]);
        }
    }

    public function job_excel_upload()
    {

        return view('backend.OMS.jobs.job_excel_upload');
    }

    public function job_sample_download()
    {

        //find the file prom public\excel_template\sample_jobs_import.xlsx and download it
        // return response()->download(public_path('excel_template/sample_jobs_import.xlsx'));

        $filePath = public_path('excel_template/sample_jobs_import.xlsx');

        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }

        return back()->download($filePath);
    }
}
