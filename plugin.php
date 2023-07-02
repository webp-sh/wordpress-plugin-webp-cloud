<?php
/*
Plugin Name: WebP Cloud Services Plugin
Plugin URI: https://webp.se
Description: Replaces all image URLs with WebP Cloud Services CDN URL
Version: 1.0
Author: WebP Cloud Services
Author URI: https://webp.se
*/

function replace_image_urls($content) {
    $origin_url = get_home_url();
    $proxy_url = get_option('proxy_url');
    $content = str_replace($origin_url, $proxy_url, $content);
    return $content;
}
add_filter('the_content', 'replace_image_urls');

function image_proxy_settings_page() {
    ?>
    <div class="wrap">
        <h1>Image Proxy Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('image_proxy_settings_group'); ?>
            <?php do_settings_sections('image_proxy_settings_group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Proxy URL</th>
                    <td><input type="text" name="proxy_url" value="<?php echo esc_attr(get_option('proxy_url')); ?>" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

function image_proxy_register_settings() {
    register_setting('image_proxy_settings_group', 'proxy_url');
}

add_action('admin_menu', function() {
    add_options_page('WebP Cloud Services Plugin Settings', 'WebP Cloud Services Plugin Settings', 'manage_options', 'image-proxy-settings', 'image_proxy_settings_page');
});

add_action('admin_init', 'image_proxy_register_settings');