@extends('layouts.app')

@section('title', 'Kelola Steps - ' . $assessment->title)

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            {{-- Breadcrumb --}}
            <div class="page-header mb-4">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.assessments.index') }}">Assessment</a>
                                </li>
                                <li class="breadcrumb-item active">Kelola Steps</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">{{ $assessment->title }}</h2>
                    <p class="text-muted mb-0">Kelola steps dan pertanyaan untuk assessment ini</p>
                </div>
                <a href="{{ route('admin.assessments.index') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-arrow-left me-1"></i> Kembali
                </a>
            </div>

            {{-- Stepper Preview --}}
            <div class="card mb-4">
                <div class="card-body">
                    <div class="stepper">
                        @foreach($assessment->steps as $step)
                        <div class="step {{ $loop->first ? 'active' : '' }}">
                            <div class="step-number">{{ $step->step_number }}</div>
                            <div class="step-label">{{ $step->step_title }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Alert --}}
            <div id="alertContainer"></div>

            {{-- Steps Tabs --}}
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                        @foreach($assessment->steps as $step)
                        <li class="nav-item">
                            <a class="nav-link {{ $loop->first ? 'active' : '' }}" 
                               data-bs-toggle="tab" 
                               href="#step{{ $step->id }}" 
                               role="tab">
                                <i class="ti ti-number-{{ $step->step_number }} me-1"></i>
                                {{ $step->step_title }}
                                <span class="badge bg-light-primary ms-2">
                                    {{ $step->questions->count() }}
                                </span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>

                <div class="card-body">
                    <div class="tab-content">
                        @foreach($assessment->steps as $step)
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" 
                             id="step{{ $step->id }}" 
                             role="tabpanel">
                            
                            {{-- Step Info --}}
                            <div class="row mb-4">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Judul Step</label>
                                        <input type="text" 
                                               class="form-control step-title" 
                                               data-step-id="{{ $step->id }}"
                                               value="{{ $step->step_title }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Deskripsi Step</label>
                                        <textarea class="form-control step-description" 
                                                  data-step-id="{{ $step->id }}"
                                                  rows="2">{{ $step->step_description }}</textarea>
                                    </div>
                                    <button type="button" 
                                            class="btn btn-sm btn-primary update-step-btn" 
                                            data-step-id="{{ $step->id }}">
                                        <i class="ti ti-check me-1"></i> Update Step Info
                                    </button>
                                </div>
                                <div class="col-md-4">
                                    <div class="alert alert-info">
                                        <h6 class="alert-heading">
                                            <i class="ti ti-info-circle me-1"></i> Info Step
                                        </h6>
                                        <p class="mb-1"><strong>Step Number:</strong> {{ $step->step_number }}</p>
                                        <p class="mb-0"><strong>Total Pertanyaan:</strong> {{ $step->questions->count() }}</p>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            {{-- Question Management --}}
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="mb-0">Daftar Pertanyaan</h5>
                                        <button type="button" 
                                                class="btn btn-sm btn-success" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#addQuestionModal{{ $step->id }}">
                                            <i class="ti ti-plus me-1"></i> Tambah Pertanyaan
                                        </button>
                                    </div>

                                    {{-- Questions List --}}
                                    <div class="sortable-questions" 
                                         data-step-id="{{ $step->id }}"
                                         id="questionsList{{ $step->id }}">
                                        @forelse($step->questions as $question)
                                        <div class="card mb-2 question-item" 
                                             data-question-id="{{ $question->id }}">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-start">
                                                    <div class="drag-handle me-3" style="cursor: move;">
                                                        <i class="ti ti-grip-vertical fs-4 text-muted"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex justify-content-between align-items-start">
                                                            <div>
                                                                <span class="badge bg-light-secondary me-2">
                                                                    {{ $loop->iteration }}
                                                                </span>
                                                                <span class="fw-semibold">
                                                                    {{ $question->question_text }}
                                                                </span>
                                                            </div>
                                                            <button type="button" 
                                                                    class="btn btn-sm btn-light-danger remove-question-btn"
                                                                    data-step-id="{{ $step->id }}"
                                                                    data-question-id="{{ $question->id }}">
                                                                <i class="ti ti-trash"></i>
                                                            </button>
                                                        </div>
                                                        
                                                        <div class="mt-2">
                                                            <span class="badge {{ $question->question_type === 'multiple_choice' ? 'bg-light-primary' : 'bg-light-info' }}">
                                                                {{ $question->question_type === 'multiple_choice' ? 'Pilihan Ganda' : 'Input Teks' }}
                                                            </span>
                                                            
                                                            @if($question->isMultipleChoice() && $question->questionOptions->count() > 0)
                                                            <button class="btn btn-sm btn-link p-0 ms-2" 
                                                                    type="button" 
                                                                    data-bs-toggle="collapse" 
                                                                    data-bs-target="#options{{ $question->id }}">
                                                                <i class="ti ti-chevron-down"></i> Lihat Opsi
                                                            </button>
                                                            @endif
                                                        </div>

                                                        @if($question->isMultipleChoice() && $question->questionOptions->count() > 0)
                                                        <div class="collapse mt-2" id="options{{ $question->id }}">
                                                            <ul class="list-group list-group-flush">
                                                                @foreach($question->questionOptions as $option)
                                                                <li class="list-group-item px-0 py-1">
                                                                    <small>{{ chr(65 + $loop->index) }}. {{ $option->option_text }}</small>
                                                                </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @empty
                                        <div class="text-center py-5 empty-state">
                                            <i class="ti ti-clipboard-off fs-1 text-muted mb-3 d-block"></i>
                                            <p class="text-muted">Belum ada pertanyaan di step ini</p>
                                        </div>
                                        @endforelse
                                    </div>
                                </div>

                                {{-- Available Questions Sidebar --}}
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-header">
                                            <h6 class="mb-0">
                                                <i class="ti ti-database me-1"></i> Bank Soal Tersedia
                                            </h6>
                                        </div>
                                        <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                                            @if($availableQuestions->isEmpty())
                                            <p class="text-muted text-center mb-0">
                                                <i class="ti ti-info-circle me-1"></i>
                                                Semua pertanyaan sudah digunakan
                                            </p>
                                            @else
                                            <p class="text-muted small mb-2">
                                                {{ $availableQuestions->count() }} pertanyaan tersedia
                                            </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Modal Add Question --}}
                        <div class="modal fade" id="addQuestionModal{{ $step->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Tambah Pertanyaan ke {{ $step->step_title }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="list-group" id="availableQuestionsList{{ $step->id }}">
                                            @forelse($availableQuestions as $question)
                                            <div class="list-group-item list-group-item-action">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        <div class="fw-semibold mb-1">{{ $question->question_text }}</div>
                                                        <span class="badge {{ $question->question_type === 'multiple_choice' ? 'bg-light-primary' : 'bg-light-info' }}">
                                                            {{ $question->question_type === 'multiple_choice' ? 'Pilihan Ganda' : 'Input Teks' }}
                                                        </span>
                                                    </div>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-primary add-question-btn"
                                                            data-step-id="{{ $step->id }}"
                                                            data-question-id="{{ $question->id }}"
                                                            data-modal-id="addQuestionModal{{ $step->id }}">
                                                        <i class="ti ti-plus"></i> Tambah
                                                    </button>
                                                </div>
                                            </div>
                                            @empty
                                            <div class="text-center py-4">
                                                <i class="ti ti-inbox fs-1 text-muted mb-2 d-block"></i>
                                                <p class="text-muted">Tidak ada pertanyaan tersedia</p>
                                                <a href="{{ route('admin.questions.create') }}" class="btn btn-sm btn-primary">
                                                    <i class="ti ti-plus me-1"></i> Buat Pertanyaan Baru
                                                </a>
                                            </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.css">
<style>
    .question-item {
        transition: all 0.3s ease;
        border-left: 3px solid transparent;
    }
    .question-item:hover {
        border-left-color: var(--accent);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .sortable-ghost {
        opacity: 0.4;
        background: #f8f9fa;
    }
    .drag-handle:hover {
        color: var(--accent) !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    const assessmentId = {{ $assessment->id }};
    const csrfToken = '{{ csrf_token() }}';

    // Initialize Sortable for each step
    document.querySelectorAll('.sortable-questions').forEach(function(el) {
        new Sortable(el, {
            animation: 150,
            handle: '.drag-handle',
            ghostClass: 'sortable-ghost',
            onEnd: function(evt) {
                const stepId = el.dataset.stepId;
                const questionIds = Array.from(el.children)
                    .map(item => item.dataset.questionId)
                    .filter(id => id);
                
                reorderQuestions(stepId, questionIds);
            }
        });
    });

    // Update Step Info
    document.querySelectorAll('.update-step-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const stepId = this.dataset.stepId;
            const title = document.querySelector(`.step-title[data-step-id="${stepId}"]`).value;
            const description = document.querySelector(`.step-description[data-step-id="${stepId}"]`).value;
            
            updateStepInfo(stepId, title, description);
        });
    });

    // Add Question
    document.querySelectorAll('.add-question-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const stepId = this.dataset.stepId;
            const questionId = this.dataset.questionId;
            const modalId = this.dataset.modalId;
            
            attachQuestion(stepId, questionId, modalId);
        });
    });

    // Remove Question
    document.querySelectorAll('.remove-question-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (confirm('Yakin ingin menghapus pertanyaan ini dari step?')) {
                const stepId = this.dataset.stepId;
                const questionId = this.dataset.questionId;
                
                detachQuestion(stepId, questionId);
            }
        });
    });

    // Functions
    function updateStepInfo(stepId, title, description) {
        fetch(`/admin/assessments/${assessmentId}/steps/${stepId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                step_title: title,
                step_description: description
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                location.reload();
            } else {
                showAlert('danger', data.message);
            }
        })
        .catch(error => {
            showAlert('danger', 'Terjadi kesalahan: ' + error.message);
        });
    }

    function attachQuestion(stepId, questionId, modalId) {
        fetch(`/admin/assessments/${assessmentId}/steps/${stepId}/questions/attach`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                question_id: questionId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                bootstrap.Modal.getInstance(document.getElementById(modalId)).hide();
                location.reload();
            } else {
                showAlert('danger', data.message);
            }
        })
        .catch(error => {
            showAlert('danger', 'Terjadi kesalahan: ' + error.message);
        });
    }

    function detachQuestion(stepId, questionId) {
        fetch(`/admin/assessments/${assessmentId}/steps/${stepId}/questions/${questionId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                location.reload();
            } else {
                showAlert('danger', data.message);
            }
        })
        .catch(error => {
            showAlert('danger', 'Terjadi kesalahan: ' + error.message);
        });
    }

    function reorderQuestions(stepId, questionIds) {
        fetch(`/admin/assessments/${assessmentId}/steps/${stepId}/questions/reorder`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                question_ids: questionIds
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
            } else {
                showAlert('danger', data.message);
            }
        })
        .catch(error => {
            showAlert('danger', 'Terjadi kesalahan: ' + error.message);
        });
    }

    function showAlert(type, message) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                <i class="ti ti-${type === 'success' ? 'check' : 'alert-circle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        document.getElementById('alertContainer').innerHTML = alertHtml;
        
        setTimeout(() => {
            document.querySelector('.alert')?.remove();
        }, 5000);
    }
</script>
@endpush
@endsection