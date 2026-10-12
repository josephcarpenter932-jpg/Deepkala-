<?php
require 'backend/config.php';
date_default_timezone_set('Asia/Kolkata');
$isLoggedIn = isset($_SESSION['user_id']);
if (!$isLoggedIn) {
    header('Location: login.php?redirect=profile.php');
    exit;
}
$uid = (int) $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update_profile') {
    header('Content-Type: application/json');
    require_csrf();
    $name = trim(preg_replace('/\s+/', ' ', $_POST['name'] ?? ''));
    $phone = trim($_POST['phone'] ?? '');
    if (!preg_match('/^[A-Za-z ]{2,60}$/', $name)) {
        echo json_encode(['ok' => false, 'error' => 'Name should be 2-60 characters, letters and spaces only.']); exit;
    }
    if (!preg_match('/^(\+91[\-\s]?)?[6-9]\d{9}$/', $phone)) {
        echo json_encode(['ok' => false, 'error' => 'Enter a valid 10-digit Indian mobile number.']); exit;
    }
    $stmt = mysqli_prepare($conn, 'UPDATE users SET name = ?, phone = ? WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'ssi', $name, $phone, $uid);
    if (!mysqli_stmt_execute($stmt)) {
        echo json_encode(['ok' => false, 'error' => 'Could not save your changes.']); exit;
    }
    $_SESSION['user_name'] = $name;
    $_SESSION['user_phone'] = $phone;
    echo json_encode(['ok' => true]); exit;
}

$stmt = mysqli_prepare($conn, 'SELECT name, phone, email, membership FROM users WHERE id = ? LIMIT 1');
mysqli_stmt_bind_param($stmt, 'i', $uid);
mysqli_stmt_execute($stmt);
$u = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$u) {
    session_unset(); session_destroy();
    header('Location: login.php?redirect=profile.php'); exit;
}
$_SESSION['membership'] = $u['membership'];
$appts = [];
$stmt = mysqli_prepare($conn, 'SELECT a.*, b.name AS barber_name FROM appointments a LEFT JOIN barbers b ON a.barber_id = b.id WHERE a.user_id = ? ORDER BY a.appointment_date DESC, a.id DESC');
mysqli_stmt_bind_param($stmt, 'i', $uid);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($r = mysqli_fetch_assoc($res)) {
    $ts = strtotime($r['appointment_date'] . ' ' . $r['appointment_time']);
    if ($ts === false) $ts = strtotime($r['appointment_date']);
    $appts[] = ['service' => explode(' — ', $r['service'])[0], 'date' => $r['appointment_date'], 'time' => $r['appointment_time'], 'ts' => $ts * 1000, 'barber' => $r['barber_name'] ?? '—', 'method' => $r['payment_method'], 'amount' => (int) $r['amount']];
}
$profileData = ['user' => ['name' => $u['name'], 'email' => $u['email'], 'phone' => $u['phone']], 'tier' => $u['membership'], 'appts' => $appts, 'updated' => isset($_GET['updated'])];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Profile — Deepkala Hair Salon, Ahmedabad</title>

  <!-- Favicon SVG Monogram -->
  <link rel="icon" type="image/png" href="assets/deepkala-emblem.png">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <!-- FONT-FAMILY UPDATED: Fraunces + Karla → Playfair Display + Inter -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300..800&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">

  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    :root {
      --bg-cream: #15130E;
      --text-ink: #F3EEE2;
      --rose-accent: #C9A66B;
      --rose-accent-hover: #B8935A;
      --rose-deep: #0A0806;
      --bronze-taupe: #A9884C;
      --card-white: #1E1A14;
      --card-cream: #221E17;
      --border-hairline: #7A6C55;
      --whatsapp-green: #25D366;
      --gold-glow: #FFD666;
    }

    html {
      scroll-behavior: smooth;
      font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif; /* ← FONT-FAMILY UPDATED */
      background-color: var(--bg-cream);
      color: var(--text-ink);
      line-height: 1.6;
      overflow-x: hidden;
      width: 100%;
    }

    body { min-height: 100vh; display: flex; flex-direction: column; width: 100%; overflow-x: hidden; }
    img, svg { max-width: 100%; height: auto; }
    a { color: inherit; text-decoration: none; }

    .container { width: 100%; max-width: 1200px; margin: 0 auto; padding: 0 24px; }
    @media (max-width: 768px) { .container { padding: 0 16px; } }

    .font-serif { font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; /* ← FONT-FAMILY UPDATED */ }
    h1.font-serif { font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; /* ← FONT-FAMILY UPDATED */ font-variation-settings: "opsz" 144, "SOFT" 0, "WONK" 0; }
    .font-sans { font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif; /* ← FONT-FAMILY UPDATED */ }

    /* Header (shared) */
    .site-header { position: sticky; top: 0; z-index: 100; background-color: rgba(21, 19, 14, 0.95); backdrop-filter: blur(8px); border-bottom: 1px solid var(--border-hairline); transition: padding 0.3s ease, box-shadow 0.3s ease; }
    .header-inner { display: flex; align-items: center; justify-content: space-between; height: 76px; }
    .brand-logo { display: flex; align-items: center; gap: 12px; }
    .logo-text-title { font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; /* ← FONT-FAMILY UPDATED */ font-variation-settings: "opsz" 144, "SOFT" 0, "WONK" 0; font-size: 1.25rem; font-weight: 700; letter-spacing: 0.06em; color: var(--text-ink); line-height: 1.1; }
    .logo-text-sub { font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif; /* ← FONT-FAMILY UPDATED */ font-size: 0.65rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.12em; color: var(--bronze-taupe); }
    .nav-links { display: flex; align-items: center; gap: 28px; list-style: none; }
    .nav-link { font-size: 0.825rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-ink); padding: 6px 0; position: relative; transition: color 0.2s ease; }
    .nav-link:hover, .nav-link.active { color: var(--rose-accent); }
    .nav-link.active::after { content: ''; position: absolute; bottom: 0; left: 0; width: 100%; height: 2px; background-color: var(--rose-accent); }
    .header-book-btn { display: inline-flex; align-items: center; justify-content: center; padding: 9px 20px; font-size: 0.8rem; font-weight: 600; letter-spacing: 0.06em; text-transform: uppercase; background-color: var(--rose-accent); color: #15130E; border: 1px solid var(--rose-accent); border-radius: 4px; transition: all 0.2s ease; white-space: nowrap; }
    .header-book-btn:hover { background-color: var(--rose-accent-hover); border-color: var(--rose-accent-hover); color: #15130E; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(10, 8, 6, 0.35); }
    .mobile-toggle { display: none; background: none; border: none; cursor: pointer; padding: 8px; color: var(--text-ink); }

    /* Page Hero */
    .page-hero { position: relative; padding: 60px 0 40px 0; text-align: center; border-bottom: 1px solid var(--border-hairline); background-color: var(--bg-cream); overflow: hidden; isolation: isolate; }
    .page-hero::before {
      content: "";
      position: absolute;
      inset: 0;
      background-color: var(--bg-cream);
      background-image:
        radial-gradient(circle at 25% 20%, rgba(201,166,107,0.12), transparent 55%),
        radial-gradient(circle at 80% 85%, rgba(201,166,107,0.07), transparent 50%),
        repeating-linear-gradient(135deg, rgba(243,238,226,0.04) 0px, rgba(243,238,226,0.04) 1px, transparent 1px, transparent 9px),
        repeating-linear-gradient(45deg, rgba(0,0,0,0.22) 0px, rgba(0,0,0,0.22) 1px, transparent 1px, transparent 9px),
        url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='180' height='180'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='2' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.5'/%3E%3C/svg%3E");
      background-size: auto, auto, auto, auto, 180px 180px;
      background-repeat: no-repeat, no-repeat, repeat, repeat, repeat;
      z-index: -2;
    }
    .page-hero::after { content: ""; position: absolute; inset: 0; background: linear-gradient(160deg, rgba(10,8,6,0.82) 0%, rgba(201,166,107,0.38) 55%, rgba(10,8,6,0.88) 100%); z-index: -1; }
    .page-hero > .container { position: relative; z-index: 1; }
    .page-hero h1 { font-size: clamp(2.2rem, 4vw, 3.2rem); font-weight: 700; color: var(--text-ink); margin-bottom: 12px; }
    .page-hero p { font-size: 1rem; color: #B3AA98; max-width: 620px; margin: 0 auto; }

    .seal-motif { display: inline-flex; align-items: center; justify-content: center; position: relative; width: 48px; height: 48px; margin-bottom: 12px; }
    .seal-motif .ring-outer { position: absolute; inset: 0; border: 1px solid var(--bronze-taupe); border-radius: 50%; opacity: 0.7; }
    .seal-motif .ring-inner { position: absolute; inset: 5px; border: 1px dashed var(--rose-deep); border-radius: 50%; opacity: 0.8; }
    .seal-motif .seal-text { font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; /* ← FONT-FAMILY UPDATED */ font-weight: 700; font-size: 1.1rem; color: var(--text-ink); }

    .eyebrow-tag { display: inline-block; background-color: var(--text-ink); color: var(--bg-cream); padding: 6px 14px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.12em; }
    .category-header { display: flex; align-items: center; gap: 16px; margin-top: 48px; margin-bottom: 16px; }
    .category-header .hairline { flex: 1; height: 1px; background-color: var(--border-hairline); }

    /* Footer */
    .site-footer {
      position: relative;
      margin: 20px 12px 12px;
      color: var(--text-ink, #F3EEE2);
    }

    /* Footer panel in the site's dark palette */
    .footer-panel {
      position: relative;
      overflow: hidden;
      background: #1A1710;
      border: 1px solid var(--border-hairline, #7A6C55);
      border-radius: 28px;
      padding: 40px clamp(14px, 4.5vw, 64px) 0;
    }
    .footer-card {
      max-width: 1560px;
      margin: 0 auto;
      padding: 44px clamp(56px, 6vw, 110px) 28px;
      background: var(--card-cream, #221E17);
      border: 1px solid var(--border-hairline, #7A6C55);
      border-radius: 22px;
      box-shadow: 0 28px 60px -26px rgba(0,0,0,0.65);
    }
    .footer-top {
      display: flex;
      justify-content: space-between;
      gap: 48px;
    }
    .footer-brand { flex: 1 1 320px; max-width: 470px; }
    .site-footer .brand-logo { gap: 14px; }
    .site-footer .brand-logo img { width: 52px; height: 52px; }
    .site-footer .logo-text-title { font-size: 1.55rem; }
    .site-footer .logo-text-sub { font-size: 0.74rem; }
    .footer-brand-desc {
      margin-top: 18px;
      font-size: 1.05rem;
      line-height: 1.65;
      color: #B3AA98;
    }
    .footer-social { display: flex; gap: 20px; margin-top: 18px; }
    .footer-social svg { width: 27px; height: 27px; }
    .footer-social a {
      display: inline-flex;
      color: var(--text-ink, #F3EEE2);
      transition: color 0.2s ease, transform 0.2s ease;
    }
    .footer-social a:hover { color: var(--rose-accent, #C9A66B); transform: translateY(-2px); }

    .footer-cols { display: flex; flex: 0 1 auto; gap: clamp(36px, 6vw, 120px); }
    .footer-col-title {
      margin-bottom: 16px;
      font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif; /* ← FONT-FAMILY UPDATED */
      font-size: 1.02rem;
      font-weight: 700;
      white-space: nowrap;
      color: var(--text-ink, #F3EEE2);
    }
    .footer-list { list-style: none; display: flex; flex-direction: column; gap: 16px; }
    .footer-list li, .footer-list a { font-size: 0.92rem; line-height: 1.45; color: #B3AA98; }
    .footer-list a { white-space: nowrap; transition: color 0.2s ease; }
    .footer-list a:hover { color: var(--rose-accent, #C9A66B); }
    .footer-col-info { min-width: 0; max-width: 250px; }

    .footer-bottom {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: space-between;
      gap: 12px 24px;
      margin-top: 32px;
      padding-top: 20px;
      border-top: 1px solid var(--border-hairline, #7A6C55);
      font-size: 0.85rem;
      color: #9C9284;
    }
    .footer-legal { display: flex; flex-wrap: wrap; gap: 8px 24px; }
    .footer-legal a { color: #9C9284; text-decoration: none; transition: color 0.2s ease; }
    .footer-legal a:hover { color: var(--rose-accent, #C9A66B); }

    .site-footer a:focus-visible { outline: 2px solid var(--rose-accent, #C9A66B); outline-offset: 3px; border-radius: 4px; }

    /* Oversized brand name that fades into the panel */
    .footer-watermark {
      margin-top: 12px;
      height: 0.62em;
      overflow: hidden;
      font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif; /* ← FONT-FAMILY UPDATED */
      font-size: clamp(48px, 13vw, 220px);
      font-weight: 800;
      line-height: 1;
      letter-spacing: 0.04em;
      text-align: center;
      white-space: nowrap;
      color: rgba(201,166,107,0.08);
      background: linear-gradient(180deg, rgba(201,166,107,0.20) 0%, rgba(201,166,107,0.02) 100%);
      -webkit-background-clip: text;
      background-clip: text;
      -webkit-text-fill-color: transparent;
      user-select: none;
      pointer-events: none;
    }

    @media (min-width: 1600px) {
      .site-footer .brand-logo img { width: 60px; height: 60px; }
      .site-footer .logo-text-title { font-size: 1.95rem; }
      .footer-brand { max-width: 540px; }
      .footer-brand-desc { font-size: 1.18rem; }
      .footer-col-title { font-size: 1.12rem; }
      .footer-list li, .footer-list a { font-size: 1.02rem; }
      .footer-col-info { max-width: 290px; }
      .footer-social svg { width: 31px; height: 31px; }
      .footer-bottom { font-size: 0.92rem; }
    }
    @media (max-width: 1180px) {
      .footer-card { padding: 32px 28px 20px; }
      .footer-top { flex-direction: column; gap: 44px; }
      .footer-brand { flex: none; max-width: 520px; }
    }
    @media (max-width: 640px) {
      .footer-panel { border-radius: 22px; padding-top: 18px; }
      .footer-card { border-radius: 18px; padding: 20px 16px 12px; }
      .footer-brand-desc { font-size: 1rem; }
      .footer-top { gap: 24px; }
      .site-footer .brand-logo img { width: 42px; height: 42px; }
      .footer-social { margin-top: 12px; }
      .footer-social svg { width: 22px; height: 22px; }
      .footer-cols { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px 16px; }
      .footer-col-title { margin-bottom: 8px; }
      .footer-list { gap: 8px; }
      .footer-col-info { grid-column: 1 / -1; max-width: none; }
      .footer-list a { white-space: normal; }
      .footer-bottom { flex-direction: column; align-items: flex-start; gap: 8px 16px; margin-top: 16px; padding-top: 12px; }
      .footer-watermark { height: 0.4em; margin-top: 4px; font-size: clamp(30px, 11vw, 220px); }
    }
    @media (max-width: 420px) {
      .footer-watermark { display: none; }
    }

    .whatsapp-float { position: fixed; bottom: max(20px, env(safe-area-inset-bottom, 20px)); right: max(20px, env(safe-area-inset-right, 20px)); width: 52px; height: 52px; background-color: var(--whatsapp-green); color: #15130E; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 16px rgba(37,211,102,0.35); z-index: 999; transition: transform 0.2s ease; }
    .whatsapp-float:hover { transform: scale(1.08) translateY(-2px); }

    .reveal { opacity: 0; transform: translateY(20px); transition: opacity 0.6s cubic-bezier(0.16,1,0.3,1), transform 0.6s cubic-bezier(0.16,1,0.3,1); }
    .reveal.active { opacity: 1; transform: translateY(0); }

    .mobile-menu-cta { display: none; }

    @media (max-width: 860px) {
      .nav-links { display: none; position: absolute; top: 100%; left: 0; right: 0; background-color: var(--bg-cream); border-bottom: 2px solid var(--rose-accent); flex-direction: column; padding: 16px 20px 24px 20px; gap: 8px; box-shadow: 0 12px 28px rgba(0,0,0,0.45); z-index: 999; max-height: calc(100vh - 80px); overflow-y: auto; }
      .nav-links.show { display: flex; }
      .nav-link { display: flex; align-items: center; justify-content: space-between; padding: 12px 14px; font-size: 0.9rem; width: 100%; border-bottom: 1px solid var(--border-hairline); border-radius: 4px; }
      .nav-link:hover, .nav-link.active { background-color: var(--card-cream); }
      .mobile-toggle { display: flex; align-items: center; justify-content: center; min-width: 44px; min-height: 44px; border: 1px solid var(--border-hairline); border-radius: 4px; }
      .mobile-menu-cta { display: block; margin-top: 8px; }
    }

    /* ===== Deepkala Membership & Auth (shared) ===== */
    .dk-auth-slot { display: flex; align-items: center; }
    .dk-login-link { font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; color: var(--text-ink); border: 1px solid var(--border-hairline); padding: 9px 16px; border-radius: 4px; transition: all 0.2s ease; white-space: nowrap; }
    .dk-login-link:hover { border-color: var(--rose-accent); color: var(--rose-accent); }
    .dk-account-pill { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .dk-account-name { font-size: 0.8rem; font-weight: 600; color: var(--text-ink); white-space: nowrap; }
    .dk-member-badge { font-size: 0.6rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; padding: 3px 9px; border-radius: 4px; white-space: nowrap; }
    .dk-badge-silver { background-color: rgba(201,166,107,0.15); color: #C9A66B; border: 1px solid rgba(201,166,107,0.4); }
    .dk-badge-gold { background-color: rgba(255,214,102,0.15); color: #FFD666; border: 1px solid rgba(255,214,102,0.45); }
    .dk-logout-btn { background: none; border: none; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--bronze-taupe); cursor: pointer; padding: 4px; }
    .dk-logout-btn:hover { color: var(--rose-accent); }
    /* Profile entry points: account-pill icon link (desktop) and nav drawer item (mobile) */
    .dk-account-pill { flex-wrap: nowrap; }
    .dk-profile-link { display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; width: 24px; height: 24px; color: var(--rose-accent); transition: color 0.2s ease; }
    .dk-profile-link:hover { color: var(--text-ink); }
    .dk-nav-profile { display: none; }
    @media (max-width: 860px) { .dk-nav-profile { display: block; } }
    .nav-member-badge { margin-left: 8px; font-size: 0.55rem !important; padding: 2px 7px !important; vertical-align: middle; }
    @media (max-width: 860px) { .dk-auth-slot { display: none; } }
  </style>
  <style>
    /* ===== Profile page (pf-) ===== */
    [hidden] { display: none !important; }

    /* Shared price treatment (same as other pages) */
    .dk-price-old { text-decoration: line-through; color: #8A8171; font-size: 0.78em; font-weight: 500; margin-right: 2px; }
    .dk-price-new { color: var(--rose-accent); font-weight: 700; }

    .pf-wrap { padding-bottom: 72px; }

    /* Buttons */
    .pf-btn {
      display: inline-flex; align-items: center; justify-content: center; gap: 8px;
      padding: 10px 20px; min-height: 42px;
      font-family: inherit; font-size: 0.8rem; font-weight: 600; letter-spacing: 0.06em; text-transform: uppercase;
      background-color: var(--rose-accent); color: #15130E;
      border: 1px solid var(--rose-accent); border-radius: 4px;
      cursor: pointer; white-space: nowrap; transition: all 0.2s ease;
    }
    .pf-btn:hover { background-color: var(--rose-accent-hover); border-color: var(--rose-accent-hover); }
    .pf-btn-outline { background-color: transparent; color: var(--text-ink); border-color: var(--border-hairline); }
    .pf-btn-outline:hover { background-color: transparent; border-color: var(--rose-accent); color: var(--rose-accent); }
    .pf-btn-block { width: 100%; }
    .pf-link { font-size: 0.75rem; font-weight: 600; letter-spacing: 0.06em; text-transform: uppercase; color: var(--rose-accent); transition: color 0.2s ease; }
    .pf-link:hover { color: var(--text-ink); }

    /* Notice banner (mirrors .member-status-bar) */
    .pf-notice { margin-top: 32px; padding: 14px 20px; background-color: var(--card-cream); border: 1px solid var(--rose-accent); color: #E4DCC8; font-size: 0.9rem; }

    /* Signed-out gate */
    .pf-gate { max-width: 560px; margin: 56px auto 0; padding: 44px 32px; text-align: center; background-color: var(--card-white); border: 1px solid var(--border-hairline); }
    .pf-gate h2 { font-size: 1.6rem; font-weight: 700; margin-bottom: 10px; }
    .pf-gate p { color: #B3AA98; font-size: 0.95rem; margin-bottom: 26px; }
    .pf-gate-actions { display: flex; justify-content: center; flex-wrap: wrap; gap: 12px; }

    /* Summary card */
    .pf-summary { display: flex; align-items: center; gap: 28px; margin-top: 40px; padding: 32px; background-color: var(--card-white); border: 1px solid var(--border-hairline); }
    .pf-avatar {
      position: relative; flex: 0 0 auto; width: 88px; height: 88px; border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; font-size: 1.9rem; font-weight: 700;
      color: var(--rose-accent); background-color: var(--card-cream); border: 1px solid var(--bronze-taupe);
    }
    .pf-avatar::after { content: ''; position: absolute; inset: 5px; border: 1px dashed var(--bronze-taupe); border-radius: 50%; opacity: 0.6; }
    .pf-identity { flex: 1 1 auto; min-width: 0; }
    .pf-name-row { display: flex; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 10px; }
    .pf-name { font-size: clamp(1.5rem, 3vw, 2rem); font-weight: 700; line-height: 1.2; }
    .pf-contact { list-style: none; display: flex; flex-wrap: wrap; gap: 8px 24px; color: #B3AA98; font-size: 0.9rem; }
    .pf-contact li { display: flex; align-items: center; gap: 8px; min-width: 0; }
    .pf-contact svg { flex-shrink: 0; color: var(--bronze-taupe); }

    /* Stats */
    .pf-stats { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; margin-top: 16px; }
    .pf-stat { padding: 20px; background-color: var(--card-white); border: 1px solid var(--border-hairline); }
    .pf-stat-value { font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; font-size: 1.6rem; font-weight: 700; line-height: 1.25; color: var(--text-ink); }
    .pf-stat-value.is-text { font-size: 1.15rem; padding-top: 5px; }
    .pf-stat-label { margin-top: 6px; font-size: 0.7rem; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase; color: var(--bronze-taupe); }

    /* Two-column layout */
    .pf-grid { display: grid; grid-template-columns: minmax(0, 1fr); column-gap: 40px; }

    /* Cards */
    .pf-card { padding: 24px; background-color: var(--card-white); border: 1px solid var(--border-hairline); }
    .pf-card-title { font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; font-size: 1.2rem; font-weight: 700; margin-bottom: 8px; }
    .pf-card-text { font-size: 0.9rem; color: #B3AA98; margin-bottom: 20px; }
    .pf-empty { text-align: center; padding: 32px 24px; }
    .pf-empty p { color: #B3AA98; font-size: 0.92rem; margin-bottom: 18px; }

    /* Appointments */
    .pf-list { list-style: none; display: flex; flex-direction: column; gap: 12px; }
    .pf-appt {
      display: grid; grid-template-columns: 64px minmax(0, 1fr) auto; align-items: center; gap: 18px;
      padding: 18px 20px; background-color: var(--card-white); border: 1px solid var(--border-hairline);
    }
    .pf-appt.is-upcoming { border-color: var(--rose-accent); }
    .pf-appt-date { text-align: center; padding: 8px 0; background-color: var(--card-cream); border: 1px solid var(--border-hairline); }
    .pf-appt-day { display: block; font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; font-size: 1.5rem; font-weight: 700; line-height: 1.1; color: var(--rose-accent); }
    .pf-appt-mon { display: block; margin-top: 2px; font-size: 0.65rem; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: #B3AA98; }
    .pf-appt-service { font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; font-size: 1.1rem; font-weight: 700; line-height: 1.3; margin-bottom: 2px; }
    .pf-appt-meta { font-size: 0.85rem; color: #B3AA98; }
    .pf-appt-meta.is-faint { font-size: 0.75rem; color: #8A8171; margin-top: 2px; }
    .pf-appt-trail { display: flex; flex-direction: column; align-items: flex-end; gap: 8px; text-align: right; }
    .pf-appt-amount { font-size: 1rem; font-weight: 700; color: var(--text-ink); }
    .pf-appt-actions { grid-column: 1 / -1; padding-top: 14px; border-top: 1px solid rgba(122, 108, 85, 0.45); }
    .pf-more { display: flex; margin: 16px auto 0; }

    /* Status chips (same shape as .dk-member-badge) */
    .pf-chip { display: inline-block; padding: 3px 9px; font-size: 0.6rem; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; border-radius: 4px; white-space: nowrap; }
    .pf-chip-confirmed { background-color: rgba(201, 166, 107, 0.15); color: #C9A66B; border: 1px solid rgba(201, 166, 107, 0.4); }
    .pf-chip-done { background-color: rgba(243, 238, 226, 0.06); color: #9C9284; border: 1px solid rgba(243, 238, 226, 0.18); }
    .pf-chip-expired { background-color: rgba(255, 107, 96, 0.12); color: #FF8A80; border: 1px solid rgba(255, 107, 96, 0.4); }

    /* Membership card */
    .pf-member.is-gold { border-color: var(--gold-glow); box-shadow: 0 10px 30px rgba(255, 214, 102, 0.08); }
    .pf-member-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 4px; }
    .pf-member-discount { font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; font-size: 1.8rem; font-weight: 700; color: var(--rose-accent); }
    .pf-member.is-gold .pf-member-discount { color: var(--gold-glow); }
    .pf-progress { height: 6px; margin: 16px 0 20px; background-color: var(--card-cream); border: 1px solid var(--border-hairline); }
    .pf-progress-fill { height: 100%; background-color: var(--rose-accent); }
    .pf-member.is-gold .pf-progress-fill { background-color: var(--gold-glow); }

    /* Key / value lists */
    .pf-kv { display: grid; gap: 14px; margin: 0 0 22px; }
    .pf-kv-row { display: flex; justify-content: space-between; gap: 16px; font-size: 0.9rem; }
    .pf-kv dt { color: #9C9284; }
    .pf-kv dd { margin: 0; font-weight: 500; text-align: right; color: var(--text-ink); }
    .pf-kv-row.is-stacked { flex-direction: column; gap: 6px; }
    .pf-kv-row.is-stacked dd { text-align: left; color: #E4DCC8; font-weight: 400; }

    /* Account links */
    .pf-links { list-style: none; }
    .pf-links li + li { border-top: 1px solid rgba(122, 108, 85, 0.45); }
    .pf-links a, .pf-links button {
      display: flex; align-items: center; justify-content: space-between; width: 100%; padding: 14px 0;
      background: none; border: 0; color: var(--text-ink); font: inherit; font-size: 0.9rem; text-align: left; cursor: pointer; transition: color 0.2s ease;
    }
    .pf-links a:hover, .pf-links button:hover { color: var(--rose-accent); }
    .pf-links svg { color: var(--bronze-taupe); flex-shrink: 0; }

    /* Edit dialog (mirrors the membership checkout modal) */
    .pf-overlay { display: none; position: fixed; inset: 0; z-index: 1000; align-items: center; justify-content: center; padding: 24px; background-color: rgba(0, 0, 0, 0.75); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); }
    .pf-overlay.show { display: flex; }
    .pf-modal { width: 100%; max-width: 520px; max-height: 90vh; overflow-y: auto; padding: 32px; background-color: var(--bg-cream); border: 1px solid var(--border-hairline); border-radius: 8px; box-shadow: 0 20px 48px rgba(0, 0, 0, 0.5); }
    .pf-modal-title { font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; font-size: 1.4rem; font-weight: 700; margin-bottom: 6px; }
    .pf-modal-sub { font-size: 0.82rem; color: #B3AA98; margin-bottom: 22px; }
    .pf-form { display: grid; gap: 16px; }
    .pf-field { display: flex; flex-direction: column; gap: 6px; }
    .pf-field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .pf-label { font-size: 0.72rem; font-weight: 600; letter-spacing: 0.07em; text-transform: uppercase; color: var(--text-ink); }
    .pf-input {
      width: 100%; min-height: 42px; padding: 11px 14px;
      font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif; font-size: 15px; color: var(--text-ink);
      background-color: var(--card-white); border: 1px solid var(--border-hairline); border-radius: 4px; color-scheme: dark;
    }
    .pf-input:focus { outline: 2px solid var(--rose-accent); border-color: var(--rose-accent); }
    .pf-input.invalid { border-color: #FF6B60; }
    .pf-input[readonly] { opacity: 0.65; cursor: not-allowed; }
    textarea.pf-input { min-height: 88px; resize: vertical; }
    .pf-hint { font-size: 0.72rem; color: #9C9284; }
    .pf-error { display: none; font-size: 0.72rem; color: #FF6B60; }
    .pf-error.show { display: block; }
    .pf-check { display: flex; align-items: flex-start; gap: 10px; font-size: 0.88rem; color: #E4DCC8; }
    .pf-check input { width: 16px; height: 16px; margin-top: 3px; accent-color: var(--rose-accent); }
    .pf-alert { display: none; padding: 9px 13px; font-size: 0.78rem; color: #FF8A80; background: rgba(255, 107, 96, 0.12); border: 1px solid rgba(255, 107, 96, 0.4); border-radius: 8px; }
    .pf-alert.show { display: block; }
    .pf-modal-actions { display: flex; flex-wrap: wrap; justify-content: flex-end; gap: 12px; margin-top: 8px; }
    body.pf-modal-lock { overflow: hidden; }

    @media (min-width: 720px) {
      .pf-stats { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    }
    @media (min-width: 980px) {
      .pf-grid { grid-template-columns: minmax(0, 1.65fr) minmax(0, 1fr); align-items: start; }
    }
    @media (max-width: 640px) {
      .pf-summary { flex-direction: column; align-items: flex-start; padding: 24px 20px; }
      .pf-summary .pf-btn { width: 100%; }
      .pf-gate { padding: 36px 20px; }
      .pf-modal { padding: 24px 20px; }
    }
    @media (max-width: 560px) {
      .pf-appt { grid-template-columns: 52px minmax(0, 1fr); gap: 14px; padding: 16px; }
      .pf-appt-trail { grid-column: 1 / -1; flex-direction: row; align-items: center; justify-content: space-between; }
      .pf-field-row { grid-template-columns: 1fr; }
    }
  </style>
  <style>
    /* Smooth native page transition (no JS, no opacity hacks -> no flicker) */
    @view-transition {
      navigation: auto;
    }
  </style>
  <style>
    /* ===== DK Auth Modal — small on-page login/signup (no more full login.html redirect) ===== */
    .dk-authmodal-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(10, 8, 6, 0.72);
      backdrop-filter: blur(4px);
      -webkit-backdrop-filter: blur(4px);
      z-index: 9999;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }
    .dk-authmodal-overlay.show { display: flex; }

    .dk-authmodal-box {
      position: relative;
      width: 100%;
      max-width: 380px;
      max-height: 88vh;
      overflow-y: auto;
      background-color: var(--card-white, #1E1A14);
      border: 1px solid var(--border-hairline, #7A6C55);
      border-radius: 18px;
      padding: 34px 24px 26px;
      box-shadow: 0 30px 70px rgba(0,0,0,0.5), 0 0 0 1px rgba(255,255,255,0.04);
      animation: dkAuthRise 0.25s cubic-bezier(0.2,0.8,0.2,1);
    }
    @keyframes dkAuthRise {
      from { opacity: 0; transform: translateY(14px) scale(0.98); }
      to { opacity: 1; transform: translateY(0) scale(1); }
    }

    .dk-authmodal-close {
      position: absolute;
      top: 14px;
      right: 14px;
      background: rgba(255,255,255,0.06);
      border: 1px solid rgba(255,255,255,0.1);
      color: var(--text-ink, #F3EEE2);
      width: 32px;
      height: 32px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      padding: 0;
    }
    .dk-authmodal-close:hover { background: rgba(255,255,255,0.14); }

    .dk-authmodal-panel { display: none; }
    .dk-authmodal-panel.active { display: block; }

    .dk-authmodal-title {
      font-size: 1.35rem;
      font-weight: 700;
      margin-bottom: 6px;
      color: var(--text-ink, #F3EEE2);
    }
    .dk-authmodal-sub { color: #9C9284; font-size: 0.82rem; margin-bottom: 20px; }
    .dk-authmodal-sub a { color: var(--rose-accent, #C9A66B); font-weight: 600; text-decoration: none; }

    .dk-authmodal-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    .dk-authmodal-group { display: flex; flex-direction: column; gap: 5px; margin-bottom: 12px; }
    .dk-authmodal-label { font-size: 0.65rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; color: #9C9284; }
    .dk-authmodal-input {
      padding: 11px 14px;
      border: 1px solid rgba(255,255,255,0.09);
      background-color: rgba(255,255,255,0.05);
      font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif; /* ← FONT-FAMILY UPDATED */
      font-size: 14px;
      color: var(--text-ink, #F3EEE2);
      width: 100%;
      border-radius: 9px;
      transition: border-color .15s ease, background .15s ease;
    }
    .dk-authmodal-input::placeholder { color: #6E675A; }
    .dk-authmodal-input:focus {
      outline: none;
      border-color: var(--rose-accent, #C9A66B);
      background-color: rgba(255,255,255,0.07);
      box-shadow: 0 0 0 3px rgba(201,166,107,0.16);
    }
    .dk-authmodal-input.invalid { border-color: #FF6B60; }
    .dk-authmodal-error { font-size: 0.68rem; color: #FF8A80; display: none; }
    .dk-authmodal-error.show { display: block; }

    .dk-authmodal-pwwrap { position: relative; }
    .dk-authmodal-pwwrap .dk-authmodal-input { padding-right: 40px; }
    .dk-authmodal-pwtoggle {
      position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
      background: none; border: none; color: #9C9284; cursor: pointer; display: flex; padding: 4px;
    }
    .dk-authmodal-pwtoggle:hover { color: var(--rose-accent, #C9A66B); }

    .dk-authmodal-terms { display: flex; align-items: flex-start; gap: 8px; margin: 2px 0 14px; }
    .dk-authmodal-terms input { width: 16px; height: 16px; margin-top: 1px; accent-color: var(--rose-accent, #C9A66B); flex-shrink: 0; cursor: pointer; }
    .dk-authmodal-terms label { font-size: 0.74rem; color: #9C9284; }
    .dk-authmodal-terms a { color: var(--rose-accent, #C9A66B); font-weight: 600; }

    .dk-authmodal-submit {
      display: flex; align-items: center; justify-content: center;
      background: linear-gradient(135deg, var(--rose-accent, #C9A66B), var(--bronze-taupe, #A9884C));
      color: #17130D; padding: 13px 24px; font-size: 0.86rem; font-weight: 700;
      border: none; cursor: pointer; width: 100%; border-radius: 9px; margin-top: 4px;
      transition: filter .2s ease, transform .15s ease;
    }
    .dk-authmodal-submit:hover { filter: brightness(1.08); }
    .dk-authmodal-submit:active { transform: scale(0.99); }

    .dk-authmodal-alert {
      display: none; background: rgba(255,107,96,0.12); border: 1px solid rgba(255,107,96,0.4);
      color: #FF8A80; font-size: 0.78rem; padding: 9px 13px; border-radius: 8px; margin-bottom: 14px;
    }
    .dk-authmodal-alert.show { display: block; }

    body.dk-authmodal-lock { overflow: hidden; }

    @media (min-width: 480px) {
      .dk-authmodal-box { padding: 38px 34px 30px; }
    }
  </style>
  <style>
    /* Accessibility: skip link */
    .skip-link {
      position: absolute;
      top: -48px;
      left: 8px;
      z-index: 3000;
      background-color: var(--rose-accent, #C9A66B);
      color: #15130E;
      font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif; /* ← FONT-FAMILY UPDATED */
      font-weight: 700;
      font-size: 0.85rem;
      padding: 10px 18px;
      border-radius: 6px;
      text-decoration: none;
      transition: top 0.2s ease;
    }
    .skip-link:focus {
      top: 8px;
    }

    /* Accessibility: visually-hidden utility (screen-reader only) */
    .sr-only {
      position: absolute;
      width: 1px;
      height: 1px;
      padding: 0;
      margin: -1px;
      overflow: hidden;
      clip: rect(0, 0, 0, 0);
      white-space: nowrap;
      border: 0;
    }

    /* Accessibility: consistent visible focus styles across interactive elements */
    a:focus-visible,
    button:focus-visible,
    input:focus-visible,
    select:focus-visible,
    textarea:focus-visible,
    [tabindex]:focus-visible,
    summary:focus-visible {
      outline: 2px solid var(--rose-accent, #C9A66B);
      outline-offset: 2px;
      border-radius: 4px;
    }

    @media (prefers-reduced-motion: reduce) {
      .reveal {
        opacity: 1;
        transform: none;
        transition: none;
      }
    }

  </style>
  <noscript>
    <style>
      /* Accessibility: if JS fails to load, scroll-reveal content must still be visible */
      .reveal {
        opacity: 1 !important;
        transform: none !important;
        transition: none !important;
      }
    </style>
  </noscript>
</head>
<body>
  <a href="#main-content" class="skip-link">Skip to content</a>
  <!-- DK Auth Modal: small on-page login/signup card (replaces full-page login.html redirect) -->
  <div class="dk-authmodal-overlay" id="dkAuthOverlay" onclick="if (event.target === this) dkCloseAuthModal();">
    <div class="dk-authmodal-box" role="dialog" aria-modal="true" aria-labelledby="dkAuthHeading">
      <button type="button" class="dk-authmodal-close" onclick="dkCloseAuthModal()" aria-label="Close">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>

      <div class="dk-authmodal-alert" id="dkAuthAlert" role="alert" aria-live="assertive" aria-atomic="true"></div>

      <!-- LOGIN PANEL -->
      <div class="dk-authmodal-panel active" id="dkAuthPanelLogin">
        <h2 id="dkAuthHeading" class="font-serif dk-authmodal-title">Welcome back</h2>
        <p class="dk-authmodal-sub">Don't have an account? <a href="#" onclick="dkAuthSwitchTab('signup'); return false;">Sign up</a></p>

        <form id="dkAuthLoginForm" onsubmit="dkAuthHandleLogin(event)" novalidate>
          <div class="dk-authmodal-group">
            <label class="dk-authmodal-label" for="dkAuthLoginEmail">Email or Phone</label>
            <input type="text" id="dkAuthLoginEmail" class="dk-authmodal-input" placeholder="you@email.com" aria-describedby="err-dkAuthLoginEmail">
            <span class="dk-authmodal-error" id="err-dkAuthLoginEmail">Enter the email or phone you signed up with.</span>
          </div>
          <div class="dk-authmodal-group">
            <label class="dk-authmodal-label" for="dkAuthLoginPassword">Password</label>
            <div class="dk-authmodal-pwwrap">
              <input type="password" id="dkAuthLoginPassword" class="dk-authmodal-input" placeholder="Enter your password" aria-describedby="err-dkAuthLoginPassword">
              <button type="button" class="dk-authmodal-pwtoggle" onclick="dkAuthTogglePassword('dkAuthLoginPassword', this)" aria-label="Show password">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
              </button>
            </div>
            <span class="dk-authmodal-error" id="err-dkAuthLoginPassword">Enter your password.</span>
          </div>
          <button type="submit" class="dk-authmodal-submit">Login</button>
        </form>
      </div>

      <!-- SIGNUP PANEL -->
      <div class="dk-authmodal-panel" id="dkAuthPanelSignup">
        <h2 class="font-serif dk-authmodal-title">Create an account</h2>
        <p class="dk-authmodal-sub">Already have an account? <a href="#" onclick="dkAuthSwitchTab('login'); return false;">Log in</a></p>

        <form id="dkAuthSignupForm" onsubmit="dkAuthHandleSignup(event)" novalidate>
          <div class="dk-authmodal-row">
            <div class="dk-authmodal-group">
              <label class="dk-authmodal-label" for="dkAuthSuFirstName">First name</label>
              <input type="text" id="dkAuthSuFirstName" class="dk-authmodal-input" placeholder="Yash" aria-describedby="err-dkAuthSuFirstName">
              <span class="dk-authmodal-error" id="err-dkAuthSuFirstName">Enter your first name.</span>
            </div>
            <div class="dk-authmodal-group">
              <label class="dk-authmodal-label" for="dkAuthSuLastName">Last name</label>
              <input type="text" id="dkAuthSuLastName" class="dk-authmodal-input" placeholder="Sathwara" aria-describedby="err-dkAuthSuLastName">
              <span class="dk-authmodal-error" id="err-dkAuthSuLastName">Enter your last name.</span>
            </div>
          </div>
          <div class="dk-authmodal-group">
            <label class="dk-authmodal-label" for="dkAuthSuEmail">Email</label>
            <input type="email" id="dkAuthSuEmail" class="dk-authmodal-input" placeholder="you@email.com" aria-describedby="err-dkAuthSuEmail">
            <span class="dk-authmodal-error" id="err-dkAuthSuEmail">Enter a valid email address.</span>
          </div>
          <div class="dk-authmodal-group">
            <label class="dk-authmodal-label" for="dkAuthSuPhone">Phone number</label>
            <input type="tel" id="dkAuthSuPhone" class="dk-authmodal-input" placeholder="99246 20958" aria-describedby="err-dkAuthSuPhone">
            <span class="dk-authmodal-error" id="err-dkAuthSuPhone">Enter a valid phone number.</span>
          </div>
          <div class="dk-authmodal-group">
            <label class="dk-authmodal-label" for="dkAuthSuPassword">Password</label>
            <div class="dk-authmodal-pwwrap">
              <input type="password" id="dkAuthSuPassword" class="dk-authmodal-input" placeholder="At least 6 characters" aria-describedby="err-dkAuthSuPassword">
              <button type="button" class="dk-authmodal-pwtoggle" onclick="dkAuthTogglePassword('dkAuthSuPassword', this)" aria-label="Show password">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
              </button>
            </div>
            <span class="dk-authmodal-error" id="err-dkAuthSuPassword">Password must be at least 6 characters.</span>
          </div>
          <div class="dk-authmodal-terms">
            <input type="checkbox" id="dkAuthSuTerms" aria-describedby="err-dkAuthSuTerms">
            <label for="dkAuthSuTerms">I agree to the <a href="#">Terms &amp; Conditions</a></label>
          </div>
          <span class="dk-authmodal-error" id="err-dkAuthSuTerms" style="margin:-8px 0 12px;">Please accept the Terms &amp; Conditions.</span>

          <button type="submit" class="dk-authmodal-submit">Create account</button>
        </form>
      </div>
    </div>
  </div>


  <!-- Header -->
  <header class="site-header" id="siteHeader">
    <div class="container header-inner">
      <a href="index.php" class="brand-logo" aria-label="Deepkala Hair Salon Homepage">
        <img src="assets/deepkala-emblem.png" width="38" height="38" alt="Deepkala Hair Salon" style="border-radius:6px;object-fit:cover;">
        <div>
          <div class="logo-text-title">DEEPKALA</div>
          <div class="logo-text-sub">HAIR SALON</div>
        </div>
      </a>

      <nav aria-label="Main Navigation">
        <ul class="nav-links" id="navLinks">
          <li><a href="index.php" class="nav-link">Home</a></li>
          <li><a href="services.php" class="nav-link">Services</a></li>
          <li><a href="reviews.php" class="nav-link">Reviews</a></li>
          <li><a href="about.php" class="nav-link">About</a></li>
          <li><a href="contact.php" class="nav-link">Contact</a></li>
          <li><a href="membership.php" class="nav-link">Membership<span id="navMemberBadge"><?php $dkTier = $_SESSION['membership'] ?? 'none'; if ($dkTier === 'gold' || $dkTier === 'silver') { echo '<span class="dk-member-badge dk-badge-' . $dkTier . ' nav-member-badge">' . ucfirst($dkTier) . '</span>'; } ?></span></a></li>
          <li class="dk-nav-profile"><a href="profile.php" class="nav-link">My Profile</a></li>
          <li class="mobile-menu-cta">
            <a href="https://wa.me/919924620958?text=Hello%20Deepkala%20Hair%20Salon" target="_blank" rel="noopener noreferrer" class="btn-whatsapp-cta">Chat on WhatsApp</a>
          </li>
        </ul>
      </nav>

      <div style="display: flex; align-items: center; gap: 12px;">
        <a href="booking.php" class="header-book-btn" title="Book Appointment at Deepkala Hair Salon">Book Appointment</a>
        <?php include 'backend/profile_menu.php'; ?>
        <button class="mobile-toggle" id="mobileMenuBtn" aria-controls="navLinks" aria-label="Toggle navigation menu">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
        </button>
      </div>
    </div>
  </header>
  <!-- Main Content -->
  <main id="main-content" tabindex="-1">
    <!-- Page Hero -->
    <section class="page-hero">
      <div class="container reveal">
        <h1 class="font-serif">My Profile</h1>
        <p>Your details, membership and visit history at Deepkala Hair Salon.</p>
      </div>
    </section>

    <div class="container pf-wrap">

      <div class="pf-notice" id="pfNotice" role="status" aria-live="polite" hidden></div>

      <!-- Signed-out state -->
      <!-- Signed-in state (rendered by the profile script below) -->
      <div id="pfApp" hidden>

        <section class="pf-summary reveal" aria-label="Profile summary">
          <div class="pf-avatar" id="pfAvatar" aria-hidden="true"></div>
          <div class="pf-identity">
            <div class="pf-name-row">
              <h2 class="font-serif pf-name" id="pfName"></h2>
              <span id="pfTierBadge"></span>
            </div>
            <ul class="pf-contact">
              <li>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 7L2 7"/></svg>
                <span id="pfEmail"></span>
              </li>
              <li>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                <span id="pfPhone"></span>
              </li>
              <li>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <span>Client since <span id="pfSince"></span></span>
              </li>
            </ul>
          </div>
          <button type="button" class="pf-btn pf-btn-outline" data-action="edit">Edit Profile</button>
        </section>

        <div class="pf-stats reveal" id="pfStats"></div>

        <div class="pf-grid">

          <!-- Main column -->
          <div class="pf-col">
            <div class="category-header reveal">
              <h2 class="eyebrow-tag">Upcoming Appointment</h2>
              <div class="hairline"></div>
            </div>
            <div id="pfUpcoming"></div>

            <div class="category-header reveal">
              <h2 class="eyebrow-tag">Visit History</h2>
              <div class="hairline"></div>
            </div>
            <div id="pfHistory"></div>
          </div>

          <!-- Side column -->
          <div class="pf-col">
            <div class="category-header reveal">
              <h2 class="eyebrow-tag">Membership</h2>
              <div class="hairline"></div>
            </div>
            <div id="pfMembership"></div>

            <div class="category-header reveal">
              <h2 class="eyebrow-tag">Account</h2>
              <div class="hairline"></div>
            </div>
            <div class="pf-card">
              <ul class="pf-links">
                <li><a href="booking.php">Book an Appointment <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg></a></li>
                <li><a href="membership.php">Membership Plans <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg></a></li>
                <li><a href="contact.php">Contact the Salon <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg></a></li>
                <li><button type="button" data-action="logout">Logout <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg></button></li>
              </ul>
            </div>
          </div>

        </div>
      </div>

    </div>
  </main>

  <!-- Edit Profile Dialog -->
  <div class="pf-overlay" id="pfEditOverlay">
    <div class="pf-modal" role="dialog" aria-modal="true" aria-labelledby="pfEditTitle">
      <h2 class="pf-modal-title" id="pfEditTitle">Edit Profile</h2>
      <p class="pf-modal-sub">Update your name and phone number.</p>

      <form class="pf-form" id="pfEditForm" novalidate>
        <div class="pf-alert" id="pfEditAlert" role="alert"></div>

        <div class="pf-field">
          <label class="pf-label" for="pfFieldName">Full name</label>
          <input type="text" id="pfFieldName" class="pf-input" autocomplete="name" maxlength="60" aria-describedby="err-pfFieldName">
          <span class="pf-error" id="err-pfFieldName">Enter your full name (2&ndash;60 characters).</span>
        </div>

        <div class="pf-field-row">
          <div class="pf-field">
            <label class="pf-label" for="pfFieldEmail">Email</label>
            <input type="email" id="pfFieldEmail" class="pf-input" readonly aria-describedby="hint-pfFieldEmail">
            <span class="pf-hint" id="hint-pfFieldEmail">Email is your login and can&rsquo;t be changed.</span>
          </div>
          <div class="pf-field">
            <label class="pf-label" for="pfFieldPhone">Phone number</label>
            <input type="tel" id="pfFieldPhone" class="pf-input" autocomplete="tel" aria-describedby="err-pfFieldPhone">
            <span class="pf-error" id="err-pfFieldPhone">Enter a valid phone number.</span>
          </div>
        </div>

        <div class="pf-modal-actions">
          <button type="button" class="pf-btn pf-btn-outline" id="pfEditCancel">Cancel</button>
          <button type="submit" class="pf-btn">Save Changes</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Footer -->
  <footer class="site-footer">
    <div class="footer-panel">
      <div class="footer-card">
        <div class="footer-top">
          <div class="footer-brand">
            <a href="index.php" class="brand-logo" aria-label="Deepkala Hair Salon">
              <img src="assets/deepkala-emblem.png" width="34" height="34" alt="Deepkala Hair Salon" style="border-radius:6px;object-fit:cover;">
              <div>
                <div class="logo-text-title">DEEPKALA</div>
                <div class="logo-text-sub">HAIR SALON</div>
              </div>
            </a>
            <div class="footer-social">
              <a href="https://wa.me/919924620958?text=Hi%20Deepkala%20Hair%20Salon" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
              </a>
              <a href="https://www.instagram.com/_yash_1249?stkn=bzVmcGRkdGc3enVs" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2.5" y="2.5" width="19" height="19" rx="5"/><circle cx="12" cy="12" r="4.2"/><circle cx="17.6" cy="6.4" r="0.6" fill="currentColor"/></svg>
              </a>
              <!-- TODO: replace # with your Facebook page URL -->
              <a href="#" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                <svg width="20" height="20" viewBox="5 2 14 21" fill="currentColor" aria-hidden="true"><path d="M13.5 22v-8.2h2.8l.5-3.4h-3.3V8.3c0-1 .4-1.7 1.8-1.7h1.6V3.5c-.3 0-1.3-.2-2.5-.2-2.6 0-4.3 1.6-4.3 4.4v2.7H7.2v3.4h2.9V22h3.4z"/></svg>
              </a>
            </div>
          </div>

          <div class="footer-cols">
            <nav class="footer-col" aria-label="Quick links">
              <h3 class="footer-col-title">Quick links</h3>
              <ul class="footer-list">
                <li><a href="index.php">Home</a></li>
                <li><a href="services.php">Services</a></li>
                <li><a href="reviews.php">Client Reviews</a></li>
                <li><a href="about.php">About</a></li>
              </ul>
            </nav>

            <nav class="footer-col" aria-label="Book and visit">
              <h3 class="footer-col-title">Book &amp; visit</h3>
              <ul class="footer-list">
                <li><a href="booking.php">Reserve Slot</a></li>
                <li><a href="membership.php">Membership</a></li>
                <li><a href="contact.php">Contact</a></li>
              </ul>
            </nav>

            <div class="footer-col footer-col-info">
              <h3 class="footer-col-title">Salon info</h3>
              <ul class="footer-list">
                <li>2 Jayvimal Society, Cadila Road, Ghodasar, Ahmedabad &ndash; 380050</li>
                <li><a href="tel:+919924620958">+91 99246 20958</a></li>
                <li>9:00 AM &ndash; 9:00 PM, Daily</li>
              </ul>
            </div>
          </div>
        </div>

        <div class="footer-bottom">
          <div>&copy; 2026 Deepkala Hair Salon. All rights reserved.</div>
          <div class="footer-legal">
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Service</a>
            <a href="#">Cookies Settings</a>
          </div>
        </div>
      </div>

      <div class="footer-watermark" aria-hidden="true">Deepkala</div>
    </div>
  </footer>

  <!-- Floating WhatsApp Button -->
  <a href="https://wa.me/919924620958?text=Hi%20Deepkala%20Hair%20Salon%2C%20I%27d%20like%20to%20know%20more" class="whatsapp-float" target="_blank" rel="noopener noreferrer" title="Chat on WhatsApp">
    <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
  </a>

  <!-- Shared Script -->
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Scroll Reveal Observer
      const observerOptions = { threshold: 0.1 };
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('active');
          }
        });
      }, observerOptions);

      document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

      // Mobile Menu Toggle & Accessibility
      const menuBtn = document.getElementById('mobileMenuBtn');
      const navLinks = document.getElementById('navLinks');
      if (menuBtn && navLinks) {
        menuBtn.setAttribute('aria-expanded', 'false');
        
        const toggleMenu = (show) => {
          const isOpen = show !== undefined ? show : !navLinks.classList.contains('show');
          navLinks.classList.toggle('show', isOpen);
          menuBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        };

        menuBtn.addEventListener('click', (e) => {
          e.stopPropagation();
          toggleMenu();
        });

        // Close when clicking outside menu
        document.addEventListener('click', (e) => {
          if (!navLinks.contains(e.target) && !menuBtn.contains(e.target)) {
            toggleMenu(false);
          }
        });

        // Close on ESC key
        document.addEventListener('keydown', (e) => {
          if (e.key === 'Escape') {
            toggleMenu(false);
          }
        });

        // Close when clicking a menu link
        navLinks.querySelectorAll('a').forEach(link => {
          link.addEventListener('click', () => toggleMenu(false));
        });
      }

      // Sticky Header Shadow
      const header = document.getElementById('siteHeader');
      if (header) {
        window.addEventListener('scroll', () => {
          if (window.scrollY > 20) {
            header.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.35)';
          } else {
            header.style.boxShadow = 'none';
          }
        }, { passive: true });
      }
    });
  </script>

  <script>
    /* ===== Deepkala Membership & Auth (client-side demo, no backend) ===== */
    const DK_USER_KEY = 'dk_user';
    const DK_MEMBERSHIP_KEY = 'dk_membership';

    const DK_SESSION_USER = <?php echo json_encode(isset($_SESSION['user_id']) ? ['name' => $_SESSION['user_name'], 'email' => $_SESSION['user_email'], 'phone' => $_SESSION['user_phone']] : null); ?>;
    const DK_SESSION_TIER = <?php echo json_encode($_SESSION['membership'] ?? 'none'); ?>;
    function dkGetUser() { return DK_SESSION_USER; }
    function dkGetMembership() {
      if (DK_SESSION_TIER === 'gold') return { tier: 'gold', discount: 20 };
      if (DK_SESSION_TIER === 'silver') return { tier: 'silver', discount: 10 };
      return null;
    }
    function dkLogout() { window.location.href = 'logout.php'; }
    function dkTierLabel(tier) {
      return tier === 'gold' ? 'Gold Member' : tier === 'silver' ? 'Silver Member' : '';
    }

    // Auto-apply member discount to every element with class "service-price"
    function dkApplyMemberPricing() {
      const membership = dkGetMembership();
      const priceEls = document.querySelectorAll('.service-price');
      priceEls.forEach(el => {
        if (!el.dataset.dkOriginal) {
          el.dataset.dkOriginal = el.textContent.trim();
        }
        const original = el.dataset.dkOriginal;
        const num = parseInt(original.replace(/[^\d]/g, ''), 10);
        if (isNaN(num)) return;
        if (membership && membership.discount) {
          const discounted = Math.round(num * (1 - membership.discount / 100));
          el.innerHTML = '<span class="dk-price-old">₹' + num.toLocaleString('en-IN') + '</span><span class="dk-price-new">₹' + discounted.toLocaleString('en-IN') + '</span>';
          el.classList.add('dk-member-priced');
        } else {
          el.textContent = original;
          el.classList.remove('dk-member-priced');
        }
      });
    }

    // Render the header auth area: Login link, or account pill with member badge + logout
    function dkRenderAuthNav() {}
    function dkCloseAuthModal() {
      const overlay = document.getElementById('dkAuthOverlay');
      if (overlay) { overlay.style.display = 'none'; overlay.classList.remove('show'); document.body.classList.remove('modal-open'); }
    }
    function dkAuthSwitchTab(tab) {
      const login = document.getElementById('dkAuthPanelLogin');
      const signup = document.getElementById('dkAuthPanelSignup');
      if (login) login.classList.toggle('active', tab === 'login');
      if (signup) signup.classList.toggle('active', tab === 'signup');
    }
    function dkAuthTogglePassword(id, button) {
      const input = document.getElementById(id);
      if (!input) return;
      input.type = input.type === 'password' ? 'text' : 'password';
      if (button) button.setAttribute('aria-label', input.type === 'password' ? 'Show password' : 'Hide password');
    }
    function dkAuthHandleLogin(event) {
      event.preventDefault();
      const page = (location.pathname.split('/').pop() || 'index.php');
      window.location.href = 'login.php?redirect=' + encodeURIComponent(page);
    }
    function dkAuthHandleSignup(event) {
      event.preventDefault();
      const page = (location.pathname.split('/').pop() || 'index.php');
      window.location.href = 'signup.php?redirect=' + encodeURIComponent(page);
    }

    document.addEventListener('DOMContentLoaded', () => {
      dkApplyMemberPricing();
      dkRenderAuthNav();
    });
  </script>

  <script>
    /* ===== Deepkala Profile — data comes from MySQL via PHP (see top of this file) ===== */
    (function () {
      'use strict';
      const D = <?php echo json_encode($profileData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
      const HISTORY_PREVIEW = 5;
      const WHATSAPP_NUMBER = '919924620958';
      let showAllHistory = false;
      let lastFocus = null;
      let noticeTimer = null;

      const $ = (id) => document.getElementById(id);
      const esc = (v) => String(v == null ? '' : v).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
      const money = (n) => '₹' + Number(n || 0).toLocaleString('en-IN');
      const fmtDate = (d) => d.toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' });
      const fmtLongDate = (d) => d.toLocaleDateString('en-IN', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
      const fmtMonthYear = (d) => d.toLocaleDateString('en-IN', { month: 'long', year: 'numeric' });
      const fmtMonthShort = (d) => d.toLocaleDateString('en-IN', { month: 'short' });

      function relativeDays(d) {
        const today = new Date(); today.setHours(0, 0, 0, 0);
        const day = new Date(d.getFullYear(), d.getMonth(), d.getDate());
        const diff = Math.round((today - day) / 86400000);
        if (diff <= 0) return 'Today';
        if (diff === 1) return 'Yesterday';
        if (diff < 60) return diff + ' days ago';
        return Math.round(diff / 30) + ' months ago';
      }
      function initials(name) {
        const w = String(name || '').trim().split(/\s+/).filter(Boolean);
        if (!w.length) return '?';
        return (w[0].charAt(0) + (w.length > 1 ? w[w.length - 1].charAt(0) : '')).toUpperCase();
      }

      function renderAppointment(a, upcoming) {
        const when = upcoming ? fmtLongDate(a.start) : fmtDate(a.start);
        const waText = 'Hello Deepkala Hair Salon, I would like to reschedule or cancel my ' + a.service + ' appointment on ' + fmtDate(a.start) + ' at ' + a.time + '.';
        const actions = upcoming
          ? '<div class="pf-appt-actions"><a class="pf-link" href="https://wa.me/' + WHATSAPP_NUMBER + '?text=' + encodeURIComponent(waText) + '" target="_blank" rel="noopener noreferrer">Reschedule or cancel on WhatsApp</a></div>'
          : '';
        return '<li class="pf-appt' + (upcoming ? ' is-upcoming' : '') + '">' +
          '<div class="pf-appt-date" aria-hidden="true"><span class="pf-appt-day">' + a.start.getDate() + '</span><span class="pf-appt-mon">' + esc(fmtMonthShort(a.start)) + '</span></div>' +
          '<div>' +
            '<h3 class="pf-appt-service">' + esc(a.service) + '</h3>' +
            '<p class="pf-appt-meta">' + esc(when) + ' &middot; ' + esc(a.time) + '</p>' +
            '<p class="pf-appt-meta">with ' + esc(a.barber) + '</p>' +
            '<p class="pf-appt-meta is-faint">Paid via ' + esc(a.method) + '</p>' +
          '</div>' +
          '<div class="pf-appt-trail">' +
            '<span class="pf-chip ' + (upcoming ? 'pf-chip-confirmed' : 'pf-chip-done') + '">' + (upcoming ? 'Confirmed' : 'Completed') + '</span>' +
            '<span class="pf-appt-amount">' + money(a.amount) + '</span>' +
          '</div>' +
          actions +
        '</li>';
      }

      function renderUpcoming(list) {
        if (!list.length) return '<div class="pf-card pf-empty"><p>You have no upcoming appointments.</p><a href="booking.php" class="pf-btn">Book Appointment</a></div>';
        return '<ul class="pf-list">' + list.map((a) => renderAppointment(a, true)).join('') + '</ul>';
      }
      function renderHistory(list) {
        if (!list.length) return '<div class="pf-card pf-empty"><p>Your completed visits will appear here.</p></div>';
        const visible = showAllHistory ? list : list.slice(0, HISTORY_PREVIEW);
        let html = '<ul class="pf-list">' + visible.map((a) => renderAppointment(a, false)).join('') + '</ul>';
        if (list.length > HISTORY_PREVIEW) {
          html += '<button type="button" class="pf-btn pf-btn-outline pf-more" data-action="toggle-history" aria-expanded="' + showAllHistory + '">' +
            (showAllHistory ? 'Show fewer visits' : 'Show all ' + list.length + ' visits') + '</button>';
        }
        return html;
      }
      function renderMembership(tier) {
        if (tier !== 'gold' && tier !== 'silver') {
          return '<div class="pf-card"><p class="pf-card-title">Not a member yet</p>' +
            '<p class="pf-card-text">Save on every service with a Silver or Gold membership. Your discount is applied automatically at booking.</p>' +
            '<a href="membership.php" class="pf-btn pf-btn-block">View Membership Plans</a></div>';
        }
        const discount = tier === 'gold' ? 20 : 10;
        const label = tier === 'gold' ? 'Gold Member' : 'Silver Member';
        return '<div class="pf-card pf-member' + (tier === 'gold' ? ' is-gold' : '') + '">' +
          '<div class="pf-member-head"><span class="dk-member-badge dk-badge-' + tier + '">' + label + '</span><span class="pf-member-discount">' + discount + '% off</span></div>' +
          '<dl class="pf-kv"><div class="pf-kv-row"><dt>Plan</dt><dd>' + (tier === 'gold' ? 'Gold' : 'Silver') + '</dd></div>' +
          '<div class="pf-kv-row"><dt>Discount</dt><dd>' + discount + '% on all services</dd></div></dl>' +
          '<a href="membership.php" class="pf-btn pf-btn-outline pf-btn-block">Manage Membership</a></div>';
      }

      function render() {
        const user = D.user;
        const now = Date.now();
        const appts = D.appts.map((a) => Object.assign({}, a, { start: new Date(a.ts) })).filter((a) => !isNaN(a.start));
        const upcoming = appts.filter((a) => a.ts >= now).sort((x, y) => x.ts - y.ts);
        const past = appts.filter((a) => a.ts < now).sort((x, y) => y.ts - x.ts);

        const spent = past.reduce((s, a) => s + a.amount, 0);
        const counts = {};
        past.forEach((a) => { counts[a.service] = (counts[a.service] || 0) + 1; });
        let favourite = '';
        past.forEach((a) => { if (!favourite || counts[a.service] > counts[favourite]) favourite = a.service; });
        const earliest = appts.length ? appts.reduce((m, a) => (a.start < m ? a.start : m), appts[0].start) : null;

        $('pfApp').hidden = false;
        $('pfAvatar').textContent = initials(user.name);
        $('pfName').textContent = user.name || 'Guest';
        $('pfEmail').textContent = user.email;
        $('pfPhone').textContent = user.phone || 'No phone added';
        $('pfSince').textContent = earliest ? fmtMonthYear(earliest) : '—';
        $('pfTierBadge').innerHTML = (D.tier === 'gold' || D.tier === 'silver')
          ? '<span class="dk-member-badge dk-badge-' + D.tier + '">' + (D.tier === 'gold' ? 'Gold Member' : 'Silver Member') + '</span>' : '';

        const stats = [
          { label: 'Completed visits', value: String(past.length) },
          { label: 'Total spent', value: money(spent) },
          { label: 'Last visit', value: past.length ? relativeDays(past[0].start) : '—', text: true },
          { label: 'Most booked', value: favourite || '—', text: true }
        ];
        $('pfStats').innerHTML = stats.map((s) =>
          '<div class="pf-stat"><div class="pf-stat-value' + (s.text ? ' is-text' : '') + '">' + esc(s.value) + '</div><div class="pf-stat-label">' + esc(s.label) + '</div></div>'
        ).join('');

        $('pfUpcoming').innerHTML = '<span id="upcoming"></span>' + renderUpcoming(upcoming);
        $('pfHistory').innerHTML = '<span id="past"></span>' + renderHistory(past);
        $('pfMembership').innerHTML = renderMembership(D.tier);
      }

      function showNotice(message) {
        const el = $('pfNotice');
        el.textContent = message;
        el.hidden = false;
        clearTimeout(noticeTimer);
        noticeTimer = setTimeout(() => { el.hidden = true; }, 5000);
      }

      /* ---------- Edit dialog ---------- */
      function fieldError(id, show) {
        const err = $('err-' + id), input = $(id);
        if (err) err.classList.toggle('show', show);
        if (input) { input.classList.toggle('invalid', show); input.setAttribute('aria-invalid', show ? 'true' : 'false'); }
      }
      function showAlert(m) { const el = $('pfEditAlert'); el.textContent = m; el.classList.add('show'); }
      function hideAlert() { $('pfEditAlert').classList.remove('show'); }

      function openEdit() {
        $('pfFieldName').value = D.user.name || '';
        $('pfFieldEmail').value = D.user.email || '';
        $('pfFieldPhone').value = D.user.phone || '';
        ['pfFieldName', 'pfFieldPhone'].forEach((id) => fieldError(id, false));
        hideAlert();
        lastFocus = document.activeElement;
        $('pfEditOverlay').classList.add('show');
        document.body.classList.add('pf-modal-lock');
        $('pfFieldName').focus();
      }
      function closeEdit() {
        $('pfEditOverlay').classList.remove('show');
        document.body.classList.remove('pf-modal-lock');
        if (lastFocus && typeof lastFocus.focus === 'function') lastFocus.focus();
        lastFocus = null;
      }

      function saveEdit(e) {
        e.preventDefault();
        hideAlert();
        const name = $('pfFieldName').value.trim().replace(/\s+/g, ' ');
        const phone = $('pfFieldPhone').value.trim();
        const nameOk = /^[A-Za-z ]{2,60}$/.test(name);
        const phoneOk = /^(\+91[\-\s]?)?[6-9]\d{9}$/.test(phone);
        fieldError('pfFieldName', !nameOk);
        fieldError('pfFieldPhone', !phoneOk);
        if (!nameOk || !phoneOk) return;

        const body = new FormData();
        body.append('action', 'update_profile');
        body.append('name', name);
        body.append('phone', phone);
        body.append('csrf_token', '<?php echo htmlspecialchars(csrf_token()); ?>');
        fetch('profile.php', { method: 'POST', body: body })
          .then((r) => r.json())
          .then((res) => {
            if (!res.ok) { showAlert(res.error || 'We could not save your changes. Please try again.'); return; }
            window.location.href = 'profile.php?updated=1'; // reload so the navbar menu shows the new name too
          })
          .catch(() => showAlert('We could not save your changes. Please try again.'));
      }

      function onKeydown(e) {
        const overlay = $('pfEditOverlay');
        if (!overlay.classList.contains('show')) return;
        if (e.key === 'Escape') { closeEdit(); return; }
        if (e.key !== 'Tab') return;
        const f = overlay.querySelectorAll('button:not([disabled]), input:not([disabled]), textarea:not([disabled])');
        if (!f.length) return;
        const first = f[0], last = f[f.length - 1];
        if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
        else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
      }

      function onAppClick(e) {
        const el = e.target.closest('[data-action]');
        if (!el) return;
        const action = el.dataset.action;
        if (action === 'edit') openEdit();
        else if (action === 'toggle-history') {
          showAllHistory = !showAllHistory;
          render();
          const btn = document.querySelector('[data-action="toggle-history"]');
          if (btn) btn.focus();
        } else if (action === 'logout') window.location.href = 'logout.php';
      }

      function init() {
        $('pfApp').addEventListener('click', onAppClick);
        $('pfEditForm').addEventListener('submit', saveEdit);
        $('pfEditCancel').addEventListener('click', closeEdit);
        $('pfEditOverlay').addEventListener('click', (e) => { if (e.target === e.currentTarget) closeEdit(); });
        document.addEventListener('keydown', onKeydown);
        render();
        if (D.updated) showNotice('Your profile has been updated.');
        if (location.hash) { const t = document.querySelector(location.hash); if (t) t.scrollIntoView(); }
      }
      document.addEventListener('DOMContentLoaded', init);
    })();
  </script>

</body>
</html>
