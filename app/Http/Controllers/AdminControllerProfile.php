<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminControllerProfile extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('admin.admin_profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        try {
            // Validate the input
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name'  => 'required|string|max:255',
                'password'   => 'nullable|string|min:6|confirmed',
                'uploadImg'  => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            ]);

            // Update name
            $user->first_name = $validated['first_name'];
            $user->last_name  = $validated['last_name'];

            // Update password if provided
            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }

            // If there is a file upload
            if ($request->hasFile('uploadImg')) {
                $file = $request->file('uploadImg');
                $extension = $file->getClientOriginalExtension();

                // generate random file name
                $randomName = Str::random(40) . '.' . $extension;

                $folder = 'avatar';  // this will be under storage/app/public/uploadImg

                // Delete old avatar if exists
                if ($user->avatar) {
                    // assume $user->avatar is something like "uploadImg/oldfile.jpg"
                    if (Storage::disk('public')->exists($user->avatar)) {
                        Storage::disk('public')->delete($user->avatar);
                    }
                }

                // Store the file in the public disk
                $path = $file->storeAs($folder, $randomName, 'public');
                // $path will be: uploadImg/randomName.jpg

                // Save the path in DB
                $user->avatar = $path;
            }

            $user->save();

            return redirect()->back()->with('success', 'Profile updated successfully!');
        } catch (\Exception $e) {
            \Log::error('Profile update failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }
}