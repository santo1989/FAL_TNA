<x-backend.layouts.master>

    <x-slot name="pageTitle">
        Admin Dashboard
    </x-slot>

    <x-slot name='breadCrumb'>
        <x-backend.layouts.elements.breadcrumb>
            <x-slot name="pageHeader">
                <div class="row">
                    <div class="col-12">Dashboard</div>

                </div>
            </x-slot>
        </x-backend.layouts.elements.breadcrumb>
    </x-slot>

    <div class="container">
        <div class="row p-1">
            <div class="col-12 pb-1">
                <div class="card">
                    <div class="card-header p-1 text-left ">
                        Module Name
                    </div>

                    <div class="card-body">

                        @can('Admin')
                            <div class="row justify-content-center">
                                <div class="col-3 pt-1 pb-1">
                                    <a class="btn btn-sm btn-outline-primary" style="width: 10rem;"
                                        href="{{ route('home') }}">
                                        <div class="sb-nav-link-icon"><i class="fas fa-home"></i></div>
                                        Home
                                    </a>
                                </div>
                                <div class="col-3 pt-1 pb-1">
                                    <a class="btn btn-sm btn-outline-primary" style="width: 10rem;"
                                        href="{{ route('users.show', ['user' => auth()->user()->id]) }}">
                                        <div class="sb-nav-link-icon"><i class="far fa-address-card"></i></div>
                                        Profile
                                    </a>
                                </div>
                                <div class="col-3 pt-1 pb-1">

                                    <a class="btn btn-sm btn-outline-primary" style="width: 10rem;"
                                        href="{{ route('divisions.index') }}">
                                        <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                        Division Management
                                    </a>
                                </div>
                                <div class="col-3 pt-1 pb-1">
                                    <a class="btn btn-sm btn-outline-primary" style="width: 10rem;"
                                        href="{{ route('companies.index') }}">
                                        <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                        Company Management
                                    </a>
                                </div>
                                <div class="col-3 pt-1 pb-1">
                                    <a class="btn btn-sm btn-outline-primary" style="width: 10rem;"
                                        href="{{ route('departments.index') }}">
                                        <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                        Department Management
                                    </a>
                                </div>
                                <div class="col-3 pt-1 pb-1">
                                    <a class="btn btn-sm btn-outline-primary" style="width: 10rem;"
                                        href="{{ route('designations.index') }}">
                                        <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                        Designation Management
                                    </a>
                                </div>
                                <div class="col-3 pt-1 pb-1">
                                    <a class="btn btn-sm btn-outline-primary" style="width: 10rem;"
                                        href="{{ route('buyers.index') }}">
                                        <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                        Buyer Management
                                    </a>
                                </div>
                                <div class="col-3 pt-1 pb-1">
                                    <a class="btn btn-sm btn-outline-primary" style="width: 10rem;"
                                        href="{{ route('suppliers.index') }}">
                                        <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                        Supplier Management
                                    </a>
                                </div>
                                <div class="col-3 pt-1 pb-1">
                                    <a class="btn btn-sm btn-outline-primary" style="width: 10rem;"
                                        href="{{ route('roles.index') }}">
                                        <div class="sb-nav-link-icon"><i class="fas fa-user-shield"></i></div>
                                        Role
                                    </a>
                                </div>
                                <div class="col-3 pt-1 pb-1">
                                    <a class="btn btn-sm btn-outline-primary" style="width: 10rem;"
                                        href="{{ route('users.index') }}">
                                        <div class="sb-nav-link-icon"><i class="fas fa-user-friends"></i></div>
                                        Users
                                    </a>
                                </div>
                                <div class="col-3 pt-1 pb-1">
                                    <a class="btn btn-sm btn-outline-primary" style="width: 10rem;"
                                        href="{{ route('online_user') }}">
                                        <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                        Online User List
                                    </a>

                                </div>
                                <div class="col-3 pt-1 pb-1">
                                    <a class="btn btn-sm btn-outline-primary" style="width: 10rem;"
                                        href="{{ route('buyer_assigns.index') }}">
                                        <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                        Buyer Assign
                                    </a>

                                </div>
                                <div class="col-3 pt-1 pb-1">
                                    <a class="btn btn-sm btn-outline-primary" style="width: 10rem;"
                                        href="{{ route('sops.index') }}">
                                        <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                        Standard SOP
                                    </a>

                                </div>
                                <div class="col-3 pt-1 pb-1">
                                    <a class="btn btn-sm btn-outline-primary" style="width: 10rem;"
                                        href="{{ route('marchent_sops.index') }}">
                                        <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                        Buyer ways SOP
                                    </a>

                                </div>
                                <div class="col-3 pt-1 pb-1">
                                    <a class="btn btn-sm btn-outline-primary" style="width: 10rem;"
                                        href="{{ route('tnas.index') }}">
                                        <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                        TNA
                                    </a>

                                </div>

                                <div class="col-3 pt-1 pb-1">
                                    <a class="btn btn-sm btn-outline-primary" style="width: 10rem;"
                                        href="{{ route('jobs.index') }}">
                                        <div class="sb-nav-link-icon"><i class="fas fa-list"></i></div>
                                        OMS
                                    </a>

                                </div>


                            </div>
                        @endcan


                        @can('General')
                            <div class="row justify-content-center">
                                {{-- <div class="col-3 pt-1 pb-1">
                                    <a class="btn btn-sm btn-outline-primary" style="width: 10rem;"
                                        href="{{ route('home') }}">
                                        <div class="sb-nav-link-icon"><i class="fas fa-home"></i></div>
                                        Home
                                    </a>
                                </div>
                                <div class="col-3 pt-1 pb-1">
                                    <a class="btn btn-sm btn-outline-primary" style="width: 10rem;"
                                        href="{{ route('users.show', ['user' => auth()->user()->id]) }}">
                                        <div class="sb-nav-link-icon"><i class="far fa-address-card"></i></div>
                                        Profile
                                    </a>
                                </div> --}}
                                <div class="col-3 pt-1 pb-1">
                                    <a class="btn btn-sm btn-outline-primary" style="width: 10rem;"
                                        href="{{ route('tnas.index') }}">
                                        <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                        TNA
                                    </a>

                                </div>

                            </div>
                        @endcan
                        @can('Marchendiser')
                            <div class="row justify-content-center">
                                {{-- <div class="col-3 pt-1 pb-1">
                                    <a class="btn btn-sm btn-outline-primary" style="width: 10rem;"
                                        href="{{ route('home') }}">
                                        <div class="sb-nav-link-icon"><i class="fas fa-home"></i></div>
                                        Home
                                    </a>
                                </div>
                                <div class="col-3 pt-1 pb-1">
                                    <a class="btn btn-sm btn-outline-primary" style="width: 10rem;"
                                        href="{{ route('users.show', ['user' => auth()->user()->id]) }}">
                                        <div class="sb-nav-link-icon"><i class="far fa-address-card"></i></div>
                                        Profile
                                    </a>
                                </div> --}}
                                <div class="col-3 pt-1 pb-1">
                                    <a class="btn btn-sm btn-outline-primary" style="width: 10rem;"
                                        href="{{ route('tnas.index') }}">
                                        <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                        TNA
                                    </a>

                                </div>

                            </div>
                        @endcan
                         @can('Factory Merchandise')
                            <div class="row justify-content-center">
                                 
                                <div class="col-3 pt-1 pb-1">
                                    <a class="btn btn-sm btn-outline-primary" style="width: 10rem;"
                                        href="{{ route('tnas.index') }}">
                                        <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                        TNA
                                    </a>

                                </div>

                            </div>
                        @endcan
                         @can('Factory Planning')
                            <div class="row justify-content-center">
                                 
                                <div class="col-3 pt-1 pb-1">
                                    <a class="btn btn-sm btn-outline-primary" style="width: 10rem;"
                                        href="{{ route('tnas.index') }}">
                                        <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                        TNA
                                    </a>

                                </div>

                            </div>
                        @endcan
                         @can('IE and Maintenance')
                            <div class="row justify-content-center">
                                {{-- <div class="col-3 pt-1 pb-1">
                                    <a class="btn btn-sm btn-outline-primary" style="width: 10rem;"
                                        href="{{ route('home') }}">
                                        <div class="sb-nav-link-icon"><i class="fas fa-home"></i></div>
                                        Home
                                    </a>
                                </div>
                                <div class="col-3 pt-1 pb-1">
                                    <a class="btn btn-sm btn-outline-primary" style="width: 10rem;"
                                        href="{{ route('users.show', ['user' => auth()->user()->id]) }}">
                                        <div class="sb-nav-link-icon"><i class="far fa-address-card"></i></div>
                                        Profile
                                    </a>
                                </div> --}}
                                <div class="col-3 pt-1 pb-1">
                                    <a class="btn btn-sm btn-outline-primary" style="width: 10rem;"
                                        href="{{ route('tnas.index') }}">
                                        <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                        TNA
                                    </a>

                                </div>

                            </div>
                        @endcan
                         @can('CAD')
                            <div class="row justify-content-center">
                                {{-- <div class="col-3 pt-1 pb-1">
                                    <a class="btn btn-sm btn-outline-primary" style="width: 10rem;"
                                        href="{{ route('home') }}">
                                        <div class="sb-nav-link-icon"><i class="fas fa-home"></i></div>
                                        Home
                                    </a>
                                </div>
                                <div class="col-3 pt-1 pb-1">
                                    <a class="btn btn-sm btn-outline-primary" style="width: 10rem;"
                                        href="{{ route('users.show', ['user' => auth()->user()->id]) }}">
                                        <div class="sb-nav-link-icon"><i class="far fa-address-card"></i></div>
                                        Profile
                                    </a>
                                </div> --}}
                                <div class="col-3 pt-1 pb-1">
                                    <a class="btn btn-sm btn-outline-primary" style="width: 10rem;"
                                        href="{{ route('tnas.index') }}">
                                        <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                        TNA
                                    </a>

                                </div>

                            </div>
                        @endcan
                        @can('Store')
                            <div class="row justify-content-center">
                                {{-- <div class="col-3 pt-1 pb-1">
                                    <a class="btn btn-sm btn-outline-primary" style="width: 10rem;"
                                        href="{{ route('home') }}">
                                        <div class="sb-nav-link-icon"><i class="fas fa-home"></i></div>
                                        Home
                                    </a>
                                </div>
                                <div class="col-3 pt-1 pb-1">
                                    <a class="btn btn-sm btn-outline-primary" style="width: 10rem;"
                                        href="{{ route('users.show', ['user' => auth()->user()->id]) }}">
                                        <div class="sb-nav-link-icon"><i class="far fa-address-card"></i></div>
                                        Profile
                                    </a>
                                </div> --}}
                                <div class="col-3 pt-1 pb-1">
                                    <a class="btn btn-sm btn-outline-primary" style="width: 10rem;"
                                        href="{{ route('tnas.index') }}">
                                        <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                        TNA
                                    </a>

                                </div>

                            </div>
                        @endcan
                        @can('SuperVisor')
                            <div class="row justify-content-center">
                                <div class="col-3 pt-1 pb-1">
                                    <a class="btn btn-sm btn-outline-primary" style="width: 10rem;"
                                        href="{{ route('buyers.index') }}">
                                        <div class="sb-nav-link-icon"><i class="fas fa-atlas"></i></div>
                                        Buyer Management
                                    </a>
                                </div>
                                <div class="col-3 pt-1 pb-1">
                                    <a class="btn btn-sm btn-outline-primary" style="width: 10rem;"
                                        href="{{ route('buyer_assigns.index') }}">
                                        <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                        Buyer Assign
                                    </a>

                                </div>
                                <div class="col-3 pt-1 pb-1">
                                    <a class="btn btn-sm btn-outline-primary" style="width: 10rem;"
                                        href="{{ route('sops.index') }}">
                                        <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                        Standard SOP
                                    </a>

                                </div>
                                <div class="col-3 pt-1 pb-1">
                                    <a class="btn btn-sm btn-outline-primary" style="width: 10rem;"
                                        href="{{ route('tnas.index') }}">
                                        <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                        TNA
                                    </a>

                                </div>
                                <div class="col-3 pt-1 pb-1">
                                    <a class="btn btn-sm btn-outline-primary" style="width: 10rem;"
                                        href="{{ route('jobs.index') }}">
                                        <div class="sb-nav-link-icon"><i class="fas fa-list"></i></div>
                                        OMS
                                    </a>

                                </div>
                            </div>
                        </div>
                    @endcan
                    
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header text-left p-1 ">
                    Reports
                </div>

                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-3 pt-1 pb-1">
                            <a class="btn btn-sm btn-outline-primary" style="width: 10rem;"
                                href="{{ route('tnas_dashboard') }}" target="_blank">
                                <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                TNA Dashboard
                            </a>

                        </div>
                        <div class="col-3 pt-1 pb-1">
                            <a class="btn btn-sm btn-outline-primary" style="width: 10rem;"
                                href="{{ route('BuyerWiseTnaSummary') }}" target="_blank">
                                <div class="sb-nav-link-icon"><i class="fas fa-user"></i></div>
                                TNA Pending List
                            </a> 

                        </div>
                        <div class="col-3 pt-1 pb-1">
                            <a class="btn btn-sm btn-outline-primary" style="width: 10rem;"
                                href="{{ route('TnaSummaryReport') }}" target="_blank">
                                <div class="sb-nav-link-icon"><i class="fas fa-user"></i></div>
                                TNA Report
                            </a>

                            

                        </div>

                         <div class="col-3 pt-1 pb-1">
                            <a class="btn btn-sm btn-outline-primary" style="width: 10rem;"
                                href="{{ route('BuyerWiseProductionLeadTimeSummary') }}" target="_blank">
                                <div class="sb-nav-link-icon"><i class="fas fa-user"></i></div>
                                Lead Time Summary
                            </a>
                        </div>
                        
                        <div class="col-3 pt-1 pb-1">
                            <a class="btn btn-sm btn-outline-primary" style="width: 10rem;"
                                href="{{ route('BuyerWiseOnTimeShipmentSummary') }}" target="_blank">
                                <div class="sb-nav-link-icon"><i class="fas fa-user"></i></div>
                                Shipment Summary
                            </a>
                        </div>
                        
                        @if (Auth()->user()->role_id == 1 || Auth()->user()->role_id == 4)
                            <div class="col-3 pt-1 pb-1">
                                <a href="{{ route('monthly_order_summary') }}" class="btn btn-sm btn-outline-primary"
                                    style="width: 10rem;" target="_blank">
                                    <div class="sb-nav-link-icon"><i class="fas fa-list"></i></div>
                                    Monthly Order Summary
                                </a>
                            </div>
                            <div class="col-3 pt-1 pb-1">
                                <a href="{{ route('quantity_wise_summary') }}" class="btn btn-sm btn-outline-primary"
                                    style="width: 10rem;" target="_blank">
                                    <div class="sb-nav-link-icon"><i class="fas fa-list"></i></div>
                                    Quantity wise Summary
                                </a>
                            </div>
                            <div class="col-3 pt-1 pb-1">
                                <a href="{{ route('item_wise_summary') }}" class="btn btn-sm btn-outline-primary"
                                    style="width: 10rem;" target="_blank">
                                    <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                    Item wise Summary
                                </a>
                            </div>
                            <div class="col-3 pt-1 pb-1">
                                <a href="{{ route('delivery_summary') }}" class="btn btn-sm btn-outline-primary"
                                    style="width: 10rem;" target="_blank">
                                    <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                    Delivery Summary
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</x-backend.layouts.master>
