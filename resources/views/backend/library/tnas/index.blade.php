 <x-backend.layouts.master>
     <div class="card mx-1 my-1" style="background-color: white; ">

         <div class="row p-1">
             <div class="col-12">
                 <h3 class="text-center p-1">TNA List</h3>
                 <div class="row p-1">
                     <div class="col-6 text-start">

                         <a href=" {{ route('home') }} " class="btn btn-outline-secondary"><i
                                 class="fas fa-arrow-left"></i>
                             Close</a>
                         <a href="{{ route('archives') }}" class="btn btn-outline-secondary"> <i
                                 class="fas fa-archive"></i> TNA Archives</a>
                         <a href="{{ route('BuyerWiseTnaSummary') }}" class="btn btn-outline-success"> <i
                                 class="fas fa-user"></i> TNA Pending List</a>
                     </div>
                     <div class="col-6 text-end">
                        @php
                            $marchent_buyer_assigns = App\Models\BuyerAssign::where('user_id', auth()->user()->id)->where('buyer_id', 11)->count();
                            // dd($marchent_buyer_assigns )
                        @endphp
                        @if ($marchent_buyer_assigns > 0 || auth()->user()->id == 1)
                             <!-- Button trigger modal of update_actual_TEX_EBO-->
                         <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                             data-bs-target="#staticBackdrop">
                             Update Common Date
                         </button>
                        @endif

                        

                         <a href="{{ route('tnas_dashboard') }}" class="btn btn-outline-success"> <i
                                 class="fas fa-tachometer-alt"></i> TNA Dashboard</a>


                         @can('TNA-CURD')
                             <a href="{{ route('tnas.create') }}" class="btn btn-outline-primary"> <i
                                     class="fas fa-plus"></i> Add TNA</a>
                         @endcan
                     </div>
                     </tr>
                 </div>
             </div>

             <div class="col-12 ">
                 <div class="card p-1">

                     <!--message show in .swl sweet alert-->
                     @if (session('message'))
                         <div class="alert alert-success">
                             <span class="close" data-dismiss="alert">&times;</span>
                             <strong>{{ session('message') }}.</strong>
                         </div>
                     @endif

                     <x-backend.layouts.elements.errors />


                     <div class="card-body">
                         <div class="table-responsive">
                             <table class="table table-bordered table-striped text-nowrap" id="datatablesSimple"
                                 style="overflow-x: auto;font-size: 14px;">
                                 <thead>
                                     <tr>
                                         <th>Buyer</th>
                                         <th>Style</th>
                                         <th>PO Number</th>
                                         <th>Item</th>
                                         {{-- <th>Color</th>
                                          <th>Picture</th> --}}
                                         <th>Total Qty</th>
                                         <th>PO Date</th>
                                         <th>Shipment Date</th>
                                         {{-- <th>Lead Days</th> --}}
                                         <th>Action</th>
                                     </tr>
                                 </thead>
                                 <tbody class="text-nowrap">
                                     @forelse ($tnas as $tna)
                                         <tr>
                                             <td>{{ $tna->buyer }}</td>
                                             <td>{{ $tna->style }}</td>
                                             <td>{{ $tna->po }}</td>
                                             <td>{{ $tna->item }}</td>
                                             {{-- <td>{{ $tna->color }}</td>
                                              <td><img src="{{ asset('storage/tna/' . $tna->picture) }}" alt="Picture"
                                                        style="width: 50px; height: 50px;"></td> --}}
                                             <td>{{ $tna->qty_pcs }}</td>
                                             <td>{{ $tna->po_receive_date }}</td>
                                             <td>{{ $tna->shipment_etd }}</td>
                                             {{-- <td>{{ $tna->total_lead_time }} --}}
                                             </td>

                                             <td>
                                                 <a href="{{ route('tnas.show', $tna->id) }}"
                                                     class="btn btn-outline-info"><i class="fas fa-eye"></i>show</a>
                                                 @if ($tna->buyer_id == 11)
                                                     <a href="{{ route('tnas.copy_tna', $tna->id) }}"
                                                         class="btn btn-outline-primary"><i
                                                             class="fas fa-copy"></i>Copy</a>
                                                 @endif

                                                 @can('TNA-CURD')
                                                     <a href="{{ route('tnas.edit', $tna->id) }}"
                                                         class="btn btn-outline-primary"><i class="fas fa-edit"></i>Edit</a>
                                                     <!--update actual date only for SuperVisor-->
                                                     @if (auth()->user()->role_id == 4 || auth()->user()->role_id == 1)
                                                         <a href="{{ route('tnas.edit_actual_date', $tna->id) }}"
                                                             class="btn btn-outline-primary"><i
                                                                 class="fas fa-calendar"></i>Plan</a>
                                                     @endif
                                                     <form action="{{ route('tnas_close', ['tna' => $tna->id]) }}"
                                                         method="POST" style="display:inline-block;">
                                                         @csrf
                                                         <input type="hidden" name="tna_id" value="{{ $tna->id }}">
                                                         <button type="submit" class="btn btn-outline-danger"><i
                                                                 class="fas fa-times"></i>close</button>

                                                     </form>
                                                     @if (auth()->user()->role_id == 4 || auth()->user()->role_id == 1)
                                                         <form action="{{ route('tnas.destroy', $tna->id) }}"
                                                             method="POST" style="display:inline-block;">
                                                             @csrf
                                                             @method('DELETE')
                                                             <button type="submit" class="btn btn-outline-danger"><i
                                                                     class="fas fa-trash"></i></button>
                                                         </form>
                                                     @endif
                                                 @endcan
                                             </td>
                                         </tr>
                                     @empty
                                         <tr>
                                             <td colspan="10" class="text-center">No TNA Found</td>
                                         </tr>
                                     @endforelse
                                 </tbody>
                             </table>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </div>

     <!--update_actual_TEX_EBO Modal start-->
     <!-- Modal -->
     <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
         <div class="modal-dialog">
             <div class="modal-content">
                 <div class="modal-header">
                     <h1 class="modal-title fs-5" id="staticBackdropLabel">Update Common Date</h1>
                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                 </div>
                 <div class="modal-body">
                     <form method="POST" action="{{ route('update_actual_TEX_EBO') }}" enctype="multipart/form-data">
                         @csrf
                         @method('POST')
                         <table class="table">
                             <tbody>
                                 <tr>
                                     <td class="create_label_column">Buyer</td>
                                     <td class="create_input_column">
                                         @php
                                             $buyer = DB::table('buyers')->where('id', 11)->first();
                                         @endphp
                                         <input type="hidden" name="buyer_id" id="buyer_id" class="form-control"
                                             required value="11">
                                         {{ $buyer->name }}
                                     </td>
                                     <td class="create_label_column">Shipment Month</td>
                                     <td class="create_input_column">
                                         <input type="month" name="shipment_etd" id="shipment_etd"
                                             class="form-control" required>
                                     </td>
                                 </tr>
                             </tbody>
                         </table>
                 </div>
                 <div class="modal-footer">
                     <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                     <button type="submit" class="btn btn-outline-success"><i class="fas fa-save"></i>
                         Search TNA</button>
                 </div>
                 </form>
             </div>
         </div>
     </div>
     <!--update_actual_TEX_EBO Modal End-->


     <script>
         $(document).ready(function() {
             $('#buyer_assign_table').DataTable();
         });
     </script>




 </x-backend.layouts.master>
