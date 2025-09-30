@extends('layouts.app')
@section('content')
<div class="container">
  <h3>Admin Dashboard</h3>
  <a href="{{ route('admin.quizzes.index') }}" class="btn btn-primary mt-3">Kelola Pertanyaan Quiz</a>
</div>
@endsection
