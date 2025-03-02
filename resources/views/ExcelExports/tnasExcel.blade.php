<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>TNA Export</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            font-size: 12px;
        }

        th {
            background-color: #343a40;
            color: white;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <table border="1">
        <thead>
            <tr>
                <th>Buyer</th>
                <th>Style</th>
                <th>PO Number</th>
                <th>Item</th>
                <th>Qty (pcs)</th>
                <th>PO Receive Date</th>
                <th>Shipment ETD</th>
                <th>Total Lead Time</th>
                <th>Order Free Time</th>
                <th>Lab Dip Submission (Plan)</th>
                <th>Lab Dip Submission (Actual)</th>
                <th>Fabric Booking (Plan)</th>
                <th>Fabric Booking (Actual)</th>
                <th>Fit Sample Submission (Plan)</th>
                <th>Fit Sample Submission (Actual)</th>
                <th>Print Strike Off Submission (Plan)</th>
                <th>Print Strike Off Submission (Actual)</th>
                <th>Bulk Accessories Booking (Plan)</th>
                <th>Bulk Accessories Booking (Actual)</th>
                <th>Fit Comments (Plan)</th>
                <th>Fit Comments (Actual)</th>
                <th>Bulk Yarn Inhouse (Plan)</th>
                <th>Bulk Yarn Inhouse (Actual)</th>
                <th>Bulk Accessories Inhouse (Plan)</th>
                <th>Bulk Accessories Inhouse (Actual)</th>
                <th>PP Sample Submission (Plan)</th>
                <th>PP Sample Submission (Actual)</th>
                <th>Bulk Fabric Knitting (Plan)</th>
                <th>Bulk Fabric Knitting (Actual)</th>
                <th>PP Comments Receive (Plan)</th>
                <th>PP Comments Receive (Actual)</th>
                <th>Bulk Fabric Dyeing (Plan)</th>
                <th>Bulk Fabric Dyeing (Actual)</th>
                <th>Bulk Fabric Delivery (Plan)</th>
                <th>Bulk Fabric Delivery (Actual)</th>
                <th>PP Meeting (Plan)</th>
                <th>PP Meeting (Actual)</th>
                <th>Fabrics and Accessories Inspection (Plan)</th>
                <th>Fabrics and Accessories Inspection (Actual)</th>
                <th>Size Set Making (Plan)</th>
                <th>Size Set Making (Actual)</th>
                <th>Pattern Correction (Plan)</th>
                <th>Pattern Correction (Actual)</th>
                <th>Machines, Layout, and Folder Preparation (Plan)</th>
                <th>Machines, Layout, and Folder Preparation (Actual)</th>
                <th>Bulk Cutting Start (Plan)</th>
                <th>Bulk Cutting Start (Actual)</th>
                <th>Print/Emb. Start (Plan)</th>
                <th>Print/Emb. Start (Actual)</th>
                <th>Bulk Sewing Input (Plan)</th>
                <th>Bulk Sewing Input (Actual)</th>
                <th>Bulk Wash Start (Plan)</th>
                <th>Bulk Wash Start (Actual)</th>
                <th>Bulk Finishing Start (Plan)</th>
                <th>Bulk Finishing Start (Actual)</th>
                <th>Bulk Cutting Close (Plan)</th>
                <th>Bulk Cutting Close (Actual)</th>
                <th>Print/Emb. Close (Plan)</th>
                <th>Print/Emb. Close (Actual)</th>
                <th>Bulk Sewing Close (Plan)</th>
                <th>Bulk Sewing Close (Actual)</th>
                <th>Bulk Wash Close/Finishing Received (Plan)</th>
                <th>Bulk Wash Close/Finishing Received (Actual)</th>
                <th>Bulk Finishing Close (Plan)</th>
                <th>Bulk Finishing Close (Actual)</th>
                <th>Pre-final Inspection (Plan)</th>
                <th>Pre-final Inspection (Actual)</th>
                <th>Final Inspection (Plan)</th>
                <th>Final Inspection (Actual)</th>
                <th>Ex-factory (Plan)</th>
                <th>Ex-factory (Actual)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tnas as $tna)
                <tr>
                    <td>{{ $tna->buyer }}</td>
                    <td>{{ $tna->style }}</td>
                    <td>{{ $tna->po }}</td>
                    <td>{{ $tna->item }}</td>
                    <td>{{ $tna->qty_pcs }}</td>
                    <td>{{ $tna->po_receive_date ? \Carbon\Carbon::parse($tna->po_receive_date)->format('d-M-Y') : '' }}
                    </td>
                    <td>{{ $tna->shipment_etd ? \Carbon\Carbon::parse($tna->shipment_etd)->format('d-M-Y') : '' }}</td>
                    <td>{{ $tna->total_lead_time }}</td>
                    <td>
                        @php
                            $orderFreeTime = $tna->pp_meeting_actual
                                ? \Carbon\Carbon::parse($tna->pp_meeting_actual)->diffInDays(
                                    \Carbon\Carbon::parse($tna->shipment_etd),
                                    false,
                                )
                                : \Carbon\Carbon::parse($tna->pp_meeting_plan)->diffInDays(
                                    \Carbon\Carbon::parse($tna->shipment_etd),
                                    false,
                                );
                            echo max($orderFreeTime, 0);
                        @endphp
                    </td>
                    @foreach (['lab_dip_submission', 'fabric_booking', 'fit_sample_submission', 'print_strike_off_submission', 'bulk_accessories_booking', 'fit_comments', 'bulk_yarn_inhouse', 'bulk_accessories_inhouse', 'pp_sample_submission', 'bulk_fabric_knitting', 'pp_comments_receive', 'bulk_fabric_dyeing', 'bulk_fabric_delivery', 'pp_meeting', 'fabrics_and_accessories_inspection', 'size_set_making', 'pattern_correction', 'machines_layout', 'cutting', 'print_start', 'bulk_sewing_input', 'bulk_wash_start', 'bulk_finishing_start', 'bulk_cutting_close', 'print_close', 'bulk_sewing_close', 'bulk_wash_close', 'bulk_finishing_close', 'pre_final_inspection', 'final_inspection', 'ex_factory'] as $task)
                        <td>
    @if(!empty($tna->{$task . '_plan'}) && strtotime($tna->{$task . '_plan'}))
        {{ \Carbon\Carbon::parse($tna->{$task . '_plan'})->format('d-M-Y') }}
    @else
        N/A
    @endif
</td>
<td>
    @if(!empty($tna->{$task . '_actual'}) && strtotime($tna->{$task . '_actual'}))
        {{ \Carbon\Carbon::parse($tna->{$task . '_actual'})->format('d-M-Y') }}
    @else
        N/A
    @endif
</td>

                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
