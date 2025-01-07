{{-- <x-backend.layouts.master>
    <!-- packages/YourVendor/ProductionTracking/resources/views/jobs/edit.blade.php -->
    <!--message show in .swl sweet alert-->
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
    <div class="container">
        <h1 class="text-center">Update Sewing Plan</h1>
        <form action="{{ route('sewing_plans.store', $jobs_no) }}" method="POST">
            @csrf

            <div class="row p-1">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <input type="hidden" name="created_by" value="{{ auth()->user()->id }}">
                            <input type="hidden" name="division_id" value="2">
                            <input type="hidden" name="division_name" value="Factory">
                            <input type="hidden" name="company_id" value="3">
                            <input type="hidden" name="company_name" value="FAL - Factory">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td class="create_label_column">Job No</td>
                                        <td class="create_input_column">
                                            <input type="text" name="job_no" class="form-control"
                                                placeholder="Job No" value="{{ $jobs_no }}" readonly>
                                        </td>
                                        <td class="create_label_column">Buyer</td>
                                        <td class="create_input_column">
                                            {{ $basic_info->buyer }}
                                        </td>
                                        <td class="create_label_column">Style</td>
                                        <td class="create_input_column">
                                            {{ $basic_info->style }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="create_label_column">PO</td>
                                        <td class="create_input_column">
                                            {{ $basic_info->po }}
                                        </td>
                                        <td class="create_label_column">Department</td>
                                        <td class="create_input_column">
                                            {{ $basic_info->department }}
                                        </td>
                                        <td class="create_label_column">Item</td>
                                        <td class="create_input_column">
                                            {{ $basic_info->item }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="create_label_column">Destination</td>
                                        <td class="create_input_column">
                                            {{ $basic_info->destination }}
                                        </td>
                                        <td class="create_label_column">Order Quantity</td>
                                        <td class="create_input_column">
                                            {{ $basic_info->order_quantity }}
                                        </td>
                                        <td class="create_label_column">Sweing Balance</td>
                                        <td class="create_input_column">
                                            <input type="number" name="sewing_balance" class="form-control"
                                                placeholder="Sewing Balance"
                                                value="{{ $old_sewing_basic_info->sewing_balance }}" required readonly
                                                id="sewing_balance">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="create_label_column">Production Plan</td>
                                        <td class="create_input_column">
                                            <input type="month" name="production_plan" class="form-control"
                                                placeholder="Production Plan" required
                                                value="{{ $old_sewing_basic_info->production_plan }}">
                                        </td>
                                        <td class="create_label_column">Target SMV</td>
                                        <td class="create_input_column">
                                            <input type="number" step="0.01" name="target_smv" class="form-control"
                                                placeholder="Target SMV" value="{{ $basic_info->target_smv }}" required
                                                readonly id="target_smv">
                                        </td>
                                        <td class="create_label_column">Production Min Balance</td>
                                        <td class="create_input_column">
                                            <input type="number" step="0.01" name="production_min_balance"
                                                class="form-control" placeholder="Production Min Balance" required
                                                readonly id="production_min_balance"
                                                value="{{ $old_sewing_basic_info->production_min_balance }}">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="table table-bordered mt-2 text-center" id="colorWayTable">
                                <thead>
                                    <tr>
                                        <th>Color</th>
                                        <th>Size</th>
                                        <th>Remain Quantity</th>
                                        <th>Sewing Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($old_sewing_balances as $color)
                                        <tr>
                                            <input type="hidden" name="color_id[]" value="{{ $color->id }}">
                                            <td>
                                                <input type="text" name="color[]" class="form-control"
                                                    placeholder="Color" value="{{ $color->color }}" readonly>
                                            </td>
                                            <td>
                                                <input type="text" name="size[]" class="form-control"
                                                    placeholder="Size" value="{{ $color->size }}" readonly>
                                            </td>
                                            <td>
                                                @php
                                                    $total_sewing_qty = 0;
                                                    $order_qty = $color_sizes_qties
                                                        ->where('id', $color->job_id)
                                                        ->sum('color_quantity');
                                                    $remaining_qty = $old_sewing_balances
                                                        ->where('color_id', $color->id)
                                                        ->sum('sewing_balance');
                                                    if ($remaining_qty > 0) {
                                                        $total_sewing_qty = $remaining_qty;
                                                    } else {
                                                        $total_sewing_qty = 0;
                                                    }
                                                    $remain_qty = $order_qty - $total_sewing_qty;

                                                @endphp
                                                <input type="number" name="color_quantity[]" class="form-control"
                                                    placeholder="Quantity" value="{{ $remain_qty }}" readonly>
                                            </td>
                                            <td>
                                                <input type="number" name="sewing_quantity[]"
                                                    class="form-control sewing_quantity" placeholder="Sewing Quantity"
                                                    value="{{ $color->sewing_balance }}">
                                                <input type="hidden" name="old_sewing_quantity[]"
                                                    value="{{ $color->sewing_balance }}">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div class="button-container">
                                <a href="{{ route('jobs.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left"></i> Cancel
                                </a>
                                <button type="submit" id="saveButton" class="btn btn-outline-success">
                                    <i class="fas fa-save"></i> Update Balance
                                </button>

                            </div>
        </form>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

     

    <script>
        $(document).ready(function() {
            $('#style-select').select2();
            $('#po-select').select2();
            $('#department-select').select2();

            calculateSewingBalance();
            calculateProductionMinBalance();

            $('.sewing_quantity').on('input', function() {
                const maxQuantity = parseInt($(this).closest('tr').find('[name="color_quantity[]"]')
                .val()) || 0;
                let sewingQuantity = parseInt($(this).val()) || 0;

                if (sewingQuantity > maxQuantity) {
                    sewingQuantity = maxQuantity;
                }

                $(this).val(sewingQuantity);

                calculateSewingBalance();
                calculateProductionMinBalance();
            });

            function calculateSewingBalance() {
                let totalSewingQuantity = 0;
                $('.sewing_quantity').each(function() {
                    totalSewingQuantity += parseInt($(this).val()) || 0;
                });
                $('#sewing_balance').val(totalSewingQuantity);
            }

            function calculateProductionMinBalance() {
                const sewingBalance = parseInt($('#sewing_balance').val()) || 0;
                const targetSmv = parseFloat($('#target_smv').val()) || 0;
                const productionMinBalance = (sewingBalance * targetSmv).toFixed(2);
                $('#production_min_balance').val(productionMinBalance);
            }
        });
    </script>
</x-backend.layouts.master> --}}

<x-backend.layouts.master>

    <!--message show in .swl sweet alert-->
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
        <form action="{{ route('sewing_plans.store') }}" method="POST">
            @csrf

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
                                                class="form-control" placeholder="Production Plan" required value="{{ $capacity_plan->production_plan }}">
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
                                        <td>
                                            Total
                                        </td>
                                        <td><input type="text" class="form-control" name="workingDays"
                                                id="workingDays" readonly value="{{ $capacity_plan->workingDays }}">
                                            </td>
                                        <td><input type="text" name="running_machines" class="form-control"
                                                id="runningMachines" readonly value="{{ $capacity_plan->running_machines }}">
                                            </td>
                                        <td><input type="text" name="helpers" class="form-control" id="helpers"
                                                readonly value="{{ $capacity_plan->helpers }}"></td>
                                        <td><input type="text" name="working_hours" class="form-control"
                                                id="workingHours" readonly value="{{ $capacity_plan->working_hours }}"> </td>
                                        <td><input type="text" name="efficiency" class="form-control" id="efficiency"
                                                readonly value="{{ $capacity_plan->efficiency }}"></td>
                                        <td><input type="text" name="smv" class="form-control" id="smv_data"
                                                readonly value="{{ $basic_info->target_smv }}"></td>
                                        <td><input type="text" class="form-control" name="daily_capacity_minutes"
                                                id="dailyCapacityMinutes" readonly value="{{ $capacity_plan->daily_capacity_minutes }}">
                                        </td>
                                        <td><input type="text" name="weekly_capacity_minutes" class="form-control"
                                                id="weeklyCapacityMinutes" readonly value="{{ $capacity_plan->weekly_capacity_minutes }}">
                                        <td><input type="text" name="monthly_capacity_minutes" class="form-control"
                                                id="monthlyCapacityMinutes" readonly value="{{ $capacity_plan->monthly_capacity_minutes }}">
                                        <td><input type="text" name="monthly_capacity_quantity" class="form-control"
                                                id="monthlyCapacityQuantity" readonly value="{{ $capacity_plan->monthly_capacity_quantity }}">

                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-left">
                                            Old Capacity
                                        </td>

                                        <td> @php
                                            //check the existing capacity plan for the selected month and if already sewin plan exists in the selected month dynamically then show the available capacity
                                            $existing_capacity = $color_sizes_qties->sum('total_sewing_quantity');
                                            $monthly_existing_capacity_quantity = $existing_capacity;
                                        @endphp
                                            <input type="text" name="monthly_existing_capacity_quantity"
                                                id="monthly_existing_capacity_quantity"
                                                value="{{ $monthly_existing_capacity_quantity }}" readonly
                                                class="form-control">
                                        </td>
                                        <td colspan="4" class="text-left">
                                            Available Capacity
                                        </td>
                                        <td><input type="text" name="monthly_capacity_quantityAvailable"
                                                class="form-control" id="monthlyCapacityQuantityAvailable" readonly>
                                        </td>

                                    </tr>
                                </tbody>
                            </table>

                            <table class="table table-bordered mt-2 text-center" id="colorWayTable"
                                style="overflow-x:auto;">
                                <thead>
                                    <tr>
                                        <th>Job ID</th>
                                        <th>Job No</th>
                                        <th>Color</th>
                                        <th>Size</th>
                                        <th>Order Quantity</th>
                                        <th>Total Sewing Quantity</th>
                                        <th>Remain Quantity</th>
                                        <th>Sewing Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($color_sizes_qties as $color)
                                        <tr>
                                            <td>
                                                <input type="text" name="color_id[]" class="form-control"
                                                    value="{{ $color->id }}" readonly>
                                            </td>
                                            <td>
                                                <input type="text" name="job_no[]" class="form-control"
                                                    value="{{ $color->job_no }}" readonly>
                                            </td>
                                            <td>
                                                <input type="text" name="color[]" class="form-control"
                                                    value="{{ $color->color }}" readonly>
                                            </td>
                                            <td>
                                                <input type="text" name="size[]" class="form-control"
                                                    value="{{ $color->size }}" readonly>
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
                                                    class="form-control sewing-quantity"
                                                    placeholder="Sewing Quantity">
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

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>



   
    <script>
        $(document).ready(function() {
            // Cache selectors for performance
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
            const sewingQuantities = $('input.sewing-quantity');

            let recalculating = false; // Flag to prevent recursive recalculation

            // Event listener for production plan change
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

                            calculateAvailableCapacity(); // Recalculate with new data
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
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to check for existing capacity plan. Please try again.'
                        });
                    }
                });
            });

            // Double-click to set sewing quantity equal to remaining quantity
            $('input.remaining-quantity').on('dblclick', function() {
                const remainingQuantity = $(this).val();
                $(this).closest('tr').find('input.sewing-quantity').val(remainingQuantity);
                calculateAvailableCapacity();
            });

            // Event listener for sewing quantity input changes
            sewingQuantities.on('input', function() {
                calculateAvailableCapacity();
            });

            // Function to calculate and update available capacity
            function calculateAvailableCapacity() {
                if (recalculating) return; // Prevent recursive recalculations

                recalculating = true;
                let totalSewingQuantity = 0;

                sewingQuantities.each(function() {
                    totalSewingQuantity += Number($(this).val()) || 0;
                });

                const maxCapacity = Number(monthlyCapacityQuantity.val()) || 0;
                const oldCapacity = Number(monthly_existing_capacity_quantity.val()) || 0;
                const availableCapacity = maxCapacity - oldCapacity- totalSewingQuantity;

                monthlyCapacityQuantityAvailable.val(Math.max(availableCapacity, 0)); // Ensure non-negative values

                // Limit sewing quantity dynamically
                if (totalSewingQuantity > maxCapacity) {
                    sewingQuantities.each(function() {
                        $(this).val(0); // Reset values
                    });
                    Swal.fire({
                        icon: 'warning',
                        title: 'Limit Exceeded',
                        text: 'Sewing quantity exceeds the monthly capacity. Adjust the values.'
                    });
                }

                recalculating = false; // Reset flag
            }
        });
    </script>
 <!--after input the production plan, then show that month's factory calendar with holidays and count of working days and if there is any holiday then show the message that to adjust the plan and redrect to the factory holiday page index-->
    {{-- <script>
        $(document).ready(function() {
            var workingDays = $('#workingDays');
            var runningMachines = $('#runningMachines');
            var helpers = $('#helpers');
            var workingHours = $('#workingHours');
            var efficiency = $('#efficiency');
            var dailyCapacityMinutes = $('#dailyCapacityMinutes');
            var weeklyCapacityMinutes = $('#weeklyCapacityMinutes');
            var monthlyCapacityMinutes = $('#monthlyCapacityMinutes');
            var monthlyCapacityQuantity = $('#monthlyCapacityQuantity');
            var monthly_existing_capacity_quantity = $('#monthly_existing_capacity_quantity');
            var smvData = $('#smv_data');
            //dynamic available capacity of monthlyCapacityQuantityAvailable = monthlyCapacityQuantity - monthly_existing_capacity_quantity
            var monthlyCapacityQuantityAvailable = $('#monthlyCapacityQuantityAvailable');

            // Event listener for production plan change
            $('#productionPlan').on('change', function() {
                var productionPlan = $(this).val();
                $.ajax({
                    url: "{{ route('check_existing_capacity') }}",
                    method: "GET",
                    data: {
                        production_plan: productionPlan
                    },
                    success: function(response) {
                        if (response.exists === true) {
                            let data = response.data;
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

                            // Set available capacity initially
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
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to check for existing capacity plan. Please try again.'
                        });
                    }
                });
            });

            // Double-click to set sewing quantity equal to remaining quantity
            $('input.remaining-quantity').dblclick(function() {
                var remainingQuantity = $(this).val();
                $(this).closest('tr').find('input.sewing-quantity').val(remainingQuantity);
                calculateAvailableCapacity();
            });

            // Calculate and limit sewing quantity input dynamically
            $('input.sewing-quantity').on('input', function() {
                calculateAvailableCapacity();
            });

            // Function to calculate and update available capacity
            function calculateAvailableCapacity() {
                let totalSewingQuantity = 0;
                $('input.sewing-quantity').each(function() {
                    totalSewingQuantity += parseInt($(this).val() || 0);
                });

                // Get monthly capacity quantity
                let maxCapacity = parseInt(monthlyCapacityQuantity.val() || 0);
                let availableCapacity = maxCapacity - totalSewingQuantity;

                // Update available capacity field
                monthlyCapacityQuantityAvailable.val(availableCapacity);

                // Limit sewing quantity based on available capacity
                $('input.sewing-quantity').each(function() {
                    if (totalSewingQuantity > maxCapacity) {
                        $(this).val(0); // Reset if exceeds max capacity
                        Swal.fire({
                            icon: 'warning',
                            title: 'Limit Exceeded',
                            text: 'Sewing quantity exceeds the monthly capacity. Adjust the values.',
                        });
                        calculateAvailableCapacity(); // Recalculate after reset
                    }
                });
            }
        });
    </script> --}}
</x-backend.layouts.master>
 
