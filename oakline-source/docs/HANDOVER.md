# Oakline — installation and client handover

## 1. Review the demo

Open the public GitHub Pages demo for client design review. All five pages, navigation, FAQs, project filters, image viewer and estimate preview work without WordPress hosting. The form's download stays on the visitor's device; it never submits an estimate to the business.

For an actual CMS demonstration, use the WordPress Playground link in README. It downloads the supplied theme and plugin, creates the five pages and logs into a temporary demo installation. Initial loading can take a minute. Changes in that browser are not changes to the public GitHub demo. Export your Playground site before closing if you want to retain your edits. Do not use it for production inquiries.

## 2. Install on WordPress

Use a fresh WordPress installation with HTTPS and current maintained PHP/WordPress versions. Tested here with WordPress 7.1.4 / PHP 8.3.36 in Playground; host-specific compatibility should be checked in staging.

1. Open **Appearance → Themes → Add New → Upload Theme**. Upload `oakline.zip`, install and activate.
2. Open **Plugins → Add New → Upload Plugin**. Upload `oakline-estimates.zip`, install and activate.
3. Open **Appearance → Oakline Setup** and create the five demo pages once.
4. Open **Settings → Permalinks**, select Post name and save. Verify all navigation links.
5. Review the homepage. The Home page is automatically selected as the front page.

The setup refuses to overwrite existing pages using the same slugs. On an established site, insert the Oakline page patterns into separately created pages instead, and set the front page yourself. Keep the theme and plugin together: the theme supplies the form's browser behavior and styling.

WordPress.com must allow custom theme/plugin uploads on the selected plan. Do not purchase a plan merely to view the demo; the public demo and Playground are sufficient for testing. GitHub Pages cannot run PHP or host a WordPress database.

## 3. Ordinary content editing

- **Pages → All Pages → Edit:** click a heading, paragraph or image to change it. Use List View to select sections. Update/Save to publish the change.
- **Images:** select the Image block, choose Replace → Media Library, upload the approved image and enter useful alt text. Retain consistent crops.
- **Appearance → Editor:** edit the Header and Footer template parts and native Navigation block. Replace phone/email, hours, social links and logo here.
- **Reusable layouts:** the inserter's Patterns section contains Oakline layouts. These are reusable starting points; inserted copies are independent, not globally synchronized content.
- **SEO description:** edit the page's Oakline description field. Page titles and one main heading should describe the page clearly.
- **Project gallery:** replace stock images and their captions with approved client projects. Keep category CSS classes (`cat-kitchen`, `cat-bathroom`, `cat-flooring`, `cat-whole-home`) on cards for filtering. Add only real before/after pairs; no fabricated comparisons are included.
- **Testimonials/team:** replace the explicit placeholders with approved text and portraits. Do not leave fictional trust claims on a real business site.

The theme uses native blocks for ordinary text, images and layouts. Filter controls and the estimate form are functional components rather than ordinary text blocks.

## 4. Enable real estimate requests

1. Obtain the approved business email and production privacy-policy URL.
2. Configure the host's authenticated outbound email or an SMTP integration. Keep passwords/API keys in the host/plugin settings, never in public source control.
3. Open **Settings → Oakline Estimates**. Set the recipient, privacy-policy URL and retention period; turn off demo mode and save.
4. Replace the Contact page's demo privacy text and footer disclaimers with approved production information. Turning off the plugin's demo mode changes the form, not all editorial demo copy.
5. Submit a clearly marked test inquiry with the owner's permission. Confirm the success reference, the private inquiry entry and receipt in the actual business inbox (including spam folder).
6. Test an invalid email, missing consent and an incomplete request; they must not be accepted.

Only administrators can access the **Oakline Inquiries** inbox. It stores the supplied project details, consent time and mail status. Mail accepted by `wp_mail` does not prove inbox delivery. Failed sends retain the inquiry, schedule retries, and can be retried by an administrator. WP-Cron requires traffic or a host cron job. The plugin does not promise guaranteed delivery.

Basic spam controls include a hidden honeypot, signed short-lived challenge, minimum form time, single-use token, input checks, same-origin checks and hashed-IP/email rate limits. These do not replace host-level abuse controls for a high-traffic site. Cache/CDN rules must exclude `/wp-json/oakline/v1/*`; the challenge uses no-store responses.

## 5. Launch checklist

- Approve real branding, services, team credentials, photos, contact details, hours and testimonials. Replace all fictional/demo notices appropriately.
- Publish the approved privacy policy and set the form recipient. Confirm successful real email delivery.
- Configure HTTPS, strong unique administrator passwords and available account MFA. Keep plugins/theme/core updated. Remove unused plugins and test accounts.
- Configure host backups for database and uploads, keep an off-site copy, and perform a restore test. Backups are hosting responsibilities, not automatically provided by this package.
- Enable host page caching where supported; exclude admin/login and form endpoints. Purge caches after edits.
- Review page titles, descriptions, alt text and URL slugs. WordPress supplies `/wp-sitemap.xml` once indexing is enabled; inspect it on the production host.
- Keep demos noindex. On the approved real site only, clear **Settings → Reading → Discourage search engines**. Remove fictional content first.
- Check every page in desktop Chrome, Firefox and Safari and on actual iOS/Android devices. Local verification here used Chromium; other browsers need final client-host testing.
- Test keyboard navigation, mobile menu, focus, image dialog Escape, required form errors and zoomed text. No critical issue was observed in the tested flows; this is not an accessibility certification.

## 6. Ownership, revisions and support

The source package contains editable code, assets, build scripts and documentation. Hosting, domain, email provider and WordPress administrator accounts should belong to the client. Preserve third-party licenses when redistributing. No paid license key is required by this implementation.

The brief's seven-day schedule, three revision rounds and fourteen-day bug-fix support can be used in your client agreement; this delivery does not automatically create scheduled monitoring, a support contract or future service commitments. Record revision requests against the agreed five-page scope and keep a backup before applying them.

## 7. Troubleshooting

**Navigation 404:** save Permalinks, confirm all five pages exist and review header links. **Form still says demo:** review plugin settings and purge cache. **No email:** inspect inquiry mail status and SMTP logs; accepted does not mean delivered. **Setup refuses:** existing page slugs are protected; use manual pattern insertion rather than deleting client content. **Missing images:** confirm theme assets uploaded and replace any localhost URLs after migrating a database using a WordPress-aware migration tool. **Playground resets:** it is a temporary test environment; export before closing or use a persistent local installation.
