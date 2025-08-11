<?php
require_once __DIR__ . '/init.php';
require_auth();

$user = current_user();
$errors = [];
$success = null;

// Handle theme change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_theme'])) {
    $token = $_POST['csrf'] ?? '';
    if (!csrf_check($token)) {
        $errors[] = 'Invalid request (CSRF). Please try again.';
    } else {
        $theme = $_POST['theme'] ?? 'light';
        if (in_array($theme, ['light', 'dark'])) {
            $_SESSION['theme'] = $theme;
            
            try {
                // Check if preferences exist
                $stmt = $pdo->prepare("SELECT user_id FROM user_preferences WHERE user_id = ?");
                $stmt->execute([$user['id']]);
                
                if ($stmt->fetch()) {
                    // Update existing
                    $stmt = $pdo->prepare("UPDATE user_preferences SET theme_preference = ? WHERE user_id = ?");
                } else {
                    // Insert new
                    $stmt = $pdo->prepare("INSERT INTO user_preferences (user_id, theme_preference) VALUES (?, ?)");
                }
                $stmt->execute([$theme, $user['id']]);
                $success = 'Theme preference updated successfully!';
            } catch (PDOException $e) {
                $errors[] = 'Failed to save theme preference.';
                error_log("Theme preference error: " . $e->getMessage());
            }
        } else {
            $errors[] = 'Invalid theme selected.';
        }
    }
}

// Handle language change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_language'])) {
    $token = $_POST['csrf'] ?? '';
    if (!csrf_check($token)) {
        $errors[] = 'Invalid request (CSRF). Please try again.';
    } else {
        $language = $_POST['language'] ?? 'en';
        if (in_array($language, ['en', 'es', 'fr', 'de', 'bn'])) { // Add more as needed
            $_SESSION['language'] = $language;
            
            try {
                // Check if preferences exist
                $stmt = $pdo->prepare("SELECT user_id FROM user_preferences WHERE user_id = ?");
                $stmt->execute([$user['id']]);
                
                if ($stmt->fetch()) {
                    // Update existing
                    $stmt = $pdo->prepare("UPDATE user_preferences SET language_preference = ? WHERE user_id = ?");
                    $stmt->execute([$language, $user['id']]);
                } else {
                    // Insert new, include theme_preference to satisfy NOT NULL constraint
                    $theme = $_SESSION['theme'] ?? 'light'; // Use session theme or default
                    $stmt = $pdo->prepare("INSERT INTO user_preferences (user_id, theme_preference, language_preference) VALUES (?, ?, ?)");
                    $stmt->execute([$user['id'], $theme, $language]);
                }
                $success = 'Language preference updated successfully!';
            } catch (PDOException $e) {
                $errors[] = 'Failed to save language preference: ' . $e->getMessage();
                error_log("Language preference error: " . $e->getMessage());
            }
        } else {
            $errors[] = 'Invalid language selected.';
        }
    }
}

// Rest of your existing settings.php code for password and account info changes...
// Make sure to update the database schema to include the user_preferences table
?>

<!DOCTYPE html>
<html lang="en" data-theme="<?= e($_SESSION['theme']) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings - Matrimonial Studio</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include __DIR__ . '/navbar.php'; ?>

    <main class="container">
        <div class="card">
            <h2>Account Settings</h2>
            
            <?php if ($errors): ?>
                <div class="alert error"><?= e(join('<br>', $errors)) ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert success"><?= e($success) ?></div>
            <?php endif; ?>

            <!-- Theme Selection -->
            <div class="form-section">
                <h3>Theme Preferences</h3>
                <form method="post" action="settings.php">
                    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                    <div class="theme-options">
                        <label class="theme-option <?= $_SESSION['theme'] === 'light' ? 'active' : '' ?>">
                            <input type="radio" name="theme" value="light" <?= $_SESSION['theme'] === 'light' ? 'checked' : '' ?>>
                            <i class="fas fa-sun"></i> Light
                        </label>
                        <label class="theme-option <?= $_SESSION['theme'] === 'dark' ? 'active' : '' ?>">
                            <input type="radio" name="theme" value="dark" <?= $_SESSION['theme'] === 'dark' ? 'checked' : '' ?>>
                            <i class="fas fa-moon"></i> Dark
                        </label>
                    </div>
                    <div class="form-actions">
                        <button type="submit" name="change_theme" class="btn">Save Theme</button>
                    </div>
                </form>
            </div>

            <!-- Language Selection -->
            <div class="form-section">
                <h3>Language Preferences</h3>
                <form method="post" action="settings.php">
                    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                    <div class="form-row">
                        <select class="input" name="language">
                            <option value="en" <?= $_SESSION['language'] === 'en' ? 'selected' : '' ?>>English</option>
                            <option value="es" <?= $_SESSION['language'] === 'es' ? 'selected' : '' ?>>Español</option>
                            <option value="fr" <?= $_SESSION['language'] === 'fr' ? 'selected' : '' ?>>Français</option>
                            <option value="de" <?= $_SESSION['language'] === 'de' ? 'selected' : '' ?>>Deutsch</option>
                            <option value="bn" <?= $_SESSION['language'] === 'bn' ? 'selected' : '' ?>>বাংলা</option>
                        </select>
                    </div>
                    <div class="form-actions">
                        <button type="submit" name="change_language" class="btn">Save Language</button>
                    </div>
                </form>
            </div>

            <!-- Your existing password and account info forms -->
            <div class="form-section">
                <h3>Change Password</h3>
                <form method="post" action="settings.php">
                    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                    <div class="form-row">
                        <label>Current Password</label>
                        <input class="input" type="password" name="current_password" required>
                    </div>
                    <div class="form-row">
                        <label>New Password</label>
                        <input class="input" type="password" name="new_password" required>
                    </div>
                    <div class="form-row">
                        <label>Confirm New Password</label>
                        <input class="input" type="password" name="confirm_password" required>
                    </div>
                    <div class="form-actions">
                        <button type="submit" name="change_password" class="btn">Change Password</button>
                    </div>
                </form>
            </div>

            <div class="form-section">
                <h3>Update Account Information</h3>
                <form method="post" action="settings.php">
                    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                    <div class="form-row">
                        <label>Username</label>
                        <input class="input" name="username" value="<?= e($currentUser['username'] ?? '') ?>" required>
                    </div>
                    <div class="form-row">
                        <label>Email</label>
                        <input class="input" type="email" name="email" value="<?= e($currentUser['email'] ?? '') ?>" required>
                    </div>
                    <div class="form-actions">
                        <button type="submit" name="update_info" class="btn">Update Information</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/footer.php'; ?>
    
    <!-- Add this script to handle theme changes without page reload -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Theme radio buttons
        const themeRadios = document.querySelectorAll('input[name="theme"]');
        themeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.checked) {
                    document.documentElement.setAttribute('data-theme', this.value);
                }
            });
        });
        
        // You can add similar AJAX handling here for language changes if you want
        // to implement real-time language switching without page reload
    });
    </script>
</body>
</html>