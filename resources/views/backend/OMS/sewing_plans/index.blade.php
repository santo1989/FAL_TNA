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
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped text-nowrap" id="datatablesSimple">
                                <thead>
                                    <tr>
                                        <th>Month</th>
                                        {{-- <th>Job ID</th> --}}
                                        <th>Job No</th>
                                        <th>Color</th>
                                        <th>Size</th>
                                        <th>Sewing Qty</th>
                                        <th>Action </th>
                                    </tr>
                                </thead>
                                <tbody>
                                  
                                    @forelse ($sewing_plan as $balance)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($balance->production_plan)->format('M Y') }}
                                            </td>
{{-- 
                                            <td>{{ $balance->job_id }}</td> --}}
                                            <td>{{ $balance->job_no }}</td>
                                            <td>{{ $balance->color }}</td>
                                            <td>{{ $balance->size }}</td>
                                            <td>{{ $balance->total_sewing_quantity }}</td>
                                            <td>
                                                {{-- <a href="{{ route('sewing_plans.edit', $balance->job_no) }}"
                                                    class="btn btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a> --}}
                                                <a href="{{ route('sewing_plans.show', $balance->job_no) }}"
                                                    class="btn btn-outline-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <!-- Blade Template -->
                                                <form
                                                    action="{{ route('sewing_plans_destroy', ['job_no' => $balance->job_no]) }}"
                                                    method="POST" style="display: inline;" class="delete-form">
                                                    @csrf
                                                    @method('POST')
                                                    <input type="hidden" name="job_no" value="{{ $balance->job_no }}">
                                                    <input type="hidden" name="production_plan"
                                                        value="{{ $balance->production_plan }}">
                                                    <input type="hidden" name="color" value="{{ $balance->color }}">
                                                    <input type="hidden" name="size" value="{{ $balance->size }}">
                                                    <input type="hidden" name="sewing_plan_id"
                                                        value="{{ $balance->sewing_plan_id }}">

                                                    <button type="submit" class="btn btn-outline-danger">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </td>

                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No Sewing Plan Found</td>
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
            $('#datatablesSimple').DataTable();
        });
    </script>
</x-backend.layouts.master>
