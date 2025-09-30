@extends('layouts.app')

@section('content')
{{-- === STEPPER GARIS RAPI === --}}
<div class="quiz-stepper mb-4">
  <div class="step active" data-step="1">
    <div class="circle">1</div>
    <span class="label">Presiden</span>
  </div>
  <div class="line"></div>
  <div class="step" data-step="2">
    <div class="circle">2</div>
    <span class="label">Wakil Presiden</span>
  </div>
  <div class="line"></div>
  <div class="step" data-step="3">
    <div class="circle">3</div>
    <span class="label">Ibu Kota</span>
  </div>
</div>

{{-- === STEP KUIS === --}}
<div id="quiz-steps">
  {{-- STEP 1 --}}
  <div class="quiz-step show" data-step="1">
    <div class="card card-body">
      <h6>Pertanyaan Presiden</h6>
      <video class="w-100 rounded" controls style="max-height:400px;object-fit:cover;">
        <source src="{{ asset('assets/vidio/coba.mp4') }}" type="video/mp4">
      </video>

      <form class="mt-3">
        <label class="form-label">Siapa nama presiden Indonesia saat ini?</label>
        <input type="text" name="answer" class="form-control" placeholder="Jawaban..."/>
      </form>

      <div class="mt-3 d-flex justify-content-end">
        <button class="btn btn-primary btn-next" data-next="2" disabled>Lanjut Step 2</button>
      </div>
    </div>
  </div>

  {{-- STEP 2 --}}
  <div class="quiz-step d-none" data-step="2">
    <div class="card card-body">
      <h6>Pertanyaan Wakil Presiden</h6>
      <video class="w-100 rounded" controls style="max-height:400px;object-fit:cover;">
        <source src="{{ asset('assets/vidio/coba2.mp4') }}" type="video/mp4">
      </video>

      <form class="mt-3">
        <label class="form-label">Siapa nama wakil presiden Indonesia saat ini?</label>
        <input type="text" name="answer" class="form-control" placeholder="Jawaban..."/>
      </form>

      <div class="mt-3 d-flex justify-content-end">
        <button class="btn btn-primary btn-next" data-next="3" disabled>Lanjut Step 3</button>
      </div>
    </div>
  </div>

  {{-- STEP 3 --}}
  <div class="quiz-step d-none" data-step="3">
    <div class="card card-body">
      <h6>Pertanyaan Ibu Kota</h6>
      <video class="w-100 rounded" controls style="max-height:400px;object-fit:cover;">
        <source src="{{ asset('assets/vidio/coba3.mp4') }}" type="video/mp4">
      </video>

      <form class="mt-3">
        <label class="form-label">Apa ibu kota Indonesia?</label>
        <input type="text" name="answer" class="form-control" placeholder="Jawaban..."/>
      </form>

      <div class="mt-3 d-flex justify-content-end">
        <button class="btn btn-success btn-submit-all" disabled>Submit Semua Jawaban</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Hasil Quiz -->
<div class="modal fade" id="quizResultModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-3 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Hasil Quiz</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="quizResultContent"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

{{-- === CSS STEP RAPI === --}}
<style>
.quiz-stepper {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0;
}
.quiz-stepper .step {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  min-width: 90px;
  position: relative;
}
.quiz-stepper .circle {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: #e0e0e0;
  color: #333;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  z-index: 1;
}
.quiz-stepper .label {
  margin-top: 6px;
  font-size: 0.9rem;
}
.quiz-stepper .line {
  flex: 1;
  height: 2px;
  background: #e0e0e0;
}
.quiz-stepper .step.active .circle {
  background: #d46a50; /* warna aktif */
  color: #fff;
}
.quiz-stepper .step.active .label {
  font-weight: 600;
}
</style>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const correctAnswers = {
    1: "Joko Widodo",
    2: "Ma'ruf Amin",
    3: "Jakarta"
  };

  function showStep(step) {
    // tampilkan hanya step yg sesuai
    document.querySelectorAll('.quiz-step').forEach(el => {
      el.classList.toggle('d-none', el.dataset.step !== String(step));
      el.classList.toggle('show', el.dataset.step === String(step));
    });
    // highlight lingkaran stepper
    document.querySelectorAll('.quiz-stepper .step').forEach(el => {
      const s = parseInt(el.dataset.step);
      el.classList.toggle('active', s <= step);
    });
  }

  // enable/disable tombol jika input terisi
  document.querySelectorAll('.quiz-step').forEach(stepDiv => {
    const input  = stepDiv.querySelector('input[name="answer"]');
    const nextBtn = stepDiv.querySelector('.btn-next');
    const submitBtn = stepDiv.querySelector('.btn-submit-all');
    input.addEventListener('input', () => {
      const filled = input.value.trim().length > 0;
      if (nextBtn) nextBtn.disabled = !filled;
      if (submitBtn) submitBtn.disabled = !filled;
    });
  });

  // tombol lanjut
  document.querySelectorAll('.btn-next').forEach(btn => {
    btn.addEventListener('click', e => {
      e.preventDefault();
      showStep(parseInt(btn.dataset.next));
    });
  });

  // tombol submit akhir
  document.querySelector('.btn-submit-all').addEventListener('click', e => {
    e.preventDefault();
    let benar = 0;
    let salah = 0;
    Object.keys(correctAnswers).forEach(step => {
      const input = document.querySelector(`.quiz-step[data-step="${step}"] input[name="answer"]`);
      const user = (input.value || "").trim().toLowerCase();
      if (user === correctAnswers[step].toLowerCase()) benar++;
      else salah++;
    });
    document.getElementById("quizResultContent").innerHTML =
      `<p>Benar: <b>${benar}</b><br>Salah: <b>${salah}</b></p>`;
    new bootstrap.Modal(document.getElementById("quizResultModal")).show();
  });

  // mulai di step 1
  showStep(1);
});
</script>
@endsection
