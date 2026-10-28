# Route Lab

A static, browser-based companion to the existing Wayline and FuelRoute Django projects. Hosted under `/hashim-portfolio/route-lab/` on GitHub Pages; no Python runtime or secret API keys are placed in the public site.

## Modes

- Route & weather: ten country presets, country-filtered city search, actual OSRM road geometry, and Open-Meteo current-weather snapshots at origin, route midpoint and destination. This does not implement Wayline's weather-risk comparison or arrival-time forecast. The original Wayline application remains linked for those features.
- USA fuel: starts with an already-owned 50-gallon tank at 10 MPG. Uses the supplied historical USD prices and 1,026 verified station coordinates. Candidate screening is a two-mile projected corridor; fuel consumption uses actual OSRM road legs through the candidate stations. At most three routing calls: base, candidates, optional refinement. A missing feasible station sequence results in an error, never invented stations or prices.

Country presets: USA, Canada, Germany, Spain, Pakistan, UK, France, Australia, UAE and Saudi Arabia. Provider road coverage and service availability determine whether a particular trip works. No international fuel-price coverage is claimed.

## Operation

Serve this repository through any static HTTP server, e.g. `python -m http.server 8000`. Open `/route-lab/`. ES modules require HTTP, not a `file://` URL. Relative paths support GitHub project Pages.

Run pure-function tests with `node --test tests/route-core.test.mjs` from the repository root. The tests include an independent exhaustive dynamic program against 120 generated fixed-route fuel cases.

Requests time out after 30 seconds and can be cancelled. In-memory caches retain routes for one hour, weather for ten minutes and location/snapshot data for a day; cache size is bounded. Caches are discarded on reload. The browser sends city queries and route coordinates to the selected providers. No contact form or analytics storage has been added; Email and WhatsApp links open the user's own messaging app.

## Assumptions and limits

- This portfolio demo does not provide navigation, truck clearance checks, live traffic, toll costs or a travel-safety determination.
- Current weather is not an ETA forecast. Displayed weather times use each checkpoint's timezone.
- Car routing can snap input locations onto nearby roads. Station coverage is partial; raw supplier prices may no longer represent current retail prices.
- The fuel algorithm minimizes purchase cost for a fixed evaluated sequence. Candidate reduction and route selection are heuristics, not a global road-network optimum.
- Browser arithmetic uses IEEE-754 numbers; displayed USD totals are rounded to two decimals after summing purchases. The original Python backend uses Decimal and remains the assessment implementation.
- Public providers and external Leaflet assets may fail or be rate limited. A weather failure leaves the valid route available with an explicit message. These public endpoints are for this demo; commercial production hosting needs a provider/licensing review and suitable service plan.
- Contact information was explicitly supplied by the portfolio owner for public display. Secrets and backend environment values are not included.

## Sources

- Station snapshot and USA boundary: original FuelRoute assessment branch, `fuelroute/data/stations.json` and `fuelroute/data/usa.geojson`. Station coordinate provenance is preserved per record. Prices are from the supplied assessment CSV. USA boundary originates from Natural Earth via geo-countries (public-domain geographic data).
- Roads: OpenStreetMap contributors; routes via OSRM. Background tiles: USGS The National Map / Natural Earth. Global reference map tiles use zoom levels up to 8, overzoomed at closer scales; the road geometry remains detailed. Attribution remains on the map.
- Weather and city lookup: Open-Meteo; source linked in the application.
- Leaflet 1.9.4, BSD-2-Clause; CDN stylesheet and script.

The existing assessment repositories and deployed Wayline service are not modified by this portfolio integration.
