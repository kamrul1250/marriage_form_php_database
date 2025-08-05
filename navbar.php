<?php
// navbar.php - include after init.php on every page
if (!function_exists('current_user')) {
    function current_user() {
        return $_SESSION['user'] ?? null;
    }
} // init.php defines this
?>
<nav class="site-nav" aria-label="Main site navigation">
  <div class="nav-inner">
    <a class="brand" href="index.php">
      <span class="logo">MS</span>
      <span>Matrimonial Studio</span>
    </a>

    <div class="nav-links" role="navigation" aria-label="Main menu">
      <a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>">Home</a>

      <?php if ($user): ?>
        <a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>">Dashboard</a>
        <a href="profile.php" class="<?= basename($_SERVER['PHP_SELF']) === 'profile.php' ? 'active' : '' ?>">My Profile</a>
        <a href="logout.php">Logout</a>
      <?php else: ?>
        <a href="login.php" class="<?= basename($_SERVER['PHP_SELF']) === 'login.php' ? 'active' : '' ?>">Login</a>
        <a href="signup.php" class="<?= basename($_SERVER['PHP_SELF']) === 'signup.php' ? 'active' : '' ?>">Sign Up</a>
      <?php endif; ?>
    </div>
  </div>
</nav>
