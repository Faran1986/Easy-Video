<?php
/*
Plugin Name: Easy Videos
Plugin URI: 
Description: 
Version: 1.0
Author: Syed Faran Sattar
Author URI: 
*/

add_action('admin_enqueue_scripts', 'ajax_test_enqueue_scripts');
function ajax_test_enqueue_scripts()
{
    wp_enqueue_script('js', plugins_url('/js/custom.js', __FILE__), array('jquery'), '1.0', true);
    wp_localize_script('js', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
}



// Registor video custom post type
require plugin_dir_path(__FILE__) . 'include/video-post-type.php';

// Registor get videos API
require plugin_dir_path(__FILE__) . 'include/video-api.php';
