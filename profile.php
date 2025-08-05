<?php
require_once __DIR__ . '/init.php';
$user = current_user();
if (!$user) {
    header('Location: login.php');
    exit();
}

$errors = [];
$success = null;

// fetch existing
$stmt = $pdo->prepare("SELECT * FROM profiles WHERE user_id = ? LIMIT 1");
$stmt->execute([$user['id']]);
$profile = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf'] ?? '';
    if (!csrf_check($token)) {
        $errors[] = 'Invalid request (CSRF).';
    } else {
        $full_name = trim($_POST['full_name'] ?? '');
        $age = (int)($_POST['age'] ?? 0);
        $gender = $_POST['gender'] ?? '';
        $religion = trim($_POST['religion'] ?? '');
        $nationality = trim($_POST['nationality'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');

        if ($full_name === '' || $age < 18 || !$gender) {
            $errors[] = 'Please fill in required fields: Full name, Age(>=18), Gender.';
        } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email.';
        } else {
            // optional photo upload (improved)
            $photoPath = $profile['photo'] ?? null;
            if (!empty($_FILES['photo']['name'])) {
                $up = $_FILES['photo'];
                $ext = strtolower(pathinfo($up['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg','jpeg','png','webp'];

                if ($up['error'] !== 0) {
                    $errors[] = 'Upload error code: ' . $up['error'];
                } elseif (!in_array($ext, $allowed)) {
                    $errors[] = 'Invalid photo format. Allowed: JPG, JPEG, PNG, WebP.';
                } elseif ($up['size'] > 2 * 1024 * 1024) {
                    $errors[] = 'Photo too large. Max size: 2MB.';
                } else {
                    $newName = 'p_' . $user['id'] . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
                    $uploadDir = __DIR__ . '/assets/img/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    $target = $uploadDir . $newName;

                    if (!move_uploaded_file($up['tmp_name'], $target)) {
                        $errors[] = 'Failed to save uploaded photo. Please check folder permissions.';
                    } else {
                        $photoPath = 'assets/img/' . $newName;
                    }
                }
            }

            if (!$errors) {
                if ($profile) {
                    $stmt = $pdo->prepare("UPDATE profiles SET full_name=?, age=?, gender=?, religion=?, nationality=?, email=?, phone=?, address=?, photo=?, updated_at=NOW() WHERE user_id=?");
                    $stmt->execute([$full_name, $age, $gender, $religion, $nationality, $email, $phone, $address, $photoPath, $user['id']]);
                    $success = 'Profile updated.';
                } else {
                    $stmt = $pdo->prepare("INSERT INTO profiles (user_id, full_name, age, gender, religion, nationality, email, phone, address, photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$user['id'], $full_name, $age, $gender, $religion, $nationality, $email, $phone, $address, $photoPath]);
                    $success = 'Profile created.';
                }

                // reload
                $stmt = $pdo->prepare("SELECT * FROM profiles WHERE user_id = ? LIMIT 1");
                $stmt->execute([$user['id']]);
                $profile = $stmt->fetch();
            }
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Profile — Matrimonial Studio</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php include __DIR__ . '/navbar.php'; ?>

  <main class="container">
    <div class="card" style="max-width:900px; margin:auto;">
      <h2><?php echo $profile ? 'Edit' : 'Create'; ?> Profile</h2>

      <?php if ($errors): ?>
        <div class="alert error"><?php echo e(join('<br>', $errors)); ?></div>
      <?php endif; ?>
      <?php if ($success): ?>
        <div class="alert success"><?php echo e($success); ?></div>
      <?php endif; ?>

      <form method="post" action="profile.php" enctype="multipart/form-data" novalidate>
        <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">

        <div class="form-grid">
          <div>
            <label>Full name</label>
            <input class="input" name="full_name" value="<?php echo e($profile['full_name'] ?? ''); ?>">
          </div>
          <div>
            <label>Age</label>
            <input class="input" type="number" name="age" min="18" max="120" value="<?php echo e($profile['age'] ?? ''); ?>">
          </div>

          <div>
            <label>Gender</label>
            <select class="input" name="gender">
              <option value="">Select</option>
              <?php foreach(['Male','Female','Other'] as $g): ?>
                <option value="<?php echo $g;?>" <?php if (($profile['gender'] ?? '')===$g) echo 'selected'; ?>><?php echo $g;?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label>Nationality</label>
            <input class="input" name="nationality" value="<?php echo e($profile['nationality'] ?? ''); ?>">
          </div>

          <div>
            <label>Religion</label>
            <input class="input" name="religion" value="<?php echo e($profile['religion'] ?? ''); ?>">
          </div>

          <div>
            <label>Email</label>
            <input class="input" type="email" name="email" value="<?php echo e($profile['email'] ?? $user['email']); ?>">
          </div>

          <div>
            <label>Phone</label>
            <input class="input" name="phone" value="<?php echo e($profile['phone'] ?? ''); ?>">
          </div>

          <div style="grid-column:1/3;">
            <label>Address</label>
            <textarea class="input" name="address"><?php echo e($profile['address'] ?? ''); ?></textarea>
          </div>

          <div>
            <label>Photo (optional)</label>
            <input class="input" type="file" name="photo" accept=".jpg,.jpeg,.png,.webp">
            <?php if (!empty($profile['photo'])): ?>
              <div style="margin-top:8px;">
                <img src="<?php echo e($profile['photo']); ?>" alt="photo" style="max-width:120px;border-radius:8px;">
              </div>
            <?php endif; ?>
          </div>
        </div>

        <p style="margin-top:12px;">
          <button class="btn" type="submit"><?php echo $profile ? 'Update Profile' : 'Create Profile'; ?></button>
          <a class="btn secondary" href="dashboard.php" style="margin-left:8px;">Back</a>
        </p>
      </form>
    </div>
  </main>

  <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>
