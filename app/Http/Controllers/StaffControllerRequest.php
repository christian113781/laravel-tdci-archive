<?php

namespace App\Http\Controllers;
use App\Models\ArchiveAccessRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\RequestMail; 

class StaffControllerRequest extends Controller
{
   public function index()
    {
        $requests = ArchiveAccessRequest::with(['user', 'archive'])
                    ->latest()
                    ->get();

        return view('staff.staff_archive_request', compact('requests'));
    }

    // Approve request
    // Approve request
    public function approve($id)
    {
        $request = ArchiveAccessRequest::with('user')->findOrFail($id);

        $request->status = 'approved';
        $request->approved_by = auth()->id();
        $request->save();

        // Send email to requester
        Mail::to($request->user->email)->send(new RequestMail($request, 'approved'));

        return redirect()->back()->with('success', 'Archive access request approved and email sent.');
    }

    // Reject request
    public function reject($id)
    {
        $request = ArchiveAccessRequest::findOrFail($id);
        $request->status = 'rejected';
        $request->approved_by = auth()->id(); // optional
        $request->save();

        return redirect()->back()->with('error', 'Archive access request rejected.');
    }
}
