 <x-backend.layouts.master>
     <style>
         .create_label_column {
             width: 20%;
         }

         .create_input_column {
             width: 30%;
         }

         table {
             font-size: 0.8rem;
         }

         input::placeholder,[type="date"] {
             font-size: 0.8rem;
         }

         .button-container {
             display: flex;
             justify-content: flex-end;
             gap: 10px;
             margin-top: 20px;
         }
     </style>
     <div class="card mx-5 my-5" style="background-color: white; ">
         <h3 class="text-center p-1">TNA Create</h3>
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
                         <form method="POST" action="{{ route('tnas.store') }}" enctype="multipart/form-data">
                             @csrf
                             <table class="table">


                                 <tbody>
                                     <tr>
                                         <td class="create_label_column">Buyer</td>
                                         <td class="create_input_column">
                                             <select name="buyer_id" id="buyer_id" class="form-control" required>
                                                 <option value="">Select Buyer</option>
                                                 @foreach ($buyers as $buyer)
                                                     <option value="{{ $buyer->id }}">{{ $buyer->name }}</option>
                                                 @endforeach
                                             </select>
                                         </td>
                                         <td class="create_label_column">Style</td>
                                         <td class="create_input_column">
                                             <input type="text" name="style" id="style" class="form-control"
                                                 required>
                                         </td>
                                     </tr>
                                     <tr>
                                         <td class="create_label_column">PO Number</td>
                                         <td class="create_input_column">
                                             <input type="text" name="po" id="po" class="form-control"
                                                 required>
                                         </td>
                                         <td class="create_label_column">Picture</td>
                                         <td class="create_input_column">
                                             <input type="file" name="picture" id="picture" class="form-control"
                                                 required>
                                         </td>
                                     </tr>
                                     <tr>
                                         <td class="create_label_column">Item</td>
                                         <td class="create_input_column">
                                             <input type="text" name="item" id="item" class="form-control"
                                                 required>
                                         </td>

                                         <td class="create_label_column">Color</td>
                                         <td class="create_input_column">
                                             <input type="text" name="color" id="color" class="form-control"
                                                 required>
                                         </td>
                                     </tr>
                                     <tr>
                                         <td class="create_label_column">Qty (pcs)</td>
                                         <td class="create_input_column">
                                             <input type="number" name="qty_pcs" id="qty_pcs" class="form-control"
                                                 required>
                                         </td>

                                         <td class="create_label_column">PO Recieve Date</td>
                                         <td class="create_input_column">
                                             <input type="date" name="po_receive_date" id="po_receive_date"
                                                 class="form-control" required>
                                         </td>
                                     </tr>
                                     <tr>
                                         <td class="create_label_column">Shipment/ ETD</td>
                                         <td class="create_input_column">
                                             <input type="date" name="shipment_etd" id="shipment_etd"
                                                 class="form-control" required>
                                         </td>
                                         <td class="create_label_column">Total Lead Time: <label
                                                 id="total_lead_time"></label>
                                         <td class="create_input_column">
                                             Order Free Time : <label id="order_free_time"></label>
                                         </td>
                                     </tr>
                                     <script>
                                         $(document).ready(function() {
                                             $('#po_receive_date').change(function() {
                                                 var po_receive_date = new Date($('#po_receive_date').val());
                                                 var shipment_etd = new Date($('#shipment_etd').val());
                                                 var diffTime = Math.abs(shipment_etd - po_receive_date);
                                                 var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                                                 $('#total_lead_time').text(diffDays + ' Days');
                                                 var order_free_time = diffDays - 30;
                                                 $('#order_free_time').text(order_free_time + ' Days');
                                             });
                                             $('#shipment_etd').change(function() {
                                                 var po_receive_date = new Date($('#po_receive_date').val());
                                                 var shipment_etd = new Date($('#shipment_etd').val());
                                                 var diffTime = Math.abs(shipment_etd - po_receive_date);
                                                 var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                                                 $('#total_lead_time').text(diffDays + ' Days');
                                                 var order_free_time = diffDays - 30;
                                                 $('#order_free_time').text(order_free_time + ' Days');
                                             });
                                         });
                                     </script>
                                 </tbody>
                             </table>

                             <div class="button-container">
                                 <button type="submit" id="saveButton" class="btn btn-outline-success">
                                     <i class="fas fa-save"></i> Save
                                 </button>
                                 <a href="{{ route('tnas.index') }}" class="btn btn-outline-secondary">
                                     <i class="fas fa-arrow-left"></i> Cancel
                                 </a>
                             </div>
                         </form>
                     </div>
                 </div>
             </div>
         </div>
 </x-backend.layouts.master>
