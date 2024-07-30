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
        <h1 class="text-center">Update Shipment Balance</h1>
        <form action="{{ route('shipments_store', $jobs_no) }}" method="POST">
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
                                        <td class="create_label_column">Shipment Quantity</td>
                                        <td class="create_input_column">
                                            <input type="number" name="total_shipped_qty" class="form-control"
                                                placeholder="Shipment Balance"
                                                value="{{ $basic_info->total_shipped_qty }}" required readonly
                                                id="total_shipped_qty">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="create_label_column">Ex Factory Date</td>
                                        <td class="create_input_column">
                                            <input type="date" name="ex_factory_date" class="form-control"
                                                placeholder="Ex Factory Date" required max="{{ date('dd-MM-yyyy') }}">

                                        </td>
                                        <td class="create_label_column">Unite Price</td>
                                        <td class="create_input_column">
                                            <input type="number" step="0.01" name="unit_price" class="form-control"
                                                placeholder="Unite Price" value="{{ $basic_info->unit_price }}" required
                                                readonly id="unit_price">
                                        </td>
                                        <td class="create_label_column">Shipment Value</td>
                                        <td class="create_input_column">
                                            <input type="number" step="0.01" name="shipped_value"
                                                class="form-control" placeholder="Shipment Value" required
                                                readonly id="shipped_value">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="table table-bordered mt-2 text-center" id="colorWayTable">
                                <thead>
                                    <tr>
                                        <th>Color</th>
                                        <th>Size</th>
                                        <th>Sewing Quantity</th>
                                        <th>Shipment Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($color_sizes_qties as $color)
                                    {{-- @dd($color) --}}
                                        <tr>
                                            <input type="hidden" name="job_id[]" value="{{ $color->job_id }}">
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

                                                    $sewing_qty = $old_shipments_entries
                                                        ->where('job_id', $color->job_id)
                                                        ->sum('shipped_qty');
                                                    if ($sewing_qty > 0) {
                                                        $total_sewing_qty = $sewing_qty;
                                                    } else {
                                                        $total_sewing_qty = 0;
                                                    }
                                                    $remain_qty = $color->total_sewing_balance - $total_sewing_qty;
                                                @endphp
                                                <input type="number" name="color_quantity[]" class="form-control"
                                                    placeholder="Quantity" value="{{ $remain_qty }}" readonly>
                                            </td>
                                            <td>
                                                <input type="number" name="shipped_qty[]"
                                                    class="form-control shipped_qty" placeholder="Shipment Quantity">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{-- $table->unsignedBigInteger('')->nullable();
                            $table->string('job_no')->nullable();
                            $table->decimal('', 8, 2)->nullable();
                            $table->date('')->nullable();
                            $table->decimal('shipped_value', 8, 2)->nullable();
                            $table->decimal('excess_short_shipment_qty')->nullable();
                            $table->decimal('excess_short_shipment_value', 8, 2)->nullable();
                            $table->string('delivery_status')->nullable(); --}}
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

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#style-select').select2();
            $('#po-select').select2();
            $('#department-select').select2();

            calculateSewingBalance();
            calculateProductionMinBalance();

            $('.shipped_qty').on('input', function() {
                //if sewing quantity is empty, set it to 0 to avoid NaN and if sewing quantity is greater than color quantity, set it to color quantity
                if (parseInt($(this).val()) > parseInt($(this).parent().prev().children().val())) {
                    $(this).val($(this).parent().prev().children().val());
                }
                calculateSewingBalance();
                calculateProductionMinBalance();
            });



            function calculateSewingBalance() {
                var total_sewing_quantity = 0;
                $('.shipped_qty').each(function() {
                    total_sewing_quantity += $(this).val() === '' ? 0 : parseInt($(this).val());
                });
                $('#total_shipped_qty').val(total_sewing_quantity);
            }

            function calculateProductionMinBalance() {
                var total_shipped_qty = $('#total_shipped_qty').val();
                var unit_price = $('#unit_price').val();
                var shipped_value = total_shipped_qty * unit_price;
                shipped_value = shipped_value.toFixed(2);
                $('#shipped_value').val(shipped_value);
            }
        });
    </script>
</x-backend.layouts.master>
