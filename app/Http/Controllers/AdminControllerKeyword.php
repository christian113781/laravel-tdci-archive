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
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', 'unique:keywords,name'],
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.keyword')
                ->withErrors($validator)
                ->withInput();
        }

        Keyword::create($validator->validated());
        return redirect()->back()->with('success', 'Keyword added.');
    }

    public function update(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', Rule::unique('keywords', 'name')->ignore($id)],
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.keyword')
                ->withErrors($validator)
                ->withInput();
        }

        $keyword = Keyword::findOrFail($id);
        $keyword->update($validator->validated());
        return redirect()->back()->with('success', 'Keyword updated.');
    }

    public function destroy($id) {
    $keyword = Keyword::findOrFail($id);
    $keyword->delete();
    return redirect()->back()->with('error', 'Keyword deleted.');
    }
}
