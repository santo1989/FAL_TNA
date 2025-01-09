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
            <h4 class="text-center">Buyer-Wise Production Lead Time Summary</h4>
            <div class="row">
                <div class="col-md-6">
                    <!-- Navigation Buttons -->
                    <a href="{{ route('tnas.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Close
                    </a>

                </div>
                <div class="col-md-6">
                    <!--month wise filter-->
                    <form action="{{ route('BuyerWiseProductionLeadTimeSummary') }}" method="GET">
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
                                    onclick="window.location.href='{{ route('BuyerWiseProductionLeadTimeSummary') }}'">Reset</button>
                            </div>
                        </div>
                    </form>

                </div>

            </div>

            <!-- Data Table -->
            <table class="table table-bordered table-hover text-center text-wrap" style="font-size: 12px;">
                <thead class="thead-dark">
                    <tr>
                        <th>Buyer</th>
                        <th colspan="2">Inadequate Lead Time</th>
                        <th colspan="2">Adequate Lead Time</th>
                        <th>Total Orders</th>
                        <th>Average Lead Time</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th>Orders</th>
                        <th>Percentage</th>
                        <th>Orders</th>
                        <th>Percentage</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($buyerSummary as $buyer => $data)
                        <tr>
                            <td>{{ $buyer }}</td>

                            {{-- Inadequate Orders --}}
                            <td>
                                @if ($data['inadequate_orders'] > 0)
                                    <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#detailsModal"
                                        data-buyer="{{ $buyer }}" data-type="inadequate"
                                        data-details="{{ json_encode($data['inadequate_details'] ?? []) }}">
                                        {{ $data['inadequate_orders'] }}
                                    </button>
                                @else
                                    {{ $data['inadequate_orders'] }}
                                @endif
                            </td>
                            <td>{{ $data['inadequate_percentage'] }}%</td>

                            {{-- Adequate Orders --}}
                            <td>
                                @if ($data['adequate_orders'] > 0)
                                    <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#detailsModal"
                                        data-buyer="{{ $buyer }}" data-type="adequate"
                                        data-details="{{ json_encode($data['adequate_details'] ?? []) }}">
                                        {{ $data['adequate_orders'] }}
                                    </button>
                                @else
                                    {{ $data['adequate_orders'] }}
                                @endif
                            </td>
                            <td>{{ $data['adequate_percentage'] }}%</td>

                            <td>{{ $data['total_orders'] }}</td>
                            <td>{{ $data['average_lead_time'] }}</td>
                        </tr>
                    @endforeach
                </tbody>

                <tfoot>
                    <tr class="font-weight-bold">
                        <td>Total</td>
                        <td>{{ $overallSummary['inadequate_orders'] }}</td>
                        <td>{{ $overallSummary['inadequate_percentage'] }}%</td>
                        <td>{{ $overallSummary['adequate_orders'] }}</td>
                        <td>{{ $overallSummary['adequate_percentage'] }}%</td>
                        <td>{{ $overallSummary['total_orders'] }}</td>
                        <td>{{ $overallSummary['average_lead_time'] }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Modal for Task Details -->
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
                {{-- <div class="modal-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Style</th>
                                <th>PO Number</th>
                                <th>Inspection Actual Date</th>
                                <th>PP Meeting Actual</th>
                                <th>Shipment Date</th>
                            </tr>
                        </thead>
                        <tbody id="detailsBody">
                            <!-- Details will be dynamically populated by JavaScript -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div> --}}
                <div class="modal-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Style</th>
                                <th>PO Number</th>
                                <th>Inspection Actual Date</th>
                                <th>PP Meeting Actual</th>
                                <th>Shipment Date</th>
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
    {{-- <script> // JavaScript code for the modal
        $(document).ready(function() {
            // Modal data population
            $('#detailsModal').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget);
                const buyer = button.data('buyer');
                const type = button.data('type'); // "inadequate" or "adequate"
                const details = button.data('details');
                const isPlanningDepartment = button.data('true'); // Pass this from the backend
                const detailsBody = $('#detailsBody');
                const saveButton = $('.save-button');

                // Clear previous data
                detailsBody.empty();

                // Toggle Save button for Planning Department only
                if (isPlanningDepartment) {
                    saveButton.show();
                } else {
                    saveButton.hide();
                }

                // Populate modal with dynamic data
                if (details.length > 0) {
                    details.forEach(function(detail) {
                        const inspectionDate = detail.inspection_actual_date || '';
                        const ppMeetingActual = detail.pp_meeting_actual || '';

                        detailsBody.append(`
                        <tr>
                            <td>${detail.style || 'N/A'}</td>
                            <td>${detail.po || 'N/A'}</td>
                            <td>${detail.shipment_etd || 'N/A'}</td>
                            <td>
                                ${
                                    isPlanningDepartment
                                        ? `<input type="date" class="form-control inspection-date" value="${inspectionDate}" data-id="${detail.id}">`
                                        : inspectionDate || 'N/A'
                                }
                            </td>
                            <td>
                                ${
                                    isPlanningDepartment
                                        ? `<input type="date" class="form-control pp-meeting-date" value="${ppMeetingActual}" data-id="${detail.id}">`
                                        : ppMeetingActual || 'N/A'
                                }
                            </td>
                        </tr>
                    `);
                    });
                } else {
                    detailsBody.append('<tr><td colspan="5">No data available</td></tr>');
                }

                // Update modal title based on type
                const typeText = type === "inadequate" ? "Inadequate Orders" : "Adequate Orders";
                $(this).find('.modal-title').text(`Task Details for ${buyer} (${typeText})`);
            });

            // Save button click event
            $('.save-button').on('click', function() {
                const updates = [];

                // Collect data from the modal
                $('#detailsBody tr').each(function() {
                    const row = $(this);
                    const id = row.find('.inspection-date').data('id'); // Get the ID of the record
                    const inspectionDate = row.find('.inspection-date').val();
                    const ppMeetingDate = row.find('.pp-meeting-date').val();

                    if (id) {
                        updates.push({
                            id: id,
                            inspection_actual_date: inspectionDate,
                            pp_meeting_actual: ppMeetingDate,
                        });
                    }
                });

                // Send AJAX request to save data
                $.ajax({
                    url: '/update-task-details', // Update this to your route
                    method: 'POST',
                    data: {
                        updates: updates,
                        _token: '{{ csrf_token() }}' // Laravel CSRF token
                    },
                    success: function(response) {
                        alert('Changes saved successfully!');
                        $('#detailsModal').modal('hide');
                    },
                    error: function() {
                        alert('An error occurred while saving changes.');
                    }
                });
            });
        });
    </script> --}}
 <script>
$(document).ready(function () {
    let originalData = []; // Store the original data for comparison

    $('#detailsModal').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const isPlanningDepartment = {{ json_encode($isPlanningDepartment) }};
        const details = button.data('details');
        const detailsBody = $('#detailsBody');
        const saveButton = $('.save-button');

        originalData = details.map(detail => ({ 
            id: detail.id, 
            inspection_actual_date: detail.inspection_actual_date, 
            pp_meeting_actual: detail.pp_meeting_actual 
        })); // Save original data

        detailsBody.empty();
        if (isPlanningDepartment) saveButton.show();
        else saveButton.hide();

        details.forEach(function (detail) {
            detailsBody.append(`
                <tr>
                    <td>${detail.style}</td>
                    <td>${detail.po}</td>
                    <td>
                        ${
                            isPlanningDepartment
                                ? `<input type="date" class="form-control inspection-date" data-id="${detail.id}" value="${detail.inspection_actual_date || ''}">`
                                : detail.inspection_actual_date || 'N/A'
                        }
                    </td>
                    <td>
                        ${
                            isPlanningDepartment
                                ? `<input type="date" class="form-control pp-meeting-date" data-id="${detail.id}" value="${detail.pp_meeting_actual || ''}">`
                                : detail.pp_meeting_actual || 'N/A'
                        }
                    </td>
                    <td>${detail.shipment_etd || 'N/A'}</td>
                </tr>
            `);
        });
    });

    $('.save-button').click(function () {
        const updates = [];

        $('#detailsBody tr').each(function () {
            const id = $(this).find('.inspection-date').data('id');
            const inspectionDate = $(this).find('.inspection-date').val();
            const ppMeetingDate = $(this).find('.pp-meeting-date').val();

            const original = originalData.find(item => item.id == id);

            if (original) {
                const isInspectionDateChanged = inspectionDate !== original.inspection_actual_date;
                const isPPMeetingDateChanged = ppMeetingDate !== original.pp_meeting_actual;

                if (isInspectionDateChanged || isPPMeetingDateChanged) {
                    updates.push({
                        id,
                        inspection_actual_date: isInspectionDateChanged ? inspectionDate : original.inspection_actual_date,
                        pp_meeting_actual: isPPMeetingDateChanged ? ppMeetingDate : original.pp_meeting_actual,
                    });
                }
            }
        });

        if (updates.length > 0) {
            $.ajax({
                url: '/update-task-details',
                method: 'POST',
                data: { updates, _token: '{{ csrf_token() }}' },
                success: function (response) {
                    alert(response.message);
                    $('#detailsModal').modal('hide');
                },
                error: function () {
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
