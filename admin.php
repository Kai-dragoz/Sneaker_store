<?php
session_start();

// Authentication check
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

session_start();

// Authentication check
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

// Database connection
require_once 'db_config.php';
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_status'])) {
        $order_id = (int)$_POST['order_id'];
        $new_status = $conn->real_escape_string($_POST['status']);
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $order_id);
        $stmt->execute();
    }
    if (isset($_POST['delete_order'])) {
        $order_id = (int)$_POST['order_id'];
        $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
    }
}

// Pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Get orders
$result = $conn->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT $offset, $per_page");
$total_orders = $conn->query("SELECT COUNT(*) as total FROM orders")->fetch_assoc()['total'];
$total_pages = ceil($total_orders / $per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Sneaker Store</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="admin-header">
        <div class="header-content">
            <h1><i class="fas fa-shoe-prints"></i> Sneaker Store Admin</h1>
            <div class="admin-actions">
                <span class="welcome-msg">Welcome, <?= htmlspecialchars($_SESSION['admin_name']) ?></span>
                <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </div>

    <div class="admin-container">
        <main class="admin-content">
            <h2>Orders</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?= $row['id'] ?></td>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td><?= date('Y-m-d H:i', strtotime($row['created_at'])) ?></td>
                                    <td>RM<?= number_format($row['total_amount'], 2) ?></td>
                                    <td>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                                            <select name="status" onchange="this.form.submit()">
                                                <?php foreach (['Pending', 'Processing', 'Shipped', 'Completed', 'Cancelled'] as $status): ?>
                                                    <option value="<?= $status ?>" <?= $row['status'] == $status ? 'selected' : '' ?>>
                                                        <?= $status ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <input type="hidden" name="update_status" value="1">
                                        </form>
                                    </td>
                                    <td>
                                        <form method="POST" onsubmit="return confirm('Delete this order?')">
                                            <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                                            <button type="submit" name="delete_order" class="btn delete-btn" title="Delete Order">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align:center;">No orders found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?= $i ?>" class="page-link <?= $i == $page ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>