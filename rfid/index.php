<?php
include "conn.php";
date_default_timezone_set('Asia/Manila');

$currentDate = date('Y-m-d');

$query = "SELECT r.profile, r.lastname, r.firstname, 
          DATE_FORMAT(MAX(t.timein), '%h:%i %p') AS timein_formatted, 
          DATE_FORMAT(t.timeout, '%h:%i %p') AS timeout_formatted
          FROM rfid r LEFT JOIN time_records t ON r.rfid = t.rfid 
          WHERE DATE(t.timein) = ? 
          GROUP BY r.rfid
          ORDER BY MIN(t.timein) ASC"; 


$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param($stmt, "s", $currentDate);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    echo "Error: " . mysqli_error($conn);
}
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
</style>

</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light fixed-top" style="background-color: white; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
  <div class="container">

    <span id="clock" class="navbar-text clock ml-auto" style="font-size: 1.3em; font-weight: bold; color: #012970;"></span>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item active">
          <a href="#"><span class="navbar-brand mb-0 h1" style="color: #012970; font-size: 1.2em; font-size: 1.4em"><b>RFID System</b></span></a>
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
                <h4 class="fw-bold" style="color:#012970;">Dashboard</h4>
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
                            <h4 style="color: #012970;">Dashboard</h4>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Time-in/Time-out</li>
                            </ol>
                        </nav>

                <div class="container mt-4">
                    <div class="row">
                    <div class="col-md-6">  
                        <form id="rfidForm" action="process.php" method="post">
                        <div class="form-group">
                            <label for="rfidNumber">RF_ID Number</label>
                            <input type="text" class="form-control" id="rfidNumber" name="rfidNumber" required>
                        </div>
                        <div class="form-group">
                            <select class="form-control" id="timeInOut" name="timeInOut" required>
                            <option value="" disabled selected hidden>Time-in/Time-out</option>
                            <option value="timeIn" name="timein">Time-in</option>
                            <option value="timeOut" name="timeout">Time-out</option>
                            </select>
                        </div>
                        </form>
                        <button class="btn btn-primary" id="scanQR">Scan QR Code</button>
                    </div>
                    </div>
                </div>

        <!-- Modal for QR code scanner -->
        <div class="modal fade" id="qrScannerModal" tabindex="-1" role="dialog" aria-labelledby="qrScannerModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="qrScannerModalLabel">Scan QR Code</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <div class="modal-body">
                <video id="qrVideo" width="100%" height="auto" playsinline></video>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
            </div>
        </div>

        <script>
            function performTimeProcess(rfidNumber, timeProcess) {
                if (timeProcess !== "") {
                    var form = document.createElement("form");
                    form.method = "post";
                    form.action = "process.php";

                    var rfidInput = document.createElement("input");
                    rfidInput.type = "hidden";
                    rfidInput.name = "rfidNumber";
                    rfidInput.value = rfidNumber;
                    form.appendChild(rfidInput);

                    var timeProcessInput = document.createElement("input");
                    timeProcessInput.type = "hidden";
                    timeProcessInput.name = "timeInOut";
                    timeProcessInput.value = timeProcess;
                    form.appendChild(timeProcessInput);

                    document.body.appendChild(form);

                    form.submit();
                } else {
                    alert("Please select time-in or time-out.");
                }
            }

            function initializeScanner() {
                let scanner = new Instascan.Scanner({ video: document.getElementById('qrVideo') });

                Instascan.Camera.getCameras().then(function (cameras) {
                    if (cameras.length > 0) {
                        scanner.start(cameras[0]);
                    } else {
                        console.error('No cameras found.');
                    }
                }).catch(function (error) {
                    console.error(error);
                });

                scanner.addListener('scan', function (content) {
                    var timeProcess = document.getElementById("timeInOut").value;

                    performTimeProcess(content, timeProcess);
                });
            }
            document.getElementById("scanQR").addEventListener("click", function () {
                var timeProcess = document.getElementById("timeInOut").value;

                if (timeProcess !== "") {
                    $('#qrScannerModal').modal('show');
                    initializeScanner();
                } else {
                    alert("Please select time-in or time-out.");
                }
            });
        </script>
        <div class="container mt-2">
            <div class="row search-container">
                <div class="col-md-6">
                    <input type="text" id="searchInput" class="form-control search-input" placeholder="Search">
                </div>
            </div>
        </div>

    <div class="row">
        <div class="col-md-12">
            <?php
            if (empty($result)) {
                echo "<table class='table'>";
                echo "<thead>";
                echo "<tr>";
                echo "<th>Profile</th>";
                echo "<th>Last Name</th>";
                echo "<th>First Name</th>";
                echo "<th>Time In</th>";
                echo "<th>Time Out</th>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";
                echo "<tr>";
                echo "<td colspan='5' class='text-center'>No records found for today.</td>";
                echo "</tr>";
                echo "</tbody>";
                echo "</table>";
                echo "";
            } else {
                if (mysqli_num_rows($result) > 0) {
                    echo "<table class='table'>";
                    echo "<thead>";
                    echo "<tr>";
                    echo "<th>Profile</th>";
                    echo "<th>Last Name</th>";
                    echo "<th>First Name</th>";
                    echo "<th>Time In</th>";
                    echo "<th>Time Out</th>";
                    echo "</tr>";
                    echo "</thead>";
                    echo "<tbody id='tableBody'>";
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td><img src='uploads/{$row['profile']}' class='img-thumbnail rounded-circle' style='width: 80px;height:80px'></td>";
                        echo "<td>{$row['lastname']}</td>";
                        echo "<td>{$row['firstname']}</td>";
                        echo "<td>{$row['timein_formatted']}</td>";
                        echo "<td>{$row['timeout_formatted']}</td>";
                        echo "</tr>";
                    }
                    echo "</tbody>";
                    echo "</table>";
                    echo "";
                } else {
                    echo "<table class='table'>";
                    echo "<thead>";
                    echo "<tr>";
                    echo "<th>Profile</th>";
                    echo "<th>Last Name</th>";
                    echo "<th>First Name</th>";
                    echo "<th>Time In</th>";
                    echo "<th>Time Out</th>";
                    echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";
                    echo "<tr>";
                    echo "<td colspan='5' class='text-center'>No records found for today.</td>";
                    echo "</tr>";
                    echo "</tbody>";   
                    echo "</table>";
                }
            }
            ?>
        </div>
    </div>
    <br>
    <br>
    <br>    
</div>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<footer class="footer" style="background-color: white; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); padding: 17px; font-weight:600;">
  <div class="container">
    <span class="text-black" style="color: #012970;">© 2024 RFID System. All rights reserved.</span>
  </div>
</footer>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>

<script>
  document.getElementById("searchInput").addEventListener("keypress", function(event) {
    if (event.keyCode === 13) {
        event.preventDefault();
        searchTable();
    }
});

document.getElementById("searchInput").addEventListener("input", function() {
    searchTable();
});

function searchTable() {
    var searchText = document.getElementById("searchInput").value.trim().toLowerCase();
    var rows = document.getElementById("tableBody").getElementsByTagName("tr");
    for (var i = 0; i < rows.length; i++) {
        var lastName = rows[i].getElementsByTagName("td")[1].innerText.trim().toLowerCase();
        var firstName = rows[i].getElementsByTagName("td")[2].innerText.trim().toLowerCase();
        if (lastName.includes(searchText) || firstName.includes(searchText)) {
            rows[i].style.display = "";
        } else {
            rows[i].style.display = "none";
        }
    }
}


  function updateClock() {
    var now = new Date();
    var hours = now.getHours();
    var minutes = now.getMinutes();
    var seconds = now.getSeconds();
    var ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12; 
    var timeString = pad(hours) + ":" + pad(minutes) + ":" + pad(seconds) + " " + ampm;
    document.getElementById('clock').innerHTML = timeString;
  }

  function pad(number) {
    if (number < 10) {
      return '0' + number;
    }
    return number;
  }

  setInterval(updateClock, 1000);

</script>

</body>
</html>
