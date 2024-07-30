 <x-backend.layouts.master>
     <div class="card mx-5 my-5" style="background-color: white; ">
         <div class="container-fluid pt-2">
             <h4 class="text-center">Monthly Order Summary</h4>
             <div class="row justify-content-center pb-2">
                 <div class="col-12">
                     <a href="{{ route('jobs.index') }}" class="btn btn-outline-secondary">
                         <i class="fas fa-arrow-left"></i> Close </a>
                     <button class="btn btn-outline-secondary" id="all-buyers-btn">All Buyers</button>
                     @foreach ($buyers as $buyer)
                         <button class="btn btn-outline-primary buyer-btn"
                             data-buyer="{{ $buyer->buyer }}">{{ $buyer->buyer }}</button>
                     @endforeach
                 </div>
             </div>
             {{-- <table class="table table-bordered table-hover text-center" style="font-size: 12px;" id="summaryTable">
                 <thead class="thead-dark">
                     <tr>
                         <th>Buyer</th>
                         <th>Number of Orders</th>
                         <th>Order Qty</th>
                         <th>Sewing Balance</th>
                         <th>Avg. SMV</th>
                         <th>Produced Min</th>
                         <th>Production Balance</th>
                         <th>Booking%</th>
                         <th>Avg. Unit Price</th>
                         <th>Total Value</th>
                         <th>Avg. CM/Pcs</th>
                         <th>Total CM</th>
                     </tr>
                 </thead>
                 <tbody id="summaryTableBody">
                     @foreach ($buyers as $buyer)
                         <tr data-buyer="{{ $buyer->buyer }}">
                             <td>{{ $buyer->buyer }}</td>
                             <td>{{ $buyer->number_of_orders }}</td>
                             <td>{{ $buyer->order_qty }}</td>
                             <td>{{ $buyer->sewing_balance }}</td>
                             <td>{{ number_format($buyer->avg_smv, 2) }}</td>
                             <td>{{ $buyer->produced_min }}</td>
                             <td>{{ $buyer->production_balance }}</td>
                             <td>{{ number_format($buyer->booking_percentage, 1) }}%</td>
                             <td>${{ number_format($buyer->avg_unit_price, 2) }}</td>
                             <td>${{ number_format($buyer->total_value, 2) }}</td>
                             <td>
                                @php
                                    $cm_pcs = $buyer->avg_cm_dzn/12;
                                @endphp
                                ${{ number_format($cm_pcs, 2) }}

                             </td>
                             <td>${{ number_format($buyer->total_cm, 2) }}</td>
                         </tr>
                     @endforeach
                 </tbody>
             </table> --}}
             <table class="table table-bordered table-hover text-center" style="font-size: 12px;" id="summaryTable">
                 <thead class="thead-dark">
                     <tr>
                         <th>Buyer</th>
                         <th>Number of Orders</th>
                         <th>Order Qty</th>
                         <th>Sewing Balance</th>
                         <th>Avg. SMV</th>
                         <th>Produced Min</th>
                         <th>Production Balance</th>
                         <th>Booking%</th>
                         <th>Avg. Unit Price</th>
                         <th>Total Value</th>
                         <th>Avg. CM/Pcs</th>
                         <th>Total CM</th>
                         <th>Shipped Qty</th>
                         <th>Shipment Balance</th>
                         <th>Excess/Short Qty</th>
                         <th>Shipped Value</th>
                         <th>Value Balance</th>
                         <th>Excess/Short Value</th>
                     </tr>
                 </thead>
                 <tbody id="summaryTableBody">
                     @foreach ($buyers as $buyer)
                         <tr data-buyer="{{ $buyer->buyer }}">
                             <td>{{ $buyer->buyer }}</td>
                             <td>{{ $buyer->number_of_orders }}</td>
                             <td>{{ $buyer->order_qty }}</td>
                             <td>{{ $buyer->sewing_balance }}</td>
                             <td>{{ number_format($buyer->avg_smv, 2) }}</td>
                             <td>{{ $buyer->produced_min }}</td>
                             <td>{{ $buyer->production_balance }}</td>
                             <td>{{ number_format($buyer->booking_percentage, 1) }}%</td>
                             <td>${{ number_format($buyer->avg_unit_price, 2) }}</td>
                             <td>${{ number_format($buyer->total_value, 2) }}</td>
                             <td>
                                 @php
                                     $cm_pcs = $buyer->avg_cm_dzn / 12;
                                 @endphp
                                 ${{ number_format($cm_pcs, 2) }}
                             </td>
                             <td>${{ number_format($buyer->total_cm, 2) }}</td>
                             <td>{{ $buyer->shipped_qty }}</td>
                             <td>{{ $buyer->shipment_balance }}</td>
                             <td>{{ $buyer->excess_short_qty }}</td>
                             <td>${{ number_format($buyer->shipped_value, 2) }}</td>
                             <td>${{ number_format($buyer->value_balance, 2) }}</td>
                             <td>${{ number_format($buyer->excess_short_value, 2) }}</td>
                         </tr>
                     @endforeach
                 </tbody>
             </table>

         </div>

         <script>
             $(document).ready(function() {
                 $(".buyer-btn").click(function() {
                     const buyer = $(this).data("buyer");
                     $("#summaryTableBody tr").hide();
                     $(`#summaryTableBody tr[data-buyer='${buyer}']`).show();
                 });

                 $("#all-buyers-btn").click(function() {
                     $("#summaryTableBody tr").show();
                 });
             });
         </script>
     </div>
 </x-backend.layouts.master>
