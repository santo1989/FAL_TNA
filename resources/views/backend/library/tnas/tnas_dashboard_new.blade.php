<x-backend.layouts.report_master>
    <style>
        /* Sticky Columns and Rows */
        thead th:nth-child(-n+4),
        tbody td:nth-child(-n+4) {
            position: sticky;
            left: 0;
            z-index: 1;
            background-color: #f8f9fa; /* Match tbody background */
        }

        thead th:nth-child(-n+4) {
            z-index: 2; /* Ensure headers are above body cells */
            background-color: #343a40; /* Match thead background */
        }

        thead {
            position: sticky;
            top: 0;
            z-index: 3; /* Ensure headers are above everything */
        }

        /* Hover Effects */
        #PrintTable tbody tr:hover td {
            background-color: #ffffff00;
        }

        #PrintTable tbody tr:hover td:nth-child(-n+4) {
            background-color: #ffcc00;
        }

        /* Additional Styles */
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

        .sortable {
            cursor: pointer;
        }

        .sortable:hover {
            background-color: #f90303;
        }
    </style>

    <div class="container-fluid pt-2">
        <h4 class="text-center text-white">
            <img src="{{ asset('images/assets/FAL_logo.png') }}" alt="NTG" width="70px"> TNA Dashboard
        </h4>

        <!-- Search and Filters -->
        <div class="row justify-content-center pb-2">
            <div class="col-12 mb-3">

                <form method="GET" class="form-inline">
                    <input type="text" name="search" class="form-control form-control-sm mr-2" placeholder="Search by Buyer, Style, PO, Item" value="{{ request('search') }}">
                    <button type="submit" class="btn btn-sm btn-primary mr-2">Search</button>
                    <a href="{{ route('tnas_dashboard_new') }}" class="btn btn-sm btn-secondary mr-2">Reset</a>
                    <a href="{{ route('tnas.index') }}" class="btn btn-sm btn-info mr-2">TNA List</a>
                    <a href="{{ route('home') }}" class="btn btn-sm btn-danger">Home</a>
                </form>
            </div>

            <div class="col-12">
                <div class="btn-group flex-wrap">
                    <a href="{{ route('tnas_dashboard_new', ['buyer' => 'all'] + request()->except('buyer')) }}"
                       class="btn btn-sm {{ !request('buyer') ? 'btn-danger' : 'btn-outline-light' }}">
                        All Buyers
                    </a>
                    @foreach ($buyerList as $buyer)
                        <a href="{{ route('tnas_dashboard_new', ['buyer' => $buyer] + request()->except('page')) }}"
                           class="btn btn-sm {{ request('buyer') == $buyer ? 'btn-danger' : 'btn-outline-light' }}">
                            {{ $buyer }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Summary Stats -->
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body p-2">
                        <h6 class="card-title mb-0">Total Qty</h6>
                        <p class="card-text mb-0">{{ number_format($totalQty) }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body p-2">
                        <h6 class="card-title mb-0">Avg Lead Time</h6>
                        <p class="card-text mb-0">{{ round($totalLeadTime) }} Days</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body p-2">
                        <h6 class="card-title mb-0">Avg Free Time</h6>
                        <p class="card-text mb-0">{{ round($avgOrderFreeTime) }} Days</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <a href="{{ route('export.tnas.excel', request()->query()) }}"
                   class="btn btn-success btn-block h-100 d-flex align-items-center justify-content-center">
                    <i class="fas fa-file-excel mr-2"></i> Export Excel
                </a>
            </div>
        </div>

        <!-- Main Table -->
        <table class="table table-bordered table-hover text-center text-nowrap" style="font-size: 12px;" id="PrintTable">
            <thead class="thead-dark">
                <tr>
                    <th rowspan="2">Action</th>
                    <th rowspan="2">Buyer</th>
                    <th rowspan="2">Style</th>
                    <th rowspan="2">PO Number</th>
                    <th rowspan="2">Item</th>
                    <th>Qty (pcs)</th>
                    <th>PO Receive Date</th>
                    <th>Shipment/ETD</th>
                    <th>Total Lead Time</th>
                    <th>Order Free Time</th>
                    @foreach ([
                        'lab_dip_submission', 'fabric_booking', 'fit_sample_submission', 'print_strike_off_submission',
                        'bulk_accessories_booking', 'fit_comments', 'bulk_yarn_inhouse', 'bulk_accessories_inhouse',
                        'pp_sample_submission', 'bulk_fabric_knitting', 'pp_comments_receive', 'bulk_fabric_dyeing',
                        'bulk_fabric_delivery', 'pp_meeting', 'fabrics_and_accessories_inspection', 'size_set_making',
                        'pattern_correction', 'machines_layout', 'cutting', 'print_start', 'bulk_sewing_input',
                        'bulk_wash_start', 'bulk_finishing_start', 'bulk_cutting_close', 'print_close', 'bulk_sewing_close',
                        'bulk_wash_close', 'bulk_finishing_close', 'pre_final_inspection', 'final_inspection', 'ex_factory'
                    ] as $task)
                        <th colspan="2">{{ ucwords(str_replace('_', ' ', $task)) }}</th>
                    @endforeach
                </tr>
                <tr>
                    <th><label id="total_qty">{{ number_format($totalQty) }}</label></th>
                    <th colspan="2"></th>
                    <th><label id="AvgLeadTime">{{ round($totalLeadTime) }}</label></th>
                    <th><label id="AvgOrderFreeTime">{{ round($avgOrderFreeTime) }}</label></th>
                    @foreach ([
                        'lab_dip_submission', 'fabric_booking', 'fit_sample_submission', 'print_strike_off_submission',
                        'bulk_accessories_booking', 'fit_comments', 'bulk_yarn_inhouse', 'bulk_accessories_inhouse',
                        'pp_sample_submission', 'bulk_fabric_knitting', 'pp_comments_receive', 'bulk_fabric_dyeing',
                        'bulk_fabric_delivery', 'pp_meeting', 'fabrics_and_accessories_inspection', 'size_set_making',
                        'pattern_correction', 'machines_layout', 'cutting', 'print_start', 'bulk_sewing_input',
                        'bulk_wash_start', 'bulk_finishing_start', 'bulk_cutting_close', 'print_close', 'bulk_sewing_close',
                        'bulk_wash_close', 'bulk_finishing_close', 'pre_final_inspection', 'final_inspection', 'ex_factory'
                    ] as $task)
                        <th>Plan</th>
                        <th>Actual</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="text-nowrap bg-light" id="tnaTableBody">
                @forelse ($tnas as $tna)
                    <tr>
                        <td>
                            <a href="{{ route('tnas.show', $tna->id) }}" class="btn btn-sm btn-outline-success">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                        <td>{{ $tna->buyer }}</td>
                        <td>{{ $tna->style }}</td>
                        <td>{{ $tna->po }}</td>
                        <td>{{ $tna->item }}</td>
                        <td>{{ $tna->qty_pcs }}</td>
                        <td>{{ $tna->po_receive_date ? \Carbon\Carbon::parse($tna->po_receive_date)->format('d-M-y') : '' }}</td>
                        <td>{{ $tna->shipment_etd ? \Carbon\Carbon::parse($tna->shipment_etd)->format('d-M-y') : '' }}</td>
                        <td>{{ $tna->total_lead_time }}</td>
                        <td>
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
                        @foreach ([
                            'lab_dip_submission', 'fabric_booking', 'fit_sample_submission', 'print_strike_off_submission',
                            'bulk_accessories_booking', 'fit_comments', 'bulk_yarn_inhouse', 'bulk_accessories_inhouse',
                            'pp_sample_submission', 'bulk_fabric_knitting', 'pp_comments_receive', 'bulk_fabric_dyeing',
                            'bulk_fabric_delivery', 'pp_meeting', 'fabrics_and_accessories_inspection', 'size_set_making',
                            'pattern_correction', 'machines_layout', 'cutting', 'print_start', 'bulk_sewing_input',
                            'bulk_wash_start', 'bulk_finishing_start', 'bulk_cutting_close', 'print_close', 'bulk_sewing_close',
                            'bulk_wash_close', 'bulk_finishing_close', 'pre_final_inspection', 'final_inspection', 'ex_factory'
                        ] as $task)
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
        </table>

        <!-- Pagination -->
        <div class="row justify-content-center">
            <div class="col-12">
                {{ $tnas->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

    <!-- Modal for Date Update -->
    <div class="modal fade" id="dateModal" tabindex="-1" role="dialog" aria-labelledby="dateModalLabel" aria-hidden="true">
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
                            <input type="date" class="form-control" id="dateInput" name="date" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
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
                    <button type="button" class="btn btn-outline-primary" onclick="submitDate()">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // JavaScript for sticky columns and rows
        document.addEventListener('DOMContentLoaded', function() {
            const table = document.getElementById('PrintTable');
            const headers = table.querySelectorAll('thead th');
            const rows = table.querySelectorAll('tbody tr');

            // Function to make columns sticky
            function makeColumnsSticky() {
                const firstFourColumns = 4; // Number of sticky columns
                let cumulativeWidth = 0;

                // Make header cells sticky
                headers.forEach((header, index) => {
                    if (index < firstFourColumns) {
                        header.style.position = 'sticky';
                        header.style.left = `${cumulativeWidth}px`;
                        header.style.zIndex = 2; // Ensure headers are above body cells
                        header.style.backgroundColor = '#343a40'; // Match thead background
                        cumulativeWidth += header.offsetWidth;
                    }
                });

                // Make body cells sticky
                rows.forEach(row => {
                    const cells = row.querySelectorAll('td');
                    let cumulativeWidth = 0;

                    cells.forEach((cell, index) => {
                        if (index < firstFourColumns) {
                            cell.style.position = 'sticky';
                            cell.style.left = `${cumulativeWidth}px`;
                            cell.style.zIndex = 1; // Ensure body cells are below headers
                            cell.style.backgroundColor = '#f8f9fa'; // Match tbody background
                            cumulativeWidth += cell.offsetWidth;
                        }
                    });
                });
            }

            // Function to make rows sticky (vertically)
            function makeRowsSticky() {
                const tableHeader = table.querySelector('thead');
                tableHeader.style.position = 'sticky';
                tableHeader.style.top = '0';
                tableHeader.style.zIndex = 3; // Ensure headers are above everything
            }

            // Initialize sticky columns and rows
            makeColumnsSticky();
            makeRowsSticky();

            // Recalculate sticky columns on window resize
            window.addEventListener('resize', function() {
                makeColumnsSticky();
            });
        });

        // JavaScript for modal and date updates
        function openModal(cell) {
            const id = cell.getAttribute('data-id');
            const task = cell.getAttribute('data-task');
            document.getElementById('tnaId').value = id;
            document.getElementById('taskName').value = task;

            if (task === 'print_strike_off_submission_actual' || task === 'fit_sample_submission_actual') {
                document.getElementById('dateInput').style.display = 'block';
                document.getElementById('naCheckbox').style.display = 'block';
            } else {
                document.getElementById('dateInput').style.display = 'block';
                document.getElementById('naCheckbox').style.display = 'none';
            }

            $('#dateModal').modal('show');
        }

        function submitDate() {
            const id = document.getElementById('tnaId').value;
            const task = document.getElementById('taskName').value;
            const dateInput = document.getElementById('dateInput').value;
            const naChecked = document.getElementById('naButton').checked;
            const explanation = document.getElementById('explanation').value;

            const formattedDate = naChecked ? 'N/A' : formatDate(dateInput);

            $.ajax({
                url: '/update-tna-date',
                type: 'POST',
                data: {
                    _token: $('input[name="_token"]').val(),
                    id: id,
                    task: task,
                    date: formattedDate,
                    explanation: explanation
                },
                success: function(response) {
                    location.reload(); // Reload the page to reflect changes
                },
                error: function(error) {
                    console.error('Error updating date:', error);
                }
            });

            $('#dateModal').modal('hide');
        }

        function formatDate(dateStr) {
            if (!dateStr) return '';
            const dateObj = new Date(dateStr);
            const day = dateObj.getDate().toString().padStart(2, '0');
            const month = dateObj.toLocaleString('en-us', { month: 'short' });
            const year = dateObj.getFullYear().toString().substr(-2);
            return `${day}-${month}-${year}`;
        }
    </script>
</x-backend.layouts.report_master>