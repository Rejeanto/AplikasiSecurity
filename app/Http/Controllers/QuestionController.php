<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    /**
     * Display a listing of questions (bank soal)
     */
    public function index(Request $request)
    {
        $query = Question::with('questionOptions')->withCount('assessmentSteps');

        // Filter by type
        if ($request->has('type') && $request->type !== '') {
            $query->where('question_type', $request->type);
        }

        // Search
        if ($request->has('search') && $request->search !== '') {
            $query->where('question_text', 'like', '%' . $request->search . '%');
        }

        $questions = $query->latest()->paginate(15);

        return view('admin.questions.index', compact('questions'));
    }

    /**
     * Show the form for creating a new question
     */
    public function create()
    {
        $questionTypes = Question::getQuestionTypes();
        return view('admin.questions.create', compact('questionTypes'));
    }

    /**
     * Store a newly created question
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:multiple_choice,text_input',
            'is_required' => 'nullable|boolean',
            'options' => 'required_if:question_type,multiple_choice|array|min:2',
            'options.*.option_text' => 'required_if:question_type,multiple_choice|string',
            'options.*.option_value' => 'nullable|integer',
        ], [
            'options.required_if' => 'Pilihan ganda harus memiliki minimal 2 opsi',
            'options.min' => 'Pilihan ganda harus memiliki minimal 2 opsi',
        ]);

        DB::beginTransaction();
        try {
            // Create question
            $question = Question::create([
                'question_text' => $validated['question_text'],
                'question_type' => $validated['question_type'],
                'is_required' => $request->has('is_required') ? true : false,
            ]);

            // Create options if multiple choice
            if ($validated['question_type'] === Question::TYPE_MULTIPLE_CHOICE && isset($validated['options'])) {
                foreach ($validated['options'] as $index => $option) {
                    if (!empty($option['option_text'])) {
                        QuestionOption::create([
                            'question_id' => $question->id,
                            'option_text' => $option['option_text'],
                            'option_value' => $option['option_value'] ?? null,
                            'order' => $index + 1,
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.questions.index')
                ->with('success', 'Pertanyaan berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal membuat pertanyaan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified question
     */
    public function show(Question $question)
    {
        $question->load(['questionOptions', 'assessmentSteps.assessment']);
        
        return view('admin.questions.show', compact('question'));
    }

    /**
     * Show the form for editing the question
     */
    public function edit(Question $question)
    {
        $question->load('questionOptions');
        $questionTypes = Question::getQuestionTypes();
        
        return view('admin.questions.edit', compact('question', 'questionTypes'));
    }

    /**
     * Update the specified question
     */
    public function update(Request $request, Question $question)
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:multiple_choice,text_input',
            'is_required' => 'nullable|boolean',
            'options' => 'required_if:question_type,multiple_choice|array|min:2',
            'options.*.option_text' => 'required_if:question_type,multiple_choice|string',
            'options.*.option_value' => 'nullable|integer',
        ], [
            'options.required_if' => 'Pilihan ganda harus memiliki minimal 2 opsi',
            'options.min' => 'Pilihan ganda harus memiliki minimal 2 opsi',
        ]);

        DB::beginTransaction();
        try {
            // Update question
            $question->update([
                'question_text' => $validated['question_text'],
                'question_type' => $validated['question_type'],
                'is_required' => $request->has('is_required') ? true : false,
            ]);

            // Handle options
            if ($validated['question_type'] === Question::TYPE_MULTIPLE_CHOICE) {
                // Delete old options
                $question->questionOptions()->delete();

                // Create new options
                if (isset($validated['options'])) {
                    foreach ($validated['options'] as $index => $option) {
                        if (!empty($option['option_text'])) {
                            QuestionOption::create([
                                'question_id' => $question->id,
                                'option_text' => $option['option_text'],
                                'option_value' => $option['option_value'] ?? null,
                                'order' => $index + 1,
                            ]);
                        }
                    }
                }
            } else {
                // Delete all options if changed to text_input
                $question->questionOptions()->delete();
            }

            DB::commit();

            return redirect()
                ->route('admin.questions.index')
                ->with('success', 'Pertanyaan berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui pertanyaan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified question
     */
    public function destroy(Question $question)
    {
        DB::beginTransaction();
        try {
            // Check if question is being used
            $usageCount = $question->assessmentSteps()->count();
            
            if ($usageCount > 0) {
                return redirect()
                    ->back()
                    ->with('error', "Pertanyaan tidak dapat dihapus karena sedang digunakan di {$usageCount} assessment!");
            }

            // Delete options
            $question->questionOptions()->delete();
            
            // Delete question (soft delete)
            $question->delete();

            DB::commit();

            return redirect()
                ->route('admin.questions.index')
                ->with('success', 'Pertanyaan berhasil dihapus!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus pertanyaan: ' . $e->getMessage());
        }
    }

    /**
     * Duplicate question
     */
    public function duplicate(Question $question)
    {
        DB::beginTransaction();
        try {
            // Duplicate question
            $newQuestion = $question->replicate();
            $newQuestion->question_text = $question->question_text . ' (Copy)';
            $newQuestion->save();

            // Duplicate options
            foreach ($question->questionOptions as $option) {
                $newOption = $option->replicate();
                $newOption->question_id = $newQuestion->id;
                $newOption->save();
            }

            DB::commit();

            return redirect()
                ->route('admin.questions.edit', $newQuestion)
                ->with('success', 'Pertanyaan berhasil diduplikasi!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Gagal menduplikasi pertanyaan: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete questions
     */
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'question_ids' => 'required|array|min:1',
            'question_ids.*' => 'exists:questions,id'
        ]);

        DB::beginTransaction();
        try {
            $questions = Question::whereIn('id', $validated['question_ids'])->get();
            $deletedCount = 0;
            $skippedCount = 0;

            foreach ($questions as $question) {
                $usageCount = $question->assessmentSteps()->count();
                
                if ($usageCount > 0) {
                    $skippedCount++;
                    continue;
                }

                $question->questionOptions()->delete();
                $question->delete();
                $deletedCount++;
            }

            DB::commit();

            $message = "Berhasil menghapus {$deletedCount} pertanyaan.";
            if ($skippedCount > 0) {
                $message .= " {$skippedCount} pertanyaan dilewati karena sedang digunakan.";
            }

            return redirect()
                ->route('admin.questions.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus pertanyaan: ' . $e->getMessage());
        }
    }
}