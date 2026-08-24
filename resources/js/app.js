import Alpine from 'alpinejs';
import { initPixiHero } from './pixi-background.js';
import { initRatingInputs } from './rating-input.js';

window.Alpine = Alpine;
Alpine.start();

// Initialize all features once DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    // 1. Initialize Pixi Hero Canvas
    initPixiHero();

    // 2. Initialize interactive star rating inputs
    initRatingInputs();

    // 3. Theme Toggle (Dark Mode / Light Mode)
    const themeToggleBtn = document.getElementById('themeToggle');
    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('bm3_theme', newTheme);
            window.dispatchEvent(new CustomEvent('themechange', { detail: { theme: newTheme } }));
        });
    }

    // 3. Review character counter
    const commentInput = document.querySelector('textarea[name="comment"]');
    const charCountEl = document.getElementById('charCount');
    if (commentInput && charCountEl) {
        const updateCount = () => {
            charCountEl.textContent = commentInput.value.length;
        };
        commentInput.addEventListener('input', updateCount);
        updateCount();
    }

    // 4. Hero stats number count-up animation
    const statNumbers = document.querySelectorAll('.hero-stats .stat-number');
    statNumbers.forEach((el) => {
        const target = parseInt(el.getAttribute('data-count'), 10) || 0;
        if (target <= 0) return;

        let start = 0;
        const duration = 1200;
        const startTime = performance.now();

        function updateNumber(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            // Ease out quad
            const easeProgress = 1 - (1 - progress) * (1 - progress);
            const currentVal = Math.floor(easeProgress * target);
            el.textContent = currentVal;

            if (progress < 1) {
                requestAnimationFrame(updateNumber);
            } else {
                el.textContent = target;
            }
        }
        requestAnimationFrame(updateNumber);
    });
});

/**
 * AJAX Toggle for Helpful Votes
 */
window.toggleVote = async function(reviewId, buttonEl) {
    if (!buttonEl) return;
    
    // Disable button momentarily to prevent double clicking
    buttonEl.disabled = true;

    try {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const response = await fetch(`/reviews/${reviewId}/vote`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token,
            },
            body: JSON.stringify({})
        });

        if (response.ok) {
            const data = await response.json();
            const countEl = buttonEl.querySelector('.helpful-count');

            if (data.voted) {
                buttonEl.classList.add('voted');
            } else {
                buttonEl.classList.remove('voted');
            }

            if (countEl && typeof data.count !== 'undefined') {
                countEl.textContent = data.count;
            }

            // Pulse micro-animation
            buttonEl.style.transform = 'scale(1.15)';
            setTimeout(() => {
                buttonEl.style.transform = '';
            }, 180);
        } else if (response.status === 401) {
            window.location.href = '/login';
        } else {
            const err = await response.json().catch(() => ({}));
            alert(err.error || 'Unable to submit vote.');
        }
    } catch (error) {
        console.error('Vote failed:', error);
    } finally {
        buttonEl.disabled = false;
    }
};
