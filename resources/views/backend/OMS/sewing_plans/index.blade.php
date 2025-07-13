<x-backend.layouts.master>
    <div class="card mx-5 my-5" style="background-color: white;">
        <div class="row p-1">
            <div class="col-12">
                <h3 class="text-center p-1">Sewing Plan History</h3>
                <div class="row p-1">
                    <div class="col-6 text-start">
                        <a href="{{ route('jobs.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Close
                        </a>
                        <a href="{{ route('sewing_plans.create') }}" class="btn btn-outline-primary">
                            <i class="fas fa-plus"></i> Add Sewing Plan
                        </a>
                    </div>
                    <div class="col-6 text-end">
                        <a href="{{ route('home') }}" class="btn btn-outline-success">
                            <i class="fas fa-tachometer-alt"></i> Home
                        </a>
                        <!-- Add this new button -->
                        <a href="{{ route('SewingPlanmonthlySummary') }}" class="btn btn-outline-primary">
                            <i class="fas fa-chart-bar"></i> Monthly Summary
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
                        <!-- Filter Form -->
                        <form method="GET" action="{{ route('sewing_plans.index') }}" id="filterForm">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="buyer_filter">Buyer</label>
                                    <select class="form-control" name="buyer_filter" id="buyer_filter">
                                        <option value="">All Buyers</option>
                                        @foreach ($buyers as $buyer)
                                            <option value="{{ $buyer->buyer_id }}"
                                                {{ request('buyer_filter') == $buyer->buyer_id ? 'selected' : '' }}>
                                                {{ $buyer->buyer }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label for="shipment_date_from">Shipment From</label>
                                    <input type="date" class="form-control" name="shipment_date_from"
                                        id="shipment_date_from" value="{{ request('shipment_date_from') }}">
                                </div>

                                <div class="col-md-3">
                                    <label for="shipment_date_to">Shipment To</label>
                                    <input type="date" class="form-control" name="shipment_date_to"
                                        id="shipment_date_to" value="{{ request('shipment_date_to') }}">
                                </div>

                                <div class="col-md-3">
                                    <label for="production_plan">Production Plan (Month)</label>
                                    <select class="form-control" name="production_plan" id="production_plan">
                                        <option value="">All Months</option>
                                        @foreach ($production_plan as $plan)
                                            <option value="{{ $plan->production_plan }}"
                                                {{ request('production_plan') == $plan->production_plan ? 'selected' : '' }}>
                                                {{ \Carbon\Carbon::parse($plan->production_plan)->format('M Y') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12 text-end">
                                    <a type="button" class="btn btn-secondary"
                                        href="{{ route('sewing_plans.index') }}">
                                        <i class="fas fa-sync"></i> Reset
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-filter"></i> Apply Filter
                                    </button>
                                    <button type="button" class="btn btn-success" id="exportBtn">
                                        <i class="fas fa-file-excel"></i> Export
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped text-nowrap" id="datatablesSimple">
                                <thead>
                                    <tr>
                                        <th>Month</th>
                                        <th>Buyer</th>
                                        <th>Style</th>
                                        <th>Shipment Date</th>
                                        <th>Order Quantity</th>
                                        <th>Total Plan Quantity</th>
                                        <th>Total Sewing Quantity</th>
                                        <th>Remain Sewing Quantity</th>
                                        <th>Remain Plan Quantity</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($sewing_plan as $plan)
                                        <tr>
                                            <td>
                                                {{-- {{ \Carbon\Carbon::parse($plan->production_plan)->format('M Y') }} --}}
                                                <!--modole button to production_plan month and year-->
                                            <!--change production_plan -->
                                                <button type="button" class="btn btn-outline-primary"
                                                    data-bs-toggle="modal" data-bs-target="#productionPlanModal"
                                                    data-production-plan="{{ $plan->production_plan }}">
                                                    {{ \Carbon\Carbon::parse($plan->production_plan)->format('M Y') }}
                                                </button>

                                            <!--end of change production_plan -->
                                            <!--modal-->
                                                <div class="modal fade" id="productionPlanModal" tabindex="-1"
                                                    aria-labelledby="productionPlanModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="productionPlanModalLabel">
                                                                    Change Production Plan</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form method="POST"
                                                                    action="{{ route('sewing_plans.update_production_plan') }}">
                                                                    @csrf
                                                                    <input type="hidden" name="job_no"
                                                                        value="{{ $plan->job_no }}">
                                                                    <input type="hidden" name="current_production_plan"
                                                                        value="{{ $plan->production_plan }}">
                                                                    <div class="mb-3">
                                                                        <label for="new_production_plan"
                                                                            class="form-label">New Production Plan
                                                                            (Month)</label>
                                                                        {{-- <select class="form-select"
                                                                            name="new_production_plan"
                                                                            id="new_production_plan">
                                                                            @foreach ($production_plan as $pp)
                                                                                <option value="{{ $pp->production_plan }}"
                                                                                    {{ $pp->production_plan == $plan->production_plan ? 'selected' : '' }}>
                                                                                    {{ \Carbon\Carbon::parse($pp->production_plan)->format('M Y') }}
                                                                                </option>
                                                                            @endforeach

                                                                        </select> --}}
                                                                        <input type="month"
                                                                            class="form-control"
                                                                            name="new_production_plan"
                                                                            id="new_production_plan"
                                                                            value="{{ \Carbon\Carbon::parse($plan->production_plan)->format('Y-m') }}"
                                                                            required>
                                                                    </div>
                                                                    <button type="submit"
                                                                        class="btn btn-outline-primary">Update Production Plan</button>
                                                                </form> 
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </td>
                                            <td>{{ $plan->buyer }}</td>
                                            <td>{{ $plan->style }}</td>
                                            <td>
                                                @if ($plan->shipment_date)
                                                    {{ \Carbon\Carbon::parse($plan->shipment_date)->format('d M Y') }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>{{ $plan->order_quantity }}</td>
                                            <td>{{ $plan->total_plan_quantity_row }}</td>
                                            <td>{{ $plan->total_sewing_quantity }}</td>
                                            <td>{{ $plan->remain_sewing }}</td>
                                            <td>{{ $plan->remain_plan }}</td>
                                            <td>
                                                <a href="{{ route('sewing_plans.show', $plan->job_no) }}"
                                                    class="btn btn-outline-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <form
                                                    action="{{ route('sewing_plans_destroy', ['job_no' => $plan->job_no]) }}"
                                                    method="POST" style="display: inline;" class="delete-form">
                                                    @csrf
                                                    @method('POST')
                                                    <input type="hidden" name="job_no" value="{{ $plan->job_no }}">
                                                    <input type="hidden" name="production_plan"
                                                        value="{{ $plan->production_plan }}">
                                                    <input type="hidden" name="color" value="{{ $plan->color }}">
                                                    <input type="hidden" name="size" value="{{ $plan->size }}">
                                                    <input type="hidden" name="sewing_plan_id"
                                                        value="{{ $plan->sewing_plan_id }}">

                                                    <button type="submit" class="btn btn-outline-danger">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center">No Sewing Plan Found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {

            // Export functionality
            $('#exportBtn').click(function() {
                // Get current filter parameters
                const params = new URLSearchParams({
                    buyer_filter: $('#buyer_filter').val(),
                    shipment_date_from: $('#shipment_date_from').val(),
                    shipment_date_to: $('#shipment_date_to').val(),
                    production_plan: $('#production_plan').val()
                });

                // Redirect to export route with filters
                window.location.href = "{{ route('sewing_plans.export') }}?" + params.toString();
            });
        });
    </script>
</x-backend.layouts.master>
