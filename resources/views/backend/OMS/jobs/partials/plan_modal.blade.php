<!-- PlanModal -->
<div class="modal fade text-center" id="PlanModal" tabindex="-1" role="dialog" aria-labelledby="PlanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="PlanModalLabel">Plan Management</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body justify-content-center">
                <div class="d-flex flex-wrap justify-content-center gap-2">
                    <a href="{{ route('factory_holidays.index') }}" class="btn btn-outline-danger m-1">
                        <i class="fas fa-calendar-alt mr-1"></i> Holydays Plan
                    </a>
                    <a href="{{ route('capacity_plans.index') }}" class="btn btn-outline-success m-1">
                        <i class="fas fa-chart-line mr-1"></i> Capacity Plan
                    </a>
                    <a href="{{ route('sewing_plans.index') }}" class="btn btn-outline-primary m-1">
                        <i class="fas fa-tshirt mr-1"></i> Sewing Plan
                    </a>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>