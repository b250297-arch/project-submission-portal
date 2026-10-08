<?php
require_once 'config.php';

function get_students_data() {
    if (USE_MYSQL) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
        if ($conn->connect_error) {
            throw new Exception('Database connection failed: ' . $conn->connect_error);
        }

        $result = $conn->query("SELECT * FROM student_projects ORDER BY submitted_at DESC");
        $students = [];
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
        $conn->close();
        return $students;
    }

    $studentsFile = DATA_FILE;
    $students = [];
    if (file_exists($studentsFile)) {
        $raw = file_get_contents($studentsFile);
        $students = json_decode($raw, true) ?: [];
    }
    usort($students, function ($a, $b) {
        return strtotime($b['submitted_at'] ?? '0') - strtotime($a['submitted_at'] ?? '0');
    });
    return $students;
}

function save_student_data($student) {
    if (USE_MYSQL) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
        if ($conn->connect_error) {
            throw new Exception('Database connection failed: ' . $conn->connect_error);
        }

        $idValue = $student['id'] ?? '';
        $nameValue = $student['name'] ?? '';
        $studentIdValue = $student['student_id'] ?? '';
        $projectTitleValue = $student['project_title'] ?? '';
        $departmentValue = $student['department'] ?? '';
        $emailValue = $student['email'] ?? '';
        $descriptionValue = $student['description'] ?? '';
        $technologyValue = $student['technology'] ?? '';
        $statusValue = $student['status'] ?? 'Pending';
        $filePathValue = $student['file_path'] ?? '';
        $commentValue = $student['comment'] ?? '';
        $downloadCountValue = (string) ((int) ($student['download_count'] ?? 0));
        $submittedAtValue = $student['submitted_at'] ?? date('Y-m-d H:i:s');

        $stmt = $conn->prepare("INSERT INTO student_projects (id, name, student_id, project_title, department, email, description, technology, status, file_path, comment, download_count, submitted_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('sssssssssssss', $idValue, $nameValue, $studentIdValue, $projectTitleValue, $departmentValue, $emailValue, $descriptionValue, $technologyValue, $statusValue, $filePathValue, $commentValue, $downloadCountValue, $submittedAtValue);
        $stmt->execute();
        $stmt->close();
        $conn->close();
        return true;
    }

    $studentsFile = DATA_FILE;
    $students = [];
    if (file_exists($studentsFile)) {
        $raw = file_get_contents($studentsFile);
        $students = json_decode($raw, true) ?: [];
    }
    $student['comment'] = $student['comment'] ?? '';
    $student['download_count'] = $student['download_count'] ?? 0;
    $student['file_path'] = $student['file_path'] ?? '';
    $students[] = $student;
    file_put_contents($studentsFile, json_encode($students, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    return true;
}

function update_student_status($id, $status) {
    if (USE_MYSQL) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
        if ($conn->connect_error) {
            throw new Exception('Database connection failed: ' . $conn->connect_error);
        }

        $stmt = $conn->prepare("UPDATE student_projects SET status = ? WHERE id = ?");
        $stmt->bind_param('ss', $status, $id);
        $stmt->execute();
        $stmt->close();
        $conn->close();
        return true;
    }

    $studentsFile = DATA_FILE;
    $students = [];
    if (file_exists($studentsFile)) {
        $raw = file_get_contents($studentsFile);
        $students = json_decode($raw, true) ?: [];
    }

    foreach ($students as &$student) {
        if (($student['id'] ?? '') === $id) {
            $student['status'] = $status;
            break;
        }
    }

    file_put_contents($studentsFile, json_encode($students, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    return true;
}

function update_student_comment($id, $comment) {
    $studentsFile = DATA_FILE;
    $students = [];
    if (file_exists($studentsFile)) {
        $raw = file_get_contents($studentsFile);
        $students = json_decode($raw, true) ?: [];
    }

    foreach ($students as &$student) {
        if (($student['id'] ?? '') === $id) {
            $student['comment'] = $comment;
            break;
        }
    }

    file_put_contents($studentsFile, json_encode($students, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    return true;
}

function increment_download_count($id) {
    $studentsFile = DATA_FILE;
    $students = [];
    if (file_exists($studentsFile)) {
        $raw = file_get_contents($studentsFile);
        $students = json_decode($raw, true) ?: [];
    }

    foreach ($students as &$student) {
        if (($student['id'] ?? '') === $id) {
            $student['download_count'] = (int) ($student['download_count'] ?? 0) + 1;
            break;
        }
    }

    file_put_contents($studentsFile, json_encode($students, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    return true;
}
