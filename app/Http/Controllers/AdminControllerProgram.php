<?php

namespace App\Http\Controllers;
use App\Models\Program;
use App\Models\Log as ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
use Illuminate\Contracts\Filesystem\FileException;
use Exception;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AdminControllerProgram extends Controller {
    
    public function index() {
        $programs = Program::latest()->get();
        return view('admin.admin_program', compact('programs'));
    }

    public function store(Request $request) {
        $request->validate(['name' => 'required|string|max:255']);
        Program::create(['name' => $request->name]);
        ActivityLog::create([
            'user_id' => auth()->id(),
            'event_type' => 'program_created',
            'description' => '[' . strtoupper(auth()->user()->role) . '] ' . auth()->user()->email . " created a new program: '{$request->name}'.",
        ]);
        return redirect()->back()->with('success', 'Program added.');
    }

    public function update(Request $request, $id) {
        $request->validate(['name' => 'required|string|max:255']);
        $program = Program::findOrFail($id);
        $program->update(['name' => $request->name]);
        ActivityLog::create([
            'user_id' => auth()->id(),
            'event_type' => 'program_updated',
            'description' => '[' . strtoupper(auth()->user()->role) . '] ' . auth()->user()->email . " updated program: '{$program->name}'.",
        ]);
        return redirect()->back()->with('success', 'Program updated.');
    }

    public function destroy($id) {
        $program = Program::findOrFail($id);
        $programName = $program->name;
        $program->delete();
        ActivityLog::create([
            'user_id' => auth()->id(),
            'event_type' => 'program_deleted',
            'description' => '[' . strtoupper(auth()->user()->role) . '] ' . auth()->user()->email . " deleted program: '{$programName}'.",
        ]);
        return redirect()->back()->with('success', 'Program deleted.');
    }
}
