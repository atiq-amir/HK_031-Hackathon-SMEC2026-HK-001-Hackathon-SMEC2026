<?php
session_start();
header("Content-Type: application/json");
require "db.php";

/* Helper: Get total items in cart */
function getTotal($conn) {
    $res = $conn->query("SELECT SUM(quantity) AS total FROM cart");
    $row = $res->fetch_assoc();
    return (int)$row['total'];
}

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);
$action = $data['action'] ?? '';

/* ---------------- GET CART ---------------- */
if ($action === "get") {
    $result = $conn->query("SELECT * FROM cart");
    $cart = [];
    while ($row = $result->fetch_assoc()) {
        $cart[] = $row;
    }
    echo json_encode($cart);
    exit;
}

/* ---------------- ADD TO CART ---------------- */
if ($action === "add") {
    $p = $data['product'];

    // Check if product already in cart
    $check = $conn->query("SELECT * FROM cart WHERE product_id = {$p['id']}");
    if ($check->num_rows > 0) {
        $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE product_id = ?");
        $stmt->bind_param("i", $p['id']);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("
            INSERT INTO cart (product_id, title, image, price, quantity)
            VALUES (?, ?, ?, ?, 1)
        ");
        $stmt->bind_param("issd", $p['id'], $p['title'], $p['image'], $p['price']);
        $stmt->execute();
    }

    echo json_encode(["status" => "added", "total" => getTotal($conn)]);
    exit;
}

/* ---------------- UPDATE QUANTITY ---------------- */
if ($action === "update") {
    if (!empty($data['increment']) && $data['increment']) {
        // Increment by 1
        $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE id = ?");
        $stmt->bind_param("i", $data['id']);
        $stmt->execute();
    } else {
        // Manual quantity change
        $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
        $stmt->bind_param("ii", $data['quantity'], $data['id']);
        $stmt->execute();
    }

    echo json_encode(["status" => "updated", "total" => getTotal($conn)]);
    exit;
}

/* ---------------- REMOVE ITEM ---------------- */
if ($action === "remove") {
    $stmt = $conn->prepare("DELETE FROM cart WHERE id = ?");
    $stmt->bind_param("i", $data['id']);
    $stmt->execute();

    echo json_encode(["status" => "removed", "total" => getTotal($conn)]);
    exit;
}

/* ---------------- CLEAR CART ---------------- */
if ($action === "clear") {
    $conn->query("DELETE FROM cart");
    echo json_encode(["status" => "cleared", "total" => 0]);
    exit;
}
