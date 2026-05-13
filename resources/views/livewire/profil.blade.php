<div class="flex flex-col gap-stack-lg">
    @if (session('success'))
        <div class="bg-primary-container/40 text-primary font-body-sm text-body-sm px-4 py-3 rounded-xl border border-primary/20">
            {{ session('success') }}
        </div>
    @endif

    {{-- Profile Card --}}
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

    {{-- Company Reviews --}}
    <a href="{{ route('histori.lamaran') }}" wire:navigate class="block space-y-stack-sm">
        <div class="flex items-center justify-between px-1">
            <h3 class="font-h2 text-h2 text-on-surface">Histori Lamaran</h3>
            @if($pendingCount > 0)
                <span class="bg-secondary-container text-on-secondary-container font-label-caps text-label-caps px-3 py-1 rounded-full">{{ $pendingCount }} tertunda</span>
            @endif
        </div>
        <div class="bg-surface rounded-2xl border border-border-subtle shadow-sm p-4 space-y-4">
            @if($latestApplication)
                @php $job = $latestApplication->jobVacancyData; @endphp
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-surface-container-low flex items-center justify-center shrink-0 overflow-hidden border border-border-subtle">
                        @if($job && $job->company_logo_url)
                            <img src="{{ $job->company_logo_url }}" alt="{{ $latestApplication->company_name }}" class="w-full h-full object-cover">
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
            <button class="w-full bg-primary text-on-primary font-body-sm text-body-sm font-semibold rounded-xl py-3 px-4 flex items-center justify-center gap-2 hover:bg-primary-fixed-variant transition-colors min-h-[48px]">
                <span class="material-symbols-outlined text-[20px]">list_alt</span>
                Lihat Semua Lamaran
            </button>
        </div>
    </a>

    {{-- AI Matching Data --}}
    <section class="flex flex-col gap-3">
        <div class="px-1">
            <h3 class="font-h2 text-h2 text-on-surface">Data Pencocokan AI</h3>
            <p class="font-body-sm text-body-sm text-text-secondary mt-1">Atribut yang digunakan untuk rekomendasi pekerjaan yang tepat.</p>
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

        {{-- Hearing Level --}}
        <div class="bg-surface rounded-2xl p-5 shadow-sm border border-border-subtle flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-secondary">hearing</span>
                    <h3 class="font-body-lg text-body-lg font-semibold text-text-primary">Tingkat Pendengaran</h3>
                </div>
                @unless ($editing)
                    <button wire:click="editSection('hearing_level')" class="text-primary hover:text-surface-tint flex items-center justify-center p-1 rounded-full hover:bg-surface-container transition-colors">
                        <span class="material-symbols-outlined">edit</span>
                    </button>
                @endunless
            </div>

            @if ($editing === 'hearing_level')
                <div class="flex flex-wrap gap-2">
                    @foreach (\App\Data\OnboardingData::HEARING_LEVELS as $value => $label)
                        <button type="button" wire:click="$set('hearing_level', '{{ $value }}')"
                            class="font-label-caps text-label-caps px-2.5 py-1.5 rounded-md border transition-all cursor-pointer
                                {{ $hearing_level === $value ? 'bg-primary-container/40 text-primary border-primary/30' : 'bg-surface-container text-on-surface border-border-subtle hover:border-primary/50' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
                <div class="flex gap-2 justify-end">
                    <button type="button" wire:click="cancelEdit"
                        class="px-4 py-2 rounded-lg border border-border-subtle text-on-surface font-body-sm text-body-sm hover:bg-surface-container transition-colors">Batal</button>
                    <button type="button" wire:click="saveSection('hearing_level')"
                        class="px-4 py-2 rounded-lg bg-primary text-on-primary font-body-sm text-body-sm font-semibold hover:bg-primary-container transition-colors">Simpan</button>
                </div>
            @else
                <span class="bg-surface-container text-on-surface font-label-caps text-label-caps px-2.5 py-1.5 rounded-md border border-border-subtle inline-block w-fit">
                    {{ \App\Data\OnboardingData::HEARING_LEVELS[$hearing_level] ?? ($hearing_level ?: 'Belum diisi') }}
                </span>
            @endif
        </div>

        {{-- Communication Preference --}}
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
                    @foreach (\App\Data\OnboardingData::COMMUNICATION_PREFERENCES as $value => $label)
                        <button type="button" wire:click="toggleArray('communication_preference', '{{ $value }}')"
                            class="font-label-caps text-label-caps px-2.5 py-1.5 rounded-md border transition-all cursor-pointer
                                {{ in_array($value, $communication_preference) ? 'bg-primary-container/40 text-primary border-primary/30' : 'bg-surface-container text-on-surface border-border-subtle hover:border-primary/50' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
                <div class="flex gap-2 justify-end">
                    <button type="button" wire:click="cancelEdit" class="px-4 py-2 rounded-lg border border-border-subtle text-on-surface font-body-sm text-body-sm hover:bg-surface-container transition-colors">Batal</button>
                    <button type="button" wire:click="saveSection('communication_preference')" class="px-4 py-2 rounded-lg bg-primary text-on-primary font-body-sm text-body-sm font-semibold hover:bg-primary-container transition-colors">Simpan</button>
                </div>
            @else
                <div class="flex flex-wrap gap-2">
                    @forelse ($communication_preference as $value)
                        <span class="bg-surface-container text-on-surface font-label-caps text-label-caps px-2.5 py-1.5 rounded-md border border-border-subtle">{{ \App\Data\OnboardingData::COMMUNICATION_PREFERENCES[$value] ?? $value }}</span>
                    @empty
                        <p class="font-body-sm text-body-sm text-secondary">Belum diisi.</p>
                    @endforelse
                </div>
            @endif
        </div>

        {{-- Work Environment --}}
        <div class="bg-surface rounded-2xl p-5 shadow-sm border border-border-subtle flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-secondary">business_center</span>
                    <h3 class="font-body-lg text-body-lg font-semibold text-text-primary">Lingkungan Kerja & Akomodasi</h3>
                </div>
                @unless ($editing)
                    <button wire:click="editSection('work_environment')" class="text-primary hover:text-surface-tint flex items-center justify-center p-1 rounded-full hover:bg-surface-container transition-colors">
                        <span class="material-symbols-outlined">edit</span>
                    </button>
                @endunless
            </div>

            @if ($editing === 'work_environment')
                <div class="flex flex-wrap gap-2">
                    @foreach (\App\Data\OnboardingData::WORK_ENVIRONMENTS as $value => $label)
                        <button type="button" wire:click="toggleArray('work_environment', '{{ $value }}')"
                            class="font-label-caps text-label-caps px-2.5 py-1.5 rounded-md border transition-all cursor-pointer
                                {{ in_array($value, $work_environment) ? 'bg-primary-container/40 text-primary border-primary/30' : 'bg-surface-container text-on-surface border-border-subtle hover:border-primary/50' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
                <div class="flex gap-2 justify-end">
                    <button type="button" wire:click="cancelEdit" class="px-4 py-2 rounded-lg border border-border-subtle text-on-surface font-body-sm text-body-sm hover:bg-surface-container transition-colors">Batal</button>
                    <button type="button" wire:click="saveSection('work_environment')" class="px-4 py-2 rounded-lg bg-primary text-on-primary font-body-sm text-body-sm font-semibold hover:bg-primary-container transition-colors">Simpan</button>
                </div>
            @else
                <div class="flex flex-wrap gap-2">
                    @forelse ($work_environment as $value)
                        <span class="bg-surface-container text-on-surface font-label-caps text-label-caps px-2.5 py-1.5 rounded-md border border-border-subtle">{{ \App\Data\OnboardingData::WORK_ENVIRONMENTS[$value] ?? $value }}</span>
                    @empty
                        <p class="font-body-sm text-body-sm text-secondary">Belum diisi.</p>
                    @endforelse
                </div>
            @endif
        </div>

        {{-- Skills --}}
        <div class="bg-surface rounded-2xl p-5 shadow-sm border border-border-subtle flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-secondary">psychology</span>
                    <h3 class="font-body-lg text-body-lg font-semibold text-text-primary">Kategori Keahlian</h3>
                </div>
                @unless ($editing)
                    <button wire:click="editSection('skill_categories')" class="text-primary hover:text-surface-tint flex items-center justify-center p-1 rounded-full hover:bg-surface-container transition-colors">
                        <span class="material-symbols-outlined">edit</span>
                    </button>
                @endunless
            </div>

            @if ($editing === 'skill_categories')
                <div class="flex flex-wrap gap-2">
                    @foreach (\App\Data\OnboardingData::SKILL_CATEGORIES as $value => $label)
                        <button type="button" wire:click="toggleSkillCategory('{{ $value }}')"
                            class="font-label-caps text-label-caps px-2.5 py-1.5 rounded-md border transition-all cursor-pointer
                                {{ in_array($value, $skill_categories) ? 'bg-primary-container/40 text-primary border-primary/30' : 'bg-surface-container text-on-surface border-border-subtle hover:border-primary/50' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
                @if (!empty($skill_categories))
                    <div class="border-t border-border-subtle pt-3">
                        <p class="font-body-sm text-body-sm text-secondary mb-2">Sub-Keahlian:</p>
                        @foreach ($this->subSkillsForCategory as $categoryKey => $subs)
                            <div class="mb-3">
                                <p class="font-label-caps text-label-caps text-primary font-semibold mb-1.5">{{ \App\Data\OnboardingData::SKILL_CATEGORIES[$categoryKey] ?? $categoryKey }}</p>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach ($subs as $subValue => $subLabel)
                                        <button type="button" wire:click="toggleArray('sub_skills', '{{ $subValue }}')"
                                            class="font-body-sm text-body-sm px-2.5 py-1 rounded-md border transition-all cursor-pointer
                                                {{ in_array($subValue, $sub_skills) ? 'bg-primary/10 text-primary border-primary/30' : 'bg-surface text-secondary border-border-subtle hover:border-primary/50' }}">
                                            {{ $subLabel }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
                <div class="flex gap-2 justify-end">
                    <button type="button" wire:click="cancelEdit" class="px-4 py-2 rounded-lg border border-border-subtle text-on-surface font-body-sm text-body-sm hover:bg-surface-container transition-colors">Batal</button>
                    <button type="button" wire:click="saveSection('skill_categories')" class="px-4 py-2 rounded-lg bg-primary text-on-primary font-body-sm text-body-sm font-semibold hover:bg-primary-container transition-colors">Simpan</button>
                </div>
            @else
                @php
                    $existingSkills = $profile->skill_categories ?? [];
                    $flatSubs = [];
                    $catLabels = [];
                    if ($existingSkills) {
                        if (isset($existingSkills[0]) && is_array($existingSkills[0])) {
                            foreach ($existingSkills as $entry) {
                                $catLabels[$entry['category'] ?? ''] = \App\Data\OnboardingData::SKILL_CATEGORIES[$entry['category'] ?? ''] ?? ($entry['category'] ?? '');
                                foreach ($entry['subs'] ?? [] as $sub) {
                                    $subKey = ($entry['category'] ?? '') . '.' . $sub;
                                    $flatSubs[$subKey] = \App\Data\OnboardingData::SKILL_SUBS[$entry['category'] ?? ''][$sub] ?? $sub;
                                }
                            }
                        }
                    }
                @endphp
                @if (!empty($catLabels))
                    @foreach ($catLabels as $catKey => $catLabel)
                        <div class="mb-2">
                            <span class="font-body-sm text-body-sm font-semibold text-on-surface block mb-1">{{ $catLabel }}</span>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($existingSkills as $entry)
                                    @if (($entry['category'] ?? '') === $catKey)
                                        @foreach ($entry['subs'] ?? [] as $sub)
                                            <span class="bg-surface-container text-on-surface font-label-caps text-label-caps px-2 py-0.5 rounded-md border border-border-subtle">{{ \App\Data\OnboardingData::SKILL_SUBS[$catKey][$sub] ?? $sub }}</span>
                                        @endforeach
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="font-body-sm text-body-sm text-secondary">Belum diisi.</p>
                @endif
            @endif
        </div>

        {{-- Education --}}
        <div class="bg-surface rounded-2xl p-5 shadow-sm border border-border-subtle flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-secondary">school</span>
                    <h3 class="font-body-lg text-body-lg font-semibold text-text-primary">Pendidikan</h3>
                </div>
                @unless ($editing)
                    <button wire:click="editSection('education')" class="text-primary hover:text-surface-tint flex items-center justify-center p-1 rounded-full hover:bg-surface-container transition-colors">
                        <span class="material-symbols-outlined">edit</span>
                    </button>
                @endunless
            </div>

            @if ($editing === 'education')
                <div class="flex flex-col gap-3">
                    <select wire:model.live="education_level"
                        class="px-3 py-2 rounded-lg border border-border-subtle bg-surface text-on-surface font-body-sm text-body-sm focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                        <option value="">-- Pilih Pendidikan --</option>
                        @foreach (\App\Data\OnboardingData::EDUCATION_LEVELS as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @if (in_array($education_level, ['d1_d4', 's1', 'profesi', 's2', 's3']) && !empty($this->availableMajors))
                        <select wire:model="education_major"
                            class="px-3 py-2 rounded-lg border border-border-subtle bg-surface text-on-surface font-body-sm text-body-sm focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                            <option value="">-- Pilih Jurusan --</option>
                            @foreach ($this->availableMajors as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    @elseif ($education_level === 'sma_smk')
                        <select wire:model="education_major"
                            class="px-3 py-2 rounded-lg border border-border-subtle bg-surface text-on-surface font-body-sm text-body-sm focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                            <option value="">-- Pilih Jurusan SMK --</option>
                            @foreach (\App\Data\OnboardingData::SMK_MAJORS as $major)
                                <option value="{{ $major }}">{{ $major }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div class="flex gap-2 justify-end">
                    <button type="button" wire:click="cancelEdit" class="px-4 py-2 rounded-lg border border-border-subtle text-on-surface font-body-sm text-body-sm hover:bg-surface-container transition-colors">Batal</button>
                    <button type="button" wire:click="saveSection('education')" class="px-4 py-2 rounded-lg bg-primary text-on-primary font-body-sm text-body-sm font-semibold hover:bg-primary-container transition-colors">Simpan</button>
                </div>
            @else
                <div class="flex flex-wrap gap-2 items-center">
                    <span class="font-body-lg text-body-lg text-on-surface">{{ \App\Data\OnboardingData::EDUCATION_LEVELS[$education_level] ?? ($education_level ?: 'Belum diisi') }}</span>
                    @if ($education_major)
                        <span class="text-secondary text-body-sm">&middot;</span>
                        <span class="bg-surface-container text-on-surface font-label-caps text-label-caps px-2 py-0.5 rounded-md border border-border-subtle">{{ $education_major }}</span>
                    @endif
                </div>
            @endif
        </div>

        {{-- Job Types --}}
        <div class="bg-surface rounded-2xl p-5 shadow-sm border border-border-subtle flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-secondary">work</span>
                    <h3 class="font-body-lg text-body-lg font-semibold text-text-primary">Tipe Pekerjaan yang Dicari</h3>
                </div>
                @unless ($editing)
                    <button wire:click="editSection('job_types')" class="text-primary hover:text-surface-tint flex items-center justify-center p-1 rounded-full hover:bg-surface-container transition-colors">
                        <span class="material-symbols-outlined">edit</span>
                    </button>
                @endunless
            </div>

            @if ($editing === 'job_types')
                <div class="flex flex-wrap gap-2">
                    @foreach (\App\Data\OnboardingData::JOB_TYPES as $value => $label)
                        <button type="button" wire:click="toggleArray('job_types', '{{ $value }}')"
                            class="font-label-caps text-label-caps px-2.5 py-1.5 rounded-md border transition-all cursor-pointer
                                {{ in_array($value, $job_types) ? 'bg-primary-container/40 text-primary border-primary/30' : 'bg-surface-container text-on-surface border-border-subtle hover:border-primary/50' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
                <div class="flex gap-2 justify-end">
                    <button type="button" wire:click="cancelEdit" class="px-4 py-2 rounded-lg border border-border-subtle text-on-surface font-body-sm text-body-sm hover:bg-surface-container transition-colors">Batal</button>
                    <button type="button" wire:click="saveSection('job_types')" class="px-4 py-2 rounded-lg bg-primary text-on-primary font-body-sm text-body-sm font-semibold hover:bg-primary-container transition-colors">Simpan</button>
                </div>
            @else
                <div class="flex flex-wrap gap-2">
                    @forelse ($job_types as $value)
                        <span class="bg-surface-container text-on-surface font-label-caps text-label-caps px-2.5 py-1.5 rounded-md border border-border-subtle">{{ \App\Data\OnboardingData::JOB_TYPES[$value] ?? $value }}</span>
                    @empty
                        <p class="font-body-sm text-body-sm text-secondary">Belum diisi.</p>
                    @endforelse
                </div>
            @endif
        </div>

        {{-- Preferred Locations --}}
        <div class="bg-surface rounded-2xl p-5 shadow-sm border border-border-subtle flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-secondary">location_on</span>
                    <h3 class="font-body-lg text-body-lg font-semibold text-text-primary">Lokasi yang Diminati</h3>
                </div>
                @unless ($editing)
                    <button wire:click="editSection('preferred_locations')" class="text-primary hover:text-surface-tint flex items-center justify-center p-1 rounded-full hover:bg-surface-container transition-colors">
                        <span class="material-symbols-outlined">edit</span>
                    </button>
                @endunless
            </div>

            @if ($editing === 'preferred_locations')
                <div class="flex flex-wrap gap-2">
                    @foreach (\App\Data\OnboardingData::LOCATIONS as $value => $label)
                        <button type="button" wire:click="toggleArray('preferred_locations', '{{ $value }}')"
                            class="font-label-caps text-label-caps px-2.5 py-1.5 rounded-md border transition-all cursor-pointer
                                {{ in_array($value, $preferred_locations) ? 'bg-primary-container/40 text-primary border-primary/30' : 'bg-surface-container text-on-surface border-border-subtle hover:border-primary/50' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
                <div class="flex gap-2 justify-end">
                    <button type="button" wire:click="cancelEdit" class="px-4 py-2 rounded-lg border border-border-subtle text-on-surface font-body-sm text-body-sm hover:bg-surface-container transition-colors">Batal</button>
                    <button type="button" wire:click="saveSection('preferred_locations')" class="px-4 py-2 rounded-lg bg-primary text-on-primary font-body-sm text-body-sm font-semibold hover:bg-primary-container transition-colors">Simpan</button>
                </div>
            @else
                <div class="flex flex-wrap gap-2">
                    @forelse ($preferred_locations as $value)
                        <span class="bg-surface-container text-on-surface font-label-caps text-label-caps px-2.5 py-1.5 rounded-md border border-border-subtle">{{ \App\Data\OnboardingData::LOCATIONS[$value] ?? $value }}</span>
                    @empty
                        <p class="font-body-sm text-body-sm text-secondary">Belum diisi.</p>
                    @endforelse
                </div>
            @endif
        </div>
    </section>

    {{-- Account & Help --}}
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
