<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Step;
use App\Models\UserResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserAssessmentController extends Controller
{
    /**
     * Display list of available assessments
     */
    public function index()
    {
        $assessments = Assessment::active()
            ->with('steps')
            ->get()
            ->map(function ($assessment) {
                $assessment->total_questions = $assessment->steps->sum(function ($step) {
                    return $step->questions->count();
                });
                
                // Check if user has completed
                $assessment->is_completed = $assessment->isCompletedBy(auth()->id());
                
                // Get progress
                $totalQuestions = $assessment->total_questions;
                $answeredQuestions = UserResponse::where('user_id', auth()->id())
                    ->where('assessment_id', $assessment->id)
                    ->count();
                
                $assessment->progress = $totalQuestions > 0 
                    ? round(($answeredQuestions / $totalQuestions) * 100) 
                    : 0;
                
                return $assessment;
            });

        return view('user.assessments.index', compact('assessments'));
    }

    /**
     * Start or continue assessment
     */
    public function start(Assessment $assessment)
    {
        if (!$assessment->is_active) {
            return redirect()
                ->route('user.assessments.index')
                ->with('error', 'Assessment ini tidak tersedia.');
        }

        // Get first step or continue from last step
        $lastAnswered = UserResponse::where('user_id', auth()->id())
            ->where('assessment_id', $assessment->id)
            ->with('question.assessmentSteps')
            ->latest()
            ->first();

        if ($lastAnswered) {
            $lastStep = $lastAnswered->question->assessmentSteps()
                ->where('assessment_id', $assessment->id)
                ->first();
            
            $nextStep = $assessment->steps()
                ->where('step_number', '>', $lastStep->step_number)
                ->orderBy('step_number')
                ->first();
            
            $currentStep = $nextStep ?? $lastStep;
        } else {
            $currentStep = $assessment->steps()->where('step_number', 1)->first();
        }

        return redirect()->route('user.assessments.show', [
            'assessment' => $assessment,
            'step' => $currentStep->step_number
        ]);
    }

    /**
     * Display assessment step
     */
    public function show(Assessment $assessment, Request $request)
    {
        if (!$assessment->is_active) {
            return redirect()
                ->route('user.assessments.index')
                ->with('error', 'Assessment ini tidak tersedia.');
        }

        $stepNumber = $request->get('step', 1);
        
        $step = $assessment->steps()
            ->with(['questions.questionOptions'])
            ->where('step_number', $stepNumber)
            ->firstOrFail();

        // Get user's existing responses for this step
        $userResponses = UserResponse::where('user_id', auth()->id())
            ->where('assessment_id', $assessment->id)
            ->whereIn('question_id', $step->questions->pluck('id'))
            ->get()
            ->keyBy('question_id');

        // Calculate progress
        $totalQuestions = $assessment->steps->sum(function ($s) {
            return $s->questions->count();
        });
        
        $answeredQuestions = UserResponse::where('user_id', auth()->id())
            ->where('assessment_id', $assessment->id)
            ->count();
        
        $progress = $totalQuestions > 0 
            ? round(($answeredQuestions / $totalQuestions) * 100) 
            : 0;

        return view('user.assessments.show', compact(
            'assessment',
            'step',
            'userResponses',
            'progress'
        ));
    }

    /**
     * Save step answers
     */
    public function saveStep(Request $request, Assessment $assessment, Step $step)
    {
        if (!$assessment->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Assessment tidak tersedia.'
            ], 403);
        }

        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'nullable'
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['answers'] as $questionId => $answer) {
                $question = $step->questions()->find($questionId);
                
                if (!$question) {
                    continue;
                }

                // Skip if answer is empty and question is not required
                if (empty($answer) && !$question->is_required) {
                    continue;
                }

                // Validate required questions
                if ($question->is_required && empty($answer)) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Semua pertanyaan wajib harus dijawab!'
                    ], 422);
                }

                // Save or update response
                $responseData = [
                    'user_id' => auth()->id(),
                    'assessment_id' => $assessment->id,
                    'question_id' => $questionId,
                ];

                if ($question->isMultipleChoice()) {
                    $responseData['selected_option_id'] = $answer;
                    $responseData['answer_text'] = null;
                } else {
                    $responseData['answer_text'] = $answer;
                    $responseData['selected_option_id'] = null;
                }

                UserResponse::updateOrCreate(
                    [
                        'user_id' => auth()->id(),
                        'assessment_id' => $assessment->id,
                        'question_id' => $questionId,
                    ],
                    $responseData
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Jawaban berhasil disimpan!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan jawaban: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit final assessment
     */
    public function submit(Assessment $assessment)
    {
        // Check if all required questions are answered
        $totalQuestions = $assessment->steps->sum(function ($step) {
            return $step->questions->where('is_required', true)->count();
        });

        $answeredRequired = UserResponse::where('user_id', auth()->id())
            ->where('assessment_id', $assessment->id)
            ->whereHas('question', function ($query) {
                $query->where('is_required', true);
            })
            ->count();

        if ($answeredRequired < $totalQuestions) {
            return redirect()
                ->back()
                ->with('error', 'Masih ada pertanyaan wajib yang belum dijawab!');
        }

        return redirect()
            ->route('user.assessments.result', $assessment)
            ->with('success', 'Assessment berhasil diselesaikan!');
    }

    /**
     * Display assessment result
     */
    public function result(Assessment $assessment)
    {
        $responses = UserResponse::where('user_id', auth()->id())
            ->where('assessment_id', $assessment->id)
            ->with(['question.questionOptions', 'selectedOption'])
            ->get();

        // Group by step
        $responsesByStep = $responses->groupBy(function ($response) use ($assessment) {
            $step = $response->question->assessmentSteps()
                ->where('assessment_id', $assessment->id)
                ->first();
            return $step ? $step->step_number : 0;
        });

        // Calculate score if applicable
        $totalScore = $responses->sum(function ($response) {
            return $response->selectedOption->option_value ?? 0;
        });

        return view('user.assessments.result', compact(
            'assessment',
            'responses',
            'responsesByStep',
            'totalScore'
        ));
    }
}