<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Record</title>
        <style>
            body { font-family: Arial, sans-serif; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid black; padding: 10px; text-align: left; }
            th { background-color: #f2f2f2; }
        </style>
</head>
<body>
    @php
        $logoPath = public_path('images/ustp-logo.png');
        $logoData = base64_encode(file_get_contents($logoPath));
    @endphp
    <div style="display: flex; align-items: flex-start; border-bottom: 1px solid #d3d3d3; padding-bottom: 50px; margin-bottom: 5px; margin-top: 1px;">
        <div style="flex: 0 0 90px; margin-right: 10px;">
            <img src="data:image/png;base64,{{ $logoData }}" alt="USTP Logo" style="height: 85px;">
        </div>
        <div style="flex: 1; text-align: center; margin-top: -65px;">
            <div style="font-size: 15px; font-weight: bold; line-height: 1.2;">
                UNIVERSITY OF SCIENCE AND TECHNOLOGY OF SOUTHERN PHILIPPINES
            </div>
            <div style="font-size: 12px; margin-top: 5px;">
                Alubijid | Balubal | Cagayan de Oro | Claveria | Jasaan | Oroquieta | Panaon | Villanueva
            </div>
        </div>
    </div>
    <h1 style="text-align: center;">Attendance Records</h1>
    @php use Illuminate\Support\Str; @endphp
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Time In</th>
                <th>Time Out</th>   
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($attendances as $attendance)
            <tr>
                <td>{{ $attendance->date }}</td>
                <td>{{ $attendance->in_time ? $attendance->in_time->format('h:i A') : '--' }}</td>
                <td>{{ $attendance->out_time ? $attendance->out_time->format('h:i A') : '--' }}</td>
                <td>{{ $attendance->status}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
