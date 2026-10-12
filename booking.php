<?php
require 'backend/config.php';
$isLoggedIn = isset($_SESSION['user_id']);
$bookingSuccess = false; $bookingMessage = ''; $bookedBarberName = ''; $bookedAmount = 0; $bookedTxnId = ''; $bookedName = ''; $bookedService = ''; $bookedDate = ''; $bookedTime = ''; $bookedMethod = '';
$servicePrices = [
    'Buzz Cut — (₹200)' => 200,
    'Crew Cut — (₹250)' => 250,
    'Fade Cut — (₹350)' => 350,
    'Undercut — (₹400)' => 400,
    'Mid Fade — (₹500)' => 500,
    'Quiff — (₹450)' => 450,
    'Side Part — (₹300)' => 300,
    'French Crop — (₹350)' => 350,
    'Texture Crop — (₹450)' => 450,
    'Ceaser Cut — (₹300)' => 300,
    'Root Touch-Up — (₹3200)' => 3200,
    'Highlights — (₹1700)' => 1700,
    'Low Lights — (₹1500)' => 1500,
    'Gloss Treatment — (₹2000)' => 2000,
    'Color Correction — (₹1600)' => 1600,
    'Fasion Color — (₹1900)' => 1900,
    'Relaxation Spa — (₹800)' => 800,
    'Aroma Treatment — (₹1200)' => 1200,
    'Head Spa — (₹700)' => 700,
    'Deep Tissue Spa — (₹1400)' => 1400,
    'Scalp Treatment — (₹700)' => 700,
    'Hydrating Spa — (₹900)' => 900,
    'Goatee Beard — (₹500)' => 500,
    'French Beard — (₹400)' => 400,
    'Full Beard Style — (₹600)' => 600,
    'Italian Beard — (₹500)' => 500,
    'Stubble Beard — (₹250)' => 250,
    'Corporate Beard — (₹350)' => 350,
    'Anchor Beard — (₹500)' => 500,
    'Boxed Beard — (₹400)' => 400,
    'Special Bridal Package — (₹8000)' => 8000,
    'Bridal Package (₹5000)' => 5000,
    'Party Makeup + Hair (₹2500)' => 2500,
    'Groom Package (₹1500)' => 1500,
];
$allowedTimes = ['09:00 AM','10:00 AM','11:00 AM','12:00 PM','01:00 PM','02:00 PM','03:00 PM','04:00 PM','05:00 PM','06:00 PM','07:00 PM','08:00 PM'];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_booking'])) {
    require_csrf();
    if (!$isLoggedIn) { $bookingMessage = 'Please login first to book an appointment.'; }
    else {
        $name = trim($_POST['fullName'] ?? ''); $phone = trim($_POST['phone'] ?? ''); $service = $_POST['service'] ?? ''; $date = $_POST['appointmentDate'] ?? ''; $time = $_POST['appointmentTime'] ?? ''; $notes = trim($_POST['notes'] ?? ''); $method = trim($_POST['paymentMethod'] ?? '');
        $dateObj = DateTime::createFromFormat('Y-m-d', $date);
        $validDate = $dateObj && $dateObj->format('Y-m-d') === $date && $date >= date('Y-m-d');
        if (!preg_match('/^[A-Za-z ]{2,150}$/', $name)) $bookingMessage = 'Enter a valid name.';
        elseif (!preg_match('/^(\+91[\-\s]?)?[6-9]\d{9}$/', $phone)) $bookingMessage = 'Enter a valid Indian mobile number.';
        elseif (!isset($servicePrices[$service])) $bookingMessage = 'Please select a valid service.';
        elseif (!$validDate) $bookingMessage = 'Please choose today or a future date.';
        elseif (!in_array($time, $allowedTimes, true)) $bookingMessage = 'Please choose a valid time slot.';
        elseif (!preg_match('/^(Card •••• \d{4}|UPI \(.+\))$/u', $method)) $bookingMessage = 'Please choose a valid payment method.';
        else {
            $amount = $servicePrices[$service]; $membership = $_SESSION['membership'] ?? 'none';
            if ($membership === 'gold') $amount = (int) round($amount * 0.80); elseif ($membership === 'silver') $amount = (int) round($amount * 0.90);
            mysqli_begin_transaction($conn);
            try {
                $slot = mysqli_prepare($conn, 'SELECT id, name FROM barbers WHERE id NOT IN (SELECT barber_id FROM appointments WHERE appointment_date = ? AND appointment_time = ?) LIMIT 1 FOR UPDATE');
                mysqli_stmt_bind_param($slot, 'ss', $date, $time); mysqli_stmt_execute($slot); $barber = mysqli_fetch_assoc(mysqli_stmt_get_result($slot));
                if (!$barber) throw new Exception('FULL');
                $stmt = mysqli_prepare($conn, 'INSERT INTO appointments (user_id, barber_id, name, phone, service, appointment_date, appointment_time, notes, amount, payment_method) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
                $uid = (int) $_SESSION['user_id']; $barberId = (int) $barber['id'];
                mysqli_stmt_bind_param($stmt, 'iissssssis', $uid, $barberId, $name, $phone, $service, $date, $time, $notes, $amount, $method);
                if (!mysqli_stmt_execute($stmt)) throw new Exception('DB');
                mysqli_commit($conn); $bookingSuccess = true; $bookedBarberName = $barber['name']; $bookedAmount = $amount; $bookedTxnId = 'TXN' . random_int(10000000, 99999999); $bookedName = $name; $bookedService = explode(' — ', $service)[0]; $bookedDate = $date; $bookedTime = $time; $bookedMethod = $method;
            } catch (Throwable $e) { mysqli_rollback($conn); $bookingMessage = $e->getMessage() === 'FULL' ? 'Sorry, all our barbers are already booked for this time slot.' : 'Something went wrong while saving your booking.'; }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reserve Appointment — Deepkala Hair Salon, Ahmedabad</title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

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

    /* Eyebrow Header */
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

    /* Form Container */
    .booking-card {
      background-color: var(--card-white);
      border: 1px solid var(--border-hairline);
      padding: 40px;
      max-width: 800px;
      margin: 0 auto;
    }

    .booking-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 20px;
    }

    @media (min-width: 768px) {
      .booking-grid { grid-template-columns: 1fr 1fr; }
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
      background-color: var(--bg-cream);
      font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif; /* ← FONT-FAMILY UPDATED */
      font-size: 16px;
      color: var(--text-ink);
      width: 100%;
      min-height: 44px;
    }

    .form-input:focus, .form-select:focus, .form-textarea:focus {
      outline: 2px solid var(--rose-accent);
      border-color: var(--rose-accent);
      background-color: #1E1A14;
    }

    .btn-submit {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 12px;
      background-color: var(--rose-accent);
      color: #15130E;
      padding: 16px 32px;
      font-size: 0.875rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      border: 1px solid var(--rose-accent);
      cursor: pointer;
      width: 100%;
      transition: all 0.2s ease;
      min-height: 44px;
    }

    .btn-submit:hover {
      background-color: var(--rose-accent-hover);
      border-color: var(--rose-accent-hover);
    }

    .booking-notes-pill {
      background-color: var(--card-cream);
      border-left: 3px solid var(--rose-deep);
      padding: 12px 16px;
      font-size: 0.825rem;
      color: #B3AA98;
      margin-top: 16px;
    }

    /* Checkout Step Indicator */
    .checkout-steps {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      margin-bottom: 32px;
    }

    .checkout-step {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 0.75rem;
      font-weight: 600;
      letter-spacing: 0.06em;
      text-transform: uppercase;
      color: #9C9284;
      transition: color 0.2s ease;
    }

    .checkout-step-num {
      width: 26px;
      height: 26px;
      border-radius: 50%;
      border: 1px solid var(--border-hairline);
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; /* ← FONT-FAMILY UPDATED */
      font-size: 0.82rem;
      color: #9C9284;
      background-color: var(--bg-cream);
      transition: all 0.2s ease;
    }

    .checkout-step.active { color: var(--rose-accent); }
    .checkout-step.active .checkout-step-num {
      border-color: var(--rose-deep);
      background-color: var(--rose-deep);
      color: #FFFFFF;
    }

    .checkout-step.done .checkout-step-num {
      border-color: var(--rose-deep);
      color: var(--rose-accent);
    }

    .checkout-step-line {
      width: 32px;
      height: 1px;
      background-color: var(--border-hairline);
    }

    /* Booking Summary Bar (shown in Step 2) */
    .booking-summary-bar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      background-color: var(--card-cream);
      border: 1px solid var(--border-hairline);
      padding: 14px 18px;
      margin-bottom: 16px;
      flex-wrap: wrap;
    }

    .booking-summary-text {
      font-size: 0.85rem;
      color: #B3AA98;
      line-height: 1.5;
    }

    .booking-summary-text strong { color: var(--text-ink); }

    .booking-summary-edit {
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      color: var(--rose-accent);
      cursor: pointer;
      background: none;
      border: none;
      padding: 4px 0;
      white-space: nowrap;
    }

    .btn-continue {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      background-color: var(--rose-deep);
      color: #FFFFFF;
      padding: 16px 32px;
      font-size: 0.875rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      border: 1px solid var(--rose-deep);
      cursor: pointer;
      width: 100%;
      transition: all 0.2s ease;
      min-height: 44px;
    }

    .btn-continue:hover {
      background-color: var(--rose-accent);
      border-color: var(--rose-accent);
    }

    /* Payment Section */
    .payment-section {
      grid-column: 1 / -1;
      margin-top: 8px;
      padding-top: 28px;
      border-top: 1px dashed var(--border-hairline);
    }

    .payment-amount-bar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      background-color: var(--card-cream);
      border: 1px solid var(--border-hairline);
      padding: 14px 18px;
      margin-bottom: 20px;
      flex-wrap: wrap;
      gap: 8px;
    }

    .payment-amount-label {
      font-size: 0.78rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: #B3AA98;
    }

    .payment-amount-value {
      font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; /* ← FONT-FAMILY UPDATED */
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--rose-accent);
    }

    .pay-method-toggle {
      display: flex;
      gap: 10px;
      margin-bottom: 20px;
      flex-wrap: wrap;
      border: 0;
      padding: 0;
      min-width: 0;
    }

    .pay-method-pill {
      flex: 1;
      min-width: 120px;
      text-align: center;
      padding: 12px 10px;
      border: 1px solid var(--border-hairline);
      background-color: var(--bg-cream);
      font-size: 0.8rem;
      font-weight: 600;
      letter-spacing: 0.03em;
      cursor: pointer;
      transition: all 0.15s ease;
      user-select: none;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      position: relative;
    }

    .pay-method-pill.selected {
      background-color: var(--rose-deep);
      border-color: var(--rose-deep);
      color: #FFFFFF;
    }

    .pay-method-radio {
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

    .pay-method-pill:focus-within {
      outline: 2px solid var(--rose-accent, #C9A66B);
      outline-offset: 2px;
    }

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

    .pay-fields { display: none; }
    .pay-fields.active { display: grid; grid-template-columns: 1fr; gap: 20px; }
    @media (min-width: 768px) { .pay-fields.active { grid-template-columns: 1fr 1fr; } }

    .demo-badge {
      display: inline-block;
      background-color: #241F17;
      color: #C9A66B;
      border: 1px solid #C9A66B;
      font-size: 0.68rem;
      font-weight: 700;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      padding: 3px 9px;
      border-radius: 3px;
      margin-left: 8px;
      vertical-align: middle;
    }

    .field-error {
      font-size: 0.75rem;
      color: #FF6B60;
      margin-top: 2px;
      display: none;
    }
    .field-error.show { display: block; }
    .form-input.invalid { border-color: #FF6B60; }

    /* Processing overlay */
    .processing-box { padding: 20px 0; }
    .spinner {
      width: 42px; height: 42px;
      border: 3px solid var(--border-hairline);
      border-top-color: var(--rose-deep);
      border-radius: 50%;
      margin: 0 auto 18px auto;
      animation: spin 0.8s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* Payment confirmation status pill */
    .r-status-pill {
      flex-shrink: 0;
      display: inline-flex;
      align-items: center;
      gap: 5px;
      background-color: rgba(37, 211, 102, 0.16);
      color: #3FDB74;
      border: 1px solid rgba(37, 211, 102, 0.4);
      font-size: 0.66rem;
      font-weight: 700;
      letter-spacing: 0.06em;
      text-transform: uppercase;
      padding: 6px 11px;
      border-radius: 4px;
      white-space: nowrap;
    }

    /* Modal / Success Confirmation */
    .modal-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background-color: rgba(0, 0, 0, 0.75);
      backdrop-filter: blur(4px);
      z-index: 1000;
      align-items: center;
      justify-content: center;
      padding: 24px;
    }

    .modal-content {
      background-color: transparent;
      border: none;
      border-radius: 0;
      padding: 0;
      max-width: 520px;
      width: 100%;
      text-align: center;
      box-shadow: none;
    }

    .modal-content .btn-submit,
    .modal-content a.btn-submit {
      border-radius: 4px;
    }

    .modal-title {
      font-family: 'Playfair Display', Georgia, 'Times New Roman', serif; /* ← FONT-FAMILY UPDATED */
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--text-ink);
      margin-bottom: 12px;
    }

    .modal-summary {
      background-color: #1E1A14;
      border: 1px solid var(--border-hairline);
      padding: 16px;
      text-align: left;
      font-size: 0.875rem;
      color: #C9C2B4;
      margin: 20px 0;
      line-height: 1.7;
    }

    /* ===== Receipt Printer ===== */
    .rp-wrap {
      position: relative;
      width: 100%;
      max-width: 360px;
      margin: 0 auto;
    }

    .rp-machine {
      position: relative;
      z-index: 2;
      width: 100%;
      background-color: #241F17;
      border: 1px solid var(--border-hairline);
      border-radius: 16px 16px 6px 6px;
      padding: 12px 14px 16px 14px;
      box-shadow:
        0 16px 30px -18px rgba(0, 0, 0, 0.65),
        inset 0 1px 0 rgba(255, 255, 255, 0.05);
    }

    .rp-screen {
      background-color: #0A0806;
      border: 1px solid #000;
      border-radius: 6px;
      padding: 9px 12px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 8px;
      margin-bottom: 10px;
    }

    .rp-screen-left {
      display: flex;
      align-items: center;
      gap: 8px;
      min-width: 0;
    }

    .rp-screen-icon {
      width: 15px;
      height: 15px;
      flex-shrink: 0;
      border: 2px solid var(--rose-accent);
      border-top-color: transparent;
      border-radius: 50%;
      animation: rp-spin 0.8s linear infinite;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 10px;
      line-height: 1;
      color: #3FDB74;
      box-sizing: border-box;
    }

    .rp-screen-icon.rp-done {
      animation: none;
      border: none;
    }

    @keyframes rp-spin {
      to { transform: rotate(360deg); }
    }

    .rp-screen-label {
      font-size: 0.76rem;
      font-weight: 600;
      color: #E4DCC8;
      letter-spacing: 0.04em;
    }

    .rp-slot {
      height: 6px;
      background-color: var(--rose-deep);
      border-radius: 3px;
      box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.65);
    }

    .rp-paper-mask {
      position: relative;
      z-index: 1;
      height: 330px;
      overflow: hidden;
      margin-top: -4px;
      border-radius: 0;
    }

    .rp-paper {
      font-family: 'Inter', ui-monospace, SFMono-Regular, Menlo, monospace; /* ← FONT-FAMILY UPDATED */
      background-color: #F3EEE2;
      color: #15130E;
      padding: 22px 16px 26px 16px;
      transform: translateY(calc(-100% + 2px));
      position: relative;
      box-shadow: none;
      /* Exact 40-tooth perforated edge, generated with the same
         receiptToothCount/receiptToothDepth math as the source
         component (40 teeth, 4px depth). */
      clip-path: polygon(0 0, 100% 0, 100% calc(100% - 4px), 98.75% 100%, 97.5% calc(100% - 4px), 96.25% 100%, 95% calc(100% - 4px), 93.75% 100%, 92.5% calc(100% - 4px), 91.25% 100%, 90% calc(100% - 4px), 88.75% 100%, 87.5% calc(100% - 4px), 86.25% 100%, 85% calc(100% - 4px), 83.75% 100%, 82.5% calc(100% - 4px), 81.25% 100%, 80% calc(100% - 4px), 78.75% 100%, 77.5% calc(100% - 4px), 76.25% 100%, 75% calc(100% - 4px), 73.75% 100%, 72.5% calc(100% - 4px), 71.25% 100%, 70% calc(100% - 4px), 68.75% 100%, 67.5% calc(100% - 4px), 66.25% 100%, 65% calc(100% - 4px), 63.75% 100%, 62.5% calc(100% - 4px), 61.25% 100%, 60% calc(100% - 4px), 58.75% 100%, 57.5% calc(100% - 4px), 56.25% 100%, 55% calc(100% - 4px), 53.75% 100%, 52.5% calc(100% - 4px), 51.25% 100%, 50% calc(100% - 4px), 48.75% 100%, 47.5% calc(100% - 4px), 46.25% 100%, 45% calc(100% - 4px), 43.75% 100%, 42.5% calc(100% - 4px), 41.25% 100%, 40% calc(100% - 4px), 38.75% 100%, 37.5% calc(100% - 4px), 36.25% 100%, 35% calc(100% - 4px), 33.75% 100%, 32.5% calc(100% - 4px), 31.25% 100%, 30% calc(100% - 4px), 28.75% 100%, 27.5% calc(100% - 4px), 26.25% 100%, 25% calc(100% - 4px), 23.75% 100%, 22.5% calc(100% - 4px), 21.25% 100%, 20% calc(100% - 4px), 18.75% 100%, 17.5% calc(100% - 4px), 16.25% 100%, 15% calc(100% - 4px), 13.75% 100%, 12.5% calc(100% - 4px), 11.25% 100%, 10% calc(100% - 4px), 8.75% 100%, 7.5% calc(100% - 4px), 6.25% 100%, 5% calc(100% - 4px), 3.75% 100%, 2.5% calc(100% - 4px), 1.25% 100%, 0% calc(100% - 4px));
    }

    /* Authentic thermal-printer feed: paper advances in short jerky
       bursts with tiny pauses in between, like the print head is
       stepping the paper forward line by line rather than gliding out
       smoothly. Values/timings copied 1:1 from printingTransformKeyframes
       / printingKeyframeTimes in the reference component. */
    @keyframes rp-print-feed {
      0%    { transform: translateY(calc(-100% + 2px)); }
      7.5%  { transform: translateY(-91%); }
      10.5% { transform: translateY(-91%); }
      18%   { transform: translateY(-81%); }
      21%   { transform: translateY(-81%); }
      28.5% { transform: translateY(-70%); }
      31.5% { transform: translateY(-70%); }
      39%   { transform: translateY(-58%); }
      42%   { transform: translateY(-58%); }
      49.5% { transform: translateY(-45%); }
      52.5% { transform: translateY(-45%); }
      60%   { transform: translateY(-32%); }
      63%   { transform: translateY(-32%); }
      70.5% { transform: translateY(-20%); }
      73.5% { transform: translateY(-20%); }
      81%   { transform: translateY(-10%); }
      84%   { transform: translateY(-10%); }
      91.5% { transform: translateY(-3%); }
      94.5% { transform: translateY(-3%); }
      100%  { transform: translateY(0%); }
    }

    .rp-wrap[data-stage="printing"] .rp-paper {
      animation: rp-print-feed 1.75s linear forwards;
    }

    .rp-wrap[data-stage="complete"] .rp-paper {
      transform: translateY(0%);
    }

    /* The paper mask itself fades in the instant the stage leaves
       "processing" — matching the source's isReceiptVisible opacity
       gate (0.16s ease-out) rather than relying on transform alone. */
    .rp-paper-mask {
      opacity: 0;
      transition: opacity 0.16s cubic-bezier(0.23, 1, 0.32, 1);
    }

    .rp-wrap[data-stage="printing"] .rp-paper-mask,
    .rp-wrap[data-stage="complete"] .rp-paper-mask {
      opacity: 1;
    }

    /* Light-leak line where the paper emerges from the printer slot,
       sitting above the receipt while it feeds/rests. */
    .rp-paper-mask::before {
      content: '';
      position: absolute;
      left: 6px;
      right: 6px;
      top: -1px;
      height: 8px;
      background: rgba(10, 8, 6, 0.75);
      filter: blur(6px);
      z-index: 2;
      pointer-events: none;
    }

    .rp-line { font-size: 0.78rem; line-height: 1.6; }
    .rp-center { text-align: center; }
    .rp-bold { font-weight: 700; }
    .rp-muted { color: #7A7160; }
    .rp-dashed { border-top: 1px dashed #B9AF9B; margin: 10px 0; }
    .rp-row {
      display: flex;
      justify-content: space-between;
      gap: 12px;
      font-size: 0.78rem;
      line-height: 1.6;
    }

    .rp-download-btn {
      display: none;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      width: 40px;
      height: 34px;
      padding: 0;
      color: var(--bronze-taupe);
      background-color: transparent;
      border: none;
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .rp-download-btn:hover {
      color: var(--rose-accent);
    }

    .rp-wrap[data-stage="complete"] .rp-download-btn {
      display: flex;
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
    .dk-profile-menu.open { display: block; }
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
          <li><a href="about.php" class="nav-link">About</a></li>
          <li><a href="contact.php" class="nav-link">Contact</a></li>
          <li><a href="membership.php" class="nav-link">Membership<span id="navMemberBadge"></span></a></li>
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
  <script>
    // Membership tier badge next to "Membership" nav link, if the user has one
    var userMembership = "<?php echo $_SESSION['membership'] ?? 'none'; ?>";
    (function () {
      var badge = document.getElementById('navMemberBadge');
      if (badge && userMembership !== 'none') {
        var label = userMembership === 'gold' ? 'Gold' : 'Silver';
        badge.innerHTML = '<span class="dk-member-badge dk-badge-' + userMembership + ' nav-member-badge">' + label + '</span>';
      }
    })();
  </script>


  <main id="main-content" tabindex="-1">
    <!-- Page Hero -->
    <section class="page-hero">
      <div class="container reveal">
<h1 class="font-serif">Reserve Your Appointment</h1>
      </div>
    </section>

    <div class="container" style="padding-bottom: 60px;">
      
      <div class="category-header reveal">
        <span class="eyebrow-tag">APPOINTMENT RESERVATION</span>
        <div class="hairline"></div>
      </div>

      <div class="booking-card reveal">

        <?php if ($bookingMessage) { ?>
          <div style="margin-bottom:20px;padding:14px 16px;border-radius:6px;background:rgba(224,102,90,0.12);border:1px solid #E0665A;color:#E0665A;font-size:0.9rem;font-weight:600;">
            <?php echo htmlspecialchars($bookingMessage); ?>
          </div>
        <?php } ?>

        <?php if (!$isLoggedIn) { ?>

          <!-- Not logged in — simple message + links to the login/signup pages -->
          <div style="text-align:center;padding:50px 20px;">
            <p style="margin-bottom:20px;font-size:1rem;">Please login to book an appointment.</p>
            <a href="login.php?redirect=booking.php" class="header-book-btn" style="margin-right:10px;">Login</a>
            <a href="signup.php?redirect=booking.php" class="header-book-btn" style="background:transparent;border:1px solid var(--rose-accent);color:var(--rose-accent);">Sign Up</a>
          </div>

        <?php } else { ?>

        <div class="checkout-steps">
          <div class="checkout-step active" id="stepIndicator1">
            <span class="checkout-step-num">1</span>
            <span>Details</span>
          </div>
          <div class="checkout-step-line"></div>
          <div class="checkout-step" id="stepIndicator2">
            <span class="checkout-step-num">2</span>
            <span>Payment</span>
          </div>
        </div>

        <form id="bookingForm" method="POST" action="booking.php" onsubmit="handleBookingSubmit(event)">
          <input type="hidden" name="confirm_booking" value="1">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
          <input type="hidden" id="hiddenAmount" name="amount" value="0">
          <input type="hidden" id="hiddenPaymentMethod" name="paymentMethod" value="">

          <!-- STEP 1: Reservation Details -->
          <div id="step1Panel">
          <div class="booking-grid">
            
            <div class="form-group">
              <label class="form-label" for="bName">Your Full Name *</label>
              <input type="text" id="bName" name="fullName" class="form-input" placeholder="e.g. Yash Sathwara" autocomplete="name" required value="<?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?>">
            </div>

            <div class="form-group">
              <label class="form-label" for="bPhone">Phone Number *</label>
              <input type="tel" id="bPhone" name="phone" class="form-input" placeholder="e.g. 99246 20958" autocomplete="tel" required value="<?php echo htmlspecialchars($_SESSION['user_phone'] ?? ''); ?>">
            </div>

            <div class="form-group full">
              <label class="form-label" for="bService">Select Service *</label>
              <select id="bService" name="service" class="form-select" required>
                <option value="" disabled selected>-- Choose Hair --</option>
                <optgroup label="Cut &amp; Style">
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
                </optgroup>
                <optgroup label="Coloring"> 
                  <option value="Root Touch-Up — (₹3200)">Root Touch-Up — ₹3200</option>
                  <option value="Highlights — (₹1700)">Highlights — ₹1700</option>
                  <option value="Low Lights — (₹1500)">Low Lights — ₹1500</option>
                  <option value="Gloss Treatment — (₹2000)">Gloss Treatment — ₹2000</option>
                  <option value="Color Correction — (₹1600)">Color Correction — ₹1600</option>
                  <option value="Fasion Color — (₹1900)">Fasion Color — ₹1900</option>
                </optgroup>
                <optgroup label="Spa &amp; Treatment">
                  <option value="Relaxation Spa — (₹800)">Relaxation Spa — ₹800</option>
                  <option value="Aroma Treatment — (₹1200)">Aroma Treatment — ₹1200</option>
                  <option value="Head Spa — (₹700)">Head Spa — ₹700</option>
                  <option value="Deep Tissue Spa — (₹1400)">Deep Tissue Spa — ₹1400</option>
                  <option value="Scalp Treatment — (₹700)">Scalp Treatment — ₹700</option>
                  <option value="Hydrating Spa — (₹900)">Hydrating Spa — ₹900</option>
                </optgroup>
                <optgroup label="Beard Grooming">
                  <option value="Goatee Beard — (₹500)">Goatee Beard — ₹500</option>
                  <option value="French Beard — (₹400)">French Beard — ₹400</option>
                  <option value="Full Beard Style — (₹600)">Full Beard Style — ₹600</option>
                  <option value="Italian Beard — (₹500)">Italian Beard — ₹500</option>
                  <option value="Stubble Beard — (₹250)">Stubble Beard — ₹250</option>
                  <option value="Corporate Beard — (₹350)">Corporate Beard — ₹350</option>
                  <option value="Anchor Beard — (₹500)">Anchor Beard — ₹500</option>
                  <option value="Boxed Beard — (₹400)">Boxed Beard — ₹400</option>
                </optgroup>
                <optgroup label="Bridal &amp; Party">
                  <option value="Special Bridal Package — (₹8000)">Special Bridal Package — ₹8000</option>
                  <option value="Bridal Package (₹5000)">Bridal Package — ₹5000</option>
                  <option value="Party Makeup + Hair (₹2500)">Party Makeup + Hair — ₹2500</option>
                  <option value="Groom Package (₹1500)">Groom Package — ₹1500</option>
                </optgroup>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label" for="bDate">Preferred Date *</label>
              <input type="date" id="bDate" name="appointmentDate" class="form-input" required>
            </div>

            <div class="form-group">
              <label class="form-label" for="bTime">Preferred Time Slot *</label>
              <select id="bTime" name="appointmentTime" class="form-select" required>
                <option value="09:00 AM">09:00 AM</option>
                <option value="10:00 AM">10:00 AM</option>
                <option value="11:00 AM">11:00 AM</option>
                <option value="12:00 PM">12:00 PM</option>
                <option value="01:00 PM">01:00 PM</option>
                <option value="02:00 PM">02:00 PM</option>
                <option value="03:00 PM">03:00 PM</option>
                <option value="04:00 PM">04:00 PM</option>
                <option value="05:00 PM">05:00 PM</option>
                <option value="06:00 PM">06:00 PM</option>
                <option value="07:00 PM">07:00 PM</option>
                <option value="08:00 PM">08:00 PM</option>
              </select>
            </div>

            <div class="form-group full">
              <label class="form-label" for="bNotes">Notes / Custom Requirements</label>
              <textarea id="bNotes" name="notes" class="form-textarea" rows="3" placeholder="e.g. Skin fade with textured top cut, first-time visit, specific stylist request..."></textarea>
            </div>

          </div>

          <div style="margin-top: 28px;">
            <button type="button" class="btn-continue" onclick="goToPayment()">
              Continue to Payment
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </button>
          </div>
          </div>
          <!-- END STEP 1 -->

          <!-- STEP 2: Payment -->
          <div id="step2Panel" style="display: none;">

          <div class="booking-summary-bar">
            <span class="booking-summary-text" id="summaryText"></span>
            <button type="button" class="booking-summary-edit" onclick="backToDetails()">Edit Details</button>
          </div>

          <div class="booking-grid">
            <!-- Payment Section -->
            <div class="form-group full payment-section" style="border-top: none; padding-top: 0; margin-top: 0;">
              <label class="form-label" style="margin-bottom: 14px; display: block;">
                Payment
              </label>

              <div class="payment-amount-bar">
                <span class="payment-amount-label" id="payAmountLabel">Amount Payable Now (100% of service)</span>
                <span class="payment-amount-value" id="payAmountDisplay">₹0</span>
              </div>

              <fieldset class="pay-method-toggle">
                <legend class="sr-only">Payment method</legend>
                <label class="pay-method-pill selected" id="payTabCard" for="payRadioCard">
                  <input type="radio" class="pay-method-radio" name="payMethod" id="payRadioCard" value="card" checked onchange="selectPayMethod('card')">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" style="vertical-align:-3px;margin-right:4px;"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>Card
                </label>
                <label class="pay-method-pill" id="payTabUpi" for="payRadioUpi">
                  <input type="radio" class="pay-method-radio" name="payMethod" id="payRadioUpi" value="upi" onchange="selectPayMethod('upi')">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" style="vertical-align:-3px;margin-right:4px;"><rect x="7" y="2" width="10" height="20" rx="2"/><line x1="11" y1="18" x2="13" y2="18"/></svg>UPI
                </label>
              </fieldset>

              <!-- Card Fields -->
              <div class="pay-fields active" id="cardFields">
                <div class="form-group full">
                  <label class="form-label" for="cardName">Name on Card *</label>
                  <input type="text" id="cardName" class="form-input" placeholder="e.g. Yash Sathwara" autocomplete="cc-name" aria-describedby="err-cardName">
                  <span class="field-error" id="err-cardName">Please enter the name on the card.</span>
                </div>
                <div class="form-group full">
                  <label class="form-label" for="cardNumber">Card Number *</label>
                  <input type="text" id="cardNumber" class="form-input" placeholder="1234 5678 9012 3456" inputmode="numeric" maxlength="19" autocomplete="cc-number" aria-describedby="err-cardNumber">
                  <span class="field-error" id="err-cardNumber">Enter a valid 16-digit card number.</span>
                </div>
                <div class="form-group">
                  <label class="form-label" for="cardExpiry">Expiry (MM/YY) *</label>
                  <input type="text" id="cardExpiry" class="form-input" placeholder="MM/YY" maxlength="5" autocomplete="cc-exp" aria-describedby="err-cardExpiry">
                  <span class="field-error" id="err-cardExpiry">Enter a valid future expiry date.</span>
                </div>
                <div class="form-group">
                  <label class="form-label" for="cardCvv">CVV *</label>
                  <input type="password" id="cardCvv" class="form-input" placeholder="•••" inputmode="numeric" maxlength="4" autocomplete="cc-csc" aria-describedby="err-cardCvv">
                  <span class="field-error" id="err-cardCvv">Enter a valid 3-4 digit CVV.</span>
                </div>
              </div>

              <!-- UPI Fields -->
              <div class="pay-fields" id="upiFields">
                <div class="form-group full">
                  <label class="form-label" for="upiId">UPI ID *</label>
                  <input type="text" id="upiId" class="form-input" placeholder="yourname@upi" aria-describedby="err-upiId">
                  <span class="field-error" id="err-upiId">Enter a valid UPI ID (e.g. name@bank).</span>
                </div>
              </div>

            </div>

          </div>

          <div style="margin-top: 28px;">
            <button type="submit" class="btn-submit" id="submitBtn">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M2 7a2 2 0 012-2h16a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V7zm2-1a1 1 0 00-1 1v1h18V7a1 1 0 00-1-1H4zm-1 4v6a1 1 0 001 1h16a1 1 0 001-1v-6H3zm3 3h4v1H6v-1z"/></svg>
              <span id="submitBtnLabel">Pay ₹0 &amp; Confirm Booking</span>
            </button>
          </div>

          </div>
          <!-- END STEP 2 -->

        </form>

        <?php } // end of the login-check / success / form if-else ?>

      </div>

    </div>
  </main>

  <!-- Modal Confirmation Overlay -->
  <div class="modal-overlay" id="bookingModal">
    <div class="modal-content">

      <div class="rp-wrap" id="rpWrap" data-stage="processing">
        <div class="rp-machine">
          <div class="rp-screen">
            <span class="rp-screen-left">
              <span class="rp-screen-icon" id="rpScreenIcon" aria-hidden="true"></span>
              <span class="rp-screen-label" id="rpScreenLabel" role="status" aria-live="polite" aria-atomic="true">Processing your order</span>
            </span>
            <button type="button" class="rp-download-btn" id="rpDownloadBtn" onclick="downloadReceipt()" aria-label="Download receipt" title="Download receipt">
              <svg width="22px" height="22px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" color="currentColor"><path d="M6 20L18 20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M12 4V16M12 16L15.5 12.5M12 16L8.5 12.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
            </button>
          </div>
          <div class="rp-slot"></div>
        </div>

        <div class="rp-paper-mask">
          <div class="rp-paper" id="rpPaper">
            <div class="rp-line rp-center rp-bold" style="font-size: 0.95rem; letter-spacing: 0.04em;">DEEPKALA HAIR SALON</div>
            <div class="rp-line rp-center rp-muted">Ghodasar, Ahmedabad</div>
            <div class="rp-dashed"></div>
            <div class="rp-row"><span class="rp-muted">Name</span><span class="rp-bold" id="rName"><?php echo htmlspecialchars($bookedName); ?></span></div>
            <div class="rp-row"><span class="rp-muted">Service</span><span id="rService"><?php echo htmlspecialchars($bookedService); ?></span></div>
            <div class="rp-row"><span class="rp-muted">Barber</span><span id="rBarber"><?php echo htmlspecialchars($bookedBarberName); ?></span></div>
            <div class="rp-row"><span class="rp-muted">Date &amp; Time</span><span id="rAppt"><?php echo htmlspecialchars($bookedDate . ' at ' . $bookedTime); ?></span></div>
            <div class="rp-dashed"></div>
            <div class="rp-row rp-bold" style="font-size: 0.86rem;"><span>Total Paid</span><span id="rAmount">₹<?php echo (int) $bookedAmount; ?></span></div>
            <div class="rp-line rp-muted" id="rMethod">Paid via <?php echo htmlspecialchars($bookedMethod); ?></div>
            <div class="rp-line rp-muted" id="rTxn">Txn ID: <?php echo htmlspecialchars($bookedTxnId); ?></div>
            <div class="rp-dashed"></div>
            <div class="rp-line rp-center rp-muted">Thank you! See you soon <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true" style="vertical-align:-1px;"><circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><line x1="20" y1="4" x2="8.12" y2="15.88"/><line x1="14.47" y1="14.48" x2="20" y2="20"/><line x1="8.12" y1="8.12" x2="12" y2="12"/></svg></div>
          </div>
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

  <!-- Floating WhatsApp -->
  <a href="https://wa.me/919924620958?text=Hi%20Deepkala%20Hair%20Salon%2C%20I%27d%20like%20to%20know%20more" class="whatsapp-float" target="_blank" rel="noopener noreferrer" title="Chat on WhatsApp">
    <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
  </a>

  <script>
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

      // Default Date to Today
      const dateInput = document.getElementById('bDate');
      if (dateInput) {
        const today = new Date().toISOString().split('T')[0];
        dateInput.value = today;
        dateInput.min = today;
      }

      // Parse URL Parameter for pre-selected service
      const urlParams = new URLSearchParams(window.location.search);
      const preSelectedService = urlParams.get('service');
      if (preSelectedService) {
        const serviceSelect = document.getElementById('bService');
        for (let option of serviceSelect.options) {
          if (option.value.toLowerCase().includes(preSelectedService.toLowerCase())) {
            option.selected = true;
            break;
          }
        }
      }

      // Update payable amount whenever service changes
      const serviceSelectEl = document.getElementById('bService');
      if (serviceSelectEl) {
        serviceSelectEl.addEventListener('change', updatePayableAmount);
        updatePayableAmount();
      }

      // Auto-format card number (adds spaces every 4 digits)
      const cardNumberEl = document.getElementById('cardNumber');
      if (cardNumberEl) {
        cardNumberEl.addEventListener('input', () => {
          let digits = cardNumberEl.value.replace(/\D/g, '').slice(0, 16);
          cardNumberEl.value = digits.replace(/(.{4})/g, '$1 ').trim();
        });
      }

      // Auto-format expiry (MM/YY)
      const cardExpiryEl = document.getElementById('cardExpiry');
      if (cardExpiryEl) {
        cardExpiryEl.addEventListener('input', () => {
          let digits = cardExpiryEl.value.replace(/\D/g, '').slice(0, 4);
          if (digits.length >= 3) {
            cardExpiryEl.value = digits.slice(0, 2) + '/' + digits.slice(2);
          } else {
            cardExpiryEl.value = digits;
          }
        });
      }

      // CVV: digits only
      const cardCvvEl = document.getElementById('cardCvv');
      if (cardCvvEl) {
        cardCvvEl.addEventListener('input', () => {
          cardCvvEl.value = cardCvvEl.value.replace(/\D/g, '').slice(0, 4);
        });
      }
    });

    let currentPayMethod = 'card';
    let currentServicePrice = 0;
    let currentBasePrice = 0;

    function extractPrice(serviceValue) {
      if (!serviceValue) return 0;
      const match = serviceValue.match(/₹\s?([\d,]+)/);
      if (!match) return 0;
      return parseInt(match[1].replace(/,/g, ''), 10) || 0;
    }

    // Membership discount % — comes from the PHP session (set at login), not localStorage
    var membershipDiscount = userMembership === 'gold' ? 20 : (userMembership === 'silver' ? 10 : 0);

    function updatePayableAmount() {
      const serviceValue = document.getElementById('bService').value;
      currentBasePrice = extractPrice(serviceValue);

      const label = document.getElementById('payAmountLabel');
      const display = document.getElementById('payAmountDisplay');

      if (membershipDiscount > 0) {
        currentServicePrice = Math.round(currentBasePrice * (1 - membershipDiscount / 100));
        const baseFormatted = '₹' + currentBasePrice.toLocaleString('en-IN');
        const discFormatted = '₹' + currentServicePrice.toLocaleString('en-IN');
        label.innerHTML = `Amount Payable Now &middot; <span class="dk-member-badge dk-badge-${userMembership}" style="margin-left:4px;">${userMembership === 'gold' ? 'Gold' : 'Silver'} ${membershipDiscount}% OFF</span>`;
        display.innerHTML = `<span class="dk-price-old">${baseFormatted}</span><span class="dk-price-new">${discFormatted}</span>`;
      } else {
        currentServicePrice = currentBasePrice;
        const formatted = '₹' + currentServicePrice.toLocaleString('en-IN');
        label.textContent = 'Amount Payable Now (100% of service)';
        display.textContent = formatted;
      }

      const formattedFinal = '₹' + currentServicePrice.toLocaleString('en-IN');
      document.getElementById('submitBtnLabel').textContent = `Pay ${formattedFinal} & Confirm Booking`;
    }

    function buildSummaryText() {
      return '';
    }

    function goToPayment() {
      const form = document.getElementById('bookingForm');

      // Only Step 1 fields carry the "required" attribute, so this validates
      // name, phone, service, date & time without touching payment fields.
      if (!form.checkValidity()) {
        form.reportValidity();
        return;
      }

      updatePayableAmount();
      document.getElementById('summaryText').innerHTML = buildSummaryText();

      document.getElementById('step1Panel').style.display = 'none';
      document.getElementById('step2Panel').style.display = 'block';

      document.getElementById('stepIndicator1').classList.remove('active');
      document.getElementById('stepIndicator1').classList.add('done');
      document.getElementById('stepIndicator2').classList.add('active');

      document.querySelector('.booking-card').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function backToDetails() {
      document.getElementById('step2Panel').style.display = 'none';
      document.getElementById('step1Panel').style.display = 'block';

      document.getElementById('stepIndicator2').classList.remove('active');
      document.getElementById('stepIndicator1').classList.remove('done');
      document.getElementById('stepIndicator1').classList.add('active');

      document.querySelector('.booking-card').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function selectPayMethod(method) {
      currentPayMethod = method;
      document.getElementById('payTabCard').classList.toggle('selected', method === 'card');
      document.getElementById('payTabUpi').classList.toggle('selected', method === 'upi');
      document.getElementById('cardFields').classList.toggle('active', method === 'card');
      document.getElementById('upiFields').classList.toggle('active', method === 'upi');
      // Clear errors when switching
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

    function validatePayment() {
      let valid = true;

      if (currentPayMethod === 'card') {
        const name = document.getElementById('cardName').value.trim();
        const number = document.getElementById('cardNumber').value.replace(/\s/g, '');
        const expiry = document.getElementById('cardExpiry').value.trim();
        const cvv = document.getElementById('cardCvv').value.trim();

        const nameOk = name.length >= 2;
        showFieldError('cardName', !nameOk);
        if (!nameOk) valid = false;

        const numberOk = /^\d{16}$/.test(number);
        showFieldError('cardNumber', !numberOk);
        if (!numberOk) valid = false;

        let expiryOk = /^\d{2}\/\d{2}$/.test(expiry);
        if (expiryOk) {
          const [mm, yy] = expiry.split('/').map(Number);
          const now = new Date();
          const curYY = now.getFullYear() % 100;
          const curMM = now.getMonth() + 1;
          if (mm < 1 || mm > 12) expiryOk = false;
          else if (yy < curYY || (yy === curYY && mm < curMM)) expiryOk = false;
        }
        showFieldError('cardExpiry', !expiryOk);
        if (!expiryOk) valid = false;

        const cvvOk = /^\d{3,4}$/.test(cvv);
        showFieldError('cardCvv', !cvvOk);
        if (!cvvOk) valid = false;

      } else {
        const upi = document.getElementById('upiId').value.trim();
        const upiOk = /^[\w.\-]{2,}@[a-zA-Z]{2,}$/.test(upi);
        showFieldError('upiId', !upiOk);
        if (!upiOk) valid = false;
      }

      return valid;
    }

    function generateTxnId() {
      return 'TXN' + Math.random().toString(36).slice(2, 10).toUpperCase();
    }

    function maskPaymentMethod() {
      if (currentPayMethod === 'card') {
        const number = document.getElementById('cardNumber').value.replace(/\s/g, '');
        return `Card •••• ${number.slice(-4)}`;
      } else {
        const upi = document.getElementById('upiId').value.trim();
        return `UPI (${upi})`;
      }
    }

    function handleBookingSubmit(e) {
      // Validate core booking fields via native HTML5 validity
      const form = document.getElementById('bookingForm');
      if (!form.checkValidity()) {
        e.preventDefault();
        form.reportValidity();
        return;
      }

      // Validate payment fields
      if (!validatePayment()) {
        e.preventDefault();
        return;
      }

      if (currentServicePrice <= 0) {
        e.preventDefault();
        alert('Please select a valid service before paying.');
        return;
      }

      // Fill the two hidden fields the PHP script needs, then let the form
      // submit normally (real page reload — no fetch/AJAX involved).
      document.getElementById('hiddenAmount').value = currentServicePrice;
      document.getElementById('hiddenPaymentMethod').value = maskPaymentMethod();
      // (no e.preventDefault() here — form submits normally to booking.php)
    }

    // ===== Receipt printer modal =====
    // The PHP block at the very top of this file already saved the booking
    // (if it succeeded) and filled in the #rName / #rService / etc. spans
    // above. This just plays the same little "printing" animation as before,
    // triggered on page load instead of after a fetch() response.
    var bookingJustSucceeded = <?php echo $bookingSuccess ? 'true' : 'false'; ?>;

    function playReceiptAnimation() {
      const modal = document.getElementById('bookingModal');
      const rpWrap = document.getElementById('rpWrap');
      const rpIcon = document.getElementById('rpScreenIcon');
      const rpLabel = document.getElementById('rpScreenLabel');

      rpWrap.dataset.stage = 'processing';
      rpIcon.classList.remove('rp-done');
      rpIcon.textContent = '';
      rpLabel.textContent = 'Processing your order';
      modal.style.display = 'flex';

      setTimeout(() => {
        rpWrap.dataset.stage = 'printing';
        rpLabel.textContent = 'Printing your receipt';
      }, 900);

      setTimeout(() => {
        rpWrap.dataset.stage = 'complete';
        rpIcon.classList.add('rp-done');
        rpIcon.innerHTML = '<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>';
        rpLabel.textContent = 'Order complete';
      }, 900 + 1750);
    }

    if (bookingJustSucceeded) {
      window.addEventListener('DOMContentLoaded', playReceiptAnimation);
    }

    function downloadReceipt() {
      const name = document.getElementById('rName').textContent;
      const service = document.getElementById('rService').textContent;
      const appt = document.getElementById('rAppt').textContent;
      const amount = document.getElementById('rAmount').textContent.replace('₹', 'Rs. ');
      const method = document.getElementById('rMethod').textContent;
      const txn = document.getElementById('rTxn').textContent;

      const { jsPDF } = window.jspdf;
      const pageW = 226;  // narrow receipt-style width (~80mm)
      const pageH = 392;
      const margin = 22;
      const contentW = pageW - margin * 2;
      const doc = new jsPDF({ unit: 'pt', format: [pageW, pageH] });

      const ink = '#15130E';
      const muted = '#7A7160';
      const bronze = '#A9884C';
      const deep = '#0A0806';
      const dash = '#B9AF9B';
      const bg = '#F3EEE2';
      const card = '#EAE2D0';
      const cx = pageW / 2;

      const dashedLine = (yy) => {
        doc.setDrawColor(dash);
        doc.setLineDashPattern([2, 2], 0);
        doc.line(margin, yy, pageW - margin, yy);
        doc.setLineDashPattern([], 0);
      };

      doc.setFillColor(bg);
      doc.rect(0, 0, pageW, pageH, 'F');
      doc.setDrawColor(bronze);
      doc.setLineWidth(1);
      doc.roundedRect(7, 7, pageW - 14, pageH - 14, 5, 5, 'S');

      let y = 40;

      doc.setDrawColor(bronze);
      doc.setLineWidth(0.75);
      doc.circle(cx, y, 17, 'S');
      doc.setDrawColor(deep);
      doc.setLineDashPattern([1.5, 1.5], 0);
      doc.circle(cx, y, 12, 'S');
      doc.setLineDashPattern([], 0);
      doc.setFont('helvetica', 'bold');
      doc.setFontSize(14);
      doc.setTextColor(ink);
      doc.text('D', cx, y, { align: 'center', baseline: 'middle' });

      y += 34;
      doc.setFont('helvetica', 'bold');
      doc.setFontSize(12.5);
      doc.setTextColor(ink);
      doc.text('DEEPKALA HAIR SALON', cx, y, { align: 'center' });

      y += 14;
      doc.setFont('helvetica', 'normal');
      doc.setFontSize(8.5);
      doc.setTextColor(muted);
      doc.text('Ghodasar, Ahmedabad', cx, y, { align: 'center' });

      y += 16;
      dashedLine(y);

      y += 26;
      const row = (label, value, weight = 'normal', size = 9.5) => {
        doc.setFont('helvetica', 'normal');
        doc.setFontSize(8);
        doc.setTextColor(muted);
        doc.text(label.toUpperCase(), margin, y);
        doc.setFont('helvetica', weight === 'bold' ? 'bold' : 'normal');
        doc.setFontSize(size);
        doc.setTextColor(ink);
        doc.text(value, pageW - margin, y, { align: 'right' });
        y += 20;
      };
      row('Name', name, 'bold');
      row('Service', service);
      row('Date & Time', appt);

      y -= 4;
      dashedLine(y);
      y += 22;

      doc.setFillColor(card);
      doc.roundedRect(margin - 4, y - 16, contentW + 8, 26, 3, 3, 'F');
      doc.setFont('helvetica', 'bold');
      doc.setFontSize(9);
      doc.setTextColor(muted);
      doc.text('TOTAL PAID', margin, y);
      doc.setFont('helvetica', 'normal');
      doc.setFontSize(13);
      doc.setTextColor(ink);
      doc.text(amount, pageW - margin, y, { align: 'right' });

      y += 30;
      doc.setFont('helvetica', 'normal');
      doc.setFontSize(8.5);
      doc.setTextColor(muted);
      doc.text(method, cx, y, { align: 'center' });
      y += 13;
      doc.setFontSize(7.5);
      doc.text(txn, cx, y, { align: 'center' });

      y += 18;
      dashedLine(y);

      y += 24;
      doc.setFont('helvetica', 'bold');
      doc.setFontSize(10);
      doc.setTextColor(ink);
      doc.text('Thank you for visiting', cx, y, { align: 'center' });

      y += 13;
      doc.setFont('helvetica', 'normal');
      doc.setFontSize(8);
      doc.setTextColor(muted);
      doc.text('We look forward to seeing you again', cx, y, { align: 'center' });

      y += 16;
      doc.setFillColor(bronze);
      doc.circle(cx - 10, y, 1.4, 'F');
      doc.circle(cx, y, 1.4, 'F');
      doc.circle(cx + 10, y, 1.4, 'F');

      const txnId = txn.replace('Txn ID: ', '');
      doc.save(`Deepkala-Receipt-${txnId}.pdf`);
    }

    function closeModal() {
      document.getElementById('bookingModal').style.display = 'none';
      // Reset the form so the customer can make a fresh booking
      window.location.href = 'booking.php';
    }

    document.getElementById('bookingModal').addEventListener('click', (e) => {
      if (e.target.id === 'bookingModal') {
        closeModal();
      }
    });

  </script>

</body>
</html>
