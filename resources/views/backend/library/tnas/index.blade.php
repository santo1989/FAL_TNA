 <x-backend.layouts.master>
     <div class="card mx-5 my-5" style="background-color: white; ">

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
                     </div>
                     <div class="col-6 text-end">
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
                                 style="overflow-x: auto;">
                                 <thead>
                                     <tr>
                                         <th>Buyer</th>
                                         <th>Style</th>
                                         <th>PO Number</th>
                                         <th>Item</th>
                                         {{--<th>Color</th>
                                          <th>Picture</th> --}}
                                         <th>Total Qty</th>
                                         <th>PO Date</th>
                                         <th>Shipment Date</th>
                                         <th>Lead Days</th>
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
                                             <td>{{ $tna->total_lead_time }}
                                             </td>

                                             <td>
                                                 <a href="{{ route('tnas.show', $tna->id) }}"
                                                     class="btn btn-outline-info"><i class="fas fa-eye"></i></a>
                                                 @can('TNA-CURD')
                                                     <a href="{{ route('tnas.edit', $tna->id) }}"
                                                         class="btn btn-outline-primary"><i class="fas fa-edit"></i></a>
                                                     <form action="{{ route('tnas_close', ['tna' => $tna->id]) }}"
                                                         method="POST" style="display:inline-block;">
                                                         @csrf
                                                         <input type="hidden" name="tna_id" value="{{ $tna->id }}">
                                                         <button type="submit" class="btn btn-outline-danger"><i
                                                                 class="fas fa-times"></i></button>
                                                     </form>
                                                     @if (auth()->user()->role_id == 1)
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

     <script>
         $(document).ready(function() {
             $('#buyer_assign_table').DataTable();
         });
     </script>




 </x-backend.layouts.master>
