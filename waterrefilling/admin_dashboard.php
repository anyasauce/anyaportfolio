<?php
include 'conn.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}
date_default_timezone_set('Asia/Manila');

$sql_total_orders = "SELECT COUNT(*) AS total_orders FROM orders";
$sql_completed_orders = "SELECT COUNT(*) AS completed_orders FROM orders WHERE status = 'completed'";
$sql_pending_orders = "SELECT COUNT(*) AS pending_orders FROM orders WHERE status = 'pending'";

$result_total_orders = mysqli_query($conn, $sql_total_orders);
$result_completed_orders = mysqli_query($conn, $sql_completed_orders);
$result_pending_orders = mysqli_query($conn, $sql_pending_orders);

if (mysqli_num_rows($result_total_orders) > 0) {
    $row_total_orders = mysqli_fetch_assoc($result_total_orders);
    $total_orders = $row_total_orders['total_orders'];
} else {
    $total_orders = 0;
}

if (mysqli_num_rows($result_completed_orders) > 0) {
    $row_completed_orders = mysqli_fetch_assoc($result_completed_orders);
    $completed_orders = $row_completed_orders['completed_orders'];
} else {
    $completed_orders = 0;
}

if (mysqli_num_rows($result_pending_orders) > 0) {
    $row_pending_orders = mysqli_fetch_assoc($result_pending_orders);
    $pending_orders = $row_pending_orders['pending_orders'];
} else {
    $pending_orders = 0;
}

$sql_recent_orders = "SELECT * FROM orders ORDER BY order_date DESC LIMIT 3";
$result_recent_orders = mysqli_query($conn, $sql_recent_orders);

$sql_recent_inventory = "SELECT * FROM inventory ORDER BY id DESC LIMIT 3";
$result_recent_inventory = mysqli_query($conn, $sql_recent_inventory);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | Water Refilling Station</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
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
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .card-body {
            padding: 20px;
        }
        .card-title {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .card-text {
            color: #555;
        }
        .btn-primary {
            background-color: #007bff !important;
            border-color: #007bff !important;
        }
        .btn-primary:hover {
            background-color: #0056b3 !important;
            border-color: #0056b3 !important;
        }
        .btn-secondary {
            background-color: #6c757d !important;
            border-color: #6c757d !important;
        }
        .btn-secondary:hover {
            background-color: #545b62 !important;
            border-color: #545b62 !important;
        }
        .stats {
            margin-top: 20px;
            padding: 10px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .stats h3 {
            font-size: 1.2rem;
            margin-bottom: 10px;
        }
        .stats p {
            margin-bottom: 5px;
        }
        .recent-activities {
            margin-top: 20px;
            padding: 10px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .recent-activities h3 {
            font-size: 1.2rem;
            margin-bottom: 10px;
        }
        .activity-item {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .activity-item:last-child {
            border-bottom: none;
        }
        .activity-item .activity-info {
            font-size: 0.9rem;
            color: #777;
        }
        .activity-item .activity-time {
            font-size: 0.8rem;
            color: #999;
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
            <h2><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h2>
            <a href="logout.php" class="btn btn-light" style="color:black;"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
        <div class="container mt-3">
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Manage Orders</h5>
                            <p class="card-text">View and manage customer orders.</p>
                            <a href="manage_orders.php" class="btn btn-primary"><i class="fas fa-box"></i> Manage Orders</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Manage Inventory</h5>
                            <p class="card-text">Update and manage product inventory.</p>
                            <a href="manage_inventory.php" class="btn btn-primary"><i class="fas fa-warehouse"></i> Manage Inventory</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-lg-6">
                    <div class="card stats">
                        <div class="card-body">
                            <h3 class="card-title">Statistics</h3>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="stat-item">
                                        <h4>Total Orders</h4>
                                        <p class="stat-value"><?php echo $total_orders; ?></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="stat-item">
                                        <h4>Completed Orders</h4>
                                        <p class="stat-value"><?php echo $completed_orders; ?></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="stat-item">
                                        <h4>Pending Orders</h4>
                                        <p class="stat-value"><?php echo $pending_orders; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card recent-activities">
                        <div class="card-body">
                            <h3 class="card-title">Recent Activities</h3>
                            <?php if (mysqli_num_rows($result_recent_orders) > 0 || mysqli_num_rows($result_recent_inventory) > 0): ?>
                                <?php
                                while ($row = mysqli_fetch_assoc($result_recent_orders)) {
                                    echo '<div class="activity-item">';
                                    echo '<p class="activity-info">Order ID: ' . $row['id'] . ', Status: ' . $row['status'] . '</p>';
                                    echo '<p class="activity-time">' . date('M j, Y H:i:s', strtotime($row['order_date'])) . '</p>';
                                    echo '</div>';
                                }

                                while ($row = mysqli_fetch_assoc($result_recent_inventory)) {
                                    echo '<div class="activity-item">';
                                    echo '<p class="activity-info">Added inventory: ' . $row['item_name'] . ', Quantity: ' . $row['quantity'] . '</p>';
                                    echo '<p class="activity-time">' . date('M j, Y H:i:s', strtotime($row['date_added'])) . '</p>';
                                    echo '</div>';
                                }
                                ?>
                            <?php else: ?>
                                <p>No recent activities.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
