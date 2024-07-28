<?php
session_start();
require_once('conn.php');

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

if (isset($_POST['update'])) {
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

$email = $_SESSION['email'];
$query = "SELECT * FROM rfid WHERE email = '$email'";
$result = mysqli_query($conn, $query);

if ($result) {
    $data = mysqli_fetch_assoc($result);
} else {
    echo "Error: " . mysqli_error($conn);
}

if (isset($_GET['download'])) {
    $fileName = $_GET['download'];
    $filePath = 'qrcodes/' . $fileName;
    if (file_exists($filePath)) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        readfile($filePath);
        exit;
    } else {
        echo "File not found.";
    }
}
function fetchUserDataFromDB() {
    global $conn;

    $today = date('Y-m-d');

    $query = "SELECT r.lastname, r.firstname, 
    MAX(t.timein) AS timein, 
    MAX(t.timeout) AS timeout
    FROM rfid r LEFT JOIN time_records t ON r.rfid = t.rfid 
    WHERE DATE(t.timein) = ? OR DATE(t.timeout) = ?
    GROUP BY r.rfid
    ORDER BY MIN(t.timein) ASC";

    if ($stmt = mysqli_prepare($conn, $query)) {
        mysqli_stmt_bind_param($stmt, "ss", $today, $today);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result) {
            if (mysqli_num_rows($result) > 0) {
                $data = '';
                while ($row = mysqli_fetch_assoc($result)) {
                    $dotColor = '';
                    $time = '';
                    if (!empty($row['timeout'])) {
                        $time = date('h:i A', strtotime($row['timeout']));
                        $dotColor = 'red';
                    } elseif (!empty($row['timein'])) {
                        $time = date('h:i A', strtotime($row['timein']));
                        $dotColor = 'green';
                    }
                    $time = '<span style="color: grey;">' . $time . '</span>';
                    $dot = '<span style="color: ' . $dotColor . '; font-size: 20px;">●</span>';
                    $data .= '<p>' . $time . ' ' . $dot . '  ' . htmlspecialchars($row['firstname']) . ' ' . htmlspecialchars($row['lastname']) . '</p>';
                }
                return $data;
            } else {
                return '<p>No recent time in/out data available for today.</p>';
            }
        } else {
            return '<p>Error in fetching result: ' . htmlspecialchars(mysqli_error($conn)) . '</p>';
        }
    } else {
        return '<p>Error preparing the SQL statement: ' . htmlspecialchars(mysqli_error($conn)) . '</p>';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Employees Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
  <style>
    body {
        font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        overflow-y: auto; 
        overflow-x: hidden;
        margin: 0;
        background-color: #f8f9fa;
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
        color: #012970;
        font-weight: 600;
        font-size: 15px;
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
    .timein-container {
            border-radius: 5px;
            background-color: white;
            padding: 15px;
            margin-bottom: 20px;
        }

        .timein-item {
            padding: 5px;
            margin-bottom: 5px;
            border-bottom: 1px solid #ddd;
        }

        .timein-item:last-child {
            border-bottom: none;
        }

        .timein-status {
            font-weight: bold;
            margin-right: 10px;
        }

        .timein-dot {
            font-size: 20px;
            margin-right: 10px;
        }
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light fixed-top" style="background-color: white; padding: 0;">
    <div class="container">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav align-items-center">
                <li class="nav-item active">
                    <a href="#" class="nav-link">
                        <span class="navbar-brand mb-0 h1" style="color: #012970; font-size: 1.4em;"><b>RFID System</b></span>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color: #012970;">
                        <i class="bi bi-person-circle"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li class="nav-item border-top border-bottom">
                            <span class="nav-link text-gray fw-bold fs-5" style="color: gray;">
                                <?php echo $data['firstname'] . ' ' . $data['lastname']; ?><br>
                                <span class="badge bg-transparent d-block mx-auto mt-1 fs-6"
                                    style="color: gray;">Employees</span>
                            </span>
                        </li>
                       
                        <li class="nav-item">
                            <a class="nav-link" href="duty.php?logout=true" style="border: none; background: none; cursor: pointer;"><i class="bi bi-box-arrow-right"></i>&nbsp;Logout</a>
                        </li>
                    </ul>
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
                  <h4 class="fw-bold" style="color: #012970;">Dashboard</h4>
                   <hr>
                   <ul class="nav flex-column mt-3">
                        <li class="nav-item" style="white-space: nowrap;">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == '#' ? 'active dashboard-link' : ''; ?>" aria-current="page" href="dashboard.php"><i class="bi bi-border-all" style="color:grey;"></i></i> Dashboard</a>
                        </li>
                        <li class="nav-item" style="white-space: nowrap;">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == '#' ? 'active dashboard-link' : ''; ?>" aria-current="page" href="index.php"><i class="bi bi-clock-fill"style="color:grey;"></i></i> Time-in/Time-out</a>
                        </li>
                        <li class="nav-item" style="white-space: nowrap;">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'listofstudent.php' ? 'active' : ''; ?>" href="duty.php"><i class="bi bi-calendar-check-fill"style="color:grey;"></i></i> Time_Records</a>
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
                            <h4 style="color:#012970;">Dashboard</h4>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Profile</li>
                            </ol>
                        </nav>
                        <div class="row">
                    <div class="col-md-12">
                        <div class="timein-container">
                        <h4 style="font-size:20px; margin-bottom: 30px; color:#012970;">Recent Timein | <span style="color:grey; font-size: 17px;">Today</span></h4>
                            <div class="timein-box">
                                <?php echo fetchUserDataFromDB(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            <div class="card">
                <div class="card-header">Update Profile</div>
                    <div class="card-body">
                        <form action="profile.php" method="POST">
                            <input type="hidden" name="id" value="<?php echo $data['id']; ?>">
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="bi bi-person-fill"></i></div>
                                        <input class="form-control" type="text" name="lastname" value="<?php echo $data['lastname']; ?>" required>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="bi bi-person-fill"></i></div>
                                        <input class="form-control" type="text" name="firstname" value="<?php echo $data['firstname']; ?>" required>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="bi bi-telephone-fill"></i></div>
                                        <input type="text" class="form-control" id="contact" name="contact" placeholder="Contact" value="<?php echo $data['contact']; ?>" required>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="bi bi-tag-fill"></i></div>
                                    <input type="text" class="form-control" id="rfid" name="rfid" placeholder="RFID" value="<?php echo $data['rfid']; ?>" required>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="fas fa-lock"></i></div>
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" value="<?php echo $data['password']; ?>" required>
                                    </div>
                                    <input class="mx-2 mt-2" type="checkbox" onclick="togglePasswordVisibility()">Show Password
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="bi bi-envelope-fill"></i></i></div>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="<?php echo $data['email']; ?>" required>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-start">
                                    <button class="btn btn-primary" type="submit" name="update">Update Profile</button>
                                </div>
                                <div class="d-flex justify-content-start mt-2">
                                    <a href="?download=<?php echo $data['qr_code_filename']; ?>" class="btn btn-success" role="button">Download QR Code</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('input').forEach(function(input) {
            input.addEventListener('focus', function() {
                this.removeAttribute('readonly');
            });
            input.setAttribute('readonly', 'readonly');
        });
    });

    function togglePasswordVisibility() {
        var passwordField = document.getElementById('password');
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
        } else {
            passwordField.type = 'password';
        }
    }

    window.onload = function() {
        document.getElementById('email').setAttribute('readonly', 'readonly');
        document.getElementById('email').style.pointerEvents = 'none'; 
        document.getElementById('email').style.cursor = 'default'; 
    };
</script>


<br>
<br>
<br>
<br>
<footer class="footer" style="background-color: white; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); padding: 17px; font-weight:600;">
  <div class="container">
    <span class="text-black" style="color: #012970;">© 2024 RFID System. All rights reserved.</span>
  </div>
</footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
