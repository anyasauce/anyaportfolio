<?php
session_start();
include ('conn.php');

if (!isset($_SESSION['email'])) {
    header("Location: duty.php");
    exit();
}

$email = $_SESSION['email'];
$query = "SELECT * FROM rfid WHERE email = '$email'";
$result = mysqli_query($conn, $query);

if ($result) {
    $data = mysqli_fetch_assoc($result);
} else {
    echo "Error: " . mysqli_error($conn);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $id = $_POST['id'];
    $lastname = $_POST['lastname'];
    $firstname = $_POST['firstname'];
    $contact = $_POST['contact'];
    $rfid = $_POST['rfid'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if(isset($_FILES['profile']) && $_FILES['profile']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "uploads/";
        $fileName = basename($_FILES['profile']['name']);
        $targetFilePath = $targetDir . $fileName;

        if(move_uploaded_file($_FILES['profile']['tmp_name'], $targetFilePath)) {
            $update_query = "UPDATE rfid SET lastname = '$lastname', firstname = '$firstname', contact = '$contact', rfid = '$rfid', email = '$email', password = '$password', profile = '$fileName' WHERE id = '$id'";
            
            if (mysqli_query($conn, $update_query)) {
                header("Location: profile.php");
                exit();
            } else {
                echo "Error updating record: " . mysqli_error($conn);
            }
        } else {
            echo "Error uploading file.";
        }
    } else {
        $update_query = "UPDATE rfid SET lastname = '$lastname', firstname = '$firstname', contact = '$contact', rfid = '$rfid', email = '$email', password = '$password' WHERE id = '$id'";
        
        if (mysqli_query($conn, $update_query)) {
            header("Location: profile.php");
            exit();
        } else {
            echo "Error updating record: " . mysqli_error($conn);
        }
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Time-in/Time-out</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
        font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        overflow-y: auto; 
        overflow-x: hidden; 
        margin: 0; 
    }
    .footer {
        position: fixed;
        left: 0;
        bottom: 0;
        width: 100%;
        background-color: #f8f9fa;
        text-align: center;
        padding: 10px;
    }
    .search-container {
        margin-bottom: 20px;
    }
    .search-input {
        width: 100%;
        max-width: 150px; 
        margin-left: 837px;
    }
    .sidebar {
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        width: 250px;
        overflow-y: auto;
        border-right: 1px solid #ced4da;
    }
    .sidebar .main-content {
        padding-top: 20px;
        padding-left: 20px; 
    }
    @media (min-width: 768px) {
        .sidebar {
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        .main-content {
            margin-left: 250px; 
            padding-left: 20px; 
        }
    }
    .dropdown-menu a {
        color: gray !important;
    }
    .text-gray {
        color: #6c757d;
    }
    .nav-link {
        color: #808080;
    }
    .breadcrumb-item a {
        color: #6c757d;
        text-decoration: none; 
    }
    .breadcrumb-item a:hover {
        color: #495057; 
        text-decoration: none; 
    }
    .breadcrumb-home {
        font-size: larger; 
        font-weight: bold; 
    }
    .dropdown-menu {
        min-width: auto; 
        width: auto; 
        white-space: nowrap; 
        max-width: 500px; 
    }
    .nav-link.nav-profile:hover .align-icons,
    .nav-link.nav-profile.active .align-icons {
        align-items: center;
    }
    .align-icons {
        margin-right: 11px; 
        color: black;
    }
    .navbar {
        z-index: 1000; 
    }
    .main-content {
        margin-left: 250px;
        overflow-y: auto; 
        overflow-x: hidden; 
        padding-top: 20px; 
        width: calc(100% - 250px); 
    }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light fixed-top" style="background-color: #55a0ff; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
  <div class="container">
    <span id="clock" class="navbar-text clock ml-auto" style="font-size: 1.3em; font-weight: bold; color: white;"></span>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item active">
          <a href="#"><span class="navbar-brand mb-0 h1" style="color: white; font-size: 1.2em;">RFID System</span></a>
        </li>
      </ul>
    </div>
  </div>
</nav>
<div class="container-fluid">
    <div class="row">
        <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
            <div class="position-sticky">
                <div class="p-4">
                  <br>
                  <br>
                  <br>
                <h4 class="fw-bold">Dashboard</h4>
                   <hr>
                    <ul class="nav flex-column mt-3">
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == '#' ? 'active dashboard-link' : ''; ?>" aria-current="page" href="#"><i class="bi bi-border-all"></i> Dashboard</a>
                        </li>
                        <li class="nav-item" style="white-space: nowrap;">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'listofstudent.php' ? 'active' : ''; ?>" href="duty.php"><i class="bi bi-clock-history"></i> Go to Time_Records</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <nav aria-label="breadcrumb">
                            <br>
                            <br>
                            <br>
                            <h4>Dashboard</h4>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Profile</li>
                            </ol>
                        </nav>
        <main class="container mt-5">
            <div class="card border rounded shadow" style="max-width: 600px; margin: 0 auto;">
                <div class="card-body" style="overflow-y: auto;">
                    <div class="text-center mb-4">
                    <img src="uploads/<?php echo $data['profile']; ?>" class="profile-img mb-3 img-thumbnail rounded-circle" alt="Profile Picture" style="width: 100px; height: 100px;">
                        <h4 class="fw-bold"><?php echo $data['firstname'] . ' ' . $data['lastname']; ?></h4>
                    </div>
                    <div class="profile-details" style="max-height: 400px; overflow-y: auto;">
                        <div class="mb-3">
                            <strong>Email:</strong>
                            <span><?php echo $data['email']; ?></span>
                        </div>
                        <div class="mb-3">
                            <strong>Contact:</strong>
                            <span><?php echo $data['contact']; ?></span>
                        </div>
                        <div class="mb-3">
                            <strong>RFID:</strong>
                            <span><?php echo $data['rfid']; ?></span>
                        </div>
                        <div class="mb-3">
                            <strong>Password:</strong>
                            <span><?php echo $data['password']; ?></span>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <button type="button" class="btn btn-success" name="update" data-bs-toggle="modal" data-bs-target="#updateModal<?php echo $data['id']; ?>">
                            Update Profile
                        </button>
                    </div>
                </div>
            </div>
        </main>
        <div class="modal fade" id="updateModal<?php echo $data['id']; ?>" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="updateModalLabel">Update Profile Information</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">                    
                        <form action="profile.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?php echo $data['id']; ?>">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="lastname" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo $data['lastname']; ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="firstname" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo $data['firstname']; ?>" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="contact" class="form-label">Contact</label>
                                    <input type="text" class="form-control" id="contact" name="contact" value="<?php echo $data['contact']; ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="rfid" class="form-label">RFID</label>
                                    <input type="text" class="form-control" id="rfid" name="rfid" value="<?php echo $data['rfid']; ?>" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo $data['email']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" value="<?php echo $data['password']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="profile" class="form-label">Upload New Profile Picture</label>
                                <input type="file" class="form-control" id="profile" name="profile" accept="image/*">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-success" name="update">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <br>
        <br>
        <footer class="footer" style="background-color: #55a0ff; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
        <div class="container">
            <span class="text-white">© 2024 RFID System. All rights reserved.</span>
        </div>
        </footer>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
