<?php
// footer.php - include at bottom of pages

?>
<section class="container">
  <div class="card contact">
    <div class="contact-card">
      <h3>Reach Out</h3>
      <p class="helper">Have a question or want help building something custom? Contact me.</p>
      <p><strong>Email:</strong> <a href="mailto:kamrulhassan1250@gmail.com">kamrulhassan1250@gmail.com</a></p>
      <p><strong>Phone:</strong> <a href="tel:+8801990207710">+8801990207710</a></p>

      <div class="socials">
        <!-- LinkedIn -->
<a href="https://www.linkedin.com/in/kamrul-hassan-799254368/" target="_blank" rel="noopener">
    <img src="https://cdn.jsdelivr.net/gh/simple-icons/simple-icons/icons/linkedin.svg" 
         alt="LinkedIn" style="width:30px; height:30px;">
</a>

<!-- GitHub -->
<a href="https://github.com/kamrul1250" target="_blank" rel="noopener">
    <img src="https://cdn.jsdelivr.net/gh/simple-icons/simple-icons/icons/github.svg" 
         alt="GitHub" style="width:30px; height:30px;">
</a>

      </div>
    </div>

    <div style="flex:1 1 300px;">
      <div class="contact-card">
        <h3>About Matrimonial Studio</h3>
        <p class="helper">A clean, secure matrimonial profile manager. Secure passwords, user profiles, professional layout.</p>
      </div>
    </div>
  </div>
</section>

<footer>
  &copy; <?= date('Y') ?> Matrimonial Studio — Designed by Kamrul Hassan
  
</footer>

<script>// Theme switcher
function setTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
    localStorage.setItem('theme', theme);
    
    // Update the radio button if it exists
    const radio = document.querySelector(`input[name="theme"][value="${theme}"]`);
    if (radio) {
        radio.checked = true;
        document.querySelectorAll('.theme-option').forEach(opt => {
            opt.classList.remove('active');
        });
        radio.closest('.theme-option').classList.add('active');
    }
}

// Check for saved theme preference
const savedTheme = localStorage.getItem('theme') || 'light';
setTheme(savedTheme);

// Handle theme radio changes
document.querySelectorAll('input[name="theme"]').forEach(radio => {
    radio.addEventListener('change', function() {
        setTheme(this.value);
        
        // Optional: Send to server to save preference
        fetch('update_theme.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `theme=${this.value}`
        });
    });
});</script>