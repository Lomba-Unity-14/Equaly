<div class="flex flex-col gap-stack-lg">
    <div class="text-center">
        <h1 class="font-h2 text-h2 text-on-surface">Lengkapi Profil</h1>
        <p class="font-body-sm text-body-sm text-secondary mt-1">Isi data dirimu untuk rekomendasi yang tepat.</p>
    </div>

    <div class="flex items-center gap-1 px-2">
        @foreach (range(1, 4) as $i)
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
        Langkah {{ $step }} dari 4
    </p>

    @if ($step === 1)
        <section class="flex flex-col gap-3">
            <h2 class="font-h3 text-h3 text-on-surface">Kondisi Disabilitas</h2>
            <p class="font-body-sm text-body-sm text-secondary">Pilih kondisi disabilitas utama kamu (bisa lebih dari satu).</p>

            <div class="flex flex-col gap-3">
                @foreach (['tunarungu' => 'Tunarungu', 'tunadaksa' => 'Tunadaksa', 'netra' => 'Netra/Low Vision', 'lainnya' => 'Tidak ada/Lainnya'] as $value => $label)
                    <button type="button" wire:click="toggleCondition('disability_condition', '{{ $value }}')"
                        class="w-full text-left p-4 rounded-2xl border-2 transition-all duration-200 flex items-center gap-3 cursor-pointer
                            {{ in_array($value, $disability_condition) ? 'border-primary bg-primary-container/30' : 'border-border-subtle bg-surface hover:border-primary/50' }}">
                        <span class="material-symbols-outlined text-[22px] shrink-0
                            {{ in_array($value, $disability_condition) ? 'text-primary' : 'text-secondary' }}"
                            style="font-variation-settings: 'FILL' {{ in_array($value, $disability_condition) ? 1 : 0 }};">
                            {{ in_array($value, $disability_condition) ? 'check_box' : 'check_box_outline_blank' }}
                        </span>
                        <span class="font-body-lg text-body-lg text-on-surface">{{ $label }}</span>
                    </button>
                @endforeach
            </div>

            @error('disability_condition')
                <p class="font-body-sm text-body-sm text-error">{{ $message }}</p>
            @enderror
        </section>
    @endif

    @if ($step === 2)
        <section class="flex flex-col gap-3">
            <h2 class="font-h3 text-h3 text-on-surface">Preferensi Komunikasi</h2>
            <p class="font-body-sm text-body-sm text-secondary">Bagaimana preferensi komunikasi kamu? (bisa lebih dari satu).</p>

            <div class="flex flex-col gap-3">
                @foreach (['full_teks' => 'Full Teks', 'bibir' => 'Bisa membaca gerak bibir', 'bahasa_isyarat' => 'Butuh juru bahasa isyarat'] as $value => $label)
                    <button type="button" wire:click="toggleCondition('communication_preference', '{{ $value }}')"
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

    @if ($step === 3)
        <section class="flex flex-col gap-3">
            <h2 class="font-h3 text-h3 text-on-surface">Lingkungan Kerja</h2>
            <p class="font-body-sm text-body-sm text-secondary">Lingkungan kerja ideal yang kamu inginkan? (bisa lebih dari satu).</p>

            <div class="flex flex-col gap-3">
                @foreach (['remote' => 'Remote', 'hybrid' => 'Hybrid', 'onsite' => 'On-site'] as $value => $label)
                    <button type="button" wire:click="toggleCondition('work_environment', '{{ $value }}')"
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

    @if ($step === 4)
        <section class="flex flex-col gap-3">
            <h2 class="font-h3 text-h3 text-on-surface">Keahlian Utama</h2>
            <p class="font-body-sm text-body-sm text-secondary">Tambahkan keahlian yang kamu kuasai.</p>

            <div class="flex flex-wrap gap-2 min-h-10">
                @forelse ($skills as $index => $skill)
                    <span class="inline-flex items-center gap-1.5 bg-primary-container/40 text-on-surface font-body-sm text-body-sm px-2.5 py-1.5 rounded-md border border-primary/20">
                        {{ $skill }}
                        <button type="button" wire:click="removeSkill({{ $index }})" class="text-secondary hover:text-error transition-colors">
                            <span class="material-symbols-outlined text-[16px]">close</span>
                        </button>
                    </span>
                @empty
                    <p class="font-body-sm text-body-sm text-secondary w-full text-center py-4">Belum ada keahlian ditambahkan.</p>
                @endforelse
            </div>

            <div class="flex gap-2">
                <input type="text" wire:model="newSkill" wire:keydown.enter.prevent="addSkill"
                    placeholder="Cth: Figma, Laravel, Copywriting"
                    class="flex-1 px-4 py-3 rounded-xl border border-border-subtle bg-surface text-on-surface font-body-lg text-body-lg placeholder:text-secondary focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                <button type="button" wire:click="addSkill"
                    class="px-4 py-3 rounded-xl bg-primary text-on-primary font-body-lg text-body-lg font-semibold hover:bg-primary-container transition-colors min-h-[48px] flex items-center justify-center">
                    Tambah
                </button>
            </div>

            @error('skills')
                <p class="font-body-sm text-body-sm text-error">{{ $message }}</p>
            @enderror
        </section>
    @endif

    <div class="flex gap-3 mt-4">
        @if ($step > 1)
            <button type="button" wire:click="back"
                class="flex-1 py-3.5 rounded-xl border-2 border-border-subtle text-on-surface font-body-lg text-body-lg font-semibold hover:bg-surface-container transition-colors min-h-[48px]">
                Kembali
            </button>
        @endif

        @if ($step < 4)
            <button type="button" wire:click="next"
                class="flex-1 py-3.5 rounded-xl bg-primary text-on-primary font-body-lg text-body-lg font-semibold hover:bg-primary-container transition-colors min-h-[48px]">
                Lanjut
            </button>
        @else
            <button type="button" wire:click="save"
                class="flex-1 py-3.5 rounded-xl bg-primary text-on-primary font-body-lg text-body-lg font-semibold hover:bg-primary-container transition-colors min-h-[48px]">
                Selesai
            </button>
        @endif
    </div>
</div>
