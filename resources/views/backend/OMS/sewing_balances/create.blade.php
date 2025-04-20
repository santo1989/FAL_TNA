{{-- <x-backend.layouts.master>
 
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
                                        <td class="create_label_column">Total Sweing</td>
                                        <td class="create_input_column">
                                            <input type="number" name="total_sewing_balance" class="form-control"
                                                placeholder="Total Sewing Balance"
                                                value="{{ $basic_info->total_sewing_balance }}" required readonly
                                                id="total_sewing_balance">
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
                                                placeholder="Total Production Min"
                                                value="{{ $basic_info->Total_Production_Min }}" required readonly
                                                id="Total_Production_Min">
                                        </td>
                                        <td class="create_label_column">Production Month</td>
                                        <td class="create_input_column">
                                            @php
                                                $color_sizes_qties_plan_month = $color_sizes_qties->first()->production_plan;
                                                $production_plan = \Carbon\Carbon::parse($color_sizes_qties_plan_month)->format('Y-m');
                                            @endphp
                                            <input type="month" name="production_plan" class="form-control"
                                                placeholder="Production Plan" required value="{{ $production_plan }}"
                                                id="production_plan" readonly  >
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="create_label_column">Sewing Month</td>
                                        <td class="create_input_column">
                                            <input type="month" name="production_plan" class="form-control"
                                                placeholder="Production Plan" required value="{{ now()->format('Y-m') }}"
                                                id="production_plan">
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
                                        <th>Order Quantity</th>
                                        <th>Plan Quantity</th>
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
                                                {{ $color->color_quantity }}
                                            </td>
                                            <td>
                                                <input type="number" name="plan_quantity[]"
                                                    class="form-control plan_quantity" placeholder="Plan Quantity"
                                                    value="{{ $color->color_quantity }}" readonly>
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
                                                <input type="number" name="color_quantity[]" id="color_quantity"
                                                    class="form-control" placeholder="Quantity"
                                                    value="{{ $remain_qty }}" readonly>
                                            </td>
                                            <td>
                                                <input type="number" name="sewing_quantity[]"
                                                    class="form-control sewing_quantity"
                                                    placeholder="Sewing Quantity">
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

   
<script>
    $(document).ready(function () {
        // Handle production plan selection and fetch color sizes and quantities
        $('input[name="production_plan"]').on('change', function () {
            var production_plan = $(this).val();
            var job_no = $('input[name="job_no"]').val();

            $.ajax({
                url: "{{ route('get_color_sizes_qties') }}",
                type: 'GET',
                data: {
                    job_no: job_no,
                    production_plan: production_plan
                },
                success: function (data) {
                    $('#colorWayTable tbody').empty();
                    console.log(data);

                    // Populate color sizes and quantities
                    $.each(data.color_sizes_qties, function (index, color) {
                        var row = `<tr>
                            <input type="hidden" name="color_id[]" value="${color.id}">
                            <td>
                                <input type="text" name="color[]" class="form-control" value="${color.color}" readonly>
                            </td>
                            <td>
                                <input type="text" name="size[]" class="form-control" value="${color.size}" readonly>
                            </td>
                            <td>
                                ${color.color_quantity}
                            </td>
                            <td>
                                <input type="number" name="color_quantity[]" class="form-control color-quantity" value="${color.color_quantity}" readonly>
                            </td>
                            <td>
                                <input type="number" name="sewing_quantity[]" class="form-control sewing-quantity" placeholder="Sewing Quantity">
                            </td>
                        </tr>`;
                        $('#colorWayTable tbody').append(row);
                    });

                    // Rebind event handlers for dynamically added rows
                    $('.sewing-quantity').on('input', function () {
                        calculateTotals();
                    });
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching data:", error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to fetch color sizes and quantities. Please try again.',
                    });
                }
            });
        });

        // Function to calculate totals and balances
        function calculateTotals() {
            let total_sewing_quantity = 0;
            let total_color_quantity = 0;
            let total_production_min = 0;

            $('.sewing-quantity').each(function () {
                total_sewing_quantity += $(this).val() === '' ? 0 : parseInt($(this).val());
            });

            $('.color-quantity').each(function () {
                total_color_quantity += $(this).val() === '' ? 0 : parseInt($(this).val());
            });



            let sewing_balance = total_color_quantity - total_sewing_quantity;
            let target_smv = $('#target_smv').val() === '' ? 0 : parseFloat($('#target_smv').val());
            
            total_production_min = (total_color_quantity * target_smv).toFixed(2);
            let production_min = (sewing_balance * target_smv).toFixed(2);
            let production_min_balance = total_production_min- total_sewing_quantity;

            $('#total_sewing_balance').val(total_sewing_quantity);
            $('#sewing_balance').val(sewing_balance);
            $('#production_min_balance').val(production_min_balance.toFixed(2));
            $('#Total_Production_Min').val(total_production_min);
        }

        // Recalculate balances on changes to target SMV or sewing quantities
        $('#target_smv').on('input', function () {
            calculateTotals();
        });

        // Initialize calculations
        calculateTotals();
    });
</script>


 


</x-backend.layouts.master> --}}

<x-backend.layouts.master>
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

            <!-- Required hidden fields -->
            <input type="hidden" name="job_no" value="{{ $jobs_no }}">
            <input type="hidden" name="production_plan" value="{{ request('production_plan', now()->format('Y-m')) }}">
            <input type="hidden" name="created_by" value="{{ auth()->user()->id }}">
            <input type="hidden" name="division_id" value="2">
            <input type="hidden" name="division_name" value="Factory">
            <input type="hidden" name="company_id" value="3">
            <input type="hidden" name="company_name" value="FAL - Factory">

            <table class="table">
                <tbody>
                    <!-- Other basic info rows omitted for brevity -->
                    <tr>
                        <td class="create_label_column">Target SMV</td>
                        <td class="create_input_column">
                            <input type="number" step="0.01" id="target_smv" class="form-control" readonly
                                   value="{{ $basic_info->target_smv }}">
                        </td>
                        <td class="create_label_column">Total Sewing Balance</td>
                        <td class="create_input_column">
                            <input type="number" id="total_sewing_balance" class="form-control" readonly>
                        </td>
                        <td class="create_label_column">Sewing Balance</td>
                        <td class="create_input_column">
                            <input type="number" id="sewing_balance" class="form-control" readonly>
                        </td>
                        <td class="create_label_column">Total Production Min</td>
                        <td class="create_input_column">
                            <input type="number" id="Total_Production_Min" class="form-control" readonly>
                        </td>
                        <td class="create_label_column">Production Min Balance</td>
                        <td class="create_input_column">
                            <input type="number" name="production_min_balance" id="production_min_balance" class="form-control" readonly>
                        </td>
                    </tr>
                </tbody>
            </table>

            <table class="table table-bordered text-center" id="colorWayTable">
                <thead>
                    <tr>
                        <th>Color</th>
                        <th>Size</th>
                        <th>Plan Qty</th>
                        <th>Old Sewn</th>
                        <th>Remaining</th>
                        <th>New Sewing</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($color_sizes_qties as $color)
                    @php
                        $old = $old_sewing_balances
                                   ->where('color',$color->color)
                                   ->where('size',$color->size)
                                   ->sum('sewing_balance');
                        $remain = $color->color_quantity - $old;
                    @endphp
                    <tr>
                        <input type="hidden" name="color_id[]" class="old-balance" value="{{ $old }}">

                        <td>
                            <input type="text" name="color[]" class="form-control" readonly value="{{ $color->color }}">
                        </td>
                        <td>
                            <input type="text" name="size[]" class="form-control" readonly value="{{ $color->size }}">
                        </td>
                        <td>
                            <input type="number" name="plan_quantity[]" class="form-control plan-quantity" readonly value="{{ $color->color_quantity }}">
                        </td>
                        <td>
                            <input type="number" class="form-control old-sewn" readonly value="{{ $old }}">
                        </td>
                        <td>
                            <input type="number" class="form-control initial-remain" readonly value="{{ $remain }}">
                        </td>
                        <td>
                            <input type="number" name="sewing_quantity[]" class="form-control new-sewing" placeholder="Enter qty">
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="mt-3">
                <a href="{{ route('jobs.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Cancel
                </a>
                <button type="submit" class="btn btn-outline-success">
                    <i class="fas fa-save"></i> Update Balance
                </button>
            </div>
        </form>
    </div>

    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    $(function(){
        $('#colorWayTable').on('input', '.new-sewing', calculateTotals);

        function calculateTotals(){
            let oldTotal  = 0;
            let newTotal  = 0;
            let planTotal = 0;

            $('.old-balance').each(function(){ oldTotal += +$(this).val() || 0; });
            $('.new-sewing').each(function(){ newTotal += +$(this).val() || 0; });
            $('.plan-quantity').each(function(){ planTotal += +$(this).val() || 0; });

            const totalSewn   = oldTotal + newTotal;
            const remaining   = planTotal - totalSewn;
            const smv         = parseFloat($('#target_smv').val()) || 0;
            const totalMin    = (planTotal * smv).toFixed(2);
            const remainMin   = (remaining * smv).toFixed(2);

            $('#total_sewing_balance').val(totalSewn);
            $('#sewing_balance').val(remaining);
            $('#Total_Production_Min').val(totalMin);
            $('#production_min_balance').val(remainMin);
        }

        $('#target_smv').on('input', calculateTotals);
        calculateTotals();
    });
    </script>
</x-backend.layouts.master>