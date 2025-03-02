<?php

namespace App\Http\Controllers;

use App\Mail\BuyerWiseTnaSummary;
use Carbon\Carbon;
use App\Models\Buyer;
use App\Models\BuyerAssign;
use App\Models\SOP;
use App\Models\TNA;
use App\Models\TnaExplanation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

use function Symfony\Component\String\b;

class TNAController extends Controller
{

    // public function index()
    // {
    //     $marchent_buyer_assigns = BuyerAssign::where('user_id', auth()->user()->id)->get();
    //     if (auth()->user()->role_id == 3) {
    //         $tnas = TNA::where('order_close', '0')->whereIn('buyer_id', $marchent_buyer_assigns->pluck('buyer_id'))->latest()->get();
    //     } elseif (auth()->user()->role_id == 2 && $marchent_buyer_assigns->count() > 0) {
    //         $tnas = TNA::where('order_close', '0')->whereIn('buyer_id', $marchent_buyer_assigns->pluck('buyer_id'))->latest()->get();
    //     } else {
    //         $tnas = TNA::where('order_close', '0')->latest()->get();
    //     }



    //     return view('backend.library.tnas.index', compact('tnas'));
    // }

    // public function real_time_data()
    // {

    //     $marchent_buyer_assigns = BuyerAssign::where('user_id', auth()->user()->id)->get();
    //     if (auth()->user()->role_id == 3) {
    //         $tnas = TNA::where('order_close', '0')->whereIn('buyer_id', $marchent_buyer_assigns->pluck('buyer_id'))->latest()->get();
    //     } elseif (auth()->user()->role_id == 2 && $marchent_buyer_assigns->count() > 0) {
    //         $tnas = TNA::where('order_close', '0')->whereIn('buyer_id', $marchent_buyer_assigns->pluck('buyer_id'))->latest()->get();
    //     } else {
    //         $tnas = TNA::where('order_close', '0')->latest()->get();
    //     }

    //     return response()->json($tnas);
    // }



    public function index()
    {
        $user = auth()->user();
        $cacheKey = 'tnas_user_' . $user->id;

        $tnas = Cache::remember($cacheKey, now()->addHours(1), function () use ($user) {
            $marchent_buyer_assigns = BuyerAssign::where('user_id', $user->id)->get();

            if ($user->role_id == 3) {
                return TNA::where('order_close', '0')
                    ->whereIn('buyer_id', $marchent_buyer_assigns->pluck('buyer_id'))
                    ->latest()
                    ->get();
            } elseif (
                $user->role_id == 2 && $marchent_buyer_assigns->isNotEmpty()
            ) {
                return TNA::where('order_close', '0')
                    ->whereIn('buyer_id', $marchent_buyer_assigns->pluck('buyer_id'))
                    ->latest()
                    ->get();
            } else {
                return TNA::where('order_close', '0')->latest()->get();
            }
        });

        return view('backend.library.tnas.index', compact('tnas'));
    }

    public function real_time_data()
    {
        $user = auth()->user();
        $cacheKey = 'tnas_user_' . $user->id;

        $tnas = Cache::remember($cacheKey, now()->addHours(1), function () use ($user) {
            // Same query logic as index method
            $marchent_buyer_assigns = BuyerAssign::where('user_id', $user->id)->get();

            if ($user->role_id == 3) {
                return TNA::where('order_close', '0')
                    ->whereIn('buyer_id', $marchent_buyer_assigns->pluck('buyer_id'))
                    ->latest()
                    ->get();
            } elseif ($user->role_id == 2 && $marchent_buyer_assigns->isNotEmpty()) {
                return TNA::where('order_close', '0')
                    ->whereIn('buyer_id', $marchent_buyer_assigns->pluck('buyer_id'))
                    ->latest()
                    ->get();
            } else {
                return TNA::where('order_close', '0')->latest()->get();
            }
        });

        $html = view(
            'backend.library.tnas.partials.tna_rows',
            compact('tnas')
        )->render();
        return response()->json(['html' => $html]);
    }


    public function create()
    {
        $marchent_buyer_assigns = BuyerAssign::where('user_id', auth()->user()->id)->get();
        $buyerList = Buyer::all()->where('is_active', '0');
        if (auth()->user()->role_id == 3) {
            $buyers = $buyerList->whereIn('id', $marchent_buyer_assigns->pluck('buyer_id'));
        } else {
            $buyers = $buyerList;
        }

        return view('backend.library.tnas.create', compact('buyers'));
    }

    public function store(Request $request)
    {
        // If $request is an array, convert it to a Request object
        if (is_array($request)) {
            $request = new Request($request);
        }
        // dd($request->all());
        // Validate request
        // $request->validate([
        //     'buyer_id' => 'required',
        //     'style' => 'required',
        //     'po' => 'required',
        //     'item' => 'required',
        //     'qty_pcs' => 'required',
        //     'po_receive_date' => 'required|date',
        //     'shipment_etd' => 'required|date',
        //     'print_wash'=>'required',
        // ]);

        // Now you can use $request as a Request object
        $request->validate([
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
        ]);

        // Calculate total lead time in days
        $poReceiveDate = Carbon::parse($request->po_receive_date);
        $shipmentETD = Carbon::parse($request->shipment_etd);
        $total_lead_time = $shipmentETD->diffInDays($poReceiveDate);
        $printAndwash = $request->print_wash;

        DB::transaction(function () use ($request) {
            // Calculate total lead time in days
            $poReceiveDate = Carbon::parse($request->po_receive_date);
            $shipmentETD = Carbon::parse($request->shipment_etd);
            $total_lead_time = $shipmentETD->diffInDays($poReceiveDate);
            $printAndwash = $request->print_wash;

            if ($total_lead_time < 0) {
                throw new \Exception('Shipment ETD must be greater than PO Receive Date');
            }

            // Create a new TNA entry
            $tna = new TNA();
            $tna->buyer_id = $request->buyer_id;
            $tna->buyer = Buyer::find($request->buyer_id)->name;
            $tna->style = $request->style;
            $tna->po = $request->po;
            if ($request->hasFile('picture')) {
                $tna->picture = $this->uploaddocument($request->file('picture'));
            }
            $tna->item = $request->item;
            $tna->color = $request->color;
            $tna->qty_pcs = $request->qty_pcs;
            $tna->po_receive_date = $poReceiveDate;
            $tna->shipment_etd = $shipmentETD;
            $tna->total_lead_time = $total_lead_time;
            $tna->assign_date = Carbon::now();
            $tna->assign_by = auth()->user()->name;
            $tna->print_wash = $printAndwash;

            // Plan dates using SOP format
            $this->planDates($tna, $poReceiveDate, $shipmentETD, $total_lead_time, $printAndwash);

            if (
                $request->has('job_id')
                && $request->has('job_no')
            ) {
                $tna->job_id = $request->job_id;
                $tna->tnas1 = $request->job_no;
            }

            $tna->save();
        });

        // //find the tna id for edit
        // $tnas = TNA::where('buyer_id', $request->buyer_id)->where('style', $request->style)->where('po', $request->po)->where('item', $request->item)->where('qty_pcs', $request->qty_pcs)->where('po_receive_date', $poReceiveDate)->where('shipment_etd', $shipmentETD)->where('print_wash', $printAndwash)->first();
        // $buyers = Buyer::all()->where('is_active', '0');
        // // dd($tna);

        // return view('backend.library.tnas.Conform_create', compact('tnas', 'buyers'));

        return redirect()->route('tnas.index')->withMessage('TNA created successfully');
    }

    public function update(Request $request, $id)
    {
        // If $request is an array, convert it to a Request object
        if (is_array($request)) {
            $request = new Request($request);
        }

        $request->validate([
            'buyer_id' => 'required',
            'style' => 'required',
            'po' => 'required',
            'item' => 'required',
            'qty_pcs' => 'required',
            'po_receive_date' => 'required|date',
            'shipment_etd' => 'required|date',
            'remarks' => 'nullable|string',
            'print_wash' => 'nullable|string',
        ]);
        // dd($request->all());



        DB::transaction(function () use ($request) {

            // Calculate total lead time in days
            $poReceiveDate = Carbon::parse($request->po_receive_date);
            $shipmentETD = Carbon::parse($request->shipment_etd);
            $total_lead_time = $shipmentETD->diffInDays($poReceiveDate);
            $printAndwash = $request->print_wash;
            $buyer_id = $request->buyer_id;
            $style = $request->style;
            $po = $request->po;
            $item = $request->item;
            $qty_pcs = $request->qty_pcs;
            $remarks = $request->remarks;

            if ($total_lead_time < 0) {
                throw new \Exception('Shipment ETD must be greater than PO Receive Date');
            }

            // Create a new TNA entry
            $tna = TNA::find($request->tnas_id);
            // dd($tna);
            $tna->buyer_id = $buyer_id;
            $tna->buyer = Buyer::find($buyer_id)->name;
            $tna->style = $style;
            $tna->po = $po;
            if ($request->hasFile('picture')) {
                $tna->picture = $this->uploaddocument($request->file('picture'));
            }
            $tna->item = $item;
            $tna->color = $request->color;
            $tna->qty_pcs = $qty_pcs;
            $tna->po_receive_date = $poReceiveDate;
            $tna->shipment_etd = $shipmentETD;
            $tna->total_lead_time = $total_lead_time;
            $tna->assign_date = Carbon::now();
            $tna->assign_by = auth()->user()->name;
            $tna->print_wash = $printAndwash;

            // Plan dates using SOP format
            $this->planDates($tna, $poReceiveDate, $shipmentETD, $total_lead_time, $printAndwash);

            if (
                $request->has('job_id')
                && $request->has('job_no')
            ) {
                $tna->job_id = $request->job_id;
                $tna->tnas1 = $request->job_no;
            }

            // dd($tna);

            $tna->save();

            if (auth()->user()->role_id != 1 && auth()->user()->role_id != 4) {
                throw new \Exception('You are not authorized to update this TNA');
            }
        });


        return redirect()->route('tnas.index')->withMessage('TNA updated successfully');
    }



    // public function planDates($tna, $poReceiveDate, $shipmentETD, $total_lead_time, $printAndwash) 
    // {
    //     // Determine SOP format based on lead time
    //     $leadTimeMap = [
    //         'short' => ['lead_time' => 60, 'inspection_offset' => 2, 'ex_factory_offset' => 1],
    //         'medium' => ['lead_time' => 75, 'inspection_offset' => 2, 'ex_factory_offset' => 1],
    //         'long' => ['lead_time' => 90, 'inspection_offset' => 5, 'ex_factory_offset' => 1],
    //     ];

    //     $leadTimeCategory = $total_lead_time <= 70
    //         ? 'short'
    //         : ($total_lead_time <= 84 ? 'medium' : 'long');

    //     $sop_format = SOP::where('lead_time', $leadTimeMap[$leadTimeCategory]['lead_time'])->get();
    //     $final_inspection_plan = $shipmentETD->copy()->subDays($leadTimeMap[$leadTimeCategory]['inspection_offset']);
    //     $ex_factory_plan = $shipmentETD->copy()->subDays($leadTimeMap[$leadTimeCategory]['ex_factory_offset']);

    //     // Define mapping of particulars to TNA fields
    //     $fieldMapping = [
    //         'Order Free Time' => 'order_free_time',
    //         'Lab Dip Submission' => 'lab_dip_submission_plan',
    //         'Fabric Booking' => 'fabric_booking_plan',
    //         'Fit Sample Submission' => 'fit_sample_submission_plan',
    //         'Print Strike Off Submission' => 'print_strike_off_submission_plan',
    //         'Bulk Accessories Booking' => 'bulk_accessories_booking_plan',
    //         'Fit Comments' => 'fit_comments_plan',
    //         'Bulk Yarn Inhouse' => 'bulk_yarn_inhouse_plan',
    //         'Bulk Accessories Inhouse' => 'bulk_accessories_inhouse_plan',
    //         'PP Sample Submission' => 'pp_sample_submission_plan',
    //         'Bulk Fabric Knitting' => 'bulk_fabric_knitting_plan',
    //         'PP Comments Receive' => 'pp_comments_receive_plan',
    //         'Bulk Fabric Dyeing' => 'bulk_fabric_dyeing_plan',
    //         'Bulk Fabric Delivery' => 'bulk_fabric_delivery_plan',
    //         'PP Meeting' => 'pp_meeting_plan',
    //         'Fabrics and Accessories Inspection' => 'fabrics_and_accessories_inspection_plan',
    //         'Size Set Making' => 'size_set_making_plan',
    //         'Pattern Correction' => 'pattern_correction_plan',
    //         'MachinesLayoutFolderPreparation' => 'machines_layout_and_folder_preparation_plan',
    //         'Bulk Cutting Start' => 'cutting_plan',
    //         'Print/Emb. Start ' => 'print_start_plan',
    //         'Bulk Sewing Input' => 'bulk_sewing_input_plan',
    //         'Bulk Wash Start ' => 'bulk_wash_start_plan',
    //         'Bulk Finishing Start' => 'bulk_finishing_start_plan',
    //         'Bulk Cutting Close' => 'bulk_cutting_close_plan',
    //         'Print/Emb. Close ' => 'print_close_plan',
    //         'Bulk Sewing Close' => 'bulk_sewing_close_plan',
    //         'Bulk Wash Close or Finihsing Recived ' => 'bulk_wash_close_plan',
    //         'Bulk Finishing Close' => 'bulk_finishing_close_plan',
    //         'Pre-final Inspection' => 'pre_final_inspection_plan',
    //     ];

    //     // Add print and wash-specific suffixes
    //     $suffix = match($printAndwash) {
    //         'No Print and Wash' => '',
    //         'Only Print' => ' ( Only Print )',
    //         'Only Wash' => ' ( Only Wash )',
    //         default => ' ( Both Print and Wash )',
    //     };

    //     // Iterate over SOP format and set plans
    //     foreach ($sop_format as $sop) {
    //         $dayOffset = $sop->day;
    //         $particular = $sop->Perticulars . $suffix;
    //         $planDate = $poReceiveDate->copy()->addDays($dayOffset);

    //         if (isset($fieldMapping[$particular])) {
    //             $fields = (array)$fieldMapping[$particular];
    //             foreach ($fields as $field) {
    //                 $tna->$field = $particular === 'Order Free Time'
    //                 ? $shipmentETD->copy()->subDays($dayOffset)
    //                     : $planDate;
    //             }
    //         }
    //     }

    //     // Set final inspection and ex-factory plans
    //     $tna->final_inspection_plan = $final_inspection_plan;
    //     $tna->ex_factory_plan = $ex_factory_plan;
    //     $tna->etd_plan = $shipmentETD;
    // }



    // public function planDates($tna, $poReceiveDate, $shipmentETD, $total_lead_time, $printAndwash)
    // {
    //     // Determine SOP format based on lead time
    //     $sop_format = $this->getSopFormat($total_lead_time);
    //     $final_inspection_plan = $shipmentETD->copy()->subDays($this->getFinalInspectionDays($total_lead_time));
    //     $ex_factory_plan = $shipmentETD->copy()->subDays(1);

    //     // Plan dates using SOP format days
    //     foreach ($sop_format as $sop) {
    //         $dayOffset = $sop->day;
    //         $particular = $sop->Perticulars;
    //         $planDate = $poReceiveDate->copy()->addDays($dayOffset);

    //         if ($this->shouldPlanDate($particular, $printAndwash)) {
    //             $this->assignTnaPlan($tna, $particular, $planDate, $dayOffset, $shipmentETD, $printAndwash);
    //         }
    //     }

    //     $tna->final_inspection_plan = $final_inspection_plan;
    //     $tna->ex_factory_plan = $ex_factory_plan;
    // }

    // private function getSopFormat($total_lead_time)
    // {
    //     if ($total_lead_time <= 70) {
    //         return SOP::where('lead_time', 60)->get();
    //     } elseif ($total_lead_time > 70 && $total_lead_time <= 84) {
    //         return SOP::where('lead_time', 75)->get();
    //     } else {
    //         return SOP::where('lead_time', 90)->get();
    //     }
    // }

    // private function getFinalInspectionDays($total_lead_time)
    // {
    //     if ($total_lead_time > 84) {
    //         return 5;
    //     }
    //     return 2;
    // }

    // private function shouldPlanDate($particular, $printAndwash)
    // {
    //     if ($printAndwash === 'No Print and Wash') {
    //         return true;
    //     }

    //     $conditions = [
    //         'Only Print' => !str_contains($particular, 'Wash'),
    //         'Only Wash' => !str_contains($particular, 'Print'),
    //         'Both Print and Wash' => true,
    //     ];

    //     return $conditions[$printAndwash] ?? false;
    // }

    // private function assignTnaPlan($tna, $particular, $planDate, $dayOffset, $shipmentETD, $printAndwash)
    // {
    //     $mapping = [
    //         'Order Free Time' => 'order_free_time',
    //         'Lab Dip Submission' => 'lab_dip_submission_plan',
    //         'Fabric Booking' => 'fabric_booking_plan',
    //         'Fit Sample Submission' => 'fit_sample_submission_plan',
    //         'Print Strike Off Submission' => 'print_strike_off_submission_plan',
    //         'Bulk Accessories Booking' => 'bulk_accessories_booking_plan',
    //         'Fit Comments' => 'fit_comments_plan',
    //         'Bulk Yarn Inhouse' => 'bulk_yarn_inhouse_plan',
    //         'Bulk Accessories Inhouse' => 'bulk_accessories_inhouse_plan',
    //         'PP Sample Submission' => 'pp_sample_submission_plan',
    //         'Bulk Fabric Knitting' => 'bulk_fabric_knitting_plan',
    //         'PP Comments Receive' => 'pp_comments_receive_plan',
    //         'Bulk Fabric Dyeing' => 'bulk_fabric_dyeing_plan',
    //         'Bulk Fabric Delivery' => 'bulk_fabric_delivery_plan',
    //         'PP Meeting' => 'pp_meeting_plan',
    //         'Fabrics and Accessories Inspection' => 'fabrics_and_accessories_inspection_plan',
    //         'Size Set Making' => 'size_set_making_plan',
    //         'Pattern Correction' => 'pattern_correction_plan',
    //         'MachinesLayoutFolderPreparation' => 'machines_layout_and_folder_preparation_plan',
    //         'Bulk Cutting Start' => 'cutting_plan',
    //         'Print/Emb. Start' => 'print_start_plan',
    //         'Bulk Sewing Input' => 'bulk_sewing_input_plan',
    //         'Bulk Wash Start' => 'bulk_wash_start_plan',
    //         'Bulk Finishing Start' => 'bulk_finishing_start_plan',
    //         'Bulk Cutting Close' => 'bulk_cutting_close_plan',
    //         'Print/Emb. Close' => 'print_close_plan',
    //         'Bulk Sewing Close' => 'bulk_sewing_close_plan',
    //         'Bulk Wash Close or Finihsing Recived' => 'bulk_wash_close_plan',
    //         'Bulk Finishing Close' => 'bulk_finishing_close_plan',
    //         'Pre-final Inspection' => 'pre_final_inspection_plan',
    //     ];

    //     if (isset($mapping[$particular])) {
    //         $fields = (array) $mapping[$particular];
    //         foreach ($fields as $field) {
    //             $tna->$field = $planDate;
    //         }
    //     }
    // }




    // public function store(Request $request)
    // {
    //     // Validate request
    //     $request->validate([
    //         'buyer_id' => 'required',
    //         'style' => 'required',
    //         'po' => 'required',
    //         'item' => 'required', 
    //         'qty_pcs' => 'required',
    //         'po_receive_date' => 'required|date',
    //         'shipment_etd' => 'required|date',
    //     ]);

    //     // Calculate total lead time in days
    //     $poReceiveDate = Carbon::parse($request->po_receive_date);
    //     $shipmentETD = Carbon::parse($request->shipment_etd);
    //     $total_lead_time = $shipmentETD->diffInDays($poReceiveDate);
    //     // dd($total_lead_time);
    //     // Check if shipment ETD not smale or equal than PO Receive Date example: po_receive_date = 2022-06-26, then minimum shipment_etd = 2022-07-27 



    //     if ($total_lead_time < 0) {
    //         return back()->withErrors(['shipment_etd' => 'Shipment ETD must be greater than PO Receive Date']);
    //     }

    //     // Determine SOP format based on lead time
    //     if ($total_lead_time <= 70) {
    //         $sop_format = SOP::where('lead_time', 60)->get();
    //     } elseif ($total_lead_time > 70 && $total_lead_time <= 84) {
    //         $sop_format = SOP::where('lead_time', 75)->get();
    //     } else {
    //         $sop_format = SOP::where('lead_time', 90)->get();
    //     }

    //     // Create a new TNA entry
    //     $tna = new TNA();
    //     $tna->buyer_id = $request->buyer_id;
    //     $tna->buyer = Buyer::find($request->buyer_id)->name;
    //     $tna->style = $request->style;
    //     $tna->po = $request->po;
    //     if ($request->hasFile('picture')) {
    //         $tna->picture = $this->uploaddocument(request()->file('picture'));
    //     }
    //     $tna->item = $request->item;
    //     $tna->color = $request->color;
    //     $tna->qty_pcs = $request->qty_pcs;
    //     $tna->po_receive_date = $poReceiveDate;
    //     $tna->shipment_etd = $shipmentETD;
    //     $tna->total_lead_time = $total_lead_time;

    //     // Plan dates using SOP format days
    //     foreach ($sop_format as $sop) {
    //         $dayOffset = $sop->day;
    //         $particular = $sop->Perticulars;
    //         $planDate = $poReceiveDate->copy()->addDays($dayOffset);

    //         switch ($particular) {
    //             case 'Order Free Time':
    //                 $tna->order_free_time = $shipmentETD->copy()->subDays($dayOffset);
    //                 break;
    //             case 'Lab Dip Submission':
    //                 $tna->lab_dip_submission_plan = $planDate;
    //                 break;
    //             case 'Fabric Booking':
    //                 $tna->fabric_booking_plan = $planDate;
    //                 break;
    //             case 'Fit Sample Submission':
    //                 $tna->fit_sample_submission_plan = $planDate;
    //                 break;
    //             case 'Print Strike Off Submission':
    //                 $tna->print_strike_off_submission_plan = $planDate;
    //                 break;
    //             case 'Bulk Accessories Booking':
    //                 $tna->bulk_accessories_booking_plan = $planDate;
    //                 break;
    //             case 'Fit Comments':
    //                 $tna->fit_comments_plan = $planDate;
    //                 break;
    //             case 'Bulk Yarn Inhouse':
    //                 $tna->bulk_yarn_inhouse_plan = $planDate;
    //                 break;
    //             case 'PP Sample Submission':
    //                 $tna->pp_sample_submission_plan = $planDate;
    //                 break;
    //             case 'Bulk Fabric Knitting':
    //                 $tna->bulk_fabric_knitting_plan = $planDate;
    //                 break;
    //             case 'PP Comments Receive':
    //                 $tna->pp_comments_receive_plan = $planDate;
    //                 break;
    //             case 'Bulk Fabric Dyeing':
    //                 $tna->bulk_fabric_dyeing_plan = $planDate;
    //                 break;
    //             case 'Bulk Fabric Delivery':
    //                 $tna->bulk_fabric_delivery_plan = $planDate;
    //                 break;
    //             case 'PP Meeting':
    //                 $tna->pp_meeting_plan = $planDate;
    //                 break;
    //         }
    //     }

    //     $tna->etd_plan = $shipmentETD;
    //     $tna->assign_date = Carbon::now();
    //     $tna->assign_by = auth()->user()->name;

    //     $tna->save();

    //     return redirect()->route('tnas.index')->withMessage('TNA created successfully');
    // }

    public function uploaddocument($image)
    {
        $imageName = time() . '.' . $image->extension();
        $image->move(public_path('images/TNA/files/'), $imageName);
        return $imageName;
    }

    public function show($id)
    {
        $tnas = TNA::where('id', $id)->get();
        // dd($tnas);
        return view('backend.library.tnas.show', compact('tnas'));
    }


    public function edit(Request $request, $id)
    {
        $marchent_buyer_assigns = BuyerAssign::where('user_id', auth()->user()->id)->get();
        $buyerList = Buyer::all()->where('is_active', '0');
        if (auth()->user()->role_id == 3) {
            $buyers = $buyerList->whereIn('id', $marchent_buyer_assigns->pluck('buyer_id'));
        } else {
            $buyers = $buyerList;
        }
        $tnas = TNA::where('order_close', '0')->find($id);
        // dd($tnas);
        return view('backend.library.tnas.edit', compact('buyers', 'tnas'));
    }


    public function destroy($id)
    {
        // dd($id);
        if (auth()->user()->role_id == 1 || auth()->user()->role_id == 4) {
            $tna = TNA::find($id);
            $tna->delete();
            return redirect()->route('tnas.index')->withMessage('TNA deleted successfully');
        } else {
            return redirect()->route('tnas.index')->withErrors('You are not authorized to delete this TNA');
        }
    }

    public function updateDate(Request $request)
    {
        $tna = TNA::find($request->id);

        if (auth()->user()->role_id == 3) {
            $marchent_buyer_assigns = BuyerAssign::where('user_id', auth()->user()->id)->get();
            if (!$marchent_buyer_assigns->contains('buyer_id', $tna->buyer_id)) {
                return response()->json(['success' => false]);
            } else {
                $this->saveTnaExplanation($request, $tna);
                return response()->json(['success' => true]);
            }
        } elseif (auth()->user()->role_id == 1 || auth()->user()->role_id == 4) {
            $this->saveTnaExplanation($request, $tna);
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    private function saveTnaExplanation($request, $tna)
    {
        $task = $request->task;

        if ($request->date == 'N/A') {
            if ($task == 'print_strike_off_submission_actual') {
                $tna->print_strike_off_submission_actual = 'N/A';
                $tna->print_strike_off_submission_plan = 'N/A';
            }
            if ($task == 'fit_sample_submission_actual') {
                $tna->fit_sample_submission_actual = 'N/A';
                $tna->fit_sample_submission_plan = 'N/A';
            }
        } else {
            $tna->$task = $request->date;
        }

        if ($task == 'etd_actual') {
            $tna->order_close = '1';
        }

        $tna->save();

        if ($request->explanation) {
            // Save explanation and actual date to the new table
            TnaExplanation::create([
                'tna_id' => $tna->id,
                'perticulars' => $task,
                'input_by' => auth()->user()->id,
                'actual_date' => $request->date,
                'explanation' => $request->explanation
            ]);
        }
    }


    public function fetchTnasData()
    {
        $user = auth()->user();
        $buyerIds = BuyerAssign::where('user_id', $user->id)->pluck('buyer_id');

        // Check if the data is cached
        $cacheKey = 'tnas_data_user_' . $user->id;
        $query = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($user, $buyerIds) {
            $query = TNA::where('order_close', '0')
                ->orderBy('shipment_etd', 'asc');

            if ($user->role_id == 3 || ($user->role_id == 2 && $buyerIds->isNotEmpty())) {
                $query->whereIn('buyer_id', $buyerIds);
            }

            return $query->get();
        });

        return $query;
    }

    public function tnas_dashboard()
    {
        $tnas = $this->fetchTnasData();
        return view('backend.library.tnas.tnas_dashboard', compact('tnas'));
    }

    public function tnas_dashboard_update()
    {
        $tnas = $this->fetchTnasData();
        // return view('backend.library.tnas.tnas_table_body', compact('tnas'));
        return view('backend.library.tnas.tnas_dashboard', compact('tnas'));
    }




    public function tnas_close(Request $request, $id)
    {
        // dd($id);
        $tna = TNA::find($id);
        // dd($tna);
        if (auth()->user()->role_id == 1 || auth()->user()->role_id == 4) {

            $tna->order_close = '1';
            $tna->save();
            return redirect()->route('tnas.index')->withMessage('TNA closed successfully');
        } else {
            return redirect()->route('tnas.index')->withErrors('You are not authorized to close this TNA');
        }
    }



    public function tnas_open(Request $request, $id)
    {
        // dd($id);
        $tna = TNA::find($id);
        // dd($tna);
        if (auth()->user()->role_id == 1 || auth()->user()->role_id == 4) {
            if ($tna->order_close == '1') {
                $tna->order_close = '0';
                $tna->save();
                return redirect()->route('tnas.index')->withMessage('TNA opened successfully');
            } else {

                $tna->order_close = '1';
                $tna->save();
                return redirect()->route('tnas.index')->withMessage('TNA closed successfully');
            }
        } else {
            return redirect()->route('tnas.index')->withErrors('You are not authorized to close this TNA');
        }
    }

    public function archives()
    {
        $marchent_buyer_assigns = BuyerAssign::where('user_id', auth()->user()->id)->get();
        if (auth()->user()->role_id == 3) {
            $tnas = TNA::where('order_close', '1')->whereIn('buyer_id', $marchent_buyer_assigns->pluck('buyer_id'))->orderBy('updated_at', 'desc')->get();
        } elseif (auth()->user()->role_id == 2 && $marchent_buyer_assigns->count() > 0) {
            $tnas = TNA::where('order_close', '1')->whereIn('buyer_id', $marchent_buyer_assigns->pluck('buyer_id'))->orderBy('updated_at', 'desc')->get();
        } else {
            $tnas = TNA::where('order_close', '1')->orderBy('updated_at', 'desc')->get();
        }
        return view('backend.library.tnas.archives', compact('tnas'));
    }

    public function archives_dashboard()
    {
        $tnas = $this->fetcharchivesData();
        return view('backend.library.tnas.archives_dashboard', compact('tnas'));
    }

    public function archives_dashboard_update()
    {
        $tnas = $this->fetcharchivesData();
        return view('backend.library.tnas.tnas_table_body', compact('tnas'));
    }

    private function fetcharchivesData()
    {
        $tnas = TNA::where('order_close', '1');
        // ->select('id', 'buyer', 'style', 'po', 'item', 'color', 'qty_pcs', 'po_receive_date', 'shipment_etd', 'total_lead_time', 'order_free_time', 'lab_dip_submission_plan', 'lab_dip_submission_actual', 'fabric_booking_plan', 'fabric_booking_actual', 'fit_sample_submission_plan', 'fit_sample_submission_actual', 'print_strike_off_submission_plan', 'print_strike_off_submission_actual', 'bulk_accessories_booking_plan', 'bulk_accessories_booking_actual', 'fit_comments_plan', 'fit_comments_actual', 'bulk_yarn_inhouse_plan', 'bulk_yarn_inhouse_actual',
        // 'bulk_accessories_inhouse_plan',
        // 'bulk_accessories_inhouse_actual',
        //  'pp_sample_submission_plan', 'pp_sample_submission_actual', 'bulk_fabric_knitting_plan', 'bulk_fabric_knitting_actual', 'pp_comments_receive_plan', 'pp_comments_receive_actual', 'bulk_fabric_dyeing_plan', 'bulk_fabric_dyeing_actual', 'bulk_fabric_delivery_plan', 'bulk_fabric_delivery_actual', 'pp_meeting_plan', 'pp_meeting_actual', 'etd_plan', 'etd_actual', 'assign_date', 'assign_by', 'remarks', 'order_close')
        // ->groupBy('id', 'buyer', 'style', 'po', 'item', 'color', 'qty_pcs', 'po_receive_date', 'shipment_etd', 'total_lead_time', 'order_free_time', 'lab_dip_submission_plan', 'lab_dip_submission_actual', 'fabric_booking_plan', 'fabric_booking_actual', 'fit_sample_submission_plan', 'fit_sample_submission_actual', 'print_strike_off_submission_plan', 'print_strike_off_submission_actual', 'bulk_accessories_booking_plan', 'bulk_accessories_booking_actual', 'fit_comments_plan', 'fit_comments_actual', 'bulk_yarn_inhouse_plan', 'bulk_yarn_inhouse_actual',
        // 'bulk_accessories_inhouse_plan',
        // 'bulk_accessories_inhouse_actual', 'pp_sample_submission_plan', 'pp_sample_submission_actual', 'bulk_fabric_knitting_plan', 'bulk_fabric_knitting_actual', 'pp_comments_receive_plan', 'pp_comments_receive_actual', 'bulk_fabric_dyeing_plan', 'bulk_fabric_dyeing_actual', 'bulk_fabric_delivery_plan', 'bulk_fabric_delivery_actual', 'pp_meeting_plan', 'pp_meeting_actual', 'etd_plan', 'etd_actual', 'assign_date', 'assign_by', 'remarks', 'order_close');

        $marchent_buyer_assigns = BuyerAssign::where('user_id', auth()->user()->id)->get();
        if (auth()->user()->role_id == 3) {
            $tnas = $tnas->whereIn('buyer_id', $marchent_buyer_assigns->pluck('buyer_id'))->get();
        } elseif (auth()->user()->role_id == 2 && $marchent_buyer_assigns->count() > 0) {
            $tnas = $tnas->whereIn('buyer_id', $marchent_buyer_assigns->pluck('buyer_id'))->get();
        } else {
            $tnas = $tnas->get();
        }



        return $tnas;
    }

    public function edit_actual_date(Request $request, $id)
    {
        // dd($id);
        $tnas = TNA::findOrFail($id);
        // dd($tnas);
        $buyers = Buyer::all()->where('is_active', '0');
        return view('backend.library.tnas.edit_plan', compact('tnas', 'buyers'));
    }

    public function
    updateActualDate(Request $request, $id)
    {
        // Print all request data for debugging
        // dd($request->all());

        // Find the TNA record by ID
        $tna = Tna::findOrFail($id);

        // Check if the user's role is 4
        if (auth()->user()->role_id == 4 || auth()->user()->role_id == 1) {
            // Only update the actual date fields
            $actualDateFields = [
                'lab_dip_submission_actual',
                'fabric_booking_actual',
                'fit_sample_submission_actual',
                'print_strike_off_submission_actual',
                'bulk_accessories_booking_actual',
                'fit_comments_actual',
                'bulk_yarn_inhouse_actual',
                'bulk_accessories_inhouse_actual',
                'pp_sample_submission_actual',
                'bulk_fabric_knitting_actual',
                'pp_comments_receive_actual',
                'bulk_fabric_dyeing_actual',
                'bulk_fabric_delivery_actual',
                'pp_meeting_actual',
                'etd_actual'
            ];

            foreach ($actualDateFields as $field) {
                if ($request->has($field)) {
                    $tna->$field = $request->input($field);
                }
            }
        }

        // Save the TNA record
        $tna->save();

        // Redirect back with a success message
        return redirect()->route('tnas.index')->with('message', 'TNA updated successfully.');
    }



    public function copy_tna(Request $request, $id)
    {
        $marchent_buyer_assigns = BuyerAssign::where('user_id', auth()->user()->id)->get();
        $buyerList = Buyer::all()->where('is_active', '0');
        if (auth()->user()->role_id == 3) {
            $buyers = $buyerList->whereIn('id', $marchent_buyer_assigns->pluck('buyer_id'));
        } else {
            $buyers = $buyerList;
        }
        $tnas = TNA::where('order_close', '0')->find($id);
        // dd($tnas);
        return view('backend.library.tnas.copy_tna', compact('buyers', 'tnas'));
    }


    public function BuyerWiseTnaSummary()
    {
        // Get current date
        $currentDate = Carbon::now()->format('Y-m-d');
        // Retrieve the user's role and assigned buyers
        $user = auth()->user();
        $buyerIds = BuyerAssign::where('user_id', $user->id)->pluck('buyer_id');

        // Query TNAs based on the user's role and assigned buyers
        $query =
            Tna::where('order_close', '0')
            ->orderBy('shipment_etd', 'asc');

        if ($user->role_id == 3 || ($user->role_id == 2 && $buyerIds->isNotEmpty())) {
            $query->whereIn('buyer_id', $buyerIds);
        }

        // Fetch data from t_n_a_s table
        $tnaData = $query->get();


        // Process data to get counts
        $buyers = [];
        $columns = [
            'lab_dip_submission',
            'fabric_booking',
            'fit_sample_submission',
            'print_strike_off_submission',
            'bulk_accessories_booking',
            'fit_comments',
            'bulk_yarn_inhouse',
            'bulk_accessories_inhouse',
            'pp_sample_submission',
            'bulk_fabric_knitting',
            'pp_comments_receive',
            'bulk_fabric_dyeing',
            'bulk_fabric_delivery',
            'pp_meeting'
        ];

        foreach ($tnaData as $row) {
            $buyerName = $row->buyer;
            if (!isset($buyers[$buyerName])) {
                $buyers[$buyerName] = [
                    'data' => array_fill_keys($columns, 0),
                    'details' => []
                ];
            }
            foreach ($columns as $column) {
                $planColumn = $column . '_plan';
                $actualColumn = $column . '_actual';
                // Check if PlanDate is set and ActualDate is not set and PlanDate is less than or equal to current date else if requested has from and to date then check if PlanDate is set and ActualDate is not set and PlanDate is between from and to date
                // Fetch data from t_n_a_s table if any request to_date and from_date
                if (request()->has('from_date') && request()->has('to_date')) {
                    $fromDate = Carbon::parse(request()->from_date)->format('Y-m-d');
                    $toDate = Carbon::parse(request()->to_date)->format('Y-m-d');
                    if ($row->$planColumn && !$row->$actualColumn && $row->$planColumn >= $fromDate && $row->$planColumn <= $toDate) {
                        $buyers[$buyerName]['data'][$column]++;
                        // Store details with formatted PlanDate
                        $buyers[$buyerName]['details'][$column][] = [
                            'style' => $row->style,
                            'po' => $row->po,
                            'task' => $column,
                            'PlanDate' => Carbon::parse($row->$planColumn)->format('d-M-y'),
                            'shipment_etd' => Carbon::parse($row->shipment_etd)->format('d-M-y')
                        ];
                    }
                } elseif ($row->$planColumn && !$row->$actualColumn && $row->$planColumn <= $currentDate) {
                    $buyers[$buyerName]['data'][$column]++;
                    // Store details with formatted PlanDate
                    $buyers[$buyerName]['details'][$column][] = [
                        'style' => $row->style,
                        'po' => $row->po,
                        'task' => $column,
                        'PlanDate' => Carbon::parse($row->$planColumn)->format('d-M-y'),
                        'shipment_etd' => Carbon::parse($row->shipment_etd)->format('d-M-y')
                    ];
                }
            }
        }

        return view('backend.OMS.reports.buyer_wise_tna_summary', [
            'buyers' => $buyers,
            'columns' => $columns
        ]);
    }

    public function BuyerWiseFactoryTnaSummary()
    {
        // Get current date
        $currentDate = Carbon::now()->format('Y-m-d');
        // Retrieve the user's role and assigned buyers
        $user = auth()->user();
        $buyerIds = BuyerAssign::where('user_id', $user->id)->pluck('buyer_id');

        // Query TNAs based on the user's role and assigned buyers
        $query =
            Tna::where('order_close', '0')
            ->orderBy('shipment_etd', 'asc');

        if ($user->role_id == 3 || ($user->role_id == 2 && $buyerIds->isNotEmpty())) {
            $query->whereIn('buyer_id', $buyerIds);
        }

        // Fetch data from t_n_a_s table
        $tnaData = $query->get();


        // Process data to get counts
        $buyers = [];
        $columns = [
            'fabrics_and_accessories_inspection',
            'size_set_making',
            'pattern_correction',
            'machines_layout',
            'print_start',
            'bulk_sewing_input',
            'bulk_wash_start',
            'bulk_finishing_start',
            'bulk_cutting_close',
            'print_close',
            'bulk_sewing_close',
            'bulk_wash_close',
            'bulk_finishing_close',
            'pre_final_inspection',
            'final_inspection',
            'ex_factory'
        ];

        foreach ($tnaData as $row) {
            $buyerName = $row->buyer;
            if (!isset($buyers[$buyerName])) {
                $buyers[$buyerName] = [
                    'data' => array_fill_keys($columns, 0),
                    'details' => []
                ];
            }
            foreach ($columns as $column) {
                $planColumn = $column . '_plan';
                $actualColumn = $column . '_actual';
                // Check if PlanDate is set and ActualDate is not set and PlanDate is less than or equal to current date else if requested has from and to date then check if PlanDate is set and ActualDate is not set and PlanDate is between from and to date
                // Fetch data from t_n_a_s table if any request to_date and from_date
                if (request()->has('from_date') && request()->has('to_date')) {
                    $fromDate = Carbon::parse(request()->from_date)->format('Y-m-d');
                    $toDate = Carbon::parse(request()->to_date)->format('Y-m-d');
                    if ($row->$planColumn && !$row->$actualColumn && $row->$planColumn >= $fromDate && $row->$planColumn <= $toDate) {
                        $buyers[$buyerName]['data'][$column]++;
                        // Store details with formatted PlanDate
                        $buyers[$buyerName]['details'][$column][] = [
                            'style' => $row->style,
                            'po' => $row->po,
                            'task' => $column,
                            'PlanDate' => Carbon::parse($row->$planColumn)->format('d-M-y'),
                            'shipment_etd' => Carbon::parse($row->shipment_etd)->format('d-M-y')
                        ];
                    }
                } elseif ($row->$planColumn && !$row->$actualColumn && $row->$planColumn <= $currentDate) {
                    $buyers[$buyerName]['data'][$column]++;
                    // Store details with formatted PlanDate
                    $buyers[$buyerName]['details'][$column][] = [
                        'style' => $row->style,
                        'po' => $row->po,
                        'task' => $column,
                        'PlanDate' => Carbon::parse($row->$planColumn)->format('d-M-y'),
                        'shipment_etd' => Carbon::parse($row->shipment_etd)->format('d-M-y')
                    ];
                }
            }
        }

        return view('backend.OMS.reports.buyer_wise_tna_summary_factory', [
            'buyers' => $buyers,
            'columns' => $columns
        ]);
    }

    // TEX_EBO
    public function update_actual_TEX_EBO(Request $request)
    {
        // Retrieve parameters from the request
        $buyer_id = $request->input('buyer_id');
        $style = $request->input('style');
        $shipment_etd = $request->input('shipment_etd');

        // Convert shipment_etd to a Carbon instance to filter by month
        $shipmentDate = Carbon::parse($shipment_etd);

        // Fetch TNA records based on buyer_id and shipment month
        $tnas = TNA::where('buyer_id', $buyer_id)
            ->where('style', $style)
            ->whereMonth('shipment_etd', $shipmentDate->month)
            ->whereYear('shipment_etd', $shipmentDate->year)
            ->get();

        // Fetch active buyers
        $buyers = Buyer::where('is_active', '0')->get();

        // Check if $tnas is empty
        if ($tnas->isEmpty()) {
            return back()->withErrors('No TNA found for the selected buyer and shipment month.');
        }

        // Return the view with the required data
        return view('backend.library.tnas.edit_actual_TEX_EBO', compact('tnas', 'buyers'));
    }


    public function tnas_update_TEX_EBO(Request $request)
    {
        // dd($request->all());

        // Retrieve parameters from the request
        $tna_ids = $request->input('tna_id'); // Array of TNA IDs
        $actual_dates = [
            'lab_dip_submission_actual' => $request->input('lab_dip_submission_actual'),
            'fabric_booking_actual' => $request->input('fabric_booking_actual'),
            'fit_sample_submission_actual' => $request->input('fit_sample_submission_actual'),
            'print_strike_off_submission_actual' => $request->input('print_strike_off_submission_actual'),
            'bulk_accessories_booking_actual' => $request->input('bulk_accessories_booking_actual'),
            'fit_comments_actual' => $request->input('fit_comments_actual'),
            'bulk_yarn_inhouse_actual' => $request->input('bulk_yarn_inhouse_actual'),
            'bulk_accessories_inhouse_actual' => $request->input('bulk_accessories_inhouse_actual'),
            'pp_sample_submission_actual' => $request->input('pp_sample_submission_actual'),
            'pp_comments_receive_actual' => $request->input('pp_comments_receive_actual'),
        ];

        // Loop through each TNA ID and update the corresponding record
        foreach ($tna_ids as $tna_id) {
            $tna = TNA::find($tna_id);

            if ($tna) {
                // Update each field if it has a value
                foreach ($actual_dates as $field => $date) {
                    if ($date) {
                        $tna->$field = $date;
                    }
                }
                $tna->save();
            }
        }

        // Optionally redirect or return a response
        return redirect()->route('tnas.index')->withMessage('TNA records updated successfully.');
    }

    // COTTON_ROSE

    public function getStyles(Request $request)
    {

        $buyerId = $request->input('buyer_id');
        $style_lists = DB::table('t_n_a_s')
            ->where('buyer_id', $buyerId)
            ->distinct()
            ->pluck('style');
        return response()->json($style_lists);
    }

    public function update_actual_COTTON_ROSE(Request $request)
    {
        // Retrieve parameters from the request
        $buyer_id = $request->input('buyer_id');
        $style = $request->input('style');

        // Fetch TNA records based on buyer_id and shipment month
        $tnas = TNA::where('buyer_id', $buyer_id)
            ->where('style', $style)
            ->get();

        // Fetch the 1st record to get the all plan dates
        $tna_first_plan_date = TNA::where('buyer_id', $buyer_id)
            ->where('style', $style)
            ->first();

        // Fetch active buyers
        $buyers = Buyer::where('is_active', '0')->get();

        // Check if $tnas is empty
        if ($tnas->isEmpty()) {
            return back()->withErrors('No TNA found for the selected buyer');
        }

        // Return the view with the required data
        return view('backend.library.tnas.edit_actual_COTTON_ROSE', compact('tnas', 'buyers', 'tna_first_plan_date'));
    }


    public function tnas_update_COTTON_ROSE(Request $request)
    {
        // dd($request->all());

        // Retrieve parameters from the request
        $tna_ids = $request->input('tna_id'); // Array of TNA IDs
        $actual_dates = [
            'lab_dip_submission_actual' => $request->input('lab_dip_submission_actual'),
            'fabric_booking_actual' => $request->input('fabric_booking_actual'),
            'fit_sample_submission_actual' => $request->input('fit_sample_submission_actual'),
            'print_strike_off_submission_actual' => $request->input('print_strike_off_submission_actual'),
            'bulk_accessories_booking_actual' => $request->input('bulk_accessories_booking_actual'),
            'fit_comments_actual' => $request->input('fit_comments_actual'),
            'bulk_yarn_inhouse_actual' => $request->input('bulk_yarn_inhouse_actual'),
            'bulk_accessories_inhouse_actual' => $request->input('bulk_accessories_inhouse_actual'),
            'pp_sample_submission_actual' => $request->input('pp_sample_submission_actual'),
            'pp_comments_receive_actual' => $request->input('pp_comments_receive_actual'),
        ];
        // dd($actual_dates, $tna_ids);
        // Loop through each TNA ID and update the corresponding record
        foreach ($tna_ids as $tna_id) {
            $tna = TNA::find($tna_id);

            if ($tna) {
                // Update each field if it has a value
                foreach ($actual_dates as $field => $date) {
                    if ($date) {
                        $tna->$field = $date;
                    }
                }
                $tna->save();
            }
        }

        // Optionally redirect or return a response
        return redirect()->route('tnas.index')->withMessage('TNA records updated successfully.');
    }


    //update code for MD Sir,
    public function FAL_BuyerWiseTnaSummary()
    {
        // Get current date
        $currentDate = Carbon::now()->format('Y-m-d');

        // Fetch data from t_n_a_s table
        $tnaData = Tna::where('order_close', '0')
            ->orderBy('shipment_etd', 'asc') // Sort by shipment_etd in ascending order
            ->get();


        // Process data to get counts
        $buyers = [];
        $columns = [
            'lab_dip_submission',
            'fabric_booking',
            'fit_sample_submission',
            'print_strike_off_submission',
            'bulk_accessories_booking',
            'fit_comments',
            'bulk_yarn_inhouse',
            'bulk_accessories_inhouse',
            'pp_sample_submission',
            'bulk_fabric_knitting',
            'pp_comments_receive',
            'bulk_fabric_dyeing',
            'bulk_fabric_delivery',
            'pp_meeting'
        ];

        foreach ($tnaData as $row) {
            $buyerName = $row->buyer;
            if (!isset($buyers[$buyerName])) {
                $buyers[$buyerName] = [
                    'data' => array_fill_keys($columns, 0),
                    'details' => []
                ];
            }
            foreach ($columns as $column) {
                $planColumn = $column . '_plan';
                $actualColumn = $column . '_actual';
                if ($row->$planColumn && !$row->$actualColumn && $row->$planColumn <= $currentDate) {
                    $buyers[$buyerName]['data'][$column]++;
                    // Store details with formatted PlanDate
                    $buyers[$buyerName]['details'][$column][] = [
                        'style' => $row->style,
                        'po' => $row->po,
                        'task' => $column,
                        'PlanDate' => Carbon::parse($row->$planColumn)->format('d-M-y'),
                        'shipment_etd' => Carbon::parse($row->shipment_etd)->format('d-M-y')
                    ];
                }
            }
        }

        return view('backend.OMS.reports.buyer_wise_tna_summary', [
            'buyers' => $buyers,
            'columns' => $columns
        ]);
    }

    public function FAL_BuyerWiseFactoryTnaSummary()
    {
        // Get current date
        $currentDate = Carbon::now()->format('Y-m-d');

        // Fetch data from t_n_a_s table
        $tnaData = Tna::where('order_close', '0')
            ->orderBy('shipment_etd', 'asc') // Sort by shipment_etd in ascending order
            ->get();


        // Process data to get counts
        $buyers = [];
        $columns = [
            'fabrics_and_accessories_inspection',
            'size_set_making',
            'pattern_correction',
            'machines_layout',
            'print_start',
            'bulk_sewing_input',
            'bulk_wash_start',
            'bulk_finishing_start',
            'bulk_cutting_close',
            'print_close',
            'bulk_sewing_close',
            'bulk_wash_close',
            'bulk_finishing_close',
            'pre_final_inspection',
            'final_inspection',
            'ex_factory'

        ];

        foreach ($tnaData as $row) {
            $buyerName = $row->buyer;
            if (!isset($buyers[$buyerName])) {
                $buyers[$buyerName] = [
                    'data' => array_fill_keys($columns, 0),
                    'details' => []
                ];
            }
            foreach ($columns as $column) {
                $planColumn = $column . '_plan';
                $actualColumn = $column . '_actual';
                if ($row->$planColumn && !$row->$actualColumn && $row->$planColumn <= $currentDate) {
                    $buyers[$buyerName]['data'][$column]++;
                    // Store details with formatted PlanDate
                    $buyers[$buyerName]['details'][$column][] = [
                        'style' => $row->style,
                        'po' => $row->po,
                        'task' => $column,
                        'PlanDate' => Carbon::parse($row->$planColumn)->format('d-M-y'),
                        'shipment_etd' => Carbon::parse($row->shipment_etd)->format('d-M-y')
                    ];
                }
            }
        }

        return view('backend.OMS.reports.buyer_wise_tna_summary_factory', [
            'buyers' => $buyers,
            'columns' => $columns
        ]);
    }


    public function fal_tnas_dashboard()
    {
        $tnas = $this->falfetchTnasData();
        // dd($tnas);

        return view('backend.library.tnas.fal_tnas_dashboard', compact('tnas'));
    }

    public function fal_tnas_dashboard_update()
    {
        $tnas = $this->falfetchTnasData();
        // return view('backend.library.tnas.fal_tnas_table_body', compact('tnas'));

        return view('backend.library.tnas.fal_tnas_dashboard', compact('tnas'));
    }

    public function falfetchTnasData()
    {
        // $tnas = TNA::where('order_close', '0')
        //     ->select(
        //         'id',
        //         'buyer_id',
        //         'buyer',
        //         'style',
        //         'po',
        //         'item',
        //         'color',
        //         'qty_pcs',
        //         'po_receive_date',
        //         'shipment_etd',
        //         'total_lead_time',
        //         'order_free_time',
        //         'lab_dip_submission_plan',
        //         'lab_dip_submission_actual',
        //         'fabric_booking_plan',
        //         'fabric_booking_actual',
        //         'fit_sample_submission_plan',
        //         'fit_sample_submission_actual',
        //         'print_strike_off_submission_plan',
        //         'print_strike_off_submission_actual',
        //         'bulk_accessories_booking_plan',
        //         'bulk_accessories_booking_actual',
        //         'fit_comments_plan',
        //         'fit_comments_actual',
        //         'bulk_yarn_inhouse_plan',
        //         'bulk_yarn_inhouse_actual',
        //         'bulk_accessories_inhouse_plan',
        //         'bulk_accessories_inhouse_actual',
        //         'pp_sample_submission_plan',
        //         'pp_sample_submission_actual',
        //         'bulk_fabric_knitting_plan',
        //         'bulk_fabric_knitting_actual',
        //         'pp_comments_receive_plan',
        //         'pp_comments_receive_actual',
        //         'bulk_fabric_dyeing_plan',
        //         'bulk_fabric_dyeing_actual',
        //         'bulk_fabric_delivery_plan',
        //         'bulk_fabric_delivery_actual',
        //         'pp_meeting_plan',
        //         'pp_meeting_actual',
        //         'etd_plan',
        //         'etd_actual',
        //         'assign_date',
        //         'assign_by',
        //         'remarks',
        //         'order_close'
        //     )
        //     ->groupBy(
        //         'id',
        //         'buyer_id',
        //         'buyer',
        //         'style',
        //         'po',
        //         'item',
        //         'color',
        //         'qty_pcs',
        //         'po_receive_date',
        //         'shipment_etd',
        //         'total_lead_time',
        //         'order_free_time',
        //         'lab_dip_submission_plan',
        //         'lab_dip_submission_actual',
        //         'fabric_booking_plan',
        //         'fabric_booking_actual',
        //         'fit_sample_submission_plan',
        //         'fit_sample_submission_actual',
        //         'print_strike_off_submission_plan',
        //         'print_strike_off_submission_actual',
        //         'bulk_accessories_booking_plan',
        //         'bulk_accessories_booking_actual',
        //         'fit_comments_plan',
        //         'fit_comments_actual',
        //         'bulk_yarn_inhouse_plan',
        //         'bulk_yarn_inhouse_actual',
        //         'bulk_accessories_inhouse_plan',
        //         'bulk_accessories_inhouse_actual',
        //         'pp_sample_submission_plan',
        //         'pp_sample_submission_actual',
        //         'bulk_fabric_knitting_plan',
        //         'bulk_fabric_knitting_actual',
        //         'pp_comments_receive_plan',
        //         'pp_comments_receive_actual',
        //         'bulk_fabric_dyeing_plan',
        //         'bulk_fabric_dyeing_actual',
        //         'bulk_fabric_delivery_plan',
        //         'bulk_fabric_delivery_actual',
        //         'pp_meeting_plan',
        //         'pp_meeting_actual',
        //         'etd_plan',
        //         'etd_actual',
        //         'assign_date',
        //         'assign_by',
        //         'remarks',
        //         'order_close'
        //     );
        // $tnas = $tnas->orderBy('shipment_etd', 'asc')->get();

        // //  dd($tnas);
        // return $tnas;


        //find the pc ip address
        $ip = $_SERVER['REMOTE_ADDR'];
        // dd($ip); 

        // Check if the data is cached

        $cacheKey = 'tnas_fal_data_user_' . $ip;
        $query = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($ip) {
            $query = TNA::where('order_close', '0')
                ->orderBy('shipment_etd', 'asc');

            return $query->get();
        });

        // dd($query);

        return $query;
    }





    public function Cutting_Plan(Request $request)
    {
        // dd($request->all());
        // Validate request
        $request->validate([
            'tna_id' => 'required',
            'cutting_plan_date' =>
            'required|date', // Ensure it's a valid date
        ]);

        DB::transaction(function () use ($request) {
            // Calculate total lead time in days
            $cutting_Plan = $request->cutting_plan_date;

            // Create a new TNA entry
            $tna = TNA::where('id', $request->tna_id)->first();

            $tna->cutting_Plan = $cutting_Plan;
            $tna->save();
        });

        return redirect()->route('tnas.index')->withMessage('TNA Cutting Plan updated successfully');
    }

    public function Cutting_actual(Request $request)
    {
        // dd($request->all());
        // Validate request
        $request->validate([
            'tna_id' => 'required',
            'actual_date' =>
            'required|date', // Ensure it's a valid date
        ]);

        DB::transaction(function () use ($request) {
            // Calculate total lead time in days
            $cutting_actual = $request->actual_date;

            // Create a new TNA entry
            $tna = TNA::where('id', $request->tna_id)->first();
            $tna->cutting_actual = $cutting_actual;
            $tna->save();
        });

        return redirect()->route('tnas.index')->withMessage('TNA Cutting Plan updated successfully');
    }


    public function TnaSummaryReport(Request $request)
    {

        // Apply filters if provided in the request
        $buyer_id = $request->buyer_id;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $order_close = $request->order_close;
        $po_receive_date = $request->po_receive_date;

        $query = TNA::select(
            'buyer_id',
            'buyer',
            DB::raw('SUM(qty_pcs) as total_order_qty'),
            DB::raw('COUNT(DISTINCT style) as total_distinct_styles'),
            DB::raw('STRING_AGG(style, \',\') as styles'),  // Removed DISTINCT here
            DB::raw('COUNT(DISTINCT po) as total_distinct_pos'),
            DB::raw('STRING_AGG(po, \',\') as po'),  // Removed DISTINCT here
            DB::raw('COUNT(DISTINCT item) as total_distinct_items'),
            DB::raw('STRING_AGG(item, \',\') as items'),  // Removed DISTINCT here
            DB::raw('COUNT(DISTINCT shipment_etd) as total_distinct_shipment_dates'),
            DB::raw('STRING_AGG(shipment_etd, \',\') as shipment_dates')  // Removed DISTINCT here
        )
            ->where('order_close', $order_close == !null ? $order_close : '0')
            ->groupBy('buyer_id', 'buyer');





        //if any filter is applied then apply the filter on the query else return all the data 

        if ($buyer_id == !null) {
            $query->where('buyer_id', $buyer_id);
        }

        if ($start_date == !null && $end_date == !null) {
            if ($po_receive_date == !null) {
                $query->whereBetween('po_receive_date', [$start_date, $end_date]);
            } else {
                $query->whereBetween('shipment_etd', [$start_date, $end_date]);
            }
        }



        $tna_summary = $query->get();

        // dd($tna_summary);

        // Calculate total summary
        $total_summary = [
            'total_order_qty' => $tna_summary->sum('total_order_qty'),
            'total_distinct_styles' => $tna_summary->sum('total_distinct_styles'),

            'total_distinct_pos' => $tna_summary->sum('total_distinct_pos'),

            'total_distinct_items' => $tna_summary->sum('total_distinct_items'),

            'total_distinct_shipment_dates' => $tna_summary->sum('total_distinct_shipment_dates'),

        ];

        // Define the columns for the table
        $columns = [
            'total_order_qty' => 'Total Order Qty',
            'total_distinct_styles' => 'Total Styles',
            'styles' => 'Styles',
            'total_distinct_pos' => 'Total POs',
            'po' => 'POs',
            'total_distinct_items' => 'Total Items',
            'items' => 'Items',
            'total_distinct_shipment_dates' => 'Total Shipment',
            'shipment_dates' => 'Shipment Dates'
        ];

        return view('backend.library.reports.TnaSummaryReport', compact('tna_summary', 'columns', 'total_summary', 'buyer_id', 'start_date', 'end_date', 'order_close', 'po_receive_date'));
    }


    public function MailBuyerWiseTnaSummary()
    {
        $currentDate = Carbon::now()->format('Y-m-d');

        // Retrieve merchants and supervisors
        $merchants = User::where('role_id', 3)->get();
        $supervisors = User::where('role_id', 2)->get();

        // Retrieve admin and supervisor emails
        $adminEmails = User::where('role_id', 1)->pluck('email')->toArray();
        $supervisorEmails = User::where('role_id', 4)->pluck('email')->toArray();

        foreach ($merchants as $merchant) {
            // Retrieve assigned buyers for the merchant
            $buyerIds = BuyerAssign::where('user_id', $merchant->id)->pluck('buyer_id');

            if ($buyerIds->isEmpty()) {
                continue; // Skip merchant with no assigned buyers
            }

            // Query TNAs for the merchant's assigned buyers
            $tnaData = Tna::whereIn('buyer_id', $buyerIds)
                ->where('order_close', '0')
                ->orderBy('shipment_etd', 'asc')
                ->get();

            // Prepare data
            $buyers = $this->prepareTnaSummary($tnaData, $currentDate);

            // Skip if no data available
            if (empty($buyers)) {
                continue;
            }

            // Generate email content
            $emailContent = view('emails.buyer_wise_tna_summary', [
                'buyers' => $buyers['filtered'],
                'columns' => $buyers['columns']
            ])->render();

            // Send email to merchant
            $this->sendEmail($merchant->email, $emailContent, $supervisorEmails, $adminEmails);
        }

        // Handle supervisors separately
        foreach ($supervisors as $supervisor) {
            // Supervisors fetch all TNAs
            $tnaData = Tna::where('order_close', '0')->orderBy('shipment_etd', 'asc')->get();

            // Prepare data
            $buyers = $this->prepareTnaSummary($tnaData, $currentDate);

            if (empty($buyers)) {
                continue;
            }

            $emailContent = view('emails.buyer_wise_tna_summary', [
                'buyers' => $buyers['filtered'],
                'columns' => $buyers['columns']
            ])->render();

            // Send email to supervisor
            $this->sendEmail($supervisor->email, $emailContent, [], $adminEmails);
        }

        return back()->withMessages('Emails sent successfully to merchants and supervisors.');
    }

    private function prepareTnaSummary($tnaData, $currentDate)
    {
        $columns = [
            'lab_dip_submission',
            'fabric_booking',
            'fit_sample_submission',
            'print_strike_off_submission',
            'bulk_accessories_booking',
            'fit_comments',
            'bulk_yarn_inhouse',
            'bulk_accessories_inhouse',
            'pp_sample_submission',
            'bulk_fabric_knitting',
            'pp_comments_receive',
            'bulk_fabric_dyeing',
            'bulk_fabric_delivery',
            'pp_meeting'
        ];

        $buyers = [];
        foreach ($tnaData as $row) {
            $buyerName = $row->buyer;
            if (!isset($buyers[$buyerName])) {
                $buyers[$buyerName] = [
                    'data' => array_fill_keys($columns, 0),
                    'details' => []
                ];
            }
            foreach ($columns as $column) {
                $planColumn = $column . '_plan';
                $actualColumn = $column . '_actual';
                if ($row->$planColumn && !$row->$actualColumn && $row->$planColumn <= $currentDate) {
                    $buyers[$buyerName]['data'][$column]++;
                    $buyers[$buyerName]['details'][$column][] = [
                        'style' => $row->style,
                        'po' => $row->po,
                        'task' => $column,
                        'PlanDate' => Carbon::parse($row->$planColumn)->format('d-M-y'),
                        'shipment_etd' => Carbon::parse($row->shipment_etd)->format('d-M-y')
                    ];
                }
            }
        }

        $filteredBuyers = array_filter($buyers, function ($buyer) {
            return array_sum($buyer['data']) > 0;
        });

        return ['filtered' => $filteredBuyers, 'columns' => $columns];
    }

    private function sendEmail($recipient, $content, $cc = [], $bcc = [])
    {
        try {
            Mail::send([], [], function ($message) use ($recipient, $content, $cc, $bcc) {
                $message->to($recipient)
                    ->bcc($cc)
                    // ->bcc($bcc)
                    ->subject('Buyer Wise TNA Summary')
                    ->setBody($content, 'text/html');
            });
        } catch (\Exception $e) {
            Log::error('Failed to send email to ' . $recipient . ': ' . $e->getMessage());
        }
    }


    public function BuyerWiseProductionLeadTimeSummary(Request $request)
    {
        $okLeadTimeThreshold = 20;
        $user = auth()->user();
        $buyerIds = BuyerAssign::where('user_id', $user->id)->pluck('buyer_id');

        $query = Tna::latest()->orderBy('shipment_etd', 'asc');

        if ($user->role_id == 3 || ($user->role_id == 2 && $buyerIds->isNotEmpty())) {
            $query->whereIn('buyer_id', $buyerIds);
        }

        if ($request->has('from_date') && $request->has('to_date')) {
            $query->whereBetween('inspection_actual_date', [$request->from_date, $request->to_date]);
        }

        $tnaData = $query->get();
        $buyerSummary = [];
        $overallSummary = [
            'inadequate_orders' => 0,
            'adequate_orders' => 0,
            'pending_orders' => 0,
            'total_orders' => 0,
            'lead_time_total' => 0,
        ];

        foreach ($tnaData as $tna) {
            $buyerName = $tna->buyer;
            $leadTime = ($tna->pp_meeting_actual && $tna->inspection_actual_date)
                ? Carbon::parse($tna->pp_meeting_actual)->diffInDays(Carbon::parse($tna->inspection_actual_date))
                : 0;

            if (!isset($buyerSummary[$buyerName])) {
                $buyerSummary[$buyerName] = [
                    'inadequate_orders' => 0,
                    'adequate_orders' => 0,
                    'pending_orders' => 0,
                    'inadequate_details' => [],
                    'adequate_details' => [],
                    'pending_details' => [],
                    'lead_time_total' => 0,
                    'total_orders' => 0,
                ];
            }

            $buyerSummary[$buyerName]['total_orders']++;
            $buyerSummary[$buyerName]['lead_time_total'] += $leadTime;

            $orderDetails = [
                'id' => $tna->id,
                'style' => $tna->style ?? 'N/A',
                'po' => $tna->po ?? 'N/A',
                'shipment_etd' => $tna->shipment_etd ?? ' ',
                'inspection_actual_date' => $tna->inspection_actual_date ?? ' ',
                'pp_meeting_actual' => $tna->pp_meeting_actual ?? ' ',
            ];

            if ($tna->pp_meeting_actual == null || $tna->inspection_actual_date == null || $tna->pp_meeting_actual == null && $tna->inspection_actual_date == null) {
                $buyerSummary[$buyerName]['pending_orders']++;
                $buyerSummary[$buyerName]['pending_details'][] = $orderDetails;

                $overallSummary['pending_orders']++;
            } elseif ($leadTime >= $okLeadTimeThreshold) {
                $buyerSummary[$buyerName]['adequate_orders']++;
                $buyerSummary[$buyerName]['adequate_details'][] = $orderDetails;

                $overallSummary['adequate_orders']++;
            } else {
                $buyerSummary[$buyerName]['inadequate_orders']++;
                $buyerSummary[$buyerName]['inadequate_details'][] = $orderDetails;

                $overallSummary['inadequate_orders']++;
            }

            $overallSummary['total_orders']++;
            $overallSummary['lead_time_total'] += $leadTime;
        }

        foreach ($buyerSummary as &$summary) {
            $totalOrders = $summary['total_orders'];
            $summary['inadequate_percentage'] = $totalOrders > 0
                ? round(($summary['inadequate_orders'] / $totalOrders) * 100, 2)
                : 0;
            $summary['adequate_percentage'] = $totalOrders > 0
                ? round(($summary['adequate_orders'] / $totalOrders) * 100, 2)
                : 0;
            $summary['average_lead_time'] = $totalOrders > 0
                ? round($summary['lead_time_total'] / $totalOrders, 2)
                : 0;
        }

        $overallSummary['inadequate_percentage'] = $overallSummary['total_orders'] > 0
            ? round(($overallSummary['inadequate_orders'] / $overallSummary['total_orders']) * 100, 2)
            : 0;
        $overallSummary['adequate_percentage'] = $overallSummary['total_orders'] > 0
            ? round(($overallSummary['adequate_orders'] / $overallSummary['total_orders']) * 100, 2)
            : 0;
        $overallSummary['average_lead_time'] = $overallSummary['total_orders'] > 0
            ? round($overallSummary['lead_time_total'] / $overallSummary['total_orders'], 2)
            : 0;

        $isPlanningDepartment = in_array($user->role_id, [
            1,
            4,
            10005
        ]);


        // dd($buyerSummary, $overallSummary, $request->from_date, $request->to_date, $isPlanningDepartment);

        return view('backend.OMS.reports.buyer_wise_production_lead_time', [
            'buyerSummary' => $buyerSummary,
            'overallSummary' => $overallSummary,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'isPlanningDepartment' => $isPlanningDepartment,
        ]);
    }

    public function updateTaskDetails(Request $request)
    {
        $updates = $request->input('updates', []);
        foreach ($updates as $update) {
            DB::table('t_n_a_s')->where('id', $update['id'])->update([
                'inspection_actual_date' => $update['inspection_actual_date'] ?? null,
                'pp_meeting_actual' => $update['pp_meeting_actual'] ?? null,
            ]);
        }
        return response()->json(['message' => 'Updates saved successfully!'], 200);
    }

    public function BuyerWiseOnTimeShipmentSummary(Request $request)
    {
        $user = auth()->user();
        $buyerIds = BuyerAssign::where('user_id', $user->id)->pluck('buyer_id');

        $query = Tna::latest()->orderBy('shipment_etd', 'asc');

        if ($user->role_id == 3 || ($user->role_id == 2 && $buyerIds->isNotEmpty())) {
            $query->whereIn('buyer_id', $buyerIds);
        }

        if ($request->has('from_date') && $request->has('to_date')) {
            $query = Tna::latest()
                ->orderBy('shipment_etd', 'asc')->whereBetween('shipment_actual_date', [$request->from_date, $request->to_date]);
        }

        $tnaData = $query->get();
        $buyerSummary = [];
        $overallSummary = [
            'on_time_orders' => 0,
            'late_orders' => 0,
            'pending_orders' => 0,
            'total_orders' => 0,
        ];

        foreach ($tnaData as $tna) {
            $buyerName = $tna->buyer;
            $shipmentDifference = $tna->shipment_actual_date
                ? Carbon::parse($tna->etd_plan)->diffInDays(Carbon::parse($tna->shipment_actual_date), false)
                : null;

            if (!isset($buyerSummary[$buyerName])) {
                $buyerSummary[$buyerName] = [
                    'on_time_orders' => 0,
                    'late_orders' => 0,
                    'pending_orders' => 0,
                    'on_time_details' => [],
                    'late_details' => [],
                    'pending_details' => [],
                    'total_orders' => 0,
                ];
            }

            $buyerSummary[$buyerName]['total_orders']++;

            $orderDetails = [
                'id' => $tna->id,
                'style' => $tna->style ?? 'N/A',
                'po' => $tna->po ?? 'N/A',
                'shipment_etd' => $tna->shipment_etd ?? ' ',
                'shipment_actual_date' => $tna->shipment_actual_date ?? ' ',
            ];

            if ($shipmentDifference !== null && $shipmentDifference <= 0) {
                $buyerSummary[$buyerName]['on_time_orders']++;
                $buyerSummary[$buyerName]['on_time_details'][] = $orderDetails;
            } elseif ($shipmentDifference == null) {
                $buyerSummary[$buyerName]['pending_orders']++;
                $buyerSummary[$buyerName]['pending_details'][] = $orderDetails;
            } else {
                $buyerSummary[$buyerName]['late_orders']++;
                $buyerSummary[$buyerName]['late_details'][] = $orderDetails;
            }

            $overallSummary['total_orders']++;
            if ($shipmentDifference !== null && $shipmentDifference <= 0) {
                $overallSummary['on_time_orders']++;
            } elseif ($shipmentDifference == null) {
                $overallSummary['pending_orders']++;
            } else {
                $overallSummary['late_orders']++;
            }
        }

        foreach ($buyerSummary as &$summary) {
            $totalOrders = $summary['total_orders'];
            $summary['on_time_percentage'] = $totalOrders > 0
                ? round(($summary['on_time_orders'] / $totalOrders) * 100, 2)
                : 0;
            $summary['pending_percentage'] = $totalOrders > 0
                ? round(($summary['pending_orders'] / $totalOrders) * 100, 2)
                : 0;
            $summary['late_percentage'] = $totalOrders > 0
                ? round(($summary['late_orders'] / $totalOrders) * 100, 2)
                : 0;
        }

        $overallSummary['on_time_percentage'] = $overallSummary['total_orders'] > 0
            ? round(($overallSummary['on_time_orders'] / $overallSummary['total_orders']) * 100, 2)
            : 0;
        $overallSummary['late_percentage'] = $overallSummary['total_orders'] > 0
            ? round(($overallSummary['late_orders'] / $overallSummary['total_orders']) * 100, 2)
            : 0;
        $overallSummary['pending_percentage'] = $overallSummary['total_orders'] > 0
            ? round(($overallSummary['pending_orders'] / $overallSummary['total_orders']) * 100, 2)
            : 0;

        $isPlanningDepartment = in_array($user->role_id, [1, 4]);

        // dd($buyerSummary, $overallSummary);

        return view('backend.OMS.reports.buyer_wise_ontime_shipment', [
            'buyerSummary' => $buyerSummary,
            'overallSummary' => $overallSummary,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'isPlanningDepartment' => $isPlanningDepartment,
        ]);
    }

    public function updateShipmentActualDates(Request $request)
    {
        $user = auth()->user();

        // Check if the user is authorized to make the updates
        if (!in_array($user->role_id, [1, 4])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $updates = $request->input('updates', []);

        // Change date format to Y-m-d before saving to database
        foreach ($updates as $update) {
            // If shipment_actual_date is empty, skip the record and continue to the next one
            if (empty($update['shipment_actual_date'])) {
                continue;
            }

            // Format the shipment_actual_date to Y-m-d
            $update['shipment_actual_date'] = Carbon::parse($update['shipment_actual_date'])->format('Y-m-d');
        }

        // Loop through updates and update the respective fields in the database
        foreach ($updates as $update) {
            // First, update the shipment_actual_date
            DB::table('t_n_a_s')->where('id', $update['id'])->update([
                'shipment_actual_date' => $update['shipment_actual_date'] ?? null,
            ]);

            // Then, update the etd_actual and set order_close to 1
            DB::table('t_n_a_s')->where('id', $update['id'])->update([
                'etd_actual' => $update['shipment_actual_date'] ?? null,  // Same date as shipment_actual_date
                //if shipment_actual_date is not empty then set order_close to 1
                'order_close' => $update['shipment_actual_date'] ? 1 : 0,
            ]);
        }

        return response()->json(['message' => 'Updates saved successfully!'], 200);
    }

    // TNA factory version 2 


    public function planDates($tna, $poReceiveDate, $shipmentETD, $total_lead_time, $printAndwash)
    {
        // dd($tna, $poReceiveDate, $shipmentETD, $total_lead_time, $printAndwash);
        // Determine SOP format based on lead time
        if ($total_lead_time <= 70) {
            $sop_format = SOP::where('lead_time', 60)->get();
            $final_inspection_plan = $shipmentETD->copy()->subDays(2);
            $ex_factory_plan = $shipmentETD->copy()->subDays(1);
        } elseif ($total_lead_time > 70 && $total_lead_time <= 84) {
            $sop_format = SOP::where('lead_time', 75)->get();
            $final_inspection_plan = $shipmentETD->copy()->subDays(2);
            $ex_factory_plan = $shipmentETD->copy()->subDays(1);
        } else {
            $sop_format = SOP::where('lead_time', 90)->get();
            $final_inspection_plan = $shipmentETD->copy()->subDays(5);
            $ex_factory_plan = $shipmentETD->copy()->subDays(1);
        }

        // Plan dates using SOP format days
        foreach ($sop_format as $sop) {
            $dayOffset = $sop->day;
            $particular = $sop->Perticulars;
            $planDate = $poReceiveDate->copy()->addDays($dayOffset);

            if ($printAndwash == null || $printAndwash == 'No Print and Wash') {

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
                        // new start
                    case 'Bulk Accessories Inhouse':
                        $tna->bulk_accessories_inhouse_plan = $planDate;
                        break;
                        // new end
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
                        //factory data
                    case 'Fabrics and Accessories Inspection':
                        $tna->fabrics_and_accessories_inspection_plan = $planDate;
                        break;
                    case 'Size Set Making':
                        $tna->size_set_making_plan = $planDate;
                        break;
                    case 'Pattern Correction':
                        $tna->pattern_correction_plan = $planDate;
                        break;
                    case 'Bulk Cutting Start':
                        $tna->cutting_plan = $planDate;
                        break;
                    case 'MC Layout and Folder Pre ':
                        $tna->machines_layout_plan = $planDate;
                        break;

                    case 'Print/Emb. Start':
                        $tna->print_start_plan = $planDate;
                        break;
                    case 'Bulk Sewing Input':
                        $tna->bulk_sewing_input_plan = $planDate;
                        break;
                    case 'Bulk Wash Start':
                        $tna->bulk_wash_start_plan = $planDate;
                        break;
                    case 'Bulk Finishing Start':
                        $tna->bulk_finishing_start_plan = $planDate;
                        break;
                    case 'Bulk Cutting Close':
                        $tna->bulk_cutting_close_plan = $planDate;
                        break;
                    case 'Print/Emb. Close':
                        $tna->print_close_plan = $planDate;
                        break;
                    case 'Bulk Sewing Close':
                        $tna->bulk_sewing_close_plan = $planDate;
                        break;
                    case 'Bulk Wash Close or Finihsing Recived':
                        $tna->bulk_wash_close_plan = $planDate;
                        break;
                    case 'Bulk Finishing Close':
                        $tna->bulk_finishing_close_plan = $planDate;
                        break;
                    case 'Pre-final Inspection':
                        $tna->pre_final_inspection_plan = $planDate;
                        break;

                    default:
                        break;
                }


                $tna->final_inspection_plan = $final_inspection_plan;

                $tna->ex_factory_plan = $ex_factory_plan;
            } elseif ($printAndwash == 'Only Print') {
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
                        // new start
                    case 'Bulk Accessories Inhouse':
                        $tna->bulk_accessories_inhouse_plan = $planDate;
                        break;
                        // new end
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
                        //factory data
                    case 'Fabrics and Accessories Inspection ( Only Print )':
                        $tna->fabrics_and_accessories_inspection_plan = $planDate;
                        break;
                    case 'Size Set Making ( Only Print )':
                        $tna->size_set_making_plan = $planDate;
                        break;
                    case 'Pattern Correction ( Only Print )':
                        $tna->pattern_correction_plan = $planDate;
                        break;
                    case 'Bulk Cutting Start ( Only Print )':
                        $tna->cutting_plan = $planDate;
                        break;
                    case 'MC Layout and Folder Pre  ( Only Print )':
                        $tna->machines_layout_plan = $planDate;
                        break;

                    case 'Print/Emb. Start ( Only Print )':
                        $tna->print_start_plan = $planDate;
                        break;
                    case 'Bulk Sewing Input ( Only Print )':
                        $tna->bulk_sewing_input_plan = $planDate;
                        break;
                        // case 'Bulk Wash Start':
                        //     $tna->bulk_wash_start_plan = $planDate;
                        //     break;
                    case 'Bulk Finishing Start ( Only Print )':
                        $tna->bulk_finishing_start_plan = $planDate;
                        break;
                    case 'Bulk Cutting Close ( Only Print )':
                        $tna->bulk_cutting_close_plan = $planDate;
                        break;
                    case 'Print/Emb. Close ( Only Print )':
                        $tna->print_close_plan = $planDate;
                        break;
                    case 'Bulk Sewing Close ( Only Print )':
                        $tna->bulk_sewing_close_plan = $planDate;
                        break;
                        // case 'Bulk Wash Close or Finihsing Recived':
                        //     $tna->bulk_wash_close_plan = $planDate;
                        //     $tna->finishing_received_plan = $planDate;
                        //     break;
                    case 'Bulk Finishing Close ( Only Print )':
                        $tna->bulk_finishing_close_plan = $planDate;
                        break;
                    case 'Pre-final Inspection ( Only Print )':
                        $tna->pre_final_inspection_plan = $planDate;
                        break;

                    default:
                        break;
                }


                $tna->final_inspection_plan = $final_inspection_plan;

                $tna->ex_factory_plan = $ex_factory_plan;
            } elseif ($printAndwash == 'Only Wash') {
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
                        // new start
                    case 'Bulk Accessories Inhouse':
                        $tna->bulk_accessories_inhouse_plan = $planDate;
                        break;
                        // new end
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
                        //factory data
                    case 'Fabrics and Accessories Inspection ( Only Wash )':
                        $tna->fabrics_and_accessories_inspection_plan = $planDate;
                        break;
                    case 'Size Set Making ( Only Wash )':
                        $tna->size_set_making_plan = $planDate;
                        break;
                    case 'Pattern Correction ( Only Wash )':
                        $tna->pattern_correction_plan = $planDate;
                        break;
                    case 'Bulk Cutting Start ( Only Wash )':
                        $tna->cutting_plan = $planDate;
                        break;
                    case 'MC Layout and Folder Pre  ( Only Wash )':
                        $tna->machines_layout_plan = $planDate;
                        break;

                        // case 'Print/Emb. Start':
                        //     $tna->print_start_plan = $planDate;
                        //     break;
                    case 'Bulk Sewing Input ( Only Wash )':
                        $tna->bulk_sewing_input_plan = $planDate;
                        break;
                    case 'Bulk Wash Start ( Only Wash )':
                        $tna->bulk_wash_start_plan = $planDate;
                        break;
                    case 'Bulk Finishing Start ( Only Wash )':
                        $tna->bulk_finishing_start_plan = $planDate;
                        break;
                    case 'Bulk Cutting Close ( Only Wash )':
                        $tna->bulk_cutting_close_plan = $planDate;
                        break;
                        // case 'Print/Emb. Close':
                        //    $tna->print_close_plan = $planDate;
                        //     break;
                    case 'Bulk Sewing Close ( Only Wash )':
                        $tna->bulk_sewing_close_plan = $planDate;
                        break;
                    case 'Bulk Wash Close or Finihsing Recived ( Only Wash )':
                        $tna->bulk_wash_close_plan = $planDate;
                        break;
                    case 'Bulk Finishing Close ( Only Wash )':
                        $tna->bulk_finishing_close_plan = $planDate;
                        break;
                    case 'Pre-final Inspection ( Only Wash )':
                        $tna->pre_final_inspection_plan = $planDate;
                        break;

                    default:
                        break;
                }


                $tna->final_inspection_plan = $final_inspection_plan;

                $tna->ex_factory_plan = $ex_factory_plan;
            } else {
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
                        // new start
                    case 'Bulk Accessories Inhouse':
                        $tna->bulk_accessories_inhouse_plan = $planDate;
                        break;
                        // new end
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
                        //factory data
                    case 'Fabrics and Accessories Inspection ( Both Print and Wash )':
                        $tna->fabrics_and_accessories_inspection_plan = $planDate;
                        break;
                    case 'Size Set Making ( Both Print and Wash )':
                        $tna->size_set_making_plan = $planDate;
                        break;
                    case 'Pattern Correction ( Both Print and Wash )':
                        $tna->pattern_correction_plan = $planDate;
                        break;
                    case 'Bulk Cutting Start ( Both Print and Wash )':
                        $tna->cutting_plan = $planDate;
                        break;
                    case 'MC Layout and Folder Pre  ( Both Print and Wash )':
                        $tna->machines_layout_plan = $planDate;
                        break;

                    case 'Print/Emb. Start ( Both Print and Wash )':
                        $tna->print_start_plan = $planDate;
                        break;
                    case 'Bulk Sewing Input ( Both Print and Wash )':
                        $tna->bulk_sewing_input_plan = $planDate;
                        break;
                    case 'Bulk Wash Start ( Both Print and Wash )':
                        $tna->bulk_wash_start_plan = $planDate;
                        break;
                    case 'Bulk Finishing Start ( Both Print and Wash )':
                        $tna->bulk_finishing_start_plan = $planDate;
                        break;
                    case 'Bulk Cutting Close ( Both Print and Wash )':
                        $tna->bulk_cutting_close_plan = $planDate;
                        break;
                    case 'Print/Emb. Close ( Both Print and Wash )':
                        $tna->print_close_plan = $planDate;
                        break;
                    case 'Bulk Sewing Close ( Both Print and Wash )':
                        $tna->bulk_sewing_close_plan = $planDate;
                        break;
                    case 'Bulk Wash Close or Finihsing Recived ( Both Print and Wash )':
                        $tna->bulk_wash_close_plan = $planDate;
                        break;
                    case 'Bulk Finishing Close ( Both Print and Wash )':
                        $tna->bulk_finishing_close_plan = $planDate;
                        break;
                    case 'Pre-final Inspection ( Both Print and Wash )':
                        $tna->pre_final_inspection_plan = $planDate;
                        break;

                    default:
                        break;
                }


                $tna->final_inspection_plan = $final_inspection_plan;

                $tna->ex_factory_plan = $ex_factory_plan;
            }
        }

        $tna->etd_plan = $shipmentETD;

        // dd($tna);

        return $tna;
    }

    // public function testtnas_dashboard()
    // {
    //     $tnas = TNA::where('order_close', '0')
    //         ->orderBy('shipment_etd', 'asc')
    //         ->get();

    //     return response()->json($tnas);
    // }

    //tna_dashboard 2 with defualt buyer filtering, pagination and search, and sorting, calculation of lead time and order free time, total quantity and buyer assign wise tna fields showing, and export to excel using laravel facade view

    public function tnas_dashboard_new(Request $request)
    {
        $user = auth()->user();
        $buyerIds = BuyerAssign::where('user_id', $user->id)->pluck('buyer_id');



        $query = TNA::query()->where('order_close', '0');

        // Default buyer filtering
        if ($user->role_id == 3 || ($user->role_id == 2 && $buyerIds->isNotEmpty())) {
            $query->whereIn('buyer_id', $buyerIds);
        }

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('po', 'like', "%$search%")
                    ->orWhere('style', 'like', "%$search%")
                    ->orWhere('buyer', 'like', "%$search%")
                    ->orWhere('item', 'like', "%$search%");
            });
        }

        // Buyer filter and if selected 'All' then show all
        if ($request->filled('buyer') && $request->buyer != 'all') {
            $query->where('buyer', $request->buyer);
        }

        // Sorting
        $sortColumn = $request->get('sort', 'shipment_etd');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortColumn, $sortDirection);

        // Calculations
        $totalQty = $query->sum('qty_pcs');
        $totalLeadTime = $query->avg('total_lead_time');
        $avgOrderFreeTime =
            $query->whereNotNull('pp_meeting_actual')
            ->whereNotNull('shipment_etd')
            ->avg(DB::raw("DATEDIFF(DAY, pp_meeting_actual, shipment_etd)"));

        // Pagination
        $tnas = $query->paginate(25);



        // Get unique buyers for filter buttons
        $buyerList = TNA::where('order_close', '0')->when(!in_array($user->role_id, [1, 2, 4]), function ($q) use ($buyerIds) {
            $q->whereIn('buyer_id', $buyerIds);
        })
            ->distinct('buyer')
            ->pluck('buyer');


        return view('backend.library.tnas.tnas_dashboard_new', compact(
            'tnas',
            'totalQty',
            'totalLeadTime',
            'avgOrderFreeTime',
            'buyerList'
        ));
    }

    public function exportTnasExcel(Request $request)
    {
        // dd($request->all());
        $user = auth()->user();
        $buyerIds = BuyerAssign::where('user_id', $user->id)->pluck('buyer_id');

        $query = TNA::query()->where('order_close', '0');

        // Apply same filters as dashboard
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('po', 'like', "%$search%")
                    ->orWhere('style', 'like', "%$search%")
                    ->orWhere('buyer', 'like', "%$search%")
                    ->orWhere('item', 'like', "%$search%");
            });
        }

        // Buyer filter and if selected 'All' then show all
        if ($request->filled('buyer') && $request->buyer != 'all') {
            $query->where('buyer', $request->buyer);
        }

        $tnas = $query->get();

        // dd($tnas);

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="tna_export_' . date('Ymd_His') . '.xls"',
        ];

        // resources\views\ExcelExports\tnasExcel.blad.php
        return response()
            ->view('ExcelExports.tnasExcel', compact('tnas'))
            ->withHeaders($headers);
    }
}
