<?php
if (!defined('ABSPATH')) exit;

class My_Custom_Plugin_Admin {
    private static $instance = null;

    private function __construct() {
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // 1. Add settings page to the admin menu
    public function add_settings_page() {
        add_options_page(
            'My Plugin Settings',
            'My Plugin',
            'manage_options',
            'my-plugin-settings',
            array($this, 'render_settings_page')
        );
    }

    // 2. Register the setting, section, and field
    public function register_settings() {
        register_setting('my_plugin_options_group', 'my_plugin_show_thank_you');

        add_settings_section(
            'my_plugin_main_section',
            'Main Settings',
            null,
            'my-plugin-settings'
        );

        add_settings_field(
            'my_plugin_show_thank_you',
            'Show Thank You Message',
            array($this, 'toggle_field_callback'),
            'my-plugin-settings',
            'my_plugin_main_section'
        );
    }

    //  3. Display the toggle field
    public function toggle_field_callback() {
        $value = get_option('my_plugin_show_thank_you', 'yes');
        ?>
        <select name="my_plugin_show_thank_you">
            <option value="yes" <?php selected($value, 'yes'); ?>>Yes</option>
            <option value="no" <?php selected($value, 'no'); ?>>No</option>
        </select>
        <?php
    }

    // 4. Render the settings form
    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1>My Plugin Settings</h1>
            <form method="post" action="options.php">
                <?php
                    settings_fields('my_plugin_options_group'); // Must match register_setting group
                    do_settings_sections('my-plugin-settings'); // Must match page slug in add_settings_page
                    submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}
