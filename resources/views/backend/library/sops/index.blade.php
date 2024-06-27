 <x-backend.layouts.master>
     <div class="card mx-5 my-5" style="background-color: white; ">

         <div class="row p-1">
             <div class="col-12">
                 <h3 class="text-center p-1">SOP List</h3>
                 <div class="row p-1">
                     <div class="col-6 text-start">

                         <a href=" {{ route('home') }} " class="btn btn-outline-secondary"><i
                                 class="fas fa-arrow-left"></i>
                             Close</a>
                     </div>
                     <div class="col-6 text-end">
                         <a href="{{ route('sops.create') }}" class="btn btn-outline-primary"> <i
                                 class="fas fa-plus"></i> Add SOP</a>
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
                             <table class="table table-bordered table-striped" id="datatablesSimple"
                                 style="overflow-x: auto;">
                                 <thead>
                                     <tr>
                                         <th>Perticulars Name</th>
                                         <th>Days</th>
                                         <th>Action</th>
                                     </tr>
                                 </thead>
                                 <tbody>
                                     @forelse ($sops as $sop)
                                         <tr>
                                             <td>{{ $sop->Perticulars }}</td>
                                             <td>{{ $sop->day }}</td>
                                             <td>
                                                 <a href="{{ route('sops.edit', $sop->id) }}"
                                                     class="btn btn-outline-primary"><i class="fas fa-edit"></i></a>
                                                 <form action="{{ route('sops.destroy', $sop->id) }}" method="POST"
                                                     style="display:inline-block;">
                                                     @csrf
                                                     @method('DELETE')
                                                     <button type="submit" class="btn btn-outline-danger"><i
                                                             class="fas fa-trash"></i></button>
                                                 </form>
                                             </td>
                                         </tr>
                                     @empty
                                         <tr>
                                             <td colspan="3">No sops found</td>
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
