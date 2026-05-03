<x-layouts.auth title="Masuk">
    <div class="flex flex-col gap-stack-lg w-full max-w-100 mx-auto">
        <div class="text-center">
            <h1 class="font-h1 text-h1 text-text-primary">Masuk</h1>
            <p class="font-body-lg text-body-lg text-text-secondary mt-1">Masuk ke akun Equaly kamu</p>
        </div>

        @if(session('success'))
            <div class="bg-score-high-bg border border-score-high-text/20 text-score-high-text rounded-xl p-4 flex items-center gap-3">
                <span class="material-symbols-outlined text-xl">check_circle</span>
                <span class="font-body-sm text-body-sm">{{ session('success') }}</span>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="flex flex-col gap-stack-md">
            @csrf

            <x-form-input
                label="Email"
                name="email"
                type="email"
                placeholder="nama@email.com"
                :error="$errors->first('email')"
            />

            <x-form-input
                label="Kata Sandi"
                name="password"
                type="password"
                placeholder="Minimal 8 karakter"
                :error="$errors->first('password')"
            />

            <x-form-button>Masuk</x-form-button>
        </form>

        <p class="text-center font-body-sm text-body-sm text-text-secondary">
            Belum punya akun?
            <a href="{{ route('register') }}" class="text-primary font-semibold hover:underline">Daftar</a>
        </p>
    </div>
</x-layouts.auth>
