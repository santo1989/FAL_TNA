<!-- packages/YourVendor/ProductionTracking/resources/views/jobs/show.blade.php -->
<x-backend.layouts.master>
    <div class="container-fluid pb-2">
        <h1 class="text-center">Job Details</h1>
        <div class="card" style="overflow-x: auto;">
            <table class="table table-bordered text-wrap">
                <thead>
                    <tr>
                        @if (auth()->user()->role_id == 1 || auth()->user()->role_id == 4)
                            <th>Actions</th>
                        @else
                            <th>SL</th>
                        @endif
                        <th>Buyer</th>
                        <th>Job No</th>
                        <th>Style</th>
                        <th>PO</th>
                        <th>Department</th>
                        <th>Item</th>
                        <th>Color</th>
                        <th>Size</th>
                        <th>Color Quantity</th>
                        <th>Destination</th>
                        <th>Order Quantity</th>
                        <th>Inspection Date</th>
                        <th>Delivery Date</th>
                        <th>Target SMV</th>
                        <th>Production Minutes</th>
                        <th>Unit Price</th>
                        <th>Total Value</th>
                        <th>CM /PCs</th>
                        <th>Total CM</th>
                        <th>Consumption/ DZN</th>
                        <th>Fabric Quantity</th>
                        <th>Fabrication</th>
                        <th>Order Received Date</th>
                        <th>AOP</th>
                        <th>Print</th>
                        <th>Embroidery</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($jobs as $job)
                        <tr>
                            @if (auth()->user()->role_id == 1 || auth()->user()->role_id == 4)
                                <td>
                                    <form action="{{ route('jobs.destroy', $job->id) }}" method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this job?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>

                                </td>
                            @else
                                <td>{{ $loop->index + 1 }}</td>
                            @endif
                            <td>{{ $job->buyer }}</td>
                            <td>{{ $job->job_no }}</td>
                            <td>{{ $job->style }}</td>
                            <td>{{ $job->po }}</td>
                            <td>{{ $job->department }}</td>
                            <td>{{ $job->item }}</td>
                            <td>{{ $job->color }}</td>
                            <td>{{ $job->size }}</td>
                            <td>{{ $job->color_quantity }}</td>
                            <td>{{ $job->destination }}</td>
                            <td>{{ $job->delivery_date }}</td>
                            <td>{{ $job->ins_date }}</td>
                            <td>{{ $job->delivery_date }}</td>
                            <td>{{ $job->target_smv }}</td>
                            <td>{{ $job->production_minutes }}</td>
                            <td>{{ $job->unit_price }}</td>
                            <td>{{ $job->total_value }}</td>
                            <td>{{ $job->cm_pc }}</td>
                            <td>{{ $job->total_cm }}</td>
                            <td>{{ $job->consumption_dzn }}</td>
                            <td>{{ $job->fabric_qnty }}</td>
                            <td>{{ $job->fabrication }}</td>
                            <td>{{ $job->order_received_date }}</td>
                            <td>{{ $job->aop }}</td>
                            <td>{{ $job->print }}</td>
                            <td>{{ $job->embroidery }}</td>
                            <td>{{ $job->remarks }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="26">No Jobs found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <br>
        <a href="{{ route('jobs.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left-circle me-1"></i> Back </a>
    </div>
</x-backend.layouts.master>
