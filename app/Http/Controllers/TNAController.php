<?php

namespace App\Http\Controllers;
 use Carbon\Carbon;
use App\Models\Buyer;
use App\Models\BuyerAssign;
use App\Models\SOP;
use App\Models\TNA;
use Illuminate\Http\Request;

class TNAController extends Controller
{
     
    public function index()
    {
        $marchent_buyer_assigns = BuyerAssign::where('user_id', auth()->user()->id)->get();
        if(auth()->user()->role_id ==3){
            $tnas = TNA::whereIn('buyer_id', $marchent_buyer_assigns->pluck('buyer_id'))->latest()->get();
        } else {
            $tnas = TNA::latest()->get();
        }  
        return view('backend.library.tnas.index', compact('tnas'));
    }

    public function create()
    {
        $buyers = Buyer::all()->where('is_active', '0'); 
        
        return view('backend.library.tnas.create', compact('buyers'));
    }

   


    public function store(Request $request)
    {
        // Validate request
        $request->validate([
            'buyer_id' => 'required',
            'style' => 'required',
            'po' => 'required',
            'item' => 'required',
            'color' => 'required',
            'qty_pcs' => 'required',
            'po_receive_date' => 'required|date',
            'shipment_etd' => 'required|date',
        ]);

        // Calculate total lead time in days
        $poReceiveDate = Carbon::parse($request->po_receive_date);
        $shipmentETD = Carbon::parse($request->shipment_etd);
        $total_lead_time = $shipmentETD->diffInDays($poReceiveDate);
// dd($total_lead_time);
        // Check if shipment ETD not smale or equal than PO Receive Date example: po_receive_date = 2022-06-26, then minimum shipment_etd = 2022-07-27 



        if ($total_lead_time < 0) {
            return back()->withErrors(['shipment_etd' => 'Shipment ETD must be greater than PO Receive Date']);
        }

        // Determine SOP format based on lead time
        if ($total_lead_time <= 70) {
            $sop_format = SOP::where('lead_time', 60)->get();
        } elseif ($total_lead_time > 70 && $total_lead_time <= 84) {
            $sop_format = SOP::where('lead_time', 75)->get();
        } else {
            $sop_format = SOP::where('lead_time', 90)->get();
        }

        // Create a new TNA entry
        $tna = new TNA();
        $tna->buyer_id = $request->buyer_id;
        $tna->buyer = Buyer::find($request->buyer_id)->name;
        $tna->style = $request->style;
        $tna->po = $request->po;
        $tna->picture = $request->picture ?? '';
        $tna->item = $request->item;
        $tna->color = $request->color;
        $tna->qty_pcs = $request->qty_pcs;
        $tna->po_receive_date = $poReceiveDate;
        $tna->shipment_etd = $shipmentETD;
        $tna->total_lead_time = $total_lead_time;

        // Plan dates using SOP format days
        foreach ($sop_format as $sop) {
            $dayOffset = $sop->day;
            $particular = $sop->Perticulars;
            $planDate = $poReceiveDate->copy()->addDays($dayOffset);

            switch ($particular) {
                case 'Order Free Time':
                    $tna->order_free_time = $shipmentETD->copy()->subDays($dayOffset);
                    break;
                case 'Lab Dip Submission':
                    $tna->lab_dip_submission_plan = $planDate;
                    break;
                case 'Fabric Booking':
                    $tna->fabric_booking_plan = $planDate;
                    break;
                case 'Fit Sample Submission':
                    $tna->fit_sample_submission_plan = $planDate;
                    break;
                case 'Print Strike Off Submission':
                    $tna->print_strike_off_submission_plan = $planDate;
                    break;
                case 'Bulk Accessories Booking':
                    $tna->bulk_accessories_booking_plan = $planDate;
                    break;
                case 'Fit Comments':
                    $tna->fit_comments_plan = $planDate;
                    break;
                case 'Bulk Yarn Inhouse':
                    $tna->bulk_yarn_inhouse_plan = $planDate;
                    break;
                case 'PP Sample Submission':
                    $tna->pp_sample_submission_plan = $planDate;
                    break;
                case 'Bulk Fabric Knitting':
                    $tna->bulk_fabric_knitting_plan = $planDate;
                    break;
                case 'PP Comments Receive':
                    $tna->pp_comments_receive_plan = $planDate;
                    break;
                case 'Bulk Fabric Dyeing':
                    $tna->bulk_fabric_dyeing_plan = $planDate;
                    break;
                case 'Bulk Fabric Delivery':
                    $tna->bulk_fabric_delivery_plan = $planDate;
                    break;
                case 'PP Meeting':
                    $tna->pp_meeting_plan = $planDate;
                    break;
            }
        }

        $tna->etd_plan = $shipmentETD;
        $tna->assign_date = Carbon::now();
        $tna->assign_by = auth()->user()->name;

        $tna->save();

        return redirect()->route('tnas.index')->with('success', 'TNA created successfully');
    }

    public function show($id)
    {
        $tnas = TNA::where('id', $id)->get(); 
        // dd($tnas);
        return view('backend.library.tnas.show', compact('tnas'));
    } 

    
    public function edit(TNA $id)
    {
        $buyers = Buyer::all()->where('is_active', '0'); 
        $tnas = TNA::where('id', $id)->get(); 
        return view('backend.library.tnas.edit', compact('buyers', 'tnas'));
    } 
    public function update(Request $request, TNA $id)
    {
        dd($request->all());
    }
 
    public function destroy($id)
    {
        // dd($id);
        $tna = TNA::find($id);
        $tna->delete();
        return redirect()->route('tnas.index')->with('success', 'TNA deleted successfully');
    }

    public function updateDate(Request $request)
    {
        $tna = TNA::find($request->id);
        $task = $request->task;
        $tna->$task = $request->date;
        $tna->save();

        return response()->json(['success' => true]);
    }

    public function tnas_dashboard()
    {
        $tnas = TNA::select('buyer', 'style', 'po', 'item', 'color', 'qty_pcs', 'po_receive_date', 'shipment_etd', 'total_lead_time', 'order_free_time', 'lab_dip_submission_plan', 'lab_dip_submission_actual', 'fabric_booking_plan', 'fabric_booking_actual', 'fit_sample_submission_plan', 'fit_sample_submission_actual', 'print_strike_off_submission_plan', 'print_strike_off_submission_actual', 'bulk_accessories_booking_plan', 'bulk_accessories_booking_actual', 'fit_comments_plan', 'fit_comments_actual', 'bulk_yarn_inhouse_plan', 'bulk_yarn_inhouse_actual', 'pp_sample_submission_plan', 'pp_sample_submission_actual', 'bulk_fabric_knitting_plan', 'bulk_fabric_knitting_actual', 'pp_comments_receive_plan', 'pp_comments_receive_actual', 'bulk_fabric_dyeing_plan', 'bulk_fabric_dyeing_actual', 'bulk_fabric_delivery_plan', 'bulk_fabric_delivery_actual', 'pp_meeting_plan', 'pp_meeting_actual', 'etd_plan', 'etd_actual', 'assign_date', 'assign_by', 'remarks', 'order_close')->groupBy('buyer', 'style', 'po', 'item', 'color', 'qty_pcs', 'po_receive_date', 'shipment_etd', 'total_lead_time', 'order_free_time', 'lab_dip_submission_plan', 'lab_dip_submission_actual', 'fabric_booking_plan', 'fabric_booking_actual', 'fit_sample_submission_plan', 'fit_sample_submission_actual', 'print_strike_off_submission_plan', 'print_strike_off_submission_actual', 'bulk_accessories_booking_plan', 'bulk_accessories_booking_actual', 'fit_comments_plan', 'fit_comments_actual', 'bulk_yarn_inhouse_plan', 'bulk_yarn_inhouse_actual', 'pp_sample_submission_plan', 'pp_sample_submission_actual', 'bulk_fabric_knitting_plan', 'bulk_fabric_knitting_actual', 'pp_comments_receive_plan', 'pp_comments_receive_actual', 'bulk_fabric_dyeing_plan', 'bulk_fabric_dyeing_actual', 'bulk_fabric_delivery_plan', 'bulk_fabric_delivery_actual', 'pp_meeting_plan', 'pp_meeting_actual', 'etd_plan', 'etd_actual', 'assign_date', 'assign_by', 'remarks', 'order_close');
        $marchent_buyer_assigns = BuyerAssign::where('user_id', auth()->user()->id)->get();
        if(auth()->user()->role_id ==3){
            $tnas = $tnas->whereIn('buyer_id', $marchent_buyer_assigns->pluck('buyer_id'))->get();
        } else {
            $tnas = $tnas->get();
        }
        
        return view('backend.library.tnas.tnas_dashboard', compact('tnas'));
    }
}
