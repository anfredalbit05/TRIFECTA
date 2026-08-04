<div class="settings-container w-full px-4 sm:px-6 lg:px-8 py-6">

    <!-- HEADER -->
    <div class="members-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-2">
        <h2 class="masterlist-title text-2xl font-bold text-slate-800">
            General Settings
        </h2>

        @if($this->canCreate)
            <button
                wire:click="openCreateModal"
                class="masterlist-btn inline-flex items-center justify-center px-4 py-2 rounded-lg bg-[#0C2C6B] text-white hover:bg-[#09204f] text-sm font-semibold shadow transition cursor-pointer">
                Add Setting
            </button>
        @endif
    </div>

    <p class="masterlist-description text-sm text-slate-500 mb-6">
        Manage general system configurations.
    </p>

    <!-- Success Message Notification -->
    @if (session()->has('message'))
        <div class="p-4 mb-4 text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-xl text-sm font-medium">
            {{ session('message') }}
        </div>
    @endif

    <!-- SETTINGS TABLE -->
    <div class="overflow-x-auto bg-white rounded-xl border border-slate-200 shadow-sm">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-slate-200 bg-[#0C2C6B] text-xs font-semibold uppercase tracking-wider text-white">
                    <th class="py-3 px-4">#</th>
                    <th class="py-3 px-4">Setting Name</th>
                    <th class="py-3 px-4">Value</th>
                    @if($this->canUpdate || $this->canDelete)
                        <th class="py-3 px-4 text-center">Action</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 text-sm text-slate-700">
                @forelse($settings as $index => $setting)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-4 px-4 font-medium text-slate-500">
                           {{ $settings->firstItem() + $index }}
                        </td>

                        <td class="py-4 px-4 font-semibold text-slate-900">
                            {{ $setting->name }}
                        </td>

                        <td class="py-4 px-4 text-slate-500 max-w-xs truncate">
                            {{ $setting->value ?? 'No value provided.' }}
                        </td>

                        <!-- Actions Column -->
                        @if($this->canUpdate || $this->canDelete)
                            <td class="py-4 px-4 text-center">
                                <div class="flex gap-2 justify-center">
                                    {{-- EDIT BUTTON --}}
                                    @if($this->canUpdate)
                                        <button
                                            wire:click="editSetting({{ $setting->SettingsID }})"
                                            title="Update Setting"
                                            class="inline-flex items-center justify-center px-3 py-2 rounded-lg bg-[#0C2C6B] text-white hover:bg-[#09204f] transition cursor-pointer text-xs font-semibold shadow">
                                            ✏️
                                        </button>
                                    @endif

                                    {{-- DELETE BUTTON --}}
                                    @if($this->canDelete)
                                        <button
                                            wire:click="confirmDeleteSetting({{ $setting->SettingsID }})"
                                            title="Delete Setting"
                                            class="inline-flex items-center justify-center px-3 py-2 rounded-lg border-amber-400 bg-amber-500 text-white hover:bg-amber-500 transition cursor-pointer text-xs font-semibold shadow">
                                            🗑️
                                        </button>
                                    @endif
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ ($this->canUpdate || $this->canDelete) ? 4 : 3 }}" class="text-center py-8 text-slate-500">
                            No settings configurations found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- PAGINATION -->
        @if($settings->hasPages())
            <div class="px-4 py-4 border-t border-slate-200 bg-slate-50 flex justify-center items-center space-x-4">

                {{-- Previous Page Link --}}
                @if ($settings->onFirstPage())
                    <span class="px-4 py-2 bg-gray-200 text-gray-400 rounded cursor-not-allowed">
                        Previous
                    </span>
                @else
                    <button
                        wire:click="previousPage"
                        wire:loading.attr="disabled"
                        class="px-4 py-2 text-white rounded transition cursor-pointer"
                        style="background-color: #D79B10;"
                        onmouseover="this.style.backgroundColor='#c58d0f'"
                        onmouseout="this.style.backgroundColor='#D79B10'"
                    >
                        Previous
                    </button>
                @endif

                <span class="text-sm text-gray-600">
                    Page {{ $settings->currentPage() }} of {{ $settings->lastPage() }}
                </span>

                {{-- Next Page Link --}}
                @if ($settings->hasMorePages())
                    <button
                        wire:click="nextPage"
                        wire:loading.attr="disabled"
                        class="px-4 py-2 text-white rounded transition cursor-pointer"
                        style="background-color: #0C2C6B;"
                        onmouseover="this.style.backgroundColor='#0a2559'"
                        onmouseout="this.style.backgroundColor='#0C2C6B'"
                    >
                        Next
                    </button>
                @else
                    <span class="px-4 py-2 bg-gray-200 text-gray-400 rounded cursor-not-allowed">
                        Next
                    </span>
                @endif

            </div>
        @endif
    </div>

    <!-- ADD/EDIT SETTING MODAL -->
    @if($isOpen)
        <div class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-950/70 p-4 backdrop-blur-sm overflow-y-auto">
            <div class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl my-8">

                <!-- MODAL HEADER -->
                <div class="bg-[#0C2C6B] px-6 py-4 text-white flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold">
                            {{ $setting_id ? 'Edit Setting' : 'Add New Setting' }}
                        </h3>
                        <p class="text-xs text-blue-100">Configure system options below.</p>
                    </div>
                    <button
                        wire:click="closeModal"
                        class="flex h-8 w-8 items-center justify-center rounded-full bg-white/20 transition hover:bg-white/30 text-white font-bold cursor-pointer"
                    >
                        ✕
                    </button>
                </div>

                <form wire:submit.prevent="saveSetting">
                    <!-- MODAL BODY -->
                    <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">

                        <!-- Name -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Setting Name</label>
                            <input type="text" wire:model="name" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0C2C6B]">
                            @error('name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Value -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Value</label>
                            <textarea wire:model="value" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0C2C6B]" rows="3"></textarea>
                            @error('value') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                    </div>

                    <!-- MODAL FOOTER -->
                    <div class="flex justify-end gap-2 border-t border-slate-200 px-6 py-3 bg-slate-50">
                        <button
                            type="button"
                            wire:click="closeModal"
                            class="px-4 py-2 text-xs font-semibold text-slate-700 bg-slate-200 rounded-lg hover:bg-slate-300 transition cursor-pointer">
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 text-xs font-semibold text-white bg-[#0C2C6B] rounded-lg hover:bg-[#09204f] transition cursor-pointer shadow">
                            {{ $setting_id ? 'Update Setting' : 'Save Setting' }}
                        </button>
                    </div>
                </form>

            </div>
        </div>
    @endif

@script
<script>
    // Listen for custom success alerts
    $wire.on('swal:success', (event) => {
        const data = event[0] || event;
        Swal.fire({
            title: data.title,
            text: data.text,
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
        });
    });

    // Listen for custom error alerts (like duplicates)
    $wire.on('swal:error', (event) => {
        const data = event[0] || event;
        Swal.fire({
            title: data.title,
            html: data.text, // This line enables the HTML formatting
            icon: 'error',
            confirmButtonColor: '#0C2C6B'
        });
    });

    // Listen for update confirmation prompt
    $wire.on('confirm-update', () => {
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to update this setting?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0C2C6B',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, update it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $wire.performUpdate();
            }
        });
    });

    // Existing delete scripts...
    $wire.on('confirm-delete', (event) => {
        const settingId = event.id || event[0]?.id;

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $wire.deleteSetting(settingId);
            }
        });
    });

    $wire.on('setting-deleted', () => {
        Swal.fire({
            title: 'Deleted!',
            text: 'Setting has been successfully deleted.',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
        });
    });
</script>
@endscript

</div>
