<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://interfused-inc.com
 * @since             1.0.0
 * @package           Esg_Clinician_Processing
 *
 * @wordpress-plugin
 * Plugin Name:       Endoscopic Spine Guide Clinician Processing
 * Plugin URI:        http://interfused-com/wordpress-plugins
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Interfused
 * Author URI:        https://interfused-inc.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       esg-clinician-processing
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('ESG_CLINICIAN_PROCESSING_VERSION', '1.0.0');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-esg-clinician-processing-activator.php
 */
function activate_esg_clinician_processing()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-esg-clinician-processing-activator.php';
	Esg_Clinician_Processing_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-esg-clinician-processing-deactivator.php
 */
function deactivate_esg_clinician_processing()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-esg-clinician-processing-deactivator.php';
	Esg_Clinician_Processing_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_esg_clinician_processing');
register_deactivation_hook(__FILE__, 'deactivate_esg_clinician_processing');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-esg-clinician-processing.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_esg_clinician_processing()
{

	$plugin = new Esg_Clinician_Processing();
	$plugin->run();
}
run_esg_clinician_processing();

//PLUGIN SHORTCODES
function tbare_wordpress_plugin_demo()
{
	echo 'this is a demo';
}

add_shortcode('tbare-plugin-demo', 'tbare_wordpress_plugin_demo');

function esg_is_clinician_authorized($desired_role = "clinician")
{
	//STUB
	return true;
}

function esg_get_template_file($relative_path_base)
{

	$theme_folder_file_overwrite = get_stylesheet_directory() . '/esgpro' . $relative_path_base;

	if (file_exists($theme_folder_file_overwrite)) {
		$template =  $theme_folder_file_overwrite;
	} else {
		$template =  __DIR__ . '/templates' . $relative_path_base;
	}
	return $template;
}

function esg_edit_profile_fields_func()
{
	if (is_admin()) {
		return;
	}
	$relative_path_base = '/myaccount/profile.php';

	$theme_folder_file_overwrite = get_stylesheet_directory() . '/esgpro' . $relative_path_base;

	if (file_exists($theme_folder_file_overwrite)) {
		$template =  $theme_folder_file_overwrite;
	} else {
		$template =  __DIR__ . '/templates' . $relative_path_base;
	}

	if (esg_is_clinician_authorized()) {
		include($template);
	} else {
		return '<h2>Need to register as CLINICIAN</h2>';
	}

	//return '<H2>edit profile fileds here</H2>';
}

function pretty_print_r($obj)
{
	echo '<pre style="max-height: 450px; overflow: auto;">' . print_r($obj, true) . '</pre>';
}

function getEsgAjaxPathUrl($location = 'public')
{
	//assume public
	$html = '<script>';
	$rel_path = $location . '';
	$html .= 'const esgPublicAjaxPath ="' . plugin_dir_url(__FILE__) . $rel_path . '/ajax";';

	$html .= 'console.log("esgPublicAjaxPath is: ", esgPublicAjaxPath);';
	$html .= '</script>';
	return $html;
}

add_shortcode('esg_edit_profile_fields', 'esg_edit_profile_fields_func');
