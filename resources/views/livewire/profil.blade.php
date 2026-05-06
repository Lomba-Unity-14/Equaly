<div class="flex flex-col gap-stack-lg">
    @if (session('success'))
        <div class="bg-primary-container/40 text-primary font-body-sm text-body-sm px-4 py-3 rounded-xl border border-primary/20">
            {{ session('success') }}
        </div>
    @endif

    <!-- Profile Card -->
    <section class="flex flex-col items-center bg-surface p-6 rounded-2xl shadow-sm border border-border-subtle text-center">
        <div class="relative w-24 h-24 mb-4">
            @if ($user->avatar)
                <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full rounded-full object-cover border-4 border-white shadow-sm" />
            @else
                <img src="https://www.gravatar.com/avatar/{{ md5(strtolower(trim($user->email))) }}?s=200&d=mp" alt="{{ $user->name }}" class="w-full h-full rounded-full object-cover border-4 border-white shadow-sm" />
            @endif
        </div>
        <h2 class="font-h1 text-h1 text-on-surface mb-1">{{ $user->name }}</h2>
        <p class="font-body-lg text-body-lg text-secondary mb-4">{{ $profile?->headline ?: $user->email }}</p>
        <div class="flex gap-3">
            <button wire:click="$set('showEditProfile', true)" class="bg-primary text-on-primary font-body-sm text-body-sm py-2 px-6 rounded-xl hover:bg-primary-container transition-colors min-h-[44px] flex items-center justify-center">
                Edit Profile
            </button>
            <button class="bg-surface-container text-on-surface font-body-sm text-body-sm py-2 px-4 rounded-xl hover:bg-surface-variant transition-colors min-h-[44px] flex items-center justify-center gap-2 border border-border-subtle">
                <span class="material-symbols-outlined text-[18px]">share</span>
                Share
            </button>
        </div>
    </section>

    <!-- Company Reviews -->
    <section class="space-y-stack-sm">
        <div class="flex items-center justify-between px-1">
            <h3 class="font-h2 text-h2 text-on-surface">Beri Ulasan Perusahaan</h3>
            <span class="bg-secondary-container text-on-secondary-container font-label-caps text-label-caps px-3 py-1 rounded-full">1 Tertunda</span>
        </div>
        <div class="bg-surface rounded-2xl border border-border-subtle shadow-sm p-4 space-y-4">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 rounded-xl bg-surface-container-low flex items-center justify-center overflow-hidden shrink-0 border border-border-subtle">
                    <img alt="Company Logo placeholder" class="w-full h-full object-cover opacity-90" data-alt="A close-up, abstract architectural view of a modern glass office building facade reflecting a bright, clear blue sky. The structural lines are sharp, geometric, and precise, conveying a sense of corporate stability and innovation. The visual style is minimal and high-contrast, fitting perfectly within a clean, light-mode interface relying on cool blues and crisp whites." src="https://lh3.googleusercontent.com/aida-public/AB6AXuDmQgPVnzRUuxIShfla69AcH6n0bUeVIKptkRyodg39JsvDGiADfMyydywQx-CEVDVgMX3lu5mjTZvl3NE6EeZexUltIBgvTnsZGAINg-y8tASJWjWI0lZAE371kmVlQmTexhehcXKv9RTbIyRntEzNi6_rjROgi5yEy91zKFLy9NKFNdow6LbyjWeJxAlaqckTtyoVLb2HIdi8eWUODgltzHqG_sXId1usaqTJMZGq2sXaWrdCgRDwuYkZSADz7xWn2ERTgQgK3w"/>
                </div>
                <div class="flex-1">
                    <h4 class="font-body-lg text-body-lg font-semibold text-on-surface">FinTech Nusantara</h4>
                    <p class="font-body-sm text-body-sm text-text-secondary mb-2">Terakhir aktif: 2 minggu lalu</p>
                    <div class="flex items-center gap-1 text-surface-dim">
                        <span class="material-symbols-outlined text-[18px] text-tertiary-container" style="font-variation-settings: 'FILL' 1;">star</span>
                        <span class="material-symbols-outlined text-[18px] text-tertiary-container" style="font-variation-settings: 'FILL' 1;">star</span>
                        <span class="material-symbols-outlined text-[18px] text-tertiary-container" style="font-variation-settings: 'FILL' 1;">star</span>
                        <span class="material-symbols-outlined text-[18px] text-tertiary-container" style="font-variation-settings: 'FILL' 1;">star</span>
                        <span class="material-symbols-outlined text-[18px]">star</span>
                        <span class="ml-2 font-label-caps text-label-caps text-text-secondary">INKLUSIVITAS</span>
                    </div>
                </div>
            </div>
            <button class="w-full bg-primary text-on-primary font-body-lg text-body-lg font-semibold rounded-xl py-3 px-4 flex items-center justify-center gap-2 hover:bg-on-primary-fixed-variant transition-colors">
                <span class="material-symbols-outlined text-[20px]">edit_square</span>
                Tulis Ulasan
            </button>
        </div>
    </section>

    <!-- AI Matching Data -->
    <section class="flex flex-col gap-3">
        <div class="px-1">
            <h3 class="font-h2 text-h2 text-on-surface">Data Pencocokan AI</h3>
            <p class="font-body-sm text-body-sm text-text-secondary mt-1">Atribut yang digunakan untuk rekomendasi yang tepat.</p>
        </div>

        @if($needsRematch)
        <div class="bg-primary-container/30 rounded-2xl p-4 border border-primary/20 flex items-center gap-3">
            <span class="material-symbols-outlined text-[28px] text-primary shrink-0" style="font-variation-settings: 'FILL' 1;">sync</span>
            <div class="flex-1">
                <p class="font-body-sm text-body-sm text-on-primary-container font-semibold">Profil kamu berubah</p>
                <p class="font-label-caps text-label-caps text-on-primary-container/70">Perbarui pencocokan lowongan agar rekomendasi lebih akurat.</p>
            </div>
            <button wire:click="rematch" class="bg-primary text-on-primary font-label-caps text-label-caps py-2.5 px-4 rounded-xl hover:bg-primary-fixed-variant transition-all shrink-0 min-h-[44px] flex items-center justify-center shadow-sm active:scale-95">
                Cocokkan Ulang
            </button>
        </div>
        @endif

        <!-- Skills -->
        <div class="bg-surface rounded-2xl p-5 shadow-sm border border-border-subtle flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-secondary">psychology</span>
                    <h3 class="font-body-lg text-body-lg font-semibold text-text-primary">Keahlian</h3>
                </div>
                @unless ($editing)
                    <button wire:click="editSection('skills')" class="text-primary hover:text-surface-tint flex items-center justify-center p-1 rounded-full hover:bg-surface-container transition-colors">
                        <span class="material-symbols-outlined">edit</span>
                    </button>
                @endunless
            </div>

            @if ($editing === 'skills')
                <div class="flex flex-wrap gap-2">
                    @forelse ($skills as $index => $skill)
                        <span class="inline-flex items-center gap-1.5 bg-surface-container text-on-surface font-label-caps text-label-caps px-2.5 py-1.5 rounded-md border border-border-subtle">
                            {{ $skill }}
                            <button type="button" wire:click="removeSkill({{ $index }})" class="text-secondary hover:text-error transition-colors">
                                <span class="material-symbols-outlined text-[16px]">close</span>
                            </button>
                        </span>
                    @empty
                        <p class="font-body-sm text-body-sm text-secondary w-full">Belum ada keahlian.</p>
                    @endforelse
                </div>
                <div class="flex gap-2">
                    <input type="text" wire:model="newSkill" wire:keydown.enter.prevent="addSkill"
                        placeholder="Cth: Figma, Laravel, Copywriting"
                        class="flex-1 px-4 py-3 rounded-xl border border-border-subtle bg-surface text-on-surface font-body-lg text-body-lg placeholder:text-secondary focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                    <button type="button" wire:click="addSkill"
                        class="px-4 py-3 rounded-xl bg-primary text-on-primary font-body-sm text-body-sm font-semibold hover:bg-primary-container transition-colors min-h-[44px] flex items-center justify-center">
                        Tambah
                    </button>
                </div>
                <div class="flex gap-2 justify-end">
                    <button type="button" wire:click="cancelEdit"
                        class="px-4 py-2 rounded-lg border border-border-subtle text-on-surface font-body-sm text-body-sm hover:bg-surface-container transition-colors">
                        Batal
                    </button>
                    <button type="button" wire:click="saveSection('skills')"
                        class="px-4 py-2 rounded-lg bg-primary text-on-primary font-body-sm text-body-sm font-semibold hover:bg-primary-container transition-colors">
                        Simpan
                    </button>
                </div>
            @else
                <div class="flex flex-wrap gap-2">
                    @forelse ($skills as $skill)
                        <span class="bg-surface-container text-on-surface font-label-caps text-label-caps px-2.5 py-1.5 rounded-md border border-border-subtle">{{ $skill }}</span>
                    @empty
                        <p class="font-body-sm text-body-sm text-secondary">Belum ada keahlian.</p>
                    @endforelse
                </div>
            @endif
        </div>

        <!-- Disability -->
        <div class="bg-surface rounded-2xl p-5 shadow-sm border border-border-subtle flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-secondary">hearing</span>
                    <h3 class="font-body-lg text-body-lg font-semibold text-text-primary">Disabilitas</h3>
                </div>
                @unless ($editing)
                    <button wire:click="editSection('disability_condition')" class="text-primary hover:text-surface-tint flex items-center justify-center p-1 rounded-full hover:bg-surface-container transition-colors">
                        <span class="material-symbols-outlined">edit</span>
                    </button>
                @endunless
            </div>

            @if ($editing === 'disability_condition')
                <div class="flex flex-wrap gap-2">
                    @foreach ($labelMap['disability_condition'] as $value => $label)
                        <button type="button" wire:click="toggleCondition('disability_condition', '{{ $value }}')"
                            class="font-label-caps text-label-caps px-2.5 py-1.5 rounded-md border transition-all cursor-pointer
                                {{ in_array($value, $disability_condition) ? 'bg-primary-container/40 text-primary border-primary/30' : 'bg-surface-container text-on-surface border-border-subtle hover:border-primary/50' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
                <div class="flex gap-2 justify-end">
                    <button type="button" wire:click="cancelEdit"
                        class="px-4 py-2 rounded-lg border border-border-subtle text-on-surface font-body-sm text-body-sm hover:bg-surface-container transition-colors">
                        Batal
                    </button>
                    <button type="button" wire:click="saveSection('disability_condition')"
                        class="px-4 py-2 rounded-lg bg-primary text-on-primary font-body-sm text-body-sm font-semibold hover:bg-primary-container transition-colors">
                        Simpan
                    </button>
                </div>
            @else
                <div class="flex flex-wrap gap-2">
                    @forelse ($disability_condition as $value)
                        <span class="bg-surface-container text-on-surface font-label-caps text-label-caps px-2.5 py-1.5 rounded-md border border-border-subtle">{{ $labelMap['disability_condition'][$value] ?? $value }}</span>
                    @empty
                        <p class="font-body-sm text-body-sm text-secondary">Belum diisi.</p>
                    @endforelse
                </div>
            @endif
        </div>

        <!-- Communication Preference -->
        <div class="bg-surface rounded-2xl p-5 shadow-sm border border-border-subtle flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-secondary">record_voice_over</span>
                    <h3 class="font-body-lg text-body-lg font-semibold text-text-primary">Preferensi Komunikasi</h3>
                </div>
                @unless ($editing)
                    <button wire:click="editSection('communication_preference')" class="text-primary hover:text-surface-tint flex items-center justify-center p-1 rounded-full hover:bg-surface-container transition-colors">
                        <span class="material-symbols-outlined">edit</span>
                    </button>
                @endunless
            </div>

            @if ($editing === 'communication_preference')
                <div class="flex flex-wrap gap-2">
                    @foreach ($labelMap['communication_preference'] as $value => $label)
                        <button type="button" wire:click="toggleCondition('communication_preference', '{{ $value }}')"
                            class="font-label-caps text-label-caps px-2.5 py-1.5 rounded-md border transition-all cursor-pointer
                                {{ in_array($value, $communication_preference) ? 'bg-primary-container/40 text-primary border-primary/30' : 'bg-surface-container text-on-surface border-border-subtle hover:border-primary/50' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
                <div class="flex gap-2 justify-end">
                    <button type="button" wire:click="cancelEdit"
                        class="px-4 py-2 rounded-lg border border-border-subtle text-on-surface font-body-sm text-body-sm hover:bg-surface-container transition-colors">
                        Batal
                    </button>
                    <button type="button" wire:click="saveSection('communication_preference')"
                        class="px-4 py-2 rounded-lg bg-primary text-on-primary font-body-sm text-body-sm font-semibold hover:bg-primary-container transition-colors">
                        Simpan
                    </button>
                </div>
            @else
                <div class="flex flex-wrap gap-2">
                    @forelse ($communication_preference as $value)
                        <span class="bg-surface-container text-on-surface font-label-caps text-label-caps px-2.5 py-1.5 rounded-md border border-border-subtle">{{ $labelMap['communication_preference'][$value] ?? $value }}</span>
                    @empty
                        <p class="font-body-sm text-body-sm text-secondary">Belum diisi.</p>
                    @endforelse
                </div>
            @endif
        </div>

        <!-- Work Environment -->
        <div class="bg-surface rounded-2xl p-5 shadow-sm border border-border-subtle flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-secondary">business_center</span>
                    <h3 class="font-body-lg text-body-lg font-semibold text-text-primary">Lingkungan Kerja</h3>
                </div>
                @unless ($editing)
                    <button wire:click="editSection('work_environment')" class="text-primary hover:text-surface-tint flex items-center justify-center p-1 rounded-full hover:bg-surface-container transition-colors">
                        <span class="material-symbols-outlined">edit</span>
                    </button>
                @endunless
            </div>

            @if ($editing === 'work_environment')
                <div class="flex flex-wrap gap-2">
                    @foreach ($labelMap['work_environment'] as $value => $label)
                        <button type="button" wire:click="toggleCondition('work_environment', '{{ $value }}')"
                            class="font-label-caps text-label-caps px-2.5 py-1.5 rounded-md border transition-all cursor-pointer
                                {{ in_array($value, $work_environment) ? 'bg-primary-container/40 text-primary border-primary/30' : 'bg-surface-container text-on-surface border-border-subtle hover:border-primary/50' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
                <div class="flex gap-2 justify-end">
                    <button type="button" wire:click="cancelEdit"
                        class="px-4 py-2 rounded-lg border border-border-subtle text-on-surface font-body-sm text-body-sm hover:bg-surface-container transition-colors">
                        Batal
                    </button>
                    <button type="button" wire:click="saveSection('work_environment')"
                        class="px-4 py-2 rounded-lg bg-primary text-on-primary font-body-sm text-body-sm font-semibold hover:bg-primary-container transition-colors">
                        Simpan
                    </button>
                </div>
            @else
                <div class="flex flex-wrap gap-2">
                    @forelse ($work_environment as $value)
                        <span class="bg-surface-container text-on-surface font-label-caps text-label-caps px-2.5 py-1.5 rounded-md border border-border-subtle">{{ $labelMap['work_environment'][$value] ?? $value }}</span>
                    @empty
                        <p class="font-body-sm text-body-sm text-secondary">Belum diisi.</p>
                    @endforelse
                </div>
            @endif
        </div>
    </section>

    <!-- Account & Help -->
    <section class="space-y-stack-sm pb-4">
        <h3 class="font-h2 text-h2 text-on-surface px-1">Akun & Bantuan</h3>
        <div class="bg-surface rounded-2xl border border-border-subtle shadow-sm overflow-hidden flex flex-col">
            <div class="flex items-center gap-4 p-4 border-b border-border-subtle">
                <div class="w-10 h-10 rounded-full bg-surface-container-low flex items-center justify-center text-on-surface-variant">
                    <span class="material-symbols-outlined">mail</span>
                </div>
                <div class="flex-1">
                    <span class="font-body-sm text-body-sm text-secondary">Email</span>
                    <span class="font-body-lg text-body-lg text-on-surface block">{{ $user->email }}</span>
                </div>
            </div>
            <button type="button" wire:click="$set('showChangePassword', true)" class="w-full flex items-center gap-4 p-4 border-b border-border-subtle hover:bg-surface-container-lowest transition-colors group cursor-pointer">
                <div class="w-10 h-10 rounded-full bg-surface-container-low flex items-center justify-center text-on-surface-variant group-hover:bg-surface-container transition-colors">
                    <span class="material-symbols-outlined">lock_reset</span>
                </div>
                <div class="flex-1 text-left">
                    <span class="font-body-lg text-body-lg text-on-surface block">Ubah Kata Sandi</span>
                </div>
                <span class="material-symbols-outlined text-text-secondary">chevron_right</span>
            </button>
            <a class="flex items-center gap-4 p-4 border-b border-border-subtle hover:bg-surface-container-lowest transition-colors group" href="#">
                <div class="w-10 h-10 rounded-full bg-surface-container-low flex items-center justify-center text-on-surface-variant group-hover:bg-surface-container transition-colors">
                    <span class="material-symbols-outlined">help</span>
                </div>
                <div class="flex-1">
                    <span class="font-body-lg text-body-lg text-on-surface block">FAQ & Dukungan</span>
                </div>
                <span class="material-symbols-outlined text-text-secondary">chevron_right</span>
            </a>
            <form action="{{ route('logout') }}" method="POST" class="contents">
                @csrf
                <button type="submit" class="w-full flex items-center gap-4 p-4 hover:bg-error-container/50 transition-colors group cursor-pointer">
                    <div class="w-10 h-10 rounded-full bg-error-container/30 group-hover:bg-error-container flex items-center justify-center text-error transition-colors">
                        <span class="material-symbols-outlined">logout</span>
                    </div>
                    <div class="flex-1 text-left">
                        <span class="font-body-lg text-body-lg text-error block font-medium">Keluar</span>
                    </div>
                </button>
            </form>
        </div>
    </section>

    @if ($showEditProfile)
        <livewire:profile-edit />
    @endif

    @if ($showChangePassword)
        <livewire:change-password />
    @endif
</div>
