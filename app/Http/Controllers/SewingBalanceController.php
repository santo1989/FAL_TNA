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
        $request->validate([
            // Uncomment and use these validations as needed
            // 'buyer_id' => 'nullable|exists:buyers,id',
            // 'po' => 'nullable|string',
            // 'style_id' => 'nullable|exists:styles,id',
            // 'shipment_start_date' => 'nullable|date',
            // 'shipment_end_date' => 'nullable|date|after_or_equal:shipment_start_date',
        ]);

        $buyer_id_filter = $request->buyer_id;
        $style_filter = $request->style_id;
        $po_filter = $request->po;
        $shipment_start_date = $request->shipment_start_date;
        $shipment_end_date = $request->shipment_end_date;

        // Convert style ID to style name
        $style_name = null;
        if ($style_filter) {
            $style = Style::find($style_filter);
            $style_name = $style ? $style->style : null;
        }

        $jobs = Job::when($buyer_id_filter, function ($query) use ($buyer_id_filter) {
            return $query->where('buyer_id', $buyer_id_filter);
        })
            ->when($style_name, function ($query) use ($style_name) {
                return $query->where('style', $style_name);
            })
            ->when($po_filter, function ($query) use ($po_filter) {
                return $query->where('po', $po_filter);
            })
            ->when($shipment_start_date || $shipment_end_date, function ($query) use ($shipment_start_date, $shipment_end_date) {
                if ($shipment_start_date && $shipment_end_date) {
                    return $query->whereBetween('delivery_date', [$shipment_start_date, $shipment_end_date]);
                } elseif ($shipment_start_date) {
                    return $query->where('delivery_date', '>=', $shipment_start_date);
                } else {
                    return $query->where('delivery_date', '<=', $shipment_end_date);
                }
            })
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
