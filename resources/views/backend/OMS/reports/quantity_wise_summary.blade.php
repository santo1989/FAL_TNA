<x-backend.layouts.master>
    <div class="card mx-5 my-5" style="background-color: white; overflow-x: auto;">
         
    <div class="container-fluid pt-2">
    <h4 class="text-center">Quantity-Wise Summary</h4>
    <a href="{{ route('jobs.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Close
    </a>

    {{-- <table class="table table-bordered table-hover text-center text-wrap" style="font-size: 12px;">
        <thead class="thead-dark">
            <tr>
                <th rowspan="3">Order Quantity Range (pcs)</th>
                @foreach ($buyers as $buyer)
                    <th colspan="8">{{ $buyer }}</th>
                @endforeach
                <th colspan="8">Total</th>
            </tr>
            <tr>
                @foreach ($buyers as $buyer)
                    <th colspan="2">Number of Orders</th>
                    <th colspan="2">Order Quantity</th>
                    <th colspan="2">Production Min</th>
                    <th colspan="2">Value</th>
                @endforeach
                <th colspan="2">Number of Orders</th>
                <th colspan="2">Order Quantity</th>
                <th colspan="2">Production Min</th>
                <th colspan="2">Value</th>
            </tr>
            <tr>
                @foreach ($buyers as $buyer)
                    <th>Orders</th>
                    <th>%</th>
                    <th>Quantity</th>
                    <th>%</th>
                    <th>Min</th>
                    <th>%</th>
                    <th>Value</th>
                    <th>%</th>
                @endforeach
                <th>Orders</th>
                <th>%</th>
                <th>Quantity</th>
                <th>%</th>
                <th>Min</th>
                <th>%</th>
                <th>Value</th>
                <th>%</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($quantityRanges as $rangeName => $range)
                <tr>
                    <td>{{ $rangeName }}</td>
                    @foreach ($buyers as $buyer)
                        @php
                            $data = $summary[$rangeName][$buyer];
                        @endphp
                        <td>{{ $data['number_of_orders'] }}</td>
                        <td>{{ number_format($data['percentage_orders'], 1) }}%</td>
                        <td>{{ $data['total_quantity'] }}</td>
                        <td>{{ number_format($data['percentage_quantity'], 1) }}%</td>
                        <td>{{ $data['produced_min'] }}</td>
                        <td>{{ number_format($data['percentage_produced_min'], 1) }}%</td>
                        <td>{{ number_format($data['total_value'], 2) }}</td>
                        <td>{{ number_format($data['percentage_value'], 1) }}%</td>
                    @endforeach
                    <td>{{ array_sum(array_column($summary[$rangeName], 'number_of_orders')) }}</td>
                    <td>{{ number_format(array_sum(array_column($summary[$rangeName], 'percentage_orders')), 1) }}%</td>
                    <td>{{ array_sum(array_column($summary[$rangeName], 'total_quantity')) }}</td>
                    <td>{{ number_format(array_sum(array_column($summary[$rangeName], 'percentage_quantity')), 1) }}%</td>
                    <td>{{ array_sum(array_column($summary[$rangeName], 'produced_min')) }}</td>
                    <td>{{ number_format(array_sum(array_column($summary[$rangeName], 'percentage_produced_min')), 1) }}%</td>
                    <td>{{ number_format(array_sum(array_column($summary[$rangeName], 'total_value')), 2) }}</td>
                    <td>{{ number_format(array_sum(array_column($summary[$rangeName], 'percentage_value')), 1) }}%</td>
                </tr>
            @endforeach
            <tr class="font-weight-bold">
                <td>Total</td>
                @foreach ($buyers as $buyer)
                    @php
                        $totalOrders = array_sum(array_column(array_column($summary, $buyer), 'number_of_orders'));
                        $totalQuantity = array_sum(array_column(array_column($summary, $buyer), 'total_quantity'));
                        $totalProducedMin = array_sum(array_column(array_column($summary, $buyer), 'produced_min'));
                        $totalValue = array_sum(array_column(array_column($summary, $buyer), 'total_value'));
                    @endphp
                    <td>{{ $totalOrders }}</td>
                    <td>{{ number_format(($totalOrders / $totals['number_of_orders']) * 100, 1) }}%</td>
                    <td>{{ $totalQuantity }}</td>
                    <td>{{ number_format(($totalQuantity / $totals['total_quantity']) * 100, 1) }}%</td>
                    <td>{{ $totalProducedMin }}</td>
                    <td>{{ number_format(($totalProducedMin / $totals['produced_min']) * 100, 1) }}%</td>
                    <td>{{ number_format($totalValue, 2) }}</td>
                    <td>{{ number_format(($totalValue / $totals['total_value']) * 100, 1) }}%</td>
                @endforeach
                <td>{{ $totals['number_of_orders'] }}</td>
                <td>100.0%</td>
                <td>{{ $totals['total_quantity'] }}</td>
                <td>100.0%</td>
                <td>{{ $totals['produced_min'] }}</td>
                <td>100.0%</td>
                <td>{{ number_format($totals['total_value'], 2) }}</td>
                <td>100.0%</td>
            </tr>
        </tbody>
    </table> --}}
    <table class="table table-bordered table-hover text-center text-wrap" style="font-size: 12px;">
        <thead class="thead-dark">
            <tr>
                @php
                    $colspan = count($buyers)+2 ;
                @endphp
                <th rowspan="2">Order Quantity Range (pcs)</th>
                 <th colspan="{{ $colspan }}">Number of Orders</th>
                <th colspan="{{ $colspan }}">Order Quantity</th>
                <th colspan="{{ $colspan }}">Production Min</th>
                <th colspan="{{ $colspan }}">Value</th> 
            </tr>
            <tr>
                @foreach ($buyers as $buyer)
                   <th>{{ $buyer }}</th>
                @endforeach
                <th>Total</th>
                <th>Percentage %</th>
                @foreach ($buyers as $buyer)
                   <th>{{ $buyer }}</th>
                @endforeach
                <th>Total</th>
                <th>Percentage %</th>
                @foreach ($buyers as $buyer)
                   <th>{{ $buyer }}</th>
                @endforeach
                <th>Total</th>
                <th>Percentage %</th>
                @foreach ($buyers as $buyer)
                   <th>{{ $buyer }}</th>
                @endforeach
                <th>Total</th>
                <th>Percentage %</th>
            </tr> 
        </thead>
        <tbody>
            @foreach ($quantityRanges as $rangeName => $range)
                <tr>
                    <td>{{ $rangeName }}</td>
                    @foreach ($buyers as $buyer)
                        @php
                            $data = $summary[$rangeName][$buyer];
                        @endphp
                        <td>{{ $data['number_of_orders'] }}</td>
                    @endforeach
                    <td>{{ array_sum(array_column($summary[$rangeName], 'number_of_orders')) }}</td>
                    <td>{{ number_format(array_sum(array_column($summary[$rangeName], 'percentage_orders')), 1) }}%</td>
                    @foreach ($buyers as $buyer)
                        @php
                            $data = $summary[$rangeName][$buyer];
                        @endphp
                        <td>{{ $data['total_quantity'] }}</td>
                    @endforeach
                    <td>{{ array_sum(array_column($summary[$rangeName], 'total_quantity')) }}</td>
                    <td>{{ number_format(array_sum(array_column($summary[$rangeName], 'percentage_quantity')), 1) }}%</td>
                    @foreach ($buyers as $buyer)
                        @php
                            $data = $summary[$rangeName][$buyer];
                        @endphp
                        <td>{{ $data['produced_min'] }}</td>
                    @endforeach
                    <td>{{ array_sum(array_column($summary[$rangeName], 'produced_min')) }}</td>
                    <td>{{ number_format(array_sum(array_column($summary[$rangeName], 'percentage_produced_min')), 1) }}%</td>
                    @foreach ($buyers as $buyer)
                        @php
                            $data = $summary[$rangeName][$buyer];
                        @endphp
                        <td>{{ number_format($data['total_value'], 2) }}</td>
                    @endforeach
                    <td>{{ number_format(array_sum(array_column($summary[$rangeName], 'total_value')), 2) }}</td>
                    <td>{{ number_format(array_sum(array_column($summary[$rangeName], 'percentage_value')), 1) }}%</td>
                </tr>
            @endforeach
            <tr class="font-weight-bold">
                <td>Total</td> 
                <th colspan="{{ $colspan-1 }}">{{ $totals['number_of_orders'] }}</th> 
                <td>100.0%</td>
                <td colspan="{{ $colspan-1 }}">{{ $totals['total_quantity'] }}</td>
                <td>100.0%</td>
                <td colspan="{{ $colspan-1 }}">{{ $totals['produced_min'] }}</td>
                <td>100.0%</td>
                <td colspan="{{ $colspan-1 }}">{{ number_format($totals['total_value'], 2) }}</td>
                <td>100.0%</td>
            </tr>

             
             
        </tbody>
    </table>
    </div>

</div>

</x-backend.layouts.master>
