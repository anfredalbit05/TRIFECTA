/**
 * Shared SweetAlert2 flow (single source of truth).
 *
 * Extracted from resources/views/components/shared/sweet-alert-handler.blade.php
 * and imported once from resources/js/app.js. Defines `window.SwalFlow` and
 * wires the Livewire events used across the app (toast, swal:*, txn-modal-*).
 *
 * PHP events ($this->dispatch(...)):
 *
 * • toast            type, message                          → top-right toast (3s)
 * • swal:confirm     title, text|html, icon, method|nextMethod,
 *                    confirmText, cancelText, requireConfirmWord,
 *                    loadingTitle, loadingText, redirect    → confirmation dialog;
 *                    requireConfirmWord: 'ACTIVATE' enables
 *                    typed confirmation (case-insensitive)
 * • swal:success     title, text|message, timer, redirect   → success dialog (closes spinners)
 * • swal:error       title, text|message|html               → error dialog (closes spinners)
 * • swal:close       —                                      → closes any open Swal (legacy: swal-close)
 *
 * JS API (for @script blocks inside components):
 *
 * SwalFlow.confirm(options, () => $wire.method())            → confirm dialog (typed word supported)
 * SwalFlow.prompt(options, (value) => $wire.method(value))   → text/textarea prompt
 * SwalFlow.loading('Working...', 'Please wait.')             → spinner dialog
 * SwalFlow.close()                                           → close any open Swal
 */

document.addEventListener('livewire:init', () => {
    // Shared theme colors — resolve from the global palette in app.css.
    const SWAL_PRIMARY = 'var(--color-primary)';
    const SWAL_ACCENT = 'var(--color-accent)';
    const SWAL_SUCCESS = '#2E8B57';
    const SWAL_DANGER = '#B03A2E';
    const SWAL_INFO = 'var(--color-primary-light)';

    // Normalize Livewire 3 payloads (event[0] vs event)
    const payload = (event) => (Array.isArray(event) ? event[0] : event) || {};

    const closeSwal = () => {
        if (window.Swal && Swal.isVisible()) Swal.close();
    };

    const fireLoading = (title = 'Processing...', text = 'Please wait...') => {
        Swal.fire({
            title,
            text,
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            showCancelButton: false,
            didOpen: () => Swal.showLoading(),
        });
    };

    // ==========================================================
    // CONFIRM DIALOG (optional typed confirmation word)
    // ==========================================================
    const openConfirm = (opts = {}, onConfirmed = null) => {
        Swal.fire({
            title: opts.title || 'Are you sure?',
            text: opts.html ? undefined : opts.text || 'Do you want to proceed?',
            html: opts.html,
            icon: opts.icon || 'warning',
            input: opts.requireConfirmWord ? 'text' : undefined,
            inputPlaceholder: opts.requireConfirmWord
                ? opts.inputPlaceholder || `Type ${opts.requireConfirmWord} to continue`
                : undefined,
            inputAttributes: { autocapitalize: 'characters', autocomplete: 'off' },
            showCancelButton: true,
            confirmButtonText: opts.confirmText || 'Yes, proceed!',
            cancelButtonText: opts.cancelText || 'Cancel',
            confirmButtonColor: SWAL_PRIMARY,
            cancelButtonColor: SWAL_ACCENT,
            allowOutsideClick: false,
            allowEscapeKey: false,

            // Keep the confirm button disabled until the typed word matches
            didOpen: () => {
                if (!opts.requireConfirmWord) return;

                const input = Swal.getInput();
                const button = Swal.getConfirmButton();
                if (!input || !button) return;

                const sync = () => {
                    const valid = input.value.trim().toUpperCase() === opts.requireConfirmWord.toUpperCase();
                    button.disabled = !valid;
                    button.style.opacity = valid ? '1' : '.5';
                    button.style.cursor = valid ? 'pointer' : 'not-allowed';
                };

                button.disabled = true;
                button.style.opacity = '.5';
                button.style.cursor = 'not-allowed';
                input.addEventListener('input', sync);
            },

            preConfirm: () => {
                if (!opts.requireConfirmWord) return true;

                const input = Swal.getInput();
                if (!input || input.value.trim().toUpperCase() !== opts.requireConfirmWord.toUpperCase()) {
                    Swal.showValidationMessage(`Please type ${opts.requireConfirmWord} exactly to continue.`);
                    return false;
                }
                return true;
            },
        }).then((result) => {
            if (!result.isConfirmed) return;

            fireLoading(opts.loadingTitle || 'Processing...', opts.loadingText || 'Please wait...');

            if (typeof onConfirmed === 'function') {
                onConfirmed();
                return;
            }

            if (opts.method || opts.nextMethod) {
                Livewire.dispatch(opts.method || opts.nextMethod);
            } else if (opts.redirect) {
                setTimeout(() => {
                    window.location.href = opts.redirect;
                }, 800);
            } else {
                setTimeout(closeSwal, 800);
            }
        });
    };

    // ==========================================================
    // PROMPT DIALOG (text / textarea with optional validation)
    // ==========================================================
    const openPrompt = (opts = {}, onConfirmed = null) => {
        Swal.fire({
            title: opts.title || 'Please confirm',
            icon: opts.icon || 'warning',
            input: opts.input || 'textarea',
            inputLabel: opts.inputLabel,
            inputPlaceholder: opts.inputPlaceholder,
            inputAttributes: {
                maxlength: opts.maxLength,
                rows: opts.rows,
                autocomplete: 'off',
            },
            showCancelButton: true,
            confirmButtonText: opts.confirmText || 'Confirm',
            cancelButtonText: opts.cancelText || 'Cancel',
            confirmButtonColor: SWAL_PRIMARY,
            cancelButtonColor: SWAL_ACCENT,
            allowOutsideClick: false,
            allowEscapeKey: false,
            inputValidator: (value) => {
                if (opts.required && (!value || value.trim() === '')) {
                    return opts.requiredMessage || 'This field is required.';
                }
            },
        }).then((result) => {
            if (!result.isConfirmed) return;

            fireLoading(opts.loadingTitle || 'Processing...', opts.loadingText || 'Please wait...');

            if (typeof onConfirmed === 'function') onConfirmed(result.value);
        });
    };

    // ==========================================================
    // PUBLIC JS API — used by component @script blocks
    // ==========================================================
    window.SwalFlow = {
        confirm: openConfirm,
        prompt: openPrompt,
        loading: fireLoading,
        close: closeSwal,
    };

    // ==========================================================
    // TOAST NOTIFICATIONS
    // ==========================================================
    Livewire.on('toast', (event) => {
        const data = payload(event);

        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: data.type || 'info',
            title: data.message || data.text || '',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });
    });

    // ==========================================================
    // GENERAL CONFIRMATION (from PHP)
    // ==========================================================
    Livewire.on('swal:confirm', (event) => openConfirm(payload(event)));

    // ==========================================================
    // SUCCESS ALERT (replaces any open spinner)
    // ==========================================================
    Livewire.on('swal:success', (event) => {
        const data = payload(event);

        Swal.close();

        Swal.fire({
            icon: 'success',
            title: data.title || 'Success!',
            text: data.text || data.message || 'Operation completed successfully.',
            confirmButtonText: 'OK',
            confirmButtonColor: SWAL_PRIMARY,
            showConfirmButton: true,
            allowOutsideClick: false,
            timer: data.timer,
            willClose: () => {
                if (data.redirect) window.location.href = data.redirect;
            },
        });
    });

    // ==========================================================
    // ERROR ALERT (replaces any open spinner)
    // ==========================================================
    Livewire.on('swal:error', (event) => {
        const data = payload(event);

        Swal.close();

        Swal.fire({
            icon: 'error',
            title: data.title || 'Error',
            html: data.html,
            text: data.html ? undefined : data.text || data.message || 'Something went wrong.',
            confirmButtonColor: SWAL_PRIMARY,
            allowOutsideClick: false,
        });
    });

    // ==========================================================
    // CLOSE LOADING / ANY ALERT
    // ==========================================================
    Livewire.on('swal:close', closeSwal);
    Livewire.on('swal-close', closeSwal); // legacy alias

    // ==========================================================
    // ACTIVATION REVIEW MODALS — typed confirmations
    // ==========================================================
    // The regular + satellite activation review modals dispatch these
    // plain browser events instead of using inline @script handlers
    // (which proved unreliable in this app — handlers intermittently
    // never register, leaving dead buttons). The events name the OPEN
    // modal that fired them, so the call below lands on that component
    // with its state (selected activation, remarks) already loaded.
    // The transaction-list shell's tab-level handlers
    // (txn-approve-activation-prompt etc.) stay untouched — modal events
    // use the txn-modal-* namespace to avoid double-firing.
    // ==========================================================

    const callByName = (name, method, ...args) => {
        const el = [...document.querySelectorAll('[wire\\:id]')].find(
            (el) => JSON.parse(el.getAttribute('wire:snapshot') || '{}').memo?.name === name,
        );

        const wire = el ? window.Livewire.find(el.getAttribute('wire:id')) : null;

        if (!wire) {
            Swal.fire({
                icon: 'error',
                title: 'Modal Not Available',
                text: 'The review modal is no longer open. Please reopen it and try again.',
                confirmButtonColor: SWAL_PRIMARY,
            });
            return;
        }

        wire.call(method, ...args);
    };

    const MODAL_NAMES = {
        regular: 'admin-pages.masterlist.activation-review-modal',
        satellite: 'admin-pages.masterlist.satellite-members-components.satellite-members-activation-review',
    };

    const firstOpenModal = () => {
        for (const name of Object.values(MODAL_NAMES)) {
            const el = [...document.querySelectorAll('[wire\\:id]')].find(
                (el) => JSON.parse(el.getAttribute('wire:snapshot') || '{}').memo?.name === name,
            );

            if (el) return name;
        }

        return null;
    };

    // ---------- MODAL APPROVE (typed APPROVE) ----------
    window.Livewire.on('txn-modal-approve-activation', (raw) => {
        const d = payload(raw);

        SwalFlow.confirm(
            {
                title: 'Approve Activation?',
                html:
                    'You are about to approve the activation of ' +
                    '<strong style="color: var(--color-primary);">' +
                    (d.memberName || 'this member') +
                    '</strong>' +
                    ' for <strong style="color: var(--color-primary);">₱' +
                    (d.amount || '0.00') +
                    '</strong>.' +
                    ' This activates the member account, writes wallets/ledgers and sends emails.' +
                    '<br><br>Type <strong>APPROVE</strong> to confirm.',
                icon: 'question',
                requireConfirmWord: 'APPROVE',
                inputPlaceholder: 'APPROVE',
                confirmText: 'Yes, approve!',
                cancelText: 'Cancel',
                loadingTitle: 'Approving activation...',
                loadingText: 'Updating records and sending emails.',
            },
            () => {
                // The payload names the modal that fired (both may be mounted
                // on the same page); firstOpenModal is a fallback.
                const name = d.modal && MODAL_NAMES[d.modal] ? MODAL_NAMES[d.modal] : firstOpenModal();

                if (name === MODAL_NAMES.regular) {
                    callByName(MODAL_NAMES.regular, 'approve');
                } else if (name === MODAL_NAMES.satellite) {
                    callByName(MODAL_NAMES.satellite, 'approve');
                }
            },
        );
    });

    // ---------- MODAL REJECT (remarks required) ----------
    window.Livewire.on('txn-modal-reject-activation', (raw) => {
        const d = payload(raw);

        SwalFlow.prompt(
            {
                title: 'Reject Activation?',
                icon: 'warning',
                input: 'textarea',
                inputLabel: 'Rejection remarks',
                inputPlaceholder: 'Enter the reason for rejection...',
                maxLength: 1000,
                rows: 5,
                required: true,
                requiredMessage: 'Remarks are required.',
                confirmText: 'Reject',
                cancelText: 'Cancel',
                loadingTitle: 'Rejecting activation...',
                loadingText: 'Sending email and updating records.',
            },
            (value) => {
                // submit-rejection sets remarks then rejects on that modal.
                const name = d.modal && MODAL_NAMES[d.modal] ? MODAL_NAMES[d.modal] : firstOpenModal();

                if (name === MODAL_NAMES.regular) {
                    callByName(MODAL_NAMES.regular, 'submitRejection', value);
                } else if (name === MODAL_NAMES.satellite) {
                    callByName(MODAL_NAMES.satellite, 'submitRejection', value);
                }
            },
        );
    });

    // ---------- MODAL RESUBMIT (typed RESUBMIT) ----------
    window.Livewire.on('txn-modal-resubmit-activation', (raw) => {
        const d = payload(raw);

        SwalFlow.confirm(
            {
                title: 'Resubmit Activation?',
                html:
                    'The activation request will be reopened and set back to ' +
                    '<strong style="color: var(--color-primary);">Pending</strong> for review.' +
                    ' The full history trail is kept.' +
                    '<br><br>Type <strong>RESUBMIT</strong> to confirm.',
                icon: 'question',
                requireConfirmWord: 'RESUBMIT',
                inputPlaceholder: 'RESUBMIT',
                confirmText: 'Yes, resubmit it!',
                cancelText: 'Cancel',
                loadingTitle: 'Resubmitting activation...',
                loadingText: 'Updating records.',
            },
            () => {
                const name = d.modal && MODAL_NAMES[d.modal] ? MODAL_NAMES[d.modal] : firstOpenModal();

                if (name === MODAL_NAMES.regular) {
                    callByName(MODAL_NAMES.regular, 'resubmitActivation');
                } else if (name === MODAL_NAMES.satellite) {
                    callByName(MODAL_NAMES.satellite, 'resubmitActivation');
                }
            },
        );
    });

    // ==========================================================
    // LEGACY SHIMS — old dispatchers keep working via the shared flow
    // ==========================================================
    Livewire.on('swal-confirm-submission', () => {
        openConfirm({
            title: 'Save Application?',
            text: 'Are you sure you want to submit this application?',
            icon: 'question',
            confirmText: 'Yes, submit it!',
            method: 'confirmed-save-application',
            loadingTitle: 'Submitting...',
            loadingText: 'Please wait.',
        });
    });

    Livewire.on('swal-confirm-update', () => {
        openConfirm({
            title: 'Update Member?',
            text: 'Are you sure you want to update this member?',
            icon: 'question',
            confirmText: 'Yes, update it!',
            method: 'confirmed-update-member',
            loadingTitle: 'Updating...',
            loadingText: 'Please wait.',
        });
    });
});
