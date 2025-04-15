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

        $stmt = $conn->prepare("SELECT * FROM posts WHERE thread_id = :thread_id ORDER BY created_at ASC");
        $stmt->bindParam(':thread_id', $thread_id);
        $stmt->execute();
        $posts = $stmt->fetchAll();
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
    <title><?php echo htmlspecialchars($thread["title"]); ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
      tinymce.init({
        selector: '#content'
      });

      $(document).ready(function() {
        $('.dropdown-toggle').dropdown();
      });
    </script>
    
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <h2><?php echo htmlspecialchars($thread["title"]); ?></h2>
            <?php if ($_SESSION["username"] == $thread["author"]): ?>
                <div>
                 <a href="edit_thread.php?id=<?php echo $thread["id"]; ?>" class="btn btn-sm btn-primary">Edit</a>
                    <a href="delete_thread.php?id=<?php echo $thread["id"]; ?>" class="btn btn-sm btn-danger">Delete</a>
                </div>
            <?php endif; ?>
        </div>
        <p>Created by <?php echo htmlspecialchars($thread["author"]); ?> on <?php echo $thread["created_at"]; ?></p>
        <p><?php echo nl2br($thread["content"]); ?></p>
        <hr>
        <h3>Posts</h3>
        <?php if ($posts): ?>
            <ul class="list-group">
                <?php foreach ($posts as $post): ?>
                    <li class="list-group-item">
                        <?php echo nl2br($post["content"]); ?>
                        - Posted by <?php echo htmlspecialchars($post["author"]); ?> on <?php echo $post["created_at"]; ?>
                        <?php if ($_SESSION["username"] == $post["author"]): ?>
                            <div class="float-right">
                                <a href="edit_post.php?id=<?php echo $post["id"]; ?>" class="btn btn-sm btn-primary">Edit</a>
                                <a href="delete_post.php?id=<?php echo $post["id"]; ?>" class="btn btn-sm btn-danger">Delete</a>
                            </div>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No posts yet.</p>
        <?php endif; ?>
        <form method="post" action="add_post.php">
            <input type="hidden" name="thread_id" value="<?php echo $thread_id; ?>">
            <div class="form-group">
                <label for="content">Add a post:</label>
                <textarea name="content" id="content" rows="3" class="form-control" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary" onclick="tinyMCE.triggerSave();">Post</button>
        </form>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
