<?php
include 'conn.php';
session_start();
date_default_timezone_set('Asia/Manila');

$password_success = '';
$password_error = '';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "SELECT * FROM users WHERE id=$user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

if (isset($_POST['update_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $password_query = "SELECT password FROM users WHERE id = $user_id";
    $password_result = mysqli_query($conn, $password_query);
    $user_password = mysqli_fetch_assoc($password_result);

    if (password_verify($current_password, $user_password['password'])) {
        if ($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            $update_query = "UPDATE users SET password = '$hashed_password' WHERE id = $user_id";
            if (mysqli_query($conn, $update_query)) {
                ?>
                <script>
                    alert("Password updated successfully!")
                </script>
                <?php
            } else {
                ?>
                <script>
                    alert("Error updating password:")
                </script>
                <?php
            }
        } else {
            ?>
           <script>
            alert("New password and confirm password do not match!")
           </script>
           <?php
        }
    } else {
        ?>
        <script>
            alert("Incorrect current password!")
        </script>
        <?php
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile | Water Refilling Station</title>
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
        .card {
            margin-bottom: 20px;
        }
        .card-title i {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="navbar-brand">Water Refilling</div>
        <a href="customer_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="create_order.php"><i class="fas fa-plus"></i> Create Order</a>
        <a href="view_orders.php"><i class="fas fa-list"></i> View Orders</a>
        <a href="profile.php" class="active"><i class="fas fa-user"></i> Profile</a>
        <a href="support.php"><i class="fas fa-question-circle"></i> Support</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
    <div class="content">
        <div class="header">
            <h2><i class="fas fa-user"></i> Profile</h2>
            <a href="logout.php" class="btn btn-light" style="color:black;"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
        <div class="container mt-3">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-info-circle"></i> User Information</h5>
                            <p class="card-text"><strong><i class="fas fa-user"></i> Name:</strong> <?= isset($user['name']) ? $user['name'] : '' ?></p>
                            <p class="card-text"><strong><i class="fas fa-envelope"></i> Email:</strong> <?= isset($user['email']) ? $user['email'] : '' ?></p>
                            <p class="card-text"><strong><i class="fas fa-phone"></i> Phone:</strong> <?= isset($user['phone']) ? $user['phone'] : '' ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-key"></i> Change Password</h5>
                            <form action="" method="POST">
                                <div class="form-group">
                                    <label for="current_password"><i class="fas fa-lock"></i> Current Password</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                                </div>
                                <div class="form-group">
                                    <label for="new_password"><i class="fas fa-key"></i> New Password</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                                </div>
                                <div class="form-group">
                                    <label for="confirm_password"><i class="fas fa-key"></i> Confirm Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>
                                <button type="submit" name="update_password" class="btn btn-primary"><i class="fas fa-save"></i> Update Password</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

