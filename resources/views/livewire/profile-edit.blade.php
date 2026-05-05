<div class="fixed inset-0 z-[60] flex items-end justify-center">
    <div class="fixed inset-0 bg-black/50" wire:click="close"></div>

    <div class="relative w-full max-w-container-max bg-surface rounded-t-[28px] p-6 max-h-[90vh] overflow-y-auto shadow-2xl">
        <div class="flex items-center justify-between mb-6">
            <h2 class="font-h2 text-h2 text-on-surface">Edit Profil</h2>
            <button type="button" wire:click="close" class="w-10 h-10 rounded-full flex items-center justify-center hover:bg-surface-container transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <div class="flex flex-col items-center mb-6">
            <div class="relative w-24 h-24 mb-3">
                @if ($avatar)
                    <img src="{{ $avatar->temporaryUrl() }}" class="w-full h-full rounded-full object-cover border-4 border-white shadow-sm" />
                @elseif ($currentAvatar && !$removeAvatar)
                    <img src="{{ Storage::url($currentAvatar) }}" class="w-full h-full rounded-full object-cover border-4 border-white shadow-sm" />
                @else
                    <img src="https://www.gravatar.com/avatar/{{ md5(strtolower(trim(auth()->user()->email))) }}?s=200&d=mp" class="w-full h-full rounded-full object-cover border-4 border-white shadow-sm" />
                @endif
            </div>
            <div class="flex gap-2">
                <label class="px-4 py-2 rounded-xl bg-primary text-on-primary font-body-sm text-body-sm cursor-pointer hover:bg-primary-container transition-colors">
                    Pilih Foto
                    <input type="file" wire:model="avatar" accept="image/png,image/jpeg,image/webp" class="hidden" />
                </label>
                @if ($avatar || ($currentAvatar && !$removeAvatar))
                    @if ($removeAvatar)
                        <button type="button" wire:click="undoClearAvatar" class="px-4 py-2 rounded-xl border border-border-subtle text-on-surface font-body-sm text-body-sm hover:bg-surface-container transition-colors">
                            Batal Hapus
                        </button>
                    @else
                        <button type="button" wire:click="clearAvatar" class="px-4 py-2 rounded-xl border border-border-subtle text-error font-body-sm text-body-sm hover:bg-error-container/30 transition-colors">
                            Hapus
                        </button>
                    @endif
                @endif
            </div>
            @error('avatar') <p class="font-body-sm text-body-sm text-error mt-2">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="font-body-sm text-body-sm text-secondary block mb-1">Nama</label>
            <input type="text" wire:model="name" class="w-full px-4 py-3 rounded-xl border border-border-subtle bg-surface text-on-surface font-body-lg text-body-lg placeholder:text-secondary focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" />
            @error('name') <p class="font-body-sm text-body-sm text-error mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="font-body-sm text-body-sm text-secondary block mb-1">Email</label>
            <input type="email" wire:model="email" class="w-full px-4 py-3 rounded-xl border border-border-subtle bg-surface text-on-surface font-body-lg text-body-lg placeholder:text-secondary focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" />
            @error('email') <p class="font-body-sm text-body-sm text-error mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-6">
            <label class="font-body-sm text-body-sm text-secondary block mb-1">Headline / Jabatan</label>
            <input type="text" wire:model="headline" placeholder="Cth: UI/UX Enthusiast" class="w-full px-4 py-3 rounded-xl border border-border-subtle bg-surface text-on-surface font-body-lg text-body-lg placeholder:text-secondary focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" />
            @error('headline') <p class="font-body-sm text-body-sm text-error mt-1">{{ $message }}</p> @enderror
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
