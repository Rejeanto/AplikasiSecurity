<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\QuizController;
use App\Models\Quiz;

Route::get('/', function () {
    $quizzes = Quiz::all();
    return view('index', compact('quizzes'));
})->name('index');

Route::view('/admin', 'admin')->name('admin');

Route::prefix('admin')->name('admin.')->group(function () {

    // daftar semua quiz
    Route::get('quizzes', [QuizController::class, 'index'])->name('quizzes.index');

    // form tambah + simpan
    Route::get('quizzes/create', [QuizController::class, 'create'])->name('quizzes.create');
    Route::post('quizzes', [QuizController::class, 'store'])->name('quizzes.store');

    // edit + update  (gunakan {quiz}, bukan {id})
    Route::get('quizzes/{quiz}/edit', [QuizController::class, 'edit'])->name('quizzes.edit');
    Route::put('quizzes/{quiz}', [QuizController::class, 'update'])->name('quizzes.update');

    // hapus
    Route::delete('quizzes/{quiz}', [QuizController::class, 'destroy'])->name('quizzes.destroy');
});
