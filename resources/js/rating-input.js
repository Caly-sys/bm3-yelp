export function initRatingInputs() {
    const ratingGroups = document.querySelectorAll('.rating-input-stars');

    ratingGroups.forEach((group) => {
        const buttons = group.querySelectorAll('.star-btn');
        const hiddenInput = group.querySelector('input[type="hidden"]');
        let currentValue = parseInt(hiddenInput?.value) || 0;

        function updateStars(highlightUpTo) {
            buttons.forEach((btn) => {
                const val = parseInt(btn.getAttribute('data-value'));
                if (val <= highlightUpTo) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
        }

        buttons.forEach((btn) => {
            const val = parseInt(btn.getAttribute('data-value'));

            // Hover preview
            btn.addEventListener('mouseenter', () => {
                updateStars(val);
            });

            // Click selection
            btn.addEventListener('click', () => {
                currentValue = val;
                if (hiddenInput) {
                    hiddenInput.value = val;
                }
                updateStars(val);

                // Micro bounce animation
                btn.style.transform = 'scale(1.4)';
                setTimeout(() => {
                    btn.style.transform = '';
                }, 180);
            });
        });

        // Restore current value on mouse leave
        group.addEventListener('mouseleave', () => {
            updateStars(currentValue);
        });

        // Initial setup
        updateStars(currentValue);
    });
}
