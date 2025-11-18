This is a [Next.js](https://nextjs.org) project bootstrapped with [`create-next-app`](https://nextjs.org/docs/app/api-reference/cli/create-next-app).

## Getting Started

First, run the development server:

```bash
npm run dev
# or
yarn dev
# or
pnpm dev
# or
bun dev
```

Open [http://localhost:3000](http://localhost:3000) with your browser to see the result.

You can start editing the page by modifying `app/page.tsx`. The page auto-updates as you edit the file.

This project uses [`next/font`](https://nextjs.org/docs/app/building-your-application/optimizing/fonts) to automatically optimize and load [Geist](https://vercel.com/font), a new font family for Vercel.

## Build for Production

This application is configured for static export, making it compatible with standard web hosting services like elin.hu.

To build the application for production:

```bash
npm run build
```

This command will:
- Compile and optimize all pages
- Generate static HTML, CSS, and JavaScript files
- Create an `out/` directory with all the production files

## Deploy to elin.hu (Static Hosting)

After building the application, follow these steps to deploy to elin.hu:

1. **Build the application:**
   ```bash
   npm run build
   ```

2. **Upload via FTP:**
   - Connect to your elin.hu hosting via FTP client (e.g., FileZilla, WinSCP)
   - Navigate to your public web directory (usually `public_html` or `www`)
   - Upload all contents from the `out/` directory to your web directory
   - Make sure to preserve the directory structure

3. **Verify deployment:**
   - Visit your website URL to confirm the deployment was successful
   - Test all pages and navigation to ensure everything works correctly

**Important notes:**
- The `out/` directory contains all static files needed for deployment
- The application uses `unoptimized: true` for images to ensure compatibility with static hosting
- No server-side rendering or API routes are available in static export mode

## Learn More

To learn more about Next.js, take a look at the following resources:

- [Next.js Documentation](https://nextjs.org/docs) - learn about Next.js features and API.
- [Learn Next.js](https://nextjs.org/learn) - an interactive Next.js tutorial.
- [Next.js Static Export](https://nextjs.org/docs/app/building-your-application/deploying/static-exports) - documentation on static exports

You can check out [the Next.js GitHub repository](https://github.com/vercel/next.js) - your feedback and contributions are welcome!

## Contact Form Setup

The contact form requires additional configuration to work properly with the PHP backend.

### Prerequisites

- PHP 7.4 or higher
- PHPMailer library (optional but recommended)
- Google reCAPTCHA v2 site key and secret key

### Configuration Steps

1. **Copy the configuration template:**
   ```bash
   cp public/config.example.php public/config.php
   ```

2. **Edit `public/config.php` with your credentials:**
   - SMTP settings (email server details)
   - reCAPTCHA secret key
   - Admin email address where form submissions will be sent
   
   **Important:** Never commit `config.php` to version control as it contains sensitive credentials.

3. **Set up reCAPTCHA:**
   - Register your site at [Google reCAPTCHA](https://www.google.com/recaptcha/admin)
   - Choose reCAPTCHA v2 ("I'm not a robot" checkbox)
   - Add your domain to the authorized domains
   - Copy the site key and secret key
   - Add the site key to your environment variables or Next.js config
   - Add the secret key to `public/config.php`

4. **Install PHPMailer (recommended):**
   ```bash
   cd public
   composer require phpmailer/phpmailer
   ```
   
   Or manually download PHPMailer and place it in the `public/PHPMailer/` directory.

5. **Configure file upload directory:**
   - The default upload directory is `public/uploads/`
   - Ensure the web server has write permissions: `chmod 755 public/uploads/`
   - This directory is automatically created by the script if it doesn't exist

6. **Environment variable for reCAPTCHA site key:**
   Create a `.env.local` file in the project root:
   ```
   NEXT_PUBLIC_RECAPTCHA_SITE_KEY=your-site-key-here
   ```

### Security Features

The contact form includes several security measures:

- **reCAPTCHA validation** - Prevents automated spam submissions
- **Honeypot field** - Hidden field to catch bots
- **Input sanitization** - All user inputs are sanitized to prevent XSS attacks
- **File type validation** - Only allows PDF and compressed files
- **File size limits** - Maximum 10MB per file
- **Email validation** - Validates email format
- **Phone validation** - Validates Hungarian phone number format
- **Secure configuration** - Sensitive credentials stored outside version control

### Testing the Contact Form

After configuration:

1. Start the development server: `npm run dev`
2. Navigate to the contact section
3. Fill out the form with valid data
4. Complete the reCAPTCHA challenge
5. Submit the form
6. Check the admin email inbox for the submission

### Troubleshooting

**Form submission fails:**
- Check that `config.php` exists and has correct credentials
- Verify reCAPTCHA keys are correct
- Check PHP error logs for detailed error messages
- Ensure SMTP settings are correct

**Files not uploading:**
- Check that `public/uploads/` directory exists and is writable
- Verify file size is under 10MB
- Ensure file type is PDF, ZIP, RAR, or 7Z

**reCAPTCHA not working:**
- Verify the site key matches the domain
- Check that the secret key is correct in `config.php`
- Ensure the domain is authorized in Google reCAPTCHA console
