<?php
/**
 * Plugin Name: tagDiv Opt-In Builder
 * Plugin URI: http://tagdiv.com
 * Description: Generate leads and increase conversion rates with opt-in content lockers and subscription lists.  tagDiv Opt-In Builder helps you easily create content lockers, subscribing lists (membership) and gives your visitors a compelling reason to enter their email address (opt-in) to unlock your content.
 * Version: 1.3.1 | built on 12.05.2022 9:11
 * Author: tagDiv
 * Author URI: http://tagdiv.com
 *
 * @package td-subscription\td-subscription
 *
 *
 */

defined( 'ABSPATH' ) || exit;

if ( !defined( 'TDS_PLUGIN_FILE' ) ) {
	define( 'TDS_PLUGIN_FILE', __FILE__ );
}

// hash
define('TD_SUBSCRIPTION', '___td-subscription___');

define('TD_SUBSCRIPTION_VERSION', '1.3.1');

// the deploy mode: dev or deploy  - it's set to deploy automatically on deploy
define( "TDS_DEPLOY_MODE", 'deploy' );

// compatibility checks
require_once('tds_version_check.php');

// check active theme compatibility and return here if the active theme doesn't support it
if ( tds_version_check::is_active_theme_compatible() === false )
	return;

// check theme version compatibility
if ( tds_version_check::is_theme_version_compatible() === false )
	return;

if ( !defined( 'TDS_URL' ) ) {
	define( 'TDS_URL', plugins_url('td-subscription') );
}

if ( !defined( 'TDS_PATH' ) ) {
	define( 'TDS_PATH', dirname(__FILE__) );
}

// main tds class
require_once('includes/td_subscription.php');
td_subscription::instance();
