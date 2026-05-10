<div class="flex flex-col gap-stack-lg">
    {{-- Hero --}}
    <section class="space-y-2">
        <h1 class="font-h1 text-h1 text-text-primary">Academy</h1>
        <p class="font-body-sm text-body-sm text-text-secondary">Tingkatkan daya saingmu dengan pelatihan dari mitra kami yang dirancang khusus untuk tunarungu.</p>
    </section>

    {{-- Rekomendasi Untukmu --}}
    @if($recommended->isNotEmpty())
    <section class="flex flex-col gap-stack-sm">
        <h2 class="font-h2 text-h2 text-text-primary">Rekomendasi Untukmu</h2>
        <div class="flex flex-col gap-3">
            @foreach($recommended as $partner)
                @include('livewire.partials.partner-card', ['partner' => $partner])
            @endforeach
        </div>
    </section>
    @endif

    {{-- Program Pemerintah --}}
    @if($government->isNotEmpty())
    <section class="flex flex-col gap-stack-sm">
        <h2 class="font-h2 text-h2 text-text-primary">Program Pemerintah</h2>
        <div class="flex flex-col gap-3">
            @foreach($government as $partner)
                @include('livewire.partials.partner-card', ['partner' => $partner])
            @endforeach
        </div>
    </section>
    @endif

    {{-- Komunitas --}}
    @if($community->isNotEmpty())
    <section class="flex flex-col gap-stack-sm">
        <h2 class="font-h2 text-h2 text-text-primary">Komunitas</h2>
        <div class="flex flex-col gap-3">
            @foreach($community as $partner)
                @include('livewire.partials.partner-card', ['partner' => $partner])
            @endforeach
        </div>
    </section>
    @endif

    {{-- Empty state --}}
    @if($recommended->isEmpty() && $government->isEmpty() && $community->isEmpty())
    <div class="text-center py-12">
        <div class="w-16 h-16 rounded-full bg-surface-container-high flex items-center justify-center mx-auto mb-4">
            <span class="material-symbols-outlined text-[32px] text-outline">school</span>
        </div>
        <p class="font-body-lg text-body-lg text-text-secondary mb-1">Belum ada rekomendasi</p>
        <p class="font-body-sm text-body-sm text-outline">Lengkapi profilmu agar rekomendasi pelatihan muncul di sini.</p>
    </div>
    @endif
</div>
