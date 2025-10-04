@extends('layouts.app')

@section('title', 'Tambah Assessment')

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
                                <li class="breadcrumb-item active">Tambah Assessment</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">Tambah Assessment Baru</h2>
                    <p class="text-muted mb-0">Lengkapi form di bawah untuk membuat assessment</p>
                </div>
                <a href="{{ route('admin.assessments.index') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-arrow-left me-1"></i> Kembali
                </a>
            </div>

            {{-- Form Card --}}
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.assessments.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- Title --}}
                        <div class="mb-4">
                            <label for="title" class="form-label">
                                Judul Assessment <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('title') is-invalid @enderror" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title') }}"
                                   placeholder="Contoh: Assessment Kepribadian Karyawan"
                                   required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Berikan judul yang jelas dan deskriptif</small>
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
                                      placeholder="Jelaskan tujuan dan cakupan assessment ini...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Total Steps --}}
                        <div class="mb-4">
                            <label for="total_steps" class="form-label">
                                Jumlah Steps <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   class="form-control @error('total_steps') is-invalid @enderror" 
                                   id="total_steps" 
                                   name="total_steps" 
                                   value="{{ old('total_steps', 1) }}"
                                   min="1"
                                   max="10"
                                   required>
                            @error('total_steps')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="ti ti-info-circle"></i> 
                                Tentukan berapa banyak langkah/halaman dalam assessment ini (1-10 steps)
                            </small>
                        </div>

                        {{-- Status --}}
                        <div class="mb-4">
                            <label class="form-label">Status Assessment</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Aktifkan assessment setelah dibuat
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                Assessment yang aktif dapat diakses oleh user
                            </small>
                        </div>

                        <hr class="my-4">

                        {{-- Info Box --}}
                        <div class="alert alert-info d-flex align-items-start" role="alert">
                            <i class="ti ti-bulb fs-4 me-2"></i>
                            <div>
                                <strong>Tips:</strong>
                                <ul class="mb-0 ps-3 mt-2">
                                    <li>Setelah membuat assessment, Anda akan diarahkan untuk setup steps dan pertanyaan</li>
                                    <li>Pastikan jumlah steps sesuai dengan kebutuhan (tidak terlalu banyak agar user tidak bosan)</li>
                                    <li>Assessment dapat dinonaktifkan kapan saja tanpa menghapus data</li>
                                </ul>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('admin.assessments.index') }}" class="btn btn-light">
                                <i class="ti ti-x me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-check me-1"></i> Simpan Assessment
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Preview Card (Optional) --}}
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="ti ti-eye me-2"></i>Preview Steps
                    </h5>
                </div>
                <div class="card-body">
                    <div id="stepsPreview" class="stepper">
                        <div class="step active">
                            <div class="step-number">1</div>
                            <div class="step-label">Step 1</div>
                        </div>
                    </div>
                    <p class="text-center text-muted mb-0 mt-3">
                        <small>Preview akan muncul saat Anda mengubah jumlah steps</small>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Preview Steps

    
    document.getElementById('total_steps').addEventListener('input', function() {

        const totalSteps = parseInt(this.value) || 1;
        const preview = document.getElementById('stepsPreview');
        
        if (totalSteps < 1 || totalSteps > 10) return;
        
        let html = '';
        for (let i = 1; i <= totalSteps; i++) {
            const activeClass = i === 1 ? 'active' : '';
            html += `
                <div class="step ${activeClass}">
                    <div class="step-number">${i}</div>
                    <div class="step-label">Step ${i}</div>
                </div>
            `;
        }
        
        preview.innerHTML = html;
    });

    // Form Validation
    (function () {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
    })()
</script>
@endpush
@endsection