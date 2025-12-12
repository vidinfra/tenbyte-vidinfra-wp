=== Tenbyte VidInfra ===
Contributors: tenbyte
Donate link: https://vidinfra.com
Tags: video, video player, vidinfra, embed, responsive, watermark, dynamic
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 1.0.1
Requires PHP: 7.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Embed Vidinfra videos easily with shortcode or Gutenberg block support, customizable player settings, and dynamic watermark options.

== Description ==

Tenbyte VidInfra is a lightweight and secure WordPress plugin that allows you to embed Vidinfra videos on your site using a simple shortcode or Gutenberg block. Perfect for video content creators and businesses using Vidinfra's video hosting platform.

= Key Features =

* **Easy Video Embedding** - Simple shortcode and Gutenberg block integration
* **Security First** - All user inputs are sanitized and validated
* **Customizable Player** - Control autoplay, loop, muted, controls, and more
* **Dynamic Watermark Support** - Automatically add watermarks based on logged-in user information (name, email, or user ID)
* **Watermark Customization** - Configure font size, color, and opacity for watermarks
* **Fully Responsive** - Automatic responsive behavior for all devices
* **Tabbed Admin Settings** - Organized settings with General and Dynamic Watermark tabs
* **Gutenberg Block** - Native block editor support
* **Translation Ready** - Full internationalization support
* **Clean Code** - Follows WordPress coding standards

= Usage =

**Basic Shortcode:**

`[vidinfra library_id="1234567" video_id="59777392"]`

**Note:** Both `library_id` and `video_id` are required parameters.

**With Autoplay and Loop:**

`[vidinfra library_id="1234567" video_id="59777392" autoplay="true" loop="true" muted="true"]`

**With Custom Dimensions:**

`[vidinfra library_id="1234567" video_id="59777392" width="800" height="450"]`

= Available Parameters =

* `video_id` (required) - The Vidinfra video ID
* `library_id` (required) - The library ID
* `player_id` (optional) - The player ID (defaults to "default")
* `width` (optional) - Player width (number in pixels or string with unit)
* `height` (optional) - Player height (number in pixels or string with unit)
* `aspect_ratio` (optional) - Aspect ratio (16:9, 4:3, 1:1, 21:9, 9:16, default: 16:9)
* `autoplay` (optional) - Auto-play video (true/false, default: false)
* `loop` (optional) - Loop video (true/false, default: false)
* `muted` (optional) - Mute video (true/false, default: false)
* `controls` (optional) - Show controls (true/false, default: true)
* `preload` (optional) - Preload video (true/false, default: true)
* `loading` (optional) - Loading strategy (lazy/eager, default: eager)
* `class_name` (optional) - Additional CSS classes

= Security Features =

* Input sanitization for all user data
* Output escaping for all content
* Capability checks for admin functions
* XSS and SQL injection prevention
* Secure by design

== Installation ==

1. Upload the `vidinfra-player` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings > Vidinfra Player to configure default settings
4. Use the `[vidinfra]` shortcode or Gutenberg block to embed videos

== Frequently Asked Questions ==

= What is Vidinfra? =

Vidinfra is a video hosting and delivery platform. This plugin allows you to embed Vidinfra videos on your WordPress site.

= Are both library_id and video_id required? =

Yes, both parameters are required for the video player to work properly. You can set a default library_id in the plugin settings.

= Can I use this with Gutenberg? =

Yes! The plugin includes a native Gutenberg block. Just search for "Vidinfra Player" in the block inserter.

= How do I add a watermark? =

Watermarks are configured in the plugin settings under the "Dynamic Watermark" tab. They are automatically applied based on the logged-in user's information.

= Is the player responsive? =

Yes, the Vidinfra player library handles all responsive behavior automatically.

== Screenshots ==

1. Admin settings page
2. Gutenberg block editor
3. Video player embedded on page
4. Shortcode usage example

== Changelog ==

= 1.0.1 =
* Initial public release
* Shortcode support
* Gutenberg block support
* Admin settings page
* Watermark functionality
* Security hardening

== Upgrade Notice ==

= 1.0.1 =
Initial release of Vidinfra Player plugin.

== Third-Party Service ==

This plugin integrates with Vidinfra's video hosting platform. When you embed a video, the player loads content from Vidinfra's servers.

* Vidinfra Website: https://vidinfra.com
* Terms of Service: https://vidinfra.com/terms
* Privacy Policy: https://vidinfra.com/privacy

== Bundled Libraries ==

This plugin includes the following third-party library:

**Vidinfra Player Library**
* Package: @vidinfra/player
* Source: https://www.npmjs.com/package/@vidinfra/player
* License: MIT License (compatible with GPL)
* The bundled version is NOT minified, it's a transpiled build for browser compatibility
* Full source code available at the NPM package link above

== Credits ==

Developed by Vidinfra team.
