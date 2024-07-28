<?php
session_start();
include('conn.php');

date_default_timezone_set('Asia/Manila');

if(isset($_GET['logout'])) {
    $_SESSION = array();
    session_destroy();
    header("Location: duty.php");
    exit();
}

if(isset($_POST['email']) && isset($_POST['password'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    $sql = "SELECT * FROM rfid WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $_SESSION['email'] = $email;
        header("Location: duty.php");
        exit();
    } else {
        $error_message = "Invalid email or password. Please try again.";
    }
}

if(isset($_POST['admin'])){
    $email = $_POST['adminemail'];
    $password = $_POST['adminpassword'];

    $validate = mysqli_query($conn, "SELECT * FROM admin WHERE email = '$email' AND password = '$password'");

    if(mysqli_num_rows($validate) == 1){
        $_SESSION['admin'] = true; 
        ?>
        <script>
            alert('Login as Admin Success!');
            window.location = "admin.php";
        </script>
        <?php
        exit(); 
    } else {
        ?>
        <script>
            alert('Admin login failed! Please try again.');
            window.location = "duty.php";
        </script>
        <?php
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
  <title>Time_Records</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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
        margin-left: auto;
        margin-right: auto;
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
    .modal-backdrop {
        background-color: rgba(0, 0, 0, 0.5);
    }
    .timein-container {
            border-radius: 5px;
            background-color: #fff;
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
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<nav class="navbar navbar-expand-lg navbar-light fixed-top" style="background-color: white; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
    <div class="container">
        <a class="navbar-brand" href="#" style="color: #012970; font-weight:600;">RFID Time Records</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <?php if (!isset($_SESSION['email'])): ?>
                    <li class="nav-item">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#loginModal" style="color:white;">Login</button>
                    </li>
                <?php endif; ?>
                <?php if (isset($_SESSION['email'])): ?>
                    <li class="nav-item dropdown">
                        <?php
                        if(isset($_SESSION['email'])) {
                            $email = $_SESSION['email'];
                            $sql = "SELECT firstname, lastname FROM rfid WHERE email = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("s", $email);
                            $stmt->execute();
                            $result = $stmt->get_result();
                        
                            if ($result->num_rows == 1) {
                                $row = $result->fetch_assoc();
                                $firstName = $row['firstname'];
                                $lastName = $row['lastname'];
                            }
                        }
                        ?>
                        <a class="nav-link dropdown-toggle" style="color:#012970;" href="#" id="navbarDropdownMenuLink" role="button" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown" style="color: white;">
                            <b><?php echo isset($firstName) && isset($lastName) ? $firstName . ' ' . $lastName : 'My Profile'; ?></b>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                        <a class="dropdown-item" href="profile.php"><i class="bi bi-person-circle"></i>&nbsp;My Profile</a>
                        </div>
                    </li>
                <?php endif; ?>
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
                                <li class="breadcrumb-item active" aria-current="page">Time_Records</li>
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
<div class="container">
    <div class="row mt-4">
        <div class="col-md-6">
        <label for="showEntries">Show Entries:</label>
        <select id="showEntries" class="form-control" style="width: 70px;" onchange="showEntries()">
            <option value="5">5</option>
            <option value="10">10</option>
            <option value="15">15</option>
            <option value="20">20</option>
            <option value="all">All</option>
        </select>
        </div>
    </div>
    <div class="table-responsive mt-4">
        <table id="dutyHoursTable" class="table table-striped">
            <thead>
                <tr>
                    <th>RFID</th>
                    <th>Date</th>
                    <th>Time In</th>
                    <th>Time Out</th>
                    <th>Duration</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                require_once('conn.php');
                date_default_timezone_set('Asia/Manila');

                if(isset($_SESSION['email'])) {
                    $email = $_SESSION['email'];

                    $sql = "SELECT rfid.email,
                                rfid.rfid, 
                                time_records.timein, 
                                time_records.timeout, 
                                SEC_TO_TIME(SUM(IFNULL(TIMESTAMPDIFF(SECOND, time_records.timein, time_records.timeout), 0))) AS total_time
                            FROM rfid 
                            LEFT JOIN time_records ON rfid.rfid = time_records.rfid
                            WHERE rfid.email = ?
                            GROUP BY time_records.timein, time_records.timeout";

                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $email);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $timein = ($row['timein']) ? date('h:ia', strtotime($row['timein'])) : 'N/A';
                            $timeout = ($row['timeout']) ? date('h:ia', strtotime($row['timeout'])) : 'N/A';
                            
                            $total_time_seconds = strtotime($row['total_time']) - strtotime('TODAY');
                            $hours = floor($total_time_seconds / 3600);
                            $minutes = floor(($total_time_seconds % 3600) / 60);

                            $total_hours = '';

                            if ($hours > 0) {
                                $total_hours .= "$hours hr";
                                if ($hours > 1) {
                                    $total_hours .= 's';
                                }
                            }

                            if ($minutes > 0) {
                                if (!empty($total_hours)) {
                                    $total_hours .= ' ';
                                }
                                $total_hours .= "$minutes min";
                                if ($minutes > 1) {
                                    $total_hours .= 's';
                                }
                            }

                            if (empty($total_hours)) {
                                $total_hours = 'N/A';
                            } else {
                                $total_hours .= "/s";
                            }
                    ?>
                            <tr>
                                <td><?php echo $row['rfid']; ?></td>
                                <td><?php echo date('m-d-Y', strtotime($row['timein'])); ?></td>
                                <td><?php echo $timein; ?></td>
                                <td><?php echo $timeout; ?></td>
                                <td><?php echo $hours . ' hr/s ' .'and '. $minutes . ' mn/s'; ?></td>
                            </tr>
                    <?php 
                        }
                    } else {
                    ?>
                        <tr>
                            <td colspan='6'>No duty hours data available.</td>
                        </tr>
                    <?php 
                    }
                } else {
                ?>
                    <tr>
                        <td colspan='6'>Please log in to view duty hours.</td>
                    </tr>
                <?php 
                }
                ?>
            </tbody>
        </table>
    </div>
    <div class="row mt-4">
        <div class="col-md-6">
            <ul id="pagination" class="pagination justify-content-center">
            </ul>
        </div>
    </div>
</div>
<script>
    function showEntries() {
        var selectedValue = document.getElementById("showEntries").value;
        var rows = document.querySelectorAll('#dutyHoursTable tbody tr');

        for (var i = 0; i < rows.length; i++) {
            if (selectedValue === 'all' || i < parseInt(selectedValue)) {
                rows[i].style.display = 'table-row';
            } else {
                rows[i].style.display = 'none';
            }
        }
    }

    window.onload = function() {
        showEntries();
    };
</script>
<?php 
include ('conn.php');
date_default_timezone_set('Asia/Manila');

$total_all_hours = 0;
$total_all_minutes = 0;

if(isset($_SESSION['email'])) {
    $email = $_SESSION['email'];

    $sql = "SELECT SEC_TO_TIME(SUM(IFNULL(TIMESTAMPDIFF(SECOND, time_records.timein, time_records.timeout), 0))) AS total_time
            FROM rfid 
            LEFT JOIN time_records ON rfid.rfid = time_records.rfid
            WHERE rfid.email = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $total_time_seconds = strtotime($row['total_time']) - strtotime('TODAY');
        $total_all_hours = floor($total_time_seconds / 3600);
        $total_all_minutes = floor(($total_time_seconds % 3600) / 60);
    }
}
?>
<hr>
<footer>
    <div class="container" style="text-align:center;">
        <div class="row">
            <div class="col-md-12">
                <?php
                $total_rendered_hours = '';
                if ($total_all_hours > 0) {
                    $total_rendered_hours = "$total_all_hours Hr";
                    if ($total_all_hours > 1) {
                        $total_rendered_hours .= '/s' . ' and';
                    }
                }
                if ($total_all_minutes > 0 || empty($total_rendered_hours)) {
                    if (!empty($total_rendered_hours)) {
                        $total_rendered_hours .= ' ';
                    }
                    $total_rendered_hours .= "$total_all_minutes mn";
                    if ($total_all_minutes > 1) {
                        $total_rendered_hours .= '';
                    }
                }
                if (empty($total_rendered_hours)) {
                    $total_rendered_hours = 'N/A';
                } else {
                    $total_rendered_hours .= "/s";
                }
                
?>
                <p><b>Total Hours Rendered:</b> <b><?php echo $total_rendered_hours; ?></b></p>

            </div>
        </div>
    </div>
</footer>
<br>
<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loginModalLabel">Employees Login 2.0</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#userLoginTab" aria-selected="true"><i class="fas fa-sign-in-alt"></i> Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#adminLoginTab" aria-selected="false"><i class="fas fa-user-shield"></i> Admin Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#forgotPasswordTab" aria-selected="false"><i class="fas fa-key"></i> Forgot Password</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="userLoginTab" role="tabpanel">
                        <form action="duty.php" method="POST" class="mb-3">
                            <div class="form-group">
                                <div class="input-group mt-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    </div>
                                <input type="email" class="form-control" name="email" required placeholder="Email">
                            </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" class="form-control" name="password" required placeholder="Password">
                                    </div>
                                </div>
                            <button type="submit" class="btn btn-primary" name="login">Login as Employees</button>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="adminLoginTab" role="tabpanel">
                        <form action="duty.php" method="POST" class="mb-3">
                            <div class="form-group">
                                <div class="input-group mt-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    </div>
                                    <input type="email" class="form-control" name="adminemail" required placeholder="Admin Email">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" name="adminpassword" required placeholder="Admin Password">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary" name="admin">Login as Admin</button>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="forgotPasswordTab" role="tabpanel">
                        <form action="duty.php" method="POST">
                                <div class="form-group">
                                    <div class="input-group mt-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        </div>
                                        <input type="email" class="form-control" id="resetPasswordEmail" placeholder="Enter email" name="email">
                                    </div>
                                </div>
                            <button type="submit" class="btn btn-primary">Reset Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <script>
    window.addEventListener('DOMContentLoaded', (event) => {
        if (window.location.hash === '#loginModal') {
            $('#loginModal').modal('show');
        }
    });

    function showLoginFailedAlert() {
        window.alert('Login Failed! Please check your email and password.');
    }
</script>
<br>
<br>
<footer class="footer" style="background-color: white; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); padding: 17px; font-weight:600;">
  <div class="container">
    <span class="text-black" style="color: #012970;">© 2024 RFID System. All rights reserved.</span>
  </div>
</footer>

<script>
    if (!<?php echo isset($_SESSION['email']) ? 'true' : 'false'; ?>) {
        document.getElementById('myProfileLink').addEventListener('click', function(event) {
            event.preventDefault();
            alert('Please login again to access this page.');
        });
    }
</script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    if (!<?php echo isset($_SESSION['email']) ? 'true' : 'false'; ?>) {
        document.getElementById('myProfileLink').addEventListener('click', function(event) {
            event.preventDefault();
            alert('Please login again to access this page.');
        });
    }
</script>
