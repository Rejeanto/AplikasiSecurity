<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentStep;
use App\Models\Question;
use App\Models\Step;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StepController extends Controller
{
    /**
     * Display assessment steps management page
     */
    public function index(Assessment $assessment)
    {
        $assessment->load(['steps' => function ($query) {
            $query->orderBy('step_number');
        }, 'steps.questions' => function ($query) {
            $query->orderBy('step_questions.order');
        }, 'steps.questions.questionOptions']);

        // Get available questions yang belum digunakan di assessment ini
        $usedQuestionIds = $assessment->steps->flatMap(function ($step) {
            return $step->questions->pluck('id');
        })->unique()->toArray();

        $availableQuestions = Question::whereNotIn('id', $usedQuestionIds)
            ->orderBy('question_text')
            ->get();

        return view('admin.assessments.steps.index', compact('assessment', 'availableQuestions'));
    }

    /**
     * Update step details (title & description)
     */
    public function update(Request $request, Assessment $assessment, Step $step)
    {
        $validated = $request->validate([
            'step_title' => 'required|string|max:255',
            'step_description' => 'nullable|string'
        ]);

        try {
            $step->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Step berhasil diperbarui!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui step: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Attach question to step
     */
    public function attachQuestion(Request $request, Assessment $assessment, Step $step)
    {
        $validated = $request->validate([
            'question_id' => 'required|exists:questions,id'
        ]);

        try {
            // Cek apakah question sudah ada di step lain dalam assessment ini
            $questionExistsInOtherStep = DB::table('step_questions')
                ->join('steps', 'step_questions.assessment_step_id', '=', 'steps.id')
                ->where('steps.assessment_id', $assessment->id)
                ->where('step_questions.question_id', $validated['question_id'])
                ->exists();

            if ($questionExistsInOtherStep) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pertanyaan ini sudah digunakan di step lain!'
                ], 422);
            }

            // Get max order untuk step ini
            $maxOrder = $step->questions()->max('step_questions.order') ?? 0;

            // Attach question dengan order
            $step->questions()->attach($validated['question_id'], [
                'order' => $maxOrder + 1
            ]);

            // Load question dengan options
            $question = Question::with('questionOptions')->find($validated['question_id']);

            return response()->json([
                'success' => true,
                'message' => 'Pertanyaan berhasil ditambahkan!',
                'question' => $question
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan pertanyaan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Detach question from step
     */
    public function detachQuestion(Assessment $assessment, Step $step, Question $question)
    {
        try {
            $step->questions()->detach($question->id);

            // Reorder remaining questions
            $remainingQuestions = $step->questions()->orderBy('step_questions.order')->get();
            
            foreach ($remainingQuestions as $index => $q) {
                $step->questions()->updateExistingPivot($q->id, [
                    'order' => $index + 1
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pertanyaan berhasil dihapus dari step!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus pertanyaan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reorder questions within a step
     */
    public function reorderQuestions(Request $request, Assessment $assessment, Step $step)
    {
        $validated = $request->validate([
            'question_ids' => 'required|array',
            'question_ids.*' => 'exists:questions,id'
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['question_ids'] as $index => $questionId) {
                $step->questions()->updateExistingPivot($questionId, [
                    'order' => $index + 1
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Urutan pertanyaan berhasil diperbarui!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah urutan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Move question between steps
     */
    public function moveQuestion(Request $request, Assessment $assessment)
    {
        $validated = $request->validate([
            'question_id' => 'required|exists:questions,id',
            'from_step_id' => 'required|exists:steps,id',
            'to_step_id' => 'required|exists:steps,id'
        ]);

        DB::beginTransaction();
        try {
            $fromStep = Step::findOrFail($validated['from_step_id']);
            $toStep = Step::findOrFail($validated['to_step_id']);

            // Detach from old step
            $fromStep->questions()->detach($validated['question_id']);

            // Get max order in target step
            $maxOrder = $toStep->questions()->max('step_questions.order') ?? 0;

            // Attach to new step
            $toStep->questions()->attach($validated['question_id'], [
                'order' => $maxOrder + 1
            ]);

            // Reorder remaining questions in source step
            $remainingQuestions = $fromStep->questions()->orderBy('step_questions.order')->get();
            foreach ($remainingQuestions as $index => $q) {
                $fromStep->questions()->updateExistingPivot($q->id, [
                    'order' => $index + 1
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pertanyaan berhasil dipindahkan!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memindahkan pertanyaan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get questions for specific step (AJAX)
     */
    public function getStepQuestions(Assessment $assessment, Step $step)
    {
        try {
            $questions = $step->questions()
                ->with('questionOptions')
                ->orderBy('step_questions.order')
                ->get();

            return response()->json([
                'success' => true,
                'questions' => $questions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat pertanyaan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk attach questions to step
     */
    public function bulkAttachQuestions(Request $request, Assessment $assessment, Step $step)
    {
        $validated = $request->validate([
            'question_ids' => 'required|array|min:1',
            'question_ids.*' => 'exists:questions,id'
        ]);

        DB::beginTransaction();
        try {
            // Get current max order
            $maxOrder = $step->questions()->max('step_questions.order') ?? 0;

            // Check for duplicates in other steps
            $existingQuestionIds = DB::table('step_questions')
                ->join('steps', 'step_questions.assessment_step_id', '=', 'steps.id')
                ->where('steps.assessment_id', $assessment->id)
                ->whereIn('step_questions.question_id', $validated['question_ids'])
                ->pluck('step_questions.question_id')
                ->toArray();

            if (!empty($existingQuestionIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Beberapa pertanyaan sudah digunakan di step lain!'
                ], 422);
            }

            // Attach all questions
            foreach ($validated['question_ids'] as $index => $questionId) {
                $step->questions()->attach($questionId, [
                    'order' => $maxOrder + $index + 1
                ]);
            }

            DB::commit();

            $questions = $step->questions()
                ->with('questionOptions')
                ->orderBy('step_questions.order')
                ->get();

            return response()->json([
                'success' => true,
                'message' => count($validated['question_ids']) . ' pertanyaan berhasil ditambahkan!',
                'questions' => $questions
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan pertanyaan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Copy all questions from another step
     */
    public function copyQuestionsFromStep(Request $request, Assessment $assessment, Step $step)
    {
        $validated = $request->validate([
            'source_step_id' => 'required|exists:steps,id'
        ]);

        DB::beginTransaction();
        try {
            $sourceStep = Step::findOrFail($validated['source_step_id']);
            $sourceQuestions = $sourceStep->questions()->orderBy('step_questions.order')->get();

            if ($sourceQuestions->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Step sumber tidak memiliki pertanyaan!'
                ], 422);
            }

            // Get current max order
            $maxOrder = $step->questions()->max('step_questions.order') ?? 0;

            // Copy questions
            foreach ($sourceQuestions as $index => $question) {
                // Skip if already exists in current step
                if (!$step->questions()->where('question_id', $question->id)->exists()) {
                    $step->questions()->attach($question->id, [
                        'order' => $maxOrder + $index + 1
                    ]);
                }
            }

            DB::commit();

            $questions = $step->questions()
                ->with('questionOptions')
                ->orderBy('step_questions.order')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Pertanyaan berhasil disalin!',
                'questions' => $questions
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyalin pertanyaan: ' . $e->getMessage()
            ], 500);
        }
    }
}