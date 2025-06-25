<x-backend.layouts.master>
    <div class="card mx-5 my-5" style="background-color: white;">
        <div class="container-fluid pt-2">
            <h4 class="text-center">Monthly Order Booking Summary</h4>
            <div class="row justify-content-center pb-2">
                <div class="col-12 text-center">
                    <button class="btn btn-outline-primary active" id="all-buyers-btn">All Buyers</button>
                    @foreach($allBuyers as $buyer)
                        <button class="btn btn-outline-primary buyer-btn" 
                                data-buyer="{{ $buyer }}">
                            {{ $buyer }}
                        </button>
                    @endforeach
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center" style="font-size: 12px;" id="summaryTable">
                    <thead class="thead-dark">
                        <tr>
                            <th></th>
                            <th>Buyer</th>
                            <th>Number of Orders</th>
                            <th>Order Qty</th>
                            <th>Sewing Balance</th>
                            <th>Avg. SMV</th>
                            <th>Produced Min</th>
                            <th>Production Balance</th>
                            <th>Booking%</th>
                            <th>Avg. Unit Price</th>
                            <th>Total Value</th>
                            <th>Avg. CM/Pcs</th>
                            <th>Total CM</th>
                            <th>Shipped Qty</th>
                            <th>Shipment Balance</th>
                            <th>Excess/Short Qty</th>
                            <th>Shipped Value</th>
                            <th>Value Balance</th>
                            <th>Excess/Short Value</th>
                        </tr>
                    </thead>
                    <tbody id="summaryTableBody">
                        @foreach($reportData as $monthData)
                            <tr class="month-header bg-light" data-month="{{ $monthData['month'] }}">
                                <td colspan="19" class="text-center font-weight-bold">
                                    {{ $monthData['month'] }}
                                </td>
                            </tr>
                            
                            @foreach($monthData['buyers'] as $buyerData)
                                <tr class="buyer-row" 
                                    data-month="{{ $monthData['month'] }}"
                                    data-buyer="{{ $buyerData['buyer'] }}">
                                    <td></td>
                                    <td>{{ $buyerData['buyer'] }}</td>
                                    <td>{{ $buyerData['number_of_orders'] }}</td>
                                    <td>{{ number_format($buyerData['order_qty']) }}</td>
                                    <td>{{ number_format($buyerData['sewing_balance']) }}</td>
                                    <td>{{ number_format($buyerData['avg_smv'], 2) }}</td>
                                    <td>{{ number_format($buyerData['produced_min']) }}</td>
                                    <td>{{ number_format($buyerData['production_balance']) }}</td>
                                    <td>{{ number_format($buyerData['booking_percentage'], 1) }}%</td>
                                    <td>${{ number_format($buyerData['avg_unit_price'], 2) }}</td>
                                    <td>${{ number_format($buyerData['total_value']) }}</td>
                                    <td>${{ number_format($buyerData['avg_cm_pcs'], 2) }}</td>
                                    <td>${{ number_format($buyerData['total_cm']) }}</td>
                                    <td>{{ number_format($buyerData['shipped_qty']) }}</td>
                                    <td>{{ number_format($buyerData['shipment_balance']) }}</td>
                                    <td>{{ number_format($buyerData['excess_short_qty']) }}</td>
                                    <td>${{ number_format($buyerData['shipped_value']) }}</td>
                                    <td>${{ number_format($buyerData['value_balance']) }}</td>
                                    <td>${{ number_format($buyerData['excess_short_value']) }}</td>
                                </tr>
                            @endforeach
                            
                            <tr class="month-total bg-info text-white" data-month="{{ $monthData['month'] }}">
                                <td>Total</td>
                                <td>{{ $monthData['month'] }}</td>
                                <td>{{ number_format($monthData['monthTotals']['number_of_orders']) }}</td>
                                <td>{{ number_format($monthData['monthTotals']['order_qty']) }}</td>
                                <td>{{ number_format($monthData['monthTotals']['sewing_balance']) }}</td>
                                <td>{{ number_format($monthData['monthTotals']['avg_smv'], 2) }}</td>
                                <td>{{ number_format($monthData['monthTotals']['produced_min']) }}</td>
                                <td>{{ number_format($monthData['monthTotals']['production_balance']) }}</td>
                                <td>{{ number_format($monthData['monthTotals']['booking_percentage'], 1) }}%</td>
                                <td>${{ number_format($monthData['monthTotals']['avg_unit_price'], 2) }}</td>
                                <td>${{ number_format($monthData['monthTotals']['total_value']) }}</td>
                                <td>${{ number_format($monthData['monthTotals']['avg_cm_pcs'], 2) }}</td>
                                <td>${{ number_format($monthData['monthTotals']['total_cm']) }}</td>
                                <td>{{ number_format($monthData['monthTotals']['shipped_qty']) }}</td>
                                <td>{{ number_format($monthData['monthTotals']['shipment_balance']) }}</td>
                                <td>{{ number_format($monthData['monthTotals']['excess_short_qty']) }}</td>
                                <td>${{ number_format($monthData['monthTotals']['shipped_value']) }}</td>
                                <td>${{ number_format($monthData['monthTotals']['value_balance']) }}</td>
                                <td>${{ number_format($monthData['monthTotals']['excess_short_value']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="font-weight-bold bg-dark text-white">
                        <tr>
                            <td colspan="2">Grand Total</td>
                            <td>{{ number_format($grandTotals['number_of_orders']) }}</td>
                            <td>{{ number_format($grandTotals['order_qty']) }}</td>
                            <td>{{ number_format($grandTotals['sewing_balance']) }}</td>
                            <td>{{ number_format($grandTotals['avg_smv'], 2) }}</td>
                            <td>{{ number_format($grandTotals['produced_min']) }}</td>
                            <td>{{ number_format($grandTotals['production_balance']) }}</td>
                            <td>{{ number_format($grandTotals['booking_percentage'], 1) }}%</td>
                            <td>${{ number_format($grandTotals['avg_unit_price'], 2) }}</td>
                            <td>${{ number_format($grandTotals['total_value']) }}</td>
                            <td>${{ number_format($grandTotals['avg_cm_pcs'], 2) }}</td>
                            <td>${{ number_format($grandTotals['total_cm']) }}</td>
                            <td>{{ number_format($grandTotals['shipped_qty']) }}</td>
                            <td>{{ number_format($grandTotals['shipment_balance']) }}</td>
                            <td>{{ number_format($grandTotals['excess_short_qty']) }}</td>
                            <td>${{ number_format($grandTotals['shipped_value']) }}</td>
                            <td>${{ number_format($grandTotals['value_balance']) }}</td>
                            <td>${{ number_format($grandTotals['excess_short_value']) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Filter buttons functionality
            $(".buyer-btn").click(function() {
                const buyer = $(this).data("buyer");
                
                // Hide all rows initially
                $(".month-header, .buyer-row, .month-total").hide();
                
                // Show buyer rows for selected buyer and their associated months
                $(`.buyer-row[data-buyer="${buyer}"]`).each(function() {
                    const month = $(this).data("month");
                    $(this).show();
                    $(`.month-header[data-month="${month}"]`).show();
                    $(`.month-total[data-month="${month}"]`).show();
                });
                
                // Update button states
                $(this).addClass('active').siblings().removeClass('active');
            });

            $("#all-buyers-btn").click(function() {
                $(".month-header, .buyer-row, .month-total").show();
                $(this).addClass('active').siblings().removeClass('active');
            });
        });
    </script>

    <style>
        .table thead th {
            position: sticky;
            top: 0;
            background-color: #343a40;
            color: white;
            z-index: 10;
        }
        .table tfoot td {
            position: sticky;
            bottom: 0;
            background-color: #343a40;
            color: white;
            z-index: 10;
        }
        .btn-outline-primary.active {
            background-color: #0a7df034;
            color: white;
        }
        .table-responsive {
            max-height: 70vh;
            overflow: auto;
        }
        .month-header {
            background-color: #e9ecef !important;
        }
        .month-total {
            background-color: #07ec07 !important;
            font-weight: bold;
        }
        tfoot tr {
            background-color: #343a40 !important;
            color: white;
        }
        .buyer-row:hover {
            background-color: #f8f9fa !important;
        }
    </style>
</x-backend.layouts.master>