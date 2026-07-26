<div class="group relative inline-flex items-center gap-2 max-w-full">

    <{{ $tag }} class="{{ $class }}">
        {!! $value !!}
    </{{ $tag }}>

    @auth
        @if(auth()->user()->RoleID == 1)

            <button
                type="button"
                x-data
                @click="$dispatch('open-context-editor', { key: '{{ $key }}' })"
                title="Edit Content"
                class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-amber-400 text-white text-[10px] shadow-sm hover:bg-amber-500 hover:scale-110 transition-all duration-200 focus:outline-none">

                <!-- Vector Pencil Icon for exact sizing/centering -->
                <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20">
                    <path d="M13.586 3.586a2 2 0 012.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                </svg>

            </button>

        @endif
    @endauth

</div>
