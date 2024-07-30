<x-backend.layouts.master>
    <div class="card mx-5 my-5" style="background-color: white;">
        <div class="row p-1">
            <div class="col-12">
                <h3 class="text-center p-1">Sewing Balance History</h3>
                <div class="row p-1">
                    <div class="col-6 text-start">
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Close
                        </a>
                        <a href="{{ route('archives') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-archive"></i> Job Archives
                        </a>
                    </div>
                    <div class="col-6 text-end">
                        <a href="{{ route('tnas_dashboard') }}" class="btn btn-outline-success">
                            <i class="fas fa-tachometer-alt"></i> Job Dashboard
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
                                        <th>Job No</th>
                                        <th>Color</th>
                                        <th>Size</th>
                                        <th>Total Sewing Balance</th>
                                        <th>Total Production Min Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($sewing_balance as $balance)
                                        <tr>
                                            <td>{{ $balance->job_no }}</td>
                                            <td>{{ $balance->color }}</td>
                                            <td>{{ $balance->size }}</td>
                                            <td>{{ $balance->total_sewing_balance }}</td>
                                            <td>{{ $balance->total_production_min_balance }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No Sewing Balance Found</td>
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
