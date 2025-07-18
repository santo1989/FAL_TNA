<x-backend.layouts.master>
    @if (session('message'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: "{{ session('message') }}",
                showConfirmButton: false,
                timer: 2000
            });
        </script>
    @endif

    <x-backend.layouts.elements.errors />
    <div class="container-fluid">
        <h1 class="text-center">Update Sewing Plan</h1>

        <div class="row p-1">
            <div class="col-12">
                <div class="card">
                    <div class="card-body" style="overflow-x:auto;">
                        <input type="hidden" name="created_by" value="{{ auth()->user()->id }}">
                        <input type="hidden" name="division_id" value="2">
                        <input type="hidden" name="division_name" value="Factory">
                        <input type="hidden" name="company_id" value="3">
                        <input type="hidden" name="company_name" value="FAL - Factory">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td class="create_label_column">Production Plan</td>
                                    <td class="create_input_column">
                                        <input type="month" name="production_plan" id="productionPlan"
                                            class="form-control" placeholder="Production Plan" required
                                            value="{{ optional($db_production_plan)->production_plan }}">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Number of Working Days</th>
                                    <th>Number of running machines</th>
                                    <th>Number of helpers</th>
                                    <th>working hours</th>
                                    <th>Expected Efficiency%</th>
                                    <th>SMV</th>
                                    <th>Daily Capacity Minutes</th>
                                    <th>Weekly Capacity Minutes</th>
                                    <th>Monthly Capacity Minutes</th>
                                    <th>Monthly Capacity Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Total</td>
                                    <td><input type="text" class="form-control" name="workingDays" id="workingDays"
                                            readonly value="{{ optional($db_production_plan)->working_days }}"></td>
                                    <td><input type="text" name="running_machines" class="form-control"
                                            id="runningMachines" readonly
                                            value="{{ optional($db_production_plan)->running_machines }}"></td>
                                    <td><input type="text" name="helpers" class="form-control" id="helpers"
                                            readonly value="{{ optional($db_production_plan)->helpers }}"></td>
                                    <td><input type="text" name="working_hours" class="form-control"
                                            id="workingHours" readonly
                                            value="{{ optional($db_production_plan)->working_hours }}"></td>
                                    <td><input type="text" name="efficiency" class="form-control" id="efficiency"
                                            readonly value="{{ optional($db_production_plan)->efficiency }}"></td>
                                    <td><input type="text" name="smv" class="form-control" id="smv_data"
                                            readonly value="{{ optional($db_production_plan)->smv }}"></td>
                                    <td><input type="text" class="form-control" name="daily_capacity_minutes"
                                            id="dailyCapacityMinutes" readonly
                                            value="{{ optional($db_production_plan)->daily_capacity_minutes }}"></td>
                                    <td><input type="text" name="weekly_capacity_minutes" class="form-control"
                                            id="weeklyCapacityMinutes" readonly
                                            value="{{ optional($db_production_plan)->weekly_capacity_minutes }}"></td>
                                    <td><input type="text" name="monthly_capacity_minutes" class="form-control"
                                            id="monthlyCapacityMinutes" readonly
                                            value="{{ optional($db_production_plan)->monthly_capacity_minutes }}"></td>
                                    <td><input type="text" name="monthly_capacity_quantity" class="form-control"
                                            id="monthlyCapacityQuantity" readonly
                                            value="{{ optional($db_production_plan)->monthly_capacity_quantity }}">
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-left">Old Capacity</td>
                                    <td>
                                        @php
                                            $existing_capacity = $color_sizes_qties->sum('total_sewing_quantity');
                                            $monthly_existing_capacity_quantity = $existing_capacity;
                                        @endphp
                                        <input type="text" name="monthly_existing_capacity_quantity"
                                            id="monthly_existing_capacity_quantity"
                                            value="{{ $monthly_existing_capacity_quantity }}" readonly
                                            class="form-control">
                                    </td>
                                    <td colspan="4" class="text-left">Available Capacity</td>
                                    <td><input type="text" name="monthly_capacity_quantityAvailable"
                                            class="form-control" id="monthlyCapacityQuantityAvailable" readonly>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <!--search panel-->
                        <div class="card">
                            <table class="table table-bordered table-striped">
                                <tr>
                                    <td class="create_label_column">Buyer</td>
                                    <td class="create_input_column">
                                        <select name="buyer_id" id="buyer_id" class="form-control">
                                            <option value="">Select Buyer</option>
                                            @foreach ($buyers as $buyer)
                                                <option value="{{ $buyer->buyer_id }}">{{ $buyer->buyer }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="create_label_column">PO</td>
                                    <td class="create_input_column">
                                        <select name="po" id="po" class="form-control">
                                            <option value="">Select PO</option>
                                        </select>
                                    </td>
                                    <td class="create_label_column">Style</td>
                                    <td class="create_input_column">
                                        <select name="style_id" id="style_id" class="form-control">
                                            <option value="">Select Style</option>
                                        </select>
                                    </td>
                                    <td class="create_label_column">Shipment Start Date</td>
                                    <td class="create_input_column">
                                        <input type="date" name="shipment_start_date" class="form-control"
                                            placeholder="Shipment Start Date">
                                    </td>
                                    <td class="create_label_column">Shipment End Date</td>
                                    <td class="create_input_column">
                                        <input type="date" name="shipment_end_date" class="form-control"
                                            placeholder="Shipment End Date">
                                    </td>
                                    <td class="create_input_column">
                                        <button type="button" class="btn btn-danger" id="searchOnlyButton">
                                            <i class="fas fa-search"></i> Search Only
                                        </button>
                                        <button type="reset" class="btn btn-secondary" id="resetButton">
                                            <i class="fas fa-undo"></i> Reset
                                        </button>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <form action="{{ route('sewing_plans_store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="production_plan" id="form_production_plan"
                                value="{{ optional($db_production_plan)->production_plan }}">
                            <table class="table table-bordered mt-2 text-center" id="colorWayTable"
                                style="overflow-x:auto;">
                                <thead>
                                    <tr>
                                        <th>Select</th>
                                        <th>Job ID / Buyer</th>
                                        <th>Job No / Style</th>
                                        <th>Color/Size/Shipment Date</th>
                                        <th>Order Quantity</th>
                                        <th>Total Sewing Quantity</th>
                                        <th>Remain Quantity</th>
                                        <th>Sewing Quantity</th>
                                    </tr>
                                </thead>
                                <tbody id="colorWayTableBody">
                                    @foreach ($color_sizes_qties as $color)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="color_ids[]"
                                                    value="{{ $color->id }}" checked>
                                            </td>
                                            <td>
                                                <input type="hidden" name="color_id[]" class="form-control"
                                                    value="{{ $color->id }}">
                                                <label class="form-control">{{ $color->buyer }}</label>
                                            </td>
                                            <td>
                                                <input type="hidden" name="job_no[]" class="form-control"
                                                    value="{{ $color->job_no }}">
                                                <label class="form-control">{{ $color->style }}</label>
                                            </td>
                                            <td>
                                                <input type="hidden" name="color[]" class="form-control"
                                                    value="{{ $color->color }}" readonly>
                                            
                                                <input type="hidden" name="size[]" class="form-control"
                                                    value="{{ $color->size }}" readonly>
                                                    @php
                                                        $shipmentDate = \Carbon\Carbon::parse($color->shipment_date)->format('d-m-Y') ?? 'N/A';
                                                    @endphp
                                                <label class="form-control">{{ $shipmentDate }}</label>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control"
                                                    value="{{ $color->color_quantity }}" readonly>
                                            </td>
                                            <td>
                                                <input type="number" name="total_sewing_quantity[]"
                                                    class="form-control" value="{{ $color->total_sewing_quantity }}"
                                                    readonly>
                                            </td>
                                            <td>
                                                <input type="number" name="remaining_quantity[]"
                                                    class="form-control remaining-quantity"
                                                    value="{{ $color->remaining_quantity }}" readonly>
                                            </td>
                                            <td>
                                                <input type="number" name="color_quantity[]"
                                                    class="form-control sewing-quantity" placeholder="Sewing Quantity"
                                                    value="{{ $color->remaining_quantity }}">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div class="button-container">
                                <a href="{{ route('sewing_plans.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left"></i> Cancel
                                </a>
                                <button type="submit" id="saveButton" class="btn btn-outline-success">
                                    <i class="fas fa-save"></i> Update Plan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Auto-capture remain quantity when sewing quantity field is focused
            $(document).on('focus', '.sewing-quantity', function() {
                const row = $(this).closest('tr');
                const remainQty = row.find('.remaining-quantity').val();
                
                // Only set if current value is empty or zero
                if ($(this).val() === '' || $(this).val() == 0) {
                    $(this).val(remainQty);
                }
            });

            // Validate sewing quantity input
            $(document).on('input', '.sewing-quantity', function() {
                const row = $(this).closest('tr');
                const max = parseInt(row.find('.remaining-quantity').val());
                const entered = parseInt($(this).val()) || 0;
                
                if (entered > max) {
                    Swal.fire('Error', 'Cannot exceed remain quantity: ' + max, 'error');
                    $(this).val(max);
                }
                
                calculateAvailableCapacity();
            });

            // Double-click to set sewing quantity
            $(document).on('dblclick', '.remaining-quantity', function() {
                const row = $(this).closest('tr');
                const remainQty = row.find('.remaining-quantity').val();
                row.find('.sewing-quantity').val(remainQty);
                calculateAvailableCapacity();
            });

            // Cache selectors
            const workingDays = $('#workingDays');
            const runningMachines = $('#runningMachines');
            const helpers = $('#helpers');
            const workingHours = $('#workingHours');
            const efficiency = $('#efficiency');
            const dailyCapacityMinutes = $('#dailyCapacityMinutes');
            const weeklyCapacityMinutes = $('#weeklyCapacityMinutes');
            const monthlyCapacityMinutes = $('#monthlyCapacityMinutes');
            const monthlyCapacityQuantity = $('#monthlyCapacityQuantity');
            const monthly_existing_capacity_quantity = $('#monthly_existing_capacity_quantity');
            const smvData = $('#smv_data');
            const monthlyCapacityQuantityAvailable = $('#monthlyCapacityQuantityAvailable');
            const production_plan_select = $('#production_plan_select');
            let recalculating = false;

            // Buyer change event to load POs and Styles
            $('#buyer_id').on('change', function() {
                const buyerId = $(this).val();
                const poSelect = $('#po');
                const styleSelect = $('#style_id');

                if (!buyerId) {
                    poSelect.empty().append('<option value="">Select PO</option>');
                    styleSelect.empty().append('<option value="">Select Style</option>');
                    return;
                }

                $.ajax({
                    url: "{{ route('get_buyer_po_styles') }}",
                    method: "GET",
                    data: {
                        buyer_id: buyerId
                    },
                    success: function(response) {
                        if (response.success) {
                            const pos = response.data.pos;
                            const styles = response.data.styles;

                            // Update PO dropdown
                            poSelect.empty().append('<option value="">Select PO</option>');
                            pos.forEach(po => {
                                poSelect.append(new Option(po, po));
                            });

                            // Update Style dropdown
                            styleSelect.empty().append(
                                '<option value="">Select Style</option>');
                            styles.forEach(style => {
                                styleSelect.append(new Option(style.style, style.id));
                            });
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Failed to load POs and Styles.', 'error');
                    }
                });
            });

            // Search Only Button Click Event
            $('#searchOnlyButton').on('click', function() {
                const buyerId = $('#buyer_id').val();
                const po = $('#po').val();
                const styleId = $('#style_id').val();
                const shipmentStartDate = $('input[name="shipment_start_date"]').val();
                const shipmentEndDate = $('input[name="shipment_end_date"]').val();
                const productionPlan = $('#productionPlan').val();

                // Validate inputs
                if (!buyerId && !po && !styleId && !shipmentStartDate && !shipmentEndDate) {
                    Swal.fire('Warning', 'Please select at least one search criteria.', 'warning');
                    return;
                }

                if (!productionPlan) {
                    Swal.fire('Warning', 'Production Plan is required.', 'warning');
                    return;
                }

                $.ajax({
                    url: "{{ route('search_color_sizes_qties') }}",
                    method: "GET",
                    data: {
                        buyer_id: buyerId,
                        po: po,
                        style_id: styleId,
                        shipment_start_date: shipmentStartDate,
                        shipment_end_date: shipmentEndDate,
                        production_plan: productionPlan
                    },
                    success: function(response) {
                        if (response.success) {
                            const colorWayTableBody = $('#colorWayTableBody');
                            colorWayTableBody.empty();

                            response.data.forEach(color => {
                                const shipmentDate = color.shipment_date ? new Date(color.shipment_date).toLocaleDateString('en-GB') : 'N/A';
                                const row = `
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="color_ids[]" value="${color.id}" checked>
                                        </td>
                                        <td>
                                            <input type="hidden" name="color_id[]" value="${color.id}">
                                            <label class="form-control">${color.buyer}</label>
                                        </td>
                                        <td>
                                            <input type="hidden" name="job_no[]" value="${color.job_no}">
                                            <label class="form-control">${color.style}</label>
                                        </td>
                                        <td>
                                            <input type="hidden" name="color[]" value="${color.color}">
                                            <input type="hidden" name="size[]" value="${color.size}">
                                            <label class="form-control">${shipmentDate}</label>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" value="${color.color_quantity}" readonly>
                                        </td>
                                        <td>
                                            <input type="number" name="total_sewing_quantity[]" value="${color.total_sewing_quantity}" class="form-control" readonly>
                                        </td>
                                        <td>
                                            <input type="number" name="remaining_quantity[]" value="${color.remaining_quantity}" class="form-control remaining-quantity" readonly>
                                        </td>
                                        <td>
                                            <input type="number" name="color_quantity[]" 
                                                class="form-control sewing-quantity" 
                                                placeholder="Sewing Quantity"
                                                value="${color.remaining_quantity}">
                                        </td>
                                    </tr>
                                `;
                                colorWayTableBody.append(row);
                            });

                            calculateAvailableCapacity();
                            localStorage.setItem('sewingPlanSearchCriteria', JSON.stringify({
                                buyerId,
                                po,
                                styleId,
                                shipmentStartDate,
                                shipmentEndDate,
                                productionPlan
                            }));
                        } else {
                            Swal.fire('Warning', response.message || 'No data found.', 'warning');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Server error', 'error');
                    }
                });
            });

            // Production Plan Change Event
            $('#productionPlan').on('change', function() {
                const productionPlan = $(this).val();
                $.ajax({
                    url: "{{ route('check_existing_capacity') }}",
                    method: "GET",
                    data: {
                        production_plan: productionPlan
                    },
                    success: function(response) {
                        if (response.exists === true) {
                            const data = response.data;
                            workingDays.val(data.workingDays);
                            runningMachines.val(data.running_machines);
                            helpers.val(data.helpers);
                            workingHours.val(data.working_hours);
                            efficiency.val(data.efficiency);
                            dailyCapacityMinutes.val(data.daily_capacity_minutes);
                            weeklyCapacityMinutes.val(data.weekly_capacity_minutes);
                            monthlyCapacityMinutes.val(data.monthly_capacity_minutes);
                            monthlyCapacityQuantity.val(data.monthly_capacity_quantity);
                            smvData.val(data.smv);
                            production_plan_select.val(data.production_plan);

                            calculateAvailableCapacity();
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'No Capacity Plan Exists',
                                text: 'No capacity plan exists for the selected month. You can proceed to create a new plan.',
                                showCancelButton: true,
                                confirmButtonText: 'Edit Existing Plan',
                                cancelButtonText: 'Create New Plan',
                                preConfirm: () => {
                                    window.location.href = response.edit_url;
                                }
                            });
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Failed to check capacity plan.', 'error');
                    }
                });
            });

            // Function to calculate available capacity
            function calculateAvailableCapacity() {
                let totalSewing = 0;
                $('.sewing-quantity').each(function() {
                    totalSewing += parseInt($(this).val(), 10) || 0;
                });

                const monthlyCapacity = parseInt(monthlyCapacityQuantity.val(), 10) || 0;
                const existingCapacity = parseInt(monthly_existing_capacity_quantity.val(), 10) || 0;
                const available = monthlyCapacity - existingCapacity - totalSewing;

                monthlyCapacityQuantityAvailable.val(Math.max(available, 0));

                if (available < 0) {
                    Swal.fire('Warning', 'Exceeds available capacity by ' + Math.abs(available), 'warning');
                }
            }

            // Reset button click event
            $('#resetButton').on('click', function() {
                localStorage.removeItem('sewingPlanSearchCriteria');
                location.reload();
            });

            // Initialize
            calculateAvailableCapacity();

            // Sync production plan to hidden input
            $('#productionPlan').on('change', function() {
                $('#form_production_plan').val($(this).val());
            });

            // Initialize hidden field
            $('#form_production_plan').val($('#productionPlan').val());

            // Save button handler
            $('#saveButton').on('click', function() {
                const checkedRows = $('#colorWayTableBody input[type="checkbox"]:checked');
                if (checkedRows.length === 0) {
                    Swal.fire('Warning', 'Please select at least one color to save.', 'warning');
                    return false;
                }

                // Disable unchecked rows
                $('#colorWayTableBody tr').each(function() {
                    if (!$(this).find('input[type="checkbox"]').is(':checked')) {
                        $(this).find('input').prop('disabled', true);
                    }
                });

                return true;
            });

            // Restore search criteria if exists
            const savedSearch = JSON.parse(localStorage.getItem('sewingPlanSearchCriteria'));
            if (savedSearch) {
                $('#buyer_id').val(savedSearch.buyerId).trigger('change');
                // Trigger PO and Style loading after buyer change
                $(document).one('buyerPoStylesLoaded', function() {
                    $('#po').val(savedSearch.po);
                    $('#style_id').val(savedSearch.styleId);
                    $('input[name="shipment_start_date"]').val(savedSearch.shipmentStartDate);
                    $('input[name="shipment_end_date"]').val(savedSearch.shipmentEndDate);
                    $('#productionPlan').val(savedSearch.productionPlan);
                });
            }
        });
    </script>
</x-backend.layouts.master>