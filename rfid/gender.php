<?php
include 'conn.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT gender FROM rfid WHERE gender IN ('Male', 'Female')";
$result = $conn->query($sql);

if ($result === false) {
    die("Error executing query: " . $conn->error);
}

$totalCount = $result->num_rows;
$maleCount = 0;
$femaleCount = 0;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if ($row['gender'] == 'Male') {
            $maleCount++;
        } elseif ($row['gender'] == 'Female') {
            $femaleCount++;
        }
    }
}

$conn->close();

$malePercentage = ($maleCount / $totalCount) * 100;
$femalePercentage = ($femaleCount / $totalCount) * 100;


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
.container-fluid{
    margin-top: 177px;
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
                  <h4 style="color:#012970;">Dashboard</h4>
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

                        <h4 style="color:#012970;">Dashboard</h4>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="admin.php">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Gender Graphs</li>
                            </ol>
                        </nav>
                        <div class="form-container col-lg-5">
                            <div class="row justify-content-center">
                                <div class="col-md-6 text-center">
                                    <canvas id="genderChart" width="200" height="200"></canvas>
                                </div>
                                <div class="col-md-6 text-center">
                                    <canvas id="genderPercentageChart" width="200" height="200"></canvas>
                                </div>
                            </div>
                            <div class="row justify-content-center mt-3">
                                <div class="col-md-6 text-center">
                                    <div class="count-section male">
                                        <span class="count-label">Male Count:</span>
                                        <span class="count-number"><?php echo $maleCount; ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6 text-center">
                                    <div class="count-section female">
                                        <span class="count-label">Female Count:</span>
                                        <span class="count-number"><?php echo $femaleCount; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <br>
        <br>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
        var genderPercentageChartCtx = document.getElementById('genderPercentageChart').getContext('2d');

        var genderPercentageChart = new Chart(genderPercentageChartCtx, {
            type: 'pie',
            data: {
                labels: ['Male', 'Female'],
                datasets: [{
                    label: 'Gender Distribution',
                    data: [<?php echo $maleCount; ?>, <?php echo $femaleCount; ?>],
                    backgroundColor: ['#007bff', '#ff69b4'],
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                    },
                    title: {
                        display: true,
                        text: 'Gender Distribution',
                        padding: 10,
                    }
                }
            }
        });
    </script>
</body>
</html>
