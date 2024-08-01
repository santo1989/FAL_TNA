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
    <div class="container">
        <h1 class="text-center">Update Sewing Balance</h1>
        <form action="{{ route('sewing_balances_store', $jobs_no) }}" method="POST">
            @csrf

            <div class="row p-1">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <input type="hidden" name="created_by" value="{{ auth()->user()->id }}">
                            <input type="hidden" name="division_id" value="2">
                            <input type="hidden" name="division_name" value="Factory">
                            <input type="hidden" name="company_id" value="3">
                            <input type="hidden" name="company_name" value="FAL - Factory">
                            <table class="table">
                                <tbody>
                                    <tr>
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
                                        <td class="create_label_column">Sweing Balance</td>
                                        <td class="create_input_column">
                                            <input type="number" name="sewing_balance" class="form-control"
                                                placeholder="Sewing Balance" value="{{ $basic_info->sewing_balance }}"
                                                required readonly id="sewing_balance">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="create_label_column">Production Plan</td>
                                        <td class="create_input_column">
                                            <input type="month" name="production_plan" class="form-control"
                                                placeholder="Production Plan" required>
                                        </td>
                                        <td class="create_label_column">Target SMV</td>
                                        <td class="create_input_column">
                                            <input type="number" step="0.01" name="target_smv" class="form-control"
                                                placeholder="Target SMV" value="{{ $basic_info->target_smv }}" required
                                                readonly id="target_smv">
                                        </td>
                                        <td class="create_label_column">Production Min Balance</td>
                                        <td class="create_input_column">
                                            <input type="number" step="0.01" name="production_min_balance"
                                                class="form-control" placeholder="Production Min Balance" required
                                                readonly id="production_min_balance">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="table table-bordered mt-2 text-center" id="colorWayTable">
                                <thead>
                                    <tr>
                                        <th>Color</th>
                                        <th>Size</th>
                                        <th>Remain Quantity</th>
                                        <th>Sewing Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($color_sizes_qties as $color)
                                        <tr>
                                            <input type="hidden" name="color_id[]" value="{{ $color->id }}">
                                            <td>
                                                <input type="text" name="color[]" class="form-control"
                                                    placeholder="Color" value="{{ $color->color }}" readonly>
                                            </td>
                                            <td>
                                                <input type="text" name="size[]" class="form-control"
                                                    placeholder="Size" value="{{ $color->size }}" readonly>
                                            </td>
                                            <td>
                                                @php
                                                    $total_sewing_qty = 0;

                                                    $sewing_qty = $old_sewing_balances
                                                        ->where('color', $color->color)
                                                        ->where('size', $color->size)
                                                        ->sum('sewing_balance');
                                                    if ($sewing_qty > 0) {
                                                        $total_sewing_qty = $sewing_qty;
                                                    } else {
                                                        $total_sewing_qty = 0;
                                                    }
                                                    $remain_qty = $color->color_quantity - $total_sewing_qty;
                                                @endphp
                                                <input type="number" name="color_quantity[]" class="form-control"
                                                    placeholder="Quantity" value="{{ $remain_qty }}" readonly>
                                            </td>
                                            <td>
                                                <input type="number" name="sewing_quantity[]"
                                                    class="form-control sewing_quantity" placeholder="Sewing Quantity">
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
                                    <i class="fas fa-save"></i> Update Balance
                                </button>

                            </div>
        </form>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    {{-- <script>
        $(document).ready(function() {
            $('#style-select').select2();
            $('#po-select').select2();
            $('#department-select').select2();

            calculateSewingBalance();
            calculateProductionMinBalance();

            $('.sewing_quantity').on('input', function() {
                //if sewing quantity is empty, set it to 0 to avoid NaN and if sewing quantity is greater than color quantity, set it to color quantity
                // if (parseInt($(this).val()) > parseInt($(this).parent().prev().children().val())) {
                    $(this).val($(this).parent().prev().children().val());
                // }
                calculateSewingBalance();
                calculateProductionMinBalance();
            });



            function calculateSewingBalance() {
                var total_sewing_quantity = 0;
                $('.sewing_quantity').each(function() {
                    total_sewing_quantity += $(this).val() === '' ? 0 : parseInt($(this).val());
                });
                $('#sewing_balance').val(total_sewing_quantity);
            }

            function calculateProductionMinBalance() {
                var sewing_balance = $('#sewing_balance').val();
                var target_smv = $('#target_smv').val();
                var production_min_balance = sewing_balance * target_smv;
                production_min_balance = production_min_balance.toFixed(2);
                $('#production_min_balance').val(production_min_balance);
            }
        });
    </script> --}}


    <script>
        $(document).ready(function() {
            $('#style-select').select2();
            $('#po-select').select2();
            $('#department-select').select2();

            calculateSewingBalance();
            calculateProductionMinBalance();

            $('.sewing_quantity').on('input', function() {
                // Allow any value to be input without restricting it to the color quantity
                calculateSewingBalance();
                calculateProductionMinBalance();
            });

            function calculateSewingBalance() {
                var total_sewing_quantity = 0;
                $('.sewing_quantity').each(function() {
                    total_sewing_quantity += $(this).val() === '' ? 0 : parseInt($(this).val());
                });
                $('#sewing_balance').val(total_sewing_quantity);
            }

            function calculateProductionMinBalance() {
                var sewing_balance = $('#sewing_balance').val();
                var target_smv = $('#target_smv').val();
                var production_min_balance = sewing_balance * target_smv;
                production_min_balance = production_min_balance.toFixed(2);
                $('#production_min_balance').val(production_min_balance);
            }
        });
    </script>


</x-backend.layouts.master>
