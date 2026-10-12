<?php
require 'backend/config.php';
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us — Deepkala Hair Salon, Ghodasar, Ahmedabad</title>

  <meta name="description" content="Deepkala Hair Salon in Ghodasar, Ahmedabad: 12+ years of haircuts, beard grooming, hair spa and bridal makeup. Open daily 9 AM–9 PM. Cuts from ₹100.">
  <meta property="og:title" content="About Deepkala Hair Salon — Ghodasar, Ahmedabad">
  <meta property="og:description" content="12+ years of craft: haircuts, beard grooming, hair spa and bridal makeup in Ghodasar.">
  <meta property="og:type" content="website">
  <script type="application/ld+json">
  {"@context":"https://schema.org","@type":"HairSalon","name":"Deepkala Hair Salon","telephone":"+919924620958","priceRange":"₹","address":{"@type":"PostalAddress","streetAddress":"2 Jayvimal Society, Cadila Road, Ghodasar","addressLocality":"Ahmedabad","postalCode":"380050","addressCountry":"IN"},"openingHours":"Mo-Su 09:00-21:00"}
  </script>

  <!-- Favicon -->
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
      overflow-x: clip; /* clip (not hidden) keeps the sticky header working */
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
    h1.font-serif { font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; /* ← FONT-FAMILY UPDATED */ }
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

    /* ============================================================
       ABOUT PAGE — NEW ARCHITECTURE
       Cinematic hero → stat strip → vertical story timeline →
       numbered values list → team filmstrip → split visit band
       ============================================================ */

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
      margin-top: 32px;
      margin-bottom: 16px;
    }

    .category-header .hairline {
      flex: 1; height: 1px;
      background-color: var(--border-hairline);
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
      margin-top: 20px;
    }

    .btn-primary:hover {
      background-color: var(--rose-accent-hover);
      border-color: var(--rose-accent-hover);
      transform: translateY(-1px);
      box-shadow: 0 6px 18px rgba(10,8,6,0.35);
    }

    .btn-ghost {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-size: 0.78rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.1em;
      color: var(--text-ink);
      border-bottom: 1px solid var(--border-hairline);
      padding-bottom: 6px;
      transition: color 0.2s ease, border-color 0.2s ease;
    }
    .btn-ghost:hover { color: var(--rose-accent); border-color: var(--rose-accent); }

    /* ---------- 1. Cinematic full-bleed hero ---------- */
    .ab-hero {
      position: relative;
      min-height: 78vh;
      display: flex;
      align-items: flex-end;
      overflow: hidden;
      border-bottom: 1px solid var(--border-hairline);
    }

    .ab-hero-bg {
      position: absolute; inset: 0;
      background: url('https://images.unsplash.com/photo-1585747860715-2ba37e788b70?w=1600&q=70&auto=format&fit=crop') center 30% / cover no-repeat;
      filter: grayscale(20%) contrast(1.05);
      transform: scale(1.02);
    }

    .ab-hero-scrim {
      position: absolute; inset: 0;
      background:
        linear-gradient(180deg, rgba(10,8,6,0.35) 0%, rgba(10,8,6,0.55) 55%, var(--bg-cream) 96%),
        linear-gradient(90deg, rgba(10,8,6,0.55) 0%, rgba(10,8,6,0.05) 55%);
    }

    .ab-hero-inner {
      position: relative;
      z-index: 1;
      padding: 180px 0 96px 0;
      max-width: 760px;
    }

    .ab-hero-inner .eyebrow-tag {
      background-color: var(--rose-accent);
      color: #15130E;
      margin-bottom: 20px;
    }

    .ab-hero h1 {
      font-size: clamp(2.6rem, 6vw, 4.4rem);
      font-weight: 700;
      line-height: 1.05;
      color: var(--text-ink);
      margin-bottom: 44px;
      text-shadow: 0 2px 24px rgba(0,0,0,0.4);
    }

    .ab-hero p {
      font-size: 1.08rem;
      color: #E4DCC9;
      max-width: 540px;
      margin-bottom: 28px;
    }

    .ab-hero-actions {
      display: flex;
      align-items: center;
      gap: 28px;
      flex-wrap: wrap;
    }

    .ab-hero-actions .btn-primary { margin-top: 0; }

    .ab-scroll-cue {
      font-size: 0.8rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.1em;
      color: var(--text-ink);
      opacity: 0.85;
      transition: opacity 0.2s ease;
    }
    .ab-scroll-cue:hover { opacity: 1; color: var(--rose-accent); }

    @media (max-width: 640px) {
      .ab-hero { min-height: 64vh; }
      .ab-hero-inner { padding: 120px 0 64px 0; }
    }

    /* ---------- 2. Stat strip ---------- */
    .ab-stat-strip {
      background-color: var(--card-white);
      border-bottom: 1px solid var(--border-hairline);
    }

    .ab-stat-strip-inner {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
    }

    .ab-stat {
      padding: 28px 20px;
      text-align: center;
      border-right: 1px solid var(--border-hairline);
    }
    .ab-stat:last-child { border-right: none; }

    .ab-stat span {
      display: block;
      font-family: 'Playfair Display', Georgia, 'Times New Roman', serif;
      font-size: clamp(1.5rem, 3vw, 2.1rem);
      font-weight: 700;
      color: var(--rose-accent);
      line-height: 1;
      margin-bottom: 6px;
    }

    .ab-stat .ab-stat-label {
      font-size: 0.68rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      color: #9C9284;
    }

    @media (max-width: 640px) {
      .ab-stat-strip-inner { grid-template-columns: repeat(2, 1fr); }
      .ab-stat:nth-child(2) { border-right: none; }
      .ab-stat:nth-child(1), .ab-stat:nth-child(2) { border-bottom: 1px solid var(--border-hairline); }
    }

    .ab-section { padding: 2px 0; }
    /* .ab-section + .ab-section { border-top: 1px solid var(--border-hairline); } */

    .ab-section-title {
      font-size: clamp(1.6rem, 3vw, 2.2rem);
      font-weight: 700;
      color: var(--text-ink);
      max-width: 640px;
      margin-bottom: 36px;
    }

    /* ---------- 3. Story timeline ---------- */
    .ab-timeline {
      position: relative;
      max-width: 780px;
    }

    .ab-timeline::before {
      content: '';
      position: absolute;
      top: 6px; bottom: 6px; left: 27px;
      width: 1px;
      background: linear-gradient(180deg, var(--rose-accent) 0%, var(--border-hairline) 100%);
      opacity: 0.5;
    }

    .ab-timeline-item {
      position: relative;
      display: flex;
      gap: 28px;
      padding-bottom: 44px;
    }
    .ab-timeline-item:last-child { padding-bottom: 0; }

    .ab-timeline-marker {
      flex-shrink: 0;
      width: 56px; height: 56px;
      border-radius: 50%;
      background-color: var(--bg-cream);
      border: 1px solid var(--rose-accent);
      display: flex; align-items: center; justify-content: center;
      font-family: 'Playfair Display', Georgia, 'Times New Roman', serif;
      font-weight: 700;
      font-size: 1.05rem;
      color: var(--rose-accent);
      position: relative;
      z-index: 1;
    }

    .ab-timeline-content { padding-top: 8px; }

    .ab-timeline-content h3 {
      font-size: 1.3rem;
      font-weight: 700;
      color: var(--text-ink);
      margin-bottom: 10px;
    }

    .ab-timeline-content p {
      font-size: 0.95rem;
      line-height: 1.8;
      color: #B3AA98;
      max-width: 620px;
    }

    @media (max-width: 560px) {
      .ab-timeline::before { left: 21px; }
      .ab-timeline-marker { width: 44px; height: 44px; font-size: 0.9rem; }
      .ab-timeline-item { gap: 18px; }
    }

    /* ---------- 4. Values — numbered list ---------- */
    .ab-values-list {
      display: flex;
      flex-direction: column;
    }

    .ab-value-row {
      display: grid;
      grid-template-columns: 88px 1fr;
      gap: 24px;
      padding: 28px 0;
      border-top: 1px solid var(--border-hairline);
      align-items: start;
      transition: padding-left 0.2s ease;
    }
    /* .ab-value-row:last-child { border-bottom: 1px solid var(--border-hairline); } */

    .ab-value-row:hover { padding-left: 8px; }

    .ab-value-num {
      font-family: 'Playfair Display', Georgia, 'Times New Roman', serif;
      font-size: 2rem;
      font-weight: 700;
      color: var(--bronze-taupe);
      line-height: 1;
      transition: color 0.2s ease;
    }
    .ab-value-row:hover .ab-value-num { color: var(--rose-accent); }

    .ab-value-body { display: grid; grid-template-columns: 1fr 1.4fr; gap: 20px; align-items: baseline; }

    .ab-value-body h3 {
      font-family: 'Playfair Display', Georgia, 'Times New Roman', serif;
      font-size: 1.2rem;
      font-weight: 700;
      color: var(--text-ink);
    }

    .ab-value-body p {
      font-size: 0.9rem;
      color: #9C9284;
      line-height: 1.65;
    }

    @media (max-width: 720px) {
      .ab-value-body { grid-template-columns: 1fr; gap: 8px; }
      .ab-value-row { grid-template-columns: 56px 1fr; }
      .ab-value-num { font-size: 1.5rem; }
    }

    /* ---------- 5. Team filmstrip (flip cards) ---------- */
    .ab-team-scroll {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 20px;
      padding-bottom: 12px;
    }
    .ab-team-scroll::-webkit-scrollbar { height: 6px; }
    .ab-team-scroll::-webkit-scrollbar-thumb { background: var(--border-hairline); border-radius: 6px; }

    /* Flip card shell: gives the 3D stage its depth and size */
    .ab-team-card {
      scroll-snap-align: start;
      width: 100%;
      height: 360px;
      background: transparent;
      border: none;
      padding: 0;
      perspective: 1400px;
    }

    .ab-team-card-inner {
      position: relative;
      width: 100%;
      height: 100%;
      cursor: pointer;
      transform-style: preserve-3d;
      transition: transform 0.7s cubic-bezier(0.45, 0.05, 0.2, 1);
      outline: none;
    }

    /* Desktop / mouse: flip on hover */
    @media (hover: hover) {
      .ab-team-card:hover .ab-team-card-inner { transform: rotateY(180deg); }
    }
    /* Touch / keyboard: flip on tap or focus */
    .ab-team-card-inner.is-flipped,
    .ab-team-card-inner:focus-visible { transform: rotateY(180deg); }

    .ab-team-card-face {
      position: absolute;
      inset: 0;
      overflow: hidden;
      backface-visibility: hidden;
      -webkit-backface-visibility: hidden;
      background-color: var(--card-white);
      border: 1px solid var(--border-hairline);
      display: flex;
      flex-direction: column;
      transition: border-color 0.25s ease;
    }
    .ab-team-card-inner:hover .ab-team-card-face { border-color: var(--rose-accent); }

    .ab-team-card-back {
      transform: rotateY(180deg);
      background-color: var(--card-cream);
      justify-content: space-between;
    }

    .ab-team-photo {
      height: 190px;
      flex-shrink: 0;
      background-color: var(--card-cream);
      display: flex; align-items: center; justify-content: center;
      font-family: 'Playfair Display', Georgia, 'Times New Roman', serif;
      font-size: 2.4rem;
      font-weight: 700;
      color: var(--rose-accent);
      border-bottom: 1px solid var(--border-hairline);
    }

    .ab-team-body { padding: 22px 24px 26px; }

    .ab-team-name {
      font-family: 'Playfair Display', Georgia, 'Times New Roman', serif;
      font-size: 1.15rem;
      font-weight: 700;
      color: var(--text-ink);
      margin-bottom: 4px;
    }

    .ab-team-role {
      font-size: 0.68rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: var(--rose-accent);
      margin-bottom: 12px;
    }

    .ab-team-bio {
      font-size: 0.85rem;
      color: #9C9284;
      line-height: 1.55;
    }

    /* Back face content */
    .ab-team-back-body {
      padding: 26px 24px;
      display: flex;
      flex-direction: column;
      height: 100%;
    }
    .ab-team-back-icon {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background-color: var(--card-white);
      border: 1px solid var(--border-hairline);
      display: flex; align-items: center; justify-content: center;
      font-family: 'Playfair Display', Georgia, 'Times New Roman', serif;
      font-weight: 700;
      font-size: 0.95rem;
      color: var(--rose-accent);
      margin-bottom: 14px;
      flex-shrink: 0;
    }
    .ab-team-back-body .ab-team-name { font-size: 1.05rem; }
    .ab-team-back-bio {
      font-size: 0.85rem;
      color: #9C9284;
      line-height: 1.6;
      flex-grow: 1;
    }
    .ab-team-back-cta {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      margin-top: 16px;
      padding: 10px 18px;
      font-size: 0.72rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      background-color: var(--rose-accent);
      color: #15130E;
      border-radius: 4px;
      transition: background-color 0.2s ease;
      flex-shrink: 0;
    }
    .ab-team-back-cta:hover { background-color: var(--rose-accent-hover); }
    .ab-team-flip-hint {
      font-size: 0.65rem;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: var(--bronze-taupe);
      padding: 0 24px 16px;
      flex-shrink: 0;
    }

    /* ---------- 6. Visit — split band ---------- */
    .ab-visit {
      display: grid;
      grid-template-columns: 1fr;
      border-top: 1px solid var(--border-hairline);
    }

    @media (min-width: 860px) {
      .ab-visit { grid-template-columns: 1fr 1fr; min-height: 380px; }
    }

    .ab-visit-info {
      background-color: var(--card-cream);
      padding: 64px clamp(24px, 6vw, 72px);
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .ab-visit-info .hours-pin {
      width: 46px; height: 46px;
      border-radius: 50%;
      border: 1px solid var(--rose-accent);
      display: flex; align-items: center; justify-content: center;
      color: var(--rose-accent);
    }

    .ab-visit-info h3 {
      font-size: clamp(1.6rem, 3vw, 2.1rem);
      font-weight: 700;
      color: var(--text-ink);
      margin: 16px 0 12px;
    }

    .ab-visit-info p { font-size: 0.95rem; color: #B3AA98; }

    .ab-visit-image {
      min-height: 280px;
      background: url('https://images.unsplash.com/photo-1521590832167-7bcbfaa6381f?w=1200&q=70&auto=format&fit=crop') center / cover no-repeat;
      filter: grayscale(10%) contrast(1.05);
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
          <li><a href="reviews.php" class="nav-link">Reviews</a></li>
          <li><a href="about.php" class="nav-link active" aria-current="page">About</a></li>
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

    <!-- 1. Cinematic Hero -->
    <section class="ab-hero">
      <div class="ab-hero-bg" aria-hidden="true"></div>
      <div class="ab-hero-scrim" aria-hidden="true"></div>
      <div class="container">
        <div class="ab-hero-inner reveal">
          <h1 class="font-serif">Twelve Years of<br>Professional Grooming</h1>
          <p>A trusted salon offering expert haircuts, grooming and bridal makeup, delivered with skilled craftsmanship, strict hygiene and transparent pricing.</p>
          <div class="ab-hero-actions">
            <a href="#ab-story" class="ab-scroll-cue">Our Story &darr;</a>
          </div>
        </div>
      </div>
    </section>

    <!-- 2. Stat Strip -->
    <section class="ab-stat-strip reveal">
      <div class="container ab-stat-strip-inner">
        <div class="ab-stat">
          <span>12+</span>
          <span class="ab-stat-label">Years Serving Ghodasar</span>
        </div>
        <div class="ab-stat">
          <span>4</span>
          <span class="ab-stat-label">Expert Stylists &amp; Artists</span>
        </div>
        <div class="ab-stat">
          <span>&#8377;100</span>
          <span class="ab-stat-label">Haircuts Starting At</span>
        </div>
        <div class="ab-stat">
          <span>7/7</span>
          <span class="ab-stat-label">Days a Week, 9 AM &ndash; 9 PM</span>
        </div>
      </div>
    </section>

    <div class="container">

      <!-- 3. Story Timeline -->
      <section class="ab-section reveal" id="ab-story">
        <div class="category-header">
          <span class="eyebrow-tag">OUR JOURNEY</span>
          <div class="hairline"></div>
        </div>
        <h2 class="font-serif ab-section-title">How Deepkala Came to Be</h2>

        <div class="ab-timeline">
          <div class="ab-timeline-item">
            <div class="ab-timeline-marker">01</div>
            <div class="ab-timeline-content">
              <h3 class="font-serif">Our Mission</h3>
              <p>Deepkala Hair Salon was established with a clear purpose: to provide every client with an expert haircut, precise beard grooming and restorative hair therapy at transparent, fair prices. We believe looking well-groomed and feeling confident should be part of everyday life, not reserved for special occasions.</p>
            </div>
          </div>
          <div class="ab-timeline-item">
            <div class="ab-timeline-marker">02</div>
            <div class="ab-timeline-content">
              <h3 class="font-serif">Our Craft</h3>
              <p>Our team combines traditional barbering with contemporary styling techniques. Whether it is a skin fade, a classic scissor cut, a relaxing hair spa or a hot towel beard shave, every service receives the same careful attention to detail.</p>
            </div>
          </div>
          <div class="ab-timeline-item">
            <div class="ab-timeline-marker">03</div>
            <div class="ab-timeline-content">
              <h3 class="font-serif">Our Clients</h3>
              <p>We have built lasting relationships with families and working professionals over the years. Every appointment is designed to be clean, comfortable and refreshing, and that is the standard we uphold every day.</p>
            </div>
          </div>
        </div>
      </section>

      <!-- 4. Values — numbered list -->
      <section class="ab-section reveal">
        <div class="category-header">
          <span class="eyebrow-tag">OUR VALUES</span>
          <div class="hairline"></div>
        </div>
        <h2 class="font-serif ab-section-title">What We Stand For</h2>

        <div class="ab-values-list">
          <div class="ab-value-row">
            <div class="ab-value-num">01</div>
            <div class="ab-value-body">
              <h3 class="font-serif">Master Craftsmanship</h3>
              <p>Every haircut is planned around the client's head shape, hair growth pattern and personal style before the first cut.</p>
            </div>
          </div>
          <div class="ab-value-row">
            <div class="ab-value-num">02</div>
            <div class="ab-value-body">
              <h3 class="font-serif">Hygiene First</h3>
              <p>All scissors, razors, clippers and capes are sanitised before every client, without exception.</p>
            </div>
          </div>
          <div class="ab-value-row">
            <div class="ab-value-num">03</div>
            <div class="ab-value-body">
              <h3 class="font-serif">Transparent Pricing</h3>
              <p>Haircuts start at &#8377;100, with clear pricing on every service and no hidden charges.</p>
            </div>
          </div>
          <div class="ab-value-row">
            <div class="ab-value-num">04</div>
            <div class="ab-value-body">
              <h3 class="font-serif">Comfort &amp; Service</h3>
              <p>Open seven days a week, 9:00 AM to 9:00 PM, with comfortable seating, punctual service and courteous staff.</p>
            </div>
          </div>
        </div>
      </section>

      <!-- 5. Team filmstrip -->
      <section class="ab-section">
        <div class="category-header">
          <span class="eyebrow-tag">OUR TEAM</span>
          <div class="hairline"></div>
        </div>
        <h2 class="font-serif ab-section-title">Meet Our Experts</h2>

        <div class="ab-team-scroll">

          <div class="ab-team-card">
            <div class="ab-team-card-inner" tabindex="0" role="button" aria-label="Flip to read more about Deepesh Parmar" onclick="this.classList.toggle('is-flipped')" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.classList.toggle('is-flipped');}">
              <div class="ab-team-card-face ab-team-card-front">
                <div class="ab-team-photo">DP</div>
                <div class="ab-team-body">
                  <div class="ab-team-name">Deepesh Parmar</div>
                  <div class="ab-team-role">Founder &amp; Master Stylist</div>
                </div>
                <div class="ab-team-flip-hint">Tap / hover to read more →</div>
              </div>
              <div class="ab-team-card-face ab-team-card-back">
                <div class="ab-team-back-body">
                  <div class="ab-team-back-icon">DP</div>
                  <div class="ab-team-name">Deepesh Parmar</div>
                  <div class="ab-team-role">Founder &amp; Master Stylist</div>
                  <p class="ab-team-back-bio">Over 12 years of experience in classic barbering, advanced scissor techniques and client consultation.</p>
                  <a href="booking.php" class="ab-team-back-cta" onclick="event.stopPropagation()">Book Now</a>
                </div>
              </div>
            </div>
          </div>

          <div class="ab-team-card">
            <div class="ab-team-card-inner" tabindex="0" role="button" aria-label="Flip to read more about Kalpesh Patel" onclick="this.classList.toggle('is-flipped')" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.classList.toggle('is-flipped');}">
              <div class="ab-team-card-face ab-team-card-front">
                <div class="ab-team-photo">KP</div>
                <div class="ab-team-body">
                  <div class="ab-team-name">Kalpesh Patel</div>
                  <div class="ab-team-role">Senior Fade &amp; Colour Specialist</div>
                </div>
                <div class="ab-team-flip-hint">Tap / hover to read more →</div>
              </div>
              <div class="ab-team-card-face ab-team-card-back">
                <div class="ab-team-back-body">
                  <div class="ab-team-back-icon">KP</div>
                  <div class="ab-team-name">Kalpesh Patel</div>
                  <div class="ab-team-role">Senior Fade &amp; Colour Specialist</div>
                  <p class="ab-team-back-bio">Specialises in skin fades, undercuts, textured crop cuts and custom hair colouring.</p>
                  <a href="booking.php" class="ab-team-back-cta" onclick="event.stopPropagation()">Book Now</a>
                </div>
              </div>
            </div>
          </div>

          <div class="ab-team-card">
            <div class="ab-team-card-inner" tabindex="0" role="button" aria-label="Flip to read more about Sanjay Kumar" onclick="this.classList.toggle('is-flipped')" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.classList.toggle('is-flipped');}">
              <div class="ab-team-card-face ab-team-card-front">
                <div class="ab-team-photo">SK</div>
                <div class="ab-team-body">
                  <div class="ab-team-name">Sanjay Kumar</div>
                  <div class="ab-team-role">Beard Specialist &amp; Spa Therapist</div>
                </div>
                <div class="ab-team-flip-hint">Tap / hover to read more →</div>
              </div>
              <div class="ab-team-card-face ab-team-card-back">
                <div class="ab-team-back-body">
                  <div class="ab-team-back-icon">SK</div>
                  <div class="ab-team-name">Sanjay Kumar</div>
                  <div class="ab-team-role">Beard Specialist &amp; Spa Therapist</div>
                  <p class="ab-team-back-bio">Experienced in razor lining, hot towel shaves, scalp massage and nourishing hair spa treatments.</p>
                  <a href="booking.php" class="ab-team-back-cta" onclick="event.stopPropagation()">Book Now</a>
                </div>
              </div>
            </div>
          </div>

          <div class="ab-team-card">
            <div class="ab-team-card-inner" tabindex="0" role="button" aria-label="Flip to read more about Nisha Solanki" onclick="this.classList.toggle('is-flipped')" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.classList.toggle('is-flipped');}">
              <!-- TODO: placeholder name — replace with the artist's real name, photo and experience -->
              <div class="ab-team-card-face ab-team-card-front">
                <div class="ab-team-photo">NS</div>
                <div class="ab-team-body">
                  <div class="ab-team-name">Nisha Solanki</div>
                  <div class="ab-team-role">Bridal &amp; Wedding Makeup Artist</div>
                </div>
                <div class="ab-team-flip-hint">Tap / hover to read more →</div>
              </div>
              <div class="ab-team-card-face ab-team-card-back">
                <div class="ab-team-back-body">
                  <div class="ab-team-back-icon">NS</div>
                  <div class="ab-team-name">Nisha Solanki</div>
                  <div class="ab-team-role">Bridal &amp; Wedding Makeup Artist</div>
                  <p class="ab-team-back-bio">Specialises in bridal, engagement and reception makeup, tailored to the occasion, outfit and skin type.</p>
                  <a href="booking.php" class="ab-team-back-cta" onclick="event.stopPropagation()">Book Now</a>
                </div>
              </div>
            </div>
          </div>


          <div class="ab-team-card">
            <div class="ab-team-card-inner" tabindex="0" role="button" aria-label="Flip to read more about Rahul Desai" onclick="this.classList.toggle('is-flipped')" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.classList.toggle('is-flipped');}">
              <div class="ab-team-card-face ab-team-card-front">
                <div class="ab-team-photo">RD</div>
                <div class="ab-team-body">
                  <div class="ab-team-name">Rahul Desai</div>
                  <div class="ab-team-role">Senior Barber</div>
                </div>
                <div class="ab-team-flip-hint">Tap / hover to read more →</div>
              </div>
              <div class="ab-team-card-face ab-team-card-back">
                <div class="ab-team-back-body">
                  <div class="ab-team-back-icon">RD</div>
                  <div class="ab-team-name">Rahul Desai</div>
                  <div class="ab-team-role">Senior Barber</div>
                  <p class="ab-team-back-bio">Skin fades, classic tapers and sharp hairlines, built on years behind the chair.</p>
                  <a href="booking.php" class="ab-team-back-cta" onclick="event.stopPropagation()">Book Now</a>
                </div>
              </div>
            </div>
          </div>

          <div class="ab-team-card">
            <div class="ab-team-card-inner" tabindex="0" role="button" aria-label="Flip to read more about Amit Joshi" onclick="this.classList.toggle('is-flipped')" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.classList.toggle('is-flipped');}">
              <div class="ab-team-card-face ab-team-card-front">
                <div class="ab-team-photo">AJ</div>
                <div class="ab-team-body">
                  <div class="ab-team-name">Amit Joshi</div>
                  <div class="ab-team-role">Beard &amp; Grooming Specialist</div>
                </div>
                <div class="ab-team-flip-hint">Tap / hover to read more →</div>
              </div>
              <div class="ab-team-card-face ab-team-card-back">
                <div class="ab-team-back-body">
                  <div class="ab-team-back-icon">AJ</div>
                  <div class="ab-team-name">Amit Joshi</div>
                  <div class="ab-team-role">Beard &amp; Grooming Specialist</div>
                  <p class="ab-team-back-bio">Beard shaping, hot-towel shaves and detailed grooming for a clean, polished finish.</p>
                  <a href="booking.php" class="ab-team-back-cta" onclick="event.stopPropagation()">Book Now</a>
                </div>
              </div>
            </div>
          </div>

          <div class="ab-team-card">
            <div class="ab-team-card-inner" tabindex="0" role="button" aria-label="Flip to read more about Priya Shah" onclick="this.classList.toggle('is-flipped')" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.classList.toggle('is-flipped');}">
              <div class="ab-team-card-face ab-team-card-front">
                <div class="ab-team-photo">PS</div>
                <div class="ab-team-body">
                  <div class="ab-team-name">Priya Shah</div>
                  <div class="ab-team-role">Hair Colour Specialist</div>
                </div>
                <div class="ab-team-flip-hint">Tap / hover to read more →</div>
              </div>
              <div class="ab-team-card-face ab-team-card-back">
                <div class="ab-team-back-body">
                  <div class="ab-team-back-icon">PS</div>
                  <div class="ab-team-name">Priya Shah</div>
                  <div class="ab-team-role">Hair Colour Specialist</div>
                  <p class="ab-team-back-bio">Global colour, highlights and toning, planned around your hair type and skin tone.</p>
                  <a href="booking.php" class="ab-team-back-cta" onclick="event.stopPropagation()">Book Now</a>
                </div>
              </div>
            </div>
          </div>

          <div class="ab-team-card">
            <div class="ab-team-card-inner" tabindex="0" role="button" aria-label="Flip to read more about Vikram Rathod" onclick="this.classList.toggle('is-flipped')" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.classList.toggle('is-flipped');}">
              <div class="ab-team-card-face ab-team-card-front">
                <div class="ab-team-photo">VR</div>
                <div class="ab-team-body">
                  <div class="ab-team-name">Vikram Rathod</div>
                  <div class="ab-team-role">Stylist</div>
                </div>
                <div class="ab-team-flip-hint">Tap / hover to read more →</div>
              </div>
              <div class="ab-team-card-face ab-team-card-back">
                <div class="ab-team-back-body">
                  <div class="ab-team-back-icon">VR</div>
                  <div class="ab-team-name">Vikram Rathod</div>
                  <div class="ab-team-role">Stylist</div>
                  <p class="ab-team-back-bio">Modern cuts and textured styling, with a calm and precise approach to every client.</p>
                  <a href="booking.php" class="ab-team-back-cta" onclick="event.stopPropagation()">Book Now</a>
                </div>
              </div>
            </div>
          </div>

        </div>
      </section>

    </div>

    <!-- 6. Visit — split band -->
    <section class="ab-visit reveal">
      <div class="ab-visit-info">
        <div class="hours-pin"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg></div>
        <h3 class="font-serif">Visit Our Salon</h3>
        <p>2 Jayvimal Society, Cadila Road, Ghodasar, Ahmedabad &ndash; 380050</p>
        <p style="font-weight: 600; color: var(--rose-accent); margin-top: 6px;">Open Daily, 9:00 AM &ndash; 9:00 PM</p>
        <div>
          <a href="booking.php" class="btn-primary">Book an Appointment</a>
        </div>
      </div>
      <div class="ab-visit-image" aria-hidden="true"></div>
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
    document.addEventListener('DOMContentLoaded', () => {
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
