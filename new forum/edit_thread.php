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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST["title"];
    $content = $_POST["content"];

    try {
        $stmt = $conn->prepare("UPDATE threads SET title = :title, content = :content WHERE id = :thread_id");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':thread_id', $thread_id);
        $stmt->execute();
        header("Location: view_thread.php?id=" . $thread_id);
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
    <title>Edit Thread</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js"></script>
    
    <script>
      tinymce.init({
        selector: '#content'
      });
    </script>
</head>
<body>
    <div class="container">
        <h2>Edit Thread</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . $thread_id; ?>">
            <div class="form-group">
                <label>Title:</label>
                <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($thread["title"]); ?>" required>
            </div>
            <div class="form-group">
                <label>Content:</label>
                <textarea name="content" rows="5" class="form-control" required><?php echo htmlspecialchars($thread["content"]); ?></textarea>
            </div>
            <input type="submit" value="Update" class="btn btn-primary">
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
