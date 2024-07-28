<?php
include 'conn.php';
session_start();

date_default_timezone_set('Asia/Manila');
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM orders WHERE user_id=$user_id";
$result = mysqli_query($conn, $query);

$order_number = 1;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Orders | Water Refilling Station</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            min-width: 250px;
            max-width: 250px;
            background-color: #343a40;
            color: white;
            padding-top: 20px;
            position: fixed;
            height: 100%;
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
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
        }
        .header a {
            color: white;
        }
        .table-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .table-title {
            margin-bottom: 20px;
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
        <div class="navbar-brand">Water Refilling</div>
        <a href="customer_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="create_order.php"><i class="fas fa-plus"></i> Create Order</a>
        <a href="view_orders.php" class="active"><i class="fas fa-list"></i> View Orders</a>
        <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
        <a href="support.php"><i class="fas fa-question-circle"></i> Support</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
    <div class="content">
        <div class="header">
            <h2><i class="fas fa-list"></i> View Orders</h2>
            <a href="logout.php" class="btn btn-light" style="color:black;"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
        <div class="container mt-3">
            <div class="table-container">
                <h2 class="table-title"><i class="fas fa-box"></i> Your Orders</h2>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Order Date</th>
                            <th>Status</th>
                            <th>Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = mysqli_fetch_assoc($result)): ?>
                            <tr class="<?= $order['status'] == 'pending' ? 'pending' : ($order['status'] == 'completed' ? 'completed' : '') ?>">
                            <td><?= $order_number++ ?></td>
                                <td><?= date('F j, Y, g:i A', strtotime($order['order_date'])) ?></td>
                                <td><?= $order['status'] ?></td>
                                <th><?= $order['quantity']?></th>
                            </tr>
                        <?php endwhile; ?>
                        <?php if (mysqli_num_rows($result) == 0): ?>
                            <tr>
                                <td colspan="3" class="text-center">No orders found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
