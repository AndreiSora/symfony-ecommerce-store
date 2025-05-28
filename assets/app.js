import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

// app.js or main script

function setupAddToCart() {
    document.body.addEventListener('click', async function (e) {
        const btn = e.target.closest('.add-to-cart-btn');
        if (!btn) return;

        e.preventDefault();
        const productId = btn.dataset.productId;

        const res = await fetch(`/cart/add/${productId}`, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        const data = await res.json();

        if (data.success) {
            const badge = document.querySelector('#cart-badge');
            if (badge) {
                badge.textContent = data.cartItemCount;
            }

            // Optional: reload or re-render
            window.location.reload();
        }
    });
}

function setupCartQuantityButtons() {
    document.body.addEventListener('click', async function (e) {
        const inc = e.target.closest('.increase-btn');
        const dec = e.target.closest('.decrease-btn');
        const rem = e.target.closest('.remove-btn');


        let action, btn;
        if (inc) {
            e.preventDefault();
            action = 'increase';
            btn = inc;
        } else if (dec) {
            e.preventDefault();
            action = 'decrease';
            btn = dec;
        } else if (rem) {
            e.preventDefault();
            action = 'remove';
            btn = rem;
        } else return;

        e.preventDefault();

        const id = btn.dataset.productId;
        // No quantity parameter, so it will default to 1 in your backend
        const res = await fetch(`/cart/${action}/${id}`, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        if (res.ok) {
            window.location.reload();
        } else {
            alert('Could not update cart.');
        }
    });
}


function setupPaymentToggle() {
    document.body.addEventListener('change', function (e) {
        if (!e.target.matches('input[name="checkout[paymentMethod]"]')) return;

        const creditFields = document.getElementById('credit-card-fields');
        if (creditFields) {
            creditFields.style.display = e.target.value === 'credit_card' ? 'flex' : 'none';
        }
    });

    const checked = document.querySelector('input[name="checkout[paymentMethod]"]:checked');
    if (checked?.value === 'credit_card') {
        const creditFields = document.getElementById('credit-card-fields');
        if (creditFields) creditFields.style.display = 'flex';
    }
}

function setupShippingMethodToggle() {
    const shippingPrices = window.shippingPrices || {};

    document.body.addEventListener('change', function (e) {
        if (!e.target.matches('input[name="checkout[shippingMethod]"]')) return;

        const orderSummaryEl = document.getElementById('order-summary');
        const subtotal = parseFloat(orderSummaryEl?.dataset.subtotal || 0);
        const methodId = e.target.value;
        const shipping = parseFloat(shippingPrices[methodId]) || 0;
        const total = subtotal + shipping;

        const format = p => {
            const [int, dec] = p.toFixed(2).split('.');
            return { int, dec };
        };

        const shippingEl = document.getElementById('shipping-amount');
        const totalEl = document.getElementById('total-amount');
        if (shippingEl && totalEl) {
            const s = format(shipping);
            const t = format(total);
            shippingEl.innerHTML = `$${s.int}<sup>${s.dec}</sup>`;
            totalEl.innerHTML = `$${t.int}<sup>${t.dec}</sup>`;
        }
    });

    // Trigger once on load if already selected
    const selected = document.querySelector('input[name="checkout[shippingMethod]"]:checked');
    if (selected) {
        selected.dispatchEvent(new Event('change', { bubbles: true }));
    }
}


// 👇 Call all setups
function initApp() {
    setupAddToCart();
    setupCartQuantityButtons();
    setupPaymentToggle();
    setupShippingMethodToggle();
}

// Re-run after Turbo renders a new page
document.addEventListener('turbo:load', initApp);