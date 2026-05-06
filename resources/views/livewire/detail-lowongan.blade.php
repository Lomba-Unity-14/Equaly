<div class="flex flex-col gap-stack-lg">
    <section
        class="bg-surface rounded-2xl border border-border-subtle shadow-sm p-6 flex flex-col items-center text-center">
        <div class="w-20 h-20 bg-surface-container rounded-xl flex items-center justify-center mb-stack-md overflow-hidden">
            @if($job->image_logo_url)
                <img src="{{ $job->image_logo_url }}" alt="{{ $job->company }}" class="w-full h-full object-cover" onerror="this.parentElement.innerHTML='<span class=\'material-symbols-outlined text-[48px] text-secondary\'>domain</span>'">
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
            @if($match)
                @php
                    $scoreVariant = $match->match_score >= 75 ? 'high' : ($match->match_score >= 60 ? 'medium' : 'low');
                    $scoreIcon = $match->match_score >= 75 ? 'check_circle' : ($match->match_score >= 60 ? 'info' : 'warning');
                @endphp
                <x-badge :icon="$scoreIcon" :text="'Match: ' . $match->match_score . '%'" :variant="$scoreVariant" />
            @endif
            @if($job->work_type)
                <x-badge icon="schedule" :text="$job->work_type" />
            @endif
        </div>
    </section>

    @if($match && $match->match_reason)
    <x-detail-section title="Alasan Kecocokan">
        <p class="font-body-lg text-body-lg text-text-secondary">{{ $match->match_reason }}</p>
    </x-detail-section>
    @endif

    @if($match)
    <x-detail-section title="Skor Kecocokan">
        <div class="flex flex-col gap-2">
            <div class="flex items-center justify-between">
                <span class="font-body-sm text-body-sm text-text-secondary">Disability Fit</span>
                <div class="flex items-center gap-2">
                    <div class="w-32 h-2 bg-surface-container-low rounded-full overflow-hidden">
                        <div class="h-full bg-primary rounded-full" style="width: {{ $match->disability_score }}%"></div>
                    </div>
                    <span class="font-label-caps text-label-caps text-text-primary w-8 text-right">{{ $match->disability_score }}%</span>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <span class="font-body-sm text-body-sm text-text-secondary">Skill</span>
                <div class="flex items-center gap-2">
                    <div class="w-32 h-2 bg-surface-container-low rounded-full overflow-hidden">
                        <div class="h-full bg-primary rounded-full" style="width: {{ $match->skill_score }}%"></div>
                    </div>
                    <span class="font-label-caps text-label-caps text-text-primary w-8 text-right">{{ $match->skill_score }}%</span>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <span class="font-body-sm text-body-sm text-text-secondary">Work Environment</span>
                <div class="flex items-center gap-2">
                    <div class="w-32 h-2 bg-surface-container-low rounded-full overflow-hidden">
                        <div class="h-full bg-primary rounded-full" style="width: {{ $match->environment_score }}%"></div>
                    </div>
                    <span class="font-label-caps text-label-caps text-text-primary w-8 text-right">{{ $match->environment_score }}%</span>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <span class="font-body-sm text-body-sm text-text-secondary">Communication</span>
                <div class="flex items-center gap-2">
                    <div class="w-32 h-2 bg-surface-container-low rounded-full overflow-hidden">
                        <div class="h-full bg-primary rounded-full" style="width: {{ $match->communication_score }}%"></div>
                    </div>
                    <span class="font-label-caps text-label-caps text-text-primary w-8 text-right">{{ $match->communication_score }}%</span>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <span class="font-body-sm text-body-sm text-text-secondary">Education</span>
                <div class="flex items-center gap-2">
                    <div class="w-32 h-2 bg-surface-container-low rounded-full overflow-hidden">
                        <div class="h-full bg-primary rounded-full" style="width: {{ $match->education_score }}%"></div>
                    </div>
                    <span class="font-label-caps text-label-caps text-text-primary w-8 text-right">{{ $match->education_score }}%</span>
                </div>
            </div>
        </div>
    </x-detail-section>
    @endif

    <x-detail-section title="Deskripsi Pekerjaan">
        <div class="font-body-lg text-body-lg text-text-secondary prose prose-sm max-w-none">
            {!! nl2br(e(Str::limit($job->jobdesk, 2000))) !!}
        </div>
    </x-detail-section>

    @if($job->skill_req)
    <x-detail-section title="Skill yang Dibutuhkan">
        <div class="flex flex-wrap gap-2">
            @foreach(explode(',', $job->skill_req) as $skill)
                <span class="inline-flex items-center px-3 py-1.5 bg-surface-container-low rounded-full font-label-caps text-label-caps text-on-surface-variant">{{ trim($skill) }}</span>
            @endforeach
        </div>
    </x-detail-section>
    @endif

    @if($job->education_req)
    <x-detail-section title="Pendidikan">
        <p class="font-body-lg text-body-lg text-text-secondary">{{ $job->education_req }}</p>
    </x-detail-section>
    @endif
</div>
