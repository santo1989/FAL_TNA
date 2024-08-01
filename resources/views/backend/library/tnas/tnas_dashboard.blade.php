<x-backend.layouts.report_master>
    {{-- @dd($tnas) --}}

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




        /* Make all table headers sticky horizontally */
        thead th {
            position: sticky;
            top: 0;
            background: #343a40;
            /* Adjust background color as needed */
            z-index: 20;
            /* Ensure headers stay above the table body */
        }

        /* Make the first 4 columns sticky vertically */
        /* thead th:nth-child(-n+4), */
        tbody td:nth-child(-n+4) {
            position: sticky;
            left: 0;
            background: #494747;
            /* Match this with your table's background */
            color: white;
        }

        /* Hover effect on table rows */
        #PrintTable tbody tr:hover {
            background-color: #fdf1f1;
            /* Red background color on hover */

        }



        /* Adjust z-index for each of the first 4 columns to stack properly */

        tbody td:nth-child(1) {
            left: 0px;
            z-index: 6;
            /* Higher value for the first column */
        }


        tbody td:nth-child(2) {
            left: 70px;
            z-index: 7;
            /* Higher value for the second column */
        }


        tbody td:nth-child(3) {
            left: 150px;
            z-index: 8;
            /* Higher value for the third column */
        }


        tbody td:nth-child(4) {
            left: 360px;
            z-index: 9;
            /* Higher value for the fourth column */
        }


        .sortable {
            cursor: pointer;
        }

        .sortable:hover {
            background-color: #f90303;
            /* Light background on hover */
        }
    </style>


    <div class="container-fluid pt-2">
        <h4 class="text-center text-white"><img src="{{ asset('images/assets/FAL_logo.png') }}" alt="NTG"
                width="70px">
            TNA Dashboard</h4>
        <div class="row justify-content-center pb-2">
            @php
                $buyerList = DB::table('t_n_a_s')->where('order_close', 0)->select('buyer')->distinct()->get();
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
                    <button class="btn btn-sm btn-outline-primary bg-light" style="width: 10rem;"
                        id="buyer-{{ $buyer->buyer }}-btn" onclick="filterByBuyer('{{ $buyer->buyer }}')">

                        {{ $buyer->buyer }}
                    </button>
                @endforeach
            </div>


        </div>
        <table class="table table-bordered table-hover text-center text-nowrap" style="font-size: 12px;"
            id="PrintTable">
            <thead class="thead-dark"
                style="font-size: 12px; text-align: center; vertical-align: middle;position: sticky;top: 0;z-index: 1;">
                <tr>
                    <th rowspan="2" style="width: 70px;">Action</th>
                    <th rowspan="2" style="width: 150px;">Buyer</th>
                    <th rowspan="2" style="width: 250px;">Style</th>
                    <th rowspan="2" style="width: 100px;">PO Number</th>
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
                    <th colspan="2">ETD</th>

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
                            <td style="width: 70px;">
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
                                <td style="width: 70px;">
                                    <a href="{{ route('tnas.show', $tna->id) }}" class="btn btn-sm btn-outline-success"
                                        data-toggle="tooltip" data-placement="top" title="show">
                                        <i class="fas fa-eye"></i>{{ $sl++ }}
                                    </a>
                                </td>
                            @else
                                <td style="width: 70px;">{{ $sl++ }}</td>
                            @endif
                        @else
                            <td style="width: 70px;">{{ $sl++ }}</td>
                        @endif

                        <td style="width: 150px;">{{ $tna->buyer }}</td>
                        <td style="width: 250px;">{{ $tna->style }}</td>
                        <td style="width: 100px;">{{ $tna->po }}</td>
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
                        @foreach (['lab_dip_submission', 'fabric_booking', 'fit_sample_submission', 'print_strike_off_submission', 'bulk_accessories_booking', 'fit_comments', 'bulk_yarn_inhouse', 'bulk_accessories_inhouse', 'pp_sample_submission', 'bulk_fabric_knitting', 'pp_comments_receive', 'bulk_fabric_dyeing', 'bulk_fabric_delivery', 'pp_meeting', 'etd'] as $task)
                            @foreach (['plan', 'actual'] as $type)
                                @php
                                    $date = $tna->{$task . '_' . $type};
                                    $cellClass = '';
                                    $explanation = ''; // Default explanation to empty
                                    if ($date && $date != 'N/A') {
                                        $today = \Carbon\Carbon::now();
                                        $cellDate = \Carbon\Carbon::parse($date);
                                        $diffDays = $today->diffInDays($cellDate, false);

                                        // if actual date is empty and plane date have value then if plan date is today or past then bg color red else plan date before 2 days then bg color yellow else bg color light example: if plan date is 10-10-2021 and actual date is empty and today date is 10-10-2021 then bg color red if plan date is 8-10-2021 and actual date is empty then bg color yellow if plan date is 9-10-2021 and actual date is empty then bg color light
                                        if ($type === 'plan' && empty($tna->{$task . '_actual'})) {
                                            if ($cellDate->isToday() || $cellDate->lt($today)) {
                                                $cellClass = 'bg-red';
                                            } elseif ($diffDays <= 2) {
                                                $cellClass = 'bg-yellow';
                                            } else {
                                                $cellClass = 'bg-light';
                                            }
                                        }

                                        //if actual date and plan date both have value then check if actual date is same or date over then plan date then bg color red expample: if plan date is 10-10-2021 and actual date is 10-10-2021 or 12-10-2021 then bg color red

                                        // if ($type === 'actual' && $tna->{$task . '_plan'}) {
                                        //     $planDate = \Carbon\Carbon::parse($tna->{$task . '_plan'});
                                        //     if ($cellDate->isToday() || $cellDate->gt($planDate)) {
                                        //         $cellClass = 'bg-red';
                                        //     }
                                        // }

                                        //if actual date and plan date both have value then check if actual date is date over then plan date then text front red and blod expample: if plan date is 10-10-2021 and actual date is  12-10-2021 then text front red and blod
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

                                        //explanation show from tna_explanations table if plan date is over from the actual date then show explanation in bootstrap tooltip
                                        // Retrieve explanation for the actual date
                                    } elseif ($date == 'N/A') {
                                        $date = 'N/A';
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
                                            // dd($buyer_privilage);
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
                                        // dd($tna->id);
                                    @endphp
                                    <td class="{{ $cellClass }}" data-toggle="tooltip" data-placement="top"
                                        title="{{ $explanation }}">
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

        function submitDate() {
            const id = document.getElementById('tnaId').value;
            const task = document.getElementById('taskName').value;
            const date = document.getElementById('dateInput').value;
            const naChecked = document.getElementById('naButton').checked;
            const explanation = document.getElementById('explanation').value;

            $.ajax({
                url: '/update-tna-date', // Your route to handle the date update
                type: 'POST',
                data: {
                    _token: $('input[name="_token"]').val(),
                    id: id,
                    task: task,
                    date: naChecked ? 'N/A' : date,
                    explanation: explanation
                },
                success: function(response) {
                    // Optionally, update the cell content and class here without reloading
                    location.reload();
                }
            });

            $('#dateModal').modal('hide');
        }

        // Reset modal form on close
        $('#dateModal').on('hidden.bs.modal', function() {
            $('#dateForm').trigger('reset');
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

        // Function to filter by buyer and recalculate totals and averages
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
                if (buyer === 'All Buyers') {
                    row.style.display = '';
                } else {
                    const buyerCell = row.querySelector('td:nth-child(2)');
                    if (buyerCell.textContent !== buyer) {
                        row.style.display = 'none';
                    } else {
                        row.style.display = '';
                    }
                }
            });

            // Recalculate totals and averages after filtering
            calculateTotalsAndAverages();
        }

        // Event listener for "All Buyers" button to show all rows
        document.getElementById('all-buyers-btn').addEventListener('click', () => {
            localStorage.removeItem('buyer');
            const rows = document.querySelectorAll('#tnaTableBody tr');
            rows.forEach(row => {
                row.style.display = '';
            });
            calculateTotalsAndAverages();
        });

        // Initial calculation
        calculateTotalsAndAverages();

        // Periodically update the table
        setInterval(() => {
            $.ajax({
                url: "{{ route('tnas_dashboard_update') }}",
                type: 'GET',
                success: function(data) {
                    // If localStorage has buyer name then show the buyer name data after page load else show all buyers data and calculateTotalsAndAverages function call to calculate total quantity, average lead time, and average order free time
                    const buyer = localStorage.getItem('buyer');

                    // If after page load buyer name change then clean the localStorage and store the new buyer name for the next time page load to show the same buyer data
                    if (buyer && !data.includes(buyer)) {
                        localStorage.removeItem('buyer');
                    }

                    console.log(data);

                    document.getElementById('tnaTableBody').innerHTML = data;

                    if (buyer) {
                        filterByBuyer(buyer);
                    } else {
                        calculateTotalsAndAverages();
                    }
                },
                error: function(error) {
                    console.error('Ajax error:', status, error);
                }
            });
        }, 50000);

        // On page load, check for stored buyer and filter if present
        window.onload = function() {
            const buyer = localStorage.getItem('buyer');
            if (buyer) {
                filterByBuyer(buyer);
            } else {
                calculateTotalsAndAverages();
            }
        };

        $(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });

        //download table data in excel format with table style 
        function downloadExcel() {
            var tab_text = "<table border='2px'><tr bgcolor='#87AFC6'>";
            var textRange;
            var j = 0;
            tab = document.getElementById('PrintTable'); // id of table

            for (j = 0; j < tab.rows.length; j++) {
                tab_text = tab_text + tab.rows[j].innerHTML + "</tr>";
                //tab_text=tab_text+"</tr>";
            }

            tab_text = tab_text + "</table>";
            tab_text = tab_text.replace(/<A[^>]*>|<\/A>/g, ""); //remove if u want links in your table
            tab_text = tab_text.replace(/<img[^>]*>/gi, ""); // remove if u want images in your table
            tab_text = tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params

            var ua = window.navigator.userAgent;
            var msie = ua.indexOf("MSIE ");

            if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) // If Internet Explorer
            {
                txtArea1.document.open("txt/html", "replace");
                txtArea1.document.write(tab_text);
                txtArea1.document.close();
                txtArea1.focus();
                sa = txtArea1.document.execCommand("SaveAs", true, "Say Thanks to Sumit.xls");
            } else //other browser not tested on IE 11
                sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));

            return (sa);
        }
    </script>


</x-backend.layouts.report_master>
