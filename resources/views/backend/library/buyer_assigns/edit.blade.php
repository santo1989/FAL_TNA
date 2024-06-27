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
         <h3 class="text-center p-1">Edit Buyer list</h3>
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
                         <form method="POST" action="{{ route('buyer_assigns.update', $buyer_assign->id) }}"
                             enctype="multipart/form-data">
                             @csrf
                             @method('PUT')
                             <table class="table">
                                 <tbody>
                                     <tr>
                                         <td class="create_label_column">user Name</td>
                                         <td class="create_input_column">
                                             <select name="user_id" id="user_id" class="form-control" required>
                                                 <option value="">Select User</option>
                                                 @foreach ($users as $user)
                                                     <option value="{{ $user->id }}"
                                                         {{ $buyer_assign->user_id == $user->id ? 'selected' : '' }}>
                                                         {{ $user->name }}</option>
                                                 @endforeach
                                             </select>
                                         </td>
                                         <td class="create_label_column">Buyer</td>
                                         <td class="create_input_column">
                                             <select name="buyer_id" id="buyer_id" class="form-control" required>
                                                 <option value="">Select Buyer</option>
                                                 @foreach ($buyers as $buyer)
                                                     <option value="{{ $buyer->id }}"
                                                         {{ $buyer_assign->buyer_id == $buyer->id ? 'selected' : '' }}>
                                                         {{ $buyer->name }}</option>
                                                 @endforeach
                                             </select>
                                         </td>
                                     </tr>
                                 </tbody>
                             </table>


                             <div class="button-container">
                                 <button type="submit" class="btn btn-outline-success"><i class="fas fa-save"></i>
                                     Update</button>
                                 <a href="{{ route('buyer_assigns.index') }}" class="btn btn-outline-secondary"><i
                                         class="fas fa-arrow-left"></i> Cancel</a>
                             </div>
                         </form>
                     </div>
                 </div>
             </div>
         </div>
     </div>

 </x-backend.layouts.master>
