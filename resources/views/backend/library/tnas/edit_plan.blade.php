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
                 <div class="card-header">
                     <table class="table table-bordered table-striped text-nowrap">
                         <tbody>
                             <tr>
                                 <td class="create_label_column">Buyer</td>
                                 <td class="create_input_column">
                                     {{ $tnas->buyer }}
                                 </td>
                                 <td class="create_label_column">Style</td>
                                 <td class="create_input_column">
                                     {{ $tnas->style }}
                                 </td>
                             </tr>
                             <tr>
                                 <td class="create_label_column">PO Number</td>
                                 <td class="create_input_column">
                                     {{ $tnas->po }}
                                 </td>
                                 <td class="create_label_column">Item</td>
                                 <td class="create_input_column"> {{ $tnas->item }}
                                 </td>
                             </tr>
                             <tr>
                                 <td class="create_label_column">Qty (pcs)</td>
                                 <td class="create_input_column">
                                     {{ $tnas->qty_pcs }}
                                 </td>

                                 <td class="create_label_column">PO Recieve Date</td>
                                 <td class="create_input_column">
                                     {{ $tnas->po_receive_date }}
                                 </td>
                             </tr>

                         </tbody>
                     </table>
                 </div>
                 <div class="card" style="overflow-x: auto; background-color: white; ">
                     <div class="card-body">
                         <form method="POST" action="{{ route('tnas.update_actual_date', $tnas->id) }}"
                             enctype="multipart/form-data">
                             @csrf 
                             <table class="table table-bordered table-striped text-wrap">
                                 <thead>
                                     <tr>
                                         <th>Lab Dip Submission</th>
                                         <th>Fabric Booking</th>
                                         <th>Fit Sample Submission</th>
                                         <th>Print Strike Off Submission</th>
                                         <th>Bulk Accessories Booking</th>
                                         <th>Fit Comments</th>
                                         <th>Bulk Yarn Inhouse</th>
                                         <th>Bulk Accessories Inhouse</th>
                                         <th>PP Sample Submission</th>
                                         <th>Bulk Fabric Knitting</th>
                                         <th>PP Comments Receive</th>
                                         <th>Bulk Fabric Dyeing</th>
                                         <th>Bulk Fabric Delivery</th>
                                         <th>PP Meeting</th>
                                         <th>ETD</th>
                                     </tr>
                                 </thead>
                                 <tbody>
                                     <tr>
                                         <td><input type="date" name="lab_dip_submission_actual"
                                                 value="{{ $tnas->lab_dip_submission_actual }}"
                                                 class="form-control"></td>
                                         <td><input type="date" name="fabric_booking_actual"
                                                 value="{{ $tnas->fabric_booking_actual }}" class="form-control"></td>
                                         <td><input type="date" name="fit_sample_submission_actual"
                                                 value="{{ $tnas->fit_sample_submission_actual }}" class="form-control"></td>
                                         <td><input type="date" name="print_strike_off_submission_actual"
                                                 value="{{ $tnas->print_strike_off_submission_actual }}" class="form-control">
                                         </td>
                                         <td><input type="date" name="bulk_accessories_booking_actual"
                                                 value="{{ $tnas->bulk_accessories_booking_actual }}" class="form-control">
                                         </td>
                                         <td><input type="date" name="fit_comments_actual"
                                                 value="{{ $tnas->fit_comments_actual }}" class="form-control"></td>
                                            <td><input type="date" name="bulk_yarn_inhouse_actual"
                                                 value="{{ $tnas->bulk_yarn_inhouse_actual }}" class="form-control"></td>
                                            <td><input type="date" name="bulk_accessories_inhouse_actual" value="{{ $tnas->bulk_accessories_inhouse_actual }}"
                                                 class="form-control"></td>
                                            <td><input type="date" name="pp_sample_submission_actual"
                                                 value="{{ $tnas->pp_sample_submission_actual }}" class="form-control"></td>
                                            <td><input type="date" name="bulk_fabric_knitting_actual" value="{{ $tnas->bulk_fabric_knitting_actual }}"
                                                 class="form-control"></td>
                                            <td><input type="date" name="pp_comments_receive_actual" value="{{ $tnas->pp_comments_receive_actual }}"
                                                 class="form-control"></td>
                                            <td><input type="date" name="bulk_fabric_dyeing_actual" value="{{ $tnas->bulk_fabric_dyeing_actual }}"
                                                    class="form-control"></td>
                                            <td><input type="date" name="bulk_fabric_delivery_actual" value="{{ $tnas->bulk_fabric_delivery_actual }}" class="form-control"></td>
                                            <td><input type="date" name="pp_meeting_actual" value="{{ $tnas->pp_meeting_actual }}" class="form-control"></td>
                                            <td><input type="date" name="etd_actual" value="{{ $tnas->etd_actual }}" class="form-control"></td>


                                          
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
