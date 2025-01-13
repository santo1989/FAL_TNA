<x-backend.layouts.master>
    <!-- Show SweetAlert message -->
    @if (session('messages'))
        <div class="alert alert-success">
            <span class="close" data-dismiss="alert">&times;</span>
            <strong>{{ session('messages') }}</strong>
        </div>
    @endif

    <!-- Main Card -->
    <div class="card mx-5 my-5" style="background-color: white; overflow-x: auto;">
        <div class="container-fluid pt-2">
            <h4 class="text-center">Buyer-Wise On-Time Shipment Summary</h4>
            <div class="row">
                <div class="col-md-6">
                    <!-- Navigation Buttons -->
                    <a href="{{ route('tnas.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Close
                    </a>

                </div>
                <div class="col-md-6">
                    <!-- Date Range Filter -->
                    <form action="{{ route('BuyerWiseOnTimeShipmentSummary') }}" method="GET">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="from_date">From Date</label>
                                    <input type="date" name="from_date" id="from_date" class="form-control"
                                        value="{{ request()->from_date }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="to_date">To Date</label>
                                    <input type="date" name="to_date" id="to_date" class="form-control"
                                        value="{{ request()->to_date }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-outline-primary mt-4">Filter</button>
                                <button type="button" class="btn btn-outline-danger mt-4"
                                    onclick="window.location.href='{{ route('BuyerWiseOnTimeShipmentSummary') }}'">Reset</button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Data Table -->
                <table class="table table-bordered table-hover text-center text-wrap" style="font-size: 12px;">
                    <thead class="thead-dark">
                        <tr>
                            <th>Buyer</th>
                            <th colspan="2">On Time Orders</th>
                            <th colspan="2">Late Orders</th>
                            <th>Total Orders</th>
                            <th>On Time Percentage</th>
                            <th>Late Percentage</th>
                        </tr>
                        <tr>
                            <th></th>
                            <th>Orders</th>
                            <th>Percentage</th>
                            <th>Orders</th>
                            <th>Percentage</th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($buyerSummary as $buyer => $data)
                            <tr>
                                <td>{{ $buyer }}</td>
                                {{-- On Time Orders --}}
                                <td>
                                    @if ($data['on_time_orders'] > 0)
                                        <button class="btn btn-info btn-sm" data-toggle="modal"
                                            data-target="#detailsModal" data-buyer="{{ $buyer }}"
                                            data-type="on_time"
                                            data-details="{{ json_encode($data['on_time_details'] ?? []) }}">
                                            {{ $data['on_time_orders'] }}
                                        </button>
                                    @else
                                        {{ $data['on_time_orders'] }}
                                    @endif
                                </td>
                                <td>{{ $data['on_time_percentage'] }}%</td>

                                {{-- Late Orders --}}
                                <td>
                                    @if ($data['late_orders'] > 0)
                                        <button class="btn btn-info btn-sm" data-toggle="modal"
                                            data-target="#detailsModal" data-buyer="{{ $buyer }}"
                                            data-type="late"
                                            data-details="{{ json_encode($data['late_details'] ?? []) }}">
                                            {{ $data['late_orders'] }}
                                        </button>
                                    @else
                                        {{ $data['late_orders'] }}
                                    @endif
                                </td>
                                <td>{{ $data['late_percentage'] }}%</td>

                                <td>{{ $data['total_orders'] }}</td>
                                <td>{{ $data['on_time_percentage'] }}%</td>
                                <td>{{ $data['late_percentage'] }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="font-weight-bold">
                            <td>Total</td>
                            <td>{{ $overallSummary['on_time_orders'] }}</td>
                            <td>{{ $overallSummary['on_time_percentage'] }}%</td>
                            <td>{{ $overallSummary['late_orders'] }}</td>
                            <td>{{ $overallSummary['late_percentage'] }}%</td>
                            <td>{{ $overallSummary['total_orders'] }}</td>
                            <td>{{ $overallSummary['on_time_percentage'] }}%</td>
                            <td>{{ $overallSummary['late_percentage'] }}%</td>
                        </tr>
                    </tfoot>
                </table>

                <!-- Modal for Order Details -->
                <div class="modal fade" id="detailsModal" tabindex="-1" role="dialog"
                    aria-labelledby="detailsModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="detailsModalLabel">Order Details</h5>
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
                                            <th>Shipment ETD</th>
                                            <th>Shipment Actual Date</th>
                                        </tr>
                                    </thead>
                                    <tbody id="detailsBody"></tbody>
                                </table>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary save-button" style="display: none;">Save
                                    Changes</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Scripts -->
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
                <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

                <script>
                    $(document).ready(function() {
                        let originalData = []; // Store the original data for comparison

                        $('#detailsModal').on('show.bs.modal', function(event) {
                            const button = $(event.relatedTarget);
                            const isPlanningDepartment = {{ json_encode($isPlanningDepartment) }};
                            const details = button.data('details');
                            const detailsBody = $('#detailsBody');
                            const saveButton = $('.save-button');

                            originalData = details.map(detail => ({
                                id: detail.id,
                                shipment_actual_date: detail.shipment_actual_date
                            })); // Save original data

                            detailsBody.empty();
                            if (isPlanningDepartment) saveButton.show();
                            else saveButton.hide();

                            details.forEach(function(detail) {
                                detailsBody.append(`
                    <tr>
                        <td>${detail.style}</td>
                        <td>${detail.po}</td>
                        <td>${detail.shipment_etd || 'N/A'}</td>
                        <td>
                            ${isPlanningDepartment
                                ? `<input type="date" class="form-control shipment-date" data-id="${detail.id}" value="${detail.shipment_actual_date || ''}">`
                                : detail.shipment_actual_date || 'N/A'}
                        </td>
                    </tr>
                `);
                            });
                        });

                        $('.save-button').click(function() {
                            const updates = [];

                            $('#detailsBody tr').each(function() {
                                const id = $(this).find('.shipment-date').data('id');
                                const shipmentDate = $(this).find('.shipment-date').val();

                                const original = originalData.find(item => item.id == id);

                                if (original) {
                                    const isShipmentDateChanged = shipmentDate !== original
                                    .shipment_actual_date;

                                    if (isShipmentDateChanged) {
                                        updates.push({
                                            id,
                                            shipment_actual_date: shipmentDate
                                        });
                                    }
                                }
                            });

                            if (updates.length > 0) {
                                $.ajax({
                                    url: '/update-shipment-actual-dates',
                                    method: 'POST',
                                    data: {
                                        updates,
                                        _token: '{{ csrf_token() }}'
                                    },
                                    success: function(response) {
                                        alert(response.message);
                                        $('#detailsModal').modal('hide');
                                    },
                                    error: function() {
                                        alert('Failed to save changes.');
                                    }
                                });
                            } else {
                                alert('No changes to save.');
                            }
                        });
                    });
                </script>




</x-backend.layouts.master>
