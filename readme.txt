=== Dog Park Weather Rating ===

Contributors:      WordPress Telex, Claude Code, Nous Research Hermes Agent, Lambros Stravelakis
Tags:              block, weather, dog park, forecast, rating
Tested up to:      6.8
Stable tag:        0.12.0
License:           GPLv2 or later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html

A beautiful weather forecast block for dog parks. Displays hourly weather data (temperature, rain chance, wind speed, UV index, cloud cover) for today and tomorrow, along with a 1–5 paw rating that tells visitors how good the conditions are for a park visit.

== Description ==

Dog Park Weather Rating fetches real-time hourly weather data from the Open-Meteo API and presents it in a gorgeous, mobile-friendly card. Each hour gets a paw rating (1–5) based on configurable scoring rules.

**Key Features:**

* Hourly forecast for today and tomorrow with temperature, rain probability, wind speed, UV index, and cloud cover.
* Smart 1–5 paw rating system that evaluates weather conditions for dog park suitability.
* Per-park mitigation settings: mark parks as having good drainage (halves rain penalty), good shade (halves UV penalty), wind shelter (halves wind penalty), or covered areas (halves cloud/cold penalty).
* Full translation support: every UI string can be customized via the settings page — perfect for Greek or any language.
* Custom animated icons: upload Lottie JSON files for paw ratings (active/inactive) and all weather conditions (sun, partly cloudy, rain, moon, storm, snow).
* Multi-park management: register unlimited parks with name, coordinates, and conditions, then select the park from a dropdown when inserting the block.
* Responsive design with a modern blue-and-pink colour scheme.
* Uses the free Open-Meteo API — no API key required.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/telex-dog-park-weather` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Go to Settings → Dog Park Weather to add your parks and customize translations and icons.
4. Insert the "Dog Park Weather Rating" block in any post or page and select a park.

== Frequently Asked Questions ==

= Do I need an API key? =

No. The block uses the Open-Meteo free API which requires no authentication.

= Can I use this in Greek or another language? =

Yes! The Translations tab in the settings page lets you override every UI string with your own wording in any language.

= How is the paw rating calculated? =

The rating starts at 5 and deducts points for rain probability, high wind, extreme UV, uncomfortable temperatures, heavy cloud cover, and darkness (based on the real sunrise/sunset for the park's location). Parks with mitigations (good drainage, shade, etc.) receive half the penalty for the relevant condition. Any hour below 5°C, above 35°C, or with a severe/thunderstorm forecast is always shown as 1 paw, regardless of other conditions. Hover the small info icon next to any hour's paws to see why it got its rating.

== Changelog ==

= 0.12.0 =
* License made consistent: GPLv2 or later everywhere (plugin header, LICENSE file, and readme.txt previously disagreed - LICENSE and readme.txt said MIT)
* Icons can now fall back to plain emoji when the licensed Lottie/SVG asset files aren't present in a build - this is what makes a public distribution possible: the bundled weather/paw icons come from a Flaticon Premium subscription, which does not permit redistributing the raw files in a downloadable plugin (confirmed against Flaticon's actual license terms). The site build keeps the licensed icons; a separate "-repo" build ships without them and automatically uses emoji instead - same code, different bundled assets
* Added a GitHub-based update checker (vendored, MIT-licensed "Plugin Update Checker" library) - only included in the "-repo" build, not the site build, so the site's licensed icons are never at risk of being silently overwritten by an auto-update from the public repo

= 0.11.1 =
* "Powered by" now reads "Powered by Skilitsa.com and Open-Meteo" (or whichever provider is active), both linked
* Found and fixed a real divergence while making this change: the editor preview already had its own "Powered by" line that nobody asked for or knew was there, hardcoded to reference the WordPress Telex tool ("telex.automattic.ai") that originally built the plugin rather than the actual weather data source - now shows the same Skilitsa.com + weather-provider attribution as the frontend
* Added api_providers to the settings REST endpoint so the editor preview can determine the actually-active provider instead of guessing

= 0.11.0 =
* "Powered by" attribution now actually appears on the frontend (the CSS and translation string existed but no template ever output the element)
* Full WCAG contrast audit with computed ratios, several real failures found and fixed: rating banner text was as low as 1.82:1 against its pink background (needs 4.5:1), "powered by" text was 2.59:1, inactive paw icons were 1.54:1, header/best-hour blue and pink were borderline - all corrected
* The "why this rating" tooltip is no longer a tiny near-invisible icon - the whole paw row is now the hoverable/focusable trigger, with a much larger target and a visible keyboard focus outline
* Screen readers now hear the actual numeric rating ("4 out of 5 paws") before the explanation - previously only the reasoning was exposed, never the rating itself
* Decorative icons (weather icon, paw icons, detail icons) marked aria-hidden so screen readers don't try to announce raw icon content
* Today/Tomorrow tabs now expose aria-pressed state for screen readers
* Added a region landmark with a descriptive label ("Dog park weather forecast for [park name]") to the widget
* Left-side hourly row elements (time, weather icon, temperature) enlarged further
* Fixed a stale fallback that could show a 600px block height instead of the real 780px default in some cases

= 0.10.0 =
* Rating system redesigned around a real "feels like" comfort model (temperature + humidity + wind combined via the standard apparent-temperature formula) instead of scoring each condition independently
* Wind is no longer penalized on its own at light-to-moderate speeds (up to Beaufort 5) - its effect is now captured correctly as cooling relief in heat and extra chill in cold; only genuinely strong wind (Beaufort 6+) still gets its own penalty
* UV penalty is now dampened under cloud cover instead of applying at full strength regardless of how overcast it looks
* Cloud cover is now context-dependent: a small comfort bonus on hot days (natural shade), a small penalty only when already cold (added gloom), neutral near room temperature - previously always treated as a flat negative
* The safety override (always-1-paw for dangerous conditions) now uses feels-like temperature instead of the raw thermometer reading, so humid heat and wind chill are both caught correctly
* The "why this rating" tooltip is now generated from the exact same shared logic as the actual scoring (previously two separate hand-written copies that could drift out of sync) - fixed in both the editor preview and the frontend
* Added humidity to the data pulled from Open-Meteo's forecast and wired it through to the rating calculation (previously fetched but completely unused)
* Updated the plain-language Weights tab descriptions to match the new behavior

= 0.9.1 =
* Fixed uninstall.php missing the DeepL API key option (added after uninstall.php was first written, never added to its cleanup list - deleting the plugin would have left it behind)
* Bumped admin script/style cache-busting version strings, which had been stuck at 0.7.0
* Verified: per-tab settings groups, the two-sibling-forms fix, CSV mitigation parsing, and English/Greek translation key parity all still correct after the last two sessions' changes

= 0.9.0 =
* Removed the location-pin and paw icons from the Gutenberg editor preview's park name header (the frontend template was already fixed in an earlier release, but the separate editor preview component still had the old markup - this is why the icons kept reappearing)
* Park name is now Title Case instead of ALL CAPS
* The hover tooltip explaining "why this rating" (rain/wind/UV/temperature/etc reasons) is now fully translatable - added 10 new translation keys, included in the Greek preset
* Added the same "why this rating" tooltip to the editor preview, which was previously missing it entirely
* Enlarged the left side of each hourly row (time, weather icon, temperature) to fill the row better and reduce the empty gap before the paw ratings

= 0.8.0 =
* CRITICAL FIX: saving any settings tab was silently wiping the options behind every OTHER tab (e.g. saving Translations blanked Parks; importing Parks blanked Translations). All settings tabs shared one WordPress "settings group," and WordPress nulls out any option in a group whose fields weren't present in the submitted form - since each tab only renders its own fields (full-page-reload tabs, not a JS show/hide), every save was wiping everything else. Fixed by giving each tab its own settings group, so a save can now only ever affect that tab's own option(s).

= 0.7.0 =
* CSV park importer now also imports the four mitigation checkboxes (Good Drainage, Good Shade, Wind Shelter, Covered Areas) when matching columns are present, instead of only name + coordinates
* Default block height increased 30% (600px -> 780px) - note this only changes the default for sites that have never saved an Appearance setting; if you've already saved settings once, update the Block Height field on the Appearance tab directly
* Added optional DeepL API key field on the Translations tab - "Translate All" uses DeepL (noticeably better quality) when a key is present, falling back to the free service otherwise
* Bumped admin script/style cache-busting version strings, which had been stuck at 0.1.0 since the first release

= 0.6.0 =
* Fixed the Parks tab Save Changes button - the CSV importer's own form was nested inside the main settings form (invalid HTML), which broke form submission on that tab. Moved it to sit alongside the settings form instead of inside it.
* HANDOVER.md is now bundled inside the plugin zip
* Added a "Use Greek preset" one-click button on the Translations tab (hand-written, matches the playful tone of the English defaults)
* Added an "Auto-translate" tool on the Translations tab: pick a language (10 common options) and a style (Neutral/Playful), and every default string is translated via a free translation service and filled into the fields for review - nothing is saved until Save Changes is clicked

= 0.5.0 =
* Added a "Settings" link next to Deactivate on the Plugins list page
* Plugin author is now Skilitsa.com (linked to https://skilitsa.com)
* Added a proper uninstall.php: deleting the plugin now removes every option, the per-park weather cache transients, and any scheduled cron events - nothing is left behind in wp_options
* Google Sheet park importer now also accepts "Publish to web" CSV links (works even when "Anyone with the link" sharing is restricted by an organization)
* Park import errors are now specific (empty input / link needs sign-in / columns not recognized / no usable coordinates) instead of one generic message
* Added a proper GitHub README.md
* Investigating a report that manually adding a park doesn't save - no reproducible cause found yet in code review; flagged for browser console + PHP error log follow-up

= 0.4.0 =
* Block height is now adjustable (Appearance tab), defaulting to 600px; only the hourly list scrolls, header/rating/tabs stay fixed and nothing is trimmed
* Removed the location-pin and paw icons that used to flank the park name in the header
* Fixed Today/Tomorrow tabs rendering as two separate pills instead of one seamless capsule (reset native button appearance)
* Swapped in a new paw icon for the active rating state and bumped icon size slightly
* Weights tab rewritten with short, plain-language explanations for each condition slider
* Hardened translations against a bug where one malformed-UTF-8 character anywhere in a saved translation could silently blank out all translations
* Added a CSV park importer (Parks tab): paste CSV directly, or import from a public Google Sheet link

= 0.3.0 =
* Weather is now fetched twice daily (2 AM and 2 PM, site timezone) via WP-Cron for every registered park, instead of on every visitor request
* The REST endpoint now serves the cached forecast and only makes a live provider call on a genuine cache miss (new park, first activation)
* Still always requests the 2-day forecast

= 0.2.0 =
* Darkness penalty now uses real sunrise/sunset instead of a fixed 7 PM-7 AM window
* Added a hard 1-paw floor for temperatures below 5°C, above 35°C, or severe weather
* Added a hover tooltip explaining each hour's rating
* Bundled the full Lottie/SVG icon set as plugin defaults (no Media Library upload needed)
* Added Icon Outline Color and Icon Accent Color settings; icons now recolor from the Appearance tab
* Fixed the Appearance and Weights settings tabs not reaching the frontend at all

= 0.1.0 =
* Initial release
