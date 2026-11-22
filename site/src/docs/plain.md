---
checkedAgainst: v0.12repo
checkedOn: 2026-11-21
---

## What it is

Dog Park Weather Rating is a **WordPress plugin** that adds one **block** to your site. The block shows a dog park's hourly weather for today and tomorrow as a score of **one to five paws**, so a visitor gets "good time to go" or "stay home" at a glance instead of a table of numbers.

- **You choose what matters.** Rain, UV, wind, temperature, cloud and darkness each have a number box.
- **Each park can have good points.** Tick drainage, shade, wind shelter or covered areas and the matching problem counts half.
- **Any language.** Every word the block shows can be changed in Settings.
- **No account and no key** for the default weather source (Open-Meteo, a free service).
- **Hover the paws** on any hour to see why it got its score.

> **In short** Install, add your parks in Settings, add the block to a page, pick a park. Weather updates itself twice a day.

**Try it first.** The **Try it live** button at the top of this page opens a throwaway WordPress in your browser with the plugin and two example parks already set up. Nothing is installed on your site. It loads the plugin from GitHub and looks up weather for the example parks.

## What you need

- A WordPress site (version 6.4 or newer) on PHP 7.4 or newer, and an administrator login.
- The ability to upload a plugin, or to copy files into `wp-content/plugins/`.
- Latitude and longitude for each park. In Google Maps, right-click the spot and click the numbers at the top of the menu to copy them.

## Install it

The latest release is `v0.12repo`. At the time of writing its GitHub page has no zip file attached, so the reliable way is to copy the files.

1. Download the repository from GitHub (**Code → Download ZIP**) and unzip it.
2. Put the plugin files in a folder called `telex-dog-park-weather` inside `wp-content/plugins/` on your site (SFTP or your host's file manager).
3. In WordPress go to **Plugins** and click **Activate** under **Dog Park Weather Rating**.
4. A **Settings** link now sits beside **Deactivate**. It opens **Settings → Dog Park Weather**.

If a release zip is attached later, use **Plugins → Add New → Upload Plugin** instead.

> **Watch out** The repository also contains a `site` folder (this documentation) and a few documents. They are harmless, but you only need the plugin files.

**Updates.** Do not count on WordPress offering updates. The plugin contains an update checker, but it points at a different GitHub address and it is not confirmed that it works. To update, replace the files with the new version. Your settings stay in the database.

## Add your parks

Go to **Settings → Dog Park Weather**. The **Parks** tab opens first.

1. Click **+ Add Park**.
2. Fill in **Park Name**, **Latitude** (for example `37.9838`) and **Longitude** (for example `23.7275`). Use numbers with a dot, nothing else.
3. Tick what applies under **Conditions**: Good Drainage (halves the rain penalty), Good Shade (UV), Wind Shelter (wind), Covered Areas (cold and heat discomfort).
4. Click **Save Changes** at the bottom.

![The Parks tab with two example parks](/DogPark-Weather-WPPlugin-TELEX/screens/settings-parks.png)

> **Watch out** Latitude and longitude are not checked when you save. If you type something that is not a plain number, the block will say "Could not load weather data".

### Many parks at once

Open **Import Parks from a spreadsheet** at the top of the Parks tab. You have two options:

- **Option A: a link.** A Google Sheet shared as **Anyone with the link → Viewer**, or **File → Share → Publish to web → CSV**. A sheet that needs a Google sign-in cannot be reached.
- **Option B: paste CSV.** Always works. Download your sheet as CSV, open it in a plain text editor, copy everything, paste it in the box.

Click **Import Parks**. The first row must be headers. Use either `Name`, `Latitude`, `Longitude`, or `Park Name / Area` plus one coordinates column written as `lat, lon`. You can add columns named `Good Drainage`, `Good Shade`, `Wind Shelter`, `Covered Areas`; a cell with `Yes`, `Y`, `True`, `1` or `Ναι` ticks the box, anything else leaves it off.

![The import panel](/DogPark-Weather-WPPlugin-TELEX/screens/settings-import.png)

Parks with no name or unusable coordinates are skipped. A park whose name already exists is left alone, so running an import twice is safe. You see a message such as "Added 3 parks (1 already existed and were skipped)".

## Put it on a page

1. Edit a page or post.
2. Click **+**, search for **Dog Park Weather Rating**, add it.
3. In the right-hand panel under **Park Selection**, use **Choose Park**.
4. **Update** or **Publish** the page, then open it.

![The block in the editor with Choose Park in the side panel](/DogPark-Weather-WPPlugin-TELEX/screens/editor-block.png)

The editor preview is a little different from the live page (icons and colours do not follow your Icons and Appearance settings, and only Today is shown). Check the real page.

You can add the block to several pages, or several times on one page, each with its own park. If a park is later deleted in Settings, the block quietly disappears from the page.

![The block on a page](/DogPark-Weather-WPPlugin-TELEX/screens/block-today.png)

How to read it: the pink banner quotes the message for the **best** hour in the list, with **TOP HOUR** below. The **Today** and **Tomorrow** tabs switch days. Each row shows the time, a weather icon, the temperature, the paws, then rain chance, wind (as a Beaufort number such as `B3`) and UV (`U4`). Hours between midnight and 6 am are not shown. The first row on Today is labelled **Now**.

## How the score works

Each hour starts at five paws and loses points for rain chance, strong wind, UV, an uncomfortable "feels like" temperature, and darkness. Cloud can take a little off on a cold day or add a little on a hot one. Each of these is multiplied by its number on the **Weights** tab. A park with a matching condition gets **half** that one penalty.

Two rules always win and the weights cannot turn them off: an hour whose **feels-like** temperature is below 5 °C or above 35 °C, or with a thunderstorm forecast, is always **one paw**.

"Feels like" blends temperature, humidity and wind. Darkness uses the park's real sunrise and sunset when the weather source supplies them.

Hover (or tab onto) the paws of any hour to see the reasons, for example "Rain chance 70% · Windy (B6)".

![The reason bubble](/DogPark-Weather-WPPlugin-TELEX/screens/block-tooltip.png)

**Weights tab.** `0` switches a condition off, `1` is normal (the default), up to `5` makes it drop the score fast.

![The Weights tab](/DogPark-Weather-WPPlugin-TELEX/screens/settings-weights.png)

## Languages

On the **Translations** tab every word the block shows has a box: day names, "Today", "Tomorrow", the five rating messages, the wind names, the reasons in the bubble, and more. Leave a box empty to keep the English.

- **Use Greek preset** fills in a hand-written Greek set.
- **Auto-translate to** (Greek, Spanish, French, German, Italian, Portuguese, Dutch, Turkish, Romanian or Bulgarian) with **Translate All** fills the boxes with machine translation. Treat it as a rough first draft and have a native speaker look.
- A **DeepL API key** is optional. With one, DeepL does the work and the quality is better.

Nothing is saved until you click **Save Changes**.

![The Translations tab](/DogPark-Weather-WPPlugin-TELEX/screens/settings-translations.png)

## Look and feel

- **Appearance** tab: colours for the header, banner, tabs, rows and paws, the font, and the **Block Height** (300 to 2000 pixels, default 780). Colour boxes want `#` and a hex code, such as `#1976D2`.
- **Icons** tab: paw and weather icons. This repository's version of the plugin ships without the licensed animated icons, so it shows plain emoji (🐾 ☀️ ☁️). You can point a box at your own icon file (a Lottie `.json` or an `.svg`) if you want something else.
- **API Providers** tab: where the weather comes from. Open-Meteo is the default and needs nothing. Google Weather and OpenWeather work if you paste your own key and tick **Enable**. If the first provider fails after the number in **Retries Before Fallback**, the next enabled one is tried.

> **Why** Stay with Open-Meteo unless you have a reason. It is free and the other two cannot supply humidity or sunrise and sunset, so scores differ slightly.

![The Appearance tab](/DogPark-Weather-WPPlugin-TELEX/screens/settings-appearance.png)

## Privacy: what leaves your site

- **Park coordinates** go from **your server** to the weather service (Open-Meteo by default) when the forecast is refreshed. Your visitors' browsers do not talk to the weather service.
- **Visitors** load the forecast from **your own site**. Nothing about them is sent to the weather service by this plugin.
- If you use **animated Lottie icons**, the visitor's browser also loads a small script from `cdnjs.cloudflare.com`. With the default emoji icons it does not.
- **Auto-translate** (only when you press it) sends the plugin's built-in English phrases, not your content, to DeepL or to MyMemory. If you use a DeepL key, the key is sent to DeepL.
- Importing from a **Google Sheet link** makes your server fetch that link.
- If you add an **API key**, it is saved as plain text in your site's database.
- The block links to **Skilitsa.com** and to the weather service named under "Powered by".

## Keep it fast

The forecast for every park is fetched twice a day (around 2 am and 2 pm in your site's time zone) and saved for 15 hours. Visitors get the saved copy. If a new park has no saved forecast yet, the first visitor waits a few seconds while it is fetched.

This relies on WordPress's own scheduler, which only runs when someone visits. On a very quiet site a refresh can be late.

## If something looks wrong

> **Do this first** Purge your caching plugin (and your host's cache), then reload. The block is built fresh on every uncached load, so a cache is the usual reason a change "did not take".

<details>
<summary>The block says "Could not load weather data"</summary>

Check the park's latitude and longitude: plain numbers with a dot, for example `37.9838`. Then make sure your host lets the server reach the internet, and that on **API Providers** the primary provider is ticked **Enable**. If it uses a key, check the key.

</details>

<details>
<summary>The block is missing from the page</summary>

You probably chose no park, or the park was deleted in Settings. Select the block in the editor and use **Choose Park**.

</details>

<details>
<summary>Everything is one paw</summary>

Under 5 °C or over 35 °C "feels like", or a thunderstorm forecast, is always one paw. Hover the paws to see the reason.

</details>

<details>
<summary>The times look off for visitors in another country</summary>

Today and the hour list are worked out in the visitor's browser. Visitors far from the park's time zone may see a shifted "Now" row. This has not been tested here; report it on GitHub if you see it.

</details>

<details>
<summary>A tab lost my changes</summary>

Each tab saves on its own and only that tab's changes are kept. Click **Save Changes** before moving to another tab.

</details>

<details>
<summary>The Icons tab shows odd "http://" boxes</summary>

In the version without the licensed icons the boxes show emoji with `http://` in front. Click **Clear** on a box you do not want, or leave the tab alone and do not save it.

</details>

> **Watch out** Keys you paste on the API Providers tab are stored as plain text in your site's database, and the plugin has not been security-reviewed for handling them. The default Open-Meteo setup needs no key, which is one more reason to keep it.

## Removing it

**Deactivate** only pauses the plugin; parks and settings stay. **Delete** (on the Plugins screen) removes all its settings, saved forecasts and scheduled jobs. Pages that contain the block keep the empty block until you remove it.
