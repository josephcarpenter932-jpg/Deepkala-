<?php
require 'backend/config.php';
$errors = [];
$nameValue = '';
$phoneValue = '';
$emailValue = '';
$redirect = safe_redirect($_GET['redirect'] ?? 'index.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $nameValue = trim($_POST['name'] ?? '');
    $phoneValue = trim($_POST['phone'] ?? '');
    $emailValue = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $redirect = safe_redirect($_POST['redirect'] ?? 'index.php');

    if (!preg_match('/^[A-Za-z ]{2,60}$/', $nameValue)) {
        $errors['name'] = 'Name should only contain letters and spaces.';
    }
    if (!preg_match('/^(\+91[\-\s]?)?[6-9]\d{9}$/', $phoneValue)) {
        $errors['phone'] = 'Enter a valid 10-digit Indian mobile number.';
    }
    if (!filter_var($emailValue, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'That does not look like a valid email address.';
    }
    if (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errors['password'] = 'Password must be at least 8 characters and contain a letter and a number.';
    }

    if (!$errors) {
        $email = strtolower($emailValue);
        $check = mysqli_prepare($conn, 'SELECT id FROM users WHERE email = ? LIMIT 1');
        mysqli_stmt_bind_param($check, 's', $email);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);
        if (mysqli_stmt_num_rows($check) > 0) {
            $errors['email'] = 'An account with this email already exists. Please login instead.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "INSERT INTO users (name, phone, email, password, membership) VALUES (?, ?, ?, ?, 'none')");
            mysqli_stmt_bind_param($stmt, 'ssss', $nameValue, $phoneValue, $email, $hash);
            if (!mysqli_stmt_execute($stmt)) {
                $errors['general'] = 'Could not create the account. Please try again.';
            } else {
                session_regenerate_id(true);
                $_SESSION['user_id'] = mysqli_insert_id($conn);
                $_SESSION['user_name'] = $nameValue;
                $_SESSION['user_phone'] = $phoneValue;
                $_SESSION['user_email'] = $email;
                $_SESSION['membership'] = 'none';
                header('Location: ' . $redirect);
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Sign Up — Deepkala Hair Salon</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    font-family: 'Inter', sans-serif;
    background-color: #15130E;
    color: #F3EEE2;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
  }
  .auth-box {
    background-color: #1E1A14;
    border: 1px solid #7A6C55;
    border-radius: 10px;
    padding: 36px 32px;
    width: 100%;
    max-width: 380px;
  }
  h1 {
    font-family: 'Playfair Display', serif;
    font-size: 1.5rem;
    margin-bottom: 6px;
    text-align: center;
  }
  .sub { text-align: center; color: #A9884C; font-size: 0.85rem; margin-bottom: 24px; }
  label { display: block; font-size: 0.8rem; margin-bottom: 6px; color: #C9A66B; }
  .field-group { margin-bottom: 16px; }
  input {
    width: 100%;
    padding: 10px 12px;
    border-radius: 6px;
    border: 1px solid #7A6C55;
    background-color: #221E17;
    color: #F3EEE2;
    font-size: 0.95rem;
  }
  input.invalid { border-color: #E0665A; }
  .field-error { display: block; font-size: 0.75rem; color: #E0665A; margin-top: 5px; }
  .hint { display: block; font-size: 0.72rem; color: #7A6C55; margin-top: 5px; }
  button {
    width: 100%;
    padding: 11px;
    background-color: #C9A66B;
    color: #15130E;
    border: none;
    border-radius: 6px;
    font-weight: 700;
    font-size: 0.9rem;
    cursor: pointer;
  }
  button:hover { background-color: #B8935A; }
  .error {
    background: rgba(224,102,90,0.12);
    border: 1px solid #E0665A;
    color: #E0665A;
    padding: 10px 12px;
    border-radius: 6px;
    font-size: 0.85rem;
    margin-bottom: 16px;
  }
  .bottom-link { text-align: center; margin-top: 18px; font-size: 0.85rem; }
  .bottom-link a { color: #C9A66B; text-decoration: none; font-weight: 600; }
  .back-link { display: block; text-align: center; margin-bottom: 20px; color: #A9884C; font-size: 0.8rem; text-decoration: none; }
</style>
</head>
<body>
  <div class="auth-box">
    <a href="<?php echo htmlspecialchars($redirect); ?>" class="back-link">&larr; Back</a>
    <h1>Create Account</h1>
    <p class="sub">Sign up to book your appointment</p>

    <form method="POST" action="signup.php" novalidate>
      <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect); ?>">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">

      <div class="field-group">
        <label for="name">Full Name</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($nameValue); ?>" class="<?php echo isset($errors['name']) ? 'invalid' : ''; ?>">
        <?php if (isset($errors['name'])) { ?>
          <span class="field-error"><?php echo htmlspecialchars($errors['name']); ?></span>
        <?php } ?>
      </div>

      <div class="field-group">
        <label for="phone">Phone Number</label>
        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($phoneValue); ?>" class="<?php echo isset($errors['phone']) ? 'invalid' : ''; ?>" placeholder="98765 43210">
        <?php if (isset($errors['phone'])) { ?>
          <span class="field-error"><?php echo htmlspecialchars($errors['phone']); ?></span>
        <?php } ?>
      </div>

      <div class="field-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($emailValue); ?>" class="<?php echo isset($errors['email']) ? 'invalid' : ''; ?>">
        <?php if (isset($errors['email'])) { ?>
          <span class="field-error"><?php echo htmlspecialchars($errors['email']); ?></span>
        <?php } ?>
      </div>

      <div class="field-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" class="<?php echo isset($errors['password']) ? 'invalid' : ''; ?>">
        <?php if (isset($errors['password'])) { ?>
          <span class="field-error"><?php echo htmlspecialchars($errors['password']); ?></span>
        <?php } else { ?>
          <span class="hint">At least 8 characters, with a letter and a number.</span>
        <?php } ?>
      </div>

      <button type="submit">Sign Up</button>
    </form>

    <div class="bottom-link">
      Already have an account? <a href="login.php?redirect=<?php echo urlencode($redirect); ?>">Login</a>
    </div>
  </div>
</body>
</html>
