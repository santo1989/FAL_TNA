<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Buyer;
use App\Models\BuyerAssign;
use App\Models\SOP;
use App\Models\TNA;
use App\Models\TnaExplanation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TNAController extends Controller
{

    public function index()
    {
        $marchent_buyer_assigns = BuyerAssign::where('user_id', auth()->user()->id)->get();
        if (auth()->user()->role_id == 3) {
            $tnas = TNA::where('order_close', '0')->whereIn('buyer_id', $marchent_buyer_assigns->pluck('buyer_id'))->latest()->get();
        } elseif (auth()->user()->role_id == 2 && $marchent_buyer_assigns->count() > 0) {
            $tnas = TNA::where('order_close', '0')->whereIn('buyer_id', $marchent_buyer_assigns->pluck('buyer_id'))->latest()->get();
        } else {
            $tnas = TNA::where('order_close', '0')->latest()->get();
        }

        return view('backend.library.tnas.index', compact('tnas'));
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
        // Validate request
        $request->validate([
            'buyer_id' => 'required',
            'style' => 'required',
            'po' => 'required',
            'item' => 'required',
            'qty_pcs' => 'required',
            'po_receive_date' => 'required|date',
            'shipment_etd' => 'required|date',
        ]);

        DB::transaction(function () use ($request) {
            // Calculate total lead time in days
            $poReceiveDate = Carbon::parse($request->po_receive_date);
            $shipmentETD = Carbon::parse($request->shipment_etd);
            $total_lead_time = $shipmentETD->diffInDays($poReceiveDate);

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

            // Plan dates using SOP format
            $this->planDates($tna, $poReceiveDate, $shipmentETD, $total_lead_time);

            $tna->save();
        });

        return redirect()->route('tnas.index')->withMessage('TNA created successfully');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'buyer_id' => 'required',
            'style' => 'required',
            'po' => 'required',
            'item' => 'required',
            'qty_pcs' => 'required',
            'po_receive_date' => 'required|date',
            'shipment_etd' => 'required|date',
        ]);

        DB::transaction(function () use ($request, $id) {
            $tna = TNA::find($id);
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

            // Calculate total lead time if po_receive_date or shipment_etd is changed
            if ($request->po_receive_date != $tna->po_receive_date || $request->shipment_etd != $tna->shipment_etd) {
                $poReceiveDate = Carbon::parse($request->po_receive_date);
                $shipmentETD = Carbon::parse($request->shipment_etd);
                $total_lead_time = $shipmentETD->diffInDays($poReceiveDate);

                if ($total_lead_time < 0) {
                    throw new \Exception('Shipment ETD must be greater than PO Receive Date');
                }

                $tna->po_receive_date = $poReceiveDate;
                $tna->shipment_etd = $shipmentETD;
                $tna->total_lead_time = $total_lead_time;

                // Plan dates using SOP format
                $this->planDates($tna, $poReceiveDate, $shipmentETD, $total_lead_time);
            }

            $tna->save();

            if (auth()->user()->role_id != 1 && auth()->user()->role_id != 4) {
                throw new \Exception('You are not authorized to update this TNA');
            }
        });

        return redirect()->route('tnas.index')->withMessage('TNA updated successfully');
    }


    public function planDates($tna, $poReceiveDate, $shipmentETD, $total_lead_time)
    {
        // Determine SOP format based on lead time
        if ($total_lead_time <= 70) {
            $sop_format = SOP::where('lead_time', 60)->get();
        } elseif ($total_lead_time > 70 && $total_lead_time <= 84) {
            $sop_format = SOP::where('lead_time', 75)->get();
        } else {
            $sop_format = SOP::where('lead_time', 90)->get();
        }

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
    }
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
    // public function update(Request $request,  $id)
    // {
    //     dd($request->all());
    //     // Validate request
    //     $request->validate([
    //         'buyer_id' => 'required',
    //         'style' => 'required',
    //         'po' => 'required',
    //         'item' => 'required',
    //         'color' => 'required',
    //         'qty_pcs' => 'required',
    //     ]);

    //     $tna = TNA::find($id);
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

    //     $tna->save();

    //     if (auth()->user()->role_id == 1 || auth()->user()->role_id == 4) {
    //          //"po_receive_date", "shipment_etd" change then total_lead_time will be change and update plan date



    //     } else {
    //         return redirect()->route('tnas.index')->withErrors('You are not authorized to update this TNA');
    //     }
    //     return redirect()->route('tnas.index')->withMessage('TNA updated successfully');
    // }

    public function destroy($id)
    {
        // dd($id);
        if (auth()->user()->role_id == 1) {
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
        } elseif (auth()->user()->role_id == 1) {
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
            $tna->print_strike_off_submission_actual = 'N/A';
            $tna->print_strike_off_submission_plan = 'N/A';
        } else {
            $tna->$task = $request->date;
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


    public function tnas_dashboard()
    {
        $tnas = $this->fetchTnasData();
        return view('backend.library.tnas.tnas_dashboard', compact('tnas'));
    }

    public function tnas_dashboard_update()
    {
        $tnas = $this->fetchTnasData();
        return view('backend.library.tnas.tnas_table_body', compact('tnas'));
    }

    private function fetchTnasData()
    {
        $tnas = TNA::where('order_close', '0')
            ->select('id', 'buyer', 'style', 'po', 'item', 'color', 'qty_pcs', 'po_receive_date', 'shipment_etd', 'total_lead_time', 'order_free_time', 'lab_dip_submission_plan', 'lab_dip_submission_actual', 'fabric_booking_plan', 'fabric_booking_actual', 'fit_sample_submission_plan', 'fit_sample_submission_actual', 'print_strike_off_submission_plan', 'print_strike_off_submission_actual', 'bulk_accessories_booking_plan', 'bulk_accessories_booking_actual', 'fit_comments_plan', 'fit_comments_actual', 'bulk_yarn_inhouse_plan', 'bulk_yarn_inhouse_actual', 'pp_sample_submission_plan', 'pp_sample_submission_actual', 'bulk_fabric_knitting_plan', 'bulk_fabric_knitting_actual', 'pp_comments_receive_plan', 'pp_comments_receive_actual', 'bulk_fabric_dyeing_plan', 'bulk_fabric_dyeing_actual', 'bulk_fabric_delivery_plan', 'bulk_fabric_delivery_actual', 'pp_meeting_plan', 'pp_meeting_actual', 'etd_plan', 'etd_actual', 'assign_date', 'assign_by', 'remarks', 'order_close')
            ->groupBy('id', 'buyer', 'style', 'po', 'item', 'color', 'qty_pcs', 'po_receive_date', 'shipment_etd', 'total_lead_time', 'order_free_time', 'lab_dip_submission_plan', 'lab_dip_submission_actual', 'fabric_booking_plan', 'fabric_booking_actual', 'fit_sample_submission_plan', 'fit_sample_submission_actual', 'print_strike_off_submission_plan', 'print_strike_off_submission_actual', 'bulk_accessories_booking_plan', 'bulk_accessories_booking_actual', 'fit_comments_plan', 'fit_comments_actual', 'bulk_yarn_inhouse_plan', 'bulk_yarn_inhouse_actual', 'pp_sample_submission_plan', 'pp_sample_submission_actual', 'bulk_fabric_knitting_plan', 'bulk_fabric_knitting_actual', 'pp_comments_receive_plan', 'pp_comments_receive_actual', 'bulk_fabric_dyeing_plan', 'bulk_fabric_dyeing_actual', 'bulk_fabric_delivery_plan', 'bulk_fabric_delivery_actual', 'pp_meeting_plan', 'pp_meeting_actual', 'etd_plan', 'etd_actual', 'assign_date', 'assign_by', 'remarks', 'order_close');

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

    public function archives()
    {
        $marchent_buyer_assigns = BuyerAssign::where('user_id', auth()->user()->id)->get();
        if (auth()->user()->role_id == 3) {
            $tnas = TNA::where('order_close', '1')->whereIn('buyer_id', $marchent_buyer_assigns->pluck('buyer_id'))->latest()->get();
        } elseif (auth()->user()->role_id == 2 && $marchent_buyer_assigns->count() > 0) {
            $tnas = TNA::where('order_close', '1')->whereIn('buyer_id', $marchent_buyer_assigns->pluck('buyer_id'))->latest()->get();
        } else {
            $tnas = TNA::where('order_close', '1')->latest()->get();
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
        $tnas = TNA::where('order_close', '1')
            ->select('id', 'buyer', 'style', 'po', 'item', 'color', 'qty_pcs', 'po_receive_date', 'shipment_etd', 'total_lead_time', 'order_free_time', 'lab_dip_submission_plan', 'lab_dip_submission_actual', 'fabric_booking_plan', 'fabric_booking_actual', 'fit_sample_submission_plan', 'fit_sample_submission_actual', 'print_strike_off_submission_plan', 'print_strike_off_submission_actual', 'bulk_accessories_booking_plan', 'bulk_accessories_booking_actual', 'fit_comments_plan', 'fit_comments_actual', 'bulk_yarn_inhouse_plan', 'bulk_yarn_inhouse_actual', 'pp_sample_submission_plan', 'pp_sample_submission_actual', 'bulk_fabric_knitting_plan', 'bulk_fabric_knitting_actual', 'pp_comments_receive_plan', 'pp_comments_receive_actual', 'bulk_fabric_dyeing_plan', 'bulk_fabric_dyeing_actual', 'bulk_fabric_delivery_plan', 'bulk_fabric_delivery_actual', 'pp_meeting_plan', 'pp_meeting_actual', 'etd_plan', 'etd_actual', 'assign_date', 'assign_by', 'remarks', 'order_close')
            ->groupBy('id', 'buyer', 'style', 'po', 'item', 'color', 'qty_pcs', 'po_receive_date', 'shipment_etd', 'total_lead_time', 'order_free_time', 'lab_dip_submission_plan', 'lab_dip_submission_actual', 'fabric_booking_plan', 'fabric_booking_actual', 'fit_sample_submission_plan', 'fit_sample_submission_actual', 'print_strike_off_submission_plan', 'print_strike_off_submission_actual', 'bulk_accessories_booking_plan', 'bulk_accessories_booking_actual', 'fit_comments_plan', 'fit_comments_actual', 'bulk_yarn_inhouse_plan', 'bulk_yarn_inhouse_actual', 'pp_sample_submission_plan', 'pp_sample_submission_actual', 'bulk_fabric_knitting_plan', 'bulk_fabric_knitting_actual', 'pp_comments_receive_plan', 'pp_comments_receive_actual', 'bulk_fabric_dyeing_plan', 'bulk_fabric_dyeing_actual', 'bulk_fabric_delivery_plan', 'bulk_fabric_delivery_actual', 'pp_meeting_plan', 'pp_meeting_actual', 'etd_plan', 'etd_actual', 'assign_date', 'assign_by', 'remarks', 'order_close');

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
}
