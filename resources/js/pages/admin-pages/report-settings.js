/**
 * Report & Settings page (resources/views/pages/admin-pages/report-settings.blade.php).
 *
 * Alpine factory for the signature pad used by the "Others" (signature) value
 * type in the shared settings modal. `SignaturePad` comes from the
 * signature_pad CDN script loaded by the page.
 */

function signaturePad() {
    return {
        pad: null,

        initPad() {
            this.$nextTick(() => {
                const canvas = this.$refs.pad;

                canvas.width = canvas.offsetWidth || 520;
                canvas.height = 140;

                this.pad = new SignaturePad(canvas, {
                    backgroundColor: 'rgb(255, 255, 255)', // needed so the export isn't transparent
                    penColor: 'rgb(15, 23, 42)',
                });
            });
        },

        clear() {
            this.pad?.clear();
        },

        save($wire) {
            if (!this.pad || this.pad.isEmpty()) {
                Swal.fire({
                    title: 'No Signature',
                    text: 'Please draw a signature first.',
                    icon: 'warning',
                    confirmButtonColor: 'var(--color-primary)',
                });

                return;
            }

            // raw base64 payload (same shape the file upload sends)
            $wire.call('saveSignatureSetting', this.pad.toDataURL('image/png').split(',')[1]);
        },
    };
}

window.signaturePad = signaturePad;
