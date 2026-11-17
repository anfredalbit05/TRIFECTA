/**
 * News & Updates tab (resources/views/livewire/admin-pages/post/news-updates.blade.php).
 *
 * Legacy delete confirmation helper. Kept for parity with the previous inline
 * script: no markup currently calls it, but external callers may still rely on
 * `confirmDelete(postId)`.
 */

import { livewireCall } from '../../../shared/livewire.js';

const COMPONENT = 'admin-pages.post.create-news-updates';

function confirmDelete(postId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: 'var(--color-accent)',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete it!',
    }).then((result) => {
        if (result.isConfirmed) {
            livewireCall(COMPONENT, 'deletePost', postId);
        }
    });
}

window.confirmDelete = confirmDelete;
