@extends('layouts.app')
@section('content')
<div class="container">
  <h1>Edit Quiz</h1>
  <form action="{{ route('admin.quizzes.update', $quiz) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="mb-3">
      <label>Judul</label>
      <input type="text" name="title" class="form-control" value="{{ $quiz->title }}" required>
    </div>
    <div class="mb-3">
      <label>Pertanyaan</label>
      <textarea name="question" class="form-control" required>{{ $quiz->question }}</textarea>
    </div>
    <div class="mb-3">
      <label>Jawaban Benar</label>
      <input type="text" name="correct_answer" class="form-control" value="{{ $quiz->correct_answer }}" required>
    </div>
    @if($quiz->video_path)
      <div class="mb-3">
        <label>Video Saat Ini</label><br>
        <video src="{{ asset('storage/'.$quiz->video_path) }}" width="200" controls></video>
      </div>
    @endif
    <div class="mb-3">
      <label>Ganti Video (opsional)</label>
      <input type="file" name="video" class="form-control" accept="video/*">
    </div>
    <button class="btn btn-success">Update</button>
  </form>
</div>
@endsection
