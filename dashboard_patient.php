<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['userType']) || $_SESSION['userType'] !== 'patient') {
    header("Location: login.php");
    exit();
}

$patientId = $_SESSION['userId'];

// Handle profile updates and blood requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update_profile'])) {
        $name = $_POST['name'];
        $location = $_POST['location'];
        $contact = $_POST['contact_info'];
        $blood_type = $_POST['blood_type'];
        $current_disease = $_POST['current_disease'];
        
        $updateSql = "UPDATE Patient SET Name = ?, Location = ?, Contact_Info = ?, Blood_Type = ?, Current_Disease = ? WHERE Patient_ID = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("sssssi", $name, $location, $contact, $blood_type, $current_disease, $patientId);
        $updateStmt->execute();
    }
    
    if (isset($_POST['submit_request'])) {
        $blood_type = $_POST['request_blood_type'];
        $amount = $_POST['amount'];
        $reason = $_POST['reason'];
        $hospital_id = $patient['Hospital_ID'];
        
        $requestSql = "INSERT INTO blood_requests (Blood_Type, Amount_ML, Reason, Hospital_ID) VALUES (?, ?, ?, ?)";
        $requestStmt = $conn->prepare($requestSql);
        $requestStmt->bind_param("sisi", $blood_type, $amount, $reason, $hospital_id);
        $requestStmt->execute();
    }
}

// Get patient information
$sql = "SELECT * FROM Patient WHERE Patient_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patientId);
$stmt->execute();
$patient = $stmt->get_result()->fetch_assoc();

// Get patient's blood requests
$requestsSql = "SELECT br.*, h.Name as Hospital_Name 
                FROM blood_requests br 
                JOIN hospital h ON br.Hospital_ID = h.Hospital_ID 
                WHERE h.Hospital_ID = ? 
                ORDER BY br.Request_Date DESC";
$requestsStmt = $conn->prepare($requestsSql);
$requestsStmt->bind_param("i", $patient['Hospital_ID']);
$requestsStmt->execute();
$requests = $requestsStmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Patient Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body>
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-md-8">
                <h2>Welcome, <?php echo htmlspecialchars($patient['Name']); ?></h2>
            </div>
            <div class="col-md-4 text-end">
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>

        <!-- Profile Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h3>Your Profile</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Name:</label>
                            <input type="text" name="name" class="form-control" 
                                   value="<?php echo htmlspecialchars($patient['Name']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Location:</label>
                            <input type="text" name="location" class="form-control" 
                                   value="<?php echo htmlspecialchars($patient['Location']); ?>" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Contact Info:</label>
                            <input type="text" name="contact_info" class="form-control" 
                                   value="<?php echo htmlspecialchars($patient['Contact_Info']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Blood Type:</label>
                            <input type="text" name="blood_type" class="form-control" 
                                   value="<?php echo htmlspecialchars($patient['Blood_Type']); ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Current Disease:</label>
                        <input type="text" name="current_disease" class="form-control" 
                               value="<?php echo htmlspecialchars($patient['Current_Disease']); ?>" required>
                    </div>
                    <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
        </div>

        <!-- Blood Request Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h3>Request Blood</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Blood Type Needed:</label>
                            <select name="request_blood_type" class="form-control" required>
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
                        <div class="col-md-6 mb-3">
                            <label>Amount (ML):</label>
                            <input type="number" name="amount" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Reason:</label>
                        <textarea name="reason" class="form-control" required></textarea>
                    </div>
                    <button type="submit" name="submit_request" class="btn btn-success">Submit Request</button>
                </form>
            </div>
        </div>

        <!-- Blood Requests History -->
        <div class="card">
            <div class="card-header">
                <h3>Blood Request History</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Blood Type</th>
                                <th>Amount (ML)</th>
                                <th>Reason</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($requests as $request): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($request['Request_Date']); ?></td>
                                <td><?php echo htmlspecialchars($request['Blood_Type']); ?></td>
                                <td><?php echo htmlspecialchars($request['Amount_ML']); ?></td>
                                <td><?php echo htmlspecialchars($request['Reason']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $request['Status'] == 'Pending' ? 'warning' : 
                                        ($request['Status'] == 'Approved' ? 'success' : 'danger'); ?>">
                                        <?php echo htmlspecialchars($request['Status']); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>