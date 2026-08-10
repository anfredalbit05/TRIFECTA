<footer class="bg-[#071D3F] border-t border-white/10 py-6 text-center text-slate-300 text-xs sm:text-sm">
    <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <span class="font-black text-[#D4A017] tracking-wider">
                <x-editable-text
                    key="footer_brand_name"
                    :value="$contexts['footer_brand_name'] ?? 'TRUELIFE'"
                    tag="span"
                    class="inline-block"
                />
            </span>
            <span class="text-slate-500">•</span>
            <span class="text-slate-400 text-xs">
                <x-editable-text
                    key="footer_brand_tagline"
                    :value="$contexts['footer_brand_tagline'] ?? 'Solidaritas • Vitae Auxilium'"
                    tag="span"
                    class="inline-block"
                />
            </span>
        </div>

        <p class="text-slate-400">
            <x-editable-text
                key="footer_copyright"
                :value="$contexts['footer_copyright'] ?? '© ' . date('Y') . ' TRUELIFE. All rights reserved.'"
                tag="span"
                class="inline-block"
            />
        </p>
    </div>
</footer>
