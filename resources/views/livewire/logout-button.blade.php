<button
    wire:click="confirmLogout"
    class="logout-button">
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
            confirmButtonColor: '#0B2A5B',
            cancelButtonColor: '#D79B10',
            confirmButtonText: 'Yes, logout',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show Loading SweetAlert
                Swal.fire({
                    title: 'Logging out...',
                    text: 'Please wait a moment.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Trigger Livewire logout action
                $wire.logout();
            }
        });
    });
</script>
@endscript
