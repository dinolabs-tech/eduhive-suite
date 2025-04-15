<?php
require_once 'db_connect.php';
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $thread_id = $_POST["thread_id"];
    $content = $_POST["content"];
    $author = $_SESSION["username"];

    try {
        $stmt = $conn->prepare("INSERT INTO posts (thread_id, content, author, created_at) VALUES (:thread_id, :content, :author, NOW())");
        $stmt->bindParam(':thread_id', $thread_id);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':author', $author);
        $stmt->execute();
        header("Location: view_thread.php?id=" . $thread_id);
        exit();
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Post</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js"></script>
    <script>
    <script>
      tinymce.init({
        selector: '#content'
      });
    </script>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container">
        <h2>Add New Post</h2>
        <form action="add_post.php" method="post">
            <input type="hidden" name="thread_id" value="<?php echo $_GET['thread_id']; ?>">
            <div class="mb-3">
                <label for="content" class="form-label">Content:</label>
                <textarea class="form-control" id="content" name="content" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary" onclick="tinyMCE.triggerSave();">Submit</button>
        </form>
    </div>
</body>
</html>
