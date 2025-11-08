<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Archive;
use App\Models\Program;
use App\Models\Keyword;
use Illuminate\Support\Facades\Storage;

class StaffControllerArchiveManage extends Controller
{
    public function index()
    {       
        // Fetch all archives belonging to the logged-in user
        $archives = Archive::with(['program', 'user', 'keywords'])
            ->where('user_id', auth()->id())
            ->get();

        // Fetch for dropdowns
        $programs = Program::all();
        $keywords = Keyword::all();

        // Generate unique archive code
        $secondsSinceMidnight = now()->diffInSeconds(now()->startOfDay());
        $archiveCode = 'ARC-' . now()->format('y') . now()->format('md') . '-' . $secondsSinceMidnight;

        return view('staff.staff_archive_manage', compact('archives', 'programs', 'keywords', 'archiveCode'));
    }


    public function storeArchive()
{

    // Fetch for dropdowns
        $programs = Program::all();
        $keywords = Keyword::all();
    // generate the “next” archive code
    $datePart = now()->format('ymd');                // e.g. “251020”
    $prefix   = 'ARC-' . $datePart . '-';

    $last     = \App\Models\Archive::where('archive_code', 'like', $prefix . '%')
                                    ->orderBy('archive_code', 'desc')
                                    ->first();

    if ($last) {
        $lastSeq = (int) substr($last->archive_code, strrpos($last->archive_code, '-') + 1);
        $nextSeq = $lastSeq + 1;
    } else {
        $nextSeq = 1;
    }

    $seqPart  = str_pad($nextSeq, 3, '0', STR_PAD_LEFT);   
    $nextCode = $prefix . $seqPart;                

    return view('staff.staff_archive_new', compact('nextCode', 'programs', 'keywords'));
}





  public function store(Request $request)
    {

    $validated = $request->validate([
    'archive_code' => 'required|string|unique:archives,archive_code',
    'archive_title' => 'required|string',
    'archive_citation' => 'required|string',
    'archive_author' => 'required|string|max:500',
    'archive_subject' => 'nullable|string',
    'archive_year' => 'required|digits:4|integer',
    'archive_program' => 'required|exists:programs,id',
    'thesis_file' => 'required|file|mimes:pdf',
    'tables' => 'nullable|file|mimes:pdf',
    'recommendation' => 'nullable|file|mimes:pdf',
    'figures' => 'nullable|file|mimes:pdf',
    'archive_category' => 'required|in:A,B',
    'multiple' => 'array',
    'multiple.*' => 'exists:keywords,id',

    ]);

    $archiveCode = $request->input('archive_code');
    $folder      = "archives/{$archiveCode}";

     $fileInputs = [
        'thesis_file'        => 'thesis',
        'tables_file'        => 'tables',
        'recommendation_file' => 'recommendation',
        'figures_file'         => 'figures',
    ];

    $paths = [];
    foreach ($fileInputs as $inputName => $basename) {
        if ($request->hasFile($inputName)) {
            $file      = $request->file($inputName);
            $extension = $file->getClientOriginalExtension();
            $filename  = "{$basename}.{$extension}";
            $paths[$inputName] = $file
                ->storeAs($folder, $filename, 'public');
        } else {
            $paths[$inputName] = null;
        }
    }



    $archive = Archive::create([
        'archive_code' => $request->archive_code,
        'citation' => $request->archive_citation,
        'title' => $request->archive_title,
        'authors' => $request->archive_author,
        'subject' => $request->archive_subject,
        'year' => $request->archive_year,
        'program_id' => $request->archive_program,
        'category' => $request->archive_category,
        'user_id' => auth()->id(),
        'thesis_file'         => $paths['thesis_file'],
        'tables_file'         => $paths['tables_file'],
        'recommendation_file' => $paths['recommendation_file'],
        'figures_file'        => $paths['figures_file'],
    ]);

    // Sync keywords safely
    $archive->keywords()->sync($request->multiple ?? []);

    return redirect()->route('staff.archive.manage')
                 ->with('success', 'Archive stored successfully!');
}


    public function getArchive($id)
    {
        $archive = Archive::with(['program'])->findOrFail($id);

        return response()->json([
            'file_path' => $archive->file_path ? asset('storage/' . $archive->file_path) : null,
            'title'     => $archive->title,
            'subject'  => $archive->subject,
            'authors'   => $archive->authors,
            'program'   => $archive->program->name ?? null,
            'year'      => $archive->year,
        ]);
    }


    // added NEW
    public function destroy($id){
    $archive = Archive::findOrFail($id);

    // Construct the folder path
    $folder = "archives/{$archive->archive_code}";

    // Delete the directory and everything inside
    \Storage::disk('public')->deleteDirectory($folder);

    // Then delete the database record
    $archive->delete();

    return redirect()->back()->with('success', 'archive deleted successfully!');    
    }


    // added new
    public function edit($id) {
    // fetch the archive record
    $archive = Archive::with(['keywords'])->findOrFail($id);

    $programs  = Program::all();    // or however you get programs
    $keywords  = Keyword::all();    // or however you get keywords

    return view('staff.staff_archive_edit', compact('archive','programs','keywords'));
    }


    public function update(Request $request, $id)
{
    $archive = Archive::findOrFail($id);

    $validated = $request->validate([
        'archive_title'        => 'required|string',
        'archive_citation'     => 'required|string',
        'archive_author'       => 'required|string|max:500',
        'archive_subject'      => 'nullable|string',
        'archive_year'         => 'required|digits:4|integer',
        'archive_program'      => 'required|exists:programs,id',
        'thesis_file'          => 'nullable|file|mimes:pdf',
        'tables_file'          => 'nullable|file|mimes:pdf',
        'recommendation_file'  => 'nullable|file|mimes:pdf',
        'figures_file'         => 'nullable|file|mimes:pdf',
        'archive_category'     => 'required|in:A,B',
        'multiple'             => 'array',
        'multiple.*'           => 'exists:keywords,id',
    ]);

    $folder = "archives/{$archive->archive_code}";

    // Define input => base filename map
    $fileInputs = [
        'thesis_file'         => 'thesis',
        'tables_file'         => 'tables',
        'recommendation_file' => 'recommendation',
        'figures_file'        => 'figures',
    ];

    // Handle file replacements
    foreach ($fileInputs as $inputName => $basename) {
        if ($request->hasFile($inputName)) {
            // Delete old file if exists
            if ($archive->$inputName && Storage::disk('public')->exists($archive->$inputName)) {
                Storage::disk('public')->delete($archive->$inputName);
            }

            // Store new file using same folder/filename structure
            $file      = $request->file($inputName);
            $extension = $file->getClientOriginalExtension();
            $filename  = "{$basename}.{$extension}";

            $archive->$inputName = $file->storeAs($folder, $filename, 'public');
        }
    }

    // Update non-file fields
    $archive->update([
        'citation'    => $request->archive_citation,
        'title'       => $request->archive_title,
        'authors'     => $request->archive_author,
        'subject'     => $request->archive_subject,
        'year'        => $request->archive_year,
        'program_id'  => $request->archive_program,
        'category'    => $request->archive_category,
    ]);

    // Sync keywords
    $archive->keywords()->sync($request->multiple ?? []);

    return redirect()->route('staff.archive.manage')
                 ->with('success', 'Archive stored successfully!');
}


    public function updateStatus($id)
    {
        $archive = Archive::findOrFail($id);

        // Toggle status
        $archive->status = strtolower($archive->status) === 'publish' ? 'unpublish' : 'publish';
        $archive->save();

        return redirect()->back()->with('success', 'Archive status updated successfully!');
    }
}
