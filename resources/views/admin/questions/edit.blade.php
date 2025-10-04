@extends('layouts.app')

@section('title', 'Edit Pertanyaan')

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
                                    <a href="{{ route('admin.questions.index') }}">Bank Soal</a>
                                </li>
                                <li class="breadcrumb-item active">Edit Pertanyaan</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">Edit Pertanyaan</h2>
                    <p class="text-muted mb-0">Update informasi pertanyaan</p>
                </div>
                <a href="{{ route('admin.questions.index') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-arrow-left me-1"></i> Kembali
                </a>
            </div>

            {{-- Error Alert --}}
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

            {{-- Form --}}
            <form action="{{ route('admin.questions.update', $question) }}" method="POST" id="questionForm">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body">
                                {{-- Question Text --}}
                                <div class="mb-4">
                                    <label for="question_text" class="form-label">
                                        Teks Pertanyaan <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control @error('question_text') is-invalid @enderror" 
                                              id="question_text" 
                                              name="question_text" 
                                              rows="4"
                                              required>{{ old('question_text', $question->question_text) }}</textarea>
                                    @error('question_text')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Question Type --}}
                                <div class="mb-4">
                                    <label for="question_type" class="form-label">
                                        Tipe Pertanyaan <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('question_type') is-invalid @enderror" 
                                            id="question_type" 
                                            name="question_type"
                                            required>
                                        @foreach($questionTypes as $value => $label)
                                            <option value="{{ $value }}" {{ old('question_type', $question->question_type) === $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('question_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-warning d-block mt-2">
                                        <i class="ti ti-alert-triangle me-1"></i>
                                        Mengubah tipe pertanyaan akan menghapus semua opsi yang ada
                                    </small>
                                </div>

                                {{-- Options (Multiple Choice) --}}
                                <div id="optionsContainer" style="display: {{ old('question_type', $question->question_type) === 'multiple_choice' ? 'block' : 'none' }};">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <label class="form-label mb-0">
                                            Opsi Jawaban <span class="text-danger">*</span>
                                        </label>
                                        <button type="button" class="btn btn-sm btn-primary" id="addOptionBtn">
                                            <i class="ti ti-plus me-1"></i> Tambah Opsi
                                        </button>
                                    </div>

                                    <div id="optionsList">
                                        {{-- Options will be loaded here --}}
                                    </div>

                                    <small class="text-muted">
                                        <i class="ti ti-info-circle me-1"></i>
                                        Minimal 2 opsi untuk pilihan ganda
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        {{-- Settings Card --}}
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="ti ti-settings me-2"></i>Pengaturan
                                </h5>
                            </div>
                            <div class="card-body">
                                {{-- Is Required --}}
                                <div class="mb-3">
                                    <label class="form-label">Status Pertanyaan</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="is_required" 
                                               name="is_required" 
                                               value="1"
                                               {{ old('is_required', $question->is_required) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_required">
                                            Wajib dijawab
                                        </label>
                                    </div>
                                </div>

                                {{-- Usage Info --}}
                                @if($question->assessmentSteps->count() > 0)
                                <div class="alert alert-info">
                                    <h6 class="alert-heading">
                                        <i class="ti ti-info-circle me-1"></i> Penggunaan
                                    </h6>
                                    <p class="mb-0 small">
                                        Pertanyaan ini digunakan di <strong>{{ $question->assessmentSteps->count() }} step assessment</strong>
                                    </p>
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="card">
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-check me-1"></i> Update Pertanyaan
                                    </button>
                                    <a href="{{ route('admin.questions.index') }}" class="btn btn-light">
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

@push('scripts')
<script>
    let optionCounter = 0;

    // Handle question type change
    document.getElementById('question_type').addEventListener('change', function() {
        const optionsContainer = document.getElementById('optionsContainer');
        
        if (this.value === 'multiple_choice') {
            optionsContainer.style.display = 'block';
            
            const optionsList = document.getElementById('optionsList');
            if (optionsList.children.length === 0) {
                addOption();
                addOption();
            }
        } else {
            optionsContainer.style.display = 'none';
        }
    });

    // Add option button
    document.getElementById('addOptionBtn').addEventListener('click', function() {
        addOption();
    });

    // Function to add option
    function addOption(optionText = '', optionValue = '') {
        optionCounter++;
        
        const optionHtml = `
            <div class="card mb-2 option-item" data-option-id="${optionCounter}">
                <div class="card-body p-3">
                    <div class="d-flex gap-2 align-items-start">
                        <div class="flex-grow-1">
                            <div class="row g-2">
                                <div class="col-md-8">
                                    <input type="text" 
                                           class="form-control form-control-sm" 
                                           name="options[${optionCounter}][option_text]" 
                                           placeholder="Teks opsi"
                                           value="${optionText}"
                                           required>
                                </div>
                                <div class="col-md-4">
                                    <input type="number" 
                                           class="form-control form-control-sm" 
                                           name="options[${optionCounter}][option_value]" 
                                           placeholder="Nilai"
                                           value="${optionValue}">
                                </div>
                            </div>
                        </div>
                        <button type="button" 
                                class="btn btn-sm btn-light-danger remove-option-btn"
                                onclick="removeOption(${optionCounter})">
                            <i class="ti ti-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.getElementById('optionsList').insertAdjacentHTML('beforeend', optionHtml);
    }

    // Function to remove option
    function removeOption(optionId) {
        const optionItem = document.querySelector(`[data-option-id="${optionId}"]`);
        const optionsList = document.getElementById('optionsList');
        
        if (optionsList.children.length <= 2) {
            alert('Minimal harus ada 2 opsi untuk pilihan ganda');
            return;
        }
        
        if (confirm('Hapus opsi ini?')) {
            optionItem.remove();
        }
    }

    // Form validation
    document.getElementById('questionForm').addEventListener('submit', function(e) {
        const questionType = document.getElementById('question_type').value;
        
        if (questionType === 'multiple_choice') {
            const optionsList = document.getElementById('optionsList');
            const filledOptions = Array.from(optionsList.querySelectorAll('input[name*="option_text"]'))
                .filter(input => input.value.trim() !== '');
            
            if (filledOptions.length < 2) {
                e.preventDefault();
                alert('Pilihan ganda harus memiliki minimal 2 opsi yang terisi');
                return false;
            }
        }
    });

    // Load existing options or old input
    @if(old('question_type', $question->question_type) === 'multiple_choice')
        @if(old('options'))
            // Load old input if validation fails
            @foreach(old('options', []) as $index => $option)
                addOption('{{ $option['option_text'] ?? '' }}', '{{ $option['option_value'] ?? '' }}');
            @endforeach
        @else
            // Load existing options from database
            @foreach($question->questionOptions as $option)
                addOption('{{ $option->option_text }}', '{{ $option->option_value }}');
            @endforeach
        @endif
    @endif
</script>
@endpush
@endsection