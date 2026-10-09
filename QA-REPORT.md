# Website test report — 9 October 2026

Current result: **All three reported defects are fixed. 188 automated checks passed after the fixes (170 existing checks plus 18 new regressions).** The original reproductions below are retained as the test history; they no longer describe the verified behaviour.

## Environment and isolation

- PHP 8.4.26, local XAMPP MariaDB 10.4.32, Playwright Chromium.
- QA application: `http://127.0.0.1:8099`, isolated database `tdh_admin_design_qa`.
- Current application code and public assets were copied into the QA instance.
- Temporary admin accounts, packages, photos, enquiries and settings were confined to QA. Browser fixtures were removed and QA settings restored after testing.
- External Booking navigation was intercepted. No WhatsApp messages were sent.

## Executed coverage

| Suite | Passed | Coverage |
| --- | ---: | --- |
| Unit | 24 | Number validation, Unicode/encoding, optional fields, counts/dates, escaping |
| HTTP/database integration | 54 | Authentication, CSRF, throttling, enquiries/idempotency/failure handling, content CRUD/publication, uploads, Booking without persistence |
| Admin enhancements | 21 | Private saved/unsaved preview, no-store/noindex, access control, image ordering/cover and invalid ID rejection |
| Package images | 14 | Upload/re-encoding, draft visibility, replacement cleanup, cross-package protection, malicious uploads, deletion |
| Chromium browser | 57 | Public layouts at 1440/820/390/320, navigation, filters, client Booking, keyboard gallery, mobile admin, reduced motion, WebGL fallback, offscreen/hidden-tab rendering pause |

Source PHP files under app/public/tests also passed syntax checks. The footer's Information links are present in the current template; About, Services and Gallery have working public routes.

## Original defects — resolved

### QA-01 — Medium: required Booking fields accept whitespace in the browser

**Steps:** Open a published package. Enter three spaces in Customer name and Departure city. Enter a valid future date, Adults 2 and Children 0. Click Booking.

**Expected:** Validation blocks navigation and asks for a meaningful name and departure city.

**Actual:** Browser opens the configured chat URL. The encoded message omits Name and Departure City because the JavaScript trims them after HTML validation. These fields are mandatory. The server fallback correctly validates trimmed input, so the discrepancy affects JavaScript-enabled clients.

**Location:** `public/assets/site.js`, package Booking submit handler.

**Suggested correction:** Validate required trimmed values before constructing/opening the chat; clear errors as inputs are corrected. Add a whitespace-only browser regression case.

### QA-02 — Low: clearing an invalid replacement image leaves a stale validation error

**Steps:** Edit a package with an existing photo. In its optional Replace image field, select a `.txt` file. Clear the selection. Try to save the image metadata without replacing the file.

**Expected:** Clearing an optional file field removes its previous error and allows metadata-only updates.

**Actual:** The file control retains `Choose JPEG, PNG or WebP up to 8 MB.` and `checkValidity()` remains false. The change handler returns on an empty selection before clearing custom validity.

**Location:** `public/assets/admin.js`, photo upload change handler.

**Suggested correction:** Clear custom validity before handling an empty selection. Restore the existing preview when cancelling a replacement. Keep required validation for new uploads.

### QA-03 — Medium: generic Images & gallery editor can remove a published package's last photo

**Steps:** Use a published package with one published image. Go to Images & gallery, edit that image, change its status to Draft and save. Open the package publicly.

**Expected:** Apply the same last-published-image guard used by the inline package image manager, or deliberately unpublish the package with a clear notice.

**Actual:** Package remains publicly accessible but displays the awaiting-approval image placeholder. Inline package image management prevents this action; generic image editing bypasses that constraint. The generic delete path also lacks that guard on inspection; only unpublication was reproduced in this run.

**Location:** `app/admin.php`, generic media update/delete paths, compared with `app/package-images.php`.

**Suggested correction:** Centralize the publication invariant and enforce it for generic image update, reassignment, unpublication and deletion within a transaction.

## Performance observations

- In one warm local Chromium homepage sample, DOMContentLoaded was 132 ms; 11 resource entries accounted for 1,657,651 encoded body bytes (about 1.58 MiB).
- This is an indicative localhost measurement, not a production/mobile-network speed guarantee. The page still loads its Three.js hero assets; slow-network budget testing remains necessary.
- Public Packages requested none of `admin.js`, `admin.css`, `package-order.js` or `package-preview.css`.
- Rendering stopped when offscreen or hidden and resumed when visible. Reduced-motion and unavailable-WebGL fallbacks passed.

## Remaining verification limits

Real iOS/Android devices, Safari/Firefox, throttled mobile networks, simultaneous-user load, target MySQL/MariaDB versions and production Apache/HTTPS configuration were not tested in this run. Business contacts, package facts, actual vehicle photographs, review verification and policy approval require business review. This report does not certify complete security or production readiness.

## Fix verification - 9 October 2026

QA-01: Required name/city are checked after trimming before chat navigation; editing clears custom errors. QA-02: Clearing a file selection resets custom validity and restores the existing photo preview; new uploads still require a file. QA-03: Shared transactional image guards protect published packages and vehicles in both inline and generic editors, including deletion, unpublication and ownership reassignment. Images are only removed from disk after a successful commit.

The 18 new tests in tests/qa-regressions.cjs cover whitespace and corrected Booking input, invalid upload recovery, valid preview cancellation, required uploads, generic last-photo deletion/unpublication/reassignment rejection, retained database/file/public photo state, vehicle protection and allowed changes when another published photo remains. Reran all 170 existing checks. PHP and JavaScript syntax checks passed. QA fixtures removed; business data untouched. No extra libraries or public asset requests were added.
