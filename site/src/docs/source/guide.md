# Guide: use the plugin

## Add your parks

Go to **Settings -> Dog Park Weather**, then open the **Parks** tab. Add a park with a name and its coordinates, and tick any mitigations that apply (good drainage, shade, wind shelter, covered areas).

To add many at once, use **Import Parks from a spreadsheet**:

- **A public Google Sheet link.** The sheet must be shared "Anyone with the link, Viewer", or published to the web as CSV.
- **Paste CSV directly.** Always works. Download your sheet as CSV, open it in a text editor and paste the contents in.

Two header styles are recognised: `Name, Latitude, Longitude`, or `Park Name / Area` plus one coordinates column written as `"lat, lon"`. Rows with unusable coordinates are skipped. Parks that already exist (matched by name) are never duplicated or overwritten, so running an import again is safe.

## Put it on a page

Add the **Dog Park Weather Rating** block to any post or page, pick a park from the dropdown and publish.

## The settings tabs

| Tab | What it does |
|---|---|
| Parks | Add, edit and remove parks. Includes the bulk importer. |
| Weights | How much each weather condition changes the rating, with plain-language notes on each slider. |
| Appearance | Colours, fonts, corner radius and the block's total height. |
| Translations | Override every visible string, including labels, tab names and the rating tooltip. |
| Icons | Swap in your own Lottie JSON for the paw ratings and weather conditions, with recolour controls. |
| API Providers | Open-Meteo is the default and needs no key. Google Weather and OpenWeather are available if you bring your own key. |

## How the rating works

Each hour starts at 5 paws and loses points for rain chance, strong wind, UV, uncomfortable temperature, cloud cover and darkness, each weighted by your Weights tab. A park with a relevant mitigation gets half the penalty for that one condition. Any hour below 5 degrees C, above 35 degrees C, or with a severe or thunderstorm forecast is always 1 paw, whatever the sliders say. Hover the info icon next to an hour's paws to see why it got its score.

## If an update does not show

The block is rendered fresh on every page load, so settings changes apply straight away. If something looks unchanged, purge your caching plugin first.

## Uninstall

Deactivating only pauses it. Your parks, translations and settings stay. Deleting it from the Plugins screen removes all settings, the cached weather and the scheduled jobs.
