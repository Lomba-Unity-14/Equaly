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

        <a href="/lowongan/ui-designer-tech-accessibility" wire:navigate>
            <article
                class="bg-surface rounded-2xl border border-border-subtle shadow-sm p-4 hover:shadow-md transition-shadow">
                <div class="flex items-start gap-4 mb-4">
                    <div class="w-12 h-12 rounded-xl bg-surface-container-high flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-secondary">domain</span>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-body-lg text-body-lg font-semibold text-text-primary">UI Designer</h3>
                        <p class="font-body-sm text-body-sm text-text-secondary">Tech Accessibility Corp</p>
                    </div>
                </div>
                <div class="flex items-center text-text-secondary font-body-sm text-body-sm mb-3">
                    <span class="material-symbols-outlined text-[18px] mr-1">location_on</span>
                    Jakarta, Indonesia (Remote)
                </div>
                <div class="flex flex-wrap gap-2">
                    <x-badge icon="check_circle" text="Disability-Friendly: High" variant="high" />
                    <x-badge icon="schedule" text="Full-time" />
                </div>
            </article>
        </a>

        <a href="/lowongan/ux-researcher-inclusive-design" wire:navigate>
            <article
                class="bg-surface rounded-2xl border border-border-subtle shadow-sm p-4 hover:shadow-md transition-shadow">
                <div class="flex items-start gap-4 mb-4">
                    <div class="w-12 h-12 rounded-xl bg-surface-container-high flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-secondary">business_center</span>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-body-lg text-body-lg font-semibold text-text-primary">UX Researcher</h3>
                        <p class="font-body-sm text-body-sm text-text-secondary">Inclusive Design Studio</p>
                    </div>
                </div>
                <div class="flex items-center text-text-secondary font-body-sm text-body-sm mb-3">
                    <span class="material-symbols-outlined text-[18px] mr-1">location_on</span>
                    Yogyakarta, Indonesia (On-site)
                </div>
                <div class="flex flex-wrap gap-2">
                    <x-badge icon="info" text="Disability-Friendly: Medium" variant="medium" />
                    <x-badge icon="schedule" text="Full-time" />
                </div>
            </article>
        </a>

        <a href="/lowongan/frontend-dev-accessible-lab" wire:navigate>
            <article
                class="bg-surface rounded-2xl border border-border-subtle shadow-sm p-4 hover:shadow-md transition-shadow">
                <div class="flex items-start gap-4 mb-4">
                    <div class="w-12 h-12 rounded-xl bg-surface-container-high flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-secondary">code</span>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-body-lg text-body-lg font-semibold text-text-primary">Frontend Developer</h3>
                        <p class="font-body-sm text-body-sm text-text-secondary">Accessible Lab</p>
                    </div>
                </div>
                <div class="flex items-center text-text-secondary font-body-sm text-body-sm mb-3">
                    <span class="material-symbols-outlined text-[18px] mr-1">location_on</span>
                    Bandung, Indonesia (Hybrid)
                </div>
                <div class="flex flex-wrap gap-2">
                    <x-badge icon="check_circle" text="Disability-Friendly: High" variant="high" />
                    <x-badge icon="schedule" text="Contract" />
                </div>
            </article>
        </a>
    </section>
</div>
