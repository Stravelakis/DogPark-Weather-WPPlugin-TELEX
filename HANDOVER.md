# Dog Park Weather Rating — Handover

Current version: **0.11.1**. This doc describes where the plugin stands and
why it's built the way it is — not a session-by-session changelog (see
`readme.txt` for that).

## What it is

A Gutenberg block for skilitsa.com: pick a park, show an hourly weather
forecast turned into a 1–5 paw rating, in Greek, with a playful tone. Fully
translatable, fully re-themeable, no required API key.

## Key decisions and why

**Dynamic block, not static.** `render.php` runs fresh on every page load
(no cached HTML baked into post content), so settings changes show up
immediately without needing to re-save posts. Trade-off: it means WordPress
caching plugins (LiteSpeed Cache etc.) sitting *in front of* PHP are the
only thing that can make an update look like it "didn't take" — worth a
cache purge as a first troubleshooting step, always.

**One settings group per tab, not one shared group.** Originally all six
tabs (Parks, API Providers, Translations, Icons, Appearance, Weights) saved
through one WordPress settings group. Because the tabs are full-page reloads
(not JS show/hide) and only the active tab's fields exist in the submitted
form, WordPress's own save mechanism was nulling out every *other* tab's
option on every save — a real, silent data-loss bug, now fixed by giving
each tab (`telex_dpw_parks_settings`, `telex_dpw_translations_settings`,
etc.) its own group. This is the single most important thing to understand
about the settings page's structure if touching it again: **never
consolidate the tabs back into one shared group**, and any new tab needs its
own group too.

**Editor preview (`edit.js`) and frontend (`render.php` + `view.js`) are two
separate implementations.** One's React, one's PHP + vanilla JS, for a
dynamic block. They have to be kept in sync by hand — there's no shared
source of truth between them. This has bitten us more than once (icons
removed from the frontend stayed in the editor preview for several sessions
before anyone caught it). Any future visual change needs to be checked in
**both** places.

**Custom icons via recolorable Lottie JSON**, not static images. Each icon
(weather conditions, paw ratings) is a Lottie file with two Color Control
layers (an outline color and an accent color) that get recolored at render
time from the Appearance settings. Bundled defaults live in `assets/icons/`.

**Rating is based on "feels like," not independent per-condition penalties.**
The original model summed independent penalties (rain + UV + wind + temp +
cloud + darkness), which meant wind was always treated as bad regardless of
temperature, and cloud cover was always treated as bad regardless of
whether it was providing welcome shade. It's now built around one derived
number — apparent ("feels like") temperature, combining air temperature,
humidity, and wind via the standard BOM formula — so wind and cloud cover
can correctly help *or* hurt the score depending on context (a moderate
breeze in 34°C heat lowers feels-like into a comfortable range and raises
the rating; the same breeze at 2°C lowers it further and correctly hurts
it). The safety override (forced 1-paw for dangerous conditions) uses
feels-like too, not raw temperature, so humid heat and wind chill both get
caught correctly. Humidity is fetched from Open-Meteo but currently only
feeds this calculation — not displayed anywhere yet, despite a `feels_like`
translation string sitting ready for it. Both `edit.js` and `view.js` build
their tooltip text from the same shared `getRatingReasons()` in
`rating-engine.js`, so the explanation can't drift out of sync with the
actual scoring the way the editor/frontend markup once did.

**Translations as a flat key→string option**, not WordPress i18n `.po`/`.mo`
files. Every visible string on the frontend — including the hover tooltip
explaining *why* an hour got its rating — has a key in
`telex_dpw_get_default_translations()`, is overridable on the Translations
tab, and has a hand-written Greek preset (`telex_dpw_get_greek_preset()`)
one click away. There's also an "auto-translate" tool (DeepL if a key's
provided, MyMemory free-tier as fallback) — treat that as a rough first pass
to hand-edit, not a finished translation.

**CSV park importer maps flexible header names**, not a fixed template —
because it's built against Ak's real "Dog parks - Greece" Google Sheet,
whose column names have already changed once. It recognizes several
plausible header variants for name/coordinates and for the four optional
mitigation columns (drainage/shade/wind shelter/covered), and skips rows
with unusable data rather than importing garbage. Existing parks are matched
by name and never duplicated or overwritten.

**Zero-cost by default.** Open-Meteo needs no API key; weather is cached via
WP-Cron (twice daily) so visitors never trigger a live API call. Google
Weather / OpenWeather are available as opt-in alternatives if a key is
provided on the API Providers tab.

**Two build variants, one codebase.** The bundled weather/paw icons come
from Ak's Flaticon Premium subscription, whose terms (checked directly)
prohibit redistributing the raw files in a downloadable product - fine for
skilitsa.com's own use, not fine for a public plugin. Rather than fork the
code, `telex_dpw_get_icons()` checks whether the licensed files actually
exist in `assets/icons/` and falls back to plain emoji (🐾 ☀️ etc., no
license needed) when they don't. The **site build** ships with the
licensed files and no updater. The **"-repo" build** ships without those
files (so it automatically uses emoji) and includes a vendored GitHub-based
update checker. This split is intentional in both directions: the site
build never auto-updates from the public repo, because doing so would
silently strip out the paid icons Ak wants on his own site. **The two
builds also carry different licenses, deliberately**: the "-repo" build is
GPLv2 (nothing in it is restricted - safe to fork, share, redistribute).
The site build's license header says "Proprietary - not for redistribution"
instead, because it contains the Flaticon assets and GPL would incorrectly
imply anyone could redistribute those too. The site build is meant to live
in a private repo, never a public one.

**GitHub-based updates, not WordPress.org.** Uses the MIT-licensed
"Plugin Update Checker" library (vendored in
`vendor/plugin-update-checker/`, "-repo" build only), pointed at the main
branch of `Stravelakis/Skilitsa.com-Dog-Park-Weather--WordPress-plugin`. It
compares the installed plugin's `Version:` header against the same header
on that branch - a new release just needs the header bumped and pushed, no
formal GitHub Release required (though the library supports that too).
Avoids WordPress.org's review queue entirely, at the cost of being less
"official-looking" than a wordpress.org/plugins listing.

## Current condition

Confirmed by direct code inspection and (where possible) automated
smoke-testing this session — **not yet confirmed on a live site**, since no
session so far has had access to the actual WordPress install:

- Settings save correctly per-tab, no cross-tab data loss (the fix is in;
  live confirmation still pending).
- CSV importer: header-flexible parsing, mitigation columns, and
  bad-data-skipping all verified against real sheet content via a script
  harness. Google Sheet URL import only works for a publicly-viewable or
  published sheet; sign-in-walled sheets need the paste-CSV fallback.
- Frontend and editor preview both use Title Case park names, no header
  icons, and the same translated tooltip.
- `uninstall.php` removes all six options plus the DeepL key, matching
  transient weather-cache entries, and both cron hooks.
- Version strings, cache-busting numbers, and the English/Greek translation
  key sets are all in sync (checked this session).
- Rating engine: apparent-temperature formula and all six condition
  penalties tested against specific scenarios via a standalone script
  (heat+wind, UV+cloud, cold+wind, an ideal day, humid-heat safety
  override) — all behaved as designed. Not yet seen against a real forecast
  on a live site.
- Accessibility: computed real WCAG contrast ratios (not eyeballed) for
  every default color pair. Found and fixed several failures — worst was
  the rating-banner quote text at 1.82:1 (needs 4.5:1). Also added: numeric
  rating exposed to screen readers (previously only the "why" reasoning
  was), aria-hidden on decorative icons, aria-pressed on the day tabs, a
  labeled region landmark, and a visible keyboard focus state on the
  tooltip trigger (which also moved from a 14px near-invisible icon to the
  whole paw row).

**Known, not-yet-fixed gap:** the editor preview doesn't reflect custom
weather icons or custom paw colors — it shows fixed defaults regardless of
Icons/Appearance settings. Not broken, just incomplete; see below.

## What we're thinking about next

- **Live QA pass** is the actual top priority — a lot has shipped across
  several sessions without ever touching a real WordPress install. Save
  each tab, import the parks CSV, check the editor preview against a
  published page, and watch a few real hourly forecasts move through the
  new rating logic, all on the actual site.
- **"Feels like" isn't displayed anywhere yet** — the `feels_like`
  translation string has existed since early on with nothing using it. Now
  that apparent temperature is actually calculated, showing it (e.g. next
  to the raw temperature when the two differ meaningfully) would make the
  new comfort model visible, not just felt.
- **Humidity for OpenWeather/Google Weather**: only Open-Meteo's response
  includes humidity; the other two providers' normalizers don't fetch it,
  so those fall back to an assumed 50% in the comfort calculation. Fine for
  Open-Meteo (the default, free, no-key path), incomplete if a site
  switches providers.
- **Editor preview parity**: wire `edit.js`'s icon and paw-color rendering
  to the same settings it already fetches (it loads `settings` via
  `apiFetch` but only reads `translations` from it right now). Also: the
  editor preview only ever shows a static "Today" tab, no working
  "Tomorrow" toggle - a smaller, longer-standing gap noticed during this
  session's accessibility pass, not fixed yet.
- **Remaining contrast items**: the row-3 border color (used behind
  "middling" hours) was nudged more visible but still doesn't clear the 3:1
  non-text minimum - it's a decorative accent, not the primary rating
  indicator (the paw icons carry that), so lower priority, but worth
  another pass if it still reads as too faint.
- **Tooltip pattern**: the "why this rating" tooltip uses `role="button"` +
  `aria-label` on the paw row rather than the full formal ARIA tooltip
  pattern (`role="tooltip"` + `aria-describedby` with per-row unique IDs).
  The simpler pattern is well-supported and was chosen to avoid generating
  ~48 unique IDs per render, but a stricter audit might flag it.
- **Drainage/shade/wind-shelter inference** for the existing 111-park Greek
  import was done by keyword-matching free-text sheet columns (fencing,
  benches, agility notes) — deliberately conservative, but unverified
  against reality. Worth spot-checking a handful of parks before trusting
  those flags.
- **Auto-translate quality**: MyMemory (free fallback) is rough. If
  translation quality matters beyond the hand-written Greek preset, a DeepL
  key is the real fix — untested so far since no key's been available in
  any session.
- **14 parks with no name + 4 with broken coordinates** in the source Google
  Sheet were excluded from the last import batch rather than guessed at —
  worth fixing at the source if those parks are wanted.
