<?php
require 'backend/config.php';

$isLoggedIn = isset($_SESSION['user_id']);

// Handle "Pay & Activate Membership" — real form submit, no fetch/AJAX
$planJustActivated = false;
$membershipMessage = "";

if ($isLoggedIn && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['choose_plan'])) {
    require_csrf();
    $plan = $_POST['choose_plan']; // demo activation only: no real card data is processed

    if ($plan == 'silver' || $plan == 'gold') {
        $stmt = mysqli_prepare($conn, 'UPDATE users SET membership = ? WHERE id = ?');
        $uid = (int) $_SESSION['user_id'];
        mysqli_stmt_bind_param($stmt, 'si', $plan, $uid);
        if (!mysqli_stmt_execute($stmt)) { $membershipMessage = 'Could not activate the membership. Please try again.'; } else {

        // Keep the session in sync with the database
        $_SESSION['membership'] = $plan;
        $planJustActivated = true;
        }
    }
}

$currentPlan = $_SESSION['membership'] ?? 'none';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Membership — Deepkala Hair Salon, Ahmedabad</title>

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

    /* Already-member banner */
    .member-status-bar {
      display: none;
      background-color: var(--card-cream);
      border: 1px solid var(--rose-accent);
      padding: 18px 22px;
      margin-bottom: 32px;
      align-items: center;
      justify-content: space-between;
      gap: 14px;
      flex-wrap: wrap;
    }
    .member-status-bar.show { display: flex; }
    .member-status-text { font-size: 0.9rem; color: #E4DCC8; }
    .member-status-text strong { color: var(--rose-accent); }

    /* Plans grid */
    .plans-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 24px;
      margin-bottom: 60px;
    }
    @media (min-width: 768px) { .plans-grid { grid-template-columns: 1fr 1fr; } }

    .plan-card {
      background-color: var(--card-white);
      border: 1px solid var(--border-hairline);
      padding: 36px 32px;
      display: flex;
      flex-direction: column;
      position: relative;
      transition: transform 0.2s ease, border-color 0.2s ease;
    }
    .plan-card:hover { transform: translateY(-3px); }
    .plan-card.gold { border-color: var(--gold-glow); box-shadow: 0 10px 30px rgba(255, 214, 102, 0.08); }

    .plan-badge-corner {
      position: absolute; top: 0; right: 0;
      background-color: var(--gold-glow);
      color: var(--rose-deep);
      font-size: 0.68rem;
      font-weight: 700;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      padding: 6px 14px;
    }

    .plan-name { font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; /* ← FONT-FAMILY UPDATED */ font-size: 1.6rem; font-weight: 700; margin-bottom: 6px; }
    .plan-card.silver .plan-name { color: var(--rose-accent); }
    .plan-card.gold .plan-name { color: var(--gold-glow); }

    .plan-desc { font-size: 0.85rem; color: #9C9284; margin-bottom: 22px; }

    .plan-price { font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; /* ← FONT-FAMILY UPDATED */ font-size: 2.4rem; font-weight: 700; color: var(--text-ink); }
    .plan-price span { font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif; /* ← FONT-FAMILY UPDATED */ font-size: 0.9rem; font-weight: 500; color: #9C9284; }
    .plan-discount-tag {
      display: inline-block;
      margin-top: 10px; margin-bottom: 22px;
      font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;
      padding: 5px 12px; border-radius: 4px;
    }
    .plan-card.silver .plan-discount-tag { background-color: rgba(201,166,107,0.15); color: var(--rose-accent); border: 1px solid rgba(201,166,107,0.4); }
    .plan-card.gold .plan-discount-tag { background-color: rgba(255,214,102,0.15); color: var(--gold-glow); border: 1px solid rgba(255,214,102,0.45); }

    .plan-features { list-style: none; margin-bottom: 28px; flex: 1; }
    .plan-features li { display: flex; align-items: flex-start; gap: 10px; font-size: 0.85rem; color: #C9C2B4; padding: 8px 0; border-bottom: 1px solid #2A2419; }
    .plan-features li:last-child { border-bottom: none; }
    .plan-features li svg { flex-shrink: 0; margin-top: 3px; }
    .plan-card.silver .plan-features li svg { color: var(--rose-accent); }
    .plan-card.gold .plan-features li svg { color: var(--gold-glow); }

    .btn-choose-plan {
      display: inline-flex; align-items: center; justify-content: center; gap: 10px;
      padding: 15px 24px; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.07em;
      cursor: pointer; border-radius: 4px; transition: all 0.2s ease; width: 100%; min-height: 46px;
    }
    .plan-card.silver .btn-choose-plan { background-color: var(--rose-accent); color: #FFF; border: 1px solid var(--rose-accent); }
    .plan-card.silver .btn-choose-plan:hover { background-color: var(--rose-deep); border-color: var(--rose-deep); }
    .plan-card.gold .btn-choose-plan { background-color: var(--gold-glow); color: var(--rose-deep); border: 1px solid var(--gold-glow); }
    .plan-card.gold .btn-choose-plan:hover { background-color: #FFE28C; }
    .btn-choose-plan.current { background-color: var(--card-cream) !important; color: #9C9284 !important; border: 1px solid var(--border-hairline) !important; cursor: default; }

    /* Payment modal (re-used pattern from booking, simplified) */
    .modal-overlay { display: none; position: fixed; inset: 0; background-color: rgba(0,0,0,0.75); backdrop-filter: blur(4px); z-index: 1000; align-items: center; justify-content: center; padding: 24px; }
    .modal-content { background-color: var(--bg-cream); border: 1px solid var(--border-hairline); border-radius: 8px; padding: 36px; max-width: 460px; width: 100%; text-align: left; box-shadow: 0 20px 48px rgba(0,0,0,0.5); }
    .modal-title { font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; /* ← FONT-FAMILY UPDATED */ font-size: 1.4rem; font-weight: 700; color: var(--text-ink); margin-bottom: 6px; text-align: center; }
    .modal-sub { font-size: 0.82rem; color: #B3AA98; text-align: center; margin-bottom: 22px; }

    .payment-amount-bar { display: flex; align-items: center; justify-content: space-between; background-color: var(--card-cream); border: 1px solid var(--border-hairline); padding: 14px 18px; margin-bottom: 20px; flex-wrap: wrap; gap: 8px; }
    .payment-amount-label { font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.07em; color: #B3AA98; }
    .payment-amount-value { font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; /* ← FONT-FAMILY UPDATED */ font-size: 1.35rem; font-weight: 700; color: var(--rose-accent); }

    .pay-method-toggle { display: flex; gap: 10px; margin-bottom: 20px; }
    .pay-method-pill { flex: 1; text-align: center; padding: 12px 10px; border: 1px solid var(--border-hairline); background-color: var(--card-white); font-size: 0.8rem; font-weight: 600; cursor: pointer; transition: all 0.15s ease; user-select: none; }
    .pay-method-pill.selected { background-color: var(--rose-deep); border-color: var(--rose-deep); color: #FFF; }

    .pay-fields { display: none; flex-direction: column; gap: 14px; }
    .pay-fields.active { display: flex; }

    .form-group { display: flex; flex-direction: column; gap: 6px; }
    .form-label { font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.07em; color: var(--text-ink); }
    .form-input { padding: 11px 14px; border: 1px solid var(--border-hairline); background-color: var(--card-white); font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif; /* ← FONT-FAMILY UPDATED */ font-size: 15px; color: var(--text-ink); width: 100%; min-height: 42px; border-radius: 4px; }
    .form-input:focus { outline: 2px solid var(--rose-accent); border-color: var(--rose-accent); }
    .field-error { font-size: 0.72rem; color: #FF6B60; display: none; }
    .field-error.show { display: block; }
    .form-input.invalid { border-color: #FF6B60; }
    .field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }

    .demo-badge { display: inline-block; background-color: #241F17; color: #C9A66B; border: 1px solid #C9A66B; font-size: 0.65rem; font-weight: 700; letter-spacing: 0.07em; text-transform: uppercase; padding: 3px 8px; border-radius: 3px; margin-bottom: 14px; }

    .btn-submit { display: inline-flex; align-items: center; justify-content: center; gap: 10px; background-color: var(--rose-accent); color: #15130E; padding: 15px 24px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.07em; border: 1px solid var(--rose-accent); cursor: pointer; width: 100%; border-radius: 4px; margin-top: 20px; min-height: 46px; }
    .btn-submit:hover { background-color: var(--rose-accent-hover); border-color: var(--rose-accent-hover); }

    .btn-link-muted { background: none; border: none; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.06em; color: var(--bronze-taupe); cursor: pointer; padding: 8px; width: 100%; margin-top: 6px; }

    .processing-box { padding: 10px 0; text-align: center; }
    .spinner { width: 40px; height: 40px; border: 3px solid var(--border-hairline); border-top-color: var(--rose-deep); border-radius: 50%; margin: 0 auto 16px auto; animation: spin 0.8s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }

    .success-box { text-align: center; }
    .success-check { width: 56px; height: 56px; border-radius: 50%; background-color: rgba(37,211,102,0.15); border: 1px solid rgba(37,211,102,0.4); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto; }
    .success-summary { background-color: var(--card-cream); border: 1px solid var(--border-hairline); padding: 14px 18px; margin: 18px 0; font-size: 0.85rem; color: #C9C2B4; text-align: left; border-radius: 4px; }
    .success-summary strong { color: var(--text-ink); }

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
      .field-row { grid-template-columns: 1fr; }
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
    .nav-member-badge { margin-left: 8px; font-size: 0.55rem !important; padding: 2px 7px !important; vertical-align: middle; }
    .dk-profile-menu.open { display: block; }
    @media (max-width: 860px) { .dk-auth-slot { display: none; } }
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
          <li><a href="membership.php" class="nav-link active" aria-current="page">Membership<span id="navMemberBadge"><?php if ($currentPlan !== 'none') { ?><span class="dk-member-badge dk-badge-<?php echo $currentPlan; ?> nav-member-badge"><?php echo $currentPlan === 'gold' ? 'Gold' : 'Silver'; ?></span><?php } ?></span></a></li>
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
  <main id="main-content" tabindex="-1">
    <!-- Page Hero -->
    <section class="page-hero">
      <div class="container reveal">
<h1 class="font-serif">Deepkala Membership</h1>
      </div>
    </section>

    <div class="container" style="padding-bottom: 40px;">

      <div class="category-header reveal">
        <span class="eyebrow-tag">CHOOSE YOUR PLAN</span>
        <div class="hairline"></div>
      </div>

      <!-- Already a member banner -->
      <?php if ($currentPlan !== 'none') { ?>
        <div class="member-status-bar reveal show" id="memberStatusBar">
          <div class="member-status-text" id="memberStatusText">You're already a <strong><?php echo $currentPlan === 'gold' ? 'Gold Member' : 'Silver Member'; ?></strong> — enjoying <?php echo $currentPlan === 'gold' ? 20 : 10; ?>% off every service.</div>
        </div>
      <?php } ?>

      <div class="plans-grid">

        <!-- Silver Plan -->
        <div class="plan-card silver reveal">
          <div class="plan-name">Silver</div>
          <div class="plan-desc">A simple way to save on every regular visit.</div>
          <div class="plan-price">₹499 <span>/ year</span></div>
          <div class="plan-discount-tag">10% off all services</div>
          <ul class="plan-features">
            <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> 10% off every service, automatically applied</li>
            <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Valid for 12 months from purchase</li>
            <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Priority WhatsApp booking confirmation</li>
          </ul>
          <?php if ($currentPlan === 'silver') { ?>
            <button type="button" class="btn-choose-plan current" id="btnPlanSilver" disabled>Current Plan</button>
          <?php } else { ?>
            <button type="button" class="btn-choose-plan" onclick="openPlanCheckout('silver')" id="btnPlanSilver"><?php echo $currentPlan === 'gold' ? 'Switch to Silver' : 'Choose Silver'; ?></button>
          <?php } ?>
        </div>

        <!-- Gold Plan -->
        <div class="plan-card gold reveal">
          <div class="plan-badge-corner">Most Popular</div>
          <div class="plan-name">Gold</div>
          <div class="plan-desc">For regulars who want the best pricing on everything.</div>
          <div class="plan-price">₹999 <span>/ year</span></div>
          <div class="plan-discount-tag">20% off all services</div>
          <ul class="plan-features">
            <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> 20% off every service, automatically applied</li>
            <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Valid for 12 months from purchase</li>
            <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Priority WhatsApp booking confirmation</li>
            <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Complimentary head massage on bridal &amp; party packages</li>
          </ul>
          <?php if ($currentPlan === 'gold') { ?>
            <button type="button" class="btn-choose-plan current" id="btnPlanGold" disabled>Current Plan</button>
          <?php } else { ?>
            <button type="button" class="btn-choose-plan" onclick="openPlanCheckout('gold')" id="btnPlanGold"><?php echo $currentPlan === 'silver' ? 'Switch to Gold' : 'Choose Gold'; ?></button>
          <?php } ?>
        </div>

      </div>

    </div>
  </main>

  <!-- Checkout Modal -->
  <div class="modal-overlay" id="checkoutModal">
    <div class="modal-content">

      <!-- Payment Form State -->
      <form id="membershipForm" method="POST" action="membership.php">
      <input type="hidden" name="choose_plan" id="hiddenChoosePlan" value="">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
      <div id="modalPaymentForm">
        <h3 class="modal-title" id="checkoutPlanTitle">Silver Membership</h3>
        <p class="modal-sub">Demo mode: no real payment is processed. Do not enter real card details.</p>

        <span class="demo-badge">Demo Payment</span>

        <div class="payment-amount-bar">
          <span class="payment-amount-label" id="checkoutAmountLabel">Amount Payable</span>
          <span class="payment-amount-value" id="checkoutAmount">₹499</span>
        </div>
        <p id="switchNote" style="display:none;font-size:0.82rem;color:var(--bronze-taupe);margin:-10px 0 16px;"></p>

        <div class="pay-method-toggle" role="tablist" id="payMethodToggle">
          <div class="pay-method-pill selected" id="payTabCard" onclick="selectPayMethod('card')"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" style="vertical-align:-3px;margin-right:4px;"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>Card</div>
          <div class="pay-method-pill" id="payTabUpi" onclick="selectPayMethod('upi')"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" style="vertical-align:-3px;margin-right:4px;"><rect x="7" y="2" width="10" height="20" rx="2"/><line x1="11" y1="18" x2="13" y2="18"/></svg>UPI</div>
        </div>

        <div class="pay-fields active" id="cardFields">
          <div class="form-group">
            <label class="form-label" for="cardName">Name on Card *</label>
            <input type="text" id="cardName" class="form-input" placeholder="e.g. Yash Sathwara" autocomplete="cc-name" aria-describedby="err-cardName">
            <span class="field-error" id="err-cardName">Enter the name on the card.</span>
          </div>
          <div class="form-group">
            <label class="form-label" for="cardNumber">Card Number *</label>
            <input type="text" id="cardNumber" class="form-input" placeholder="1234 5678 9012 3456" inputmode="numeric" maxlength="19" autocomplete="cc-number" aria-describedby="err-cardNumber">
            <span class="field-error" id="err-cardNumber">Enter a valid 16-digit card number.</span>
          </div>
          <div class="field-row">
            <div class="form-group">
              <label class="form-label" for="cardExpiry">Expiry (MM/YY) *</label>
              <input type="text" id="cardExpiry" class="form-input" placeholder="MM/YY" maxlength="5" autocomplete="cc-exp" aria-describedby="err-cardExpiry">
              <span class="field-error" id="err-cardExpiry">Enter a valid future date.</span>
            </div>
            <div class="form-group">
              <label class="form-label" for="cardCvv">CVV *</label>
              <input type="password" id="cardCvv" class="form-input" placeholder="•••" inputmode="numeric" maxlength="4" autocomplete="cc-csc" aria-describedby="err-cardCvv">
              <span class="field-error" id="err-cardCvv">Enter a valid CVV.</span>
            </div>
          </div>
        </div>

        <div class="pay-fields" id="upiFields">
          <div class="form-group">
            <label class="form-label" for="upiId">UPI ID *</label>
            <input type="text" id="upiId" class="form-input" placeholder="yourname@upi" aria-describedby="err-upiId">
            <span class="field-error" id="err-upiId">Enter a valid UPI ID (e.g. name@bank).</span>
          </div>
        </div>

        <button type="button" class="btn-submit" onclick="submitMembershipPayment()">Pay &amp; Activate Membership</button>
        <button type="button" class="btn-link-muted" onclick="closeCheckoutModal()">Cancel</button>
      </div>
      </form>

      <!-- Processing State -->
      <div id="modalProcessing" style="display:none;">
        <div class="processing-box">
          <div class="spinner"></div>
          <h3 class="modal-title">Processing Payment…</h3>
          <p class="modal-sub" style="margin-bottom:0;">Please wait while we (simulate) confirming your payment.</p>
        </div>
      </div>

      <!-- Success State (no receipt/bill — simple confirmation only) -->
      <div id="modalSuccess" style="display:none;">
        <div class="success-box">
          <div class="success-check">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#3FDB74" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
          </div>
          <h3 class="modal-title" id="successTitle">Welcome to the Family!</h3>
          <p class="modal-sub" id="successSub">Your membership is now active.</p>

          <div class="success-summary" id="successSummary"></div>

          <button type="button" class="btn-submit" onclick="window.location.href='services.php'">Browse Services at Member Pricing</button>
          <button type="button" class="btn-link-muted" onclick="closeCheckoutModal()">Close</button>
        </div>
      </div>

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

  <a href="https://wa.me/919924620958?text=Hi%20Deepkala%20Hair%20Salon%2C%20I%27d%20like%20to%20know%20more" class="whatsapp-float" target="_blank" rel="noopener noreferrer" title="Chat on WhatsApp">
    <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
  </a>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => { if (entry.isIntersecting) entry.target.classList.add('active'); });
      }, { threshold: 0.1 });
      document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

      const menuBtn = document.getElementById('mobileMenuBtn');
      const navLinks = document.getElementById('navLinks');
      if (menuBtn && navLinks) {
        menuBtn.setAttribute('aria-expanded', 'false');
        const toggleMenu = (show) => {
          const isOpen = show !== undefined ? show : !navLinks.classList.contains('show');
          navLinks.classList.toggle('show', isOpen);
          menuBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        };
        menuBtn.addEventListener('click', (e) => { e.stopPropagation(); toggleMenu(); });
        document.addEventListener('click', (e) => { if (!navLinks.contains(e.target) && !menuBtn.contains(e.target)) toggleMenu(false); });
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') toggleMenu(false); });
        navLinks.querySelectorAll('a').forEach(link => link.addEventListener('click', () => toggleMenu(false)));
      }

      const header = document.getElementById('siteHeader');
      if (header) {
        window.addEventListener('scroll', () => {
          header.style.boxShadow = window.scrollY > 20 ? '0 4px 12px rgba(0,0,0,0.35)' : 'none';
        }, { passive: true });
      }

      const cardNumberEl = document.getElementById('cardNumber');
      if (cardNumberEl) cardNumberEl.addEventListener('input', () => {
        let digits = cardNumberEl.value.replace(/\D/g, '').slice(0, 16);
        cardNumberEl.value = digits.replace(/(.{4})/g, '$1 ').trim();
      });
      const cardExpiryEl = document.getElementById('cardExpiry');
      if (cardExpiryEl) cardExpiryEl.addEventListener('input', () => {
        let digits = cardExpiryEl.value.replace(/\D/g, '').slice(0, 4);
        cardExpiryEl.value = digits.length >= 3 ? digits.slice(0,2) + '/' + digits.slice(2) : digits;
      });
      const cardCvvEl = document.getElementById('cardCvv');
      if (cardCvvEl) cardCvvEl.addEventListener('input', () => {
        cardCvvEl.value = cardCvvEl.value.replace(/\D/g, '').slice(0, 4);
      });

      // (member status bar + plan buttons are rendered directly by PHP now)
    });

    const PLANS = {
      silver: { label: 'Silver Membership', price: 499, discount: 10 },
      gold:   { label: 'Gold Membership',   price: 999, discount: 20 }
    };

    var userIsLoggedIn = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;
    var userCurrentPlan = "<?php echo $currentPlan; ?>";

    let currentPlanKey = null;
    let currentPayMethod = 'card';
    let currentSwitchAmount = 0;

    function openPlanCheckout(planKey) {
      if (userCurrentPlan === planKey) return; // already on this plan

      if (!userIsLoggedIn) {
        window.location.href = 'login.php?redirect=membership.php';
        return;
      }

      currentPlanKey = planKey;
      const plan = PLANS[planKey];
      document.getElementById('checkoutPlanTitle').textContent = plan.label;

      const note = document.getElementById('switchNote');
      const label = document.getElementById('checkoutAmountLabel');
      const amountEl = document.getElementById('checkoutAmount');
      const toggle = document.getElementById('payMethodToggle');

      if (userCurrentPlan !== 'none') {
        // Already a member and switching plans — charge only the difference
        const oldPrice = PLANS[userCurrentPlan].price;
        const diff = plan.price - oldPrice;

        if (diff > 0) {
          // Upgrading (Silver → Gold): pay just the difference
          currentSwitchAmount = diff;
          label.textContent = 'Upgrade Amount Payable';
          amountEl.textContent = '₹' + diff.toLocaleString('en-IN');
          note.style.display = 'block';
          note.textContent = `Your existing ₹${oldPrice} ${userCurrentPlan} membership is adjusted — you only pay the ₹${diff} difference to upgrade to ${plan.label}.`;
          toggle.style.display = '';
          document.getElementById('modalPaymentForm').style.display = 'block';
        } else {
          // Downgrading (Gold → Silver): no payment, refund message instead
          currentSwitchAmount = 0;
          label.textContent = 'Amount Payable';
          amountEl.textContent = '₹0';
          note.style.display = 'block';
          note.textContent = `You're switching down from ${userCurrentPlan} to ${plan.label}. The ₹${Math.abs(diff)} difference will be refunded to your original payment method within 5–7 business days. No extra payment needed — just confirm below.`;
          toggle.style.display = 'none';
          document.getElementById('cardFields').classList.remove('active');
          document.getElementById('upiFields').classList.remove('active');
        }
      } else {
        currentSwitchAmount = plan.price;
        label.textContent = 'Amount Payable';
        amountEl.textContent = '₹' + plan.price.toLocaleString('en-IN');
        note.style.display = 'none';
        toggle.style.display = '';
      }

      document.getElementById('modalPaymentForm').style.display = 'block';
      document.getElementById('modalProcessing').style.display = 'none';
      document.getElementById('modalSuccess').style.display = 'none';
      document.getElementById('checkoutModal').style.display = 'flex';
    }

    function closeCheckoutModal() {
      document.getElementById('checkoutModal').style.display = 'none';
    }

    function selectPayMethod(method) {
      currentPayMethod = method;
      document.getElementById('payTabCard').classList.toggle('selected', method === 'card');
      document.getElementById('payTabUpi').classList.toggle('selected', method === 'upi');
      document.getElementById('cardFields').classList.toggle('active', method === 'card');
      document.getElementById('upiFields').classList.toggle('active', method === 'upi');
      document.querySelectorAll('.field-error').forEach(el => el.classList.remove('show'));
      document.querySelectorAll('.form-input.invalid').forEach(el => el.classList.remove('invalid'));
    }

    function showFieldError(id, show) {
      const err = document.getElementById('err-' + id);
      const input = document.getElementById(id);
      if (err) err.classList.toggle('show', show);
      if (input) {
        input.classList.toggle('invalid', show);
        input.setAttribute('aria-invalid', show ? 'true' : 'false');
      }
    }

    // Card & UPI validation — same rules as the booking page:
    // card = 16 digits, expiry must be a real MM/YY that hasn't passed, CVV 3-4 digits
    // UPI = something like "name@bank"
    function validatePayment() {
      let valid = true;
      if (currentPayMethod === 'card') {
        const name = document.getElementById('cardName').value.trim();
        const number = document.getElementById('cardNumber').value.replace(/\s/g, '');
        const expiry = document.getElementById('cardExpiry').value.trim();
        const cvv = document.getElementById('cardCvv').value.trim();

        const nameOk = name.length >= 2;
        showFieldError('cardName', !nameOk); if (!nameOk) valid = false;

        const numberOk = /^\d{16}$/.test(number);
        showFieldError('cardNumber', !numberOk); if (!numberOk) valid = false;

        let expiryOk = /^\d{2}\/\d{2}$/.test(expiry);
        if (expiryOk) {
          const [mm, yy] = expiry.split('/').map(Number);
          const now = new Date();
          const curYY = now.getFullYear() % 100;
          const curMM = now.getMonth() + 1;
          if (mm < 1 || mm > 12) expiryOk = false;
          else if (yy < curYY || (yy === curYY && mm < curMM)) expiryOk = false;
        }
        showFieldError('cardExpiry', !expiryOk); if (!expiryOk) valid = false;

        const cvvOk = /^\d{3,4}$/.test(cvv);
        showFieldError('cardCvv', !cvvOk); if (!cvvOk) valid = false;
      } else {
        const upi = document.getElementById('upiId').value.trim();
        const upiOk = /^[\w.\-]{2,}@[a-zA-Z]{2,}$/.test(upi);
        showFieldError('upiId', !upiOk); if (!upiOk) valid = false;
      }
      return valid;
    }

    // Card → "Card •••• 1234", UPI → "UPI (name@bank)" — this exact string
    // is what gets saved in appointments.payment_method / this membership purchase
    function maskPaymentMethod() {
      if (currentPayMethod === 'card') {
        const number = document.getElementById('cardNumber').value.replace(/\s/g, '');
        return `Card •••• ${number.slice(-4)}`;
      } else {
        const upi = document.getElementById('upiId').value.trim();
        return `UPI (${upi})`;
      }
    }

    function submitMembershipPayment() {
      // Downgrading (₹0 payable) skips card/UPI entry entirely
      if (currentSwitchAmount > 0 && !validatePayment()) return;

      // Show the "processing" look while the real page reload happens
      document.getElementById('modalPaymentForm').style.display = 'none';
      document.getElementById('modalProcessing').style.display = 'block';

      // Real form submit (no fetch/AJAX) — PHP saves it to the database
      document.getElementById('hiddenChoosePlan').value = currentPlanKey;
      document.getElementById('membershipForm').submit();
    }

    // If PHP just activated a plan (real page reload after "Pay & Activate"),
    // show the success state of the same modal straight away.
    var planJustActivated = <?php echo $planJustActivated ? 'true' : 'false'; ?>;
    <?php if ($planJustActivated) { ?>
    window.addEventListener('DOMContentLoaded', function () {
      document.getElementById('successTitle').textContent = <?php echo json_encode($currentPlan === 'gold' ? 'Welcome to Gold!' : 'Welcome to Silver!'); ?>;
      document.getElementById('successSub').textContent = 'Your membership is now active.';
      document.getElementById('successSummary').innerHTML =
        '<strong><?php echo $currentPlan === 'gold' ? 'Gold Membership' : 'Silver Membership'; ?></strong> activated<br><?php echo $currentPlan === 'gold' ? 20 : 10; ?>% off every service, automatically';

      document.getElementById('modalPaymentForm').style.display = 'none';
      document.getElementById('modalProcessing').style.display = 'none';
      document.getElementById('modalSuccess').style.display = 'block';
      document.getElementById('checkoutModal').style.display = 'flex';
    });
    <?php } ?>
  </script>
</body>
</html>
