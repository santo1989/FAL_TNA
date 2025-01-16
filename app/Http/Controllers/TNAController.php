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


    // public function BuyerWiseProductionLeadTimeSummary(Request $request)
    // {
    //     $okLeadTimeThreshold = 20;
    //     $user = auth()->user();
    //     $buyerIds = BuyerAssign::where('user_id', $user->id)->pluck('buyer_id');

    //     $query = Tna::where('order_close', '0')->orderBy('shipment_etd', 'asc');

    //     if ($user->role_id == 3 || ($user->role_id == 2 && $buyerIds->isNotEmpty())) {
    //         $query->whereIn('buyer_id', $buyerIds);
    //     }

    //     if ($request->has('from_date') && $request->has('to_date')) {
    //         $query->whereBetween('inspection_actual_date', [$request->from_date, $request->to_date]);
    //     }

    //     $tnaData = $query->get();
    //     $buyerSummary = [];
    //     $overallSummary = [
    //         'inadequate_orders' => 0,
    //         'adequate_orders' => 0,
    //         'total_orders' => 0,
    //         'lead_time_total' => 0,
    //     ];

    //     foreach ($tnaData as $tna) {
    //         $buyerName = $tna->buyer;
    //         $leadTime = $tna->pp_meeting_actual
    //             ? Carbon::parse($tna->pp_meeting_actual)->diffInDays(Carbon::parse($tna->inspection_actual_date))
    //             : 0;

    //         if (!isset($buyerSummary[$buyerName])) {
    //             $buyerSummary[$buyerName] = [
    //                 'inadequate_orders' => 0,
    //                 'adequate_orders' => 0,
    //                 'inadequate_details' => [],
    //                 'adequate_details' => [],
    //                 'lead_time_total' => 0,
    //                 'total_orders' => 0,
    //             ];
    //         }

    //         $buyerSummary[$buyerName]['total_orders']++;
    //         $buyerSummary[$buyerName]['lead_time_total'] += $leadTime;

    //         $orderDetails = [
    //             'id' => $tna->id,
    //             'style' => $tna->style ?? 'N/A',
    //             'po' => $tna->po ?? 'N/A',
    //             'shipment_etd' => $tna->shipment_etd ?? ' ',
    //             'inspection_actual_date' => $tna->inspection_actual_date ?? ' ',
    //             'pp_meeting_actual' => $tna->pp_meeting_actual ?? ' ',
    //         ];

    //         if ($leadTime >= $okLeadTimeThreshold) {
    //             $buyerSummary[$buyerName]['adequate_orders']++;
    //             $buyerSummary[$buyerName]['adequate_details'][] = $orderDetails;
    //         } else {
    //             $buyerSummary[$buyerName]['inadequate_orders']++;
    //             $buyerSummary[$buyerName]['inadequate_details'][] = $orderDetails;
    //         }

    //         $overallSummary['total_orders']++;
    //         $overallSummary['lead_time_total'] += $leadTime;
    //         if ($leadTime >= $okLeadTimeThreshold) {
    //             $overallSummary['adequate_orders']++;
    //         } else {
    //             $overallSummary['inadequate_orders']++;
    //         }
    //     }

    //     foreach ($buyerSummary as &$summary) {
    //         $totalOrders = $summary['total_orders'];
    //         $summary['inadequate_percentage'] = $totalOrders > 0
    //             ? round(($summary['inadequate_orders'] / $totalOrders) * 100, 2)
    //             : 0;
    //         $summary['adequate_percentage'] = $totalOrders > 0
    //             ? round(($summary['adequate_orders'] / $totalOrders) * 100, 2)
    //             : 0;
    //         $summary['average_lead_time'] = $totalOrders > 0
    //             ? round($summary['lead_time_total'] / $totalOrders, 2)
    //             : 0;
    //     }

    //     $overallSummary['inadequate_percentage'] = $overallSummary['total_orders'] > 0
    //         ? round(($overallSummary['inadequate_orders'] / $overallSummary['total_orders']) * 100, 2)
    //         : 0;
    //     $overallSummary['adequate_percentage'] = $overallSummary['total_orders'] > 0
    //         ? round(($overallSummary['adequate_orders'] / $overallSummary['total_orders']) * 100, 2)
    //         : 0;
    //     $overallSummary['average_lead_time'] = $overallSummary['total_orders'] > 0
    //         ? round($overallSummary['lead_time_total'] / $overallSummary['total_orders'], 2)
    //         : 0;

    //     $isPlanningDepartment = in_array($user->role_id, [1, 4]);

    //     return view('backend.OMS.reports.buyer_wise_production_lead_time', [
    //         'buyerSummary' => $buyerSummary,
    //         'overallSummary' => $overallSummary,
    //         'from_date' => $request->from_date,
    //         'to_date' => $request->to_date,
    //         'isPlanningDepartment' => $isPlanningDepartment,
    //     ]);
    // }


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

            if ($tna->pp_meeting_actual == null || $tna->inspection_actual_date == null ||$tna->pp_meeting_actual == null && $tna->inspection_actual_date == null) {
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

        $isPlanningDepartment = in_array($user->role_id, [1, 4,
            10005]); 


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
            } elseif($shipmentDifference == null){
                $buyerSummary[$buyerName]['pending_orders']++;
                $buyerSummary[$buyerName]['pending_details'][] = $orderDetails;
            } else
            {
                $buyerSummary[$buyerName]['late_orders']++;
                $buyerSummary[$buyerName]['late_details'][] = $orderDetails;
            }

            $overallSummary['total_orders']++;
            if ($shipmentDifference !== null && $shipmentDifference <= 0) {
                $overallSummary['on_time_orders']++;
            }elseif($shipmentDifference == null){
                $overallSummary['pending_orders']++;
            }
             else {
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


    // public function updateShipmentActualDates(Request $request)
    // {
    //     $user = auth()->user();

    //     if (!in_array($user->role_id, [1, 4])) {
    //         return response()->json(['message' => 'Unauthorized'], 403);
    //     }

    //     //check date format validation
    //     // $validator = Validator::make($request->all(), [
    //     //     'updates.*.shipment_actual_date' => 'required|date',
    //     // ]);




    //     // if ($validator->fails()) {
    //     //     return response()->json(['message' => 'Invalid date format'], 422);
    //     // }




    //     $updates = $request->input('updates', []);
    //     //change date format to Y-m-d before saving to database
    //     foreach ($updates as $update) {
    //         //if shipment_actual_date is empty, then skib that record and continue to next record
    //         if (empty($update['shipment_actual_date'])) {
    //             continue;
    //         }
    //         $update['shipment_actual_date'] = Carbon::parse($update['shipment_actual_date'])->format('Y-m-d');
    //     }
    //     foreach ($updates as $update) {
    //         DB::table('t_n_a_s')->where('id', $update['id'])->update([
    //             'shipment_actual_date' => $update['shipment_actual_date'] ?? null,
    //         ]);

    //         // etd_actual also needs to be updated same as shipment_actual_date and order_close to 1
    //         DB::table('t_n_a_s')->where('id', $update['id'])->update([
    //             'etd_actual' => $update['shipment_actual_date'] ?? null,
    //             'order_close' => $update['order_close'] ? 1 : 0,
    //         ]);
    //     }

    //     return response()->json(['message' => 'Updates saved successfully!'], 200);
    // }


    // public function MailBuyerWiseTnaSummary()
    // {
    //     // Get current date
    //     $currentDate = Carbon::now()->format('Y-m-d');

    //     // Fetch data from t_n_a_s table
    //     $tnaData = Tna::where('order_close', '0')
    //         ->orderBy('shipment_etd', 'asc')
    //         ->get();

    //     // Fetch buyer assignments (buyer_id and user_id (merchandiser))
    //     $marchendiserWiseBuyer = DB::table('buyer_assigns')->get();

    //     // Fetch emails of merchandisers, admins, and supervisors
    //     $marchendiserEmails = DB::table('users')->pluck('email', 'id'); // Merchandiser emails by user ID
    //     $adminEmails = 'santo@ntg.com.bd'; // Assuming you have a role field
    //     $supervisorEmails = DB::table('users')->where('role_id', 4)->pluck('email'); // Same for supervisor role

    //     // Process data to get counts for pending tasks
    //     $buyers = [];
    //     $columns = [
    //         'lab_dip_submission',
    //         'fabric_booking',
    //         'fit_sample_submission',
    //         'print_strike_off_submission',
    //         'bulk_accessories_booking',
    //         'fit_comments',
    //         'bulk_yarn_inhouse',
    //         'bulk_accessories_inhouse',
    //         'pp_sample_submission',
    //         'bulk_fabric_knitting',
    //         'pp_comments_receive',
    //         'bulk_fabric_dyeing',
    //         'bulk_fabric_delivery',
    //         'pp_meeting'
    //     ];

    //     foreach ($tnaData as $row) {
    //         $buyerName = $row->buyer;
    //         if (!isset($buyers[$buyerName])) {
    //             $buyers[$buyerName] = [
    //                 'data' => array_fill_keys($columns, 0),
    //                 'details' => []
    //             ];
    //         }
    //         foreach ($columns as $column) {
    //             $planColumn = $column . '_plan';
    //             $actualColumn = $column . '_actual';
    //             if ($row->$planColumn && !$row->$actualColumn && $row->$planColumn <= $currentDate) {
    //                 $buyers[$buyerName]['data'][$column]++;
    //                 $buyers[$buyerName]['details'][$column][] = [
    //                     'style' => $row->style,
    //                     'po' => $row->po,
    //                     'task' => $column,
    //                     'PlanDate' => Carbon::parse($row->$planColumn)->format('d-M-y')
    //                 ];
    //             }
    //         }
    //     }

    //     // Send email to each buyer's assigned merchant, and cc to admin and supervisor
    //     foreach ($marchendiserWiseBuyer as $assignment) {
    //         $buyerId = $assignment->buyer_id;
    //         $userId = $assignment->user_id; // Merchandiser ID

    //         // Get the merchandiser email by ID
    //         $merchantEmail = isset($marchendiserEmails[$userId]) ? $marchendiserEmails[$userId] : null;

    //         // Prepare the email only for buyers with pending tasks
    //         if (isset($buyers[$buyerId]) && array_sum($buyers[$buyerId]['data']) > 0) {
    //             try {
    //                 if (!$merchantEmail) {
    //                     Log::warning('No email found for user ID ' . $userId);
    //                     continue;
    //                 }

    //                 // Send the email to the merchant and cc admins and supervisors
    //                 Mail::to($merchantEmail)
    //                     ->cc($adminEmails) // CC admins
    //                     ->bcc($supervisorEmails) // BCC supervisors
    //                     ->send(new BuyerWiseTnaSummary($buyers[$buyerId], $columns));

    //                 Log::info('Email sent to ' . $merchantEmail);
    //             } catch (\Exception $e) {
    //                 Log::error('Error sending email to ' . $merchantEmail . ': ' . $e->getMessage());
    //             }
    //         }
    //     }

    //     return view('backend.OMS.reports.buyer_wise_tna_summary', [
    //         'buyers' => $buyers,
    //         'columns' => $columns
    //     ]);
    //     // return response()->json(['status' => 'Emails sent successfully']);
    // }

    // public function MailBuyerWiseTnaSummary()
    // {
    //     // Get current date
    //     $currentDate = Carbon::now()->format('Y-m-d');

    //     // Retrieve the user's role and assigned buyers
    //     $user = auth()->user();
    //     $buyerIds = BuyerAssign::where('user_id', $user->id)->pluck('buyer_id');

    //     // Query TNAs based on the user's role and assigned buyers
    //     $query =
    //         Tna::where('order_close', '0')
    //         ->orderBy('shipment_etd', 'asc');

    //     if ($user->role_id == 3 || ($user->role_id == 2 && $buyerIds->isNotEmpty())) {
    //         $query->whereIn('buyer_id', $buyerIds);
    //     }

    //     // Fetch data from t_n_a_s table
    //     $tnaData = $query->get();

    //     // Process data to get counts
    //     $buyers = [];
    //     $columns = [
    //         'lab_dip_submission',
    //         'fabric_booking',
    //         'fit_sample_submission',
    //         'print_strike_off_submission',
    //         'bulk_accessories_booking',
    //         'fit_comments',
    //         'bulk_yarn_inhouse',
    //         'bulk_accessories_inhouse',
    //         'pp_sample_submission',
    //         'bulk_fabric_knitting',
    //         'pp_comments_receive',
    //         'bulk_fabric_dyeing',
    //         'bulk_fabric_delivery',
    //         'pp_meeting'
    //     ];

    //     foreach ($tnaData as $row) {
    //         $buyerName = $row->buyer;
    //         if (!isset($buyers[$buyerName])) {
    //             $buyers[$buyerName] = [
    //                 'data' => array_fill_keys($columns, 0),
    //                 'details' => []
    //             ];
    //         }
    //         foreach ($columns as $column) {
    //             $planColumn = $column . '_plan';
    //             $actualColumn = $column . '_actual';
    //             if ($row->$planColumn && !$row->$actualColumn && $row->$planColumn <= $currentDate) {
    //                 $buyers[$buyerName]['data'][$column]++;
    //                 $buyers[$buyerName]['details'][$column][] = [
    //                     'style' => $row->style,
    //                     'po' => $row->po,
    //                     'task' => $column,
    //                     'PlanDate' => Carbon::parse($row->$planColumn)->format('d-M-y'),
    //                     'shipment_etd' => Carbon::parse($row->shipment_etd)->format('d-M-y')
    //                 ];
    //             }
    //         }
    //     }

    //     // Filter buyers with no data
    //     $filteredBuyers = array_filter($buyers, function ($buyer) {
    //         return array_sum($buyer['data']) > 0;
    //     });

    //     if (empty($filteredBuyers)) {
    //         return response()->json(['status' => 'error', 'message' => 'No data available for the selected buyers.']);
    //     }

    //     // Generate email content
    //     $emailContent = view('emails.buyer_wise_tna_summary', [
    //         'buyers' => $filteredBuyers,
    //         'columns' => $columns
    //     ])->render();

    //     // Send email
    //     try {
    //         Mail::send([], [], function ($message) use ($emailContent) {
    //             $message->to('santo@ntg.com.bd')
    //             ->subject('Buyer Wise TNA Summary')
    //             ->setBody($emailContent, 'text/html');
    //         });

    //         return response()->json(['status' => 'success', 'message' => 'Email sent successfully.']);
    //     } catch (\Exception $e) {
    //         return response()->json(['status' => 'error', 'message' => 'Failed to send email.']);
    //     }
    // }





    // public function MailBuyerWiseTnaSummary()
    // {
    //     // Get current date
    //     $currentDate = Carbon::now()->format('Y-m-d');

    //     // Retrieve merchants from users table
    //     $merchants = User::where('role_id', 3)->get();

    //     foreach ($merchants as $merchant) {
    //         // Retrieve assigned buyers for the merchant
    //         $buyerIds = BuyerAssign::where('user_id', $merchant->id)->pluck('buyer_id');

    //         if ($buyerIds->isEmpty()) {
    //             continue; // Skip merchant with no assigned buyers
    //         }

    //         // Query TNAs for the merchant's assigned buyers
    //         $tnaData = Tna::whereIn('buyer_id', $buyerIds)
    //             ->where('order_close', '0')
    //             ->orderBy('shipment_etd', 'asc')
    //             ->get();

    //         // Process data to get counts
    //         $buyers = [];
    //         $columns = [
    //             'lab_dip_submission',
    //             'fabric_booking',
    //             'fit_sample_submission',
    //             'print_strike_off_submission',
    //             'bulk_accessories_booking',
    //             'fit_comments',
    //             'bulk_yarn_inhouse',
    //             'bulk_accessories_inhouse',
    //             'pp_sample_submission',
    //             'bulk_fabric_knitting',
    //             'pp_comments_receive',
    //             'bulk_fabric_dyeing',
    //             'bulk_fabric_delivery',
    //             'pp_meeting'
    //         ];

    //         foreach ($tnaData as $row) {
    //             $buyerName = $row->buyer;
    //             if (!isset($buyers[$buyerName])) {
    //                 $buyers[$buyerName] = [
    //                     'data' => array_fill_keys($columns, 0),
    //                     'details' => []
    //                 ];
    //             }
    //             foreach ($columns as $column) {
    //                 $planColumn = $column . '_plan';
    //                 $actualColumn = $column . '_actual';
    //                 if ($row->$planColumn && !$row->$actualColumn && $row->$planColumn <= $currentDate) {
    //                     $buyers[$buyerName]['data'][$column]++;
    //                     $buyers[$buyerName]['details'][$column][] = [
    //                         'style' => $row->style,
    //                         'po' => $row->po,
    //                         'task' => $column,
    //                         'PlanDate' => Carbon::parse($row->$planColumn)->format('d-M-y'),
    //                         'shipment_etd' => Carbon::parse($row->shipment_etd)->format('d-M-y')
    //                     ];
    //                 }
    //             }
    //         }

    //         // Filter buyers with no data
    //         $filteredBuyers = array_filter($buyers, function ($buyer) {
    //             return array_sum($buyer['data']) > 0;
    //         });

    //         if (empty($filteredBuyers)) {
    //             continue; // Skip merchants with no data for their buyers
    //         }

    //         // Generate email content
    //         $emailContent = view('emails.buyer_wise_tna_summary', [
    //             'buyers' => $filteredBuyers,
    //             'columns' => $columns
    //         ])->render();

    //         // Retrieve email recipients
    //         $merchantEmail = $merchant->email; // Merchant's email
    //         $adminEmails = User::where('role_id', 1)->pluck('email')->toArray(); // Admin emails
    //         $supervisorEmails = User::where('role_id', 4)->pluck('email')->toArray(); // Supervisors

    //         // Send email
    //         try {
    //             Mail::send([], [], function ($message) use ($emailContent, $merchantEmail, $adminEmails, $supervisorEmails) {
    //                 $message->to($merchantEmail) // To merchant
    //                     ->bcc($supervisorEmails) // BCC admin emails
    //                     // ->cc('santo@ntg.com.bd') // CC supervisor emails
    //                     ->subject('Buyer Wise TNA Summary')
    //                     ->setBody($emailContent, 'text/html');
    //             });
    //         } catch (\Exception $e) {
    //             // Log the exception and continue with the next merchant
    //             Log::error('Failed to send email to ' . $merchantEmail . ': ' . $e->getMessage());
    //         }
    //     }

    //     // return response()->json(['status' => 'success', 'message' => 'Emails sent successfully to merchants with available data.']);

    //     return back()->withMessages('Emails sent successfully to merchants with available data.');
    // }


    // public function BuyerWiseProductionLeadTimeSummary()
    // {
    //     // Define the threshold for OK lead time
    //     $okLeadTimeThreshold = 20;

    //     // Retrieve the current user's role and assigned buyers
    //     $user = auth()->user();
    //     $buyerIds = BuyerAssign::where('user_id', $user->id)->pluck('buyer_id');

    //     // Query TNAs based on role and assigned buyers
    //     $query = Tna::where('order_close', '0')->orderBy('shipment_etd', 'asc');
    //     if ($user->role_id == 3 || ($user->role_id == 2 && $buyerIds->isNotEmpty())) {
    //         $query->whereIn('buyer_id', $buyerIds);
    //     }

    //     // Fetch data
    //     $tnaData = $query->get();

    //     // Process data to generate the table
    //     $buyerSummary = [];
    //     foreach ($tnaData as $tna) {
    //         $buyerName = $tna->buyer;
    //         $leadTime = $tna->pp_meeting_actual ? Carbon::parse($tna->pp_meeting_actual)->diffInDays(Carbon::parse($tna->shipment_etd)) : 0;

    //         if (!isset($buyerSummary[$buyerName])) {
    //             $buyerSummary[$buyerName] = [
    //                 'total_style' => 0,
    //                 'ok_lead_time' => 0,
    //             ];
    //         }

    //         // Increment Total style
    //         $buyerSummary[$buyerName]['total_style']++;

    //         // Increment OK lead time if the condition is met
    //         if ($leadTime >= $okLeadTimeThreshold) {
    //             $buyerSummary[$buyerName]['ok_lead_time']++;
    //         }
    //     }

    //     // Calculate OK lead time percentage
    //     foreach ($buyerSummary as $buyerName => $summary) {
    //         $totalStyle = $summary['total_style'];
    //         $okLeadTime = $summary['ok_lead_time'];

    //         $buyerSummary[$buyerName]['ok_lead_time_percentage'] = $totalStyle > 0 ? round(($okLeadTime / $totalStyle) * 100, 2) : 0;
    //     }

    //     // Calculate overall averages
    //     $totalStyles = array_sum(array_column($buyerSummary, 'total_style'));
    //     $totalOkLeadTimes = array_sum(array_column($buyerSummary, 'ok_lead_time'));
    //     $overallPercentage = $totalStyles > 0 ? round(($totalOkLeadTimes / $totalStyles) * 100, 2) : 0;

    //     // Return summarized data to view
    //     return view('backend.OMS.reports.buyer_wise_production_lead_time', [
    //         'buyerSummary' => $buyerSummary,
    //         'totalStyles' => $totalStyles,
    //         'totalOkLeadTimes' => $totalOkLeadTimes,
    //         'overallPercentage' => $overallPercentage,
    //     ]);
    // }

    // public function BuyerWiseProductionLeadTimeSummary(Request $request)
    // {
    //     // Define the threshold for OK lead time
    //     $okLeadTimeThreshold = 20;

    //     // Retrieve the current user's role and assigned buyers
    //     $user = auth()->user();
    //     $buyerIds = BuyerAssign::where('user_id', $user->id)->pluck('buyer_id');

    //     // Query TNAs based on role, assigned buyers, and date range
    //     $query = Tna::where('order_close', '0')->orderBy('shipment_etd', 'asc');

    //     // Apply buyer filtering based on role
    //     if ($user->role_id == 3 || ($user->role_id == 2 && $buyerIds->isNotEmpty())) {
    //         $query->whereIn('buyer_id', $buyerIds);
    //     }

    //     // Apply date filters if provided
    //     if ($request->has('from_date') && $request->has('to_date')) {
    //         $query->whereBetween('shipment_etd', [$request->from_date, $request->to_date]);
    //     }

    //     // Fetch data
    //     $tnaData = $query->get();

    //     // Process data to generate the table (same logic as before)
    //     $buyerSummary = [];
    //     foreach ($tnaData as $tna) {
    //         $buyerName = $tna->buyer;
    //         $leadTime = $tna->pp_meeting_actual ? Carbon::parse($tna->pp_meeting_actual)->diffInDays(Carbon::parse($tna->shipment_etd)) : 0;

    //         if (!isset($buyerSummary[$buyerName])) {
    //             $buyerSummary[$buyerName] = [
    //                 'inadequate_orders' => 0,
    //                 'adequate_orders' => 0,
    //                 'lead_time_total' => 0,
    //                 'total_orders' => 0,
    //             ];
    //         }

    //         // Increment Total Orders
    //         $buyerSummary[$buyerName]['total_orders']++;
    //         $buyerSummary[$buyerName]['lead_time_total'] += $leadTime;

    //         // Check if lead time is adequate or inadequate
    //         if ($leadTime >= $okLeadTimeThreshold) {
    //             $buyerSummary[$buyerName]['adequate_orders']++;
    //         } else {
    //             $buyerSummary[$buyerName]['inadequate_orders']++;
    //         }
    //     }

    //     // Calculate percentages and averages
    //     foreach ($buyerSummary as $buyerName => &$summary) {
    //         $totalOrders = $summary['total_orders'];
    //         $summary['inadequate_percentage'] = $totalOrders > 0 ? round(($summary['inadequate_orders'] / $totalOrders) * 100, 2) : 0;
    //         $summary['adequate_percentage'] = $totalOrders > 0 ? round(($summary['adequate_orders'] / $totalOrders) * 100, 2) : 0;
    //         $summary['average_lead_time'] = $totalOrders > 0 ? round($summary['lead_time_total'] / $totalOrders, 2) : 0;
    //     }

    //     // Calculate overall totals
    //     $overallSummary = [
    //         'inadequate_orders' => array_sum(array_column($buyerSummary, 'inadequate_orders')),
    //         'adequate_orders' => array_sum(array_column($buyerSummary, 'adequate_orders')),
    //         'total_orders' => array_sum(array_column($buyerSummary, 'total_orders')),
    //         'lead_time_total' => array_sum(array_column($buyerSummary, 'lead_time_total')),
    //     ];
    //     $overallSummary['inadequate_percentage'] = $overallSummary['total_orders'] > 0 ? round(($overallSummary['inadequate_orders'] / $overallSummary['total_orders']) * 100, 2) : 0;
    //     $overallSummary['adequate_percentage'] = $overallSummary['total_orders'] > 0 ? round(($overallSummary['adequate_orders'] / $overallSummary['total_orders']) * 100, 2) : 0;
    //     $overallSummary['average_lead_time'] = $overallSummary['total_orders'] > 0 ? round($overallSummary['lead_time_total'] / $overallSummary['total_orders'], 2) : 0;

    //     foreach ($tnaData as $tna) {
    //         $buyerName = $tna->buyer;
    //         $leadTime = $tna->pp_meeting_actual
    //         ? Carbon::parse($tna->pp_meeting_actual)->diffInDays(Carbon::parse($tna->shipment_etd))
    //         : 0;

    //         if (!isset($buyerSummary[$buyerName])) {
    //             $buyerSummary[$buyerName] = [
    //                 'inadequate_orders' => 0,
    //                 'adequate_orders' => 0,
    //                 'inadequate_details' => [],
    //                 'adequate_details' => [],
    //                 'lead_time_total' => 0,
    //                 'total_orders' => 0,
    //             ];
    //         }

    //         // Increment Total Orders
    //         $buyerSummary[$buyerName]['total_orders']++;
    //         $buyerSummary[$buyerName]['lead_time_total'] += $leadTime;

    //         // Check if lead time is adequate or inadequate
    //         $orderDetails = [
    //             'style' => $tna->style ?? 'N/A',
    //             'po' => $tna->po ?? 'N/A',
    //             'shipment_etd' => $tna->shipment_etd ?? 'N/A',
    //             // 7 days before shipment_etd
    //             // 'inspection_plan' => $tna->shipment_etd ? Carbon::parse($tna->shipment_etd)->subDays(7)->format('Y-m-d') : 'N/A', 
    //             'inspection_actual_date' => $tna->inspection_actual_date,
    //             // 'pp_meeting_plan' => $tna->pp_meeting_plan ?? 'N/A',
    //             'pp_meeting_actual' => $tna->pp_meeting_actual,
    //         ];

    //         if ($leadTime >= $okLeadTimeThreshold) {
    //             $buyerSummary[$buyerName]['adequate_orders']++;
    //             $buyerSummary[$buyerName]['adequate_details'][] = $orderDetails;
    //         } else {
    //             $buyerSummary[$buyerName]['inadequate_orders']++;
    //             $buyerSummary[$buyerName]['inadequate_details'][] = $orderDetails;
    //         }
    //     }

    //     // FAL Planning department users from users table

    //     // dd($isPlanningDepartment);
    //     if(auth()->user()->role_id == 1 || auth()->user()->role_id == 4){
    //         $user = User::where('role_id', auth()->user()->role_id)->get();
    //         $isPlanningDepartment = 'planning';
    //     } 



    //     // Return summarized data to view
    //     return view('backend.OMS.reports.buyer_wise_production_lead_time', [
    //         'buyerSummary' => $buyerSummary,
    //         'overallSummary' => $overallSummary,
    //         'from_date' => $request->from_date,
    //         'to_date' => $request->to_date,
    //         'isPlanningDepartment' => $isPlanningDepartment,
    //     ]);
    // }



    // public function BuyerWiseProductionLeadTimeSummary(Request $request)
    // {
    //     // Define the threshold for OK lead time
    //     $okLeadTimeThreshold = 20;

    //     // Retrieve the current user's role and assigned buyers
    //     $user = auth()->user();
    //     $buyerIds = BuyerAssign::where('user_id', $user->id)->pluck('buyer_id');

    //     // Query TNAs based on role, assigned buyers, and date range
    //     $query = Tna::where('order_close', '0')->orderBy('shipment_etd', 'asc');

    //     // Apply buyer filtering based on role
    //     if ($user->role_id == 3 || ($user->role_id == 2 && $buyerIds->isNotEmpty())) {
    //         $query->whereIn('buyer_id', $buyerIds);
    //     }

    //     // Apply date filters if provided
    //     if ($request->has('from_date') && $request->has('to_date')) {
    //         $query->whereBetween('shipment_etd', [$request->from_date, $request->to_date]);
    //     }

    //     // Fetch data
    //     $tnaData = $query->get();

    //     // Initialize buyer summary
    //     $buyerSummary = [];
    //     $overallSummary = [
    //         'inadequate_orders' => 0,
    //         'adequate_orders' => 0,
    //         'total_orders' => 0,
    //         'lead_time_total' => 0,
    //     ];

    //     // Process data to generate summaries
    //     foreach ($tnaData as $tna) {
    //         $buyerName = $tna->buyer;
    //         $leadTime = $tna->pp_meeting_actual
    //             ? Carbon::parse($tna->pp_meeting_actual)->diffInDays(Carbon::parse($tna->shipment_etd))
    //             : 0;

    //         // Initialize buyer summary if not set
    //         if (!isset($buyerSummary[$buyerName])) {
    //             $buyerSummary[$buyerName] = [
    //                 'inadequate_orders' => 0,
    //                 'adequate_orders' => 0,
    //                 'inadequate_details' => [],
    //                 'adequate_details' => [],
    //                 'lead_time_total' => 0,
    //                 'total_orders' => 0,
    //             ];
    //         }

    //         // Increment Total Orders and Lead Time
    //         $buyerSummary[$buyerName]['total_orders']++;
    //         $buyerSummary[$buyerName]['lead_time_total'] += $leadTime;

    //         // Prepare order details
    //         $orderDetails = [
    //             'style' => $tna->style ?? 'N/A',
    //             'po' => $tna->po ?? 'N/A',
    //             'shipment_etd' => $tna->shipment_etd ?? 'N/A',
    //             'inspection_actual_date' => $tna->inspection_actual_date ?? 'N/A',
    //             'pp_meeting_actual' => $tna->pp_meeting_actual ?? 'N/A',
    //         ];

    //         // Classify orders based on lead time
    //         if ($leadTime >= $okLeadTimeThreshold) {
    //             $buyerSummary[$buyerName]['adequate_orders']++;
    //             $buyerSummary[$buyerName]['adequate_details'][] = $orderDetails;
    //         } else {
    //             $buyerSummary[$buyerName]['inadequate_orders']++;
    //             $buyerSummary[$buyerName]['inadequate_details'][] = $orderDetails;
    //         }

    //         // Update overall summary
    //         $overallSummary['total_orders']++;
    //         $overallSummary['lead_time_total'] += $leadTime;
    //         if ($leadTime >= $okLeadTimeThreshold) {
    //             $overallSummary['adequate_orders']++;
    //         } else {
    //             $overallSummary['inadequate_orders']++;
    //         }
    //     }

    //     // Calculate percentages and averages
    //     foreach ($buyerSummary as $buyerName => &$summary) {
    //         $totalOrders = $summary['total_orders'];
    //         $summary['inadequate_percentage'] = $totalOrders > 0
    //             ? round(($summary['inadequate_orders'] / $totalOrders) * 100, 2)
    //             : 0;
    //         $summary['adequate_percentage'] = $totalOrders > 0
    //             ? round(($summary['adequate_orders'] / $totalOrders) * 100, 2)
    //             : 0;
    //         $summary['average_lead_time'] = $totalOrders > 0
    //             ? round($summary['lead_time_total'] / $totalOrders, 2)
    //             : 0;
    //     }

    //     $overallSummary['inadequate_percentage'] = $overallSummary['total_orders'] > 0
    //     ? round(($overallSummary['inadequate_orders'] / $overallSummary['total_orders']) * 100, 2)
    //         : 0;
    //     $overallSummary['adequate_percentage'] = $overallSummary['total_orders'] > 0
    //     ? round(($overallSummary['adequate_orders'] / $overallSummary['total_orders']) * 100, 2)
    //         : 0;
    //     $overallSummary['average_lead_time'] = $overallSummary['total_orders'] > 0
    //     ? round($overallSummary['lead_time_total'] / $overallSummary['total_orders'], 2)
    //     : 0;

    //     // Check user department for special handling
    //     $isPlanningDepartment = null;
    //     if (in_array(auth()->user()->role_id, [1, 4])) {
    //         $isPlanningDepartment = 'planning';
    //     }

    //     // Return summarized data to view
    //     return view('backend.OMS.reports.buyer_wise_production_lead_time', [
    //         'buyerSummary' => $buyerSummary,
    //         'overallSummary' => $overallSummary,
    //         'from_date' => $request->from_date,
    //         'to_date' => $request->to_date,
    //         'isPlanningDepartment' => $isPlanningDepartment,
    //     ]);
    // }


    //    public function updateTaskDetails(Request $request)
    // {
    //     $updates = $request->input('updates', []);

    //     foreach ($updates as $update) {
    //         DB::table('tnas')
    //         ->where('id', $update['id'])
    //             ->update([
    //                 'inspection_actual_date' => $update['inspection_actual_date'],
    //                 'pp_meeting_actual' => $update['pp_meeting_actual'],
    //             ]);
    //     }

    //     return response()->json(['message' => 'Updates saved successfully!'], 200);
    // }


}
