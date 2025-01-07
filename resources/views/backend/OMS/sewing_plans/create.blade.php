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
                                                class="form-control" placeholder="Production Plan" required>
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
                                                id="workingDays" readonly></td>
                                        <td><input type="text" name="running_machines" class="form-control"
                                                id="runningMachines" readonly></td>
                                        <td><input type="text" name="helpers" class="form-control" id="helpers"
                                                readonly></td>
                                        <td><input type="text" name="working_hours" class="form-control"
                                                id="workingHours" readonly></td>
                                        <td><input type="text" name="efficiency" class="form-control" id="efficiency"
                                                readonly></td>
                                        <td><input type="text" name="smv" class="form-control" id="smv_data"
                                                readonly></td>
                                        <td><input type="text" class="form-control" name="daily_capacity_minutes"
                                                id="dailyCapacityMinutes" readonly></td>
                                        </td>
                                        <td><input type="text" name="weekly_capacity_minutes" class="form-control"
                                                id="weeklyCapacityMinutes" readonly></td>
                                        <td><input type="text" name="monthly_capacity_minutes" class="form-control"
                                                id="monthlyCapacityMinutes" readonly></td>
                                        <td><input type="text" name="monthly_capacity_quantity" class="form-control"
                                                id="monthlyCapacityQuantity" readonly></td>

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
                const availableCapacity = maxCapacity - totalSewingQuantity;

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

</x-backend.layouts.master>
