<div class ="flex flex-col gap-stack-lg">
    <div>
        <h1 class="font-h1 text-h1 text-text-primary mb-1">Halo, {{ $user->name }}</h1>
        <p class="font-body-sm text-body-sm text-text-secondary">Siap mencari peluang baru hari ini?</p>
    </div>

    <div class="relative drop-shadow-md">
        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
            <span class="material-symbols-outlined text-outline">search</span>
        </div>
        <input
            class="block w-full pl-12 pr-4 py-3 bg-surface border-2 border-border-subtle rounded-2xl text-body-lg font-body-lg text-text-primary placeholder-outline focus:ring-primary focus:border-primary min-h-[48px] shadow-sm transition-all focus:shadow-md focus:outline-none"
            placeholder="Cari pekerjaan inklusif..." type="text" />
    </div>

    <div
        class="bg-linear-to-br from-secondary-container to-primary-fixed-dim rounded-3xl p-6 relative overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.08)] border border-white/50 transform">
        <div class="relative z-10 w-2/3">
            <h2 class="font-h2 text-h2 text-on-secondary-container mb-4 font-bold">Tingkatkan skill UI/UX kamu ke 70%!
            </h2>
            <button
                class="bg-primary text-on-primary font-label-caps text-label-caps py-3 px-6 rounded-full hover:bg-primary-fixed-variant transition-all min-h-[48px] inline-flex items-center justify-center shadow-lg hover:shadow-xl active:scale-95">
                Mulai Belajar
            </button>
        </div>
        <!-- Decorative Element -->
        <div class="absolute right-10 bottom-5 opacity-20 pointer-events-none transform rotate-[-10deg] scale-600">
            <span class="material-symbols-outlined text-[140px] text-primary"
                style="font-variation-settings: 'FILL' 1;">school</span>
        </div>
    </div>

    <section class="flex flex-col gap-stack-md">
        <h2 class="font-h2 text-h2 text-text-primary">Rekomendasi Untukmu</h2>

        @forelse($matches as $match)
            @php $job = $match->jobVacancyData; @endphp
            <a href="{{ route('lowongan.detail', $job->id) }}" wire:navigate>
                <article
                    class="bg-surface rounded-2xl border border-border-subtle shadow-sm p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-start gap-4 mb-4">
                        <div class="w-12 h-12 rounded-xl bg-surface-container-high flex items-center justify-center shrink-0 overflow-hidden">
                            @if($job->image_logo_url)
                                <img src="{{ $job->image_logo_url }}" alt="{{ $job->company }}" class="w-full h-full object-cover" onerror="this.parentElement.innerHTML='<span class=\'material-symbols-outlined text-secondary\'>domain</span>'">
                            @else
                                <span class="material-symbols-outlined text-secondary">domain</span>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h3 class="font-body-lg text-body-lg font-semibold text-text-primary">{{ $job->job_title }}</h3>
                            <p class="font-body-sm text-body-sm text-text-secondary">{{ $job->company ?? 'Perusahaan Rahasia' }}</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @php
                            $scoreVariant = $match->match_score >= 75 ? 'high' : ($match->match_score >= 60 ? 'medium' : 'low');
                            $scoreIcon = $match->match_score >= 75 ? 'check_circle' : ($match->match_score >= 60 ? 'info' : 'warning');
                        @endphp
                        <x-badge :icon="$scoreIcon" :text="'Match: ' . $match->match_score . '%'" :variant="$scoreVariant" />
                        @if($job->work_type)
                            <x-badge icon="schedule" :text="$job->work_type" />
                        @endif
                        @php $agg = $companyAggregates[$job->company] ?? null; @endphp
                        @if($agg && $agg->total > 0)
                            @php
                                $pct = round(($agg->friendly / $agg->total) * 100);
                                $isFriendly = $pct >= 50;
                            @endphp
                            <x-badge icon="{{ $isFriendly ? 'diversity_3' : 'warning' }}" :text="$isFriendly ? 'Ramah Disabilitas' : 'Kurang Ramah'" :variant="$isFriendly ? 'high' : 'low'" />
                        @endif
                    </div>
                </article>
            </a>
        @empty
            <div class="text-center py-12">
                <div class="w-16 h-16 rounded-full bg-surface-container-high flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-[32px] text-outline">search_off</span>
                </div>
                <p class="font-body-lg text-body-lg text-text-secondary mb-1">Belum ada rekomendasi</p>
                <p class="font-body-sm text-body-sm text-outline">Coba lengkapi profilmu agar lowongan yang cocok muncul di sini.</p>
            </div>
        @endforelse
    </section>
</div>
