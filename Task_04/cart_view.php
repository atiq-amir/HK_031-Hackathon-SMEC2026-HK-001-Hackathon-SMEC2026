<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Your Cart</title>

<style>
body {
    background: #f4f6f8;
    font-family: 'Segoe UI', sans-serif;
    padding: 40px;
}

h1 {
    margin-bottom: 25px;
}

.cart-item {
    background: #fff;
    padding: 20px;
    margin-bottom: 15px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    gap: 20px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.06);
}

.cart-item img {
    height: 80px;
    width: 80px;
    object-fit: contain;
}

.cart-item h3 {
    font-size: 15px;
    flex: 1;
}

.qty {
    width: 60px;
    padding: 6px;
}

button {
    color: #fff;
    border: none;
    padding: 8px 14px;
    border-radius: 6px;
    cursor: pointer;
    margin-left: 5px;
}

button.remove-btn {
    background: #dc2626;
}

button.remove-btn:hover {
    background: #b91c1c;
}

button.add-btn {
    background: #2563eb;
}

button.add-btn:hover {
    background: #1e40af;
}

.total {
    margin-top: 25px;
    font-size: 20px;
    font-weight: bold;
}

/* ---------- EMPTY CART STYLING ---------- */
.empty-cart {
    text-align: center;
    color: #6b7280;
    margin-top: 100px;
}

.empty-cart img {
    width: 150px;
    margin-bottom: 20px;
}

.empty-cart h2 {
    margin-bottom: 10px;
}

.empty-cart p {
    margin-bottom: 20px;
}

.go-shopping {
    display: inline-block;
    padding: 12px 25px;
    background: #2563eb;
    color: #fff;
    border-radius: 8px;
    text-decoration: none;
}

.go-shopping:hover {
    background: #1e40af;
}
</style>
</head>
<body>

<h1>Your Shopping Cart</h1>

<div id="cart"></div>

<div class="total" id="total"></div>

<script>
const cartDiv = document.getElementById("cart");
const totalDiv = document.getElementById("total");

// Show a temporary loading message
cartDiv.innerHTML = "<p>Loading your cart...</p>";

fetchCart();

function fetchCart() {
    fetch("cart.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "get" })
    })
    .then(res => res.json())
    .then(cart => renderCart(cart))
    .catch(err => {
        console.error(err);
        cartDiv.innerHTML = "<p style='color:red;'>Failed to load cart. Please reload.</p>";
    });
}

function renderCart(cart) {
    cartDiv.innerHTML = "";
    totalDiv.innerText = "";

    if (!cart || cart.length === 0) {
        cartDiv.innerHTML = `
            <div class="empty-cart">
                <img src="https://cdn-icons-png.flaticon.com/512/2038/2038854.png" alt="Empty Cart">
                <h2>Your Cart is Empty</h2>
                <p>Looks like you haven't added anything yet.</p>
                <a href="index.php" class="go-shopping">Go Shopping</a>
            </div>
        `;
        return;
    }

    let total = 0;

    cart.forEach(item => {
        total += item.price * item.quantity;

        cartDiv.innerHTML += `
            <div class="cart-item">
                <img src="${item.image}">
                <h3>${item.title}</h3>
                <input class="qty" type="number" min="1"
                    value="${item.quantity}"
                    onchange="updateQty(${item.id}, this.value)">
                <strong>$${item.price}</strong>
                <button class="add-btn" onclick="window.location.href='index.php'">Add Item</button>
                <button class="remove-btn" onclick="removeItem(${item.id})">Remove</button>
            </div>
        `;
    });

    totalDiv.innerText = "Total: $" + total.toFixed(2);
}

// Functions for remove and update stay the same
function updateQty(id, qty) {
    fetch("cart.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "update", id: id, quantity: qty })
    })
    .then(res => res.json())
    .then(cart => renderCart(cart));
}

function removeItem(id) {
    fetch("cart.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "remove", id: id })
    })
    .then(res => res.json())
    .then(cart => renderCart(cart));
}
</script>

</body>
</html>
