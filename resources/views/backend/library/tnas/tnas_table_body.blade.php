 
  @php
      $sl = 1;
  @endphp
  @forelse ($tnas as $tna)
      <tr>
          @if ($tna->order_close == 1)
              <td class="bg-red" >{{ $sl++ }}</td>
          @else
              @if (auth()->user()->role_id == 4 || auth()->user()->role_id == 1)
                  <td >
                      <a href="{{ route('tnas.show', $tna->id) }}" class="btn btn-sm btn-outline-success"
                          data-toggle="tooltip" data-placement="top" title="show">
                          <i class="fas fa-eye"></i>{{ $sl++ }}
                      </a>
                  </td>
              @elseif (auth()->user()->role_id == 3)
                  @php
                      $privileges = DB::table('buyer_assigns')
                          ->where('buyer_id', $tna->buyer_id)
                          ->where('user_id', auth()->user()->id)
                          ->count();
                      // dd($privileges)
                  @endphp
                  @if ($privileges > 0)
                      <td >
                          <a href="{{ route('tnas.show', $tna->id) }}" class="btn btn-sm btn-outline-success"
                              data-toggle="tooltip" data-placement="top" title="show">
                              <i class="fas fa-eye"></i>{{ $sl++ }}
                          </a>
                      </td>
                  @else
                      <td >{{ $sl++ }}</td>
                  @endif
              @else
                  <td >{{ $sl++ }}</td>
              @endif
          @endif
          <td >{{ $tna->buyer }}</td>
          <td >{{ $tna->style }}</td>
          <td >{{ $tna->po }}</td>
          <td >{{ $tna->item }}</td>
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

                          // if actual date is empty and plane date have value then if plan date is today or past then bg color red else plan date before 2 days then bg color yellow else bg color light example: if plan date is 10-10-2021 and actual date is empty and today date is 10-10-2021 then bg color red if plan date is 8-10-2021 and actual date is empty then bg color yellow if plan date is 9-10-2021 and actual date is empty then bg color light
                          if ($type === 'plan' && empty($tna->{$task . '_actual'})) {
                              if ($cellDate->isToday() || $cellDate->lt($today)) {
                                  $cellClass = 'bg-red';
                              } elseif ($diffDays <= 2) {
                                  $cellClass = 'bg-yellow';
                              } else {
                                  $cellClass = 'bg-light';
                              }
                          }

                          //if actual date and plan date both have value then check if actual date is same or date over then plan date then bg color red expample: if plan date is 10-10-2021 and actual date is 10-10-2021 or 12-10-2021 then bg color red

                          // if ($type === 'actual' && $tna->{$task . '_plan'}) {
                          //     $planDate = \Carbon\Carbon::parse($tna->{$task . '_plan'});
                          //     if ($cellDate->isToday() || $cellDate->gt($planDate)) {
                          //         $cellClass = 'bg-red';
                          //     }
                          // }

                          //if actual date and plan date both have value then check if actual date is date over then plan date then text front red and blod expample: if plan date is 10-10-2021 and actual date is  12-10-2021 then text front red and blod
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

                          //explanation show from tna_explanations table if plan date is over from the actual date then show explanation in bootstrap tooltip
                          // Retrieve explanation for the actual date
                      } elseif ($date == 'N/A') {
                          $date = 'N/A';
                      }
                  @endphp
                  <!-- if actual date is empty then modal button show else show date -->
                  @if ($type === 'actual' && empty($date))
                      @if (auth()->user()->role_id == 3)
                          @php
                              $buyer_privilage = DB::table('buyer_assigns')
                                  ->where('buyer_id', $tna->buyer_id)
                                  ->where('user_id', auth()->user()->id)
                                  ->count();
                              // dd($buyer_privilage);
                          @endphp
                          @if ($buyer_privilage > 0)
                              <td class="{{ $cellClass }}" data-id="{{ $tna->id }}"
                                  data-task="{{ $task . '_' . $type }}" onclick="openModal(this)"
                                  data-plan-date="{{ $tna->{$task . '_plan'} }}">
                              </td>
                          @endif
                      @else
                          <td></td>
                      @endif
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
          {{-- @dd($explanation); --}}

      </tr>
  @empty
      <tr>
          <td colspan="36" class="text-center">No TNA Found</td>
      </tr>
  @endforelse
