<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Journal Records</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 10px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header {
            display: flex;
            align-items: flex-start;
            border-bottom: 1px solid #d3d3d3;
            padding-bottom: 50px;
            margin-bottom: 5px;
            margin-top: 1px;
        }
        .header-logo {
            flex: 0 0 90px;
            margin-right: 10px;
        }
        .header-text {
            flex: 1;
            text-align: center;
            margin-top: -65px;
        }
        .header-title {
            font-size: 15px;
            font-weight: bold;
            line-height: 1.2;
        }
        .header-subtitle {
            font-size: 12px;
            margin-top: 5px;
        }
        .page-title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin: 20px 0 10px 0;
        }
        .entry {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #bbbbbb;
            page-break-inside: avoid;
        }
        .day {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 5px;
        }
        .content {
            margin-bottom: 10px;
            text-align: justify;
            width: 100%;
            word-wrap: break-word;
            overflow-wrap: break-word;
            white-space: normal;
            font-size: 14px;
            
        }
        .images {
            margin-top: 50px;
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start;
            align-items: center;
            gap: 10px;
            width: 100%;
            page-break-inside: avoid;
        }
        .images img {
            width: 100%;
            max-width: 120px;
            max-height: 120px;
            height: auto;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            margin: 0;
            display: block;
        }
    </style>
</head>
<body>
@php
    $logoPath = public_path('images/ustp-logo.png');
    $logoData = base64_encode(file_get_contents($logoPath));
@endphp

<div class="header">
    <div class="header-logo">
        <img src="data:image/png;base64,{{ $logoData }}" alt="USTP Logo" style="height: 85px;">
    </div>
    <div class="header-text">
        <div class="header-title">
            UNIVERSITY OF SCIENCE AND TECHNOLOGY OF SOUTHERN PHILIPPINES
        </div>
        <div class="header-subtitle">
            Alubijid | Balubal | Cagayan de Oro | Claveria | Jasaan | Oroquieta | Panaon | Villanueva
        </div>
    </div>
</div>
<h1 style="text-align: center;">Journal Record</h1>
@php
    $dayCounter = 1;
@endphp
@foreach($journals->sortBy('created_at') as $journal)
    <div class="entry">
        <div class="day">Day {{ $dayCounter }}</div>
        <div class="content">{{ $journal->content }}</div>
        @if($journal->image && !empty($journal->base64Images))
            <div class="images" style="text-align: center;">
                @foreach($journal->base64Images as $base64Image)
                    <img src="{{ $base64Image }}" alt="Journal Image" />
                @endforeach
            </div>
        @endif
    </div>
    @php $dayCounter++; @endphp
@endforeach
</body>
</html>
