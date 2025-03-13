<x-backend.layouts.master>
    <div class="card mx-5 my-5" style="background-color: white;">
        <div class="row p-1">
            <div class="col-12">
                <h3 class="text-center p-1">Sewing Plan History</h3>
                <div class="row p-1">
                    <div class="col-6 text-start">
                        <a href="{{ route('sewing_plans.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Close
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
                            <table class="table table-bordered table-striped text-wrap" id="datatablesSimple">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Job No</th>
                                        <th>Buyer</th>
                                        <th>Style</th>
                                        <th>PO</th>
                                        <th>Itam</th>
                                        <th>Color</th>
                                        <th>Size</th>
                                        <th>Sewing Plan Qty</th> 
                                        <th>Action</th> 
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($old_sewing_balances as $balance)
                                    {{-- @dd($balance) --}}
                                        <tr>
                                            <td>{{ $balance->id }}</td>
                                            <td>{{ $balance->job_no }}</td>
                                            <td>{{ $balance->buyer }}</td>
                                            <td>{{ $balance->style }}</td>
                                            <td>{{ $balance->po }}</td>
                                            <td>{{ $balance->item }}</td>
                                            <td>{{ $balance->color }}</td>
                                            <td>{{ $balance->size }}</td>
                                            <td>{{ $balance->color_quantity }}</td>  
                                            <td>
                                                <a href="{{ route('sewing_plans.edit', $balance->id) }}"
                                                    class="btn btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                
                                                <!-- Blade Template -->
                                                <form
                                                    action="{{ route('sewing_plans_destroy', ['sewing_plan' => $balance->id]) }}"
                                                    method="POST" style="display: inline;" class="delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No Sewing Plan Found</td>
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
