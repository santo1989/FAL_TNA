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

         input::value,[type="date"] {
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
         <h3 class="text-center p-1">Edit TNA list</h3>
         @if (session('message'))
             <div class="alert alert-success">
                 <span class="close" data-dismiss="alert">&times;</span>
                 <strong>{{ session('message') }}.</strong>
             </div>
         @endif

         <x-backend.layouts.elements.errors />
         <div class="row p-1">
             <div class="col-12 ">
                 <div class="card">
                     <div class="card-body">
                         <form method="POST" action="{{ route('tnas.update', $tnas->id) }}"
                             enctype="multipart/form-data">
                             @csrf
                             @method('PUT')
                             <table class="table">
                                <input type="hidden" name="tnas_id" value="{{ $tnas->id }}">
                                 <tbody>
                                     <tr>
                                         <td class="create_label_column">Buyer</td>
                                         <td class="create_input_column">
                                             <select name="buyer_id" id="buyer_id" class="form-control" required>
                                                 <option value="">Select Buyer</option>
                                                 @foreach ($buyers as $buyer)
                                                     <option value="{{ $buyer->id }}"
                                                         {{ $tnas->buyer_id == $buyer->id ? 'selected' : '' }}>
                                                         {{ $buyer->name }}</option>
                                                 @endforeach
                                             </select>
                                         </td>
                                         <td class="create_label_column">Style</td>
                                         <td class="create_input_column">
                                             <input type="text" name="style" id="style" class="form-control"
                                                 placeholder="Must be Full Style Number" required
                                                 value="{{ $tnas->style }}">
                                         </td>
                                     </tr>
                                     <tr>
                                         <td class="create_label_column">PO Number</td>
                                         <td class="create_input_column">
                                             <input type="text" name="po" id="po" class="form-control"
                                                 placeholder="Must be Full PO Number" required
                                                 value="{{ $tnas->po }}">
                                         </td>
                                         {{-- <td class="create_label_column">Picture</td>
                                         <td class="create_input_column">
                                             <input type="file" name="picture" id="picture" class="form-control"
                                                 value="{{ $tnas->picture }}">
                                         </td>
                                     </tr>
                                     <tr> --}}
                                         <td class="create_label_column">Item</td>
                                         <td class="create_input_column">

                                             <select id="item" name="item" class="form-control" required>
                                                 <option value="">Select Item</option>
                                                 <option value="T-shirt"
                                                     {{ $tnas->item == 'T-shirt' ? 'selected' : '' }}>
                                                     T-shirt</option>
                                                 <option value="Polo Shirt"
                                                     {{ $tnas->item == 'Polo Shirt' ? 'selected' : '' }}>
                                                     Polo Shirt</option>
                                                 <option value="Romper"
                                                     {{ $tnas->item == 'Romper' ? 'selected' : '' }}>
                                                     Romper</option>
                                                 <option value="Sweat Shirt"
                                                     {{ $tnas->item == 'Sweat Shirt' ? 'selected' : '' }}>
                                                     Sweat Shirt</option>
                                                 <option value="Jacket"
                                                     {{ $tnas->item == 'Jacket' ? 'selected' : '' }}>
                                                     Jacket</option>
                                                 <option value="Hoodie"
                                                     {{ $tnas->item == 'Hoodie' ? 'selected' : '' }}>
                                                     Hoodie</option>
                                                 <option value="Jogger"
                                                     {{ $tnas->item == 'Jogger' ? 'selected' : '' }}>
                                                     Jogger</option>
                                                 <option value="Pant/Bottom"
                                                     {{ $tnas->item == 'Pant/Bottom' ? 'selected' : '' }}>
                                                     Pant/Bottom</option>
                                                 <option value="Cargo Pant"
                                                     {{ $tnas->item == 'Cargo Pant' ? 'selected' : '' }}>
                                                     Cargo Pant</option>
                                                 <option value="Leggings"
                                                     {{ $tnas->item == 'Leggings' ? 'selected' : '' }}>
                                                     Leggings</option>
                                                 <option value="Ladies/Girls Dress"
                                                     {{ $tnas->item == 'Ladies/Girls Dress' ? 'selected' : '' }}>
                                                     Ladies/Girls Dress</option>
                                                 <option value="Others"
                                                     {{ $tnas->item == 'Others' ? 'selected' : '' }}>
                                                     Others</option>
                                             </select>
                                         </td>

                                         {{-- <td class="create_label_column">Color</td>
                                         <td class="create_input_column">
                                             <input type="text" name="color" id="color" class="form-control"
                                                  value="{{ $tnas->color }}">
                                         </td> --}}
                                     </tr>
                                     <tr>
                                         <td class="create_label_column">Qty (pcs)</td>
                                         <td class="create_input_column">
                                             <input type="number" name="qty_pcs" id="qty_pcs" class="form-control"
                                                 required value="{{ $tnas->qty_pcs }}">
                                         </td>

                                         <td class="create_label_column">PO Recieve Date</td>
                                         <td class="create_input_column">
                                             <input type="date" name="po_receive_date" id="po_receive_date"
                                                 class="form-control" required value="{{ $tnas->po_receive_date }}"
                                                 readonly>
                                         </td>
                                     </tr>
                                     <tr>
                                         <td class="create_label_column">Shipment/ ETD</td>
                                         <td class="create_input_column">
                                             <input type="date" name="shipment_etd" id="shipment_etd"
                                                 class="form-control" required value="{{ $tnas->shipment_etd }}"
                                                 readonly>
                                         </td>
                                         <td class="create_label_column">Total Lead Time: </td>
                                         <td class="create_input_column">
                                             <input type="text" name="total_lead_time" id="total_lead_time"
                                                 class="form-control" required value="{{ $tnas->total_lead_time }}"
                                                 readonly>
                                         </td>
                                     </tr>
                                     <tr>
                                         <td class="create_label_column">Remarks</td>
                                         <td class="create_input_column">
                                             <textarea name="remarks" id="remarks" class="form-control" rows="3">{{ $tnas->remarks }}</textarea>
                                         </td>
                                         <!-- create a dropdown for the print_wash where option are only_print, only_wash, both_print_and_wash, no_print_or_wash -->
                                            <td class="create_label_column">Print/Wash</td>
                                            <td class="create_input_column">
                                                <select id="print_wash" name="print_wash" class="form-control" required>
                                                    <option value="">Select Print/Wash</option>
                                                    <option value="Only Print"{{ $tnas->print_wash == 'Only Print' ? 'selected' : '' }}>Only Print</option>
                                                    <option value="Only Wash"{{ $tnas->print_wash == 'Only Wash' ? 'selected' : '' }}>Only Wash</option>
                                                    <option value="Both Print and Wash"{{ $tnas->print_wash == 'Both Print and Wash' ? 'selected' : '' }}>Both Print and Wash</option>
                                                    <option value="No Print and Wash"{{ $tnas->print_wash == 'No Print and Wash' ? 'selected' : '' }}>No Print and Wash</option>
                                                </select>
                                            </td>
                                    </tr>
                                 </tbody>
                             </table>


                             <div class="button-container">
                                 <button type="submit" class="btn btn-outline-success"><i class="fas fa-save"></i>
                                     Update</button>
                                 <a href="{{ route('tnas.index') }}" class="btn btn-outline-secondary"><i
                                         class="fas fa-arrow-left"></i> Cancel</a>
                             </div>
                         </form>
                     </div>
                 </div>
             </div>
         </div>
     </div>
     @if (auth()->user()->role_id == 1 || auth()->user()->role_id == 4)
         <script>
             //po_receive_date, shipment_etd can be editable by Admin and SuperVisor only and total_lead_time will be calculated automatically
             $(document).ready(function() {
                 $('#po_receive_date').removeAttr('readonly');
                 $('#shipment_etd').removeAttr('readonly');
             });
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
         @endif
 </x-backend.layouts.master>
