<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Karyawan - Sistem Absensi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4 selection:bg-sky-500 selection:text-white">
    <div class="max-w-md w-full">
        <div class="bg-white/80 backdrop-blur-md rounded-2xl shadow-xl p-8 md:p-10 border border-slate-100">
            <div class="text-center mb-8">
                <div
                    class="mx-auto w-20 h-20 bg-gradient-to-tr from-sky-500 to-blue-400 rounded-full flex items-center justify-center mb-5 shadow-lg border-4 border-white">
                    <i class="fas fa-user-shield text-white text-4xl"></i>
                </div>
                <h1 class="text-3xl font-extrabold text-slate-800 mb-1 tracking-tight">Login Karyawan</h1>
                <p class="text-slate-500">Sistem Absensi Online</p>
            </div>

            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md" role="alert">
                    <p><i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}</p>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li><i class="fas fa-times-circle mr-2"></i>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('karyawan.login.post') }}" class="space-y-6">
                @csrf
                <div>
                    <label for="nip" class="block text-sm font-semibold text-slate-700 mb-1">
                        <i class="fas fa-id-badge mr-2 text-slate-400"></i>ID Karyawan
                    </label>
                    <input type="text" id="nip" name="nip" value="{{ old('nip') }}"
                        class="w-full outline-0 px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-400 focus:border-sky-400 transition duration-150 ease-in-out placeholder-slate-400 text-slate-700 bg-slate-50 shadow-sm"
                        placeholder="Masukkan ID Karyawan" required autofocus>
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700 mb-1">
                        <i class="fas fa-lock mr-2 text-slate-400"></i>Password
                    </label>
                    <div class="relative">
                        <input type="password" id="password" name="password"
                            class="w-full px-4 outline-0 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-400 focus:border-sky-400 transition duration-150 ease-in-out placeholder-slate-400 text-slate-700 bg-slate-50 shadow-sm"
                            placeholder="Masukkan Password Anda" required>
                        <button type="button" onclick="togglePasswordVisibility()" title="Toggle password visibility"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-slate-400 hover:text-sky-500 focus:outline-none">
                            <i id="password-icon" class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="remember"
                            class="w-4 h-4 text-sky-600 bg-slate-100 border-slate-300 rounded focus:ring-sky-500 focus:ring-offset-0 cursor-pointer">
                        <span class="ml-2 text-sm text-slate-600">Ingat saya</span>
                    </label>
                </div>

                <button type="submit"
                    class="w-full bg-gradient-to-tr from-sky-500 to-blue-500 hover:from-sky-600 hover:to-blue-600 text-white font-bold py-3 px-4 rounded-xl transition duration-150 ease-in-out transform active:scale-95 focus:outline-none focus:ring-2 focus:ring-sky-400 focus:ring-offset-2 shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                    <i class="fas fa-sign-in-alt"></i> Masuk
                </button>
            </form>

            <div class="mt-10 text-center">
                <p class="text-xs text-slate-400">
                    &copy; {{ date('Y') }} <span class="font-semibold text-sky-500">Sistem Absensi Karyawan</span>. All
                    rights reserved.
                </p>
            </div>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('password-icon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        }
        document.addEventListener('DOMContentLoaded', function () {
            // Autofocus NIP field
            const nipInput = document.getElementById('nip');
            if (nipInput) {
                nipInput.focus();
            }
        });
    </script>
</body>

</html>