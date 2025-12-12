# WordPress Plugin Directory Pre-Submission Checklist

## ✅ COMPLETED ITEMS

- [x] Plugin follows WordPress Coding Standards
- [x] All inputs are properly sanitized
- [x] All outputs are properly escaped
- [x] Text domain is consistent (`vidinfra-player`)
- [x] Plugin is internationalized (i18n ready)
- [x] Direct file access is prevented
- [x] Proper capability checks in place
- [x] GPL v2 or later license
- [x] Uninstall script properly removes data
- [x] No direct database queries (using Options API)
- [x] index.php files in all directories
- [x] readme.txt properly formatted
- [x] Plugin headers complete
- [x] Version numbers match across files

## ⚠️ REQUIRED BEFORE SUBMISSION

### 1. WordPress.org Account Setup

- [ ] Create WordPress.org account if you don't have one
- [ ] Verify your contributor username matches `readme.txt` (currently: `tenbyte`)
- [ ] Update `Contributors:` field in readme.txt if needed

### 2. Assets Required (Create `.wordpress-org/` directory)

- [ ] banner-772x250.png (high-resolution banner)
- [ ] banner-1544x500.png (retina banner - optional but recommended)
- [ ] icon-128x128.png (plugin icon)
- [ ] icon-256x256.png (retina icon)
- [ ] screenshot-1.png (Admin settings page)
- [ ] screenshot-2.png (Gutenberg block editor)
- [ ] screenshot-3.png (Video player embedded on page)
- [ ] screenshot-4.png (Shortcode usage example)

**Note:** Screenshots go in `.wordpress-org/` directory, NOT in the plugin root.

### 3. Third-Party Service Verification

- [ ] Verify https://vidinfra.com is accessible
- [ ] Verify https://vidinfra.com/terms exists and is accurate
- [ ] Verify https://vidinfra.com/privacy exists and is accurate
- [ ] Document that your plugin requires external service (already done ✓)

### 4. Bundled Library Check

- [ ] Ensure `assets/js/vendor/player.global.js` is NOT minified
  - If minified, provide unminified source
  - Or link to NPM package: https://www.npmjs.com/package/@vidinfra/player
- [ ] Verify library license (MIT) is compatible with GPL
- [ ] Consider adding LICENSE file for bundled library

### 5. Testing

- [ ] Test on WordPress 6.4+ (update "Tested up to" field)
- [ ] Test with PHP 7.2, 7.4, 8.0, 8.1, 8.2, 8.3
- [ ] Test with other popular plugins (WooCommerce, Yoast SEO, etc.)
- [ ] Test uninstall process (verify options are removed)
- [ ] Test on multisite installation
- [ ] Test with block themes (FSE)
- [ ] Test with classic themes

### 6. Documentation Updates

- [ ] Verify all URLs in plugin work (plugin URI, author URI)
- [ ] Add FAQ section based on expected questions
- [ ] Add upgrade notice if needed
- [ ] Review CHANGELOG.md for accuracy

### 7. Security Final Check

- [ ] Run security scan (Plugin Check plugin)
- [ ] Verify no SQL injection vulnerabilities
- [ ] Verify no XSS vulnerabilities
- [ ] Verify no CSRF vulnerabilities
- [ ] Check file upload handling (N/A for this plugin)
- [ ] Check remote request handling (N/A for this plugin)

### 8. Code Quality

- [ ] Run PHP CodeSniffer with WordPress standards
  ```bash
  phpcs --standard=WordPress .
  ```
- [ ] Fix any errors or warnings
- [ ] Remove debug code, console.log, var_dump, etc.
- [ ] Remove commented-out code

### 9. Performance

- [ ] Minimize number of database queries
- [ ] Scripts loaded only where needed (conditional loading) ✓
- [ ] No large files loaded unnecessarily
- [ ] Proper script dependencies declared ✓

### 10. Legal & Compliance

- [ ] Ensure you have rights to use "Vidinfra" name
- [ ] Verify trademark compliance
- [ ] Ensure GDPR compliance if collecting user data
- [ ] Privacy policy mentions external service usage ✓

## 📝 KNOWN ISSUES ADDRESSED

### Fixed:

1. ✅ Updated "Tested up to" from 6.7 to 6.4
2. ✅ Added input validation check in sanitize_settings()
3. ✅ Security documentation is complete

### Still Needs Attention:

1. ⚠️ Add plugin assets (.wordpress-org directory)
2. ⚠️ Verify contributor username
3. ⚠️ Test with latest WordPress version
4. ⚠️ Verify bundled library is not minified

## 🚀 SUBMISSION PROCESS

### Step 1: Prepare Plugin ZIP

```bash
cd /Users/ebnsina/Works
zip -r tenbyte-vidinfra-wp.zip tenbyte-vidinfra-wp -x "*.git*" "*.DS_Store" "node_modules/*" "*.wordpress-org/*"
```

### Step 2: Submit to WordPress.org

1. Go to: https://wordpress.org/plugins/developers/add/
2. Upload your ZIP file
3. Fill out the form
4. Wait for review (usually 2-14 days)

### Step 3: After Approval

1. You'll receive SVN repository access
2. Commit your plugin files to SVN
3. Add assets to `.wordpress-org/` directory in SVN
4. Plugin will go live on WordPress.org

## 📧 EXPECTED REVIEW FEEDBACK

WordPress reviewers commonly check for:

- ❌ Minified JavaScript without source maps
- ❌ Direct database queries instead of $wpdb
- ❌ Missing nonce verification
- ❌ Unsanitized input
- ❌ Unescaped output
- ❌ Plugin functionality requires paid service
- ❌ Trademark violations
- ❌ Bundled libraries without proper attribution

Your plugin passes most of these! The bundled player.global.js is properly attributed with NPM source links.

## 💡 RECOMMENDATIONS

1. **Create Demo Site**: Have a live demo ready for reviewers
2. **Documentation**: Create comprehensive documentation on your site
3. **Support Plan**: Be ready to provide support after launch
4. **Update Schedule**: Plan regular updates for WordPress compatibility

## 📚 HELPFUL RESOURCES

- Plugin Handbook: https://developer.wordpress.org/plugins/
- Plugin Review Guidelines: https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/
- Plugin Check Plugin: https://wordpress.org/plugins/plugin-check/
- SVN Guide: https://developer.wordpress.org/plugins/wordpress-org/how-to-use-subversion/
