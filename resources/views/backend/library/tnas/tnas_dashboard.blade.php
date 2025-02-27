<x-backend.layouts.report_master>


    {{-- <style>
        .bg-red {
            background-color: red !important;
            color: white;
            font-weight: bold;
        }

        .bg-yellow {
            background-color: yellow !important;
            color: black;
            font-weight: bold;
        }

        /* Make all table headers sticky horizontally */
        thead th {
            position: sticky;
            top: 0;
            background: #343a40;
            color: white;
            z-index: 20;
        }

        /* Make the first 4 columns sticky vertically with fixed widths and z-index */
        tbody td:nth-child(1),
        tbody td:nth-child(2),
        tbody td:nth-child(3),
        tbody td:nth-child(4) {
            position: sticky;
            background: #494747;
            color: white;
            padding: 5px;
            z-index: 10;
        }

        /* Hover effect on table rows */
        #PrintTable tbody tr:hover td {
            background-color: #ffffff00;
            /* Transparent background on hover */
        }

        /* Change background color for the first 4 columns on hover */
        #PrintTable tbody tr:hover td:nth-child(-n+4) {
            background-color: #ffcc00;
        }

        /* Define column widths for the headers */
        thead th:nth-child(1) {
            width: 150px;
        }

        thead th:nth-child(2) {
            width: 150px;
        }

        thead th:nth-child(3) {
            width: 150px;
        }

        thead th:nth-child(4) {
            width: 150px;
        }

        /* Sortable column styles */
        .sortable {
            cursor: pointer;
        }

        .sortable:hover {
            background-color: #f90303;
            /* Light background on hover */
        }
    </style> --}}
    <style>
        .bg-red {
            background-color: red !important;
            color: white;
            font-weight: bold;
        }

        .bg-yellow {
            background-color: yellow !important;
            color: black;
            font-weight: bold;
        }

        /* Sortable column styles */
        .sortable {
            cursor: pointer;
        }

        .sortable:hover {
            background-color: #f90303;
        }




        /* Hover effect on table rows */
        #PrintTable tbody tr:hover td {
            background-color: #ffffff00;
        }

        /* Change background color for the first 4 columns on hover */
        #PrintTable tbody tr:hover td:nth-child(-n+4) {
            background-color: #ffcc00;
        }
    </style>


    <div class="container-fluid pt-2">
        <h4 class="text-center text-white"><img src="{{ asset('images/assets/FAL_logo.png') }}" alt="NTG"
                width="70px">
            TNA Dashboard</h4>
        <div class="row justify-content-center pb-2">
            @php
                // Retrieve the user's role and assigned buyers
$user = auth()->user();
$buyerIds = DB::table('buyer_assigns')
    ->where('user_id', $user->id)
    ->pluck('buyer_id');

// Query TNAs based on the user's role and assigned buyers
                $query = DB::table('t_n_a_s')->where('order_close', '0');

                if ($user->role_id == 3 || ($user->role_id == 2 && $buyerIds->isNotEmpty())) {
                    $query->whereIn('buyer_id', $buyerIds);
                }
                $buyerList = $query->select('buyer')->distinct()->get();
            @endphp

            <div class="col-12">
                <a href="{{ route('tnas.index') }}" class="btn btn-outline-secondary bg-light btn-sm"
                    style="width: 10rem;">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                <button class="btn btn-outline-secondary bg-light btn-sm" onclick="downloadExcel()"
                    style="width: 10rem;">
                    <i class="fas fa-download"></i> Download</button>
                <button class="btn btn-sm btn-outline-primary bg-light" style="width: 10rem;" id="all-buyers-btn">
                    All Buyers
                </button>
                @foreach ($buyerList as $buyer)
                    <button class="btn btn-sm btn-outline-primary bg-light" style="width: 10rem;" id="filter-buyer-btn"
                        data-buyer="{{ $buyer->buyer }}">

                        {{ $buyer->buyer }}
                    </button>
                @endforeach
            </div>


        </div>

        {{-- <div class="row justify-content-center pb-2"> 
            <div class="col-12">
                {{ $tnas->links() }}
            </div> 
        </div> --}}

        <table class="table table-bordered table-hover text-center text-nowrap" style="font-size: 12px;"
            id="PrintTable">
            <thead class="thead-dark"
                style="font-size: 12px; text-align: center; vertical-align: middle;position: sticky;top: 0;z-index: 1;">
                <tr>
                    <th rowspan="2">Action</th>
                    <th rowspan="2">Buyer</th>
                    <th rowspan="2">Style</th>
                    <th rowspan="2">PO Number</th>
                    <th rowspan="2">Item</th>
                    <th>Qty (pcs)</th>
                    <th>PO Receive Date</th>
                    <th id="shortablehead">Shipment/ETD</th>
                    <th>Total Lead Time</th>
                    <th>Order Free Time</th>
                    <th colspan="2">Lab Dip Submission</th>
                    <th colspan="2">Fabric Booking</th>
                    <th colspan="2">Fit Sample Submission</th>
                    <th colspan="2">Print Strike Off Submission</th>
                    <th colspan="2">Bulk Accessories Booking</th>
                    <th colspan="2">Fit Comments</th>
                    <th colspan="2">Bulk Yarn Inhouse</th>
                    <th colspan="2">Bulk Accessories Inhouse</th>
                    <th colspan="2">PP Sample Submission</th>
                    <th colspan="2">Bulk Fabric Knitting</th>
                    <th colspan="2">PP Comments Receive</th>
                    <th colspan="2">Bulk Fabric Dyeing</th>
                    <th colspan="2">Bulk Fabric Delivery</th>
                    <th colspan="2">PP Meeting</th>
                    {{-- <th colspan="2">Cutting</th> --}}
                    <th colspan="2">ETD</th>
                    <th colspan="2">Fabrics and Accessories Inspection</th>
                    <th colspan="2">Size Set Making</th>
                    <th colspan="2">Pattern Correction</th>
                    <th colspan="2">Machines, Layout, and Folder Preparation</th>
                    <th colspan="2">Bulk Cutting Start</th>
                    <th colspan="2">Print/Emb. Start</th>
                    <th colspan="2">Bulk Sewing Input</th>
                    <th colspan="2">Bulk Wash Start</th>
                    <th colspan="2">Bulk Finishing Start</th>
                    <th colspan="2">Bulk Cutting Close</th>
                    <th colspan="2">Print/Emb. Close</th>
                    <th colspan="2">Bulk Sewing Close</th>
                    <th colspan="2">Bulk Wash Close/ Finishing Received</th>
                    <th colspan="2">Bulk Finishing Close</th>
                    <th colspan="2">Pre-final Inspection</th>
                    <th colspan="2">Final Inspection</th>
                    <th colspan="2">ex-factory</th>

                </tr>
                <tr>
                    <th><label id="total_qty"></label></th>
                    <th colspan="2"></th>
                    <th><label id="AvgLeadTime"></label></th>
                    <th><label id="AvgOrderFreeTime"></label></th>
                    <th>Plan</th>
                    <th>Actual</th>
                    <th>Plan</th>
                    <th>Actual</th>
                    <th>Plan</th>
                    <th>Actual</th>
                    <th>Plan</th>
                    <th>Actual</th>
                    <th>Plan</th>
                    <th>Actual</th>
                    <th>Plan</th>
                    <th>Actual</th>
                    <th>Plan</th>
                    <th>Actual</th>
                    <th>Plan</th>
                    <th>Actual</th>
                    <th>Plan</th>
                    <th>Actual</th>
                    <th>Plan</th>
                    <th>Actual</th>
                    <th>Plan</th>
                    <th>Actual</th>
                    <th>Plan</th>
                    <th>Actual</th>
                    <th>Plan</th>
                    <th>Actual</th>
                    <th>Plan</th>
                    <th>Actual</th>
                    <th>Plan</th>
                    <th>Actual</th>
                    <th>Plan</th>
                    <th>Actual</th>
                    <th>Plan</th>
                    <th>Actual</th>
                    <th>Plan</th>
                    <th>Actual</th>
                    <th>Plan</th>
                    <th>Actual</th>
                    <th>Plan</th>
                    <th>Actual</th>
                    <th>Plan</th>
                    <th>Actual</th>
                    <th>Plan</th>
                    <th>Actual</th>
                    <th>Plan</th>
                    <th>Actual</th>
                    <th>Plan</th>
                    <th>Actual</th>
                    <th>Plan</th>
                    <th>Actual</th>
                    <th>Plan</th>
                    <th>Actual</th>
                    <th>Plan</th>
                    <th>Actual</th>
                    <th>Plan</th>
                    <th>Actual</th>
                    <th>Plan</th>
                    <th>Actual</th>
                    <th>Plan</th>
                    <th>Actual</th>
                    <th>Plan</th>
                    <th>Actual</th>
                    <th>Plan</th>
                    <th>Actual</th>
                    {{-- <th>Plan</th>
                    <th>Actual</th> --}}
                </tr>
            </thead>
            <tbody class="text-nowrap bg-light" id="tnaTableBody">
                @php
                    $sl = 1;
                @endphp
                @forelse ($tnas as $tna)
                    {{-- @dd($tna) --}}
                    <tr>
                        @if (auth()->user()->role_id == 4 || auth()->user()->role_id == 1)
                            <td>
                                <a href="{{ route('tnas.show', $tna->id) }}" class="btn btn-sm btn-outline-success"
                                    data-toggle="tooltip" data-placement="top" title="show">
                                    <i class="fas fa-eye"></i>{{ $sl++ }}
                                </a>
                            </td>
                        @elseif (auth()->user()->role_id == 3)
                            @php
                                $privileges = DB::table('buyer_assigns')
                                    ->where('buyer_id', $tna->buyer_id)
                                    ->where('user_id', auth()->user()->id)
                                    ->count();
                                // dd($privileges)
                            @endphp
                            @if ($privileges > 0)
                                {{-- @dd($tna->buyer) --}}
                                <td>
                                    <a href="{{ route('tnas.show', $tna->id) }}" class="btn btn-sm btn-outline-success"
                                        data-toggle="tooltip" data-placement="top" title="show">
                                        <i class="fas fa-eye"></i>{{ $sl++ }}
                                    </a>
                                </td>
                            @else
                                <td>{{ $sl++ }}</td>
                            @endif
                        @else
                            <td>{{ $sl++ }}</td>
                        @endif

                        <td>{{ $tna->buyer }}</td>
                        {{-- <td>{{ $tna->style }}</td>
                        <td>{{ $tna->po }}</td> --}}
                        <td class="text-wrap"> {{ str_replace(',', ' ', $tna->style) }}</td>
                        <td class="text-wrap"> {{ str_replace(',', ' ', $tna->po) }}</td>
                        <td>{{ $tna->item }}</td>
                        <td id="qty_pcs">{{ $tna->qty_pcs }}</td>
                        <td>{{ \Carbon\Carbon::parse($tna->po_receive_date)->format('d-M-y') ?? '' }}</td>
                        <td class="text-bold" id="shortablerow">
                            {{ \Carbon\Carbon::parse($tna->shipment_etd)->format('d-M-y') ?? '' }}
                        </td>
                        <td id="total_lead_time">{{ $tna->total_lead_time }}</td>
                        <td id="order_free_time">
                            @if ($tna->pp_meeting_actual == null)
                                @php
                                    $today = \Carbon\Carbon::parse($tna->pp_meeting_plan);
                                    $shipment_etd = \Carbon\Carbon::parse($tna->shipment_etd);
                                    $diffDays = $today->diffInDays($shipment_etd, false);
                                    if ($diffDays > 0) {
                                        echo $diffDays;
                                    } else {
                                        echo '0';
                                    }
                                @endphp
                            @else
                                @php
                                    $today = \Carbon\Carbon::parse($tna->pp_meeting_plan);
                                    $shipment_etd = \Carbon\Carbon::parse($tna->pp_meeting_actual);
                                    $diffDays = $today->diffInDays($shipment_etd, false);
                                    if ($diffDays > 0) {
                                        echo $diffDays;
                                    } else {
                                        echo '0';
                                    }
                                @endphp
                            @endif

                        </td>
                        {{-- @foreach (['lab_dip_submission', 'fabric_booking', 'fit_sample_submission', 'print_strike_off_submission', 'bulk_accessories_booking', 'fit_comments', 'bulk_yarn_inhouse', 'bulk_accessories_inhouse', 'pp_sample_submission', 'bulk_fabric_knitting', 'pp_comments_receive', 'bulk_fabric_dyeing', 'bulk_fabric_delivery', 'pp_meeting', 'cutting', 'etd'] as $task)
                            @foreach (['plan', 'actual'] as $type)
                                @php
                                    $date = $tna->{$task . '_' . $type};
                                    $cellClass = '';
                                    $explanation = ''; // Default explanation to empty

                                    // Check if $date is a valid date and not 'N/A'
                                    if ($date && $date !== 'N/A' && strtotime($date) !== false) {
                                        try {
                                            $cellDate = \Carbon\Carbon::parse($date);
                                            $today = \Carbon\Carbon::now();
                                            $diffDays = $today->diffInDays($cellDate, false);

                                            if ($type === 'plan' && empty($tna->{$task . '_actual'})) {
                                                if ($cellDate->isToday() || $cellDate->lt($today)) {
                                                    $cellClass = 'bg-red';
                                                } elseif ($diffDays <= 2) {
                                                    $cellClass = 'bg-yellow';
                                                } else {
                                                    $cellClass = 'bg-light';
                                                }
                                            }

                                            if ($type === 'actual' && $tna->{$task . '_plan'}) {
                                                $planDate = \Carbon\Carbon::parse($tna->{$task . '_plan'});
                                                if ($cellDate->gt($planDate)) {
                                                    $cellClass = 'text-danger font-weight-bold';
                                                }
                                            }
                                        } catch (\Exception $e) {
                                            // Log the error or handle it appropriately
                                            $date = ''; // Reset the date if parsing fails
                                        }
                                    } elseif ($date === 'N/A') {
                                        $cellClass = 'text-muted'; // Optional: add a class for 'N/A'
                                    }
                                @endphp

                                <!-- if actual date is empty then modal button show else show date -->
                                @if ($type === 'actual' && empty($date))
                                    @if (auth()->user()->role_id == 3)
                                        @php
                                            $buyer_privilage = DB::table('buyer_assigns')
                                                ->where('buyer_id', $tna->buyer_id)
                                                ->where('user_id', auth()->user()->id)
                                                ->count();
                                        @endphp
                                        @if ($buyer_privilage > 0)
                                            <td class="{{ $cellClass }}" data-id="{{ $tna->id }}"
                                                data-task="{{ $task . '_' . $type }}" onclick="openModal(this)"
                                                data-plan-date="{{ $tna->{$task . '_plan'} }}">
                                            </td>
                                        @endif
                                    @else
                                        <td></td>
                                    @endif
                                @else
                                    @php
                                        $explanation =
                                            DB::table('tna_explanations')
                                                ->where('perticulars', $task . '_' . $type)
                                                ->where('tna_id', $tna->id)
                                                ->first()->explanation ?? '';
                                    @endphp
                                    <td class="{{ $cellClass }}" data-toggle="tooltip" data-placement="top"
                                        title="{{ $explanation }}">
                                        {{ $date == 'N/A' ? 'N/A' : ($date ? \Carbon\Carbon::parse($date)->format('d-M-y') : '') }}
                                    </td>
                                @endif
                            @endforeach
                        @endforeach --}}

                           @foreach (['lab_dip_submission', 'fabric_booking', 'fit_sample_submission', 'print_strike_off_submission', 'bulk_accessories_booking', 'fit_comments', 'bulk_yarn_inhouse', 'bulk_accessories_inhouse', 'pp_sample_submission', 'bulk_fabric_knitting', 'pp_comments_receive', 'bulk_fabric_dyeing', 'bulk_fabric_delivery', 'pp_meeting', 'etd', 'fabrics_and_accessories_inspection', 'size_set_making', 'pattern_correction', 'machines_layout', 'cutting', 'print_start', 'bulk_sewing_input', 'bulk_wash_start', 'bulk_finishing_start', 'bulk_cutting_close', 'print_close', 'bulk_sewing_close', 'bulk_wash_close', 'bulk_finishing_close', 'pre_final_inspection', 'final_inspection', 'ex_factory'] as $task)
                            @foreach (['plan', 'actual'] as $type)
                                @php
                                    $date = $tna->{$task . '_' . $type};
                                    $cellClass = '';

                                    if ($date && $date != 'N/A') {
                                        $today = \Carbon\Carbon::now();
                                        $cellDate = \Carbon\Carbon::parse($date);
                                        $diffDays = $today->diffInDays($cellDate, false);

                                        if ($type === 'plan' && empty($tna->{$task . '_actual'})) {
                                            if ($cellDate->isToday() || $cellDate->lt($today)) {
                                                $cellClass = 'bg-red';
                                            } elseif ($diffDays <= 2) {
                                                $cellClass = 'bg-yellow';
                                            } else {
                                                $cellClass = 'bg-light';
                                            }
                                        }

                                        if ($type === 'actual' && $tna->{$task . '_plan'}) {
                                            $planDate = \Carbon\Carbon::parse($tna->{$task . '_plan'});
                                            $actualDate = \Carbon\Carbon::parse($date);
                                            if ($cellDate->gt($planDate)) {
                                                $cellClass = 'text-danger font-weight-bold';
                                            }
                                            if ($cellDate->gt($actualDate)) {
                                                $cellClass = 'bg-light';
                                            }
                                        }
                                    } elseif ($date == 'N/A') {
                                        $date = 'N/A';
                                    }
                                @endphp
                                @if ($type === 'actual' && empty($date))
                                    @if (
                                        $task == 'lab_dip_submission' ||
                                            $task == 'fabric_booking' ||
                                            $task == 'fit_sample_submission' ||
                                            $task == 'print_strike_off_submission' ||
                                            $task == 'bulk_accessories_booking' ||
                                            $task == 'fit_comments' ||
                                            $task == 'bulk_yarn_inhouse' ||
                                            $task == 'bulk_accessories_inhouse' ||
                                            $task == 'pp_sample_submission' ||
                                            $task == 'bulk_fabric_knitting' ||
                                            $task == 'pp_comments_receive' ||
                                            $task == 'bulk_fabric_dyeing' ||
                                            $task == 'bulk_fabric_delivery')
                                        @if (auth()->user()->role_id == 1 || auth()->user()->role_id == 3 || auth()->user()->role_id == 4)
                                            <td class="{{ $cellClass }}" data-id="{{ $tna->id }}"
                                                data-task="{{ $task . '_' . $type }}" onclick="openModal(this)"></td>
                                        @else
                                            <td class="{{ $cellClass }}" data-id="{{ $tna->id }}"
                                                data-task="{{ $task . '_' . $type }}"></td>
                                        @endif
                                    @elseif ($task == 'fabrics_and_accessories_inspection')
                                        @if (auth()->user()->role_id == 1 ||
                                                auth()->user()->role_id == 10008 ||
                                                auth()->user()->role_id == 10009 ||
                                                auth()->user()->role_id == 4)
                                            <td class="{{ $cellClass }}" data-id="{{ $tna->id }}"
                                                data-task="{{ $task . '_' . $type }}" onclick="openModal(this)"></td>
                                        @else
                                            <td class="{{ $cellClass }}" data-id="{{ $tna->id }}"
                                                data-task="{{ $task . '_' . $type }}"></td>
                                        @endif
                                    @elseif ($task == 'size_set_making')
                                        @if (auth()->user()->role_id == 1 ||
                                                auth()->user()->role_id == 10005 ||
                                                auth()->user()->role_id == 4)
                                            <td class="{{ $cellClass }}" data-id="{{ $tna->id }}"
                                                data-task="{{ $task . '_' . $type }}" onclick="openModal(this)"></td>
                                        @else
                                            <td class="{{ $cellClass }}" data-id="{{ $tna->id }}"
                                                data-task="{{ $task . '_' . $type }}"></td>
                                        @endif
                                    @elseif ($task == 'pattern_correction')
                                        @if (auth()->user()->role_id == 1 ||
                                                auth()->user()->role_id == 10007 ||
                                                auth()->user()->role_id == 10008 ||
                                                auth()->user()->role_id == 4)
                                            <td class="{{ $cellClass }}" data-id="{{ $tna->id }}"
                                                data-task="{{ $task . '_' . $type }}" onclick="openModal(this)"></td>
                                        @else
                                            <td class="{{ $cellClass }}" data-id="{{ $tna->id }}"
                                                data-task="{{ $task . '_' . $type }}"></td>
                                        @endif
                                    @elseif ($task == 'machines_layout')
                                        @if (auth()->user()->role_id == 1 || auth()->user()->role_id == 10006 || auth()->user()->role_id == 4)
                                            <td class="{{ $cellClass }}" data-id="{{ $tna->id }}"
                                                data-task="{{ $task . '_' . $type }}" onclick="openModal(this)"></td>
                                        @else
                                            <td class="{{ $cellClass }}" data-id="{{ $tna->id }}"
                                                data-task="{{ $task . '_' . $type }}"></td>
                                        @endif
                                    @elseif (
                                        $task == 'cutting' ||
                                            $task == 'print_start' ||
                                            $task == 'bulk_sewing_input' ||
                                            $task == 'bulk_wash_start' ||
                                            $task == 'bulk_finishing_start' ||
                                            $task == 'bulk_cutting_close' ||
                                            $task == 'print_close' ||
                                            $task == 'bulk_sewing_close' ||
                                            $task == 'bulk_wash_close' ||
                                            $task == 'bulk_finishing_close')
                                        @if (auth()->user()->role_id == 1 || auth()->user()->role_id == 4 || auth()->user()->role_id == 5)
                                            <td class="{{ $cellClass }}" data-id="{{ $tna->id }}"
                                                data-task="{{ $task . '_' . $type }}" onclick="openModal(this)"></td>
                                        @else
                                            <td class="{{ $cellClass }}" data-id="{{ $tna->id }}"
                                                data-task="{{ $task . '_' . $type }}"></td>
                                        @endif
                                    @elseif ($task == 'pre_final_inspection' || $task == 'final_inspection' || $task == 'ex_factory')
                                        @if (auth()->user()->role_id == 1 || auth()->user()->role_id == 10008 || auth()->user()->role_id == 4)
                                            <td class="{{ $cellClass }}" data-id="{{ $tna->id }}"
                                                data-task="{{ $task . '_' . $type }}" onclick="openModal(this)"></td>
                                        @else
                                            <td class="{{ $cellClass }}" data-id="{{ $tna->id }}"
                                                data-task="{{ $task . '_' . $type }}"></td>
                                        @endif
                                    @elseif ($task == 'final_inspection' || $task == 'ex_factory')
                                        @if (auth()->user()->role_id == 1 || auth()->user()->role_id == 10005 || auth()->user()->role_id == 4)
                                            <td class="{{ $cellClass }}" data-id="{{ $tna->id }}"
                                                data-task="{{ $task . '_' . $type }}" onclick="openModal(this)"></td>
                                        @else
                                            <td class="{{ $cellClass }}" data-id="{{ $tna->id }}"
                                                data-task="{{ $task . '_' . $type }}"></td>
                                        @endif
                                    @else
                                        <td class="{{ $cellClass }}" data-id="{{ $tna->id }}"
                                            data-task="{{ $task . '_' . $type }}">
                                        </td>
                                    @endif
                                @else
                                    <td class="{{ $cellClass }}" data-id="{{ $tna->id }}"
                                        data-task="{{ $task . '_' . $type }}">
                                        {{ $date == 'N/A' ? 'N/A' : ($date ? \Carbon\Carbon::parse($date)->format('d-M-y') : '') }}
                                    </td>
                                @endif
                            @endforeach
                        @endforeach


                    </tr>
                @empty
                    <tr>
                        <td colspan="41" class="text-center">No TNA Found</td>
                    </tr>
                @endforelse
            </tbody>
            {{-- {{ $tnas->links() }} --}}

        </table>

    </div> <!-- container -->

    </div>
    <a href="{{ route('tnas.index') }}" class="btn btn-outline-secondary bg-light m-2">
        <i class="fas fa-arrow-left"></i> Cancel
    </a>
    <a href="{{ route('tnas_dashboard') }}" class="btn btn-outline-secondary bg-light m-2">
        <i class="fas fa-sync"></i> Refresh Page </a>
    <!-- Modal for Date Update -->
    <div class="modal fade" id="dateModal" tabindex="-1" role="dialog" aria-labelledby="dateModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="dateModalLabel">Update Date</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="dateForm">
                        @csrf
                        <input type="hidden" name="id" id="tnaId">
                        <input type="hidden" name="task" id="taskName">
                        <div class="form-group">
                            <label for="dateInput">Date</label>
                            <input type="date" class="form-control" id="dateInput" name="date"
                                value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                            <br>
                            <div class="form-check" id="naCheckbox" style="display:none;">
                                <input class="form-check-input" type="checkbox" value="na" id="naButton">
                                <label class="form-check-label" for="naButton">N/A</label>
                            </div>
                            <textarea class="form-control" id="explanation" rows="3" style="display: none;" placeholder="Remarks"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-outline-primary" onclick="submitDate()">Save
                        changes</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <!-- Add jQuery and Bootstrap JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

    <script>
        // document.addEventListener('DOMContentLoaded', function() {
        //     const table = document.querySelector('#PrintTable');
        //     const headers = table.querySelectorAll('thead th');
        //     const rows = table.querySelectorAll('tbody tr');
        //     const filterButtons = document.querySelectorAll('[data-buyer]');

        //     filterButtons.forEach(button => {
        //         button.addEventListener('click', function() {
        //             const buyer = this.getAttribute('data-buyer');
        //             filterByBuyer(buyer);
        //             console.log(buyer);

        //         });
        //     });

        //     // Function to calculate the maximum width of each visible column
        //     function calculateColumnWidths() {
        //         let columnWidths = Array.from(headers).map(header => header.offsetWidth);

        //         // Update column widths based on the maximum content width of visible rows
        //         rows.forEach(row => {
        //             if (row.style.display !== 'none') { // Only consider visible rows
        //                 row.querySelectorAll('td').forEach((cell, index) => {
        //                     if (index < columnWidths.length) {
        //                         const cellWidth = cell.scrollWidth;
        //                         if (cellWidth > columnWidths[index]) {
        //                             columnWidths[index] = cellWidth;
        //                         }
        //                     }
        //                 });
        //             }
        //         });

        //         return columnWidths;
        //     }

        //     // Function to set sticky column widths and positions
        //     function updateStickyColumnWidths() {
        //         const columnWidths = calculateColumnWidths();
        //         let cumulativeWidth = 0;

        //         headers.forEach((header, index) => {
        //             if (index < 4) { // Adjust if more columns need to be sticky
        //                 header.style.width = `${columnWidths[index]}px`;
        //                 header.style.left = `${cumulativeWidth}px`;
        //                 header.style.position = 'sticky';
        //                 header.style.zIndex = '2';

        //                 const cells = table.querySelectorAll(`tbody td:nth-child(${index + 1})`);
        //                 cells.forEach(cell => {
        //                     if (cell.closest('tr').style.display !==
        //                         'none') { // Only update visible rows
        //                         cell.style.width = `${columnWidths[index]}px`;
        //                         cell.style.left = `${cumulativeWidth}px`;
        //                         cell.style.position = 'sticky';
        //                         cell.style.zIndex = '1';
        //                         cell.style.background = '#fff';
        //                     }
        //                 });

        //                 cumulativeWidth += columnWidths[index];
        //             }
        //         });
        //     }

        //     // Function to filter by buyer and update the sticky columns
        //     function filterByBuyer(buyer) {
        //         const allBuyersBtn = document.getElementById('all-buyers-btn');
        //         allBuyersBtn.classList.remove('btn-primary');
        //         allBuyersBtn.classList.add('btn-outline-primary');
        //         allBuyersBtn.style.color = 'black';
        //         allBuyersBtn.style.fontWeight = 'normal';

        //         if (buyer === 'All Buyers') {
        //             localStorage.removeItem('buyer');
        //         } else {
        //             localStorage.setItem('buyer', buyer);
        //         }

        //         const rows = document.querySelectorAll('#tnaTableBody tr');
        //         rows.forEach(row => {
        //             const buyerCell = row.querySelector('td:nth-child(2)');
        //             if (buyer === 'All Buyers' || buyerCell.textContent === buyer) {
        //                 row.style.display = '';
        //             } else {
        //                 row.style.display = 'none';
        //             }
        //         });

        //         // Recalculate totals, averages, and update sticky columns
        //         calculateTotalsAndAverages();
        //         updateStickyColumnWidths();
        //     }

        //     // Initialize on page load
        //     updateStickyColumnWidths();
        //     window.addEventListener('resize', updateStickyColumnWidths);

        //     // Event listener for "All Buyers" button
        //     document.getElementById('all-buyers-btn').addEventListener('click', () => {
        //         localStorage.removeItem('buyer');
        //         const rows = document.querySelectorAll('#tnaTableBody tr');
        //         rows.forEach(row => {
        //             row.style.display = '';
        //         });
        //         calculateTotalsAndAverages();
        //         updateStickyColumnWidths();
        //     });

        //     // On page load, check for stored buyer and filter if present
        //     window.onload = function() {
        //         const buyer = localStorage.getItem('buyer');
        //         if (buyer) {
        //             filterByBuyer(buyer);
        //         } else {
        //             calculateTotalsAndAverages();
        //             updateStickyColumnWidths();
        //         }
        //     };
        // });
        document.addEventListener('DOMContentLoaded', function() {
            const table = document.querySelector('#PrintTable');
            const headers = table.querySelectorAll('thead th');
            const rows = table.querySelectorAll('tbody tr');
            const filterButtons = document.querySelectorAll('[data-buyer]');

            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const buyer = this.getAttribute('data-buyer');
                    filterByBuyer(buyer);
                    console.log(buyer);
                });
            });

            // Function to calculate the maximum width of each visible column
            function calculateColumnWidths() {
                let columnWidths = Array.from(headers).map(header => header.offsetWidth);

                rows.forEach(row => {
                    if (row.style.display !== 'none') {
                        row.querySelectorAll('td').forEach((cell, index) => {
                            if (index < columnWidths.length) {
                                const cellWidth = cell.scrollWidth;
                                if (cellWidth > columnWidths[index]) {
                                    columnWidths[index] = cellWidth;
                                }
                            }
                        });
                    }
                });

                // Set minimum and maximum widths for the columns
                columnWidths = columnWidths.map(width => Math.min(Math.max(width, 50),
                    300)); // Adjust the values as needed

                return columnWidths;
            }

            // Function to set sticky column widths and positions
            function updateStickyColumnWidths() {
                const columnWidths = calculateColumnWidths();
                let cumulativeWidth = 0;

                headers.forEach((header, index) => {
                    if (index < 4) { // Adjust if more columns need to be sticky
                        header.style.width = `${columnWidths[index]}px`;
                        header.style.left = `${cumulativeWidth}px`;
                        header.style.position = 'sticky';
                        header.style.zIndex = '2';

                        const cells = table.querySelectorAll(`tbody td:nth-child(${index + 1})`);
                        cells.forEach(cell => {
                            if (cell.closest('tr').style.display !== 'none') {
                                cell.style.width = `${columnWidths[index]}px`;
                                cell.style.left = `${cumulativeWidth}px`;
                                cell.style.position = 'sticky';
                                cell.style.zIndex = '1';
                                cell.style.background = '#fff';
                            }
                        });

                        cumulativeWidth += columnWidths[index];
                    }
                });
            }

            // Function to filter by buyer and update the sticky columns
            function filterByBuyer(buyer) {
                const allBuyersBtn = document.getElementById('all-buyers-btn');
                allBuyersBtn.classList.remove('btn-primary');
                allBuyersBtn.classList.add('btn-outline-primary');
                allBuyersBtn.style.color = 'black';
                allBuyersBtn.style.fontWeight = 'normal';

                if (buyer === 'All Buyers') {
                    localStorage.removeItem('buyer');
                } else {
                    localStorage.setItem('buyer', buyer);
                }

                const rows = document.querySelectorAll('#tnaTableBody tr');
                rows.forEach(row => {
                    const buyerCell = row.querySelector('td:nth-child(2)');
                    if (buyer === 'All Buyers' || buyerCell.textContent === buyer) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Recalculate totals, averages, and update sticky columns
                calculateTotalsAndAverages();
                updateStickyColumnWidths();
            }

            // Initialize on page load
            updateStickyColumnWidths();

            // Throttle resize event to prevent performance issues
            let resizeTimeout;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(updateStickyColumnWidths, 100);
            });

            // Event listener for "All Buyers" button
            document.getElementById('all-buyers-btn').addEventListener('click', () => {
                localStorage.removeItem('buyer');
                const rows = document.querySelectorAll('#tnaTableBody tr');
                rows.forEach(row => {
                    row.style.display = '';
                });
                calculateTotalsAndAverages();
                updateStickyColumnWidths();
            });

            // On page load, check for stored buyer and filter if present
            window.onload = function() {
                const buyer = localStorage.getItem('buyer');
                if (buyer) {
                    filterByBuyer(buyer);
                } else {
                    calculateTotalsAndAverages();
                    updateStickyColumnWidths();
                }
            };
        });
    </script>




    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Make the "Shipment/ETD" column sortable
            document.getElementById('shortablehead').addEventListener('click', function() {
                sortTable(7); // Assuming this is the index of the column you want to sort
            });
        });

        function sortTable(columnIndex) {
            const table = document.getElementById('PrintTable');
            const rows = Array.from(table.querySelectorAll('tbody tr'));

            rows.sort((a, b) => {
                const aText = a.children[columnIndex].textContent.trim();
                const bText = b.children[columnIndex].textContent.trim();
                return aText.localeCompare(bText, undefined, {
                    numeric: true
                });
            });

            rows.forEach(row => table.querySelector('tbody').appendChild(row));
            calculateTotalsAndAverages(); // Recalculate after sorting
        }





        // Function to calculate total quantity, average lead time, and average order free time
        function calculateTotalsAndAverages() {
            const visibleRows = document.querySelectorAll('#tnaTableBody tr:not([style*="display: none"])');

            // Calculate total quantity
            let totalQty = 0;
            visibleRows.forEach(row => {
                const qtyCell = row.querySelector('#qty_pcs');
                totalQty += parseInt(qtyCell.textContent);
            });
            document.getElementById('total_qty').textContent = totalQty;

            // Calculate average lead time
            let totalLeadTime = 0;
            visibleRows.forEach(row => {
                const leadTimeCell = row.querySelector('#total_lead_time');
                totalLeadTime += parseInt(leadTimeCell.textContent);
            });

            // Calculate average lead time and show ceil value
            document.getElementById('AvgLeadTime').textContent = Math.ceil(totalLeadTime / visibleRows.length);

            // Calculate average order free time
            let totalOrderFreeTime = 0;
            visibleRows.forEach(row => {
                const orderFreeTimeCell = row.querySelector('#order_free_time');
                totalOrderFreeTime += parseInt(orderFreeTimeCell.textContent);
            });

            // Calculate average order free time and show ceil value
            document.getElementById('AvgOrderFreeTime').textContent = Math.ceil(totalOrderFreeTime / visibleRows.length);
        }

        // // Function to filter by buyer and recalculate totals and averages
        // function filterByBuyer(buyer) {
        //     const allBuyersBtn = document.getElementById('all-buyers-btn');
        //     allBuyersBtn.classList.remove('btn-primary');
        //     allBuyersBtn.classList.add('btn-outline-primary');
        //     allBuyersBtn.style.color = 'black';
        //     allBuyersBtn.style.fontWeight = 'normal';

        //     if (buyer === 'All Buyers') {
        //         localStorage.removeItem('buyer');
        //     } else {
        //         localStorage.setItem('buyer', buyer);
        //     }

        //     const rows = document.querySelectorAll('#tnaTableBody tr');
        //     rows.forEach(row => {
        //         if (buyer === 'All Buyers') {
        //             row.style.display = '';
        //         } else {
        //             const buyerCell = row.querySelector('td:nth-child(2)');
        //             if (buyerCell.textContent !== buyer) {
        //                 row.style.display = 'none';
        //             } else {
        //                 row.style.display = '';
        //             }
        //         }
        //     });

        //     // Recalculate totals and averages after filtering
        //     calculateTotalsAndAverages();
        //     // Update the sticky header and cells
        //     updateStickyColumnWidths();
        // }

        // // Event listener for "All Buyers" button to show all rows
        // document.getElementById('all-buyers-btn').addEventListener('click', () => {
        //     localStorage.removeItem('buyer');
        //     const rows = document.querySelectorAll('#tnaTableBody tr');
        //     rows.forEach(row => {
        //         row.style.display = '';
        //     });
        //     calculateTotalsAndAverages();

        // });

        // // Initial calculation
        // calculateTotalsAndAverages();

        // Function to generate a hash for the table's current content
        function getTableHash() {
            const tableContent = document.getElementById('tnaTableBody').innerHTML;
            // Simple hash function for the content
            return Array.from(tableContent).reduce((hash, char) => {
                hash = ((hash << 5) - hash) + char.charCodeAt(0);
                return hash & hash; // Convert to 32bit integer
            }, 0);
        }

        // Initial hash of the table data
        let currentTableHash = getTableHash();

        // Periodically update the table
        // setInterval(() => {
        //     $.ajax({
        //         url: "{{ route('tnas_dashboard_update') }}",
        //         type: 'GET',
        //         success: function(data) {
        //             const newTableHash = getTableHash(data);

        //             // Update only if the new data is different
        //             if (currentTableHash !== newTableHash) {
        //                 document.getElementById('tnaTableBody').innerHTML = data;
        //                 currentTableHash = newTableHash;

        //                 const buyer = localStorage.getItem('buyer');
        //                 if (buyer) {
        //                     filterByBuyer(buyer);
        //                 } else {
        //                     calculateTotalsAndAverages();
        //                      updateStickyColumnWidths();

        //                 }

        //             }
        //         },
        //         error: function(error) {
        //             console.error('Ajax error:', error);
        //         }
        //     });
        // }, 5000);

        // // On page load, check for stored buyer and filter if present
        // window.onload = function() {
        //     const buyer = localStorage.getItem('buyer');
        //     if (buyer) {
        //         filterByBuyer(buyer);
        //     } else {
        //         calculateTotalsAndAverages();
        //     }
        // };

        $(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });

        function downloadExcel() {
            var tab_text = "<table border='2px'><tr bgcolor='#87AFC6'>";
            var tab = document.getElementById('PrintTable'); // ID of the table

            // Loop through each row in the table
            for (var j = 0; j < tab.rows.length; j++) {
                tab_text += "<tr>" + tab.rows[j].innerHTML + "</tr>";
            }

            tab_text += "</table>";
            tab_text = tab_text.replace(/<A[^>]*>|<\/A>/g, ""); // Remove links
            tab_text = tab_text.replace(/<img[^>]*>/gi, ""); // Remove images
            tab_text = tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // Remove inputs

            // Create a Blob with the table data
            var blob = new Blob([tab_text], {
                type: 'application/vnd.ms-excel'
            });

            // Create a link element
            var link = document.createElement('a');
            link.href = URL.createObjectURL(blob);

            link.download = 'table-data.xls'; // Filename for the downloaded file

            // Append the link to the body and trigger the download
            document.body.appendChild(link);
            link.click();

            // Clean up
            document.body.removeChild(link);
        }
    </script>

    <script>
        function openModal(cell) {
            const id = cell.getAttribute('data-id');
            const task = cell.getAttribute('data-task');
            const planDate = cell.getAttribute('data-plan-date');
            document.getElementById('tnaId').value = id;
            document.getElementById('taskName').value = task;

            if (task === 'print_strike_off_submission_actual' || task === 'fit_sample_submission_actual') {
                document.getElementById('dateInput').style.display = 'block';
                document.getElementById('naCheckbox').style.display = 'block';
            } else {
                document.getElementById('dateInput').style.display = 'block';
                document.getElementById('naCheckbox').style.display = 'none';
            }

            const today = new Date().toISOString().split('T')[0];
            document.getElementById('explanation').style.display = planDate && new Date(planDate) < new Date(today) ?
                'block' : 'none';

            $('#dateModal').modal('show');
        }

        // function submitDate() {
        //     const id = document.getElementById('tnaId').value;
        //     const task = document.getElementById('taskName').value;
        //     const date = document.getElementById('dateInput').value;
        //     const naChecked = document.getElementById('naButton').checked;
        //     const explanation = document.getElementById('explanation').value;

        //     $.ajax({
        //         url: '/update-tna-date', // Your route to handle the date update
        //         type: 'POST',
        //         data: {
        //             _token: $('input[name="_token"]').val(),
        //             id: id,
        //             task: task,
        //             date: naChecked ? 'N/A' : date,
        //             explanation: explanation
        //         },
        //         success: function(response) {
        //             // Optionally, update the cell content and class here without reloading
        //             // location.reload();
        //             // refresh the url with the updated data
        //             const cellToUpdate = document.querySelector(`[data-id="${id}"][data-task="${task}"]`);
        //             if (cellToUpdate) {
        //                 cellToUpdate.textContent = naChecked ? 'N/A' : date;
        //             }

        //             // Optionally update totals, averages, and sticky column widths
        //             calculateTotalsAndAverages();
        //             updateStickyColumnWidths();


        //         }
        //     });

        //     $('#dateModal').modal('hide');
        // }

        function submitDate() {
            const id = document.getElementById('tnaId').value;
            const task = document.getElementById('taskName').value;
            const dateInput = document.getElementById('dateInput').value;
            const naChecked = document.getElementById('naButton').checked;
            const explanation = document.getElementById('explanation').value;

            const formattedDate = naChecked ? 'N/A' : formatDate(dateInput); // Format the date

            $.ajax({
                url: '/update-tna-date', // Your route to handle the date update
                type: 'POST',
                data: {
                    _token: $('input[name="_token"]').val(),
                    id: id,
                    task: task,
                    date: formattedDate,
                    explanation: explanation
                },
                success: function(response) {
                    // // Update the cell content dynamically without reloading
                    // const cellToUpdate = document.querySelector(`[data-id="${id}"][data-task="${task}"]`);
                    // if (cellToUpdate) {
                    //     cellToUpdate.textContent = formattedDate;
                    //     cellToUpdate.classList.add('updated-class'); // Optional: add a class for styling
                    // }
                    console.log(response);
                    // window.location.reload();

                    $.ajax({
                url: "{{ route('tnas_dashboard') }}",
                type: 'GET',
                success: function(data) {
                    $('#tnaTableBody').html(data);
                    calculateTotalsAndAverages();
                    updateStickyColumnWidths();
                },
                error: function(error) {
                    console.error('Ajax error:', error);
                }
            });
                

                    // // Optionally update totals, averages, and sticky column widths
                    // calculateTotalsAndAverages();
                    // updateStickyColumnWidths();
                },
                error: function(error) {
                    console.error('Error updating date:', error);
                }
            });

            $('#dateModal').modal('hide');
        }

        // Function to format the date as "d-M-y"
        function formatDate(dateStr) {
            if (!dateStr) return '';
            const dateObj = new Date(dateStr);

            const day = dateObj.getDate().toString().padStart(2, '0'); // Day with leading zero
            const month = dateObj.toLocaleString('en-us', {
                month: 'short'
            }); // Short month name
            const year = dateObj.getFullYear().toString().substr(-2); // Last two digits of the year

            return `${day}-${month}-${year}`; // Format "d-M-y"
        }


        // Reset modal form on close
        $('#dateModal').on('hidden.bs.modal', function() {
            $('#dateForm').trigger('reset');
        });

    </script>

</x-backend.layouts.report_master>
