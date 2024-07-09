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
                                                 placeholder="Must be Full Style Number" required>
                                         </td>
                                     </tr>
                                     <tr>
                                         <td class="create_label_column">PO Number</td>
                                         <td class="create_input_column">
                                             <input type="text" name="po" id="po" class="form-control"
                                                 placeholder="Must be Full PO Number" required>
                                         </td>
                                         {{-- <td class="create_label_column">Picture</td>
                                         <td class="create_input_column">
                                             <input type="file" name="picture" id="picture" class="form-control">
                                         </td> --}}
                                     {{-- </tr>
                                     <tr> --}}
                                         <td class="create_label_column">Item</td>
                                         <td class="create_input_column">
                                           
                                             <select id="item" name="item" class="form-control" required>
                                                 <option value="">Select Item</option>
                                                 <option value="T-shirt">T-shirt</option>
                                                 <option value="Polo Shirt">Polo Shirt</option>
                                                 <option value="Romper">Romper</option>
                                                 <option value="Sweat Shirt">Sweat Shirt</option>
                                                 <option value="Jacket">Jacket</option>
                                                 <option value="Hoodie">Hoodie</option>
                                                 <option value="Jogger">Jogger</option>
                                                 <option value="Pant/Bottom">Pant/Bottom</option>
                                                 <option value="Cargo Pant">Cargo Pant</option>
                                                 <option value="Leggings">Leggings</option>
                                                 <option value="Ladies/Girls Dress">Ladies/Girls Dress</option>
                                                 <option value="Others">Others</option>
                                             </select>
                                         </td>

                                         {{-- <td class="create_label_column">Color</td>
                                         <td class="create_input_column">
                                             <input type="text" name="color" id="color" class="form-control"
                                                 >
                                         </td> --}}
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
                                         <td class="create_label_column">Total Lead Time: </td>
                                         <td class="create_input_column">
                                             <input type="text" name="total_lead_time" id="total_lead_time"
                                                 class="form-control" required readonly>
                                         </td>
                                     </tr>
                                     <script>
                                         $(document).ready(function() {
                                             $('#po_receive_date').change(function() {
                                                 var po_receive_date = new Date($('#po_receive_date').val());
                                                 var shipment_etd = new Date($('#shipment_etd').val());
                                                 var diffTime = Math.abs(shipment_etd - po_receive_date);
                                                 var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                                                 $('#total_lead_time').val(diffDays);
                                             });
                                             $('#shipment_etd').change(function() {
                                                 var po_receive_date = new Date($('#po_receive_date').val());
                                                 var shipment_etd = new Date($('#shipment_etd').val());
                                                 var diffTime = Math.abs(shipment_etd - po_receive_date);
                                                 var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                                                 $('#total_lead_time').val(diffDays);
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
