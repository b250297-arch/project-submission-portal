<?php
require_once 'config.php';
require_once 'database.php';

$requiredFields = ['name', 'student_id', 'project_title', 'department', 'email', 'description', 'technology'];
foreach ($requiredFields as $field) {
    if (empty($_POST[$field])) {
        header('Location: index.php?status=error');
        exit;
    }
}

$uploadPath = '';
if (isset($_FILES['project_file']) && $_FILES['project_file']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['project_file']['error'] !== UPLOAD_ERR_OK) {
        header('Location: index.php?status=upload_error');
        exit;
    }

    if ($_FILES['project_file']['size'] > MAX_UPLOAD_SIZE) {
        header('Location: index.php?status=upload_error');
        exit;
    }

    $extension = strtolower(pathinfo($_FILES['project_file']['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_EXTENSIONS, true)) {
        header('Location: index.php?status=upload_error');
        exit;
    }

    $uploadDir = __DIR__ . '/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = uniqid('project_') . '_' . basename($_FILES['project_file']['name']);
    $targetFile = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['project_file']['tmp_name'], $targetFile)) {
        $uploadPath = 'uploads/' . $fileName;
    }
}

$student = [
    'id' => uniqid('student_'),
    'name' => trim($_POST['name']),
    'student_id' => trim($_POST['student_id']),
    'project_title' => trim($_POST['project_title']),
    'department' => trim($_POST['department']),
    'email' => trim($_POST['email']),
    'description' => trim($_POST['description']),
    'technology' => trim($_POST['technology']),
    'status' => 'Pending',
    'file_path' => $uploadPath,
    'comment' => '',
    'download_count' => 0,
    'submitted_at' => date('Y-m-d H:i:s')
];

save_student_data($student);

header('Location: index.php?status=success');
exit;
