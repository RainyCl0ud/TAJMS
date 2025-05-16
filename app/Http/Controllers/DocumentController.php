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
    
        $existingDocument = Document::where('user_id', Auth::id())
            ->where('document_type', $request->type)
            ->first();
    
        if ($existingDocument) {
            // Optionally delete from Google Drive by file ID if you store it
            $existingDocument->delete();
        }
    
        $file = $request->file('document');
        $fileName = $file->getClientOriginalName();
        $filePath = $file->storeAs('', $fileName, 'google'); // 'google' is the disk name
    
        // Extract file ID from the path
        $googleDriveFileId = Storage::disk('google')->getMetadata($filePath)['path'] ?? null;
    
        // Create a shareable link (or thumbnail URL)
        $publicUrl = "https://drive.google.com/file/d/{$googleDriveFileId}/view";
    
        Document::create([
            'user_id' => Auth::id(),
            'document_type' => $request->type,
            'document_path' => $publicUrl,
            'status' => 'pending',
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
