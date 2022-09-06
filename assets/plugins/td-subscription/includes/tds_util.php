<?php


class tds_util {

	private static $tds_options = [];

	/**
	 * determines if an email(cpt) exists in a list(custom tax) based on email(post title)
	 *
	 * @param string $email - tds email (post title)
	 * @param int $list - tds list (taxonomy) id
	 *
	 * @return int post ID if post exists, 0 otherwise
	 *
	 */
	static function exists( string $email, int $list ): int {
		global $wpdb;

		$post_title = wp_unslash( sanitize_post_field( 'post_title', $email, 0, 'db' ) );
		$list_id = wp_unslash( sanitize_term_field( 'term_id', $list, $list, 'tds_list', 'db' ) );

		$join = " INNER JOIN $wpdb->term_relationships ON ($wpdb->posts.ID = $wpdb->term_relationships.object_id)";
		$where = " WHERE 1=1";

		$query = "SELECT ID FROM $wpdb->posts";
		$query .= $join;
		$query .= $where;

		$args = array();

		$query .= " AND post_title = %s";
		$args[] = $post_title;

		$query .= " AND post_type = 'tds_email'";
		$query .= " AND post_status = 'publish'";

		$query .= " AND $wpdb->term_relationships.term_taxonomy_id = %d";
		$args[] = $list_id;

		return (int) $wpdb->get_var( $wpdb->prepare( $query, $args ) );

	}


	/**
	 * determines if a shortcode exists in content
	 *
	 * @param string $content - post content
	 * @param string $shortcode - the shortcode to search for
	 *
	 * @return bool - true if shortcode has been found, false otherwise
	 *
	 */
	static function get_shortcode( $content, $shortcode ) {

		// parse content shortcode
		preg_match_all( '/\[(.*?)\]/', $content, $matches );

		// search for the shortcode
		if ( !empty( $matches[0] ) and is_array( $matches[0] ) ) {
			foreach ( $matches[0] as $match ) {
				if ( strpos( $match, $shortcode ) !== false ) {
					return true;
				}
			}
		}

		return false;
	}


	/**
	 * retrieves if a shortcode attribute if the shortcode is found and the att exists in given content
	 *
	 * @param string $content - post content
	 * @param string $shortcode - the shortcode to search for
	 * @param string $att - the shortcode attribute to search for
	 *
	 * @return string - the attribute value if the shortcode att has been found or an empty string otherwise
	 *
	 */
	static function get_shortcode_att( $content, $shortcode, $att ) {

		// parse content shortcode
		preg_match_all( '/\[(.*?)\]/', $content, $matches );

		// search for the shortcode
		if ( !empty( $matches[0] ) and is_array( $matches[0] ) ) {
			foreach ( $matches[0] as $match ) {
				if ( strpos( $match, $shortcode ) !== false ) {
					$shortcode = $match;
				}
			}
		}

		// get the shortcode att if we have a shortcode match
		if ( !empty( $shortcode ) ) {
			$shortcode = str_replace( array( '[',']' ), '', $shortcode );
			$shortcode_atts = shortcode_parse_atts( $shortcode );

			if ( isset( $shortcode_atts[$att] ) ) {
				return $shortcode_atts[$att];
			}
		}

		return '';
	}

	static function enqueue_js_files_array($js_files_array, $dependency_array) {
		$last_js_file_id = '';
		foreach ($js_files_array as $js_file_id => $js_file) {
			if ($last_js_file_id == '') {
				wp_enqueue_script($js_file_id, TDS_URL . $js_file, $dependency_array, TD_SUBSCRIPTION, true); //first, load it with jQuery dependency
			} else {
				wp_enqueue_script($js_file_id, TDS_URL. $js_file, array($last_js_file_id), TD_SUBSCRIPTION, true);  //not first - load with the last file dependency
			}
			$last_js_file_id = $js_file_id;
		}
	}



	/**
	 * Retrieve an option from subscription settings
	 *
	 * @param $option_name
	 *
	 * @return null
	 */
	static function get_tds_option($option_name) {
		global $wpdb;

		$table_name = 'tds_options';
		if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
			$get_result = $wpdb->get_var( $wpdb->prepare( "SELECT value FROM tds_options WHERE name = %s", $option_name ) );
			if ( false !== $get_result ) {
				return $get_result;
			}
		}
		return null;
	}


	static function set_tds_option($option_name, $option_value) {
		global $wpdb;

		$table_name = 'tds_options';
		if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
			$get_result = $wpdb->get_var( $wpdb->prepare( "SELECT count(*) FROM tds_options WHERE name = %s", $option_name ) );
			if ( false !== $get_result ) {
				if ( '0' === $get_result ) {
					$insert_result = $wpdb->insert( 'tds_options',
						array(
							'name'  => $option_name,
							'value' => $option_value,
						),
						array( '%s', '%s' ) );

					if ( false !== $insert_result ) {
						$result[ 'inserted_id' ] = $wpdb->insert_id;

						if ( 'go_wizard' === $option_name && '1' == $option_value ) {
							update_option( 'users_can_register', true );
						}

						return $result;
					}
				} else {
					$update_result = $wpdb->update( 'tds_options',
						array(
							'name'  => $option_name,
							'value' => $option_value
						),
						array( 'name' => $option_name ),
						array( '%s', '%s' ),
						array( '%s' )
					);

					if ( false !== $update_result ) {
						$result[ 'success' ] = true;

						if ( 'go_wizard' === $option_name && '1' == $option_value ) {
							update_option( 'users_can_register', true );
						}

						return $result;
					}
				}
			}
		}
		return null;
	}


	static function get_subscriptions($user_id = null, $all = true) {
		global $wpdb;

		$result = [];
		$payment_bank = false;
		$payment_paypal = false;
		$subscriptions_query = "SELECT
					tds_subscriptions.*, 
					tds_plans.name AS 'plan_name' 
				FROM 
					tds_subscriptions 
					LEFT JOIN tds_plans
					ON tds_subscriptions.plan_id = tds_plans.id";

		if (!$all) {
			$subscriptions_query .= " AND tds_subscriptions.status <> 'closed'";
		}

		if (is_null($user_id)) {
			$subscriptions_query .= " ORDER BY tds_subscriptions.ID desc";
			$subscriptions = $wpdb->get_results($subscriptions_query, ARRAY_A);
		} else {
			$subscriptions_query .= " WHERE tds_subscriptions.user_id = %s ORDER BY tds_subscriptions.ID desc";
			$subscriptions = $wpdb->get_results($wpdb->prepare( $subscriptions_query, $user_id), ARRAY_A);
		}

		if ( null !== $subscriptions) {

			if (count($subscriptions)) {

				$subscriptions_modified = false;

				foreach ($subscriptions as &$subscription) {

					if (!empty($subscriptions['is_free'])) {
						continue;
					}

//					var_dump(date( "Y-m-d", strtotime( "2009-01-31 +1 month" ) )); // PHP:  2009-03-03
//					var_dump(date( "Y-m-d", strtotime( "2009-01-31 +2 month" ) )); // PHP:  2009-03-31

					// change subscription status - on request
					switch ($subscription['status']) {

						case 'trial':
							if (!empty($subscription['trial_days'])) {
								$start_date = date_create_from_format('Y-m-d H:i:s', $subscription['start_date']);
								if (strtotime('now') > strtotime('+' . $subscription['trial_days'] . ' days', date_timestamp_get($start_date))) {


									$update_result = $wpdb->update( 'tds_subscriptions',
										array(
											'status'     => 'waiting_payment',
											'start_day' => date('j'),
											'start_date' => date('Y-m-d'),
											'price' => empty($subscription['next_price']) ? $subscription['price'] : $subscription['next_price'],
										),
										array( 'id' => $subscription['id'] ),
										array('%s', '%s', '%s', '%s'),
										array( '%d' )
									);

									if ( false !== $update_result ) {
										$subscriptions_modified = true;
									}
								}
							}
							break;

						case 'active':

							//$start_date = date_create_from_format('Y-m-d H:i:s', $subscription['start_date']);

							$start_date = $subscription['start_date'];
							$last_months_in_cycle = $subscription['last_months_in_cycle'];
							$start_day = $subscription['start_day'];
							$end_date = self::get_end_date($start_date, $last_months_in_cycle, $start_day);

							$start_date_next_cycle = self::get_next_day_date($end_date);
							$end_date_next_cycle = self::get_end_date(date_format($start_date_next_cycle, 'Y-m-d'), $last_months_in_cycle, $start_day);

							$start_date_next_next_cycle = self::get_next_day_date($end_date_next_cycle);

							if (strtotime('now') > date_timestamp_get($start_date_next_next_cycle)) {
								$update_result = $wpdb->update( 'tds_subscriptions',
									array(
										'status'     => 'closed',
									),
									array( 'id' => $subscription['id'] ),
									array( '%s' ),
									array( '%d' )
								);

								// Add a new subscription from the current date - with gap
								if ( false !== $update_result ) {
									$data_values = array(
										'plan_id'              => $subscription['plan_id'],
										'user_id'              => $subscription['user_id'],
										'ref_id' => $subscription['id'],
										'price' => empty($subscription['next_price']) ? $subscription['price'] : $subscription['next_price'],
										'next_price' => $subscription['next_price'],
										'curr_name' => $subscription['curr_name'],
										'curr_pos' => $subscription['curr_pos'],
										'curr_th_sep' => $subscription['curr_th_sep'],
										'curr_dec_sep' => $subscription['curr_dec_sep'],
										'curr_dec_no' => $subscription['curr_dec_no'],
										'billing_first_name' => $subscription['billing_first_name'],
										'billing_last_name' => $subscription['billing_last_name'],
										'billing_company_name' => $subscription['billing_company_name'],
										'billing_cui' => $subscription['billing_cui'],
										'billing_j' => $subscription['billing_j'],
										'billing_address' => $subscription['billing_address'],
										'billing_county' => $subscription['billing_county'],
										'billing_city' => $subscription['billing_city'],
										'billing_country' => $subscription['billing_country'],
										'billing_phone' => $subscription['billing_phone'],
										'billing_email' => $subscription['billing_email'],
										'billing_bank_account' => $subscription['billing_bank_account'],
										'billing_post_code' => $subscription['billing_post_code'],
										'billing_vat_number' => $subscription['billing_vat_number'],
										'payment_type'         => $subscription['payment_type'],
										'status'               => 'waiting_payment',
										'last_months_in_cycle' => $last_months_in_cycle,
										'start_day' => $subscription['start_day'],
										'start_date' => date('Y-m-d'),
										'created_at' => date('Y-m-d H:i:s'),
									);
									$data_format = array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', );

									global $wpdb;
									$wpdb->suppress_errors = true;

									$insert_result = $wpdb->insert( 'tds_subscriptions',
										$data_values,
										$data_format );

									if ( false !== $insert_result ) {
										$subscriptions_modified = true;
									}
								}

							} else if (strtotime('now') > date_timestamp_get($start_date_next_cycle)) {

								$update_result = $wpdb->update( 'tds_subscriptions',
									array(
										'status'     => 'closed',
									),
									array( 'id' => $subscription['id'] ),
									array( '%s' ),
									array( '%d' )
								);

								if ( false !== $update_result ) {
									$data_values = array(
										'plan_id'              => $subscription['plan_id'],
										'user_id'              => $subscription['user_id'],
										'ref_id' => $subscription['id'],
										'price' => empty($subscription['next_price']) ? $subscription['price'] : $subscription['next_price'],
										'next_price' => $subscription['next_price'],
										'curr_name' => $subscription['curr_name'],
										'curr_pos' => $subscription['curr_pos'],
										'curr_th_sep' => $subscription['curr_th_sep'],
										'curr_dec_sep' => $subscription['curr_dec_sep'],
										'curr_dec_no' => $subscription['curr_dec_no'],
										'billing_first_name' => $subscription['billing_first_name'],
										'billing_last_name' => $subscription['billing_last_name'],
										'billing_company_name' => $subscription['billing_company_name'],
										'billing_cui' => $subscription['billing_cui'],
										'billing_j' => $subscription['billing_j'],
										'billing_address' => $subscription['billing_address'],
										'billing_county' => $subscription['billing_county'],
										'billing_city' => $subscription['billing_city'],
										'billing_country' => $subscription['billing_country'],
										'billing_phone' => $subscription['billing_phone'],
										'billing_email' => $subscription['billing_email'],
										'billing_bank_account' => $subscription['billing_bank_account'],
										'billing_post_code' => $subscription['billing_post_code'],
										'billing_vat_number' => $subscription['billing_vat_number'],
										'payment_type'         => $subscription['payment_type'],
										'status'               => 'waiting_payment',
										'last_months_in_cycle' => $last_months_in_cycle,
										'start_day' => $subscription['start_day'],
										'start_date' => $start_date_next_cycle->format('Y-m-d'),
										'created_at' => date('Y-m-d H:i:s'),
									);
									$data_format = array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%d', '%s');

									global $wpdb;
									$wpdb->suppress_errors = true;

									$insert_result = $wpdb->insert( 'tds_subscriptions',
										$data_values,
										$data_format );

									if ( false !== $insert_result ) {
										$subscriptions_modified = true;
									}
								}
							}
							break;

						case 'waiting_payment':

							$start_date = $subscription['start_date'];
							$last_months_in_cycle = $subscription['last_months_in_cycle'];
							$start_day = $subscription['start_day'];

							$end_date = self::get_end_date($start_date, $last_months_in_cycle, $start_day);

							if (strtotime('now') > date_timestamp_get($end_date)) {

								$update_result = $wpdb->update( 'tds_subscriptions',
									array(
										'status'     => 'closed_not_paid',
									),
									array( 'id' => $subscription['id'] ),
									array( '%s' ),
									array( '%d' )
								);

								if ( false !== $update_result ) {
									$subscriptions_modified = true;
								}
							}
							break;
					}
				}

				if ($subscriptions_modified) {
					$subscriptions = $wpdb->get_results($wpdb->prepare( $subscriptions_query, get_current_user_id()), ARRAY_A);
				}
			}

			if (null !== $subscriptions && count($subscriptions)) {

				foreach ( $subscriptions as &$subscription ) {

					if ( ! empty( $subscription[ 'user_id' ] ) ) {
						$subscription[ 'user_name' ] = get_user_meta( $subscription[ 'user_id' ], 'nickname', true );
					}
					$subscription[ 'end_date' ] = tds_util::get_end_date( $subscription[ 'start_date' ], $subscription[ 'last_months_in_cycle' ], $subscription[ 'start_day' ], 'trial' === $subscription['status'] ? $subscription['trial_days'] : 0)->format( 'Y-m-d' );

					if (empty($subscription['curr_name'])) {
						$subscription[ 'formatted_price' ] = tds_util::get_basic_currency( $subscription[ 'price' ] );
					} else {
						$subscription[ 'formatted_price' ] = tds_util::get_formatted_currency( $subscription[ 'price' ], $subscription[ 'curr_name' ], $subscription[ 'curr_pos' ], $subscription[ 'curr_th_sep' ], $subscription[ 'curr_dec_sep' ], $subscription[ 'curr_dec_no' ] );
					}

					switch ($subscription['payment_type']) {

						case 'direct':
							if (false === $payment_bank) {
								$payment_bank = $wpdb->get_results( "SELECT * FROM tds_payment_bank LIMIT 1", ARRAY_A );
							}
							if ( null !== $payment_bank && is_array($payment_bank) && count($payment_bank) && 1 == $payment_bank[0]['is_active']) {
								foreach ($payment_bank[0] as $key => $val) {
									if (in_array($key, ['id', 'is_active', 'created_at'])) {
										continue;
									}
									$subscription[$key] = $val;
								}
							}
							break;

						case 'paypal':
							if (false === $payment_paypal) {
								$payment_paypal = $wpdb->get_results( "SELECT * FROM tds_payment_paypal LIMIT 1", ARRAY_A );
							}
							if ( null !== $payment_paypal && is_array($payment_paypal) && count($payment_paypal) && 1 == $payment_paypal[0]['is_active']) {
								foreach ($payment_paypal[0] as $key => $val) {
									if (in_array($key, ['id', 'is_active', 'created_at'])) {
										continue;
									}
									$subscription[$key] = $val;
								}
							}
							break;
					}
				}
			}

			$result['subscriptions'] = $subscriptions;
		}
		return $result;
	}

	static function get_end_date($start_date, $last_months_in_cycle, $start_day, $trial_days = 0) {

		$parsed_date = date_parse($start_date);

		$new_year = intval($parsed_date['year']);
		$new_month = intval($parsed_date['month']);
		$new_day = intval($parsed_date['day']);
		$start_day = intval($start_day);

		if (!empty($trial_days)) {
			$trial_days = intval($trial_days) - 1;
			if ($trial_days <= 0) {
				$trial_days = 0;
			}
			return new DateTime( date( 'Y-m-d', strtotime($start_date . ' + ' . $trial_days . 'days')));
		} else {

			if ( $new_day - 1 ) {
				$new_day = $new_day - 1;
			} else {
				$new_day   = 31;
				$new_month = $new_month - 1;
			}

			$new_day = $start_day - 1 > $new_day ? $start_day - 1 : $new_day;

			switch ( $last_months_in_cycle ) {
				case '1':
					if ( 12 === $new_month ) {
						$new_month = 1;
						$new_year ++;
					} else {
						$new_month ++;
					}
					break;

				case '12':
					$new_year ++;
					break;
			}

			$tries = 3;
			while ( $new_day > 0 && ! checkdate( $new_month, $new_day, $new_year ) && $tries > 0 ) {
				$new_day --;
				$tries --;

				if ( ! $new_day ) {
					$new_day   = 31;
					$new_month = $new_month - 1;
				}
			}
		}

		return date_create_from_format('Y-m-d H:i:s',
			$new_year . '-' .
			$new_month . '-' .
            $new_day . ' 00:00:00'
		);
	}

	static function get_next_day_date($end_date) {
		return new DateTime( date( 'Y-m-d', strtotime($end_date->format('Y-m-d') . ' + 1 days')));
	}

	static function clean_param( $var ) {
		if ( is_array( $var ) ) {
			return array_map( 'clean_param', $var );
		} else {
			return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
		}
	}

	static function get_wizard_pages() {
		$items = [
			'paymentPage' => [
				'db_id' => 'payment_page_id',
				'data' => [
					'post_type' => 'page',
					'post_title' => 'Checkout',
					'post_name' => 'tds-checkout',
					'post_status' => 'publish',
					'post_content' => '[tdc_zone type="tdc_content"][vc_row tdc_css="eyJhbGwiOnsicGFkZGluZy10b3AiOiI0MSIsInBhZGRpbmctYm90dG9tIjoiNDgiLCJkaXNwbGF5IjoiIn0sInBvcnRyYWl0Ijp7InBhZGRpbmctdG9wIjoiMzUiLCJwYWRkaW5nLWJvdHRvbSI6IjQyIiwiZGlzcGxheSI6IiJ9LCJwb3J0cmFpdF9tYXhfd2lkdGgiOjEwMTgsInBvcnRyYWl0X21pbl93aWR0aCI6NzY4LCJwaG9uZSI6eyJwYWRkaW5nLXRvcCI6IjI1IiwicGFkZGluZy1ib3R0b20iOiIzMiIsImRpc3BsYXkiOiIifSwicGhvbmVfbWF4X3dpZHRoIjo3Njd9"][vc_column][tdb_breadcrumbs tdicon="td-icon-right" show_home="yes"][tdm_block_column_title title_text="Q2hlY2tvdXQ=" title_tag="h3" title_size="tdm-title-sm" tds_title1-f_title_font_weight="400" tds_title1-f_title_font_size="eyJhbGwiOiIzMCIsInBvcnRyYWl0IjoiMjQifQ==" tds_title1-f_title_font_line_height="1.3" tdc_css="eyJhbGwiOnsibWFyZ2luLXRvcCI6Ii0xOCIsIm1hcmdpbi1ib3R0b20iOiIyMSIsImRpc3BsYXkiOiIifSwicG9ydHJhaXQiOnsibWFyZ2luLWJvdHRvbSI6IjE4IiwiZGlzcGxheSI6IiJ9LCJwb3J0cmFpdF9tYXhfd2lkdGgiOjEwMTgsInBvcnRyYWl0X21pbl93aWR0aCI6NzY4fQ=="][tds_payment][/vc_column][/vc_row][/tdc_zone]'
				]
			],
			'myAccountPage' => [
				'db_id' => 'my_account_page_id',
				'data' => [
					'post_type' => 'page',
					'post_title' => 'My account',
					'post_name' => 'tds-my-account',
					'post_status' => 'publish',
					'post_content' => '[tdc_zone type="tdc_content"][vc_row tdc_css="eyJhbGwiOnsicGFkZGluZy10b3AiOiI0MSIsInBhZGRpbmctYm90dG9tIjoiNDgiLCJkaXNwbGF5IjoiIn0sInBvcnRyYWl0Ijp7InBhZGRpbmctdG9wIjoiMzUiLCJwYWRkaW5nLWJvdHRvbSI6IjQyIiwiZGlzcGxheSI6IiJ9LCJwb3J0cmFpdF9tYXhfd2lkdGgiOjEwMTgsInBvcnRyYWl0X21pbl93aWR0aCI6NzY4LCJwaG9uZSI6eyJwYWRkaW5nLXRvcCI6IjI1IiwicGFkZGluZy1ib3R0b20iOiIzMiIsImRpc3BsYXkiOiIifSwicGhvbmVfbWF4X3dpZHRoIjo3Njd9"][vc_column][tdb_breadcrumbs tdicon="td-icon-right" show_home="yes"][tdm_block_column_title title_text="TXklMjBhY2NvdW50" title_tag="h3" title_size="tdm-title-sm" tds_title1-f_title_font_weight="400" tds_title1-f_title_font_size="eyJhbGwiOiIzMCIsInBvcnRyYWl0IjoiMjQifQ==" tds_title1-f_title_font_line_height="1.3" tdc_css="eyJhbGwiOnsibWFyZ2luLXRvcCI6Ii0xOCIsIm1hcmdpbi1ib3R0b20iOiIyMSIsImRpc3BsYXkiOiIifSwicG9ydHJhaXQiOnsibWFyZ2luLWJvdHRvbSI6IjE4IiwiZGlzcGxheSI6IiJ9LCJwb3J0cmFpdF9tYXhfd2lkdGgiOjEwMTgsInBvcnRyYWl0X21pbl93aWR0aCI6NzY4fQ=="][tds_my_account][/vc_column][/vc_row][/tdc_zone]',
				]
			],
			'createAccountPage' => [
				'db_id' => 'create_account_page_id',
				'data' => [
					'post_type' => 'page',
					'post_title' => 'Login/Register',
					'post_name' => 'tds-login-register',
					'post_status' => 'publish',
					'post_content' => '[tdc_zone type="tdc_content"][vc_row tdc_css="eyJhbGwiOnsibWFyZ2luLXRvcCI6IjQ4IiwibWFyZ2luLWJvdHRvbSI6IjQ4IiwicGFkZGluZy10b3AiOiI2MCIsInBhZGRpbmctYm90dG9tIjoiNjAiLCJiYWNrZ3JvdW5kLWNvbG9yIjoiI2Y3ZjdmNyIsImJhY2tncm91bmQtcG9zaXRpb24iOiJjZW50ZXIgY2VudGVyIiwib3BhY2l0eSI6Ii41IiwiZGlzcGxheSI6IiJ9LCJwb3J0cmFpdCI6eyJwYWRkaW5nLXRvcCI6IjM1IiwicGFkZGluZy1ib3R0b20iOiI0MiIsImRpc3BsYXkiOiIifSwicG9ydHJhaXRfbWF4X3dpZHRoIjoxMDE4LCJwb3J0cmFpdF9taW5fd2lkdGgiOjc2OCwicGhvbmUiOnsicGFkZGluZy10b3AiOiIyNSIsInBhZGRpbmctYm90dG9tIjoiMzIiLCJkaXNwbGF5IjoiIn0sInBob25lX21heF93aWR0aCI6NzY3fQ==" flex_layout="row" flex_vert_align="center" flex_order="2" gap="0"][vc_column flex_layout="row" flex_vert_align="flex-start" flex_horiz_align="center"][tds_create_account tdc_css="eyJhbGwiOnsicGFkZGluZy10b3AiOiIzMCIsInBhZGRpbmctcmlnaHQiOiIyNSIsInBhZGRpbmctYm90dG9tIjoiMzUiLCJwYWRkaW5nLWxlZnQiOiIyNSIsImJvcmRlci1yYWRpdXMiOiIzIiwid2lkdGgiOiI0MCUiLCJzaGFkb3ctc2l6ZSI6IjQiLCJzaGFkb3ctY29sb3IiOiJyZ2JhKDAsMCwwLDAuMTIpIiwic2hhZG93LW9mZnNldC1oIjoiMCIsInNoYWRvdy1vZmZzZXQtdiI6IjIiLCJiYWNrZ3JvdW5kLWNvbG9yIjoiI2ZmZmZmZiIsImRpc3BsYXkiOiIifSwicGhvbmUiOnsid2lkdGgiOiIxMDAlIiwiZGlzcGxheSI6IiJ9LCJwaG9uZV9tYXhfd2lkdGgiOjc2NywicG9ydHJhaXQiOnsid2lkdGgiOiI2MCUiLCJkaXNwbGF5IjoiIn0sInBvcnRyYWl0X21heF93aWR0aCI6MTAxOCwicG9ydHJhaXRfbWluX3dpZHRoIjo3NjgsImxhbmRzY2FwZSI6eyJ3aWR0aCI6IjUwJSIsImRpc3BsYXkiOiIifSwibGFuZHNjYXBlX21heF93aWR0aCI6MTE0MCwibGFuZHNjYXBlX21pbl93aWR0aCI6MTAxOX0="][/vc_column][/vc_row][/tdc_zone]',
				]
			]
		];

        if (TD_THEME_NAME === 'Newsmag') {
            $items['paymentPage']['data']['post_content'] = '[tdc_zone type="tdc_content"][vc_row][vc_column][tds_payment][/vc_column][/vc_row][/tdc_zone]';
            $items['myAccountPage']['data']['post_content'] = '[tdc_zone type="tdc_content"][vc_row ][vc_column][tds_my_account][/vc_column][/vc_row][/tdc_zone]';
            $items['createAccountPage']['data']['post_content'] = '[tdc_zone type="tdc_content"][vc_row][vc_column][tds_create_account][/vc_column][/vc_row][/tdc_zone]';
        }

		return $items;
	}

	static function get_tds_options() {
		if (empty(self::$tds_options)) {
			global $wpdb;

			$results = $wpdb->get_results("SELECT * FROM tds_options", ARRAY_A);

			if ( null !== $results) {
				self::$tds_options = $results;
			}
		}
		return self::$tds_options;
	}


    static function is_my_account_page() {
        $page_id = self::get_tds_option('my_account_page_id');

        return ( $page_id && is_page( $page_id ) ) || self::post_content_has_shortcode('tds_ny_account');
    }

    static function is_login_register_page() {
        $page_id = self::get_tds_option('my_create_account_id');

        return ( $page_id && is_page( $page_id ) ) || self::post_content_has_shortcode('tds_create_account');
    }

    static function is_checkout_page() {
        $page_id = self::get_tds_option('payment_page_id');

        return ( $page_id && is_page( $page_id ) ) || self::post_content_has_shortcode('tds_payment');
    }


    static function post_content_has_shortcode( $shortcode_tag = '' ) {
        global $post;

	    return is_singular() && is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, $shortcode_tag );
    }

	static function maybe_add_default_template() {
		global $wpdb;

		$default_tpl = $wpdb->get_results( "SELECT * FROM tds_trackings_templates WHERE name = 'Default Template'", ARRAY_A );

		if ( !$default_tpl ) {

			$wpdb->insert( 'tds_trackings_templates',
				array(
					'name' => 'Default Template',
					'page_html' => file_get_contents( TDS_PATH . '/includes/link_conversion/default_tpl.html' )
				),
				array( '%s', '%s' )
			);

		}
	}

	static function get_currency($val = '', $only_symbol = false) {
		$curr = [
			"AED" => "United Arab Emirates dirham (د.إ)",
	        "AFN" => "Afghan afghani (؋)",
	        "ALL" => "Albanian lek (L)",
	        "AMD" => "Armenian dram (AMD)",
	        "ANG" => "Netherlands Antillean guilder (ƒ)",
	        "AOA" => "Angolan kwanza (Kz)",
	        "ARS" => "Argentine peso ($)",
	        "AUD" => "Australian dollar ($)",
	        "AWG" => "Aruban florin (Afl.)",
	        "AZN" => "Azerbaijani manat (AZN)",
	        "BAM" => "Bosnia and Herzegovina convertible mark (KM)",
	        "BBD" => "Barbadian dollar ($)",
	        "BDT" => "Bangladeshi taka (৳&nbsp;)",
	        "BGN" => "Bulgarian lev (лв.)",
	        "BHD" => "Bahraini dinar (.د.ب)",
	        "BIF" => "Burundian franc (Fr)",
	        "BMD" => "Bermudian dollar ($)",
	        "BND" => "Brunei dollar ($)",
	        "BOB" => "Bolivian boliviano (Bs.)",
	        "BRL" => "Brazilian real (R$)",
	        "BSD" => "Bahamian dollar ($)",
	        "BTN" => "Bhutanese ngultrum (Nu.)",
	        "BWP" => "Botswana pula (P)",
	        "BYR" => "Belarusian ruble (old) (Br)",
	        "BYN" => "Belarusian ruble (Br)",
	        "BZD" => "Belize dollar ($)",
	        "CAD" => "Canadian dollar ($)",
	        "CDF" => "Congolese franc (Fr)",
	        "CHF" => "Swiss franc (CHF)",
	        "CLP" => "Chilean peso ($)",
	        "CNY" => "Chinese yuan (¥)",
	        "COP" => "Colombian peso ($)",
	        "CRC" => "Costa Rican colón (₡)",
	        "CUC" => "Cuban convertible peso ($)",
	        "CUP" => "Cuban peso ($)",
	        "CVE" => "Cape Verdean escudo ($)",
	        "CZK" => "Czech koruna (Kč)",
	        "DJF" => "Djiboutian franc (Fr)",
	        "DKK" => "Danish krone (DKK)",
	        "DOP" => "Dominican peso (RD$)",
	        "DZD" => "Algerian dinar (د.ج)",
	        "EGP" => "Egyptian pound (EGP)",
	        "ERN" => "Eritrean nakfa (Nfk)",
	        "ETB" => "Ethiopian birr (Br)",
	        "EUR" => "Euro (€)",
	        "FJD" => "Fijian dollar ($)",
	        "FKP" => "Falkland Islands pound (£)",
	        "GBP" => "Pound sterling (£)",
	        "GEL" => "Georgian lari (₾)",
	        "GGP" => "Guernsey pound (£)",
	        "GHS" => "Ghana cedi (₵)",
	        "GIP" => "Gibraltar pound (£)",
	        "GMD" => "Gambian dalasi (D)",
	        "GNF" => "Guinean franc (Fr)",
	        "GTQ" => "Guatemalan quetzal (Q)",
	        "GYD" => "Guyanese dollar ($)",
	        "HKD" => "Hong Kong dollar ($)",
	        "HNL" => "Honduran lempira (L)",
	        "HRK" => "Croatian kuna (kn)",
	        "HTG" => "Haitian gourde (G)",
	        "HUF" => "Hungarian forint (Ft)",
	        "IDR" => "Indonesian rupiah (Rp)",
	        "ILS" => "Israeli new shekel (₪)",
	        "IMP" => "Manx pound (£)",
	        "INR" => "Indian rupee (₹)",
	        "IQD" => "Iraqi dinar (ع.د)",
	        "IRR" => "Iranian rial (﷼)",
	        "IRT" => "Iranian toman (تومان)",
	        "ISK" => "Icelandic króna (kr.)",
	        "JEP" => "Jersey pound (£)",
	        "JMD" => "Jamaican dollar ($)",
	        "JOD" => "Jordanian dinar (د.ا)",
	        "JPY" => "Japanese yen (¥)",
	        "KES" => "Kenyan shilling (KSh)",
	        "KGS" => "Kyrgyzstani som (сом)",
	        "KHR" => "Cambodian riel (៛)",
	        "KMF" => "Comorian franc (Fr)",
	        "KPW" => "North Korean won (₩)",
	        "KRW" => "South Korean won (₩)",
	        "KWD" => "Kuwaiti dinar (د.ك)",
	        "KYD" => "Cayman Islands dollar ($)",
	        "KZT" => "Kazakhstani tenge (₸)",
	        "LAK" => "Lao kip (₭)",
	        "LBP" => "Lebanese pound (ل.ل)",
	        "LKR" => "Sri Lankan rupee (රු)",
	        "LRD" => "Liberian dollar ($)",
	        "LSL" => "Lesotho loti (L)",
	        "LYD" => "Libyan dinar (ل.د)",
	        "MAD" => "Moroccan dirham (د.م.)",
	        "MDL" => "Moldovan leu (MDL)",
	        "MGA" => "Malagasy ariary (Ar)",
	        "MKD" => "Macedonian denar (ден)",
	        "MMK" => "Burmese kyat (Ks)",
	        "MNT" => "Mongolian tögrög (₮)",
	        "MOP" => "Macanese pataca (P)",
	        "MRU" => "Mauritanian ouguiya (UM)",
	        "MUR" => "Mauritian rupee (₨)",
	        "MVR" => "Maldivian rufiyaa (.ރ)",
	        "MWK" => "Malawian kwacha (MK)",
	        "MXN" => "Mexican peso ($)",
	        "MYR" => "Malaysian ringgit (RM)",
	        "MZN" => "Mozambican metical (MT)",
	        "NAD" => "Namibian dollar (N$)",
	        "NGN" => "Nigerian naira (₦)",
	        "NIO" => "Nicaraguan córdoba (C$)",
	        "NOK" => "Norwegian krone (kr)",
	        "NPR" => "Nepalese rupee (₨)",
	        "NZD" => "New Zealand dollar ($)",
	        "OMR" => "Omani rial (ر.ع.)",
	        "PAB" => "Panamanian balboa (B/.)",
	        "PEN" => "Sol (S/)",
	        "PGK" => "Papua New Guinean kina (K)",
	        "PHP" => "Philippine peso (₱)",
	        "PKR" => "Pakistani rupee (₨)",
	        "PLN" => "Polish złoty (zł)",
	        "PRB" => "Transnistrian ruble (р.)",
	        "PYG" => "Paraguayan guaraní (₲)",
	        "QAR" => "Qatari riyal (ر.ق)",
	        "RON" => "Romanian leu (lei)",
	        "RSD" => "Serbian dinar (рсд)",
	        "RUB" => "Russian ruble (₽)",
	        "RWF" => "Rwandan franc (Fr)",
	        "SAR" => "Saudi riyal (ر.س)",
	        "SBD" => "Solomon Islands dollar ($)",
	        "SCR" => "Seychellois rupee (₨)",
	        "SDG" => "Sudanese pound (ج.س.)",
	        "SEK" => "Swedish krona (kr)",
	        "SGD" => "Singapore dollar ($)",
	        "SHP" => "Saint Helena pound (£)",
	        "SLL" => "Sierra Leonean leone (Le)",
	        "SOS" => "Somali shilling (Sh)",
	        "SRD" => "Surinamese dollar ($)",
	        "SSP" => "South Sudanese pound (£)",
	        "STN" => "São Tomé and Príncipe dobra (Db)",
	        "SYP" => "Syrian pound (ل.س)",
	        "SZL" => "Swazi lilangeni (L)",
	        "THB" => "Thai baht (฿)",
	        "TJS" => "Tajikistani somoni (ЅМ)",
	        "TMT" => "Turkmenistan manat (m)",
	        "TND" => "Tunisian dinar (د.ت)",
	        "TOP" => "Tongan paʻanga (T$)",
	        "TRY" => "Turkish lira (₺)",
	        "TTD" => "Trinidad and Tobago dollar ($)",
	        "TWD" => "New Taiwan dollar (NT$)",
	        "TZS" => "Tanzanian shilling (Sh)",
	        "UAH" => "Ukrainian hryvnia (₴)",
	        "UGX" => "Ugandan shilling (UGX)",
	        "USD" => "United States (US) dollar ($)",
	        "UYU" => "Uruguayan peso ($)",
	        "UZS" => "Uzbekistani som (UZS)",
	        "VEF" => "Venezuelan bolívar (Bs F)",
	        "VES" => "Bolívar soberano (Bs.S)",
	        "VND" => "Vietnamese đồng (₫)",
	        "VUV" => "Vanuatu vatu (Vt)",
	        "WST" => "Samoan tālā (T)",
	        "XAF" => "Central African CFA franc (CFA)",
	        "XCD" => "East Caribbean dollar ($)",
	        "XOF" => "West African CFA franc (CFA)",
	        "XPF" => "CFP franc (Fr)",
	        "YER" => "Yemeni rial (﷼)",
	        "ZAR" => "South African rand (R)",
	        "ZMW" => "Zambian kwacha (ZK)"
		];

		if (empty($val)) {
			return $curr;
		}

		if (!empty($curr[$val])) {
			if ($only_symbol) {
				preg_match('/.*\((.*)\)/', $curr[$val], $data);
				if (2 === count($data)) {
					return $data[1];
				}
			}
			return $curr[$val];
		}
		return '';
	}

	static function check_paypal_currency($curr, &$is_paypal = false, &$is_digit = false) {
		$paypal_currencies = [
			'AUD' => ['is_digit' => true],
			//'BRL',
			'CAD' => ['is_digit' => true],
			//'CNY',
			'CZK' => ['is_digit' => true],
			'DKK' => ['is_digit' => true],
			'EUR' => ['is_digit' => true],
			'HKD' => ['is_digit' => true],
			'HUF' => ['is_digit' => false],
			'ILS' => ['is_digit' => true],
			'JPY' => ['is_digit' => false],
			//'MYR',
			'MXN' => ['is_digit' => true],
			'TWD' => ['is_digit' => false],
			'NZD' => ['is_digit' => true],
			'NOK' => ['is_digit' => true],
			'PHP' => ['is_digit' => true],
			'PLN' => ['is_digit' => true],
			'GBP' => ['is_digit' => true],
			'RUB' => ['is_digit' => true],
			'SGD' => ['is_digit' => true],
			'SEK' => ['is_digit' => true],
			'CHF' => ['is_digit' => true],
			'THB' => ['is_digit' => true],
			'USD' => ['is_digit' => true]
		];

		foreach ($paypal_currencies as $key => $val) {
			if ($key === $curr) {
				$is_paypal = true;
				$is_digit = $val['is_digit'];
				break;
			}
		}
	}

	static function check_stripe_currency($curr, &$is_stripe = false, &$is_digit = false) {
		$stripe_currencies = [
			'AED' => ['is_digit' => true],
			'AFN' => ['is_digit' => true],
			'ALL' => ['is_digit' => true],
			'AMD' => ['is_digit' => true],
			'ANG' => ['is_digit' => true],
			'AOA' => ['is_digit' => true],
			'ARS' => ['is_digit' => true],
			'AUD' => ['is_digit' => true],
			'AWG' => ['is_digit' => true],
			'AZN' => ['is_digit' => true],
			'BAM' => ['is_digit' => true],
			'BBD' => ['is_digit' => true],
			'BDT' => ['is_digit' => true],
			'BGN' => ['is_digit' => true],
			'BHD' => ['is_digit' => true],
			'BIF' => ['is_digit' => false],
			'BMD' => ['is_digit' => true],
			'BND' => ['is_digit' => true],
			'BOB' => ['is_digit' => true],
			'BRL' => ['is_digit' => true],
			'BSD' => ['is_digit' => true],
			'BWP' => ['is_digit' => true],
			'BYN' => ['is_digit' => true],
			'BZD' => ['is_digit' => true],
			'CAD' => ['is_digit' => true],
			'CDF' => ['is_digit' => true],
			'CHF' => ['is_digit' => true],
			'CLP' => ['is_digit' => false],
			'CNY' => ['is_digit' => true],
			'COP' => ['is_digit' => true],
			'CRC' => ['is_digit' => true],
			'CVE' => ['is_digit' => true],
			'CZK' => ['is_digit' => true],
			'DJF' => ['is_digit' => false],
			'DKK' => ['is_digit' => true],
			'DOP' => ['is_digit' => true],
			'DZD' => ['is_digit' => true],
			'EGP' => ['is_digit' => true],
			'ETB' => ['is_digit' => true],
			'EUR' => ['is_digit' => true],
			'FJD' => ['is_digit' => true],
			'FKP' => ['is_digit' => true],
			'GBP' => ['is_digit' => true],
			'GEL' => ['is_digit' => true],
			'GIP' => ['is_digit' => true],
			'GMD' => ['is_digit' => true],
			'GNF' => ['is_digit' => false],
			'GTQ' => ['is_digit' => true],
			'GYD' => ['is_digit' => true],
			'HKD' => ['is_digit' => true],
			'HNL' => ['is_digit' => true],
			'HRK' => ['is_digit' => true],
			'HTG' => ['is_digit' => true],
			'HUF' => ['is_digit' => true],
			'IDR' => ['is_digit' => true],
			'ILS' => ['is_digit' => true],
			'INR' => ['is_digit' => true],
			'ISK' => ['is_digit' => true],
			'JMD' => ['is_digit' => true],
			'JOD' => ['is_digit' => true],
			'JPY' => ['is_digit' => false],
			'KES' => ['is_digit' => true],
			'KGS' => ['is_digit' => true],
			'KHR' => ['is_digit' => true],
			'KMF' => ['is_digit' => false],
			'KRW' => ['is_digit' => false],
			'KWD' => ['is_digit' => true],
			'KYD' => ['is_digit' => true],
			'KZT' => ['is_digit' => true],
			'LAK' => ['is_digit' => true],
			'LBP' => ['is_digit' => true],
			'LKR' => ['is_digit' => true],
			'LRD' => ['is_digit' => true],
			'LSL' => ['is_digit' => true],
			'MAD' => ['is_digit' => true],
			'MDL' => ['is_digit' => true],
			'MGA' => ['is_digit' => false],
			'MKD' => ['is_digit' => true],
			'MMK' => ['is_digit' => true],
			'MNT' => ['is_digit' => true],
			'MOP' => ['is_digit' => true],
			'MUR' => ['is_digit' => true],
			'MVR' => ['is_digit' => true],
			'MWK' => ['is_digit' => true],
			'MXN' => ['is_digit' => true],
			'MYR' => ['is_digit' => true],
			'MZN' => ['is_digit' => true],
			'NAD' => ['is_digit' => true],
			'NGN' => ['is_digit' => true],
			'NIO' => ['is_digit' => true],
			'NOK' => ['is_digit' => true],
			'NPR' => ['is_digit' => true],
			'NZD' => ['is_digit' => true],
			'OMR' => ['is_digit' => true],
			'PAB' => ['is_digit' => true],
			'PEN' => ['is_digit' => true],
			'PGK' => ['is_digit' => true],
			'PHP' => ['is_digit' => true],
			'PKR' => ['is_digit' => true],
			'PLN' => ['is_digit' => true],
			'PYG' => ['is_digit' => false],
			'QAR' => ['is_digit' => true],
			'RON' => ['is_digit' => true],
			'RSD' => ['is_digit' => true],
			'RUB' => ['is_digit' => true],
			'RWF' => ['is_digit' => false],
			'SAR' => ['is_digit' => true],
			'SBD' => ['is_digit' => true],
			'SCR' => ['is_digit' => true],
			'SEK' => ['is_digit' => true],
			'SGD' => ['is_digit' => true],
			'SHP' => ['is_digit' => true],
			'SLL' => ['is_digit' => true],
			'SOS' => ['is_digit' => true],
			'SRD' => ['is_digit' => true],
			'SZL' => ['is_digit' => true],
			'THB' => ['is_digit' => true],
			'TJS' => ['is_digit' => true],
			'TND' => ['is_digit' => true],
			'TOP' => ['is_digit' => true],
			'TRY' => ['is_digit' => true],
			'TTD' => ['is_digit' => true],
			'TWD' => ['is_digit' => true],
			'TZS' => ['is_digit' => true],
			'UAH' => ['is_digit' => true],
			'UGX' => ['is_digit' => false],
			'USD' => ['is_digit' => true],
			'UYU' => ['is_digit' => true],
			'UZS' => ['is_digit' => true],
			'VND' => ['is_digit' => false],
			'VUV' => ['is_digit' => false],
			'WST' => ['is_digit' => true],
			'XAF' => ['is_digit' => false],
			'XCD' => ['is_digit' => true],
			'XOF' => ['is_digit' => false],
			'XPF' => ['is_digit' => false],
			'YER' => ['is_digit' => true],
			'ZAR' => ['is_digit' => true],
			'ZMW' => ['is_digit' => true],
			'GHS' => ['is_digit' => true],
		];

		foreach ($stripe_currencies as $key => $val) {
			if ($key === $curr) {
				$is_stripe = true;
				$is_digit = $val['is_digit'];
				break;
			}
		}
	}

	static function get_currency_options(&$curr_name = '', &$curr_pos = 'left_space', &$curr_th_sep = ',', &$curr_dec_sep = '.', &$curr_dec_no = '0') {
		$currency_options = [];
		$tds_options = self::get_tds_options();
        foreach ($tds_options as $tds_option) {
        	if (!empty($tds_option[ 'value' ])) {
		        switch ( $tds_option[ 'name' ] ) {
			        case 'curr_name':
			        case 'curr_pos':
			        case 'curr_th_sep':
			        case 'curr_dec_sep':
			        case 'curr_dec_no':
				        $var_name  = $tds_option[ 'name' ];
				        $$var_name = $currency_options[ $tds_option[ 'name' ] ] = $tds_option[ 'value' ];
				        break;
		        }
	        }
        }
		return $currency_options;
	}

	static function get_basic_currency($price) {
		self::get_currency_options($curr_name, $curr_pos, $curr_th_sep, $curr_dec_sep, $curr_dec_no);
		$curr_name = tds_util::get_currency($curr_name, true);
		$price = number_format( floatval($price), intval($curr_dec_no), $curr_dec_sep, $curr_th_sep);
		if (empty($curr_pos) || in_array($curr_pos, ['left', 'left_space'])) {
			$price = $curr_name . ( 'left_space' === $curr_pos ? ' ' : '' ) . $price;
		} else if (empty($curr_pos) || in_array($curr_pos, ['right', 'right_space'])) {
			$price .= ( 'right_space' === $curr_pos ? ' ' : '' ) . $curr_name;
		}
		return $price;
	}

	static function get_formatted_currency($price, $curr_name = '', $curr_pos = 'left_space', $curr_th_sep = ',', $curr_dec_sep = '.', $curr_dec_no = '0') {
		$price = number_format( floatval($price), intval($curr_dec_no), $curr_dec_sep, $curr_th_sep);
		if (!empty($curr_name)) {
			$curr_name = tds_util::get_currency( $curr_name, true );
		}
		if (empty($curr_pos) || in_array($curr_pos, ['left', 'left_space'])) {
			$price = $curr_name . ( 'left_space' === $curr_pos ? ' ' : '' ) . $price;
		} else if (empty($curr_pos) || in_array($curr_pos, ['right', 'right_space'])) {
			$price .= ( 'right_space' === $curr_pos ? ' ' : '' ) . $curr_name;
		}
		return $price;
	}

	static function get_formatted_date($date, $format = 'Y-m-d') {
		$format_date = tds_util::get_tds_option('format_date');
		if ( 'custom' === $format_date) {
			$custom_format_date = tds_util::get_tds_option('custom_format_date');
			return date_format(date_create($date), $custom_format_date);
		} else {
			switch ($format_date) {
				case 'd-m-Y' :
				case 'm-d-Y' :
					return date_format(date_create($date), $format_date);
					break;
			}
		}

		return date_format(date_create($date), $format);
	}
}
