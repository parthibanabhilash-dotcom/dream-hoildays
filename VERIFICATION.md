# Verification record

Verified locally on 8 October 2026. No WhatsApp messages were sent; external chat navigation was intercepted in browser tests and HTTP redirects were not followed.

## Automated results

- **24 unit checks passed**: configured-number validation, Unicode/special characters, encoded line breaks, direct-card message fields, optional omission, zero children, required fields, dates/counts, email validation and output escaping.
- **54 HTTP/database checks passed**: all public pages, draft protection and sitemap visibility, protected admin routes, first-admin setup and lockout, login/logout/password change/throttling, CSRF, settings validation, package creation/publication/unpublication, photo processing, Unicode Booking redirects, no Booking persistence, enquiry insertion/reference/success, duplicates, simulated database-storage failure without success, escaped customer input, status/notes, unverified review denial, script-upload rejection, vehicle publication and no-JavaScript booking, spam denial, and missing-number behaviour.
- **57 Chromium browser checks passed**: async 3D rendering, hidden-tab/offscreen pause and resume, section reveals, keyboard marker alternatives, horizontal overflow across 1440/820/390/320 widths and eight public page types, mobile navigation/Escape, current-package sticky Booking, client validation and messages, service/vehicle context, combined filters, gallery keyboard dialog/focus restoration, mobile admin login, reduced motion, WebGL failure, no-JavaScript server-rendered content, and no uncaught JavaScript errors.
- **4 additional gallery viewport checks passed** after visual review corrected fixed image height: image proportions and no overflow at 1440/820/390/320 widths.
- All source PHP files passed syntax checks with PHP 8.4.26; main JavaScript passed Node syntax checking. Desktop/mobile draft-ready screenshots were rendered and visually reviewed.

Final local state: 12 draft packages, 7 draft services, 4 draft editable pages, 3 gallery categories. Zero published packages, invented vehicles/reviews, test administrators, saved test enquiries, internal test notes, or temporary media records. Business settings are blank.

## Environment and limits

- PHP 8.4.26, GD WebP, PDO MySQL, Chromium (Playwright-managed).
- Existing local XAMPP database: MariaDB **10.4.32**. SQL/CRUD tests passed there. Deployment targets remain MySQL 8.0+/MariaDB 10.6+; those server versions and the hosting provider's Apache configuration were not executed locally.
- Desktop/tablet/mobile were emulated in Chromium. Safari/Firefox and physical-device testing were not performed.
- Hidden-tab behaviour was exercised through a controlled visibility-state test; offscreen behaviour used real browser scrolling.
- Live business contacts, actual vehicle photos, published company facts, policies and prices are unavailable, so final delivery keeps them blank/draft. Temporary scenic photos attached to vehicle browser fixtures were test data only and were removed.
- Production SSL, real contact number, maps location and production rewrite/private-directory permissions require hosting acceptance checks before launch. No deployment to an external host was performed.

## Re-run checks

`php tests/unit.php`

`php tests/integration.php` requires the isolated `dream_holidays_local_test` database, local private configuration, no permanent administrator, and a PHP server bound to `127.0.0.1:8098`. The test script guards the database name/base URL and removes its fixtures. It temporarily simulates an insert failure with a trigger only on this isolated database.

For browser checks, install Playwright in a separate developer tools folder (not production), set `NODE_PATH` to its node_modules folder and `PLAYWRIGHT_BROWSERS_PATH` to its browser folder, then:

1. Run `php tests/browser-fixtures.php`.
2. Run `node tests/browser.cjs`.
3. Always run `php tests/browser-fixtures.php clean` afterward, including if browser checks fail. Fixtures temporarily include an admin, sample published content and a test Booking number; never run them on a business or production database.

`tests/bootstrap-local.php` is a CLI-only convenience for this XAMPP test environment. It creates its own database/user and private local configuration, and must never be run on a hosting account.
## Transport animation update — 9 October 2026

Added original bus/train/flight 3D models, gentle entrance/floating motion and SVG travel icon badges. Targeted Chromium checks passed at 1440, 820, 390 and 320 pixels: rendered transport scene, visible package action, no horizontal overflow and no JavaScript errors. Reduced-motion and unavailable-WebGL checks retain all three static icon badges and the static hero. Desktop hero rendering was visually inspected; model positions were adjusted to prevent the label covering the train. PHP/JavaScript syntax checks passed. No database or Booking behaviour changed.

## Logo integration and all-page scroll update — 9 October 2026

54 backend checks passed after adding secure logo upload. Targeted browser checks passed for nine public page types at 1440 and 390 pixels, confirming scroll targets reveal and layouts do not overflow. Additional checks passed for focused form visibility, header/footer logo upload and image decoding, reduced motion, and a blocked GSAP dependency. Temporary policy, logo, accounts and package fixtures were removed. The exact logo shown in chat has no accessible image file in the local attachment folders; it has not been substituted with a generated recreation. Its installation remains pending receipt/upload of the original file.

## Modern admin redesign — 9 October 2026

Redesigned login/setup presentation, grouped sidebar with active indicators, mobile navigation, dashboard welcome/quick actions, real database statistics, recent enquiries, status badges, forms and tables. PHP syntax and 54 backend regression checks passed on a separate 	dh_admin_design_qa database and localhost port 8099. Six admin screens were verified at 1440, 820, 390 and 320 pixels for visible headings, correct active navigation, no horizontal overflow and no JavaScript errors; mobile Menu/Escape behaviour passed at 390/320. Desktop dashboard and login screenshots were visually reviewed. Production/local business accounts were not used for these QA login checks. QA-only enquiries and temporary administrator were isolated from the main application.

## Inline package images - 9 October 2026

Added package-scoped upload, preview, replacement, ordering, publication and deletion directly on the package edit page. Existing records and database schema are preserved. 14 targeted HTTP/database checks passed on the isolated QA database: upload/re-encoding, draft visibility, approved image/order updates, replacement cleanup, cross-package access rejection, CSRF, disguised script rejection and deletion. All 54 backend regression checks and 24 unit checks passed. Chromium verified 1440/820/390/320 layouts with no horizontal overflow and a working local file preview under CSP. Main business data/accounts were not used for tests.

## Private live preview and quick photo ordering - 9 October 2026

Added authenticated saved/unsaved package preview using the public template with no-store/noindex headers, escaped form values and disabled Booking. Added native drag ordering, mobile/keyboard arrows, Set cover, explicit save and atomic package-scoped order validation. 21 targeted enhancement checks passed (authentication, CSRF, preview privacy/non-persistence, draft isolation, valid ordering, cover changes, duplicate/foreign/stale ID rejection). Existing 54 backend, 24 unit and 14 image checks passed. Chromium verified native dragging, keyboard focus/movement, cover persistence, live unsaved preview and disabled Booking, layouts at 1440/820/390/320, no JavaScript errors, and no admin enhancement requests on the public packages page. New ordering JS is 3154 bytes; preview CSS is 372 bytes. No production hosting speed guarantee is implied by local checks; these additions introduce no public asset requests. Fixtures were confined to the QA database and removed.

## QA defect fixes - 9 October 2026

Resolved whitespace-only client Booking inputs, stale upload validation after clearing, and published listings losing their last approved photo via generic media edits/deletes/reassignment. Shared image guards execute inside transactions and preserve files on rollback. 188 checks passed: 24 unit, 54 HTTP/database, 21 preview/order, 14 package image, 57 browser and 18 new bug regressions. Source PHP/JavaScript syntax passed. New regression runner tests/qa-regressions.cjs is for an isolated QA copy at localhost:8099 with browser-fixtures.php loaded; run it after browser.cjs and clean fixtures afterwards. Main business data was not changed.

## About page expansion - 9 October 2026

Added fuller generic travel-planning content, existing licensed mountain photograph and new optimized beach photography, icon cards, planning steps and enquiry CTA. Existing approved About body/settings remain editable and respected. About-only CSS avoids extra styling downloads on other pages. Targeted Chromium checks passed at 1440/820/390/320: no overflow, headings present and beach images decode; enquiry CTA, reduced motion, no-JavaScript content and absence of About CSS on home passed. No uncaught browser JavaScript errors. Desktop full-page design visually inspected; PHP syntax passed. No business records or publication statuses changed.

## Five public page content expansion - 9 October 2026

Added reusable editorial guidance and photographs to Packages, Services, Vehicles, Gallery and Contact; added planning cards, FAQs and enquiry links. Reused documented licensed inspiration photographs. No publication status, business contact or booking availability data changed. All 54 HTTP/database regression checks passed on isolated QA. Targeted Chromium checks passed for all five pages at 1440/820/390/320 (20 layouts), image decoding, guidance cards, FAQ interaction, gallery keyboard dialog/focus, Contact interest prefill, reduced motion visibility and no JavaScript errors. Home does not request page-guides.css. Vehicle desktop and other page screenshots were captured; vehicle desktop visually inspected. PHP syntax checks passed. Main application testing was read-only; QA fixtures cleaned by the regression suite.

## Vercel adaptation - 9 October 2026

Added pinned PHP community runtime, API entrypoint, asset-only static build, environment configuration, TLS MySQL options, database-backed sessions and signed Cloudinary media adapter. Existing local/cPanel media remains supported. Vercel uploads capped at 3 MB for multipart payload headroom. 54 HTTP/database and 21 preview/order checks passed against isolated QA with the new database session handler; 24 unit and 12 serverless configuration/media checks passed. Static build contains 22 asset files and no PHP source. PHP/JS syntax passed. External provider TLS, actual Cloudinary uploads/deletes and hosted runtime execution still require provisioned environment variables; those live operations have not been represented as tested.

Live Vercel verification: GitHub reports successful deployment of commit 35efec7. https://dream-hoildays.vercel.app returns HTML setup-in-progress (HTTP 503), not downloadable PHP. Private config URL returns 404; CSS and supplied logo assets return 200 with their expected content types. Runtime routing is verified live. External database/schema/TLS and Cloudinary credentials are not yet configured, so full admin/enquiry/upload operations remain pending those accounts.
