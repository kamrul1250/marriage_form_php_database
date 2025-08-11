<?php
require_once __DIR__ . '/init.php';
require_auth();

$user = current_user();

// Check if profile exists
$stmt = $pdo->prepare("SELECT * FROM profiles WHERE user_id = ? LIMIT 1");
$stmt->execute([$user['id']]);
$profile = $stmt->fetch();

if (!$profile) {
    flash_set('error', 'No profile found to delete.');
    header('Location: profile.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf'] ?? '';
    if (!csrf_check($token)) {
        flash_set('error', 'Invalid request (CSRF). Please try again.');
        header('Location: profile.php');
        exit();
    }

    try {
        // Delete profile photo if exists
        if (!empty($profile['photo']) && file_exists(__DIR__ . '/' . $profile['photo'])) {
            unlink(__DIR__ . '/' . $profile['photo']);
        }

        // Delete profile record
        $stmt = $pdo->prepare("DELETE FROM profiles WHERE user_id = ?");
        $stmt->execute([$user['id']]);

        flash_set('success', 'Your profile has been deleted successfully.');
        header('Location: dashboard.php');
        exit();

    } catch (Exception $e) {
        flash_set('error', 'Failed to delete profile: ' . $e->getMessage());
        header('Location: profile.php');
        exit();
    }
}

// If not POST request, show confirmation page
?>
<!DOCTYPE html>
<html lang="en" data-theme="<?= e($_SESSION['theme']) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Profile - Matrimonial Studio</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include __DIR__ . '/navbar.php'; ?>

    <main class="container">
        <div class="card" style="max-width: 600px; margin: auto;">
            <h2>Delete Profile</h2>
            <div class="alert error">
                <strong>Warning:</strong> This will permanently delete your profile and cannot be undone.
            </div>
            
            <p>Are you sure you want to delete your profile? All your profile information will be lost.</p>
            
            <form method="post" action="delete_profile.php">
                <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                <div class="form-actions">
                    <button type="submit" class="btn danger">Yes, Delete My Profile</button>
                    <a href="profile.php" class="btn secondary">Cancel</a>
                </div>
            </form>
        </div>
    </main>

    <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>