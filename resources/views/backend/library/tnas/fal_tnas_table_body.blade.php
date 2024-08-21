 @php
     $sl = 1;
 @endphp
 @forelse ($tnas as $tna)
     <tr>
         <td>{{ $sl++ }}</td>
         <td>{{ $tna->buyer }}</td>
         <td class="text-wrap"> {{ str_replace(',', ' ', $tna->style) }}</td>
         <td class="text-wrap"> {{ str_replace(',', ' ', $tna->po) }}</td>
         <td>{{ $tna->item }}</td>
         <td id="qty_pcs">{{ $tna->qty_pcs }}</td>
         <td>{{ \Carbon\Carbon::parse($tna->po_receive_date)->format('d-M-y') ?? '' }}</td>
         <td class="text-bold" id="shortablerow">
             {{ \Carbon\Carbon::parse($tna->shipment_etd)->format('d-M-y') ?? '' }}</td>
         <td id="total_lead_time">{{ $tna->total_lead_time }}</td>
         <td id="order_free_time">
             @if ($tna->pp_meeting_actual == null)
                 @php
                     $today = \Carbon\Carbon::parse($tna->pp_meeting_plan);
                     $shipment_etd = \Carbon\Carbon::parse($tna->shipment_etd);
                     $diffDays = $today->diffInDays($shipment_etd, false);
                     if ($diffDays > 0) {
                         echo $diffDays;
                     } else {
                         echo '0';
                     }
                 @endphp
             @else
                 @php
                     $today = \Carbon\Carbon::parse($tna->pp_meeting_plan);
                     $shipment_etd = \Carbon\Carbon::parse($tna->pp_meeting_actual);
                     $diffDays = $today->diffInDays($shipment_etd, false);
                     if ($diffDays > 0) {
                         echo $diffDays;
                     } else {
                         echo '0';
                     }
                 @endphp
             @endif

         </td>
         @foreach (['lab_dip_submission', 'fabric_booking', 'fit_sample_submission', 'print_strike_off_submission', 'bulk_accessories_booking', 'fit_comments', 'bulk_yarn_inhouse', 'bulk_accessories_inhouse', 'pp_sample_submission', 'bulk_fabric_knitting', 'pp_comments_receive', 'bulk_fabric_dyeing', 'bulk_fabric_delivery', 'pp_meeting', 'etd'] as $task)
             @foreach (['plan', 'actual'] as $type)
                 @php
                     $date = $tna->{$task . '_' . $type};
                     $cellClass = '';
                     $explanation = ''; // Default explanation to empty
                     if ($date && $date != 'N/A') {
                         $today = \Carbon\Carbon::now();
                         $cellDate = \Carbon\Carbon::parse($date);
                         $diffDays = $today->diffInDays($cellDate, false);

                         if ($type === 'plan' && empty($tna->{$task . '_actual'})) {
                             if ($cellDate->isToday() || $cellDate->lt($today)) {
                                 $cellClass = 'bg-red';
                             } elseif ($diffDays <= 2) {
                                 $cellClass = 'bg-yellow';
                             } else {
                                 $cellClass = 'bg-light';
                             }
                         }

                         if ($type === 'actual' && $tna->{$task . '_plan'}) {
                             $planDate = \Carbon\Carbon::parse($tna->{$task . '_plan'});
                             $actualDate = \Carbon\Carbon::parse($date);
                             if ($cellDate->gt($planDate)) {
                                 $cellClass = 'text-danger font-weight-bold';
                             }
                             if ($cellDate->gt($actualDate)) {
                                 $cellClass = 'bg-light';
                             }
                         }

                         // Retrieve explanation for the actual date
                     } elseif ($date == 'N/A') {
                         $date = 'N/A';
                     }
                 @endphp
                 <!-- if actual date is empty then modal button show else show date -->
                 @if ($type === 'actual' && empty($date))
                     <td></td>
                 @else
                     @php
                         $explanation =
                             DB::table('tna_explanations')
                                 ->where('perticulars', $task . '_' . $type)
                                 ->where('tna_id', $tna->id)
                                 ->first()->explanation ?? '';
                         // dd($tna->id);
                     @endphp
                     <td class="{{ $cellClass }}" data-toggle="tooltip" data-placement="top"
                         title="{{ $explanation }}">
                         {{ $date == 'N/A' ? 'N/A' : ($date ? \Carbon\Carbon::parse($date)->format('d-M-y') : '') }}
                     </td>
                 @endif
             @endforeach
         @endforeach

     </tr>
 @empty
     <tr>
         <td colspan="36" class="text-center">No TNA Found</td>
     </tr>
 @endforelse
