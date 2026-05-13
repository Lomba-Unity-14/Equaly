<div class="flex flex-col gap-stack-lg" x-data="{ tab: 'profil' }">
    @if (session('success'))
        <div class="bg-primary-container/40 text-primary font-body-sm text-body-sm px-4 py-3 rounded-xl border border-primary/20">
            {{ session('success') }}
        </div>
    @endif

    {{-- Tab Navigation --}}
    <div class="flex gap-1 bg-surface-container rounded-xl p-1">
        <button @click="tab = 'profil'" :class="tab === 'profil' ? 'bg-surface text-primary font-semibold shadow-sm' : 'text-on-surface-variant hover:text-on-surface'"
            class="flex-1 py-2.5 px-4 rounded-lg text-sm font-medium transition-all cursor-pointer">
            Profil
        </button>
        <button @click="tab = 'pencocokan'" :class="tab === 'pencocokan' ? 'bg-surface text-primary font-semibold shadow-sm' : 'text-on-surface-variant hover:text-on-surface'"
            class="flex-1 py-2.5 px-4 rounded-lg text-sm font-medium transition-all cursor-pointer">
            Data Pengguna
        </button>
        <button @click="tab = 'akun'" :class="tab === 'akun' ? 'bg-surface text-primary font-semibold shadow-sm' : 'text-on-surface-variant hover:text-on-surface'"
            class="flex-1 py-2.5 px-4 rounded-lg text-sm font-medium transition-all cursor-pointer">
            Akun
        </button>
    </div>


    {{-- Tab: Profil --}}
    <div x-show="tab === 'profil'" x-transition class="flex flex-col gap-stack-lg pb-6">
        <section class="flex flex-col items-center bg-surface p-6 rounded-2xl shadow-sm border border-outline-variant text-center">
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
                <button wire:click="$set('showEditProfile', true)" class="bg-primary text-on-primary font-body-sm text-body-sm py-2 px-6 rounded-xl hover:brightness-110 transition-all min-h-[44px] flex items-center justify-center active:scale-[0.98]">
                    Edit Profil
                </button>
                <button class="bg-surface text-on-surface font-body-sm text-body-sm py-2 px-4 rounded-xl border border-outline-variant hover:bg-surface-container transition-all min-h-[44px] flex items-center justify-center gap-2 active:scale-[0.98]">
                    <span class="material-symbols-outlined text-[18px]">share</span>
                    Share
                </button>
            </div>
        </section>

        <a href="{{ route('histori.lamaran') }}" wire:navigate class="block space-y-stack-sm">
            <div class="flex items-center justify-between px-1">
                <h3 class="font-h2 text-h2 text-on-surface">Histori Lamaran</h3>
                @if($pendingCount > 0)
                    <span class="bg-secondary-container text-on-secondary-container font-label-caps text-label-caps px-3 py-1 rounded-full">{{ $pendingCount }} tertunda</span>
                @endif
            </div>
            <div class="bg-surface rounded-2xl border border-outline-variant shadow-sm p-4 space-y-4">
                @if($latestApplication)
                    @php $job = $latestApplication->jobVacancyData; @endphp
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-surface-container-low flex items-center justify-center shrink-0 overflow-hidden border border-border-subtle">
                            @if($job && $job->image_logo_url)
                                <img src="{{ $job->image_logo_url }}" alt="{{ $latestApplication->company_name }}" class="w-full h-full object-cover">
                            @else
                                <span class="material-symbols-outlined text-secondary">domain</span>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h4 class="font-body-lg text-body-lg font-semibold text-on-surface">{{ $job->job_title ?? 'Lowongan' }}</h4>
                            <p class="font-body-sm text-body-sm text-text-secondary">{{ $latestApplication->company_name ?? 'Perusahaan' }}</p>
                            <p class="font-label-caps text-label-caps text-outline mt-1">{{ $latestApplication->applied_at?->diffForHumans() ?? '-' }}</p>
                        </div>
                    </div>
                    @if($latestApplication->review)
                        <div class="bg-primary-container/20 rounded-xl p-3 flex items-center gap-3">
                            <span class="material-symbols-outlined text-primary shrink-0" style="font-variation-settings: 'FILL' 1;">verified</span>
                            <p class="font-body-sm text-body-sm text-on-primary-container">Ulasan telah diberikan &middot; {{ $latestApplication->review->is_friendly ? 'Ramah disabilitas' : 'Kurang ramah' }}</p>
                        </div>
                    @else
                        <div class="bg-surface-container-low rounded-xl p-3 flex items-center gap-3">
                            <span class="material-symbols-outlined text-warning shrink-0">pending</span>
                            <p class="font-body-sm text-body-sm text-text-secondary">Belum diberikan ulasan</p>
                        </div>
                    @endif
                @else
                    <div class="text-center py-2">
                        <span class="material-symbols-outlined text-[32px] text-outline mb-2">history</span>
                        <p class="font-body-sm text-body-sm text-text-secondary">Belum ada lamaran.</p>
                    </div>
                @endif
                <button class="w-full bg-surface text-on-surface font-body-sm text-body-sm font-semibold rounded-xl py-3 px-4 border border-outline-variant hover:bg-surface-container transition-all min-h-[48px] cursor-pointer active:scale-[0.98]">
                    <span class="material-symbols-outlined text-[20px] align-middle mr-1">list_alt</span>
                    Lihat Semua Lamaran
                </button>
            </div>
        </a>
    </div>

    {{-- Tab: Profil Pencocokan --}}
    <div x-show="tab === 'pencocokan'" x-transition class="flex flex-col gap-stack-lg pb-6">
        <div class="flex flex-col">
            <h2 class="font-h2 text-h2 text-on-surface">Profil Pencocokan</h2>
            <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">Atribut yang digunakan untuk rekomendasi pekerjaan yang tepat.</p>
        </div>

        @if($needsRematch)
            <div class="bg-primary-container/10 border border-primary/20 rounded-2xl p-4 flex items-center gap-3">
                <span class="material-symbols-outlined text-[28px] text-primary shrink-0" style="font-variation-settings: 'FILL' 1;">sync</span>
                <div class="flex-1">
                    <p class="font-body-sm text-body-sm text-on-surface font-semibold">Profil kamu berubah</p>
                    <p class="font-label-caps text-label-caps text-on-surface/70">Perbarui pencocokan agar rekomendasi lebih akurat.</p>
                </div>
                <button wire:click="rematch" class="bg-primary text-on-primary font-label-caps text-label-caps py-2.5 px-4 rounded-xl hover:brightness-110 transition-all shrink-0 min-h-[44px] flex items-center justify-center shadow-sm active:scale-95 cursor-pointer">
                    Cocokkan Ulang
                </button>
            </div>
        @endif

        {{-- Group: Profil Diri --}}
        <div class="bg-surface rounded-2xl border border-outline-variant shadow-sm overflow-hidden">
            <div class="p-5 space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-xl">person</span>
                        <span class="font-label-caps text-label-caps text-primary font-semibold tracking-wider">PROFIL DIRI</span>
                    </div>
                    @unless ($editing === 'profil_diri')
                        <button wire:click="editSection('profil_diri')" class="text-primary hover:text-surface-tint flex items-center justify-center p-1 rounded-full hover:bg-surface-container transition-colors cursor-pointer">
                            <span class="material-symbols-outlined text-xl">edit</span>
                        </button>
                    @endunless
                </div>

                @if ($editing === 'profil_diri')
                    <div class="flex flex-col gap-4">
                        <div class="flex flex-col gap-1.5">
                            <label class="font-body-sm text-body-sm font-semibold text-on-surface">Tingkat Pendengaran</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach (\App\Data\OnboardingData::HEARING_LEVELS as $value => $label)
                                    <button type="button" wire:click="$set('hearing_level', '{{ $value }}')"
                                        class="px-4 py-2 rounded-full border text-sm font-medium transition-all cursor-pointer
                                            {{ $hearing_level === $value ? 'bg-primary-container/20 text-primary border-primary' : 'bg-surface text-secondary border-outline-variant hover:border-primary/50' }}">
                                        {{ $label }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="font-body-sm text-body-sm font-semibold text-on-surface">Pendidikan Terakhir</label>
                            <select wire:model.live="education_level"
                                class="w-full px-4 py-3 rounded-xl border border-border-subtle bg-surface text-on-surface font-body-lg text-body-lg focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                                <option value="">-- Pilih Pendidikan --</option>
                                @foreach (\App\Data\OnboardingData::EDUCATION_LEVELS as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if (in_array($education_level, ['d1_d4', 's1', 'profesi', 's2', 's3']) && !empty($this->availableMajors))
                            <div class="flex flex-col gap-1.5">
                                <label class="font-body-sm text-body-sm font-semibold text-on-surface">Jurusan</label>
                                <select wire:model="education_major"
                                    class="w-full px-4 py-3 rounded-xl border border-border-subtle bg-surface text-on-surface font-body-lg text-body-lg focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                                    <option value="">-- Pilih Jurusan --</option>
                                    @foreach ($this->availableMajors as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @elseif ($education_level === 'sma_smk')
                            <div class="flex flex-col gap-1.5">
                                <label class="font-body-sm text-body-sm font-semibold text-on-surface">Jurusan SMK</label>
                                <select wire:model="education_major"
                                    class="w-full px-4 py-3 rounded-xl border border-border-subtle bg-surface text-on-surface font-body-lg text-body-lg focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                                    <option value="">-- Pilih Jurusan SMK --</option>
                                    @foreach (\App\Data\OnboardingData::SMK_MAJORS as $major)
                                        <option value="{{ $major }}">{{ $major }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>
                    <div class="flex gap-2 justify-end">
                        <button type="button" wire:click="cancelEdit"
                            class="px-6 py-3 rounded-xl border border-outline-variant text-on-surface font-body-lg text-body-lg font-semibold hover:bg-surface-container transition-all cursor-pointer">Batal</button>
                        <button type="button" wire:click="saveSection('profil_diri')"
                            class="px-6 py-3 rounded-xl bg-primary text-on-primary font-bold text-body-lg shadow-sm hover:brightness-110 active:scale-[0.98] transition-all cursor-pointer">Simpan</button>
                    </div>
                @else
                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1">
                            <span class="font-label-caps text-label-caps text-outline">Tingkat Pendengaran</span>
                            <span class="font-body-lg text-body-lg text-on-surface font-medium">{{ \App\Data\OnboardingData::HEARING_LEVELS[$hearing_level] ?? ($hearing_level ?: 'Belum diisi') }}</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="font-label-caps text-label-caps text-outline">Pendidikan</span>
                            <span class="font-body-lg text-body-lg text-on-surface font-medium">
                                {{ \App\Data\OnboardingData::EDUCATION_LEVELS[$education_level] ?? ($education_level ?: 'Belum diisi') }}
                                @if ($education_major)
                                    — {{ $education_major }}
                                @endif
                            </span>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Group: Preferensi --}}
        <div class="bg-surface rounded-2xl border border-outline-variant shadow-sm overflow-hidden">
            <div class="p-5 space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-xl">tune</span>
                        <span class="font-label-caps text-label-caps text-primary font-semibold tracking-wider">PREFERENSI</span>
                    </div>
                    @unless ($editing === 'preferensi')
                        <button wire:click="editSection('preferensi')" class="text-primary hover:text-surface-tint flex items-center justify-center p-1 rounded-full hover:bg-surface-container transition-colors cursor-pointer">
                            <span class="material-symbols-outlined text-xl">edit</span>
                        </button>
                    @endunless
                </div>

                @if ($editing === 'preferensi')
                    <div class="flex flex-col gap-4">
                        <div class="flex flex-col gap-1.5">
                            <label class="font-body-sm text-body-sm font-semibold text-on-surface">Preferensi Komunikasi</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach (\App\Data\OnboardingData::COMMUNICATION_PREFERENCES as $value => $label)
                                    <button type="button" wire:click="toggleArray('communication_preference', '{{ $value }}')"
                                        class="px-4 py-2 rounded-full border text-sm font-medium transition-all cursor-pointer
                                            {{ in_array($value, $communication_preference) ? 'bg-primary-container/20 text-primary border-primary' : 'bg-surface text-secondary border-outline-variant hover:border-primary/50' }}">
                                        {{ $label }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="font-body-sm text-body-sm font-semibold text-on-surface">Lingkungan Kerja & Akomodasi</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach (\App\Data\OnboardingData::WORK_ENVIRONMENTS as $value => $label)
                                    <button type="button" wire:click="toggleArray('work_environment', '{{ $value }}')"
                                        class="px-4 py-2 rounded-full border text-sm font-medium transition-all cursor-pointer
                                            {{ in_array($value, $work_environment) ? 'bg-primary-container/20 text-primary border-primary' : 'bg-surface text-secondary border-outline-variant hover:border-primary/50' }}">
                                        {{ $label }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="font-body-sm text-body-sm font-semibold text-on-surface">Tipe Pekerjaan</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach (\App\Data\OnboardingData::JOB_TYPES as $value => $label)
                                    <button type="button" wire:click="toggleArray('job_types', '{{ $value }}')"
                                        class="px-4 py-2 rounded-full border text-sm font-medium transition-all cursor-pointer
                                            {{ in_array($value, $job_types) ? 'bg-primary-container/20 text-primary border-primary' : 'bg-surface text-secondary border-outline-variant hover:border-primary/50' }}">
                                        {{ $label }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="font-body-sm text-body-sm font-semibold text-on-surface">Lokasi yang Diminati</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach (\App\Data\OnboardingData::LOCATIONS as $value => $label)
                                    <button type="button" wire:click="toggleArray('preferred_locations', '{{ $value }}')"
                                        class="px-3 py-1.5 rounded-full border text-label-caps font-medium transition-all cursor-pointer
                                            {{ in_array($value, $preferred_locations) ? 'bg-primary-container/20 text-primary border-primary' : 'bg-surface text-secondary border-outline-variant hover:border-primary/50' }}">
                                        {{ $label }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-2 justify-end">
                        <button type="button" wire:click="cancelEdit"
                            class="px-6 py-3 rounded-xl border border-outline-variant text-on-surface font-body-lg text-body-lg font-semibold hover:bg-surface-container transition-all cursor-pointer">Batal</button>
                        <button type="button" wire:click="saveSection('preferensi')"
                            class="px-6 py-3 rounded-xl bg-primary text-on-primary font-bold text-body-lg shadow-sm hover:brightness-110 active:scale-[0.98] transition-all cursor-pointer">Simpan</button>
                    </div>
                @else
                    <div class="flex flex-col gap-3">
                        <div class="flex flex-col gap-1">
                            <span class="font-label-caps text-label-caps text-outline">Preferensi Komunikasi</span>
                            <div class="flex flex-wrap gap-1.5 mt-0.5">
                                @forelse ($communication_preference as $value)
                                    <span class="inline-flex px-3 py-1 rounded-full bg-surface-container text-on-surface text-label-caps font-medium border border-border-subtle">{{ \App\Data\OnboardingData::COMMUNICATION_PREFERENCES[$value] ?? $value }}</span>
                                @empty
                                    <span class="font-body-sm text-body-sm text-secondary">Belum diisi.</span>
                                @endforelse
                            </div>
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="font-label-caps text-label-caps text-outline">Lingkungan Kerja & Akomodasi</span>
                            <div class="flex flex-wrap gap-1.5 mt-0.5">
                                @forelse ($work_environment as $value)
                                    <span class="inline-flex px-3 py-1 rounded-full bg-surface-container text-on-surface text-label-caps font-medium border border-border-subtle">{{ \App\Data\OnboardingData::WORK_ENVIRONMENTS[$value] ?? $value }}</span>
                                @empty
                                    <span class="font-body-sm text-body-sm text-secondary">Belum diisi.</span>
                                @endforelse
                            </div>
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="font-label-caps text-label-caps text-outline">Tipe Pekerjaan</span>
                            <div class="flex flex-wrap gap-1.5 mt-0.5">
                                @forelse ($job_types as $value)
                                    <span class="inline-flex px-3 py-1 rounded-full bg-surface-container text-on-surface text-label-caps font-medium border border-border-subtle">{{ \App\Data\OnboardingData::JOB_TYPES[$value] ?? $value }}</span>
                                @empty
                                    <span class="font-body-sm text-body-sm text-secondary">Belum diisi.</span>
                                @endforelse
                            </div>
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="font-label-caps text-label-caps text-outline">Lokasi yang Diminati</span>
                            <div class="flex flex-wrap gap-1.5 mt-0.5">
                                @forelse ($preferred_locations as $value)
                                    <span class="inline-flex px-3 py-1 rounded-full bg-surface-container text-on-surface text-label-caps font-medium border border-border-subtle">{{ \App\Data\OnboardingData::LOCATIONS[$value] ?? $value }}</span>
                                @empty
                                    <span class="font-body-sm text-body-sm text-secondary">Belum diisi.</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Group: Keahlian --}}
        <div class="bg-surface rounded-2xl border border-outline-variant shadow-sm overflow-hidden">
            <div class="p-5 space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-xl">code</span>
                        <span class="font-label-caps text-label-caps text-primary font-semibold tracking-wider">KEAHLIAN</span>
                    </div>
                    @unless ($editing === 'keahlian')
                        <button wire:click="editSection('keahlian')" class="text-primary hover:text-surface-tint flex items-center justify-center p-1 rounded-full hover:bg-surface-container transition-colors cursor-pointer">
                            <span class="material-symbols-outlined text-xl">edit</span>
                        </button>
                    @endunless
                </div>

                @if ($editing === 'keahlian')
                    <div class="flex flex-col gap-4">
                        <div class="flex flex-col gap-1.5">
                            <label class="font-body-sm text-body-sm font-semibold text-on-surface">Kategori Keahlian</label>
                            <div class="grid grid-cols-2 gap-3">
                                @foreach (\App\Data\OnboardingData::SKILL_CATEGORIES as $value => $label)
                                    <button type="button" wire:click="toggleSkillCategory('{{ $value }}')"
                                        class="flex flex-col items-start p-4 rounded-xl border transition-all cursor-pointer text-left
                                            {{ in_array($value, $skill_categories) ? 'border-primary bg-primary-container/10' : 'border-border-subtle bg-surface hover:border-primary/50' }}">
                                        <span class="material-symbols-outlined text-2xl mb-2 {{ in_array($value, $skill_categories) ? 'text-primary' : 'text-on-surface-variant' }}">
                                            {{ \App\Data\OnboardingData::SKILL_CATEGORY_ICONS[$value] ?? 'category' }}
                                        </span>
                                        <span class="font-body-sm text-body-sm font-semibold {{ in_array($value, $skill_categories) ? 'text-primary' : 'text-on-surface' }}">{{ $label }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                        @if (!empty($skill_categories))
                            <div class="border-t border-border-subtle pt-4">
                                <label class="font-body-sm text-body-sm font-semibold text-on-surface block mb-3">Sub-Keahlian</label>
                                @foreach ($this->subSkillsForCategory as $categoryKey => $subs)
                                    <div class="mb-4 last:mb-0">
                                        <span class="inline-block px-3 py-1 bg-primary text-on-primary text-label-caps font-bold rounded-full mb-3">
                                            {{ \App\Data\OnboardingData::SKILL_CATEGORIES[$categoryKey] ?? $categoryKey }}
                                        </span>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach ($subs as $subValue => $subLabel)
                                                <button type="button" wire:click="toggleArray('sub_skills', '{{ $subValue }}')"
                                                    class="px-4 py-2 rounded-full border text-sm font-medium transition-all cursor-pointer
                                                        {{ in_array($subValue, $sub_skills) ? 'bg-primary-container/20 text-primary border-primary' : 'bg-surface text-secondary border-outline-variant hover:border-primary/50' }}">
                                                    {{ $subLabel }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="flex gap-2 justify-end">
                        <button type="button" wire:click="cancelEdit"
                            class="px-6 py-3 rounded-xl border border-outline-variant text-on-surface font-body-lg text-body-lg font-semibold hover:bg-surface-container transition-all cursor-pointer">Batal</button>
                        <button type="button" wire:click="saveSection('keahlian')"
                            class="px-6 py-3 rounded-xl bg-primary text-on-primary font-bold text-body-lg shadow-sm hover:brightness-110 active:scale-[0.98] transition-all cursor-pointer">Simpan</button>
                    </div>
                @else
                    <div class="flex flex-col gap-1">
                        <span class="font-label-caps text-label-caps text-outline">Kategori & Sub-Keahlian</span>
                        <div class="flex flex-col gap-3 mt-1">
                            @php
                                $existingSkills = $profile->skill_categories ?? [];
                                $hasSkills = false;
                            @endphp
                            @if ($existingSkills && isset($existingSkills[0]) && is_array($existingSkills[0]))
                                @foreach ($existingSkills as $entry)
                                    @php
                                        $catKey = $entry['category'] ?? '';
                                        $catLabel = \App\Data\OnboardingData::SKILL_CATEGORIES[$catKey] ?? $catKey;
                                        $subs = $entry['subs'] ?? [];
                                    @endphp
                                    @if ($catKey && !empty($subs))
                                        @php $hasSkills = true; @endphp
                                        <div>
                                            <span class="font-body-sm text-body-sm font-semibold text-on-surface">{{ $catLabel }}</span>
                                            <div class="flex flex-wrap gap-1.5 mt-1">
                                                @foreach ($subs as $sub)
                                                    @php $subLabel = \App\Data\OnboardingData::SKILL_SUBS[$catKey][$sub] ?? $sub; @endphp
                                                    <span class="inline-flex px-3 py-1 rounded-full bg-surface-container text-on-surface text-label-caps font-medium border border-border-subtle">{{ $subLabel }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                            @if (!$hasSkills)
                                <span class="font-body-sm text-body-sm text-secondary">Belum diisi.</span>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Group: Pengalaman Kerja --}}
        <div class="bg-surface rounded-2xl border border-outline-variant shadow-sm overflow-hidden">
            <div class="p-5 space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-xl">work</span>
                        <span class="font-label-caps text-label-caps text-primary font-semibold tracking-wider">PENGALAMAN KERJA</span>
                    </div>
                    @unless ($editing === 'work_experience')
                        <button wire:click="editSection('work_experience')" class="text-primary hover:text-surface-tint flex items-center justify-center p-1 rounded-full hover:bg-surface-container transition-colors cursor-pointer">
                            <span class="material-symbols-outlined text-xl">edit</span>
                        </button>
                    @endunless
                </div>

                @if ($editing === 'work_experience')
                    <div class="flex flex-col gap-4">
                        <button type="button" wire:click="$toggle('no_work_experience')"
                            class="w-full text-left p-4 rounded-2xl border-2 transition-all duration-200 flex items-center gap-3 cursor-pointer
                                {{ $no_work_experience ? 'border-primary bg-primary-container/30' : 'border-border-subtle bg-surface hover:border-primary/50' }}">
                            <span class="material-symbols-outlined text-[22px] shrink-0
                                {{ $no_work_experience ? 'text-primary' : 'text-secondary' }}"
                                style="font-variation-settings: 'FILL' {{ $no_work_experience ? 1 : 0 }};">
                                {{ $no_work_experience ? 'check_box' : 'check_box_outline_blank' }}
                            </span>
                            <span class="font-body-lg text-body-lg text-on-surface">Saya belum memiliki pengalaman kerja</span>
                        </button>

                        @if (!$no_work_experience)
                            <div class="space-y-3">
                                @foreach ($work_experiences as $index => $exp)
                                    <div class="bg-surface-container-low rounded-2xl border border-border-subtle p-4 space-y-4">
                                        <div class="flex items-center justify-between">
                                            <span class="font-label-caps text-label-caps text-primary font-semibold tracking-wider">Pengalaman {{ $index + 1 }}</span>
                                            <button type="button" wire:click="removeWorkExperience({{ $index }})"
                                                class="text-error hover:text-error/80 flex items-center gap-1 text-label-caps cursor-pointer">
                                                <span class="material-symbols-outlined text-[18px]">delete</span>
                                                Hapus
                                            </button>
                                        </div>
                                        <div class="flex flex-col gap-3">
                                            <div class="grid grid-cols-2 gap-3">
                                                <div class="flex flex-col gap-1.5">
                                                    <label class="font-body-sm text-body-sm font-semibold text-on-surface">Nama Perusahaan</label>
                                                    <input type="text" wire:model="work_experiences.{{ $index }}.company_name"
                                                        placeholder="Contoh: PT Maju Jaya"
                                                        class="w-full px-4 py-3 rounded-xl border border-border-subtle bg-surface text-on-surface font-body-lg text-body-lg focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                                                </div>
                                                <div class="flex flex-col gap-1.5">
                                                    <label class="font-body-sm text-body-sm font-semibold text-on-surface">Posisi / Jabatan</label>
                                                    <input type="text" wire:model="work_experiences.{{ $index }}.position"
                                                        placeholder="Contoh: Web Developer"
                                                        class="w-full px-4 py-3 rounded-xl border border-border-subtle bg-surface text-on-surface font-body-lg text-body-lg focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-2 gap-3">
                                                <div class="flex flex-col gap-1.5">
                                                    <label class="font-body-sm text-body-sm font-semibold text-on-surface">Bulan Masuk</label>
                                                    <select wire:model="work_experiences.{{ $index }}.start_month"
                                                        class="w-full px-4 py-3 rounded-xl border border-border-subtle bg-surface text-on-surface font-body-lg text-body-lg focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                                                        <option value="">-- Pilih Bulan --</option>
                                                        @foreach ($this->monthRange as $value => $label)
                                                            <option value="{{ $value }}">{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="flex flex-col gap-1.5">
                                                    <label class="font-body-sm text-body-sm font-semibold text-on-surface">Tahun Masuk</label>
                                                    <select wire:model="work_experiences.{{ $index }}.start_year"
                                                        class="w-full px-4 py-3 rounded-xl border border-border-subtle bg-surface text-on-surface font-body-lg text-body-lg focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                                                        <option value="">-- Pilih Tahun --</option>
                                                        @foreach ($this->yearRange as $year => $label)
                                                            <option value="{{ $year }}">{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-3">
                                                <button type="button" wire:click="$set('work_experiences.{{ $index }}.still_working', {{ !($exp['still_working'] ?? false) ? 'true' : 'false' }})"
                                                    class="flex items-center gap-2 cursor-pointer">
                                                    <span class="material-symbols-outlined text-[22px] shrink-0
                                                        {{ ($exp['still_working'] ?? false) ? 'text-primary' : 'text-secondary' }}"
                                                        style="font-variation-settings: 'FILL' {{ ($exp['still_working'] ?? false) ? 1 : 0 }};">
                                                        {{ ($exp['still_working'] ?? false) ? 'check_box' : 'check_box_outline_blank' }}
                                                    </span>
                                                    <span class="font-body-sm text-body-sm text-on-surface">Masih bekerja sampai saat ini</span>
                                                </button>
                                            </div>

                                            <div class="grid grid-cols-2 gap-3">
                                                <div class="flex flex-col gap-1.5">
                                                    <label class="font-body-sm text-body-sm font-semibold {{ ($exp['still_working'] ?? false) ? 'text-outline' : 'text-on-surface' }}">Bulan Keluar</label>
                                                    <select wire:model="work_experiences.{{ $index }}.end_month"
                                                        @if($exp['still_working'] ?? false) disabled @endif
                                                        class="w-full px-4 py-3 rounded-xl border border-border-subtle bg-surface font-body-lg text-body-lg focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all
                                                            {{ ($exp['still_working'] ?? false) ? 'text-outline bg-surface-container-low cursor-not-allowed opacity-50' : 'text-on-surface' }}">
                                                        <option value="">-- Pilih Bulan --</option>
                                                        @foreach ($this->monthRange as $value => $label)
                                                            <option value="{{ $value }}">{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="flex flex-col gap-1.5">
                                                    <label class="font-body-sm text-body-sm font-semibold {{ ($exp['still_working'] ?? false) ? 'text-outline' : 'text-on-surface' }}">Tahun Keluar</label>
                                                    <select wire:model="work_experiences.{{ $index }}.end_year"
                                                        @if($exp['still_working'] ?? false) disabled @endif
                                                        class="w-full px-4 py-3 rounded-xl border border-border-subtle bg-surface font-body-lg text-body-lg focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all
                                                            {{ ($exp['still_working'] ?? false) ? 'text-outline bg-surface-container-low cursor-not-allowed opacity-50' : 'text-on-surface' }}">
                                                        <option value="">-- Pilih Tahun --</option>
                                                        @foreach ($this->yearRange as $year => $label)
                                                            <option value="{{ $year }}">{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                @if (count($work_experiences) < 10)
                                    <button type="button" wire:click="addWorkExperience"
                                        class="w-full py-4 rounded-xl border-2 border-dashed border-outline-variant text-on-surface-variant font-body-lg text-body-lg font-semibold flex items-center justify-center gap-2 hover:border-primary/50 hover:text-primary hover:bg-primary-container/10 transition-all cursor-pointer">
                                        <span class="material-symbols-outlined text-xl">add</span>
                                        Tambah Pengalaman Kerja
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>
                    <div class="flex gap-2 justify-end">
                        <button type="button" wire:click="cancelEdit"
                            class="px-6 py-3 rounded-xl border border-outline-variant text-on-surface font-body-lg text-body-lg font-semibold hover:bg-surface-container transition-all cursor-pointer">Batal</button>
                        <button type="button" wire:click="saveSection('work_experience')"
                            class="px-6 py-3 rounded-xl bg-primary text-on-primary font-bold text-body-lg shadow-sm hover:brightness-110 active:scale-[0.98] transition-all cursor-pointer">Simpan</button>
                    </div>
                @else
                    <div class="flex flex-col gap-1">
                        <span class="font-label-caps text-label-caps text-outline">Riwayat Pekerjaan</span>
                        @if ($no_work_experience || empty($work_experiences))
                            <span class="font-body-sm text-body-sm text-secondary mt-0.5">Belum ada pengalaman kerja.</span>
                        @else
                            <div class="flex flex-col gap-2 mt-1">
                                @foreach ($work_experiences as $exp)
                                    <div class="flex items-start gap-3 bg-surface-container-low rounded-xl p-3 border border-border-subtle">
                                        <span class="material-symbols-outlined text-secondary text-xl mt-0.5 shrink-0">business_center</span>
                                        <div>
                                            <span class="font-body-sm text-body-sm font-semibold text-on-surface block">{{ $exp['position'] }}</span>
                                            <span class="font-label-caps text-label-caps text-secondary">{{ $exp['company_name'] }}</span>
                                            <span class="font-label-caps text-label-caps text-outline block mt-0.5">
                                                {{ \App\Data\OnboardingData::MONTHS[$exp['start_month']] ?? '' }} {{ $exp['start_year'] }} – {{ ($exp['still_working'] ?? false) ? 'Sekarang' : (\App\Data\OnboardingData::MONTHS[$exp['end_month']] ?? '').' '.$exp['end_year'] }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Tab: Akun --}}
    <div x-show="tab === 'akun'" x-transition class="flex flex-col gap-stack-lg pb-6">
        <div class="flex flex-col">
            <h2 class="font-h2 text-h2 text-on-surface">Akun & Bantuan</h2>
            <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">Kelola akun dan preferensi kamu.</p>
        </div>

        <div class="bg-surface rounded-2xl border border-outline-variant shadow-sm overflow-hidden flex flex-col">
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
    </div>

    @if ($showEditProfile)
        <livewire:profile-edit />
    @endif

    @if ($showChangePassword)
        <livewire:change-password />
    @endif
</div>
