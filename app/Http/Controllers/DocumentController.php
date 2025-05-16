<?php 

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\GoogleDriveService;

class DocumentController extends Controller
{
    protected $drive;

    public function __construct(GoogleDriveService $drive)
    {
        $this->drive = $drive;
    }

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
        $type = $request->type;
        $file = $request->file('document');

        $mimeType = $file->getMimeType();
        $tempPath = $file->getRealPath();
        $originalName = $file->getClientOriginalName();

        Log::info('Starting document upload', [
            'user_id' => Auth::id(),
            'original_name' => $originalName,
            'mime_type' => $mimeType,
            'temp_path_exists' => file_exists($tempPath),
        ]);

        try {
            $fileUrl = $this->drive->uploadFile($tempPath, $mimeType, $originalName);

            if (!$fileUrl) {
                Log::error('GoogleDriveService returned null URL during upload', [
                    'user_id' => Auth::id(),
                    'original_name' => $originalName,
                ]);
                return back()->withErrors(['document' => 'Failed to upload to Google Drive. Please try again.']);
            }

            // Remove existing document
            Document::where('user_id', Auth::id())
                ->where('document_type', $type)
                ->delete();

            Document::create([
                'user_id' => Auth::id(),
                'document_type' => $type,
                'document_path' => str_replace('@https://', 'https://', $fileUrl),
                'status' => 'pending',
            ]);

            Log::info('Document uploaded successfully', [
                'user_id' => Auth::id(),
                'file_url' => $fileUrl,
            ]);

            return redirect()->route('pre_user.dashboard')->with('success', 'Document uploaded successfully!');

        } catch (\Exception $e) {
            Log::error('Exception during Google Drive upload', [
                'user_id' => Auth::id(),
                'exception_message' => $e->getMessage(),
            ]);

            return back()->withErrors(['document' => 'An error occurred while uploading the document.']);
        }
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
