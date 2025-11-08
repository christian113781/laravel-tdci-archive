<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Archive;
use App\Models\Program;
use App\Models\Keyword;
use App\Models\Bookmark;
use Illuminate\Support\Facades\Storage;
use App\Models\ArchiveAccessRequest;

class ArchiveController extends Controller {

    public function index(Request $request){

    $query = Archive::with(['program','user', 'keywords'])
                    ->where('status', 'Publish');

    
    $totalArchives = Archive::where('status', 'Publish')->count();

    
    if ($request->filled('field') && $request->filled('search')) {
        $search = $request->input('search');
        $field  = $request->input('field');

        switch ($field) {
            case '1': 
                $query->where('title', 'like', "%$search%");
                break;
            case '2': 
                $query->where('authors', 'like', "%$search%");
                break;
            case '3':
                $query->whereHas('keywords', function($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                });
                break;
            case '4':
                $query->where('year', $search);
                break;
            case '5':
                $query->whereHas('program', function($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                });
                break;
            case '6': 
                $query->where('subject', 'like', "%$search%");
                break;
        }
    }

    
    $filteredCount = $query->count();

    
    $query->orderByDesc('views')->orderByDesc('created_at');
    $archives = $query->paginate(50)->withQueryString();

    return view('shared.archive', compact('archives', 'totalArchives', 'filteredCount'));
    }   








    public function indexPatron(Request $request){

    $query = Archive::with(['program','user', 'keywords','accessRequests'])
                    ->where('status', 'Publish');



   
    $totalArchives = Archive::where('status', 'Publish')->count();

    
    if ($request->filled('field') && $request->filled('search')) {
        $search = $request->input('search');
        $field  = $request->input('field');

        switch ($field) {
            case '1': 
                $query->where('title', 'like', "%$search%");
                break;
            case '2': 
                $query->where('authors', 'like', "%$search%");
                break;
            case '3':
                $query->whereHas('keywords', function($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                });
                break;
            case '4':
                $query->where('year', $search);
                break;
            case '5':
                $query->whereHas('program', function($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                });
                break;
            case '6': 
                $query->where('subject', 'like', "%$search%");
                break;
        }
    }

    $filteredCount = $query->count();

    
    $query->orderByDesc('views')->orderByDesc('created_at');
    $archives = $query->paginate(50)->withQueryString();

    return view('shared.archive_patron', compact('archives', 'totalArchives', 'filteredCount'));
}

























    public function archiveDetails($id) {

    $archive = Archive::with('keywords')->findOrFail($id);
    
    $archive->increment('views');
    $basePath = 'public/archives/' . $archive->archive_code . '/';

    $figuresSize = $this->getFileSize($basePath . 'figures.pdf');
    $recommendationSize = $this->getFileSize($basePath . 'recommendation.pdf');
    $tablesSize = $this->getFileSize($basePath . 'tables.pdf');
    $thesisSize = $this->getFileSize($basePath . 'thesis.pdf');

    return view('shared.archive_details', compact('archive','figuresSize','recommendationSize','tablesSize','thesisSize'));
    
    }
    /**
    * Get the size of a file in a human-readable format.
    *
     * @param  string  $filePath
    * @return string
    */
    private function getFileSize($filePath) {
        
    if (Storage::exists($filePath)) {
        $size = Storage::size($filePath);
        return $this->formatFileSize($size);
    }
    return 'No File';
    }

    /**
     * Format the file size into a human-readable format.
       *
    * @param  int  $bytes
    * @return string
    */
private function formatFileSize($bytes) {

    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);

    return round($bytes, 2) . ' ' . $units[$pow];

    }

    public function requestAccess(Request $request, $id)
{
    $userId = auth()->id();
    $archiveId = $id;

    // Optionally -> find the archive model
    $archive = Archive::findOrFail($archiveId);

    // Now your logic
    $alreadyRequested = ArchiveAccessRequest::where('user_id', $userId)
                        ->where('archive_id', $archiveId)
                        ->exists();

    if ($alreadyRequested) {
        return redirect()->back()->with('success', 'You have already requested access.');
    }

    ArchiveAccessRequest::create([
        'user_id'    => $userId,
        'archive_id' => $archiveId,
        'status'     => 'pending',
        'approved_by'=> null,
    ]);

    return redirect()->back()->with('success', 'Access request submitted successfully.');
}
}
