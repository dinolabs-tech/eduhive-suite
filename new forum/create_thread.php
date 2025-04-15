<?php
require_once 'db_connect.php';
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST["title"];
    $content = $_POST["content"];
    $author = $_SESSION["username"];

    try {
        $stmt = $conn->prepare("INSERT INTO threads (title, content, author, created_at) VALUES (:title, :content, :author, NOW())");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':author', $author);
        $stmt->execute();
        header("Location: index.php");
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
    <title>Create Thread</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js"></script>
    <script>
      tinymce.init({
        selector: '#content'
      });
    </script>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container">
        <h2>Create New Thread</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="mb-3">
                <label for="title" class="form-label">Title:</label>
                <input type="text" name="title" id="title" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="content" class="form-label">Content:</label>
                <textarea name="content" id="content" rows="5" class="form-control" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary" onclick="tinyMCE.triggerSave();">Create</button>
        </form>
    </div>
</body>
</html>
