<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QuizController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::latest()->paginate(10);
        return view('admin.quizzes.index', compact('quizzes'));
    }

    public function create()
    {
        return view('admin.quizzes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'          => 'required|string|max:255',
            'question'       => 'required|string',
            'correct_answer' => 'required|string',
            'video' => 'nullable|mimes:mp4,mov,avi,wmv|max:51200',
        ]);

        if ($request->hasFile('video')) {
            $data['video_path'] = $request->file('video')->store('videos', 'public');
        }

        Quiz::create($data);
        return redirect()->route('admin.quizzes.index')->with('success', 'Quiz berhasil ditambahkan!');
    }

    public function edit(Quiz $quiz)
    {
        return view('admin.quizzes.edit', compact('quiz'));
    }

    public function update(Request $request, Quiz $quiz)
    {
        $data = $request->validate([
            'title'          => 'required|string|max:255',
            'question'       => 'required|string',
            'correct_answer' => 'required|string',
            'video' => 'nullable|mimes:mp4,mov,avi,wmv|max:51200', // 100 MB
        ]);

        if ($request->hasFile('video')) {
            if ($quiz->video_path && Storage::disk('public')->exists($quiz->video_path)) {
                Storage::disk('public')->delete($quiz->video_path);
            }
            $data['video_path'] = $request->file('video')->store('videos', 'public');
        }

        $quiz->update($data);
        return redirect()->route('admin.quizzes.index')->with('success', 'Quiz berhasil diperbarui!');
    }

    public function destroy(Quiz $quiz)
    {
        if ($quiz->video_path && Storage::disk('public')->exists($quiz->video_path)) {
            Storage::disk('public')->delete($quiz->video_path);
        }
        $quiz->delete();
        return redirect()->route('admin.quizzes.index')->with('success', 'Quiz berhasil dihapus!');
    }

    public function show(Quiz $quiz)
    {
        return view('admin.quizzes.show', compact('quiz'));
    }
}
