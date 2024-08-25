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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

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
                        id="filter-buyer-btn" data-buyer="{{ $buyer->buyer }}">

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
                    <th>Item</th>
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
                    <th colspan="2">Bulk Accessories Inhouse</th>
                    <th colspan="2">PP Sample Submission</th>
                    <th colspan="2">Bulk Fabric Knitting</th>
                    <th colspan="2">PP Comments Receive</th>
                    <th colspan="2">Bulk Fabric Dyeing</th>
                    <th colspan="2">Bulk Fabric Delivery</th>
                    <th colspan="2">PP Meeting</th>
                    <th colspan="2">Cutting</th>
                    <th colspan="2">ETD</th>

                </tr>
                <tr>
                    <th colspan="5"></th>
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
                </tr>
            </thead>
            <tbody class="text-nowrap bg-light" id="tnaTableBody">
                @php
                    $sl = 1;
                @endphp
                @forelse ($tnas as $tna)
                    <tr>
                        <td>
                            @if (auth()->user()->role_id == 1)
                                <form action="{{ route('tnas.destroy', $tna->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit"
                                        onclick="return confirm('Are you sure want to delete ?')">
                                        <i class="fas fa-trash"></i>{{ $sl++ }}
                                    </button>
                                </form>
                            @else
                                {{ $sl++ }}
                            @endif
                        </td>
                        <td>{{ $tna->buyer }}</td>
                        <td>{{ $tna->style }}</td>
                        <td>{{ $tna->po }}</td>
                        <td>{{ $tna->item }}</td>
                        <td id="qty_pcs">{{ $tna->qty_pcs }}</td>
                        <td>{{ \Carbon\Carbon::parse($tna->po_receive_date)->format('d-M-y') ?? '' }}</td>
                        <td class="text-bold">{{ \Carbon\Carbon::parse($tna->shipment_etd)->format('d-M-y') ?? '' }}
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
                        @foreach (['lab_dip_submission', 'fabric_booking', 'fit_sample_submission', 'print_strike_off_submission', 'bulk_accessories_booking', 'fit_comments', 'bulk_yarn_inhouse', 'bulk_accessories_inhouse', 'pp_sample_submission', 'bulk_fabric_knitting', 'pp_comments_receive', 'bulk_fabric_dyeing', 'bulk_fabric_delivery', 'pp_meeting','cutting', 'etd'] as $task)
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
                        <td colspan="38" class="text-center">No TNA Found</td>
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

 <script> 
    document.addEventListener('DOMContentLoaded', function () {
        const table = document.querySelector('#PrintTable');
        const headers = table.querySelectorAll('thead th');
        const rows = table.querySelectorAll('tbody tr');
         const filterButtons = document.querySelectorAll('[data-buyer]');

    filterButtons.forEach(button => {
        button.addEventListener('click', function () {
            const buyer = this.getAttribute('data-buyer');
            filterByBuyer(buyer); 
            console.log(buyer);

        });
    });

        // Function to calculate the maximum width of each visible column
        function calculateColumnWidths() {
            let columnWidths = Array.from(headers).map(header => header.offsetWidth);

            // Update column widths based on the maximum content width of visible rows
            rows.forEach(row => {
                if (row.style.display !== 'none') { // Only consider visible rows
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
                        if (cell.closest('tr').style.display !== 'none') { // Only update visible rows
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
        window.addEventListener('resize', updateStickyColumnWidths);

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
        window.onload = function () {
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
        setInterval(() => {
            $.ajax({
                url: "{{ route('tnas_dashboard_update') }}",
                type: 'GET',
                success: function(data) {
                    const newTableHash = getTableHash(data);

                    // Update only if the new data is different
                    if (currentTableHash !== newTableHash) {
                        document.getElementById('tnaTableBody').innerHTML = data;
                        currentTableHash = newTableHash;

                        const buyer = localStorage.getItem('buyer');
                        if (buyer) {
                            filterByBuyer(buyer);
                        } else {
                            calculateTotalsAndAverages();
                        }
                    }
                },
                error: function(error) {
                    console.error('Ajax error:', error);
                }
            });
        }, 500000);

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





</body>

</html>
