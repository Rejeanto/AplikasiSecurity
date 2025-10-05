@extends('layouts.app')

@section('title', 'Hasil Assessment')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            {{-- Success Header --}}
            <div class="text-center mb-5">
                <div class="mb-3">
                    <i class="ti ti-circle-check text-success" style="font-size: 80px;"></i>
                </div>
                <h2 class="mb-2">Assessment Selesai!</h2>
                <p class="text-muted">Terima kasih telah menyelesaikan assessment ini</p>
            </div>

            {{-- Summary Card --}}
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="mb-4">
                        <i class="ti ti-clipboard-check me-2"></i>{{ $assessment->title }}
                    </h5>

                    <div class="row text-center">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="p-3 bg-light rounded">
                                <i class="ti ti-list fs-2 text-primary mb-2 d-block"></i>
                                <h4 class="mb-1">{{ $responses->count() }}</h4>
                                <small class="text-muted">Pertanyaan Dijawab</small>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="p-3 bg-light rounded">
                                <i class="ti ti-stairs fs-2 text-info mb-2 d-block"></i>
                                <h4 class="mb-1">{{ $assessment->total_steps }}</h4>
                                <small class="text-muted">Total Steps</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <i class="ti ti-star fs-2 text-warning mb-2 d-block"></i>
                                <h4 class="mb-1">{{ $totalScore }}</h4>
                                <small class="text-muted">Total Skor</small>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted d-block">Tanggal Selesai</small>
                            <strong>{{ now()->format('d M Y, H:i') }}</strong>
                        </div>
                        <div class="text-end">
                            <small class="text-muted d-block">User</small>
                            <strong>{{ auth()->user()->name }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Answers Review --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="ti ti-file-text me-2"></i>Review Jawaban Anda
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($assessment->steps->sortBy('step_number') as $step)
                        @if($responsesByStep->has($step->step_number))
                        <div class="mb-4 pb-4 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge bg-primary me-2">Step {{ $step->step_number }}</span>
                                <h6 class="mb-0">{{ $step->step_title }}</h6>
                            </div>

                            @foreach($responsesByStep[$step->step_number] as $index => $response)
                            <div class="question-review mb-3 p-3 bg-light rounded">
                                {{-- Question Number & Text --}}
                                <div class="mb-2">
                                    <span class="badge bg-light-secondary me-2">{{ $index + 1 }}</span>
                                    <strong>{{ $response->question->question_text }}</strong>
                                    @if($response->question->is_required)
                                        <span class="badge bg-danger ms-2">Wajib</span>
                                    @endif
                                </div>

                                {{-- Answer --}}
                                <div class="answer-box p-3 bg-white rounded border">
                                    @if($response->question->isMultipleChoice())
                                        <div class="d-flex align-items-start">
                                            <i class="ti ti-check-circle text-success me-2 mt-1 fs-5"></i>
                                            <div>
                                                <div class="fw-semibold mb-1">{{ $response->selectedOption->option_text }}</div>
                                                @if($response->selectedOption->option_value !== null)
                                                    <span class="badge bg-light-primary">
                                                        <i class="ti ti-star"></i> {{ $response->selectedOption->option_value }} poin
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <div class="d-flex align-items-start">
                                            <i class="ti ti-message-circle text-primary me-2 mt-1 fs-5"></i>
                                            <div class="text-break">{{ $response->answer_text }}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    @endforeach

                    @if($responsesByStep->isEmpty())
                    <div class="text-center py-5">
                        <i class="ti ti-inbox fs-1 text-muted mb-3 d-block"></i>
                        <p class="text-muted mb-0">Tidak ada jawaban yang tersimpan</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Additional Info Card --}}
            @if($totalScore > 0)
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="mb-3">
                        <i class="ti ti-chart-bar me-2"></i>Rincian Skor
                    </h5>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Step</th>
                                    <th>Pertanyaan Dijawab</th>
                                    <th class="text-end">Total Skor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($assessment->steps->sortBy('step_number') as $step)
                                    @if($responsesByStep->has($step->step_number))
                                    @php
                                        $stepResponses = $responsesByStep[$step->step_number];
                                        $stepScore = $stepResponses->sum(function($r) {
                                            return $r->selectedOption->option_value ?? 0;
                                        });
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>Step {{ $step->step_number }}</strong>
                                            <div class="small text-muted">{{ $step->step_title }}</div>
                                        </td>
                                        <td>{{ $stepResponses->count() }} pertanyaan</td>
                                        <td class="text-end">
                                            <span class="badge bg-light-primary">{{ $stepScore }} poin</span>
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                                <tr class="table-light">
                                    <td colspan="2" class="text-end"><strong>TOTAL SKOR</strong></td>
                                    <td class="text-end">
                                        <strong class="text-primary fs-5">{{ $totalScore }} poin</strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            {{-- Action Buttons --}}
            <div class="text-center mt-4 mb-5">
                <a href="{{ route('user.assessments.index') }}" class="btn btn-primary btn-lg">
                    <i class="ti ti-arrow-left me-2"></i>Kembali ke Daftar Assessment
                </a>
                <button onclick="window.print()" class="btn btn-outline-secondary btn-lg ms-2">
                    <i class="ti ti-printer me-2"></i>Cetak Hasil
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    @media print {
        /* Hide elements */
        .btn, .breadcrumb, nav, footer, .page-header {
            display: none !important;
        }
        
        /* Card styles */
        .card {
            border: 1px solid #dee2e6 !important;
            box-shadow: none !important;
            page-break-inside: avoid;
        }
        
        /* Remove backgrounds */
        .bg-light {
            background-color: #ffffff !important;
        }
        
        /* Adjust spacing */
        body {
            padding: 20px;
        }
        
        .container {
            max-width: 100%;
        }
        
        /* Question review */
        .question-review {
            page-break-inside: avoid;
            border: 1px solid #dee2e6 !important;
        }
        
        /* Answer box */
        .answer-box {
            background-color: #f8f9fa !important;
            page-break-inside: avoid;
        }
        
        /* Icons */
        .ti {
            print-color-adjust: exact;
            -webkit-print-color-adjust: exact;
        }
    }
    
    /* Screen styles */
    .question-review {
        transition: all 0.3s ease;
    }
    
    .question-review:hover {
        background: #e9ecef !important;
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .answer-box {
        border-left: 4px solid var(--accent) !important;
    }
    
    .badge.bg-light-primary,
    .badge.bg-light-secondary,
    .badge.bg-light-info {
        font-weight: 500;
    }
</style>
@endpush

@push('scripts')
<script>
    // Smooth scroll for print preview
    document.querySelector('[onclick="window.print()"]').addEventListener('click', function(e) {
        e.preventDefault();
        
        // Show print-friendly message
        const originalTitle = document.title;
        document.title = '{{ $assessment->title }} - Hasil Assessment - {{ auth()->user()->name }}';
        
        // Trigger print
        window.print();
        
        // Restore title
        setTimeout(() => {
            document.title = originalTitle;
        }, 100);
    });

    // Copy result to clipboard
    function copyResults() {
        let text = '=== HASIL ASSESSMENT ===\n\n';
        text += 'Assessment: {{ $assessment->title }}\n';
        text += 'User: {{ auth()->user()->name }}\n';
        text += 'Tanggal: {{ now()->format("d M Y, H:i") }}\n\n';
        text += 'RINGKASAN:\n';
        text += '- Pertanyaan Dijawab: {{ $responses->count() }}\n';
        text += '- Total Steps: {{ $assessment->total_steps }}\n';
        text += '- Total Skor: {{ $totalScore }}\n\n';
        text += '=========================\n\n';
        
        @foreach($assessment->steps->sortBy('step_number') as $step)
            @if($responsesByStep->has($step->step_number))
            text += 'STEP {{ $step->step_number }}: {{ $step->step_title }}\n\n';
                @foreach($responsesByStep[$step->step_number] as $index => $response)
                text += '{{ $index + 1 }}. {{ $response->question->question_text }}\n';
                    @if($response->question->isMultipleChoice())
                    text += '   Jawaban: {{ $response->selectedOption->option_text }}';
                        @if($response->selectedOption->option_value !== null)
                        text += ' ({{ $response->selectedOption->option_value }} poin)';
                        @endif
                    text += '\n\n';
                    @else
                    text += '   Jawaban: {{ $response->answer_text }}\n\n';
                    @endif
                @endforeach
            text += '\n';
            @endif
        @endforeach
        
        // Copy to clipboard
        navigator.clipboard.writeText(text).then(() => {
            alert('Hasil assessment berhasil disalin ke clipboard!');
        }).catch(err => {
            console.error('Gagal menyalin:', err);
        });
    }

    // Add copy button (optional)
    // You can add this button to the UI if needed
</script>
@endpush
@endsection