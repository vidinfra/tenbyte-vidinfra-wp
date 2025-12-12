<?php
/**
 * Plugin Name: Tenbyte VidInfra
 * Plugin URI: https://tenbyte.io
 * Description: A secure WordPress plugin to embed Vidinfra video player with iframe support, dynamic watermark options, and customizable settings.
 * Version: 1.0.1
 * Author: Tenbyte
 * Author URI: https://tenbyte.io
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: vidinfra-player
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.2
 *
 * @package VidinfraPlayer
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('VIDINFRA_PLAYER_VERSION', '1.0.1');
define('VIDINFRA_PLAYER_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('VIDINFRA_PLAYER_PLUGIN_URL', plugin_dir_url(__FILE__));
define('VIDINFRA_PLAYER_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main plugin class
 */
class Vidinfra_Player {
    
    /**
     * Instance of this class
     *
     * @var object
     */
    private static $instance = null;
    
    /**
     * Get instance of the class
     *
     * @return object
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->init_hooks();
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Load text domain
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        
        // Register scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        
        // Register shortcode
        add_shortcode('vidinfra', array($this, 'render_shortcode'));
        add_shortcode('vidinfra_player', array($this, 'render_shortcode')); // Legacy support
        
        // Admin menu
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        
        // Gutenberg block
        add_action('init', array($this, 'register_gutenberg_block'));
        
        // Add settings link to plugins page
        add_filter('plugin_action_links_' . VIDINFRA_PLAYER_PLUGIN_BASENAME, array($this, 'add_settings_link'));
    }
    
    /**
     * Load plugin textdomain
     */
    public function load_textdomain() {
        load_plugin_textdomain('vidinfra-player', false, dirname(VIDINFRA_PLAYER_PLUGIN_BASENAME) . '/languages');
    }
    
    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        // Enqueue bundled Vidinfra Player library
        // Library: @vidinfra/player (MIT License)
        // NPM: https://www.npmjs.com/package/@vidinfra/player
        // This is a bundled (not minified) version for compatibility
        wp_enqueue_script(
            'vidinfra-player-lib',
            VIDINFRA_PLAYER_PLUGIN_URL . 'assets/js/vendor/player.global.js',
            array(),
            VIDINFRA_PLAYER_VERSION,
            true
        );
        
        // Enqueue frontend styles
        wp_enqueue_style(
            'vidinfra-player-frontend',
            VIDINFRA_PLAYER_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            VIDINFRA_PLAYER_VERSION
        );
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        // Only load on our admin page
        if ('settings_page_vidinfra-player-settings' !== $hook) {
            return;
        }
        
        // Enqueue WordPress color picker
        wp_enqueue_style('wp-color-picker');
        
        wp_enqueue_style(
            'vidinfra-player-admin',
            VIDINFRA_PLAYER_PLUGIN_URL . 'assets/css/admin.css',
            array('wp-color-picker'),
            VIDINFRA_PLAYER_VERSION
        );
        
        wp_enqueue_script(
            'vidinfra-player-admin',
            VIDINFRA_PLAYER_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery', 'wp-color-picker'),
            VIDINFRA_PLAYER_VERSION,
            true
        );
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_options_page(
            __('Tenbyte VidInfra Settings', 'vidinfra-player'),
            __('Tenbyte VidInfra', 'vidinfra-player'),
            'manage_options',
            'vidinfra-player-settings',
            array($this, 'render_admin_page')
        );
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        register_setting(
            'vidinfra_player_settings',
            'vidinfra_player_options',
            array($this, 'sanitize_settings')
        );
        
        // General Settings Section
        add_settings_section(
            'vidinfra_player_general',
            __('General Settings', 'vidinfra-player'),
            array($this, 'render_general_section'),
            'vidinfra-player-settings-general'
        );
        
        // Default Library ID
        add_settings_field(
            'default_library_id',
            __('Default Library ID', 'vidinfra-player'),
            array($this, 'render_library_id_field'),
            'vidinfra-player-settings-general',
            'vidinfra_player_general'
        );
        
        // Default Player ID
        add_settings_field(
            'default_player_id',
            __('Default Player ID', 'vidinfra-player'),
            array($this, 'render_player_id_field'),
            'vidinfra-player-settings-general',
            'vidinfra_player_general'
        );
        
        // Dynamic Watermark Settings Section
        add_settings_section(
            'vidinfra_player_watermark',
            __('Dynamic Watermark Settings', 'vidinfra-player'),
            array($this, 'render_watermark_section'),
            'vidinfra-player-settings-watermark'
        );
        
        // Enable Watermark Checkbox
        add_settings_field(
            'watermark_enable',
            __('Enable Watermark', 'vidinfra-player'),
            array($this, 'render_watermark_enable_field'),
            'vidinfra-player-settings-watermark',
            'vidinfra_player_watermark'
        );
        
        // Line 1 Heading
        add_settings_field(
            'watermark_line1_heading',
            __('Line 1:', 'vidinfra-player'),
            array($this, 'render_line1_heading'),
            'vidinfra-player-settings-watermark',
            'vidinfra_player_watermark'
        );
        
        // Line 1: Watermark Text Field
        add_settings_field(
            'watermark_text_field',
            __('Watermark Text', 'vidinfra-player'),
            array($this, 'render_watermark_text_field'),
            'vidinfra-player-settings-watermark',
            'vidinfra_player_watermark'
        );
        
        // Line 1: Watermark Font Size
        add_settings_field(
            'watermark_font_size',
            __('Font Size', 'vidinfra-player'),
            array($this, 'render_watermark_font_size_field'),
            'vidinfra-player-settings-watermark',
            'vidinfra_player_watermark'
        );
        
        // Line 1: Watermark Font Color
        add_settings_field(
            'watermark_font_color',
            __('Font Color', 'vidinfra-player'),
            array($this, 'render_watermark_font_color_field'),
            'vidinfra-player-settings-watermark',
            'vidinfra_player_watermark'
        );
        
        // Line 1: Watermark Font Opacity
        add_settings_field(
            'watermark_font_opacity',
            __('Font Opacity', 'vidinfra-player'),
            array($this, 'render_watermark_font_opacity_field'),
            'vidinfra-player-settings-watermark',
            'vidinfra_player_watermark'
        );
        
        // Enable Line 2 Checkbox
        add_settings_field(
            'watermark_enable_line2',
            __('Add Second Line', 'vidinfra-player'),
            array($this, 'render_watermark_enable_line2_field'),
            'vidinfra-player-settings-watermark',
            'vidinfra_player_watermark'
        );
        
        // Line 2 Divider (no label, just divider)
        add_settings_field(
            'watermark_line2_divider',
            '',
            array($this, 'render_line2_divider'),
            'vidinfra-player-settings-watermark',
            'vidinfra_player_watermark'
        );
        
        // Line 2 Heading
        add_settings_field(
            'watermark_line2_heading',
            __('Line 2:', 'vidinfra-player'),
            array($this, 'render_line2_heading'),
            'vidinfra-player-settings-watermark',
            'vidinfra_player_watermark'
        );
        
        // Line 2: Watermark Text Field
        add_settings_field(
            'watermark_text_field_line2',
            __('Watermark Text', 'vidinfra-player'),
            array($this, 'render_watermark_text_field_line2'),
            'vidinfra-player-settings-watermark',
            'vidinfra_player_watermark'
        );
        
        // Line 2: Watermark Font Size
        add_settings_field(
            'watermark_font_size_line2',
            __('Font Size', 'vidinfra-player'),
            array($this, 'render_watermark_font_size_field_line2'),
            'vidinfra-player-settings-watermark',
            'vidinfra_player_watermark'
        );
        
        // Line 2: Watermark Font Color
        add_settings_field(
            'watermark_font_color_line2',
            __('Font Color', 'vidinfra-player'),
            array($this, 'render_watermark_font_color_field_line2'),
            'vidinfra-player-settings-watermark',
            'vidinfra_player_watermark'
        );
        
        // Line 2: Watermark Font Opacity
        add_settings_field(
            'watermark_font_opacity_line2',
            __('Font Opacity', 'vidinfra-player'),
            array($this, 'render_watermark_font_opacity_field_line2'),
            'vidinfra-player-settings-watermark',
            'vidinfra_player_watermark'
        );
    }
    
    /**
     * Sanitize settings
     */
    public function sanitize_settings($input) {
        // Get existing options to preserve values from other tabs
        $existing_options = get_option('vidinfra_player_options', array());
        $sanitized = $existing_options;
        
        // Additional nonce verification (settings_fields() already handles this, but being explicit)
        // WordPress automatically verifies the nonce via settings_fields(), but we check for security
        if (!is_array($input)) {
            return $sanitized;
        }
        
        // Sanitize General tab settings
        if (isset($input['default_library_id'])) {
            // Library ID can be string or number
            $library_id = sanitize_text_field($input['default_library_id']);
            if (empty($library_id)) {
                add_settings_error(
                    'vidinfra_player_options',
                    'library_id_required',
                    __('Library ID is required.', 'vidinfra-player'),
                    'error'
                );
            } else {
                $sanitized['default_library_id'] = $library_id;
            }
        }
        
        if (isset($input['default_player_id'])) {
            // Player ID can be string or number
            $sanitized['default_player_id'] = sanitize_text_field($input['default_player_id']);
        }
        
        if (isset($input['default_aspect_ratio'])) {
            $sanitized['default_aspect_ratio'] = sanitize_text_field($input['default_aspect_ratio']);
        }
        
        // Sanitize watermark settings
        // Enable watermark checkbox
        $sanitized['watermark_enable'] = isset($input['watermark_enable']) ? 1 : 0;
        
        // Enable Line 2 checkbox
        $sanitized['watermark_enable_line2'] = isset($input['watermark_enable_line2']) ? 1 : 0;
        
        if (isset($input['watermark_text_field'])) {
            $sanitized['watermark_text_field'] = sanitize_text_field($input['watermark_text_field']);
        }
        
        if (isset($input['watermark_font_size'])) {
            $font_size = intval($input['watermark_font_size']);
            $allowed_sizes = array(10, 11, 12, 14, 15, 16);
            $sanitized['watermark_font_size'] = in_array($font_size, $allowed_sizes) ? $font_size : 10;
        }
        
        if (isset($input['watermark_font_color'])) {
            // Validate hex color
            $color = sanitize_text_field($input['watermark_font_color']);
            if (preg_match('/^#[a-f0-9]{6}$/i', $color)) {
                $sanitized['watermark_font_color'] = $color;
            } else {
                $sanitized['watermark_font_color'] = '#ffffff';
            }
        }
        
        if (isset($input['watermark_font_opacity'])) {
            $opacity = intval($input['watermark_font_opacity']);
            $sanitized['watermark_font_opacity'] = max(0, min(100, $opacity));
        }
        
        // Sanitize Line 2 watermark settings
        if (isset($input['watermark_text_field_line2'])) {
            $sanitized['watermark_text_field_line2'] = sanitize_text_field($input['watermark_text_field_line2']);
        }
        
        if (isset($input['watermark_font_size_line2'])) {
            $font_size = intval($input['watermark_font_size_line2']);
            $allowed_sizes = array(10, 11, 12, 14, 15, 16);
            $sanitized['watermark_font_size_line2'] = in_array($font_size, $allowed_sizes) ? $font_size : 10;
        }
        
        if (isset($input['watermark_font_color_line2'])) {
            // Validate hex color
            $color = sanitize_text_field($input['watermark_font_color_line2']);
            if (preg_match('/^#[a-f0-9]{6}$/i', $color)) {
                $sanitized['watermark_font_color_line2'] = $color;
            } else {
                $sanitized['watermark_font_color_line2'] = '#ffffff';
            }
        }
        
        if (isset($input['watermark_font_opacity_line2'])) {
            $opacity = intval($input['watermark_font_opacity_line2']);
            $sanitized['watermark_font_opacity_line2'] = max(0, min(100, $opacity));
        }
        
        return $sanitized;
    }
    
    /**
     * Render general section
     */
    public function render_general_section() {
        echo '<p>' . esc_html__('Configure default settings for the Tenbyte VidInfra Player.', 'vidinfra-player') . '</p>';
    }
    
    /**
     * Render watermark section
     */
    public function render_watermark_section() {
        echo '<p>' . esc_html__('Configure dynamic watermark settings. The watermark text will be dynamically generated based on logged-in user information.', 'vidinfra-player') . '</p>';
    }
    
    /**
     * Render watermark enable checkbox
     */
    public function render_watermark_enable_field() {
        $options = get_option('vidinfra_player_options', array());
        $checked = isset($options['watermark_enable']) && $options['watermark_enable'] ? 'checked' : '';
        ?>
        <label>
            <input type="checkbox" 
                   name="vidinfra_player_options[watermark_enable]" 
                   id="watermark_enable"
                   value="1" 
                   <?php echo $checked; ?> />
            <?php esc_html_e('Enable dynamic watermark on videos', 'vidinfra-player'); ?>
        </label>
        <p class="description">
            <?php esc_html_e('Check this box to enable watermark functionality.', 'vidinfra-player'); ?>
        </p>
        <?php
    }
    
    /**
     * Render watermark enable Line 2 checkbox
     */
    public function render_watermark_enable_line2_field() {
        $options = get_option('vidinfra_player_options', array());
        $checked = isset($options['watermark_enable_line2']) && $options['watermark_enable_line2'] ? 'checked' : '';
        ?>
        <label>
            <input type="checkbox" 
                   name="vidinfra_player_options[watermark_enable_line2]" 
                   id="watermark_enable_line2"
                   value="1" 
                   <?php echo $checked; ?> />
            <?php esc_html_e('Add a second watermark line', 'vidinfra-player'); ?>
        </label>
        <p class="description">
            <?php esc_html_e('Check this box to add a second line of watermark text.', 'vidinfra-player'); ?>
        </p>
        <?php
    }
    
    /**
     * Render watermark text field
     */
    public function render_watermark_text_field() {
        $options = get_option('vidinfra_player_options', array());
        $value = isset($options['watermark_text_field']) ? $options['watermark_text_field'] : 'name';
        ?>
        <select name="vidinfra_player_options[watermark_text_field]" id="watermark_text_field">
            <option value="name" <?php selected($value, 'name'); ?>><?php esc_html_e('User Name', 'vidinfra-player'); ?></option>
            <option value="email" <?php selected($value, 'email'); ?>><?php esc_html_e('User Email', 'vidinfra-player'); ?></option>
            <option value="user_id" <?php selected($value, 'user_id'); ?>><?php esc_html_e('User ID', 'vidinfra-player'); ?></option>
        </select>
        <p class="description">
            <?php esc_html_e('Select which user information to display as watermark text.', 'vidinfra-player'); ?>
        </p>
        <?php
    }
    
    /**
     * Render watermark font size field
     */
    public function render_watermark_font_size_field() {
        $options = get_option('vidinfra_player_options', array());
        $value = isset($options['watermark_font_size']) ? $options['watermark_font_size'] : 10;
        $sizes = array(10, 11, 12, 14, 15, 16);
        ?>
        <select name="vidinfra_player_options[watermark_font_size]" id="watermark_font_size">
            <?php foreach ($sizes as $size) : ?>
                <option value="<?php echo esc_attr($size); ?>" <?php selected($value, $size); ?>>
                    <?php echo esc_html($size . 'px'); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p class="description">
            <?php esc_html_e('Select the font size for the watermark text.', 'vidinfra-player'); ?>
        </p>
        <?php
    }
    
    /**
     * Render watermark font color field
     */
    public function render_watermark_font_color_field() {
        $options = get_option('vidinfra_player_options', array());
        $value = isset($options['watermark_font_color']) ? $options['watermark_font_color'] : '#ffffff';
        ?>
        <input type="text" 
               name="vidinfra_player_options[watermark_font_color]" 
               id="watermark_font_color"
               value="<?php echo esc_attr($value); ?>" 
               class="vidinfra-color-picker" 
               data-default-color="#ffffff" />
        <p class="description">
            <?php esc_html_e('Select the font color for the watermark text.', 'vidinfra-player'); ?>
        </p>
        <?php
    }
    
    /**
     * Render watermark font opacity field
     */
    public function render_watermark_font_opacity_field() {
        $options = get_option('vidinfra_player_options', array());
        $value = isset($options['watermark_font_opacity']) ? $options['watermark_font_opacity'] : 25;
        ?>
        <input type="number" 
               name="vidinfra_player_options[watermark_font_opacity]" 
               id="watermark_font_opacity"
               value="<?php echo esc_attr($value); ?>" 
               min="0" 
               max="100" 
               step="1" 
               class="small-text" />
        <span>%</span>
        <p class="description">
            <?php esc_html_e('Set the opacity for the watermark text (0-100%).', 'vidinfra-player'); ?>
        </p>
        <?php
    }
    
    /**
     * Render Line 1 heading
     */
    public function render_line1_heading() {
        echo '<strong style="font-size: 14px;">' . esc_html__('First watermark line', 'vidinfra-player') . '</strong>';
    }
    
    /**
     * Render Line 2 divider
     */
    public function render_line2_divider() {
        echo '</td></tr><tr><td colspan="2" style="padding: 0;"><hr style="border: none; border-top: 1px solid #dcdcde; margin: 30px 0;" /></td></tr>';
    }
    
    /**
     * Render Line 2 heading
     */
    public function render_line2_heading() {
        echo '<strong style="font-size: 14px;">' . esc_html__('Second watermark line', 'vidinfra-player') . '</strong>';
    }
    
    /**
     * Render Line 2 watermark text field
     */
    public function render_watermark_text_field_line2() {
        $options = get_option('vidinfra_player_options', array());
        $value = isset($options['watermark_text_field_line2']) ? $options['watermark_text_field_line2'] : 'name';
        ?>
        <select name="vidinfra_player_options[watermark_text_field_line2]" id="watermark_text_field_line2">
            <option value="name" <?php selected($value, 'name'); ?>><?php esc_html_e('User Name', 'vidinfra-player'); ?></option>
            <option value="email" <?php selected($value, 'email'); ?>><?php esc_html_e('User Email', 'vidinfra-player'); ?></option>
            <option value="user_id" <?php selected($value, 'user_id'); ?>><?php esc_html_e('User ID', 'vidinfra-player'); ?></option>
        </select>
        <p class="description">
            <?php esc_html_e('Select which user information to display as watermark text.', 'vidinfra-player'); ?>
        </p>
        <?php
    }
    
    /**
     * Render Line 2 watermark font size field
     */
    public function render_watermark_font_size_field_line2() {
        $options = get_option('vidinfra_player_options', array());
        $value = isset($options['watermark_font_size_line2']) ? $options['watermark_font_size_line2'] : 10;
        $sizes = array(10, 11, 12, 14, 15, 16);
        ?>
        <select name="vidinfra_player_options[watermark_font_size_line2]" id="watermark_font_size_line2">
            <?php foreach ($sizes as $size) : ?>
                <option value="<?php echo esc_attr($size); ?>" <?php selected($value, $size); ?>>
                    <?php echo esc_html($size . 'px'); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p class="description">
            <?php esc_html_e('Select the font size for the watermark text.', 'vidinfra-player'); ?>
        </p>
        <?php
    }
    
    /**
     * Render Line 2 watermark font color field
     */
    public function render_watermark_font_color_field_line2() {
        $options = get_option('vidinfra_player_options', array());
        $value = isset($options['watermark_font_color_line2']) ? $options['watermark_font_color_line2'] : '#ffffff';
        ?>
        <input type="text" 
               name="vidinfra_player_options[watermark_font_color_line2]" 
               id="watermark_font_color_line2"
               value="<?php echo esc_attr($value); ?>" 
               class="vidinfra-color-picker" 
               data-default-color="#ffffff" />
        <p class="description">
            <?php esc_html_e('Select the font color for the watermark text.', 'vidinfra-player'); ?>
        </p>
        <?php
    }
    
    /**
     * Render Line 2 watermark font opacity field
     */
    public function render_watermark_font_opacity_field_line2() {
        $options = get_option('vidinfra_player_options', array());
        $value = isset($options['watermark_font_opacity_line2']) ? $options['watermark_font_opacity_line2'] : 25;
        ?>
        <input type="number" 
               name="vidinfra_player_options[watermark_font_opacity_line2]" 
               id="watermark_font_opacity_line2"
               value="<?php echo esc_attr($value); ?>" 
               min="0" 
               max="100" 
               step="1" 
               class="small-text" />
        <span>%</span>
        <p class="description">
            <?php esc_html_e('Set the opacity for the watermark text (0-100%).', 'vidinfra-player'); ?>
        </p>
        <?php
    }
    
    /**
     * Render library ID field
     */
    public function render_library_id_field() {
        $options = get_option('vidinfra_player_options', array());
        $value = isset($options['default_library_id']) ? $options['default_library_id'] : '';
        ?>
        <input type="text" 
               name="vidinfra_player_options[default_library_id]" 
               value="<?php echo esc_attr($value); ?>" 
               class="regular-text" 
               required />
        <p class="description">
            <?php esc_html_e('Enter your default Vidinfra library ID (required).', 'vidinfra-player'); ?>
        </p>
        <?php
    }
    
    /**
     * Render player ID field
     */
    public function render_player_id_field() {
        $options = get_option('vidinfra_player_options', array());
        $value = isset($options['default_player_id']) ? $options['default_player_id'] : '';
        ?>
        <input type="text" 
               name="vidinfra_player_options[default_player_id]" 
               value="<?php echo esc_attr($value); ?>" 
               class="regular-text" 
               placeholder="default" />
        <p class="description">
            <?php esc_html_e('Enter your default Vidinfra player ID (optional, defaults to "default").', 'vidinfra-player'); ?>
        </p>
        <?php
    }
    
    /**
     * Render aspect ratio field
     */
    public function render_aspect_ratio_field() {
        $options = get_option('vidinfra_player_options', array());
        $value = isset($options['default_aspect_ratio']) ? $options['default_aspect_ratio'] : '16:9';
        $ratios = array('16:9', '4:3', '1:1', '21:9');
        ?>
        <select name="vidinfra_player_options[default_aspect_ratio]">
            <?php foreach ($ratios as $ratio) : ?>
                <option value="<?php echo esc_attr($ratio); ?>" <?php selected($value, $ratio); ?>>
                    <?php echo esc_html($ratio); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p class="description">
            <?php esc_html_e('Select the default aspect ratio for videos.', 'vidinfra-player'); ?>
        </p>
        <?php
    }
    
    /**
     * Render admin page
     */
    public function render_admin_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // Get current tab
        $current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';
        ?>
        <div class="wrap">
            <div style="margin: 20px 0;">
                <img src="<?php echo esc_url(VIDINFRA_PLAYER_PLUGIN_URL . 'assets/images/logo.png'); ?>" alt="Tenbyte VidInfra" style="max-width: 200px; height: auto;" />
            </div>
            
            <!-- Tab Navigation -->
            <nav class="nav-tab-wrapper">
                <a href="?page=vidinfra-player-settings&tab=general" 
                   class="nav-tab <?php echo $current_tab === 'general' ? 'nav-tab-active' : ''; ?>">
                    <?php esc_html_e('General', 'vidinfra-player'); ?>
                </a>
                <a href="?page=vidinfra-player-settings&tab=watermark" 
                   class="nav-tab <?php echo $current_tab === 'watermark' ? 'nav-tab-active' : ''; ?>">
                    <?php esc_html_e('Dynamic Watermark', 'vidinfra-player'); ?>
                </a>
            </nav>
            
            <div class="vidinfra-player-admin-wrapper">
                <div class="vidinfra-player-admin-content">
                    <form method="post" action="options.php">
                        <?php
                        settings_fields('vidinfra_player_settings');
                        
                        // Get existing options to preserve values from inactive tab
                        $options = get_option('vidinfra_player_options', array());
                        
                        if ($current_tab === 'general') {
                            // Include hidden fields for watermark settings - Enable checkboxes
                            if (isset($options['watermark_enable'])) {
                                echo '<input type="hidden" name="vidinfra_player_options[watermark_enable]" value="' . esc_attr($options['watermark_enable']) . '" />';
                            }
                            if (isset($options['watermark_enable_line2'])) {
                                echo '<input type="hidden" name="vidinfra_player_options[watermark_enable_line2]" value="' . esc_attr($options['watermark_enable_line2']) . '" />';
                            }
                            // Include hidden fields for watermark settings - Line 1
                            if (isset($options['watermark_text_field'])) {
                                echo '<input type="hidden" name="vidinfra_player_options[watermark_text_field]" value="' . esc_attr($options['watermark_text_field']) . '" />';
                            }
                            if (isset($options['watermark_font_size'])) {
                                echo '<input type="hidden" name="vidinfra_player_options[watermark_font_size]" value="' . esc_attr($options['watermark_font_size']) . '" />';
                            }
                            if (isset($options['watermark_font_color'])) {
                                echo '<input type="hidden" name="vidinfra_player_options[watermark_font_color]" value="' . esc_attr($options['watermark_font_color']) . '" />';
                            }
                            if (isset($options['watermark_font_opacity'])) {
                                echo '<input type="hidden" name="vidinfra_player_options[watermark_font_opacity]" value="' . esc_attr($options['watermark_font_opacity']) . '" />';
                            }
                            // Include hidden fields for watermark settings - Line 2
                            if (isset($options['watermark_text_field_line2'])) {
                                echo '<input type="hidden" name="vidinfra_player_options[watermark_text_field_line2]" value="' . esc_attr($options['watermark_text_field_line2']) . '" />';
                            }
                            if (isset($options['watermark_font_size_line2'])) {
                                echo '<input type="hidden" name="vidinfra_player_options[watermark_font_size_line2]" value="' . esc_attr($options['watermark_font_size_line2']) . '" />';
                            }
                            if (isset($options['watermark_font_color_line2'])) {
                                echo '<input type="hidden" name="vidinfra_player_options[watermark_font_color_line2]" value="' . esc_attr($options['watermark_font_color_line2']) . '" />';
                            }
                            if (isset($options['watermark_font_opacity_line2'])) {
                                echo '<input type="hidden" name="vidinfra_player_options[watermark_font_opacity_line2]" value="' . esc_attr($options['watermark_font_opacity_line2']) . '" />';
                            }
                            
                            do_settings_sections('vidinfra-player-settings-general');
                        } elseif ($current_tab === 'watermark') {
                            // Include hidden fields for general settings
                            if (isset($options['default_library_id'])) {
                                echo '<input type="hidden" name="vidinfra_player_options[default_library_id]" value="' . esc_attr($options['default_library_id']) . '" />';
                            }
                            if (isset($options['default_player_id'])) {
                                echo '<input type="hidden" name="vidinfra_player_options[default_player_id]" value="' . esc_attr($options['default_player_id']) . '" />';
                            }
                            if (isset($options['default_aspect_ratio'])) {
                                echo '<input type="hidden" name="vidinfra_player_options[default_aspect_ratio]" value="' . esc_attr($options['default_aspect_ratio']) . '" />';
                            }
                            
                            do_settings_sections('vidinfra-player-settings-watermark');
                        }
                        
                        submit_button();
                        ?>
                    </form>
                    
                    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #dcdcde;">
                        <p style="color: #666; font-size: 14px;">
                            <?php esc_html_e('This plugin is powered by', 'vidinfra-player'); ?> 
                            <a href="https://tenbyte.io" target="_blank" rel="noopener noreferrer" style="color: #2271b1; text-decoration: none; font-weight: 600;">Tenbyte</a>
                        </p>
                    </div>
                </div>
                
                <div class="vidinfra-player-admin-sidebar">
                    <div class="vidinfra-player-sidebar-box">
                        <h3><?php esc_html_e('Shortcode Usage', 'vidinfra-player'); ?></h3>
                        <p><?php esc_html_e('Use the following shortcode to embed a video:', 'vidinfra-player'); ?></p>
                        <code>[vidinfra video_id="59777392"]</code>
                        
                        <h4><?php esc_html_e('Example with Options:', 'vidinfra-player'); ?></h4>
                        <code>[vidinfra video_id="59777392" autoplay="true" loop="true" muted="true"]</code>
                        
                        <h4><?php esc_html_e('Available Parameters:', 'vidinfra-player'); ?></h4>
                        <ul>
                            <li><code>video_id</code> - <?php esc_html_e('(Required) Video ID', 'vidinfra-player'); ?></li>
                            <li><code>library_id</code> - <?php esc_html_e('(Optional) Library ID - uses default from settings if not provided', 'vidinfra-player'); ?></li>
                            <li><code>player_id</code> - <?php esc_html_e('(Optional) Player ID - defaults to "default"', 'vidinfra-player'); ?></li>
                            <li><code>autoplay</code> - <?php esc_html_e('"true" or "false" (default: false)', 'vidinfra-player'); ?></li>
                            <li><code>loop</code> - <?php esc_html_e('"true" or "false" (default: false)', 'vidinfra-player'); ?></li>
                            <li><code>muted</code> - <?php esc_html_e('"true" or "false" (default: false)', 'vidinfra-player'); ?></li>
                            <li><code>controls</code> - <?php esc_html_e('"true" or "false" (default: true)', 'vidinfra-player'); ?></li>
                            <li><code>preload</code> - <?php esc_html_e('"true" or "false" (default: true)', 'vidinfra-player'); ?></li>
                            <li><code>aspect_ratio</code> - <?php esc_html_e('"16:9", "4:3", "1:1", "21:9", "9:16"', 'vidinfra-player'); ?></li>
                            <li><code>width</code> - <?php esc_html_e('Width (e.g., "800" or "100%")', 'vidinfra-player'); ?></li>
                            <li><code>height</code> - <?php esc_html_e('Height (e.g., "450" or "auto")', 'vidinfra-player'); ?></li>
                            <li><code>loading</code> - <?php esc_html_e('"lazy" or "eager" (default: eager)', 'vidinfra-player'); ?></li>
                            <li><code>class_name</code> - <?php esc_html_e('Additional CSS classes', 'vidinfra-player'); ?></li>
                        </ul>
                        
                        <h4><?php esc_html_e('Dynamic Watermark:', 'vidinfra-player'); ?></h4>
                        <p><?php esc_html_e('Watermark is automatically applied based on the settings in the Dynamic Watermark tab.', 'vidinfra-player'); ?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render shortcode
     */
    public function render_shortcode($atts) {
        $defaults = array(
            'video_id' => '',
            'library_id' => '',
            'player_id' => '',
            'width' => '',
            'height' => '',
            'autoplay' => 'false',
            'loop' => 'false',
            'muted' => 'false',
            'controls' => 'true',
            'preload' => 'true',
            'aspect_ratio' => '16:9',
            'loading' => 'eager',
            'class_name' => '',
        );
        
        $atts = shortcode_atts($defaults, $atts, 'vidinfra_player');
        
        // Sanitize video_id (can be string or number)
        $video_id = sanitize_text_field($atts['video_id']);
        
        // Video ID is required
        if (empty($video_id)) {
            return '<p class="vidinfra-error">' . esc_html__('Error: video_id is required.', 'vidinfra-player') . '</p>';
        }
        
        // Get default options
        $options = get_option('vidinfra_player_options', array());
        
        // Sanitize library_id (can be string or number) - Use shortcode value if provided, otherwise use default from settings
        $library_id = !empty($atts['library_id']) ? sanitize_text_field($atts['library_id']) : (isset($options['default_library_id']) ? $options['default_library_id'] : '');
        
        // Library ID is required (either from shortcode or admin settings)
        if (empty($library_id)) {
            return '<p class="vidinfra-error">' . esc_html__('Error: library_id is required. Please set a default library ID in admin settings or provide it in the shortcode.', 'vidinfra-player') . '</p>';
        }
        
        // Sanitize player_id (can be string or number, defaults to 'default')
        $player_id = !empty($atts['player_id']) ? sanitize_text_field($atts['player_id']) : (isset($options['default_player_id']) && !empty($options['default_player_id']) ? $options['default_player_id'] : 'default');
        
        // Sanitize width and height (can be number or string)
        $width = !empty($atts['width']) ? sanitize_text_field($atts['width']) : '';
        $height = !empty($atts['height']) ? sanitize_text_field($atts['height']) : '';
        
        // Sanitize boolean values
        $autoplay = filter_var($atts['autoplay'], FILTER_VALIDATE_BOOLEAN);
        $loop = filter_var($atts['loop'], FILTER_VALIDATE_BOOLEAN);
        $muted = filter_var($atts['muted'], FILTER_VALIDATE_BOOLEAN);
        $controls = filter_var($atts['controls'], FILTER_VALIDATE_BOOLEAN);
        $preload = filter_var($atts['preload'], FILTER_VALIDATE_BOOLEAN);
        
        // Sanitize aspect ratio
        $allowed_ratios = array('16:9', '4:3', '1:1', '21:9', '9:16');
        $aspect_ratio = in_array($atts['aspect_ratio'], $allowed_ratios) ? $atts['aspect_ratio'] : '16:9';
        
        // Sanitize loading attribute
        $loading = in_array($atts['loading'], array('lazy', 'eager')) ? $atts['loading'] : 'eager';
        
        // Sanitize className
        $class_name = !empty($atts['class_name']) ? sanitize_html_class($atts['class_name']) : '';
        
        // Generate unique player ID
        $unique_id = 'vidinfra-player-' . wp_generate_password(8, false);
        
        // Prepare configuration - libraryId, videoId, and playerId are required/default
        $config = array(
            'libraryId' => is_numeric($library_id) ? intval($library_id) : $library_id,
            'videoId' => is_numeric($video_id) ? intval($video_id) : $video_id,
            'playerId' => is_numeric($player_id) ? intval($player_id) : $player_id,
            'autoplay' => $autoplay,
            'loop' => $loop,
            'muted' => $muted,
            'controls' => $controls,
            'preload' => $preload,
            'aspectRatio' => $aspect_ratio,
            'loading' => $loading,
        );
        
        // Add optional width/height if specified
        if (!empty($width)) {
            $config['width'] = is_numeric($width) ? intval($width) : $width;
        }
        
        if (!empty($height)) {
            $config['height'] = is_numeric($height) ? intval($height) : $height;
        }
        
        // Add className if specified
        if (!empty($class_name)) {
            $config['className'] = $class_name;
        }
        
        // Get dynamic watermark settings from options - Line 1
        $watermark_text_field = isset($options['watermark_text_field']) ? $options['watermark_text_field'] : '';
        $watermark_font_size = isset($options['watermark_font_size']) ? intval($options['watermark_font_size']) : 10;
        $watermark_font_color = isset($options['watermark_font_color']) ? $options['watermark_font_color'] : '#ffffff';
        $watermark_font_opacity = isset($options['watermark_font_opacity']) ? intval($options['watermark_font_opacity']) : 25;
        
        // Get dynamic watermark settings from options - Line 2
        $watermark_text_field_line2 = isset($options['watermark_text_field_line2']) ? $options['watermark_text_field_line2'] : '';
        $watermark_font_size_line2 = isset($options['watermark_font_size_line2']) ? intval($options['watermark_font_size_line2']) : 10;
        $watermark_font_color_line2 = isset($options['watermark_font_color_line2']) ? $options['watermark_font_color_line2'] : '#ffffff';
        $watermark_font_opacity_line2 = isset($options['watermark_font_opacity_line2']) ? intval($options['watermark_font_opacity_line2']) : 25;
        
        // Build watermark configuration array (supports multiple lines)
        $watermark_configs = array();
        
        // Check if watermark is enabled
        $watermark_enabled = isset($options['watermark_enable']) && $options['watermark_enable'];
        $line2_enabled = isset($options['watermark_enable_line2']) && $options['watermark_enable_line2'];
        
        if (is_user_logged_in() && $watermark_enabled) {
            $current_user = wp_get_current_user();
            
            // Line 1 watermark
            if (!empty($watermark_text_field)) {
                $dynamic_watermark_text = '';
                switch ($watermark_text_field) {
                    case 'name':
                        $dynamic_watermark_text = $current_user->display_name;
                        break;
                    case 'email':
                        $dynamic_watermark_text = $current_user->user_email;
                        break;
                    case 'user_id':
                        $dynamic_watermark_text = 'User ID: ' . $current_user->ID;
                        break;
                }
                
                if (!empty($dynamic_watermark_text)) {
                    $watermark_configs[] = array(
                        'text' => $dynamic_watermark_text,
                        'color' => $watermark_font_color,
                        'opacity' => $watermark_font_opacity / 100, // Convert percentage to decimal
                        'fontSize' => $watermark_font_size,
                    );
                }
            }
            
            // Line 2 watermark (only if enabled)
            if ($line2_enabled && !empty($watermark_text_field_line2)) {
                $dynamic_watermark_text_line2 = '';
                switch ($watermark_text_field_line2) {
                    case 'name':
                        $dynamic_watermark_text_line2 = $current_user->display_name;
                        break;
                    case 'email':
                        $dynamic_watermark_text_line2 = $current_user->user_email;
                        break;
                    case 'user_id':
                        $dynamic_watermark_text_line2 = 'User ID: ' . $current_user->ID;
                        break;
                }
                
                if (!empty($dynamic_watermark_text_line2)) {
                    $watermark_configs[] = array(
                        'text' => $dynamic_watermark_text_line2,
                        'color' => $watermark_font_color_line2,
                        'opacity' => $watermark_font_opacity_line2 / 100, // Convert percentage to decimal
                        'fontSize' => $watermark_font_size_line2,
                    );
                }
            }
        }
        
        // Generate output
        ob_start();
        ?>
        <div id="<?php echo esc_attr($unique_id); ?>"></div>
        <script>
        (function() {
            if (typeof Vidinfra !== 'undefined') {
                initPlayer();
            } else {
                document.addEventListener('DOMContentLoaded', function() {
                    // Wait for Vidinfra library to load
                    var checkInterval = setInterval(function() {
                        if (typeof Vidinfra !== 'undefined') {
                            clearInterval(checkInterval);
                            initPlayer();
                        }
                    }, 100);
                });
            }
            
            function initPlayer() {
                try {
                    var player = new Vidinfra.Player("<?php echo esc_js($unique_id); ?>", <?php echo wp_json_encode($config); ?>);
                    
                    <?php if (!empty($watermark_configs)) : ?>
                    // Add dynamic watermarks if configured
                    if (player.addWatermark && typeof player.addWatermark === 'function') {
                        player.addWatermark(<?php echo wp_json_encode($watermark_configs); ?>);
                    }
                    <?php endif; ?>
                } catch (error) {
                    console.error('Vidinfra Player Error:', error);
                }
            }
        })();
        </script>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Add settings link to plugin action links
     */
    public function add_settings_link($links) {
        $settings_link = '<a href="' . esc_url(admin_url('options-general.php?page=vidinfra-player-settings')) . '">' . esc_html__('Settings', 'vidinfra-player') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }
    
    /**
     * Register Gutenberg block
     */
    public function register_gutenberg_block() {
        if (!function_exists('register_block_type')) {
            return;
        }
        
        wp_register_script(
            'vidinfra-player-block',
            VIDINFRA_PLAYER_PLUGIN_URL . 'assets/js/block.js',
            array('wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-data'),
            VIDINFRA_PLAYER_VERSION,
            true
        );
        
        wp_localize_script(
            'vidinfra-player-block',
            'vidinfraPlayerData',
            array(
                'iconUrl' => VIDINFRA_PLAYER_PLUGIN_URL . 'assets/images/icon-128x128.png',
            )
        );
        
        register_block_type('vidinfra/player', array(
            'api_version' => 2,
            'editor_script' => 'vidinfra-player-block',
            'render_callback' => array($this, 'render_shortcode'),
            'attributes' => array(
                'video_id' => array('type' => 'string', 'default' => ''),
                'library_id' => array('type' => 'string', 'default' => ''),
                'player_id' => array('type' => 'string', 'default' => ''),
                'width' => array('type' => 'string', 'default' => ''),
                'height' => array('type' => 'string', 'default' => ''),
                'autoplay' => array('type' => 'boolean', 'default' => false),
                'loop' => array('type' => 'boolean', 'default' => false),
                'muted' => array('type' => 'boolean', 'default' => false),
                'controls' => array('type' => 'boolean', 'default' => true),
                'preload' => array('type' => 'boolean', 'default' => true),
                'aspect_ratio' => array('type' => 'string', 'default' => '16:9'),
                'loading' => array('type' => 'string', 'default' => 'eager'),
                'class_name' => array('type' => 'string', 'default' => ''),
            ),
        ));
    }
}

/**
 * Initialize the plugin
 */
function vidinfra_player_init() {
    return Vidinfra_Player::get_instance();
}
add_action('plugins_loaded', 'vidinfra_player_init');

/**
 * Activation hook
 */
function vidinfra_player_activate() {
    // Set default options on activation if they don't exist
    $options = get_option('vidinfra_player_options');
    if (false === $options) {
        $default_options = array(
            'default_library_id' => '',
            'default_player_id' => 'default',
            'default_aspect_ratio' => '16:9',
            'watermark_enable' => 0,
            'watermark_text_field' => 'name',
            'watermark_font_size' => 10,
            'watermark_font_color' => '#ffffff',
            'watermark_font_opacity' => 25,
            'watermark_enable_line2' => 0,
            'watermark_text_field_line2' => 'name',
            'watermark_font_size_line2' => 10,
            'watermark_font_color_line2' => '#ffffff',
            'watermark_font_opacity_line2' => 25,
        );
        add_option('vidinfra_player_options', $default_options);
    }
    
    // Clear cache
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'vidinfra_player_activate');

/**
 * Deactivation hook
 */
function vidinfra_player_deactivate() {
    // Clear cache
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'vidinfra_player_deactivate');
