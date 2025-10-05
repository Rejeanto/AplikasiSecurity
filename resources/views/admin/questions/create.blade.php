@extends('layouts.app')

@section('title', 'Tambah Pertanyaan')

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
                                <li class="breadcrumb-item active">Tambah Pertanyaan</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">Tambah Pertanyaan Baru</h2>
                    <p class="text-muted mb-0">Buat pertanyaan untuk bank soal</p>
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
            <form action="{{ route('admin.questions.store') }}" method="POST" id="questionForm">
                @csrf

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
                                              placeholder="Tulis pertanyaan Anda di sini..."
                                              required>{{ old('question_text') }}</textarea>
                                    @error('question_text')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- <div class="mb-4">
                                    <label for="question_text" class="form-label">
                                        Teks Jawaban <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control @error('question_text') is-invalid @enderror" 
                                              id="question_text" 
                                              name="question_text" 
                                              rows="4"
                                              placeholder="Tulis jawaban Anda di sini..."
                                              required>{{ old('question_text') }}</textarea>
                                    @error('question_text')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div> --}}

                                <div class="mb-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="show_media" 
                                               name="show_media" 
                                               value="0"
                                               {{ old('show_media', false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="show_media">
                                            Tampilkan Media
                                        </label>
                                    </div>
                                </div>

                                {{-- media --}}

                                <div id="media_form" style="display: none;">
                                    <div class="mb-4">
                                        <label for="media_url" class="form-label">
                                            Tautan Media <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control @error('media_url') is-invalid @enderror" 
                                                  id="media_url" 
                                                  name="media_url" 
                                                  rows="1"
                                                  placeholder="Tautkan Link Disini"
                                                  >{{ old('media_url') }}</textarea>
                                        @error('media_url')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
    
                                    <div class="mb-4">
                                        <label for="media_type" class="form-label">
                                            Tipe Media <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select @error('media_type') is-invalid @enderror" 
                                                id="media_type" 
                                                name="media_type"
                                                >
                                            <option value="">Pilih Tipe</option>
                                            <option value="video" {{ old('media_type') === 'video' ? 'selected' : '' }}>
                                                Video
                                            </option>
                                            <option value="image" {{ old('media_type') === 'image' ? 'selected' : '' }}>
                                                Gambar
                                            </option>
                                        </select>
                                        @error('media_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
    
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
                                        <option value="">Pilih Tipe</option>
                                        @foreach($questionTypes as $value => $label)
                                            <option value="{{ $value }}" {{ old('question_type') === $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('question_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>


                                {{-- Options (Multiple Choice) --}}
                                <div id="optionsContainer" style="display: none;">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <label class="form-label mb-0">
                                            Opsi Jawaban <span class="text-danger">*</span>
                                        </label>
                                        <button type="button" class="btn btn-sm btn-primary" id="addOptionBtn">
                                            <i class="ti ti-plus me-1"></i> Tambah Opsi
                                        </button>
                                    </div>

                                    <div id="optionsList">
                                        {{-- Options will be added here dynamically --}}
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
                                               {{ old('is_required', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_required">
                                            Wajib dijawab
                                        </label>
                                    </div>
                                    <small class="text-muted d-block mt-1">
                                        User harus menjawab pertanyaan ini
                                    </small>
                                </div>
                            </div>
                        </div>

                        {{-- Info Card --}}
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="ti ti-info-circle me-2"></i>Informasi
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info mb-0">
                                    <h6 class="alert-heading mb-2">
                                        <i class="ti ti-bulb me-1"></i> Tips
                                    </h6>
                                    <ul class="mb-0 ps-3">
                                        <li class="mb-1">Buat pertanyaan yang jelas dan mudah dipahami</li>
                                        <li class="mb-1">Untuk pilihan ganda, minimal 2 opsi</li>
                                        <li class="mb-1">Gunakan option value untuk scoring (opsional)</li>
                                        <li>Pertanyaan dapat digunakan di multiple assessment</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="card">
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-check me-1"></i> Simpan Pertanyaan
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
            
            // Add 2 default options if empty
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

    document.getElementById('show_media').addEventListener('change', function() {
        if (this.checked) {
            document.getElementById('media_form').style.display = 'block';
        } else {
            document.getElementById('media_form').style.display = 'none';
            document.getElementById('media_url').value = '';
            document.getElementById('media_type').value = '';
        }
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
                                           placeholder="Teks opsi (contoh: Sangat Setuju)"
                                           value="${optionText}"
                                           >
                                </div>
                                <div class="col-md-4">
                                    <input type="number" 
                                           class="form-control form-control-sm" 
                                           name="options[${optionCounter}][option_value]" 
                                           placeholder="Nilai/Score"
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
        
        // Prevent removing if only 2 options left
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

    // Load old options if validation fails
    @if(old('question_type') === 'multiple_choice' && old('options'))
        document.getElementById('question_type').value = 'multiple_choice';
        document.getElementById('optionsContainer').style.display = 'block';
        
        @foreach(old('options', []) as $index => $option)
            addOption('{{ $option['option_text'] ?? '' }}', '{{ $option['option_value'] ?? '' }}');
        @endforeach
    @endif
</script>
@endpush
@endsection