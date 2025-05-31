<?php
session_start();
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $userType = $_POST['userType'];
    
    $table = "";
    switch($userType) {
        case 'doctor':
            $table = "Doctor";
            break;
        case 'patient':
            $table = "Patient";
            break;
        case 'donor':
            $table = "Donor";
            break;
        case 'admin':
            $table = "Admin";
            break;
    }
    
    $sql = "SELECT * FROM $table WHERE Username = ? AND Password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $_SESSION['userType'] = $userType;
        $_SESSION['userId'] = $user[$table.'_ID'];
        header("Location: dashboard_".$userType.".php");
    } else {
        $error = "Invalid credentials!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Blood Bank Management - Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Login</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label>User Type:</label>
                                <select name="userType" class="form-control" required>
                                    <option value="doctor">Doctor</option>
                                    <option value="patient">Patient</option>
                                    <option value="donor">Donor</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Username:</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Password:</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>
                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger mt-3"><?php echo $error; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>