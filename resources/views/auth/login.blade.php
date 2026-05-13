<x-layouts.auth title="Masuk">
    <div class="w-full max-w-md flex flex-col items-center mx-auto">
        <!-- Logo Section -->
        <div class="mb-stack-lg flex flex-col items-center">
            <div class="w-16 h-16 bg-primary-container rounded-2xl flex items-center justify-center mb-4 shadow-sm border border-outline-variant">
                <span class="material-symbols-outlined text-on-primary text-4xl">diversity_3</span>
            </div>
            <h1 class="font-h1 text-h1 text-primary">Equaly</h1>
        </div>

        <!-- Form Card -->
        <div class="w-full bg-surface rounded-2xl p-8 border border-outline-variant shadow-sm">
            <header class="mb-stack-lg">
                <h2 class="font-h2 text-h2 text-text-primary mb-2">Selamat Datang</h2>
                <p class="font-body-sm text-body-sm text-text-secondary">Masuk untuk melanjutkan perjalanan karier inklusifmu</p>
            </header>

            @if(session('success'))
                <div class="bg-score-high-bg border border-score-high-text/20 text-score-high-text rounded-xl p-4 flex items-center gap-3 mb-stack-md">
                    <span class="material-symbols-outlined text-xl">check_circle</span>
                    <span class="font-body-sm text-body-sm">{{ session('success') }}</span>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-stack-md">
                @csrf

                <x-form-input
                    label="Email"
                    name="email"
                    type="email"
                    placeholder="nama@email.com"
                    icon="mail"
                    :error="$errors->first('email')"
                />

                <x-form-input
                    label="Kata Sandi"
                    name="password"
                    type="password"
                    placeholder="••••••••"
                    icon="lock"
                    :error="$errors->first('password')"
                >
                    <x-slot:labelEnd>
                        <a href="#" class="text-primary hover:underline">Lupa Password?</a>
                    </x-slot:labelEnd>
                    <x-slot:trailing>
                        <button type="button" onclick="togglePasswordVisibility()" class="flex items-center justify-center text-outline hover:text-text-primary transition-colors">
                            <span class="material-symbols-outlined text-xl" id="passwordIcon">visibility</span>
                        </button>
                    </x-slot:trailing>
                </x-form-input>

                <button type="submit" class="w-full py-4 bg-primary text-on-primary font-bold text-body-lg rounded-xl shadow-sm hover:brightness-110 active:scale-[0.98] transition-all mt-2 min-h-touch-target-min cursor-pointer">
                    Masuk
                </button>
            </form>
        </div>

        <!-- Footer -->
        <p class="mt-stack-lg font-body-sm text-body-sm text-text-secondary">
            Belum punya akun?
            <a href="{{ route('register') }}" class="text-primary font-bold hover:underline">Daftar Sekarang</a>
        </p>
    </div>

    @push('scripts')
        <script>
            function togglePasswordVisibility() {
                const input = document.getElementById('password');
                const icon = document.getElementById('passwordIcon');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.textContent = 'visibility_off';
                } else {
                    input.type = 'password';
                    icon.textContent = 'visibility';
                }
            }
        </script>
    @endpush
</x-layouts.auth>
