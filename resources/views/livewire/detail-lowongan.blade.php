<div class="flex flex-col gap-stack-lg">
    <section
        class="bg-surface rounded-2xl border border-border-subtle shadow-sm p-6 flex flex-col items-center text-center">
        <div class="w-20 h-20 bg-surface-container rounded-xl flex items-center justify-center mb-stack-md overflow-hidden">
            @if($job->company_logo_url)
                <img src="{{ $job->company_logo_url }}" alt="{{ $job->company }}" class="w-full h-full object-cover" onerror="this.parentElement.innerHTML='<span class=\'material-symbols-outlined text-[48px] text-secondary\'>domain</span>'">
            @else
                <span class="material-symbols-outlined text-[48px] text-secondary">domain</span>
            @endif
        </div>
        <h2 class="font-h1 text-h1 text-text-primary mb-1">{{ $job->job_title }}</h2>
        <p class="font-body-lg text-body-lg text-text-secondary mb-1">{{ $job->company ?? 'Perusahaan Rahasia' }}</p>
        <div class="flex items-center text-text-secondary font-body-sm text-body-sm mb-stack-md">
            <span class="material-symbols-outlined text-[18px] mr-1">location_on</span>
            {{ $job->location }}
        </div>
        <div class="flex flex-wrap gap-2 justify-center">
            @if($job->employment_type)
                <x-badge icon="work_history" :text="$job->employment_type" />
            @endif
        </div>
    </section>

    @if($job->salary)
    <x-detail-section title="Gaji">
        <span class="font-body-lg text-body-lg text-text-primary">
            {{ preg_replace('/(Rp\s[\d.]+)\s+(?=Rp)/', '$1 - ', $job->salary) }}
        </span>
    </x-detail-section>
    @endif

    @if($match)
    <x-detail-section title="Kecocokan">
        @php $tier = \App\Data\OnboardingData::matchTier($match->match_score); @endphp
        <div class="flex items-start gap-3 mb-4">
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-full font-label-caps text-label-caps
                {{ $tier['variant'] === 'high' ? 'bg-score-high-bg text-score-high-text' : '' }}
                {{ $tier['variant'] === 'medium' ? 'bg-score-medium-bg text-score-medium-text' : '' }}
                {{ $tier['variant'] === 'low' ? 'bg-score-low-bg text-score-low-text' : '' }}">
                <span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">{{ $tier['icon'] }}</span>
                {{ $tier['label'] }}
            </div>
        </div>
        @if($tier['desc'])
            <p class="font-body-sm text-body-sm text-text-secondary mb-4">{{ $tier['desc'] }}</p>
        @endif
        @if($match->match_reason)
            <div class="bg-surface-container-low rounded-xl p-4">
                <p class="font-body-lg text-body-lg text-text-secondary">{{ $match->match_reason }}</p>
            </div>
        @endif
    </x-detail-section>
    @endif

    @if($academyRecommendation)
    <x-detail-section title="Tingkatkan Peluangmu">
        @php
            $rec = $academyRecommendation;
            $typeLabels = [
                'pelatihan' => ['label' => 'Pelatihan', 'icon' => 'school', 'variant' => 'medium'],
                'sertifikasi' => ['label' => 'Sertifikasi', 'icon' => 'verified', 'variant' => 'high'],
            ];
            $recType = $typeLabels[$rec->type] ?? ['label' => $rec->type, 'icon' => 'school', 'variant' => 'default'];
        @endphp
        <p class="font-body-sm text-body-sm text-text-secondary mb-3">Perkuat skillmu dengan program berikut agar peluangmu lebih besar:</p>
        <a href="{{ route('academy.detail', $rec->id) }}" wire:navigate
            class="block bg-surface-container-low rounded-xl p-3 hover:shadow-sm transition-shadow active:scale-[0.98] border border-border-subtle">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-primary-fixed-dim flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-primary text-[22px]" style="font-variation-settings: 'FILL' 1;">{{ $recType['icon'] }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="font-body-lg text-body-lg font-semibold text-text-primary leading-tight">{{ $rec->name }}</h4>
                    <p class="font-body-sm text-body-sm text-text-secondary mt-0.5">
                        @if($rec->duration)
                            <span>{{ $rec->duration }}</span>
                        @endif
                        @if($rec->duration && $rec->level)
                            <span> · </span>
                        @endif
                        @if($rec->level)
                            <span>{{ $rec->level }}</span>
                        @endif
                    </p>
                    <div class="flex gap-2 mt-2">
                        <x-badge :icon="$recType['icon']" :text="$recType['label']" :variant="$recType['variant']" />
                    </div>
                </div>
                <span class="material-symbols-outlined text-outline shrink-0 mt-2">chevron_right</span>
            </div>
        </a>
    </x-detail-section>
    @endif

    <x-detail-section title="Deskripsi Pekerjaan">
        <div class="font-body-lg text-body-lg text-text-secondary prose prose-sm max-w-none">
            {!! nl2br(e(Str::limit($job->job_detail, 2000))) !!}
        </div>
    </x-detail-section>

    @if($job->education_req)
    <x-detail-section title="Pendidikan">
        <p class="font-body-lg text-body-lg text-text-secondary">{{ $job->education_req }}</p>
    </x-detail-section>
    @endif

    @if(!empty($companyScore) && $companyScore['total'] > 0)
    <x-detail-section title="Skor Perusahaan">
        <div class="flex items-center gap-3 mb-4">
            @if($companyScore['score'] >= 50)
                <div class="bg-score-high-bg text-score-high-text font-label-caps text-label-caps px-3 py-1.5 rounded-full inline-flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                    Ramah Disabilitas
                </div>
            @else
                <div class="bg-score-low-bg text-score-low-text font-label-caps text-label-caps px-3 py-1.5 rounded-full inline-flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">warning</span>
                    Kurang Ramah Disabilitas
                </div>
            @endif
            <span class="font-body-sm text-body-sm text-outline">{{ $companyScore['total'] }} ulasan</span>
        </div>

        <div class="flex flex-col gap-3">
            @foreach(array_slice($companyReviews, 0, 5) as $review)
                <div class="bg-surface-container-low rounded-xl p-3">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="font-body-sm text-body-sm font-semibold text-text-primary">{{ $review['user']['name'] ?? 'Anonim' }}</span>
                        <span class="font-label-caps text-label-caps {{ $review['is_accepted'] ? 'text-score-high-text' : 'text-text-secondary' }}">
                            {{ $review['is_accepted'] ? 'Diterima' : 'Belum diterima' }}
                        </span>
                    </div>
                    @if($review['experience'])
                        <p class="font-body-sm text-body-sm text-text-secondary">{{ $review['experience'] }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    </x-detail-section>
    @endif

    <div class="mt-6 mb-2">
        <button wire:click="apply"
            class="w-full flex items-center justify-center bg-primary text-on-primary rounded-xl py-3 px-8 font-label-caps text-label-caps font-bold active:scale-[0.98] transition-all hover:bg-primary-fixed-variant min-h-[48px] shadow-md">
            <span class="material-symbols-outlined mr-2">open_in_new</span>
            Lihat Lowongan
        </button>
    </div>
</div>
