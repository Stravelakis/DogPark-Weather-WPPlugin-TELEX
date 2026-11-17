---
# Not reviewed yet: owner ticks templates/docs/REVIEW-CHECKLIST.md, then adds
# checkedAgainst: v0.12.0 and checkedOn: YYYY-MM-DD here.
---

## Dog Park Weather Rating in plain English

It is a block you add to a WordPress page. It shows a simple paw rating, from 1 to 5, for a dog park, hour by hour, for today and tomorrow. Visitors get an answer in half a second instead of a wall of numbers.

- **It thinks in dog terms.** Not "22 degrees, 0% rain, 4 km/h wind" but five paws.
- **You decide what matters.** Set how much rain, wind, sun (UV), temperature, cloud and darkness should count. Two rules always win: an hour below 5 degrees, above 35 degrees, or with a storm is always 1 paw.
- **Each park can have good points.** Mark a park as drained, shaded, sheltered or covered, and the matching problem counts half.
- **Any language.** Every word on screen can be changed in Settings. It was built and tested in Greek.
- **No account needed.** It uses a free weather service (Open-Meteo). You can switch to Google Weather or OpenWeather if you want to use your own key.
- **Fast pages.** Weather is fetched twice a day and saved, so visitors do not wait for a live lookup.
- **Hover to see why.** The small info next to the paws explains how an hour got its score.
- **Leaves no mess.** Deleting the plugin removes everything it stored.

You need WordPress 6.4 or newer and PHP 7.4 or newer.
