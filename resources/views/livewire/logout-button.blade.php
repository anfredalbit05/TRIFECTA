<button wire:click="confirmLogout" class="logout-button">
    Logout
</button>


@script
<script>
    $wire.on('confirm-logout', () => {
        Swal.fire({
            title: 'Are you sure?',
            text: 'You will be logged out from your account.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: 'var(--color-primary)',
            cancelButtonColor: 'var(--color-accent)',
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
