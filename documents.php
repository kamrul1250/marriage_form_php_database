<?php
require_once __DIR__ . '/init.php';
require_auth();

$user = current_user();
$errors = [];
$success = null;

// Fetch user documents
$stmt = $pdo->prepare("SELECT * FROM documents WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user['id']]);
$documents = $stmt->fetchAll();

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload'])) {
    $token = $_POST['csrf'] ?? '';
    if (!csrf_check($token)) {
        $errors[] = 'Invalid request (CSRF). Please try again.';
    } else {
        try {
            $file = handle_file_upload('document');
            if ($file) {
                $stmt = $pdo->prepare("INSERT INTO documents (user_id, name, file_path, file_type, file_size) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([
                    $user['id'],
                    $file['name'],
                    $file['path'],
                    $file['type'],
                    $file['size']
                ]);
                $success = 'File uploaded successfully!';
                // Refresh documents list
                $stmt = $pdo->prepare("SELECT * FROM documents WHERE user_id = ? ORDER BY created_at DESC");
                $stmt->execute([$user['id']]);
                $documents = $stmt->fetchAll();
            }
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
    }
}

// Handle file deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $token = $_POST['csrf'] ?? '';
    if (!csrf_check($token)) {
        $errors[] = 'Invalid request (CSRF). Please try again.';
    } else {
        $docId = (int)($_POST['doc_id'] ?? 0);
        if ($docId > 0) {
            try {
                // Get document info first
                $stmt = $pdo->prepare("SELECT * FROM documents WHERE id = ? AND user_id = ? LIMIT 1");
                $stmt->execute([$docId, $user['id']]);
                $doc = $stmt->fetch();
                
                if ($doc) {
                    // Delete from database
                    $stmt = $pdo->prepare("DELETE FROM documents WHERE id = ?");
                    $stmt->execute([$docId]);
                    
                    // Delete file
                    if (delete_file($doc['file_path'])) {
                        $success = 'Document deleted successfully!';
                        // Refresh documents list
                        $stmt = $pdo->prepare("SELECT * FROM documents WHERE user_id = ? ORDER BY created_at DESC");
                        $stmt->execute([$user['id']]);
                        $documents = $stmt->fetchAll();
                    } else {
                        $errors[] = 'Document removed from records but file could not be deleted.';
                    }
                }
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-theme="<?= e($_SESSION['theme']) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Documents - Matrimonial Studio</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include __DIR__ . '/navbar.php'; ?>

    <main class="container">
        <div class="card">
            <h2>My Documents</h2>
            <p class="helper">Upload and manage your documents here (max 2MB each).</p>

            <?php if ($errors): ?>
                <div class="alert error"><?= e(join('<br>', $errors)) ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert success"><?= e($success) ?></div>
            <?php endif; ?>

            <!-- Upload Form -->
            <div class="form-section">
                <h3>Upload New Document</h3>
                <form method="post" action="documents.php" enctype="multipart/form-data">
                    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                    <div class="form-row">
                        <label>Select File (PDF, DOC, JPG, PNG)</label>
                        <input class="input" type="file" name="document" required>
                    </div>
                    <div class="form-actions">
                        <button type="submit" name="upload" class="btn">Upload Document</button>
                    </div>
                </form>
            </div>

            <!-- Documents List -->
            <div class="form-section">
                <h3>Your Documents</h3>
                <?php if (empty($documents)): ?>
                    <p class="helper">No documents uploaded yet.</p>
                <?php else: ?>
                    <div class="documents-list">
                        <?php foreach ($documents as $doc): ?>
                            <div class="document-item">
                                <div class="document-info">
                                    <h4><?= e($doc['name']) ?></h4>
                                    <p class="helper">
                                        <?= strtoupper(pathinfo($doc['file_path'], PATHINFO_EXTENSION)) ?> • 
                                        <?= round($doc['file_size'] / 1024) ?> KB • 
                                        <?= date('M d, Y', strtotime($doc['created_at'])) ?>
                                    </p>
                                </div>
                                <div class="document-actions">
                                    <a href="<?= e($doc['file_path']) ?>" target="_blank" class="btn secondary">View</a>
                                    <form method="post" action="documents.php" style="display:inline;">
                                        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                                        <input type="hidden" name="doc_id" value="<?= $doc['id'] ?>">
                                        <button type="submit" name="delete" class="btn danger" 
                                                onclick="return confirm('Are you sure you want to delete this document?')">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>