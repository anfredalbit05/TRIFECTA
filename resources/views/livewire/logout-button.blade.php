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
            confirmButtonColor: '#D79B10',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, logout',
            cancelButtonText: 'Cancel'
        }).then((result) => {

            if(result.isConfirmed){

                $wire.logout();

            }

        });

    });

</script>
@endscript
