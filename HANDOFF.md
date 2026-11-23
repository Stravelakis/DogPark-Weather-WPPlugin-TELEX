# HANDOFF

Where this repo stands, for whoever picks it up next. (`HANDOVER.md` is the plugin's older design note; its "current version 0.11.1" line is stale, the plugin header says 0.12.0.)

## 2026-11-21: docs site moved to the v2 docs theme

**What changed (docs only; no plugin code touched)**

- `site/` now uses the master docs theme v2 (layout, components, scripts, styles, `sync-docs.mjs`, `package.json` with the three `@fontsource-variable` fonts).
- Rewrote `site/src/docs/dev.md`, `plain.md`, `eli5.md` and `site/glossary.yml` from the code at tag `v0.12repo`. Review stamp: `checkedAgainst: v0.12repo`, `checkedOn: 2026-11-21`.
- New front page, hero buttons, level cards. "Try it live" opens WordPress Playground with the `v0.12repo` tag and two example parks (a real WordPress, real plugin, not a mock-up).
- Added real screenshots in `site/public/screens/`, taken from that Playground run (example park "Riverside Dog Park", example coordinates).

**Nothing unreleased is documented.** Main equals the `v0.12repo` code plus the docs commit.

**Not confirmed / not tested**

- Google Weather and OpenWeather paths were read, not run (no keys).
- Visitor time-zone behaviour (below) was read from code, not reproduced.
- Saving the Icons tab in this emoji-only build was not tested.

## Plugin problems found (NOT fixed, by instruction)

1. **API keys readable by anyone (security).** `GET /wp-json/telex-dpw/v1/settings` is public (`permission_callback => __return_true`) and returns `api_providers`, which contains the saved provider API keys. Fix: remove the keys from that response (the editor preview only needs the chosen provider) or require `edit_posts`. The docs do not mention this route's contents.
2. **Update checker points elsewhere.** `telex-dog-park-weather.php` builds the checker for `Stravelakis/Skilitsa.com-Dog-Park-Weather--WordPress-plugin`, which resolves to a private repo (`DnD-World/mitSkilitsa.com-Dog-Park-Weather--WordPress-plugin`), not this repo. So `INSTALL.md`'s "WordPress can offer new versions from this repository" is probably wrong. The docs tell people to replace files by hand.
3. **Docs say "corner radius" on the Appearance tab** (`GUIDE.md`, `README.md`). The code has no corner-radius setting; Appearance has 24 colour/background fields, Font Family and Block Height.
4. **`README.md` build steps** (`npm install && npm run build`, `plugin-zip`) refer to a source tree that is not in this repo (no `package.json`, no `src/`).
5. **"Covered Areas (halves cloud/cold penalty)"** label: in `view.js`/`index.js` the covered flag halves only the comfort (temperature) penalty, not the cloud term.
6. **Weights tab says wind counts "above Beaufort 5"**; code threshold is `wind > 39` km/h (Beaufort 6 starts at 39).
7. **OpenWeather times** are formatted with `gmdate()` (UTC, no zone marker); `view.js` reads them as local time. Suspected wrong hours for non-UTC parks. Not run.
8. **Visitor time zone / "today".** `view.js` takes "today" from `toISOString().slice(0,10)` (the UTC date) and parses zone-less hour strings in the visitor's zone. Suspected wrong "Now" row for visitors far from the park or near midnight. Not run.
9. **First row of Today is always labelled "Now"** even if it is hours away (early morning).
10. **Icons tab in the emoji-only build** shows `http://🐾` in the URL boxes; saving may store junk. Not tested.
11. **Park latitude/longitude are not validated on save** (only the importer checks they are numbers). Bad text gives "Could not load weather data".
12. **Only appearance keys ending `_color` are hex-checked**; gradient, `*_bg`, `row_border_*` etc. are plain text put into a `<style>` (admin-only, low risk).
13. **`upload_mimes` allows `.json` site-wide** for anyone who can upload.
14. **Licence mismatch with standards.** Header and `LICENSE` are GPL v2 ("or later" in the header); standards ask GPL-3.0-or-later for WordPress plugins. GitHub shows the licence as "Other".
15. **Stale comments/notes:** "35 strings" (there are 53), `HANDOVER.md` version 0.11.1, admin assets versioned with the literal `0.12.0`.
16. **Duplicate heading ids on the docs page** come from `GUIDE.md` and `INSTALL.md` both having an "Uninstall" heading (and INSTALL's "Install" matching the section id). Source files not edited.
17. OpenWeather call uses the `pro.` hourly-forecast host; the settings text says a free key works. Unknown.
