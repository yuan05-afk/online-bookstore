/**
 * Main JavaScript
 * Client-side functionality for the bookstore
 */

// Cart functionality
document.addEventListener('DOMContentLoaded', function () {
    // Add to cart buttons
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const bookId = this.getAttribute('data-book-id');

            // Check if there's a quantity selector (book detail page)
            const quantityElement = document.getElementById('quantity');
            let quantity = 1;

            if (quantityElement) {
                // Get text content from span (new design) or value from input (old design)
                quantity = quantityElement.textContent || quantityElement.value || 1;
            }

            addToCart(bookId, quantity);
        });
    });

    // Update cart quantity
    const quantityInputs = document.querySelectorAll('.quantity-input');
    quantityInputs.forEach(input => {
        input.addEventListener('change', function () {
            const cartItemId = this.getAttribute('data-cart-item-id');
            const quantity = this.value;

            updateCartItem(cartItemId, quantity);
        });
    });

    // Remove from cart
    const removeButtons = document.querySelectorAll('.btn-remove, .user-cart-remove-btn');
    removeButtons.forEach(button => {
        button.addEventListener('click', function () {
            const cartItemId = this.getAttribute('data-cart-item-id');

            if (confirm('Remove this item from cart?')) {
                removeFromCart(cartItemId);
            }
        });
    });

    // Mobile menu toggle
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function () {
            const nav = document.querySelector('.main-nav');
            nav.classList.toggle('active');
        });
    }

    // User menu toggle
    const userMenuToggle = document.getElementById('userMenuToggle');
    const userMenuDropdown = document.getElementById('userMenuDropdown');

    if (userMenuToggle && userMenuDropdown) {
        userMenuToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            userMenuDropdown.classList.toggle('show');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function (event) {
            if (!event.target.closest('.user-menu')) {
                userMenuDropdown.classList.remove('show');
            }
        });
    }
});

// Add to cart function
function addToCart(bookId, quantity = 1) {
    const formData = new FormData();
    formData.append('action', 'add');
    formData.append('book_id', bookId);
    formData.append('quantity', quantity);

    fetch('/online-bookstore/api/cart_actions.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Added to cart!', 'success');
                updateCartCount();
            } else {
                showNotification(data.message || 'Failed to add to cart', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred', 'error');
        });
}

// Update cart item
function updateCartItem(cartItemId, quantity) {
    const formData = new FormData();
    formData.append('action', 'update');
    formData.append('cart_item_id', cartItemId);
    formData.append('quantity', quantity);

    fetch('/online-bookstore/api/cart_actions.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                showNotification(data.message || 'Failed to update cart', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred', 'error');
        });
}

// Remove from cart
function removeFromCart(cartItemId) {
    const formData = new FormData();
    formData.append('action', 'remove');
    formData.append('cart_item_id', cartItemId);

    fetch('/online-bookstore/api/cart_actions.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                showNotification(data.message || 'Failed to remove item', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred', 'error');
        });
}

// Update cart count
function updateCartCount() {
    const formData = new FormData();
    formData.append('action', 'get_count');

    fetch('/online-bookstore/api/cart_actions.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const cartBadge = document.getElementById('cart-count');
                if (cartBadge) {
                    cartBadge.textContent = data.count;
                    if (data.count > 0) {
                        cartBadge.style.display = 'inline-block';
                    } else {
                        cartBadge.style.display = 'none';
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

// Show notification
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.classList.add('show');
    }, 10);

    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// Format card number input
const cardNumberInput = document.getElementById('card_number');
if (cardNumberInput) {
    cardNumberInput.addEventListener('input', function (e) {
        let value = e.target.value.replace(/\s/g, '');
        let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
        e.target.value = formattedValue;
    });
}

// Global function for cart quantity updates (called from onclick in cart.php)
function updateQuantity(cartItemId, change) {
    const cartItem = document.querySelector(`[data-cart-item-id="${cartItemId}"]`);
    if (!cartItem) return;

    const quantitySpan = cartItem.querySelector('.user-cart-quantity span');
    if (!quantitySpan) return;

    let currentQuantity = parseInt(quantitySpan.textContent);
    let newQuantity = currentQuantity + change;

    if (newQuantity < 1) return;

    updateCartItem(cartItemId, newQuantity);
}
