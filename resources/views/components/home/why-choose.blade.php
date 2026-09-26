<section class="relative py-20 sm:py-24 lg:py-32 bg-slate-50 overflow-hidden">


    {{-- Background Pattern --}}
    <div class="absolute inset-0 opacity-25 [background-image:radial-gradient(#d97706_1px,transparent_1px)] [background-size:28px_28px]"></div>



    <div class="relative max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">


        {{-- HEADER --}}

        <div class="max-w-3xl text-left">


            <x-editable-text
                key="why_badge"
                :value="$contexts['why_badge'] ?? ''"
                tag="span"
                class="inline-block px-4 py-1.5 rounded-full bg-amber-100/80 text-amber-700 font-bold tracking-widest uppercase text-xs sm:text-sm border border-amber-200"
            />



            <h2 class="mt-4 text-2xl sm:text-3xl md:text-4xl font-extrabold text-[#0C2C6B] leading-tight">


                <x-editable-text
                    key="why_title_line1"
                    :value="$contexts['why_title_line1'] ?? ''"
                    tag="span"
                />


                <br class="hidden sm:inline">


                <span class="text-amber-500">

                    <x-editable-text
                        key="why_title_highlight"
                        :value="$contexts['why_title_highlight'] ?? ''"
                        tag="span"
                    />

                </span>


            </h2>



            <x-editable-text
                key="why_description"
                :value="$contexts['why_description'] ?? ''"
                tag="div"
                class="mt-4 text-slate-600 text-base sm:text-lg leading-relaxed"
            />


        </div>




        {{-- CARDS --}}

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8 mt-12">



            {{-- CARD 1 --}}

            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 p-8 flex flex-col justify-between">


                <div>


                    <div class="w-16 h-16 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center shadow-sm">
                        <flux:icon.shield-check class="w-8 h-8 text-[#0B2A5B]" />
                    </div>


                    <x-editable-text
                        key="why_card1_title"
                        :value="$contexts['why_card1_title'] ?? ''"
                        tag="h3"
                        class="mt-8 text-2xl font-bold text-[#0C2C6B]"
                    />


                    <x-editable-text
                        key="why_card1_description"
                        :value="$contexts['why_card1_description'] ?? ''"
                        tag="div"
                        class="mt-4 text-slate-600 leading-relaxed text-sm sm:text-base"
                    />


                </div>


                <x-editable-text
                    key="why_card1_footer"
                    :value="$contexts['why_card1_footer'] ?? ''"
                    tag="div"
                    class="mt-6 pt-4 border-t border-slate-100 text-xs font-semibold text-amber-600"
                />


            </div>





            {{-- CARD 2 --}}

            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 p-8 flex flex-col justify-between">


                <div>


                    <div class="w-16 h-16 rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-center shadow-sm">
                        <flux:icon.home-modern class="w-8 h-8 text-amber-600" />
                    </div>


                    <x-editable-text
                        key="why_card2_title"
                        :value="$contexts['why_card2_title'] ?? ''"
                        tag="h3"
                        class="mt-8 text-2xl font-bold text-[#0C2C6B]"
                    />


                    <x-editable-text
                        key="why_card2_description"
                        :value="$contexts['why_card2_description'] ?? ''"
                        tag="div"
                        class="mt-4 text-slate-600 leading-relaxed text-sm sm:text-base"
                    />


                </div>


                <x-editable-text
                    key="why_card2_footer"
                    :value="$contexts['why_card2_footer'] ?? ''"
                    tag="div"
                    class="mt-6 pt-4 border-t border-slate-100 text-xs font-semibold text-amber-600"
                />


            </div>





            {{-- CARD 3 --}}

            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 p-8 flex flex-col justify-between">


                <div>


                    <div class="w-16 h-16 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center shadow-sm">
                        <flux:icon.user-group class="w-8 h-8 text-emerald-600" />
                    </div>


                    <x-editable-text
                        key="why_card3_title"
                        :value="$contexts['why_card3_title'] ?? ''"
                        tag="h3"
                        class="mt-8 text-2xl font-bold text-[#0C2C6B]"
                    />


                    <x-editable-text
                        key="why_card3_description"
                        :value="$contexts['why_card3_description'] ?? ''"
                        tag="div"
                        class="mt-4 text-slate-600 leading-relaxed text-sm sm:text-base"
                    />


                </div>


                <x-editable-text
                    key="why_card3_footer"
                    :value="$contexts['why_card3_footer'] ?? ''"
                    tag="div"
                    class="mt-6 pt-4 border-t border-slate-100 text-xs font-semibold text-amber-600"
                />


            </div>


        </div>





        {{-- STATISTICS BANNER --}}


        <div class="mt-16 rounded-3xl bg-[#0C2C6B] p-8 sm:p-10 text-white shadow-xl relative overflow-hidden">


            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-amber-500/20 rounded-full blur-2xl"></div>



            <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 text-center relative z-10">


                @for($i = 1; $i <= 4; $i++)

                    <div>

                        <x-editable-text
                            key="why_stat{{ $i }}_value"
                            :value="$contexts['why_stat'.$i.'_value'] ?? ''"
                            tag="h3"
                            class="text-3xl sm:text-4xl font-black text-amber-400"
                        />


                        <x-editable-text
                            key="why_stat{{ $i }}_label"
                            :value="$contexts['why_stat'.$i.'_label'] ?? ''"
                            tag="p"
                            class="mt-1 text-slate-300 text-xs sm:text-sm font-medium"
                        />

                    </div>

                @endfor


            </div>


        </div>


    </div>


</section>
