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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $content = $_POST["content"];

    try {
        $stmt = $conn->prepare("UPDATE posts SET content = :content WHERE id = :post_id");
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':post_id', $post_id);
        $stmt->execute();
        header("Location: view_thread.php?id=" . $post["thread_id"]);
        exit();
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post</title>
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
        <h2>Edit Post</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . $post_id; ?>">
            <div class="mb-3">
                <label for="content" class="form-label">Content:</label>
                <textarea name="content" id="content" rows="5" class="form-control" required><?php echo htmlspecialchars($post["content"]); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary" onclick="tinyMCE.triggerSave();">Update</button>
        </form>
    </div>
</body>
</html>
