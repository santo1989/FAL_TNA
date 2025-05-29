<div class="modal fade" id="sewingModal" tabindex="-1" role="dialog" aria-labelledby="sewingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="sewingModalLabel">Sewing Balance: <span id="sewingJobNo"></span></h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Content will be loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-outline-primary" id="refreshSewingData">
                    <i class="fas fa-sync-alt mr-1"></i> Refresh
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Handle refresh button
    $('#refreshSewingData').click(function() {
        const jobNo = $('#sewingJobNo').text();
        if(jobNo) {
            loadSewingData(jobNo);
        }
    });
    
    // Handle modal show event
    $('#sewingModal').on('show.bs.modal', function(event) {
        const button = $(event.relatedTarget);
        const jobNo = button.data('job-no');
        $('#sewingJobNo').text(jobNo);
        loadSewingData(jobNo);
    });
    
    function loadSewingData(jobNo) {
        const modalBody = $('#sewingModal .modal-body');
        modalBody.html(`
            <div class="text-center py-4">
                <div class="spinner-border text-danger" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p class="mt-2">Loading sewing data...</p>
            </div>
        `);
        
        $.ajax({
            url: '/jobs/' + jobNo + '/sewing-data',
            type: 'GET',
            success: function(data) {
                modalBody.html(data);
            },
            error: function() {
                modalBody.html(`
                    <div class="alert alert-danger text-center">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                        <h4>Failed to load sewing data</h4>
                        <p>Please try again later</p>
                        <button class="btn btn-outline-danger mt-2" onclick="loadSewingData('${jobNo}')">
                            <i class="fas fa-redo mr-1"></i> Retry
                        </button>
                    </div>
                `);
            }
        });
    }
});
</script>