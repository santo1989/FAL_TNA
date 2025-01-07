<x-backend.layouts.master>
    <x-slot name="pageTitle">
        Edit Holiday Information
    </x-slot>

    <x-slot name='breadCrumb'>
        <x-backend.layouts.elements.breadcrumb>
            <x-slot name="pageHeader"> Holiday </x-slot>
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('factory_holidays.index') }}">Holiday</a></li>
            <li class="breadcrumb-item active">Edit Holiday Information</li>
        </x-backend.layouts.elements.breadcrumb>
    </x-slot>


    <x-backend.layouts.elements.errors />
    <form action="{{ route('factory_holidays.update', ['factory_holiday' => $holiday->id]) }}" method="post"
        enctype="multipart/form-data">
        {{-- @dd($holiday); --}}
        <div class="pb-3">
            @csrf
            @method('put')
            <div class="form-group">
                <label for="description">Holiday Name</label>
                <input type="text" class="form-control" id="description" name="description"
                    value="{{ $holiday->description }}">
            </div>
            <div class="form-group">
                <label for="holiday_date">Holiday Date</label>
                <input type="date" class="form-control" id="holiday_date" name="holiday_date"
                    value="{{ $holiday->holiday_date }}">
            </div>
            
            <br>


            <a href="{{ route('factory_holidays.index') }}" class="btn btn-outline-danger"><i
                    class="fas fa-arrow-left"></i> Back</a>
            <x-backend.form.saveButton>Save</x-backend.form.saveButton>
        </div>
    </form>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script></script>


</x-backend.layouts.master>
