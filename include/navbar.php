<nav class="navbar navbar-expand-md navbar-light bg-light">
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#div-navbar-items" aria-controls="div-navbar-items" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="div-navbar-items">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item">
        <a class="nav-link" href="<?= APP_URL ?>/index.php">Home</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">About us</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">Contact us</a>
      </li>
    </ul>
    <ul class="navbar-nav ml-auto">
      <?php if (!isset($_SESSION) || !array_key_exists("email", $_SESSION)) { ?>
      <li class="nav-item">
        <a class="nav-link" href="<?= APP_URL ?>/login-form.php">Login</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?= APP_URL ?>/register-form.php">Register</a>
      </li>
      <?php } else { ?>
        <li class="nav-item">
          <a class="nav-link" href="<?= APP_URL ?>/logout.php">Logout</a>
        </li>
      <?php } ?>
    </ul>
  </div>
</nav>
