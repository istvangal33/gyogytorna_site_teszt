<?php
/**
 * Configuration file template for contact form
 * 
 * IMPORTANT: 
 * 1. Copy this file to config.php
 * 2. Fill in your actual credentials
 * 3. Never commit config.php to version control
 */

// SMTP Configuration
define('SMTP_HOST', 'mail.example.com');           // Your SMTP server
define('SMTP_PORT', 587);                           // SMTP port (587 for TLS, 465 for SSL)
define('SMTP_SECURE', 'tls');                       // 'tls' or 'ssl'
define('SMTP_USERNAME', 'your-email@example.com'); // Your email address
define('SMTP_PASSWORD', 'your-password-here');     // Your email password
define('SMTP_FROM_EMAIL', 'your-email@example.com'); // From email address
define('SMTP_FROM_NAME', 'Your Website Name');     // From name

// reCAPTCHA Configuration
define('RECAPTCHA_SECRET_KEY', 'your-recaptcha-secret-key-here');

// Email Settings
define('ADMIN_EMAIL', 'admin@example.com');        // Where to send contact form submissions
define('MAIL_CHARSET', 'UTF-8');                   // Character encoding (UTF-8 for Hungarian characters)

// File Upload Settings
define('MAX_FILE_SIZE', 10485760);                 // 10MB in bytes
define('ALLOWED_FILE_TYPES', ['pdf', 'zip', 'rar', '7z']);
define('UPLOAD_DIR', __DIR__ . '/uploads/');       // Directory for uploaded files

// Security Settings
define('ENABLE_HONEYPOT', true);                   // Enable honeypot spam protection
define('HONEYPOT_FIELD', 'website');               // Name of honeypot field
