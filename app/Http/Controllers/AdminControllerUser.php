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


class AdminControllerUser extends Controller
{
    public function index()
    {
        $users = User::whereIn('role', ['staff', 'admin'])->get();
        return view('admin.admin_user', compact('users')); 
    }

    
    public function destroy($id)
    {
    $user = User::findOrFail($id);

    // Optional: Delete avatar from storage
    if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
        Storage::disk('public')->delete($user->avatar);
    }

    $userEmail = $user->email;
    $user->delete();

    ActivityLog::create([
        'user_id' => auth()->id(),
        'event_type' => 'user_deleted',
        'description' => '[' . strtoupper(auth()->user()->role) . '] ' . auth()->user()->email . " deleted user: '{$userEmail}'.",
    ]);

    return redirect()
        ->route('admin.user')
        ->with('success', 'User deleted successfully.');
}
}
