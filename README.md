# 🐾 Dog Park Weather Rating

**A gorgeous, translatable "should I take my dog to the park right now?" widget for WordPress.**

Dog Park Weather Rating is a Gutenberg block that pulls live hourly weather for any dog park and turns it into a simple 1–5 paw rating — so visitors get an answer in half a second instead of a wall of numbers. Built for [skilitsa.com](https://skilitsa.com), a Greek dog-owner community site, and made to work for any language or any park, anywhere.

<p align="center">
  <em>Today's forecast, in dog terms: 🐾🐾🐾🐾🐾 not 22°C / 0% / 4 km/h.</em>
</p>

---

## Why this exists

Weather widgets tell you the temperature. They don't tell you whether it's actually a good time to take the dog out. This block does the second thing: it looks at rain, wind, UV, cloud cover, real sunrise/sunset-based darkness, and temperature together, weighs them the way *you* decide they should be weighed, and shows a paw rating a dog owner can read in a glance — with the full hour-by-hour breakdown right underneath if they want it.

## ✨ Features

- **Hourly paw ratings, today and tomorrow** — a 1–5 paw score per hour, built from live weather, not a static badge.
- **Configurable scoring** — set how much rain, wind, UV, temperature, cloud cover, and darkness should each matter (0 = ignore, 1 = normal, 5 = severe). Two safety rules always apply on top: sub-5°C/over-35°C/thunderstorm hours are always shown as 1 paw, no matter the sliders.
- **Real sunrise/sunset darkness**, computed per park location from Open-Meteo — not a fixed clock window.
- **Per-park mitigations** — mark a park as having good drainage, shade, wind shelter, or covered areas, and it gets half the usual penalty for that condition.
- **Fully translatable UI** — every visible string (labels, the "why this rating" tooltip, tab names, everything) is overridable from Settings → Translations. Built and battle-tested in Greek, works in any language.
- **Custom animated icons** — bring your own Lottie JSON for the paw rating and every weather condition (sun, partly cloudy, rain, moon, storm, snow), or use the bundled defaults. Recolor them from the Appearance tab without touching a single file.
- **Adjustable block height** — pick a fixed height for the widget; the header/rating banner/tabs stay put and only the hourly list scrolls, so nothing gets cut off.
- **CSV park importer** — bulk-add parks from a spreadsheet (works with a plain Name/Latitude/Longitude sheet, or with a richer "Park Name / Area" + combined coordinates sheet) instead of typing each one in by hand.
- **No API key required** — runs on the free [Open-Meteo](https://open-meteo.com/) API by default, with optional Google Weather / OpenWeather provider support if you want to switch.
- **Cached, not hammered** — weather is refreshed twice a day via WP-Cron for every registered park; visitors get the cached forecast instantly instead of triggering a live API call on every page load.
- **Clean uninstall** — deleting the plugin removes every option, cached transient, and scheduled cron event it created. Nothing lingers in `wp_options`.

## 📦 Installation

1. Download the latest release zip (or clone this repo and run `npm install && npm run build`).
2. In WordPress: **Plugins → Add New → Upload Plugin**, select the zip, and click **Install Now**.
3. Activate it. You'll see a **Settings** link right next to Deactivate on the Plugins page — or go to **Settings → Dog Park Weather**.
4. Add your parks (Parks tab), tune the scoring and look-and-feel (Weights / Appearance tabs), and translate the UI if needed (Translations tab).
5. Add the **"Dog Park Weather Rating"** block to any post or page, pick a park from the dropdown, and publish.

### Building from source

```bash
npm install
npm run start    # dev build with watch
npm run build    # production build (outputs to /build)
npm run plugin-zip  # bundles a WordPress-installable zip
```

## ⚙️ Settings overview

| Tab | What it does |
|---|---|
| **Parks** | Add, edit, and remove parks (name, coordinates, mitigations). Includes a bulk CSV importer — paste CSV directly, or point it at a public Google Sheet link. |
| **Weights** | Tune how much each weather condition affects the paw rating, with plain-language explanations for each slider. |
| **Appearance** | Colors, fonts, corner radius, and the block's total height. |
| **Translations** | Override every UI string — labels, tab names, the rating tooltip, everything. |
| **Icons** | Swap in your own Lottie JSON for the paw ratings and weather conditions, with live recolor controls. |
| **API Providers** | Open-Meteo is the default and needs no key; Google Weather and OpenWeather are available if you'd rather bring your own key. |

### Importing parks from a spreadsheet

The Parks tab has an **Import Parks from a spreadsheet** panel with two options:

- **A public Google Sheet link** — works if the sheet is shared "Anyone with the link → Viewer", *or* published to the web (File → Share → Publish to web → CSV). The plugin converts either link format into the right CSV export URL automatically.
- **Paste CSV directly** — always works, no sharing settings required. Download your sheet as CSV (File → Download → CSV), open it in a text editor, and paste the contents in.

It recognizes two header styles out of the box: a simple `Name, Latitude, Longitude`, or `Park Name / Area` plus a single `Municipality coordinates for weather reports` column formatted as `"lat, lon"`. Rows with unusable coordinates (blank cells, placeholder text) are skipped rather than imported broken, and parks that already exist (matched by name) are never duplicated or overwritten — re-running an import is always safe.

## 🐕 How the rating works

Each hour starts at 5 paws and loses points for rain probability, wind, UV, uncomfortable temperature, cloud cover, and darkness — each weighted by whatever you've set on the Weights tab. Parks with a relevant mitigation (e.g. good drainage) get half the usual penalty for that one condition. Two rules override everything else: any hour below 5°C, above 35°C, or with a severe/thunderstorm forecast is always shown as 1 paw, regardless of the weights. Hovering the small info icon next to any hour's paws shows exactly why it got the score it did.

## 🌍 Requirements

- WordPress 6.4+
- PHP 7.4+
- No API key needed for the default Open-Meteo provider

## 🗑️ Uninstalling

Deactivating the plugin just pauses it — your parks, translations, and settings stay put. **Deleting** it from the Plugins screen removes everything: all settings, the per-park weather cache, and any scheduled cron events. Nothing is left behind in the database.

## 🤝 Contributing

Issues and pull requests are welcome. This plugin is built and maintained for [skilitsa.com](https://skilitsa.com), a Greek dog-owner community platform, so Greek-language edge cases get priority attention — but the whole thing is designed to work for any language and any park, anywhere.

## 📄 License

GPLv2 or later — see [LICENSE](LICENSE).

## 🙏 Credits

Built by [Skilitsa.com](https://skilitsa.com) using WordPress Telex, with development support from Claude and the Nous Research Hermes Agent.
