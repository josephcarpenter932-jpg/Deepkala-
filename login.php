<?php
// ---------------------------------------------------------------
// Backend logic: UNCHANGED from your original login.php
// ---------------------------------------------------------------
require 'backend/config.php';
$errors = [];
$emailValue = '';
$redirect = safe_redirect($_GET['redirect'] ?? 'index.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $emailValue = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $redirect = safe_redirect($_POST['redirect'] ?? 'index.php');

    if (!filter_var($emailValue, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'That does not look like a valid email address.';
    }
    if ($password === '') {
        $errors['password'] = 'Please enter your password.';
    }

    if (!$errors) {
        $email = strtolower($emailValue);
        $stmt = mysqli_prepare($conn, 'SELECT id, name, phone, email, password, membership FROM users WHERE email = ? LIMIT 1');
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_phone'] = $user['phone'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['membership'] = $user['membership'];
            header('Location: ' . $redirect);
            exit;
        }
        $errors['general'] = 'No account matches that email/password. Check your details, or sign up.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login — Deepkala Hair Salon</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700&family=Cormorant+Garamond:wght@500;600&display=swap" rel="stylesheet">
<style>
  /* Theme tokens: swap these to restyle the whole page (e.g. light theme) */
  :root {
    --bg: #15130E;
    --panel: #1E1A14;
    --field: #221E17;
    --ink: #F3EEE2;
    --muted: #A9884C;
    --hairline: #7A6C55;
    --accent: #C9A66B;
    --accent-hover: #B8935A;
    --on-accent: #15130E;
    --danger: #E0665A;
  }
  * { box-sizing: border-box; margin: 0; padding: 0; }
  html, body { height: 100%; }
  body {
    font-family: 'Inter', sans-serif;
    background: var(--bg);
    color: var(--ink);
    display: flex;
    flex-direction: column;
    min-height: 100vh;
  }

  /* ===== Page shell: two columns (form | visual) + footer ===== */
  .page { flex: 1; display: grid; grid-template-columns: 1fr 1fr; }

  /* ===== Left: form column ===== */
  .form-col { display: flex; align-items: center; justify-content: center; padding: 40px 24px; }
  .form-wrap { width: 100%; max-width: 330px; }

  h1 {
    font-family: 'Playfair Display', serif;
    font-size: 1.7rem;
    line-height: 1.25;
    font-weight: 700;
    margin-bottom: 10px;
  }
  .signup-line { font-size: 0.78rem; color: var(--muted); margin-bottom: 28px; }
  .signup-line a, .foot-note a { color: var(--accent); text-decoration: none; font-weight: 600; }
  .signup-line a:hover, .foot-note a:hover { text-decoration: underline; }

  .error {
    background: rgba(224,102,90,0.12);
    border: 1px solid var(--danger);
    color: var(--danger);
    padding: 10px 12px;
    border-radius: 8px;
    font-size: 0.82rem;
    margin-bottom: 16px;
  }

  .field-group { margin-bottom: 12px; position: relative; }
  .field-group input {
    width: 100%;
    padding: 14px 16px;
    border-radius: 10px;
    border: 1px solid transparent;
    background: var(--field);
    color: var(--ink);
    font-size: 0.9rem;
    font-family: inherit;
    transition: border-color .2s;
  }
  .field-group input::placeholder { color: var(--hairline); }
  .field-group input:focus { outline: none; border-color: var(--accent); }
  .field-group input.invalid { border-color: var(--danger); }
  .field-group input.has-toggle { padding-right: 56px; }
  .field-error { display: block; font-size: 0.74rem; color: var(--danger); margin-top: 5px; }

  .pw-toggle {
    position: absolute; right: 8px; top: 7px;
    background: none; border: none; width: auto; padding: 6px 8px;
    color: var(--muted); font-size: 0.72rem; font-weight: 600; cursor: pointer; border-radius: 6px;
  }
  .pw-toggle:hover { color: var(--accent); background: none; }

  /* Primary button (equivalent of "Continue with email") */
  .btn-primary {
    width: 100%; margin-top: 8px;
    padding: 14px;
    background: var(--accent);
    color: var(--on-accent);
    border: none; border-radius: 10px;
    font-weight: 700; font-size: 0.88rem; font-family: inherit;
    cursor: pointer; transition: background .2s;
  }
  .btn-primary:hover { background: var(--accent-hover); }

  /* "Or" divider */
  .divider { display: flex; align-items: center; gap: 12px; margin: 18px 0; font-size: 0.72rem; color: var(--hairline); }
  .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: var(--hairline); opacity: .6; }

  /* Secondary outlined buttons (equivalent of QR / Google rows) */
  .btn-secondary {
    display: flex; align-items: center; justify-content: center; gap: 10px;
    width: 100%; margin-bottom: 10px;
    padding: 13px;
    background: var(--panel);
    border: 1px solid var(--hairline);
    border-radius: 10px;
    color: var(--ink); text-decoration: none;
    font-size: 0.82rem; font-weight: 600;
    transition: border-color .2s, color .2s;
  }
  .btn-secondary:hover { border-color: var(--accent); color: var(--accent); }
  button.btn-secondary { font-family: inherit; cursor: pointer; }
  .btn-secondary svg { flex-shrink: 0; }
  .demo-note { font-size: 0.74rem; color: var(--muted); text-align: center; margin: 2px 0 4px; }

  .foot-note { text-align: center; margin-top: 22px; font-size: 0.75rem; color: var(--hairline); }

  /* ===== Right: visual column ===== */
  .visual-col { display: flex; align-items: center; justify-content: center; padding: 24px; overflow: hidden;
    background: radial-gradient(ellipse at 60% 45%, rgba(201,166,107,0.10), transparent 65%); }
  .visual-col svg { width: 100%; max-width: 500px; height: auto; }

  /* ===== Footer strip ===== */
  .page-footer {
    display: flex; align-items: center; gap: 8px;
    padding: 18px 28px;
    font-size: 0.7rem; color: var(--hairline);
  }
  .page-footer a { color: var(--hairline); text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
  .page-footer a:hover { color: var(--accent); }

  /* ===== Responsive: visual drops away on tablet/mobile ===== */
  @media (max-width: 860px) {
    .page { grid-template-columns: 1fr; }
    .visual-col { display: none; }
    .page-footer { justify-content: center; }
  }
</style>
</head>
<body>

<div class="page">

  <!-- ============ LEFT: FORM AREA ============ -->
  <main class="form-col">
    <div class="form-wrap">
      <h1>Welcome to Deepkala</h1>
      <p class="signup-line">Don't have an account? <a href="signup.php?redirect=<?php echo urlencode($redirect); ?>">Sign up</a></p>

      <?php if (isset($errors['general'])) { ?>
        <div class="error"><?php echo htmlspecialchars($errors['general']); ?></div>
      <?php } ?>

      <form method="POST" action="login.php" novalidate>
        <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect); ?>">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">

        <div class="field-group">
          <input type="email" id="email" name="email" placeholder="Email" aria-label="Email" autocomplete="email"
                 value="<?php echo htmlspecialchars($emailValue); ?>"
                 class="<?php echo isset($errors['email']) ? 'invalid' : ''; ?>">
          <?php if (isset($errors['email'])) { ?>
            <span class="field-error"><?php echo htmlspecialchars($errors['email']); ?></span>
          <?php } ?>
        </div>

        <div class="field-group">
          <input type="password" id="password" name="password" placeholder="Password" aria-label="Password" autocomplete="current-password"
                 class="has-toggle <?php echo isset($errors['password']) ? 'invalid' : ''; ?>">
          <button type="button" class="pw-toggle" id="pwToggle" aria-label="Show password">Show</button>
          <?php if (isset($errors['password'])) { ?>
            <span class="field-error"><?php echo htmlspecialchars($errors['password']); ?></span>
          <?php } ?>
        </div>

        <button type="submit" class="btn-primary">Log in</button>
      </form>

      <div class="divider">Or</div>

      <!-- Social sign-in: DEMO ONLY. Buttons are not wired to any provider.
           To make them real later, replace the click handler with an OAuth redirect. -->
      <button type="button" class="btn-secondary" data-demo="Google">
        <svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true"><path fill="#4285F4" d="M23.52 12.27c0-.85-.08-1.67-.22-2.45H12v4.64h6.46a5.52 5.52 0 0 1-2.4 3.62v3h3.88c2.27-2.09 3.58-5.17 3.58-8.81z"/><path fill="#34A853" d="M12 24c3.24 0 5.96-1.07 7.94-2.91l-3.88-3a7.2 7.2 0 0 1-10.7-3.78H1.4v3.1A12 12 0 0 0 12 24z"/><path fill="#FBBC05" d="M5.36 14.31a7.2 7.2 0 0 1 0-4.62v-3.1H1.4a12 12 0 0 0 0 10.82l3.96-3.1z"/><path fill="#EA4335" d="M12 4.75c1.76 0 3.34.6 4.58 1.8l3.43-3.43A12 12 0 0 0 1.4 6.59l3.96 3.1A7.2 7.2 0 0 1 12 4.75z"/></svg>
        Continue with Google
      </button>
      <button type="button" class="btn-secondary" data-demo="Apple">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.152 6.896c-.948 0-2.415-1.078-3.96-1.04-2.04.027-3.91 1.183-4.961 3.014-2.117 3.675-.546 9.103 1.519 12.09 1.013 1.454 2.208 3.09 3.792 3.039 1.52-.065 2.09-.987 3.935-.987 1.831 0 2.35.987 3.96.948 1.637-.026 2.676-1.48 3.676-2.948 1.156-1.688 1.636-3.325 1.662-3.415-.039-.013-3.182-1.221-3.22-4.857-.026-3.04 2.48-4.494 2.597-4.559-1.429-2.09-3.623-2.324-4.39-2.376-2-.156-3.675 1.09-4.61 1.09zM15.53 3.83c.843-1.012 1.4-2.427 1.245-3.83-1.207.052-2.662.805-3.532 1.818-.78.896-1.454 2.338-1.273 3.714 1.338.104 2.715-.688 3.559-1.701"/></svg>
        Continue with Apple
      </button>
      <p class="demo-note" id="demoNote" role="status" hidden></p>

      <p class="foot-note">Interested in perks? <a href="membership.php">View membership plans</a></p>
    </div>
  </main>

  <!-- ============ RIGHT: VISUAL ELEMENT ============ -->
  <!-- PROJECT-SPECIFIC VISUAL SUBSTITUTION:
       Reference had a tilted bank card + floating metal coins.
       Here: a static salon scene. Arched gold-framed mirror, leather barber chair,
       pendant lamp glow, striped barber pole, product shelf and no emblem. -->
  <aside class="visual-col" aria-hidden="true">
    <svg viewBox="0 0 480 550" xmlns="http://www.w3.org/2000/svg">
      <defs>
        <linearGradient id="gold" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#E6C98D"/><stop offset="1" stop-color="#A9884C"/></linearGradient>
        <linearGradient id="chrome" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#7A6C55"/><stop offset=".45" stop-color="#F0DDB0"/><stop offset="1" stop-color="#8d7a57"/></linearGradient>
        <linearGradient id="leather" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#7a3630"/><stop offset="1" stop-color="#3b1916"/></linearGradient>
        <linearGradient id="glass" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#2a241a"/><stop offset="1" stop-color="#14110c"/></linearGradient>
        <linearGradient id="poleGloss" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#000" stop-opacity=".35"/><stop offset=".3" stop-color="#fff" stop-opacity=".28"/><stop offset=".6" stop-color="#fff" stop-opacity="0"/><stop offset="1" stop-color="#000" stop-opacity=".45"/></linearGradient>
        <linearGradient id="floorFade" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#C9A66B" stop-opacity=".14"/><stop offset="1" stop-color="#C9A66B" stop-opacity="0"/></linearGradient>
        <linearGradient id="leaf" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#6f8250"/><stop offset="1" stop-color="#3f4e2d"/></linearGradient>
        <radialGradient id="lampGlow" cx=".5" cy=".5" r=".5"><stop offset="0" stop-color="#F6DDA4" stop-opacity=".6"/><stop offset="1" stop-color="#F6DDA4" stop-opacity="0"/></radialGradient>
        <radialGradient id="bgGlow" cx=".5" cy=".45" r=".6"><stop offset="0" stop-color="#C9A66B" stop-opacity=".16"/><stop offset="1" stop-color="#C9A66B" stop-opacity="0"/></radialGradient>
        <pattern id="pole" width="30" height="30" patternUnits="userSpaceOnUse" patternTransform="rotate(-38)">
          <rect width="10" height="30" fill="#C9A66B"/><rect x="10" width="10" height="30" fill="#F3EEE2"/><rect x="20" width="10" height="30" fill="#8a3a33"/>
        </pattern>
        <pattern id="tile" width="48" height="24" patternUnits="userSpaceOnUse">
          <path d="M24 0 L48 12 L24 24 L0 12Z" fill="none" stroke="#C9A66B" stroke-width="1" opacity=".22"/>
        </pattern>
        <clipPath id="archClip"><path d="M122 470 V190 A118 118 0 0 1 358 190 V470 Z"/></clipPath>
        <filter id="soft" x="-30%" y="-30%" width="160%" height="160%"><feDropShadow dx="0" dy="10" stdDeviation="10" flood-color="#000" flood-opacity=".5"/></filter>
      </defs>

      <ellipse cx="240" cy="270" rx="250" ry="270" fill="url(#bgGlow)"/>

      <!-- Floor with diamond tiles -->
      <rect x="0" y="470" width="480" height="80" fill="url(#tile)"/>
      <rect x="0" y="470" width="480" height="80" fill="url(#floorFade)"/>
      <line x1="16" y1="470" x2="464" y2="470" stroke="#7A6C55" stroke-width="1" opacity=".6"/>

      <!-- Arched mirror with double frame -->
      <path d="M98 470 V190 A142 142 0 0 1 382 190 V470" fill="none" stroke="#7A6C55" stroke-width="1" opacity=".7"/>
      <g filter="url(#soft)">
        <path d="M110 470 V190 A130 130 0 0 1 370 190 V470 Z" fill="url(#glass)" stroke="url(#gold)" stroke-width="3.5"/>
      </g>
      <path d="M122 470 V190 A118 118 0 0 1 358 190 V470 Z" fill="none" stroke="#7A6C55" stroke-width="1"/>
      <g clip-path="url(#archClip)">
        <circle cx="240" cy="165" r="165" fill="url(#lampGlow)"/>
        <polygon points="150,40 195,40 120,470 75,470" fill="#fff" opacity=".05"/>
        <polygon points="215,40 240,40 170,470 145,470" fill="#fff" opacity=".03"/>
        <polygon points="300,40 312,40 250,470 238,470" fill="#fff" opacity=".02"/>
      </g>
      <path d="M240 40 L249 51 L240 62 L231 51Z" fill="url(#gold)"/>

      <!-- Pendant lamp -->
      <line x1="240" y1="62" x2="240" y2="124" stroke="url(#gold)" stroke-width="1.5"/>
      <path d="M214 148 Q240 106 266 148 Z" fill="url(#gold)"/>
      <rect x="212" y="147" width="56" height="4" rx="2" fill="#F0DDB0"/>
      <ellipse cx="240" cy="154" rx="12" ry="5" fill="#FFF0C8"/>

      <g fill="#F0DDB0">
        <path transform="translate(305 118) scale(.85)" d="M0 -9 L2.2 -2.2 L9 0 L2.2 2.2 L0 9 L-2.2 2.2 L-9 0 L-2.2 -2.2Z" opacity=".9"/>
        <path transform="translate(178 100) scale(.6)" d="M0 -9 L2.2 -2.2 L9 0 L2.2 2.2 L0 9 L-2.2 2.2 L-9 0 L-2.2 -2.2Z" opacity=".7"/>
        <path transform="translate(330 196) scale(.5)" d="M0 -9 L2.2 -2.2 L9 0 L2.2 2.2 L0 9 L-2.2 2.2 L-9 0 L-2.2 -2.2Z" opacity=".6"/>
      </g>

      <!-- Wall sconces (symmetrical pair, kept small) -->
      <g transform="translate(52 168) scale(.62)">
        <circle cx="0" cy="20" r="62" fill="url(#lampGlow)" opacity=".5"/>
        <rect x="-5" y="-6" width="10" height="34" rx="4" fill="url(#gold)"/>
        <path d="M-15 24 Q0 -2 15 24 Z" fill="url(#gold)"/>
        <ellipse cx="0" cy="27" rx="9" ry="3.5" fill="#FFF0C8"/>
      </g>
      <g transform="translate(428 168) scale(.62)">
        <circle cx="0" cy="20" r="62" fill="url(#lampGlow)" opacity=".5"/>
        <rect x="-5" y="-6" width="10" height="34" rx="4" fill="url(#gold)"/>
        <path d="M-15 24 Q0 -2 15 24 Z" fill="url(#gold)"/>
        <ellipse cx="0" cy="27" rx="9" ry="3.5" fill="#FFF0C8"/>
      </g>

      <!-- Potted plant (left) -->
      <g transform="translate(52 470)">
        <path d="M0 -66 C-3 -120 -5 -170 -4 -226" fill="none" stroke="#4b5a36" stroke-width="3" stroke-linecap="round"/>
        <g fill="url(#leaf)" stroke="#34412a" stroke-width="1">
          <ellipse cx="-22" cy="-108" rx="14" ry="28" transform="rotate(-38 -22 -108)"/>
          <ellipse cx="20" cy="-132" rx="14" ry="28" transform="rotate(36 20 -132)"/>
          <ellipse cx="-24" cy="-162" rx="14" ry="29" transform="rotate(-32 -24 -162)"/>
          <ellipse cx="22" cy="-186" rx="13" ry="28" transform="rotate(34 22 -186)"/>
          <ellipse cx="-4" cy="-226" rx="13" ry="32"/>
        </g>
        <path d="M-24 -66 L24 -66 L17 0 L-17 0 Z" fill="#2b251a" stroke="#A9884C" stroke-width="1.2"/>
        <rect x="-27" y="-72" width="54" height="9" rx="3" fill="url(#gold)"/>
      </g>

      <!-- Barber chair -->
      <ellipse cx="240" cy="476" rx="104" ry="10" fill="#000" opacity=".4"/>
      <g>
        <ellipse cx="240" cy="466" rx="54" ry="9" fill="url(#chrome)"/>
        <ellipse cx="240" cy="463" rx="54" ry="8" fill="#241f16" stroke="#A9884C" stroke-width="1.2"/>
        <rect x="231" y="420" width="18" height="44" fill="url(#chrome)"/>
        <rect x="235" y="398" width="10" height="24" fill="#E6C98D"/>
        <rect x="212" y="438" width="56" height="8" rx="4" fill="url(#chrome)"/>
        <rect x="170" y="334" width="28" height="12" rx="6" fill="url(#chrome)"/>
        <rect x="178" y="344" width="9" height="28" rx="3" fill="url(#chrome)"/>
        <rect x="282" y="334" width="28" height="12" rx="6" fill="url(#chrome)"/>
        <rect x="293" y="344" width="9" height="28" rx="3" fill="url(#chrome)"/>
        <rect x="188" y="368" width="104" height="34" rx="14" fill="url(#leather)" stroke="#A9884C" stroke-width="1"/>
        <rect x="202" y="262" width="76" height="116" rx="24" fill="url(#leather)" stroke="#A9884C" stroke-width="1"/>
        <rect x="233" y="266" width="14" height="10" fill="url(#chrome)"/>
        <rect x="217" y="232" width="46" height="38" rx="14" fill="url(#leather)" stroke="#A9884C" stroke-width="1"/>
        <path d="M210 292 Q207 332 214 368" fill="none" stroke="#fff" stroke-width="2" opacity=".14" stroke-linecap="round"/>
        <g fill="#E6C98D">
          <circle cx="240" cy="290" r="2.4"/><circle cx="222" cy="312" r="2.4"/><circle cx="258" cy="312" r="2.4"/>
          <circle cx="240" cy="334" r="2.4"/><circle cx="240" cy="312" r="2.4"/>
        </g>
      </g>

      <!-- Barber pole (right) -->
      <g filter="url(#soft)" transform="translate(18 0)">
        <rect x="406" y="438" width="8" height="32" fill="url(#gold)"/>
        <rect x="398" y="284" width="24" height="146" rx="10" fill="url(#pole)" stroke="#A9884C" stroke-width="1"/>
        <rect x="398" y="284" width="24" height="146" rx="10" fill="url(#poleGloss)"/>
        <rect x="394" y="272" width="32" height="14" rx="4" fill="url(#gold)"/>
        <rect x="394" y="426" width="32" height="14" rx="4" fill="url(#gold)"/>
        <circle cx="410" cy="266" r="7" fill="url(#gold)"/>
      </g>


      <!-- Brand name: light, wide-spaced serif in gold, flanked by thin rules -->
      <line x1="96" y1="510" x2="138" y2="510" stroke="url(#gold)" stroke-width="1" opacity=".7"/>
      <line x1="342" y1="510" x2="384" y2="510" stroke="url(#gold)" stroke-width="1" opacity=".7"/>
      <text x="245" y="518" text-anchor="middle" font-family="'Cormorant Garamond', 'Playfair Display', Georgia, serif" font-size="26" font-weight="600" letter-spacing="9" fill="url(#gold)">DEEPKALA</text>
    </svg>
  </aside>

</div>

<!-- ============ FOOTER ============ -->
<footer class="page-footer">
  <span>Deepkala Hair Salon</span>
  <span>·</span>
  <a href="<?php echo htmlspecialchars($redirect); ?>">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15 15 0 0 1 0 20M12 2a15 15 0 0 0 0 20"/></svg>
    Back to site
  </a>
</footer>

<script>
  // Demo-only social buttons: no real sign-in, just a notice
  (function () {
    var note = document.getElementById('demoNote');
    document.querySelectorAll('[data-demo]').forEach(function (b) {
      b.addEventListener('click', function () {
        note.textContent = b.getAttribute('data-demo') + ' sign-in is a demo for now and is not connected yet.';
        note.hidden = false;
      });
    });
  })();

  // Show/hide password toggle
  (function () {
    var btn = document.getElementById('pwToggle');
    var input = document.getElementById('password');
    btn.addEventListener('click', function () {
      var show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      btn.textContent = show ? 'Hide' : 'Show';
      btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
    });
  })();
</script>
</body>
</html>
