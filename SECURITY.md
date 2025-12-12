# Security Policy

## Supported Versions

Currently supported versions with security updates:

| Version | Supported          |
| ------- | ------------------ |
| 1.0.x   | :white_check_mark: |

## Reporting a Vulnerability

If you discover a security vulnerability within Vidinfra Player, please send an email to security@vidinfra.com. All security vulnerabilities will be promptly addressed.

Please do not publicly disclose the issue until it has been addressed by the team.

## Security Measures

The Vidinfra Player plugin implements the following security measures:

### Input Sanitization
- All user inputs are sanitized using WordPress sanitization functions
- `sanitize_text_field()` for text inputs
- `sanitize_html_class()` for CSS classes
- `intval()` and `floatval()` for numeric values
- Boolean validation using `filter_var()`

### Output Escaping
- All output is escaped using appropriate WordPress functions:
  - `esc_html()` for HTML content
  - `esc_attr()` for HTML attributes
  - `esc_url()` for URLs
  - `esc_js()` for JavaScript strings
  - `wp_json_encode()` for JSON data

### Capability Checks
- Admin functions require `manage_options` capability
- Settings are only accessible to authorized users

### XSS Prevention
- All dynamic content is properly escaped
- No direct output of user input
- JavaScript variables properly encoded

### SQL Injection Prevention
- Uses WordPress Options API (no direct database queries)
- All data stored through `update_option()` and retrieved via `get_option()`

### CSRF Protection
- WordPress nonce verification for all form submissions
- Settings forms use `settings_fields()` for automatic nonce handling

### Direct File Access Prevention
- All PHP files check for `ABSPATH` or `WP_UNINSTALL_PLUGIN` constant
- Exit immediately if accessed directly

### Best Practices
- Follows WordPress Coding Standards
- Uses WordPress APIs exclusively
- No use of deprecated functions
- Proper text domain for translations
- Clean uninstallation process
