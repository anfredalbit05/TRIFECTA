<section class="overview-section bg-gradient-to-b from-slate-50 to-white py-20 sm:py-24 lg:py-32">

    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">


            {{-- =========================
                CONTENT SIDE
            ========================== --}}

            <div class="lg:col-span-7 space-y-6">

                <div class="max-w-3xl text-left">


                    {{-- Badge --}}

                    <x-editable-text
                        key="overview_badge"
                        :value="$contexts['overview_badge'] ?? ''"
                        tag="span"
                        class="inline-block px-4 py-1.5 rounded-full bg-amber-100/80 text-amber-700 font-bold tracking-widest uppercase text-xs sm:text-sm border border-amber-200"
                    />


                    {{-- Title --}}

                    <h2 class="mt-4 text-2xl sm:text-3xl md:text-4xl font-extrabold text-[#0C2C6B] leading-tight">

                        <x-editable-text
                            key="overview_title"
                            :value="$contexts['overview_title'] ?? ''"
                            tag="span"
                        />

                        <br>

                        <span class="text-amber-500">

                            <x-editable-text
                                key="overview_title_highlight"
                                :value="$contexts['overview_title_highlight'] ?? ''"
                                tag="span"
                            />

                        </span>

                    </h2>


                    {{-- Description --}}

                    <x-editable-text
                        key="overview_description"
                        :value="$contexts['overview_description'] ?? ''"
                        tag="div"
                        class="mt-4 text-slate-600 text-base sm:text-lg leading-relaxed"
                    />


                </div>



                {{-- =========================
                    COVERAGE PLANS
                ========================== --}}

                <div class="bg-slate-100/70 rounded-2xl p-3 border border-slate-200 grid grid-cols-3 gap-3 text-center">


                    {{-- Bronze --}}

                    <div class="bg-white p-3 rounded-xl shadow-sm border border-amber-200">

                        <x-editable-text
                            key="bronze_title"
                            :value="$contexts['bronze_title'] ?? ''"
                            tag="div"
                            class="text-xs font-bold text-amber-800 uppercase"
                        />

                        <x-editable-text
                            key="bronze_amount"
                            :value="$contexts['bronze_amount'] ?? ''"
                            tag="div"
                            class="text-sm sm:text-base font-extrabold text-[#0C2C6B]"
                        />

                    </div>



                    {{-- Silver --}}

                    <div class="bg-white p-3 rounded-xl shadow-sm border border-slate-300">

                        <x-editable-text
                            key="silver_title"
                            :value="$contexts['silver_title'] ?? ''"
                            tag="div"
                            class="text-xs font-bold text-slate-700 uppercase"
                        />

                        <x-editable-text
                            key="silver_amount"
                            :value="$contexts['silver_amount'] ?? ''"
                            tag="div"
                            class="text-sm sm:text-base font-extrabold text-[#0C2C6B]"
                        />

                    </div>



                    {{-- Gold --}}

                    <div class="bg-white p-3 rounded-xl shadow-sm border border-amber-400">

                        <x-editable-text
                            key="gold_title"
                            :value="$contexts['gold_title'] ?? ''"
                            tag="div"
                            class="text-xs font-bold text-amber-600 uppercase"
                        />

                        <x-editable-text
                            key="gold_amount"
                            :value="$contexts['gold_amount'] ?? ''"
                            tag="div"
                            class="text-sm sm:text-base font-extrabold text-[#0C2C6B]"
                        />

                    </div>


                </div>



                {{-- =========================
                    FEATURES
                ========================== --}}

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">


                    {{-- Feature 1 --}}

                    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">

                        <h3 class="text-sm font-bold text-[#0C2C6B]">

                            <x-editable-text
                                key="feature_1_title"
                                :value="$contexts['feature_1_title'] ?? ''"
                                tag="span"
                            />

                        </h3>


                        <x-editable-text
                            key="feature_1_text"
                            :value="$contexts['feature_1_text'] ?? ''"
                            tag="p"
                            class="text-xs text-slate-500 mt-1"
                        />

                    </div>



                    {{-- Feature 2 --}}

                    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">

                        <h3 class="text-sm font-bold text-[#0C2C6B]">

                            <x-editable-text
                                key="feature_2_title"
                                :value="$contexts['feature_2_title'] ?? ''"
                                tag="span"
                            />

                        </h3>


                        <x-editable-text
                            key="feature_2_text"
                            :value="$contexts['feature_2_text'] ?? ''"
                            tag="p"
                            class="text-xs text-slate-500 mt-1"
                        />

                    </div>


                </div>


            </div>




            {{-- =========================
                IMAGE SIDE
            ========================== --}}

            <div class="lg:col-span-5 relative">


                <img
                    src="{{ asset('images/hero-bg.jpg') }}"
                    alt="DAYONGAN Community Support"
                    class="w-full h-[480px] object-cover rounded-3xl shadow-2xl border-4 border-white"
                >



                {{-- Pricing Badge --}}

                <div class="absolute bottom-6 left-6 right-6 bg-white/95 rounded-2xl p-4 shadow-lg">


                    <x-editable-text
                        key="pricing_title"
                        :value="$contexts['pricing_title'] ?? ''"
                        tag="span"
                        class="text-xs uppercase font-bold text-slate-500"
                    />


                    <x-editable-text
                        key="pricing_amount"
                        :value="$contexts['pricing_amount'] ?? ''"
                        tag="div"
                        class="text-2xl font-black text-[#E5B83B]"
                    />


                </div>


            </div>


        </div>

    </div>

</section>
