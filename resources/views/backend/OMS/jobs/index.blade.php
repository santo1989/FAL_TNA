<x-backend.layouts.master>
    <div class="card mx-5 my-5 bg-white">
        <div class="row p-1">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="text-center p-1">Job List</h3>
                    <div id="last-updated" class="text-muted small">
                        Last updated: {{ now()->format('h:i:s A') }}
                    </div>
                </div>
                
                <div class="row p-1">
                    <div class="col-md-6 text-md-start text-center mb-2 mb-md-0">
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Close
                        </a>
                        <a href="{{ route('factory_holidays.create') }}" class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-plus"></i> Create Holydays
                        </a>
                        <a href="{{ route('capacity_plans.create') }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-plus"></i> Add Capacity Plan
                        </a>
                    </div>
                    
                    <div class="col-md-6 text-md-end text-center">
                        <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#PlanModal">
                            <i class="fas fa-tachometer-alt"></i> Plan
                        </button>
                        <button type="button" class="btn btn-outline-success btn-sm" data-toggle="modal" data-target="#ReportModal">
                            <i class="fas fa-tachometer-alt"></i> Report
                        </button>
                        <button type="button" class="btn btn-outline-info btn-sm" data-toggle="modal" data-target="#HistoryModal">
                            <i class="fas fa-tachometer-alt"></i> History
                        </button>

                        @can('TNA-CURD')
                            <a href="{{ route('jobs.create') }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-plus"></i> Add Job
                            </a>
                            <a href="{{ route('job_excel_upload') }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-plus"></i> Job Excel Upload
                            </a>
                        @endcan
                    </div>
                </div>
            </div>

            <div class="col-12 mt-3">
                <div class="card p-1">
                    @if (session('message'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <strong>{{ session('message') }}</strong>
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    @endif

                    <x-backend.layouts.elements.errors />

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped text-nowrap" id="jobsTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Job No</th>
                                        <th>Buyer</th>
                                        <th>Style</th>
                                        <th>PO Number</th>
                                        <th>Item</th>
                                        <th>Order Qty</th>
                                        <th>Sewing Balance</th>
                                        <th>Shipped Qty</th>
                                        <th>Receive Date</th>
                                        <th>Delivery Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="jobTableBody">
                                    @include('backend.OMS.jobs.partials.job_rows', ['jobs' => $jobs])
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Modals -->
    @include('backend.OMS.jobs.partials.plan_modal')
    @include('backend.OMS.jobs.partials.report_modal')
    @include('backend.OMS.jobs.partials.history_modal')
    @include('backend.OMS.jobs.partials.job_modal')
    @include('backend.OMS.jobs.partials.sewing_modal')
    @include('backend.OMS.jobs.partials.shipment_modal')

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            const dataTable = $('#jobsTable').DataTable({
                processing: true,
                serverSide: false,
                paging: true,
                pageLength: 25,
                lengthMenu: [10, 25, 50, 100],
                autoWidth: true,
                order: [[8, 'desc']],
                language: {
                    emptyTable: "No jobs found",
                    zeroRecords: "No matching jobs found"
                }
            });

            // Auto-update function
            function updateJobTable() {
                $.ajax({
                    url: '{{ route('jobs.tableBody') }}',
                    type: 'GET',
                    beforeSend: function() {
                        $('#loading-indicator').show();
                    },
                    success: function(data) {
                        $('#jobTableBody').html(data);
                        $('#last-updated').text('Last updated: ' + new Date().toLocaleTimeString());
                    },
                    error: function() {
                        console.error('Error updating job table');
                    },
                    complete: function() {
                        $('#loading-indicator').hide();
                        setTimeout(updateJobTable, 10000); // Refresh every 10 seconds
                    }
                });
            }

            // Start auto-update
            setTimeout(updateJobTable, 10000);
            
            // Job modal handler
            $('#jobModal').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget);
                const jobId = button.data('job-id');
                const jobNo = button.data('job-no');
                
                const modal = $(this);
                modal.find('#modalJobNo').text(jobNo);
                modal.find('#viewJobLink').attr('href', '/jobs/' + jobId);
                modal.find('#editJobLink').attr('href', '/jobs/' + jobId + '/edit_jobs');
                modal.find('#calendarJobLink').attr('href', '/shipments/create/' + jobId);
                modal.find('#SewingBalance').attr('href', '/sewing_balances/create/' + jobId);
                modal.find('#deleteJobForm').attr('action', '/jobs/' + jobId);
            });

            // Sewing modal handler (AJAX loading)
            $('#sewingModal').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget);
                const jobNo = button.data('job-no');
                const modal = $(this);
                
                modal.find('.modal-body').html(`
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2">Loading sewing data...</p>
                    </div>
                `);
                
                $.ajax({
                    url: '/jobs/' + jobNo + '/sewing-data',
                    type: 'GET',
                    success: function(data) {
                        modal.find('.modal-body').html(data);
                    },
                    error: function() {
                        modal.find('.modal-body').html(`
                            <div class="alert alert-danger">
                                Failed to load sewing data
                            </div>
                        `);
                    }
                });
            });

            // Shipment modal handler (AJAX loading)
            $('#ShipmentModal').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget);
                const jobNo = button.data('job-no');
                const modal = $(this);
                
                modal.find('.modal-body').html(`
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2">Loading shipment data...</p>
                    </div>
                `);
                
                $.ajax({
                    url: '/jobs/' + jobNo + '/shipment-data',
                    type: 'GET',
                    success: function(data) {
                        modal.find('.modal-body').html(data);
                    },
                    error: function() {
                        modal.find('.modal-body').html(`
                            <div class="alert alert-danger">
                                Failed to load shipment data
                            </div>
                        `);
                    }
                });
            });
        });
    </script>

    <div id="loading-indicator" style="display:none; position:fixed; bottom:20px; right:20px; z-index:1000;">
        <div class="alert alert-info d-flex align-items-center">
            <div class="spinner-border spinner-border-sm mr-2" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            Updating job data...
        </div>
    </div>
</x-backend.layouts.master>