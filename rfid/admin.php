<?php
include ('conn.php');
include ('phpqrcode/qrlib.php');

session_start();

if (!isset($_SESSION['admin'])) {
    ?>
    <script>
        alert("You don't have permission to access this page.");
        window.location = "duty.php";
    </script>
    <?php
    exit;
}

if(basename($_SERVER['PHP_SELF']) !== 'admin.php' && isset($_SESSION['admin']) && $_SESSION['admin'] === true){
    header("Location: admin.php");
    exit;
}

if (isset($_POST["lastname"], $_POST["firstname"], $_POST["contact"], $_POST["rfid"], $_POST["email"], $_POST["password"], $_POST["gender"])) {
    $lastname = $_POST["lastname"];
    $firstname = $_POST["firstname"];
    $contact = $_POST["contact"];
    $rfid = $_POST["rfid"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $gender = $_POST["gender"];

    $profile_tmp = $_FILES['profile']['tmp_name'];
    $profile_name = $_FILES['profile']['name'];
    $profile_destination = 'uploads/' . $profile_name; 
    move_uploaded_file($profile_tmp, $profile_destination);
    $profile_filename = basename($profile_destination);

    $qr_code_filename = 'qr_' . $rfid . '.png';
    $qr_code_path = 'qrcodes/' . $qr_code_filename;
    if (!file_exists('qrcodes')) {
        mkdir('qrcodes', 0777, true); 
    }
    QRcode::png($rfid, $qr_code_path, QR_ECLEVEL_L, 5);

    $sql = "INSERT INTO rfid (profile, lastname, firstname, contact, rfid, email, password, gender, qr_code_filename) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sssssssss", $profile_filename, $lastname, $firstname, $contact, $rfid, $email, $password, $gender, $qr_code_filename);
        if ($stmt->execute()) {
            ?>
            <script>
                alert("Added to Database!");
            </script>
            <?php
        } else {
            ?>
            <script>
                alert("Error Adding!");
            </script>
            <?php
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
       body {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-color: #f8f9fa;
}

.form-container {
    max-width: 600px; /* Set maximum width */
    width: 100%; /* Ensure it fills the available width */
    background-color: #fff;
    border-radius: 5px;
    padding: 20px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.custom-file-input {
    cursor: pointer;
}

.custom-file-input::-webkit-file-upload-button {
    visibility: hidden;
}

.custom-file-input::before {
    content: 'Choose File';
    display: inline-block;
    background: #007bff;
    color: #fff;
    border: none;
    border-radius: 3px;
    padding: 5px 10px;
    outline: none;
    white-space: nowrap;
    cursor: pointer;
    font-weight: 700;
    font-size: 1rem;
}

.custom-file-input:hover::before {
    border-color: #0056b3;
}

.custom-file-input:active::before {
    background: #0056b3;
}

.custom-file-input:hover,
.custom-file-input:focus {
    outline: none;
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

@media (max-width: 768px) {
    .form-container {
        max-width: 90%; /* Adjust maximum width for smaller screens */
    }

    .main-content {
        margin-left: 0; /* Remove left margin for main content */
        width: 100%; /* Make main content full width */
    }

    .sidebar {
        width: 100%; /* Make sidebar full width */
        border-right: none; /* Remove right border */
    }

    .sidebar .main-content {
        padding-left: 0; /* Remove padding for main content inside sidebar */
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

    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light fixed-top" style="background-color: white; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
  <div class="container">
    <span id="clock" class="navbar-text clock ml-auto" style="font-size: 1.3em; font-weight: bold; color: white;"></span>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item active">
        <a href="#"><span class="navbar-brand mb-0 h1" style="color: #012970; font-size: 1.4em;"><b>ADMIN DASHBOARD</b></span></a>
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
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == '#' ? 'active dashboard-link' : ''; ?>" aria-current="page" href="admin.php"><i class="bi bi-person-fill-add"style="color:grey; font-size:17px"></i></i> Adding Employees</a>
                        </li>
                        <li class="nav-item" style="white-space: nowrap;">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'listofstudent.php' ? 'active' : ''; ?>" href="gender.php"><i class="bi bi-person-square"style="color:grey; font-size:17px"></i></i> Gender Graphs</a>
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
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <h4 style="color:#012970;">Dashboard</h4>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="admin.php">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Adding Employees</li>
                            </ol>
                        </nav>
    <div class="form-container">
        <h2 class="text-center mb-4">Add Employees</h2>
        <form action="admin.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="profile">Profile Picture:</label>
                <div class="custom-file">
                    <input type="file" class="custom-file-input" id="profile" name="profile" required onchange="displayFileName()">
                    <label class="custom-file-label" for="profile">Choose file...</label>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="lastname">Last Name:</label>
                        <input type="text" class="form-control" id="lastname" name="lastname" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="firstname">First Name:</label>
                        <input type="text" class="form-control" id="firstname" name="firstname" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="contact">Contact:</label>
                        <input type="text" class="form-control" id="contact" name="contact" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="rfid">RFID:</label>
                        <input type="text" class="form-control" id="rfid" name="rfid" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="gender">Gender:</label>
                        <select class="form-control" id="gender" name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Submit</button>
        </form>
    </div>
    <br>
    <br>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function displayFileName() {
            var fileName = document.getElementById("profile").files[0].name;
            document.querySelector(".custom-file-label").innerText = fileName;
        }
    </script>
</body>
</html>
