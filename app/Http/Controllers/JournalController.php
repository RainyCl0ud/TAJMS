<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Services\GoogleDriveService;



class JournalController extends Controller
{
    protected $drive;

    public function __construct(GoogleDriveService $drive)
    {
        $this->drive = $drive;
    }

    public function index()
    {
        $pageTitle = 'My Journals';
        $journals = Journal::where('user_id', Auth::id())
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($journal, $index) {
                $journal->day = 'Day ' . ($index + 1); 
                return $journal;
            })->reverse();  
    
        return view('journal.index', compact('journals', 'pageTitle'));
    }

    public function show($id)
{
    $journal = Journal::findOrFail($id);
    $trainee = $journal->user;

    // Fetch all journals of the trainee and recalculate the day
    $journals = Journal::where('user_id', $trainee->id)
        ->orderBy('created_at', 'desc')
        ->get();

    // Find the position of this journal in the list
    $journalIndex = $journals->search(fn($j) => $j->id === $journal->id);

    // Calculate the day based on the reversed order
    $day = 'Day ' . ($journals->count() - $journalIndex);

    // Set the page title dynamically with the trainee's name and day
    $pageTitle =  $trainee->first_name ." ". $trainee->last_name .  " / Journal / "  . $day;

    return view('journal.showEntry', compact('journal', 'pageTitle', 'trainee', 'day'));
    }
    
    public function create()
    {
        $pageTitle = 'Create new journal';
        return view('journal.create', compact (('pageTitle')));
    }






    
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|max:65535',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        $imageUrls = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $mimeType = $image->getMimeType();
                $tempPath = $image->getRealPath();
                $originalName = $image->getClientOriginalName();

                try {
                    $uploadedUrl = $this->drive->uploadFile($tempPath, $mimeType, $originalName);
                    if ($uploadedUrl) {
                        $imageUrls[] = str_replace('@https://', 'https://', $uploadedUrl);
                    }
                } catch (\Exception $e) {
                    \Log::error('Error uploading journal image to Google Drive', [
                        'user_id' => Auth::id(),
                        'exception_message' => $e->getMessage(),
                        'file_name' => $originalName,
                    ]);
                    return redirect()->back()->with('error', 'Failed to upload one or more images. Please try again.');
                }
            }
        }

        Journal::create([
            'user_id' => Auth::id(),
            'content' => $request->input('content'),
            'image' => json_encode($imageUrls),
        ]);

        return redirect()->route('journal.index')->with('success', 'Journal entry created successfully.');
    }
    





    public function update(Request $request, $id)
{
    $request->validate([
        'content' => 'required|string|min:10', // Ensure content is not empty and has a minimum length of 10
    ]);

    // Find the journal entry
    $journal = Journal::findOrFail($id);
    
    // Update the content
    $journal->content = $request->input('content');
    $journal->save();
    
    // Redirect back to the show page without 'edit' mode and with a success message
    return redirect()->route('journal.show', $id)->with('success', 'Journal entry updated successfully.');
    }

    
    public function previewPdf()
{
    try {
        $user = Auth::user();

        $journals = Journal::where('user_id', $user->id)
            ->orderBy('created_at', 'asc')
            ->get();

        // Prepare journals with base64 images
        foreach ($journals as $journal) {
            if ($journal->image) {
                $images = json_decode($journal->image, true);
                $base64Images = [];

                foreach ($images as $imageUrl) {
                    // Download the image data from Google Drive
                    $imageData = @file_get_contents($imageUrl);

                    if ($imageData !== false) {
                        $finfo = finfo_open();
                        $mimeType = finfo_buffer($finfo, $imageData, FILEINFO_MIME_TYPE);
                        finfo_close($finfo);

                        // Encode image data as base64 string
                        $base64 = 'data:' . $mimeType . ';base64,' . base64_encode($imageData);
                        $base64Images[] = $base64;
                    }
                }

                $journal->base64Images = $base64Images;
            } else {
                $journal->base64Images = [];
            }
        }

        $pdf = Pdf::loadView('journal.pdf', compact('journals'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream('journal_report_' . $user->id . '.pdf');

    } catch (\Exception $e) {
        Log::error('Journal PDF generation failed: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Failed to generate Journal PDF.',
            'error' => $e->getMessage()
        ], 500);
    }
}

    
    }

   
     

