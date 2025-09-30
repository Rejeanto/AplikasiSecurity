@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Tambah Quiz</h1>

    {{-- Tampilkan pesan error atau sukses jika ada --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('admin.quizzes.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="title" class="form-label">Judul</label>
            <input id="title" type="text" name="title"
                   value="{{ old('title') }}"
                   class="form-control @error('title') is-invalid @enderror"
                   required>
            @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="question" class="form-label">Pertanyaan</label>
            <textarea id="question" name="question"
                      class="form-control @error('question') is-invalid @enderror"
                      rows="4" required>{{ old('question') }}</textarea>
            @error('question')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="correct_answer" class="form-label">Jawaban Benar</label>
            <input id="correct_answer" type="text" name="correct_answer"
                   value="{{ old('correct_answer') }}"
                   class="form-control @error('correct_answer') is-invalid @enderror"
                   required>
            @error('correct_answer')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="video" class="form-label">Upload Video (opsional)</label>
            <input id="video" type="file" name="video"
                   accept="video/*"
                   class="form-control @error('video') is-invalid @enderror">
            @error('video')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button class="btn btn-primary">Simpan</button>
    </form>
</div>
@endsection
