# Deepkala Hair Salon

A PHP/MySQL salon website for browsing services, creating customer accounts, booking appointments, managing memberships, viewing a profile, and submitting reviews.

## Technology

- PHP 8.1+
- MySQL 8+
- HTML5, CSS3, JavaScript
- InnoDB database tables with UTF-8 support

## Setup

1. Copy the project into the web root of Apache/XAMPP, for example `htdocs/deepkala`.
2. Create/import the database using `schema.sql` in phpMyAdmin or the MySQL CLI.
3. Configure database values in `backend/config.php`, or set these environment variables:
   - `DEEPKALA_DB_HOST`
   - `DEEPKALA_DB_USER`
   - `DEEPKALA_DB_PASS`
   - `DEEPKALA_DB_NAME`
4. Open `index.php` through Apache/PHP. Do not open PHP files directly from the filesystem.

## Main features

- Responsive salon landing page
- Service catalogue with search and category filters
- Signup/login using password hashing and prepared statements
- Session-protected profile and appointment history
- CSRF-protected profile, booking, membership, and review requests
- Server-side service pricing and membership discounts
- Transaction-protected barber-slot allocation
- Demo membership flow that does not process real payments
- Review submission with server-side validation and moderation status

## Important demo limitation

Membership payment is intentionally demo-only. The application must not be used with real card details. A production implementation should use a payment provider such as Razorpay or Stripe test mode and verify payment signatures server-side before activating a plan.

## Testing checklist

- Import `schema.sql` into a clean database.
- Create an account and confirm that the password column contains a hash, not the original password.
- Test invalid signup, login, booking, profile, membership, and review requests.
- Try changing the hidden booking amount in browser developer tools; the server must still calculate the correct amount.
- Try a missing or invalid CSRF token; the request must be rejected.
- Book the same barber slot concurrently and confirm that the database constraint prevents duplicates.
- Test the site at 320px, 768px, 1024px, and 1440px widths.
- Run keyboard-only and screen-reader checks for navigation, forms, and dialogs.

## Security notes

- Passwords use `password_hash()` and `password_verify()`.
- SQL writes use prepared statements in the security-sensitive handlers.
- Session cookies are HttpOnly and use SameSite protection.
- State-changing requests require a session CSRF token.
- Redirects are restricted to approved local pages.
- Raw demo payment fields are not submitted to the backend.
- Review output is escaped before dynamic rendering.

## Future work

- Add a real payment provider integration.
- Add an admin dashboard for appointments, services, and review moderation.
- Store and validate approved review images server-side.
- Add cancellation, rescheduling, email notifications, and audit logs.
- Move repeated page CSS and JavaScript into shared asset files.
