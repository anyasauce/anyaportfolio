<?php
include 'conn.php';
session_start();
date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_POST['add_item'])) {
    $item_name = $_POST['item_name'];
    $quantity = $_POST['quantity'];

    $query = "INSERT INTO inventory (item_name, quantity) VALUES ('$item_name', $quantity)";
    mysqli_query($conn, $query);
}

if (isset($_POST['update_item'])) {
    $item_id = $_POST['item_id'];
    $quantity = $_POST['quantity'];

    $query = "UPDATE inventory SET quantity=$quantity WHERE id=$item_id";
    if (mysqli_query($conn, $query)) {
        ?>
        <script>
            alert("Item quantity updated successfully!");
        </script>
        <?php
    } else {
        ?>
        <script>
            alert("Error updating item quantity!");
        </script>
        <?php
    }
}

if (isset($_POST['delete_item'])) {
    $item_id = $_POST['item_id'];

    $query = "DELETE FROM inventory WHERE id=$item_id";
    if (mysqli_query($conn, $query)) {
        ?>
        <script>
            alert("Item deleted successfully!");
        </script>
        <?php
    } else {
        ?>
        <script>
            alert("Error deleting item!");
        </script>
        <?php
    }
}

$query = "SELECT * FROM inventory";
$result = mysqli_query($conn, $query);

$order_number = 1;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Inventory | Water Refilling Station</title>
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
        .table-form {
            display: flex;
            align-items: center;
        }
        .table-form .form-control {
            width: auto;
            display: inline;
        }
        .table-form .btn {
            margin-left: 10px;
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
            <h2><i class="fas fa-warehouse"></i> Manage Inventory</h2>
            <a href="logout.php" class="btn btn-light" style="color:black;"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
        <div class="container mt-3">
            <form action="" method="POST">
                <div class="form-row">
                    <div class="col">
                        <input type="text" class="form-control" placeholder="Item Name" name="item_name" required>
                    </div>
                    <div class="col">
                        <input type="number" class="form-control" placeholder="Quantity" name="quantity" required>
                    </div>
                    <div class="col">
                        <button type="submit" class="btn btn-primary" name="add_item"><i class="fas fa-plus"></i> Add Item</button>
                    </div>
                </div>
            </form>
            <hr>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Item ID</th>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($item = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $order_number++ ?></td>
                            <td><?= $item['item_name'] ?></td>
                            <td>
                                <form action="" method="POST" class="table-form">
                                    <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                    <input type="number" name="quantity" value="<?= $item['quantity'] ?>" class="form-control">
                                    <button type="submit" name="update_item" class="btn btn-primary"><i class="fas fa-sync-alt"></i> Update</button>
                                </form>
                            </td>
                            <td>
                                <form action="" method="POST" class="table-form">
                                    <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                    <button type="submit" name="delete_item" class="btn btn-danger"><i class="fas fa-trash-alt"></i> Delete</button>
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