<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "sneakerstore";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle POST: Insert payment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required fields
    $required = ['name', 'phone', 'address', 'card_number', 'expiry_month', 'expiry_year', 'cvv'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            die("Missing field: $field");
        }
    }

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO payments (name, phone, address, card_number, expiry_month, expiry_year, cvv) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "sssssss",
        $_POST['name'],
        $_POST['phone'],
        $_POST['address'],
        $_POST['card_number'],
        $_POST['expiry_month'],
        $_POST['expiry_year'],
        $_POST['cvv']
    );

    if ($stmt->execute()) {
        echo "<script>alert('Checkout complete');</script>";
        echo "<script>window.setTimeout(function(){ window.location.href = 'index.html'; }, 1000);</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Handle GET: Show sneakers (or orders)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT * FROM sneakers"; // Change table name if needed
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "Sneaker ID: " . $row["id"] . " - Name: " . $row["name"] . "<br>";
            // Add more fields as needed
        }
    } else {
        echo "No sneakers found.";
    }
}

$conn->close();
?>