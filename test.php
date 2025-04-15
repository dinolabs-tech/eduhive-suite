<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start the session to maintain user state
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
include 'db_connection.php';
// include 'createforumusers.php';

// Check connection to the database
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}




$parentId = $_SESSION['user_id'];
// Fetch student_id string from parent table
$stmt = $conn->prepare("SELECT student_id FROM parent WHERE id = ?");
$stmt->bind_param("i", $parentId);
$stmt->execute();
$result = $stmt->get_result();
$students = [];

if ($row = $result->fetch_assoc()) {
    $studentIdString = $row['student_id'];
    
    if (!empty($studentIdString)) {
        // Convert comma-separated IDs into array
        $studentIds = array_map('trim', explode(',', $studentIdString));

        // Sanitize and quote each ID for SQL
        $safeIds = array_map(function($id) use ($conn) {
            return "'" . $conn->real_escape_string($id) . "'";
        }, $studentIds);

        // Create IN clause
        $inClause = implode(",", $safeIds);

        // Run query to fetch student records
        $query = "SELECT id, name, mobile, email FROM students WHERE id IN ($inClause)";
        $studentResult = $conn->query($query);

        while ($student = $studentResult->fetch_assoc()) {
            $students[] = $student;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Children</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-4">
        <h2 class="mb-4">Students Linked to Your Account</h2>

        <?php if (empty($students)): ?>
            <div class="alert alert-warning">No students assigned to your account.</div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($students as $stu): ?>
                    <div class="col-md-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($stu['name']) ?></h5>
                                <p class="card-text"><strong>Student ID:</strong> <?= htmlspecialchars($stu['id']) ?></p>
                                <p class="card-text"><strong>Mobile:</strong> <?= htmlspecialchars($stu['mobile']) ?></p>
                                <p class="card-text"><strong>Email:</strong> <?= htmlspecialchars($stu['email']) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS CDN (Optional, for interactive components) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
