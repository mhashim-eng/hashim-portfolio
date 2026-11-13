# Aurelia Aesthetics & Wellness

A complete fictional studio website and installable WordPress booking system, created for Muhammad Hashim. Premium cream/champagne styling, responsive pages, six services, four-step reservations, provider availability, private appointment/lead management, and editable journal content.

## Start here

**View the public demo:** https://mhashim-eng.github.io/hashim-portfolio/aurelia/

The public GitHub Pages version is a browser-only simulation. It does not send email, accept payments, or reserve real studio appointments. Fictional appointment references/times are saved in your browser; names, email addresses and phone numbers are not persisted by the demo. Clear sample reservations under Booking → Manage an appointment.

**Install the real WordPress version:** extract the full delivery ZIP. Inside `installers`, upload `aurelia-theme.zip` through Appearance → Themes → Add New → Upload. Activate it. Upload `aurelia-booking-plugin.zip` through Plugins → Add New → Upload and activate. On a fresh WordPress site, open Appearance → Aurelia Setup → Create demo content. Set Settings → Permalinks → Post name.

No coding is required to edit ordinary page text, photographs, prices or schedules. Use a WordPress installation that supports custom theme/plugin uploads. A static GitHub Pages host cannot execute the PHP booking backend.

## Included

- 12 core pages: Home, About, Services, Booking, Team, Gallery, Reviews, Journal, Contact, FAQ, Policies and Thank You.
- Two reusable detail pages: Service Details and Journal Article.
- Six sample services, two fictional studio hosts, four editable sample journal posts.
- Service/category filtering, responsive navigation, gallery lightbox, FAQ accordions and a preset question guide.
- Date/time/provider booking with service duration, closed dates, lead time, 90-day booking horizon and Miami daylight-saving handling.
- Database-backed interval locks to reject overlapping bookings, input validation, honeypot, rate limiting and signed one-use submission challenges.
- Private WordPress appointment records, monthly appointment calendar, lead status and staff assignment.
- Email confirmation/reminder/cancellation/rescheduling through WordPress mail; private management code for customer changes; downloadable calendar note.
- Pay at studio. No payment gateway or paid booking plugin is required.
- Self-hosted fonts, optimized WebP photographs, title/meta descriptions and WordPress sitemap support. Demo indexing is disabled deliberately.

## Technology and source paths

| Path | Purpose |
|---|---|
| `wordpress/aurelia/` | WordPress block theme: PHP, Gutenberg HTML, theme.json, CSS, JavaScript |
| `wordpress/aurelia-booking/` | Custom PHP plugin, REST routes, WordPress database records and scheduling |
| `dist/` | Portable static public demo: HTML, CSS, vanilla JavaScript |
| `src/build.py` | Python generator for the demo and editable block content |
| `src/assets.py` | Photo preparation script; existing optimized assets are included |
| `tests/` | WordPress runtime checks and recorded results |
| `docs/` | Editing, setup, security, testing and launch handover |
| `licenses/` | Third-party license texts |

There is no React, paid page builder, external AI API or Node runtime needed for ordinary use. WordPress supplies PHP/MySQL, its block editor, user authentication and cron. The static demo only needs a web server.

## Local testing without deployment

For the static demo, install Python 3 and run `python -m http.server 8000 --directory dist` from this folder. Open http://localhost:8000. Use `py` instead of `python` on Windows if necessary. The supplied `START-DEMO.bat` opens this server. Keep the terminal open; Ctrl+C stops it. Opening index.html directly with file:// will not load JSON correctly.

For the real backend, use a fresh local WordPress environment with PHP 8.0+ and install the two ZIPs above. No Render deployment is needed. Keep demo/test modes enabled while exploring. Use a mail capture service for local testing; actual email delivery requires an approved mail configuration.

To regenerate the static demo/content: `python src/build.py`. The generator overwrites generated HTML and theme content files. Installed WordPress page edits live in its database and are not exported by this script; back up both database and files.

## Production readiness

Read `docs/HANDOVER.md` before accepting real bookings. Hosting, business identity, approved privacy/policy text, approved inbox, mail credentials, actual provider information and live delivery tests remain deployment-specific. All demo business details are intentionally fictional. Do not publish stock images as real clinic results or invent reviews/credentials.

## Ownership and redistribution

Custom theme/plugin source is supplied in full under GPL-2.0-or-later. You may modify, deploy and hand the code to clients subject to that license. Third-party fonts and photographs retain their own licenses; see THIRD_PARTY_NOTICES.md. This does not grant exclusive ownership of WordPress, fonts or stock photography. Hosting/domain accounts and future service fees belong to their respective account owners.
