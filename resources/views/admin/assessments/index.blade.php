@extends('layouts.app')

@section('title', 'Daftar Assessment')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">Daftar Assessment</h2>
                    <p class="text-muted mb-0">Kelola semua assessment Anda</p>
                </div>
                <a href="{{ route('admin.assessments.create') }}" 
                   class="btn btn-primary">
                    <i class="ti ti-plus me-1"></i> Tambah Assessment
                </a>
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
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Card Tabel --}}
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th width="50">#</th>
                                    <th>Judul</th>
                                    <th>Deskripsi</th>
                                    <th class="text-center" width="80">Steps</th>
                                    <th class="text-center" width="100">Questions</th>
                                    <th class="text-center" width="100">Status</th>
                                    <th class="text-center" width="150">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($assessments as $assessment)
                                    <tr>
                                        <td>{{ $loop->iteration + $assessments->firstItem() - 1 }}</td>
                                        <td>
                                            <h6 class="mb-0">{{ $assessment->title }}</h6>
                                        </td>
                                        <td>
                                            <span class="text-muted">
                                                {{ Str::limit($assessment->description, 60) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light-secondary">{{ $assessment->total_steps }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light-info">{{ $assessment->total_questions ?? 0 }}</span>
                                        </td>
                                        <td class="text-center">
                                            @if($assessment->is_active)
                                                <span class="badge bg-light-success">
                                                    <i class="ti ti-circle-check me-1"></i>Aktif
                                                </span>
                                            @else
                                                <span class="badge bg-light-danger">
                                                    <i class="ti ti-circle-x me-1"></i>Nonaktif
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="{{ route('admin.assessments.edit', $assessment) }}" 
                                                   class="btn btn-sm btn-light-primary" 
                                                   data-bs-toggle="tooltip" 
                                                   title="Edit Assessment">
                                                    <i class="ti ti-edit"></i>
                                                </a>
                                                <a href="{{ route('admin.assessments.steps.index', $assessment) }}" 
                                                   class="btn btn-sm btn-light-info" 
                                                   data-bs-toggle="tooltip" 
                                                   title="Kelola Steps">
                                                    <i class="ti ti-stairs"></i>
                                                </a>
                                                <form action="{{ route('admin.assessments.destroy', $assessment) }}" 
                                                      method="POST" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('Yakin ingin menghapus assessment ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-light-danger"
                                                            data-bs-toggle="tooltip" 
                                                            title="Hapus Assessment">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="ti ti-clipboard-off fs-1 mb-3 d-block"></i>
                                                <p class="mb-0">Belum ada assessment. Silakan tambah assessment baru.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Pagination --}}
                @if($assessments->hasPages())
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <p class="text-muted mb-0">
                                Menampilkan {{ $assessments->firstItem() }} - {{ $assessments->lastItem() }} 
                                dari {{ $assessments->total() }} assessment
                            </p>
                            {{ $assessments->links('pagination::bootstrap-5') }}
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
</script>
@endpush
@endsection