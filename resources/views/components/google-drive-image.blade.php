@props([
    'url',
    'alt' => 'Image',
    'class' => '',
    'defaultImage' => asset('storage/images/default.png')
])

@php
    use App\Helpers\GoogleDriveHelper;
    $thumbnailUrl = GoogleDriveHelper::getThumbnailUrl($url);
@endphp

<img src="{{ $thumbnailUrl ?? $defaultImage }}"
     alt="{{ $alt }}"
     class="{{ $class }}"
     onerror="this.onerror=null;this.src='{{ $defaultImage }}'; console.error('Failed to load image from Google Drive');"
> 