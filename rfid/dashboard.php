<?php
include "conn.php";
session_start();

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
    <title>Main Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        .form-container {
            width: 600px;
            background-color: #fff;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .sidebar {
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            overflow-y: auto;
            background-color: white;
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
            .form-container {
                margin-left: 0;
                width: 100%;
            }
        }
        @media (max-width: 767px) {
            .form-container {
                width: 90%;
                margin-left: 5%;
            }
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
        #genderPercentageChart {
            margin-left: -210px;
        }
        .count-section {
            padding: 10px;
            border: 2px solid #007bff;
            border-radius: 5px;
        }
        .count-section.male {
            background-color: #007bff;
        }
        .count-section.female {
            border-color: #ff69b4;
            background-color: #ff69b4;
        }
        .count-label {
            color: #fff;
            font-weight: bold;
        }
        .count-number {
            color: #fff;
            font-size: 1.2em;
        }
        .container-fluid {
            margin-top: 28px; 
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
        .policy-section {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .policy-section-title {
            text-align: center;
            font-size: 20px;
            color: grey;
            margin-bottom: 20px;
        }

        .work-item {
            max-width: 100%;
            min-height: 360px; 
            border: 1px solid #fff;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .work-item h5 {
            color: #012970;
            margin-top: 0;
        }

        .work-item p {
            color: #666;
            word-wrap: break-word;
        }

        .work-item-image {
            max-width: 100%;
            height: auto;
            margin-bottom: 10px;
            text-align: center;
        }

        .work-item-image img {
            max-width: 100%;
            height: 150px;
            border-radius: 5px;
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
          <a href="#"><span class="navbar-brand mb-0 h1" style="color: #012970; font-size: 1.4em;"><b>Dashboard</b></span></a>
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
                            <h4 style="color:#012970;">Dashboard</h4>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="timein-container">
                        <h4 style="font-size:20px; margin-bottom: 30px;color:#012970;">Recent Timein | <span style="color:grey; font-size: 17px;">Today</span></h4>
                            <div class="timein-box">
                                <?php echo fetchUserDataFromDB(); ?>
                            </div>
                        </div>
                    </div>
                </div>
                
    <div class="policy-section">
        <h2 class="policy-section-title">Policy Work Items</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="work-item">
                    <div class="work-item-image">
                        <img src="images/policy1.gif" alt="Policy 1 Image">
                    </div>
                    <h5>Policy 1</h5>
                    <p>Allow employees to choose their work hours within a specified range, promoting work-life balance and accommodating different schedules.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="work-item">
                    <div class="work-item-image">
                        <img src="images/policy2.gif" alt="Policy 2 Image">
                    </div>
                    <h5>Policy 2</h5>
                    <p>Establish guidelines for employees to work remotely, including communication expectations, equipment provisions, and cybersecurity measures.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="work-item">
                    <div class="work-item-image">
                        <img src="images/policy3.gif" alt="Policy 3 Image">
                    </div>
                    <h5>Policy 3</h5>
                    <p>Define appropriate attire for the workplace, considering industry standards, company culture, and client interactions.</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="work-item">
                    <div class="work-item-image">
                        <img src="images/policy4.gif" alt="Policy 4 Image">
                    </div>
                    <h5>Policy 4</h5>
                    <p>Employees must maintain tidy workspaces, ensuring both physical areas and digital files are regularly organized.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="work-item">
                    <div class="work-item-image">
                        <img src="images/policy5.gif" alt="Policy 5 Image">
                    </div>
                    <h5>Policy 5</h5>
                    <p> All communications, whether digital or in-person, must be professional, timely, respectful, and use proper language.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="work-item">
                    <div class="work-item-image">
                        <img src="images/policy6.gif" alt="Policy 6 Image">
                    </div>
                    <h5>Policy 6</h5>
                    <p>Employees must follow health and safety guidelines, including ergonomic setups for remote work and office protocols.</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="work-item">
                    <div class="work-item-image">
                        <img src="images/policy7.gif" alt="Policy 7 Image">
                    </div>
                    <h5>Policy 7</h5>
                    <p>Employees must meet work goals and deadlines; regular performance reviews provide feedback and support.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="work-item">
                    <div class="work-item-image">
                        <img src="images/policy8.gif" alt="Policy 8 Image">
                    </div>
                    <h5>Policy 8</h5>
                    <p>Employees must securely handle company data, including using secure networks for online work.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="work-item">
                    <div class="work-item-image">
                        <img src="images/policy9.gif" alt="Policy 9 Image">
                    </div>
                    <h5>Policy 9</h5>
                    <p>Employees must start work on time; repeated lateness or absences may lead to discipline.</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 offset-md-0">
                <div class="work-item">
                    <div class="work-item-image">
                        <img src="images/policy10.gif" alt="Policy 10 Image">
                    </div>
                    <h5>Policy 10</h5>
                    <p>Employees must actively contribute to projects, support colleagues, and utilize collaboration tools effectively, fostering communication.</p>
                </div>
            </div>
        </div>
    </div>


</body>
<script>
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

</html>
