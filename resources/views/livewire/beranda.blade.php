<div class ="flex flex-col gap-stack-lg">
    <div>
        <h1 class="font-h1 text-h1 text-text-primary mb-1">Halo, Yazid</h1>
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
                <div class="flex flex-wrap gap-2 mb-4">
                    <div class="inline-flex items-center gap-1 bg-score-high-bg px-3 py-1.5 rounded-full">
                        <span class="material-symbols-outlined text-[16px] text-score-high-text"
                            style="font-variation-settings: 'FILL' 1;">check_circle</span>
                        <span class="font-label-caps text-label-caps text-score-high-text">Disability-Friendly: High</span>
                    </div>
                    <div class="inline-flex items-center px-3 py-1.5 bg-surface-container-low rounded-full">
                        <span class="font-label-caps text-label-caps text-on-surface-variant">Remote</span>
                    </div>
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
                <div class="flex flex-wrap gap-2 mb-4">
                    <div class="inline-flex items-center gap-1 bg-score-medium-bg px-3 py-1.5 rounded-full">
                        <span class="material-symbols-outlined text-[16px] text-score-medium-text"
                            style="font-variation-settings: 'FILL' 1;">info</span>
                        <span class="font-label-caps text-label-caps text-score-medium-text">Disability-Friendly:
                            Medium</span>
                    </div>
                    <div class="inline-flex items-center px-3 py-1.5 bg-surface-container-low rounded-full">
                        <span class="font-label-caps text-label-caps text-on-surface-variant">On-site</span>
                    </div>
                </div>
            </article>
        </a>
    </section>
</div>
