<?php
if (!defined('ABSPATH')) {
    exit;
}
class My_Custom_Plugin_Public {
    private static $instance = null;


    private function __construct() {
         error_log("Constructor called");
        add_action('init', array($this, 'register_custom_post_type'));
        add_filter('the_content', array($this, 'append_custom_message'));
        add_shortcode('my_plugin_form', array($this, 'render_submission_form'));
        add_shortcode('my_plugin_data', array($this, 'render_submission_data'));
        add_action('init', array($this, 'handle_form_submission'));
        $this->register_shortcodes(); 
    }
    
    public function render_submission_form() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['my_plugin_name'])) {
        global $wpdb;
        $table = $wpdb->prefix . 'my_plugin_data';

        $name = sanitize_text_field($_POST['my_plugin_name']);
        $text = sanitize_textarea_field($_POST['my_plugin_text']);

        $wpdb->insert($table, [
            'name' => $name,
            'text' => $text
        ]);

        echo '<p><strong>Thank you for your submission!</strong></p>';
    }
    

    ob_start(); ?>
    <form method="post">
        <label for="my_plugin_name">Your Name:</label><br>
        <input type="text" name="my_plugin_name" required><br><br>

        <label for="my_plugin_text">Your Message:</label><br>
        <textarea name="my_plugin_text" required></textarea><br><br>

        <button type="submit">Submit</button>
    </form>
    <?php
    return ob_get_clean();
}


    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    public function register_custom_post_type() {
        register_post_type('my_custom_post', array(
            'public' => true,
            'label' => 'my Custom Posts',
            'supports' => array('title', 'editor', 'thumbnail'),
            'show_in_rest' => true,
        ));
    }

  public function append_custom_message($content) {
    if (is_singular('my_custom_post')) {
        $show = get_option('my_plugin_show_thank_you', 'yes');  // checks DB setting

        if ($show === 'yes') {
            $content .= '<p><strong>Thanks for reading this custom post!</strong></p>';
        }
    }
    return $content;
}
    public function render_submission_data() {
    global $wpdb;
    $table = $wpdb->prefix . 'my_plugin_data';

      // Check for cached data
    $cached = get_transient('my_plugin_cached_submissions');

    if ($cached !== false) {
        $results = $cached;
    } else {
        // Fetch from DB and set cache
        $results = $wpdb->get_results("SELECT * FROM $table ORDER BY time DESC");
        set_transient('my_plugin_cached_submissions', $results, 60); // Cache for 60 seconds
    }

    // Handle Deletion
    if (isset($_GET['delete_id']) && current_user_can('manage_options')) {
        $delete_id = intval($_GET['delete_id']);
        check_admin_referer('delete_submission_' . $delete_id); // Nonce check
        $wpdb->delete($table, ['id' => $delete_id]);
        // Redirect to avoid re-submission on page reload
        delete_transient('my_plugin_cached_submissions');
        wp_redirect(remove_query_arg(['delete_id', '_wpnonce']));
        exit;
    }

    // // Fetch entries
    // $results = $wpdb->get_results("SELECT * FROM $table ORDER BY time DESC");

    if (empty($results)) {
        return '<p>No submissions found.</p>';
    }

    ob_start();
    echo '<div class="my-plugin-submissions">';
    echo '<h3>Submitted Entries</h3><ul>';

    foreach ($results as $row) {
        echo '<li>';
        echo '<strong>' . esc_html($row->name) . '</strong>: ';
        echo esc_html($row->text);
        echo ' <em>(' . esc_html($row->time) . ')</em>';

        if (current_user_can('manage_options')) {
            $nonce = wp_create_nonce('delete_submission_' . $row->id);
            $delete_url = add_query_arg([
                'delete_id' => $row->id,
                '_wpnonce'  => $nonce
            ]);

            echo ' <a href="' . esc_url($delete_url) . '" onclick="return confirm(\'Delete this?\')">🗑 Delete</a>';
        }

        echo '</li>';
    }

    echo '</ul></div>';
    return ob_get_clean();
}

public function register_shortcodes() {
    add_shortcode('my_custom_form', array($this, 'render_custom_form'));
    }
    public function render_custom_form() {
    ob_start();
    ?>
    <form method="POST">
        <input type="text" name="my_name" placeholder="Your Name" required>
        <textarea name="my_message" placeholder="Your Message" required></textarea>
        <button type="submit" name="my_custom_form_submit">Submit</button>
    </form>
    <?php
    return ob_get_clean();
}
public function handle_form_submission() {
    if (isset($_POST['my_custom_form_submit'])) {
        $name = sanitize_text_field($_POST['my_name']);
        $message = sanitize_textarea_field($_POST['my_message']);

        error_log("Submitting to FastAPI + WordPress DB: $name - $message");

        // 1. Save to WordPress DB
        global $wpdb;
        $table = $wpdb->prefix . 'my_plugin_data';
        $wpdb->insert($table, [
            'name' => $name,
            'text' => $message,
        ]);
        delete_transient('my_plugin_cached_submissions');

        // 2. Send to FastAPI
        $api_url = 'http://127.0.0.1:8000/receive-data';
        $response = wp_remote_post($api_url, array(
            'method'    => 'POST',
            'headers'   => array('Content-Type' => 'application/json'),
            'body'      => json_encode(array(
                'name'    => $name,
                'message' => $message,
            )),
        ));

        if (is_wp_error($response)) {
            error_log('FastAPI POST failed: ' . $response->get_error_message());
        } else {
            error_log('FastAPI POST succeeded: ' . wp_remote_retrieve_body($response));
        }

        // Redirect to avoid form resubmission
        wp_redirect(add_query_arg('submitted', 'true'));
        exit;
    }
}



}


