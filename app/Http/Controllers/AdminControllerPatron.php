<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Log as ActivityLog;
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
use Illuminate\Support\Facades\Mail;
use App\Mail\PatronVerificationEmail;


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

    ActivityLog::create([
        'user_id' => auth()->id(),
        'event_type' => 'user_verified',
        'description' => '[' . strtoupper(auth()->user()->role) . '] ' . auth()->user()->email . " verified user: '{$student->email}'.",
    ]);

    try {
        Mail::to($student->email)->send(new PatronVerificationEmail($student->name, 'verified'));
        return redirect()->back()->with('status_success', 'Patron verified successfully.');
    } catch (\Exception $e) {
        Log::error('Patron verification email failed: ' . $e->getMessage());
        return redirect()->back()->with('status_success', 'Patron verified successfully, but the email could not be sent.');
    }
    }

    public function reject($id) {
    $student = User::findOrFail($id);
    $student->status = 'rejected';
    $student->save();

    try {
        Log::info('Sending patron rejection email to ' . $student->email);
        Mail::to($student->email)->send(new PatronVerificationEmail(
            $student->name,
            'rejected',
            'Your account verification request has been rejected. Please contact support for further assistance.'
        ));
        return redirect()->back()->with('status_success', 'Patron rejected successfully.');
    } catch (\Exception $e) {
        Log::error('Patron rejection email failed: ' . $e->getMessage());
        return redirect()->back()->with('status_success', 'Patron rejected successfully, but the email could not be sent.');
    }
    }

    public function destroy($id) {
    $student = User::findOrFail($id);
    $studentEmail = $student->email;
    $student->delete();

    ActivityLog::create([
        'user_id' => auth()->id(),
        'event_type' => 'patron_deleted',
        'description' => '[' . strtoupper(auth()->user()->role) . '] ' . auth()->user()->email . " deleted patron: '{$studentEmail}'.",
    ]);

    return redirect()->back()->with('destroy_success', 'Patron deleted successfully.');
    } 

    
}
