<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\JsonResponse;


class JournalController extends Controller
{
     
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
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // max 5MB
        ]);
    
        $imageUrls = [];
    
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                // Store image in Google Drive (in "journal_images" folder)
                $path = $image->store('journal_images', 'google');
    
                // Extract Google Drive file ID
                $fileId = basename($path);
    
                // Generate public preview/download URL
                $url = "https://drive.google.com/uc?id={$fileId}&export=download";
    
                $imageUrls[] = $url;
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

    


public function previewPdf(): JsonResponse
{
    $journals = Journal::with('user')->orderBy('created_at', 'desc')->get();

    // Generate the PDF with a refined layout
    $pdf = Pdf::loadView('journal.pdf', compact('journals'))
        ->setPaper('A4', 'portrait');

    // Define a unique PDF filename
    $pdfPath = 'journal_pdfs/journal_records_' . time() . '.pdf';

    // Store the PDF in public storage
    Storage::disk('public')->put($pdfPath, $pdf->output());

    // Return JSON response with the correct URL
    return response()->json(['url' => asset('storage/' . $pdfPath)]);
}

    }

   
     

