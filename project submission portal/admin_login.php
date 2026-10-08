<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: admin.php');
        exit;
    }

    $error = 'Invalid admin credentials.';
} else {
    $error = '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <header class="hero compact">
        <div class="container">
            <h1>Admin Login</h1>
            <p>Access the project review dashboard.</p>
        </div>
    </header>
    <main class="container">
        <section class="card auth-card">
            <?php if ($error): ?>
                <div class="message error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form action="admin_login.php" method="post" class="submission-form">
                <label>Username
                    <input type="text" name="username" required>
                </label>
                <label>Password
                    <input type="password" name="password" required>
                </label>
                <button type="submit" class="btn">Login</button>
            </form>
        </section>
    </main>
</body>
</html>
