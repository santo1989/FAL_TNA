{{-- @switch(auth()->user()->role_id)
    @case('1')
        @include('layouts.Admin')
    @break

    @case('2')
        @include('layouts.User')
    @break

    @case('3')
        @include('layouts.supervisor')
    @break

    @case('4')
        @include('layouts.hr_manager')
    @break

    @case('2')
        @include('layouts.hr_officer')
    @break

    @default
        <x-backend.layouts.master>


        </x-backend.layouts.master>
@endswitch

<script>
    $(document).ready(function() {
        $('#example').DataTable();
    } );
</script> --}}

@include('layouts.Admin')
