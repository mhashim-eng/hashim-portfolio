# Oakline Renovations

A five-page renovation business website by Muhammad Hashim. Oakline is a fictional company; stock photographs and missing business materials are explicitly identified.

## Start here

- **Shareable visual demo:** https://mhashim-eng.github.io/hashim-portfolio/oakline/
- **Optional WordPress Playground link (cloud-browser initialization was blocked; see validation notes):** https://playground.wordpress.net/?blueprint-url=https%3A%2F%2Fmhashim-eng.github.io%2Fhashim-portfolio%2Foakline%2Fblueprint.json
- **Install on your own WordPress:** use `dist/downloads/oakline.zip` and `dist/downloads/oakline-estimates.zip`.
- See `docs/HANDOVER.md` for setup, editing, form delivery and launch instructions.

The GitHub Pages demo is static: its estimate form previews and downloads a project brief locally. It does not email or store inquiries. The supplied WordPress plugin implements the server-side inquiry workflow. WordPress Playground is temporary browser-based testing, not production hosting or a persistent inbox. Never put real customer data in the demo.

## What is included

Home, About, Services, Projects and Contact; four service categories; premium responsive styling; local DM Serif Display and Inter fonts; optimized WebP photography; accessible mobile menu; gallery category filters and image dialog; FAQs; service-prefilled estimate form; downloadable demo brief; Gutenberg block content and reusable page patterns; native WordPress navigation; private administrator inquiry inbox; email retry handling; consent and retention settings; basic anti-spam controls; page descriptions; and installation/testing documentation.

No paid theme, page-builder subscription, WooCommerce installation, external analytics or customer tracking is required. E-commerce was not part of this renovation-site brief.

## Source structure

| Directory | Purpose |
| --- | --- |
| `wordpress/oakline` | Editable WordPress block theme, templates, patterns and setup |
| `wordpress/oakline-estimates` | Inquiry storage, validation, mail and admin controls |
| `dist` | Static five-page demo and installable packages |
| `src/build.py` | Rebuild demo pages and WordPress block content |
| `tests` | PHP integration checks and Playground blueprint |
| `docs` | Editing, deployment, acceptance and validation records |
| `licenses` | Third-party license texts |

Languages: PHP, HTML, CSS, JavaScript, JSON; Python is only a development-time content builder. WordPress supplies the CMS and database APIs. No React or proprietary builder is required. Production WordPress uses the database provided by your host; local automated tests used Playground's SQLite integration.

## Development

Python 3: `python3 src/build.py` rebuilds demo HTML and seed content, copying `dist/assets` into the theme. Edit shared CSS/JS in `dist/assets` before rebuilding. The builder does not update an already-created WordPress database; edit published WordPress content in Gutenberg instead. Use `python3 src/package.py` to regenerate installation ZIPs and the public Playground blueprint.

For a local static preview, run `python3 -m http.server 8080 --directory dist`, then open http://localhost:8080. This is only the visual demo. For an actual local WordPress install, use WordPress Studio or the Playground link above, or install on your own WordPress host.

## Handover status

Theme and plugin source are supplied without activation keys or vendor lock-in. A real launch still needs the client's approved identity, contact details, project photographs, testimonials, privacy policy, hosting and approved email recipient. Real inbox delivery, hosting backups and production caching cannot be verified before that environment is configured. No performance score, search ranking, legal compliance certification or universal browser compatibility is claimed.

Original theme/plugin code is GPL-2.0-or-later. Fonts and photos retain their own licenses, documented in `THIRD_PARTY_NOTICES.md`. You can modify and commercially deliver the package while preserving applicable notices and source obligations. Third-party assets are not exclusively owned by you. No client credentials or paid licenses are bundled.
