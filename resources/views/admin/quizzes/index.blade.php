@extends('layouts.app')
@section('content')
<div class="container">
  <h1 class="mb-3">Daftar Quiz</h1>
  <a href="{{ route('admin.quizzes.create') }}" class="btn btn-primary mb-3">Tambah Quiz</a>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Judul</th>
        <th>Pertanyaan</th>
        <th>Jawaban</th>
        <th>Video</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      @foreach($quizzes as $quiz)
      <tr>
        <td>{{ $quiz->title }}</td>
        <td>{{ $quiz->question }}</td>
        <td>{{ $quiz->correct_answer }}</td>
        <td>
          @if($quiz->video_path)
            <video src="{{ asset('storage/'.$quiz->video_path) }}" width="120" controls></video>
          @else
            -
          @endif
        </td>
        <td>
          <a href="{{ route('admin.quizzes.edit',$quiz->id) }}" class="btn btn-sm btn-warning">Edit</a>
          <form action="{{ route('admin.quizzes.destroy',$quiz->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus quiz ini?')">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-danger">Hapus</button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>

  {{ $quizzes->links() }}
</div>
@endsection
