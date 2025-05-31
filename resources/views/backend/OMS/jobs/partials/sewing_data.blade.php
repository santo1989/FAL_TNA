@php
    $total_sewing_balance = 0;
    $total_production_min_balance = 0;
@endphp

<div class="table-responsive">
    <table class="table table-bordered table-hover table-sm text-nowrap">
        <thead class="bg-light">
            <tr>
                <th>Color</th>
                <th>Size</th>
                <th>Order Qty</th>
                <th>Total Sewing Qty</th>
                <th>Balance Qty</th>
                <th>Production Min Balance</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @php
                // Group sewing data by color and size
                $groupedData = [];
                foreach ($sewingData as $balance) {
                    $key = $balance->color . '|' . $balance->size;
                    if (!isset($groupedData[$key])) {
                        $groupedData[$key] = [
                            'color' => $balance->color,
                            'size' => $balance->size,
                            'total_sewing_balance' => 0,
                            'total_production_min_balance' => 0
                        ];
                    }
                    $groupedData[$key]['total_sewing_balance'] += $balance->sewing_balance;
                    $groupedData[$key]['total_production_min_balance'] += $balance->production_min_balance;
                }
            @endphp

            @forelse($groupedData as $key => $balance)
                @php
                    $order_qty = App\Models\Job::where('job_no', $jobNo)
                        ->where('color', $balance['color'])
                        ->where('size', $balance['size'])
                        ->value('color_quantity') ?? 0;
                    
                    $balance_qty = $order_qty - $balance['total_sewing_balance'];
                    
                    $total_sewing_balance += $balance['total_sewing_balance'];
                    $total_production_min_balance += $balance['total_production_min_balance'];
                @endphp
                <tr>
                    <td>{{ $balance['color'] }}</td>
                    <td>{{ $balance['size'] }}</td>
                    <td class="text-right">{{ number_format($order_qty) }}</td>
                    <td class="text-right">{{ number_format($balance['total_sewing_balance']) }}</td>
                    <td class="text-right font-weight-bold @if($balance_qty > 0) text-danger @else text-success @endif">
                        {{ number_format($balance_qty) }}
                    </td>
                    <td class="text-right">{{ number_format($balance['total_production_min_balance']) }} min</td>
                    <td class="text-center">
                        <a href="{{ route('sewing_balances.index', ['job_no' => $jobNo, 'color' => $balance['color'], 'size' => $balance['size']]) }}"
                           class="btn btn-sm btn-outline-info">
                            <i class="fas fa-history"></i> History
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-4">
                        <i class="fas fa-tshirt fa-2x mb-3 text-muted"></i>
                        <h5>No Sewing Data Found</h5>
                        <p class="text-muted">No sewing records exist for this job</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if(!empty($groupedData))
        <tfoot class="bg-light font-weight-bold">
            <tr>
                <td colspan="2" class="text-right">Totals:</td>
                <td class="text-right">{{ number_format(array_sum(array_column($groupedData, 'order_qty'))) }}</td>
                <td class="text-right">{{ number_format($total_sewing_balance) }}</td>
                <td class="text-right">{{ number_format(array_sum(array_column($groupedData, 'balance_qty'))) }}</td>
                <td class="text-right">{{ number_format($total_production_min_balance) }} min</td>
                <td></td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>