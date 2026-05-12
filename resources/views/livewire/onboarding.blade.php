<div class="flex flex-col gap-stack-lg" x-data="{}">
    {{-- Header --}}
    <header class="flex justify-between items-start">
        <div>
            <h1 class="text-h1 font-h1 text-on-surface">Lengkapi Profil</h1>
            <p class="text-body-sm text-on-surface-variant mt-1">Isi data dirimu untuk rekomendasi pekerjaan yang tepat.</p>
        </div>
        @if ($step === 1)
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center gap-1.5 text-on-surface-variant hover:text-error transition-colors text-body-sm font-body-sm cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">logout</span>
                    Keluar
                </button>
            </form>
        @endif
    </header>

    {{-- Progress Bar --}}
    <div>
        <div class="flex gap-1.5 h-1.5 w-full mb-2">
            @foreach (range(1, \App\Livewire\Onboarding::TOTAL_STEPS) as $i)
                <div class="flex-1 {{ $i <= $step ? 'bg-primary' : 'bg-surface-container-high' }} rounded-full"></div>
            @endforeach
        </div>
        <p class="text-label-caps text-on-surface-variant">Langkah {{ $step }} dari {{ \App\Livewire\Onboarding::TOTAL_STEPS }}</p>
    </div>

    {{-- Step 1: Kondisi Disabilitas --}}
    @if ($step === 1)
        <section class="flex flex-col gap-3">
            <div class="flex flex-col">
                <h2 class="text-h2 font-h2 text-on-surface">Kondisi Disabilitas</h2>
                <p class="font-body-sm text-body-sm text-on-surface-variant">Pilih kondisi disabilitas kamu.</p>
            </div>

            <div class="bg-primary-container/10 border border-primary/20 rounded-xl p-4 flex gap-3 mb-1">
                <span class="material-symbols-outlined text-primary text-[20px] shrink-0">info</span>
                <p class="text-body-sm text-on-primary-fixed-variant leading-relaxed">Equaly saat ini fokus untuk teman <strong>Tunarungu</strong>. Dukungan untuk disabilitas lain akan hadir di pengembangan selanjutnya.</p>
            </div>

            <div class="flex flex-col gap-3">
                @foreach (['tunarungu' => 'Tunarungu (Tuli/Deaf)'] as $value => $label)
                    <button type="button"
                        class="w-full text-left p-4 rounded-2xl border-2 transition-all duration-200 flex items-center gap-3 cursor-pointer
                            {{ $disability_condition === $value ? 'border-primary bg-primary-container/30' : 'border-border-subtle bg-surface hover:border-primary/50' }}">
                        <span class="material-symbols-outlined text-[22px] shrink-0 text-primary"
                            style="font-variation-settings: 'FILL' 1;">radio_button_checked</span>
                        <div>
                            <span class="font-body-lg text-body-lg text-on-surface block">{{ $label }}</span>
                        </div>
                    </button>
                @endforeach

                @foreach (['tunadaksa' => 'Tunadaksa (Fisik)', 'netra' => 'Netra / Low Vision', 'lainnya' => 'Lainnya'] as $value => $label)
                    <button type="button" disabled
                        class="w-full text-left p-4 rounded-2xl border-2 transition-all duration-200 flex items-center gap-3 cursor-not-allowed opacity-50 border-border-subtle bg-surface-container-low">
                        <span class="material-symbols-outlined text-[22px] shrink-0 text-outline">radio_button_unchecked</span>
                        <div>
                            <span class="font-body-lg text-body-lg text-outline block">{{ $label }}</span>
                            <span class="font-label-caps text-label-caps text-outline/60">Segera Hadir</span>
                        </div>
                    </button>
                @endforeach
            </div>

            @error('disability_condition')
                <p class="font-body-sm text-body-sm text-error">{{ $message }}</p>
            @enderror
        </section>
    @endif

    {{-- Step 2: Tingkat Pendengaran --}}
    @if ($step === 2)
        <section class="flex flex-col gap-3">
            <div class="flex flex-col">
                <h2 class="text-h2 font-h2 text-on-surface">Tingkat Pendengaran</h2>
                <p class="font-body-sm text-body-sm text-on-surface-variant">Pilih tingkat pendengaran yang paling sesuai dengan kondisi kamu.</p>
            </div>

            <div class="flex flex-col gap-3">
                @foreach (\App\Data\OnboardingData::HEARING_LEVELS as $value => $label)
                    <button type="button" wire:click="$set('hearing_level', '{{ $value }}')"
                        class="w-full text-left p-4 rounded-2xl border-2 transition-all duration-200 flex items-start gap-3 cursor-pointer
                            {{ $hearing_level === $value ? 'border-primary bg-primary-container/30' : 'border-border-subtle bg-surface hover:border-primary/50' }}">
                        <span class="material-symbols-outlined text-[22px] shrink-0 mt-0.5
                            {{ $hearing_level === $value ? 'text-primary' : 'text-secondary' }}"
                            style="font-variation-settings: 'FILL' {{ $hearing_level === $value ? 1 : 0 }};">
                            {{ $hearing_level === $value ? 'radio_button_checked' : 'radio_button_unchecked' }}
                        </span>
                        <div>
                            <span class="font-body-lg text-body-lg text-on-surface block">{{ $label }}</span>
                            <span class="font-body-sm text-body-sm text-secondary mt-0.5 block">{{ \App\Data\OnboardingData::HEARING_DESCRIPTIONS[$value] ?? '' }}</span>
                        </div>
                    </button>
                @endforeach
            </div>

            @error('hearing_level')
                <p class="font-body-sm text-body-sm text-error">{{ $message }}</p>
            @enderror
        </section>
    @endif

    {{-- Step 3: Preferensi Komunikasi --}}
    @if ($step === 3)
        <section class="flex flex-col gap-3">
            <div class="flex flex-col">
                <h2 class="text-h2 font-h2 text-on-surface">Preferensi Komunikasi</h2>
                <p class="font-body-sm text-body-sm text-on-surface-variant">Pilih metode komunikasi yang kamu gunakan (bisa lebih dari satu).</p>
            </div>

            <div class="flex flex-col gap-3">
                @foreach (\App\Data\OnboardingData::COMMUNICATION_PREFERENCES as $value => $label)
                    <button type="button" wire:click="toggleArray('communication_preference', '{{ $value }}')"
                        class="w-full text-left p-4 rounded-2xl border-2 transition-all duration-200 flex items-center gap-3 cursor-pointer
                            {{ in_array($value, $communication_preference) ? 'border-primary bg-primary-container/30' : 'border-border-subtle bg-surface hover:border-primary/50' }}">
                        <span class="material-symbols-outlined text-[22px] shrink-0
                            {{ in_array($value, $communication_preference) ? 'text-primary' : 'text-secondary' }}"
                            style="font-variation-settings: 'FILL' {{ in_array($value, $communication_preference) ? 1 : 0 }};">
                            {{ in_array($value, $communication_preference) ? 'check_box' : 'check_box_outline_blank' }}
                        </span>
                        <span class="font-body-lg text-body-lg text-on-surface">{{ $label }}</span>
                    </button>
                @endforeach
            </div>

            @error('communication_preference')
                <p class="font-body-sm text-body-sm text-error">{{ $message }}</p>
            @enderror
        </section>
    @endif

    {{-- Step 4: Lingkungan Kerja + Akomodasi --}}
    @if ($step === 4)
        <section class="flex flex-col gap-3">
            <div class="flex flex-col">
                <h2 class="text-h2 font-h2 text-on-surface">Lingkungan Kerja & Akomodasi</h2>
                <p class="font-body-sm text-body-sm text-on-surface-variant">Pilih lingkungan kerja dan akomodasi yang kamu butuhkan (bisa lebih dari satu).</p>
            </div>

            <div class="flex flex-col gap-3">
                @foreach (\App\Data\OnboardingData::WORK_ENVIRONMENTS as $value => $label)
                    <button type="button" wire:click="toggleArray('work_environment', '{{ $value }}')"
                        class="w-full text-left p-4 rounded-2xl border-2 transition-all duration-200 flex items-center gap-3 cursor-pointer
                            {{ in_array($value, $work_environment) ? 'border-primary bg-primary-container/30' : 'border-border-subtle bg-surface hover:border-primary/50' }}">
                        <span class="material-symbols-outlined text-[22px] shrink-0
                            {{ in_array($value, $work_environment) ? 'text-primary' : 'text-secondary' }}"
                            style="font-variation-settings: 'FILL' {{ in_array($value, $work_environment) ? 1 : 0 }};">
                            {{ in_array($value, $work_environment) ? 'check_box' : 'check_box_outline_blank' }}
                        </span>
                        <span class="font-body-lg text-body-lg text-on-surface">{{ $label }}</span>
                    </button>
                @endforeach
            </div>

            @error('work_environment')
                <p class="font-body-sm text-body-sm text-error">{{ $message }}</p>
            @enderror
        </section>
    @endif

    {{-- Step 5: Kategori & Sub-Keahlian --}}
    @if ($step === 5)
        <section class="flex flex-col gap-3">
            <div class="flex flex-col">
                <h2 class="text-h2 font-h2 text-on-surface">Kategori Keahlian</h2>
                <p class="font-body-sm text-body-sm text-on-surface-variant">Pilih bidang keahlian yang kamu kuasai (bisa lebih dari satu).</p>
            </div>

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

            @error('skill_categories')
                <p class="font-body-sm text-body-sm text-error">{{ $message }}</p>
            @enderror

            @if (!empty($skill_categories))
                <div class="bg-primary-container/10 border border-primary/20 rounded-2xl p-5 mt-1">
                    <div class="flex flex-col mb-4">
                        <h3 class="text-h2 font-h2 text-on-surface">Sub-Keahlian</h3>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">Pilih sub-keahlian yang lebih spesifik (bisa lebih dari satu).</p>
                    </div>

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

            @error('sub_skills')
                <p class="font-body-sm text-body-sm text-error">{{ $message }}</p>
            @enderror
        </section>
    @endif

    {{-- Step 6: Data Diri & Preferensi Kerja --}}
    @if ($step === 6)
        <section class="flex flex-col gap-3">
            <div class="flex flex-col">
                <h2 class="text-h2 font-h2 text-on-surface">Data Diri & Preferensi Kerja</h2>
                <p class="font-body-sm text-body-sm text-on-surface-variant">Lengkapi informasi berikut untuk rekomendasi yang lebih akurat.</p>
            </div>

            {{-- Card 1: Informasi Dasar --}}
            <div class="bg-surface rounded-2xl border border-outline-variant shadow-sm p-5 space-y-5">
                {{-- Tanggal Lahir --}}
                <div class="flex flex-col gap-1.5">
                    <label class="font-body-sm text-body-sm font-semibold text-on-surface">Tanggal Lahir</label>
                    <input type="date" wire:model="date_of_birth"
                        class="w-full px-4 py-3 rounded-xl border border-border-subtle bg-surface text-on-surface font-body-lg text-body-lg focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                    @error('date_of_birth')
                        <p class="font-body-sm text-body-sm text-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Pendidikan Terakhir --}}
                <div class="flex flex-col gap-1.5">
                    <label class="font-body-sm text-body-sm font-semibold text-on-surface">Pendidikan Terakhir</label>
                    <div x-data="{ open: false }" class="relative">
                        <button type="button" @click="open = !open" @keydown.escape="open = false"
                            class="w-full flex items-center justify-between px-4 py-3 rounded-xl border bg-surface text-body-lg focus:outline-none focus:ring-2 focus:ring-primary/30 transition-all cursor-pointer
                                {{ $errors->first('education_level') ? 'border-error' : 'border-border-subtle' }}
                                {{ $education_level ? 'text-on-surface' : 'text-secondary' }}"
                            :class="open && 'border-primary ring-2 ring-primary/30'">
                            <span>{{ $education_level ? \App\Data\OnboardingData::EDUCATION_LEVELS[$education_level] : '-- Pilih Pendidikan --' }}</span>
                            <span class="material-symbols-outlined text-on-surface-variant transition-transform" :class="open && 'rotate-180'">unfold_more</span>
                        </button>

                        <div x-show="open" @click.away="open = false"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute z-20 mt-1 w-full bg-surface rounded-xl border border-outline-variant shadow-lg overflow-hidden">
                            <div class="max-h-60 overflow-y-auto py-1">
                                <button type="button" wire:click="$set('education_level', '')" @click="open = false"
                                    class="w-full px-4 py-3 text-left text-body-lg text-secondary hover:bg-surface-container transition-colors cursor-pointer">
                                    -- Pilih Pendidikan --
                                </button>
                                @foreach (\App\Data\OnboardingData::EDUCATION_LEVELS as $value => $label)
                                    <button type="button" wire:click="$set('education_level', '{{ $value }}')" @click="open = false"
                                        class="w-full px-4 py-3 text-left text-body-lg transition-colors cursor-pointer
                                            {{ $education_level === $value ? 'bg-primary-container/20 text-primary font-semibold' : 'text-on-surface hover:bg-surface-container' }}">
                                        {{ $label }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @error('education_level')
                        <p class="font-body-sm text-body-sm text-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Jurusan (kondisional) --}}
                @if ($this->showMajorField && !empty($this->availableMajors))
                    <div class="flex flex-col gap-1.5">
                        <label class="font-body-sm text-body-sm font-semibold text-on-surface">Jurusan</label>
                        <div x-data="{ open: false }" class="relative">
                            <button type="button" @click="open = !open" @keydown.escape="open = false"
                                class="w-full flex items-center justify-between px-4 py-3 rounded-xl border bg-surface text-body-lg focus:outline-none focus:ring-2 focus:ring-primary/30 transition-all cursor-pointer
                                    {{ $errors->first('education_major') ? 'border-error' : 'border-border-subtle' }}
                                    {{ $education_major ? 'text-on-surface' : 'text-secondary' }}"
                                :class="open && 'border-primary ring-2 ring-primary/30'">
                                <span>{{ $education_major ?: '-- Pilih Jurusan --' }}</span>
                                <span class="material-symbols-outlined text-on-surface-variant transition-transform" :class="open && 'rotate-180'">unfold_more</span>
                            </button>

                            <div x-show="open" @click.away="open = false"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute z-20 mt-1 w-full bg-surface rounded-xl border border-outline-variant shadow-lg overflow-hidden">
                                <div class="max-h-60 overflow-y-auto py-1">
                                    <button type="button" wire:click="$set('education_major', '')" @click="open = false"
                                        class="w-full px-4 py-3 text-left text-body-lg text-secondary hover:bg-surface-container transition-colors cursor-pointer">
                                        -- Pilih Jurusan --
                                    </button>
                                    @foreach ($this->availableMajors as $value => $label)
                                        <button type="button" wire:click="$set('education_major', '{{ $value }}')" @click="open = false"
                                            class="w-full px-4 py-3 text-left text-body-lg transition-colors cursor-pointer
                                                {{ $education_major === $value ? 'bg-primary-container/20 text-primary font-semibold' : 'text-on-surface hover:bg-surface-container' }}">
                                            {{ $label }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @error('education_major')
                            <p class="font-body-sm text-body-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>
                @elseif ($this->showMajorField && empty($this->skill_categories))
                    <div class="bg-surface-container-low rounded-xl p-3 border border-border-subtle">
                        <p class="font-body-sm text-body-sm text-secondary">Pilih kategori keahlian terlebih dahulu di langkah sebelumnya agar jurusan yang relevan dapat ditampilkan.</p>
                    </div>
                @endif
            </div>

            {{-- Card 2: Preferensi Kerja --}}
            <div class="bg-surface rounded-2xl border border-outline-variant shadow-sm p-5 space-y-5">
                {{-- Tipe Pekerjaan --}}
                <div class="flex flex-col gap-2">
                    <label class="font-body-sm text-body-sm font-semibold text-on-surface">Tipe Pekerjaan yang Dicari</label>
                    <p class="font-body-sm text-body-sm text-on-surface-variant -mt-1">Pilih tipe pekerjaan yang kamu inginkan (bisa lebih dari satu).</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach (\App\Data\OnboardingData::JOB_TYPES as $value => $label)
                            <button type="button" wire:click="toggleArray('job_types', '{{ $value }}')"
                                class="px-4 py-2 rounded-full border text-sm font-medium transition-all cursor-pointer
                                    {{ in_array($value, $job_types) ? 'bg-primary-container/20 text-primary border-primary' : 'bg-surface text-secondary border-outline-variant hover:border-primary/50' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                    @error('job_types')
                        <p class="font-body-sm text-body-sm text-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Lokasi --}}
                <div class="flex flex-col gap-2">
                    <label class="font-body-sm text-body-sm font-semibold text-on-surface">Lokasi yang Diminati</label>
                    <p class="font-body-sm text-body-sm text-on-surface-variant -mt-1">Pilih lokasi tempat kamu ingin bekerja — area Jabodetabek (bisa lebih dari satu).</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach (\App\Data\OnboardingData::LOCATIONS as $value => $label)
                            <button type="button" wire:click="toggleArray('preferred_locations', '{{ $value }}')"
                                class="px-3 py-1.5 rounded-full border text-label-caps font-medium transition-all cursor-pointer
                                    {{ in_array($value, $preferred_locations) ? 'bg-primary-container/20 text-primary border-primary' : 'bg-surface text-secondary border-outline-variant hover:border-primary/50' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                    @error('preferred_locations')
                        <p class="font-body-sm text-body-sm text-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>
    @endif

    {{-- Step 7: Review --}}
    @if ($step === 7)
        <section class="flex flex-col gap-3">
            <div class="flex flex-col">
                <h2 class="text-h2 font-h2 text-on-surface">Konfirmasi Data</h2>
                <p class="font-body-sm text-body-sm text-on-surface-variant">Periksa kembali data kamu sebelum disimpan. AI akan menggunakan data ini untuk mencari pekerjaan yang cocok.</p>
            </div>

            <div class="bg-surface rounded-2xl border border-outline-variant shadow-sm overflow-hidden">
                {{-- Section: Profil Diri --}}
                <div class="p-5 space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-xl">person</span>
                        <span class="font-label-caps text-label-caps text-primary font-semibold tracking-wider">PROFIL DIRI</span>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1">
                            <span class="font-label-caps text-label-caps text-outline">Kondisi Disabilitas</span>
                            <span class="font-body-lg text-body-lg text-on-surface font-medium">Tunarungu (Tuli/Deaf)</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="font-label-caps text-label-caps text-outline">Tingkat Pendengaran</span>
                            <span class="font-body-lg text-body-lg text-on-surface font-medium">{{ \App\Data\OnboardingData::HEARING_LEVELS[$hearing_level] ?? $hearing_level }}</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="font-label-caps text-label-caps text-outline">Tanggal Lahir</span>
                            <span class="font-body-lg text-body-lg text-on-surface font-medium">{{ $date_of_birth ? \Carbon\Carbon::parse($date_of_birth)->format('d F Y') : '-' }}</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="font-label-caps text-label-caps text-outline">Pendidikan</span>
                            <span class="font-body-lg text-body-lg text-on-surface font-medium">
                                {{ \App\Data\OnboardingData::EDUCATION_LEVELS[$education_level] ?? $education_level }}
                                @if ($education_major)
                                    — {{ $education_major }}
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <hr class="border-border-subtle">

                {{-- Section: Preferensi --}}
                <div class="p-5 space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-xl">tune</span>
                        <span class="font-label-caps text-label-caps text-primary font-semibold tracking-wider">PREFERENSI</span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="font-label-caps text-label-caps text-outline">Preferensi Komunikasi</span>
                        <div class="flex flex-wrap gap-1.5 mt-0.5">
                            @foreach ($communication_preference as $value)
                                <span class="inline-flex px-3 py-1 rounded-full bg-surface-container text-on-surface text-label-caps font-medium border border-border-subtle">{{ \App\Data\OnboardingData::COMMUNICATION_PREFERENCES[$value] ?? $value }}</span>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="font-label-caps text-label-caps text-outline">Lingkungan Kerja & Akomodasi</span>
                        <div class="flex flex-wrap gap-1.5 mt-0.5">
                            @foreach ($work_environment as $value)
                                <span class="inline-flex px-3 py-1 rounded-full bg-surface-container text-on-surface text-label-caps font-medium border border-border-subtle">{{ \App\Data\OnboardingData::WORK_ENVIRONMENTS[$value] ?? $value }}</span>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="font-label-caps text-label-caps text-outline">Tipe Pekerjaan</span>
                        <div class="flex flex-wrap gap-1.5 mt-0.5">
                            @foreach ($job_types as $value)
                                <span class="inline-flex px-3 py-1 rounded-full bg-surface-container text-on-surface text-label-caps font-medium border border-border-subtle">{{ \App\Data\OnboardingData::JOB_TYPES[$value] ?? $value }}</span>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="font-label-caps text-label-caps text-outline">Lokasi yang Diminati</span>
                        <div class="flex flex-wrap gap-1.5 mt-0.5">
                            @foreach ($preferred_locations as $value)
                                <span class="inline-flex px-3 py-1 rounded-full bg-surface-container text-on-surface text-label-caps font-medium border border-border-subtle">{{ \App\Data\OnboardingData::LOCATIONS[$value] ?? $value }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <hr class="border-border-subtle">

                {{-- Section: Keahlian --}}
                <div class="p-5 space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-xl">code</span>
                        <span class="font-label-caps text-label-caps text-primary font-semibold tracking-wider">KEAHLIAN</span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="font-label-caps text-label-caps text-outline">Kategori & Sub-Keahlian</span>
                        <div class="flex flex-col gap-3 mt-1">
                            @foreach (\App\Data\OnboardingData::getSelectedSubSkills($skill_categories, $sub_skills) as $categoryLabel => $subLabels)
                                <div>
                                    <span class="font-body-sm text-body-sm font-semibold text-on-surface">{{ $categoryLabel }}</span>
                                    <div class="flex flex-wrap gap-1.5 mt-1">
                                        @foreach ($subLabels as $subLabel)
                                            <span class="inline-flex px-3 py-1 rounded-full bg-surface-container text-on-surface text-label-caps font-medium border border-border-subtle">{{ $subLabel }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- Navigation Buttons --}}
    <div class="flex gap-3 mt-4">
        @if ($step > 1)
            <button type="button" wire:click="back"
                class="flex-1 py-3.5 rounded-xl border border-outline-variant bg-surface text-on-surface font-body-lg text-body-lg font-semibold shadow-sm hover:bg-surface-container hover:border-outline transition-all min-h-touch-target-min cursor-pointer">
                Kembali
            </button>
        @endif

        @if ($step < \App\Livewire\Onboarding::TOTAL_STEPS)
            <button type="button" wire:click="next"
                class="flex-1 py-4 bg-primary text-on-primary font-bold text-body-lg rounded-xl shadow-sm hover:brightness-110 active:scale-[0.98] transition-all min-h-touch-target-min cursor-pointer">
                Lanjut
            </button>
        @else
            <button type="button" wire:click="save"
                class="flex-1 py-4 bg-primary text-on-primary font-bold text-body-lg rounded-xl shadow-sm hover:brightness-110 active:scale-[0.98] transition-all min-h-touch-target-min cursor-pointer">
                Simpan & Mulai Pencocokan
            </button>
        @endif
    </div>
</div>
