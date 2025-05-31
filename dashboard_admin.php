<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['userType']) || $_SESSION['userType'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$adminId = $_SESSION['userId'];

// Fetch admin information
$sql = "SELECT * FROM admin WHERE Admin_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $adminId);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

// Fetch statistics
$stats = [
    'total_requests' => $conn->query("SELECT COUNT(*) FROM blood_requests")->fetch_row()[0],
    'pending_requests' => $conn->query("SELECT COUNT(*) FROM blood_requests WHERE Status = 'Pending'")->fetch_row()[0],
    'total_donors' => $conn->query("SELECT COUNT(*) FROM donor")->fetch_row()[0],
    'total_donations' => $conn->query("SELECT COUNT(*) FROM donations")->fetch_row()[0],
    'total_hospitals' => $conn->query("SELECT COUNT(*) FROM hospital")->fetch_row()[0]
];

// Fetch recent blood requests
$recentRequests = $conn->query("
    SELECT br.*, d.Name as DoctorName, h.Name as HospitalName 
    FROM blood_requests br
    JOIN doctor d ON br.Doctor_ID = d.Doctor_ID
    JOIN hospital h ON br.Hospital_ID = h.Hospital_ID
    ORDER BY Request_Date DESC LIMIT 5
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Welcome, <?php echo htmlspecialchars($admin['Name']); ?></h2>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
        
        <!-- Statistics Cards -->
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h5 class="card-title">Blood Requests</h5>
                        <p class="display-4"><?php echo $stats['total_requests']; ?></p>
                        <p>Pending: <?php echo $stats['pending_requests']; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h5 class="card-title">Donors</h5>
                        <p class="display-4"><?php echo $stats['total_donors']; ?></p>
                        <p>Total Donations: <?php echo $stats['total_donations']; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h5 class="card-title">Hospitals</h5>
                        <p class="display-4"><?php echo $stats['total_hospitals']; ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Quick Actions</h4>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <a href="admin_blood_requests.php" class="list-group-item list-group-item-action">
                                Manage Blood Requests
                            </a>
                            <a href="manage_donors.php" class="list-group-item list-group-item-action">
                                Manage Donors
                            </a>
                            <a href="manage_hospitals.php" class="list-group-item list-group-item-action">
                                Manage Hospitals
                            </a>
                            <a href="admin_profile.php" class="list-group-item list-group-item-action">
                                Update Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Blood Requests -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Recent Blood Requests</h4>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Doctor</th>
                                    <th>Hospital</th>
                                    <th>Blood Type</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($request = $recentRequests->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo date('Y-m-d', strtotime($request['Request_Date'])); ?></td>
                                    <td><?php echo htmlspecialchars($request['DoctorName']); ?></td>
                                    <td><?php echo htmlspecialchars($request['HospitalName']); ?></td>
                                    <td><?php echo htmlspecialchars($request['Blood_Type']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $request['Status'] == 'Pending' ? 'warning' : 'success'; ?>">
                                            <?php echo htmlspecialchars($request['Status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="view_request.php?id=<?php echo $request['Request_ID']; ?>" 
                                           class="btn btn-sm btn-primary">View</a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>