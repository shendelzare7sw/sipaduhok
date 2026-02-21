@extends('layouts.sneat')

@section('title', 'Alumni Dashboard')

@section('content')
<div class="container-xxl d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card text-center shadow-lg" style="max-width: 500px; width: 100%; border-radius: 1rem;">
        <div class="card-body p-5">
            <div class="mb-4">
                <div class="avatar avatar-xl mx-auto mb-3">
                    <img src="{{ $siswa->foto_profil ?? asset('assets/img/avatars/1.png') }}" alt="Avatar" class="rounded-circle shadow-sm" style="width: 100px; height: 100px; object-fit: cover;">
                </div>
                <h3 class="fw-bold text-primary mb-2">Selamat, {{ $siswa->nama_lengkap }}! <i class="fas fa-graduation-cap"></i></h3>
                <p class="text-muted mb-4">Anda telah dinyatakan <strong>LULUS</strong> dari PKBM House of Knowledge.</p>
                <div class="alert alert-success d-flex align-items-center justify-content-center" role="alert">
                    <i class="bx bx-check-circle me-2"></i>
                    <div>
                        Terima kasih telah menjadi bagian dari kami.
                    </div>
                </div>
            </div>
            
            <hr class="my-4">

            <div>
                <p class="small text-muted mb-2">Anda akan dialihkan keluar dalam:</p>
                <h1 class="display-4 fw-bold text-danger" id="countdown">5</h1>
                <p class="small text-muted">detik</p>
            </div>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let timeLeft = 5;
        const countdownEl = document.getElementById('countdown');
        
        const timer = setInterval(function() {
            timeLeft--;
            countdownEl.textContent = timeLeft;
            
            if (timeLeft <= 0) {
                clearInterval(timer);
                document.getElementById('logout-form').submit();
            }
        }, 1000);
    });
</script>
@endsection
