<x-backend.layouts.master>
    <div class="container mt-5 pb-5">
         <!--message show in .swl sweet alert-->
         @if (session('message'))
         <div class="alert alert-success">
             <span class="close" data-dismiss="alert">&times;</span>
             <strong>{{ session('message') }}.</strong>
         </div>
     @endif

     <x-backend.layouts.elements.errors />
        <div class="row justify-content-center mt-5 pb-5">
            <div class="col-md-8 mt-5 pb-5">
                <div class="card">
                    <div class="card-header">Import Jobs</div>

                    <div class="card-body">
                        <form action="{{ route('jobs.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="file">Choose Excel File</label>
                                <input type="file" class="form-control" name="file" required>
                            </div>
                            <button class="btn btn-outline-primary mt-3"><i class="fas fa-upload"></i>
                                Upload The Excel</button>
                        </form>

                       
                    </div>
                </div>
            </div>
            <div class="col-md-4 mt-5 pb-5">
                <div class="card">
                    <div class="card-header">Instructions</div>

                    <div class="card-body">
                        <ul>
                            <li>Download the sample Excel file.</li>
                            <li>Fill in the required fields.</li>
                            <li>Upload the filled Excel file here.</li>
                        </ul>
                    </div>
                    <div class="m-4">
                        <a href="{{ route('jobs.index') }}" 
                         class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left"></i>
                            Back
                        </a>
                            <a href="{{ route('job_sample_download') }}" class="btn btn-outline-danger"><i class="fas fa-download"></i>
                                <i class="fas fa-file-excel"></i>
                                Download Sample Excel
                            </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-backend.layouts.master>
