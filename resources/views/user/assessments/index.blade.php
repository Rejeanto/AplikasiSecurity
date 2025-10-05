@extends('layouts.app')

@section('title', 'Daftar Assessment')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            {{-- Header --}}
            <div class="mb-4">
                <h2 class="mb-1">Assessment Tersedia</h2>
                <p class="text-muted mb-0">Pilih assessment yang ingin Anda kerjakan</p>
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

            {{-- Assessment Cards --}}
            <div class="row">
                @forelse($assessments as $assessment)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 assessment-card {{ $assessment->is_completed ? 'completed' : '' }}">
                        @if($assessment->is_completed)
                        <div class="card-badge">
                            <span class="badge bg-success">
                                <i class="ti ti-check-circle me-1"></i>Selesai
                            </span>
                        </div>
                        @endif

                        <div class="card-body d-flex flex-column">
                            <div class="mb-3">
                                <h5 class="card-title mb-2">{{ $assessment->title }}</h5>
                                <p class="card-text text-muted small">
                                    {{ Str::limit($assessment->description, 100) }}
                                </p>
                            </div>

                            {{-- Info --}}
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">
                                        <i class="ti ti-list me-1"></i>Pertanyaan
                                    </span>
                                    <strong>{{ $assessment->total_questions }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">
                                        <i class="ti ti-stairs me-1"></i>Steps
                                    </span>
                                    <strong>{{ $assessment->total_steps }}</strong>
                                </div>
                            </div>

                            {{-- Progress Bar --}}
                            @if($assessment->progress > 0 && !$assessment->is_completed)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted">Progress</small>
                                    <small class="text-muted">{{ $assessment->progress }}%</small>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-primary" 
                                         role="progressbar" 
                                         style="width: {{ $assessment->progress }}%">
                                    </div>
                                </div>
                            </div>
                            @endif

                            {{-- Action Button --}}
                            <div class="mt-auto">
                                @if($assessment->is_completed)
                                    <a href="{{ route('user.assessments.result', $assessment) }}" 
                                       class="btn btn-success w-100">
                                        <i class="ti ti-eye me-1"></i> Lihat Hasil
                                    </a>
                                @elseif($assessment->progress > 0)
                                    <a href="{{ route('user.assessments.start', $assessment) }}" 
                                       class="btn btn-primary w-100">
                                        <i class="ti ti-player-play me-1"></i> Lanjutkan
                                    </a>
                                @else
                                    <a href="{{ route('user.assessments.start', $assessment) }}" 
                                       class="btn btn-primary w-100">
                                        <i class="ti ti-player-play me-1"></i> Mulai Assessment
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="ti ti-clipboard-off fs-1 text-muted mb-3 d-block"></i>
                            <h5 class="text-muted">Belum Ada Assessment</h5>
                            <p class="text-muted mb-0">Saat ini tidak ada assessment yang tersedia</p>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .assessment-card {
        transition: all 0.3s ease;
        border: 2px solid transparent;
        position: relative;
    }
    .assessment-card:hover {
        border-color: var(--accent);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    .assessment-card.completed {
        border-color: #28a745;
        background: linear-gradient(135deg, #f8f9fa 0%, #e8f5e9 100%);
    }
    .card-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 1;
    }
</style>
@endpush
@endsection