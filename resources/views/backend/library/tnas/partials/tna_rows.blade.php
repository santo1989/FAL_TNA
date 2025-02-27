<tbody class="text-nowrap" id="datarowbody">
    @forelse ($tnas as $tna)
        <tr>
            <td>{{ $tna->buyer }}</td>
            <td class="text-wrap">{{ $tna->style }}</td>
            <td class="text-wrap">{{ $tna->po }}</td>
            <td>{{ $tna->item }}</td>
            <td>{{ $tna->qty_pcs }}</td>
            <td>{{ $tna->po_receive_date }}</td>
            <td>{{ $tna->shipment_etd }}</td>
            @if (auth()->user()->role_id == 4 || auth()->user()->role_id == 1)
                <td>{{ $tna->updated_at->diffForHumans() }}</td>
            @endif
            <td>
                <!-- Action buttons (same as original Blade) -->
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="10" class="text-center">No TNA Found</td>
        </tr>
    @endforelse
</tbody>