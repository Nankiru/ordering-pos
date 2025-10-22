// Intercept add-to-cart forms and submit via fetch, updating cart count badge
document.addEventListener('DOMContentLoaded', function () {
  function getBadgeCount() {
    const badge = document.getElementById('cart-count-badge');
    if (!badge) return 0;
    const n = parseInt(badge.textContent.replace(/\D/g, ''));
    return isNaN(n) ? 0 : n;
  }

  function setBadgeCount(n) {
    const badge = document.getElementById('cart-count-badge');
    if (!badge) return;
    badge.textContent = String(n);
  }
  function handleAddForm(form) {
    // Prevent attaching the same listener multiple times
    if (form.dataset.cartHandled === '1') return;
    form.dataset.cartHandled = '1';

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      const url = form.action;
      const formData = new FormData(form);

      // optimistic badge update
      const prevBadge = getBadgeCount();
      const addQty = Math.max(1, parseInt(formData.get('qty')) || 1);
      setBadgeCount(prevBadge + addQty);

      fetch(url, {
        method: (form.method || 'POST').toUpperCase(),
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        },
        body: formData,
        credentials: 'same-origin'
      })
        .then(res => res.json())
        .then(data => {
          if (data && data.success) {
            setBadgeCount((data.cart_count != null) ? data.cart_count : getBadgeCount());
            // small visual feedback
            const btn = form.querySelector('button, input[type="submit"]');
            if (btn) {
              btn.classList.add('opacity-75');
              setTimeout(() => btn.classList.remove('opacity-75'), 300);
            }
          } else {
            // revert optimistic update
            setBadgeCount(prevBadge);
            console.warn('Add to cart failed', data);
          }
        })
        .catch(err => {
          // revert optimistic update
          setBadgeCount(prevBadge);
          console.error('Add to cart error', err);
        });
    });
  }

  // Select candidate forms once and attach handler if they match the add route
  const forms = Array.from(document.querySelectorAll('form'));
  forms.forEach(form => {
    const action = (form.getAttribute('action') || '').toLowerCase();
    if (action.includes('/shop/cart/add') || action.includes('shop.cart.add') || action.includes('shop/cart')) {
      handleAddForm(form);
    }
  });

  // Delegated click handler for Add buttons that may not submit forms normally
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.js-add-to-cart');
    if (!btn) return;
    e.preventDefault();
    // Find nearest form (the button may be outside or inside)
    const form = btn.closest('form');
    if (!form) return console.warn('Add button not inside a form');
    // prepare form data
    const formData = new FormData(form);

    // optimistic badge update
    const prevBadge = getBadgeCount();
    const addQty = Math.max(1, parseInt(formData.get('qty')) || 1);
    setBadgeCount(prevBadge + addQty);

    const url = form.action;
    fetch(url, {
      method: (form.method || 'POST').toUpperCase(),
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      },
      body: formData,
      credentials: 'same-origin'
    })
      .then(res => res.json())
      .then(data => {
        if (data && data.success) {
          setBadgeCount((data.cart_count != null) ? data.cart_count : getBadgeCount());
          btn.classList.add('opacity-75');
          setTimeout(() => btn.classList.remove('opacity-75'), 300);
        } else {
          // revert optimistic update
          setBadgeCount(prevBadge);
          console.warn('Add to cart failed', data);
        }
      })
      .catch(err => {
        // revert optimistic update
        setBadgeCount(prevBadge);
        console.error('Add to cart error', err);
      });
  });

  // Delegated handlers for increment/decrement buttons
  document.addEventListener('click', function (e) {
    const dec = e.target.closest('.js-decrement');
    if (dec) {
      const input = dec.closest('form')?.querySelector('input[name="qty"]');
      if (input) {
        const val = Math.max(1, (parseInt(input.value) || 1) - 1);
        input.value = val;
      }
      return;
    }
    const inc = e.target.closest('.js-increment');
    if (inc) {
      const input = inc.closest('form')?.querySelector('input[name="qty"]');
      if (input) {
        const val = (parseInt(input.value) || 0) + 1;
        input.value = val;
      }
      return;
    }
  });
});
