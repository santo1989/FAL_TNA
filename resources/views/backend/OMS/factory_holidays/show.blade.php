<x-backend.layouts.master>
    <x-slot name="pageTitle">
        Holidays List
    </x-slot>

    <x-slot name='breadCrumb'>
        <x-backend.layouts.elements.breadcrumb>
            <x-slot name="pageHeader"> Holidays </x-slot>

            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('factory_holidays.index') }}">Holidays</a></li>
        </x-backend.layouts.elements.breadcrumb>
    </x-slot>

    <section class="content">
        <div class="container-fluid">
            @if (is_null($holiday) || empty($holiday))
                <div class="row">
                    <div class="col-md-12 col-lg-12 col-sm-12">
                        <h1 class="text-danger"> <strong>Currently No Information Available!</strong> </h1>
                    </div>
                </div>
            @else
                {{-- <x-backend.layouts.elements.message /> --}}

                <x-backend.layouts.elements.errors />

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <a href=" {{ route('jobs.index') }} " class="btn btn-lg btn-outline-danger"><i
                                        class="fas fa-arrow-left"></i>
                                    Close</a>
                                <x-backend.form.anchor :href="route('factory_holidays.create')" type="create" />
                                <a href="{{ route('factory_holidays.calander_views') }}"
                                    class="btn btn-lg btn-outline-primary"><i class="fas fa-plus"></i> Calander view</a>
                                <a href="{{ route('capacity_plans.create') }}" class="btn btn-lg btn-outline-success"><i
                                        class="fas fa-tachometer-alt"></i> Add Capacity Plan</a>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                {{-- holiday Table goes here --}}

                                <table id="datatablesSimple" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Description</th>
                                            <th>Weekend</th>
                                            <th>Active</th>
                                            <th>Actions</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td> {{ Carbon\Carbon::parse($holiday->holiday_date)->format('d-M-Y') }}
                                            </td>
                                            <td>{{ $holiday->description }}</td>
                                            <td>
                                                @if ($holiday->is_weekend == 1)
                                                    <span class="badge badge-success">Yes</span>
                                                @else
                                                    <span class="badge badge-danger">No</span>
                                                @endif
                                            </td>
                                            <td>
                                                <form
                                                    action="{{ route('factory_holidays.active', ['factory_holiday' => $holiday->id]) }}"
                                                    method="POST">
                                                    @csrf
                                                    <button
                                                        onclick="return confirm('Are you sure want to change status ?')"
                                                        class="btn btn-sm {{ $holiday->is_active ? 'btn-success' : 'btn-outline-danger' }}"
                                                        type="submit">{{ $holiday->is_active ? 'Active' : 'Inactive' }}</button>
                                                </form>
                                            </td>
                                            <td>
                                                <x-backend.form.anchor :href="route('factory_holidays.edit', [
                                                    'factory_holiday' => $holiday->id,
                                                ])" type="edit" />
                                                <x-backend.form.anchor :href="route('factory_holidays.show', [
                                                    'factory_holiday' => $holiday->id,
                                                ])" type="show" />
                                                @if (auth()->user()->role_id == 1)
                                                    <button class="btn btn-outline-danger my-1 mx-1 inline btn-sm"
                                                        onclick="confirmDelete('{{ route('factory_holidays.destroy', ['factory_holiday' => $holiday->id]) }}')">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>



                                    </tbody>
                                </table>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->


                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    @endif

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function confirmDelete(url) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit the form if the user confirms
                    let form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    form.innerHTML = `@csrf @method('delete')`;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>

</x-backend.layouts.master>
