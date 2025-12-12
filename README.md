# Tenbyte VidInfra WordPress Plugin

A secure and feature-rich WordPress plugin to embed Vidinfra video player with iframe support, dynamic watermark options, and customizable settings.

## Features

- 🎥 **Easy Video Embedding** - Simple shortcode and Gutenberg block integration
- 🔒 **Security First** - All user inputs are sanitized and validated
- 🎨 **Customizable Player** - Control autoplay, loop, muted, controls, and more
- 💧 **Dynamic Watermark Support** - Automatically add watermarks based on logged-in user information (name, email, or user ID)
- 🎨 **Watermark Customization** - Configure font size, color, and opacity for watermarks
- 📱 **Fully Responsive** - Player library handles all responsive behavior automatically
- ⚙️ **Admin Settings** - Tabbed interface with General and Dynamic Watermark settings
- 🧩 **Gutenberg Block** - Full support for the WordPress block editor
- 🌐 **Translation Ready** - Internationalization support with text domain
- 🎯 **Clean Output** - Minimal markup, all styling handled by Vidinfra player library

## Installation

1. Download the plugin files
2. Upload the `vidinfra-player` folder to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Go to Settings > Tenbyte VidInfra to configure default settings

## Usage

### Shortcode

Use the `[vidinfra_player]` shortcode to embed videos:

```
[vidinfra_player video_id="59777392"]
```

**Note:** Only `video_id` is required. The `library_id` will be taken from the default settings configured in the admin panel. You can override it in the shortcode if needed.

#### Available Parameters

- `video_id` (required) - The Vidinfra video ID (string or number)
- `library_id` (optional) - The library ID (string or number, uses default from settings if not provided)
- `player_id` (optional) - The player ID (string or number, defaults to "default")
- `width` (optional) - Player width (number in pixels or string with unit, e.g., "800" or "100%")
- `height` (optional) - Player height (number in pixels or string with unit, e.g., "450" or "auto")
- `aspect_ratio` (optional) - Aspect ratio when width/height not specified (16:9, 4:3, 1:1, 21:9, 9:16, default: 16:9)
- `autoplay` (optional) - Auto-play video (true/false, default: false)
- `loop` (optional) - Loop video (true/false, default: false)
- `muted` (optional) - Mute video (true/false, default: false)
- `controls` (optional) - Show controls (true/false, default: true)
- `preload` (optional) - Preload video (true/false, default: true)
- `loading` (optional) - Loading strategy (lazy/eager, default: eager)
- `class_name` (optional) - Additional CSS classes for customization

#### Example with Override

Override the default library ID for a specific video:

```
[vidinfra_player video_id="59777392" library_id="9876543"]
```

#### Example with Additional Parameters

```
[vidinfra_player video_id="59777392" width="800" height="450" autoplay="false" loop="false" muted="false" controls="true" preload="true" aspect_ratio="16:9" loading="lazy" class_name="featured-video"]
```

#### Example with Custom Dimensions

When you specify `width` and/or `height`, the Vidinfra player library will apply those dimensions to the generated iframe:

```
[vidinfra_player library_id="1234567" video_id="59777392" width="800" height="450"]
```

#### Example with Aspect Ratio

The `aspect_ratio` parameter is passed to the Vidinfra player library to control the iframe's aspect ratio:

```
[vidinfra_player library_id="1234567" video_id="59777392" aspect_ratio="16:9"]
```

**Note:** All styling and responsiveness is handled by the Vidinfra player library. The plugin generates a simple container div, and the library creates its own iframe with built-in styles.

### Gutenberg Block

1. In the block editor, click the (+) icon to add a new block
2. Search for "Vidinfra Player"
3. Configure the video settings in the block sidebar
4. Enter the required Video ID
5. Optionally configure player options and watermark settings

### Admin Settings

Navigate to **Settings > Vidinfra Player** to configure:

- Default Library ID
- Default Player ID
- Default Aspect Ratio

These defaults will be used when parameters are not specified in the shortcode or block.

## Security Features

- ✅ Input sanitization for all user-provided data
- ✅ Output escaping for all displayed content
- ✅ Nonce verification for settings forms
- ✅ Capability checks for admin functions
- ✅ Validation of all parameters
- ✅ XSS protection
- ✅ SQL injection prevention

## Developer Information

### Hooks and Filters

The plugin follows WordPress coding standards and provides the following:

- Action: `plugins_loaded` - Initialize the plugin
- Action: `wp_enqueue_scripts` - Enqueue frontend assets
- Action: `admin_enqueue_scripts` - Enqueue admin assets
- Shortcode: `vidinfra_player` - Render video player

### File Structure

```
vidinfra-player/
├── vidinfra-player.php       # Main plugin file
├── uninstall.php             # Uninstall cleanup
├── assets/
│   ├── css/
│   │   ├── frontend.css      # Frontend styles
│   │   └── admin.css         # Admin styles
│   └── js/
│       ├── frontend.js       # Frontend scripts
│       ├── admin.js          # Admin scripts
│       └── block.js          # Gutenberg block
├── languages/                # Translation files
└── README.md                 # This file
```

## Requirements

- WordPress 5.0 or higher
- PHP 7.2 or higher

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for detailed version history.

### Version 1.0.1 (Latest)

- Improved WordPress Plugin Directory compliance
- Added activation/deactivation hooks
- Enhanced security measures
- Modernized Clipboard API usage
- Added settings link to plugins page
- Improved translation support
- Added security documentation
- Code refactoring and optimization

## Support

For support, feature requests, or bug reports, please visit the plugin repository.

## License

This plugin is licensed under the GPL v2 or later.

## Credits

This plugin uses the Vidinfra Player library: https://www.npmjs.com/package/@vidinfra/player
