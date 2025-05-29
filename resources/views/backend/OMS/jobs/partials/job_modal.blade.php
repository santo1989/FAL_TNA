<div class="modal fade text-center" id="jobModal" tabindex="-1" role="dialog" aria-labelledby="jobModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="jobModalLabel">Manage Job: <span id="modalJobNo"></span></h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="d-flex flex-wrap justify-content-center gap-2">
                    <a href="#" id="viewJobLink" class="btn btn-outline-info flex-fill m-1">
                        <i class="fas fa-eye mr-1"></i> View Job
                    </a>
                    
                    @can('TNA-CURD')
                    <a href="#" id="editJobLink" class="btn btn-outline-primary flex-fill m-1">
                        <i class="fas fa-edit mr-1"></i> Edit Job
                    </a>
                    
                    @if(auth()->user()->role_id == 4 || auth()->user()->role_id == 1)
                    <a href="#" id="SewingBalance" class="btn btn-outline-secondary flex-fill m-1">
                        <i class="fas fa-tshirt mr-1"></i> Sewing Balance
                    </a>
                    
                    <a href="#" id="calendarJobLink" class="btn btn-outline-success flex-fill m-1">
                        <i class="fas fa-shipping-fast mr-1"></i> Shipment
                    </a>
                    @endif
                    
                    @if(auth()->user()->role_id == 1)
                    <form id="deleteJobForm" method="POST" style="display:inline; flex:1 0 auto;" class="m-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100" 
                                onclick="return confirm('Are you sure you want to delete this job?')">
                            <i class="fas fa-trash mr-1"></i> Delete Job
                        </button>
                    </form>
                    @endif
                    @endcan
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>