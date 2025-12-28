function getBasePath() {
    if (window.location.pathname.includes('/DA_LTW/')) {
        return '/DA_LTW';
    }
    return '';
}

function showToast(message, success = true) {
    const oldToast = document.querySelector('.toast-notification');
    if (oldToast) oldToast.remove();

    const notification = document.createElement('div');
    notification.className = 'toast-notification';
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${success ? '#2E7D32' : '#f44336'};
        color: white;
        padding: 15px 25px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        z-index: 10000;
        animation: slideIn 0.3s ease;
        font-weight: 600;
    `;
    notification.innerHTML = `<span>${message}</span>`;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease forwards';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

function addToCart(productId) {
    let quantity = 1;
    const quantityInput = document.getElementById('quantity');
    if (quantityInput) {
        quantity = parseInt(quantityInput.value) || 1;
    }

    showToast('⏳ Đang xử lý...', true);

    const url = getBasePath() + '/includes/add-to-cart.php';
    fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ product_id: productId, quantity })
    })
    .then(res => {
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        return res.json();
    })
    .then(data => {
        if (data.success) {
            showToast('✅ Đã thêm vào giỏ hàng!', true);
            
            const cartCountEl = document.getElementById('cart-count');
            if (cartCountEl) {
                cartCountEl.innerText = data.total_items;
                cartCountEl.style.display = 'inline-block'; 
                
                cartCountEl.style.transition = 'transform 0.2s ease'; 
                cartCountEl.style.transform = 'scale(1.5)'; 
                
                setTimeout(() => { 
                    cartCountEl.style.transform = 'scale(1)'; 
                }, 200);
            }
        } else {
            showToast(data.message, false);
        }
    })
    .catch(err => {
        console.error('Fetch error:', err);
        showToast('❌ ' + err.message, false);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    document.body.addEventListener('click', e => {
        const button = e.target.closest('.add-to-cart:not(.disabled)');
        if (button) {
            e.preventDefault();
            const productId = button.getAttribute('data-product-id');
            if (productId) addToCart(productId);
        }
    });

    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
    `;
    document.head.appendChild(style);

    handleSearch();
});

function handleSearch() {
    const searchForm = document.querySelector('.searchbar form');
    if (searchForm) {
        searchForm.addEventListener('submit', e => {
            e.preventDefault();
            const keyword = searchForm.querySelector('input').value.trim();
            if (keyword) {
                window.location.href = getBasePath() + `/pages/products.php?search=${encodeURIComponent(keyword)}`;
            }
        });
    }
}