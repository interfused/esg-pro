<?php


//AJAX VIEW PANEL LOADING
add_action( 'wp_ajax_tdb_panel_vue_get_data', array( 'tdb_panel_vue', 'get_data'));



class tdb_panel_vue {

	static function get_data() {

		// die if request is fake
		check_ajax_referer('td-panel-box', 'td_magic_token');


		//if user is logged in and can switch themes
		if (!current_user_can('edit_theme_options')) {
			die;
		}

		$buffy = array();
		$data_type = td_util::get_http_post_val('td_data_type');
		$woo_template_type = td_util::get_http_post_val('td_woo_template_type');

		switch($data_type) {
			case 'author-templates':
				$author_data = self::get_authors_data();
				$buffy = array_merge($buffy, $author_data);
				break;
			case '404-templates':
				$page_404_data = self::get_template_data( '404' );
				$buffy = array_merge($buffy, $page_404_data);
				break;
            case 'date-templates':
                $date_data = self::get_template_data( 'date' );
                $buffy = array_merge($buffy, $date_data);
				break;
            case 'attachment-templates':
                $attachment_data = self::get_template_data( 'attachment' );
                $buffy = array_merge($buffy, $attachment_data);
				break;
            case 'search-templates':
                $search_data = self::get_template_data( 'search' );
                $buffy = array_merge($buffy, $search_data);
				break;
            case 'tag-templates':
                $tag_data = self::get_tag_data();
                $buffy = array_merge($buffy, $tag_data);
				break;
			case 'woo-templates':
	            $woo_data = self::get_template_data($woo_template_type);
	            $buffy = array_merge($buffy, $woo_data);
				break;
			default:
				die;
		}

		die(json_encode($buffy));
	}

    /**
     * This function retrieves the given 'template type' global theme option,
     * check's to be a valid and existing template and returns an array containing all available templates and
     * the global template set in the panel
     *
     * @param $template_type - the template type
     * @return array
     *
     * ex. 'tdb_search_templates' - the tdb templates by the template type param
     *     'tdb_panel_template_id' - the panel tdb template id
     */
    private static function get_template_data( $template_type ) {

		$option_id = 'tdb_' . $template_type . '_template';
		if ( class_exists( 'SitePress', false ) ) {
			global $sitepress;
			$sitepress_settings = $sitepress->get_settings();
			if ( isset($sitepress_settings['custom_posts_sync_option'][ 'tdb_templates']) ) {
				$translation_mode = (int)$sitepress_settings['custom_posts_sync_option']['tdb_templates'];
				if (1 === $translation_mode) {
					$option_id .= $sitepress->get_current_language();
				}
			}
		}

        // the template type panel option
        $template = td_util::get_option($option_id);
		// check the template id .. if is not a valid id set an empty string
        $tdb_panel_template_id = td_global::is_tdb_template( $template, true ) ? $template : '';

        // return a list of available templates and the panel set template id
        return array(
            'tdb_' . $template_type . '_templates' => self::get_tdb_templates( $template_type ),
            'tdb_panel_template_id' => $tdb_panel_template_id
        );
    }


    /**
	 * Get authors data
	 * @return array
     *
     * 'td_users_list' - a wp 'administrator', 'editor', 'author' users list
     * 'td_author_templates' - the tdb type author templates
	 */
	private static function get_authors_data() {
		$td_users_list = array();

		//wp users
		$wp_users_list = self::get_wp_users();
		//tdb templates
		$tdb_author_templates = self::get_tdb_templates( 'author' );

		$option_id = 'tdb_author_templates';
		if ( class_exists( 'SitePress', false ) ) {
			global $sitepress;
			$sitepress_settings = $sitepress->get_settings();
			if ( isset($sitepress_settings['custom_posts_sync_option'][ 'tdb_templates']) ) {
				$translation_mode = (int)$sitepress_settings['custom_posts_sync_option']['tdb_templates'];
				if (1 === $translation_mode) {
					$option_id .= $sitepress->get_current_language();
				}
			}
		}
		//theme panel
		$panel_author_templates = td_util::get_option( $option_id );

		foreach ( $wp_users_list as $user ) {
			$tdb_template_id = '';
			if (!empty($panel_author_templates[$user->ID])) {
				foreach ($tdb_author_templates as $id => $title) {
					if ((int)$panel_author_templates[$user->ID] === (int)$id) {
						$tdb_template_id = $panel_author_templates[$user->ID];
					}
				}
			}

			$td_users_list[ $user->ID ] = array(
				'ID'            => $user->ID,
				'user_login'    => $user->data->user_login,
				'user_url'      => get_author_posts_url($user->ID),
				'user_nicename' => $user->data->user_nicename,
				'tdb_template_id' => $tdb_template_id
			);
		}

		$option_id = 'tdb_author_template';
		if ( class_exists( 'SitePress', false ) ) {
			global $sitepress;
			$sitepress_settings = $sitepress->get_settings();
			if ( isset($sitepress_settings['custom_posts_sync_option'][ 'tdb_templates']) ) {
				$translation_mode = (int)$sitepress_settings['custom_posts_sync_option']['tdb_templates'];
				if (1 === $translation_mode) {
					$option_id .= $sitepress->get_current_language();
				}
			}
		}

		return array(
			'td_users_list' => $td_users_list,
			'td_author_templates' => $tdb_author_templates,
            'tdb_panel_general_template_id' => td_util::get_option( $option_id )
		);
	}

	/**
	 * Get tag data
	 * @return array
	 */
	private static function get_tag_data() {

		$tag_data = self::get_template_data( 'tag' );

		$option_id = 'tdb_tag_templates';
		if ( class_exists( 'SitePress', false ) ) {
			global $sitepress;
			$sitepress_settings = $sitepress->get_settings();
			if ( isset($sitepress_settings['custom_posts_sync_option'][ 'tdb_templates']) ) {
				$translation_mode = (int)$sitepress_settings['custom_posts_sync_option']['tdb_templates'];
				if (1 === $translation_mode) {
					$option_id .= $sitepress->get_current_language();
				}
			}
		}

        $tdb_tag_templates = td_util::get_option( $option_id );

        foreach ($tag_data['tdb_tag_templates'] as $tdb_tag_template_id => &$tdb_tag_template ) {
        	$tdb_tag_template = ['name' => $tdb_tag_template];
            if (!empty($tdb_tag_templates)) {
                if (array_key_exists($tdb_tag_template_id, $tdb_tag_templates)) {
                    $tdb_tag_template['tags'] = $tdb_tag_templates[$tdb_tag_template_id];
                }
            }
        }

        return $tag_data;
	}


	/**
	 * Get Wordpress users
	 * @param int $number
	 * @param int $offset
	 *
	 * @return array
	 */
	private static function get_wp_users($number = 70, $offset = 0) {
		$args = array(
			'number' => $number,
			'offset' => $offset,
			'role__in' => array('administrator', 'editor', 'author')
		);

		$user_query = new WP_User_Query($args);

		return $user_query->get_results();
	}


    /**
     * Get TD Cloud Library templates
     * @param $type - the template type
     * @return array - array of templates by given type as 'template id' => template name
     */
    private static function get_tdb_templates( $type ) {
        $tdb_templates = array();

        $args = array(
	        'post_type' => array('tdb_templates'),
	        'post_status' => 'publish',
	        'meta_query' => array(
	            array(
	                'key'     => 'tdc_is_mobile_template',
	                'compare' => 'NOT EXISTS'
	            )
	        ),
	        'posts_per_page' => '-1'
	    );

        if ( !empty($type) ) {

			// for tags & attributes template types get the 'woo_archive' type templates
			if ( in_array( $type, array( 'woo_archive_tag', 'woo_archive_attribute' ) ) ) {
				$type = 'woo_archive';
			}

        	$args['meta_query'][] = array(
                'key'     => 'tdb_template_type',
                'value'   => $type,
            );
        }

	    $wp_query_templates = new WP_Query( $args );

        if ( !empty( $wp_query_templates->posts ) ) {
            foreach ( $wp_query_templates->posts as $post ) {
                $tdb_template_type = get_post_meta( $post->ID, 'tdb_template_type', true );

                if ( $tdb_template_type === $type ) {
                    $tdb_templates['tdb_template_' . $post->ID] = $post->post_title;
                }
            }
        }

        return $tdb_templates;
    }

}
