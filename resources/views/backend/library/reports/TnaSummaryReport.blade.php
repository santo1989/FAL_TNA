<x-backend.layouts.master>
    <div class="card mx-5 my-5" style="background-color: white; overflow-x: auto;">
        <div class="container-fluid pt-2">
            <h4 class="text-center">TNA Buyer, Shipment, Closing Wise Summary Report</h4>

            <div class="col-md-12">
                <!-- Back Button -->
                <a href="{{ route('tnas.index') }}" class="btn btn-outline-secondary mb-3">
                    <i class="fas fa-arrow-left"></i> Close
                </a>
                <!-- Excel Download Buttons -->
                <button id="downloadExcel" class="btn btn-outline-info mb-3">
                    <i class="fas fa-file-excel"></i> Download Excel
                </button>

                <!-- Reset Button -->
                <a href="{{ route('TnaSummaryReport') }}" class="btn btn-outline-danger mb-3">
                    <i class="fas fa-undo"></i> Reset</a>


                <!-- Filter search by buyer or shipment date range -->
                <form action="{{ route('TnaSummaryReport') }}" method="GET">
                    <div class="row justify-content-between">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="buyer_id">Buyer</label>
                                <select name="buyer_id" id="buyer_id" class="form-control">
                                    <option value="">Select Buyer</option>
                                    @php
                                        $buyers = App\Models\TNA::where('order_close', '0')->select('buyer_id', 'buyer')->distinct()->get();
                                    @endphp
                                    @foreach ($buyers as $buyer)
                                        <option  value="{{ $buyer->buyer_id }}" {{ $buyer_id == $buyer->buyer_id ? 'selected' : '' }}>
                                            {{ $buyer->buyer }}
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="start_date">Start Date</label>
                                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $start_date }}">
                            </div>
                        </div>
                            <div class="col-md-3">
                            <div class="form-group">
                                <label for="end_date">End Date</label>
                                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $end_date }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mt-4">
                                <!--close or open order select option in checkbox-->
                                <input type="checkbox" name="order_close" id="order_close" value="1" {{ $order_close == '1' ? 'checked' : '' }}>
                                <label for="order_close">Close Order</label>


                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary mt-2">Search</button>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Table for Data Display -->
                <table class="table table-bordered table-hover text-center text-wrap" style="font-size: 12px;">
                    <thead class="thead-dark">
                        <tr>
                            <th>Buyer</th>
                            @foreach ($columns as $column => $label)
                                <th>{{ $label }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody id="buyer-summary-body">
                        @foreach ($tna_summary as $row)
                            <tr>
                                <td>{{ $row->buyer }}</td>

                                @foreach ($columns as $column => $label)
                                    <td>
                                        @if ($column == 'total_order_qty')
                                            {{ $row->$column }}
                                        @elseif ($column == 'total_distinct_styles')
                                            {{ $row->$column }}
                                        @elseif ($column == 'styles')
                                            
                                            <!-- Separate styles with commas and if same style is repeated, show only once -->
                                            @if ($row->$column)
                                                @php
                                                    $styles = explode(',', $row->$column);
                                                    $distinct_styles = array_unique($styles);
                                                @endphp
                                                {{ implode(', ', $distinct_styles) }}
                                                
                                            @else
                                                N/A
                                                
                                            @endif
                                           


                                        @elseif ($column == 'total_distinct_pos')
                                            {{ $row->$column }}
                                        @elseif ($column == 'po')
                                            <!-- Separate POs with commas and if same PO is repeated, show only once -->
                                            @if ($row->$column)
                                                @php
                                                    $pos = explode(',', $row->$column);
                                                    $distinct_pos = array_unique($pos);
                                                @endphp
                                                {{ implode(', ', $distinct_pos) }}
                                            @else
                                                N/A
                                            @endif
                                        @elseif ($column == 'total_distinct_items')
                                            {{ $row->$column }}
                                        @elseif ($column == 'items')
                                            <!-- Separate items with commas and if same item is repeated, show only once -->
                                            @if ($row->$column)
                                                @php
                                                    $items = explode(',', $row->$column);
                                                    $distinct_items = array_unique($items);
                                                @endphp
                                                {{ implode(', ', $distinct_items) }}
                                            @else
                                                N/A
                                            @endif
                                        @elseif ($column == 'total_distinct_shipment_dates')
                                            {{ $row->$column }}
                                        @elseif ($column == 'shipment_dates')
                                            <!-- Separate shipment dates with commas and if same date is repeated, show only once -->
                                            @if ($row->$column)
                                                @php
                                                    $dates = explode(',', $row->$column);
                                                    $distinct_dates = array_unique($dates);
                                                @endphp
                                                {{ implode(', ', $distinct_dates) }}
                                            @else
                                                N/A
                                            @endif
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                        <tr>
                            <td><b>Total</b></td>
                            @foreach ($columns as $column => $label)
                                <td>
                                    @if (isset($total_summary[$column]))
                                        @if ($column == 'total_order_qty')
                                            {{ $total_summary[$column] }}
                                        @elseif ($column == 'total_distinct_styles')
                                            {{ $total_summary[$column] }}
                                        @elseif ($column == 'total_distinct_pos')
                                            {{ $total_summary[$column] }}
                                        @elseif ($column == 'total_distinct_items')
                                            {{ $total_summary[$column] }}
                                        @elseif ($column == 'total_distinct_shipment_dates')
                                            {{ $total_summary[$column] }}
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </td>
                            @endforeach
                        </tr>

                    </tbody>
                </table>

            </div>
        </div>

        <!-- Include JS Libraries -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

        <!-- JavaScript for Client-Side Excel Download -->
        <script>
            document.getElementById('downloadExcel').addEventListener('click', function() {
                // Convert table to worksheet
                const table = document.querySelector('.table');
                const worksheet = XLSX.utils.table_to_sheet(table);

                // Create a workbook and add the worksheet
                const workbook = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(workbook, worksheet, 'TnaSummary');

                // Trigger the download
                XLSX.writeFile(workbook, 'TnaSummary.xlsx');
            });
        </script>
</x-backend.layouts.master>
