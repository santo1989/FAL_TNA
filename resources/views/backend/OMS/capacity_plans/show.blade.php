<x-backend.layouts.master>
    <div class="card mx-5 my-5" style="background-color: white;">
        <div class="row p-1">
            <div class="col-12">
                <h3 class="text-center p-1">Capacity Plan Details</h3>
                <div class="row p-1">
                    <div class="col-6 text-start">
                        <a href="{{ route('jobs.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Close
                        </a>
                        <a href="{{ route('capacity_plans.create') }}" class="btn btn-outline-primary">
                            <i class="fas fa-plus"></i> Add Capacity Plan </a>

                    </div>
                    <div class="col-6 text-end">
                        <a href="{{ route('home') }}" class="btn btn-outline-success">
                            <i class="fas fa-tachometer-alt"></i> Home
                        </a>


                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card p-1">
                    @if (session('message'))
                        <div class="alert alert-success">
                            <span class="close" data-dismiss="alert">&times;</span>
                            <strong>{{ session('message') }}.</strong>
                        </div>
                    @endif

                    <x-backend.layouts.elements.errors />

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped text-wrap text-center">
                                <thead>
                                    <tr>
                                        
                                        <th rowspan="2">Month</th>
                                        <th rowspan="2">From</th>
                                        <th rowspan="2">To</th>
                                        <th rowspan="2">Number of Working Days</th>
                                        <th rowspan="2">Number of running machines</th>
                                        <th rowspan="2">Number of helpers</th>
                                        <th rowspan="2">working hours</th>
                                        <th rowspan="2">Expected Efficiency%</th>
                                        <th colspan="3">Capacity Min</th>
                                        <th colspan="3">Capacity Pcs</th>
                                        <th colspan="3">Capacity Value</th> 
                                    </tr>
                                    <tr>
                                        <th>Daily</th>
                                        <th>Weekly</th>
                                        <th>Monthly</th>
                                        <th>Daily</th>
                                        <th>Weekly</th>
                                        <th>Monthly</th>
                                        <th>Daily</th>
                                        <th>Weekly</th>
                                        <th>Monthly</th>
                                </thead>
                                <tbody>

                                     
                                        <tr>
                                            
                                            <td>{{ Carbon\Carbon::parse($capacity_plan->production_plan)->format('F-y') }}
                                            </td>
                                            @php
                                                // from the month name get 1st date of the month and last date of the month
                                                $from_date = Carbon\Carbon::parse(
                                                    $capacity_plan->production_plan,
                                                )->startOfMonth();
                                                $to_date = Carbon\Carbon::parse(
                                                    $capacity_plan->production_plan,
                                                )->endOfMonth();
                                            @endphp
                                            <td>{{ $from_date->format('d-M-Y') }}</td>
                                            <td>{{ $to_date->format('d-M-Y') }}</td>
                                            <td>{{ $capacity_plan->workingDays }}</td>
                                            <td>{{ $capacity_plan->running_machines }}</td>
                                            <td>{{ $capacity_plan->helpers }}</td>
                                            <td>{{ $capacity_plan->working_hours }}</td>
                                            <td>{{ $capacity_plan->efficiency }}</td>
                                            <td>{{ $capacity_plan->daily_capacity_minutes }}</td>
                                            <td>{{ $capacity_plan->weekly_capacity_minutes }}</td>
                                            <td>{{ $capacity_plan->monthly_capacity_minutes }}</td>
                                            @php
                                                // calculate the capacity pcs
                                                $capacity_pcs_weekly = number_format(
                                                    ($capacity_plan->monthly_capacity_quantity /
                                                        $capacity_plan->workingDays) *
                                                        7,
                                                    0,
                                                );
                                                $capacity_pcs_daily = number_format(
                                                    $capacity_plan->monthly_capacity_quantity /
                                                        $capacity_plan->workingDays,
                                                    0,
                                                );

                                            @endphp
                                            <td>{{ $capacity_pcs_daily }}</td>
                                            <td>{{ $capacity_pcs_weekly }}</td>
                                            <td>{{ $capacity_plan->monthly_capacity_quantity }}</td>
                                            @php
                                                // calculate the capacity value
                                                $capacity_value_daily = number_format(
                                                    $capacity_plan->monthly_capacity_value /
                                                        $capacity_plan->workingDays,
                                                    0,
                                                );
                                                $capacity_value_weekly = number_format(
                                                    ($capacity_plan->monthly_capacity_value /
                                                        $capacity_plan->workingDays) *
                                                        7,
                                                    0,
                                                );
                                            @endphp
                                            <td>{{ $capacity_value_daily }}</td>
                                            <td>{{ $capacity_value_weekly }}</td>
                                            <td>{{ $capacity_plan->monthly_capacity_value }}</td>  
                                        </tr> 
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

 
</x-backend.layouts.master>
