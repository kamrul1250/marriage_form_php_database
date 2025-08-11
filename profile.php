<?php
require_once __DIR__ . '/init.php';
require_auth();

$user = current_user();
$errors = [];
$success = null;

// Check if user has a profile
$stmt = $pdo->prepare("SELECT * FROM profiles WHERE user_id = ? LIMIT 1");
$stmt->execute([$user['id']]);
$profile = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en" data-theme="<?= e($_SESSION['theme']) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Matrimonial Studio</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include __DIR__ . '/navbar.php'; ?>

    <main class="container">
        <div class="card">
            <div class="profile-header">
                <?php if (!empty($profile['photo'])): ?>
                    <img src="<?= e($profile['photo']) ?>" alt="Profile Photo" class="profile-photo">
                <?php else: ?>
                    <div class="profile-photo placeholder">
                        <i class="fas fa-user"></i>
                    </div>
                <?php endif; ?>
                <div class="profile-info">
                    <h1>Welcome, <?= e($user['username']) ?></h1>
                    <p class="helper">Manage your matrimonial profile from here.</p>
                </div>
            </div>

            <?php if ($profile): ?>
                <div class="action-buttons">
                    <a href="view.php?id=<?= $profile['id'] ?>" class="btn">
                        View/Edit Profile
                    </a>
                    <a href="settings.php" class="btn secondary">
                        Account Settings
                    </a>
                </div>
            <?php else: ?>
                <!-- Show create profile card if no profile exists -->
                <div class="create-profile-card" onclick="window.location.href='edit_profile.php'">
                    <div class="create-profile-inner">
                        <div class="create-profile-icon">
                            <i class="fas fa-plus"></i>
                        </div>
                        <h3>Create Your Matrimonial Profile</h3>
                        <p>Click here to start building your profile</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>