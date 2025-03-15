<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\PreUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\SidebarController;
use App\Http\Controllers\TraineeController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CoordinatorController;


// Public Route
Route::get('/', function () {
    return view('welcome');
});

// Profile Routes
Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

// Authenticated Routes
Route::middleware('auth')->group(function () {

    // Pre-User Routes
    Route::prefix('pre-user')->name('pre_user.')->group(function () {
        Route::get('/dashboard', [PreUserController::class, 'index'])->name('dashboard');
        Route::post('/documents', [PreUserController::class, 'uploadDocuments'])->name('documents.upload');
    });

    // Coordinator Routes
    Route::prefix('coordinator')->name('coordinator.')->group(function () {
        Route::get('/dashboard', [CoordinatorController::class, 'index'])->name('dashboard');
        Route::get('/pre-users', [CoordinatorController::class, 'preUsers'])->name('pre_users');
        Route::get('/trainees', [CoordinatorController::class, 'trainees'])->name('trainees');
        Route::get('/user-documents/{user}', [CoordinatorController::class, 'showUserDocuments'])->name('user.documents');
        Route::post('/promote/{user}', [CoordinatorController::class, 'promoteToTrainee'])->name('promote');
        Route::get('/records/{traineeId}', [CoordinatorController::class, 'records'])->name('trainee-records'); 
        Route::get('/trainee-journal-records/{traineeId}', [CoordinatorController::class, 'traineeJournalRecords'])->name('trainee-journal-records');
        Route::get('/trainee-attendance-all-records', [CoordinatorController::class, 'attendanceAll'])->name('trainee-attendance-all-records');
        Route::get('/trainee-journal-entry/{journal}', [CoordinatorController::class, 'traineeJournalEntry'])->name('trainee-journal-entry');
       
        Route::post('/trainee/{traineeId}/add-hours', [CoordinatorController::class, 'addRenderedHours'])->name('addRenderedHours');
    
    });

    // Trainee Routes
    Route::prefix('trainee')->name('trainee.')->group(function () {
        Route::get('/dashboard', [TraineeController::class, 'index'])->name('dashboard');

    });

    // Document Routes
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/upload/{type}', [DocumentController::class, 'upload'])->name('upload');
        Route::post('/store', [DocumentController::class, 'store'])->name('store');
        Route::post('/update-status/{document}', [DocumentController::class, 'updateStatus'])->name('update-status');
    });

    // Journal routes
    Route::patch('/journal/update/{id}', [JournalController::class, 'update'])->name('journal.update');
    Route::get('/journal/preview-pdf', [JournalController::class, 'previewPdf'])->name('journal.preview-pdf');
    Route::resource('journal', JournalController::class);
    Route::get('/journal/{journal}', [JournalController::class, 'show'])->name('journal.show');
    

    
    // Attendance routes
    Route::resource('attendance', AttendanceController::class)->except(['show']);
    Route::post('/attendance/mark', [AttendanceController::class, 'markAttendance'])->name('attendance.mark');
    Route::get('/attendance/preview-pdf', [AttendanceController::class, 'previewPdf'])->name('attendance.preview-pdf');


    // Sidebar routes
    Route::get('/sidebar/coordinator', [SidebarController::class, 'coordinator'])->name('sidebar.coordinator');
    Route::get('/sidebar/trainee', [SidebarController::class, 'trainee'])->name('sidebar.trainee');


    // Request routes
    Route::get('/request', [RequestController::class, 'index'])->name('request.forgot');
    Route::post('/request/store', [RequestController::class, 'store'])->name('request.store');
    Route::post('/request/absent', [RequestController::class, 'storeAbsentRequest'])->name('request.absent.store');


    Route::get('/requests', [RequestController::class, 'index'])->name('coordinator.requests');

    Route::post('/request/{id}/approve', [RequestController::class, 'approve'])->name('coordinator.request.approve');
    Route::post('/request/{id}/reject', [RequestController::class, 'reject'])->name('coordinator.request.reject');


     // Approve/Reject Absent Requests
     Route::post('/absent/{id}/approve', [RequestController::class, 'approveAbsent'])->name('absents.approve');
     Route::post('/absent/{id}/reject', [RequestController::class, 'rejectAbsent'])->name('absents.reject');
 
     // View Absent Requests
     Route::get('/absents', [RequestController::class, 'showAbsentRequests'])->name('absents.index');

     Route::delete('/request/{id}', [RequestController::class, 'delete'])->name('requests.delete');


});

// ORIGINAL
require __DIR__.'/auth.php';
