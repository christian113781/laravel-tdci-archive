<?php

namespace App\Http\Controllers;
use App\Models\User;
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


class AdminControllerPatron extends Controller
{
    public function index() {
         $patrons = User::where('role', 'patron')->get();
        return view('admin.admin_patron', compact('patrons'));
    }

    public function verify($id) {
    $student = User::findOrFail($id);
    $student->status = 'verified';
    $student->save();
    return redirect()->back()->with('verified_success', 'Patron verified successfully.');
    }

    public function destroy($id) {
    $student = User::findOrFail($id);
    $student->delete();

    return redirect()->back()->with('destroy_success', 'Patron deleted successfully.');
    } 

    
}
