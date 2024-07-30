<?php

namespace App\Http\Controllers;

use App\Models\Buyer;
use App\Models\Job;
use App\Models\SewingBlance;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JobController extends Controller
{
    public function index()
    {
        $jobs = Job::select('buyer', 'job_no', 'style', 'po', 'department', 'item', 'order_quantity', 'delivery_date','order_received_date')->groupBy('buyer', 'job_no', 'style', 'po', 'department', 'item', 'order_quantity', 'delivery_date', 'order_received_date')->get();
        // dd($jobs);
        return view('backend.OMS.jobs.index', compact('jobs'));
    }

    public function create()
    {
        return view('backend.OMS.jobs.create');
    }

    // public function store(Request $request)
    // {
    //     dd($request->all());
    //     try {
    //         $color_name = $request->input('color_name');
    //         $color_quantity = $request->input('color_quantity');
    //         foreach($color_name as $key => $color) { 
    //         $job = Job::create([
    //             'company_id' => $request->input('company_id'),
    //             'division_id' => $request->input('division_id'),
    //             'buyer_id' => $request->input('buyer_id'),
    //             'company_name' => $request->input('company_name'),
    //             'division_name' => $request->input('division_name'),
    //             'buyer' => Buyer::find($request->input('buyer_id'))->name,
    //             'job_no' => $request->input('job_no'),
    //             'style' => ucwords($request->input('style')),
    //             'po' => ucwords($request->input('po')),
    //             'department' => ucwords($request->input('department')),
    //             'item' => $request->input('item'),
    //             'color' => ucwords($color_name[$key]),
    //             'color_quantity' => $color_quantity[$key],
    //             'destination' => $request->input('country'),
    //             'order_quantity' => $request->input('order_quantity'),
    //             'sewing_balance' => $request->input('sewing_balance'),
    //             'production_plan' => $request->input('production_plan'),
    //             'ins_date' => $request->input('ins_date'),
    //             'delivery_date' => $request->input('delivery_date'),
    //             'target_smv' => $request->input('target_smv'),
    //             'production_minutes' => $request->input('production_minutes'),
    //             // 'production_min_balance' => $request->input('production_min_balance'),
    //             'unit_price' => $request->input('unit_price'),
    //             'total_value' => $request->input('total_value'),
    //             'cm_pc' => $request->input('cm_pc'),
    //             'total_cm' => $request->input('total_cm'),
    //             'consumption_dzn' => $request->input('consumption_dzn'),
    //             'fabric_qnty' => $request->input('fabric_qnty'),
    //             'fabrication' => $request->input('fabrication'),
    //             'order_received_date' => $request->input('order_received_date'),
    //             'aop' => $request->input('aop'),
    //             'print' => $request->input('print'),
    //             'embroidery' => $request->input('embroidery'),
    //             'remarks' => $request->input('remarks'),
    //             // 'shipped_qty' => $request->input('shipped_qty'),
    //             // 'ex_factory_date' => $request->input('ex_factory_date'),
    //             // 'shipped_value' => $request->input('shipped_value'),
    //             // 'excess_short_shipment_qty' => $request->input('excess_short_shipment_qty'),
    //             // 'excess_short_shipment_value' => $request->input('excess_short_shipment_value'),
    //         ]);

    //         return redirect()->route('jobs.index')->withMessage('Job created successfully.');
    //     }
    //     } catch (\Exception $e) {
    //         return redirect()->back()->withInput()->withErrors(['error' => $e->getMessage()]);
    //     }
    // }


    public function store(Request $request)
    {
        //dd($request->all()); // Uncomment this line for debugging purposes only.

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

            // Extract the color and color_quantity arrays from the request.
            $colors = $request->input('color');
            $sizes = $request->input('size');
            $colorQuantities = $request->input('color_quantity');

            foreach ($colors as $key => $color) {
                Job::create([
                    
                    'company_id' => $request->input('company_id'),
                    'division_id' => $request->input('division_id'),
                    'company_name' => $request->input('company_name'),
                    'division_name' => $request->input('division_name'),
                    'buyer_id' => $request->input('buyer_id'),
                    'buyer' => Buyer::find($request->input('buyer_id'))->name,
                    'job_no' => $request->input('job_no'),
                    'style' => $request->input('style'),
                    'po' => $request->input('po'),
                    'department' => $request->input('department'),
                    'item' => $request->input('item'),
                    'color' => $color,
                    'size' => $sizes[$key],
                    'color_quantity' => $colorQuantities[$key],
                    'destination' => $request->input('country'),
                    'order_quantity' => $request->input('order_quantity'),
                    'ins_date' => $request->input('ins_date'),
                    'delivery_date' => $request->input('delivery_date'),
                    'target_smv' => $request->input('target_smv'),
                    'production_minutes' => $request->input('production_minutes'),
                    'unit_price' => $request->input('unit_price'),
                    'total_value' => $request->input('total_value'),
                    'cm_pc' => $request->input('cm_pc'),
                    'total_cm' => $request->input('total_cm'),
                    'consumption_dzn' => $request->input('consumption_dzn'),
                    'fabric_qnty' => $request->input('fabric_qnty'),
                    'fabrication' => $request->input('fabrication'),
                    'order_received_date' => $request->input('order_received_date'),
                    'aop' => $request->input('aop'),
                    'print' => $request->input('print'),
                    'embroidery' => $request->input('embroidery'),
                    'remarks' => $request->input('remarks'),
                ]);
            }

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

    // public function edit(Request $request, $job_no)
    // {
    //     $jobs = Job::where('job_no', $job_no)->get();
    //     // dd($jobs);
    //     return view('backend.OMS.jobs.edit', compact('jobs'));
    // }

    // public function update(Request $request, Job $job)
    // {
    //     $job->update($request->all());
    //     return redirect()->route('jobs.index');
    // }
    

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
                        'job_no' => $request->input('job_no'),
                        'style' => $request->input('style'),
                        'po' => $request->input('po'),
                        'department' => $request->input('department'),
                        'item' => $request->input('item'),
                        'color' => $color,
                        'size' => $sizes[$key],
                        'color_quantity' => $colorQuantities[$key],
                        'destination' => $request->input('country'),
                        'order_quantity' => $request->input('order_quantity'),
                        'ins_date' => $request->input('ins_date'),
                        'delivery_date' => $request->input('delivery_date'),
                        'target_smv' => $request->input('target_smv'),
                        'production_minutes' => $request->input('production_minutes'),
                        'unit_price' => $request->input('unit_price'),
                        'total_value' => $request->input('total_value'),
                        'cm_pc' => $request->input('cm_pc'),
                        'total_cm' => $request->input('total_cm'),
                        'consumption_dzn' => $request->input('consumption_dzn'),
                        'fabric_qnty' => $request->input('fabric_qnty'),
                        'fabrication' => $request->input('fabrication'),
                        'order_received_date' => $request->input('order_received_date'),
                        'aop' => $request->input('aop'),
                        'print' => $request->input('print'),
                        'embroidery' => $request->input('embroidery'),
                        'remarks' => $request->input('remarks'),
                    ]);
                }
            }

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
        $sewings = SewingBlance::where('job_id', $job->id)->get();
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
        $sewings = SewingBlance::where('job_no', $job_no)->get();
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
        ->leftJoin('sewing_blances', 'jobs.id', '=', 'sewing_blances.job_id')
        ->leftJoin('shipments', 'jobs.id', '=', 'shipments.job_id')
        ->select(
            'jobs.buyer',
            DB::raw('COUNT(DISTINCT jobs.job_no) as number_of_orders'),
            DB::raw('SUM(jobs.order_quantity) as order_qty'),
            DB::raw('SUM(sewing_blances.sewing_balance) as sewing_balance'),
            DB::raw('AVG(jobs.target_smv) as avg_smv'),
            DB::raw('SUM(jobs.production_minutes) as produced_min'),
            DB::raw('SUM(sewing_blances.production_min_balance) as production_balance'),
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
            'T-Shirt', 'Polo Shirt', 'Romper', 'Sweat Shirt', 'Jacket', 'Hoodie', 'Jogger', 'Pant/Bottom', 'Cargo Pant', 'Leggings', 'Ladies/Girls Dress', 'Others'
        ];

        $buyers = Job::distinct()->pluck('buyer')->toArray();
        $summary = [];

        foreach ($items as $item) {
            foreach ($buyers as $buyer) {
                $data = DB::table('jobs')
                ->join('shipments', 'jobs.id', '=', 'shipments.job_id')
                ->join('sewing_blances', 'jobs.id', '=', 'sewing_blances.job_id')
                ->select(
                    DB::raw('COUNT(DISTINCT jobs.job_no) as number_of_orders'),
                    DB::raw('SUM(shipments.shipped_qty) as total_quantity'),
                    DB::raw('SUM(shipments.total_shipped_value) as total_value'),
                    DB::raw('SUM(sewing_blances.production_min_balance) as produced_min'),
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


    





}
