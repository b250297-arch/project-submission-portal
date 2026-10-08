<?php
require_once 'config.php';
require_once 'database.php';

$students = get_students_data();
$students = array_values($students);
$message = '';
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'success') {
        $message = 'Student project submitted successfully.';
    } elseif ($_GET['status'] === 'upload_error') {
        $message = 'Upload failed. Please use a supported file type and keep the file under 10MB.';
    } else {
        $message = 'Please complete all required fields.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AJ Project Submission Portal</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <header class="hero">
        <div class="container">
            <h1>AJ Project Submission Portal</h1>
            <p>Submit student project information and manage approvals from one place.</p>
            <div class="hero-actions">
                <a class="btn" href="admin_login.php">Admin Login</a>
                <a class="btn secondary" href="admin.php">Open Admin View</a>
            </div>
        </div>
    </header>

    <main class="container grid">
        <section class="card">
            <h2>Submit New Project</h2>
            <?php if ($message): ?>
                <div class="message <?php echo ($_GET['status'] ?? '') === 'upload_error' ? 'error' : 'success'; ?>"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            <form action="process.php" method="post" enctype="multipart/form-data" class="submission-form">
                <label>Student Name
                    <input type="text" name="name" required>
                </label>
                <label>Student ID / Roll Number
                    <input type="text" name="student_id" required>
                </label>
                <label>Project Title
                    <input type="text" name="project_title" required>
                </label>
                <label>Department
                    <input type="text" name="department" required>
                </label>
                <label>Email
                    <input type="email" name="email" required>
                </label>
                <label>Project Description
                    <textarea name="description" rows="4" required></textarea>
                </label>
                <label>Technology Used
                    <input type="text" name="technology" placeholder="HTML, PHP, MySQL..." required>
                </label>
                <label>Upload Project File
                    <input type="file" name="project_file">
                </label>
                <button type="submit" class="btn">Save Project</button>
            </form>
        </section>

        <section class="card">
            <h2>Recent Submissions</h2>
            <?php if (empty($students)): ?>
                <p>No project submissions yet.</p>
            <?php else: ?>
                <ul class="submission-list">
                    <?php foreach (array_slice($students, 0, 5) as $student): ?>
                        <li>
                            <strong><?php echo htmlspecialchars($student['project_title'] ?? 'Untitled'); ?></strong>
                            <span>by <?php echo htmlspecialchars($student['name'] ?? 'Unknown'); ?></span>
                            <small><?php echo htmlspecialchars($student['status'] ?? 'Pending'); ?></small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
    </main>

    <script src="assets/js/app.js"></script>
</body>
</html>
