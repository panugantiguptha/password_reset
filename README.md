# Password Reset (PHP)

A small, self-contained "forgot password" flow built with plain PHP, PDO, and MySQL. A user enters their email, receives a time-limited reset token, and uses it to set a new password (hashed with bcrypt).

> **Note:** This is a demo/learning project. Instead of emailing the reset link, `send_reset.php` prints it directly on the page — see [Security Notes](#security-notes) before using this anywhere near production.

## Features

- Email-based password reset request form
- Cryptographically random, single-use reset tokens (`random_bytes(16)` → 32-char hex)
- Tokens expire after 1 hour and are stored in the database
- Old tokens for an email are invalidated when a new one is requested
- Client-side password strength meter and match checker (jQuery)
- Server-side validation: required fields, password match, minimum length (8 chars)
- Passwords hashed with `PASSWORD_BCRYPT` before storage
- Token deleted immediately after a successful reset (prevents reuse)

## Project Structure

```
password_reset/
├── assets/
│   ├── script.js       # Password strength meter, match check, client-side validation
│   └── style.css        # Styling for all pages
├── config.php            # PDO database connection
├── request_reset.php     # Step 1: form to enter email
├── send_reset.php        # Step 2: validates email, creates token, shows reset link
├── reset_password.php    # Step 3: validates token, shows new-password form
└── update_password.php   # Step 4: validates + saves new password, deletes token
```

## Requirements

- PHP 7.4+ (uses PDO, `password_hash`, `random_bytes`)
- MySQL/MariaDB
- A web server (Apache/Nginx) or PHP's built-in server

## Database Setup

The code expects two tables. Create them with something like:

```sql
CREATE DATABASE password_reset_db;
USE password_reset_db;

CREATE TABLE users (
    id       INT AUTO_INCREMENT PRIMARY KEY,
    email    VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE password_resets (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    email      VARCHAR(255) NOT NULL,
    token      VARCHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL
);
```

Update the connection details in `config.php` to match your environment:

```php
$host     = "127.0.0.1";
$port     = "3307";
$dbname   = "password_reset_db";
$username = "root";
$password = "";
```

## Running Locally

1. Place the `password_reset/` folder in your web server's document root (e.g. `htdocs/`, `www/`).
2. Create the database and tables (see above), and insert at least one test user with a hashed password:
   ```sql
   INSERT INTO users (email, password)
   VALUES ('test@example.com', '$2y$10$examplebcrypthashhere...');
   ```
3. Start MySQL and your web server (or run `php -S localhost:8000` from the project folder).
4. Visit `request_reset.php` in your browser and walk through the flow.

## How It Works

1. **`request_reset.php`** — User submits their email address.
2. **`send_reset.php`** — Looks up the email in `users`. If found, deletes any existing reset token for that email, generates a new random token, stores it in `password_resets` with a 1-hour expiry, and displays the reset link.
3. **`reset_password.php`** — Reads the `token` from the URL, checks it exists and hasn't expired, and shows the new-password form (with a hidden token field).
4. **`update_password.php`** — Re-validates the token, checks the passwords match and meet the length requirement, hashes the new password with bcrypt, updates `users`, and deletes the used token.

## Security Notes

This project demonstrates the core mechanics of a reset flow, but a few things should be hardened before real-world use:

- **No email delivery** — the reset link is shown directly in the browser rather than sent to the user's inbox. Anyone who can view `send_reset.php`'s response can reset that account's password. Wire up an actual mail service (e.g. PHPMailer/SMTP) and stop echoing the link to the page.
- **Email enumeration** — `send_reset.php` reveals whether an email exists in the system ("Email address not found."). Consider returning the same generic message regardless of whether the email was found.
- **No rate limiting / CSRF protection** — the request and update endpoints aren't protected against automated abuse or cross-site request forgery.
- **`die()` for error handling** — errors halt execution with plain text instead of user-friendly pages or logging.
