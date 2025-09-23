<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminControllerArchive;
use App\Http\Controllers\AdminControllerPatron;
use App\Http\Controllers\AdminControllerProgram;

use App\Http\Controllers\AdminControllerUser;
use App\Http\Controllers\AdminControllerManage;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StaffControllerArchive;
use App\Http\Controllers\StaffControllerArchiveManage;
use App\Http\Controllers\StaffControllerProgram;
use App\Http\Controllers\StaffControllerKeyword;
use App\Http\Controllers\AdminControllerKeyword;
use App\Http\Controllers\StaffControllerRequest;
use App\Http\Controllers\StaffControllerProfile;
use App\Http\Controllers\AdminControllerProfile;
use App\Http\Controllers\PatronControllerProfile;
use App\Http\Controllers\PatronController;
use App\Http\Controllers\PatronControllerArchive;
use App\Http\Controllers\PatronControllerRequest;
use App\Http\Controllers\sendRequestEmail;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });


Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');




// Admin Middleware Section-------------------

Route::middleware(['auth','role:admin'])->group(function(){

    // index
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');



    // archive

    Route::post('/admin/archive/request-access/{id}', [AdminControllerArchive::class, 'requestAccess'])->name('admin.archive.requestAccess');
    Route::get('/admin/archives/{id}/view', [AdminControllerArchive::class, 'getArchive'])->name('admin.archives.view');
    Route::get('/admin/archive', [AdminControllerArchive::class, 'index'])->name('admin.archive');


    // patron                        
    Route::get('/admin/patron', [AdminControllerPatron::class, 'index'])->name('admin.patron'); 
    Route::patch('/admin/patron/{id}/verify', [AdminControllerPatron::class, 'verify'])->name('admin.patron.verify');
    Route::delete('/admin/patron/{id}', [AdminControllerPatron::class, 'destroy'])->name('admin.patron.destroy');    

    // program
    Route::get('/admin/program', [AdminControllerProgram::class, 'index'])->name('admin.program');
    Route::post('/admin/program', [AdminControllerProgram::class, 'store'])->name('admin.program.store');
    Route::put('/admin/program/{id}', [AdminControllerProgram::class, 'update'])->name('admin.program.update');
    Route::delete('/admin/program/{id}', [AdminControllerProgram::class, 'destroy'])->name('admin.program.destroy');


    // user
    Route::get('/admin/user', [AdminControllerUser::class, 'index'])->name('admin.user');
    Route::delete('/admin/user/{id}', [AdminControllerUser::class, 'destroy'])->name('admin.user.destroy');

    // manage user
    Route::get('/admin/manage', [AdminControllerManage::class, 'index'])->name('admin.manage');
    Route::get('/admin/manage/{id}', [AdminControllerManage::class, 'fetchEditID'])->name('admin.manage.fetchID');
    Route::post('/admin/manage', [AdminControllerManage::class, 'create'])->name('admin.manage.store');
    Route::put('/admin/manage/{user}', [AdminControllerManage::class, 'update'])->name('admin.manage.update');
    Route::post('/admin/manage', [AdminControllerManage::class, 'create'])->name('admin_manage_store');


        // Keyword
    Route::get('/admin/keyword', [AdminControllerKeyword::class, 'index'])->name('admin.keyword');
    Route::post('/admin/keyword', [AdminControllerKeyword::class, 'store'])->name('admin.keyword.store');
    Route::put('/admin/keyword/{id}', [AdminControllerKeyword::class, 'update'])->name('admin.keyword.update');
    Route::delete('/admin/keyword/{id}', [AdminControllerKeyword::class, 'destroy'])->name('admin.keyword.destroy');

    

        // Profile
    Route::get('/admin/profile', [AdminControllerProfile::class, 'index'])->name('admin.profile');
    Route::post('/admin/profile/update', [AdminControllerProfile::class, 'update'])->name('admin.profile.update');


});

// Staff Middleware Section-------------------
Route::middleware(['auth','role:staff'])->group(function(){

    // index
    Route::get('/staff', [StaffController::class, 'index'])->name('staff.dashboard');

    // Archive
    Route::get('/staff/archives/{id}/view', [StaffControllerArchive::class, 'getArchive'])->name('staff.archives.view');
    Route::get('/staff/archive', [StaffControllerArchive::class, 'index'])->name('staff.archive');


    
    // Archive Manage
    Route::get('/staff/manage', [StaffControllerArchiveManage::class, 'index'])->name('staff.archive.manage');
    Route::post('/staff/archive/store', [StaffControllerArchiveManage::class, 'store'])->name('staff.archive.manage.store');
    Route::put('/staff/archives/{id}/status', [StaffControllerArchiveManage::class, 'updateStatus'])->name('staff.archives.manage.updateStatus');
    Route::delete('/staff/archives/{id}', [StaffControllerArchiveManage::class, 'destroy'])->name('staff.archives.manage.destroy');
    Route::get('/staff/archives/{id}/edit', [StaffControllerArchiveManage::class, 'edit'])->name('staff.archives.edit');
    Route::put('/staff/archives/{id}', [StaffControllerArchiveManage::class, 'update'])->name('staff.archives.update');
    Route::get('/staff/archives/{id}', [StaffControllerArchiveManage::class, 'getArchive'])->name('staff.archives.get');
 

    // Program
    Route::get('/staff/program', [StaffControllerProgram::class, 'index'])->name('staff.program');
    Route::post('/staff/program', [StaffControllerProgram::class, 'store'])->name('staff.program.store');
    Route::put('/staff/program/{id}', [StaffControllerProgram::class, 'update'])->name('staff.program.update');
    Route::delete('/staff/program/{id}', [StaffControllerProgram::class, 'destroy'])->name('staff.program.destroy');

    // Keyword
    Route::get('/staff/keyword', [StaffControllerKeyword::class, 'index'])->name('staff.keyword');
    Route::post('/staff/keyword', [StaffControllerKeyword::class, 'store'])->name('staff.keyword.store');
    Route::put('/staff/keyword/{id}', [StaffControllerKeyword::class, 'update'])->name('staff.keyword.update');
    Route::delete('/staff/keyword/{id}', [StaffControllerKeyword::class, 'destroy'])->name('staff.keyword.destroy');

    // Request Archive Access


    Route::post('/staff/archive/request-access/{id}', [StaffControllerArchive::class, 'requestAccess'])->name('staff.archive.requestAccess');
    Route::get('/staff/request', [StaffControllerRequest::class, 'index'])->name('staff.archive.request');
    Route::get('/staff/archive-request/{id}/approve', [StaffControllerRequest::class, 'approve'])->name('staff.archive.request.approve');
    Route::get('/staff/archive-request/{id}/reject', [StaffControllerRequest::class, 'reject'])->name('staff.archive.request.reject');

    // Profile
    Route::get('/staff/profile', [StaffControllerProfile::class, 'index'])->name('staff.profile');
    Route::post('/staff/profile/update', [StaffControllerProfile::class, 'update'])->name('staff.profile.update');

    

}); 

// Patron Middleware Section-------------------
Route::middleware(['auth','role:patron'])->group(function(){

    // index
    Route::get('/patron', [PatronController::class, 'index'])->name('patron.dashboard');

    Route::get('/patron/archives/{id}/view', [PatronControllerArchive::class, 'getArchive'])->name('patron.archives.view');
    Route::get('/patron/archive', [PatronControllerArchive::class, 'index'])->name('patron.archive');
    Route::post('/patron/archive/request-access/{id}', [PatronControllerArchive::class, 'requestAccess'])->name('patron.archive.requestAccess');


    // Profile
    Route::get('/patron/profile', [PatronControllerProfile::class, 'index'])->name('patron.profile');
    Route::post('/patron/profile/update', [PatronControllerProfile::class, 'update'])->name('patron.profile.update');

    // Request Archive Access
    Route::post('/patron/archive/request-access/{id}', [PatronControllerArchive::class, 'requestAccess'])->name('patron.archive.requestAccess');
    Route::get('/patron/request', [PatronControllerRequest::class, 'index'])->name('patron.archive.request');
    Route::delete('/patron/request/{id}', [PatronControllerRequest::class, 'destroy'])->name('patron.archive.request.destroy');


}); 


Route::get('/send-request-email', [sendRequestEmail::class, 'sendEmail'])
     ->name('send.request.email');
        
        













require __DIR__.'/auth.php';
