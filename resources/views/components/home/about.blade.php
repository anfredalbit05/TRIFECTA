<section class="py-20 sm:py-24 lg:py-32 bg-slate-50">
    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">

        <!-- Section Header -->
        <div class="max-w-3xl text-left">

            <x-editable-text
                key="about_badge"
                :value="$contexts['about_badge'] ?? ''"
                tag="span"
                class="inline-block px-4 py-1.5 rounded-full bg-amber-100/80 text-amber-700 font-bold tracking-widest uppercase text-xs sm:text-sm border border-amber-200"
            />

            <div class="mt-4">

                <x-editable-text
                    key="about_title_line1"
                    :value="$contexts['about_title_line1'] ?? ''"
                    tag="h2"
                    class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-[#0C2C6B] leading-tight"
                />

                <x-editable-text
                    key="about_title_line2"
                    :value="$contexts['about_title_line2'] ?? ''"
                    tag="h2"
                    class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-amber-500 leading-tight"
                />

                <x-editable-text
                    key="about_title_line3"
                    :value="$contexts['about_title_line3'] ?? ''"
                    tag="h2"
                    class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-[#0C2C6B] leading-tight"
                />

            </div>

            <x-editable-text
                key="about_description"
                :value="$contexts['about_description'] ?? ''"
                tag="div"
                class="mt-4 text-slate-600 text-base sm:text-lg leading-relaxed"
            />

        </div>

        <!-- Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mt-12">

            <!-- Mission -->
            <div class="group bg-white rounded-3xl shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 p-8 border-t-4 border-amber-500 border-x border-b border-slate-200">

                <div class="w-16 h-16 rounded-2xl bg-blue-50 flex items-center justify-center text-3xl shadow-sm border border-blue-100">
                    🛡️
                </div>

                <x-editable-text
                    key="mission_title"
                    :value="$contexts['mission_title'] ?? ''"
                    tag="h3"
                    class="mt-8 text-2xl font-bold text-[#0C2C6B]"
                />

                <x-editable-text
                    key="mission_description"
                    :value="$contexts['mission_description'] ?? ''"
                    tag="div"
                    class="mt-4 text-slate-600 leading-relaxed text-sm sm:text-base"
                />

            </div>

            <!-- Vision -->
            <div class="group bg-white rounded-3xl shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 p-8 border-t-4 border-amber-500 border-x border-b border-slate-200">

                <div class="w-16 h-16 rounded-2xl bg-amber-50 flex items-center justify-center text-3xl shadow-sm border border-amber-100">
                    🤝
                </div>

                <x-editable-text
                    key="vision_title"
                    :value="$contexts['vision_title'] ?? ''"
                    tag="h3"
                    class="mt-8 text-2xl font-bold text-[#0C2C6B]"
                />

                <x-editable-text
                    key="vision_description"
                    :value="$contexts['vision_description'] ?? ''"
                    tag="div"
                    class="mt-4 text-slate-600 leading-relaxed text-sm sm:text-base"
                />

            </div>

            <!-- Core Values -->
            <div class="group bg-white rounded-3xl shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 p-8 border-t-4 border-amber-500 border-x border-b border-slate-200">

                <div class="w-16 h-16 rounded-2xl bg-emerald-50 flex items-center justify-center text-3xl shadow-sm border border-emerald-100">
                    💙
                </div>

                <x-editable-text
                    key="values_title"
                    :value="$contexts['values_title'] ?? ''"
                    tag="h3"
                    class="mt-8 text-2xl font-bold text-[#0C2C6B]"
                />

                <x-editable-text
                    key="values_subtitle"
                    :value="$contexts['values_subtitle'] ?? ''"
                    tag="p"
                    class="mt-1 text-xs font-semibold uppercase tracking-wider text-amber-600 mb-4"
                />

                <ul class="space-y-3 text-slate-600 text-sm font-medium">

                    <li class="flex items-start gap-2">
                        <span class="text-amber-500 font-bold">✓</span>

                        <x-editable-text
                            key="value_1"
                            :value="$contexts['value_1'] ?? ''"
                            tag="span"
                        />
                    </li>

                    <li class="flex items-start gap-2">
                        <span class="text-amber-500 font-bold">✓</span>

                        <x-editable-text
                            key="value_2"
                            :value="$contexts['value_2'] ?? ''"
                            tag="span"
                        />
                    </li>

                    <li class="flex items-start gap-2">
                        <span class="text-amber-500 font-bold">✓</span>

                        <x-editable-text
                            key="value_3"
                            :value="$contexts['value_3'] ?? ''"
                            tag="span"
                        />
                    </li>

                    <li class="flex items-start gap-2">
                        <span class="text-amber-500 font-bold">✓</span>

                        <x-editable-text
                            key="value_4"
                            :value="$contexts['value_4'] ?? ''"
                            tag="span"
                        />
                    </li>

                    <li class="flex items-start gap-2">
                        <span class="text-amber-500 font-bold">✓</span>

                        <x-editable-text
                            key="value_5"
                            :value="$contexts['value_5'] ?? ''"
                            tag="span"
                        />
                    </li>

                </ul>

            </div>

        </div>

    </div>
</section>
