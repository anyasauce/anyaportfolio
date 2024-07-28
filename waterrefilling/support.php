<?php
include 'conn.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
    header("Location: login.php");
    exit();
}
date_default_timezone_set('Asia/Manila');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Support | Water Refilling Station</title>
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
        }
        .header h2 {
            margin: 0;
        }
        .header a {
            color: white;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="navbar-brand">Water Refilling</div>
        <a href="customer_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="create_order.php"><i class="fas fa-plus"></i> Create Order</a>
        <a href="view_orders.php"><i class="fas fa-list"></i> View Orders</a>
        <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
        <a href="support.php" class="active"><i class="fas fa-question-circle"></i> Support</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
    <div class="content">
        <div class="header">
            <h2><i class="fas fa-question-circle"></i> Support</h2>
            <a href="logout.php" class="btn btn-light" style="color:black;"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
        <div class="container mt-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-info-circle"></i> Contact Information</h5>
                    <p class="card-text">For any support or inquiries, please contact us:</p>
                    <ul>
                        <li><i class="fas fa-envelope"></i> Email: joeu.gallenero.ui@phinmaed.com</li>
                        <li><i class="fas fa-phone"></i> Phone: 0910 061 6716</li>
                        <li><i class="fas fa-mobile-alt"></i> Gcash: 0910 061 6716 / Josiah Danielle Gallenero</li>
                    </ul>
                    <p class="card-text"><i class="far fa-clock"></i> Our support team is available from Monday to Friday, 9:00 AM to 5:00 PM.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
