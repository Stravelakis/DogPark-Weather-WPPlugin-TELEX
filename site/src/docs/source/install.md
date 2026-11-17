# Install

You need WordPress 6.4 or newer and PHP 7.4 or newer. The default weather source needs no API key.

## From a release

1. The latest release is `v0.12repo`, a build without the licensed icon files (it uses emoji icons instead). If a plugin zip is attached to it, in WordPress go to **Plugins -> Add New -> Upload Plugin**, choose the zip and click **Install Now**.
2. Otherwise follow the next section.
3. Activate it. A **Settings** link appears beside Deactivate, or go to **Settings -> Dog Park Weather**.
4. Add your parks, tune the Weights and Appearance tabs, and translate the text if you need to.
5. Add the **Dog Park Weather Rating** block to a post or page and pick a park.

The `v0.12repo` release page has no attached file at the time of writing, so use the method below.

## Install from the repository files

This repository holds the finished plugin, not the block's source code (there is no `package.json` or `src/` here). To install it by hand, copy the repository's files into `wp-content/plugins/telex-dog-park-weather/` on your site, then activate it on the Plugins screen. That folder name is the one the plugin's own `readme.txt` gives.

## Updates

The public "-repo" build includes a GitHub-based update checker, so WordPress can offer new versions from this repository.

## Uninstall

Deleting the plugin from the Plugins screen removes every option, cached weather entry and scheduled job it created.
