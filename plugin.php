<?php
/**
 * Plugin Name: WordCamp Barcelona 2016
 * Description: Create annotations on your posts or pages
 * Text Domain: wcbcn2016
 * Domain Path: /languages
 * Version:     1.0
 * Author:      xyulex
 * Author URI:  https://profiles.wordpress.org/xyulex/
 * License:     GPLv2 or later
 * License URI:	http://www.gnu.org/licenses/gpl-2.0.html
 */


add_filter('the_content', 'wcbcn2016_annotate_backend');
add_filter('mce_css', 'wcbcn2016_annotate_css');
add_action('admin_head', 'wcbcn2016_annotate');


function wcbcn2016_annotate_css($mce_css) {
  if (!empty($mce_css))
    $mce_css .= ',';
    $mce_css .= plugins_url('css/style.css', __FILE__);
    return $mce_css;
}

// Don't display annotations in frontend
function wcbcn2016_annotate_backend($content) {
    return preg_replace('/(<[^>]+) class="annotation" style=".*?"/i', '$1', $content);
}

function wcbcn2016_annotate() {
    global $typenow;

    // Only apply to posts and pages
    if ( !in_array($typenow, array('post', 'page')) )
        return ;

	// Add as an external TinyMCE plugin
    add_filter('mce_external_plugins', 'wcbcn2016_annotate_plugin');

    // Add to first row of the TinyMCE buttons
    add_filter('mce_buttons', 'wcbcn2016_annotate_button');

    // I18n
    load_plugin_textdomain('wcbcn2016', FALSE, dirname(plugin_basename(__FILE__)).'/languages/');
    $current_user = wp_get_current_user();
    wp_register_script( 'wcbcn2016js', plugins_url('/plugin.js', __FILE__));
    wp_localize_script( 'wcbcn2016js', 'wcbcn2016',
        array(
            'id'        => $current_user->ID,
            'author'    => $current_user->display_name,
            'errors'    => array(
                            'missing_fields'        => __('Select the color and the annotation text', 'wcbcn2016'),
                            'missing_annotation'    => __('Please select some text for creating an annotation', 'wcbcn2016'),
                            'missing_selected'      => __('Please select the annotation you want to delete', 'wcbcn2016')
                            ),
            'tooltips'  => array(
                            'annotation_settings'   => __('Annotation settings', 'wcbcn2016'),
                            'annotation_create'     => __('Create annotation', 'wcbcn2016'),
                            ),
            'settings'  => array(
                            'setting_annotation'    => __('Annotation', 'wcbcn2016'),
                            'setting_background'    => __('Background color', 'wcbcn2016')
                            )
        )
    );

    wp_enqueue_script( 'wcbcn2016js' );
}

// Include the JS
function wcbcn2016_annotate_plugin($plugin_array) {
    $plugin_array['wcbcn2016_annotate'] = plugins_url('/plugin.js', __FILE__);
    return $plugin_array;
}

// Add the button key for address via JS
function wcbcn2016_annotate_button($buttons) {
    array_push($buttons, 'wcbcn2016_annotate');
    return $buttons;
}