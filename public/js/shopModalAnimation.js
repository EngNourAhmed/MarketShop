// Smooth Fade Animation for Ordering Options Modal

let shopModalIsClosing = false;
let shopModalAnimationHandlersAttached = false;

function openShopProductModalWithAnimation(productCard) {
    const modal = document.getElementById('shop-product-modal');
    const overlay = document.getElementById('shop-product-modal-overlay');

    if (!modal || !overlay) return;

    // Set initial state (do NOT change modal geometry/positioning; Tailwind classes must control that)
    modal.style.opacity = '0';
    overlay.style.opacity = '0';

    // Show elements
    overlay.classList.remove('hidden');
    modal.classList.remove('hidden');

    // Clean up any existing clones or states
    document.querySelectorAll('.expanding-clone').forEach(el => el.remove());

    // Reset transition to avoid jumping during initial set
    modal.style.transition = 'none';
    overlay.style.transition = 'none';

    // Create Clone for smoother expansion
    const cardRect = productCard.getBoundingClientRect();
    const clone = productCard.cloneNode(true);
    clone.className = 'expanding-clone fixed z-[9999] overflow-hidden rounded-2xl shadow-2xl';
    clone.style.left = cardRect.left + 'px';
    clone.style.top = cardRect.top + 'px';
    clone.style.width = cardRect.width + 'px';
    clone.style.height = cardRect.height + 'px';
    clone.style.transition = 'all 400ms cubic-bezier(0.34, 1.56, 0.64, 1)';
    clone.style.opacity = '1';

    // Strip ID to avoid duplicates and remove click handlers via cloning
    clone.removeAttribute('id');

    document.body.appendChild(clone);

    // Measure destination without modifying modal styles
    modal.style.visibility = 'hidden';
    const modalRect = modal.getBoundingClientRect();
    modal.style.visibility = '';

    requestAnimationFrame(() => {
        // Expand clone to the real modal destination rect
        clone.style.left = modalRect.left + 'px';
        clone.style.top = modalRect.top + 'px';
        clone.style.width = modalRect.width + 'px';
        clone.style.height = modalRect.height + 'px';
        clone.style.opacity = '0'; // Fade out clone as real modal fades in? Or keep it?
        // Let's fade out clone while fading in modal

        modal.style.transition = 'opacity 400ms ease-out';
        modal.style.opacity = '1';
        overlay.style.opacity = '1';

        setTimeout(() => {
            clone.remove();
        }, 400);
    });

    document.body.classList.add('overflow-hidden');

    // Re-initialize icons
    setTimeout(() => {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }, 50);
}

function closeShopProductModalWithAnimation() {
    const modal = document.getElementById('shop-product-modal');
    const overlay = document.getElementById('shop-product-modal-overlay');

    if (!modal || !overlay) return;

    if (shopModalIsClosing) return;
    shopModalIsClosing = true;

    // Remove any leftover clones (can cause visual glitches)
    document.querySelectorAll('.expanding-clone').forEach(el => el.remove());

    // Animate out
    // We want a smooth reverse of the opening if possible, or just a nice fade/scale down
    modal.style.transition = 'opacity 300ms ease-out';
    modal.style.opacity = '0';

    overlay.style.transition = 'opacity 300ms ease-out';
    overlay.style.opacity = '0';

    setTimeout(() => {
        modal.classList.add('hidden');
        overlay.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');

        // Reset styles for next open
        modal.style.opacity = '';
        modal.style.transition = '';
        overlay.style.opacity = '';
        overlay.style.transition = '';
        shopModalIsClosing = false;
    }, 300);
}

// Auto-attach to product cards
document.addEventListener('DOMContentLoaded', () => {
    // If the Blade layout already provides the authoritative shop modal logic,
    // avoid attaching a second, conflicting handler set.
    if (typeof window.openShopProductModal === 'function') return;
    if (shopModalAnimationHandlersAttached) return;
    shopModalAnimationHandlersAttached = true;

    const productCards = document.querySelectorAll('[data-shop-product]');

    productCards.forEach(card => {
        card.addEventListener('click', (e) => {
            // Don't trigger if clicking on a button or link inside the card
            if (e.target.closest('button, a')) return;

            const modalMode = card.dataset.modalMode;
            if (modalMode === 'popup' || modalMode === 'center') {
                openShopProductModalWithAnimation(card);

                // Populate modal with product data
                const productData = {
                    id: card.dataset.productId,
                    nameEn: card.dataset.nameEn,
                    nameAr: card.dataset.nameAr,
                    descriptionEn: card.dataset.descriptionEn,
                    descriptionAr: card.dataset.descriptionAr,
                    image: card.dataset.image,
                    suppliers: JSON.parse(card.dataset.suppliers || '[]'),
                    pricingTiers: JSON.parse(card.dataset.pricingTiers || '[]'),
                    colors: card.dataset.colors,
                    sizes: card.dataset.sizes
                };

                // Populate modal
                populateShopProductModal(productData);
            }
        });
    });

    // Close button
    const closeBtn = document.getElementById('shop-product-modal-close');
    if (closeBtn) {
        closeBtn.addEventListener('click', closeShopProductModalWithAnimation);
    }

    // Close on overlay click
    const overlay = document.getElementById('shop-product-modal-overlay');
    if (overlay) {
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                closeShopProductModalWithAnimation();
            }
        });
    }

    // Close on ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeShopProductModalWithAnimation();
        }
    });
});

// Placeholder for modal population
function populateShopProductModal(productData) {
    console.log('Populating modal with:', productData);
}
