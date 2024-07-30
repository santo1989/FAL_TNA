<x-backend.layouts.master>
    <style>
        .create_label_column {
            width: 5%;
        }

        .create_input_column {
            width: 7.5%;
        }

        .label_large {
            width: 20%;
        }

        .input_large {
            width: 30%;
        }

        table {
            font-size: 0.8rem;
        }

        input::placeholder,
        [type="date"] {
            font-size: 0.8rem;
        }

        .button-container {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }
    </style>
    <div class="card mx-5 my-5" style="background-color: white;">
        <h3 class="text-center p-1">Sewing Entry</h3>
        @if (session('message'))
            <div class="alert alert-success">
                <span class="close" data-dismiss="alert">&times;</span>
                <strong>{{ session('message') }}.</strong>
            </div>
        @endif

        <x-backend.layouts.elements.errors />
        <div class="row p-1">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('shipments.store') }}" enctype="multipart/form-data">
                            @csrf

                            <table class="table pb-1">
                                <tbody>
                                    <tr>
                                        <td class="create_label_column">Job No:</td>
                                        <td class="create_input_column">
                                            {{ $shipments_entry->job_no }}
                                            <input type="hidden" name="job_no" value="{{ $shipments_entry->job_no }}">
                                        <td class="create_label_column">Buyer</td>
                                        <td class="create_input_column">
                                            {{ $shipments_entry->buyer }}
                                        </td>
                                        <td class="create_label_column">Style</td>
                                        <td class="create_input_column">
                                            {{ $shipments_entry->style }}
                                        </td>
                                        <td class="create_label_column">PO</td>
                                        <td class="create_input_column">
                                            {{ $shipments_entry->po }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="create_label_column">Department</td>
                                        <td class="create_input_column">
                                            {{ $shipments_entry->department }}
                                        </td>



                                        <td class="create_label_column">Item</td>
                                        <td class="create_input_column">
                                            {{ $shipments_entry->item }}
                                        </td>
                                        <td class="create_label_column">Destination</td>
                                        <td class="create_input_column">
                                            {{ $shipments_entry->destination }}
                                        </td>
                                        <td class="create_label_column">Color</td>
                                        <td class="create_input_column">
                                            {{ $shipments_entry->color }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="create_label_column">Order Quantity</td>
                                        <td class="create_input_column">
                                            <input type="number" name="order_quantity" class="form-control"
                                                placeholder="Order Quantity"
                                                value="{{ $shipments_entry->order_quantity }}" readonly>
                                        </td>
                                        <td class="create_label_column">Unit Price</td>
                                        <td class="create_input_column">
                                            <input type="number" step="0.01" name="unit_price" class="form-control"
                                                placeholder="Unit Price" value="{{ $shipments_entry->unit_price }}"
                                                readonly>
                                        </td>
                                        <td class="create_label_column">Total Value</td>
                                        <td class="create_input_column">
                                            <input type="number" step="0.01" name="total_value" class="form-control"
                                                placeholder="Total Value" required readonly
                                                value="{{ $shipments_entry->total_value }}">
                                        </td>

                                        <td class="create_label_column">Order Received Date</td>
                                        <td class="create_input_column">
                                            <input type="date" name="order_received_date" class="form-control"
                                                required {{ $shipments_entry->order_received_date ? 'readonly' : '' }}>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="table">
                                <tbody>
                                    <tr>
                                         {{--  <td class="create_label_column">Sewing Balance</td>
                                        <td class="create_input_column">
                                            <input type="number" name="sewing_balance" class="form-control"
                                                placeholder="Sewing Balance">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="create_label_column">Production Plan</td>
                                        <td class="create_input_column">
                                            <input type="month" name="production_plan" class="form-control"
                                                placeholder="Production Plan">
                                        </td> --}}
                                        <td class="label_large">Shipped Quantity</td>
                                        <td class="input_large">
                                            <input type="number" name="shipped_qty" class="form-control"
                                                placeholder="Shipped Quantity">
                                        </td>
                                        {{-- <td class="create_label_column">Production Min Balance</td>
                                        <td class="create_input_column">
                                            <input type="number" step="0.01" name="production_min_balance"
                                                class="form-control" placeholder="Production Min Balance" required
                                                readonly>
                                            <script>
                                                //production_min_balance = sewing_balance * target_smv
                                                document.querySelector('input[name="sewing_balance"]').addEventListener('input', function() {
                                                    var sewing_balance = document.querySelector('input[name="sewing_balance"]').value;
                                                    var target_smv = document.querySelector('input[name="target_smv"]').value;
                                                    var production_min_balance = sewing_balance * target_smv;
                                                    document.querySelector('input[name="production_min_balance"]').value = production_min_balance;
                                                });

                                                document.querySelector('input[name="target_smv"]').addEventListener('input', function() {
                                                    var sewing_balance = document.querySelector('input[name="sewing_balance"]').value;
                                                    var target_smv = document.querySelector('input[name="target_smv"]').value;
                                                    var production_min_balance = sewing_balance * target_smv;
                                                    document.querySelector('input[name="production_min_balance"]').value = production_min_balance;
                                                });
                                            </script>
                                        </td> --}}
                                        <td class="label_large">Ex-Factory Date</td>
                                        <td class="input_large">
                                            <input type="date" name="ex_factory_date" class="form-control">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label_large">Shipped Value</td>
                                        <td class="input_large">
                                            <input type="number" step="0.01" name="shipped_value"
                                                class="form-control" placeholder="Shipped Value" required readonly>
                                            <script>
                                                //shipped_value = shipped_qty * unit_price
                                                document.querySelector('input[name="shipped_qty"]').addEventListener('input', function() {
                                                    var shipped_qty = document.querySelector('input[name="shipped_qty"]').value;
                                                    var unit_price = document.querySelector('input[name="unit_price"]').value;
                                                    var shipped_value = shipped_qty * unit_price;
                                                    document.querySelector('input[name="shipped_value"]').value = shipped_value;
                                                });

                                                document.querySelector('input[name="unit_price"]').addEventListener('input', function() {
                                                    var shipped_qty = document.querySelector('input[name="shipped_qty"]').value;
                                                    var unit_price = document.querySelector('input[name="unit_price"]').value;
                                                    var shipped_value = shipped_qty * unit_price;
                                                    document.querySelector('input[name="shipped_value"]').value = shipped_value;
                                                });
                                            </script>
                                        </td>

                                        <td class="label_large">Excess/Short Shipment Qty</td>
                                        <td class="input_large">
                                            <input type="number" name="excess_short_shipment_qty" class="form-control"
                                                placeholder="Excess/Short Shipment Qty" required readonly>
                                            <script>
                                                //excess_short_shipment_qty = shipped_qty - order_quantity
                                                document.querySelector('input[name="shipped_qty"]').addEventListener('input', function() {
                                                    var shipped_qty = document.querySelector('input[name="shipped_qty"]').value;
                                                    var order_quantity = document.querySelector('input[name="order_quantity"]').value;
                                                    var excess_short_shipment_qty = shipped_qty - order_quantity;
                                                    document.querySelector('input[name="excess_short_shipment_qty"]').value = excess_short_shipment_qty;
                                                });

                                                document.querySelector('input[name="order_quantity"]').addEventListener('input', function() {
                                                    var shipped_qty = document.querySelector('input[name="shipped_qty"]').value;
                                                    var order_quantity = document.querySelector('input[name="order_quantity"]').value;
                                                    var excess_short_shipment_qty = shipped_qty - order_quantity;
                                                    document.querySelector('input[name="excess_short_shipment_qty"]').value = excess_short_shipment_qty;
                                                });
                                            </script>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label_large">Excess/Short Shipment Value</td>
                                        <td class="input_large">
                                            <input type="number" step="0.01" name="excess_short_shipment_value"
                                                class="form-control" placeholder="Excess/Short Shipment Value" required
                                                readonly>
                                            <script>
                                                //excess_short_shipment_value = excess_short_shipment_qty * unit_price
                                                document.querySelector('input[name="excess_short_shipment_qty"]').addEventListener('input', function() {
                                                    var excess_short_shipment_qty = document.querySelector('input[name="excess_short_shipment_qty"]').value;
                                                    var unit_price = document.querySelector('input[name="unit_price"]').value;
                                                    var excess_short_shipment_value = excess_short_shipment_qty * unit_price;
                                                    document.querySelector('input[name="excess_short_shipment_value"]').value = excess_short_shipment_value;
                                                });

                                                document.querySelector('input[name="unit_price"]').addEventListener('input', function() {
                                                    var excess_short_shipment_qty = document.querySelector('input[name="excess_short_shipment_qty"]').value;
                                                    var unit_price = document.querySelector('input[name="unit_price"]').value;
                                                    var excess_short_shipment_value = excess_short_shipment_qty * unit_price;
                                                    document.querySelector('input[name="excess_short_shipment_value"]').value = excess_short_shipment_value;
                                                });
                                            </script>
                                        </td>

                                        <td class="label_large">Delivery Status</td>
                                        <td class="input_large">
                                            <input type="text" name="delivery_status" class="form-control"
                                                placeholder="Delivery Status">

                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="button-container">
                                <button type="submit" id="saveButton" class="btn btn-outline-success">
                                    <i class="fas fa-save"></i> Save
                                </button>
                                <a href="{{ route('jobs.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelector('input[name="order_quantity"]').addEventListener('input', function() {
            var order_quantity = document.querySelector('input[name="order_quantity"]').value;
            var unit_price = document.querySelector('input[name="unit_price"]').value;
            var total_value = order_quantity * unit_price;
            document.querySelector('input[name="total_value"]').value = total_value;
        });

        document.querySelector('input[name="unit_price"]').addEventListener('input', function() {
            var order_quantity = document.querySelector('input[name="order_quantity"]').value;
            var unit_price = document.querySelector('input[name="unit_price"]').value;
            var total_value = order_quantity * unit_price;
            document.querySelector('input[name="total_value"]').value = total_value;
        });

        document.querySelector('input[name="shipped_qty"]').addEventListener('input', function() {
            var shipped_qty = document.querySelector('input[name="shipped_qty"]').value;
            var unit_price = document.querySelector('input[name="unit_price"]').value;
            var shipped_value = shipped_qty * unit_price;
            document.querySelector('input[name="shipped_value"]').value = shipped_value;
        });

        document.querySelector('input[name="unit_price"]').addEventListener('input', function() {
            var shipped_qty = document.querySelector('input[name="shipped_qty"]').value;
            var unit_price = document.querySelector('input[name="unit_price"]').value;
            var shipped_value = shipped_qty * unit_price;
            document.querySelector('input[name="shipped_value"]').value = shipped_value;
        });

        document.querySelector('input[name="shipped_qty"]').addEventListener('input', function() {
            var shipped_qty = document.querySelector('input[name="shipped_qty"]').value;
            var order_quantity = document.querySelector('input[name="order_quantity"]').value;
            var excess_short_shipment_qty = shipped_qty - order_quantity;
            document.querySelector('input[name="excess_short_shipment_qty"]').value = excess_short_shipment_qty;
        });

        document.querySelector('input[name="order_quantity"]').addEventListener('input', function() {
            var shipped_qty = document.querySelector('input[name="shipped_qty"]').value;
            var order_quantity = document.querySelector('input[name="order_quantity"]').value;
            var excess_short_shipment_qty = shipped_qty - order_quantity;
            document.querySelector('input[name="excess_short_shipment_qty"]').value = excess_short_shipment_qty;
        });

        document.querySelector('input[name="excess_short_shipment_qty"]').addEventListener('input', function() {
            var excess_short_shipment_qty = document.querySelector('input[name="excess_short_shipment_qty"]').value;
            var unit_price = document.querySelector('input[name="unit_price"]').value;
            var excess_short_shipment_value = excess_short_shipment_qty * unit_price;
            document.querySelector('input[name="excess_short_shipment_value"]').value = excess_short_shipment_value;
        });

        document.querySelector('input[name="unit_price"]').addEventListener('input', function() {
            var excess_short_shipment_qty = document.querySelector('input[name="excess_short_shipment_qty"]').value;
            var unit_price = document.querySelector('input[name="unit_price"]').value;
            var excess_short_shipment_value = excess_short_shipment_qty * unit_price;
            document.querySelector('input[name="excess_short_shipment_value"]').value = excess_short_shipment_value;
        });
    </script>
</x-backend.layouts.master>
