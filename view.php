<?php
require_once __DIR__ . '/init.php';
require_auth();

$user = current_user();
$errors = [];
$success = null;

$profileId = $_GET['id'] ?? 0;
if ($profileId === 0) {
    header('Location: profile.php');
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM profiles WHERE id = ? AND user_id = ? LIMIT 1");
$stmt->execute([$profileId, $user['id']]);
$profile = $stmt->fetch();

if (!$profile) {
    flash_set('error', 'Profile not found.');
    header('Location: profile.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en" data-theme="<?= e($_SESSION['theme']) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Profile - Matrimonial Studio</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include __DIR__ . '/navbar.php'; ?>

    <main class="container">
        <div class="card">
            <div class="profile-header">
                <?php if (!empty($profile['photo'])): ?>
                    <img src="<?= e($profile['photo']) ?>" alt="Profile Photo" class="profile-photo-large">
                <?php else: ?>
                    <div class="profile-photo-large placeholder">
                        <i class="fas fa-user"></i>
                    </div>
                <?php endif; ?>
                <div class="profile-info">
                    <h1><?= e($profile['full_name']) ?></h1>
                    <div class="profile-meta">
                        <span><i class="fas fa-user"></i> <?= e($profile['gender']) ?></span>
                        <span><i class="fas fa-birthday-cake"></i> <?= e($profile['age']) ?> years</span>
                        <span><i class="fas fa-map-marker-alt"></i> <?= e($profile['city'] ?? '') ?>, <?= e($profile['country'] ?? '') ?></span>
                    </div>
                    
                    <div class="profile-actions">
                        <a href="edit_profile.php?id=<?= $profile['id'] ?>" class="btn">
                            <i class="fas fa-edit"></i> Edit Profile
                        </a>
                        <a href="delete_profile.php?id=<?= $profile['id'] ?>" class="btn danger">
                            <i class="fas fa-trash"></i> Delete Profile
                        </a>
                      
                    </div>
                </div>
            </div>

            <div class="profile-section">
                <h2><i class="fas fa-info-circle"></i> Basic Information</h2>
                <div class="section-content info-grid">
                    <div>
                        <p><strong>Full Name:</strong> <?= e($profile['full_name']) ?></p>
                        <p><strong>Gender:</strong> <?= e($profile['gender']) ?></p>
                        <p><strong>Date of Birth:</strong> <?= e($profile['dob'] ?? 'Not specified') ?></p>
                    </div>
                    <div>
                        <p><strong>Age:</strong> <?= e($profile['age']) ?></p>
                        <p><strong>Marital Status:</strong> <?= e($profile['marital_status'] ?? 'Not specified') ?></p>
                        <p><strong>Religion:</strong> <?= e($profile['religion'] ?? 'Not specified') ?></p>
                    </div>
                </div>
            </div>

            <div class="profile-section">
                <h2><i class="fas fa-address-card"></i> Contact Information</h2>
                <div class="section-content contact-grid">
                    <div>
                        <p><strong>Email:</strong> <?= e($user['email']) ?></p>
                        <p><strong>Phone:</strong> <?= e($profile['phone'] ?? 'Not specified') ?></p>
                    </div>
                    <div>
                        <p><strong>Address:</strong> <?= e($profile['address'] ?? 'Not specified') ?></p>
                        <p><strong>Location:</strong> <?= e($profile['city'] ?? '') ?>, <?= e($profile['country'] ?? '') ?></p>
                    </div>
                </div>
            </div>

            <!-- Add more profile sections as needed -->
        </div>
    </main>

    <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>