<?php
require_once 'db_connect.php';
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET["id"])) {
    $thread_id = $_GET["id"];

    try {
        $stmt = $conn->prepare("SELECT * FROM threads WHERE id = :thread_id");
        $stmt->bindParam(':thread_id', $thread_id);
        $stmt->execute();
        $thread = $stmt->fetch();

        if (!$thread || $_SESSION["username"] != $thread["author"]) {
            header("Location: index.php");
            exit();
        }
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    header("Location: index.php");
    exit();
}

try {
    $stmt = $conn->prepare("DELETE FROM threads WHERE id = :thread_id");
    $stmt->bindParam(':thread_id', $thread_id);
    $stmt->execute();
    header("Location: index.php");
    exit();
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Thread</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Delete Thread</h2>
        <p>Are you sure you want to delete this thread?</p>
        <a href="delete_thread.php?id=<?php echo $_GET['id']; ?>" class="btn btn-danger">Yes, Delete</a>
        <a href="index.php" class="btn btn-secondary">No, Cancel</a>
    </div>
</body>
</html>
