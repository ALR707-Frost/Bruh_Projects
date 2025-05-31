<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['userType']) || $_SESSION['userType'] !== 'employee') {
    header("Location: login.php");
    exit();
}

$employeeId = $_SESSION['userId'];
$sql = "SELECT e.*, o.Name as OfficeName FROM Employee e 
        LEFT JOIN Office o ON e.Office_ID = o.Office_ID 
        WHERE e.Employee_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employeeId);
$stmt->execute();
$result = $stmt->get_result();
$employee = $result->fetch_assoc();

// Fetch all offices for the dropdown
$officesSql = "SELECT * FROM Office";
$officesResult = $conn->query($officesSql);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $role = $_POST['role'];
    $phone = $_POST['phone_number'];
    $email = $_POST['email'];
    $office_id = $_POST['office_id'];
    
    $updateSql = "UPDATE Employee SET Name = ?, Role = ?, Phone_Number = ?, Email = ?, Office_ID = ? WHERE Employee_ID = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("ssssii", $name, $role, $phone, $email, $office_id, $employeeId);
    $updateStmt->execute();
    
    header("Location: dashboard_employee.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Employee Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Welcome, <?php echo htmlspecialchars($employee['Name']); ?> (Admin)</h2>
        
        <div class="card mt-4">
            <div class="card-header">
                <h3>Your Profile</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label>Name:</label>
                        <input type="text" name="name" class="form-control" 
                               value="<?php echo htmlspecialchars($employee['Name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Role:</label>
                        <input type="text" name="role" class="form-control" 
                               value="<?php echo htmlspecialchars($employee['Role']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Phone Number:</label>
                        <input type="text" name="phone_number" class="form-control" 
                               value="<?php echo htmlspecialchars($employee['Phone_Number']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Email:</label>
                        <input type="email" name="email" class="form-control" 
                               value="<?php echo htmlspecialchars($employee['Email']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Office:</label>
                        <select name="office_id" class="form-control" required>
                            <?php while($office = $officesResult->fetch_assoc()): ?>
                                <option value="<?php echo $office['Office_ID']; ?>" 
                                    <?php echo ($office['Office_ID'] == $employee['Office_ID']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($office['Name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h3>Admin Functions</h3>
            </div>
            <div class="card-body">
                <a href="manage_users.php" class="btn btn-info me-2">Manage Users</a>
                <a href="view_donations.php" class="btn btn-success me-2">View Donations</a>
                <a href="view_requests.php" class="btn btn-warning me-2">View Requests</a>
                <a href="manage_inventory.php" class="btn btn-primary">Manage Inventory</a>
            </div>
        </div>
        
        <a href="logout.php" class="btn btn-danger mt-3">Logout</a>
    </div>
</body>
</html>