<?php

namespace App\Http\Controllers;
use App\Models\Keyword;
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

class AdminControllerKeyword extends Controller {
    
    public function index() {
        $keywords = Keyword::latest()->get();
        return view('admin.admin_keyword', compact('keywords'));
    }

    public function store(Request $request) {
    $request->validate(['name' => 'required|string|max:255']);
    Keyword::create(['name' => $request->name]);
    return redirect()->back()->with('success', 'Keyword added.');
    }

    public function update(Request $request, $id) {
    $request->validate(['name' => 'required|string|max:255']);
    $keyword = Keyword::findOrFail($id);
    $keyword->update(['name' => $request->name]);
    return redirect()->back()->with('success', 'Keyword updated.');
    }

    public function destroy($id) {
    $keyword = Keyword::findOrFail($id);
    $keyword->delete();
    return redirect()->back()->with('success', 'Keyword deleted.');
    }
}
