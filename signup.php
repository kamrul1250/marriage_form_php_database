<?php
require_once __DIR__ . '/init.php';
if (current_user()) {
    header('Location: dashboard.php');
    exit();
}

$errors = [];
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf'] ?? '';
    if (!csrf_check($token)) {
        $errors[] = 'Invalid request (CSRF). Try again.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if ($username === '' || $email === '' || $password === '') {
            $errors[] = 'Please fill in all required fields.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email address.';
        } elseif ($password !== $confirm) {
            $errors[] = 'Passwords do not match.';
        } else {
            // check existing
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1");
            $stmt->execute([$username, $email]);
            if ($stmt->fetch()) {
                $errors[] = 'Username or email already exists.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
                $stmt->execute([$username, $email, $hash]);
                $success = 'Registration successful. You can now <a href="login.php">log in</a>.';
            }
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Sign Up — Matrimonial Studio</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php include __DIR__ . '/navbar.php'; ?>

  <main class="container">
    <div class="card" style="max-width:720px; margin:auto;">
      <h2>Create an Account</h2>

      <?php if ($errors): ?>
        <div class="alert error"><?php echo e(join('<br>', $errors)); ?></div>
      <?php endif; ?>

      <?php if ($success): ?>
        <div class="alert success"><?php echo $success; ?></div>
      <?php else: ?>
        <form method="post" action="signup.php" novalidate>
          <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
          <div class="form-grid">
            <div>
              <label>Username</label>
              <input class="input" name="username" value="<?php echo e($_POST['username'] ?? ''); ?>" required>
            </div>
            <div>
              <label>Email</label>
              <input class="input" type="email" name="email" value="<?php echo e($_POST['email'] ?? ''); ?>" required>
            </div>
          </div>

          <div class="form-grid">
            <div>
              <label>Password</label>
              <input class="input" type="password" name="password" required>
            </div>
            <div>
              <label>Confirm Password</label>
              <input class="input" type="password" name="confirm_password" required>
            </div>
          </div>

          <p><button class="btn" type="submit">Create Account</button></p>
          <p class="helper">Already a member? <a href="login.php">Log in</a></p>
        </form>
      <?php endif; ?>
    </div>
  </main>

  <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>
