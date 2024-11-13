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
                                        <td><input type="text" name="smv" class="form-control" id="smv"
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
                                        <td colspan="10" class="text-left">
                                            Available Capacity
                                        </td>

                                        {{-- <td><input type="text" class="form-control"
                                                name="daily_capacity_minutesAvailable"
                                                id="dailyCapacityMinutesAvailable" readonly></td>
                                        </td>
                                        <td><input type="text" name="weekly_capacity_minutesAvailable"
                                                class="form-control" id="weeklyCapacityMinutesAvailable" readonly></td>
                                        <td><input type="text" name="monthly_capacity_minutesAvailable"
                                                class="form-control" id="monthlyCapacityMinutesAvailable" readonly></td> --}}
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
                                                <input type="number" name="sewing_quantity[]"
                                                    class="form-control sewing-quantity"
                                                    placeholder="Sewing Quantity">
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
                                    <i class="fas fa-save"></i> Update Plan
                                </button>

                            </div>
        </form>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    

    <!--after input the production plan, then show that month's factory calendar with holidays and count of working days and if there is any holiday then show the message that to adjust the plan and redrect to the factory holiday page index-->
    <script>
        $(document).ready(function() {
            var workingDays = document.getElementById('workingDays');
            var numberOfMachines = document.getElementById('runningMachines');
            var numberOfHelpers = document.getElementById('helpers');
            var workingHours = document.getElementById('workingHours');
            var efficiency = document.getElementById('efficiency');
            var dailyCapacityMinutes = document.getElementById('dailyCapacityMinutes');
            var weeklyCapacityMinutes = document.getElementById('weeklyCapacityMinutes');
            var monthlyCapacityMinutes = document.getElementById('monthlyCapacityMinutes');
            var capacityQuantity = document.getElementById('monthlyCapacityQuantity');
            var capacityValue = document.getElementById('monthlyCapacityValue');
            var smv = document.getElementById('smv');
            var data = {};

            document.getElementById('productionPlan').addEventListener('change', function() {
                var production_plan_selected = this.value;
                $.ajax({
                    url: "{{ route('check_existing_capacity') }}",
                    method: "GET",
                    data: {
                        production_plan: production_plan_selected
                    },
                    success: function(response) {
                        if (response.exists === true) {
                            data = response.data;
                            console.log(response);
                            workingDays.value = data.workingDays;
                            numberOfMachines.value = data.running_machines;
                            numberOfHelpers.value = data.helpers;
                            workingHours.value = data.working_hours;
                            efficiency.value = data.efficiency;
                            dailyCapacityMinutes.value = data.daily_capacity_minutes;
                            weeklyCapacityMinutes.value = data.weekly_capacity_minutes;
                            monthlyCapacityMinutes.value = data.monthly_capacity_minutes;
                            capacityQuantity.value = data.monthly_capacity_quantity;
                            capacityValue.value = data.monthly_capacity_value;
                            smv.valu = data.smv;

                        }
                        if (response.exists === false) {
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
        });
    </script>
    <script>
        $(document).ready(function() {
            // On double-click, set the sewing quantity equal to the remaining quantity
            $('input.remaining-quantity').dblclick(function() {
                // Find the related sewing quantity input in the same row
                var remaining_quantity = $(this).val();
                $(this).closest('tr').find('input.sewing-quantity').val(remaining_quantity);
            });

            // On change of sewing quantity, calculate the Monthly Capacity Quantity available and show a warning if the sewing quantity exceeds the available capacity
             let totalSewingQuantity = 0;
$('input.sewing-quantity').each(function() {
    totalSewingQuantity += parseInt($(this).val() || 0);
});
$('#monthlyCapacityQuantityAvailable').val(monthly_capacity_quantity - totalSewingQuantity);

        });
    </script>
</x-backend.layouts.master>
