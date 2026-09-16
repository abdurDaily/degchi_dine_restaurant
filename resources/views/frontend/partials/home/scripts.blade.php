<script>
    document.addEventListener('DOMContentLoaded', function() {
        var triggerButtons = document.querySelectorAll('.trigger-menu-popup');
        var modalOverlay = document.querySelector('.js-modal-overlay');
        var closeModalBtn = document.querySelector('.js-close-modal');
        var modalImg = document.getElementById('modal-active-display-img');

        triggerButtons.forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                var platterImage = this.getAttribute('data-menu-image');
                var platterTitle = this.getAttribute('data-platter-title');

                if (platterImage && modalImg && modalOverlay) {
                    modalImg.src = platterImage;
                    modalImg.alt = platterTitle || 'Signature Platter Menu';
                    modalOverlay.style.display = 'flex';
                }
            });
        });

        if (closeModalBtn) {
            closeModalBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if (modalOverlay) { modalOverlay.style.display = 'none'; }
            });
        }

        if (modalOverlay) {
            modalOverlay.addEventListener('click', function(e) {
                if (e.target === this) { this.style.display = 'none'; }
            });
        }
    });
</script>
