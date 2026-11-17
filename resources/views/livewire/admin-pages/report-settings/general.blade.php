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
                    <th>Variant</th>
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
                                @if ((int) $setting->BranchID === 0)
                                    All Branches
                                @else
                                    {{ $branchNames[$setting->BranchID] ?? 'Unknown Branch' }}
                                @endif
                            </span>
                        </td>

                        <td class="cell-name">
                            <span class="value-text">

                                    {{ $setting->name ?? 'Unknown Name' }}

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
                                        <x-action-btn icon="edit" tone="edit"
                                            wire:click="editSetting({{ $setting->SettingsID }})"
                                            title="Update Setting" />
                                    @endif

                                    {{-- DELETE BUTTON --}}
                                    @if ($this->canDelete)
                                        <x-action-btn icon="delete" tone="delete"
                                            wire:click="confirmDeleteSetting({{ $setting->SettingsID }})"
                                            title="Delete Setting" />
                                    @endif
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $this->canUpdate || $this->canDelete ? 5 : 4 }}"
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

    <!-- ADD/EDIT SETTING MODAL --><!--
      Rendered ALWAYS and toggled with x-show="$wire.isOpen" (same pattern as
      the page tabs): inserting this subtree via a morph made Alpine skip its
      x-show effects, so the value-type toggles stopped reacting. A static
      subtree stays fully reactive no matter how often it is shown/hidden.
    -->
    <div class="modal-overlay" x-data="{ open: $wire.entangle('isOpen') }" :class="open ? '' : 'force-hidden'" x-cloak>
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

                   <div>
                    <label class="field-label">Branch</label>

                    <select wire:model="BranchID" class="field-input">
                        <option value="0">All Branches</option>

                        @foreach ($branches as $branch)
                            <option value="{{ $branch->BranchID }}">
                                {{ $branch->branchname }}
                            </option>
                        @endforeach
                    </select>

                    @error('BranchID')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>
                        <!-- Name -->
                        <div>
                            <label class="field-label">Setting
                                Name</label>
                            <input type="text" wire:model.blur="name"
                                class="field-input">
                            @error('name')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Value with Input Type Switcher.
                             valueType lives purely in Alpine; the component
                             pushes the correct initial panel via the
                             setting-modal-type browser event when it opens the
                             modal (one-way: no entangle sync can clobber it). -->
                        <div x-data="{ valueType: 'text' }" @setting-modal-type.window="valueType = $event.detail.type" x-cloak>
                            <div class="value-type-header">
                                <label class="value-type-label">Value Type / Input</label>
                                <div class="value-type-toggle">
                                    <span class="toggle-label">Select:</span>
                                    <button type="button" @click="valueType = 'boolean'" :class="valueType === 'boolean' ? 'toggle-btn-active' : 'toggle-btn-inactive'" class="toggle-btn">True/False</button>
                                    <button type="button" @click="valueType = 'text'" :class="valueType === 'text' ? 'toggle-btn-active' : 'toggle-btn-inactive'" class="toggle-btn">Custom Value</button>
                                    <button type="button" @click="valueType = 'signature'" :class="valueType === 'signature' ? 'toggle-btn-active' : 'toggle-btn-inactive'" class="toggle-btn">Others</button>
                                </div>
                            </div>

                            <!-- Boolean Selector (Button-Like Style with Larger Height & Padding).
                                 Visibility is driven by :class instead of x-show:
                                 x-show effects are unreliable on morph-touched
                                 subtrees, :class bindings are not. -->
                            <div :class="valueType === 'boolean' ? '' : 'force-hidden'" class="boolean-select-wrapper" x-cloak>
                                <select wire:model.blur="value"
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
                            <div :class="valueType === 'text' ? '' : 'force-hidden'" x-cloak>
                                <textarea wire:model.blur="value"
                                    class="field-textarea"
                                    rows="3" placeholder="Enter configuration value..."></textarea>
                            </div>

                            <!-- Others — Signature Upload (stores storage path as the value) -->
                            <div :class="valueType === 'signature' ? '' : 'force-hidden'" x-cloak>
                            <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-4">

                                <label class="field-label">Cashier Signature</label>

                                <p class="mb-3 text-xs text-slate-500">
                                    Upload or draw the cashier signature. The image will be saved automatically
                                    and its storage path will be placed into the Custom Value.
                                </p>

                                {{-- Automatically generated storage path --}}
                                <div class="mb-3">
                                    <label class="field-label">Custom Value</label>

                                    <input
                                        type="text"
                                        wire:model="value"
                                        readonly
                                        class="field-input bg-slate-100"
                                        placeholder="Signature path will appear here..."
                                    >

                                    @if ($value)
                                        <p class="mt-1 text-xs text-[var(--color-primary)]">
                                            ✓ Signature path automatically generated
                                        </p>
                                    @endif
                                </div>

                                {{-- Existing signature preview --}}
                                @if (str_contains((string) $value, 'SIG_'))
                                    <div class="mb-3 flex items-center gap-4">
                                        <img
                                            src="{{ asset('storage/' . $value) }}"
                                            alt="Current signature"
                                            class="h-12 rounded border border-slate-200 bg-white px-3"
                                        >

                                        <span class="text-xs font-semibold text-[var(--color-primary)]">
                                            ✓ Current signature
                                        </span>
                                    </div>
                                @endif

                                {{-- Draw signature --}}
                                <div
                                    x-data="signaturePad()"
                                    x-init="initPad()"
                                    class="mb-3"
                                >
                                    <canvas
                                        x-ref="pad"
                                        width="520"
                                        height="140"
                                        class="w-full cursor-crosshair rounded-lg border border-slate-300 bg-white touch-none"
                                    ></canvas>

                                    <div class="mt-2 flex gap-2">

                                        <button
                                            type="button"
                                            @click="clear"
                                            class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-bold text-slate-600 transition hover:bg-slate-100"
                                        >
                                            Clear
                                        </button>

                                        <button
                                            type="button"
                                            @click="save($wire)"
                                            class="rounded-lg bg-[var(--color-primary)] px-3 py-1.5 text-xs font-bold text-white transition hover:bg-[var(--color-primary-mid)]"
                                        >
                                            Use Drawn Signature
                                        </button>

                                    </div>
                                </div>

                               {{-- Upload signature --}}
<div class="mt-4">

    <label
        class="inline-flex cursor-pointer items-center gap-2 rounded-lg
               border border-slate-300 bg-white px-3 py-2
               text-xs font-bold text-slate-600 transition
               hover:bg-slate-100"
    >
        <svg
            class="h-4 w-4"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 16V4m0 0L8 8m4-4 4 4M4 16v3a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-3"
            />
        </svg>

        Upload Signature Image

        <input
            type="file"
            accept="image/png,image/jpeg"
            wire:model="signatureUpload"
            class="hidden"
        >
    </label>

    {{-- Upload loading --}}
    <div
        wire:loading
        wire:target="signatureUpload"
        class="mt-2 text-xs font-semibold text-slate-500"
    >
        Uploading signature...
    </div>

    {{-- Upload errors --}}
    @error('signatureUpload')
        <div class="mt-2 text-xs font-semibold text-red-600">
            {{ $message }}
        </div>
    @enderror

    {{-- Uploaded successfully --}}
    @if ($signatureUpload)
        <div
            class="mt-3 rounded-lg border border-[var(--color-primary-tint-border)]
                   bg-[var(--color-primary-tint)] p-3"
        >
            <div class="mb-2 text-xs font-bold text-[var(--color-primary)]">
                Signature selected
            </div>

            <div class="flex items-center gap-3">

                <img
                    src="{{ $signatureUpload->temporaryUrl() }}"
                    alt="Uploaded signature"
                    class="h-16 max-w-xs rounded border
                           border-slate-200 bg-white px-3"
                >

                <button
                    type="button"
                    wire:click="$set('signatureUpload', null)"
                    class="rounded-lg border border-red-200
                           bg-white px-3 py-1.5 text-xs
                           font-bold text-red-600
                           hover:bg-red-50"
                >
                    Remove
                </button>

            </div>

            <p class="mt-2 text-xs text-[var(--color-primary)]">
                Click <strong>Save Setting</strong> or
                <strong>Update Setting</strong> to store this signature.
            </p>
        </div>
    @endif

</div>

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

    {{-- All styles now live in general.css --}}

    @script
        <script>
            {{-- swal:success and swal:error are handled globally by resources/js/shared/sweet-alert-handler.js --}}

            $wire.on('confirm-update', () => {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to update this setting?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: 'var(--color-primary)',
                    cancelButtonColor: 'var(--color-accent)',
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
                    confirmButtonColor: 'var(--color-primary)',
                    cancelButtonColor: 'var(--color-accent)',
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

            // Bridge the server's value-type push (sent when the modal opens)
            // to the Alpine scope inside the modal.
            $wire.on('setting-modal-type', (event) => {
                const detail = event && event.type ? event : (event && event[0]) || {};
                window.dispatchEvent(new CustomEvent('setting-modal-type', { detail }));
            });

            function signaturePad() {
    return {
        canvas: null,
        ctx: null,
        drawing: false,

        initPad() {
            this.canvas = this.$refs.pad;
            this.ctx = this.canvas.getContext('2d');

            this.ctx.lineWidth = 2;
            this.ctx.lineCap = 'round';
            this.ctx.lineJoin = 'round';
            this.ctx.strokeStyle = '#000';

            this.canvas.addEventListener('pointerdown', (event) => {
                this.drawing = true;

                const rect = this.canvas.getBoundingClientRect();

                const x =
                    (event.clientX - rect.left) *
                    (this.canvas.width / rect.width);

                const y =
                    (event.clientY - rect.top) *
                    (this.canvas.height / rect.height);

                this.ctx.beginPath();
                this.ctx.moveTo(x, y);
            });

            this.canvas.addEventListener('pointermove', (event) => {
                if (!this.drawing) {
                    return;
                }

                const rect = this.canvas.getBoundingClientRect();

                const x =
                    (event.clientX - rect.left) *
                    (this.canvas.width / rect.width);

                const y =
                    (event.clientY - rect.top) *
                    (this.canvas.height / rect.height);

                this.ctx.lineTo(x, y);
                this.ctx.stroke();
            });

            this.canvas.addEventListener('pointerup', () => {
                this.drawing = false;
            });

            this.canvas.addEventListener('pointerleave', () => {
                this.drawing = false;
            });

            this.canvas.addEventListener('pointercancel', () => {
                this.drawing = false;
            });
        },

        clear() {
            this.ctx.clearRect(
                0,
                0,
                this.canvas.width,
                this.canvas.height
            );
        },

        save($wire) {
            const dataUrl = this.canvas.toDataURL('image/png');

            const base64 = dataUrl.split(',')[1];

            if (!base64) {
                Swal.fire({
                    title: 'No Signature',
                    text: 'Please draw a signature first.',
                    icon: 'warning'
                });

                return;
            }

            // Send signature to Livewire.
            // saveSignatureSetting() will save the image and
            // automatically set $this->value to the storage path.
            $wire.call('saveSignatureSetting', base64);
        }
    };
}

        </script>
    @endscript


</div>
