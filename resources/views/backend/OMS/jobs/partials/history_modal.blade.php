<div class="modal fade text-center" id="HistoryModal" tabindex="-1" role="dialog" aria-labelledby="HistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="HistoryModalLabel">Historical Data</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="d-flex flex-wrap justify-content-center gap-2">
                    <a href="{{ route('sewing_balances.index') }}" class="btn btn-outline-info flex-fill m-1">
                        <i class="fas fa-tshirt mr-1"></i> Sewing History
                    </a>
                    <a href="{{ route('shipments.index') }}" class="btn btn-outline-warning flex-fill m-1">
                        <i class="fas fa-ship mr-1"></i> Shipment History
                    </a>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>