 <x-backend.layouts.master>
     <div class="card mx-5 my-5" style="background-color: white; ">

         <div class="row p-1">
             <div class="col-12">
                 <h3 class="text-center p-1">Buyer ways SOP List</h3>
                 <div class="row p-1">
                     <div class="col-6 text-start">

                         <a href=" {{ route('home') }} " class="btn btn-outline-secondary"><i
                                 class="fas fa-arrow-left"></i>
                             Close</a>
                     </div>
                     <div class="col-6 text-end">
                         <a href="{{ route('marchent_sops.create') }}" class="btn btn-outline-primary"> <i
                                 class="fas fa-plus"></i> Add Buyer ways SOP</a>
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

                                         <th>Buyer Name</th>
                                         <th>Perticulars</th>
                                         <th>Days</th>
                                         <th>Action</th>
                                     </tr>
                                 </thead>
                                 <tbody>
                                     @forelse ($marchent_sops as $key=> $marchent_sop)
                                         @if ($key === 0 || $marchent_sop->buyer_name !== $marchent_sops[$key - 1]->buyer_name)
                                             <tr>

                                                 <td>{{ $marchent_sop->buyer_name }}</td>
                                                 <td>{{ $marchent_sop->Perticulars }}</td>
                                                 <td>{{ $marchent_sop->day }}</td>
                                                 <td>
                                                     <a href="{{ route('marchent_sops.edit', $marchent_sop->id) }}"
                                                         class="btn btn-outline-primary"><i class="fas fa-edit"></i></a>
                                                     <form
                                                         action="{{ route('marchent_sops.destroy', $marchent_sop->id) }}"
                                                         method="POST" style="display:inline-block;">
                                                         @csrf
                                                         @method('DELETE')
                                                         <button type="submit" class="btn btn-outline-danger"><i
                                                                 class="fas fa-trash"></i></button>
                                                     </form>
                                                 </td>
                                             </tr>
                                         @else
                                             <tr>
                                                 <td></td>
                                                 <td>{{ $marchent_sop->Perticulars }}</td>
                                                 <td>{{ $marchent_sop->day }}</td>
                                                 <td>
                                                     <a href="{{ route('marchent_sops.edit', $marchent_sop->id) }}"
                                                         class="btn btn-outline-primary"><i class="fas fa-edit"></i></a>
                                                     <form
                                                         action="{{ route('marchent_sops.destroy', $marchent_sop->id) }}"
                                                         method="POST" style="display:inline-block;">
                                                         @csrf
                                                         @method('DELETE')
                                                         <button type="submit" class="btn btn-outline-danger"><i
                                                                 class="fas fa-trash"></i></button>
                                                     </form>
                                                 </td>
                                             </tr>
                                         @endif
                                     @empty
                                         <tr>
                                             <td colspan="3">No marchent_sops found</td>
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
