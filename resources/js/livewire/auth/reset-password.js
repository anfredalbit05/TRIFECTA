/**
 * Reset password page (resources/views/livewire/auth/reset-password.blade.php).
 *
 * Show/hide password toggles. Delegated from `document` so the handler also
 * works after Livewire `wire:navigate` swaps the page body.
 */

document.addEventListener('click', (event) => {
    const btn = event.target.closest('.pw-toggle');
    if (!btn) return;

    const input = document.getElementById(btn.getAttribute('data-target'));
    if (!input) return;

    const show = input.type === 'password';
    input.type = show ? 'text' : 'password';

    const showIcon = btn.querySelector('[data-ico="show"]');
    const hideIcon = btn.querySelector('[data-ico="hide"]');

    if (showIcon) {
        showIcon.classList.toggle('hidden', show);
    }
    if (hideIcon) {
        hideIcon.classList.toggle('hidden', !show);
    }
});
