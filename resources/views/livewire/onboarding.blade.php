<div class="flex flex-col gap-stack-lg" x-data="{}">
    <div class="text-center">
        <h1 class="font-h2 text-h2 text-on-surface">Lengkapi Profil</h1>
        <p class="font-body-sm text-body-sm text-secondary mt-1">Isi data dirimu untuk rekomendasi pekerjaan yang tepat.</p>
    </div>

    {{-- Progress Bar --}}
    <div class="flex items-center gap-1 px-2">
        @foreach (range(1, \App\Livewire\Onboarding::TOTAL_STEPS) as $i)
            <div class="flex-1 flex flex-col items-center gap-1">
                <div class="flex items-center w-full">
                    <div class="h-1 flex-1 rounded-full {{ $i <= $step ? 'bg-primary' : 'bg-surface-dim' }}"></div>
                </div>
                <span class="font-label-caps text-label-caps {{ $i == $step ? 'text-primary font-semibold' : ($i < $step ? 'text-primary' : 'text-secondary') }}">
                    {{ $i }}
                </span>
            </div>
        @endforeach
    </div>

    <p class="font-body-sm text-body-sm text-secondary text-center -mt-2">
        Langkah {{ $step }} dari {{ \App\Livewire\Onboarding::TOTAL_STEPS }}
    </p>

    {{-- Step 1: Kondisi Disabilitas --}}
    @if ($step === 1)
        <section class="flex flex-col gap-3">
            <h2 class="font-h3 text-h3 text-on-surface">Kondisi Disabilitas</h2>
            <p class="font-body-sm text-body-sm text-secondary">Pilih kondisi disabilitas kamu.</p>

            <div class="bg-primary-container/20 rounded-xl p-3 border border-primary/20 flex items-start gap-2 mb-1">
                <span class="material-symbols-outlined text-primary text-[18px] shrink-0 mt-0.5">info</span>
                <p class="font-body-sm text-body-sm text-on-primary-container">Equaly saat ini fokus untuk teman <strong>Tunarungu</strong>. Dukungan untuk disabilitas lain akan hadir di pengembangan selanjutnya.</p>
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
                            <span class="font-label-caps text-label-caps text-outline/60">Coming Soon</span>
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
            <h2 class="font-h3 text-h3 text-on-surface">Tingkat Pendengaran</h2>
            <p class="font-body-sm text-body-sm text-secondary">Pilih tingkat pendengaran yang paling sesuai dengan kondisi kamu.</p>

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
            <h2 class="font-h3 text-h3 text-on-surface">Preferensi Komunikasi</h2>
            <p class="font-body-sm text-body-sm text-secondary">Pilih metode komunikasi yang kamu gunakan (bisa lebih dari satu).</p>

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
            <h2 class="font-h3 text-h3 text-on-surface">Lingkungan Kerja & Akomodasi</h2>
            <p class="font-body-sm text-body-sm text-secondary">Pilih lingkungan kerja dan akomodasi yang kamu butuhkan (bisa lebih dari satu).</p>

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
        <section class="flex flex-col gap-4">
            <div>
                <h2 class="font-h3 text-h3 text-on-surface">Kategori Keahlian</h2>
                <p class="font-body-sm text-body-sm text-secondary">Pilih bidang keahlian yang kamu kuasai (bisa lebih dari satu).</p>
            </div>

            <div class="flex flex-wrap gap-2">
                @foreach (\App\Data\OnboardingData::SKILL_CATEGORIES as $value => $label)
                    <button type="button" wire:click="toggleSkillCategory('{{ $value }}')"
                        class="font-label-caps text-label-caps px-3 py-2 rounded-lg border transition-all cursor-pointer
                            {{ in_array($value, $skill_categories) ? 'bg-primary-container/40 text-primary border-primary/30' : 'bg-surface-container text-on-surface border-border-subtle hover:border-primary/50' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            @error('skill_categories')
                <p class="font-body-sm text-body-sm text-error">{{ $message }}</p>
            @enderror

            @if (!empty($skill_categories))
                <div class="border-t border-border-subtle pt-4">
                    <h3 class="font-body-lg text-body-lg font-semibold text-on-surface mb-3">Sub-Keahlian</h3>
                    <p class="font-body-sm text-body-sm text-secondary mb-3">Pilih sub-keahlian yang lebih spesifik (bisa lebih dari satu).</p>

                    @foreach ($this->subSkillsForCategory as $categoryKey => $subs)
                        <div class="mb-4">
                            <p class="font-label-caps text-label-caps text-primary font-semibold mb-2">{{ \App\Data\OnboardingData::SKILL_CATEGORIES[$categoryKey] ?? $categoryKey }}</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($subs as $subValue => $subLabel)
                                    <button type="button" wire:click="toggleArray('sub_skills', '{{ $subValue }}')"
                                        class="font-body-sm text-body-sm px-3 py-1.5 rounded-md border transition-all cursor-pointer
                                            {{ in_array($subValue, $sub_skills) ? 'bg-primary/10 text-primary border-primary/30' : 'bg-surface text-secondary border-border-subtle hover:border-primary/50' }}">
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
        <section class="flex flex-col gap-5">
            <div>
                <h2 class="font-h3 text-h3 text-on-surface">Data Diri & Preferensi Kerja</h2>
                <p class="font-body-sm text-body-sm text-secondary">Lengkapi informasi berikut untuk rekomendasi yang lebih akurat.</p>
            </div>

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
                <select wire:model.live="education_level"
                    class="w-full px-4 py-3 rounded-xl border border-border-subtle bg-surface text-on-surface font-body-lg text-body-lg focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                    <option value="">-- Pilih Pendidikan --</option>
                    @foreach (\App\Data\OnboardingData::EDUCATION_LEVELS as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('education_level')
                    <p class="font-body-sm text-body-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Jurusan (kondisional) --}}
            @if ($this->showMajorField && !empty($this->availableMajors))
                <div class="flex flex-col gap-1.5">
                    <label class="font-body-sm text-body-sm font-semibold text-on-surface">Jurusan</label>
                    <select wire:model="education_major"
                        class="w-full px-4 py-3 rounded-xl border border-border-subtle bg-surface text-on-surface font-body-lg text-body-lg focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                        <option value="">-- Pilih Jurusan --</option>
                        @foreach ($this->availableMajors as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('education_major')
                        <p class="font-body-sm text-body-sm text-error">{{ $message }}</p>
                    @enderror
                </div>
            @elseif ($this->showMajorField && empty($this->skill_categories))
                <div class="bg-surface-container-low rounded-xl p-3 border border-border-subtle">
                    <p class="font-body-sm text-body-sm text-secondary">Pilih kategori keahlian terlebih dahulu di langkah sebelumnya agar jurusan yang relevan dapat ditampilkan.</p>
                </div>
            @endif

            {{-- Tipe Pekerjaan --}}
            <div class="flex flex-col gap-2">
                <label class="font-body-sm text-body-sm font-semibold text-on-surface">Tipe Pekerjaan yang Dicari</label>
                <p class="font-body-sm text-body-sm text-secondary -mt-1">Pilih tipe pekerjaan yang kamu inginkan (bisa lebih dari satu).</p>
                <div class="flex flex-wrap gap-2">
                    @foreach (\App\Data\OnboardingData::JOB_TYPES as $value => $label)
                        <button type="button" wire:click="toggleArray('job_types', '{{ $value }}')"
                            class="font-label-caps text-label-caps px-3 py-2 rounded-lg border transition-all cursor-pointer
                                {{ in_array($value, $job_types) ? 'bg-primary-container/40 text-primary border-primary/30' : 'bg-surface-container text-on-surface border-border-subtle hover:border-primary/50' }}">
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
                <p class="font-body-sm text-body-sm text-secondary -mt-1">Pilih lokasi tempat kamu ingin bekerja — area Jabodetabek (bisa lebih dari satu).</p>
                <div class="flex flex-wrap gap-2">
                    @foreach (\App\Data\OnboardingData::LOCATIONS as $value => $label)
                        <button type="button" wire:click="toggleArray('preferred_locations', '{{ $value }}')"
                            class="font-label-caps text-label-caps px-3 py-2 rounded-lg border transition-all cursor-pointer
                                {{ in_array($value, $preferred_locations) ? 'bg-primary-container/40 text-primary border-primary/30' : 'bg-surface-container text-on-surface border-border-subtle hover:border-primary/50' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
                @error('preferred_locations')
                    <p class="font-body-sm text-body-sm text-error">{{ $message }}</p>
                @enderror
            </div>
        </section>
    @endif

    {{-- Step 7: Review --}}
    @if ($step === 7)
        <section class="flex flex-col gap-4">
            <div>
                <h2 class="font-h3 text-h3 text-on-surface">Konfirmasi Data</h2>
                <p class="font-body-sm text-body-sm text-secondary">Periksa kembali data kamu sebelum disimpan. AI akan menggunakan data ini untuk mencari pekerjaan yang cocok.</p>
            </div>

            <div class="bg-surface rounded-2xl border border-border-subtle divide-y divide-border-subtle overflow-hidden">
                <div class="p-4 flex flex-col gap-1">
                    <span class="font-label-caps text-label-caps text-outline">Kondisi Disabilitas</span>
                    <span class="font-body-lg text-body-lg text-on-surface">Tunarungu (Tuli/Deaf)</span>
                </div>

                <div class="p-4 flex flex-col gap-1">
                    <span class="font-label-caps text-label-caps text-outline">Tingkat Pendengaran</span>
                    <span class="font-body-lg text-body-lg text-on-surface">{{ \App\Data\OnboardingData::HEARING_LEVELS[$hearing_level] ?? $hearing_level }}</span>
                </div>

                <div class="p-4 flex flex-col gap-1">
                    <span class="font-label-caps text-label-caps text-outline">Preferensi Komunikasi</span>
                    <div class="flex flex-wrap gap-1.5 mt-0.5">
                        @foreach ($communication_preference as $value)
                            <span class="bg-surface-container text-on-surface font-label-caps text-label-caps px-2 py-1 rounded-md border border-border-subtle">{{ \App\Data\OnboardingData::COMMUNICATION_PREFERENCES[$value] ?? $value }}</span>
                        @endforeach
                    </div>
                </div>

                <div class="p-4 flex flex-col gap-1">
                    <span class="font-label-caps text-label-caps text-outline">Lingkungan Kerja & Akomodasi</span>
                    <div class="flex flex-wrap gap-1.5 mt-0.5">
                        @foreach ($work_environment as $value)
                            <span class="bg-surface-container text-on-surface font-label-caps text-label-caps px-2 py-1 rounded-md border border-border-subtle">{{ \App\Data\OnboardingData::WORK_ENVIRONMENTS[$value] ?? $value }}</span>
                        @endforeach
                    </div>
                </div>

                <div class="p-4 flex flex-col gap-1">
                    <span class="font-label-caps text-label-caps text-outline">Kategori Keahlian</span>
                    <div class="flex flex-col gap-2 mt-1">
                        @foreach (\App\Data\OnboardingData::getSelectedSubSkills($skill_categories, $sub_skills) as $categoryLabel => $subLabels)
                            <div>
                                <span class="font-body-sm text-body-sm font-semibold text-on-surface">{{ $categoryLabel }}</span>
                                <div class="flex flex-wrap gap-1 mt-1">
                                    @foreach ($subLabels as $subLabel)
                                        <span class="bg-surface-container text-on-surface font-label-caps text-label-caps px-2 py-0.5 rounded-md border border-border-subtle">{{ $subLabel }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="p-4 flex flex-col gap-1">
                    <span class="font-label-caps text-label-caps text-outline">Tanggal Lahir</span>
                    <span class="font-body-lg text-body-lg text-on-surface">{{ $date_of_birth ? \Carbon\Carbon::parse($date_of_birth)->format('d F Y') : '-' }}</span>
                </div>

                <div class="p-4 flex flex-col gap-1">
                    <span class="font-label-caps text-label-caps text-outline">Pendidikan</span>
                    <span class="font-body-lg text-body-lg text-on-surface">
                        {{ \App\Data\OnboardingData::EDUCATION_LEVELS[$education_level] ?? $education_level }}
                        @if ($education_major)
                            — {{ $education_major }}
                        @endif
                    </span>
                </div>

                <div class="p-4 flex flex-col gap-1">
                    <span class="font-label-caps text-label-caps text-outline">Tipe Pekerjaan</span>
                    <div class="flex flex-wrap gap-1.5 mt-0.5">
                        @foreach ($job_types as $value)
                            <span class="bg-surface-container text-on-surface font-label-caps text-label-caps px-2 py-1 rounded-md border border-border-subtle">{{ \App\Data\OnboardingData::JOB_TYPES[$value] ?? $value }}</span>
                        @endforeach
                    </div>
                </div>

                <div class="p-4 flex flex-col gap-1">
                    <span class="font-label-caps text-label-caps text-outline">Lokasi yang Diminati</span>
                    <div class="flex flex-wrap gap-1.5 mt-0.5">
                        @foreach ($preferred_locations as $value)
                            <span class="bg-surface-container text-on-surface font-label-caps text-label-caps px-2 py-1 rounded-md border border-border-subtle">{{ \App\Data\OnboardingData::LOCATIONS[$value] ?? $value }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- Navigation Buttons --}}
    <div class="flex gap-3 mt-4">
        @if ($step > 1)
            <button type="button" wire:click="back"
                class="flex-1 py-3.5 rounded-xl border-2 border-border-subtle text-on-surface font-body-lg text-body-lg font-semibold hover:bg-surface-container transition-colors min-h-[48px]">
                Kembali
            </button>
        @endif

        @if ($step < \App\Livewire\Onboarding::TOTAL_STEPS)
            <button type="button" wire:click="next"
                class="flex-1 py-3.5 rounded-xl bg-primary text-on-primary font-body-lg text-body-lg font-semibold hover:bg-primary-container transition-colors min-h-[48px]">
                Lanjut
            </button>
        @else
            <button type="button" wire:click="save"
                class="flex-1 py-3.5 rounded-xl bg-primary text-on-primary font-body-lg text-body-lg font-semibold hover:bg-primary-container transition-colors min-h-[48px]">
                Simpan & Mulai Pencocokan
            </button>
        @endif
    </div>
</div>
