<x-backend.layouts.master>
    <div class="card mx-5 my-5" style="background-color: white; overflow-x: auto;">
        <div class="container-fluid pt-2">
            <h4 class="text-center">Item-Wise Summary</h4>
            <a href="{{ route('jobs.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Close
            </a>
            <table class="table table-bordered table-hover text-center text-wrap" style="font-size: 12px;">
                <thead class="thead-dark">
                    <tr>
                        @php
                            $colspan = count($buyers) + 2;
                        @endphp
                        <th rowspan="2">Item</th>
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
                    @foreach ($summary as $item => $buyersData)
                        <tr>
                            <td>{{ $item }}</td>
                            @foreach ($buyers as $buyer)
                                <td>{{ $buyersData[$buyer]['number_of_orders'] }}</td>
                            @endforeach
                            <td>{{ array_sum(array_column($buyersData, 'number_of_orders')) }}</td>
                            <td>{{ number_format(array_sum(array_column($buyersData, 'percentage_orders')), 1) }}%</td>
                            @foreach ($buyers as $buyer)
                                <td>{{ $buyersData[$buyer]['total_quantity'] }}</td>
                            @endforeach
                            <td>{{ array_sum(array_column($buyersData, 'total_quantity')) }}</td>
                            <td>{{ number_format(array_sum(array_column($buyersData, 'percentage_quantity')), 1) }}%
                            </td>
                            @foreach ($buyers as $buyer)
                                <td>{{ $buyersData[$buyer]['produced_min'] }}</td>
                            @endforeach
                            <td>{{ array_sum(array_column($buyersData, 'produced_min')) }}</td>
                            <td>{{ number_format(array_sum(array_column($buyersData, 'percentage_produced_min')), 1) }}%
                            </td>
                            @foreach ($buyers as $buyer)
                                <td>{{ number_format($buyersData[$buyer]['total_value'], 2) }}</td>
                            @endforeach
                            <td>{{ number_format(array_sum(array_column($buyersData, 'total_value')), 2) }}</td>
                            <td>{{ number_format(array_sum(array_column($buyersData, 'percentage_value')), 1) }}%</td>
                        </tr>
                    @endforeach
                    <tr class="font-weight-bold">
                        <td>Total</td>
                        <th colspan="{{ $colspan - 1 }}">{{ $totals['number_of_orders'] }}</th>
                        <td>100.0%</td>
                        <td colspan="{{ $colspan - 1 }}">{{ $totals['total_quantity'] }}</td>
                        <td>100.0%</td>
                        <td colspan="{{ $colspan - 1 }}">{{ $totals['produced_min'] }}</td>
                        <td>100.0%</td>
                        <td colspan="{{ $colspan - 1 }}">{{ number_format($totals['total_value'], 2) }}</td>
                        <td>100.0%</td>
                    </tr>
                </tbody>
            </table>
        </div>


    </div>
</x-backend.layouts.master>
