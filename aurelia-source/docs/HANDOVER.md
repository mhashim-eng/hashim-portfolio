# Installation, editing and launch handover

## Quick installation

1. Back up an existing site; preferably use a new local/staging WordPress installation.
2. Upload and activate installers/aurelia-theme.zip and installers/aurelia-booking-plugin.zip.
3. Appearance → Aurelia Setup → Create demo content. The importer refuses existing conflicting page slugs and will not overwrite previously seeded content.
4. Settings → Permalinks → Post name. Refresh the public homepage.
5. Leave search indexing disabled for the fictional portfolio demo.

## Everyday editing

- Pages → Edit: page copy, images, buttons, section order. Select an Image block → Replace for approved photographs; write meaningful alt text.
- Appearance → Editor: header, navigation, footer and global styles. Page patterns are reusable starting layouts.
- Posts → Add/Edit: journal articles. Published posts automatically appear in the WordPress Journal. The static demo has four bundled articles.
- Aurelia Studio: edit service names, descriptions, sample prices/durations, host names/schedules and unavailable dates. Provider-to-service assignments and global hours/timezone live in plugin defaults/config; changing those requires a developer. Default provider schedule values cannot exceed the global studio hours.
- Team page: replace role placeholders with approved names/biographies/credentials; also update provider labels in Aurelia Studio.
- Appointments: open private records, cancel with the cancellation checkbox (releases slots), resend a new management code. Do not simply trash a confirmed appointment: cancel it first.
- Leads: open a private inquiry, set New/Contacted/Consultation Scheduled/Closed and assign a staff label. Save/update.
- Calendar: view the monthly appointment schedule in Aurelia Studio.

Only WordPress administrators currently manage booking/lead records. Staff assignment is an internal label, not a separate staff login permission system. Never share administrator passwords; give access only to authorized operators.

## Activate real reservations on staging first

1. Replace the fictional identity, contacts, location, host information, sample services/prices and placeholder reviews. No genuine testimonials or before/after results are included.
2. Approve the privacy notice, booking policy and retention period. Policy copy is a starter and needs business approval.
3. Configure the approved test recipient and privacy URL in Aurelia Studio. Keep test mode on. Disable demo mode only on private staging while testing.
4. Configure authenticated WordPress mail through the chosen host/SMTP provider. Do not put credentials in the source repository. Mail functions being accepted is not proof of inbox delivery.
5. Book a future appointment using test details. Check admin record, actual delivered confirmation and private management code. Check spam folders. Test rescheduling, cancellation, reminder delivery and a rejected duplicate appointment.
6. Use a host/system scheduler to invoke WordPress cron reliably; traffic-driven WP-Cron can delay reminders. Reminders are scheduled 24 hours before appointments when the reminder time remains in the future. Same-day bookings do not receive a retroactive reminder.
7. Set the approved business recipient and switch test mode off only after delivery verification. Test mode routes messages to the configured test inbox instead of customers.
8. Configure HTTPS, backups, account protections, server updates and appropriate access logs. Exclude `/wp-json/aurelia/v1/*` from caching; challenges and availability must remain fresh.
9. Disable any public demo banner only after the business content and launch are approved. Some fictional labels are deliberately embedded in editable pages and must be replaced manually.
10. Enable indexing only for the approved real website. WordPress core exposes `/wp-sitemap.xml` when indexing is enabled. Add the real domain to any SEO configuration; the static demo's sitemap uses the portfolio URL.

## Security and data operations

The plugin validates/sanitizes inputs and escapes admin output. Private records are not exposed through the public WordPress post REST API. Random management codes are hashed in the database. Only send the raw code to the authorized customer/studio inbox. Do not place codes in public URLs.

Bookings reserve every 15-minute interval for their full duration using unique database option keys. Rescheduling obtains new intervals before releasing old ones. Cancellation releases reserved intervals. Cancellation/rescheduling is restricted inside 24 hours of the existing appointment; contact the studio for exceptions. Configuration uses America/New_York and 15-minute scheduling increments.

Default retention removes eligible past appointments and leads older than 120 days in daily batches of up to 100. Confirm your actual retention requirements before launch. Maintain a restore-tested database/files backup on the host. Hosting disk/database encryption, transport TLS and mail security are deployment responsibilities; the plugin does not encrypt individual fields at rest. This is not an electronic medical record or a certified HIPAA platform. Do not collect health histories, clinical photographs or card details through these forms.

The plugin uses WordPress mail, not a queued transactional delivery system. A failed send is recorded on the record; an administrator can resend the confirmation with a new code. A crash during a management operation can leave an `au_mutex_<post-id>` lock; an authorized developer should verify no update is running before removing that single stale option. Do not remove slot options indiscriminately.

## What is intentionally not connected

- Public GitHub demo reservations are local simulations; no real booking database or email.
- Newsletter box is a clearly labeled preview, not a mailing-list subscription integration.
- FAQ question guide uses prepared answers; it is not AI and does not diagnose.
- SMS/WhatsApp, deposits/Stripe, Google Calendar synchronization, CRM, live chat, customer accounts and clinical intake are not included integrations.
- No invented reviews, real practitioner credentials, appointment outcome guarantees or medical efficacy claims.
- Mobile site is responsive; it is not a separate iOS/Android app.

## Client handover checklist

Give the client the source ZIP, installer ZIPs, this guide and an administrator account on their chosen WordPress host. Record domain/hosting ownership, backup location, approved mail sender and provider access separately from source code. Demonstrate page editing, journal publishing, schedule changes, leads and cancellation. Retain GPL/font/photo notices when redistributing. Any paid hosting, domain or mail charges are external. Revision rounds and continuing support require a separate agreed arrangement; this source package does not schedule future maintenance automatically.
