<x-backend.layouts.master>
    <div class="card mx-5 my-5" style="background-color: white;">
        <div class="row p-1">
            <div class="col-12">
                <h3 class="text-center p-1">Monthly Sewing Plan Summary</h3>
                <div class="row p-1">
                    <div class="col-6 text-start">
                        <a href="{{ route('sewing_plans.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Details
                        </a>
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
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped text-nowrap">
                                <thead>
                                    <tr>
                                        <th>Month</th>
                                        <th>Total Order Qty</th>
                                        <th>Total Plan Qty</th>
                                        <th>Total Sewing Qty</th>
                                        <th>Total Capacity</th>
                                        <th>Booking %</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($summary as $item)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($item['month'])->format('M Y') }}</td>
                                            <td>{{ number_format($item['total_order_qty']) }}</td>
                                            <td>{{ number_format($item['total_plan_qty']) }}</td>
                                            <td>{{ number_format($item['total_sewing_qty']) }}</td>
                                            <td>{{ number_format($item['total_capacity']) }}</td>
                                            <td>{{ number_format($item['booking_percent'], 2) }}%</td>
                                            <td>
                                                <a href="{{ route('sewing_plans.index', ['production_plan' => $item['month']]) }}"
                                                    class="btn btn-outline-info">
                                                    <i class="fas fa-eye"></i> View Details
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-backend.layouts.master>