<!DOCTYPE html>
<html>
<head>
    <title>Buyer Wise TNA Summary</title>
</head>
<body>
    <h1>Buyer Wise TNA Summary</h1>
    @foreach ($buyers as $buyerName => $buyerData)
        <h2>{{ $buyerName }}</h2>
        <table border="1" cellpadding="5" cellspacing="0">
            <thead>
                <tr>
                    <th>Task</th>
                    <th>Style</th>
                    <th>PO</th>
                    <th>Plan Date</th>
                    <th>Shipment ETD</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($buyerData['details'] as $task => $details)
                    @foreach ($details as $detail)
                        <tr>
                            <td>{{ $task }}</td>
                            <td>{{ $detail['style'] }}</td>
                            <td>{{ $detail['po'] }}</td>
                            <td>{{ $detail['PlanDate'] }}</td>
                            <td>{{ $detail['shipment_etd'] }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    @endforeach

    
</body>
</html>
