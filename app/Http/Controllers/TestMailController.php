<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\MailTrap;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class TestMailController extends Controller
{
   public function sendMail($id)
    {
        // Get the user
        $user = User::findOrFail($id);

        // Use last name as email name
        $name = $user->last_name ?? 'User';

        // Send email
        Mail::to($user->email)->send(new MailTrap($name));

        return response()->json([
            'status' => 'Mail sent successfully',
            'recipient' => $user->email,
            'name_used' => $name,
        ]);
    }
}
