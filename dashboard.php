<?php
require_once __DIR__ . '/init.php';
$user = current_user();
if (!$user) {
    header('Location: login.php');
    exit();
}

// optional: load profile summary
$stmt = $pdo->prepare("SELECT * FROM profiles WHERE user_id = ? LIMIT 1");
$stmt->execute([$user['id']]);
$profile = $stmt->fetch();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Dashboard — Matrimonial Studio</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php include __DIR__ . '/navbar.php'; ?>

  <main class="container">
    <div class="card">
      <h1>Hello, <?php echo e($user['username']); ?></h1>
      <p class="helper">Manage your matrimonial profile and settings from here.</p>

      <div style="display:flex; gap:12px; align-items:center; flex-wrap:wrap; margin-top:18px;">
        <a class="btn" href="profile.php"><?php echo $profile ? 'Edit Profile' : 'Create Profile'; ?></a>
        <a class="btn secondary" href="logout.php">Logout</a>
      </div>

      <?php if ($profile): ?>
        <section style="margin-top:20px;">
          <h2>Profile Summary</h2>
          <p><strong>Name:</strong> <?php echo e($profile['full_name']); ?></p>
          <p><strong>Age / Gender:</strong> <?php echo e($profile['age']); ?> / <?php echo e($profile['gender']); ?></p>
          <p><strong>Location:</strong> <?php echo e($profile['nationality']); ?></p>
        </section>
      <?php endif; ?>
    </div>
  </main>

  <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>
