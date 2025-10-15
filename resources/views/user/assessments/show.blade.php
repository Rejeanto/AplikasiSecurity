@extends('layouts.app')

@section('title', $assessment->title)

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            {{-- Header --}}
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h3 class="mb-0">{{ $assessment->title }}</h3>
                    <a href="{{ route('user.assessments.index') }}" class="btn btn-light btn-sm">
                        <i class="ti ti-x me-1"></i> Keluar
                    </a>
                </div>
                <p class="text-muted mb-0">{{ $assessment->description }}</p>
            </div>

            {{-- Progress Bar --}}
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Progress Keseluruhan</span>
                        <span class="fw-semibold">{{ $progress }}%</span>
                    </div>
                    <div class="progress" style="height: 12px;">
                        <div class="progress-bar" 
                             role="progressbar" 
                             style="width: {{ $progress }}%; background-color: var(--accent);">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stepper --}}
            <div class="card mb-4">
                <div class="card-body">
                    <div class="stepper">
                        @foreach($assessment->steps as $s)
                        <div class="step {{ $s->id === $step->id ? 'active' : '' }} {{ $s->step_number < $step->step_number ? 'completed' : '' }}">
                            <div class="step-number">{{ $s->step_number }}</div>
                            <div class="step-label">{{ $s->step_title }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Questions Form --}}
            <form id="assessmentForm">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1">{{ $step->step_title }}</h5>
                                @if($step->step_description)
                                <p class="text-muted mb-0 small">{{ $step->step_description }}</p>
                                @endif
                            </div>
                            <span class="badge bg-light-secondary">
                                {{ $step->questions->count() }} Pertanyaan
                            </span>
                        </div>
                    </div>

                    <div class="card-body">
                        @forelse($step->questions as $question)
                        <div class="question-item mb-4 pb-4 {{ !$loop->last ? 'border-bottom' : '' }}">
                            {{-- Question Number & Text --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold fs-6">
                                    <span class="badge bg-primary me-2">{{ $loop->iteration }}</span>
                                    {{ $question->question_text }}
                                    @if($question->is_required)
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>
                            </div>

                            @if($question->show_media)
                                @if($question->media_type == 'image')
                                    <div class="mb-3">
                                        <img src="{{ $question->media_url }}" alt="Media" class="img-fluid" style="max-width: 100%; height: auto;">
                                    </div>
                                @elseif($question->media_type == 'video')
                                    <div class="mb-3" style="aspect-ratio: 16 / 9; width: 100%;">
                                        <iframe 
                                            src="{{ $question->media_url }}" 
                                            style="width: 100%; height: 100%; border: 0; border-radius: 8px;"
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                            allowfullscreen>
                                        </iframe>
                                    </div>
                                @endif
                            @endif


                            {{-- Multiple Choice --}}
                            @if($question->isMultipleChoice())
                                <div class="options-list">
                                    @foreach($question->questionOptions as $option)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" 
                                               type="radio" 
                                               name="answers[{{ $question->id }}]" 
                                               id="option{{ $option->id }}"
                                               value="{{ $option->id }}"
                                               {{ isset($userResponses[$question->id]) && $userResponses[$question->id]->selected_option_id == $option->id ? 'checked' : '' }}
                                               {{ $question->is_required ? 'required' : '' }}>
                                        <label class="form-check-label" for="option{{ $option->id }}">
                                            <span class="badge bg-light-secondary me-2">{{ chr(65 + $loop->index) }}</span>
                                            {{ $option->option_text }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Text Input --}}
                            @if($question->isTextInput())
                                <textarea class="form-control" 
                                          name="answers[{{ $question->id }}]" 
                                          rows="4"
                                          placeholder="Ketik jawaban Anda di sini..."
                                          {{ $question->is_required ? 'required' : '' }}>{{ $userResponses[$question->id]->answer_text ?? '' }}</textarea>
                            @endif

                            {{-- Required Info --}}
                            @if($question->is_required)
                            <small class="text-danger d-block mt-2">
                                <i class="ti ti-alert-circle"></i> Pertanyaan ini wajib dijawab
                            </small>
                            @endif
                        </div>
                        @empty
                        <div class="text-center py-5">
                            <i class="ti ti-inbox fs-1 text-muted mb-3 d-block"></i>
                            <p class="text-muted mb-0">Tidak ada pertanyaan di step ini</p>
                        </div>
                        @endforelse
                    </div>

                    {{-- Navigation Footer --}}
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                @if(!$step->isFirstStep())
                                <a href="{{ route('user.assessments.show', ['assessment' => $assessment, 'step' => $step->step_number - 1]) }}" 
                                   class="btn btn-light">
                                    <i class="ti ti-arrow-left me-1"></i> Sebelumnya
                                </a>
                                @endif
                            </div>

                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-secondary" id="saveDraftBtn">
                                    <i class="ti ti-device-floppy me-1"></i> Simpan Draft
                                </button>

                                @if($step->isLastStep())
                                <button type="button" class="btn btn-success" id="submitBtn">
                                    <i class="ti ti-check me-1"></i> Submit Assessment
                                </button>
                                @else
                                <button type="button" class="btn btn-primary" id="nextBtn">
                                    Selanjutnya <i class="ti ti-arrow-right ms-1"></i>
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            {{-- Alert Container --}}
            <div id="alertContainer" class="mt-3"></div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .question-item {
        transition: all 0.3s ease;
    }
    
    .form-check {
        padding: 12px;
        border-radius: 6px;
        transition: all 0.2s ease;
    }
    
    .form-check:hover {
        background-color: #f8f9fa;
    }
    
    .form-check-input:checked + .form-check-label {
        font-weight: 600;
    }
    
    .form-control:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 0.2rem rgba(242, 139, 107, 0.25);
    }
</style>
@endpush

@push('scripts')
<script>
    const assessmentId = {{ $assessment->id }};
    const stepId = {{ $step->id }};
    const isLastStep = {{ $step->isLastStep() ? 'true' : 'false' }};
    const nextStepNumber = {{ $step->step_number + 1 }};

    // Save Draft
    document.getElementById('saveDraftBtn').addEventListener('click', function() {
        saveAnswers(false);
    });

    // Next Button
    @if(!$step->isLastStep())
    document.getElementById('nextBtn').addEventListener('click', function() {
        if (validateForm()) {
            saveAnswers(true);
        }
    });
    @endif

    // Submit Button
    @if($step->isLastStep())
    document.getElementById('submitBtn').addEventListener('click', function() {
        if (validateForm()) {
            if (confirm('Yakin ingin submit assessment? Anda tidak dapat mengubah jawaban setelah submit.')) {
                saveAnswers(true, true);
            }
        }
    });
    @endif

    // Validate Form
    function validateForm() {
        const form = document.getElementById('assessmentForm');
        
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            showAlert('danger', 'Mohon jawab semua pertanyaan yang wajib!');
            
            // Scroll to first invalid element
            const firstInvalid = form.querySelector(':invalid');
            if (firstInvalid) {
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            
            return false;
        }
        
        return true;
    }

    // Save Answers
    function saveAnswers(moveNext = false, submitFinal = false) {
        const formData = new FormData(document.getElementById('assessmentForm'));
        const answers = {};
        
        for (let [key, value] of formData.entries()) {
            if (key.startsWith('answers[')) {
                const questionId = key.match(/\d+/)[0];
                answers[questionId] = value;
            }
        }

        // Show loading
        const btn = submitFinal ? document.getElementById('submitBtn') : 
                    moveNext ? document.getElementById('nextBtn') : 
                    document.getElementById('saveDraftBtn');
        
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="ti ti-loader-2 ti-spin me-1"></i> Menyimpan...';

        fetch(`/assessments/${assessmentId}/steps/${stepId}/save`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ answers: answers })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (submitFinal) {
                    // Submit final
                    window.location.href = `/assessments/${assessmentId}/submit`;
                } else if (moveNext) {
                    // Move to next step
                    window.location.href = `/assessments/${assessmentId}?step=${nextStepNumber}`;
                } else {
                    // Just save draft
                    showAlert('success', data.message);
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            } else {
                showAlert('danger', data.message);
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        })
        .catch(error => {
            showAlert('danger', 'Terjadi kesalahan: ' + error.message);
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }

    // Show Alert
    function showAlert(type, message) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                <i class="ti ti-${type === 'success' ? 'check' : 'alert-circle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        document.getElementById('alertContainer').innerHTML = alertHtml;
        
        // Scroll to alert
        document.getElementById('alertContainer').scrollIntoView({ behavior: 'smooth' });
        
        // Auto dismiss after 5 seconds
        setTimeout(() => {
            const alert = document.querySelector('#alertContainer .alert');
            if (alert) {
                alert.remove();
            }
        }, 5000);
    }

    // Auto-save every 30 seconds
    let autoSaveInterval = setInterval(function() {
        const form = document.getElementById('assessmentForm');
        if (form && !form.classList.contains('was-validated')) {
            // Silent save without validation
            const formData = new FormData(form);
            const answers = {};
            
            for (let [key, value] of formData.entries()) {
                if (key.startsWith('answers[') && value) {
                    const questionId = key.match(/\d+/)[0];
                    answers[questionId] = value;
                }
            }

            if (Object.keys(answers).length > 0) {
                fetch(`/assessments/${assessmentId}/steps/${stepId}/save`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ answers: answers })
                })
                .then(response => response.json())
                .then(data => {
                    // Show subtle indicator
                    if (data.success) {
                        console.log('Auto-saved at ' + new Date().toLocaleTimeString());
                    }
                });
            }
        }
    }, 30000); // 30 seconds

    // Clear interval on page unload
    window.addEventListener('beforeunload', function() {
        clearInterval(autoSaveInterval);
    });

    // Confirm before leaving if there are unsaved changes
    let formChanged = false;
    const formInputs = document.querySelectorAll('#assessmentForm input, #assessmentForm textarea');
    
    formInputs.forEach(input => {
        input.addEventListener('change', function() {
            formChanged = true;
        });
    });

    window.addEventListener('beforeunload', function(e) {
        if (formChanged) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

    // Reset formChanged when saving
    document.getElementById('saveDraftBtn').addEventListener('click', function() {
        formChanged = false;
    });
    
    @if(!$step->isLastStep())
    document.getElementById('nextBtn').addEventListener('click', function() {
        formChanged = false;
    });
    @endif

    @if($step->isLastStep())
    document.getElementById('submitBtn').addEventListener('click', function() {
        formChanged = false;
    });
    @endif
</script>
@endpush
@endsection