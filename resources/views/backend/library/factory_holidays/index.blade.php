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
            @if (is_null($holidays) || empty($holidays))
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
                                <a href=" {{ route('home') }} " class="btn btn-lg btn-outline-danger"><i
                                        class="fas fa-arrow-left"></i>
                                    Close</a>
                                <x-backend.form.anchor :href="route('factory_holidays.create')" type="create" />
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                {{-- holiday Table goes here --}}

                                <table id="datatablesSimple" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Sl#</th>
                                            <th>Division</th>
                                            <th>Company</th>
                                            <th>Name</th>
                                            <th>Active</th>
                                            <th>Actions</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $sl=0 @endphp

                                        @forelse ($holidays as $holiday) 
                                                <tr>
                                                    <td>{{ ++$sl }}</td>
                                                    <td>{{ $holiday->division->name }}</td>
                                                    <td>{{ $holiday->company->name }}</td>
                                                    <td>{{ $holiday->name }}</td>
                                                    <td>
                                                        <form
                                                            action="{{ route('factory_holidays.active', ['holiday' => $holiday->id]) }}"
                                                            method="POST">
                                                            @csrf
                                                            <button
                                                                onclick="return confirm('Are you sure want to change status ?')"
                                                                class="btn btn-sm {{ $holiday->is_active ? 'btn-danger' : 'btn-success' }}"
                                                                type="submit">{{ $holiday->is_active ? 'Inactive' : 'Active' }}</button>
                                                        </form>
                                                    </td>
                                                    <td>
                                                        <x-backend.form.anchor :href="route('factory_holidays.edit', ['holiday' => $holiday->id])" type="edit" />
                                                        <x-backend.form.anchor :href="route('factory_holidays.show', ['holiday' => $holiday->id])" type="show" />
                                                        @if (auth()->user()->role_id == 1)
                                                            <button
                                                                class="btn btn-outline-danger my-1 mx-1 inline btn-sm"
                                                                onclick="confirmDelete('{{ route('factory_holidays.destroy', ['holiday' => $holiday->id]) }}')">
                                                                <i class="bi bi-trash"></i> Delete
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr> 
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">No data found!</td>
                                            </tr>
                                        @endforelse



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
