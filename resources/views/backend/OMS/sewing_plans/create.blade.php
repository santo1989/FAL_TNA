<x-backend.layouts.master>
    <!-- packages/YourVendor/ProductionTracking/resources/views/jobs/edit.blade.php -->
    <!--message show in .swl sweet alert-->
    @if (session('message'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: "{{ session('message') }}",
                showConfirmButton: false,
                timer: 2000
            });
        </script>
    @endif

    <x-backend.layouts.elements.errors />
    <div class="container-fluid">
        <h1 class="text-center">Update Sewing Plan</h1>
        <form action="{{ route('sewing_plans.store') }}" method="POST">
            @csrf

            <div class="row p-1">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body" style="overflow-x:auto;">
                            <input type="hidden" name="created_by" value="{{ auth()->user()->id }}">
                            <input type="hidden" name="division_id" value="2">
                            <input type="hidden" name="division_name" value="Factory">
                            <input type="hidden" name="company_id" value="3">
                            <input type="hidden" name="company_name" value="FAL - Factory">
                            <table class="table">
                                <tbody>
                                    {{--   <tr>
                                      <td class="create_label_column">Job No</td>
                                        <td class="create_input_column">
                                            <input type="text" name="job_no" class="form-control"
                                                placeholder="Job No" value="{{ $jobs_no }}" readonly>
                                        </td>
                                        <td class="create_label_column">Buyer</td>
                                        <td class="create_input_column">
                                            {{ $basic_info->buyer }}
                                        </td>
                                        <td class="create_label_column">Style</td>
                                        <td class="create_input_column">
                                            {{ $basic_info->style }}
                                        </td>
                                    </tr>
                                     <tr>
                                        <td class="create_label_column">PO</td>
                                        <td class="create_input_column">
                                            {{ $basic_info->po }}
                                        </td>
                                        <td class="create_label_column">Department</td>
                                        <td class="create_input_column">
                                            {{ $basic_info->department }}
                                        </td>
                                        <td class="create_label_column">Item</td>
                                        <td class="create_input_column">
                                            {{ $basic_info->item }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="create_label_column">Destination</td>
                                        <td class="create_input_column">
                                            {{ $basic_info->destination }}
                                        </td>
                                        <td class="create_label_column">Order Quantity</td>
                                        <td class="create_input_column">
                                            {{ $basic_info->order_quantity }}
                                        </td>
                                        <td class="create_label_column">Total Sweing</td>
                                        <td class="create_input_column">
                                            <input type="number" name="total_sewing_balance" class="form-control"
                                                placeholder="Total Sewing Balance" value="{{ $basic_info->total_sewing_balance }}"
                                                required readonly id="total_sewing_balance">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="create_label_column">Sweing Balance</td>
                                        <td class="create_input_column">
                                            <input type="number" name="sewing_balance" class="form-control"
                                                placeholder="Sewing Balance" value="{{ $basic_info->sewing_balance }}"
                                                required readonly id="sewing_balance">
                                        </td>
                                        <td class="create_label_column">Total Production Min</td>
                                        <td class="create_input_column">
                                            <input type="number" name="Total_Production_Min" class="form-control"
                                                placeholder="Total Production Min" value="{{ $basic_info->Total_Production_Min }}"
                                                required readonly id="Total_Production_Min">
                                        </td>
                                    </tr> --}}
                                    <tr>
                                        <td class="create_label_column">Production Plan</td>
                                        <td class="create_input_column">
                                            <input type="month" name="production_plan" class="form-control"
                                                placeholder="Production Plan" required>
                                        </td>
                                    </tr>
                                    <!--after input the production plan, then show that month's factory calendar with holidays and count of working days and if there is any holiday then show the message that to adjust the plan and redrect to the factory holiday page index--> 
                                    

                                </tbody>
                            </table>

                            <table class="table table-bordered mt-2 text-center" id="colorWayTable"
                                style="overflow-x:auto;">
                                <thead>
                                    <tr>
                                        <th>Job ID</th>
                                        <th>Job No</th>
                                        <th>Color</th>
                                        <th>Size</th>
                                        <th>Order Quantity</th>
                                        <th>Total Sewing Quantity</th>
                                        <th>Remain Quantity</th>
                                        <th>Sewing Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($color_sizes_qties as $color)
                                        <tr>
                                            <td>
                                                <input type="text" name="color_id[]" class="form-control"
                                                    value="{{ $color->id }}" readonly>
                                            </td>
                                            <td>
                                                <input type="text" name="job_no[]" class="form-control"
                                                    value="{{ $color->job_no }}" readonly>
                                            </td>
                                            <td>
                                                <input type="text" name="color[]" class="form-control"
                                                    value="{{ $color->color }}" readonly>
                                            </td>
                                            <td>
                                                <input type="text" name="size[]" class="form-control"
                                                    value="{{ $color->size }}" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control"
                                                    value="{{ $color->color_quantity }}" readonly>
                                            </td>
                                            <td>
                                                <input type="number" name="total_sewing_quantity[]"
                                                    class="form-control" value="{{ $color->total_sewing_quantity }}"
                                                    readonly>
                                            </td>
                                            <td>
                                                <input type="number" name="remaining_quantity[]"
                                                    class="form-control remaining-quantity"
                                                    value="{{ $color->remaining_quantity }}" readonly>
                                            </td>
                                            <td>
                                                <input type="number" name="sewing_quantity[]"
                                                    class="form-control sewing-quantity" placeholder="Sewing Quantity">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>



                            <div class="button-container">
                                <a href="{{ route('jobs.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left"></i> Cancel
                                </a>
                                <button type="submit" id="saveButton" class="btn btn-outline-success">
                                    <i class="fas fa-save"></i> Update Plan
                                </button>

                            </div>
        </form>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function() {
            // On double-click, set the sewing quantity equal to the remaining quantity
            $('input.remaining-quantity').dblclick(function() {
                // Find the related sewing quantity input in the same row
                var remaining_quantity = $(this).val();
                $(this).closest('tr').find('input.sewing-quantity').val(remaining_quantity);
            });
        });
    </script>

</x-backend.layouts.master>
