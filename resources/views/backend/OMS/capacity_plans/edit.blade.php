<x-backend.layouts.master>
    <style>
        .month-calendar {
            margin: 20px 0;
        }

        .month-header {
            font-size: 1.5em;
            font-weight: bold;
            text-align: center;
            margin-bottom: 10px;
        }

        .day-names {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            font-weight: bold;
            text-align: center;
            margin-bottom: 5px;
        }

        .day-name {
            padding: 5px;
            background: #e0e0e0;
            border-radius: 4px;
        }

        .month-days {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
        }

        .calendar-day {
            padding: 10px;
            background: #f0f0f0;
            border-radius: 4px;
            text-align: center;
            position: relative;
        }

        .blank-day {
            background: none;
        }

        .calendar-holiday {
            background: #ffcccc;
            color: #a00;
        }

        .holiday-description {
            font-size: 0.8em;
            color: #666;
            position: absolute;
            bottom: -1.5em;
            left: 50%;
            transform: translateX(-50%);
            white-space: nowrap;
        }
    </style>
    @php
        $factory_holidays = \App\Models\FactoryHoliday::all();
    @endphp
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

    <div class="container-fluid">
        <x-backend.layouts.elements.errors />
        <h1 class="text-center">Edit Capacity Plan</h1>
        <form action="{{ route('capacity_plans.update', $capacity_plan->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row p-1">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body" style="overflow-x:auto;">
                            <input type="hidden" name="created_by" value="{{ auth()->user()->id }}">

                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td>Production Plan</td>
                                        <td>
                                            <input type="month" id="productionPlan" name="production_plan"
                                                class="form-control" required
                                                value="{{ $capacity_plan->production_plan }}">

                                        </td>
                                        <td>Running Machines</td>
                                        <td>
                                            <input type="number" name="running_machines" id="running_machines"
                                                class="form-control" required
                                                value="{{ $capacity_plan->running_machines }}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Helpers</td>
                                        <td>
                                            <input type="number" name="helpers" id="helpers" class="form-control"
                                                required value="{{ $capacity_plan->helpers }}">
                                        </td>
                                        <td>Working Hours</td>
                                        <td>
                                            <input type="number" name="working_hours" id="working_hours"
                                                class="form-control" required
                                                value="{{ $capacity_plan->working_hours }}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Efficiency (%)</td>
                                        <td>
                                            <input type="number" name="efficiency" id="efficiency" class="form-control"
                                                required value="{{ $capacity_plan->efficiency }}">
                                        </td>
                                        <td>SMV</td>
                                        <td>
                                            <input type="number" name="smv" id="smv" class="form-control"
                                                required step="0.01" value="{{ $capacity_plan->smv }}">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <div id="factoryCalendar"></div>
                            <input type="hidden" name="workingDays" id="workingDays">

                            <div class="table-responsive mt-3">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Daily Capacity Minutes</th>
                                            <th>Weekly Capacity Minutes</th>
                                            <th>Monthly Capacity Minutes</th>
                                            <th>Monthly Capacity Quantity</th>
                                            <th>Monthly Capacity Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <input type="text" name="daily_capacity_minutes" class="form-control"
                                                    id="dailyCapacityMinutes" readonly
                                                    value="{{ $capacity_plan->daily_capacity_minutes }}">
                                            </td>
                                            <td>
                                                <input type="text" name="weekly_capacity_minutes"
                                                    class="form-control" id="weeklyCapacityMinutes" readonly
                                                    value="{{ $capacity_plan->weekly_capacity_minutes }}">
                                            </td>
                                            <td>
                                                <input type="text" name="monthly_capacity_minutes"
                                                    class="form-control" id="monthlyCapacityMinutes" readonly
                                                    value="{{ $capacity_plan->monthly_capacity_minutes }}">
                                            </td>
                                            <td>
                                                <input type="text" name="monthly_capacity_quantity"
                                                    class="form-control" id="monthlyCapacityQuantity" readonly
                                                    value="{{ $capacity_plan->monthly_capacity_quantity }}">
                                            </td>
                                            <td>
                                                <input type="text" name="monthly_capacity_value" class="form-control"
                                                    id="monthlyCapacityValue" readonly
                                                    value="{{ $capacity_plan->monthly_capacity_value }}">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <!-- Factory Calendar Display -->
                                <div id="factoryCalendar"></div>
                                <input type="hidden" name="workingDays" id="workingDays">
                                <div class="button-container">
                                    <a href="{{ route('capacity_plans.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-outline-success">
                                        <i class="fas fa-save"></i> Update Plan
                                    </button>
                                </div>
        </form>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarContainer = document.getElementById('factoryCalendar');
            const holidays = @json($factory_holidays);
            let workingDays = 0;
            let avgUnitPrice = 0;

            // Generate Monthly Calendar with Holidays and Working Days
            function generateMonthlyCalendar(year, month) {
                calendarContainer.innerHTML = '';

                // Calendar Structure
                const monthDiv = document.createElement("div");
                monthDiv.classList.add("month-calendar");

                const monthHeader = document.createElement("div");
                monthHeader.classList.add("month-header");
                monthHeader.textContent = new Date(year, month).toLocaleString('en-US', {
                    month: 'long',
                    year: 'numeric'
                });
                monthDiv.appendChild(monthHeader);

                const dayNamesContainer = document.createElement("div");
                dayNamesContainer.classList.add("day-names");
                const dayNames = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
                dayNames.forEach(day => {
                    const dayNameDiv = document.createElement("div");
                    dayNameDiv.classList.add("day-name");
                    dayNameDiv.textContent = day;
                    dayNamesContainer.appendChild(dayNameDiv);
                });
                monthDiv.appendChild(dayNamesContainer);

                const daysContainer = document.createElement("div");
                daysContainer.classList.add("month-days");

                const firstDay = new Date(year, month, 1);
                const lastDay = new Date(year, month + 1, 0);

                workingDays = 0;
                let holidayCount = 0;

                for (let i = 0; i < firstDay.getDay(); i++) {
                    const blankDay = document.createElement("div");
                    blankDay.classList.add("calendar-day", "blank-day");
                    daysContainer.appendChild(blankDay);
                }

                for (let date = 1; date <= lastDay.getDate(); date++) {
                    const dayDiv = document.createElement("div");
                    dayDiv.classList.add("calendar-day");

                    const fullDate =
                        `${year}-${String(month + 1).padStart(2, '0')}-${String(date).padStart(2, '0')}`;
                    const holiday = holidays.find(h => h.holiday_date === fullDate);

                    dayDiv.textContent = date;

                    if (holiday) {
                        dayDiv.classList.add("calendar-holiday");
                        const descriptionDiv = document.createElement("div");
                        descriptionDiv.classList.add("holiday-description");
                        descriptionDiv.textContent = holiday.description;
                        dayDiv.appendChild(descriptionDiv);
                        holidayCount++;
                    } else if (new Date(fullDate).getDay() !== 5) {
                        workingDays++;
                    }

                    daysContainer.appendChild(dayDiv);
                }

                monthDiv.appendChild(daysContainer);
                calendarContainer.appendChild(monthDiv);

                const workingDaysDiv = document.createElement('div');
                workingDaysDiv.innerHTML = `<p><strong>Working Days:</strong> ${workingDays}</p>`;
                calendarContainer.appendChild(workingDaysDiv);

                calculateCapacity(workingDays);
            }

            // Event Listener for Production Plan Change

            const productionPlan = document.getElementById('productionPlan');
            if (productionPlan) {
                productionPlan.addEventListener('change', function() {
                    const [year, month] = this.value.split('-').map(Number);
                    generateMonthlyCalendar(year, month - 1);

                    $.ajax({
                        url: "{{ route('capacity_plans.getAvgSMV') }}",
                        method: "GET",
                        data: {
                            production_plan: this.value
                        },
                        success: function(response) {
                            document.getElementById('smv').value = response.avg_smv || 0;
                            avgUnitPrice = response.avg_unit_price || 0;
                            calculateCapacity(workingDays, avgUnitPrice);
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to retrieve SMV. Please try again.'
                            });
                        }
                    });
                });
            } else {
                console.error("Element with ID 'productionPlan' not found.");
            }

            const inputs = ['running_machines', 'helpers', 'working_hours', 'efficiency', 'smv'];
            inputs.forEach(id => {
                const inputElement = document.getElementById(id);
                if (inputElement) {
                    inputElement.addEventListener('input', () => calculateCapacity(workingDays,
                        avgUnitPrice));
                } else {
                    console.error(`Element with ID '${id}' not found.`);
                }
            });

            // Dynamically Calculate Helpers Based on Running Machines
            document.getElementById('running_machines').addEventListener('input', function() {
                const machines = parseInt(this.value) || 0;
                const helpers = Math.ceil(machines * 0.2);
                document.getElementById('helpers').value = helpers;
                calculateCapacity(workingDays, avgUnitPrice);
            });

            // Capacity Calculation Function
            function calculateCapacity(workingDays, avgUnitPrice = 0) {
                const machines = parseInt(document.getElementById('running_machines').value) || 0;
                const helpers = parseInt(document.getElementById('helpers').value) || 0;
                const workingHours = parseInt(document.getElementById('working_hours').value) || 0;
                const efficiency = parseFloat(document.getElementById('efficiency').value) / 100 || 0;
                const avgSMV = parseFloat(document.getElementById('smv').value) || 0;

                const dailyCapacityMinutes = machines * helpers * workingHours * 60 * efficiency;
                const weeklyCapacityMinutes = dailyCapacityMinutes * 5;
                const monthlyCapacityMinutes = dailyCapacityMinutes * workingDays;
                const capacityQuantity = avgSMV > 0 ? monthlyCapacityMinutes / avgSMV : 0;
                const capacityValue = capacityQuantity * avgUnitPrice;

                document.getElementById('dailyCapacityMinutes').value = dailyCapacityMinutes.toFixed(2);
                document.getElementById('weeklyCapacityMinutes').value = weeklyCapacityMinutes.toFixed(2);
                document.getElementById('monthlyCapacityMinutes').value = monthlyCapacityMinutes.toFixed(2);
                document.getElementById('monthlyCapacityQuantity').value = capacityQuantity.toFixed(2);
                document.getElementById('monthlyCapacityValue').value = capacityValue.toFixed(2);
            }
        });
    </script> --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const calendarContainer = document.getElementById('factoryCalendar');
    const holidays = @json($factory_holidays);
    let workingDays = 0;
    let avgUnitPrice = 0;

    // Generate Monthly Calendar with Holidays and Working Days
    function generateMonthlyCalendar(year, month) {
        calendarContainer.innerHTML = '';

        // Calendar Structure
        const monthDiv = document.createElement("div");
        monthDiv.classList.add("month-calendar");

        const monthHeader = document.createElement("div");
        monthHeader.classList.add("month-header");
        monthHeader.textContent = new Date(year, month).toLocaleString('en-US', {
            month: 'long',
            year: 'numeric'
        });
        monthDiv.appendChild(monthHeader);

        const dayNamesContainer = document.createElement("div");
        dayNamesContainer.classList.add("day-names");
        const dayNames = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
        dayNames.forEach(day => {
            const dayNameDiv = document.createElement("div");
            dayNameDiv.classList.add("day-name");
            dayNameDiv.textContent = day;
            dayNamesContainer.appendChild(dayNameDiv);
        });
        monthDiv.appendChild(dayNamesContainer);

        const daysContainer = document.createElement("div");
        daysContainer.classList.add("month-days");

        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);

        workingDays = 0;
        let holidayCount = 0;

        for (let i = 0; i < firstDay.getDay(); i++) {
            const blankDay = document.createElement("div");
            blankDay.classList.add("calendar-day", "blank-day");
            daysContainer.appendChild(blankDay);
        }

        for (let date = 1; date <= lastDay.getDate(); date++) {
            const dayDiv = document.createElement("div");
            dayDiv.classList.add("calendar-day");

            const fullDate =
                `${year}-${String(month + 1).padStart(2, '0')}-${String(date).padStart(2, '0')}`;
            const holiday = holidays.find(h => h.holiday_date === fullDate);

            dayDiv.textContent = date;

            if (holiday) {
                dayDiv.classList.add("calendar-holiday");
                const descriptionDiv = document.createElement("div");
                descriptionDiv.classList.add("holiday-description");
                descriptionDiv.textContent = holiday.description;
                dayDiv.appendChild(descriptionDiv);
                holidayCount++;
            } else if (new Date(fullDate).getDay() !== 5) {
                workingDays++;
            }

            daysContainer.appendChild(dayDiv);
        }

        monthDiv.appendChild(daysContainer);
        calendarContainer.appendChild(monthDiv);

        const workingDaysDiv = document.createElement('div');
        workingDaysDiv.innerHTML = `<p><strong>Working Days:</strong> ${workingDays}</p>`;
        calendarContainer.appendChild(workingDaysDiv);

        calculateCapacity(workingDays);
    }

    // Initialize the Calendar on Page Load (Default or from selected production_plan)
    const productionPlanValue = document.getElementById('productionPlan').value;
    if (productionPlanValue) {
        const [year, month] = productionPlanValue.split('-').map(Number);
        generateMonthlyCalendar(year, month - 1); // Months are 0-based
    } else {
        const now = new Date();
        generateMonthlyCalendar(now.getFullYear(), now.getMonth()); // Current Month
    }

    // Event Listener for Production Plan Change
    const productionPlan = document.getElementById('productionPlan');
    if (productionPlan) {
        productionPlan.addEventListener('change', function() {
            const [year, month] = this.value.split('-').map(Number);
            generateMonthlyCalendar(year, month - 1);

            $.ajax({
                url: "{{ route('capacity_plans.getAvgSMV') }}",
                method: "GET",
                data: {
                    production_plan: this.value
                },
                success: function(response) {
                    document.getElementById('smv').value = response.avg_smv || 0;
                    avgUnitPrice = response.avg_unit_price || 0;
                    calculateCapacity(workingDays, avgUnitPrice);
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to retrieve SMV. Please try again.'
                    });
                }
            });
        });
    } else {
        console.error("Element with ID 'productionPlan' not found.");
    }

    const inputs = ['running_machines', 'helpers', 'working_hours', 'efficiency', 'smv'];
    inputs.forEach(id => {
        const inputElement = document.getElementById(id);
        if (inputElement) {
            inputElement.addEventListener('input', () => calculateCapacity(workingDays, avgUnitPrice));
        } else {
            console.error(`Element with ID '${id}' not found.`);
        }
    });

    // Dynamically Calculate Helpers Based on Running Machines
    document.getElementById('running_machines').addEventListener('input', function() {
        const machines = parseInt(this.value) || 0;
        const helpers = Math.ceil(machines * 0.2);
        document.getElementById('helpers').value = helpers;
        calculateCapacity(workingDays, avgUnitPrice);
    });

    // Capacity Calculation Function
    function calculateCapacity(workingDays, avgUnitPrice = 0) {
        const machines = parseInt(document.getElementById('running_machines').value) || 0;
        const helpers = parseInt(document.getElementById('helpers').value) || 0;
        const workingHours = parseInt(document.getElementById('working_hours').value) || 0;
        const efficiency = parseFloat(document.getElementById('efficiency').value) / 100 || 0;
        const avgSMV = parseFloat(document.getElementById('smv').value) || 0;

        const dailyCapacityMinutes = machines * helpers * workingHours * 60 * efficiency;
        const weeklyCapacityMinutes = dailyCapacityMinutes * 5;
        const monthlyCapacityMinutes = dailyCapacityMinutes * workingDays;
        const capacityQuantity = avgSMV > 0 ? monthlyCapacityMinutes / avgSMV : 0;
        const capacityValue = capacityQuantity * avgUnitPrice;

        document.getElementById('dailyCapacityMinutes').value = dailyCapacityMinutes.toFixed(2);
        document.getElementById('weeklyCapacityMinutes').value = weeklyCapacityMinutes.toFixed(2);
        document.getElementById('monthlyCapacityMinutes').value = monthlyCapacityMinutes.toFixed(2);
        document.getElementById('monthlyCapacityQuantity').value = capacityQuantity.toFixed(2);
        document.getElementById('monthlyCapacityValue').value = capacityValue.toFixed(2);
    }
});

    </script>
</x-backend.layouts.master>
