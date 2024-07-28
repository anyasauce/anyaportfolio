<?php
include "conn.php";
date_default_timezone_set('Asia/Manila');

if(isset($_POST["rfidNumber"]) && isset($_POST["timeInOut"])) {
    $rfidNumber = $_POST["rfidNumber"];
    $timeInOut = $_POST["timeInOut"];
    
    if(empty($rfidNumber)) {
        ?>
        <script>
            alert("Error: RFID number cannot be empty.");
            window.location.href = "index.php";
        </script>
        <?php
        exit;
    }
    
    if(isPastMidnight()) {
        $checkRFIDQuery = "SELECT * FROM rfid WHERE rfid = ?";
        $stmtCheckRFID = $conn->prepare($checkRFIDQuery);
        $stmtCheckRFID->bind_param("s", $rfidNumber);
        $stmtCheckRFID->execute();
        $resultRFID = $stmtCheckRFID->get_result();
        
        if($resultRFID->num_rows === 0) {
            ?>
            <script>
                alert("Error: This RFID number is not registered.");
                window.location.href = "index.php";
            </script>
            <?php
            exit;
        }
        
        $checkRecordQuery = "SELECT * FROM time_records WHERE rfid = ? AND DATE(timein) = CURDATE()";
        $stmtCheckRecord = $conn->prepare($checkRecordQuery);
        $stmtCheckRecord->bind_param("s", $rfidNumber);
        $stmtCheckRecord->execute();
        $result = $stmtCheckRecord->get_result();
        
        if($result->num_rows > 0) {
            if($timeInOut == "timeIn") {
                ?>
                <script>
                    alert("Error: You have already timed in for the day.");
                    window.location.href = "index.php";
                </script>
                <?php
            } else {
                $checkTimeoutQuery = "SELECT * FROM time_records WHERE rfid = ? AND DATE(timeout) = CURDATE()";
                $stmtCheckTimeout = $conn->prepare($checkTimeoutQuery);
                $stmtCheckTimeout->bind_param("s", $rfidNumber);
                $stmtCheckTimeout->execute();
                $resultTimeout = $stmtCheckTimeout->get_result();

                if($resultTimeout->num_rows > 0) {
                    ?>
                    <script>
                        alert("Error: You have already timed out for the day.");
                        window.location.href = "index.php";
                    </script>
                    <?php
                } else {
                    $updateTimeoutQuery = "UPDATE time_records SET timeout = NOW() WHERE rfid = ? AND DATE(timein) = CURDATE()";
                    $stmtUpdateTimeout = $conn->prepare($updateTimeoutQuery);
                    $stmtUpdateTimeout->bind_param("s", $rfidNumber);

                    if($stmtUpdateTimeout->execute()) {
                        ?>
                        <script>
                            alert("Time-out Successfully!");
                            window.location.href = "index.php";
                        </script>
                        <?php
                    } else {
                        ?>
                        <script>
                            alert("Error: Unable to update time-out record.");
                            window.location.href = "index.php";
                        </script>
                        <?php
                    }
                }
            }
        } else {
            if($timeInOut == "timeOut") {
                ?>
                <script>
                    alert("Error: You have not timed in for the day.");
                    window.location.href = "index.php";
                </script>
                <?php
            } else {
                $insertQuery = "INSERT INTO time_records (rfid, timein) VALUES (?, NOW())";
                $stmtInsert = $conn->prepare($insertQuery);
                $stmtInsert->bind_param("s", $rfidNumber);

                if($stmtInsert->execute()) {
                    ?>
                    <script>
                        alert("Time-in Successfully!");
                        window.location.href = "index.php";
                    </script>
                    <?php
                } else {
                    ?>
                    <script>
                        alert("Error: Unable to insert time-in record.");
                        window.location.href = "index.php";
                    </script>
                    <?php
                }
            }
        }
        
        $stmtCheckRecord->close();
        if(isset($stmtCheckTimeout)) {
            $stmtCheckTimeout->close();
        }
        if(isset($stmtUpdateTimeout)) {
            $stmtUpdateTimeout->close();
        }
        if(isset($stmtInsert)) {
            $stmtInsert->close();
        }
        
        $stmtCheckRFID->close();
        
    } else {
        ?>
        <script>
            alert("Error: You can only time in/out after 12 AM (Philippine time).");
            window.location.href = "index.php";
        </script>
        <?php
    }
} else {
    ?>
    <script>
        alert("Error: RFID number or timeInOut not set.");
        window.location.href = "index.php";
    </script>
    <?php
}


function isPastMidnight() {
    $now = strtotime(date('Y-m-d H:i:s'));
    $midnight = strtotime(date('Y-m-d 00:00:00'));
    return $now > $midnight;
}

$conn->close();

?>
