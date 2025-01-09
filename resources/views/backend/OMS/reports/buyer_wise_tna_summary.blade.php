<x-backend.layouts.master>
    <!--message show in .swl sweet alert-->
    @if (session('messages'))
        <div class="alert alert-success">
            <span class="close" data-dismiss="alert">&times;</span>
            <strong>{{ session('messages') }}.</strong>
        </div>
    @endif
    <div class="card mx-5 my-5" style="background-color: white; overflow-x: auto;">

        <div class="container-fluid pt-2">
            <h4 class="text-center">Buyer-Wise Pending Tasks Summary</h4>
            <a href="{{ route('tnas.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Close
            </a>
            @if (auth()->user()->role_id == 1 || auth()->user()->role_id == 4)
                <a href="{{ route('MailBuyerWiseTnaSummary') }}" class="btn btn-outline-secondary float-right">
                    <i class="fas fa-envelope"></i> Mail Report to Marchandiser </a>
            @else
            @endif

            <table class="table table-bordered table-hover text-center text-wrap" style="font-size: 12px;">
                <thead class="thead-dark">
                    <tr>
                        <th>Buyer</th>
                        @foreach ($columns as $column)
                            <th>{{ ucwords(str_replace('_', ' ', $column)) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($buyers as $buyer => $data)
                        <tr>
                            <td>{{ $buyer }}</td>
                            @foreach ($columns as $column)
                                <td>
                                    @if ($data['data'][$column] > 0)
                                        <button class="btn btn-info btn-sm" data-toggle="modal"
                                            data-target="#detailsModal" data-buyer="{{ $buyer }}"
                                            data-task="{{ $column }}"
                                            data-details="{{ json_encode($data['details'][$column] ?? []) }}">
                                            {{ $data['data'][$column] }}
                                        </button>
                                    @else
                                        {{ $data['data'][$column] }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailsModalLabel">Task Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Style</th>
                                <th>PO Number</th>
                                <th>Plan Date</th>
                                <th>Shipment Date</th>
                            </tr>
                        </thead>
                        <tbody id="detailsBody">
                            <!-- Details will be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add jQuery and Bootstrap JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script>
        // $('#detailsModal').on('show.bs.modal', function(event) {
        //     var button = $(event.relatedTarget);
        //     var buyer = button.data('buyer');
        //     var task = button.data('task');
        //     var PlanDate = button.data('PlanDate');
        //     //date show in 11-july-24 in that format

        //     var details = button.data('details');
        //     var modal = $(this);
        //     var detailsBody = $('#detailsBody');

        //     detailsBody.empty(); // Clear previous data

        //     details.forEach(function(detail) {
        //         detailsBody.append(
        //             `<tr>
    //                 <td>${detail.style}</td>
    //                 <td>${detail.po}</td>
    //                 <td>${detail.PlanDate}</td>
    //             </tr>`
        //         );
        //     });
        //     console.log(details);
        //     modal.find('.modal-title').text(`Task Details for ${buyer} - ${task}`);
        // });

        // //update tbody with the new data in every 10 seconds if new data is available 
        // setInterval(function() {
        //     $.ajax({
        //         url: '{{ route('FAL_BuyerWiseTnaSummary') }}',
        //         type: 'GET',
        //         success: function(data) {
        //             const newTableHash = getTableHash(data);
        //             // Update only if the new data is different
        //             if (currentTableHash !== newTableHash) {
        //                 document.getElementById('tbody').innerHTML = data;
        //                 currentTableHash = newTableHash;
        //             }
        //         }
        //     });

        // }, 10000);

        // // Function to generate a unique hash for the table data
        // function getTableHash(data) {
        //     const tableContent = JSON.stringify(data);
        //     // Simple hash function for the content
        //     return Array.from(tableContent).reduce((hash, char) => {
        //         hash = ((hash << 5) - hash) + char.charCodeAt(0);
        //         return hash & hash; // Convert to 32bit integer
        //     }, 0);
        // }
        // // Initial hash of the table data
        // let currentTableHash = getTableHash(data);

        $(document).ready(function() {
            // Initial data fetch and hash calculation
            let currentTableHash;

            function fetchDataAndUpdateTable() {
                $.ajax({
                    url: '{{ route('FAL_BuyerWiseTnaSummary') }}',
                    type: 'GET',
                    success: function(data) {
                        const newTableHash = getTableHash(data);
                        // Update the table only if the data has changed
                        if (currentTableHash !== newTableHash) {
                            $('#tbody').html(data);
                            currentTableHash = newTableHash;
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Failed to update data:', error);
                    }
                });
            }

            // Initial data fetch
            fetchDataAndUpdateTable();

            // Update tbody with the new data every 10 seconds if new data is available
            setInterval(fetchDataAndUpdateTable, 10000);

            // Function to generate a unique hash for the table data
            function getTableHash(data) {
                const tableContent = JSON.stringify(data);
                // Simple hash function for the content
                return Array.from(tableContent).reduce((hash, char) => {
                    hash = ((hash << 5) - hash) + char.charCodeAt(0);
                    return hash & hash; // Convert to 32bit integer
                }, 0);
            }

            // Modal show event handler
            $('#detailsModal').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget);
                const buyer = button.data('buyer');
                const task = button.data('task');
                const details = button.data('details');
                const modal = $(this);
                const detailsBody = $('#detailsBody');

                detailsBody.empty(); // Clear previous data

                details.forEach(function(detail) {
                    detailsBody.append(
                        `<tr>
                    <td>${detail.style}</td>
                    <td>${detail.po}</td>
                    <td>${detail.PlanDate}</td>
                    <td>${detail.shipment_etd}</td>
                </tr>`
                    );
                });
                modal.find('.modal-title').text(`Task Details for ${buyer} - ${task}`);
            });
        });
    </script>

</x-backend.layouts.master>
