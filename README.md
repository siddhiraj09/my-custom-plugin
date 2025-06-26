=== My Custom Plugin ===
Contributors: siddhiraj09
Tags: custom post type, form, api, fastapi
Requires at least: 5.8
Tested up to: 6.5
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

== Description ==
A custom plugin that registers a post type, adds a submission form, displays entries, and connects to FastAPI.

== Installation ==
1. Upload the plugin to the `/wp-content/plugins/` directory.
2. Activate through the 'Plugins' menu in WordPress.
3. Use `[my_plugin_form]` to embed the form and `[my_plugin_data]` to display data.

== Changelog ==
= 1.0.0 =
* Initial version with custom post type, form handling, DB insert, deletion, and FastAPI integration.

== Frequently Asked Questions ==
= Can I modify the API endpoint? =
Yes, edit the `$api_url` variable inside `class-public.php`.
