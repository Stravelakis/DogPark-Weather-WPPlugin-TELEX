---
checkedAgainst: v0.12repo
checkedOn: 2026-11-21
---

## Orientation

Dog Park Weather Rating is a **dynamic block** plus a settings page, all inside one WordPress plugin (`telex-dog-park-weather`, header version 0.12.0, needs WordPress 6.4 and PHP 7.4). A site owner saves parks (name, latitude, longitude, four "mitigation" flags). The block shows one park: a server-rendered shell, then a script that fetches a cached forecast from the site's own REST route and turns each hour into a 1 to 5 paw score **in the visitor's browser**.

| Piece | Where | Job |
|---|---|---|
| Plugin file | `telex-dog-park-weather.php` (about 2,400 lines) | Settings page, option getters and sanitizers, REST routes, the three weather providers, cache and cron, CSV importer, auto-translate helper |
| Block metadata | `build/block.json` | `telex/block-telex-dog-park-weather`, one attribute, `render.php` |
| Frontend markup | `build/render.php` | Looks up the park, prints a `<style>` and a `<div>` carrying all data as `data-*` attributes |
| Frontend script | `build/view.js` | Fetches the forecast, scores each hour, draws the rows |
| Editor script | `build/index.js` | Block edit screen: park picker and a live preview (carries its own copy of the scoring code) |
| Admin assets | `admin-script.js`, `admin-style.css` | Add/remove park rows, colour pickers, Greek preset, auto-translate button |
| Cleanup | `uninstall.php` | Runs on Delete: removes options, cached forecasts, cron events |
| Updater | `vendor/plugin-update-checker/` | Third-party GitHub update checker (see Updates) |

> **Watch out** The repo ships **compiled** block files only (`build/`). There is no `src/`, no `package.json` and no test suite here, and `view.js` and `index.js` are minified. You can read and patch the PHP; you cannot rebuild the JavaScript from this repo. The README still shows `npm install && npm run build` for a source tree that is not in this repository.

> **Why** One scoring function exists twice in the JavaScript (editor and frontend) because they are two separate bundles. `HANDOVER.md` says the editor and frontend "have to be kept in sync by hand"; in the live preview the paw and weather icons visibly differ from the frontend.

## Run it and try it

There is no local build. To see the real plugin you need WordPress:

1. Copy the repository files into `wp-content/plugins/telex-dog-park-weather/` (the folder name matches the text domain and `readme.txt`).
2. Activate it. Activation schedules the refresh (below) and a first fetch 30 seconds later.
3. **Settings → Dog Park Weather → Parks**: add a park, save.
4. Add the **Dog Park Weather Rating** block to a page, pick the park, publish.

The **Try it live** button at the top of this site does the same in a throwaway WordPress in your browser (WordPress Playground): it installs the `v0.12repo` tag from GitHub, adds two example parks and a page with the block. The plugin's server code runs there, so the weather lookup is real.

This docs site: `cd site && npm install && npm run dev`. `sync-docs.mjs` copies the repo-root `GUIDE.md` and `INSTALL.md` into the Guide and Install sections first.

## The block

`register_block_type( __DIR__ . '/build/' )` on `init`.

| Field | Value |
|---|---|
| name | `telex/block-telex-dog-park-weather` |
| apiVersion | 3 |
| category, icon | `widgets`, `pets` |
| attributes | `parkId` (string, default `""`), nothing else |
| supports | `html: false`, `align: ["wide","full"]` |
| rendering | `render.php` (dynamic, so there is no saved markup in post content) |
| scripts | `editorScript` index.js, `viewScript` view.js (a plain script, not a module), styles `style-index.css` |

`render.php` outputs **nothing** when `parkId` is empty, when the park id is not in the saved list (for example after the park was removed), or when the helper functions are missing. A deleted park makes the block disappear without an error.

What it prints for a valid park:

- `<style>#telex-dpw-N{ --dpw-…: … }</style>`, built from the Appearance option through a key to CSS variable map (`header_gradient_start` → `--dpw-header-start`, and so on). Values pass through `wp_strip_all_tags`.
- A `<div class="telex-dpw-block" role="region" aria-label="Dog park weather forecast for <name>">` with `data-rest-url`, `data-nonce`, and JSON in `data-park`, `data-translations`, `data-icons`, `data-appearance`, `data-weights`. `wp_json_encode` returning `false` (any malformed UTF-8) is replaced by `{}` for that attribute.
- The card shell (header, rating banner showing the Loading text, Today/Tomorrow tabs, list, "Powered by Skilitsa.com and <provider>"). The provider named is the **primary** provider setting, even if a fallback actually answered.

`view.js` runs on `DOMContentLoaded` for every `.telex-dpw-block`, so several blocks on one page work. It calls `GET <rest-url>?lat=…&lon=…` with an `X-WP-Nonce` header and renders on success. On any failure it shows the `error` translation.

## Data and settings

All state is in `wp_options`. Each settings tab has **its own** settings group.

| Option | Shape | Notes |
|---|---|---|
| `telex_dpw_parks` | list of `{id, name, lat, lon, drainage, shade, wind_shelter, covered}` | `lat`/`lon` are stored as **text**, not checked as numbers on save |
| `telex_dpw_weights` | `rain, uv, wind, temp, cloud, darkness` floats | each clamped to 0 to 5, default 1.0 |
| `telex_dpw_appearance` | 26 keys: 24 colours/backgrounds, `font_family`, `block_height` | `block_height` clamped 300 to 2000, default 780 |
| `telex_dpw_translations` | key to string overrides | 53 keys; see Translations |
| `telex_dpw_icons` | key to URL (or emoji) | 10 keys |
| `telex_dpw_api_providers` | `{primary, retries, providers{open_meteo, openweather, google_weather: {enabled, api_key}}}` | defaults: primary `open_meteo`, retries 2, only Open-Meteo enabled |
| `telex_dpw_deepl_key` | string | only for the auto-translate helper |

The page is `add_options_page(... 'manage_options', 'telex-dpw-settings')`, tabs chosen by `?tab=` (`parks`, `api_providers`, `translations`, `icons`, `appearance`, `weights`), each a full page load with its own `register_setting` group.

> **Watch out** Never merge the tabs back into one settings group. WordPress's `options.php` sets every registered option in the submitted group to empty when its fields are not in the form. With one shared group, saving Translations blanked the other tabs. `HANDOVER.md` records this as a real data-loss bug that the per-tab groups fixed. A new tab needs its own group.

Sanitizer rules worth knowing:

- Appearance keys ending in `_color` must be valid `#rgb` or `#rrggbb` (else the default is stored). Keys that do **not** end in `_color` (the gradient starts and ends, `*_bg`, `row_border_*`, `header_text`, `card_bg`, `row_bg`) are only run through `sanitize_text_field`. Only administrators can write them.
- `font_family` keeps letters, digits, whitespace, `, - ' "` and drops everything else.
- Translations run `wp_check_invalid_utf8` first; one bad byte would otherwise make `wp_json_encode` return `false` and blank every string on the block.
- Providers: `primary` must be one of the three, `retries` is capped at 10.

## REST routes

Namespace `telex-dpw/v1`, all `GET`, all `permission_callback => __return_true` (public by design: visitors are not logged in).

| Route | Returns |
|---|---|
| `/parks` | the saved parks |
| `/settings` | the settings bundle the editor preview needs (translations, icons, appearance, weights, provider settings) |
| `/weather?lat=&lon=` | the cached forecast for a park, see below |

`/weather` steps, in order:

1. `lat` and `lon` must be numeric, else `400 invalid_coords`.
2. They must match a saved park within **0.001 degrees** on both axes, else `403 unknown_location`. This stops the route acting as an open proxy to the weather provider.
3. Cache key `telex_dpw_weather_` + `md5( lat . '_' . lon )` (the cron job uses the strings as saved; the route uses the request's own strings, which match because `render.php` passes the saved values). If a transient exists, return it.
4. On a miss, do a live fetch through the provider chain, store it, return it. If every provider fails: a `WP_Error` (`api_error`, HTTP 502 when no provider returned an error object of its own).

The response is the normalised body below plus `_provider` (which provider answered).

## Weather providers

Order: the **primary** first, then the other enabled providers in the fixed order `open_meteo`, `openweather`, `google_weather`. A disabled provider is skipped even if it is primary. Each provider gets `max(1, retries)` attempts, 0.5 s apart; each HTTP call has a 10 s timeout. The first answer with a non-empty `hourly` wins. No enabled provider, or all failing, gives the 502 above.

Everything is normalised to Open-Meteo's shape: `hourly.time`, `temperature_2m`, `precipitation_probability`, `weather_code` (WMO), `cloud_cover`, `wind_speed_10m` (km/h), `uv_index`.

| | Open-Meteo (default) | OpenWeather | Google Weather |
|---|---|---|---|
| Call | `GET api.open-meteo.com/v1/forecast` | `GET pro.openweathermap.org/data/2.5/forecast/hourly`, `cnt=48`, `units=metric`, `appid` | `POST weather.googleapis.com/v1/forecast/hours?key=`, body `hours: 48`, `unitsSystem: METRIC` |
| Hourly fields | temperature, **humidity**, rain probability, weather code, cloud, wind 10 m, UV | temp, `pop` × 100, condition id mapped to WMO, clouds, wind m/s × 3.6 | temperature, rain percent, condition type mapped to WMO, cloud cover, wind (× 3.6 if m/s), `uvIndex` |
| Daily | `sunrise`, `sunset`, `timezone=auto`, `forecast_days=2` | none | none |
| Humidity | yes | no | no |
| UV | yes | always 0 | yes |
| Key | optional `apikey` parameter | required | required |

> **Watch out** Only Open-Meteo gives humidity and sunrise/sunset. For the other two the scorer assumes **50% humidity** and a fixed night of **19:00 to 06:59**. OpenWeather's UV is a constant 0, so the UV penalty never fires there. OpenWeather times are formatted with `gmdate` (UTC, no zone marker), which `view.js` then reads as local time; Google's come from `interval.startTime`. Neither of the two was run against a live key here, so treat their time handling as unconfirmed.

> **Watch out** The OpenWeather call goes to the `pro.` host's hourly-forecast product. The settings page calls the key "free or paid". Whether a free OpenWeather key works against that URL is not confirmed here.

## Cache and cron

- The forecast is stored as a transient for **15 hours** (`TELEX_DPW_WEATHER_CACHE_TTL`, defined only `if ( ! defined(...) )`, so a `wp-config.php` constant overrides it). The gap between refreshes is 12 hours, so a late cron run does not cause a visitor-facing miss.
- Custom schedule `telex_dpw_twicedaily` = every 12 hours. On activation the first event is the **next 2:00 or 14:00 in the site's timezone** (`wp_timezone()`), whichever is sooner, on hook `telex_dpw_refresh_weather`.
- Activation also queues a single event `telex_dpw_refresh_weather_now` for 30 seconds later.
- The refresh job loops over parks, skips any with an empty latitude or longitude, runs the provider chain, stores on success, and sleeps 0.25 s between parks. A failed park keeps its old transient until it expires.
- `telex_dpw_ensure_cron_scheduled` runs on **every `init`** and calls the activation routine if the recurring event is missing. That covers file-copy updates, which do not fire activation hooks.
- WP-Cron only runs when the site gets a visit, so refreshes can be late on a quiet site.
- Deactivation clears both hooks. Cached transients are **not** removed on deactivation, only on delete.

## The rating, exactly

All of this is in `view.js` (and again in `index.js`). Inputs per hour: temperature `T` in °C, humidity `RH` (default 50), wind `W` in km/h, rain probability, UV, cloud %, `isDark`, `severe`.

**Feels-like** (apparent temperature):

```text
e   = RH/100 * 6.105 * exp( 17.27*T / (237.7 + T) )
AT  = T + 0.33*e - 0.7*(W/3.6) - 4
```

**Hard override:** if `AT < 5`, `AT > 35`, or the hour is `severe`, the score is **1** and nothing else is computed. `severe` means the weather code maps to the "storm" icon, that is WMO codes **95 to 99**.

Otherwise start at **5** and subtract (each term multiplied by its weight, default 1):

| Condition | Penalty | Mitigation |
|---|---|---|
| Rain probability | `> 80`: 3 · `> 60`: 2 · `> 30`: 1 | drainage halves it |
| UV | `> 8`: 1.5 · `> 6`: 1 · `> 4`: 0.5, then × `(1 − min(cloud,100)/100 × 0.4)` | shade halves it |
| Wind (km/h) | `> 50`: 1.5 · `> 39`: 0.5 | wind shelter halves it |
| Comfort (uses `AT`) | `AT < 10` or `AT > 29`: 1 · `AT < 15` or `AT > 25`: 0.5 | covered areas halves it |
| Cloud | `AT > 26`: **bonus** of `cloud/100 × 0.5` · `AT < 12`: penalty of `cloud/100 × 0.3` | none |
| Darkness | 1 when `isDark` | none |

Final score: `Math.max(1, Math.min(5, Math.round(h)))`. JavaScript rounds halves up, so 3.5 is 4.

Worked example: 17 °C, 50% humidity, 5 km/h wind, no rain, no UV, after dark. `e` is about 9.67 and `AT` about 15.2, so the comfort and cloud terms are 0. Darkness takes 1. The score is **4**.

> **Why** Wind used to count as bad regardless of temperature, and cloud as bad regardless of whether it was shade. Basing the comfort terms on one feels-like number lets a breeze help on a hot day and hurt on a cold one (`HANDOVER.md`).

Things that are easy to miss:

- **The `covered` flag only halves the comfort term.** The settings label says "halves cloud/cold penalty", but the code applies it to the comfort row, not the cloud row.
- The wind weight text on the Weights tab says "above Beaufort 5". The code threshold is `wind > 39` km/h, where Beaufort 6 starts.
- **Darkness:** if the response has `daily.sunrise/sunset` for that date, `isDark = time < sunrise || time >= sunset`; otherwise the hour is dark when `hour >= 19 || hour < 7`.
- **Which hours show:** hours 00:00 to 05:59 are never shown. On Today, an hour that started more than one hour ago is dropped.
- **"Now":** the first row on the Today tab always gets the Now label, even if that hour is hours away (for example early morning, when the hours before 06:00 are hidden).
- **Banner:** it quotes the text for the **best** hour of the visible list (first hour wins a tie), and "TOP HOUR" is that hour. It is not an average.
- **Time zone:** "today" is `new Date()` (plus one day for Tomorrow) cut with `toISOString().slice(0,10)`, which is the **UTC date**, and hour strings without an offset are parsed in the visitor's own zone. A visitor far from the park's time zone, or near midnight, can get an unexpected "today". This is read from the code, not reproduced.
- **Icons:** WMO code to icon: 0 sun; 1, 2 partly cloudy; 3 and 45 to 48 cloudy; 51 to 67 and 80 to 82 rain; 71 to 77 and 85 to 86 snow; 95 to 99 storm; anything else partly cloudy. In the dark, sun becomes moon and partly cloudy becomes moon cloudy.
- Each row also shows rain %, wind as a Beaufort number (`B3`) and UV (`U4`).
- **Tooltip reasons** use slightly different cut-offs from the scoring: rain `> 30` (shown with the percentage), UV `> 4`, wind `> 39` (shown as `B#`), comfort `AT < 15 or > 25`, cloud only when `cloud > 75` and `AT < 12`, dark. No reasons gives "Ideal conditions". Reasons are joined with " · ". The same text is the paw row's `aria-label`, prefixed by "N out of 5 paws".

## Translations and icons

- Visitor-facing text is **not** WordPress i18n. `telex_dpw_get_default_translations()` returns 53 keys (labels, tab names, five rating lines, four mitigation names, thirteen Beaufort names, ten tooltip reasons, `aria_rating_label` with a `%d`). Saved overrides are merged over the defaults. `load_plugin_textdomain` is never called; the `__()` calls in the admin pages use the text domain `telex-dog-park-weather`.
- `telex_dpw_get_greek_preset()` returns all 53 keys in Greek; the button only fills the form fields, and nothing saves until Save Changes.
- Auto-translate (admin only, nonce and `manage_options` checked): languages `el es fr de it pt nl tr ro bg`. With a DeepL key it posts all 53 English strings in one request, trying `api-free.deepl.com` then `api.deepl.com`. Without a key, or if DeepL fails, it calls MyMemory once per string (`api.mymemory.translated.net`, 8 s timeout). "Playful" appends " 🐾" to `rating_5` and `rating_1` only.
- Icons: ten keys. For each, if `assets/icons/<name>.json|svg` exists in the plugin the default is that file's URL, **otherwise a plain emoji**. The repo ships no `assets/` folder, so this build uses emoji. `view.js` treats a value with no `/` as an emoji, a `.svg` URL as an SVG it fetches (script tags, `on…=` attributes and `javascript:` links are stripped with regexes), and anything else as Lottie JSON, which it recolours by swapping the black and `#33CCCC` colour controls.
- If any icon is Lottie, `view.js` loads **lottie-web 5.12.2 from `cdnjs.cloudflare.com`**. With the default emoji build that script is not loaded.
- `upload_mimes` is filtered to allow `.json` in the Media Library for everyone who can upload, not only on this plugin's screen.

## CSV importer

`admin_post_telex_dpw_import_parks`, needs `manage_options` and a nonce. Takes pasted CSV, or if the paste box is empty a Google Sheet URL (fetched server-side with a 20 s timeout; a body that starts with `<` is treated as a login page and ignored). Both normal `/d/<id>/edit` and "published to the web" `/d/e/<id>` links are turned into CSV export URLs.

- Headers are lower-cased. Name: `park name / area`, `park name`, `name`. Coordinates: `municipality coordinates for weather reports`, `coordinates`, `coords` (as `"lat, lon"`) **or** `latitude`/`lat` plus `longitude`/`lon`/`lng`.
- Optional mitigation columns: `good drainage`/`drainage`, `good shade`/`shade`, `wind shelter`/`shelter`/`wind-sheltered`/`wind protection`, `covered areas`/`covered`/`covered area`. True means `yes y true 1 ναι`.
- Rows with a blank name or non-numeric coordinates are skipped. Existing names (case-insensitive) are skipped, not overwritten. A UTF-8 BOM is stripped.
- Result goes back through query arguments `imported`, `skipped`, `importerror` (`empty`, `fetch_failed`, `no_columns`, `no_valid_rows`).

## Hooks and constants

The plugin registers these and defines **no filters or actions of its own** for others to use.

| Hook | Callback purpose |
|---|---|
| `init` | register the block; make sure cron is scheduled |
| `admin_init` | register the seven options |
| `admin_menu`, `admin_enqueue_scripts` | settings page and its assets (only on `settings_page_telex-dpw-settings`) |
| `plugin_action_links_<plugin>` | the Settings link next to Deactivate |
| `admin_post_telex_dpw_import_parks`, `wp_ajax_telex_dpw_translate_strings` | importer, auto-translate |
| `rest_api_init` | the three routes |
| `cron_schedules` | adds `telex_dpw_twicedaily` |
| `telex_dpw_refresh_weather`, `telex_dpw_refresh_weather_now` | the refresh job |
| `upload_mimes` | allow `.json` |
| activation / deactivation hooks | schedule / clear cron |

Constant: `TELEX_DPW_WEATHER_CACHE_TTL` (15 hours, overridable). Admin assets are versioned with the literal string `0.12.0`, not read from the header, so bump it by hand with a release.

## Updates, release, uninstall

- **Updater.** `Plugin Update Checker` (v5, MIT) is vendored and loaded if present. It is pointed at the `main` branch of `Stravelakis/Skilitsa.com-Dog-Park-Weather--WordPress-plugin`, a different address from this repository, and compares the `Version:` header there. Whether that address serves the public is not confirmed here, so do not count on in-dashboard updates; replace the files by hand.
- **Release.** The latest release is the tag `v0.12repo`. There is no build or test workflow. `.github/workflows/deploy-docs.yml` builds this docs site and deploys it on every `v*` tag.
- **Two build variants** (`HANDOVER.md`): the author's own site build carries licensed Lottie icons; the public "-repo" build omits them and falls back to emoji. This repo is the "-repo" build.
- **Uninstall** (Delete, not Deactivate): deletes the seven options (and their site-option twins), every `_transient_telex_dpw_weather_*` and `_transient_timeout_telex_dpw_weather_*` row, and both cron hooks. Pages that contain the block are not touched.
- **License.** The plugin header and the `LICENSE` file say GPL version 2 or later.

## Gotchas

> **Watch out** Page caches. The block is dynamic, so setting changes show on the next uncached load. A caching plugin in front of PHP is the usual reason a change "did not take". Purge it first.

> **Watch out** A park's latitude and longitude are free text. Save "37,98" or "N 37.98" and the block shows "Could not load weather data" because `/weather` returns `400 invalid_coords`. The importer filters non-numeric values; the manual form does not.

- Editing a park's coordinates changes the cache key, so the next view does a live fetch until the next refresh.
- A park added since the last refresh has no transient. The first visitor triggers a live fetch (up to retries × 10 s per provider, in the worst case) before seeing anything.
- The editor preview ignores your custom icons and paw colours, and only shows the Today tab (`HANDOVER.md`, "known, not-yet-fixed gap").
- The Icons tab, in a build with no icon files, fills its URL boxes with emoji, which the browser shows as `http://🐾`. Saving that tab was not tested here.
- `HANDOVER.md` still says "Current version 0.11.1", and the auto-translate handler comment and its on-screen message say "35" strings. Both are stale: the version is 0.12.0 and there are 53 strings.
