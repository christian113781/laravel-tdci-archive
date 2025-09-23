<?php

namespace App\Http\Controllers;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
use Illuminate\Contracts\Filesystem\FileException;
use Exception;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class StaffControllerProgram extends Controller
{
    public function index()
    {
        $programs = Program::latest()->get();
        return view('staff.staff_program', compact('programs'));
    }

   public function store(Request $request) {
    $request->validate(['name' => 'required|string|max:255']);
    Program::create(['name' => $request->name]);
    return redirect()->back()->with('success', 'Program added.');
}

public function update(Request $request, $id) {
    $request->validate(['name' => 'required|string|max:255']);
    $program = Program::findOrFail($id);
    $program->update(['name' => $request->name]);
    return redirect()->back()->with('success', 'Program updated.');
}

public function destroy($id) {
    $program = Program::findOrFail($id);
    $program->delete();
    return redirect()->back()->with('success', 'Program deleted.');
}
}
