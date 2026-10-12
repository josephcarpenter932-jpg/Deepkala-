<?php
require 'backend/config.php';
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Deepkala Hair Salon — Precision Haircuts & Grooming in Ghodasar, Ahmedabad</title>

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

    a {
      color: inherit;
      text-decoration: none;
    }

    /* Container Utility */
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

    /* Typography */
    .font-serif { font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; /* ← FONT-FAMILY UPDATED */ }
    h1.font-serif { font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; /* ← FONT-FAMILY UPDATED */ font-variation-settings: "opsz" 144, "SOFT" 0, "WONK" 0; }
    .font-sans { font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif; /* ← FONT-FAMILY UPDATED */ }

    /* Eyebrow Tag */
    .eyebrow-tag {
      display: inline-block;
      background-color: var(--text-ink);
      color: var(--bg-cream);
      padding: 4px 12px;
      font-size: 0.72rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.1em;
      font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif; /* ← FONT-FAMILY UPDATED */
    }

    .category-header {
      display: flex;
      align-items: center;
      gap: 16px;
      margin-bottom: 16px;
    }

    .category-header .hairline {
      flex: 1;
      height: 1px;
      background-color: var(--border-hairline);
    }

    /* Navbar */
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

    .brand-logo {
      display: flex;
      align-items: center;
      gap: 12px;
    }

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

    .nav-links {
      display: flex;
      align-items: center;
      gap: 28px;
      list-style: none;
    }

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

    .nav-link:hover, .nav-link.active {
      color: var(--rose-accent);
    }

    .nav-link.active::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 2px;
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

    /* Buttons */
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
    }

    .btn-primary:hover {
      background-color: var(--rose-accent-hover);
      border-color: var(--rose-accent-hover);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(10, 8, 6, 0.35);
    }

    .btn-secondary {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      background-color: transparent;
      color: var(--text-ink);
      padding: 14px 28px;
      font-size: 0.825rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      border: 1px solid var(--text-ink);
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .btn-secondary:hover {
      background-color: var(--text-ink);
      color: var(--bg-cream);
      transform: translateY(-2px);
    }

    /* Seal Motif */
    .seal-motif {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      position: relative;
      width: 52px;
      height: 52px;
      margin-bottom: 16px;
    }

    .seal-motif .ring-outer {
      position: absolute;
      inset: 0;
      border: 1px solid var(--bronze-taupe);
      border-radius: 50%;
      opacity: 0.7;
    }

    .seal-motif .ring-inner {
      position: absolute;
      inset: 5px;
      border: 1px dashed var(--rose-deep);
      border-radius: 50%;
      opacity: 0.8;
    }

    .seal-motif .seal-text {
      font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; /* ← FONT-FAMILY UPDATED */
      font-weight: 700;
      font-size: 1.2rem;
      color: var(--text-ink);
    }

    /* Hero Section */
    .hero-section {
      height: calc(100vh - 77px);
      min-height: 560px;
      max-height: calc(100vh - 77px);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 32px 0;
      text-align: center;
      position: relative;
      border-bottom: 1px solid var(--border-hairline);
      background-color: var(--bg-cream);
      overflow: hidden;
      isolation: isolate;
    }

    .hero-section > .container {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      max-height: 100%;
    }

    @media (max-width: 640px) {
      .hero-section {
        height: calc(100svh - 77px);
        max-height: calc(100svh - 77px);
        min-height: 480px;
      }
    }

    .hero-section::before {
      content: "";
      position: absolute;
      inset: 0;
      background-image: url('data:image/jpeg;base64,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');
      background-size: cover;
      background-position: center;
      z-index: -2;
    }

    .hero-section::after {
      content: "";
      position: absolute;
      inset: 0;
      background: linear-gradient(160deg, rgba(10,8,6,0.78) 0%, rgba(201,166,107,0.42) 55%, rgba(10,8,6,0.85) 100%);
      z-index: -1;
    }

    .hero-section > .container {
      position: relative;
      z-index: 1;
    }

    .hero-eyebrow {
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.15em;
      color: #E8C784;
      margin-bottom: 12px;
    }

    .hero-title {
      font-size: clamp(2rem, 4.2vw, 3.75rem);
      font-weight: 700;
      line-height: 1.1;
      color: #FFFFFF;
      margin-bottom: 16px;
      letter-spacing: -0.02em;
    }

    .hero-tagline {
      font-size: clamp(0.95rem, 1.4vw, 1.125rem);
      color: #E4DCC8;
      max-width: 640px;
      margin: 0 auto 24px auto;
      font-weight: 400;
    }

    .hero-section .btn-secondary {
      color: #FFFFFF;
      border-color: #FFFFFF;
    }

    .hero-section .btn-secondary:hover {
      background-color: #1E1A14;
      color: var(--text-ink);
    }

    .hero-section .seal-motif .ring-outer {
      border-color: #E8C784;
      opacity: 0.85;
    }

    .hero-section .seal-motif .ring-inner {
      border-color: #FFFFFF;
      opacity: 0.85;
    }

    .hero-section .seal-motif .seal-text {
      color: #FFFFFF;
    }

    .hero-price-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background-color: var(--card-cream);
      border: 1px solid var(--border-hairline);
      padding: 6px 16px;
      font-size: 0.8rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: var(--rose-accent);
      margin-bottom: 32px;
    }

    .hero-ctas {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 16px;
      flex-wrap: wrap;
    }

    /* Highlights Section */
    .highlights-section {
      padding: 60px 0;
      border-bottom: 1px solid var(--border-hairline);
      background-color: var(--card-white);
    }

    .highlights-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 24px;
    }

    .highlight-card {
      padding: 24px;
      border: 1px solid var(--border-hairline);
      background-color: var(--bg-cream);
      text-align: left;
    }

    .highlight-number {
      font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; /* ← FONT-FAMILY UPDATED */
      font-size: 1.75rem;
      font-weight: 700;
      color: var(--rose-accent);
      margin-bottom: 8px;
    }

    .highlight-title {
      font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; /* ← FONT-FAMILY UPDATED */
      font-size: 1.1rem;
      font-weight: 700;
      margin-bottom: 6px;
      color: var(--text-ink);
    }

    .highlight-desc {
      font-size: 0.85rem;
      color: #9C9284;
      line-height: 1.5;
    }

    /* Services Preview Section */
    .services-preview-section {
      padding: 80px 0;
      border-bottom: 1px solid var(--border-hairline);
    }

    .services-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 20px;
      margin-bottom: 36px;
    }

    @media (min-width: 768px) {
      .services-grid {
        grid-template-columns: 1fr 1fr;
      }
    }

    .service-card {
      background-color: var(--card-white);
      border: 1px solid var(--border-hairline);
      padding: 24px;
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 16px;
      transition: transform 0.2s ease, border-color 0.2s ease;
    }

    .service-card:hover {
      transform: translateY(-2px);
      border-color: var(--bronze-taupe);
    }

    .service-card {
      position: relative;
      overflow: hidden;
      isolation: isolate;
      border-color: rgba(232, 199, 132, 0.35) !important;
    }

    .service-card::before {
      content: "";
      position: absolute;
      inset: 0;
      background-image: url('data:image/jpeg;base64,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');
      background-size: cover;
      background-position: center;
      z-index: -2;
    }

    .service-card::after {
      content: "";
      position: absolute;
      inset: 0;
      background: linear-gradient(160deg, rgba(201,166,107,0.35) 0%, rgba(10,8,6,0.72) 100%);
      z-index: -1;
    }

    .service-card > * {
      position: relative;
      z-index: 1;
    }

    .service-card .service-title,
    .service-card .service-price {
      color: #FFFFFF !important;
    }

    .service-card .service-desc {
      color: #E4DCC8 !important;
    }

    .service-card .book-mini-btn {
      color: #E8C784 !important;
    }

    .service-title {
      font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; /* ← FONT-FAMILY UPDATED */
      font-size: 1.25rem;
      font-weight: 700;
      color: var(--text-ink);
      margin-bottom: 4px;
    }

    .service-desc {
      font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif; /* ← FONT-FAMILY UPDATED */
      font-size: 0.72rem;
      font-weight: 500;
      color: #9C9284;
      text-transform: uppercase;
      letter-spacing: 0.08em;
    }

    .service-price {
      font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; /* ← FONT-FAMILY UPDATED */
      font-size: 1.25rem;
      font-weight: 700;
      color: var(--text-ink);
      white-space: nowrap;
    }

    .text-center {
      text-align: center;
    }

    /* Testimonials Preview */
    .testimonials-section {
      padding: 80px 0;
      background-color: var(--card-cream);
      border-bottom: 1px solid var(--border-hairline);
    }

    .reviews-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 24px;
      margin-bottom: 36px;
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
    }

    .review-header {
      display: flex;
      align-items: center;
      gap: 14px;
      margin-bottom: 16px;
    }

    .avatar-circle {
      width: 44px;
      height: 44px;
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
    }

    .reviewer-name {
      font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; /* ← FONT-FAMILY UPDATED */
      font-size: 1.05rem;
      font-weight: 700;
      color: var(--text-ink);
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
      display: flex;
      gap: 4px;
    }

    .review-body {
      font-size: 0.925rem;
      color: #F0EBDF;
      line-height: 1.6;
    }

    /* CTA Banner */
    .cta-banner {
      padding: 70px 0;
      background-color: var(--rose-deep);
      color: var(--text-ink);
      text-align: center;
      position: relative;
      overflow: hidden;
      isolation: isolate;
    }

    .cta-banner::before {
      content: "";
      position: absolute;
      inset: 0;
      background-image: url('data:image/jpeg;base64,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');
      background-size: cover;
      background-position: center;
      mix-blend-mode: screen;
      opacity: 0.22;
      pointer-events: none;
      z-index: 0;
    }

    .cta-banner > * {
      position: relative;
      z-index: 1;
    }

    .cta-banner h2 {
      font-size: clamp(1.8rem, 3.5vw, 2.5rem);
      font-weight: 700;
      margin-bottom: 12px;
      color: #FFFFFF;
    }

    .cta-banner p {
      font-size: 1rem;
      color: #9C9284;
      max-width: 560px;
      margin: 0 auto 28px auto;
    }

    .btn-cta-light {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      background-color: var(--rose-accent);
      color: #15130E;
      padding: 14px 28px;
      font-size: 0.825rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      border: none;
      transition: background-color 0.2s ease;
    }

    .btn-cta-light:hover {
      background-color: var(--rose-accent-hover);
    }

    /* Footer */
    .site-footer {
      position: relative;
      margin: auto 12px 12px;
      color: var(--text-ink, #F3EEE2);
    }
    /* Softens the edge between the section above and the footer panel */
    .site-footer::before {
      content: "";
      position: absolute;
      left: -12px;
      right: -12px;
      top: -72px;
      height: 72px;
      z-index: 2;
      pointer-events: none;
      background: linear-gradient(to bottom,
        rgba(21,19,14,0) 0%,
        rgba(21,19,14,0.35) 40%,
        rgba(21,19,14,0.80) 75%,
        var(--bg-cream, #15130E) 100%);
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

    /* Scroll Reveal */
    .reveal {
      opacity: 0;
      transform: translateY(20px);
      transition: opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1), transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .reveal.active {
      opacity: 1;
      transform: translateY(0);
    }

    @media (prefers-reduced-motion: reduce) {
      .reveal {
        opacity: 1;
        transform: none;
        transition: none;
      }
    }

    /* Mobile Responsive Nav Drawer */
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


  <!-- Site Header / Navbar -->
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
          <li><a href="index.php" class="nav-link active" aria-current="page">Home</a></li>
          <li><a href="services.php" class="nav-link">Services</a></li>
          <li><a href="reviews.php" class="nav-link">Reviews</a></li>
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
  <!-- Main Content -->
  <main id="main-content" tabindex="-1">
    <!-- Hero Section -->
    <section class="hero-section">
      <div class="container reveal">
        <h1 class="hero-title font-serif">Master Craftsmanship in Hair Styling & Grooming</h1>
        <p class="hero-tagline">Precision cuts, modern skin fades, relaxing hair treatments, and sharp beard styling crafted for the modern individual.</p>
        
        <div class="hero-ctas">
          <a href="booking.php" class="btn-primary">Book Appointment</a>
          <a href="services.php" class="btn-secondary">View Services & Pricing</a>
        </div>
      </div>
    </section>

    <!-- Highlights Strip -->
    <section class="highlights-section">
      <div class="container">
        <div class="highlights-grid">
          <div class="highlight-card reveal">
            <div class="highlight-number">01</div>
            <h2 class="highlight-title">Precision Cuts</h2>
            <p class="highlight-desc">Custom tailored scissors and clipper work designed specifically for your scalp shape and style.</p>
          </div>
          <div class="highlight-card reveal">
            <div class="highlight-number">02</div>
            <h2 class="highlight-title">Master Hair Styling</h2>
            <p class="highlight-desc">Tailored grooming, sharp skin fades, and hair treatments crafted with passion and precision.</p>
          </div>
          <div class="highlight-card reveal">
            <div class="highlight-number">03</div>
            <h2 class="highlight-title">Clean & Hygienic</h2>
            <p class="highlight-desc">Sanitized blades, fresh towels, and a spotless setting for every single customer.</p>
          </div>
          <div class="highlight-card reveal">
            <div class="highlight-number">04</div>
            <h2 class="highlight-title">Daily 9 AM – 9 PM</h2>
            <p class="highlight-desc">Open 7 days a week at Ghodasar, Cadila Road for convenient walk-ins and bookings.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Services Preview Section -->
    <section class="services-preview-section">
      <div class="container">
        <div class="category-header reveal">
          <span class="eyebrow-tag">POPULAR SERVICES</span>
          <div class="hairline"></div>
        </div>

        <div class="services-grid">
          <div class="service-card reveal">
            <div>
              <h3 class="service-title">Classic Haircut</h3>
              <p class="service-desc">PRECISION SCISSOR & CLIPPER CUT</p>
            </div>
            <span class="service-price">₹150</span>
          </div>

          <div class="service-card reveal">
            <div>
              <h3 class="service-title">Skin Fade</h3>
              <p class="service-desc">CLEAN, BLENDED FADE WITH SHARP FINISH</p>
            </div>
            <span class="service-price">₹250</span>
          </div>

          <div class="service-card reveal">
            <div>
              <h3 class="service-title">Hair Spa</h3>
              <p class="service-desc">DEEP NOURISHING CRÈME MASSAGE & STEAM</p>
            </div>
            <span class="service-price">₹600</span>
          </div>

          <div class="service-card reveal">
            <div>
              <h3 class="service-title">Beard Shape-up</h3>
              <p class="service-desc">RAZOR LINING & CONTOUR DEFINITION</p>
            </div>
            <span class="service-price">₹150</span>
          </div>
        </div>

        <div class="text-center reveal">
          <a href="services.php" class="btn-secondary">Explore All Services & Pricing</a>
        </div>
      </div>
    </section>

    <!-- Testimonials Preview -->
    <section class="testimonials-section">
      <div class="container">
        <div class="category-header reveal">
          <span class="eyebrow-tag">CLIENT FEEDBACK</span>
          <div class="hairline"></div>
        </div>

        <div class="reviews-grid">
          <div class="review-card reveal">
            <div class="review-header">
              <div class="avatar-circle">RP</div>
              <div>
                <h3 class="reviewer-name">Rahul Patel</h3>
                <div class="reviewer-service">SKIN FADE & BEARD TRIM</div>
              </div>
            </div>
            <div class="star-rating">
              ★★★★★
            </div>
            <p class="review-body">"Best fade in Ghodasar! Deepesh bhai is extremely skilled and takes time to get the lines perfect. Clean salon and very comfortable atmosphere."</p>
          </div>

          <div class="review-card reveal">
            <div class="review-header">
              <div class="avatar-circle">PS</div>
              <div>
                <h3 class="reviewer-name">Parth Shah</h3>
                <div class="reviewer-service">HAIR SPA & STYLING</div>
              </div>
            </div>
            <div class="star-rating">
              ★★★★★
            </div>
            <p class="review-body">"Got a hair spa and classic haircut before an event. My hair felt so smooth and healthy. Outstanding value for money at ₹600."</p>
          </div>
        </div>

        <div class="text-center reveal">
          <a href="reviews.php" class="btn-secondary">Read More Client Reviews</a>
        </div>
      </div>
    </section>

    <!-- CTA Banner -->
    <section class="cta-banner">
      <div class="container reveal">
        <h2 class="font-serif">Ready for Your Next Fresh Look?</h2>
        <p>Walk-ins are always welcome, or send us a quick message on WhatsApp to reserve your slot directly.</p>
        <div style="display: flex; justify-content: center; gap: 16px; flex-wrap: wrap;">
          <a href="booking.php" class="btn-cta-light">Book on WhatsApp</a>
          <a href="tel:+919924620958" class="btn-secondary" style="border-color: #FFFFFF; color: #FFFFFF;">Call +91 99246 20958</a>
        </div>
      </div>
    </section>
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

  </body>
</html>
