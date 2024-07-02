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
        }

        .bg-yellow {
            background-color: yellow !important;
        }
    </style>

</head>

<body style="background-image: url('{{ asset('images/assets/back.png') }}'); background-size: cover; background-repeat: repeat;">



    <div class="container-fluid pt-2">
        <div class="row justify-content-center pb-2">
            @php
                $buyerList = DB::table('t_n_a_s')->select('buyer')->distinct()->get();
            @endphp
             <div class="col-12">
            <button class="btn btn-sm btn-outline-primary" style="width: 10rem;" id="all-buyers-btn">
                <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                All Buyers
            </button>
            @foreach ($buyerList as $buyer)
                <button class="btn btn-sm btn-outline-primary" style="width: 10rem;" id="buyer-{{ $buyer->buyer }}-btn" onclick="filterByBuyer('{{ $buyer->buyer }}')">
                    <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                    {{ $buyer->buyer }}
                </button>
            @endforeach
        </div>
            

        </div>
        <table class="table table-bordered table-hover text-center" style="font-size: 10px;">
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
                </tr>
                <tr>
                    <th colspan="12"></th>
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
            <tbody class="text-center bg-light" id="tnaTableBody" >
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
                        <td>{{ $tna->qty_pcs }}</td>
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
                                    if ($date) {
                                        $today = \Carbon\Carbon::now();
                                        $cellDate = \Carbon\Carbon::parse($date);
                                        $diffDays = $today->diffInDays($cellDate, false);

                                        // if actual date is empty then
                                        if ($type === 'plan' && empty($date)) {
                                            $cellClass = 'bg-light';
                                        } elseif ($cellDate->isToday() || $cellDate->isPast()) {
                                            $cellClass = 'bg-red';
                                        } elseif ($diffDays <= 2 && $diffDays > 0) {
                                            $cellClass = 'bg-yellow';
                                        } 

                                        //if actual date and plan date both have value then check if actual date is same or date over then plan date then bg color red expample: if plan date is 10-10-2021 and actual date is 10-10-2021 or 12-10-2021 then bg color red
                                        if ($type === 'actual' && $tna->{$task . '_plan'}) {
                                            $planDate = \Carbon\Carbon::parse($tna->{$task . '_plan'});
                                            if ($cellDate->isToday() || $cellDate->gt($planDate)) {
                                                $cellClass = 'bg-red';
                                            }
                                        }
                                    }
                                @endphp
                                <!-- if actual date is empty then modal button show else show date -->
                                @if ($type === 'actual' && empty($date))
                                    <td>
                                         
                                    </td>
                                @else
                                    <td class="{{ $cellClass }}" data-id="{{ $tna->id }}"
                                        data-task="{{ $task . '_' . $type }}">
                                        {{ \Carbon\Carbon::parse($date)->format('d-M-y') ?? '' }}
                                    </td>
                                @endif
                                
                            @endforeach
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="36" class="text-center">No TNA Found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div> 

    <!-- Add jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    
    <script>
    function filterByBuyer(buyer) {
        const allBuyersBtn = document.getElementById('all-buyers-btn');
        allBuyersBtn.classList.remove('btn-primary');
        allBuyersBtn.classList.add('btn-outline-primary');
        allBuyersBtn.style.color = 'black';
        allBuyersBtn.style.fontWeight = 'normal';

        const buyerBtn = document.getElementById(`buyer-${buyer}-btn`);
        buyerBtn.classList.remove('btn-outline-primary');
        buyerBtn.classList.add('btn-primary');
        buyerBtn.style.color = 'white';
        buyerBtn.style.fontWeight = 'bold';

        const rows = document.querySelectorAll('#tnaTableBody tr');
        rows.forEach(row => {
            if (buyer === 'All Buyers') {
                // Show all rows
                row.style.display = '';
            } else {
                const buyerCell = row.querySelector('td:nth-child(2)');
                if (buyerCell.textContent !== buyer) {
                    // Hide the row if the buyer doesn't match
                    row.style.display = 'none';
                } else {
                    // Show the row if the buyer matches
                    row.style.display = '';
                }
            }
            //if again click on all buyers button then show all rows
            allBuyersBtn.addEventListener('click', () => {
                row.style.display = '';
            });
        });
    
        
    }
       
</script>
    </table>
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

</body>

</html>
