<?php

/**
 * Plugin Name: Admin Select Box To Select2
 * Description: This plugin is used to convert all simple Select boxes to Select2.
 * Version: 1.0.0
 * Author: Aftab Muni
 * Author URI: https://aftabmuni.wordpress.com/
 * @package Admin Select Box To Select2
 * @version 1.0.0
 */
// Terminate if accessing directlty
if (!defined('ABSPATH')) {
    exit;
}

define('ASBTS2', __FILE__);
function amm_select2_enqueue() {

    wp_enqueue_style('select2', plugins_url('', ASBTS2) . '/css/select2.min.css');
    wp_enqueue_script('select2', plugins_url('', ASBTS2) . '/js/select2.min.js', array('jquery'));
    // please create also an empty JS file in your theme directory and include it too
    wp_enqueue_script('mycustom', plugins_url('', ASBTS2) . '/js/mycustom.js', array('jquery', 'select2'));
}
add_action('admin_enqueue_scripts', 'amm_select2_enqueue');
