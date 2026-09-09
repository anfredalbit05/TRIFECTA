<div class="settings-container">

   <!-- HEADER & POST TOGGLE BUTTON -->
    <div class="members-header">
        <div>
            <h2 class="masterlist-title">General Settings</h2>
            <p class="masterlist-description">Manage system-wide configurations and rules (e.g., Maintenance Mode, Coverage, Limits)</p>
        </div>

        @if ($this->canCreate)
            <button
                type="button"
                wire:click="openCreateModal"
                class="masterlist-btn"
            >
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Setting
            </button>
        @endif
    </div>

    <!-- SEARCH INPUT & REFRESH BUTTON CONTAINER -->
    <div class="settings-toolbar">
        <!-- SEARCH INPUT -->
    <div class="search-wrapper">
        <span class="search-icon">
            <svg
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"
                />
            </svg>
        </span>

        <input
            type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="Search settings..."
            class="search-input"
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
        <div class="success-banner">
            {{ session('message') }}
        </div>
    @endif

    <!-- SETTINGS TABLE -->
    <div class="settings-table-wrapper">
        <table class="settings-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Setting Name</th>
                    <th>Value</th>
                    @if ($this->canUpdate || $this->canDelete)
                        <th class="th-center">Action</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($settings as $index => $setting)
                    <tr class="setting-row">
                        <td class="cell-index">
                            {{ $settings->firstItem() + $index }}
                        </td>

                        <td class="cell-name">
                            <span class="name-badge">
                                {{ $setting->name }}
                            </span>
                        </td>

                        <td class="cell-value">
                            @php
                                $val = $setting->value;
                                $isBool =
                                    is_bool($val) ||
                                    in_array(strtolower((string) $val), ['1', '0', 'true', 'false', 'yes', 'no']);
                                $isTrue = filter_var($val, FILTER_VALIDATE_BOOLEAN);
                            @endphp

                            @if ($isBool)
                              <span class="bool-badge {{ $isTrue ? 'bool-badge-true' : 'bool-badge-false' }}">
                                    {{ $isTrue ? 'true' : 'false' }}
                                </span>
                            @else
                                <span class="value-text">
                                    {{ $setting->value ?? 'No value provided.' }}
                                </span>
                            @endif
                        </td>

                        <!-- Actions Column -->
                        @if ($this->canUpdate || $this->canDelete)
                            <td class="cell-actions-center">
                                <div class="actions-row">
                                    {{-- EDIT BUTTON --}}
                                    @if ($this->canUpdate)
                                        <button wire:click="editSetting({{ $setting->SettingsID }})"
                                            title="Update Setting"
                                            class="edit-btn">
                                            ✏️ Edit
                                        </button>
                                    @endif

                                    {{-- DELETE BUTTON --}}
                                    @if ($this->canDelete)
                                        <button wire:click="confirmDeleteSetting({{ $setting->SettingsID }})"
                                            title="Delete Setting"
                                            class="delete-btn">
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
                            class="empty-row">
                            No settings configurations found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- PAGINATION -->
        @if ($settings->hasPages())
            <div class="pagination-wrapper">
                @if ($settings->onFirstPage())
                    <span class="pagination-btn-disabled">Previous</span>
                @else
                    <button wire:click="previousPage" wire:loading.attr="disabled"
                        class="pagination-btn pagination-btn-prev">Previous</button>
                @endif

                <span class="pagination-label">Page {{ $settings->currentPage() }} of
                    {{ $settings->lastPage() }}</span>

                @if ($settings->hasMorePages())
                    <button wire:click="nextPage" wire:loading.attr="disabled"
                        class="pagination-btn pagination-btn-next">Next</button>
                @else
                    <span class="pagination-btn-disabled">Next</span>
                @endif
            </div>
        @endif
    </div>

    <!-- ADD/EDIT SETTING MODAL -->
    @if ($isOpen)
        <div class="modal-overlay">
            <div class="modal-box">

                <!-- MODAL HEADER -->
                <div class="modal-header">
                    <div>
                        <h3 class="modal-title">
                            {{ $setting_id ? 'Edit Setting' : 'Add New Setting' }}
                        </h3>
                        <p class="modal-subtitle">Configure system options below.</p>
                    </div>
                    <button wire:click="closeModal"
                        class="modal-close-btn">
                        ✕
                    </button>
                </div>

                <form wire:submit.prevent="saveSetting">
                    <div class="modal-body">

                        <!-- Name -->
                        <div>
                            <label class="field-label">Setting
                                Name</label>
                            <input type="text" wire:model="name"
                                class="field-input">
                            @error('name')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Value with Input Type Switcher -->
                        <div x-data="{
                            valueType: '{{ in_array(strtolower((string) $value), ['true', 'false', '1', '0']) ? 'boolean' : 'text' }}'
                        }">
                            <div class="value-type-header">
                                <label class="value-type-label">Value Type / Input</label>
                                <div class="value-type-toggle">
                                    <span class="toggle-label">Select:</span>
                                    <button type="button" @click="valueType = 'boolean'" :class="valueType === 'boolean' ? 'toggle-btn-active' : 'toggle-btn-inactive'" class="toggle-btn">True/False</button>
                                    <button type="button" @click="valueType = 'text'" :class="valueType === 'text' ? 'toggle-btn-active' : 'toggle-btn-inactive'" class="toggle-btn">Custom Value</button>
                                </div>
                            </div>

                            <!-- Boolean Selector (Button-Like Style with Larger Height & Padding) -->
                            <div x-show="valueType === 'boolean'" class="boolean-select-wrapper">
                                <select wire:model="value"
                                    class="boolean-select">
                                    <option value="true">True</option>
                                    <option value="false">False</option>
                                </select>
                                <div class="select-chevron">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>

                            <!-- Custom Textarea Input -->
                            <div x-show="valueType === 'text'">
                                <textarea wire:model="value"
                                    class="field-textarea"
                                    rows="3" placeholder="Enter configuration value..."></textarea>
                            </div>

                            @error('value')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>

                    <!-- MODAL FOOTER -->
                    <div class="modal-footer">
                        <button type="button" wire:click="closeModal"
                            class="modal-cancel-btn">
                            Cancel
                        </button>
                        <button type="submit"
                            class="modal-submit-btn">
                            {{ $setting_id ? 'Update Setting' : 'Save Setting' }}
                        </button>
                    </div>
                </form>

            </div>
        </div>
    @endif

    {{-- All styles now live in general.css --}}

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
