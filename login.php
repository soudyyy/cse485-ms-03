<?php
declare(strict_types=1);

session_start();

// da login roi thi ve dashboard
if (!empty($_SESSION['auth'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === 'admin' && $password === 'MiniShop@03') {
        $_SESSION['auth'] = true;
        $_SESSION['username'] = $username;
        header('Location: dashboard.php');
        exit;
    }

    $error = 'Sai thong tin dang nhap';
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Dang nhap - MiniShop</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 50px; }
        .error { color: red; }
        form { border: 1px solid #ccc; padding: 20px; width: 300px; }
        input { margin: 5px 0; padding: 5px; width: 100%; box-sizing: border-box; }
    </style>
</head>
<body>
    <h2>Dang nhap MiniShop</h2>
    <?php if ($error !== ''): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="post" action="login.php">
        <p>
            <label>Username</label><br>
            <input type="text" name="username" required>
        </p>
        <p>
            <label>Password</label><br>
            <input type="password" name="password" required>
        </p>
        <p>
            <button type="submit">Dang nhap</button>
        </p>
    </form>
</body>
</html>