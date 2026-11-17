---
# Not reviewed yet: owner ticks templates/docs/REVIEW-CHECKLIST.md, then adds
# checkedAgainst: v0.12.0 and checkedOn: YYYY-MM-DD here.
---

## Dog Park Weather Rating: developer view

A dynamic Gutenberg block plugin. Version `0.12.0`. Requires WordPress 6.4+ and PHP 7.4+. Licence: GPLv2 or later.

- **Dynamic block.** `render.php` runs on every page load, so settings changes apply without re-saving posts. If an update looks like it did not take, purge any page cache in front of PHP first.
- **Two implementations.** The editor preview is React (`edit.js`); the frontend is PHP plus vanilla JS (`render.php`, `view.js`). They are kept in sync by hand, so check any visual change in both.
- **Settings.** Six tabs: Parks, API Providers, Translations, Icons, Appearance, Weights. Each tab saves through its own settings group (for example `telex_dpw_parks_settings`). Never merge them into one group: the tabs are full-page reloads, and a shared group nulls out every other tab's option on save.
- **Weather data.** Open-Meteo by default (no key). Optional Google Weather and OpenWeather. Weather is refreshed twice a day through WP-Cron for every registered park and served from cache.
- **Rating model.** Based on a "feels like" temperature (air temperature, humidity and wind, standard apparent-temperature formula). Wind alone is penalised only from Beaufort 6 and up; UV is dampened under cloud; cloud cover helps on hot days and hurts when cold. Any hour below 5 C, above 35 C, or with a severe or thunderstorm forecast is always 1 paw. Darkness comes from real sunrise and sunset per park.
- **Mitigations.** Per park: good drainage, shade, wind shelter or covered areas halve the penalty for that one condition.
- **Icons.** Recolourable Lottie JSON for the paw rating and each weather condition. The public "-repo" build ships without the licensed icon files and falls back to emoji. It also includes a vendored MIT-licensed Plugin Update Checker that reads GitHub.
- **What this repo holds:** the finished plugin: `telex-dog-park-weather.php`, `build/` (compiled block, `render.php`, `view.js`, CSS), `admin-script.js`, `admin-style.css`, `uninstall.php` and the vendored update checker in `vendor/`. The block source (`src/`) and `package.json` are not in this repository, so the README's `npm` build commands cannot be run from it.
- **Uninstall:** `uninstall.php` removes options, per-park transients and cron events.
