/**
 * Landing page – home (resources/views/livewire/landing-page/home.blade.php).
 *
 * Opens the application modal with the referral code pre-filled when the page
 * is visited through a referral link. The code is read from the
 * `data-referral-code` attribute on the page root.
 */

document.addEventListener('livewire:init', () => {
    const code = document.querySelector('[data-referral-code]')?.dataset.referralCode;

    if (!code) return;

    setTimeout(() => {
        Livewire.dispatch('open-application-modal', {
            upline: code,
        });
    }, 500);
});
