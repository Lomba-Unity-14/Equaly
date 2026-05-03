<div class="flex flex-col gap-stack-lg">
    <section
        class="bg-surface rounded-2xl border border-border-subtle shadow-sm p-6 flex flex-col items-center text-center">
        <div class="w-20 h-20 bg-surface-container rounded-xl flex items-center justify-center mb-stack-md">
            <span class="material-symbols-outlined text-[48px] text-secondary">domain</span>
        </div>
        <h2 class="font-h1 text-h1 text-text-primary mb-1">UI Designer</h2>
        <p class="font-body-lg text-body-lg text-text-secondary mb-1">Tech Accessibility Corp</p>
        <div class="flex items-center text-text-secondary font-body-sm text-body-sm mb-stack-md">
            <span class="material-symbols-outlined text-[18px] mr-1">location_on</span>
            Jakarta, Indonesia (Remote)
        </div>
        <div class="flex flex-wrap gap-2 justify-center">
            <x-badge icon="check_circle" text="Disability-Friendly: High" variant="high" />
            <x-badge icon="schedule" text="Full-time" />
        </div>
    </section>

    <x-detail-section title="Detail Aksesibilitas">
        <div class="flex flex-col gap-stack-sm">
            <x-accessibility-item icon="accessible" title="Akses Kursi Roda"
                description="Tersedia di seluruh area kantor." />
            <x-accessibility-item icon="diversity_3" title="Budaya Inklusif"
                description="Sangat Baik (Didukung oleh program keberagaman internal)." />
            <x-accessibility-item icon="devices" title="Alat Bantu Kerja"
                description="Disediakan sesuai kebutuhan (Screen reader, ergonomic desk, dll)." />
        </div>
    </x-detail-section>

    <x-detail-section title="Deskripsi Pekerjaan">
        <p class="font-body-lg text-body-lg text-text-secondary">
            Kami mencari UI Designer yang berdedikasi untuk menciptakan antarmuka digital yang tidak hanya estetis
            tetapi juga dapat diakses oleh semua orang. Anda akan memimpin inisiatif desain inklusif kami, memastikan
            setiap produk memenuhi standar aksesibilitas WCAG tertinggi.
        </p>
    </x-detail-section>

    <x-detail-section title="Persyaratan">
        <ul class="font-body-lg text-body-lg text-text-secondary flex flex-col gap-stack-sm list-disc list-inside">
            <li>Pengalaman minimal 2 tahun dalam UI/UX Design.</li>
            <li>Pemahaman mendalam tentang panduan aksesibilitas (WCAG).</li>
            <li>Mampu berkolaborasi dengan tim engineering dan product.</li>
            <li>Portofolio yang menunjukkan proyek desain inklusif.</li>
        </ul>
    </x-detail-section>
</div>
