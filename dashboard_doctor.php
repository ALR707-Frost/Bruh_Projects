<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['userType']) || $_SESSION['userType'] !== 'doctor') {
    header("Location: login.php");
    exit();
}

$doctorId = $_SESSION['userId'];
$sql = "SELECT * FROM Doctor WHERE Doctor_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctorId);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $specialty = $_POST['specialty'];
    $contact = $_POST['contact_info'];
    
    $updateSql = "UPDATE Doctor SET Name = ?, Specialty = ?, Contact_Info = ? WHERE Doctor_ID = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("sssi", $name, $specialty, $contact, $doctorId);
    $updateStmt->execute();
    
    header("Location: dashboard_doctor.php");
}

// Handle blood request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_request'])) {
    $bloodType = $_POST['blood_type'];
    $amount = $_POST['amount'];
    $reason = $_POST['reason'];
    $hospitalId = $doctor['Hospital_ID'];
    
    $requestSql = "INSERT INTO blood_requests (Doctor_ID, Blood_Type, Amount_ML, Reason, Hospital_ID) 
                   VALUES (?, ?, ?, ?, ?)";
    $requestStmt = $conn->prepare($requestSql);
    $requestStmt->bind_param("isisi", $doctorId, $bloodType, $amount, $reason, $hospitalId);
    $requestStmt->execute();
}

// Fetch existing requests
$requestsSql = "SELECT * FROM blood_requests WHERE Doctor_ID = ? ORDER BY Request_Date DESC";
$requestsStmt = $conn->prepare($requestsSql);
$requestsStmt->bind_param("i", $doctorId);
$requestsStmt->execute();
$requests = $requestsStmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Doctor Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Welcome, Dr. <?php echo htmlspecialchars($doctor['Name']); ?></h2>
        
        <div class="row mt-4">
            <!-- Profile Section -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3>Your Profile</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label>Name:</label>
                                <input type="text" name="name" class="form-control" 
                                       value="<?php echo htmlspecialchars($doctor['Name']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label>Specialty:</label>
                                <input type="text" name="specialty" class="form-control" 
                                       value="<?php echo htmlspecialchars($doctor['Specialty']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label>Contact Info:</label>
                                <input type="text" name="contact_info" class="form-control" 
                                       value="<?php echo htmlspecialchars($doctor['Contact_Info']); ?>" required>
                            </div>
                            <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Blood Request Section -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3>Request Blood</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label>Blood Type:</label>
                                <select name="blood_type" class="form-control" required>
                                    <option value="A+">A+</option>
                                    <option value="A-">A-</option>
                                    <option value="B+">B+</option>
                                    <option value="B-">B-</option>
                                    <option value="AB+">AB+</option>
                                    <option value="AB-">AB-</option>
                                    <option value="O+">O+</option>
                                    <option value="O-">O-</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Amount (in ML):</label>
                                <input type="number" name="amount" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Reason:</label>
                                <textarea name="reason" class="form-control" required></textarea>
                            </div>
                            <button type="submit" name="submit_request" class="btn btn-success">Submit Request</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Previous Requests Section -->
        <div class="card mt-4">
            <div class="card-header">
                <h3>Your Previous Requests</h3>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Request Date</th>
                            <th>Blood Type</th>
                            <th>Amount (ML)</th>
                            <th>Reason</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($request = $requests->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($request['Request_Date']); ?></td>
                            <td><?php echo htmlspecialchars($request['Blood_Type']); ?></td>
                            <td><?php echo htmlspecialchars($request['Amount_ML']); ?></td>
                            <td><?php echo htmlspecialchars($request['Reason']); ?></td>
                            <td><?php echo htmlspecialchars($request['Status']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <a href="logout.php" class="btn btn-danger mt-3">Logout</a>
    </div>
</body>
</html>