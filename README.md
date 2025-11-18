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
