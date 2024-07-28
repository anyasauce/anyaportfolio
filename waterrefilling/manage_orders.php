<?php
include 'conn.php';
session_start();

date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    $query = "UPDATE orders SET status='$status' WHERE id=$order_id";
    if (mysqli_query($conn, $query)) {
        ?>
        <script>
            alert("Order status updated successfully!")
        </script>
        <?php
    } else {
        ?>
        <script>
            alert("Error updating order status: ")
        </script>
        <?php
    }
}

if (isset($_POST['delete_order'])) {
    $order_id = $_POST['order_id'];

    $query = "DELETE FROM orders WHERE id=$order_id";
    if (mysqli_query($conn, $query)) {
        ?>
        <script>
            alert("Order deleted successfully!")
        </script>
        <?php
    } else {
        ?>
        <script>
            alert("Error deleting order: ")
        </script>
        <?php
    }
}

$query = "SELECT orders.*, users.name, users.phone FROM orders JOIN users ON orders.user_id = users.id";
$result = mysqli_query($conn, $query);

$order_number = 1;
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Orders | Water Refilling Station</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            background-color: #f0f0f0;
        }
        .sidebar {
            min-width: 250px;
            max-width: 250px;
            background-color: #343a40;
            color: white;
            padding-top: 20px;
            position: fixed;
            height: 100%;
            overflow-y: auto;
        }
        .sidebar a {
            color: white;
            padding: 15px;
            text-decoration: none;
            display: block;
        }
        .sidebar a:hover {
            background-color: #007bff;
            text-decoration: none;
        }
        .content {
            margin-left: 250px;
            padding: 20px;
            flex-grow: 1;
        }
        .navbar-brand {
            font-size: 1.5rem;
            padding: 1rem;
            color: white;
            text-align: center;
            margin-bottom: 0;
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h2 {
            margin: 0;
        }
        .header a {
            color: white;
        }
        .pending {
            background-color: #ffff99 !important; 
        }
        .completed {
            background-color: #ccffcc !important;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="navbar-brand">Admin Panel</div>
        <a href="admin_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="manage_orders.php"><i class="fas fa-box"></i> Manage Orders</a>
        <a href="manage_inventory.php"><i class="fas fa-warehouse"></i> Manage Inventory</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
    <div class="content">
        <div class="header">
            <h2><i class="fas fa-box"></i> Manage Orders</h2>
            <a href="logout.php" class="btn btn-light" style="color:black;"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
        <div class="container mt-3">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Phone Number</th>
                        <th>Order Date</th>
                        <th>Status</th>
                        <th>Quantity</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($order = mysqli_fetch_assoc($result)): ?>
                    <tr class="<?= $order['status'] == 'pending' ? 'pending' : ($order['status'] == 'completed' ? 'completed' : '') ?>">
                        <td><?= $order_number++ ?></td>
                        <td><?= $order['name'] ?></td>
                        <td><?= $order['phone']?></td>
                        <td><?= date('F j, Y, g:i A', strtotime($order['order_date'])) ?></td>
                        <td><?= $order['status'] ?></td>
                        <td><?= $order['quantity']?></td>
                        <td>
                            <form action="" method="POST" style="display:inline;">
                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                <select name="status" class="form-control" style="display:inline;width:auto;">
                                    <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="completed" <?= $order['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                                </select>
                                <button type="submit" name="update_status" class="btn btn-primary"><i class="fas fa-check"></i> Update</button>
                            </form>
                            <form action="" method="POST" style="display:inline;">
                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                <button type="submit" name="delete_order" class="btn btn-danger"><i class="fas fa-trash"></i> Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>