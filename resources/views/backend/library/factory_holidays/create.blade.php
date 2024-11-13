<x-backend.layouts.master>
    <style>
        .calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
        }

        .calendar-header {
            font-weight: bold;
            background-color: #f1f1f1;
            text-align: center;
        }

        .calendar-day {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
            position: relative;
        }

        .calendar-weekend {
            background-color: #ffe6e6;
        }

        .calendar-holiday {
            background-color: #ff4d4d;
            color: white;
        }

        .holiday-description {
            font-size: 0.8em;
            color: #555;
            margin-top: 5px;
        }

        .day-name {
            font-weight: bold;
        }
    </style>
    <x-slot name="pageTitle">Create Holiday</x-slot>
    <x-slot name="breadCrumb">
        <x-backend.layouts.elements.breadcrumb>
            <x-slot name="pageHeader">Holiday Entry</x-slot>
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('factory_holidays.index') }}">Holiday</a></li>
            <li class="breadcrumb-item active">Create Holiday</li>
        </x-backend.layouts.elements.breadcrumb>
    </x-slot>

    <x-backend.layouts.elements.errors />

  

    <form action="{{ route('factory_holidays.store') }}" method="post" enctype="multipart/form-data" id="holidayForm">
        @csrf
        <div class="row justify-content-between">
            <div class="col-md-6 form-group">
                <label for="production_month">Select Production Month</label>
                <input type="month" id="production_month" name="production_month" class="form-control"
                    placeholder="Select a Month" required />
            </div>
            <div class="col-md-6 form-group">
                <label for="weekend">Select Weekend</label>
                <select id="weekend" name="weekend" class="form-control" required>
                    <option value="" disabled selected>Select a weekend</option>
                    <option value="0">Sunday</option>
                    <option value="1">Monday</option>
                    <option value="2">Tuesday</option>
                    <option value="3">Wednesday</option>
                    <option value="4">Thursday</option>
                    <option value="5">Friday</option>
                    <option value="6">Saturday</option>
                </select>
            </div>
        </div>

        <div>
            <div id="calendarContainer" class="calendar"></div> 
            <button type="submit" class="btn btn-primary" id="saveBtn">Save</button>
            <button type="reset" class="btn btn-secondary">Reset</button>
            <a href="{{ route('factory_holidays.index') }}" class="btn btn-danger">Cancel</a>
        </div>
    </form>

    <!-- Holiday Detail Modal -->
    <div class="modal fade" id="holidayModal" tabindex="-1" aria-labelledby="holidayModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="holidayModalLabel">Add Holiday Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="selected_date" name="selected_date">
                    <div class="mb-3">
                        <label for="holiday_description" class="form-label">Description</label>
                        <input type="text" id="holiday_description" name="holiday_description" class="form-control"
                            placeholder="Enter description">
                    </div>
                    <div class="form-check">
                        <input type="checkbox" id="is_weekend" class="form-check-input">
                        <label for="is_weekend" class="form-check-label">Set as Weekend</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="addHolidayDetail">Add</button>
                </div>
            </div>
        </div>
    </div>
 
    <!-- JavaScript to Handle Calendar and Holiday Details -->
    <script> 
        document.addEventListener('DOMContentLoaded', function() {
                    const productionMonth = document.getElementById('production_month');
                    const calendarContainer = document.getElementById('calendarContainer');
                    let holidayData = []; // Array to hold holiday data dynamically
                    let weekendDay = null;

                    function generateCalendar(year, month) {
                        calendarContainer.innerHTML = "";
                        const firstDay = new Date(year, month, 1);
                        const lastDay = new Date(year, month + 1, 0);

                        // Fill blank days before the first day of the month
                        for (let i = 0; i < firstDay.getDay(); i++) {
                            calendarContainer.appendChild(document.createElement("div"));
                        }

                        // Generate days of the month
                        for (let date = 1; date <= lastDay.getDate(); date++) {
                            const currentDate = new Date(year, month, date);
                            const fullDate =
                                `${year}-${String(month + 1).padStart(2, '0')}-${String(date).padStart(2, '0')}`;

                            const dayElement = document.createElement("div");
                            dayElement.classList.add("calendar-day");

                            const dayNameDiv = document.createElement("div");
                            dayNameDiv.classList.add("day-name");
                            dayNameDiv.textContent = currentDate.toLocaleDateString('en-US', {
                                weekday: 'short'
                            });

                            dayElement.append(dayNameDiv, document.createTextNode(date));

                            // Highlight weekends
                            if (weekendDay !== null && weekendDay === currentDate.getDay()) {
                                dayElement.classList.add("calendar-weekend");

                                // push the weekendDay data to the holidayData array, first check if the weekendDay is already in the array before pushing
                                if (!holidayData.find(h => h.holiday_date === fullDate)) {
                                    holidayData.push({
                                        holiday_date: fullDate,
                                        description: 'Weekend',
                                        is_weekend: true
                                    });
                                }
                            }

                                // Highlight holidays
                                const holiday = holidayData.find(h => h.holiday_date === fullDate);
                                if (holiday) {
                                    dayElement.classList.add("calendar-holiday");
                                    const descriptionDiv = document.createElement("div");
                                    descriptionDiv.classList.add("holiday-description");
                                    descriptionDiv.textContent = holiday.description;
                                    dayElement.appendChild(descriptionDiv);
                                }

                                dayElement.addEventListener("click", function() {
                                    $('#selected_date').val(fullDate);
                                    $('#holidayModal').modal('show');
                                });

                                calendarContainer.appendChild(dayElement);
                            }
                        }

                        // Update calendar on month change
                        productionMonth.addEventListener('change', function() {
                            const [year, month] = productionMonth.value.split("-").map(Number);
                            generateCalendar(year, month - 1);
                        });

                        // Update calendar on weekend selection
                        $('#weekend').on('change', function() {
                            weekendDay = parseInt($(this).val());
                            const [year, month] = productionMonth.value.split("-").map(Number);
                            generateCalendar(year, month - 1);
                        });

                        // Add holiday details to the array and refresh calendar display
                        $('#addHolidayDetail').on('click', function() {
                            const selectedDate = $('#selected_date').val();
                            const description = $('#holiday_description').val();
                            const isWeekend = $('#is_weekend').is(':checked');

                            holidayData.push({
                                holiday_date: selectedDate,
                                description,
                                is_weekend: isWeekend
                            });
                            generateCalendar(new Date(selectedDate).getFullYear(), new Date(selectedDate)
                            .getMonth());
                            $('#holidayModal').modal('hide');
                        });

                        console.log(holidayData);

                        // Convert holidayData to JSON and add to the form on save
                        $('#saveBtn').on('click', function(e) {
                            e.preventDefault();
                            $('<input>').attr({
                                type: 'hidden',
                                name: 'holiday_details',
                                value: JSON.stringify(holidayData)
                            }).appendTo('#holidayForm');
                            $('#holidayForm').submit();
                        });
                    });
    </script>
</x-backend.layouts.master>
