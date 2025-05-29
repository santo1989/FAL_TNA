<div class="modal fade" id="ShipmentModal" tabindex="-1" role="dialog" aria-labelledby="ShipmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="ShipmentModalLabel">Shipment Details: <span id="shipmentJobNo"></span></h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Content will be loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-outline-primary" id="refreshShipmentData">
                    <i class="fas fa-sync-alt mr-1"></i> Refresh
                </button>
                <a href="#" id="addShipmentLink" class="btn btn-outline-success">
                    <i class="fas fa-plus mr-1"></i> Add Shipment
                </a>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Handle refresh button
    $('#refreshShipmentData').click(function() {
        const jobNo = $('#shipmentJobNo').text();
        if(jobNo) {
            loadShipmentData(jobNo);
        }
    });
    
    // Handle modal show event
    $('#ShipmentModal').on('show.bs.modal', function(event) {
        const button = $(event.relatedTarget);
        const jobNo = button.data('job-no');
        $('#shipmentJobNo').text(jobNo);
        $('#addShipmentLink').attr('href', '/shipments/create/' + jobNo);
        loadShipmentData(jobNo);
    });
    
    function loadShipmentData(jobNo) {
        const modalBody = $('#ShipmentModal .modal-body');
        modalBody.html(`
            <div class="text-center py-4">
                <div class="spinner-border text-success" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p class="mt-2">Loading shipment data...</p>
            </div>
        `);
        
        $.ajax({
            url: '/jobs/' + jobNo + '/shipment-data',
            type: 'GET',
            success: function(data) {
                modalBody.html(data);
            },
            error: function() {
                modalBody.html(`
                    <div class="alert alert-danger text-center">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                        <h4>Failed to load shipment data</h4>
                        <p>Please try again later</p>
                        <button class="btn btn-outline-danger mt-2" onclick="loadShipmentData('${jobNo}')">
                            <i class="fas fa-redo mr-1"></i> Retry
                        </button>
                    </div>
                `);
            }
        });
    }
});
</script>