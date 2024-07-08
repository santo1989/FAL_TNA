<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="TNA Management Softwear from NTG, MIS Department" />
    <meta name="author" content="Md. Hasibul Islam Santo, MIS, NTG" />
    <title> {{ $pageTitle ?? 'FAL' }} </title>

    <!-- <link href="css/styles.css" rel="stylesheet" /> -->

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- bootstrap 5 cdn  -->

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.1/css/bootstrap.min.css">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.1/js/bootstrap.min.js"></script>


    <!-- font-awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>

    <!-- Bootstrap core icon -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />



    <!-- sweetalert2 cdn-->

    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <!-- DataTable -->

    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />

    <!-- Custom CSS -->

    <link href="{{ asset('ui/backend/css/styles.css') }}" rel="stylesheet" />

    <!-- Push Notification -->

    <script src="{{ asset('js/push.min.js') }}"></script>

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
    </style>

</head>

<body style="background-color:#a5bcfc">



    <div class="container-fluid pt-2">
        <h4 class="text-center text-white"><img src="{{ asset('images/assets/FAL_logo.png') }}" alt="NTG"
                width="70px">
            TNA Dashboard</h4>
        <div class="row justify-content-center pb-2">
            @php
                $buyerList = DB::table('t_n_a_s')->where('order_close', 1)->select('buyer')->distinct()->get();
            @endphp
            <div class="col-12">
                <button class="btn btn-outline-secondary bg-light btn-sm" onclick="downloadExcel()"
                    style="width: 10rem;">
                    <i class="fas fa-download"></i> Download Excel File </button>
                <button class="btn btn-sm btn-outline-primary bg-light" style="width: 10rem;" id="all-buyers-btn">
                    {{-- <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div> --}}
                    All Buyers
                </button>
                @foreach ($buyerList as $buyer)
                    <button class="btn btn-sm btn-outline-primary bg-light" style="width: 10rem;"
                        id="buyer-{{ $buyer->buyer }}-btn" onclick="filterByBuyer('{{ $buyer->buyer }}')">
                        {{-- <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div> --}}
                        {{ $buyer->buyer }}
                    </button>
                @endforeach
            </div>


        </div>
        <table class="table table-bordered table-hover text-center text-nowrap
        " style="font-size: 13px;"
            id="PrintTable">
            <thead class="thead-dark">
                <tr>
                    <th>SL</th>
                    <th>Buyer</th>
                    <th>Style</th>
                    <th>PO</th>
                    <th>Picture</th>
                    <th>Item</th>
                    <th>Color</th>
                    <th>Qty (pcs)</th>
                    <th>PO Receive Date</th>
                    <th>Shipment/ETD</th>
                    <th>Total Lead Time</th>
                    <th>Order Free Time</th>
                    <th colspan="2">Lab Dip Submission</th>
                    <th colspan="2">Fabric Booking</th>
                    <th colspan="2">Fit Sample Submission</th>
                    <th colspan="2">Print Strike Off Submission</th>
                    <th colspan="2">Bulk Accessories Booking</th>
                    <th colspan="2">Fit Comments</th>
                    <th colspan="2">Bulk Yarn Inhouse</th>
                    <th colspan="2">PP Sample Submission</th>
                    <th colspan="2">Bulk Fabric Knitting</th>
                    <th colspan="2">PP Comments Receive</th>
                    <th colspan="2">Bulk Fabric Dyeing</th>
                    <th colspan="2">Bulk Fabric Delivery</th>
                    <th colspan="2">PP Meeting</th>
                    <th colspan="2">ETD</th>
                    <th rowspan="2">Action</th>
                </tr>
                <tr>
                    <th colspan="7"></th>
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
                </tr>
            </thead>
            <tbody class="text-nowrap bg-light" id="tnaTableBody">
                @php
                    $sl = 1;
                @endphp
                @forelse ($tnas as $tna)
                    <tr>
                        <td>{{ $sl++ }}</td>
                        <td>{{ $tna->buyer }}</td>
                        <td>{{ $tna->style }}</td>
                        <td>{{ $tna->po }}</td>
                        <td></td>
                        <td>{{ $tna->item }}</td>
                        <td>{{ $tna->color }}</td>
                        <td id="qty_pcs">{{ $tna->qty_pcs }}</td>
                        <td>{{ \Carbon\Carbon::parse($tna->po_receive_date)->format('d-M-y') ?? '' }}</td>
                        <td>{{ \Carbon\Carbon::parse($tna->shipment_etd)->format('d-M-y') ?? '' }}</td>
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
                        @foreach (['lab_dip_submission', 'fabric_booking', 'fit_sample_submission', 'print_strike_off_submission', 'bulk_accessories_booking', 'fit_comments', 'bulk_yarn_inhouse', 'pp_sample_submission', 'bulk_fabric_knitting', 'pp_comments_receive', 'bulk_fabric_dyeing', 'bulk_fabric_delivery', 'pp_meeting', 'etd'] as $task)
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
                                    <td>

                                    </td>
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
                        {{-- @dd($explanation); --}}
                    </tr>
                @empty
                    <tr>
                        <td colspan="41" class="text-center">No TNA Found</td>
                    </tr>
                @endforelse
            </tbody>

        </table>
    </div> <!-- container -->
    <a href="{{ route('tnas.index') }}" class="btn btn-outline-secondary bg-light m-2">
        <i class="fas fa-arrow-left"></i> Cancel
    </a>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script> <!-- Core theme JS-->
    <script src="{{ asset('ui/backend/js/scripts.js') }}"></script>

    <!-- Bootstrap core JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
    </script>



    <!-- DataTable JS -->
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>
    <script src="{{ asset('ui/backend/js/datatables-simple-demo.js') }}"></script>

    <!-- Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <!-- Add jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

    <script>
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
                const leadTimeCell = row.querySelector('td:nth-child(11)');
                totalLeadTime += parseInt(leadTimeCell.textContent);
            });

            // Calculate average lead time and show in celing format
            document.getElementById('AvgLeadTime').textContent = Math.ceil(totalLeadTime / visibleRows.length);

            // Calculate average order free time
            let totalOrderFreeTime = 0;
            visibleRows.forEach(row => {
                const orderFreeTimeCell = row.querySelector('td:nth-child(12)');
                totalOrderFreeTime += parseInt(orderFreeTimeCell.textContent);
            });

            // Calculate average order free time and show in celing format
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
                url: "{{ route('archives_dashboard_update') }}",
                type: 'GET',
                success: function(data) {
                    // If localStorage has buyer name then show the buyer name data after page load else show all buyers data and calculateTotalsAndAverages function call to calculate total quantity, average lead time, and average order free time
                    const buyer = localStorage.getItem('buyer');

                    // If after page load buyer name change then clean the localStorage and store the new buyer name for the next time page load to show the same buyer data
                    if (buyer && !data.includes(buyer)) {
                        localStorage.removeItem('buyer');
                    }

                    document.getElementById('tnaTableBody').innerHTML = data;

                    if (buyer) {
                        filterByBuyer(buyer);
                    } else {
                        calculateTotalsAndAverages();
                    }
                }
            });
        }, 5000);

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





</body>

</html>
