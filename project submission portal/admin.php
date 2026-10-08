<?php
require_once 'config.php';
require_once 'database.php';

if (!($_SESSION['admin_logged_in'] ?? false)) {
    header('Location: admin_login.php');
    exit;
}

$students = get_students_data();
$search = trim($_GET['search'] ?? '');
$filter = $_GET['status'] ?? 'all';

if ($search !== '') {
    $students = array_values(array_filter($students, function ($student) use ($search) {
        return stripos($student['name'] ?? '', $search) !== false || stripos($student['project_title'] ?? '', $search) !== false || stripos($student['student_id'] ?? '', $search) !== false;
    }));
}

if ($filter !== 'all') {
    $students = array_values(array_filter($students, function ($student) use ($filter) {
        return ($student['status'] ?? 'Pending') === $filter;
    }));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_id'])) {
    $studentId = $_POST['student_id'];
    if (isset($_POST['status'])) {
        update_student_status($studentId, $_POST['status']);
    }
    if (isset($_POST['comment'])) {
        update_student_comment($studentId, trim($_POST['comment']));
    }
    header('Location: admin.php?search=' . urlencode($search) . '&status=' . urlencode($filter));
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Project Submissions</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <header class="hero compact">
        <div class="container">
            <h1>Admin Dashboard</h1>
            <p>Review submissions, update status, and search projects.</p>
            <div class="hero-actions">
                <a class="btn" href="index.php">Back to Home</a>
                <a class="btn secondary" href="logout.php">Logout</a>
            </div>
        </div>
    </header>

    <main class="container">
        <section class="card">
            <h2>Student Project List</h2>
            <form method="get" class="filter-form">
                <input type="text" name="search" placeholder="Search by name, roll no., or project" value="<?php echo htmlspecialchars($search); ?>">
                <select name="status">
                    <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>All Status</option>
                    <option value="Pending" <?php echo $filter === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="Approved" <?php echo $filter === 'Approved' ? 'selected' : ''; ?>>Approved</option>
                    <option value="Rejected" <?php echo $filter === 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
                </select>
                <button type="submit" class="btn">Filter</button>
            </form>
            <?php if (empty($students)): ?>
                <p>No submissions found.</p>
            <?php else: ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Roll No.</th>
                                <th>Project</th>
                                <th>Department</th>
                                <th>Technology</th>
                                <th>Status</th>
                                <th>Submitted</th>
                                <th>File</th>
                                <th>Downloads</th>
                                <th>Review</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($student['name'] ?? 'Unknown'); ?></td>
                                    <td><?php echo htmlspecialchars($student['student_id'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($student['project_title'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($student['department'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($student['technology'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($student['status'] ?? 'Pending'); ?></td>
                                    <td><?php echo htmlspecialchars($student['submitted_at'] ?? '-'); ?></td>
                                    <td>
                                        <?php if (!empty($student['file_path'])): ?>
                                            <a href="download.php?id=<?php echo urlencode($student['id'] ?? ''); ?>&file=<?php echo urlencode($student['file_path']); ?>" target="_blank">Download</a>
                                        <?php else: ?>
                                            No file
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo (int) ($student['download_count'] ?? 0); ?></td>
                                    <td>
                                        <form method="post" class="review-form">
                                            <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student['id'] ?? ''); ?>">
                                            <label class="sr-only">Comment</label>
                                            <textarea name="comment" rows="2" class="comment-box"><?php echo htmlspecialchars($student['comment'] ?? ''); ?></textarea>
                                            <select name="status">
                                                <option value="Pending" <?php echo (($student['status'] ?? 'Pending') === 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                                <option value="Approved" <?php echo (($student['status'] ?? 'Pending') === 'Approved') ? 'selected' : ''; ?>>Approved</option>
                                                <option value="Rejected" <?php echo (($student['status'] ?? 'Pending') === 'Rejected') ? 'selected' : ''; ?>>Rejected</option>
                                            </select>
                                            <button type="submit" class="btn small">Save</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
