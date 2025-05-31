<?php
session_start();
require_once('config.php');

if (!isset($_SESSION['userType']) || $_SESSION['userType'] !== 'donor') {
    header("Location: login.php");
    exit();
}

$donorId = $_SESSION['userId'];

// Handle profile updates
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $blood_type = $_POST['blood_type'];
    $phone = $_POST['phone_number'];
    $address = $_POST['address'];
    $age = $_POST['age'];
    $medical_history = $_POST['medical_history'];
    
    $updateSql = "UPDATE Donor SET Name = ?, Blood_Type = ?, Phone_Number = ?, Address = ?, Age = ?, Medical_History = ? WHERE Donor_ID = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("ssssssi", $name, $blood_type, $phone, $address, $age, $medical_history, $donorId);
    $updateStmt->execute();
}

// Get donor information
$stmt = $conn->prepare("SELECT * FROM donor WHERE Donor_ID = ?");
$stmt->bind_param("i", $donorId);
$stmt->execute();
$donor = $stmt->get_result()->fetch_assoc();

// Get donation history
$stmt = $conn->prepare("
    SELECT d.*, h.Name as Hospital_Name 
    FROM donations d 
    JOIN hospital h ON d.Hospital_ID = h.Hospital_ID 
    WHERE d.Donor_ID = ? 
    ORDER BY d.Donation_Date DESC
");
$stmt->bind_param("i", $donorId);
$stmt->execute();
$donations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get pending notifications
$stmt = $conn->prepare("
    SELECT dn.*, br.Blood_Type, br.Amount_ML, h.Name as Hospital_Name
    FROM donor_notifications dn
    JOIN blood_requests br ON dn.Request_ID = br.Request_ID
    JOIN hospital h ON br.Hospital_ID = h.Hospital_ID
    WHERE dn.Donor_ID = ? AND dn.Status = 'Pending'
");
$stmt->bind_param("i", $donorId);
$stmt->execute();
$notifications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row">
            <div class="col-md-8">
                <h2>Welcome, <?php echo htmlspecialchars($donor['Name']); ?></h2>
            </div>
            <div class="col-md-4 text-end">
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>

        <!-- Profile Card -->
        <div class="card mt-4">
            <div class="card-header">
                <h3>Your Profile</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Name:</label>
                            <input type="text" name="name" class="form-control" 
                                   value="<?php echo htmlspecialchars($donor['Name']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Blood Type:</label>
                            <input type="text" name="blood_type" class="form-control" 
                                   value="<?php echo htmlspecialchars($donor['Blood_Type']); ?>" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Phone Number:</label>
                            <input type="text" name="phone_number" class="form-control" 
                                   value="<?php echo htmlspecialchars($donor['Phone_Number']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Age:</label>
                            <input type="number" name="age" class="form-control" 
                                   value="<?php echo htmlspecialchars($donor['Age']); ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Address:</label>
                        <input type="text" name="address" class="form-control" 
                               value="<?php echo htmlspecialchars($donor['Address']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Medical History:</label>
                        <textarea name="medical_history" class="form-control" rows="3" required><?php echo htmlspecialchars($donor['Medical_History']); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
        </div>

        <!-- Blood Request Notifications -->
        <?php if (!empty($notifications)): ?>
        <div class="card mt-4">
            <div class="card-header">
                <h3>Blood Request Notifications</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Hospital</th>
                                <th>Blood Type</th>
                                <th>Amount (ML)</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($notifications as $notification): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($notification['Hospital_Name']); ?></td>
                                <td><?php echo htmlspecialchars($notification['Blood_Type']); ?></td>
                                <td><?php echo htmlspecialchars($notification['Amount_ML']); ?></td>
                                <td><?php echo htmlspecialchars($notification['Notification_Date']); ?></td>
                                <td>
                                    <a href="respond_request.php?id=<?php echo $notification['Notification_ID']; ?>&action=accept" 
                                       class="btn btn-success btn-sm">Accept</a>
                                    <a href="respond_request.php?id=<?php echo $notification['Notification_ID']; ?>&action=decline" 
                                       class="btn btn-danger btn-sm">Decline</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Donation History -->
        <div class="card mt-4">
            <div class="card-header">
                <h3>Donation History</h3>
            </div>
            <div class="card-body">
                <?php if (empty($donations)): ?>
                    <p class="text-muted">No donation history available.</p>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Hospital</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($donations as $donation): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($donation['Donation_Date']); ?></td>
                                <td><?php echo htmlspecialchars($donation['Hospital_Name']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>