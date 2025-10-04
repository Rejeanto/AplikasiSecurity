<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssessmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assessments = Assessment::withCount([
            'steps'
            // 'steps as total_questions' => function ($query) {
            //     $query->join('step_questions', 'assessment_steps.id', '=', 'step_questions.assessment_step_id');
            // }
        ])
        ->latest()
        ->paginate(10);

        return view('admin.assessments.index', compact('assessments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.assessments.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_steps' => 'required|integer|min:1|max:10',
            'is_active' => 'nullable|boolean'
        ]);

        // Set is_active
        $validated['is_active'] = $request->has('is_active') ? true : false;
        $validated['total_questions'] = 0;


        DB::beginTransaction();
        try {
            // Create assessment
            $assessment = Assessment::create($validated);

            // Auto-create steps sesuai total_steps
            for ($i = 1; $i <= $validated['total_steps']; $i++) {
                $assessment->steps()->create([
                    'step_number' => $i,
                    'step_title' => "Step {$i}",
                    'step_description' => "Deskripsi untuk step {$i}"
                ]);
            }

            DB::commit();

            return redirect()
                ->route('admin.assessments.index')
                ->with('success', 'Assessment berhasil dibuat! Silakan setup steps dan pertanyaan.');

            // return redirect()
            //     ->route('admin.assessments.steps.index', $assessment)
            //     ->with('success', 'Assessment berhasil dibuat! Silakan setup steps dan pertanyaan.');

        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal membuat assessment: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Assessment $assessment)
    {
        $assessment->load(['steps.questions.questionOptions']);
        
        // Hitung total pertanyaan
        $totalQuestions = $assessment->steps->sum(function ($step) {
            return $step->questions->count();
        });

        return view('admin.assessments.show', compact('assessment', 'totalQuestions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Assessment $assessment)
    {
        return view('admin.assessments.edit', compact('assessment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Assessment $assessment)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_steps' => 'required|integer|min:1|max:10',
            'is_active' => 'nullable|boolean'
        ]);

        // Set is_active
        $validated['is_active'] = $request->has('is_active') ? true : false;

        DB::beginTransaction();
        try {
            $oldTotalSteps = $assessment->total_steps;
            $newTotalSteps = $validated['total_steps'];

            // Update assessment
            $assessment->update($validated);

            // Handle perubahan jumlah steps
            if ($newTotalSteps > $oldTotalSteps) {
                // Tambah steps baru
                for ($i = $oldTotalSteps + 1; $i <= $newTotalSteps; $i++) {
                    $assessment->steps()->create([
                        'step_number' => $i,
                        'step_title' => "Step {$i}",
                        'step_description' => "Deskripsi untuk step {$i}"
                    ]);
                }
            } elseif ($newTotalSteps < $oldTotalSteps) {
                // Hapus steps yang berlebih (dari belakang)
                $stepsToDelete = $assessment->steps()
                    ->where('step_number', '>', $newTotalSteps)
                    ->get();

                foreach ($stepsToDelete as $step) {
                    // Hapus relasi step_questions terlebih dahulu
                    $step->questions()->detach();
                    $step->delete();
                }
            }

            DB::commit();

            return back()->with('success', 'Assessment berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui assessment: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Assessment $assessment)
    {
        DB::beginTransaction();
        try {
            // Hapus semua relasi
            foreach ($assessment->steps as $step) {
                $step->questions()->detach();
                $step->delete();
            }

            // Hapus user responses jika ada
            $assessment->userResponses()->delete();

            // Hapus assessment
            $assessment->delete();

            DB::commit();

            return redirect()
                ->route('admin.assessments.index')
                ->with('success', 'Assessment berhasil dihapus!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus assessment: ' . $e->getMessage());
        }
    }

    /**
     * Toggle status aktif/nonaktif assessment
     */
    public function toggleStatus(Assessment $assessment)
    {
        try {
            $assessment->update([
                'is_active' => !$assessment->is_active
            ]);

            $status = $assessment->is_active ? 'diaktifkan' : 'dinonaktifkan';

            return redirect()
                ->back()
                ->with('success', "Assessment berhasil {$status}!");

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    }

    /**
     * Duplicate assessment beserta steps dan questions
     */
    public function duplicate(Assessment $assessment)
    {
        DB::beginTransaction();
        try {
            // Duplikasi assessment
            $newAssessment = $assessment->replicate();
            $newAssessment->title = $assessment->title . ' (Copy)';
            $newAssessment->is_active = false;
            $newAssessment->save();

            // Duplikasi steps dan relasi questions
            foreach ($assessment->steps as $step) {
                $newStep = $step->replicate();
                $newStep->assessment_id = $newAssessment->id;
                $newStep->save();

                // Attach questions dengan order yang sama
                $questions = $step->questions()->get();
                foreach ($questions as $question) {
                    $newStep->questions()->attach($question->id, [
                        'order' => $question->pivot->order
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.assessments.edit', $newAssessment)
                ->with('success', 'Assessment berhasil diduplikasi!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Gagal menduplikasi assessment: ' . $e->getMessage());
        }
    }
}