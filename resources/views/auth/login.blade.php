<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Sekretariat GPdI</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white rounded-xl shadow-lg p-8">

        {{-- Logo / Header --}}
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">GPdI Sahabat Allah</h1>
            <p class="text-gray-500 text-sm mt-1">Sistem Manajemen Sekretariat</p>
        </div>

        {{-- Error Flash --}}
        @if(session('error'))
            <div class="mb-4 flex items-start gap-2 bg-red-50 border border-red-300 text-red-700 rounded-lg p-3 text-sm">
                <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- Success Flash --}}
        @if(session('success'))
            <div class="mb-4 bg-green-50 border border-green-300 text-green-700 rounded-lg p-3 text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Login Form --}}
        <form method="POST" action="{{ route('login.submit') }}" class="space-y-5">
            @csrf

            <div>
                <label for="id_jemaat" class="block text-sm font-medium text-gray-700 mb-1.5">
                    ID Jemaat
                </label>
                <input
                    id="id_jemaat"
                    type="text"
                    name="id_jemaat"
                    value="{{ old('id_jemaat') }}"
                    autofocus
                    placeholder="Contoh: 01011980"
                    class="w-full px-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                           {{ $errors->has('id_jemaat') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}"
                >
                @error('id_jemaat')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Password
                </label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    placeholder="••••••••"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                           {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}"
                >
                @error('password')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button
                type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
            >
                Masuk
            </button>
        </form>

        <p class="mt-6 text-center text-xs text-gray-400">
            GPdI Sahabat Allah Palembang
        </p>
    </div>

</body>
</html>
