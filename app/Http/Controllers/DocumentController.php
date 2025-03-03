<?php 

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
   
    public function upload($type)
    {
        $pageTitle = 'Pre user dashboard'; 
        $document = Document::where('user_id', Auth::id())->where('document_type', $type)->first();
        return view('documents.upload', compact('type', 'document', 'pageTitle'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        // Check if there's an existing document for the given type
        $existingDocument = Document::where('user_id', Auth::id())
            ->where('document_type', $request->type)
            ->first();

        if ($existingDocument) {
            // Delete the old document from storage
            Storage::delete('public' . $existingDocument->document_path);

            // Remove the old document record from the database
            $existingDocument->delete();
        }

        // Store the new document
        $path = $request->file('document')->store('document_images', 'public');

        // Create a new document entry
        Document::create([
            'user_id' => Auth::id(),
            'document_type' => $request->type,
            'document_path' => $path,
            'status' => 'pending', // Set the new document status to pending
        ]);

        return redirect()->route('pre_user.dashboard')->with('success', 'Document uploaded successfully!');
    }

    public function updateStatus(Request $request, Document $document)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $document->update([
            'status' => $request->status,
        ]);

        return response()->json(['message' => 'Document status updated successfully.']);
    }

    
    
    

}
