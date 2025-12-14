# Changelog

## Version 1.0.0 - 2024-12-14

### Initial Release

Tenbyte VidInfra is a WordPress plugin that seamlessly integrates the Vidinfra video player into your WordPress site with advanced features including dynamic watermarking and flexible embedding options.

#### Core Features

**Video Embedding:**

- Simple shortcode implementation: `[vidinfra video_id="59777392"]`
- Gutenberg block support for visual editing
- Support for both shortcode and block editor workflows
- Clean, minimal HTML output with library-managed iframe

**Configuration Options:**

- Tabbed admin interface for organized settings
- Default library ID and player ID configuration
- Simplified shortcode usage with fallback to admin defaults
- Support for custom player dimensions (width/height)
- Lazy loading support for performance optimization
- Custom CSS class names for styling flexibility

**Dynamic Watermark System:**

- Automatic watermarking based on logged-in user data
- Watermark options: User Name, User Email, or User ID
- Customizable font size (10px - 16px)
- Color picker for watermark color (default: white #ffffff)
- Opacity control slider (0-100%, default 30%)
- Full integration with Vidinfra player watermark API

**WordPress Integration:**

- WordPress Coding Standards compliant
- Full internationalization (i18n) support
- Translation-ready with text domain: `vidinfra-player`
- Proper input sanitization and output escaping
- Capability checks for security
- Clean uninstallation process
- Settings link on plugins page

**Developer-Friendly:**

- GPL v2 or later licensed
- Well-documented codebase
- Index.php protection in all directories
- Modern JavaScript (Clipboard API)
- WordPress Color Picker integration
- Follows WordPress best practices

#### Shortcode Parameters

- `video_id` (required): The video ID from Vidinfra
- `library_id` (optional): Override default library ID
- `player_id` (optional): Custom player configuration
- `width` (optional): Custom player width
- `height` (optional): Custom player height
- `loading` (optional): "lazy" or "eager" loading strategy
- `class_name` (optional): Additional CSS classes

#### Example Usage

**Basic usage:**

```
[vidinfra video_id="59777392"]
```

**With custom settings:**

```
[vidinfra
    video_id="59777392"
    library_id="1234567"
    player_id="custom-player"
    width="800"
    height="450"
    loading="lazy"
    class_name="featured-video"]
```

#### Requirements

- WordPress 5.0 or higher
- PHP 7.2 or higher
- Active Vidinfra account and library ID

#### Third-Party Service

This plugin connects to Vidinfra (https://vidinfra.com) to load the video player library and stream video content. By using this plugin, you agree to Vidinfra's terms of service and privacy policy:

- Terms: https://vidinfra.com/terms
- Privacy: https://vidinfra.com/privacy
