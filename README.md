My Custom Plugin
Contributors: siddhirajpurohit
Tags: custom-post-type, shortcode, admin-settings, fastapi, submissions
Requires at least: 5.8
Tested up to: 6.5
Requires PHP: 7.4


A lightweight custom plugin to handle user submissions via shortcode, store data in DB, and connect with FastAPI.

=Description 
My Custom Plugin lets you:
- Create a custom post type
- Display a submission form via shortcode
- Store data in WordPress DB
- View submissions with delete option
- Send data to FastAPI backend
- Add admin settings (like toggle for thank-you message)

Installation 
1. Upload the plugin to `/wp-content/plugins/my-custom-plugin`
2. Activate via the Plugins menu in WordPress
3. Use `[my_custom_form]` to render the form
4. Use `[my_plugin_data]` to show saved submissions

Frequently Asked Questions

= Can I disable the thank-you message? =
Yes! Go to Settings > My Plugin and choose "No".

Where is the data stored? 
It's stored in a custom table `wp_my_plugin_data`.

How does FastAPI work with this? 
Each submission is sent via HTTP POST to your FastAPI server (`/receive-data`).


