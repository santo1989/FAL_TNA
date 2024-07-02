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
         <h3 class="text-center p-1">SOP List</h3>
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
                         <form method="POST" action="{{ route('sops.store') }}" enctype="multipart/form-data">
                             @csrf
                             <table class="table">
                                 <tbody>
                                     <tr>
                                         <td class="create_label_column">Lead Time</td>
                                         <td class="create_input_column">
                                             <input type="number" name="lead_time" id="lead_time" class="form-control"
                                                 required>
                                         </td>
                                         <td class="create_label_column">Perticulars</td>
                                         <td class="create_input_column">
                                             <input type="text" name="Perticulars" id="Perticulars"
                                                 class="form-control" required>
                                         </td>
                                         <td class="create_label_column">Days</td>
                                         <td class="create_input_column">
                                             <input type="number" name="day" id="day" class="form-control"
                                                 required>
                                         </td>
                                     </tr>
                                 </tbody>
                             </table>

                             <div class="button-container">
                                 <button type="submit" id="saveButton" class="btn btn-outline-success">
                                     <i class="fas fa-save"></i> Save
                                 </button>
                                 <a href="{{ route('sops.index') }}" class="btn btn-outline-secondary">
                                     <i class="fas fa-arrow-left"></i> Cancel
                                 </a>
                             </div>
                         </form>
                     </div>
                 </div>
             </div>
         </div>
 </x-backend.layouts.master>
