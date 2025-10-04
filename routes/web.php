<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\QuizController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\StepController;
use App\Models\Assessment;
use App\Models\Quiz;

Route::get('/', function () {
    $quizzes = Quiz::all();
    return view('index', compact('quizzes'));
})->name('index');

Route::view('/admin', 'admin')->name('admin');

// Route::prefix('admin')->name('admin.')->group(function () {

//     // daftar semua quiz
//     Route::get('quizzes', [QuizController::class, 'index'])->name('quizzes.index');

//     // form tambah + simpan
//     Route::get('quizzes/create', [QuizController::class, 'create'])->name('quizzes.create');
//     Route::post('quizzes', [QuizController::class, 'store'])->name('quizzes.store');

//     // edit + update  (gunakan {quiz}, bukan {id})
//     Route::get('quizzes/{quiz}/edit', [QuizController::class, 'edit'])->name('quizzes.edit');
//     Route::put('quizzes/{quiz}', [QuizController::class, 'update'])->name('quizzes.update');

//     // hapus
//     Route::delete('quizzes/{quiz}', [QuizController::class, 'destroy'])->name('quizzes.destroy');
// });

Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', function() {
        return phpinfo();
    })->name('dashboard');

    Route::resource('assessments', AssessmentController::class);
    // Additional Routes
    Route::post('assessments/{assessment}/toggle-status', [AssessmentController::class, 'toggleStatus'])
        ->name('assessments.toggle-status');
    
    Route::post('assessments/{assessment}/duplicate', [AssessmentController::class, 'duplicate'])
        ->name('assessments.duplicate');
    
    // Steps Management (akan kita buat nanti)
    Route::get('assessments/{assessment}/steps', [StepController::class, 'index'])
        ->name('assessments.steps.index');
    Route::resource('assessments.steps', StepController::class);
    Route::resource('assessments.questions', QuestionController::class);

    // Step Management Routes
    Route::prefix('assessments/{assessment}/steps')->name('assessments.steps.')->group(function () {
        // Main page
        Route::get('/', [StepController::class, 'index'])
            ->name('index');
        
        // Update step info
        Route::put('/{step}', [StepController::class, 'update'])
            ->name('update');
        
        // Attach/Detach Questions
        Route::post('/{step}/questions/attach', [StepController::class, 'attachQuestion'])
            ->name('attach-question');
        
        Route::delete('/{step}/questions/{question}', [StepController::class, 'detachQuestion'])
            ->name('detach-question');
        
        // Reorder questions
        Route::post('/{step}/questions/reorder', [StepController::class, 'reorderQuestions'])
            ->name('reorder-questions');
        
        // Move question between steps
        Route::post('/move-question', [StepController::class, 'moveQuestion'])
            ->name('move-question');
        
        // Get step questions (AJAX)
        Route::get('/{step}/questions', [StepController::class, 'getStepQuestions'])
            ->name('get-questions');
        
        // Bulk operations
        Route::post('/{step}/questions/bulk-attach', [StepController::class, 'bulkAttachQuestions'])
            ->name('bulk-attach-questions');
        
        Route::post('/{step}/questions/copy-from', [StepController::class, 'copyQuestionsFromStep'])
            ->name('copy-questions');
    });

    Route::resource('questions', QuestionController::class);
    Route::post('questions/{question}/duplicate', [QuestionController::class, 'duplicate'])
        ->name('questions.duplicate');
    Route::post('questions/bulk-delete', [QuestionController::class, 'bulkDelete'])
        ->name('questions.bulk-delete');
});
