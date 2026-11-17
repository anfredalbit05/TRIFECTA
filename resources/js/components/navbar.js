/**
 * Navbar component (resources/views/components/navbar.blade.php).
 *
 * Logout confirmation dialog. Reads the form id from the button so the same
 * helper works for every logout form on the page.
 */

function showLogoutConfirmation(formId) {
    Swal.fire({
        title: 'Logout?',
        text: 'Are you sure you want to logout?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: 'var(--color-primary)',
        cancelButtonColor: 'var(--color-accent)',
        confirmButtonText: 'Yes, Logout',
        cancelButtonText: 'Cancel',
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById(formId);

            if (form) {
                form.submit();
            }
        }
    });
}

window.showLogoutConfirmation = showLogoutConfirmation;
