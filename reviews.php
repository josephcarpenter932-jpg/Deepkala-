<?php
require 'backend/config.php';
$isLoggedIn = isset($_SESSION['user_id']);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create_review') {
    header('Content-Type: application/json');
    require_csrf();
    $name = trim($_POST['name'] ?? '');
    $service = trim($_POST['service'] ?? '');
    $rating = (int) ($_POST['rating'] ?? 0);
    $message = trim($_POST['message'] ?? '');
    if (!preg_match('/^[A-Za-z ]{2,100}$/', $name) || $service === '' || $rating < 1 || $rating > 5 || strlen($message) < 5 || strlen($message) > 2000) {
        http_response_code(422);
        echo json_encode(['ok' => false, 'error' => 'Please provide a valid name, service, rating, and review message.']);
        exit;
    }
    if (isset($_SESSION['user_id'])) {
        $userId = (int) $_SESSION['user_id'];
        $stmt = mysqli_prepare($conn, "INSERT INTO reviews (user_id, name, service, rating, message, status) VALUES (?, ?, ?, ?, ?, 'pending')");
        mysqli_stmt_bind_param($stmt, 'issis', $userId, $name, $service, $rating, $message);
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO reviews (user_id, name, service, rating, message, status) VALUES (NULL, ?, ?, ?, ?, 'pending')");
        mysqli_stmt_bind_param($stmt, 'ssis', $name, $service, $rating, $message);
    }
    if (!mysqli_stmt_execute($stmt)) {
        http_response_code(500);
        echo json_encode(['ok' => false, 'error' => 'Could not save the review.']);
        exit;
    }
    echo json_encode(['ok' => true, 'message' => 'Thank you. Your review was submitted for approval.']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Client Reviews & Feedback — Deepkala Hair Salon, Ahmedabad</title>

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
    }

    html {
      scroll-behavior: smooth;
      font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif; /* ← FONT-FAMILY UPDATED */
      background-color: var(--bg-cream);
      color: var(--text-ink);
      line-height: 1.6;
      text-size-adjust: 100%;
      -webkit-text-size-adjust: 100%;
      overflow-x: hidden;
      width: 100%;
    }

    body {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      width: 100%;
      overflow-x: hidden;
    }

    img, svg, iframe, video {
      max-width: 100%;
      height: auto;
    }

    h1, h2, h3, h4, h5, h6, p, span, a, label, button, input, select, textarea {
      overflow-wrap: break-word;
      word-break: break-word;
    }

    a { color: inherit; text-decoration: none; }

    .container {
      width: 100%;
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 24px;
    }

    @media (max-width: 768px) {
      .container {
        padding: 0 16px;
      }
    }

    @media (max-width: 380px) {
      .container {
        padding: 0 12px;
      }
    }

    .font-serif { font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; /* ← FONT-FAMILY UPDATED */ }
    h1.font-serif { font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; /* ← FONT-FAMILY UPDATED */ font-variation-settings: "opsz" 144, "SOFT" 0, "WONK" 0; }
    .font-sans { font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif; /* ← FONT-FAMILY UPDATED */ }

    /* Header */
    .site-header {
      position: sticky;
      top: 0;
      z-index: 100;
      background-color: rgba(21, 19, 14, 0.95);
      backdrop-filter: blur(8px);
      border-bottom: 1px solid var(--border-hairline);
      transition: padding 0.3s ease, box-shadow 0.3s ease;
    }

    .header-inner {
      display: flex;
      align-items: center;
      justify-content: space-between;
      height: 76px;
    }

    .brand-logo { display: flex; align-items: center; gap: 12px; }

    .logo-text-title {
      font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; /* ← FONT-FAMILY UPDATED */
      font-variation-settings: "opsz" 144, "SOFT" 0, "WONK" 0;
      font-size: 1.25rem;
      font-weight: 700;
      letter-spacing: 0.06em;
      color: var(--text-ink);
      line-height: 1.1;
    }

    .logo-text-sub {
      font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif; /* ← FONT-FAMILY UPDATED */
      font-size: 0.65rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.12em;
      color: var(--bronze-taupe);
    }

    .nav-links { display: flex; align-items: center; gap: 28px; list-style: none; }

    .nav-link {
      font-size: 0.825rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: var(--text-ink);
      padding: 6px 0;
      position: relative;
      transition: color 0.2s ease;
    }

    .nav-link:hover, .nav-link.active { color: var(--rose-accent); }

    .nav-link.active::after {
      content: '';
      position: absolute;
      bottom: 0; left: 0;
      width: 100%; height: 2px;
      background-color: var(--rose-accent);
    }

    .header-book-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 9px 20px;
      font-size: 0.8rem;
      font-weight: 600;
      letter-spacing: 0.06em;
      text-transform: uppercase;
      background-color: var(--rose-accent);
      color: #15130E;
      border: 1px solid var(--rose-accent);
      border-radius: 4px;
      transition: all 0.2s ease;
      white-space: nowrap;
    }

    .header-book-btn:hover {
      background-color: var(--rose-accent-hover);
      border-color: var(--rose-accent-hover);
      color: #15130E;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(10, 8, 6, 0.35);
    }

    .btn-whatsapp-cta {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      width: 100%;
      padding: 10px 16px;
      background-color: var(--whatsapp-green);
      color: #15130E !important;
      font-size: 0.85rem;
      font-weight: 600;
      border-radius: 4px;
      transition: background-color 0.2s ease;
      box-shadow: 0 2px 8px rgba(37, 211, 102, 0.25);
    }

    .btn-whatsapp-cta:hover {
      background-color: #20BA56;
      color: #15130E !important;
    }

    .mobile-toggle {
      display: none;
      background: none;
      border: none;
      cursor: pointer;
      padding: 8px;
      color: var(--text-ink);
    }

    /* Page Hero */
    .page-hero {
      position: relative;
      padding: 60px 0 40px 0;
      text-align: center;
      border-bottom: 1px solid var(--border-hairline);
      background-color: var(--bg-cream);
      overflow: hidden;
      isolation: isolate;
    }

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

    .page-hero::after {
      content: "";
      position: absolute;
      inset: 0;
      background: linear-gradient(160deg, rgba(10,8,6,0.82) 0%, rgba(201,166,107,0.38) 55%, rgba(10,8,6,0.88) 100%);
      z-index: -1;
    }

    .page-hero > .container {
      position: relative;
      z-index: 1;
    }

    .page-hero h1 {
      font-size: clamp(2.2rem, 4vw, 3.2rem);
      font-weight: 700;
      color: var(--text-ink);
      margin-bottom: 12px;
    }

    .page-hero p {
      font-size: 1rem;
      color: #B3AA98;
      max-width: 620px;
      margin: 0 auto;
    }

    .seal-motif {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      position: relative;
      width: 48px;
      height: 48px;
      margin-bottom: 12px;
    }

    .seal-motif .ring-outer {
      position: absolute; inset: 0;
      border: 1px solid var(--bronze-taupe);
      border-radius: 50%; opacity: 0.7;
    }

    .seal-motif .ring-inner {
      position: absolute; inset: 5px;
      border: 1px dashed var(--rose-deep);
      border-radius: 50%; opacity: 0.8;
    }

    .seal-motif .seal-text {
      font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; /* ← FONT-FAMILY UPDATED */
      font-weight: 700; font-size: 1.1rem;
      color: var(--text-ink);
    }

    /* Reviews Grid & Cards */
    .eyebrow-tag {
      display: inline-block;
      background-color: var(--text-ink);
      color: var(--bg-cream);
      padding: 6px 14px;
      font-size: 0.75rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.12em;
    }

    .category-header {
      display: flex;
      align-items: center;
      gap: 16px;
      margin-top: 48px;
      margin-bottom: 16px;
    }

    .category-header .hairline {
      flex: 1; height: 1px;
      background-color: var(--border-hairline);
    }

    .reviews-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 24px;
    }

    @media (min-width: 768px) {
      .reviews-grid {
        grid-template-columns: 1fr 1fr;
      }
    }

    .review-card {
      background-color: var(--card-white);
      border: 1px solid var(--border-hairline);
      padding: 28px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .review-header {
      display: flex;
      align-items: center;
      gap: 14px;
      margin-bottom: 16px;
    }

    .avatar-circle {
      width: 46px;
      height: 46px;
      border-radius: 50%;
      background-color: var(--card-cream);
      border: 1px solid var(--bronze-taupe);
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; /* ← FONT-FAMILY UPDATED */
      font-weight: 700;
      color: var(--rose-accent);
      font-size: 1.1rem;
      flex-shrink: 0;
      overflow: hidden;
    }

    .avatar-circle img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .reviewer-name {
      font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; /* ← FONT-FAMILY UPDATED */
      font-size: 1.1rem;
      font-weight: 700;
      color: var(--text-ink);
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.65);
    }

    .reviewer-service {
      font-size: 0.72rem;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: var(--bronze-taupe);
    }

    .star-rating {
      color: var(--rose-accent);
      margin-bottom: 12px;
      font-size: 1.1rem;
    }

    .review-body {
      font-size: 0.925rem;
      color: #F0EBDF;
      line-height: 1.6;
      margin-bottom: 16px;
    }

    .review-photo-preview {
      width: 100%;
      max-height: 200px;
      object-fit: cover;
      border: 1px solid var(--border-hairline);
      margin-top: 12px;
    }

    .review-date {
      font-size: 0.72rem;
      color: #9C9284;
      text-transform: uppercase;
      letter-spacing: 0.08em;
    }

    /* Review Form Section */
    .form-section {
      background-color: var(--card-cream);
      border: 1px solid var(--border-hairline);
      padding: 40px;
      margin-top: 60px;
    }

    .form-title {
      font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; /* ← FONT-FAMILY UPDATED */
      font-size: 1.75rem;
      font-weight: 700;
      margin-bottom: 8px;
    }

    .form-desc {
      font-size: 0.9rem;
      color: #9C9284;
      margin-bottom: 16px;
    }

    .form-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 20px;
    }

    @media (min-width: 768px) {
      .form-grid { grid-template-columns: 1fr 1fr; }
    }

    .form-group {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .form-group.full {
      grid-column: 1 / -1;
    }

    .form-label {
      font-size: 0.78rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: var(--text-ink);
    }

    .form-input, .form-select, .form-textarea {
      padding: 12px 16px;
      border: 1px solid var(--border-hairline);
      background-color: #1E1A14;
      font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif; /* ← FONT-FAMILY UPDATED */
      font-size: 16px;
      color: var(--text-ink);
      width: 100%;
      min-height: 44px;
    }

    .form-input:focus, .form-select:focus, .form-textarea:focus {
      outline: 2px solid var(--rose-accent);
      border-color: var(--rose-accent);
    }

    /* Star Picker */
    .star-picker-fieldset {
      border: 0;
      margin: 0;
      padding: 0;
      min-width: 0;
    }

    .star-picker-fieldset legend {
      padding: 0;
      margin-bottom: 8px;
    }

    .star-picker {
      display: flex;
      gap: 8px;
      font-size: 1.5rem;
      cursor: pointer;
      color: #6B6152;
    }

    .star-picker .star {
      transition: color 0.15s ease;
      position: relative;
      cursor: pointer;
    }

    .star-picker .star.active, .star-picker .star:hover {
      color: var(--rose-accent);
    }

    .star-radio {
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

    .star-radio:focus-visible + .star {
      outline: 2px solid var(--rose-accent, #C9A66B);
      outline-offset: 3px;
      border-radius: 3px;
    }

    .btn-primary {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      background-color: var(--rose-accent);
      color: #15130E;
      padding: 14px 28px;
      font-size: 0.825rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      border: 1px solid var(--rose-accent);
      cursor: pointer;
      transition: all 0.2s ease;
      min-height: 44px;
    }

    .btn-primary:hover {
      background-color: var(--rose-accent-hover);
      border-color: var(--rose-accent-hover);
    }

    /* Footer */
    .site-footer {
      position: relative;
      margin: 80px 12px 12px;
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

    /* Floating WhatsApp Button */
    .whatsapp-float {
      position: fixed;
      bottom: max(20px, env(safe-area-inset-bottom, 20px));
      right: max(20px, env(safe-area-inset-right, 20px));
      width: 52px;
      height: 52px;
      min-width: 48px;
      min-height: 48px;
      background-color: var(--whatsapp-green);
      color: #15130E;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 16px rgba(37, 211, 102, 0.35);
      z-index: 999;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .whatsapp-float:hover {
      transform: scale(1.08) translateY(-2px);
      box-shadow: 0 6px 20px rgba(37, 211, 102, 0.5);
    }

    .reveal {
      opacity: 0; transform: translateY(20px);
      transition: opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1), transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .reveal.active { opacity: 1; transform: translateY(0); }

    .mobile-menu-cta {
      display: none;
    }

    @media (max-width: 860px) {
      .nav-links {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background-color: var(--bg-cream);
        border-bottom: 2px solid var(--rose-accent);
        flex-direction: column;
        padding: 16px 20px 24px 20px;
        gap: 8px;
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.45);
        z-index: 999;
        max-height: calc(100vh - 80px);
        overflow-y: auto;
      }

      .nav-links.show {
        display: flex;
      }

      .nav-link {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 14px;
        font-size: 0.9rem;
        width: 100%;
        border-bottom: 1px solid var(--border-hairline);
        border-radius: 4px;
      }

      .nav-link:hover, .nav-link.active {
        background-color: var(--card-cream);
      }

      .mobile-toggle {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 44px;
        min-height: 44px;
        border: 1px solid var(--border-hairline);
        border-radius: 4px;
      }

      .nav-phone {
        display: none;
      }

      .mobile-menu-cta {
        display: block;
        margin-top: 8px;
      }
    }
      /* ===== Deepkala Membership & Auth (shared) ===== */
    .dk-price-old {
      text-decoration: line-through;
      color: #8A8171;
      font-size: 0.78em;
      font-weight: 500;
      margin-right: 2px;
    }
    .dk-price-new {
      color: var(--rose-accent);
      font-weight: 700;
    }
    .dk-member-priced {
      display: inline-flex;
      align-items: baseline;
      gap: 7px;
      flex-wrap: wrap;
      justify-content: flex-end;
    }
    .dk-auth-slot {
      display: flex;
      align-items: center;
    }
    .dk-login-link {
      font-size: 0.8rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      color: var(--text-ink);
      border: 1px solid var(--border-hairline);
      padding: 9px 16px;
      border-radius: 4px;
      transition: all 0.2s ease;
      white-space: nowrap;
    }
    .dk-login-link:hover {
      border-color: var(--rose-accent);
      color: var(--rose-accent);
    }
    .dk-account-pill {
      display: flex;
      align-items: center;
      gap: 10px;
      flex-wrap: wrap;
    }
    .dk-account-name {
      font-size: 0.8rem;
      font-weight: 600;
      color: var(--text-ink);
      white-space: nowrap;
    }
    .dk-member-badge {
      font-size: 0.6rem;
      font-weight: 700;
      letter-spacing: 0.05em;
      text-transform: uppercase;
      padding: 3px 9px;
      border-radius: 4px;
      white-space: nowrap;
    }
    .dk-badge-silver {
      background-color: rgba(201, 166, 107, 0.15);
      color: #C9A66B;
      border: 1px solid rgba(201, 166, 107, 0.4);
    }
    .dk-badge-gold {
      background-color: rgba(255, 214, 102, 0.15);
      color: #FFD666;
      border: 1px solid rgba(255, 214, 102, 0.45);
    }
    .dk-logout-btn {
      background: none;
      border: none;
      font-size: 0.7rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: var(--bronze-taupe);
      cursor: pointer;
      padding: 4px;
    }
    .dk-logout-btn:hover { color: var(--rose-accent); }
.nav-member-badge { margin-left: 8px; font-size: 0.55rem !important; padding: 2px 7px !important; vertical-align: middle; }

    @media (max-width: 860px) {
      .dk-auth-slot { display: none; }
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
      <a href="index.php" class="brand-logo" aria-label="Deepkala Hair Salon">
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
          <li><a href="reviews.php" class="nav-link active" aria-current="page">Reviews</a></li>
          <li><a href="about.php" class="nav-link">About</a></li>
          <li><a href="contact.php" class="nav-link">Contact</a></li>
          <li><a href="membership.php" class="nav-link">Membership<span id="navMemberBadge"><?php $dkTier = $_SESSION['membership'] ?? 'none'; if ($dkTier === 'gold' || $dkTier === 'silver') { echo '<span class="dk-member-badge dk-badge-' . $dkTier . ' nav-member-badge">' . ucfirst($dkTier) . '</span>'; } ?></span></a></li>
          <li class="mobile-menu-cta">
            <a href="https://wa.me/919924620958?text=Hello%20Deepkala%20Hair%20Salon%2C%20I%20would%20like%20to%20book%20an%20appointment" target="_blank" rel="noopener noreferrer" class="btn-whatsapp-cta">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.893 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
              Chat on WhatsApp
            </a>
          </li>
        </ul>
      </nav>

      <div style="display: flex; align-items: center; gap: 12px;">
        <a href="booking.php" class="header-book-btn" title="Book Appointment at Deepkala Hair Salon">
          Book Appointment
        </a>
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
<h1 class="font-serif">Client Reviews &amp; Stories</h1>
      </div>
    </section>

    <div class="container" style="padding-bottom: 60px;">
      
      <!-- Reviews Header -->
      <div class="category-header reveal">
        <span class="eyebrow-tag">VERIFIED CLIENT REVIEWS</span>
        <div class="hairline"></div>
      </div>

      <!-- Pre-written Reviews Grid -->
      <div class="reviews-grid" id="reviewsGrid">
        
        <div class="review-card reveal">
          <div>
            <div class="review-header">
              <div class="avatar-circle">RP</div>
              <div>
                <h3 class="reviewer-name">Rahul Patel</h3>
                <div class="reviewer-service">SKIN FADE &amp; BEARD SHAPE-UP</div>
              </div>
            </div>
            <div class="star-rating" role="img" aria-label="5 out of 5 stars">★★★★★</div>
            <p class="review-body">"Best fade in Ghodasar! Deepesh bhai is extremely skilled and takes time to get the lines perfect. Clean salon and very comfortable atmosphere."</p>
          </div>
          <div class="review-date">JUNE 2026</div>
        </div>

        <div class="review-card reveal">
          <div>
            <div class="review-header">
              <div class="avatar-circle">PS</div>
              <div>
                <h3 class="reviewer-name">Parth Shah</h3>
                <div class="reviewer-service">HAIR SPA &amp; CLASSIC CUT</div>
              </div>
            </div>
            <div class="star-rating" role="img" aria-label="5 out of 5 stars">★★★★★</div>
            <p class="review-body">"Got a hair spa and classic haircut before an event. My hair felt so smooth and healthy. Outstanding value for money at ₹600."</p>
          </div>
          <div class="review-date">MAY 2026</div>
        </div>

        <div class="review-card reveal">
          <div>
            <div class="review-header">
              <div class="avatar-circle">HJ</div>
              <div>
                <h3 class="reviewer-name">Hetal Joshi</h3>
                <div class="reviewer-service">KERATIN TREATMENT</div>
              </div>
            </div>
            <div class="star-rating" role="img" aria-label="5 out of 5 stars">★★★★★</div>
            <p class="review-body">"The Keratin treatment at Deepkala completely changed my hair texture. No frizz even in summer! Very attentive service."</p>
          </div>
          <div class="review-date">MAY 2026</div>
        </div>

        <div class="review-card reveal">
          <div>
            <div class="review-header">
              <div class="avatar-circle">AV</div>
              <div>
                <h3 class="reviewer-name">Amit Varma</h3>
                <div class="reviewer-service">BEARD TRIM &amp; HOT TOWEL SHAVE</div>
              </div>
            </div>
            <div class="star-rating" role="img" aria-label="5 out of 5 stars">★★★★★</div>
            <p class="review-body">"The hot towel shave experience is super relaxing. Razor work is extremely precise and painless. Highly recommended for beard lovers."</p>
          </div>
          <div class="review-date">APRIL 2026</div>
        </div>

        <div class="review-card reveal">
          <div>
            <div class="review-header">
              <div class="avatar-circle">VS</div>
              <div>
                <h3 class="reviewer-name">Vikrant Solanki</h3>
                <div class="reviewer-service">UNDERCUT &amp; HAIR WASH</div>
              </div>
            </div>
            <div class="star-rating" role="img" aria-label="5 out of 5 stars">★★★★★</div>
            <p class="review-body">"Regular client for 2 years now. Always consistent quality, hygienic tools, and friendly staff. Never disappointed."</p>
          </div>
          <div class="review-date">APRIL 2026</div>
        </div>

        <div class="review-card reveal">
          <div>
            <div class="review-header">
              <div class="avatar-circle">SM</div>
              <div>
                <h3 class="reviewer-name">Sneha Mehta</h3>
                <div class="reviewer-service">GLOBAL COLOR &amp; STYLING</div>
              </div>
            </div>
            <div class="star-rating" role="img" aria-label="5 out of 5 stars">★★★★★</div>
            <p class="review-body">"They matched my exact requested color tone. Very clean work and friendly consultation before starting."</p>
          </div>
          <div class="review-date">MARCH 2026</div>
        </div>

      </div>

      <!-- Write a Review Form -->
      <section class="form-section reveal">
        <h2 class="form-title">Write a Review</h2>
        <p class="form-desc">Visited Deepkala Hair Salon recently? Share your feedback and optional photo with our community.</p>

        <div id="reviewSuccess" class="sr-only" role="status" aria-live="polite" aria-atomic="true"></div>

        <form id="reviewForm" onsubmit="handleReviewSubmit(event)">
          <div class="form-grid">
            
            <div class="form-group">
              <label class="form-label" for="revName">Your Full Name *</label>
              <input type="text" id="revName" class="form-input" placeholder="e.g. Chirag Patel" required>
            </div>

            <div class="form-group">
              <label class="form-label" for="revService">Service Used *</label>
              <select id="revService" class="form-select" required>
                <option value="" disabled selected>Select a service</option>
                <option value="">--- Cut & Style ---</option>
                <option value="Buzz Cut — (₹200)">Buzz Cut — ₹200</option>
                  <option value="Crew Cut — (₹250)">Crew Cut — ₹250</option>
                  <option value="Fade Cut — (₹350)">Fade Cut — ₹350</option>
                  <option value="Undercut — (₹400)">Undercut — ₹400</option>
                  <option value="Mid Fade — (₹500)">Mid Fade — ₹500</option>
                  <option value="Quiff — (₹450)">Quiff — ₹450</option>
                  <option value="Side Part — (₹300)">Side Part — ₹300</option>
                  <option value="French Crop — (₹350)">French Crop — ₹350</option>
                  <option value="Texture Crop — (₹450)">Texture Crop — ₹450</option>
                  <option value="Ceaser Cut — (₹300)">Ceaser Cut — ₹300</option>
                  <option value="">--- Coloring ---</option>
                  <option value="Root Touch-Up — (₹3200)">Root Touch-Up — ₹3200</option>
                  <option value="Highlights — (₹1700)">Highlights — ₹1700</option>
                  <option value="Low Lights — (₹1500)">Low Lights — ₹1500</option>
                  <option value="Gloss Treatment — (₹2000)">Gloss Treatment — ₹2000</option>
                  <option value="Color Correction — (₹1600)">Color Correction — ₹1600</option>
                  <option value="Fasion Color — (₹1900)">Fasion Color — ₹1900</option>
                  <option value="">--- Spa & Touchup ---</option>
                  <option value="Relaxation Spa — (₹800)">Relaxation Spa — ₹800</option>
                  <option value="Aroma Treatment — (₹1200)">Aroma Treatment — ₹1200</option>
                  <option value="Head Spa — (₹700)">Head Spa — ₹700</option>
                  <option value="Deep Tissue Spa — (₹1400)">Deep Tissue Spa — ₹1400</option>
                  <option value="Scalp Treatment — (₹700)">Scalp Treatment — ₹700</option>
                  <option value="Hydrating Spa — (₹900)">Hydrating Spa — ₹900</option>
                  <option value="">--- Beard Grooming ---</option>
                  <option value="Goatee Beard — (₹500)">Goatee Beard — ₹500</option>
                  <option value="French Beard — (₹400)">French Beard — ₹400</option>
                  <option value="Full Beard Style — (₹600)">Full Beard Style — ₹600</option>
                  <option value="Italian Beard — (₹500)">Italian Beard — ₹500</option>
                  <option value="Stubble Beard — (₹250)">Stubble Beard — ₹250</option>
                  <option value="Corporate Beard — (₹350)">Corporate Beard — ₹350</option>
                  <option value="Anchor Beard — (₹500)">Anchor Beard — ₹500</option>
                  <option value="Boxed Beard — (₹400)">Boxed Beard — ₹400</option>
                  <option value="">--- Bridal & Party ---</option>
                  <option value="Special Bridal Package — (₹8000)">Special Bridal Package — ₹8000</option>
                  <option value="Bridal Package (₹5000)">Bridal Package — ₹5000</option>
                  <option value="Party Makeup + Hair (₹2500)">Party Makeup + Hair — ₹2500</option>
                  <option value="Groom Package (₹1500)">Groom Package — ₹1500</option>
              </select>
            </div>

            <div class="form-group">
              <fieldset class="star-picker-fieldset">
                <legend class="form-label">Your Rating *</legend>
                <div class="star-picker" id="starPicker">
                  <input type="radio" class="star-radio" name="revRatingRadio" id="revStar1" value="1" data-value="1">
                  <label class="star active" for="revStar1" data-value="1" aria-label="1 star">★</label>
                  <input type="radio" class="star-radio" name="revRatingRadio" id="revStar2" value="2" data-value="2">
                  <label class="star active" for="revStar2" data-value="2" aria-label="2 stars">★</label>
                  <input type="radio" class="star-radio" name="revRatingRadio" id="revStar3" value="3" data-value="3">
                  <label class="star active" for="revStar3" data-value="3" aria-label="3 stars">★</label>
                  <input type="radio" class="star-radio" name="revRatingRadio" id="revStar4" value="4" data-value="4">
                  <label class="star active" for="revStar4" data-value="4" aria-label="4 stars">★</label>
                  <input type="radio" class="star-radio" name="revRatingRadio" id="revStar5" value="5" data-value="5" checked>
                  <label class="star active" for="revStar5" data-value="5" aria-label="5 stars">★</label>
                </div>
              </fieldset>
              <input type="hidden" id="revRating" value="5">
            </div>

            <div class="form-group">
              <label class="form-label" for="revPhoto">Optional Hair/Style Photo</label>
              <input type="file" id="revPhoto" class="form-input" accept="image/*" onchange="previewReviewPhoto(event)">
            </div>

            <div class="form-group full">
              <label class="form-label" for="revMessage">Your Review Message *</label>
              <textarea id="revMessage" class="form-textarea" rows="4" placeholder="Tell us about your haircut, service quality, and experience..." required></textarea>
            </div>

          </div>

          <!-- Photo Image Preview Container -->
          <div id="photoPreviewContainer" style="display: none; margin-top: 16px;">
            <div style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: var(--bronze-taupe); margin-bottom: 6px;">Image Preview:</div>
            <img id="photoPreviewImg" style="max-height: 180px; border: 1px solid var(--border-hairline);" alt="Review Photo Preview">
          </div>

          <div style="margin-top: 24px;">
            <button type="submit" class="btn-primary">Post Review</button>
          </div>

        </form>
      </section>

    </div>
  </main>

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

  <!-- Floating WhatsApp -->
  <a href="https://wa.me/919924620958?text=Hi%20Deepkala%20Hair%20Salon%2C%20I%27d%20like%20to%20know%20more" class="whatsapp-float" target="_blank" rel="noopener noreferrer" title="Chat on WhatsApp">
    <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
  </a>

  <script>
    let currentPhotoDataUrl = null;
    function escapeReviewText(value) {
      return String(value).replace(/[&<>\"']/g, ch => ({'&':'&amp;','<':'&lt;','>':'&gt;','\"':'&quot;',"'":'&#039;'}[ch]));
    }

    document.addEventListener('DOMContentLoaded', () => {
      // Intersection Observer
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) entry.target.classList.add('active');
        });
      }, { threshold: 0.1 });

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

        document.addEventListener('click', (e) => {
          if (!navLinks.contains(e.target) && !menuBtn.contains(e.target)) {
            toggleMenu(false);
          }
        });

        document.addEventListener('keydown', (e) => {
          if (e.key === 'Escape') {
            toggleMenu(false);
          }
        });

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

      // Star Picker Logic
      const starRadios = document.querySelectorAll('#starPicker .star-radio');
      const starLabels = document.querySelectorAll('#starPicker .star');
      const ratingInput = document.getElementById('revRating');

      starRadios.forEach(radio => {
        radio.addEventListener('change', () => {
          const val = parseInt(radio.value, 10);
          ratingInput.value = val;
          starLabels.forEach(label => {
            const labelVal = parseInt(label.getAttribute('data-value'), 10);
            label.classList.toggle('active', labelVal <= val);
          });
        });
      });
    });

    // Preview uploaded photo via FileReader
    function previewReviewPhoto(event) {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        if (!file.type.startsWith('image/') || file.size > 5 * 1024 * 1024) { alert('Please choose an image smaller than 5 MB.'); event.target.value = ''; return; }
        reader.onload = function(e) {
          currentPhotoDataUrl = e.target.result;
          const container = document.getElementById('photoPreviewContainer');
          const img = document.getElementById('photoPreviewImg');
          img.src = currentPhotoDataUrl;
          container.style.display = 'block';
        };
        reader.readAsDataURL(file);
      }
    }

    // Handle Review Form Submission (Appends to top of review list in session)
    async function handleReviewSubmit(e) {
      e.preventDefault();

      const name = document.getElementById('revName').value.trim();
      const service = document.getElementById('revService').value;
      const ratingVal = parseInt(document.getElementById('revRating').value);
      const message = document.getElementById('revMessage').value.trim();

      if (!name || !service || !message) return;

      const body = new FormData();
      body.append('action', 'create_review');
      body.append('name', name);
      body.append('service', service);
      body.append('rating', String(ratingVal));
      body.append('message', message);
      body.append('csrf_token', '<?php echo htmlspecialchars(csrf_token()); ?>');
      try {
        const response = await fetch('reviews.php', { method: 'POST', body });
        const saved = await response.json();
        if (!response.ok || !saved.ok) throw new Error(saved.error || 'Could not save the review.');
      } catch (error) {
        const status = document.getElementById('reviewSuccess');
        status.textContent = error.message;
        status.classList.remove('sr-only');
        return;
      }

      // Extract Initials
      const initials = name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2) || 'CL';

      // Build Star String
      let starsHtml = '';
      for (let i = 0; i < 5; i++) {
        starsHtml += (i < ratingVal) ? '★' : '☆';
      }

      // Create Review Element
      const reviewCard = document.createElement('div');
      reviewCard.className = 'review-card reveal active';
      reviewCard.style.border = '2px solid var(--rose-accent)';

      let photoHtml = '';
      if (currentPhotoDataUrl) {
        photoHtml = `<img src="${currentPhotoDataUrl}" class="review-photo-preview" alt="Customer haircut photo preview">`;
      }

      reviewCard.innerHTML = `
        <div>
          <div class="review-header">
            <div class="avatar-circle">${initials}</div>
            <div>
              <h3 class="reviewer-name">${escapeReviewText(name)}</h3>
              <div class="reviewer-service">${escapeReviewText(service.toUpperCase())}</div>
            </div>
          </div>
          <div class="star-rating" role="img" aria-label="${ratingVal} out of 5 stars">${starsHtml}</div>
          <p class="review-body">"${escapeReviewText(message)}"</p>
          ${photoHtml}
        </div>
        <div class="review-date">JUST NOW (NEW SESSION REVIEW)</div>
      `;

      // Prepend to grid
      const grid = document.getElementById('reviewsGrid');
      grid.insertBefore(reviewCard, grid.firstChild);

      // Reset Form
      document.getElementById('reviewForm').reset();
      document.getElementById('photoPreviewContainer').style.display = 'none';
      currentPhotoDataUrl = null;

      // Reset Stars
      const stars = document.querySelectorAll('#starPicker .star');
      stars.forEach(s => s.classList.add('active'));
      document.getElementById('revRating').value = 5;

      // Smooth scroll to new review
      reviewCard.scrollIntoView({ behavior: 'smooth', block: 'center' });

      // Announce success to assistive tech
      const successRegion = document.getElementById('reviewSuccess');
      if (successRegion) successRegion.textContent = 'Thank you! Your review has been posted.';
    }
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

  </body>
</html>
