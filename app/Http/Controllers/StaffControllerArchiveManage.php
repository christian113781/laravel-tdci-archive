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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'archive_code' => 'required|string|unique:archives,archive_code',
            'title'        => 'required|string|max:255',
            'authors'      => 'required|string|max:500',
            'abstract'     => 'nullable|string',
            'year'         => 'required|digits:4|integer',
            'program_id'   => 'required|exists:programs,id',
            'file_path'    => 'nullable|file|mimes:pdf',
            'status'       => 'required|in:publish,unpublish',
            'category'     => 'required|in:A,B',
            'multiple'     => 'nullable|array',
            'multiple.*'   => 'exists:keywords,id',
        ]);

        // Handle file upload
        $filePath = $request->hasFile('file_path')
            ? $request->file('file_path')->store('archives', 'public')
            : null;

        // Create archive
        $archive = Archive::create([
            'archive_code' => $request->archive_code,
            'title'        => $request->title,
            'authors'      => $request->authors,
            'abstract'     => $request->abstract,
            'year'         => $request->year,
            'program_id'   => $request->program_id,
            'category'     => $request->category,
            'user_id'      => auth()->id(),
            'file_path'    => $filePath,
            'status'       => $request->status,
        ]);

        // Sync keywords
        $archive->keywords()->sync($request->multiple ?? []);

        return redirect()->back()->with('success', 'Archive stored successfully!');
    }

    public function getArchive($id)
    {
        $archive = Archive::with(['program'])->findOrFail($id);

        return response()->json([
            'file_path' => $archive->file_path ? asset('storage/' . $archive->file_path) : null,
            'title'     => $archive->title,
            'abstract'  => $archive->abstract,
            'authors'   => $archive->authors,
            'program'   => $archive->program->name ?? null,
            'year'      => $archive->year,
        ]);
    }

    public function destroy($id)
    {
        $archive = Archive::findOrFail($id);

        // Delete file if it exists
        if ($archive->file_path && Storage::disk('public')->exists($archive->file_path)) {
            Storage::disk('public')->delete($archive->file_path);
        }

        $archive->delete();

        return redirect()->route('staff.archive.manage')
            ->with('delete_success', 'Archive deleted successfully!');
    }

    public function edit($id)
    {
        $archive = Archive::with('keywords')->findOrFail($id);
        return response()->json($archive);
    }

    public function update(Request $request, $id)
    {
        $archive = Archive::findOrFail($id);

        $validated = $request->validate([
            'title'      => 'required|string|max:255',
            'authors'    => 'required|string|max:500',
            'abstract'   => 'nullable|string',
            'year'       => 'required|digits:4|integer',
            'program_id' => 'required|exists:programs,id',
            'file_path'  => 'nullable|file|mimes:pdf',
            'status'     => 'required|in:publish,unpublish',
            'category'   => 'required|in:A,B',
            'multiple'   => 'nullable|array',
            'multiple.*' => 'exists:keywords,id',
        ]);

        // If new file uploaded, replace old
        if ($request->hasFile('file_path')) {
            if ($archive->file_path && Storage::disk('public')->exists($archive->file_path)) {
                Storage::disk('public')->delete($archive->file_path);
            }
            $archive->file_path = $request->file('file_path')->store('archives', 'public');
        }

        // Update fields
        $archive->update([
            'title'      => $request->title,
            'authors'    => $request->authors,
            'abstract'   => $request->abstract,
            'year'       => $request->year,
            'program_id' => $request->program_id,
            'status'     => $request->status,
            'category'   => $request->category,
            'file_path'  => $archive->file_path, // Keep old if not replaced
        ]);

        // Sync keywords
        $archive->keywords()->sync($request->multiple ?? []);

        return redirect()->back()->with('success', 'Archive updated successfully!');
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
