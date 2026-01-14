<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Smart Shopping Cart</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            background: #f4f6f8;
            color: #333;
        }

        header {
            background: #111827;
            color: #fff;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            font-size: 22px;
        }

        .cart-btn {
            text-decoration: none;
            color: #fff;
            background: #2563eb;
            padding: 10px 18px;
            border-radius: 6px;
            font-weight: 500;
        }

        .products {
            padding: 40px;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 25px;
        }

        .product {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.06);
            display: flex;
            flex-direction: column;
        }

        .product img {
            height: 180px;
            object-fit: contain;
            margin-bottom: 15px;
        }

        .product h3 {
            font-size: 15px;
            margin-bottom: 8px;
        }

        .product p {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 12px;
        }

        .price {
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 15px;
        }

        button {
            margin-top: auto;
            background: #111827;
            color: #fff;
            border: none;
            padding: 12px;
            border-radius: 8px;
            cursor: pointer;
        }

        button:hover {
            background: #2563eb;
        }
    </style>

</head>
<body>

<header>
    <h1>Smart Cart</h1>
    <a href="cart_view.php" class="cart-btn">View Cart</a>
</header>

<main class="products" id="product-list">
    <!-- Products will load here -->
</main>
<script>
const productList = document.getElementById("product-list");
let allProducts = [];

// Fetch products from API
fetch("https://fakestoreapi.com/products")
    .then(res => res.json())
    .then(products => {
        allProducts = products;
        products.forEach(product => {
            productList.innerHTML += `
                <div class="product">
                    <img src="${product.image}">
                    <h3>${product.title}</h3>
                    <p>${product.category}</p>
                    <div class="price">$${product.price}</div>
                    <button onclick="addToCart(${product.id})">Add to Cart</button>
                </div>
            `;
        });
    });

function addToCart(id) {
    const product = allProducts.find(p => p.id === id);

    // Send product object to cart.php
    fetch("cart.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            action: "add",
            product: {
                id: product.id,
                title: product.title,
                image: product.image,
                price: product.price
            }
        })
    })
    .then(res => res.json())
    .then(data => {
        alert("Added to cart! Total items: " + data.total);
    });
}
</script>


</body>
</html>