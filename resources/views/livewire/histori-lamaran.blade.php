<div class="flex flex-col gap-stack-lg">
    <div>
        <h1 class="font-h1 text-h1 text-text-primary mb-1">Histori Lamaran</h1>
        <p class="font-body-sm text-body-sm text-text-secondary">Lacak lamaran dan berikan ulasan perusahaan.</p>
    </div>

    @if($reviewing && $reviewingApp)
        <div class="bg-surface rounded-2xl border border-border-subtle shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="font-h2 text-h2 text-text-primary">Ulasan Perusahaan</h2>
                    <p class="font-body-sm text-body-sm text-text-secondary">{{ $reviewingApp->company_name ?? 'Perusahaan' }}</p>
                </div>
                <button wire:click="cancelReview" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-surface-container transition-colors">
                    <span class="material-symbols-outlined text-text-secondary">close</span>
                </button>
            </div>

            <div class="flex gap-1 mb-6">
                @foreach([1,2,3,4] as $i)
                    <div class="h-1.5 flex-1 rounded-full {{ $step >= $i ? 'bg-primary' : 'bg-surface-container-high' }}"></div>
                @endforeach
            </div>

            @if($step === 1)
                <h3 class="font-body-lg text-body-lg font-semibold text-text-primary mb-2">Apakah anda diterima di perusahaan ini?</h3>
                <p class="font-body-sm text-body-sm text-text-secondary mb-4">Pilih salah satu.</p>
                <div class="flex flex-col gap-3">
                    <button wire:click="is_accepted = true; next()" class="w-full text-left p-4 rounded-2xl border-2 border-border-subtle hover:border-primary/50 transition-all bg-surface">
                        <span class="font-body-lg text-body-lg font-semibold text-text-primary flex items-center gap-3">
                            <span class="material-symbols-outlined text-primary">check_circle</span> Ya, diterima
                        </span>
                    </button>
                    <button wire:click="is_accepted = false; next()" class="w-full text-left p-4 rounded-2xl border-2 border-border-subtle hover:border-primary/50 transition-all bg-surface">
                        <span class="font-body-lg text-body-lg font-semibold text-text-primary flex items-center gap-3">
                            <span class="material-symbols-outlined text-error">cancel</span> Tidak diterima
                        </span>
                    </button>
                </div>

            @elseif($step === 2)
                <h3 class="font-body-lg text-body-lg font-semibold text-text-primary mb-2">Apakah ada karyawan disabilitas lain di perusahaan ini?</h3>
                <p class="font-body-sm text-body-sm text-text-secondary mb-4">Sepengetahuan anda.</p>
                <div class="flex flex-col gap-3">
                    <button wire:click="has_disability_employees = 1; next()" class="w-full text-left p-4 rounded-2xl border-2 border-border-subtle hover:border-primary/50 transition-all bg-surface">
                        <span class="font-body-lg text-body-lg font-semibold text-text-primary flex items-center gap-3">
                            <span class="material-symbols-outlined text-primary">groups</span> Ya, ada
                        </span>
                    </button>
                    <button wire:click="has_disability_employees = 0; next()" class="w-full text-left p-4 rounded-2xl border-2 border-border-subtle hover:border-primary/50 transition-all bg-surface">
                        <span class="font-body-lg text-body-lg font-semibold text-text-primary flex items-center gap-3">
                            <span class="material-symbols-outlined text-error">person_off</span> Tidak ada
                        </span>
                    </button>
                    <button wire:click="has_disability_employees = 2; next()" class="w-full text-left p-4 rounded-2xl border-2 border-border-subtle hover:border-primary/50 transition-all bg-surface">
                        <span class="font-body-lg text-body-lg font-semibold text-text-primary flex items-center gap-3">
                            <span class="material-symbols-outlined text-outline">help</span> Tidak tahu
                        </span>
                    </button>
                </div>

            @elseif($step === 3)
                <h3 class="font-body-lg text-body-lg font-semibold text-text-primary mb-2">Apakah perusahaan ini ramah untuk penyandang disabilitas?</h3>
                <p class="font-body-sm text-body-sm text-text-secondary mb-4">Berdasarkan pengalaman anda secara keseluruhan.</p>
                <div class="flex flex-col gap-3">
                    <button wire:click="is_friendly = true; next()" class="w-full text-left p-4 rounded-2xl border-2 border-border-subtle hover:border-primary/50 transition-all bg-surface">
                        <span class="font-body-lg text-body-lg font-semibold text-text-primary flex items-center gap-3">
                            <span class="material-symbols-outlined text-primary">sentiment_satisfied</span> Ya, ramah
                        </span>
                    </button>
                    <button wire:click="is_friendly = false; next()" class="w-full text-left p-4 rounded-2xl border-2 border-border-subtle hover:border-primary/50 transition-all bg-surface">
                        <span class="font-body-lg text-body-lg font-semibold text-text-primary flex items-center gap-3">
                            <span class="material-symbols-outlined text-error">sentiment_dissatisfied</span> Tidak ramah
                        </span>
                    </button>
                </div>

            @elseif($step === 4)
                <h3 class="font-body-lg text-body-lg font-semibold text-text-primary mb-2">Ceritakan pengalaman anda</h3>
                <p class="font-body-sm text-body-sm text-text-secondary mb-4">Bagikan pengalaman melamar di perusahaan ini agar membantu komunitas.</p>
                <textarea wire:model="experience" rows="5"
                    class="w-full p-4 rounded-2xl border-2 border-border-subtle bg-surface text-text-primary font-body-lg text-body-lg placeholder:text-secondary focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all"
                    placeholder="Ceritakan pengalamanmu..."></textarea>
            @endif

            <div class="flex gap-3 justify-between mt-6 pt-4 border-t border-border-subtle">
                <div>
                    @if($step > 1)
                        <button wire:click="back()" class="px-4 py-3 rounded-xl border border-border-subtle text-on-surface font-body-sm text-body-sm hover:bg-surface-container transition-colors min-h-[48px] flex items-center">
                            <span class="material-symbols-outlined text-[18px] mr-1">arrow_back</span> Sebelumnya
                        </button>
                    @endif
                </div>
                <div>
                    @if($step === 4)
                        <button wire:click="saveReview" class="px-6 py-3 rounded-xl bg-primary text-on-primary font-body-sm text-body-sm font-semibold hover:bg-primary-fixed-variant transition-colors min-h-[48px] flex items-center">
                            Simpan Ulasan
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <section class="flex flex-col gap-stack-md">
        @forelse($applications as $app)
            @php $job = $app->jobVacancyData; @endphp
            <div class="bg-surface rounded-2xl border border-border-subtle shadow-sm p-4">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-surface-container-high flex items-center justify-center shrink-0 overflow-hidden">
                        @if($job && $job->company_logo_url)
                            <img src="{{ $job->company_logo_url }}" alt="{{ $app->company_name }}" class="w-full h-full object-cover">
                        @else
                            <span class="material-symbols-outlined text-secondary">domain</span>
                        @endif
                    </div>
                    <div class="flex-1">
                        <h3 class="font-body-lg text-body-lg font-semibold text-text-primary">{{ $job->job_title ?? 'Lowongan' }}</h3>
                        <p class="font-body-sm text-body-sm text-text-secondary">{{ $app->company_name ?? 'Perusahaan Rahasia' }}</p>
                        <p class="font-label-caps text-label-caps text-outline mt-1">{{ $app->applied_at?->diffForHumans() ?? '-' }}</p>
                    </div>
                </div>

                @if($app->review)
                    <div class="bg-primary-container/20 rounded-xl p-3 flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary shrink-0" style="font-variation-settings: 'FILL' 1;">verified</span>
                        <div>
                            <p class="font-body-sm text-body-sm text-on-primary-container font-semibold">Ulasan sudah diberikan</p>
                            <p class="font-label-caps text-label-caps text-on-primary-container/70">
                                {{ $app->review->is_accepted ? 'Diterima' : 'Belum diterima' }} &middot;
                                {{ $app->review->is_friendly ? 'Ramah disabilitas' : 'Kurang ramah disabilitas' }}
                            </p>
                        </div>
                    </div>
                @else
                    <div class="bg-surface-container-low rounded-xl p-3">
                        <p class="font-body-sm text-body-sm text-text-primary font-semibold mb-3">Apakah anda telah melamar di sini?</p>
                        <div class="flex gap-2">
                            <button wire:click="deny({{ $app->id }})"
                                class="flex-1 py-2.5 rounded-xl border border-error/30 text-error font-label-caps text-label-caps hover:bg-error-container/30 transition-colors min-h-[44px] flex items-center justify-center">
                                Tidak
                            </button>
                            <button wire:click="startReview({{ $app->id }})"
                                class="flex-1 py-2.5 rounded-xl bg-primary text-on-primary font-label-caps text-label-caps hover:bg-primary-fixed-variant transition-colors min-h-[44px] flex items-center justify-center">
                                Ya, Saya Melamar
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center py-12">
                <div class="w-16 h-16 rounded-full bg-surface-container-high flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-[32px] text-outline">history</span>
                </div>
                <p class="font-body-lg text-body-lg text-text-secondary mb-1">Belum ada lamaran</p>
                <p class="font-body-sm text-body-sm text-outline">Lamaran akan muncul di sini setelah kamu klik Lamar.</p>
            </div>
        @endforelse
    </section>
</div>
