<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Records</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Attendance Records</h1>
    <h2>{{Auth::user()->first_name . ' ' . Auth::user()->middle_name . ' ' . Auth::user()->last_name}}</h2>
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
