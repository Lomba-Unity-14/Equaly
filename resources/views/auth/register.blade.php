<x-layouts.auth title="Daftar">
    <div class="flex flex-col gap-stack-lg w-full max-w-100 mx-auto">
        <div class="text-center">
            <h1 class="font-h1 text-h1 text-text-primary">Daftar</h1>
            <p class="font-body-lg text-body-lg text-text-secondary mt-1">Buat akun Equaly kamu</p>
        </div>

        <form action="{{ route('register') }}" method="POST" class="flex flex-col gap-stack-md">
            @csrf

            <x-form-input
                label="Nama"
                name="name"
                type="text"
                placeholder="Nama lengkap kamu"
                :error="$errors->first('name')"
            />

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

            <x-form-input
                label="Konfirmasi Kata Sandi"
                name="password_confirmation"
                type="password"
                placeholder="Ulangi kata sandi kamu"
            />

            <x-form-button>Daftar</x-form-button>
        </form>

        <p class="text-center font-body-sm text-body-sm text-text-secondary">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-primary font-semibold hover:underline">Masuk</a>
        </p>
    </div>
</x-layouts.auth>
