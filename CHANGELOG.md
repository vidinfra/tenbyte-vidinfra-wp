# Changelog

## Version 1.1.0 - 2024-12-11

### Major Updates: Tenbyte VidInfra Rebrand & Dynamic Watermark

**Plugin Rebranding:**

- ✅ Renamed plugin from "Vidinfra Player" to "Tenbyte VidInfra"
- ✅ Updated plugin name, description, and author in plugin header
- ✅ Updated all admin menu titles and page headings
- ✅ Updated README.md and readme.txt with new branding

**Tabbed Admin Interface:**

- ✅ Implemented tabbed navigation in settings page
- ✅ **General Tab**: Configure default library ID and player ID
- ✅ **Dynamic Watermark Tab**: Configure automatic watermark settings
- ✅ Clean, organized settings interface

**Simplified Shortcode Usage:**

- ✅ Library ID now uses default from admin settings
- ✅ Only `video_id` is required in shortcode
- ✅ Users can override library ID in shortcode if needed
- ✅ Example: `[vidinfra_player video_id="59777392"]`

**Dynamic Watermark Feature:**

- ✅ Automatic watermark based on logged-in user information
- ✅ Watermark text options: User Name, User Email, or User ID
- ✅ Font size dropdown: 10px, 11px, 12px, 14px, 15px, 16px
- ✅ Font color picker with default white (#ffffff)
- ✅ Font opacity slider (0-100%, default 30%)
- ✅ Watermark automatically applied to all videos for logged-in users
- ✅ Full integration with Vidinfra player watermark API

**Technical Improvements:**

- ✅ Added WordPress color picker integration
- ✅ Enhanced sanitization for watermark settings
- ✅ Updated default plugin options on activation
- ✅ Improved admin CSS for better UI presentation
- ✅ Enhanced admin JS with color picker initialization

**API Compatibility:**

The watermark implementation follows the Vidinfra player API specification:

```javascript
{
  text: string,      // Dynamic user data
  color?: string,    // Hex color code
  opacity?: number,  // 0-1 decimal value
  fontSize?: number  // Font size in pixels
}
```

## Version 1.0.1 - 2024-12-11

### WordPress Plugin Directory Compliance

**Enhanced Security & Best Practices:**

- ✅ Added activation and deactivation hooks
- ✅ Added settings link to plugins page
- ✅ Enhanced translation file (.pot) with proper headers
- ✅ Added index.php files to all directories (prevent directory browsing)
- ✅ Modernized admin.js to use Clipboard API (deprecated execCommand fallback)
- ✅ Removed unused frontend.js code
- ✅ Added comprehensive security documentation (SECURITY.md)
- ✅ Added contributing guidelines (CONTRIBUTING.md)
- ✅ Added GPL v2 LICENSE file
- ✅ Created phpcs.xml for WordPress Coding Standards
- ✅ Created .distignore for SVN deployment
- ✅ Created .gitattributes for exports
- ✅ Improved readme.txt with third-party service disclosure
- ✅ Added proper file headers to all CSS/JS files
- ✅ All code follows WordPress Coding Standards

**Code Quality Improvements:**

- Proper input sanitization and output escaping
- Capability checks for admin functions
- No direct file access allowed
- Clean uninstallation process
- Translation-ready with proper text domain

### Simplified: Removed Custom Wrapper Styles

**Breaking Change:** Removed all custom CSS wrapper styles as the Vidinfra player library generates its own iframe with built-in styling.

#### Changes Made

- **Simplified HTML output** - Now generates only a single `<div>` container
- **Removed custom CSS** - All wrapper, aspect-ratio, and positioning styles removed
- **Cleaner markup** - No more nested divs or custom classes for layout
- **Library handles everything** - Width, height, aspect ratio, and responsiveness all managed by Vidinfra library

#### Why This Change?

The Vidinfra player library creates its own iframe element with complete styling. Custom wrapper divs with padding-based aspect ratios were causing layout issues and were unnecessary.

#### Migration

No action needed. Your existing shortcodes will work the same, but the output HTML will be simpler and cleaner.

**Before:**

```html
<div
  class="vidinfra-player-wrapper vidinfra-player-custom-size"
  style="width: 800px; height: 450px"
>
  <div id="vidinfra-player-xyz" class="vidinfra-player-container"></div>
</div>
```

**After:**

```html
<div id="vidinfra-player-xyz"></div>
```

The Vidinfra library then injects its iframe into this container with all necessary styles.

---

## Version 1.0.0 - 2025-12-10

### Fixed: Type Validation & PlayerOptions Interface Compliance

Updated the plugin to match the official Vidinfra PlayerOptions TypeScript interface:

#### Breaking Changes

- **`library_id` is now REQUIRED** (previously optional)
  - Must be provided in shortcode or set as default in settings
  - Error message displayed if missing
  - Accepts both string and number types

#### Type Handling Improvements

- **All IDs now accept string OR number types** (not just integers)
  - `library_id`: string | number (required)
  - `video_id`: string | number (required)
  - `player_id`: string | number (optional, defaults to "default")

#### New Parameters Added

- **`width`**: number | string - Custom player width
- **`height`**: number | string - Custom player height
- **`loading`**: "lazy" | "eager" - Loading strategy (default: "eager")
- **`class_name`**: string - Additional CSS classes for customization

#### Validation Enhancements

- Proper sanitization for string/number ID types
- Validation for `loading` attribute (lazy/eager only)
- Settings error notification when library_id is missing
- Updated default value for `player_id` to "default" when not specified

#### Updated Documentation

- Admin settings page reflects library_id as required
- Shortcode examples updated to include library_id
- Gutenberg block labels updated with requirement indicators
- Help text clarified for all parameters

#### Files Modified

- `vidinfra-player.php` - Main plugin file with type fixes
- `assets/js/block.js` - Gutenberg block with new fields
- `examples.txt` - Updated usage examples
- `README.md` - Documentation updates

### Example Usage (Updated)

**Minimum required shortcode:**

```
[vidinfra_player library_id="1234567" video_id="59777392"]
```

**With new optional parameters:**

```
[vidinfra_player
    library_id="1234567"
    video_id="59777392"
    player_id="custom-player"
    width="800"
    height="450"
    loading="lazy"
    class_name="featured-video"]
```

### Migration Guide

If you were previously using the plugin without `library_id`:

**Before:**

```
[vidinfra_player video_id="59777392"]
```

**After (update required):**

```
[vidinfra_player library_id="YOUR_LIBRARY_ID" video_id="59777392"]
```

Or set a default library_id in Settings > Vidinfra Player.
