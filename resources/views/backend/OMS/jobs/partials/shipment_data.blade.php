@php
    $grandTotals = [
        'order_qty' => 0,
        'shipped_qty' => 0,
        'shipped_value' => 0,
        'short_qty' => 0,
        'short_value' => 0,
        'excess_qty' => 0,
        'excess_value' => 0,
    ];
@endphp

<div class="table-responsive">
    <table class="table table-bordered table-hover table-sm">
        <thead class="bg-light">
            <tr>
                <th>Color</th>
                <th>Size</th>
                <th>Order Qty</th>
                <th>Shipped Qty</th>
                <th>Shipped Value</th>
                <th>Short Qty</th>
                <th>Short Value</th>
                <th>Excess Qty</th>
                <th>Excess Value</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @php
                // Group shipment data by color and size
                $groupedData = [];
                foreach ($shipmentData as $shipment) {
                    $key = $shipment->color . '|' . $shipment->size;
                    if (!isset($groupedData[$key])) {
                        $groupedData[$key] = [
                            'color' => $shipment->color,
                            'size' => $shipment->size,
                            'total_shipped_qty' => 0,
                            'total_shipped_value' => 0,
                            'total_excess_short_shipment_qty' => 0,
                            'total_excess_short_shipment_value' => 0
                        ];
                    }
                    $groupedData[$key]['total_shipped_qty'] += $shipment->shipped_qty;
                    $groupedData[$key]['total_shipped_value'] += $shipment->shipped_value;
                    $groupedData[$key]['total_excess_short_shipment_qty'] += $shipment->excess_short_shipment_qty;
                    $groupedData[$key]['total_excess_short_shipment_value'] += $shipment->excess_short_shipment_value;
                }
            @endphp

            @forelse($groupedData as $key => $shipment)
                @php
                    $job = App\Models\Job::where('job_no', $jobNo)
                        ->where('color', $shipment['color'])
                        ->where('size', $shipment['size'])
                        ->first();
                    
                    $order_qty = $job->color_quantity ?? 0;
                    $unit_price = $job->unit_price ?? 0;
                    
                    $short_qty = max(0, $order_qty - $shipment['total_shipped_qty']);
                    $short_value = $short_qty * $unit_price;
                    
                    // Update grand totals
                    $grandTotals['order_qty'] += $order_qty;
                    $grandTotals['shipped_qty'] += $shipment['total_shipped_qty'];
                    $grandTotals['shipped_value'] += $shipment['total_shipped_value'];
                    $grandTotals['short_qty'] += $short_qty;
                    $grandTotals['short_value'] += $short_value;
                    $grandTotals['excess_qty'] += $shipment['total_excess_short_shipment_qty'];
                    $grandTotals['excess_value'] += $shipment['total_excess_short_shipment_value'];
                @endphp
                <tr>
                    <td>{{ $shipment['color'] }}</td>
                    <td>{{ $shipment['size'] }}</td>
                    <td class="text-right">{{ number_format($order_qty) }}</td>
                    <td class="text-right">{{ number_format($shipment['total_shipped_qty']) }}</td>
                    <td class="text-right">${{ number_format($shipment['total_shipped_value'], 2) }}</td>
                    <td class="text-right @if($short_qty > 0) text-danger font-weight-bold @endif">
                        {{ number_format($short_qty) }}
                    </td>
                    <td class="text-right">${{ number_format($short_value, 2) }}</td>
                    <td class="text-right @if($shipment['total_excess_short_shipment_qty'] > 0) text-success font-weight-bold @endif">
                        {{ number_format($shipment['total_excess_short_shipment_qty']) }}
                    </td>
                    <td class="text-right">${{ number_format($shipment['total_excess_short_shipment_value'], 2) }}</td>
                    <td class="text-center">
                        <a href="{{ route('shipments.index', ['job_no' => $jobNo, 'color' => $shipment['color'], 'size' => $shipment['size']]) }}"
                           class="btn btn-sm btn-outline-info">
                            <i class="fas fa-history"></i> History
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center py-4">
                        <i class="fas fa-ship fa-2x mb-3 text-muted"></i>
                        <h5>No Shipment Data Found</h5>
                        <p class="text-muted">No shipment records exist for this job</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if(!empty($groupedData))
        <tfoot class="bg-light font-weight-bold">
            <tr>
                <td colspan="2" class="text-right">Grand Totals:</td>
                <td class="text-right">{{ number_format($grandTotals['order_qty']) }}</td>
                <td class="text-right">{{ number_format($grandTotals['shipped_qty']) }}</td>
                <td class="text-right">${{ number_format($grandTotals['shipped_value'], 2) }}</td>
                <td class="text-right">{{ number_format($grandTotals['short_qty']) }}</td>
                <td class="text-right">${{ number_format($grandTotals['short_value'], 2) }}</td>
                <td class="text-right">{{ number_format($grandTotals['excess_qty']) }}</td>
                <td class="text-right">${{ number_format($grandTotals['excess_value'], 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>