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
    <title>Customer Dashboard | Water Refilling Station</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            background-color: #f8f9fa;
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
            border-bottom: 2px solid #0056b3;
        }
        .header h2 {
            margin: 0;
        }
        .header a {
            color: white;
        }
        .card {
            margin-bottom: 20px;
        }
        .card-title {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .card-text {
            margin-bottom: 15px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            font-size: 1rem;
            padding: 10px 20px;
        }
        .btn-primary:hover {
            background-color: #0069d9;
            border-color: #0062cc;
        }
        
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="navbar-brand">Water Refilling</div>
        <a href="customer_dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
        <a href="create_order.php"><i class="fas fa-plus"></i> Create Order</a>
        <a href="view_orders.php"><i class="fas fa-list"></i> View Orders</a>
        <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
        <a href="support.php"><i class="fas fa-question-circle"></i> Support</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
    <div class="content">
        <div class="header">
            <h2><i class="fas fa-home"></i> Customer Dashboard</h2>
            <a href="logout.php" class="btn btn-light" style="color:black;"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
        <div class="container mt-3">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Create New Order</h5>
                            <p class="card-text">Place a new order for water refilling.</p>
                            <a href="create_order.php" class="btn btn-primary"><i class="fas fa-plus-circle"></i> Create Order</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">View Orders</h5>
                            <p class="card-text">Check the status of your current and past orders.</p>
                            <a href="view_orders.php" class="btn btn-primary"><i class="fas fa-list"></i> View Orders</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Profile</h5>
                            <p class="card-text">Manage your profile and account settings.</p>
                            <a href="profile.php" class="btn btn-primary"><i class="fas fa-user"></i> Profile</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Support</h5>
                            <p class="card-text">Get help and support for any issues or questions.</p>
                            <a href="support.php" class="btn btn-primary"><i class="fas fa-question-circle"></i> Support</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
