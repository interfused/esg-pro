<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://interfused-inc.com
 * @since      1.0.0
 *
 * @package    Esg_Clinician_Processing
 * @subpackage Esg_Clinician_Processing/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Esg_Clinician_Processing
 * @subpackage Esg_Clinician_Processing/admin
 * @author     Interfused <jeremy@interfused-inc.com>
 */
class Esg_Clinician_Processing_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Esg_Clinician_Processing_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Esg_Clinician_Processing_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/esg-clinician-processing-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Esg_Clinician_Processing_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Esg_Clinician_Processing_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/esg-clinician-processing-admin.js', array('jquery'), $this->version, false);
	}

	public function esg_add_admin_menus()
	{
		$menu_slug = 'esg-clinicians-processing';
		$capability_required = 'update_plugins';

		add_menu_page(
			esc_html__('ESG Clinicians Processing', 'esg-clinicians-portal'),
			esc_html__('ESG Clinicians Processing', 'esg-clinicians-portal'),
			$capability_required,
			$menu_slug,
			array($this, 'esg_add_main_menu_page_callback'),
			'dashicons-businessman',
			55.5
		);

		add_submenu_page(
			$menu_slug,
			esc_html__('Testing Dev Title', 'esg-clinicians-portal'),
			esc_html__('Testing Dev Menu Title', 'esg-clinicians-portal'),
			$capability_required,
			'esg_submenu1',
			array($this, 'esg_add_submenu_page1_callback'),
		);
	}



	/**
	 * Add page to admin menu callback
	 */
	public function esg_add_main_menu_page_callback()
	{
		include   'partials/esg-clinician-processing-admin-display.php';
	}

	public function esg_add_submenu_page1_callback()
	{
		include   'partials/esg-clinician-processing-admin-display-submenu1.php';
	}

	public function esg_add_user_roles_and_capabilities()
	{
		add_role('clinician', 'Clinician');

		//role heirarchy
		$custom_role_heirarchy = ['Clinician : Free Member', 'Clinician : Silver Member', 'Clinician : Platinum Member'];

		for ($i = 1; $i <= count($custom_role_heirarchy); $i++) {
			add_role('clinician_level_' . $i, $custom_role_heirarchy[$i - 1]);
		}


		//ADD BASIC CLINICIAN
		$subscriber = get_role('subscriber');
		$subscriber_cap = array_keys($subscriber->capabilities); //get administator capabilities

		$new_role = get_role('clinician');
		foreach ($subscriber_cap as $cap) {
			$new_role->add_cap($cap);
		}

		// We add additional levels for clinicians

		//CLINICIAN LEVEL 1


		//AUTHOR CAPABILITIES : LEVEL 2 & 3
		$author = get_role('author');
		$author_cap = array_keys($author->capabilities);

		$new_role_level2 = get_role('clinician_level_2');
		$new_role_level3 = get_role('clinician_level_3');
		foreach ($author_cap as $cap) {
			$new_role_level2->add_cap($cap);
			$new_role_level3->add_cap($cap);
		}

		//CASE STUDIES : LEVEL 3

		$cpt = 'case_study';
		$custom_capabilities = ['delete_' . $cpt, 'delete_published_' . $cpt, 'edit_' . $cpt, 'edit_published_' . $cpt, 'publish_' . $cpt];
		foreach ($custom_capabilities as $cap) {
			$new_role_level3->add_cap($cap);
		}
	}

	public function esg_admin_acf_enqueue_scripts()
	{
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/esg-clinician-processing-admin-acf.js', array('jquery'), $this->version, false);
	}

	public function esg_allow_contributor_uploads()
	{
		$allowed_extra_roles = [];
		foreach ($allowed_extra_roles as $role) {
			$subscriber = get_role($role);
			$subscriber->add_cap('upload_files');
			$subscriber->add_cap('edit_published_pages');
		}
	}
}
