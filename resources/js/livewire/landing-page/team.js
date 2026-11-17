/**
 * Landing page team (resources/views/livewire/landing-page/team.blade.php).
 *
 * Board member portrait modal.
 */

function openBoardModal(imageSrc, name, position) {
    document.getElementById('boardModalImage').src = imageSrc;
    document.getElementById('boardModalImage').alt = name;
    document.getElementById('boardModalOverlay').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeBoardModal(event) {
    if (
        event.target.id === 'boardModalOverlay' ||
        event.target.classList.contains('board-modal-close-btn')
    ) {
        document.getElementById('boardModalOverlay').classList.remove('active');
        document.body.style.overflow = '';
    }
}

window.openBoardModal = openBoardModal;
window.closeBoardModal = closeBoardModal;

// Close on Escape key
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        document.getElementById('boardModalOverlay').classList.remove('active');
        document.body.style.overflow = '';
    }
});
