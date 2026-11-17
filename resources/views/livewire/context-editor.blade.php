<div>

@if($open)

<div class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/60">

    <div class="bg-white rounded-xl w-full max-w-2xl p-6">

        <h2 class="text-xl font-bold">

            Edit Content

        </h2>

        <p class="text-sm text-slate-500">

            {{ $contextKey }}

        </p>

        <textarea
            wire:model.defer="details"
            rows="10"
            class="w-full mt-5 border rounded-lg p-3"></textarea>

        <div class="flex justify-end gap-3 mt-6">

            <button
                wire:click="$set('open',false)"
                class="px-5 py-2 rounded bg-slate-200">

                Cancel

            </button>

            <button
                wire:click="save"
                class="px-5 py-2 rounded bg-[var(--color-primary)] text-white">

                Save

            </button>

        </div>

    </div>

</div>

@endif

</div>
