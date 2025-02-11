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

    <!-- SweetAlert Message -->
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
        <h1 class="text-center">Update Capacity Plan</h1>

        <form action="{{ route('capacity_plans.store') }}" method="POST">
            @csrf
            <div class="row p-1">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body" style="overflow-x:auto;">
                            <!-- Hidden Fields -->
                            <input type="hidden" name="created_by" value="{{ auth()->user()->id }}">
                            <input type="hidden" name="division_id" value="2">
                            <input type="hidden" name="division_name" value="Factory">
                            <input type="hidden" name="company_id" value="3">
                            <input type="hidden" name="company_name" value="FAL - Factory">

                            <!-- Production Plan Input -->
                            <table class="table">
                                <tbody>
                                    
                                    <tr>
                                        <td class="create_label_column">Production Plan</td>
                                        <td class="create_input_column">
                                            <input type="month" name="production_plan" id="productionPlan"
                                                class="form-control" placeholder="Production Plan" required>
                                        </td>
                                        

                                        <td class="create_label_column">Number of running machines</td>
                                        <td class="create_input_column">
                                            <input type="number" name="running_machines" id="running_machines"
                                                class="form-control" placeholder="Number of running machines" required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="create_label_column">Number of helpers</td>
                                        <td class="create_input_column">
                                            <input type="number" name="helpers" id="helpers" class="form-control"
                                                placeholder="helpers" required>
                                        </td>
                                        <td class="create_label_column">Working hours</td>
                                        <td class="create_input_column">
                                            <input type="number" name="working_hours" id="working_hours"
                                                class="form-control" placeholder="Working hours" required>
                                        </td>
                                    </tr>
                                    <tr>

                                        <td class="create_label_column">Expected Efficiency%</td>
                                        <td class="create_input_column">
                                            <input type="number" name="efficiency" id="efficiency" class="form-control"
                                                placeholder="efficiency" required>
                                        </td>
                                        <td class="create_label_column">Avg SMV</td>
                                        <td class="create_input_column">
                                            <input type="number" name="smv" id="smv" class="form-control"
                                                placeholder="SMV" required step="0.01">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- Factory Calendar Display -->
                            <div id="factoryCalendar"></div>
                            <input type="hidden" name="workingDays" id="workingDays">
                            <!--show all capacity output here in resposive table-->
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered table-striped">
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
                                            <td><input type="text" class="form-control" name="daily_capacity_minutes"
                                                    id="dailyCapacityMinutes" readonly></td>
                                            </td>
                                            <td><input type="text" name="weekly_capacity_minutes"
                                                    class="form-control" id="weeklyCapacityMinutes" readonly></td>
                                            <td><input type="text" name="monthly_capacity_minutes"
                                                    class="form-control" id="monthlyCapacityMinutes" readonly></td>
                                            <td><input type="text" name="monthly_capacity_quantity"
                                                    class="form-control" id="monthlyCapacityQuantity" readonly></td>
                                            <td><input type="text" name="monthly_capacity_value" class="form-control"
                                                    id="monthlyCapacityValue" readonly></td>
                                        </tr>
                                    </tbody>
                                </table>
                                @php
                                    $factory_holidays = \App\Models\FactoryHoliday::all();
                                @endphp
                                <div class="button-container">
                                    <a href="{{ route('capacity_plans.index') }}" class="btn btn-outline-secondary">
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


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            const holidays = @json($factory_holidays);
            const factoryCalendar = document.getElementById('factoryCalendar');
            let workingDays = 0; // Define workingDays in the outer scope
            let avgUnitPrice = 0; // Define avgUnitPrice in the outer scope

            document.getElementById('productionPlan').addEventListener('change', function() {
                const selectedMonth = this.value;
                showFactoryCalendar(selectedMonth);

                $.ajax({
                    url: "{{ route('capacity_plans.getAvgSMV') }}",
                    method: "GET",
                    data: {
                        production_plan: selectedMonth
                    },
                    success: function(response) {
                        console.log(response);
                        document.getElementById('smv').value = response.avg_smv || 0;
                        avgUnitPrice = response.avg_unit_price ||
                        0; // Store avg_unit_price globally
                        calculateCapacity(
                        workingDays); // Calculate capacity with updated avgUnitPrice
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

            function showFactoryCalendar(month) {
                const [year, monthIndex] = month.split('-').map(Number);
                const daysInMonth = new Date(year, monthIndex, 0).getDate();
                workingDays = 0; // Reset workingDays each time this function runs

                factoryCalendar.innerHTML = '<h4>Factory Calendar for Selected Month</h4>';
                let holidaysThisMonth = [];

                const calendarDiv = document.createElement('div');
                calendarDiv.classList.add('calendar-grid');

                for (let day = 1; day <= daysInMonth; day++) {
                    const fullDate =
                        `${year}-${String(monthIndex).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                    const holiday = holidays.find(h => h.holiday_date === fullDate);

                    const dayDiv = document.createElement('div');
                    dayDiv.classList.add('calendar-day');
                    dayDiv.textContent = day;

                    if (holiday) {
                        dayDiv.classList.add('calendar-holiday');
                        dayDiv.title = holiday.description;
                        holidaysThisMonth.push(holiday);
                    } else if (new Date(fullDate).getDay() !== 5) { // Skip Fridays as default holidays
                        workingDays++;
                    }

                    calendarDiv.appendChild(dayDiv);
                }

                factoryCalendar.appendChild(calendarDiv);

                const workingDaysDiv = document.createElement('div');
                workingDaysDiv.innerHTML = `<p><strong>Working Days:</strong> ${workingDays}</p>`;
                factoryCalendar.appendChild(workingDaysDiv);

                calculateCapacity(workingDays); // Pass workingDays to calculateCapacity

                if (holidaysThisMonth.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Holiday Adjustment Needed',
                        text: 'No holidays this month. Please adjust the production plan.',
                        confirmButtonText: 'View Holidays',
                        preConfirm: () => {
                            window.location.href = "{{ route('factory_holidays.index') }}";
                        }
                    });
                }
            }

            const inputs = ['running_machines', 'helpers', 'working_hours', 'efficiency', 'smv'];
            inputs.forEach(id => {
                document.getElementById(id).addEventListener('input', () => calculateCapacity(workingDays));
            });

            function calculateCapacity(workingDays) {
                const machines = parseInt(document.getElementById('running_machines').value) || 0;
                const helpers = parseInt(document.getElementById('helpers').value) || 0;
                const workingHours = parseInt(document.getElementById('working_hours').value) || 0;
                const efficiency = parseFloat(document.getElementById('efficiency').value) / 100 || 0;
                const avgSMV = parseFloat(document.getElementById('smv').value) || 0;

                const dailyCapacityMinutes = machines * helpers * workingHours * 60 * efficiency;
                const weeklyCapacityMinutes = dailyCapacityMinutes * 5;
                const monthlyCapacityMinutes = dailyCapacityMinutes * workingDays;

                const capacityQuantity = avgSMV > 0 ? monthlyCapacityMinutes / avgSMV : 0;
                const capacityValue = capacityQuantity * avgUnitPrice; // Use the global avgUnitPrice

                document.getElementById('capacityOutput').innerHTML = `
                <p><strong>Daily Capacity Minutes:</strong> ${dailyCapacityMinutes.toFixed(2)}</p>
                <p><strong>Weekly Capacity Minutes:</strong> ${weeklyCapacityMinutes.toFixed(2)}</p>
                <p><strong>Monthly Capacity Minutes:</strong> ${monthlyCapacityMinutes.toFixed(2)}</p>
                <p><strong>Monthly Capacity Quantity:</strong> ${capacityQuantity.toFixed(2)}</p>
                <p><strong>Monthly Capacity Value:</strong> ${capacityValue.toFixed(2)}</p>
            `;
            }
        });
    </script> --}}

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarContainer = document.getElementById('factoryCalendar');
            const holidays = @json($factory_holidays);
            let workingDays = 0;
            let avgUnitPrice = 0;

            // Generate calendar with day names and holiday highlights
            function generateMonthlyCalendar(year, month) {
                calendarContainer.innerHTML = '';

                const monthDiv = document.createElement("div");
                monthDiv.classList.add("month-calendar");

                const monthHeader = document.createElement("div");
                monthHeader.classList.add("month-header");
                monthHeader.textContent = new Date(year, month).toLocaleString('en-US', {
                    month: 'long',
                    year: 'numeric'
                });
                monthDiv.appendChild(monthHeader);

                // Create day name headers (Sun, Mon, Tue, ...)
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

                if (holidayCount === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Holiday Adjustment Needed',
                        text: 'No holidays this month. Please adjust the production plan.',
                        confirmButtonText: 'View Holidays',
                        preConfirm: () => {
                            window.location.href = "{{ route('factory_holidays.index') }}";
                        }
                    });
                }

                monthDiv.appendChild(daysContainer);
                calendarContainer.appendChild(monthDiv);

                const workingDaysDiv = document.createElement('div');
                workingDaysDiv.innerHTML = `<p><strong>Working Days:</strong> ${workingDays}</p>`;
                calendarContainer.appendChild(workingDaysDiv);

                calculateCapacity(workingDays);
            }

            document.getElementById('productionPlan').addEventListener('change', function() {
                const [year, month] = this.value.split('-').map(Number);
                generateMonthlyCalendar(year, month - 1);
                var production_plan_selected = this.value;

                $.ajax({
                    url: "{{ route('capacity_plans.getAvgSMV') }}",
                    method: "GET",
                    data: {
                        production_plan: this.value
                    },
                    success: function(response) {
                        console.log(response);
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

                // AJAX request to check if a capacity plan already exists for the selected month and year
                $.ajax({
                    url: "{{ route('check_existing_plan') }}",
                    method: "GET",
                    data: {
                        production_plan: production_plan_selected
                    },
                    success: function(response) {
                        if (response.exists) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Capacity Plan Exists',
                                text: 'A capacity plan already exists for the selected month. Please edit the existing plan.',
                                confirmButtonText: 'View Existing Plan',
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

            const inputs = ['running_machines', 'helpers', 'working_hours', 'efficiency', 'smv'];
            inputs.forEach(id => {
                document.getElementById(id).addEventListener('input', () => calculateCapacity(workingDays,
                    avgUnitPrice));
            });

            //helpers value calculation based on running machines dynamically
            document.getElementById('running_machines').addEventListener('input', function() {
                const machines = parseInt(this.value) || 0;
                const helpers = Math.ceil(machines * 0.2) || 0; // Calculate helpers as 2% of machines
                document.getElementById('helpers').value = helpers; // Update helpers input field
                calculateCapacity(workingDays,
                    avgUnitPrice); // Recalculate capacity based on updated helpers
            });

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

                document.getElementById('workingDays').value = workingDays;
                document.getElementById('dailyCapacityMinutes').value = dailyCapacityMinutes.toFixed(2);
                document.getElementById('weeklyCapacityMinutes').value = weeklyCapacityMinutes.toFixed(2);
                document.getElementById('monthlyCapacityMinutes').value = monthlyCapacityMinutes.toFixed(2);
                document.getElementById('monthlyCapacityQuantity').value = capacityQuantity;
                document.getElementById('monthlyCapacityValue').value = capacityValue.toFixed(2);
            }
        });
    </script>



</x-backend.layouts.master>
