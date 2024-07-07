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

<body
    style="background-image: url('{{ asset('images/assets/back.png') }}'); background-size: cover; background-repeat: repeat;">



    <div class="container-fluid pt-2">
        <table class="table table-bordered table-hover text-center">
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
            <tbody class="text-nowrap bg-light">
                @forelse ($tnas as $tna)
                    <tr>
                        <td>{{ $tna->id }}</td>
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
                                    }elseif($date == 'N/A'){
                                        $date = 'N/A';
                                    }

                                @endphp
                                <!-- if actual date is empty then modal button show else show date -->
                                @if ($type === 'actual' && empty($date))
                                    
                                    <td class="{{ $cellClass }}" data-id="{{ $tna->id }}"
                                        data-task="{{ $task . '_' . $type }}" onclick="openModal(this)"
                                        data-plan-date="{{ $tna->{$task . '_plan'} }}">
                                    </td>
                                @else
                                    <td class="{{ $cellClass }}" data-id="{{ $tna->id }}"
                                        data-task="{{ $task . '_' . $type }}">
                                        {{-- {{ \Carbon\Carbon::parse($date)->format('d-M-y') ?? '' }} --}}
                                        {{ $date == 'N/A' ? 'N/A' : ($date ? \Carbon\Carbon::parse($date)->format('d-M-y') : '') }}
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

    <!-- Modal -->
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
                            <label class="form-check-label" for="naButton">
                                N/A
                            </label>
                        </div> 
                        <textarea class="form-control" id="explanation" rows="3" style="display: none;" placeholder="Explanation"></textarea>
                      
                        </div>
                        <script>
                            // Get the current date in YYYY-MM-DD format
                            const today = new Date().toISOString().split('T')[0];

                            // Set the max attribute to today's date
                            document.getElementById('dateInput').setAttribute('max', today);

                            // Add an event listener to check the input date
                            document.getElementById('dateInput').addEventListener('change', function() {
                                const selectedDate = this.value;
                                if (selectedDate > today) {
                                    // If selected date is in the future, reset it to today
                                    this.value = today;
                                }
                            });
                        </script>
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

    <!-- Add jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script>
        function openModal(cell) {
            const id = cell.getAttribute('data-id');
            const task = cell.getAttribute('data-task');
             const planDate = cell.getAttribute('data-plan-date');
            document.getElementById('tnaId').value = id;
            document.getElementById('taskName').value = task;

             if (task === 'print_strike_off_submission_actual') {
                document.getElementById('dateInput').style.display = 'block';
                document.getElementById('naCheckbox').style.display = 'block';
            } else {
                document.getElementById('dateInput').style.display = 'block';
                document.getElementById('naCheckbox').style.display = 'none';
            }

            const today = new Date().toISOString().split('T')[0];
            if (planDate && new Date(planDate) < new Date(today)) {
                document.getElementById('explanation').style.display = 'block';
            } else {
                document.getElementById('explanation').style.display = 'none';
            }

            $('#dateModal').modal('show'); // Open the modal
        }

        function submitDate() {
             const id = document.getElementById('tnaId').value;
            const task = document.getElementById('taskName').value;
            const date = document.getElementById('dateInput').value;
            const naChecked = document.getElementById('naButton').checked;
            const explanation = document.getElementById('explanation').value;
            // AJAX request to update the date in the database
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
                    // Refresh the page or update the cell content and class
                    location.reload();
                }
            });

            $('#dateModal').modal('hide'); // Close the modal
        }

        //close modal if click modal close button
        $('#dateModal').on('hidden.bs.modal', function() {
            $('#dateForm').trigger('reset');
        });
    </script>
    </table>
    <a href="{{ route('tnas.index') }}" class="btn btn-outline-secondary bg-light">
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
