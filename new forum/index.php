<?php
require_once 'db_connect.php';
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$threads_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $threads_per_page;

$search = isset($_GET['search']) ? $_GET['search'] : '';

try {
    $sql = "SELECT * FROM threads WHERE title LIKE :search OR author LIKE :search ORDER BY created_at DESC LIMIT :start, :threads_per_page";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
    $stmt->bindParam(':start', $start, PDO::PARAM_INT);
    $stmt->bindParam(':threads_per_page', $threads_per_page, PDO::PARAM_INT);
    $stmt->execute();
    $threads = $stmt->fetchAll();

    $sql = "SELECT COUNT(*) FROM threads WHERE title LIKE :search OR author LIKE :search";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
    $stmt->execute();
    $total_threads = $stmt->fetchColumn();
    $total_pages = ceil($total_threads / $threads_per_page);
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container">
        <h2>Forum Threads</h2>
        <form method="GET" action="index.php" class="mb-3">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Search threads" name="search">
                <button class="btn btn-outline-secondary" type="submit">Search</button>
            </div>
        </form>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="create_thread.php" class="btn btn-primary">Create New Thread</a>
        </div>
        <?php if ($threads): ?>
            <div class="row">
                <?php foreach ($threads as $thread): ?>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <a href="view_thread.php?id=<?php echo $thread["id"]; ?>">
                                        <?php echo htmlspecialchars($thread["title"]); ?>
                                    </a>
                                </h5>
                                <p class="card-text">
                                    Created by <?php echo htmlspecialchars($thread["author"]); ?> on <?php echo date('F j, Y, g:i a', strtotime($thread["created_at"])); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No threads yet.</p>
        <?php endif; ?>

        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php if($total_pages > 1): ?>
                    <?php if($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="index.php?page=<?php echo $page - 1; ?>">Previous</a>
                        </li>
                    <?php endif; ?>

                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php if($i == $page) echo 'active'; ?>">
                            <a class="page-link" href="index.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="index.php?page=<?php echo $page + 1; ?>">Next</a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
