<div class="flex flex-col gap-stack-lg">
    <div>
        <h1 class="font-h1 text-h1 text-text-primary mb-1">Lowongan</h1>
        <p class="font-body-sm text-body-sm text-text-secondary">Temukan pekerjaan inklusif untukmu.</p>
    </div>

    <div class="relative drop-shadow-md">
        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
            <span class="material-symbols-outlined text-outline">search</span>
        </div>
        <input
            class="block w-full pl-12 pr-4 py-3 bg-surface border-2 border-border-subtle rounded-2xl text-body-lg font-body-lg text-text-primary placeholder-outline focus:ring-primary focus:border-primary min-h-[48px] shadow-sm transition-all focus:shadow-md focus:outline-none"
            placeholder="Cari pekerjaan inklusif..." type="text" />
    </div>

    <section class="flex flex-col gap-stack-md">
        <h2 class="font-h2 text-h2 text-text-primary">Semua Lowongan</h2>

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
                    <div class="flex items-center text-text-secondary font-body-sm text-body-sm mb-3">
                        <span class="material-symbols-outlined text-[18px] mr-1">location_on</span>
                        {{ $job->location }}
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @if($match->match_score >= 60)
                            @php
                                $scoreVariant = $match->match_score >= 75 ? 'high' : 'medium';
                                $scoreIcon = $match->match_score >= 75 ? 'check_circle' : 'info';
                            @endphp
                            <x-badge :icon="$scoreIcon" :text="'Match: ' . $match->match_score . '%'" :variant="$scoreVariant" />
                        @endif
                        @if($job->work_type)
                            <x-badge icon="schedule" :text="$job->work_type" />
                        @endif
                    </div>
                </article>
            </a>
        @empty
            <div class="text-center py-12">
                <div class="w-16 h-16 rounded-full bg-surface-container-high flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-[32px] text-outline">work_off</span>
                </div>
                <p class="font-body-lg text-body-lg text-text-secondary mb-1">Belum ada lowongan</p>
                <p class="font-body-sm text-body-sm text-outline">Silakan cek kembali nanti.</p>
            </div>
        @endforelse
    </section>
</div>
