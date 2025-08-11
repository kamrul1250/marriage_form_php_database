<?php
require_once __DIR__ . '/init.php';
$user = current_user();
if (!$user) {
    header('Location: login.php');
    exit();
}

// Load profile summary
$stmt = $pdo->prepare("SELECT * FROM profiles WHERE user_id = ? LIMIT 1");
$stmt->execute([$user['id']]);
$profile = $stmt->fetch();

// Get documents count
$stmt = $pdo->prepare("SELECT COUNT(*) FROM documents WHERE user_id = ?");
$stmt->execute([$user['id']]);
$documentsCount = $stmt->fetchColumn();
?>
<!doctype html>
<html lang="en" data-theme="<?= e($_SESSION['theme']) ?>">
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
      <div class="profile-header">
        <?php if ($profile && !empty($profile['photo'])): ?>
          <img src="<?= e($profile['photo']) ?>" alt="Profile Photo" class="profile-photo">
        <?php else: ?>
          <div class="profile-photo placeholder">
            <i class="fas fa-user"></i>
          </div>
        <?php endif; ?>
        <div class="profile-info">
          <h1>Welcome, <?php echo e($user['username']); ?></h1>
          <p class="helper">Manage your matrimonial profile and settings from here.</p>
          
          <div class="profile-stats">
            <div class="stat-item">
              <span class="stat-number"><?= $profile ? '100%' : '0%' ?></span>
              <span class="stat-label">Profile Complete</span>
            </div>
            <div class="stat-item">
              <span class="stat-number"><?= $documentsCount ?></span>
              <span class="stat-label">Documents</span>
            </div>
          </div>
        </div>
      </div>

      <div class="action-buttons">
        <a class="btn" href="profile.php"><?php echo $profile ? 'Edit Profile' : 'Create Profile'; ?></a>
        <a class="btn secondary" href="documents.php">My Documents</a>
        <a class="btn secondary" href="view.php?id=<?= $profile['id'] ?? '' ?>">View Profile</a>
      </div>

      <?php if ($profile): ?>
        <section class="profile-summary">
          <h2>Profile Summary</h2>
          <div class="summary-grid">
            <div>
              <p><strong>Name:</strong> <?php echo e($profile['full_name']); ?></p>
              <p><strong>Age / Gender:</strong> <?php echo e($profile['age']); ?> / <?php echo e($profile['gender']); ?></p>
            </div>
            <div>
              <p><strong>Location:</strong> <?php echo e($profile['city'] ?? ''); ?>, <?php echo e($profile['country'] ?? ''); ?></p>
              <p><strong>Marital Status:</strong> <?php echo e($profile['marital_status'] ?? 'Not specified'); ?></p>
            </div>
          </div>
        </section>
      <?php else: ?>
        <div class="alert info" style="margin-top: 20px;">
          <strong>Profile Incomplete:</strong> You haven't created your profile yet. 
          <a href="profile.php" style="color: var(--primary); font-weight: 600;">Create your profile</a> 
          to increase your matches.
        </div>
      <?php endif; ?>
    </div>
  </main>

  <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>