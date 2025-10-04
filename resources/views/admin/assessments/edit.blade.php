@extends('layouts.app')

@section('title', 'Edit Assessment')

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
                                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.assessments.index') }}">Assessment</a>
                                </li>
                                <li class="breadcrumb-item active">Edit Assessment</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">Edit Assessment</h2>
                    <p class="text-muted mb-0">Update informasi assessment dan kelola steps</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.assessments.steps.index', $assessment) }}" 
                       class="btn btn-info">
                        <i class="ti ti-stairs me-1"></i> Kelola Steps & Pertanyaan
                    </a>
                    <a href="{{ route('admin.assessments.index') }}" 
                       class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>

            {{-- Alert Messages --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="ti ti-check me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="ti ti-alert-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="ti ti-alert-circle me-2"></i>
                    <strong>Terdapat kesalahan:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Form Card --}}
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.assessments.update', $assessment) }}" method="POST" id="assessmentForm">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-lg-8">
                                {{-- Basic Info --}}
                                <h5 class="mb-3">
                                    <i class="ti ti-info-circle me-2"></i>Informasi Dasar
                                </h5>

                                {{-- Title --}}
                                <div class="mb-4">
                                    <label for="title" class="form-label">
                                        Judul Assessment <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('title') is-invalid @enderror" 
                                           id="title" 
                                           name="title" 
                                           value="{{ old('title', $assessment->title) }}"
                                           placeholder="Contoh: Assessment Kepribadian Karyawan"
                                           required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Description --}}
                                <div class="mb-4">
                                    <label for="description" class="form-label">
                                        Deskripsi
                                    </label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" 
                                              name="description" 
                                              rows="4"
                                              placeholder="Jelaskan tujuan dan cakupan assessment ini...">{{ old('description', $assessment->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <hr class="my-4">

                                {{-- Steps Management --}}
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">
                                        <i class="ti ti-stairs me-2"></i>Kelola Steps
                                    </h5>
                                </div>

                                {{-- Total Steps --}}
                                <div class="mb-3">
                                    <label for="total_steps" class="form-label">
                                        Jumlah Steps <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <button type="button" class="btn btn-outline-secondary" id="decreaseSteps">
                                            <i class="ti ti-minus"></i>
                                        </button>
                                        <input type="number" 
                                               class="form-control text-center @error('total_steps') is-invalid @enderror" 
                                               id="total_steps" 
                                               name="total_steps" 
                                               value="{{ old('total_steps', $assessment->total_steps) }}"
                                               min="1"
                                               max="10"
                                               readonly
                                               required>
                                        <button type="button" class="btn btn-outline-secondary" id="increaseSteps">
                                            <i class="ti ti-plus"></i>
                                        </button>
                                    </div>
                                    @error('total_steps')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        <i class="ti ti-info-circle"></i> 
                                        Saat ini: <strong>{{ $assessment->steps->count() }} steps</strong>
                                    </small>
                                </div>

                                {{-- Steps List --}}
                                <div class="card bg-light mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">Daftar Steps Saat Ini</h6>
                                    </div>
                                    <div class="card-body">
                                        <div id="stepsList">
                                            @foreach($assessment->steps->sortBy('step_number') as $step)
                                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-white rounded">
                                                <div>
                                                    <span class="badge bg-primary me-2">{{ $step->step_number }}</span>
                                                    <strong>{{ $step->step_title }}</strong>
                                                    <span class="badge bg-light-info ms-2">
                                                        {{ $step->questions->count() }} pertanyaan
                                                    </span>
                                                </div>
                                                @if($step->questions->count() > 0)
                                                <i class="ti ti-lock text-muted" 
                                                   data-bs-toggle="tooltip" 
                                                   title="Step ini memiliki pertanyaan"></i>
                                                @endif
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                {{-- Warning Box --}}
                                <div class="alert alert-warning d-flex align-items-start" role="alert" id="stepWarning" style="display: none !important;">
                                    <i class="ti ti-alert-triangle fs-4 me-2"></i>
                                    <div>
                                        <strong>Peringatan!</strong>
                                        <p class="mb-0 mt-1" id="stepWarningText"></p>
                                    </div>
                                </div>

                                {{-- Status --}}
                                <hr class="my-4">
                                <div class="mb-4">
                                    <label class="form-label">Status Assessment</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="is_active" 
                                               name="is_active" 
                                               value="1"
                                               {{ old('is_active', $assessment->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Assessment aktif dan dapat diakses user
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                {{-- Statistics Card --}}
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">
                                            <i class="ti ti-chart-bar me-2"></i>Statistik
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="text-muted">Total Steps</span>
                                                <strong>{{ $assessment->steps->count() }}</strong>
                                            </div>
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="text-muted">Total Pertanyaan</span>
                                                <strong>{{ $assessment->total_questions }}</strong>
                                            </div>
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="text-muted">Status</span>
                                                <span class="badge {{ $assessment->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $assessment->is_active ? 'Aktif' : 'Nonaktif' }}
                                                </span>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="small text-muted">
                                            <i class="ti ti-clock me-1"></i>
                                            Dibuat: {{ $assessment->created_at->format('d M Y') }}
                                        </div>
                                        <div class="small text-muted">
                                            <i class="ti ti-pencil me-1"></i>
                                            Update: {{ $assessment->updated_at->format('d M Y') }}
                                        </div>
                                    </div>
                                </div>

                                {{-- Preview Card --}}
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="mb-0">
                                            <i class="ti ti-eye me-2"></i>Preview Steps
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="stepsPreview" class="stepper">
                                            @foreach($assessment->steps->sortBy('step_number') as $step)
                                            <div class="step {{ $loop->first ? 'active' : '' }}">
                                                <div class="step-number">{{ $step->step_number }}</div>
                                                <div class="step-label">Step {{ $step->step_number }}</div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="card mt-3">
                                    <div class="card-body">
                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                                <i class="ti ti-check me-1"></i> Update Assessment
                                            </button>
                                            <a href="{{ route('admin.assessments.index') }}" class="btn btn-light">
                                                <i class="ti ti-x me-1"></i> Batal
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const currentSteps = {{ $assessment->steps->count() }};
    const stepsWithQuestions = @json($assessment->steps->map(function($step) {
        return [
            'number' => $step->step_number,
            'title' => $step->step_title,
            'questions_count' => $step->questions->count()
        ];
    })->values());

    let totalStepsInput = document.getElementById('total_steps');
    let stepWarning = document.getElementById('stepWarning');
    let stepWarningText = document.getElementById('stepWarningText');
    let submitBtn = document.getElementById('submitBtn');

    // Increase steps
    document.getElementById('increaseSteps').addEventListener('click', function() {
        let current = parseInt(totalStepsInput.value);
        if (current < 10) {
            totalStepsInput.value = current + 1;
            updatePreview();
            updateWarning();
        }
    });

    // Decrease steps
    document.getElementById('decreaseSteps').addEventListener('click', function() {
        let current = parseInt(totalStepsInput.value);
        if (current > 1) {
            totalStepsInput.value = current - 1;
            updatePreview();
            updateWarning();
        }
    });

    // Update preview stepper
    function updatePreview() {
        const total = parseInt(totalStepsInput.value);
        const preview = document.getElementById('stepsPreview');
        
        let html = '';
        for (let i = 1; i <= total; i++) {
            const activeClass = i === 1 ? 'active' : '';
            html += `
                <div class="step ${activeClass}">
                    <div class="step-number">${i}</div>
                    <div class="step-label">Step ${i}</div>
                </div>
            `;
        }
        
        preview.innerHTML = html;
    }

    // Update warning message
    function updateWarning() {
        const newTotal = parseInt(totalStepsInput.value);
        
        stepWarning.style.display = 'none';
        
        if (newTotal > currentSteps) {
            // Adding steps
            const addCount = newTotal - currentSteps;
            stepWarningText.innerHTML = `Anda akan menambahkan <strong>${addCount} step baru</strong>. Step baru akan dibuat dengan nama default "Step X".`;
            stepWarning.style.display = 'block';
        } else if (newTotal < currentSteps) {
            // Removing steps
            const removeCount = currentSteps - newTotal;
            const stepsToRemove = stepsWithQuestions.filter(s => s.number > newTotal);
            const hasQuestions = stepsToRemove.some(s => s.questions_count > 0);
            
            if (hasQuestions) {
                const stepsWithQ = stepsToRemove.filter(s => s.questions_count > 0);
                const stepNumbers = stepsWithQ.map(s => `Step ${s.number} (${s.questions_count} pertanyaan)`).join(', ');
                
                stepWarningText.innerHTML = `
                    <strong>PERHATIAN!</strong> Anda akan menghapus <strong>${removeCount} step</strong>: ${stepNumbers}. 
                    <br>Semua pertanyaan di step tersebut akan dihapus dari assessment ini.
                `;
                stepWarning.classList.remove('alert-warning');
                stepWarning.classList.add('alert-danger');
            } else {
                stepWarningText.innerHTML = `Anda akan menghapus <strong>${removeCount} step terakhir</strong>. Step tersebut tidak memiliki pertanyaan.`;
                stepWarning.classList.remove('alert-danger');
                stepWarning.classList.add('alert-warning');
            }
            
            stepWarning.style.display = 'block';
        }
    }

    // Form submission confirmation
    document.getElementById('assessmentForm').addEventListener('submit', function(e) {
        const newTotal = parseInt(totalStepsInput.value);
        
        if (newTotal < currentSteps) {
            const removeCount = currentSteps - newTotal;
            const stepsToRemove = stepsWithQuestions.filter(s => s.number > newTotal);
            const hasQuestions = stepsToRemove.some(s => s.questions_count > 0);
            
            if (hasQuestions) {
                const confirmed = confirm(
                    `PERINGATAN!\n\n` +
                    `Anda akan menghapus ${removeCount} step yang memiliki pertanyaan.\n` +
                    `Semua pertanyaan di step tersebut akan dihapus dari assessment.\n\n` +
                    `Lanjutkan?`
                );
                
                if (!confirmed) {
                    e.preventDefault();
                    return false;
                }
            }
        }
    });

    // Initialize
    updateWarning();

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
@endpush
@endsection