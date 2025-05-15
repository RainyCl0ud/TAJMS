<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Journal Records</title>
    <style>
        @page { margin: 40px; }
        body { 
            font-family: "Times New Roman", serif; 
            font-size: 16px; 
            line-height: 1.6; 
            text-align: justify; 
            margin: 40px;
        }
        h2 { text-align: center; margin-bottom: 20px; font-size: 20px; }
        .entry { margin-bottom: 40px; padding-bottom: 20px; border-bottom: 1px solid #ccc; }
        .day { font-weight: bold; font-size: 18px; margin-bottom: 5px; }
        .content { 
    margin-bottom: 50px; 
    text-align: justify; 
    width: 100%; 
    word-wrap: break-word; /* Allows long words to break */
    overflow-wrap: break-word; /* Ensures text wraps within container */
    white-space: normal; /* Prevents text from staying on one line */
}



.images { 
    margin-top: 10px; 
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); /* Dynamic columns */
    place-content: center; /* Ensures centering */
    width: 100%;
    padding: 20px;
    gap: 20px; /* Adds gap between images */
    text-align: center;
}

.images img { 
    width: 150px; 
    height: 150px; 
    object-fit: cover; 
    border: 1px solid #aaa; 
    padding: 3px; 
    display: inline-block;
}

    </style>
</head>
<body>

    <h2>Journal Records</h2>

    @php
        $dayCounter = 1; // Start counting days from 1
    @endphp

    @foreach($journals->sortBy('created_at') as $journal)
        <div class="entry">
            <div class="day">Day {{ $dayCounter }}</div>
            <div class="content">{{ $journal->content }}</div>

            @if($journal->image)
                @php
                    $images = json_decode($journal->image, true);
                @endphp
                <div class="images">
                @foreach($images as $image)
    @php
        $imagePath = public_path('storage/' . $image);
        if (file_exists($imagePath)) {
            $imageData = base64_encode(file_get_contents($imagePath));
            $mimeType = mime_content_type($imagePath);
        }
    @endphp

    @if (!empty($imageData))
        <img src="data:{{ $mimeType }};base64,{{ $imageData }}" alt="Journal Image">
    @else
        <p style="color:red;">Image not found: {{ $image }}</p>
    @endif
@endforeach

                </div>
            @endif
        </div>
        @php $dayCounter++; @endphp
    @endforeach

</body>
</html>
