<x-backend.layouts.master>
    <div class="card mx-5 my-5" style="background-color: white;">
        <div class="row p-1">
            <div class="col-12">
                <h3 class="text-center p-1">Shipment Balance History</h3>
                <div class="row p-1">
                    <div class="col-6 text-start">
                        <a href="{{ route('jobs.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Close
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
                    @if (session('message'))
                        <div class="alert alert-success">
                            <span class="close" data-dismiss="alert">&times;</span>
                            <strong>{{ session('message') }}.</strong>
                        </div>
                    @endif

                    <x-backend.layouts.elements.errors />

                    <div class="card-body">


                        <div class="table-responsive">
                            <table class="table table-bordered table-striped text-wrap text-center"
                                id="datatablesSimple">
                                <thead>
                                    <tr>
                                        <th>Job No</th>
                                        <th>Color</th>
                                        <th>Size</th>
                                        <th>Order Qty</th>
                                        <th>Total Shipped Qty</th>
                                        <th>Total Shipped Value</th>
                                        <th>Total Short Shipment Qty</th>
                                        <th>Total Short Shipment Value</th>
                                        <th>Total Excess Qty</th>
                                        <th>Total Excess Value</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($shipped_qty as $balance)
                                        @php
                                            // Fetch the order quantity and unit price
                                            $job = App\Models\Job::where('job_no', $balance->job_no)
                                                ->where('color', $balance->color)
                                                ->where('size', $balance->size)
                                                ->first();

                                            $order_qty = $job->color_quantity ?? 0;
                                            $unit_price = $job->unit_price ?? 0;

                                            // Calculate total_short_qty and total_short_value
                                            $total_short_qty = $order_qty - $balance->total_shipped_qty;
                                            $total_short_value = $total_short_qty * $unit_price;
                                        @endphp
                                        <tr>
                                            <td>{{ $balance->job_no }}</td>
                                            <td>{{ $balance->color }}</td>
                                            <td>{{ $balance->size }}</td>
                                            <td class="order_qty">{{ $order_qty }}</td>
                                            <td class="total_shipped_qty">{{ $balance->total_shipped_qty }}</td>
                                            <td class="total_shipped_value">{{ $balance->total_shipped_value }}</td>
                                            <td class="total_short_qty">{{ $total_short_qty }}</td>
                                            <td class="total_short_value">{{ $total_short_value }}</td>
                                            <td class="total_excess_short_shipment_qty">
                                                {{ $balance->total_excess_short_shipment_qty }}</td>
                                            <td class="total_excess_short_shipment_value">
                                                {{ $balance->total_excess_short_shipment_value }}</td>
                                            <td>
                                                <a href="{{ route('sewing_balances.show', $balance->job_no) }}"
                                                    class="btn btn-outline-info">
                                                    <i class="fas fa-eye"></i> Show
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="11" class="text-center">No Sewing Balance Found</td>
                                        </tr>
                                    @endforelse

                                </tbody>
                            </table>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#datatablesSimple').DataTable();
        });
    </script>
</x-backend.layouts.master>
