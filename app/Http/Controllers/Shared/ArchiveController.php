<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\Archive;
use App\Models\ArchiveAccessRequest;
use App\Models\ArchiveViewLog;
use App\Models\SearchLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Milon\Barcode\DNS1D;

class ArchiveController extends Controller
{
    public function index(Request $request)
    {

        $query = Archive::with(['program', 'user', 'keywords'])
            ->where('status', 'Publish');

        $totalArchives = Archive::where('status', 'Publish')->count();

        if ($request->filled('field') && $request->filled('search')) {
            $search = $request->input('search');
            $field = $request->input('field');

            switch ($field) {
                case '1':
                    $query->where('title', 'like', "%$search%");
                    break;
                case '2':
                    $query->where('authors', 'like', "%$search%");
                    break;
                case '3':
                    $query->whereHas('keywords', function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%");
                    });
                    break;
                case '4':
                    $query->where('year', $search);
                    break;
                case '5':
                    $query->whereHas('program', function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%");
                    });
                    break;
                case '6':
                    $query->where('subject', 'like', "%$search%");
                    break;
            }
        }

        $filteredCount = $query->count();

        $query->orderByDesc('created_at');
        $archives = $query->paginate(50)->withQueryString();

        return view('shared.archive', compact('archives', 'totalArchives', 'filteredCount'));
    }

    public function indexPatron(Request $request)
    {

        $query = Archive::with(['program', 'user', 'keywords', 'accessRequests'])
            ->where('status', 'Publish');

        $totalArchives = Archive::where('status', 'Publish')->count();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $field = $request->input('field');
            $fieldNames = ['1' => 'Title', '2' => 'Author', '3' => 'Keyword', '4' => 'Year', '5' => 'Program', '6' => 'Abstract'];

            // Log the search ONLY for patron users
            if (auth()->check() && auth()->user()->role === 'patron') {
                SearchLog::create([
                    'user_id' => auth()->id(),
                    'search_term' => $search,
                    'field' => (!empty($field) && isset($fieldNames[$field])) ? $fieldNames[$field] : 'Any field',
                ]);
            }

            // Only apply field filter if a specific field is selected
            if ($request->filled('field')) {
                switch ($field) {
                    case '1':
                        $query->where('title', 'like', "%$search%");
                        break;
                    case '2':
                        $query->where('authors', 'like', "%$search%");
                        break;
                    case '3':
                        $query->whereHas('keywords', function ($q) use ($search) {
                            $q->where('name', 'like', "%$search%");
                        });
                        break;
                    case '4':
                        $query->where('year', $search);
                        break;
                    case '5':
                        $query->whereHas('program', function ($q) use ($search) {
                            $q->where('name', 'like', "%$search%");
                        });
                        break;
                    case '6':
                        $query->where('subject', 'like', "%$search%");
                        break;
                }
            } else {
                // Search across all fields if "Any field" is selected
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%$search%")
                        ->orWhere('authors', 'like', "%$search%")
                        ->orWhere('subject', 'like', "%$search%")
                        ->orWhere('year', 'like', "%$search%")
                        ->orWhereHas('keywords', function ($kq) use ($search) {
                            $kq->where('name', 'like', "%$search%");
                        })
                        ->orWhereHas('program', function ($pq) use ($search) {
                            $pq->where('name', 'like', "%$search%");
                        });
                });
            }
        }

        $filteredCount = $query->count();

        $query->orderByDesc('created_at');
        $archives = $query->paginate(50)->withQueryString();

        return view('shared.archive_patron', compact('archives', 'totalArchives', 'filteredCount'));
    }

    public function archiveDetails($id)
    {

        $archive = Archive::with('keywords')->findOrFail($id);

        // Log archive view for all authenticated users
        if (auth()->check()) {
            ArchiveViewLog::create([
                'user_id' => auth()->id(),
                'archive_id' => $archive->id,
            ]);
        }
        $basePath = 'public/archives/'.$archive->archive_code.'/';

        $figuresSize = $this->getFileSize($basePath.'figures.pdf');
        $recommendationSize = $this->getFileSize($basePath.'recommendation.pdf');
        $tablesSize = $this->getFileSize($basePath.'tables.pdf');
        $thesisSize = $this->getFileSize($basePath.'thesis.pdf');

        // Generate barcode image with value under it
        $d = new DNS1D;
        $d->setStorPath(storage_path('app/public/barcodes/'));
        $barcode = $d->getBarcodePNG($archive->archive_code, 'C128', 3, 50, [0, 0, 0], true);

        return view('shared.archive_details', compact('archive', 'figuresSize', 'recommendationSize', 'tablesSize', 'thesisSize', 'barcode'));

    }

    /**
     * Get the size of a file in a human-readable format.
     *
     * @param  string  $filePath
     * @return string
     */
    private function getFileSize($filePath)
    {

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
    private function formatFileSize($bytes)
    {

        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, 2).' '.$units[$pow];

    }

    public function requestAccess(Request $request, $id)
    {
        $userId = auth()->id();
        $archiveId = $id;

        // Optionally -> find the archive model
        $archive = Archive::findOrFail($archiveId);

        $isAjaxRequest = $request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest';

        // Now your logic
        $alreadyRequested = ArchiveAccessRequest::where('user_id', $userId)
            ->where('archive_id', $archiveId)
            ->exists();

        if ($alreadyRequested) {
            if ($isAjaxRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already requested access.'
                ], 400);
            }

            return redirect()->back()->with('success', 'You have already requested access.');
        }

        ArchiveAccessRequest::create([
            'user_id' => $userId,
            'archive_id' => $archiveId,
            'status' => 'pending',
            'approved_by' => null,
        ]);

        if ($isAjaxRequest) {
            return response()->json([
                'success' => true,
                'message' => 'Access request submitted successfully.'
            ]);
        }

        return redirect()->back()->with('success', 'Access request submitted successfully.');
    }

    // After
    public function downloadArchiveFile($archive, $section)
    {
        $archive = Archive::findOrFail($archive);
        $user = auth()->user();

        if (! $user) {
            abort(403);
        }

        $maxDownloads = 10;

        if (strtolower($user->role) === 'patron') {

            $record = DB::table('archive_user_downloads')
                ->where('archive_id', $archive->id)
                ->where('user_id', $user->id)
                ->first();

            $currentDownloads = $record ? $record->downloads : 0;

            if ($currentDownloads >= $maxDownloads) {
                return back()->with('error', 'You have reached the view limit for this thesis.');
            }

            if ($record) {
                DB::table('archive_user_downloads')
                    ->where('archive_id', $archive->id)
                    ->where('user_id', $user->id)
                    ->update([
                        'downloads' => $currentDownloads + 1,
                        'updated_at' => now(),
                    ]);
            } else {
                DB::table('archive_user_downloads')->insert([
                    'archive_id' => $archive->id,
                    'user_id' => $user->id,
                    'downloads' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $filePath = "public/archives/{$archive->archive_code}/{$section}.pdf";

        if (! Storage::exists($filePath)) {
            return back()->with('error', 'File not found.');
        }

        return response()->file(Storage::path($filePath), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$section.'.pdf"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
