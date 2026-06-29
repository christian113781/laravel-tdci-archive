<?php

namespace App\Http\Controllers;

use App\Models\Archive;
use App\Models\Keyword;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use TCPDF;

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
        $archiveCode = 'ARC-'.now()->format('y').now()->format('md').'-'.$secondsSinceMidnight;

        return view('staff.staff_archive_manage', compact('archives', 'programs', 'keywords', 'archiveCode'));
    }

    public function storeArchive()
    {

        // Fetch for dropdowns
        $programs = Program::all();
        $keywords = Keyword::all();
        // generate the “next” archive code
        $datePart = now()->format('ymd');                // e.g. “251020”
        $prefix = 'ARC-'.$datePart.'-';

        $last = \App\Models\Archive::where('archive_code', 'like', $prefix.'%')
            ->orderBy('archive_code', 'desc')
            ->first();

        if ($last) {
            $lastSeq = (int) substr($last->archive_code, strrpos($last->archive_code, '-') + 1);
            $nextSeq = $lastSeq + 1;
        } else {
            $nextSeq = 1;
        }

        $seqPart = str_pad($nextSeq, 3, '0', STR_PAD_LEFT);
        $nextCode = $prefix.$seqPart;

        return view('staff.staff_archive_new', compact('nextCode', 'programs', 'keywords'));
    }

    public function store(Request $request)
    {
        
        // Normalize title - remove newlines and extra spaces
    $request->merge([
        'archive_title' => preg_replace('/\s+/', ' ', trim($request->archive_title)),
    ]);
    
        $validated = $request->validate([
            'archive_code' => 'required|string|unique:archives,archive_code',
            'archive_title' => 'required|string|unique:archives,title',
            'archive_citation' => 'required|string',
            'archive_author' => 'required|string|max:500',
            'archive_subject' => 'nullable|string',
            'archive_year' => 'required|digits:4|integer',
            'archive_program' => 'required|exists:programs,id',
            'archive_category' => 'required|in:A,B',

            // Multiple image uploads
            'thesis_file' => 'required|array',
            'thesis_file.*' => 'image|mimes:jpg,jpeg,png|max:3072',

            'tables_file' => 'nullable|array',
            'tables_file.*' => 'image|mimes:jpg,jpeg,png|max:3072',

            'recommendation_file' => 'nullable|array',
            'recommendation_file.*' => 'image|mimes:jpg,jpeg,png|max:3072',

            'figures_file' => 'nullable|array',
            'figures_file.*' => 'image|mimes:jpg,jpeg,png|max:3072',

            'multiple' => 'nullable|array',
            'multiple.*' => 'string',
            'new_keywords' => 'nullable|string',
        ], [
            'archive_title.unique' => 'This thesis title already exists in the system. Please use a different title.',
            'archive_title.required' => 'The thesis title is required.',
            'archive_code.unique' => 'This archive code already exists.',
        ]);

        $archiveCode = $request->archive_code;
        $folder = "archives/{$archiveCode}";

        $fullPath = storage_path("app/public/{$folder}");

if (!file_exists($fullPath)) {
    mkdir($fullPath, 0775, true); // true = recursive
}

        $fileSections = [
            'thesis_file' => 'thesis',
            'tables_file' => 'tables',
            'recommendation_file' => 'recommendation',
            'figures_file' => 'figures',
        ];

        $pdfPaths = [];

        // Watermark text
        $watermarkText = 'TDCI ';

        foreach ($fileSections as $inputName => $sectionName) {

            if ($request->hasFile($inputName)) {

                $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
                $pdf->SetMargins(0, 0, 0);
                $pdf->SetAutoPageBreak(false);
                $pdf->SetCreator('Thesis Archive System');

                foreach ($request->file($inputName) as $image) {

                    $imagePath = $image->getRealPath();
                    [$width, $height] = getimagesize($imagePath);

                    // Detect orientation
                    if ($width > $height) {
                        $pdf->AddPage('L');
                        $pageWidth = 297;
                        $pageHeight = 210;
                    } else {
                        $pdf->AddPage('P');
                        $pageWidth = 210;
                        $pageHeight = 297;
                    }

                    // Maintain aspect ratio
                    $ratio = min($pageWidth / $width, $pageHeight / $height);
                    $newWidth = $width * $ratio;
                    $newHeight = $height * $ratio;

                    $x = ($pageWidth - $newWidth) / 2;
                    $y = ($pageHeight - $newHeight) / 2;

                    // Insert image
                    $pdf->Image($imagePath, $x, $y, $newWidth, $newHeight, '', '', '', true, 80);

                    // Add repeating watermark
                    $pdf->SetAlpha(0.1); // light opacity
                    $pdf->SetFont('helvetica', 'B', 40);
                    $pdf->SetTextColor(200, 200, 200);

                    $stepX = 200; // horizontal spacing between watermarks
                    $stepY = 150; // vertical spacing between watermarks
                    $angle = 45;  // diagonal rotation

                    for ($posY = -$pageHeight; $posY < $pageHeight * 2; $posY += $stepY) {
                        for ($posX = -$pageWidth; $posX < $pageWidth * 2; $posX += $stepX) {
                            $pdf->StartTransform();
                            $pdf->Rotate($angle, $posX + $pageWidth / 2, $posY + $pageHeight / 2);

                            // Repeat 2 times per placement, with a space
                            $pdf->Text($posX, $posY, $watermarkText);

                            $pdf->StopTransform();
                        }
                    }

                    $pdf->SetAlpha(1); // reset opacity
                }

                $pdfFileName = "{$sectionName}.pdf";
                $pdfFullPath = storage_path("app/public/{$folder}/{$pdfFileName}");

                $pdf->Output($pdfFullPath, 'F');

                $pdfPaths[$inputName] = "{$folder}/{$pdfFileName}";
            } else {
                $pdfPaths[$inputName] = null;
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

            'thesis_file' => $pdfPaths['thesis_file'],
            'tables_file' => $pdfPaths['tables_file'],
            'recommendation_file' => $pdfPaths['recommendation_file'],
            'figures_file' => $pdfPaths['figures_file'],
        ]);

        // Handle keywords - both existing and new
        $keywordIds = [];

        // Process existing keywords
        if ($request->multiple) {
            foreach ($request->multiple as $keywordValue) {
                if (strpos($keywordValue, 'existing_') === 0) {
                    // Existing keyword
                    $keywordId = str_replace('existing_', '', $keywordValue);
                    $keywordIds[] = $keywordId;
                }
            }
        }

        // Process new keywords
        if ($request->new_keywords) {
            $newKeywords = array_filter(array_map('trim', explode('|', $request->new_keywords)));
            foreach ($newKeywords as $keywordName) {
                // Create or get the keyword
                $keyword = Keyword::firstOrCreate(
                    ['name' => $keywordName],
                    ['name' => $keywordName]
                );
                $keywordIds[] = $keyword->id;
            }
        }

        // Sync all keywords to the archive
        if (!empty($keywordIds)) {
            $archive->keywords()->sync($keywordIds);
        }

        return redirect()
            ->route('staff.archive.manage')
            ->with('success', 'Archive stored successfully!');
    }

    public function getArchive($id)
    {
        $archive = Archive::with(['program'])->findOrFail($id);

        return response()->json([
            'file_path' => $archive->file_path ? asset('storage/'.$archive->file_path) : null,
            'title' => $archive->title,
            'subject' => $archive->subject,
            'authors' => $archive->authors,
            'program' => $archive->program->name ?? null,
            'year' => $archive->year,
        ]);
    }

    // added NEW
    public function destroy($id)
    {
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
    public function edit($id)
    {
        // fetch the archive record
        $archive = Archive::with(['keywords'])->findOrFail($id);

        $programs = Program::all();    // or however you get programs
        $keywords = Keyword::all();    // or however you get keywords

        return view('staff.staff_archive_edit', compact('archive', 'programs', 'keywords'));
    }

    public function update(Request $request, $id)
    {
        $archive = Archive::findOrFail($id);
        
        // Normalize title - remove newlines and extra spaces
    $request->merge([
        'archive_title' => preg_replace('/\s+/', ' ', trim($request->archive_title)),
    ]);

        $validated = $request->validate([
            'archive_title' => 'required|string',
            'archive_citation' => 'required|string',
            'archive_author' => 'required|string|max:500',
            'archive_subject' => 'nullable|string',
            'archive_year' => 'required|digits:4|integer',
            'archive_program' => 'required|exists:programs,id',
            'archive_category' => 'required|in:A,B',

            // Multiple image uploads
            'thesis_file' => 'nullable|array',
            'thesis_file.*' => 'image|mimes:jpg,jpeg,png|max:3072',

            'tables_file' => 'nullable|array',
            'tables_file.*' => 'image|mimes:jpg,jpeg,png|max:3072',

            'recommendation_file' => 'nullable|array',
            'recommendation_file.*' => 'image|mimes:jpg,jpeg,png|max:3072',

            'figures_file' => 'nullable|array',
            'figures_file.*' => 'image|mimes:jpg,jpeg,png|max:3072',

            'multiple' => 'nullable|array',
            'multiple.*' => 'exists:keywords,id',
        ]);

        $folder = "archives/{$archive->archive_code}";
        $fullPath = storage_path("app/public/{$folder}");

if (!file_exists($fullPath)) {
    mkdir($fullPath, 0775, true);
}

        $fileSections = [
            'thesis_file' => 'thesis',
            'tables_file' => 'tables',
            'recommendation_file' => 'recommendation',
            'figures_file' => 'figures',
        ];

        $watermarkText = 'TDCI';

        foreach ($fileSections as $inputName => $sectionName) {

            if ($request->hasFile($inputName)) {

                // Delete old PDF if exists
                if ($archive->$inputName && Storage::disk('public')->exists($archive->$inputName)) {
                    Storage::disk('public')->delete($archive->$inputName);
                }

                $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
                $pdf->SetMargins(0, 0, 0);
                $pdf->SetAutoPageBreak(false);
                $pdf->SetCreator('Thesis Archive System');

                foreach ($request->file($inputName) as $image) {

                    $imagePath = $image->getRealPath();
                    [$width, $height] = getimagesize($imagePath);

                    // Page orientation
                    if ($width > $height) {
                        $pdf->AddPage('L');
                        $pageWidth = 297;
                        $pageHeight = 210;
                    } else {
                        $pdf->AddPage('P');
                        $pageWidth = 210;
                        $pageHeight = 297;
                    }

                    // Fit image while maintaining aspect ratio
                    $ratio = min($pageWidth / $width, $pageHeight / $height);
                    $newWidth = $width * $ratio;
                    $newHeight = $height * $ratio;

                    $x = ($pageWidth - $newWidth) / 2;
                    $y = ($pageHeight - $newHeight) / 2;

                    $pdf->Image($imagePath, $x, $y, $newWidth, $newHeight, '', '', '', true, 80);

                    // Add repeating watermark with spacing
                    $pdf->SetAlpha(0.1); // light opacity
                    $pdf->SetFont('helvetica', 'B', 40);
                    $pdf->SetTextColor(200, 200, 200);

                    $stepX = 200; // horizontal spacing
                    $stepY = 150; // vertical spacing
                    $angle = 45;  // diagonal

                    for ($posY = -$pageHeight; $posY < $pageHeight * 2; $posY += $stepY) {
                        for ($posX = -$pageWidth; $posX < $pageWidth * 2; $posX += $stepX) {
                            $pdf->StartTransform();
                            $pdf->Rotate($angle, $posX + $pageWidth / 2, $posY + $pageHeight / 2);
                            $pdf->Text($posX, $posY, $watermarkText);
                            $pdf->StopTransform();
                        }
                    }

                    $pdf->SetAlpha(1); // reset opacity
                }

                $pdfFileName = "{$sectionName}.pdf";
                $pdfFullPath = storage_path("app/public/{$folder}/{$pdfFileName}");

                $pdf->Output($pdfFullPath, 'F');

                $archive->$inputName = "{$folder}/{$pdfFileName}";
            }
        }

        // Update text fields
        $archive->update([
            'citation' => $request->archive_citation,
            'title' => $request->archive_title,
            'authors' => $request->archive_author,
            'subject' => $request->archive_subject,
            'year' => $request->archive_year,
            'program_id' => $request->archive_program,
            'category' => $request->archive_category,
        ]);

        // Sync keywords
        $archive->keywords()->sync($request->multiple ?? []);

        return redirect()
            ->route('staff.archive.manage')
            ->with('success', 'Archive updated successfully!');
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
