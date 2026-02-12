<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Non-Aktif - SIPADU</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: #1f2937;
        }
        .container {
            text-align: center;
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            max-width: 400px;
            width: 90%;
        }
        .icon {
            font-size: 4rem;
            color: #ef4444;
            margin-bottom: 1rem;
        }
        h1 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: #111827;
        }
        p {
            color: #6b7280;
            margin-bottom: 1.5rem;
        }
        .timer {
            font-weight: bold;
            color: #ef4444;
        }
        .btn {
            display: inline-block;
            background-color: #3b82f6;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            text-decoration: none;
            transition: background-color 0.2s;
        }
        .btn:hover {
            background-color: #2563eb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">🚫</div>
        <h1>Akses Ditolak</h1>
        <p>Akun Anda telah dinonaktifkan oleh Administrator.</p>
        <p>Anda akan diarahkan ke halaman login dalam <span id="countdown" class="timer">3</span> detik...</p>
        <a href="{{ route('login') }}" class="btn">Login Sekarang</a>
    </div>

    <script>
        let seconds = 3;
        const countdownElement = document.getElementById('countdown');
        
        const timer = setInterval(() => {
            seconds--;
            countdownElement.textContent = seconds;
            
            if (seconds <= 0) {
                clearInterval(timer);
                window.location.href = "{{ route('login') }}";
            }
        }, 1000);
    </script>
</body>
</html>
