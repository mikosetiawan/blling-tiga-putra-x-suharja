<x-guest-layout>
    <div class="mb-6">
        <h3 class="text-lg font-700 text-white leading-tight">Buat Akun Baru ✨</h3>
        <p class="text-[13px] text-slate-400">Daftarkan diri Anda untuk mengakses sistem.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="form-label">{{ __('Nama Lengkap') }}</label>
            <input id="name" type="text" name="name" class="form-input" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="John Doe" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="form-label">{{ __('Email Address') }}</label>
            <input id="email" type="email" name="email" class="form-input" value="{{ old('email') }}" required autocomplete="username" placeholder="john@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="form-label">{{ __('Password') }}</label>
            <input id="password" type="password" name="password" class="form-input" required autocomplete="new-password" placeholder="Minimal 8 karakter" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="form-label">{{ __('Konfirmasi Password') }}</label>
            <input id="password_confirmation" type="password" name="password_confirmation" class="form-input" required autocomplete="new-password" placeholder="Ulangi password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="pt-4 flex flex-col gap-3">
            <button type="submit" class="btn-primary">
                {{ __('Daftar Sekarang') }}
            </button>
            <div class="text-center">
                <a class="text-[13px] text-slate-400 hover:text-white transition-colors" href="{{ route('login') }}">
                    {{ __('Sudah berhak akses? Log in disini') }}
                </a>
            </div>
        </div>
    </form>
</x-guest-layout>
