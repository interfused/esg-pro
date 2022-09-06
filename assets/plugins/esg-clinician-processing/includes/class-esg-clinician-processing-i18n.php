<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://interfused-inc.com
 * @since      1.0.0
 *
 * @package    Esg_Clinician_Processing
 * @subpackage Esg_Clinician_Processing/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Esg_Clinician_Processing
 * @subpackage Esg_Clinician_Processing/includes
 * @author     Interfused <jeremy@interfused-inc.com>
 */
class Esg_Clinician_Processing_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'esg-clinician-processing',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
