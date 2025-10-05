@extends('layouts.app')

@section('title', 'Detail Pertanyaan')

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
                                <li class="breadcrumb-item active">Detail Pertanyaan</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">Detail Pertanyaan</h2>
                    <p class="text-muted mb-0">Informasi lengkap tentang pertanyaan ini</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.questions.edit', $question) }}" 
                       class="btn btn-primary">
                        <i class="ti ti-edit me-1"></i> Edit
                    </a>
                    <form action="{{ route('admin.questions.duplicate', $question) }}" 
                          method="POST" 
                          class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-warning">
                            <i class="ti ti-copy me-1"></i> Duplikat
                        </button>
                    </form>
                    <a href="{{ route('admin.questions.index') }}" 
                       class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>

            <div class="row">
                {{-- Main Content --}}
                <div class="col-lg-8">
                    {{-- Question Info Card --}}
                    <div class="card mb-4">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="ti ti-help me-2"></i>Pertanyaan
                                </h5>
                                <div>
                                    <span class="badge {{ $question->question_type === 'multiple_choice' ? 'bg-primary' : 'bg-info' }}">
                                        {{ $question->question_type === 'multiple_choice' ? 'Pilihan Ganda' : 'Input Teks' }}
                                    </span>
                                    @if($question->is_required)
                                        <span class="badge bg-danger ms-2">
                                            <i class="ti ti-asterisk"></i> Wajib
                                        </span>
                                    @else
                                        <span class="badge bg-secondary ms-2">Opsional</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="question-text p-4 bg-light rounded mb-3">
                                <h4 class="mb-0">{{ $question->question_text }}</h4>
                            </div>

                            {{-- Options (Multiple Choice) --}}
                            @if($question->isMultipleChoice() && $question->questionOptions->count() > 0)
                                <h6 class="mb-3">Opsi Jawaban:</h6>
                                <div class="list-group">
                                    @foreach($question->questionOptions as $option)
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="badge bg-light-secondary me-2">
                                                    {{ chr(65 + $loop->index) }}
                                                </span>
                                                <span class="fw-semibold">{{ $option->option_text }}</span>
                                            </div>
                                            @if($option->option_value !== null)
                                                <span class="badge bg-light-primary">
                                                    <i class="ti ti-star me-1"></i>Nilai: {{ $option->option_value }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Text Input Info --}}
                            @if($question->isTextInput())
                                <div class="alert alert-info d-flex align-items-center" role="alert">
                                    <i class="ti ti-keyboard fs-4 me-3"></i>
                                    <div>
                                        <strong>Tipe Input Teks</strong>
                                        <p class="mb-0">User akan mengisi jawaban dalam bentuk teks bebas</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Usage in Assessments --}}
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="ti ti-list-check me-2"></i>Penggunaan dalam Assessment
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($question->assessmentSteps->count() > 0)
                                <p class="text-muted mb-3">
                                    Pertanyaan ini digunakan di <strong>{{ $question->assessmentSteps->count() }} step</strong> 
                                    dari assessment berikut:
                                </p>

                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Assessment</th>
                                                <th>Step</th>
                                                <th class="text-center">Urutan</th>
                                                <th class="text-center">Status</th>
                                                <th class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($question->assessmentSteps as $step)
                                            <tr>
                                                <td>
                                                    <div class="fw-semibold">{{ $step->assessment->title }}</div>
                                                    <small class="text-muted">{{ Str::limit($step->assessment->description, 50) }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light-secondary">
                                                        Step {{ $step->step_number }}
                                                    </span>
                                                    <div class="small text-muted">{{ $step->step_title }}</div>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-light-info">
                                                        Urutan: {{ $step->pivot->order }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    @if($step->assessment->is_active)
                                                        <span class="badge bg-light-success">
                                                            <i class="ti ti-check"></i> Aktif
                                                        </span>
                                                    @else
                                                        <span class="badge bg-light-secondary">Nonaktif</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('admin.assessments.steps.index', $step->assessment) }}" 
                                                       class="btn btn-sm btn-light-primary"
                                                       data-bs-toggle="tooltip"
                                                       title="Lihat Step Management">
                                                        <i class="ti ti-external-link"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="ti ti-inbox fs-1 text-muted mb-3 d-block"></i>
                                    <p class="text-muted mb-0">
                                        Pertanyaan ini belum digunakan dalam assessment manapun
                                    </p>
                                    <a href="{{ route('admin.assessments.index') }}" class="btn btn-sm btn-primary mt-3">
                                        <i class="ti ti-plus me-1"></i> Tambahkan ke Assessment
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="col-lg-4">
                    {{-- Info Card --}}
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="ti ti-info-circle me-2"></i>Informasi
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <small class="text-muted d-block mb-1">ID Pertanyaan</small>
                                <strong>#{{ $question->id }}</strong>
                            </div>
                            
                            <div class="mb-3">
                                <small class="text-muted d-block mb-1">Tipe Pertanyaan</small>
                                <span class="badge {{ $question->question_type === 'multiple_choice' ? 'bg-primary' : 'bg-info' }}">
                                    {{ $question->question_type === 'multiple_choice' ? 'Pilihan Ganda' : 'Input Teks' }}
                                </span>
                            </div>

                            @if($question->isMultipleChoice())
                            <div class="mb-3">
                                <small class="text-muted d-block mb-1">Jumlah Opsi</small>
                                <strong>{{ $question->questionOptions->count() }} opsi</strong>
                            </div>
                            @endif

                            <div class="mb-3">
                                <small class="text-muted d-block mb-1">Status</small>
                                @if($question->is_required)
                                    <span class="badge bg-danger">
                                        <i class="ti ti-asterisk me-1"></i>Wajib dijawab
                                    </span>
                                @else
                                    <span class="badge bg-secondary">Opsional</span>
                                @endif
                            </div>

                            <hr>

                            <div class="mb-3">
                                <small class="text-muted d-block mb-1">Digunakan di</small>
                                <strong>{{ $question->assessmentSteps->count() }} assessment</strong>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted d-block mb-1">
                                    <i class="ti ti-calendar"></i> Dibuat
                                </small>
                                <strong>{{ $question->created_at->format('d M Y H:i') }}</strong>
                            </div>

                            <div class="mb-0">
                                <small class="text-muted d-block mb-1">
                                    <i class="ti ti-pencil"></i> Terakhir Update
                                </small>
                                <strong>{{ $question->updated_at->format('d M Y H:i') }}</strong>
                            </div>
                        </div>
                    </div>

                    {{-- Preview Card --}}
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="ti ti-eye me-2"></i>Preview User
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-3">Preview tampilan untuk user:</p>
                            
                            <div class="border rounded p-3 bg-light">
                                <label class="form-label fw-semibold">
                                    {{ $question->question_text }}
                                    @if($question->is_required)
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>

                                @if($question->isMultipleChoice())
                                    @foreach($question->questionOptions as $option)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" 
                                               type="radio" 
                                               name="preview_option" 
                                               id="preview{{ $option->id }}"
                                               disabled>
                                        <label class="form-check-label" for="preview{{ $option->id }}">
                                            {{ $option->option_text }}
                                        </label>
                                    </div>
                                    @endforeach
                                @else
                                    <textarea class="form-control" 
                                              rows="3" 
                                              placeholder="Ketik jawaban Anda di sini..."
                                              disabled></textarea>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Actions Card --}}
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="ti ti-settings me-2"></i>Aksi
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('admin.questions.edit', $question) }}" 
                                   class="btn btn-primary">
                                    <i class="ti ti-edit me-1"></i> Edit Pertanyaan
                                </a>
                                
                                <form action="{{ route('admin.questions.duplicate', $question) }}" 
                                      method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-warning w-100">
                                        <i class="ti ti-copy me-1"></i> Duplikat
                                    </button>
                                </form>

                                <hr class="my-2">

                                <form action="{{ route('admin.questions.destroy', $question) }}" 
                                      method="POST"
                                      onsubmit="return confirm('Yakin ingin menghapus pertanyaan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-danger w-100"
                                            {{ $question->assessmentSteps->count() > 0 ? 'disabled' : '' }}>
                                        <i class="ti ti-trash me-1"></i> Hapus Pertanyaan
                                    </button>
                                </form>

                                @if($question->assessmentSteps->count() > 0)
                                <small class="text-danger d-block text-center mt-2">
                                    <i class="ti ti-alert-circle"></i>
                                    Tidak dapat dihapus karena sedang digunakan
                                </small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
@endpush
@endsection