# Verification record — 13 November 2026

The supplied WordPress runtime suite executes in WordPress Playground with the real theme/plugin installed. See tests/results.txt for assertion results. Email is mocked; this does not verify delivery through a real mail provider.

Covered: editable block pages, safe seeding, six services, dates/provider compatibility, closed dates, booking horizon, Miami summer/winter offsets, input validation, demo protection, private records, interval overlap, duplicate submission, atomic lock conflict, reminder scheduling, test mail routing, private code validation, rescheduling/releasing slots, cancellation, leads and unauthorized access.

Browser review: Chromium desktop homepage, four-step booking through successful fictional reservation; mobile 375px content viewport and 768px tablet; calendar overflow found and fixed. Responsive mobile calendar fits without horizontal overflow after correction.

Run the PHP integration suite with @wp-playground/cli:

```
wp-playground-cli run-blueprint --mount=./wordpress/aurelia:/wordpress/wp-content/themes/aurelia --mount=./wordpress/aurelia-booking:/wordpress/wp-content/plugins/aurelia-booking --mount=./tests:/aurelia-tests --blueprint=./tests/blueprint.json
```

This is a useful functional verification, not a penetration test, load test, formal accessibility audit or proof of all browsers. Production mail delivery, host cron, backups/restores, host TLS, real business content and iOS/Safari/Android device checks must be verified on the chosen deployment. No real customer data was used.
