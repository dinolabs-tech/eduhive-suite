<?php include('components/parent_logic.php');


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
    $safeIds = array_map(function ($id) use ($conn) {
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
// expose count for the template
$totalStudents = count($students);
?>

<!DOCTYPE html>
<html lang="en">
<?php include('head.php'); ?>

<body>
  <div class="wrapper">
    <!-- Sidebar -->
    <?php

    include('parentnav.php');

    ?>
    <!-- End Sidebar -->

    <div class="main-panel">
      <div class="main-header">
        <div class="main-header-logo">
          <!-- Logo Header -->
          <?php include('logo_header.php'); ?>
          <!-- End Logo Header -->
        </div>
        <!-- Navbar Header -->
        <?php include('navbar.php'); ?>
        <!-- End Navbar -->
      </div>

      <div class="container" id="content-container">
        <div class="page-inner">
          <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            <div>
              <h3 class="fw-bold mb-3">Dashboard</h3>
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="students.php">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
              </ol>
            </div>

          </div>

          <!-- PERSONAL AI ============================ -->
          <div class="row">

            <div class="col-md-12">
              <div class="card card-primary card-round">
                <div class="card-header">
                  <div class="card-head-row">
                    <div class="card-title">Personal AI</div>
                  </div>
                </div>
                <div class="card-body pb-0">
                  <div class="mb-4 mt-2">

                    <p id="message" data-message="<?php echo htmlspecialchars($message); ?>"></p>

                  </div>

                </div>
              </div>

            </div>
          </div>

          <div class="row">

            <div class="col-md-12">
              <div class="card card-round">
                <div class="card-header">
                  <div class="card-head-row">
                    <div class="card-title">My Ward(s) </div>
                    <div class="card-tools">
                      Total Ward(s) <strong><?= $totalStudents ?></strong>
                    </div>

                  </div>
                </div>
                <div class="card-body">
                  <?php if (empty($students)): ?>
                    <div class="alert alert-warning">No students assigned to your account.</div>
                  <?php else: ?>
                    <div class="row g-4">
                      <?php foreach ($students as $stu): ?>
                        <div class="col-md-4">
                          <div class="card shadow-sm h-100 student-card"
                            data-student-id="<?= htmlspecialchars($stu['id']) ?>" style="cursor: pointer;">
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

              </div>
            </div>


          </div>

        </div>
      </div>

      <?php include('footer.php'); ?>
    </div>

    <!-- Custom template | don't include it in your project! -->
    <?php include('cust-color.php'); ?>
    <!-- End Custom template -->
  </div>
  <?php include('scripts.php'); ?>
  <script>
    $(document).ready(function () {
      $('.student-card').click(function () {
        const studentId = $(this).data('student-id');

        $.ajax({
          url: 'components/student_container.php',
          method: 'GET',
          data: { id: studentId },
          success: function (response) {
            $('#content-container').html(response);
          },
          error: function () {
            alert('Something went wrong while loading student details.');
          }
        });
      });
    });
  </script>

</body>

</html>