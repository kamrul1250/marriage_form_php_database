<?php
require_once __DIR__ . '/init.php';
require_auth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $theme = $_POST['theme'] ?? 'light';
    if (in_array($theme, ['light', 'dark'])) {
        $_SESSION['theme'] = $theme;
        
        $user = current_user();
        if ($user) {
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
                
                echo json_encode(['success' => true]);
                exit;
            } catch (PDOException $e) {
                error_log("Theme update error: " . $e->getMessage());
            }
        }
    }
}

echo json_encode(['success' => false]);