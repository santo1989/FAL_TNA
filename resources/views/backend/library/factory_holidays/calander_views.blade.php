<x-backend.layouts.master>
    <style>
        .calendar-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 20px;
        }

        .month-calendar {
            display: grid;
            grid-template-rows: auto 1fr;
            border: 1px solid #555454;
            border-radius: 8px;
            overflow: hidden;
        }

        .month-header {
            background-color: #59a0fc;
            text-align: center;
            font-weight: bold;
            padding: 8px;
        }

        .month-days {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
            padding: 10px;
        }

        .calendar-day {
            padding: 8px;
            border: 1px solid #0c0707;
            text-align: center;
            position: relative;
            font-size: 0.9em;
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

    <x-slot name="pageTitle">Show Yearly Calendar</x-slot>
    <x-slot name="breadCrumb">
        <x-backend.layouts.elements.breadcrumb>
            <x-slot name="pageHeader">Holiday Calendar</x-slot>
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Yearly Calendar</li>
        </x-backend.layouts.elements.breadcrumb>
    </x-slot>

    <x-backend.layouts.elements.errors />
<div class="row">
    <div class="col-6"></div>
    <div class="col-6">
        <a href="{{ route('home') }}" class="btn btn-lg btn-outline-danger"><i class="fas fa-arrow-left"></i> Close</a>
        <a href="{{ route('factory_holidays.index') }}" class="btn btn-lg btn-outline-primary"><i class="fas fa-list"></i> List View</a>
    </div>
</div>
    <div id="calendarContainer" class="calendar-container"></div>

    <!-- JavaScript to Generate Calendar -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const calendarContainer = document.getElementById('calendarContainer');
            const holidayData = @json($factory_holidays); // Load holiday data from the controller
            const currentYear = new Date().getFullYear();

            function generateMonthlyCalendar(year, month) {
                const monthDiv = document.createElement("div");
                monthDiv.classList.add("month-calendar");

                const monthHeader = document.createElement("div");
                monthHeader.classList.add("month-header");
                monthHeader.textContent = new Date(year, month).toLocaleString('en-US', { month: 'long', year: 'numeric' });
                monthDiv.appendChild(monthHeader);

                const daysContainer = document.createElement("div");
                daysContainer.classList.add("month-days");

                const firstDay = new Date(year, month, 1);
                const lastDay = new Date(year, month + 1, 0);

                // Blank days before the first day of the month
                for (let i = 0; i < firstDay.getDay(); i++) {
                    daysContainer.appendChild(document.createElement("div"));
                }

                // Generate each day of the month
                for (let date = 1; date <= lastDay.getDate(); date++) {
                    const dayDiv = document.createElement("div");
                    dayDiv.classList.add("calendar-day");

                    const fullDate = `${year}-${String(month + 1).padStart(2, '0')}-${String(date).padStart(2, '0')}`;
                    const holiday = holidayData.find(h => h.holiday_date === fullDate);

                    dayDiv.textContent = date;

                    // Highlight holiday and add description
                    if (holiday) {
                        dayDiv.classList.add("calendar-holiday");
                        const descriptionDiv = document.createElement("div");
                        descriptionDiv.classList.add("holiday-description");
                        descriptionDiv.textContent = holiday.description;
                        dayDiv.appendChild(descriptionDiv);
                    }

                    daysContainer.appendChild(dayDiv);
                }

                monthDiv.appendChild(daysContainer);
                calendarContainer.appendChild(monthDiv);
            }

            function generateYearlyCalendar(year) {
                calendarContainer.innerHTML = "";

                // Generate a mini-calendar for each month
                for (let month = 0; month < 12; month++) {
                    generateMonthlyCalendar(year, month);
                }
            }

            // Generate calendar for the current year
            generateYearlyCalendar(currentYear);
        });
    </script>
   
</x-backend.layouts.master>
