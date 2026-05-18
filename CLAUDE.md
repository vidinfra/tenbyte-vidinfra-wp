# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

WordPress plugin (`vidinfra-player`) that embeds Vidinfra video players via shortcode and Gutenberg block. Single-file PHP architecture — all plugin logic lives in `vidinfra-player.php` as the `Vidinfra_Player` singleton class.

## Linting

```bash
# Run PHPCS with WordPress Coding Standards
phpcs --standard=phpcs.xml .

# Auto-fix fixable issues
phpcbf --standard=phpcs.xml .
```

Standards enforced: `WordPress-Core`, `WordPress-Extra`, `WordPress.WP.I18n`, `PHPCompatibility` (PHP 7.2+). Vendor JS (`assets/js/vendor/`) is excluded from scanning.

## Architecture

**`vidinfra-player.php`** — entire plugin in one class. Key methods:

- `init_hooks()` — registers all WP hooks (enqueue, shortcode, admin menu, Gutenberg block)
- `render_shortcode($atts)` — core output method, also used as Gutenberg block `render_callback`. Builds player config array, resolves watermark from DB options, outputs a `<div>` + inline `<script>` that calls `new Vidinfra.Player(id, config)`
- `sanitize_settings($input)` — settings form handler; merges active-tab input with hidden fields for inactive tab (tab-based form with single `options.php` target)
- `register_gutenberg_block()` — server-side rendered block (`save: () => null`), editor UI in `assets/js/block.js`

**Settings** stored as single option `vidinfra_player_options` (array). Two admin tabs (General, Watermark) each submit the full option array with hidden fields preserving the inactive tab's values.

**Watermark flow**: enabled in admin → on `render_shortcode`, if `is_user_logged_in()` and watermark enabled, resolves user data (name/email/user_id) → passes array to `player.addWatermark()` in inline JS. Opacity stored as 0–100 integer, converted to 0–1 decimal before passing to JS.

**Frontend JS library**: `assets/js/vendor/player.global.js` — bundled `@vidinfra/player` npm package (MIT). Exposes global `Vidinfra.Player`.

**Gutenberg block** (`assets/js/block.js`): vanilla WP block API (no JSX/build step). Registered as `vidinfra/player`. Editor preview rendered in JS; frontend output delegated to PHP `render_callback`.

## Shortcode

```
[vidinfra video_id="59777392"]
[vidinfra video_id="59777392" library_id="9876543" autoplay="true" loop="true" muted="true" aspect_ratio="16:9"]
```

`video_id` required. `library_id` falls back to admin default. Legacy alias `[vidinfra_player]` also registered.

## Key constants

- `VIDINFRA_PLAYER_VERSION` — current version string
- `VIDINFRA_PLAYER_PLUGIN_DIR` / `VIDINFRA_PLAYER_PLUGIN_URL` — filesystem/URL paths
- `VIDINFRA_PLAYER_PLUGIN_BASENAME` — used for settings link filter

## i18n

Text domain: `vidinfra-player`. POT file: `languages/vidinfra-player.pot`. All user-facing strings must use `__()` / `esc_html_e()` with this text domain.
