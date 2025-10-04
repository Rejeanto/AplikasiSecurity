@extends('layouts.app')

@section('title', 'Bank Soal')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">Bank Soal</h2>
                    <p class="text-muted mb-0">Kelola semua pertanyaan untuk assessment</p>
                </div>
                <a href="{{ route('admin.questions.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus me-1"></i> Tambah Pertanyaan
                </a>
            </div>

            {{-- Alert Messages --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="ti ti-check me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="ti ti-alert-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Filter & Search --}}
            <div class="card mb-4">
                <div class="card-body">
                    <form action="{{ route('admin.questions.index') }}" method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Cari Pertanyaan</label>
                            <input type="text" 
                                   name="search" 
                                   class="form-control" 
                                   placeholder="Ketik kata kunci..."
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tipe Pertanyaan</label>
                            <select name="type" class="form-select">
                                <option value="">Semua Tipe</option>
                                <option value="multiple_choice" {{ request('type') === 'multiple_choice' ? 'selected' : '' }}>
                                    Pilihan Ganda
                                </option>
                                <option value="text_input" {{ request('type') === 'text_input' ? 'selected' : '' }}>
                                    Input Teks
                                </option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-search me-1"></i> Filter
                                </button>
                                <a href="{{ route('admin.questions.index') }}" class="btn btn-light">
                                    <i class="ti ti-refresh me-1"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Questions Table --}}
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" class="form-check-input" id="selectAll">
                                    </th>
                                    <th width="50">#</th>
                                    <th>Pertanyaan</th>
                                    <th class="text-center" width="120">Tipe</th>
                                    <th class="text-center" width="100">Opsi</th>
                                    <th class="text-center" width="120">Digunakan Di</th>
                                    <th class="text-center" width="80">Required</th>
                                    <th class="text-center" width="180">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($questions as $question)
                                    <tr>
                                        <td>
                                            <input type="checkbox" 
                                                   class="form-check-input question-checkbox" 
                                                   value="{{ $question->id }}">
                                        </td>
                                        <td>{{ $loop->iteration + $questions->firstItem() - 1 }}</td>
                                        <td>
                                            <div class="fw-semibold">{{ Str::limit($question->question_text, 80) }}</div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $question->question_type === 'multiple_choice' ? 'bg-light-primary' : 'bg-light-info' }}">
                                                {{ $question->question_type === 'multiple_choice' ? 'Pilihan Ganda' : 'Input Teks' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if($question->isMultipleChoice())
                                                <span class="badge bg-light-secondary">
                                                    {{ $question->questionOptions->count() }} opsi
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($question->assessment_steps_count > 0)
                                                <span class="badge bg-light-success">
                                                    {{ $question->assessment_steps_count }} assessment
                                                </span>
                                            @else
                                                <span class="badge bg-light-secondary">Belum digunakan</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($question->is_required)
                                                <i class="ti ti-check text-success fs-5"></i>
                                            @else
                                                <i class="ti ti-x text-muted fs-5"></i>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-1">
                                                <a href="{{ route('admin.questions.show', $question) }}" 
                                                   class="btn btn-sm btn-light-info"
                                                   data-bs-toggle="tooltip" 
                                                   title="Lihat Detail">
                                                    <i class="ti ti-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.questions.edit', $question) }}" 
                                                   class="btn btn-sm btn-light-primary"
                                                   data-bs-toggle="tooltip" 
                                                   title="Edit">
                                                    <i class="ti ti-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.questions.duplicate', $question) }}" 
                                                      method="POST" 
                                                      class="d-inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-light-warning"
                                                            data-bs-toggle="tooltip" 
                                                            title="Duplikat">
                                                        <i class="ti ti-copy"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.questions.destroy', $question) }}" 
                                                      method="POST" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('Yakin ingin menghapus pertanyaan ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-light-danger"
                                                            data-bs-toggle="tooltip" 
                                                            title="Hapus"
                                                            {{ $question->assessment_steps_count > 0 ? 'disabled' : '' }}>
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <i class="ti ti-clipboard-off fs-1 text-muted mb-3 d-block"></i>
                                            <p class="text-muted mb-0">Belum ada pertanyaan. Silakan tambah pertanyaan baru.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Bulk Actions --}}
                <div class="card-footer" id="bulkActions" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">
                            <span id="selectedCount">0</span> pertanyaan dipilih
                        </span>
                        <button type="button" class="btn btn-sm btn-danger" id="bulkDeleteBtn">
                            <i class="ti ti-trash me-1"></i> Hapus Terpilih
                        </button>
                    </div>
                </div>

                {{-- Pagination --}}
                @if($questions->hasPages())
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <p class="text-muted mb-0">
                                Menampilkan {{ $questions->firstItem() }} - {{ $questions->lastItem() }} 
                                dari {{ $questions->total() }} pertanyaan
                            </p>
                            {{ $questions->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                @endif
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

    // Select All
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.question-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateBulkActions();
    });

    // Individual checkbox
    document.querySelectorAll('.question-checkbox').forEach(cb => {
        cb.addEventListener('change', updateBulkActions);
    });

    function updateBulkActions() {
        const selectedCheckboxes = document.querySelectorAll('.question-checkbox:checked');
        const count = selectedCheckboxes.length;
        
        document.getElementById('selectedCount').textContent = count;
        document.getElementById('bulkActions').style.display = count > 0 ? 'block' : 'none';
    }

    // Bulk Delete
    document.getElementById('bulkDeleteBtn').addEventListener('click', function() {
        const selectedIds = Array.from(document.querySelectorAll('.question-checkbox:checked'))
            .map(cb => cb.value);
        
        if (selectedIds.length === 0) {
            alert('Pilih minimal 1 pertanyaan');
            return;
        }

        if (confirm(`Yakin ingin menghapus ${selectedIds.length} pertanyaan?`)) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("admin.questions.bulk-delete") }}';
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);
            
            selectedIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'question_ids[]';
                input.value = id;
                form.appendChild(input);
            });
            
            document.body.appendChild(form);
            form.submit();
        }
    });
</script>
@endpush
@endsection