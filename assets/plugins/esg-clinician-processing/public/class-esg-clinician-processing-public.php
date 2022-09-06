<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://interfused-inc.com
 * @since      1.0.0
 *
 * @package    Esg_Clinician_Processing
 * @subpackage Esg_Clinician_Processing/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Esg_Clinician_Processing
 * @subpackage Esg_Clinician_Processing/public
 * @author     Interfused <jeremy@interfused-inc.com>
 */
class Esg_Clinician_Processing_Public
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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		//wp_enqueue_style('chosen_styles', 'https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.css', false);

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/esg-clinician-processing-public.css', array(), $this->version, 'all');
		wp_enqueue_style('select2-css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', array(), '4.1.0-rc.0');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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
		wp_enqueue_script('select2-js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array('jquery'), '4.1.0-rc.0');
		//wp_enqueue_script('chosen_js', 'https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js', array('jquery'), null, true);

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/esg-clinician-processing-public.js', array('jquery'), $this->version, false);
	}



	public function my_custom_template($single_template)
	{

		global $post;
		$user = wp_get_current_user();
		$allowed_roles = array('editor', 'administrator', 'author');

		/* Checks for single template by post type */
		if ('case_study' === $post->post_type) {
			if (array_intersect($allowed_roles, $user->roles)) {
				// Stuff here for allowed roles
				$single_template = dirname(__FILE__) . '/templates/single-case-study.php';
			} else {
				$single_template = dirname(__FILE__) . '/templates/single-case-study-excerpt.php';
			}
		}

		return $single_template;
	}

	function esg_restrict_case_study($content)
	{
		global $post;

		$user = wp_get_current_user();
		$allowed_roles = array('editor', 'administrator', 'author', 'clinician');
		$excerpt_length = 55;

		if ('case_study' === $post->post_type) {
			if (array_intersect($allowed_roles, $user->roles)) {
				// Stuff here for allowed roles
				return $content;
			} else {
				$excerpt = '';
				if (has_excerpt()) {
					$excerpt = wp_strip_all_tags(get_the_excerpt());
				} else {
					$custom = wp_trim_words(get_the_content(),  $excerpt_length);

					return $custom;
				}
				return $excerpt;
			}
		}
		return $content;
	}

	public function esg_get_profile_fields()
	{
		echo 'profile fileds (public)';
	}


	public function esg_citystatezip_action_callback()
	{
		$json = array();

		if (isset($_REQUEST['zipcode'])) {
			$json['message'] = 'Success!';
			wp_send_json_success($json);
		} else {
			$json['message'] = 'Zipcode was not set!';
			wp_send_json_error($json);
		}
	}

	public function esg_ajax_call1()
	{

		echo 'Ajax call output:';

		echo '<pre>';
		var_dump($_POST);
		echo '</pre>';

		wp_die(); // this is required to terminate immediately and return a proper response
	}

	public function esg_zip_table_search()
	{
		echo 'gobble';
		return 'asdf';
		//exit("Woof Woof Woof");
		wp_die(); // this is required to terminate immediately and return a proper response:- https://codex.wordpress.org/AJAX_in_Plugins
	}

	public function ajax_search_script()
	{

		$script_handle = 'custom-script';
		$src = plugin_dir_url(__FILE__) . 'js/custom.js';
		$in_footer = true;
		wp_register_script($script_handle, $src, array('jquery'), $this->version,  $in_footer);


		/*
		$script_data_array = array(
			'url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('search_titles_nonce')
		);
		*/
		$script_data_array = array(
			'url' => plugin_dir_url(__FILE__) . 'ajax/ajax-test.php',
			'nonce' => wp_create_nonce('search_titles_nonce')
		);
		wp_localize_script($script_handle, 'ajax_object', $script_data_array);
		wp_enqueue_script($script_handle);
	}

	public function get_articles_titles()
	{

		echo 'asdfasdf';
		wp_die();
		return;
		check_ajax_referer('ajax_nonce', 'nonce');
		$input = $_POST['search_text'];
		$args = array(
			'posts_per_page'   => -1,
			'post_type'        => 'post',
		);
		$the_query = new WP_Query($args);

		$autocomplete = "<br>";

		if ($the_query->have_posts()) {
			while ($the_query->have_posts()) {
				$the_query->the_post();
				$title = get_the_title(get_the_ID());

				if (strpos(strtolower($title), strtolower($input)) !== false) {
					$autocomplete .= $title . "<br>";
				}
			}
			wp_reset_postdata();
		}
		echo $autocomplete;

		wp_die();
	}
	public function acf_form_header()
	{
		acf_form_head();
	}
}
