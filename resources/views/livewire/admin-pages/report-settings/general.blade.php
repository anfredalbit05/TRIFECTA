<div class="settings-container w-full px-4 sm:px-6 lg:px-8 py-6">

   <!-- HEADER & POST TOGGLE BUTTON -->
    <div class="members-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="masterlist-title text-2xl font-bold text-slate-800">General Settings</h2>
            <p class="masterlist-description text-sm text-slate-500 mt-0.5">Manage system-wide configurations and rules (e.g., Maintenance Mode, Coverage, Limits)</p>
        </div>

        @if ($this->canCreate)
            <button
                type="button"
                wire:click="openCreateModal"
                class="masterlist-btn inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition cursor-pointer shadow bg-[#0C2C6B] text-white hover:bg-[#09204f] w-full sm:w-auto"
            >
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Setting
            </button>
        @endif
    </div>

    <!-- SEARCH INPUT & REFRESH BUTTON CONTAINER -->
    <div class="flex items-center justify-between gap-4 mb-6">
        <!-- SEARCH INPUT -->
        <div class="relative max-w-sm w-full">
            <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none">
                🔍
            </span>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search settings..."
                class="w-full pl-10 pr-4 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] bg-white shadow-sm"
            >
        </div>

        <!-- REFRESH BUTTON -->
        {{-- REFRESH TABLE --}}
        <button
            type="button"
            wire:click="resetTable"
            wire:loading.attr="disabled"
            title="Refresh Table"
            class="refresh-btn"
        >
            {{-- NORMAL STATE --}}
            <span
                wire:loading.remove
                wire:target="resetTable"
                class="refresh-icon"
            >
                <svg
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M4 4v5h5M20 20v-5h-5M5.5 9A7 7 0 0 1 18 6.5L20 9M18.5 15A7 7 0 0 1 6 17.5L4 15"
                    />
                </svg>
            </span>

            {{-- LOADING STATE --}}
            <span
                wire:loading
                wire:target="resetTable"
                class="refresh-loading"
            >
                <svg
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <circle
                        cx="12"
                        cy="12"
                        r="9"
                        stroke-width="3"
                        class="refresh-circle"
                    />
                    <path
                        d="M21 12a9 9 0 0 1-9 9"
                        stroke-width="3"
                        stroke-linecap="round"
                    />
                </svg>
            </span>

            <span class="hidden sm:inline">Refresh</span>
        </button>
    </div>


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
                <tr
                    class="border-b border-slate-200 bg-[#0C2C6B] text-xs font-semibold uppercase tracking-wider text-white">
                    <th class="py-3 px-4">#</th>
                    <th class="py-3 px-4">Setting Name</th>
                    <th class="py-3 px-4">Value</th>
                    @if ($this->canUpdate || $this->canDelete)
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
                            <span
                                class="font-mono text-xs bg-slate-100 text-slate-700 px-2 py-1 rounded border border-slate-200">
                                {{ $setting->name }}
                            </span>
                        </td>

                        <td class="py-4 px-4 text-slate-600">
                            @php
                                $val = $setting->value;
                                $isBool =
                                    is_bool($val) ||
                                    in_array(strtolower((string) $val), ['1', '0', 'true', 'false', 'yes', 'no']);
                                $isTrue = filter_var($val, FILTER_VALIDATE_BOOLEAN);
                            @endphp

                            @if ($isBool)
                              <span
                                    class="px-2.5 py-1 text-xs font-semibold rounded-full border text-white"
                                    style="background-color: {{ $isTrue ? '#0B2A5B' : '#D79B10' }}; border-color: {{ $isTrue ? '#0B2A5B' : '#D79B10' }};">
                                    {{ $isTrue ? 'true' : 'false' }}
                                </span>
                            @else
                                <span class="max-w-md truncate block text-slate-700 font-medium">
                                    {{ $setting->value ?? 'No value provided.' }}
                                </span>
                            @endif
                        </td>

                        <!-- Actions Column -->
                        @if ($this->canUpdate || $this->canDelete)
                            <td class="py-4 px-4 text-center">
                                <div class="flex gap-2 justify-center">
                                    {{-- EDIT BUTTON --}}
                                    @if ($this->canUpdate)
                                        <button wire:click="editSetting({{ $setting->SettingsID }})"
                                            title="Update Setting"
                                            class="inline-flex items-center justify-center px-3 py-2 rounded-lg bg-[#0C2C6B] text-white hover:bg-[#09204f] transition cursor-pointer text-xs font-semibold shadow">
                                            ✏️ Edit
                                        </button>
                                    @endif

                                    {{-- DELETE BUTTON --}}
                                    @if ($this->canDelete)
                                        <button wire:click="confirmDeleteSetting({{ $setting->SettingsID }})"
                                            title="Delete Setting"
                                            class="inline-flex items-center justify-center px-3 py-2 rounded-lg bg-amber-500 text-white hover:bg-amber-600 transition cursor-pointer text-xs font-semibold shadow">
                                            🗑️ Delete
                                        </button>
                                    @endif
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $this->canUpdate || $this->canDelete ? 4 : 3 }}"
                            class="text-center py-8 text-slate-500">
                            No settings configurations found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- PAGINATION -->
        @if ($settings->hasPages())
            <div class="px-4 py-4 border-t border-slate-200 bg-slate-50 flex justify-center items-center space-x-4">
                @if ($settings->onFirstPage())
                    <span class="px-4 py-2 bg-gray-200 text-gray-400 rounded text-xs cursor-not-allowed">Previous</span>
                @else
                    <button wire:click="previousPage" wire:loading.attr="disabled"
                        class="px-4 py-2 text-white rounded text-xs transition cursor-pointer"
                        style="background-color: #D79B10;">Previous</button>
                @endif

                <span class="text-xs font-medium text-slate-600">Page {{ $settings->currentPage() }} of
                    {{ $settings->lastPage() }}</span>

                @if ($settings->hasMorePages())
                    <button wire:click="nextPage" wire:loading.attr="disabled"
                        class="px-4 py-2 text-white rounded text-xs transition cursor-pointer"
                        style="background-color: #0C2C6B;">Next</button>
                @else
                    <span class="px-4 py-2 bg-gray-200 text-gray-400 rounded text-xs cursor-not-allowed">Next</span>
                @endif
            </div>
        @endif
    </div>

    <!-- ADD/EDIT SETTING MODAL -->
    @if ($isOpen)
        <div
            class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-950/70 p-4 backdrop-blur-sm overflow-y-auto">
            <div class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl my-8">

                <!-- MODAL HEADER -->
                <div class="bg-[#0C2C6B] px-6 py-4 text-white flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold">
                            {{ $setting_id ? 'Edit Setting' : 'Add New Setting' }}
                        </h3>
                        <p class="text-xs text-blue-100">Configure system options below.</p>
                    </div>
                    <button wire:click="closeModal"
                        class="flex h-8 w-8 items-center justify-center rounded-full bg-white/20 transition hover:bg-white/30 text-white font-bold cursor-pointer">
                        ✕
                    </button>
                </div>

                <form wire:submit.prevent="saveSetting">
                    <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">

                        <!-- Name -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Setting
                                Name</label>
                            <input type="text" wire:model="name"
                                class="w-full px-3.5 py-3 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0C2C6B]">
                            @error('name')
                                <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Value with Input Type Switcher -->
                        <div x-data="{
                            valueType: '{{ in_array(strtolower((string) $value), ['true', 'false', '1', '0']) ? 'boolean' : 'text' }}'
                        }">
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">Value Type / Input</label>
                                <div class="flex items-center space-x-2 text-xs">
                                    <span class="text-slate-400">Select:</span>
                                    <button type="button" @click="valueType = 'boolean'" :class="valueType === 'boolean' ? 'bg-[#0C2C6B] text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-3 py-1.5 rounded-lg font-semibold transition cursor-pointer">True/False</button>
                                    <button type="button" @click="valueType = 'text'" :class="valueType === 'text' ? 'bg-[#0C2C6B] text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-3 py-1.5 rounded-lg font-semibold transition cursor-pointer">Custom Value</button>
                                </div>
                            </div>

                            <!-- Boolean Selector (Button-Like Style with Larger Height & Padding) -->
                            <div x-show="valueType === 'boolean'" class="relative">
                                <select wire:model="value"
                                    class="w-full px-4 py-3 text-sm font-medium bg-slate-50 hover:bg-slate-100/80 border border-slate-200 rounded-xl appearance-none focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] focus:bg-white pr-10 cursor-pointer transition">
                                    <option value="true">True</option>
                                    <option value="false">False</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>

                            <!-- Custom Textarea Input -->
                            <div x-show="valueType === 'text'">
                                <textarea wire:model="value"
                                    class="w-full px-4 py-3 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0C2C6B]"
                                    rows="3" placeholder="Enter configuration value..."></textarea>
                            </div>

                            @error('value')
                                <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>

                    <!-- MODAL FOOTER -->
                    <div class="flex justify-end gap-2 border-t border-slate-200 px-6 py-3 bg-slate-50">
                        <button type="button" wire:click="closeModal"
                            class="px-4 py-2 text-xs font-semibold text-slate-700 bg-slate-200 rounded-lg hover:bg-slate-300 transition cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit"
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

            $wire.on('swal:error', (event) => {
                const data = event[0] || event;
                Swal.fire({
                    title: data.title,
                    html: data.text,
                    icon: 'error',
                    confirmButtonColor: '#0C2C6B'
                });
            });

            $wire.on('confirm-update', () => {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to update this setting?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#0B2A5B',
                    cancelButtonColor: '#D79B10',
                    confirmButtonText: 'Yes, update it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $wire.performUpdate();
                    }
                });
            });

            $wire.on('confirm-delete', (event) => {
                const settingId = event.id || event[0]?.id;
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#0B2A5B',
                    cancelButtonColor: '#D79B10',
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
