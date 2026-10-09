# The Dream Holidays

A standalone, server-rendered PHP/MySQL travel website with original responsive design, local Three.js/GSAP animations, WhatsApp Booking actions, database-backed website enquiries, and a secure content administration panel.

## Local preview

The current workspace has a local PHP server at **http://127.0.0.1:8098/** and an isolated database named `dream_holidays_local_test`. Sample packages, services, About content and policies are drafts. No contact number or customer review is invented. Booking remains disabled until an approved number is entered.

Create your own administrator at http://127.0.0.1:8098/index.php?route=setup using the private `installation_token` in `config/config.php`. There is no default administrator or shared password. Do not share or upload that local configuration. Browser/HTTP test accounts are temporary and removed after tests.

## Hosting requirements

- PHP **8.2 or newer** with PDO MySQL, GD with WebP support, fileinfo, JSON, sessions and OpenSSL.
- MySQL **8.0+** or MariaDB **10.6+**; InnoDB and utf8mb4.
- HTTPS, writable private `storage/` and `public/media/`, upload limit at least 8 MB and POST limit at least 10 MB.
- Apache 2.4 for the supplied `.htaccess` rules and optional readable URLs.
- No production Node.js, npm or Composer commands are required. All animation dependencies are locally bundled.

## Production installation

1. Upload `app/`, `config/`, `database/` and `storage/` outside the public document root. Configure the domain/subdomain document root to `dream-holidays/public/`. Never expose the project root, tests, configuration or storage as the document root.
2. Create an empty database and a dedicated database user restricted to this database. Import `database/schema.sql`, then optionally import `database/drafts.sql` once. Draft seeds contain no approved prices, contacts, reviews or vehicle claims.
3. Copy `config/config.example.php` to private `config/config.php`. Enter your hosting DSN, database username/password, actual HTTPS `base_url` (without a trailing slash), and a unique random installation token of at least 32 characters. A suggested token command is `php -r "echo bin2hex(random_bytes(32));"`.
4. Leave `session_secure=true` in production. Set `clean_urls=true` with Apache rewrite enabled; set false for query-string routing. If your host fixes the web root to `public_html`, put the contents of `public/` there, put `app/`, `config/`, `storage/` alongside `public_html`, and set `public_dir` to the absolute `public_html` path. The entry point expects the private folders directly above the web root.
5. Give the web server write access only to private storage and public media. Typical permissions are directories 0755 and files 0644, with private configuration restricted to the hosting account/server as required. Do not use 0777. Confirm the media `.htaccess` is uploaded, including hidden files.
6. Visit `/setup` (or `/index.php?route=setup`), enter the private token and create an administrator with a unique 12–72 character password. Setup rejects further account creation as soon as one administrator exists. Remove/rotate the token after setup.
7. Sign in at `/admin` (or `/index.php?route=admin`). Fill Business settings; then approve and publish content following `LAUNCH-CHECKLIST.md`.
8. Test public pages, Booking with the actual approved number, enquiry persistence, image uploads, sitemap and HTTPS cookies on the hosting account before public launch.

The root/private-folder deny rules are additional protection; a private filesystem layout is still required. For non-Apache hosting, configure equivalent private-directory and media execution protections at the server level.

## Admin workflow

- Create packages as drafts and save. On the same edit page, scroll to Package images to upload photographs, preview, replace, delete and set display order. Record alt text and actual photo ownership/source; approve and publish images before publishing the package. The first published image is the card cover. Services/vehicles can use Images & gallery with their record #ID and owner type.
- Package duration is stored in days. Manage day-wise plans through Itinerary days and variations through Package options. Leave starting price blank for **Request Quote**. Featured homepage packages are selected by the Featured checkbox.
- Vehicle listings require a published actual vehicle photo and approved rental terms before publication. Availability is never presented as real time.
- Gallery images use owner type Gallery and owner ID 0. Choose a gallery category; record alternative text, photo ownership/source and licensing, then publish. Sort order controls image order. Edit an image to replace it; delete removes its file.
- Reviews require verification and an internal verification note before publication. Policy/About draft bodies are not exposed publicly; the About page has a factual pending-information notice until approved copy is published.
- Enquiries can be searched by reference, name, mobile or interest, filtered by status/date and annotated with internal notes. **Confirmed** means an enquiry marked Confirmed by staff; it is not an imported WhatsApp conversation or an automatic booking event.
- Change the global Booking number in Business settings. Use country code and digits only, without `+`, spaces or punctuation. Visitors review/send their messages themselves.

## Structure

- `public/`: only web-accessible files, front controller, assets and processed photos.
- `app/`: reusable functions, public page renderer and administration application.
- `config/`: private configuration and credential-free example.
- `database/`: schema and unpublished sample seeds.
- `storage/`: private logs and local QA artifacts; never include it in a public upload.
- `tests/`: CLI-only unit, database/HTTP and browser checks for the isolated local database.

## Routing and data interfaces

Public GET routes: Home, About, Packages, `package/{slug}`, Services, Vehicles, Gallery, Contact, approved page slugs, `sitemap.xml`, and `robots.txt`. Package filters use category/destination/duration/trip_type query parameters. Gallery uses category ID.

POST `/enquiry` stores a validated website enquiry and redirects to Contact with a database-generated reference. POST `/package/{slug}` and `/vehicle-booking` are server fallbacks for Booking: they validate context/fields, open the encoded chat URL and do not store personal booking details. JavaScript opens the same chat directly. State-changing admin and setup requests require CSRF tokens. Admin logout is POST only.

## Maintenance and backups

Back up the database, `public/media/`, and private production configuration. Store encrypted backups away from the web root, apply a business-approved retention policy and test restoration. Check private error logs periodically; no database errors are shown publicly. Keep hosting PHP/security updates current and retest when updating animation libraries. Serve the bundled assets with caching/compression through hosting controls. Do not use PHP's development server as a public production server.

See `VERIFICATION.md` for tested scenarios/limitations and `ASSETS.md` for attribution. Customer accounts, online payments, checkout, email notifications and WhatsApp synchronization are intentionally outside this project.
## Business logo and page animations

Upload your exact logo under Admin → Business settings → Business logo. PNG (including transparency), JPEG and WebP are validated, resized and re-encoded; saving replaces the public header/footer branding. Alternatively place the exact supplied PNG at public/assets/business-logo.png. The original fallback branding remains until an actual logo file is available. Scroll reveals now run on every public page, including mobile, with one-time card staggering, focus protection and reduced-motion/dependency-failure fallbacks.

## Package preview and quick photo ordering

On Packages > Add/Edit, Live preview opens the current form in a private tab without saving or publishing. The real public package template is reused; only approved published photos are shown. Upload/save photographs and edit itinerary/options before previewing those changes. Booking is disabled in preview. Close the preview tab to return to your unsaved editor.

Under Package images, drag a photo by its Grip handle or use arrow buttons (touch/keyboard). Set cover moves an approved published photo first. Save image order applies the arrangement; the first published image becomes the cover. Draft images remain unpublished. Keyboard arrow keys also move a focused Grip handle. Without JavaScript, individual Display order fields remain available.

The ordering script loads only on admin package pages containing images (about 3.2 KB). Preview CSS loads only in private previews. Public pages download neither file; no new third-party libraries or continuous animation loops are introduced.

The additional tests/admin-enhancements.php test requires an isolated QA copy using database tdh_admin_design_qa and localhost:8099; it deliberately refuses the primary application configuration.

## About page content

The About page includes a scenic hero, introduction, travel-preference cards, planning approach, enquiry steps and enquiry CTA. Published About page title/body in Pages & policies overrides the generic introduction. Business settings controls the travel approach, team and service locations; team/locations appear only when supplied. Approved Team media is shown when available. Policies and draft About records remain unpublished. Scenic images are labelled inspiration; no experience, awards, team identities or service locations are invented. About CSS loads only on this page; the below-fold beach photo is lazy-loaded with responsive WebP sizes.

## Public page guidance and imagery

Packages, Services, Vehicles, Gallery and Contact include editorial planning guidance, scenic photographs and FAQs. Existing approved database records, filters, Booking controls and the enquiry form remain in their original flows. Guide cards do not represent published packages or confirmed vehicle availability. Gallery inspiration images are explicitly separate from approved business photography and use the same accessible image dialog. Existing optimized local photographs are reused; no remote image requests or new JavaScript library is introduced. page-guides.css loads only on these five routes. Manage real listings/photos and business contact details through admin as before.

## Vercel deployment

See [VERCEL.md](VERCEL.md) for PHP runtime routing, external TLS MySQL, persistent database sessions, Cloudinary image uploads and environment-variable setup. The Vercel static build includes assets only. Existing cPanel/local installations keep file sessions and local processed images unless configured otherwise.
