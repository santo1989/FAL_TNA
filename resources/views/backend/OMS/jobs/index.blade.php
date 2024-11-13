 <x-backend.layouts.master>
     <div class="card mx-5 my-5" style="background-color: white; ">

         <div class="row p-1">
             <div class="col-12">
                 <h3 class="text-center p-1">Job List</h3>
                 <div class="row p-1">
                     <div class="col-6 text-start">

                         <a href=" {{ route('home') }} " class="btn btn-outline-secondary"><i
                                 class="fas fa-arrow-left"></i>
                             Close</a>
                         <a href="{{ route('factory_holidays.index') }}" class="btn btn-outline-danger"> <i
                                 class="fas fa-plus"></i> Holydays Plan</a> 
                         <a href="{{ route('capacity_plans.create') }}" class="btn  btn-outline-success"><i
                                        class="fas fa-tachometer-alt"></i> Add Capacity Plan</a>

                     </div>
                     <div class="col-6 text-end">
                          <a href="{{ route('sewing_plans.create') }}" class="btn btn-outline-primary"> <i
                                 class="fas fa-plus"></i> Add Sewing Plan</a> 
                         <button type="button" class="btn btn-outline-success" data-toggle="modal"
                             data-target="#ReportModal">
                             Report
                         </button>


                         <a href="{{ route('sewing_balances.index') }}" class="btn btn-outline-info"> <i
                                 class="fas fa-tachometer-alt"></i> Sewing History</a>
                         <a href="{{ route('shipments.index') }}" class="btn btn-outline-warning"> <i
                                 class="fas fa-tachometer-alt"></i> Shipment History</a>


                         @can('TNA-CURD')
                             <a href="{{ route('jobs.create') }}" class="btn btn-outline-primary"> <i
                                     class="fas fa-plus"></i> Add Job</a>
                         @endcan
                     </div>
                     </tr>
                 </div>
             </div>

             <div class="col-12 ">
                 <div class="card p-1">

                     <!--message show in .swl sweet alert-->
                     @if (session('message'))
                         <div class="alert alert-success">
                             <span class="close" data-dismiss="alert">&times;</span>
                             <strong>{{ session('message') }}.</strong>
                         </div>
                     @endif

                     <x-backend.layouts.elements.errors />


                     <div class="card-body">
                         <div class="table-responsive">
                             <table class="table table-bordered table-striped text-nowrap" id="datatablesSimple"
                                 style="overflow-x: auto;">
                                 <thead>
                                     <tr>
                                         <th>Job No</th>
                                         <th>Buyer</th>
                                         <th>Style</th>
                                         <th>PO Number</th>
                                         <th>Item</th>
                                         <th>Order Qty</th>
                                         <th>Sewing Balance</th>
                                         <th>Shipped Qty</th>
                                         <th>Receive Date</th>
                                         <th>Delivery Date</th>
                                         <th>Action</th>
                                     </tr>
                                 </thead>
                                 <tbody class="text-nowrap" id="jobTableBody">
                                     @forelse ($jobs as $job)
                                         <tr>
                                             <td>
                                                 <button type="button" class="btn btn-outline-success"
                                                     data-toggle="modal" data-target="#jobModal"
                                                     data-job-id="{{ $job->job_no }}"
                                                     data-job-no="{{ $job->job_no }}">
                                                     {{ $job->job_no }}
                                                 </button>

                                             </td>
                                             <td>{{ $job->buyer }}</td>
                                             <td>{{ $job->style }}</td>
                                             <td>{{ $job->po }}</td>
                                             <td>{{ $job->item }}</td>
                                             <td>{{ $job->order_quantity }}</td>
                                             <td>
                                                 @php
                                                     $sewing_qty = DB::table('sewing_blances')
                                                         ->where('job_no', $job->job_no)
                                                         ->get()
                                                         ->sum('sewing_balance');
                                                     $total_sewing_qty = $job->order_quantity - $sewing_qty;
                                                 @endphp
                                                 <button type="button" class="btn btn-outline-danger"
                                                     data-toggle="modal" data-target="#sewingModal"
                                                     data-job-id="{{ $job->job_no }}"
                                                     data-job-no="{{ $job->job_no }}">
                                                     {{ $total_sewing_qty }}
                                                 </button>



                                             </td>
                                             <td>
                                                 @php
                                                     $total_shipped_qty = DB::table('shipments')
                                                         ->where('job_no', $job->job_no)
                                                         ->get()
                                                         ->sum('shipped_qty');

                                                 @endphp
                                                 <button type="button" class="btn btn-outline-danger"
                                                     data-toggle="modal" data-target="#ShipmentModal"
                                                     data-job-id="{{ $job->job_no }}"
                                                     data-job-no="{{ $job->job_no }}">
                                                     {{ $total_shipped_qty }}
                                                 </button>


                                             </td>
                                             <td>{{ $job->order_received_date }}</td>
                                             <td>{{ $job->delivery_date }}</td>

                                             <td>
                                                 <a href="{{ route('jobs.show', $job->job_no) }}"
                                                     class="btn btn-outline-info"><i class="fas fa-eye"></i></a>

                                                 @if (auth()->user()->role_id == 1)
                                                     <form action="{{ route('jobs.destroy_all', $job->job_no) }}"
                                                         method="POST" style="display:inline-block;">
                                                         @csrf
                                                         @method('POST')
                                                         <button type="submit" class="btn btn-outline-danger"><i
                                                                 class="fas fa-trash"></i></button>
                                                     </form>
                                                 @endif

                                             </td>
                                         </tr>
                                     @empty
                                         <tr>
                                             <td colspan="10" class="text-center">No Job Found</td>
                                         </tr>
                                     @endforelse
                                 </tbody>
                             </table>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </div>

     <!-- ReportModal Start-->
     <div class="modal fade text-center" id="ReportModal" tabindex="-1" role="dialog"
         aria-labelledby="ReportModalLabel" aria-hidden="true">
         <div class="modal-dialog  modal-lg" role="document">
             <div class="modal-content">
                 <div class="modal-header">
                     <h5 class="modal-title" id="ReportModalLabel">Report
                         <span id="modalJobNo"></span>
                     </h5>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                         <span aria-hidden="true">&times;</span>
                     </button>
                 </div>
                 <div class="modal-body justify-content-center">
                     <a href="{{ route('quantity_wise_summary') }}" class="btn btn-outline-info"><i
                             class="fas fa-eye"></i> Quantity-Wise Summary</a>
                     <a href="{{ route('item_wise_summary') }}" class="btn btn-outline-primary"><i
                             class="fas fa-edit"></i> Item-Wise Summary</a>
                     <a href="{{ route('monthly_order_summary') }}" class="btn btn-outline-secondary"><i
                             class="fas fa-boxes"></i> Monthly Order Summary</a>
                     <a href="{{ route('delivery_summary') }}" class="btn btn-outline-primary"><i
                             class="fas fa-ship"></i> On-time Delivery Summary</a>
                 </div>
             </div>
         </div>
     </div>
     <!-- ReportModal End-->

     <!-- JobModal Start-->
     <div class="modal fade text-center" id="jobModal" tabindex="-1" role="dialog" aria-labelledby="jobModalLabel"
         aria-hidden="true">
         <div class="modal-dialog  modal-lg" role="document">
             <div class="modal-content">
                 <div class="modal-header">
                     <h5 class="modal-title" id="jobModalLabel">Manage Job
                         <span id="modalJobNo"></span>
                     </h5>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                         <span aria-hidden="true">&times;</span>
                     </button>
                 </div>
                 <div class="modal-body justify-content-center">
                     <a href="#" id="viewJobLink" class="btn btn-outline-info"><i class="fas fa-eye"></i>
                         View</a>
                     @can('TNA-CURD')
                         <a href="#" id="editJobLink" class="btn btn-outline-primary"><i class="fas fa-edit"></i>
                             Edit</a>
                         @if (auth()->user()->role_id == 4 || auth()->user()->role_id == 1)
                             <a href="#" id="SewingBalance" class="btn btn-outline-secondary"><i
                                     class="fas fa-boxes"></i> Sewing
                                 Balance</a>
                             <a href="#" id="calendarJobLink" class="btn btn-outline-primary"><i
                                     class="fas fa-ship"></i> Shipment</a>
                         @endif
                         @if (auth()->user()->role_id == 1)
                             <form id="deleteJobForm" action="#" method="POST" style="display:inline-block;">
                                 @csrf
                                 @method('DELETE')
                                 <button type="submit" class="btn btn-outline-danger"><i class="fas fa-trash"></i>
                                     Delete</button>
                             </form>
                         @endif
                     @endcan
                 </div>
             </div>
         </div>
     </div>
     <!-- JobModal End-->




     <!-- SewingModal Start-->
     <div class="modal fade" id="sewingModal" tabindex="-1" role="dialog" aria-labelledby="sewingModalLabel"
         aria-hidden="true">
         <div class="modal-dialog modal-xl" role="document">
             <div class="modal-content">
                 <div class="modal-header">
                     <h5 class="modal-title" id="sewingModalLabel">Manage
                         Sewing Balance <span id="modalJobNo"></span></h5>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                         <span aria-hidden="true">&times;</span>
                     </button>
                 </div>
                 <div class="modal-body">
                     @php
                         $sewing_balance = App\Models\SewingBlance::select(
                             'job_no',
                             'color',
                             'size',
                             DB::raw('SUM(sewing_balance) as total_sewing_balance'),
                             DB::raw('SUM(production_min_balance) as total_production_min_balance'),
                         )
                             ->where('job_no', $job->job_no)
                             ->groupBy('job_no', 'color', 'size')
                             ->get();
                     @endphp
                     <div class="table-responsive">
                         <table class="table table-bordered table-striped text-nowrap" id="datatablesSimple">
                             <thead>
                                 <tr>
                                     <th>Job No</th>
                                     <th>Color</th>
                                     <th>Size</th>
                                     <th>Order Qty</th>
                                     <th>Total Sewing QTY</th>
                                     <th>Total Sewing Balance</th>
                                     <th>Total Production Min Balance</th>
                                     <th>Action</th>
                                 </tr>
                             </thead>
                             <tbody>
                                 @forelse ($sewing_balance as $balance)
                                     <tr>
                                         <td>{{ $balance->job_no }}</td>
                                         <td>{{ $balance->color }}</td>
                                         <td>{{ $balance->size }}</td>
                                         @php
                                             $order_qty = App\Models\Job::where('job_no', $balance->job_no)
                                                 ->where('color', $balance->color)
                                                 ->where('size', $balance->size)
                                                 ->first()->color_quantity;
                                         @endphp
                                         <td>{{ $order_qty }}</td>
                                         <td>{{ $balance->total_sewing_balance }}
                                         </td>
                                         <td>{{ $order_qty - $balance->total_sewing_balance }}

                                         <td>{{ $balance->total_production_min_balance }}
                                         </td>
                                         <td>
                                             <a href="{{ route('sewing_balances.show', $balance->job_no) }}"
                                                 class="btn btn-outline-info"><i class="fas fa-eye"></i>
                                                 show</a>
                                         </td>
                                     </tr>
                                 @empty
                                     <tr>
                                         <td colspan="5" class="text-center">No Sewing
                                             Balance Found</td>
                                     </tr>
                                 @endforelse
                             </tbody>
                         </table>
                     </div>
                 </div>
             </div>
         </div>
     </div>
     <!-- SewingModal End-->

     <!-- ShipmentModal Start-->
     <div class="modal fade" id="ShipmentModal" tabindex="-1" role="dialog" aria-labelledby="ShipmentModalLabel"
         aria-hidden="true">
         <div class="modal-dialog modal-xl" role="document">
             <div class="modal-content">
                 <div class="modal-header">
                     <h5 class="modal-title" id="sewingModalLabel">Manage
                         Shipment Qty <span id="modalJobNo"></span></h5>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                         <span aria-hidden="true">&times;</span>
                     </button>
                 </div>
                 <div class="modal-body">
                     @php
                         // Fetch shipped quantities and values
                         $shipped_qty = App\Models\Shipment::select(
                             'job_no',
                             'color',
                             'size',
                             DB::raw('SUM(shipped_qty) as total_shipped_qty'),
                             DB::raw('SUM(shipped_value) as total_shipped_value'),
                             DB::raw('SUM(excess_short_shipment_qty) as total_excess_short_shipment_qty'),
                             DB::raw('SUM(excess_short_shipment_value) as total_excess_short_shipment_value'),
                         )
                             ->where('job_no', $job->job_no)
                             ->groupBy('job_no', 'color', 'size')
                             ->get();
                     @endphp

                     <div class="table-responsive">
                         <table class="table table-bordered table-striped text-wrap text-center"
                             id="datatablesSimple">
                             <thead>
                                 <tr>
                                     <th>Job No</th>
                                     <th>Color</th>
                                     <th>Size</th>
                                     <th>Order Qty</th>
                                     <th>Total Shipped Qty</th>
                                     <th>Total Shipped Value</th>
                                     <th>Total Short Shipment Qty</th>
                                     <th>Total Short Shipment Value</th>
                                     <th>Total Excess Qty</th>
                                     <th>Total Excess Value</th>
                                     <th>Action</th>
                                 </tr>
                             </thead>
                             <tbody>
                                 @forelse ($shipped_qty as $balance)
                                     @php
                                         // Fetch the order quantity and unit price
                                         $job = App\Models\Job::where('job_no', $balance->job_no)
                                             ->where('color', $balance->color)
                                             ->where('size', $balance->size)
                                             ->first();

                                         $order_qty = $job->color_quantity ?? 0;
                                         $unit_price = $job->unit_price ?? 0;

                                         // Calculate total_short_qty and total_short_value
                                         $total_short_qty = $order_qty - $balance->total_shipped_qty;
                                         $total_short_value = $total_short_qty * $unit_price;
                                     @endphp
                                     <tr>
                                         <td>{{ $balance->job_no }}</td>
                                         <td>{{ $balance->color }}</td>
                                         <td>{{ $balance->size }}</td>
                                         <td class="order_qty">{{ $order_qty }}</td>
                                         <td class="total_shipped_qty">{{ $balance->total_shipped_qty }}</td>
                                         <td class="total_shipped_value">{{ $balance->total_shipped_value }}</td>
                                         <td class="total_short_qty">{{ $total_short_qty }}</td>
                                         <td class="total_short_value">{{ $total_short_value }}</td>
                                         <td class="total_excess_short_shipment_qty">
                                             {{ $balance->total_excess_short_shipment_qty }}</td>
                                         <td class="total_excess_short_shipment_value">
                                             {{ $balance->total_excess_short_shipment_value }}</td>
                                         <td>
                                             <a href="{{ route('sewing_balances.show', $balance->job_no) }}"
                                                 class="btn btn-outline-info">
                                                 <i class="fas fa-eye"></i> Show
                                             </a>
                                         </td>
                                     </tr>
                                 @empty
                                     <tr>
                                         <td colspan="11" class="text-center">No Sewing Balance Found</td>
                                     </tr>
                                 @endforelse
                                 <tr>
                                     <th colspan="3" class="text-center">Total</th>
                                     <th class="text-center" id="total_order_qty"></th>
                                     <th class="text-center" id="total_shipped_qty"></th>
                                     <th class="text-center" id="total_shipped_value"></th>
                                     <th class="text-center" id="total_short_qty"></th>
                                     <th class="text-center" id="total_short_value"></th>
                                     <th class="text-center" id="total_excess_short_shipment_qty"></th>
                                     <th class="text-center" id="total_excess_short_shipment_value"></th>
                                 </tr>
                             </tbody>
                         </table>
                     </div>

                     <script>
                         $(document).ready(function() {
                             var total_order_qty = 0;
                             var total_shipped_qty = 0;
                             var total_shipped_value = 0;
                             var total_short_qty = 0;
                             var total_short_value = 0;
                             var total_excess_short_shipment_qty = 0;
                             var total_excess_short_shipment_value = 0;

                             $('#datatablesSimple tbody tr').each(function() {
                                 // Check if the row is not the header or the total row
                                 if ($(this).find('.order_qty').length) {
                                     total_order_qty += parseInt($(this).find('.order_qty').text()) || 0;
                                     total_shipped_qty += parseInt($(this).find('.total_shipped_qty').text()) || 0;
                                     total_shipped_value += parseInt($(this).find('.total_shipped_value').text()) || 0;
                                     total_short_qty += parseInt($(this).find('.total_short_qty').text()) || 0;
                                     total_short_value += parseInt($(this).find('.total_short_value').text()) || 0;
                                     total_excess_short_shipment_qty += parseInt($(this).find(
                                         '.total_excess_short_shipment_qty').text()) || 0;
                                     total_excess_short_shipment_value += parseInt($(this).find(
                                         '.total_excess_short_shipment_value').text()) || 0;
                                 }
                             });

                             $('#total_order_qty').text(total_order_qty);
                             $('#total_shipped_qty').text(total_shipped_qty);
                             $('#total_shipped_value').text(total_shipped_value);
                             $('#total_short_qty').text(total_short_qty);
                             $('#total_short_value').text(total_short_value);
                             $('#total_excess_short_shipment_qty').text(total_excess_short_shipment_qty);
                             $('#total_excess_short_shipment_value').text(total_excess_short_shipment_value);
                         });
                     </script>
                 </div>

             </div>
         </div>
     </div>
     </div>
     <!-- ShipmentModal End-->

     <script>
         setInterval(function() {
             $.ajax({
                 url: '{{ route('jobs.index') }}', // Adjust route if needed
                 type: 'GET',
                 success: function(data) {
                     $('#jobTableBody').html(data.html); // Inject new job data into the table body
                     console.log('Data updated', data);

                 }
             });
         }, 5000); // Poll every 5 seconds
     </script>
     <script>
         $('#jobModal').on('show.bs.modal', function(event) {
             var button = $(event.relatedTarget); // Button that triggered the modal
             var jobId = button.data('job-id'); // Extract info from data-* attributes
             var jobNo = button.data('job-no');

             // Update the modal's content.
             var modal = $(this);
             modal.find('.modal-title span#modalJobNo').text(jobNo);
             modal.find('a#viewJobLink').attr('href', '/jobs/' + jobId);
             modal.find('a#editJobLink').attr('href', '/jobs/' + jobId + '/edit_jobs');
             modal.find('a#calendarJobLink').attr('href', '/shipments/create/' + jobId);
             modal.find('a#SewingBalance').attr('href', '/sewing_balances/create/' + jobId);
             modal.find('form#deleteJobForm').attr('action', '/jobs/' + jobId);
         });
     </script>




 </x-backend.layouts.master>
