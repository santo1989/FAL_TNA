<x-backend.layouts.master>
    <div class="card mx-5 my-5" style="background-color: white; ">
        {{-- <div class="card-header">
               <h3 class="card-title">Quantity Wise Summary</h3>
               <a href="{{ route('jobs.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Close </a>
          </div> --}}

        <div class="container-fluid pt-2">
            <h4 class="text-center">On-time Delivery Summary</h4>
            <a href="{{ route('jobs.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Close
            </a>
            <table class="table table-bordered table-hover text-center text-wrap" style="font-size: 12px;">
                <thead class="thead-dark">
                    <tr>
                        @php
                            $colspan = count($buyers) + 2;
                        @endphp
                        <th rowspan="2">Delivery Status</th>
                        <th colspan="{{ $colspan }}">Number of Deliveries</th>
                        <th colspan="{{ $colspan }}">Total Quantity</th>
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
                    </tr>
                </thead>
                <tbody>
                    @foreach ($statuses as $status)
                        <tr>
                            <td>{{ $status }}</td>
                            @foreach ($buyers as $buyer)
                                <td>{{ $summary[$status][$buyer]['number_of_deliveries'] }}</td>
                            @endforeach
                            <td>{{ array_sum(array_column($summary[$status], 'number_of_deliveries')) }}</td>
                            <td>{{ number_format(array_sum(array_column($summary[$status], 'percentage_deliveries')), 1) }}%
                            </td>
                            @foreach ($buyers as $buyer)
                                <td>{{ $summary[$status][$buyer]['total_quantity'] }}</td>
                            @endforeach
                            <td>{{ array_sum(array_column($summary[$status], 'total_quantity')) }}</td>
                            <td>{{ number_format(array_sum(array_column($summary[$status], 'percentage_quantity')), 1) }}%
                            </td>
                        </tr>
                    @endforeach
                    <tr class="font-weight-bold">
                        <td>Total</td>
                        @foreach ($buyers as $buyer)
                            <td>{{ array_sum(array_column(array_column($summary, $buyer), 'number_of_deliveries')) }}
                            </td>
                        @endforeach
                        <td>{{ $totals['number_of_deliveries'] }}</td>
                        <td>100.0%</td>
                        @foreach ($buyers as $buyer)
                            <td>{{ array_sum(array_column(array_column($summary, $buyer), 'total_quantity')) }}</td>
                        @endforeach
                        <td>{{ $totals['total_quantity'] }}</td>
                        <td>100.0%</td>
                    </tr>
                </tbody>
            </table>
        </div>


    </div>
</x-backend.layouts.master>
