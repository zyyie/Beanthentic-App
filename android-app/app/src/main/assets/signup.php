<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sign up · Beanthentic Coffee</title>
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/layout.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/responsive.css">
</head>
<body>
  <button type="button" id="nav-mobile-overlay" class="nav-mobile-overlay" hidden aria-hidden="true" tabindex="-1"></button>
  <header>
    <div class="nav">
      <a href="index.php" class="logo" aria-label="Beanthentic home">
        <img class="logo-mark" src="beantHentic_logo.png" alt="Beanthentic logo" />
        <span>BEANTHENTIC</span>
      </a>
      <button
        type="button"
        id="nav-menu-toggle"
        class="nav-menu-toggle"
        aria-label="Open menu"
        aria-expanded="false"
        aria-controls="main-nav-links"
      >
        <span class="nav-menu-toggle-line" aria-hidden="true"></span>
        <span class="nav-menu-toggle-line" aria-hidden="true"></span>
        <span class="nav-menu-toggle-line" aria-hidden="true"></span>
      </button>
      <nav class="nav-links" id="main-nav-links">
        <a href="index.php#home" data-no-loader="true">Home</a>
        <a href="login.php" data-no-loader="true">Login</a>
        <a href="signup.php" data-no-loader="true" aria-current="page">Sign up</a>
      </nav>
    </div>
  </header>

  <main class="auth-main">
    <div class="auth-card">
      <h1>Create account</h1>
      <p class="auth-lead">Join Beanthentic to connect with local coffee farmers.</p>
      <form class="auth-form" method="post" action="#" autocomplete="on">
        <label for="signup-name">Full name</label>
        <input id="signup-name" name="name" type="text" required autocomplete="name" placeholder="Your name" />

        <label for="signup-email">Email</label>
        <input id="signup-email" name="email" type="email" required autocomplete="email" placeholder="you@example.com" />

        <label for="signup-password">Password</label>
        <input id="signup-password" name="password" type="password" required autocomplete="new-password" placeholder="••••••••" minlength="8" />

        <label for="signup-password2">Confirm password</label>
        <input id="signup-password2" name="password_confirm" type="password" required autocomplete="new-password" placeholder="••••••••" minlength="8" />

        <button type="submit" class="btn-primary">Create account</button>
      </form>
      <p class="auth-switch">Already have an account? <a href="login.php">Sign in</a></p>
    </div>
  </main>

  <footer>
    <div class="footer-inner">
      <span><span class="footer-dot"></span> Beanthentic &copy; <span id="year"><?php echo date('Y'); ?></span> · Brewed with care.</span>
      <span>Serving honest coffee, one cup at a time.</span>
    </div>
  </footer>

  <script src="js/navigation.js"></script>
  <script src="js/ui.js"></script>
</body>
</html>
