@forelse ($jobs as $job)
    @php
        // Precompute values to avoid repeated calculations
        $sewing_qty = $job->sewingBalances->sum('sewing_balance');
        $total_sewing_qty = $job->order_quantity - $sewing_qty;
        $total_shipped_qty = $job->shipments->sum('shipped_qty');
    @endphp
    
    <tr>
        <td>
            <button type="button" class="btn btn-outline-success btn-sm" 
                data-toggle="modal" data-target="#jobModal"
                data-job-id="{{ $job->job_no }}"
                data-job-no="{{ $job->job_no }}">
                {{ $job->job_no }}
            </button>
        </td>
        <td>{{ $job->buyer ?? 'N/A' }}</td>
        <td>{{ $job->style ?? 'N/A' }}</td>
        <td>{{ $job->po ?? 'N/A' }}</td>
        <td>{{ $job->item ?? 'N/A' }}</td>
        <td>{{ number_format($job->order_quantity) }}</td>
        <td>
            <button type="button" class="btn btn-outline-danger btn-sm" 
                data-toggle="modal" data-target="#sewingModal"
                data-job-id="{{ $job->job_no }}"
                data-job-no="{{ $job->job_no }}">
                {{ number_format($total_sewing_qty) }}
            </button>
        </td>
        <td>
            <button type="button" class="btn btn-outline-info btn-sm" 
                data-toggle="modal" data-target="#ShipmentModal"
                data-job-id="{{ $job->job_no }}"
                data-job-no="{{ $job->job_no }}">
                {{ number_format($total_shipped_qty) }}
            </button>
        </td>
        <td>{{ $job->order_received_date ? $job->order_received_date->format('Y-m-d') : 'N/A' }}</td>
        <td>{{ $job->delivery_date ? $job->delivery_date->format('Y-m-d') : 'N/A' }}</td>
        <td>
            <div class="btn-group" role="group">
                <a href="{{ route('jobs.show', $job->job_no) }}"
                    class="btn btn-outline-info btn-sm">
                    <i class="fas fa-eye"></i>
                </a>

                @if (auth()->user()->role_id == 1)
                    <form action="{{ route('jobs.destroy_all', $job->job_no) }}"
                        method="POST" class="d-inline">
                        @csrf
                        @method('POST')
                        <button type="submit" class="btn btn-outline-danger btn-sm" 
                            onclick="return confirm('Are you sure?')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                @endif
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="11" class="text-center py-4">No Jobs Found</td>
    </tr>
@endforelse