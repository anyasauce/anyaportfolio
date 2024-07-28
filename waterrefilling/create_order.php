<?php
include 'conn.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
    header("Location: login.php");
    exit();
}

date_default_timezone_set('Asia/Manila');

if (isset($_POST['create_order'])) {
    $user_id = $_SESSION['user_id'];
    $order_date = date('Y-m-d H:i:s');
    $status = 'pending';
    $quantity = (int)$_POST['quantity'];

    $query = "INSERT INTO orders (user_id, order_date, status, quantity) VALUES ($user_id, '$order_date', '$status', '$quantity')";
    if (mysqli_query($conn, $query)) {
        ?>
        <script>
        alert("Order created successfully!");
        </script>
        <?php
    } else {
        ?>
        <script>
        alert("Error creating order:");
        </script>
        <?php
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Order | Water Refilling Station</title>
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
        .container {
            max-width: 600px;
            margin-top: 20px;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-text {
            margin-bottom: 20px;
            font-size: 1.2rem;
            font-weight: 500;
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            font-weight: 600;
            font-size: 1.1rem;
            color: #555;
        }

        input[type="number"] {
            padding: 10px;
            font-size: 1rem;
            border-radius: 5px;
            border: 1px solid #ddd;
            width: 100%;
            box-sizing: border-box;
            transition: border-color 0.2s ease-in-out;
        }

        input[type="number"]:focus {
            border-color: #007bff;
            outline: none;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            padding: 12px;
            font-size: 1.2rem;
        }
        .btn-primary:hover {
            background-color: #0069d9;
            border-color: #0062cc;
        }

        .form-text {
            margin-bottom: 20px;
            font-size: 1.1rem;
        }
        
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="navbar-brand">Water Refilling</div>
        <a href="customer_dashboard.php"><i class="fas fa-home icon"></i> Dashboard</a>
        <a href="create_order.php" class="active"><i class="fas fa-plus icon"></i> Create Order</a>
        <a href="view_orders.php"><i class="fas fa-list icon"></i> View Orders</a>
        <a href="profile.php"><i class="fas fa-user icon"></i> Profile</a>
        <a href="support.php"><i class="fas fa-question-circle icon"></i> Support</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt icon"></i> Logout</a>
    </div>
    <div class="content">
        <div class="header">
            <h2><i class="fas fa-plus icon"></i> Create Order</h2>
            <a href="logout.php" class="btn btn-light logout-btn" style="color:black;"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
        <div class="container">
            <p class="form-text">Enter the quantity of gallons and click the button below to create a new order:</p>
            <form action="" method="POST">
                <div class="form-group">
                    <label for="quantity">Quantity (gallons):</label>
                    <input type="number" name="quantity" id="quantity" class="form-control" required>
                </div>
                <button type="submit" name="create_order" class="btn btn-primary"><i class="fas fa-plus-circle"></i> Create Order</button>
            </form>
        </div>
    </div>
</body>
</html>