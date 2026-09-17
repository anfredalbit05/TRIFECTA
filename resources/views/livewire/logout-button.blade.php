<button wire:click="confirmLogout" class="logout-button">
    Logout
</button>

<style>
    .swal2-container {
        z-index: 99999 !important;
    }

    /* WARNING ICON */
    .swal2-icon.swal2-warning {
        border-color: #0B2A5B !important;
        color: #0B2A5B !important;
    }
</style>

@script
<script>
    $wire.on('confirm-logout', () => {
        Swal.fire({
            title: 'Are you sure?',
            text: 'You will be logged out from your account.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0B2A5B',
            cancelButtonColor: '#D79B10',
            confirmButtonText: 'Yes, logout',
            cancelButtonText: 'Cancel'
        }).then(result => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Logging out...',
                    text: 'Please wait a moment.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => Swal.showLoading()
                });

                $wire.logout();
            }
        });
    });
</script>
@endscript
