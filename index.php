<?php
require_once __DIR__ . '/init.php';
$user = current_user();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Matrimonial Studio — Find your match</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php include __DIR__ . '/navbar.php'; ?>

  <main class="container">
    <div class="card" style="display:flex; gap:20px; align-items:center; justify-content:space-between; flex-wrap:wrap;">
      <div style="flex:1;">
        <h1>Welcome to Matrimonial Studio</h1>
        <p class="helper">Create a professional matrimonial profile and let others find you easier. Secure, modern and simple.</p>

        <?php if ($user): ?>
          <p><a class="btn" href="dashboard.php">Go to Dashboard</a></p>
        <?php else: ?>
          <p><a class="btn" href="signup.php">Get Started — Create an account</a></p>
          <p class="helper" style="margin-top:8px;">Already registered? <a href="login.php">Login</a></p>
        <?php endif; ?>
      </div>

      <div style="width:320px; text-align:center;">
        <img src="2.jpg" alt="Professional profile" style="max-width:100%; opacity:0.95;">
      </div>
    </div>
  </main>

  <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>
