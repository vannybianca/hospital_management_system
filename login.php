<?php
session_start();
require_once "config/database.php";

$error = "";
$username = trim($_POST['username'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $statement = $conn->prepare("SELECT id, username, password, full_name, role FROM users WHERE username = ? LIMIT 1");
    $statement->bind_param("s", $username);
    $statement->execute();
    $user = $statement->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $login_statement = $conn->prepare("UPDATE users SET last_login_at = CURRENT_TIMESTAMP WHERE id = ?");
        $login_statement->bind_param("i", $user['id']);
        $login_statement->execute();
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['last_login_at'] = date('Y-m-d H:i:s');
        header("Location: admin/dashboard.php");
        exit;
    }

    $error = "The username or password is incorrect.";
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in | MEDICORE</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-page">
<main class="login-shell">
    <section class="login-intro">
        <h1 class="brand-name">MEDICORE</h1>
        <p class="system-label">Integrated Hospital Management System</p>
        <p class="tagline">Smarter Management. Better Care.</p>
        <p>Sign in to securely manage patients, appointments, records and daily hospital operations.</p>
    </section>
    <section class="login-panel" aria-labelledby="login-title">
        <div class="brand-mark" aria-hidden="true">M</div>
        <h2 id="login-title">Welcome back</h2>
        <p class="login-subtitle">Use your staff account to continue.</p>
        <?php if ($error): ?><p class="error login-error" role="alert"><?=htmlspecialchars($error)?></p><?php elseif (isset($_GET['logged_out'])): ?><p class="success login-error" role="status">You have been logged out successfully.</p><?php endif; ?>
        <form method="post" autocomplete="on">
            <label for="username">Username</label>
            <input id="username" name="username" value="<?=htmlspecialchars($username)?>" autocomplete="username" required autofocus>
            <label for="password">Password</label>
            <div class="password-field"><input id="password" name="password" type="password" autocomplete="current-password" required><button class="password-toggle" type="button" aria-controls="password" aria-label="Show password">Show</button></div>
            <button class="login-submit" type="submit">Sign in</button>
        </form>
        <a class="back-link" href="index.php">Back to home</a>
    </section>
</main>
<script>
const passwordInput = document.getElementById('password');
const passwordToggle = document.querySelector('.password-toggle');
passwordToggle.addEventListener('click', () => {
    const isHidden = passwordInput.type === 'password';
    passwordInput.type = isHidden ? 'text' : 'password';
    passwordToggle.textContent = isHidden ? 'Hide' : 'Show';
    passwordToggle.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
});
</script>
</body>
</html>
