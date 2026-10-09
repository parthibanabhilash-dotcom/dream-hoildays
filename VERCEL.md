# Deploy The Dream Holidays on Vercel

The PHP entrypoint is `api/index.php`, using pinned `vercel-php@0.8.0` (PHP 8.4). The static build copies only public assets; PHP/configuration files are never published as downloadable static files. MySQL holds application data and shared sessions. Cloudinary holds processed image uploads. Apache/cPanel installation continues to work with the original private configuration and local images.

## Vercel project settings

Use the repository root as **Root Directory** (leave it blank). Do not set Root Directory to `public`.

- Framework Preset: Other.
- Build Command: `node scripts/build-vercel.cjs` (from vercel.json).
- Output Directory: `vercel-static` (from vercel.json).
- Install Command: empty; no Node/Composer dependencies are needed by the application.
- After adding/changing environment variables, redeploy.

If existing dashboard overrides point to `public`, remove those overrides. A deployment must show one PHP function for `api/index.php`; serving `public/index.php` as a static file is incorrect.

## External database

Create an externally reachable **MySQL 8.0+ / MariaDB 10.6+** database with TLS and MySQL advisory locks (`GET_LOCK` / `RELEASE_LOCK`). Import in order:

1. `database/schema.sql`
2. `database/drafts.sql` (optional unpublished example content)
3. `database/serverless.sql`

For an existing application database, apply only missing migrations; do not overwrite existing business data. The application does not automatically create a production database, import records or publish drafts. Use a separate database for preview/test deployments.

## Environment variables

Add these in **Vercel → Project → Settings → Environment Variables** for the required environment. Do not paste passwords/API secrets into GitHub or chat.

| Name | Value |
| --- | --- |
| `TDH_BASE_URL` | Your canonical HTTPS website URL, e.g. `https://dream-hoildays.vercel.app` |
| `TDH_DB_HOST` | Database provider hostname |
| `TDH_DB_PORT` | Provider port, usually 3306 |
| `TDH_DB_NAME` | Database name |
| `TDH_DB_USER` | Database user |
| `TDH_DB_PASSWORD` | Database password (secret) |
| `TDH_DB_SSL_CA` | Provider CA certificate in PEM format, including BEGIN/END lines and actual line breaks |
| `TDH_INSTALLATION_TOKEN` | A private random token of at least 32 characters; generate locally |
| `TDH_TIMEZONE` | Optional, defaults to `Asia/Kolkata` |
| `CLOUDINARY_CLOUD_NAME` | Cloudinary product environment cloud name |
| `CLOUDINARY_API_KEY` | Cloudinary API key |
| `CLOUDINARY_API_SECRET` | Cloudinary API secret (secret) |

Database certificate and secrets remain server-side. Cloudinary's API secret is never sent to the browser. Signed server uploads accept validated JPEG/PNG/WebP, enforce size/dimension limits, and request a WebP conversion with a maximum 1600-pixel side. Database sessions persist across function instances and redeployments; normal admin idle expiry remains enforced.

## First administrator and images

After successful database configuration/import, open `/setup`, supply the installation token and create your first administrator. Setup disables itself after the first account. Open `/admin/login` afterwards.

Cloud uploads are public image assets, suitable for business-approved website photos; do not upload confidential documents. Set Cloudinary variables before using image upload. Existing local photos are not automatically migrated to Cloudinary. Re-upload them through admin before changing a populated site's media driver; the supplied business logo is bundled as a fallback asset.

## Verify before launch

- Visit `/`: response is HTML, not a PHP download. Missing database environment shows a setup-in-progress HTML response with HTTP 503.
- `/config/config.php`, `/app/admin.php`, `/storage`, `/tests` and dotfiles must not expose source.
- Verify login, logout, CSRF, password change and enquiries against the external database.
- Verify an image upload, replacement and deletion in Cloudinary; publish only approved photos/listings.
- Redeploy and verify sessions, enquiry records and uploaded images persist.
- Configure the approved Booking number, business contacts and policies in admin.
- Run the production checks in LAUNCH-CHECKLIST.md.

Local tests do not prove external database TLS, cloud API credentials, live provider image conversion or the hosted PHP build. Those checks require actual provisioned accounts/environment variables. Vercel function request-size limits may reject large multipart uploads before PHP runs; keep uploaded photos below the hosting limit the application uses a 3 MB upload limit on Vercel (8 MB on ordinary PHP hosting) to leave room for multipart overhead.

References: [PHP runtime](https://github.com/vercel-community/php), [Vercel runtimes](https://vercel.com/docs/functions/runtimes), [Cloudinary upload API](https://cloudinary.com/documentation/image_upload_api_reference).
