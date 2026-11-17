<span class="group relative inline-flex max-w-full items-center gap-2">

    <{{ $tag }} class="{{ $class }}">
        {!! $value !!}
    </{{ $tag }}>

    @auth
        @if(auth()->user()->RoleID == 1 || auth()->user()->RoleID == 2 || auth()->user()->RoleID == 5 || auth()->user()->RoleID == 6 || auth()->user()->RoleID == 7)

            <span
                x-data
                role="button"
                tabindex="0"
                @click="$dispatch('open-context-editor', { key: '{{ $key }}' })"
                @keydown.enter.prevent="$dispatch('open-context-editor', { key: '{{ $key }}' })"
                @keydown.space.prevent="$dispatch('open-context-editor', { key: '{{ $key }}' })"
                title="Edit Content"
                class="inline-flex h-5 w-5 shrink-0 cursor-pointer items-center justify-center rounded-full bg-[var(--color-accent)] text-[10px] text-white shadow-sm transition-all duration-200 hover:scale-110 hover:bg-[var(--color-accent-dark)] focus:outline-none">

                <!-- Vector Pencil Icon for exact sizing/centering -->
                <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20">
                    <path d="M13.586 3.586a2 2 0 012.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                </svg>

            </span>

        @endif
    @endauth

</span>
