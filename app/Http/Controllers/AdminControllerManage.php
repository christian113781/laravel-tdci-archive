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

class AdminControllerManage extends Controller
{

    public function index()
    {
    $user = new \App\Models\User(); // or whatever your User model is
    return view('admin.admin_manage', compact('user'));
    }   
    


    public function fetchEditID($id)
    {
    $user = User::findOrFail($id); // fetch user from DB

    return view('admin.admin_manage', compact('user')); // pass user to view
    }



    public function create(Request $request)
    {

        $validator = Validator::make($request->all(), [
        'firstname'   => 'required|string|max:255',
        'lastname'    => 'required|string|max:255',
        'email'       => 'required|email|unique:users,email',
        'password'    => 'required|min:6|same:confirmpassword',
        'usertype'    => 'required|in:Admin,Staff',
        'uploadImg'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ], [
        'email.unique' => 'That email address is already registered.',
    ]);

    if ($validator->fails()) {
        return redirect()
            ->route('admin.user')
            ->withErrors($validator)
            ->with('error', 'Email is already taken.')
            ->withInput();
    }

    $validated = $validator->validated();

    DB::beginTransaction();

    try {
        $imagePath = null;

        if ($request->hasFile('uploadImg')) {
            $file = $request->file('uploadImg');
            $filename = time() . '_' . $file->getClientOriginalName();
            $imagePath = $file->storeAs('uploads/users', $filename, 'public');
        }

        User::create([
            'first_name' => $validated['firstname'],
            'last_name'  => $validated['lastname'],
            'email'      => $validated['email'],
            'avatar'     => $imagePath,
            'password'   => Hash::make($validated['password']),
            'role'       => strtolower($validated['usertype']),
            'status'     => 'verified',
        ]);

        DB::commit();

        return redirect()
            ->route('admin.user')
            ->with('success', 'User created successfully.');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('User store error: ' . $e->getMessage());

        return redirect()
            ->route('admin.user')
            ->with('error', 'An unexpected error occurred.')
            ->withInput();
    }
    }


    public function update(Request $request, $id)
    {
    $user = User::findOrFail($id);

    $data = $request->validate([
        'firstname'   => 'sometimes|string|max:255',
        'lastname'    => 'sometimes|string|max:255',
        'email'       => [
            'nullable',
            'email',
            Rule::unique('users')->ignore($user->id),
        ],
        'password'    => 'sometimes|nullable|string|min:6',
        'uploadImg'   => 'sometimes|file|image|max:2048',
        'usertype'    => ['sometimes', 'in:Admin,Staff'], 
    ]);

    if ($request->filled('firstname')) {
        $user->first_name = $data['firstname'];
    }

    if ($request->filled('lastname')) {
        $user->last_name = $data['lastname'];
    }

    if ($request->filled('email')) {
        $user->email = $data['email'];
    }

    if ($request->filled('password')) {
        $user->password = Hash::make($data['password']);
    }

    if ($request->has('usertype')) {
        $user->role = strtolower($data['usertype']); // Save as 'admin' or 'staff'
    }

    if ($request->hasFile('uploadImg')) {
        $path = $request->file('uploadImg')->store('uploadImg', 'public');
        $user->avatar = $path;
    }

    $user->save();

    return redirect()
        ->route('admin.user')
        ->with('success', 'User updated successfully.');
}
}
