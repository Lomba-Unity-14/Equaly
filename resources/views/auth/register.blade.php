<x-layouts.auth title="Daftar">
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
                <h2 class="font-h2 text-h2 text-text-primary mb-2">Buat Akun</h2>
                <p class="font-body-sm text-body-sm text-text-secondary">Mulai perjalanan karier inklusifmu bersama Equaly</p>
            </header>

            <form action="{{ route('register') }}" method="POST" class="space-y-stack-md">
                @csrf

                <x-form-input
                    label="Nama"
                    name="name"
                    type="text"
                    placeholder="Nama lengkap kamu"
                    icon="person"
                    :error="$errors->first('name')"
                />

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
                    placeholder="Minimal 8 karakter"
                    icon="lock"
                    :error="$errors->first('password')"
                >
                    <x-slot:trailing>
                        <button type="button" onclick="toggleFieldVisibility('password', 'passwordIcon')" class="flex items-center justify-center text-outline hover:text-text-primary transition-colors">
                            <span class="material-symbols-outlined text-xl" id="passwordIcon">visibility</span>
                        </button>
                    </x-slot:trailing>
                </x-form-input>

                <x-form-input
                    label="Konfirmasi Kata Sandi"
                    name="password_confirmation"
                    type="password"
                    placeholder="Ulangi kata sandi kamu"
                    icon="lock"
                >
                    <x-slot:trailing>
                        <button type="button" onclick="toggleFieldVisibility('password_confirmation', 'passwordConfirmationIcon')" class="flex items-center justify-center text-outline hover:text-text-primary transition-colors">
                            <span class="material-symbols-outlined text-xl" id="passwordConfirmationIcon">visibility</span>
                        </button>
                    </x-slot:trailing>
                </x-form-input>

                <button type="submit" class="w-full py-4 bg-primary text-on-primary font-bold text-body-lg rounded-xl shadow-sm hover:brightness-110 active:scale-[0.98] transition-all mt-2 min-h-touch-target-min cursor-pointer">
                    Daftar
                </button>
            </form>
        </div>

        <!-- Footer -->
        <p class="mt-stack-lg font-body-sm text-body-sm text-text-secondary">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-primary font-bold hover:underline">Masuk</a>
        </p>
    </div>

    @push('scripts')
        <script>
            function toggleFieldVisibility(inputId, iconId) {
                const input = document.getElementById(inputId);
                const icon = document.getElementById(iconId);
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
