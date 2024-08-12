<x-backend.layouts.master>
    <div class="card mx-5 my-5" style="background-color: white;">
        <div class="row p-1">
            <div class="col-12">
                <h3 class="text-center p-1">Shipment Balance History</h3>
                <div class="row p-1">
                    <div class="col-6 text-start">
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Close
                        </a>
                        <a href="{{ route('archives') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-archive"></i> Job Archives
                        </a>
                    </div>
                    <div class="col-6 text-end">
                        <a href="{{ route('tnas_dashboard') }}" class="btn btn-outline-success">
                            <i class="fas fa-tachometer-alt"></i> Job Dashboard
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
                                            <th>Total Excess/Short Shipment Qty</th>
                                            <th>Total Excess/Short Shipment Value</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($shipped_qty as $balance)
                                            <tr>
                                                <td>{{ $balance->job_no }}</td>
                                                <td>{{ $balance->color }}</td>
                                                <td>{{ $balance->size }}</td>
                                                @php
                                                    $order_qty = App\Models\Job::where('job_no', $balance->job_no)
                                                        ->where('color', $balance->color)
                                                        ->where('size', $balance->size)
                                                        ->first()->color_quantity;
                                                @endphp
                                                <td class="order_qty">{{ $order_qty }}</td>
                                                <td class="total_shipped_qty">{{ $balance->total_shipped_qty }}</td>
                                                <td class="total_shipped_value">{{ $balance->total_shipped_value }}</td>
                                                <td class="total_excess_short_shipment_qty">
                                                    {{ $balance->total_excess_short_shipment_qty }}</td>
                                                <td class="total_excess_short_shipment_value">
                                                    {{ $balance->total_excess_short_shipment_value }}</td>
                                                <td>
                                                    <a href="{{ route('sewing_balances.show', $balance->job_no) }}"
                                                        class="btn btn-outline-info">
                                                        <i class="fas fa-eye"></i> show
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center">No Sewing Balance Found</td>
                                            </tr>
                                        @endforelse
                                        {{-- <tr>
                                            <th colspan="3" class="text-center">Total</th>
                                            <th class="text-center" id="total_order_qty"></th>
                                            <th class="text-center" id="total_shipped_qty"></th>
                                            <th class="text-center" id="total_shipped_value"></th>
                                            <th class="text-center" id="total_excess_short_shipment_qty"></th>
                                            <th class="text-center" id="total_excess_short_shipment_value"></th>
                                        </tr> --}}
                                    </tbody>
                                </table>
                            </div>

                            <script>
                                $(document).ready(function() {
                                    var total_order_qty = 0;
                                    var total_shipped_qty = 0;
                                    var total_shipped_value = 0;
                                    var total_excess_short_shipment_qty = 0;
                                    var total_excess_short_shipment_value = 0;

                                    $('#datatablesSimple tbody tr').each(function() {
                                        // Check if the row is not the header or the total row
                                        if ($(this).find('.order_qty').length) {
                                            total_order_qty += parseInt($(this).find('.order_qty').text()) || 0;
                                            total_shipped_qty += parseInt($(this).find('.total_shipped_qty').text()) || 0;
                                            total_shipped_value += parseInt($(this).find('.total_shipped_value').text()) || 0;
                                            total_excess_short_shipment_qty += parseInt($(this).find(
                                                '.total_excess_short_shipment_qty').text()) || 0;
                                            total_excess_short_shipment_value += parseInt($(this).find(
                                                '.total_excess_short_shipment_value').text()) || 0;
                                        }
                                    });

                                    $('#total_order_qty').text(total_order_qty);
                                    $('#total_shipped_qty').text(total_shipped_qty);
                                    $('#total_shipped_value').text(total_shipped_value);
                                    $('#total_excess_short_shipment_qty').text(total_excess_short_shipment_qty);
                                    $('#total_excess_short_shipment_value').text(total_excess_short_shipment_value);
                                });
                            </script>
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
