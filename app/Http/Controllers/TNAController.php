<?php

namespace App\Http\Controllers;

use App\Mail\BuyerWiseTnaSummary;
use Carbon\Carbon;
use App\Models\Buyer;
use App\Models\BuyerAssign;
use App\Models\SOP;
use App\Models\TNA;
use App\Models\TnaExplanation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

use function Symfony\Component\String\b;

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
        // dd($request->all());
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


    public function destroy($id)
    {
        // dd($id);
        if (auth()->user()->role_id == 1 || auth()->user()->role_id == 1) {
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


    public function fal_tnas_dashboard()
    {
        $tnas = $this->falfetchTnasData();
        // dd($tnas);
        return view('backend.library.tnas.fal_tnas_dashboard', compact('tnas'));
    }

    public function fal_tnas_dashboard_update()
    {
        $tnas = $this->falfetchTnasData();
        return view('backend.library.tnas.fal_tnas_table_body', compact('tnas'));
    }

    public function falfetchTnasData()
    {
        $tnas = TNA::where('order_close', '0')
            ->select(
                'id',
                'buyer_id',
                'buyer',
                'style',
                'po',
                'item',
                'color',
                'qty_pcs',
                'po_receive_date',
                'shipment_etd',
                'total_lead_time',
                'order_free_time',
                'lab_dip_submission_plan',
                'lab_dip_submission_actual',
                'fabric_booking_plan',
                'fabric_booking_actual',
                'fit_sample_submission_plan',
                'fit_sample_submission_actual',
                'print_strike_off_submission_plan',
                'print_strike_off_submission_actual',
                'bulk_accessories_booking_plan',
                'bulk_accessories_booking_actual',
                'fit_comments_plan',
                'fit_comments_actual',
                'bulk_yarn_inhouse_plan',
                'bulk_yarn_inhouse_actual',
                'bulk_accessories_inhouse_plan',
                'bulk_accessories_inhouse_actual',
                'pp_sample_submission_plan',
                'pp_sample_submission_actual',
                'bulk_fabric_knitting_plan',
                'bulk_fabric_knitting_actual',
                'pp_comments_receive_plan',
                'pp_comments_receive_actual',
                'bulk_fabric_dyeing_plan',
                'bulk_fabric_dyeing_actual',
                'bulk_fabric_delivery_plan',
                'bulk_fabric_delivery_actual',
                'pp_meeting_plan',
                'pp_meeting_actual',
                'etd_plan',
                'etd_actual',
                'assign_date',
                'assign_by',
                'remarks',
                'order_close'
            )
            ->groupBy(
                'id',
                'buyer_id',
                'buyer',
                'style',
                'po',
                'item',
                'color',
                'qty_pcs',
                'po_receive_date',
                'shipment_etd',
                'total_lead_time',
                'order_free_time',
                'lab_dip_submission_plan',
                'lab_dip_submission_actual',
                'fabric_booking_plan',
                'fabric_booking_actual',
                'fit_sample_submission_plan',
                'fit_sample_submission_actual',
                'print_strike_off_submission_plan',
                'print_strike_off_submission_actual',
                'bulk_accessories_booking_plan',
                'bulk_accessories_booking_actual',
                'fit_comments_plan',
                'fit_comments_actual',
                'bulk_yarn_inhouse_plan',
                'bulk_yarn_inhouse_actual',
                'bulk_accessories_inhouse_plan',
                'bulk_accessories_inhouse_actual',
                'pp_sample_submission_plan',
                'pp_sample_submission_actual',
                'bulk_fabric_knitting_plan',
                'bulk_fabric_knitting_actual',
                'pp_comments_receive_plan',
                'pp_comments_receive_actual',
                'bulk_fabric_dyeing_plan',
                'bulk_fabric_dyeing_actual',
                'bulk_fabric_delivery_plan',
                'bulk_fabric_delivery_actual',
                'pp_meeting_plan',
                'pp_meeting_actual',
                'etd_plan',
                'etd_actual',
                'assign_date',
                'assign_by',
                'remarks',
                'order_close'
            );
        $tnas = $tnas->orderBy('shipment_etd', 'asc')->get();

        //  dd($tnas);
        return $tnas;
    }

    public function MailBuyerWiseTnaSummary()
    {
        // Get current date
        $currentDate = Carbon::now()->format('Y-m-d');

        // Fetch data from t_n_a_s table
        $tnaData = Tna::where('order_close', '0')
            ->orderBy('shipment_etd', 'asc')
            ->get();

        $marchendiser_wise_buyer = DB::table('buyer_assigns')->select(array('buyer_id', 'user_id'))->get();

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
                    $buyers[$buyerName]['details'][$column][] = [
                        'style' => $row->style,
                        'po' => $row->po,
                        'task' => $column,
                        'PlanDate' => Carbon::parse($row->$planColumn)->format('d-M-y')
                    ];
                }
            }
        }

        // Send email
        // Mail::to('santo@ntg.com.bd') // Replace with actual buyer email or loop through multiple emails
        // ->send(new BuyerWiseTnaSummary($buyers, $columns));
        try {
            Mail::to('santo@ntg.com.bd')->send(new BuyerWiseTnaSummary($buyers, $columns));
        } catch (\Exception $e) {
            Log::error('Error sending email: ' . $e->getMessage());
        }


        return view('backend.OMS.reports.buyer_wise_tna_summary', [
            'buyers' => $buyers,
            'columns' => $columns
        ]);
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
}
