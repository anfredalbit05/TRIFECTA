<div class="settings-container w-full px-4 sm:px-6 lg:px-8 py-6">

    <!-- HEADER -->
    <div class="members-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-2">
        <h2 class="masterlist-title text-2xl font-bold text-slate-800">
            General Settings
        </h2>

        <button
            wire:click="openCreateModal"
            class="masterlist-btn inline-flex items-center justify-center px-4 py-2 rounded-lg bg-[#0C2C6B] text-white hover:bg-[#09204f] text-sm font-semibold shadow transition cursor-pointer">
            Add Setting
        </button>
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
                    <th class="py-3 px-4 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 text-sm text-slate-700">
                @forelse($settings as $index =>$setting)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-4 px-4 font-medium text-slate-500">
                            {{ $index + 1 }}
                        </td>

                        <td class="py-4 px-4 font-semibold text-slate-900">
                            {{ $setting->name }}
                        </td>

                        <td class="py-4 px-4 text-slate-500 max-w-xs truncate">
                            {{ $setting->value ?? 'No value provided.' }}
                        </td>

                        <!-- Actions Column -->
                        <td class="py-4 px-4 text-center">
                            <div class="flex gap-2 justify-center">
                                {{-- EDIT BUTTON --}}
                                <button
                                    wire:click="editSetting({{ $setting->SettingsID }})"
                                    title="Update Setting"
                                    class="inline-flex items-center justify-center px-3 py-2 rounded-lg bg-[#0C2C6B] text-white hover:bg-[#09204f] transition cursor-pointer text-xs font-semibold shadow">
                                    ✏️
                                </button>

                                {{-- DELETE BUTTON --}}
                                <button
                                    wire:click="confirmDeleteSetting({{ $setting->SettingsID }})"
                                    title="Delete Setting"
                                    class="inline-flex items-center justify-center px-3 py-2 rounded-lg bg-red-500 text-white hover:bg-red-600 transition cursor-pointer text-xs font-semibold shadow">
                                    🗑️
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-8 text-slate-500">
                            No settings configurations found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
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
                            Save Setting
                        </button>
                    </div>
                </form>

            </div>
        </div>
    @endif

    @script
    <script>
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
