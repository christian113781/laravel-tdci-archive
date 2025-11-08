<?php

namespace App\Http\Controllers;
use App\Models\ArchiveAccessRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\MailTrap; 

class StaffControllerRequest extends Controller
{
   public function index()
    {
        $requests = ArchiveAccessRequest::with(['user', 'archive'])
                    ->latest()
                    ->get();

        return view('staff.staff_archive_request', compact('requests'));
    }

    // Manual test (optional)
    public function sendMail($id)
    {
        $user = User::findOrFail($id);
        $name = $user->last_name ?? 'User';

        Mail::to($user->email)->send(new MailTrap($name, 'approved'));

        return response()->json([
            'status' => 'Mail sent successfully',
            'recipient' => $user->email,
            'name_used' => $name,
        ]);
    }

    // Approve request
    public function approve($id)
    {
        $request = ArchiveAccessRequest::with('user')->findOrFail($id);
        $request->status = 'approved';
        $request->approved_by = auth()->id();
        $request->save();

        // Send email to requester
        $name = $request->user->last_name ?? 'User';
        Mail::to($request->user->email)->send(new MailTrap($name, 'approved'));

        return redirect()->back()->with('success', 'Archive access request approved and email sent.');
    }

    // Reject request
    public function reject($id)
    {
        $request = ArchiveAccessRequest::with('user')->findOrFail($id);
        $request->status = 'rejected';
        $request->approved_by = auth()->id();
        $request->save();

        // Send rejection email
        $name = $request->user->last_name ?? 'User';
        Mail::to($request->user->email)->send(new MailTrap($name, 'rejected'));

        return redirect()->back()->with('error', 'Archive access request rejected and email sent.');
    }
}
