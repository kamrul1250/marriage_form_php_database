<?php
require_once __DIR__ . '/init.php';
if (current_user()) {
    header('Location: dashboard.php');
    exit();
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf'] ?? '';
    if (!csrf_check($token)) {
        $errors[] = 'Invalid request (CSRF).';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        if ($username === '' || $password === '') {
            $errors[] = 'Enter both username and password.';
        } else {
            $stmt = $pdo->prepare("SELECT id, password_hash FROM users WHERE username = ? LIMIT 1");
            $stmt->execute([$username]);
            $u = $stmt->fetch();
            if ($u && password_verify($password, $u['password_hash'])) {
                // login
                session_regenerate_id(true);
                $_SESSION['user_id'] = $u['id'];
                header('Location: dashboard.php');
                exit();
            } else {
                $errors[] = 'Invalid username or password.';
            }
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Login — Matrimonial Studio</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php include __DIR__ . '/navbar.php'; ?>

  <main class="container">
    <div class="card" style="max-width:520px;margin:auto;">
      <h2>Login</h2>

      <?php if ($errors): ?>
        <div class="alert error"><?php echo e(join('<br>', $errors)); ?></div>
      <?php endif; ?>

      <form method="post" action="login.php" novalidate>
        <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
        <label>Username</label>
        <input class="input" name="username" value="<?php echo e($_POST['username'] ?? ''); ?>" autofocus>

        <label>Password</label>
        <input class="input" type="password" name="password">

        <p><button class="btn" type="submit">Login</button></p>
        <p class="helper">New here? <a href="signup.php">Create an account</a></p>
      </form>
    </div>
  </main>

  <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>
