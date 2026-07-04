<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminControllerAnnouncement;
use App\Http\Controllers\AdminControllerArchive;
use App\Http\Controllers\AdminControllerArchiveManage;
use App\Http\Controllers\AdminControllerKeyword;
use App\Http\Controllers\AdminControllerLogs;
use App\Http\Controllers\AdminControllerManage;
use App\Http\Controllers\AdminControllerPatron;
use App\Http\Controllers\AdminControllerProfile;
use App\Http\Controllers\AdminControllerProgram;
use App\Http\Controllers\AdminControllerReport;
use App\Http\Controllers\AdminControllerUser;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\DebugBackupController;
use App\Http\Controllers\PatronController;
use App\Http\Controllers\PatronControllerAnnouncement;
use App\Http\Controllers\PatronControllerArchive;
use App\Http\Controllers\PatronControllerBookmark;
use App\Http\Controllers\PatronControllerProfile;
use App\Http\Controllers\PatronControllerRequest;
use App\Http\Controllers\sendRequestEmail;
use App\Http\Controllers\Shared\ArchiveController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StaffControllerArchive;
use App\Http\Controllers\StaffControllerArchiveManage;
use App\Http\Controllers\StaffControllerKeyword;
use App\Http\Controllers\StaffControllerProfile;
use App\Http\Controllers\StaffControllerProgram;
use App\Http\Controllers\StaffControllerRequest;
use App\Http\Controllers\AdminControllerRequest;
// use App\Http\Controllers\TestMailController;
use Illuminate\Support\Facades\Route;

Route::get('/archive/{archive}/download/{section}', [ArchiveController::class, 'downloadArchiveFile'])
    ->name('archive.download');

Route::get('/', function () {
    return view('welcome');
});


// Admin Middleware Section-------------------

Route::middleware(['auth', 'role:admin'])->group(function () {

    // index
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');

    // AJAX endpoint for filtering archives by program
    Route::get('/admin/archives/by-program/{programId}', [AdminController::class, 'getArchivesByProgram'])->name('admin.archives.by_program');

    // AJAX endpoint for getting views by program
    Route::get('/admin/views/by-program/{programId}', [AdminController::class, 'getViewsByProgram'])->name('admin.views.by_program');

    // Archives
    Route::get('/admin/archive', [ArchiveController::class, 'index'])->name('admin.archive');
    Route::get('/admin/archive/details/{id}', [ArchiveController::class, 'archiveDetails'])->name('admin.archive.details');
    Route::get('/admin/archive/export/most-searched', [AdminControllerArchive::class, 'exportMostSearched'])->name('admin.archive.export-most-searched');
    Route::post('/admin/archive/request-access/{id}', [AdminControllerArchive::class, 'requestAccess'])->name('admin.archive.requestAccess');

    // Archive Create
    Route::get('/admin/archive-page', [AdminControllerArchiveManage::class, 'storeArchive'])->name('admin.manage.archive.page');
    Route::get('/admin/archive-page/{id}', [AdminControllerArchiveManage::class, 'edit'])->name('admin.manage.archive.page.edit');

    // Archive Manage
    Route::get('/admin/archive/manage', [AdminControllerArchiveManage::class, 'index'])->name('admin.archive.manage');
    Route::get('/admin/archive/manage/edit/{id}', [AdminControllerArchiveManage::class, 'edit'])->name('admin.archive.manage.edit');
    Route::delete('/admin/archive/manage/destroy/{id}', [AdminControllerArchiveManage::class, 'destroy'])->name('admin.archive.manage.destroy');
    Route::post('/admin/archive/store', [AdminControllerArchiveManage::class, 'store'])->name('admin.archive.manage.store');
    Route::put('/admin/archives/{id}/status', [AdminControllerArchiveManage::class, 'updateStatus'])->name('admin.archives.manage.updateStatus');
    Route::get('/admin/archives/{id}/edit', [AdminControllerArchiveManage::class, 'edit'])->name('admin.archives.edit');
    Route::put('/admin/archives/{id}', [AdminControllerArchiveManage::class, 'update'])->name('admin.archives.update');
    Route::get('/admin/archives/{id}', [AdminControllerArchiveManage::class, 'getArchive'])->name('admin.archives.get');

    // Request
    Route::get('/admin/request', [AdminControllerRequest::class, 'index'])->name('admin.archive.request');
    Route::get('/admin/archive-request/{id}/approve', [AdminControllerRequest::class, 'approve'])->name('admin.archive.request.approve');
    Route::get('/admin/archive-request/{id}/reject', [AdminControllerRequest::class, 'reject'])->name('admin.archive.request.reject');
    Route::delete('/admin/archive-request/{id}', [AdminControllerRequest::class, 'destroy'])->name('admin.archive.request.destroy');

    // Patron
    Route::get('/admin/patron', [AdminControllerPatron::class, 'index'])->name('admin.patron');
    Route::match(['post','patch'], '/admin/patron/{id}/verify', [AdminControllerPatron::class, 'verify'])->name('admin.patron.verify');
    Route::match(['post','patch'], '/admin/patron/{id}/reject', [AdminControllerPatron::class, 'reject'])->name('admin.patron.reject');
    Route::delete('/admin/patron/{id}', [AdminControllerPatron::class, 'destroy'])->name('admin.patron.destroy');

    // Program
    Route::get('/admin/program', [AdminControllerProgram::class, 'index'])->name('admin.program');
    Route::post('/admin/program', [AdminControllerProgram::class, 'store'])->name('admin.program.store');
    Route::put('/admin/program/{id}', [AdminControllerProgram::class, 'update'])->name('admin.program.update');
    Route::delete('/admin/program/{id}', [AdminControllerProgram::class, 'destroy'])->name('admin.program.destroy');

    // User
    Route::get('/admin/user', [AdminControllerUser::class, 'index'])->name('admin.user');
    Route::delete('/admin/user/{id}', [AdminControllerUser::class, 'destroy'])->name('admin.user.destroy');

    // Manage User
    Route::get('/admin/manage', [AdminControllerManage::class, 'index'])->name('admin.manage');
    Route::get('/admin/manage/{id}', [AdminControllerManage::class, 'fetchEditID'])->name('admin.manage.fetchID');
    Route::post('/admin/manage', [AdminControllerManage::class, 'create'])->name('admin.manage.store');
    Route::put('/admin/manage/{user}', [AdminControllerManage::class, 'update'])->name('admin.manage.update');

    // Keyword
    Route::get('/admin/keyword', [AdminControllerKeyword::class, 'index'])->name('admin.keyword');
    Route::post('/admin/keyword', [AdminControllerKeyword::class, 'store'])->name('admin.keyword.store');
    Route::put('/admin/keyword/{id}', [AdminControllerKeyword::class, 'update'])->name('admin.keyword.update');
    Route::delete('/admin/keyword/{id}', [AdminControllerKeyword::class, 'destroy'])->name('admin.keyword.destroy');

    // Profile
    Route::get('/admin/profile', [AdminControllerProfile::class, 'index'])->name('admin.profile');
    Route::post('/admin/profile/update', [AdminControllerProfile::class, 'update'])->name('admin.profile.update');

    // Reports
    Route::get('/admin/reports', [AdminControllerReport::class, 'index'])->name('admin.report');
    Route::get('/admin/logs', [AdminControllerLogs::class, 'index'])->name('admin.logs');
    Route::get('/admin/reports/top-views-export', [AdminControllerReport::class, 'exportTop10ByViews'])->name('admin.reports.archives.top_views_export');
    Route::get('/admin/reports/program-views-export', [AdminControllerReport::class, 'exportProgramViewsByProgram'])->name('admin.reports.archives.program_views_export');
    Route::get('/admin/reports/monthly-login-stats-export/{year?}', [AdminControllerReport::class, 'exportMonthlyLoginStats'])->name('admin.reports.monthly_login_stats_export')->where('year', '\d{4}');
    Route::get('/admin/reports/patron-login-count-export/{year?}', [AdminControllerReport::class, 'exportPatronLoginCount'])->name('admin.reports.patron_login_count_export')->where('year', '\d{4}');
    Route::get('/admin/reports/user/{month}', [AdminControllerReport::class, 'exportPatronsByMonth'])->name('admin.reports.patrons.by_month')->where('month', '[1-9]|1[0-2]');
    Route::get('/admin/reports/patrons-range', [AdminControllerReport::class, 'exportPatronsByDateRange'])->name('admin.reports.patrons.by_date_range');
    Route::get('/admin/reports/archives/{year}', [AdminControllerReport::class, 'exportArchivesByYear'])->name('admin.reports.archives.by_year')->where('year', '\d{4}');
    Route::get('/admin/reports/archives-range', [AdminControllerReport::class, 'exportArchivesByDateRange'])->name('admin.reports.archives.by_date_range');

    // Announcement
    Route::get('/admin/announcement', [AdminControllerAnnouncement::class, 'index'])->name('admin.announcement');
    Route::post('/admin/announcement', [AdminControllerAnnouncement::class, 'store'])->name('admin.announcement.store');
    Route::put('/admin/announcement/{id}', [AdminControllerAnnouncement::class, 'update'])->name('admin.announcement.update');
    Route::delete('/admin/announcement/{id}', [AdminControllerAnnouncement::class, 'destroy'])->name('admin.announcement.destroy');

    // Backup
    Route::get('/admin/backup', [BackupController::class, 'backup'])->name('admin.backup');
    Route::post('/admin/restore', [BackupController::class, 'restore'])->name('admin.restore');
    Route::post('/admin/restore/full', [BackupController::class, 'restoreFull'])->name('admin.restore.full');
    Route::post('/admin/restore/sql', [BackupController::class, 'restoreSql'])->name('admin.restore.sql');

    // Debug Backup
    Route::get('/admin/debug/backup', [DebugBackupController::class, 'debugInfo'])->name('admin.debug.backup');
    Route::post('/admin/debug/backup/clear-temp', [DebugBackupController::class, 'clearTempFiles'])->name('admin.debug.backup.clear-temp');

});

// Route for test mail removed because controller file is missing.
// Route::get('/send-test-mail', [TestMailController::class, 'sendMail'])->middleware('auth');

// Staff Middleware Section-------------------
Route::middleware(['auth', 'role:staff'])->group(function () {

    // index
    Route::get('/staff', [StaffController::class, 'index'])->name('staff.dashboard');

    // Archive
    Route::get('/staff/archive', [ArchiveController::class, 'index'])->name('staff.archive');
    Route::get('/staff/archive/details/{id}', [ArchiveController::class, 'archiveDetails'])->name('staff.archive.details');

    // Archive Create
    Route::get('/staff/archive-page', [StaffControllerArchiveManage::class, 'storeArchive'])->name('staff.manage.archive.page');
    Route::get('/staff/archive-page/{id}', [StaffControllerArchiveManage::class, 'edit'])->name('staff.manage.archive.page.edit');

    // Archive Manage
    Route::get('/staff/manage', [StaffControllerArchiveManage::class, 'index'])->name('staff.archive.manage');
    Route::get('/staff/archive/manage/edit/{id}', [StaffControllerArchiveManage::class, 'edit'])->name('staff.archive.manage.edit');
    Route::delete('/staff/archive/manage/destroy/{id}', [StaffControllerArchiveManage::class, 'destroy'])->name('staff.archive.manage.destroy');
    Route::post('/staff/archive/store', [StaffControllerArchiveManage::class, 'store'])->name('staff.archive.manage.store');
    Route::put('/staff/archives/{id}/status', [StaffControllerArchiveManage::class, 'updateStatus'])->name('staff.archives.manage.updateStatus');
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
    Route::delete('/staff/archive-request/{id}', [StaffControllerRequest::class, 'destroy'])->name('staff.archive.request.destroy');

    // Profile
    Route::get('/staff/profile', [StaffControllerProfile::class, 'index'])->name('staff.profile');
    Route::post('/staff/profile/update', [StaffControllerProfile::class, 'update'])->name('staff.profile.update');

});

// Patron Middleware Section-------------------
Route::middleware(['auth', 'role:patron'])->group(function () {

    // index
    Route::get('/patron', [PatronController::class, 'index'])->name('patron.dashboard');

    // Archive
    Route::get('/patron/archive', [ArchiveController::class, 'indexPatron'])->name('patron.archive');
    Route::get('/patron/archive/details/{id}', [ArchiveController::class, 'archiveDetails'])->name('patron.archive.details');
    Route::get('/patron/archives/{id}/view', [PatronControllerArchive::class, 'getArchive'])->name('patron.archives.view');
    Route::post('/patron/archive/request-access/{id}', [ArchiveController::class, 'requestAccess'])->name('patron.archive.requestAccess');
    Route::put('/patron/archive/{id}/toggle', [PatronControllerArchive::class, 'toggleBookmark'])->name('patron.archive.toggle');

    // Profile
    Route::get('/patron/profile', [PatronControllerProfile::class, 'index'])->name('patron.profile');
    Route::post('/patron/profile/update', [PatronControllerProfile::class, 'update'])->name('patron.profile.update');

    // Bookmark
    Route::get('/patron/bookmark', [PatronControllerBookmark::class, 'index'])->name('patron.bookmark');
    Route::get('/patron/bookmark/{id}/view', [PatronControllerBookmark::class, 'getArchive'])->name('patron.bookmark.view');
    Route::post('/patron/bookmark/request-access/{id}', [PatronControllerBookmark::class, 'requestAccess'])->name('patron.bookmark.requestAccess');
    Route::put('/patron/bookmark/{id}/toggle', [PatronControllerBookmark::class, 'toggleBookmark'])->name('patron.bookmark.toggle');

    // Request
    Route::get('/patron/request', [PatronControllerRequest::class, 'index'])->name('patron.archive.request');
    Route::get('/patron/request/{id}/view', [PatronControllerRequest::class, 'getArchive'])->name('patron.request.view');
    Route::post('/patron/request/request-access/{id}', [PatronControllerRequest::class, 'requestAccess'])->name('patron.request.requestAccess');

    // Announcement
    Route::get('/patron/announcement/{id}', [PatronControllerAnnouncement::class, 'page'])->name('patron.announcement.page');

});

Route::get('/send-request-email', [sendRequestEmail::class, 'sendEmail'])
    ->name('send.request.email');

// Removed stale test mail route because TestMailController is not present.
// Route::get('/send-test-mail', [TestMailController::class, 'sendMail'])->middleware('auth');

Route::view('/staff/archive/view', 'staff.staff_archive_view')
    ->name('staff.archive.view');

require __DIR__.'/auth.php';