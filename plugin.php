<?php
/*
Plugin Name: WebP Cloud Services Plugin
Plugin URI: https://webp.se
Description: Replaces all image URLs with WebP Cloud Services CDN URL
Version: 1.1
Author: WebP Cloud Services
Author URI: https://webp.se
*/

function replace_image_urls($content) {
    $origin_url = get_home_url();
    $proxy_url = get_option('proxy_url');
    
    // Regular expression pattern to match img tags
    $pattern = '/<img(.*?)\s(?:src|srcset)=["\'](.*?)["\'](.*?)>/i';
    
    // Replace the origin_url within img tags using a callback function
    $content = preg_replace_callback($pattern, function($matches) use ($origin_url, $proxy_url) {
        $img_tag = $matches[0];
        $img_src = $matches[2];
        $img_srcset = $matches[3];
        
        // Replace the origin_url with proxy_url
        $new_img_src = str_replace($origin_url, $proxy_url, $img_src);
        $new_img_srcset = str_replace($origin_url, $proxy_url, $img_srcset);
        
        // Replace the img src attribute in the img tag
        $new_img_tag = str_replace($img_srcset, $new_img_srcset, $img_tag);
        $new_img_tag = str_replace($img_src, $new_img_src, $new_img_tag);
        
        return $new_img_tag;
    }, $content);
    
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

function image_proxy_plugin_settings_link($links) {
    $settings_link = '<a href="options-general.php?page=image-proxy-settings">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'image_proxy_plugin_settings_link');

add_action('admin_menu', function() {
    add_options_page('WebP Cloud Services Plugin Settings', 'WebP Cloud Services', 'manage_options', 'image-proxy-settings', 'image_proxy_settings_page');
});

add_action('admin_init', 'image_proxy_register_settings');