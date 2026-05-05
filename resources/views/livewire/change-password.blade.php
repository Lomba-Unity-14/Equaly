<div class="fixed inset-0 z-[60] flex items-end justify-center">
    <div class="fixed inset-0 bg-black/50" wire:click="close"></div>

    <div class="relative w-full max-w-container-max bg-surface rounded-t-[28px] p-6 max-h-[90vh] overflow-y-auto shadow-2xl">
        <div class="flex items-center justify-between mb-6">
            <h2 class="font-h2 text-h2 text-on-surface">Ubah Kata Sandi</h2>
            <button type="button" wire:click="close" class="w-10 h-10 rounded-full flex items-center justify-center hover:bg-surface-container transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <div class="mb-4">
            <label class="font-body-sm text-body-sm text-secondary block mb-1">Kata Sandi Saat Ini</label>
            <input type="password" wire:model="current_password" class="w-full px-4 py-3 rounded-xl border border-border-subtle bg-surface text-on-surface font-body-lg text-body-lg placeholder:text-secondary focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" />
            @error('current_password') <p class="font-body-sm text-body-sm text-error mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="font-body-sm text-body-sm text-secondary block mb-1">Kata Sandi Baru</label>
            <input type="password" wire:model="password" placeholder="Minimal 8 karakter" class="w-full px-4 py-3 rounded-xl border border-border-subtle bg-surface text-on-surface font-body-lg text-body-lg placeholder:text-secondary focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" />
            @error('password') <p class="font-body-sm text-body-sm text-error mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-6">
            <label class="font-body-sm text-body-sm text-secondary block mb-1">Konfirmasi Kata Sandi Baru</label>
            <input type="password" wire:model="password_confirmation" placeholder="Masukkan ulang kata sandi baru" class="w-full px-4 py-3 rounded-xl border border-border-subtle bg-surface text-on-surface font-body-lg text-body-lg placeholder:text-secondary focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" />
        </div>

        <div class="flex gap-3">
            <button type="button" wire:click="close" class="flex-1 py-3.5 rounded-xl border-2 border-border-subtle text-on-surface font-body-lg text-body-lg font-semibold hover:bg-surface-container transition-colors min-h-[48px]">
                Batal
            </button>
            <button type="button" wire:click="save" class="flex-1 py-3.5 rounded-xl bg-primary text-on-primary font-body-lg text-body-lg font-semibold hover:bg-primary-container transition-colors min-h-[48px]">
                Simpan
            </button>
        </div>
    </div>
</div>
