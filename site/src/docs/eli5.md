---
checkedAgainst: v0.12repo
checkedOn: 2026-11-21
---

## Start here

This page takes you from "I have never added anything to a website" to "my page shows a paw rating for my dog park". Go slowly. Every step says what you should see, so you always know if it worked.

> **Do this first** You do not need to understand everything before you start. Read **The five ideas** (about five minutes), then follow **Step 1** onward. Come back to the rest when you want it.

**What you will have at the end:**

- A web page that shows today's and tomorrow's weather for a dog park, hour by hour, as one to five paws.
- A park you added yourself, with the right name and place.
- A feeling for what the settings do, and what to try when something looks wrong.

**What you need:** a WordPress website that you can log in to as an administrator. That is all. No code, no weather account, no credit card.

**What if you do not have a website?** There is a **Try it live** button at the top of this site. It opens a pretend website inside your browser with the plugin already working, so you can look around first. Nothing you do there stays or changes anything real.

**How long:** about 20 minutes.

## The five ideas

### 1. WordPress is a website maker

**WordPress** is a program that runs a website. You log in to a control room (the **dashboard**), and you write pages there, like writing in a document. Visitors only see the finished pages.

### 2. A plugin is an add-on

A **plugin** is an extra piece you add to WordPress to teach it a new trick, like adding an app to a phone. This plugin is called **Dog Park Weather Rating**. Its trick: show how good the weather is for a dog park.

### 3. A block is a Lego brick for a page

When you write a page, the page is made of **blocks**: a paragraph block, a picture block, a heading block. You stack them. This plugin gives you one new brick, the **Dog Park Weather Rating** block.

### 4. The rating is paws

Instead of a table of numbers, each hour gets **one to five paws**. Five paws means a lovely time to go. One paw means stay home. The plugin decides by looking at rain, sun strength (UV), wind, how warm or cold it really feels, cloud and whether it is dark. You can say which of those matter most to you.

### 5. The weather comes from a free website

The plugin asks a free weather website called **Open-Meteo** for the forecast. It needs no account and no password. Your site asks twice a day and keeps the answer, so visitors do not have to wait.

> **In short** WordPress is the website maker. The plugin adds a brick (block). The brick shows paws. The paws come from a free weather website.

## Step 1: Get the plugin onto your site

You are now going to put the plugin's files on your website. There are a few ways. Pick the one that fits you.

1. Open the project's page on **GitHub**, a website where programmers keep their projects: [the Dog Park Weather Rating page](https://github.com/Stravelakis/DogPark-Weather-WPPlugin-TELEX).
2. Click the green **Code** button, then **Download ZIP**.
3. Unzip the file on your computer (right-click, **Extract all** on Windows).
4. Your web host gives you a way to put files on your site, either a **file manager** in their control panel or a program using **SFTP**. Open it and find the folder `wp-content`, then the folder `plugins` inside it.
5. Make a new folder called `telex-dog-park-weather` there, and upload the unzipped plugin files into it. You need all the `.php` files (including `uninstall.php`), `admin-script.js`, `admin-style.css`, and the folder `build`. The `vendor` folder is optional. The `site` folder is only this documentation; you can leave it out.

> **Why** The folder name matters a little: the plugin's own instructions use that exact name.

**What you should see:** in your file manager, `wp-content/plugins/telex-dog-park-weather/` now holds `telex-dog-park-weather.php` and other files.

<details>
<summary>I cannot find a file manager or SFTP</summary>

Ask your web host's support: "How do I upload a plugin folder to wp-content/plugins?" They answer this all the time. Or use the **Try it live** button first to learn the plugin before you deal with your host.

</details>

## Step 2: Turn it on

1. Log in to your site's dashboard (usually `yoursite.com/wp-admin`).
2. In the left menu click **Plugins**.
3. Find **Dog Park Weather Rating** in the list and click **Activate**.

**What you should see:** a message that the plugin was activated, and next to **Deactivate** a new link called **Settings**.

> **Try it** If you see no **Dog Park Weather Rating** in the list, the folder is in the wrong place. It must be directly inside `wp-content/plugins/`.

## Step 3: Add your first park

1. In the left menu click **Settings**, then **Dog Park Weather**. (Or click the **Settings** link next to **Deactivate**.)
2. A page called **Dog Park Weather Settings** opens on a tab called **Parks**.
3. Click **+ Add Park**. A small form appears.
4. In **Park Name** type the name, for example *Riverside Dog Park*.
5. In **Latitude** and **Longitude** type the park's place. These two numbers are like a street address for the whole planet. To find them, open Google Maps, right-click the park, and click the two numbers at the top of the little menu to copy them. Put the first in Latitude, the second in Longitude.
6. Under **Conditions** tick what is true for this park (see the box below), or leave all four empty.
7. Scroll to the bottom and click **Save Changes**.

> **Watch out** Type the numbers with a **dot**, like `37.9838`, and nothing else. No letters, no commas inside a number. If you break this the block will say it could not load the weather.

**The four ticks, in plain words:**

| Tick | When to use it | What it does |
|---|---|---|
| Good Drainage | The ground dries quickly | Rain counts half as much |
| Good Shade | There are plenty of trees | Strong sun counts half as much |
| Wind Shelter | Walls or trees block the wind | Wind counts half as much |
| Covered Areas | There is a roof to stand under | Feeling too cold or too hot counts half as much |

**What you should see:** the page reloads and your park is still there, with your numbers.

![The Parks tab with two example parks](/DogPark-Weather-WPPlugin-TELEX/screens/settings-parks.png)

## Step 4: Put the block on a page

1. In the left menu click **Pages**, then **Add New Page** (or open a page you already have).
2. Click the blue **+** button at the top. A list of blocks opens.
3. Type **Dog Park** in the search box and click **Dog Park Weather Rating**.
4. A brick appears on your page. On the right, a panel called **Park Selection** has a drop-down called **Choose Park**. Pick your park.
5. Click **Publish** (or **Update**) at the top right, then **View Page**.

**What you should see:** a card with your park's name in a blue bar, a pink box with a short sentence and **TOP HOUR**, two buttons **Today** and **Tomorrow**, and a list of hours with paws.

![The block in the editor with Choose Park in the side panel](/DogPark-Weather-WPPlugin-TELEX/screens/editor-block.png)

![The block on a finished page](/DogPark-Weather-WPPlugin-TELEX/screens/block-tomorrow.png)

> **Watch out** The first look inside the editor is only a preview. It may look a little different from the real page. Always check the real page with **View Page**.

<details>
<summary>The block says "Loading weather data..." for a long time, or "Could not load weather data."</summary>

Give it a few seconds first. If it stays, the weather could not be fetched. Check the park's Latitude and Longitude (dots, plain numbers). If your page uses a caching add-on, use its **Purge cache** button and try again.

</details>

<details>
<summary>I see nothing at all where the block should be</summary>

The block hides itself if no park is chosen, or if the park was removed in Settings. Click the block in the editor and choose a park again.

</details>

## Step 5: Read the card

Look at the real page you just published.

- **Blue bar:** the park's name.
- **Pink box:** one sentence for the **best** hour in the list, and **TOP HOUR** with that hour's time.
- **Today / Tomorrow:** two buttons. Click one to switch day.
- **Each row:** the time, a small weather picture, the temperature, and the paws. Below them: the chance of rain (a drop), the wind (a letter **B** and a number, from the Beaufort scale where 0 is calm and 12 is a hurricane), and the sun strength (a letter **U** and a number, the UV index).
- **The first row on Today** says **Now**.
- Hours from midnight to 6 am are left out. Hours that are already past are hidden.

Now hover your mouse over the paws of any hour (or press the **Tab** key until a row lights up). A small dark bubble appears that says **why** the hour got that score, for example *After dark*.

![The reason bubble](/DogPark-Weather-WPPlugin-TELEX/screens/block-tooltip.png)

**What you should see:** a bubble with one or more reasons. If nothing is wrong with the hour it says **Ideal conditions**.

## Step 6: How the paws are decided

You do not need this to use the plugin, but it is nice to know.

Every hour **starts with five paws**. Then it loses some for each thing that makes the park less pleasant: rain chance, strong sun, strong wind, an uncomfortable temperature, and darkness. A cold, cloudy hour loses a little more. A hot, cloudy hour gains a little, because cloud is shade.

The temperature used is the **feels-like** one, which mixes the real temperature with humidity and wind. A breeze on a hot day makes it feel cooler. A breeze on a cold day makes it feel colder.

Two rules always win, whatever you set:

1. If it **feels** colder than 5 °C or hotter than 35 °C, the hour is **one paw**.
2. If a **thunderstorm** is forecast, the hour is **one paw**.

> **Why** Those are the cases where going out is simply not a good idea for a dog, so no setting can hide them.

If a park has a matching tick from Step 3, the matching loss is cut in half.

## Step 7: Change what matters

1. Go to **Settings → Dog Park Weather** and click the **Weights** tab.
2. You see six boxes: **Rain**, **UV**, **Wind**, **Temperature**, **Cloud Cover**, **Darkness (nighttime)**.
3. A number tells the plugin how much that thing matters. **0** means ignore it. **1** is normal (that is what it starts as). **5** means it drops the score fast.
4. Change a number, scroll down and click **Save Changes**.
5. Reload your real page.

**What you should see:** the paws change to match. For example setting **Darkness** to 0 stops night hours from losing a paw just for being dark.

![The Weights tab](/DogPark-Weather-WPPlugin-TELEX/screens/settings-weights.png)

## Step 8: Use your own words

Everything the card says can be changed, so it can speak your language.

1. Click the **Translations** tab.
2. Each line has the original English under its name and a box to type your own wording. Leave a box empty to keep the English.
3. To save time, click **Use Greek preset** (a hand-written Greek set) or pick a language next to **Auto-translate to:** and click **Translate All**.
4. Read what was filled in and fix anything that is odd. **Machine translation is a rough first draft.**
5. Click **Save Changes**.

**What you should see:** the card on your page uses your new words after you reload it.

> **Watch out** Nothing is saved until you click **Save Changes**. The preset and the translate button only fill in the boxes.

![The Translations tab](/DogPark-Weather-WPPlugin-TELEX/screens/settings-translations.png)

## Step 9: Change the look

On the **Appearance** tab you can change colours, the font, and the **Block Height** (how tall the card is, from 300 to 2000 pixels). Colours are written like `#1976D2`: a hash sign, then six letters or digits. There is a small colour square beside each box if you would rather click.

On the **Icons** tab you can swap the paw and weather pictures. This copy of the plugin comes without the fancy animated icons, so it uses simple emoji pictures instead (🐾, ☀️, ☁️). You can leave this tab alone.

> **Try it** Change one colour, save, and reload your page. If you dislike it, type the old colour back. The grey text inside each box shows the original.

## Step 10: Add a lot of parks at once

If you have a list of parks in a spreadsheet, you do not need to type each one.

1. Go to **Settings → Dog Park Weather → Parks**.
2. Click **Import Parks from a spreadsheet** to open it.
3. Either paste a link to a Google Sheet that anyone with the link can view, **or** paste the sheet's contents as plain text (called **CSV**, which is just the rows of a table with commas between the columns).
4. The first row of the sheet must be column names, either `Name`, `Latitude`, `Longitude`, or `Park Name / Area` and one column holding both numbers like `37.9838, 23.7275`.
5. Click **Import Parks**.

**What you should see:** a message like **Added 3 parks (0 already existed and were skipped).** Run it twice if you like: a park whose name already exists is not added again.

![The import panel](/DogPark-Weather-WPPlugin-TELEX/screens/settings-import.png)

## What leaves your site

In plain words:

- Your **server** (the computer that holds your site) asks the weather website for the forecast of each park's place. That is the only thing sent to the weather website: the place.
- Your **visitors** only talk to **your** site. The plugin does not send anything about them to the weather website.
- If you use fancy animated icons, a visitor's browser also fetches one small script from a company called Cloudflare. With the simple emoji icons it does not.
- The **translate button** sends the plugin's built-in English phrases to a translation website. It does not send your pages.

## When something is wrong

<details>
<summary>I changed something and nothing happened</summary>

Many sites keep a quick copy of each page so it loads fast. Find your **caching** add-on (or your host's cache) and click **Purge** or **Clear cache**. Then reload the page.

</details>

<details>
<summary>"Could not load weather data."</summary>

Look at the park's **Latitude** and **Longitude** on the Parks tab. Plain numbers with a dot, nothing else. If they are right, wait a minute and reload: the weather website might be busy.

</details>

<details>
<summary>Every hour is one paw</summary>

That happens when it feels below 5 °C or above 35 °C, or a thunderstorm is forecast. Hover the paws to see the reason.

</details>

<details>
<summary>A tab forgot what I typed</summary>

Each tab has its own save. Click **Save Changes** on a tab before you go to the next one.

</details>

<details>
<summary>I want to remove it</summary>

On **Plugins**, click **Deactivate** to pause it (your parks and settings stay). Then click **Delete** to remove it and everything it saved. The block on your pages will go quiet; remove it from the page too.

</details>

## Small dictionary

| Word | Meaning |
|---|---|
| WordPress | The program that runs your website |
| Dashboard | The control room you log in to |
| Plugin | An add-on that teaches WordPress a new trick |
| Block | A Lego brick you put on a page |
| Latitude / Longitude | The two numbers that say where a place is on Earth |
| Feels-like | The temperature your body would sense, mixing in humidity and wind |
| UV | How strong the sun's burning rays are |
| Beaufort | A 0 to 12 scale for wind strength |
| Cache | A quick saved copy, kept so things load faster |
| CSV | A table written as plain text, one row per line |
