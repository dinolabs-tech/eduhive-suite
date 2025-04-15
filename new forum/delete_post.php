<?php
require_once 'db_connect.php';
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET["id"])) {
    $post_id = $_GET["id"];

    try {
        $stmt = $conn->prepare("SELECT * FROM posts WHERE id = :post_id");
        $stmt->bindParam(':post_id', $post_id);
        $stmt->execute();
        $post = $stmt->fetch();

        if (!$post || $_SESSION["username"] != $post["author"]) {
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
    $stmt = $conn->prepare("DELETE FROM posts WHERE id = :post_id");
    $stmt->bindParam(':post_id', $post_id);
    $stmt->execute();
    header("Location: view_thread.php?id=" . $post["thread_id"]);
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
    <title>Delete Post</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Delete Post</h2>
        <p>Are you sure you want to delete this post?</p>
        <a href="delete_post.php?id=<?php echo $_GET['id']; ?>" class="btn btn-danger">Yes, Delete</a>
        <a href="view_thread.php?id=<?php echo isset($post['thread_id']) ? $post['thread_id'] : ''; ?>" class="btn btn-secondary">No, Cancel</a>
    </div>
</body>
</html>
