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
         <h3 class="text-center p-1">Create Buyer ways SOP List</h3>
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
                         <form method="POST" action="{{ route('marchent_sops.store') }}" enctype="multipart/form-data">
                             @csrf
                             <table class="table">
                                 <tbody>
                                     <tr>
                                         <td class="create_label_column">Buyer Name</td>
                                         <td class="create_input_column">
                                             <select name="buyer_id" id="buyer_id" class="form-control" required>
                                                    <option value="">Select Buyer</option>
                                                 @foreach ($buyers as $buyer)
                                                     <option value="{{ $buyer->id }}">{{ $buyer->name }}</option>
                                                 @endforeach
                                             </select>
                                         </td>
                                     </tr>

                                     <tr>
                                         <td class="create_label_column">Perticulars</td>
                                         <td class="create_input_column">
                                             Days
                                         </td>
                                     </tr>
                                     @foreach ($sops as $sop)
                                         <tr>
                                             <td class="create_label_column">
                                                 {{ $sop->Perticulars }}
                                                 <input type="hidden" name="sop_id[]" value="{{ $sop->id }}">
                                             </td>
                                             <td class="create_input_column">
                                                 @if (auth::user()->role_id == 1 || auth::user()->role_id == 4)
                                                     <input type="text" name="day[]"
                                                         value="{{ $sop->day }}" class="form-control" required>
                                                 @else
                                                     <input type="text" name="day[]"
                                                         value="{{ $sop->day }}" class="form-control" required
                                                         readonly>
                                                 @endif

                                             </td>
                                         </tr>
                                     @endforeach
                                 </tbody>
                             </table>

                             <div class="button-container">
                                 <button type="submit" id="saveButton" class="btn btn-outline-success">
                                     <i class="fas fa-save"></i> Save
                                 </button>
                                 <a href="{{ route('marchent_sops.index') }}" class="btn btn-outline-secondary">
                                     <i class="fas fa-arrow-left"></i> Cancel
                                 </a>
                             </div>
                         </form>
                     </div>
                 </div>
             </div>
         </div>

         <!-- Script for select2 -->
         <script>
             $(document).ready(function() {
               
                 // Multiple buyer id can be selected
                 $('#buyer_id').select2();
             });
         </script>




 </x-backend.layouts.master>
