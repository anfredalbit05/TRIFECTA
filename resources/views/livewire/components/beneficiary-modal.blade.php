<div>
    @if ($open)
        <div class="member-modal-overlay">
            <div class="member-modal">

                <div class="member-modal-header">
                    <h3 class="member-modal-title">Beneficiaries</h3>
                    <button type="button" wire:click="close" class="member-modal-close">&times;</button>
                </div>

                <div class="member-modal-body">
                    <p class="text-sm text-slate-600 mb-3">
                        Member: <strong>{{ $memberName }}</strong> (max 3 beneficiaries)
                    </p>

                    @foreach ($rows as $i => $row)
                        <div wire:key="beneficiary-row-{{ $i }}"
                             class="border border-slate-200 rounded-xl p-3 mb-3 {{ $row['remove'] ? 'opacity-50' : '' }}">

                            {{-- <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold text-slate-500">Beneficiary {{ $i + 1 }}</span>

                                @if ($row['id'])
                                    <label class="text-xs text-red-600 flex items-center gap-1 cursor-pointer">
                                        <input type="checkbox" wire:model.live="rows.{{ $i }}.remove">
                                        Remove
                                    </label>
                                @endif
                            </div> --}}

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                <div class="sm:col-span-3">
                                    <input type="text" wire:model="rows.{{ $i }}.fullname"
                                           placeholder="Full name" @disabled($row['remove'])
                                           class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                                    @error("rows.$i.fullname") <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <input type="date" wire:model="rows.{{ $i }}.birthdate"
                                           min="1900-01-01" max="{{ $this->today() }}" @disabled($row['remove'])
                                           class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                                    @error("rows.$i.birthdate") <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="sm:col-span-2">
                                    <input type="text" wire:model="rows.{{ $i }}.relationship"
                                           placeholder="Relationship" @disabled($row['remove'])
                                           class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                                    @error("rows.$i.relationship") <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="member-modal-footer">

                    {{-- CONFIRMATION CHECKBOX --}}
                    <label class="flex items-start gap-2 text-xs text-slate-600 cursor-pointer"
                           style="flex-basis:100%;width:100%;margin:0 0 10px;">
                        <input type="checkbox" wire:model.live="confirmed" class="mt-0.5">
                        <span>I confirm that the beneficiary details above are correct and I want to save these changes.</span>
                    </label>

                    <button type="button" wire:click="close" class="modal-btn modal-btn-cancel">Cancel</button>

                    <button type="button"
                            wire:click="reviewChanges"
                            wire:loading.attr="disabled"
                            @disabled(! $confirmed)
                            @style(['opacity: .5' => ! $confirmed, 'cursor: not-allowed' => ! $confirmed])
                            title="{{ $confirmed ? 'Review and save changes' : 'Tick the confirmation checkbox first' }}"
                            class="modal-btn modal-btn-approve">
                        <span wire:loading.remove wire:target="reviewChanges,save">Review &amp; Save</span>
                        <span wire:loading wire:target="reviewChanges,save">Processing...</span>
                    </button>
                </div>

            </div>
        </div>
    @endif

    @script
    <script>
        $wire.on('beneficiary-confirm-save', (data) => {

            const d = Array.isArray(data) ? data[0] : data;

            const esc = (v) => String(v ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');

            const show = (v) => (v === null || v === undefined || v === '')
                ? '<em style="color:#94a3b8;">empty</em>'
                : esc(v);

            const colors = {
                Added:   '#16a34a',
                Updated: '#d97706',
                Removed: '#dc2626'
            };

            let html = '<div style="text-align:left;max-height:340px;overflow:auto;">'
                + '<p style="margin:0 0 12px;font-size:13px;color:#475569;">'
                + 'Please review the changes for <b style="color:var(--color-primary);">' + esc(d.name) + '</b>:'
                + '</p>';

            (d.changes || []).forEach((c) => {

                html += '<div style="border:1px solid #e2e8f0;border-radius:10px;padding:10px 12px;margin-bottom:10px;">'
                    + '<div style="display:flex;justify-content:space-between;margin-bottom:6px;">'
                    + '<strong style="font-size:13px;">' + esc(c.label) + '</strong>'
                    + '<span style="font-size:11px;font-weight:700;color:' + (colors[c.type] || '#475569') + ';">'
                    + esc(c.type).toUpperCase() + '</span>'
                    + '</div>';

                (c.fields || []).forEach((f) => {

                    html += '<div style="font-size:12px;line-height:1.6;color:#334155;">'
                        + '<span style="color:#64748b;">' + esc(f.field) + ':</span> ';

                    if (c.type === 'Added') {
                        html += '<b style="color:#16a34a;">' + show(f.new) + '</b>';
                    } else if (c.type === 'Removed') {
                        html += '<s style="color:#dc2626;">' + show(f.old) + '</s>';
                    } else {
                        html += '<s style="color:#94a3b8;">' + show(f.old) + '</s>'
                            + ' &rarr; '
                            + '<b style="color:#d97706;">' + show(f.new) + '</b>';
                    }

                    html += '</div>';
                });

                html += '</div>';
            });

            html += '</div>';

            Swal.fire({
                title: 'Confirm Changes',
                html: html,
                icon: 'question',
                width: 560,
                showCancelButton: true,
                confirmButtonText: 'Yes, save changes',
                cancelButtonText: 'Go back',
                confirmButtonColor: 'var(--color-primary)',
                cancelButtonColor: 'var(--color-accent)',
                allowOutsideClick: false,
            }).then((result) => {

                if (result.isConfirmed) {

                    Swal.fire({
                        title: 'Saving...',
                        text: 'Please wait.',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => Swal.showLoading()
                    });

                    $wire.save();
                }

            });

        });
    </script>
    @endscript
</div>
