<div class="modal fade text-center" id="ReportModal" tabindex="-1" role="dialog" aria-labelledby="ReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="ReportModalLabel">Report Management</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="d-flex flex-wrap justify-content-center gap-2">
                    <a href="{{ route('quantity_wise_summary') }}" class="btn btn-outline-info flex-fill m-1">
                        <i class="fas fa-chart-bar mr-1"></i> Quantity-Wise Summary
                    </a>
                    <a href="{{ route('item_wise_summary') }}" class="btn btn-outline-primary flex-fill m-1">
                        <i class="fas fa-list-alt mr-1"></i> Item-Wise Summary
                    </a>
                    <a href="{{ route('monthly_order_summary') }}" class="btn btn-outline-secondary flex-fill m-1">
                        <i class="fas fa-calendar-alt mr-1"></i> Monthly Order Summary
                    </a>
                    <a href="{{ route('delivery_summary') }}" class="btn btn-outline-success flex-fill m-1">
                        <i class="fas fa-truck-loading mr-1"></i> On-time Delivery Summary
                    </a>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>