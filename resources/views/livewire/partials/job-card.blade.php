@php $job = $match->jobVacancyData; @endphp
<a href="{{ route('lowongan.detail', $job->id) }}" wire:navigate>
    <article
        class="bg-surface rounded-2xl border border-border-subtle shadow-sm p-4 hover:shadow-md transition-shadow">
        <div class="flex items-start gap-4 mb-4">
            <div class="w-12 h-12 rounded-xl bg-surface-container-high flex items-center justify-center shrink-0 overflow-hidden">
                @if($job->company_logo_url)
                    <img src="{{ $job->company_logo_url }}" alt="{{ $job->company }}" class="w-full h-full object-cover" onerror="this.parentElement.innerHTML='<span class=\'material-symbols-outlined text-secondary\'>domain</span>'">
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
        <div class="flex items-center text-text-secondary font-body-sm text-body-sm mb-3">
            <span class="material-symbols-outlined text-[18px] mr-1">payments</span>
            @if($job->salary)
                {{ preg_replace('/(Rp\s[\d.]+)\s+(?=Rp)/', '$1 - ', $job->salary) }}
            @else
                Gaji tidak ditampilkan
            @endif
        </div>
        <div class="flex flex-wrap gap-2">
            @if($match->match_score >= 60)
                @php $tier = \App\Data\OnboardingData::matchTier($match->match_score, $match->id); @endphp
                <x-badge :icon="$tier['icon']" :text="$tier['label']" :variant="$tier['variant']" />
            @endif
            @if($job->employment_type)
                <x-badge icon="work_history" :text="$job->employment_type" />
            @endif
        </div>

        @php
            $agg = $companyAggregates[$job->company] ?? null;
            $trivia = \App\Data\OnboardingData::getDisabilityTrivia($agg, $job->job_detail);
        @endphp
        @php
            $triviaClasses = [
                'good' => 'bg-score-high-bg text-score-high-text border-score-high-text/20',
                'bad' => 'bg-score-low-bg text-score-low-text border-score-low-text/20',
                'neutral' => 'bg-surface-container-high text-on-surface border-outline-variant',
                'noinfo' => 'bg-surface-container-low text-on-surface-variant border-border-subtle',
            ];
        @endphp
        <div class="flex items-start gap-2 rounded-xl p-3 mt-2 border {{ $triviaClasses[$trivia['variant']] }}">
            <span class="material-symbols-outlined text-[18px] shrink-0 mt-0.5">{{ $trivia['icon'] }}</span>
            <span class="font-body-sm text-body-sm">{{ $trivia['text'] }}</span>
        </div>
    </article>
</a>
