<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
include 'db_connection.php';

// Check connection to the database
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get class and arm from POST or GET
$class = isset($_POST['d_class']) ? $_POST['d_class'] : (isset($_GET['d_class']) ? $_GET['d_    class'] : '');
$arm = isset($_POST['d_arm']) ? $_POST['d_arm'] : (isset($_GET['d_arm']) ? $_GET['d_arm'] : '');

if (!$class || !$arm) {
    die("Class and Arm are required to download the template.");
}

// Create a safe and readable filename
$filename = ucwords(strtolower($class . ' ' . $arm)) . ' Class Teacher Comment Template.csv';
$filename = str_replace(['/', '\\'], '-', $filename); // Remove unsafe characters

// Set headers for download
header('Content-Type: text/csv; charset=utf-8');
header("Content-Disposition: attachment; filename=\"$filename\"");

// Open output stream
$output = fopen('php://output', 'w');

// Write the CSV header
$header = ['ID', 'NAME', 'COMMENT','SCHL_OPEN', 'DAYS_PRESENT', 'DAYS_ABSENT'];
fputcsv($output, $header);

// Fetch students based on selected class and arm
$sql = "SELECT id, name FROM students WHERE class = ? AND arm = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $class, $arm);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $line = [$row['id'], $row['name'], '', '', ''];
    fputcsv($output, $line);
}

fclose($output);
exit();
?>
