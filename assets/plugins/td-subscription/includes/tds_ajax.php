<?php

add_action( 'rest_api_init', function (){

	register_rest_route( 'tds_preview', '/do_job/', array(
		'methods'  => 'POST',
		'callback' => array ( 'tds_ajax', 'on_ajax_render_preview_shortcode' ),
		'permission_callback' => function() {
			return current_user_can('edit_posts');
		}
	));

	register_rest_route( 'tds-api', '/tds-proxy/', array(
		'methods'  => 'POST',
		'callback' => array ( 'tds_ajax', 'on_ajax_tds_proxy' ),
		'permission_callback' => function() {
			return current_user_can('edit_posts');
		}
	));

	register_rest_route( 'tds_subscription', '/create_subscription/', array(
		'methods'  => 'POST',
		'callback' => array ( 'tds_ajax', 'on_ajax_create_subscription' ),
		'permission_callback' => function() {
			return 1 == get_option('users_can_register');
		}
	));

	register_rest_route( 'tds_subscription', '/update_paypal_subscription/', array(
		'methods'  => 'POST',
		'callback' => array ( 'tds_ajax', 'on_ajax_update_paypal_subscription' ),
		'permission_callback' => function() {
			return 1 == get_option('users_can_register');
		}
	));

	register_rest_route( 'tds_subscription', '/get_stripe_info/', array(
		'methods'  => 'POST',
		'callback' => array ( 'tds_ajax', 'on_ajax_get_stripe_info' ),
		'permission_callback' => function() {
			return 1 == get_option('users_can_register');
		}
	));

	register_rest_route( 'tds_subscription', '/update_stripe_subscription/', array(
		'methods'  => 'POST',
		'callback' => array ( 'tds_ajax', 'on_ajax_update_stripe_subscription' ),
		'permission_callback' => function() {
			return 1 == get_option('users_can_register');
		}
	));

});

class tds_ajax {

	static function on_ajax_render_preview_shortcode( WP_REST_Request $request ) {

		// get the $_POST parameters only
		$parameters = $request->get_body_params();

		$shortcode = $request->get_param('shortcode');
		$parameters['shortcode'] = $shortcode;

		$reply_html = do_shortcode( $shortcode );

		$parameters['replyHtml'] = $reply_html;

		die( json_encode( $parameters ) );

	}

	static function update_post_settings_meta( $post_id, $meta_key, $meta_value ) {
		$td_post_theme_settings = td_util::get_post_meta_array($post_id, 'td_post_theme_settings');
		$td_post_theme_settings[$meta_key] = $meta_value;
		return update_post_meta($post_id, 'td_post_theme_settings', $td_post_theme_settings);
	}

	static function on_ajax_get_stripe_info(WP_REST_Request $request) {
		global $wpdb;

		$result = [];

		// Trying to get the price: from the plan id or from the existing subscription

		$errors = [];

		$subscription_plan_id = $request->get_param('subscriptionPlanId');
		if (empty($subscription_plan_id)) {
			$errors[] = 'Invalid plan id';
		}

		$subscription_id = $request->get_param('subscriptionId');
		if (empty($subscription_id)) {
			$errors[] = 'Invalid subscription id';
		}

		if (2 === count($errors)) {
			$result[ 'error' ] = 'Invalid data supplied';
		}

		if (empty($result)) {

			tds_util::get_currency_options($curr_name, $curr_pos, $curr_th_sep, $curr_dec_sep, $curr_dec_no);

			$price = '';

			if (!empty($subscription_plan_id)) {

				// check plan
				$valid_plan = false;
				$results = self::get_all_plans( $request );
				if ( ! empty( $results[ 'plans' ] ) ) {
					foreach ( $results[ 'plans' ] as $plan ) {
						if ( $plan[ 'id' ] == $subscription_plan_id ) {
							$valid_plan = true;
							$price = $plan[ 'price' ];

							break;
						}
					}
				}

				if (! $valid_plan) {
					$result[ 'error' ] = 'Invalid plan id';
				}
			} else if (!empty( $subscription_id)) {

				$results = $wpdb->get_results( $wpdb->prepare( "SELECT 
						tds_subscriptions.* 
					FROM 
						tds_subscriptions 
					WHERE
						tds_subscriptions.id = %d", $subscription_id), ARRAY_A );

				if ( null !== $results ) {
					$price = $results[0]['price'];
					$curr_name = $results[0]['curr_name'];
				} else {
					$result[ 'error' ] = 'Invalid subscription id';
				}
			}

			if (empty($result) && !empty($price)) {

				$results = $wpdb->get_results("SELECT * FROM tds_payment_stripe LIMIT 1", ARRAY_A);

				if ( null !== $results) {

					if (!empty($curr_name)) {

			        	require_once TDS_PATH . '/includes/vendor/stripe/init.php';

			        	$is_testing = '';
			        	if (!empty($results[0]['is_sandbox'])) {
			        		$is_testing = 'sandbox_';
				        }

						\Stripe\Stripe::setApiKey($results[0][ $is_testing . 'secret_key']);

			        	try {

			                tds_util::check_stripe_currency($curr_name, $is_stripe, $is_digit );
			                if ($is_stripe) {

			                	// Create a PaymentIntent with amount and currency
				                $paymentIntent = \Stripe\PaymentIntent::create( [
					                'amount'                    => $price * ($is_digit ? 100 : 1),
					                'currency'                  => $curr_name,
//					                'automatic_payment_methods' => [
//						                'enabled' => true,
//					                ],
				                ] );
				                $result[ 'stripe_info' ] = $paymentIntent->client_secret;
			                } else {
			                	$result['error'] = 'Stripe does not support the current currency';
			                }
				        } catch (Exception $ex) {
			        		$result['error'] = $ex->getMessage();
				        }
			        }
				}
			}
		}

		die( json_encode( $result ) );
	}

	static function on_ajax_create_subscription( WP_REST_Request $request ) {
		$result = [];

		$subscription_user_id = $request->get_param('subscriptionUserId');
		$subscription_plan_id = $request->get_param('subscriptionPlanId');

		if (empty($subscription_user_id)) {
			$result[ 'error' ] = 'Invalid user id';
		}

		if (empty($subscription_plan_id)) {
			$result[ 'error' ] = 'Invalid plan id';
		}

		// check plan
		$valid_plan = false;

		if (empty($result)) {

            $plan_name = '';
			$last_months_in_cycle = 0;
			$trial_days = 0;
			$is_free = 0;
			$price = 0;

			$curr_name = $curr_pos = $curr_th_sep = $curr_dec_sep = $curr_dec_no = '';

			$results = self::get_all_plans($request);
			if (!empty($results['plans'])) {
				foreach ( $results['plans'] as $plan ) {
					if ( $plan[ 'id' ] == $subscription_plan_id ) {
						$valid_plan = true;
                        $plan_name = $plan['name'];
						$last_months_in_cycle = $plan[ 'months_in_cycle' ];
						$trial_days = intval($plan['trial_days']) > 0 ? intval($plan['trial_days']) : 0;
						$is_free = $plan['is_free'];
						$price = $plan['price'];

						tds_util::get_currency_options($curr_name, $curr_pos, $curr_th_sep, $curr_dec_sep, $curr_dec_no);
						break;
					}
				}
			}

			$billing_first_name = $request->get_param('billingFirstName');
			$billing_last_name = $request->get_param('billingLastName');
			$billing_company_name = $request->get_param('billingCompanyName');
			$billing_vat_number = $request->get_param('billingVatNumber');
			$billing_country = $request->get_param('billingCountry');
			$billing_address = $request->get_param('billingAddress');
			$billing_city = $request->get_param('billingCity');
			$billing_county = $request->get_param('billingCounty');
			$billing_postcode = $request->get_param('billingPostcode');
			$billing_phone = $request->get_param('billingPhone');
			$billing_email = $request->get_param('billingEmail');

			if (empty($billing_first_name)) {
				$result[ 'error' ]['billingFirstName'] = __td('Empty first name', TD_THEME_NAME);
			}
			if (empty($billing_last_name)) {
				$result[ 'error' ]['billingLastName'] = __td('Empty last name', TD_THEME_NAME);
			}
			if (empty($billing_country)) {
				$result[ 'error' ]['billingCountry'] = __td('Empty country', TD_THEME_NAME);
			}
			if (empty($billing_address)) {
				$result[ 'error' ]['billingAddress'] = __td('Empty address', TD_THEME_NAME);
			}
			if (empty($billing_city)) {
				$result[ 'error' ]['billingCity'] = __td('Empty city', TD_THEME_NAME);
			}
			if (empty($billing_county)) {
				$result[ 'error' ]['billingCounty'] = __td('Empty county', TD_THEME_NAME);
			}
			if (empty($billing_postcode)) {
				$result[ 'error' ]['billingPostcode'] = __td('Empty postcode', TD_THEME_NAME);
			}
			if (empty($billing_phone)) {
				$result[ 'error' ]['billingPhone'] = __td('Empty phone', TD_THEME_NAME);
			}
			if (empty($billing_email)) {
				$result[ 'error' ]['billingEmail'] = __td('Empty email', TD_THEME_NAME);
			}

			if (empty($result)) {
				if ( empty( $is_free ) ) {
					$billing_payment_method = $request->get_param( 'billingPaymentMethod' );

					if ( empty( $billing_payment_method ) ) {
						$result[ 'error' ][ 'billingPaymentMethod' ] = 'Invalid payment method';

					} else {

						if ( 'paypal' === $billing_payment_method ) {

							$paypal_order_id                   = $request->get_param( 'paypalOrderId' );
							$paypal_order_intent               = $request->get_param( 'paypalOrderIntent' );
							$paypal_order_status               = $request->get_param( 'paypalOrderStatus' );
							$paypal_order_payer_id             = $request->get_param( 'paypalOrderPayerId' );
							$paypal_order_payer_given_name     = $request->get_param( 'paypalOrderPayerGivenName' );
							$paypal_order_payer_surname        = $request->get_param( 'paypalOrderPayerSurname' );
							$paypal_order_payer_email          = $request->get_param( 'paypalOrderPayerEmail' );
							$paypal_order_payee_id             = $request->get_param( 'paypalOrderPayeeId' );
							$paypal_order_payee_email          = $request->get_param( 'paypalOrderPayeeEmail' );
							$paypal_order_amount_currency_code = $request->get_param( 'paypalOrderAmountCurrencyCode' );
							$paypal_order_amount_value         = $request->get_param( 'paypalOrderAmountValue' );
							$paypal_order_info                 = $request->get_param( 'paypalOrderInfo' );
							$paypal_order_create_time          = $request->get_param( 'paypalOrderCreateTime' );
							$paypal_order_update_time          = $request->get_param( 'paypalOrderUpdateTime' );
							$paypal_order_capture_create_time  = $request->get_param( 'paypalOrderCaptureCreateTime' );
							$paypal_order_capture_update_time  = $request->get_param( 'paypalOrderCaptureUpdateTime' );

						} else if ( 'stripe' === $billing_payment_method ) {

							$stripe_payment_intent = $request->get_param( 'stripePaymentIntent' );
							$stripe_payment_status = $request->get_param( 'stripePaymentStatus' );
							$stripe_payment_info = $request->get_param( 'stripePaymentInfo' );
						}
					}
				}
			}
		}

		if (empty($result)) {

			if (!$valid_plan) {
				$result['error'] = 'Invalid plan';
			} else {

				$confirm_key = $subscription_user_id . uniqid() . $subscription_plan_id;

				$data_values = array(
					'user_id' => $subscription_user_id,
					'plan_id' => $subscription_plan_id,
					'price' => $price,
					'curr_name' => $curr_name,
					'curr_pos' => $curr_pos,
					'curr_th_sep' => $curr_th_sep,
					'curr_dec_sep' => $curr_dec_sep,
					'curr_dec_no' => $curr_dec_no,
					'billing_first_name' => $billing_first_name,
					'billing_last_name' => $billing_last_name,
					'billing_company_name' => $billing_company_name,
					'billing_vat_number' => $billing_vat_number,
					'billing_country' => $billing_country,
					'billing_address' => $billing_address,
					'billing_city' => $billing_city,
					'billing_county' => $billing_county,
					'billing_post_code' => $billing_postcode,
					'billing_phone' => $billing_phone,
					'billing_email' => $billing_email,
					'payment_type'         => $billing_payment_method,
					'status'               => (empty($is_free) ? (empty($trial_days) ? 'waiting_payment' : 'trial') : 'free'),
					'last_months_in_cycle' => $last_months_in_cycle,
					'start_day'            => date('j'),
					'trial_days'           => $trial_days,
					'is_free'              => $is_free,
					'start_date' => date('Y-m-d'),
					'confirm_key' => $confirm_key
				);


				if ('paypal' === $billing_payment_method) {

					$data_values['paypal_order_id'] = $paypal_order_id;
					$data_values['paypal_order_intent'] = $paypal_order_intent;
					$data_values['paypal_order_status'] = $paypal_order_status;
					$data_values['paypal_order_payer_id'] = $paypal_order_payer_id;
					$data_values['paypal_order_payer_given_name'] = $paypal_order_payer_given_name;
					$data_values['paypal_order_payer_surname'] = $paypal_order_payer_surname;
					$data_values['paypal_order_payer_email'] = $paypal_order_payer_email;
					$data_values['paypal_order_payee_id'] = $paypal_order_payee_id;
					$data_values['paypal_order_payee_email'] = $paypal_order_payee_email;
					$data_values['paypal_order_amount_currency_code'] = $paypal_order_amount_currency_code;
					$data_values['paypal_order_amount_value'] = $paypal_order_amount_value;
					$data_values['paypal_order_info'] = $paypal_order_info;
					$data_values['paypal_order_create_time'] = $paypal_order_create_time;
					$data_values['paypal_order_update_time'] = $paypal_order_update_time;
					$data_values['paypal_order_capture_create_time'] = $paypal_order_capture_create_time;
					$data_values['paypal_order_capture_update_time'] = $paypal_order_capture_update_time;

					if ('COMPLETED' === $paypal_order_status) {
						$data_values['status'] = 'active';
					}

					$data_format = array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s',
						'%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s');

				} else if ('stripe' === $billing_payment_method) {

					$data_values['stripe_payment_intent'] = $stripe_payment_intent;
					$data_values['stripe_payment_status'] = $stripe_payment_status;
					$data_values['stripe_payment_info'] = $stripe_payment_info;

					if ('succeeded' === $stripe_payment_status) {
						$data_values['status'] = 'active';
					}

					$data_format = array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s',
						'%s', '%s', '%s');

				} else {
					$data_format = array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' );
				}

				$data_values['created_at'] = date('Y-m-d H:i:s');

				global $wpdb;
				$wpdb->suppress_errors = true;

				$insert_result = $wpdb->insert( 'tds_subscriptions',
					$data_values,
					$data_format );

				if ( false !== $insert_result ) {

					$result['response'] = [
						'local_subscription_id' => $wpdb->insert_id,
						'local_plan_id' => $subscription_plan_id,
                        'local_plan_name' => $plan_name,
                        'start_date' => date('Y-m-d'),
                        'end_date' => empty($is_free) ? tds_util::get_end_date(date('Y-m-d'), $last_months_in_cycle, date('j'))->format('Y-m-d') : __td('unlimited', TD_THEME_NAME),
						'price' => $price,
                        'is_free' => $is_free,
						'curr_name' => $curr_name
					];

                    if( empty($is_free) ) {
                        $result['response']['last_months_in_cycle'] = $last_months_in_cycle;
                    }

					if ('direct' === $billing_payment_method) {
	                    $result['response']['payment_type'] = 'Direct bank transfer';

	                    $payment_bank = $wpdb->get_results("SELECT * FROM tds_payment_bank LIMIT 1", ARRAY_A);
                        if ( null !== $payment_bank && count($payment_bank) && 1 == $payment_bank[0]['is_active']) {

                            $result['response']['payment_bank'] = $payment_bank[0]['bank_name'];
                            $result['response']['payment_account_name'] = $payment_bank[0]['account_name'];
                            $result['response']['payment_account_number'] = $payment_bank[0]['account_number'];
                            $result['response']['payment_routing_number'] = $payment_bank[0]['routing_number'];
                            $result['response']['payment_iban'] = $payment_bank[0]['iban'];
                            $result['response']['payment_bic_swift'] = $payment_bank[0]['bic_swift'];
                            $result['response']['payment_instruction'] = $payment_bank[0]['instruction'];
                        }
                    } else if ( 'paypal' === $billing_payment_method ) {
                        $result['response']['payment_type'] = 'PayPal';
                    }

					$payment_page_id = tds_util::get_tds_option('payment_page_id');
		            if (!is_null($payment_page_id) ) {
		                $payment_page_permalink = get_permalink( $payment_page_id );
		                if ( false !== $payment_page_permalink ) {
		                    $confirm_url = add_query_arg( array(
		                    	'subscription' => $result['response']['local_subscription_id'],
			                    'key' => $confirm_key,
		                    ), $payment_page_permalink );
		                }
		            }

					if (!empty($confirm_url )) {
			            $result['response']['confirm_url'] = $confirm_url;
		            }
					if (empty($is_free)) {
						$result['response']['billing_company_name'] = $billing_company_name;
						$result['response']['billing_vat_number'] = $billing_vat_number;
					}
				} else {
					$result[ 'error' ] = $wpdb->last_error;
				}
			}
		}

		die( json_encode( $result ) );
	}

	static function on_ajax_update_paypal_subscription( WP_REST_Request $request ) {
		$result = [];

		$subscription_id = $request->get_param('subscriptionId');
		if (empty($subscription_id)) {
			$result[ 'error' ] = 'Invalid subscription id';
		}

		if (empty($result)) {
			$paypalOrderId = $request->get_param( 'paypalOrderId' );
			if ( empty( $paypalOrderId ) ) {
				$result[ 'error' ] = 'Invalid PayPal order id';
			}
		}

		if (empty($result)) {
			$paypal_order_intent = $request->get_param('paypalOrderIntent');
			$paypal_order_status = $request->get_param('paypalOrderStatus');
			$paypal_order_payer_id = $request->get_param('paypalOrderPayerId');
			$paypal_order_payer_given_name = $request->get_param('paypalOrderPayerGivenName');
			$paypal_order_payer_surname = $request->get_param('paypalOrderPayerSurname');
			$paypal_order_payer_email = $request->get_param('paypalOrderPayerEmail');
			$paypal_order_payee_id = $request->get_param('paypalOrderPayeeId');
			$paypal_order_payee_email = $request->get_param('paypalOrderPayeeEmail');
			$paypal_order_amount_currency_code = $request->get_param('paypalOrderAmountCurrencyCode');
			$paypal_order_amount_value = $request->get_param('paypalOrderAmountValue');
			$paypal_order_info = $request->get_param('paypalOrderInfo');
			$paypal_order_create_time = $request->get_param('paypalOrderCreateTime');
			$paypal_order_update_time = $request->get_param('paypalOrderUpdateTime');
			$paypal_order_capture_create_time = $request->get_param('paypalOrderCaptureCreateTime');
			$paypal_order_capture_update_time = $request->get_param('paypalOrderCaptureUpdateTime');
		}

		if (empty($result)) {

			$data_values = [];
			$data_values['paypal_order_id'] = $paypal_order_id;
			$data_values['paypal_order_intent'] = $paypal_order_intent;
			$data_values['paypal_order_status'] = $paypal_order_status;
			$data_values['paypal_order_payer_id'] = $paypal_order_payer_id;
			$data_values['paypal_order_payer_given_name'] = $paypal_order_payer_given_name;
			$data_values['paypal_order_payer_surname'] = $paypal_order_payer_surname;
			$data_values['paypal_order_payer_email'] = $paypal_order_payer_email;
			$data_values['paypal_order_payee_id'] = $paypal_order_payee_id;
			$data_values['paypal_order_payee_email'] = $paypal_order_payee_email;
			$data_values['paypal_order_amount_currency_code'] = $paypal_order_amount_currency_code;
			$data_values['paypal_order_amount_value'] = $paypal_order_amount_value;
			$data_values['paypal_order_info'] = $paypal_order_info;
			$data_values['paypal_order_create_time'] = $paypal_order_create_time;
			$data_values['paypal_order_update_time'] = $paypal_order_update_time;
			$data_values['paypal_order_capture_create_time'] = $paypal_order_capture_create_time;
			$data_values['paypal_order_capture_update_time'] = $paypal_order_capture_update_time;
			$data_values['created_at'] = date('Y-m-d H:i:s');

			if ('COMPLETED' === $paypal_order_status) {
				$data_values['status'] = 'active';
			} else {
				$data_values['status'] = 'paid_incomplete';
			}

			$data_format = array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s');

			global $wpdb;
			$wpdb->suppress_errors = true;


			$update_result = $wpdb->update( 'tds_subscriptions',
				$data_values,
				array( 'id' => $subscription_id ),
				$data_format,
				array( '%d' ));

			if ( false !== $update_result ) {
				$result[ 'success' ] = true;
			} else {
				$result[ 'error' ] = $wpdb->last_error;
			}
		}

		die( json_encode( $result ) );
	}



	static function on_ajax_update_stripe_subscription( WP_REST_Request $request ) {
		$result = [];

		$subscription_id = $request->get_param('subscriptionId');
		if (empty($subscription_id)) {
			$result[ 'error' ] = 'Invalid subscription id';
		}

		if (empty($result)) {
			$stripePaymentIntent = $request->get_param( 'stripePaymentIntent' );
			if ( empty( $stripePaymentIntent ) ) {
				$result[ 'error' ] = 'Invalid Stripe Payment Intent';
			}
		}

		if (empty($result)) {
			$stripePaymentStatus = $request->get_param( 'stripePaymentStatus' );
			if ( empty( $stripePaymentStatus ) ) {
				$result[ 'error' ] = 'Invalid Stripe Payment Status';
			}
		}

		if (empty($result)) {
			$stripePaymentInfo = $request->get_param( 'stripePaymentInfo' );
			if ( empty( $stripePaymentInfo ) ) {
				$result[ 'error' ] = 'Invalid Stripe Payment Info';
			}
		}

		if (empty($result)) {

			$data_values = [];
			$data_values['stripe_payment_intent'] = $stripePaymentIntent;
			$data_values['stripe_payment_status'] = $stripePaymentStatus;
			$data_values['stripe_payment_info'] = $stripePaymentInfo;
			$data_values['created_at'] = date('Y-m-d H:i:s');

			if ('succeeded' === $stripePaymentStatus) {
				$data_values['status'] = 'active';
			} else {
				$data_values['status'] = 'paid_incomplete';
			}

			$data_format = array( '%s', '%s', '%s', '%s');

			global $wpdb;
			$wpdb->suppress_errors = true;

			$update_result = $wpdb->update( 'tds_subscriptions',
				$data_values,
				array( 'id' => $subscription_id ),
				$data_format,
				array( '%d' ));

			if ( false !== $update_result ) {
				$result[ 'success' ] = true;

				$my_account_page_id = tds_util::get_tds_option('my_account_page_id');

	            if (!is_null($my_account_page_id) ) {
	                $my_account_permalink = get_permalink( $my_account_page_id );
	                if ( false !== $my_account_permalink ) {
	                    $result['redirect_url'] = add_query_arg( 'subscriptions', '', $my_account_permalink );
	                }
	            }

			} else {
				$result[ 'error' ] = $wpdb->last_error;
			}
		}

		die( json_encode( $result ) );
	}



	static function on_ajax_tds_proxy( WP_REST_Request $request ) {
		$reply = [];

        $end_point = $request->get_param('endPoint');
        if (empty($end_point)) {
            $reply['error'] = array(
                array(
		            'type' => 'API ERROR',
		            'message' => 'No endPoint received. Please use tdsApi.run for proxy requests.',
		            'debug_data' => $request
	            )
            );
            die( json_encode( $reply ) );
        }

        $end_point = $request->get_param('endPoint');

        switch ($end_point) {

        	case 'set_option':
	        	$intern_result = self::set_option($request);
	        	break;
            case 'get_option':
	        	$intern_result = self::get_option($request);
	        	break;
            case 'get_all_options':
	        	$intern_result = self::get_all_options();
	        	break;
            case 'set_options':
	        	$intern_result = self::set_options($request);
	        	break;
            case 'get_all_currencies':
	        	$intern_result = self::get_all_currencies();
	        	break;
            case 'get_all_stripe_currencies':
	        	$intern_result = self::get_all_stripe_currencies();
	        	break;

        	case 'create_plan':
            	$intern_result = self::create_plan($request);
	        	break;
            case 'delete_plan':
            	$intern_result = self::delete_plan($request);
	        	break;
	        case 'get_all_plans':
	        	$intern_result = self::get_all_plans($request);
	        	break;

	        case 'create_company':
	        	$intern_result = self::create_company($request);
	        	break;
            case 'get_company':
	        	$intern_result = self::get_company($request);
	        	break;

            case 'create_payment_bank':
	        	$intern_result = self::create_payment_bank($request);
	        	break;
            case 'get_payment_bank':
	        	$intern_result = self::get_payment_bank($request);
	        	break;

            case 'create_payment_paypal':
	        	$intern_result = self::create_payment_paypal($request);
	        	break;
            case 'get_payment_paypal':
	        	$intern_result = self::get_payment_paypal($request);
	        	break;
	        case 'get_token_paypal':
	        	self::get_token_paypal($request);
	        	break;

            case 'create_payment_stripe':
	        	$intern_result = self::create_payment_stripe($request);
	        	break;
            case 'get_payment_stripe':
	        	$intern_result = self::get_payment_stripe($request);
	        	break;

            case 'modify_subscription':
            	$intern_result = self::modify_subscription($request);
	        	break;
	        case 'get_all_subscriptions':
	        	$intern_result = self::get_all_subscriptions($request);
	        	break;
            case 'get_latest_subscriptions':
	        	$intern_result = self::get_latest_subscriptions($request);
	        	break;
            case 'get_info_subscriptions':
	        	$intern_result = self::get_info_subscriptions($request);
	        	break;

            case 'create_wizard_locker':
            	$intern_result = self::create_wizard_locker($request);
	        	break;

            case 'get_page_info':
            	$intern_result = self::get_page_info($request);
	        	break;
            case 'create_wizard_pages':
	        	$intern_result = self::create_wizard_pages($request);
	        	break;

            case 'get_list_pages':
            	$intern_result = self::get_list_pages($request);
	        	break;

            case 'get_dashboard_permalinks':
            	$intern_result = self::get_dashboard_permalinks($request);
	        	break;

	        default:
	        	$intern_result['error'] = 'Invalid endPoint';
        }

        if (empty($intern_result['error'])) {
        	$reply = $intern_result;
        } else {
	        $reply['error'] = array(
	            array(
			        'type' => 'API ERROR',
			        'message' => $intern_result['error'],
		            'debug_data' => $request
		        )
	        );
        }

		die( json_encode( $reply ) );
	}

	private static function set_option(WP_REST_Request $request) {
		$result = [];
        $option_name = $request->get_param('optionName');
        $option_value = $request->get_param('optionValue');

        if (empty($option_name)) {
			$result[ 'error' ][] = 'Invalid option name';
		}
		if (empty($option_value)) {
			$result[ 'error' ][] = 'Invalid option value';
		}
		if (!empty($result)) {
			// return error
		} else {
			$response = tds_util::set_tds_option($option_name, $option_value);
			if (!is_null($response)) {
				$result = $response;
			}
		}

		return $result;
	}

	private static function get_option(WP_REST_Request $request) {
		$result = [];
        $option_name = $request->get_param('optionName');

        if (empty($option_name)) {
			$result[ 'error' ][] = 'Invalid option name';
		}
		if (!empty($result)) {
			// return error
		} else {
			global $wpdb;

			$get_result = $wpdb->get_var( $wpdb->prepare( "SELECT value FROM tds_options WHERE name = %s", $option_name) );
			if ( false !== $get_result ) {
				$result[ 'value' ] = $get_result;
			} else {
				$result[ 'error' ][] = 'Invalid value';
			}
		}

		return $result;
	}

	static function get_all_options() {
		global $wpdb;

		$result = [];
		$results = $wpdb->get_results("SELECT * FROM tds_options", ARRAY_A);

		if ( null !== $results) {
			$result['options'] = $results;
		}
		return $result;
	}

	static function get_all_currencies() {
		$result = [];
		$currencies = tds_util::get_currency();

		foreach ($currencies as $key_currency => $val_currency) {
			$is_paypal = false;
			$is_digit = false;
			tds_util::check_paypal_currency($key_currency, $is_paypal, $is_digit);
			$results[$key_currency] = [
				'description' => $val_currency,
				'is_paypal' => $is_paypal,
				'is_digit' => $is_digit
			];
		}

		if ( null !== $results) {
			$result['currency'] = $results;
		}
		return $result;
	}

	static function get_all_stripe_currencies() {
		$result = [];
		$currencies = tds_util::get_currency();

		foreach ($currencies as $key_currency => $val_currency) {
			$is_stripe = false;
			$is_digit = false;
			tds_util::check_stripe_currency($key_currency, $is_stripe, $is_digit);
			$results[$key_currency] = [
				'description' => $val_currency,
				'is_stripe' => $is_stripe,
				'is_digit' => $is_digit
			];
		}

		if ( null !== $results) {
			$result['currency'] = $results;
		}
		return $result;
	}

	private static function get_all_plans(WP_REST_Request $request) {
		global $wpdb;

		$lockers = get_posts( array(
			'post_type' => 'tds_locker',
			'numberposts' => -1
		));

		$result = [];
		$results = $wpdb->get_results("SELECT 
				tds_plans.*,
				test.count_subscriptions
			FROM 
				tds_plans LEFT JOIN (
					SELECT 
						tds_plans.id as 'plan_id',
			            COUNT( DISTINCT tds_subscriptions.id ) as 'count_subscriptions'
					FROM 
						tds_plans INNER join tds_subscriptions ON tds_plans.id = tds_subscriptions.plan_id
			            AND tds_subscriptions.status IN ('waiting_payment', 'active', 'free', 'trial')
					GROUP BY
						tds_plans.id
			    ) as test on test.plan_id = tds_plans.id", ARRAY_A);

		if ( null !== $results) {
			foreach ( $results as &$item ) {
				$item['formatted_price'] = tds_util::get_basic_currency($item['price']);

				$item['count_lockers'] = 0;
				foreach ( $lockers as $locker ) {
					$tds_locker_types = get_post_meta( $locker->ID, 'tds_locker_types', true );
					if ( ! empty( $tds_locker_types[ 'tds_paid_subs_plan_ids' ] ) && is_array( $tds_locker_types[ 'tds_paid_subs_plan_ids' ] ) && in_array( $item[ 'id' ], $tds_locker_types[ 'tds_paid_subs_plan_ids' ] ) ) {
						$item['count_lockers']++;
						continue;
					}
				}
			}
			$result['plans'] = $results;
		}
		return $result;
	}

	private static function create_plan(WP_REST_Request $request) {
		$result = [];
        $plan_id = $request->get_param('planId');
        $plan_name = $request->get_param('planName');
        $plan_price = $request->get_param('planPrice');
        $plan_months = $request->get_param('planMonths');
        $plan_trial = $request->get_param('planTrial');
        $plan_free = $request->get_param('planFree');

        if (empty($plan_name)) {
			$result[ 'error' ][] = 'Invalid plan name';
		}
		if (!empty($plan_free) && '0' === $plan_free) {

			if ( empty( $plan_price ) ) {
				$result[ 'error' ][] = 'Invalid plan price';
			}
			if ( empty( $plan_months ) ) {
				$result[ 'error' ][] = 'Invalid plan months';
			}
		}
		if (!empty($result)) {
			// return error
		} else {
			global $wpdb;

			if (empty($plan_id)) {
				$insert_result = $wpdb->insert( 'tds_plans',
					array(
						'name'            => $plan_name,
						'price'           => $plan_price,
						'months_in_cycle' => $plan_months,
						'trial_days' => intval($plan_trial) > 0 ? intval($plan_trial) : 0,
						'is_free' => $plan_free,
					),
					array( '%s', '%s', '%s' ) );

				if ( false !== $insert_result ) {
					$result[ 'inserted_id' ] = $wpdb->insert_id;
				}
			} else {
//				$wpdb->query( "ALTER TABLE `tds_subscriptions` ADD `curr_dec_no` VARCHAR(30) NULL DEFAULT NULL AFTER `price`" );
//				$wpdb->query( "ALTER TABLE `tds_subscriptions` ADD `curr_dec_sep` VARCHAR(30) NULL DEFAULT NULL AFTER `price`" );
//				$wpdb->query( "ALTER TABLE `tds_subscriptions` ADD `cur_th_sep` VARCHAR(30) NULL DEFAULT NULL AFTER `price`" );
//				$wpdb->query( "ALTER TABLE `tds_subscriptions` ADD `curr_pos` VARCHAR(30) NULL DEFAULT NULL AFTER `price`" );
//				$wpdb->query( "ALTER TABLE `tds_subscriptions` ADD `curr_name` VARCHAR(50) NULL DEFAULT NULL AFTER `price`" );
				$update_result = $wpdb->update( 'tds_plans',
					array(
						'name'            => $plan_name,
						'price'           => $plan_price,
						'months_in_cycle' => $plan_months,
						'trial_days' => intval($plan_trial) > 0 ? intval($plan_trial) : 0,
						'is_free' => $plan_free,
					),
					array( 'id' => $plan_id ),
					array('%s', '%s', '%s'),
					array( '%d' )
				);

				if ( false !== $update_result ) {
					$result[ 'success' ] = true;
				}
			}

			if (!empty($result)) {
				$result[ 'formatted_price' ] = tds_util::get_basic_currency($plan_price);
			}
		}

		return $result;
	}

	private static function delete_plan(WP_REST_Request $request) {
		$result = [];
        $plan_id = $request->get_param('planId');

        if (empty($plan_id)) {
			$result[ 'error' ][] = 'Invalid plan id';
		}
		if (!empty($result)) {
			// return error
		} else {
			global $wpdb;

			$delete_result = $wpdb->delete( 'tds_plans',
				array(
					'id' => $plan_id
				),
				array( '%d' ) );

			if ( false !== $delete_result ) {
				$result[ 'success' ] = true;
			}
		}

		return $result;
	}

	private static function get_company(WP_REST_Request $request) {
		global $wpdb;

		$result = [];
		$results = $wpdb->get_results("SELECT * FROM tds_companies LIMIT 1", ARRAY_A);

		if ( null !== $results) {
			$result['company'] = $results;
		}
		return $result;
	}

	private static function create_company(WP_REST_Request $request) {
		$result = [];
        $company_id = $request->get_param('companyId');
        $company_name = $request->get_param('companyName');
        $billing_cui = $request->get_param('billingCUI');
        $billing_j = $request->get_param('billingJ');
        $billing_address = $request->get_param('billingAddress');
        $billing_city = $request->get_param('billingCity');
        $billing_country = $request->get_param('billingCountry');
        $billing_email = $request->get_param('billingEmail');
        $billing_bank_account = $request->get_param('billingBankAccount');
        $billing_post_code = $request->get_param('billingPostCode');
        $billing_vat_number = $request->get_param('billingVatNumber');

        if (empty($company_name)) {
			$result[ 'error' ]['companyName'] = 'Invalid company name';
		}
//		if (empty($billing_cui)) {
//			$result[ 'error' ][] = 'Invalid billing CUI';
//		}
//		if (empty($billing_j)) {
//			$result[ 'error' ][] = 'Invalid billing J';
//		}
//		if (empty($billing_address)) {
//			$result[ 'error' ][] = 'Invalid billing address';
//		}
//		if (empty($billing_city)) {
//			$result[ 'error' ][] = 'Invalid billing city';
//		}
//		if (empty($billing_country)) {
//			$result[ 'error' ][] = 'Invalid billing country';
//		}
		if (empty($billing_email)) {
			$result[ 'error' ]['billingEmail'] = 'Empty billing email';
		} else if (!is_email($billing_email)) {
			$result[ 'error' ]['billingEmail'] = 'Invalid billing email';
		}
//		if (empty($billing_bank_account)) {
//			$result[ 'error' ][] = 'Invalid billing bank account';
//		}
//		if (empty($billing_post_code)) {
//			$result[ 'error' ][] = 'Invalid billing post code';
//		}
//		if (empty($billing_vat_number)) {
//			$result[ 'error' ][] = 'Invalid billing vat number';
//		}
		if (!empty($result)) {
			// return error
		} else {
			global $wpdb;

			$data_values = array(
				'company_name' => $company_name,
				'billing_cui' => $billing_cui,
				'billing_j' => $billing_j,
				'billing_address' => $billing_address,
				'billing_city' => $billing_city,
				'billing_country' => $billing_country,
				'billing_email' => $billing_email,
				'billing_bank_account' => $billing_bank_account,
				'billing_post_code' => $billing_post_code,
				'billing_vat_number' => $billing_vat_number,
			);
			$data_format = array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s');

			$wpdb->suppress_errors = true;

			if (empty($company_id)) {

				$insert_result = $wpdb->insert( 'tds_companies',
					$data_values,
					$data_format);

				if ( false !== $insert_result ) {
					$result[ 'inserted_id' ] = $wpdb->insert_id;
				} else {
					$result[ 'error' ] = $wpdb->last_error;
				}

			} else {

				$update_result = $wpdb->update( 'tds_companies',
					$data_values,
					array( 'id' => $company_id ),
					$data_format,
					array( '%d' )
				);

				if ( false !== $update_result ) {
					$result[ 'success' ] = true;
				} else {
					$result[ 'error' ] = $wpdb->last_error;
				}
			}
		}

		return $result;
	}

	private static function create_payment_bank(WP_REST_Request $request) {
		$result = [];
        $payment_id = $request->get_param('paymentId');
        $account_name = $request->get_param('accountName');
        $account_number = $request->get_param('accountNumber');
        $bank_name = $request->get_param('bankName');
        $routing_number = $request->get_param('routingNumber');
        $iban = $request->get_param('iban');
        $bic_swift = $request->get_param('bicSwift');
        $description = $request->get_param('description');
        $instruction = $request->get_param('instruction');
        $is_active = $request->get_param('isActive');

        if (empty($account_name)) {
			$result[ 'error' ]['accountName'] = 'Empty account name';
		}
		if (empty($account_number)) {
			$result[ 'error' ]['accountNumber'] = 'Empty account number';
		}
		if (empty($bank_name)) {
			$result[ 'error' ]['bankName'] = 'Empty bank name';
		}
//		if (empty($routing_number)) {
//			$result[ 'error' ][] = 'Invalid routing number';
//		}
//		if (empty($iban)) {
//			$result[ 'error' ][] = 'Invalid IBAN';
//		}
//		if (empty($bic_swift)) {
//			$result[ 'error' ][] = 'Invalid BIC/SWIFT';
//		}
//		if (empty($description)) {
//			$result[ 'error' ][] = 'Invalid description';
//		}
//		if (empty($instruction)) {
//			$result[ 'error' ][] = 'Invalid instruction';
//		}
		if (!empty($result)) {
			// return error
		} else {
			global $wpdb;

			$data_values = array(
				'account_name' => $account_name,
				'account_number' => $account_number,
				'bank_name' => $bank_name,
				'routing_number' => $routing_number,
				'iban' => $iban,
				'bic_swift' => $bic_swift,
				'description' => $description,
				'instruction' => $instruction,
				'is_active' => empty($is_active) ? 0 : 1,
			);
			$data_format = array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s');

			$wpdb->suppress_errors = true;

			if (empty($payment_id)) {

				$insert_result = $wpdb->insert( 'tds_payment_bank',
					$data_values,
					$data_format);

				if ( false !== $insert_result ) {
					$result[ 'inserted_id' ] = $wpdb->insert_id;
				} else {
					$result[ 'error' ] = $wpdb->last_error;
				}

			} else {

				$update_result = $wpdb->update( 'tds_payment_bank',
					$data_values,
					array( 'id' => $payment_id ),
					$data_format,
					array( '%d' )
				);

				if ( false !== $update_result ) {
					$result[ 'success' ] = true;
				} else {
					$result[ 'error' ] = $wpdb->last_error;
				}
			}
		}

		return $result;
	}

	private static function get_payment_bank(WP_REST_Request $request) {
		global $wpdb;

		$result = [];
		$results = $wpdb->get_results("SELECT * FROM tds_payment_bank LIMIT 1", ARRAY_A);

		if ( null !== $results) {
			$result['payment'] = $results;
		}
		return $result;
	}

	private static function create_payment_paypal(WP_REST_Request $request) {
		$result = [];
        $payment_id = $request->get_param('paymentId');
        $client_id = $request->get_param('clientId');
        $client_id_sandbox = $request->get_param('clientIdSandbox');
        $is_active = $request->get_param('isActive');
        $is_sandbox = $request->get_param('isSandbox');

        if (!empty($is_active) ) {
        	if ( empty($is_sandbox) ) {
        		if (empty($client_id) ) {
        		    $result[ 'error' ][ 'clientId' ] = 'Empty client id';
		        }
	        } else {
	            if (empty($client_id_sandbox)) {
			        $result[ 'error' ][ 'clientIdSandbox' ] = 'Empty Sandbox Merchant Id';
		        }
	        }
		}

		if (!empty($result)) {
			// return error
		} else {
			global $wpdb;

			$data_values = array(
				'client_id' => empty($client_id) ? '' : $client_id,
				'client_id_sandbox' => empty($client_id_sandbox) ? '' : $client_id_sandbox,
				'is_active' => empty($is_active) ? 0 : 1,
				'is_sandbox' => empty($is_sandbox) ? 0 : 1
			);
			$data_format = array('%s', '%s', '%d', '%d');

			$wpdb->suppress_errors = true;

			if (empty($payment_id)) {

				$insert_result = $wpdb->insert( 'tds_payment_paypal',
					$data_values,
					$data_format);

				if ( false !== $insert_result ) {
					$result[ 'inserted_id' ] = $wpdb->insert_id;
				} else {
					$result[ 'error' ] = $wpdb->last_error;
				}

			} else {

				$update_result = $wpdb->update( 'tds_payment_paypal',
					$data_values,
					array( 'id' => $payment_id ),
					$data_format,
					array( '%d' )
				);

				if ( false !== $update_result ) {
					$result[ 'success' ] = true;
				} else {
					$result[ 'error' ] = $wpdb->last_error;
				}
			}
		}

		return $result;
	}

	private static function get_payment_paypal(WP_REST_Request $request) {
		global $wpdb;

		$result = [];
		$results = $wpdb->get_results("SELECT * FROM tds_payment_paypal LIMIT 1", ARRAY_A);

		if ( null !== $results) {
			$result['payment'] = $results;
		}
		return $result;
	}

	private static function get_token_paypal(WP_REST_Request $request) {
		$result = td_subscription::get_payment_method_credentials('paypal', $info);

		die( json_encode([
			'token' =>$result['token'],
			'info' => $info
		]) );
	}

	private static function create_payment_stripe(WP_REST_Request $request) {
		$result = [];
        $payment_id = $request->get_param('paymentId');
        $secret_key = $request->get_param('secretKey');
        $public_key = $request->get_param('publicKey');
        $secret_key_sandbox = $request->get_param('secretKeySandbox');
        $public_key_sandbox = $request->get_param('publicKeySandbox');
        $is_active = $request->get_param('isActive');
        $is_sandbox = $request->get_param('isSandbox');

        if (!empty($is_active) ) {
        	if ( empty($is_sandbox) ) {
        		if (empty($secret_key) ) {
        		    $result[ 'error' ][ 'secretKey' ] = 'Empty Secret Key';
		        }
		        if (empty($public_key) ) {
        		    $result[ 'error' ][ 'publicKey' ] = 'Empty Public Key';
		        }
	        } else {
	            if (empty($secret_key_sandbox) ) {
        		    $result[ 'error' ][ 'secretKeySandbox' ] = 'Empty Sandbox Secret Key';
		        }
		        if (empty($public_key_sandbox) ) {
        		    $result[ 'error' ][ 'publicKeySandbox' ] = 'Empty Sandbox Public Key';
		        }
	        }
		}

		if (!empty($result)) {
			// return error
		} else {
			global $wpdb;

			$data_values = array(
				'secret_key' => empty($secret_key) ? '' : $secret_key,
				'public_key' => empty($public_key) ? '' : $public_key,
				'sandbox_secret_key' => empty($secret_key_sandbox) ? '' : $secret_key_sandbox,
				'sandbox_public_key' => empty($public_key_sandbox) ? '' : $public_key_sandbox,
				'is_active' => empty($is_active) ? 0 : 1,
				'is_sandbox' => empty($is_sandbox) ? 0 : 1
			);
			$data_format = array('%s', '%s', '%s', '%s', '%d', '%d');

			$wpdb->suppress_errors = true;

			if (empty($payment_id)) {

				$insert_result = $wpdb->insert( 'tds_payment_stripe',
					$data_values,
					$data_format);

				if ( false !== $insert_result ) {
					$result[ 'inserted_id' ] = $wpdb->insert_id;
				} else {
					$result[ 'error' ] = $wpdb->last_error;
				}

			} else {

				$update_result = $wpdb->update( 'tds_payment_stripe',
					$data_values,
					array( 'id' => $payment_id ),
					$data_format,
					array( '%d' )
				);

				if ( false !== $update_result ) {
					$result[ 'success' ] = true;
				} else {
					$result[ 'error' ] = $wpdb->last_error;
				}
			}

			if (empty($result['error'])) {

				$is_testing = '';
	            if (!empty($is_sandbox)) {
	                $is_testing = 'sandbox_';
		        }

		        require_once TDS_PATH . '/includes/vendor/stripe/init.php';

				try {
	                // try get the balance
	                $stripeClient = new \Stripe\StripeClient($data_values[ $is_testing . 'secret_key']);
	                $stripeClient->balance->retrieve();
	                $result['valid_secret_key'] = true;

		        } catch (Exception $ex) {
	                $result['valid_secret_key'] = false;
		        }

		        $result['valid_public_key'] = false;
				$result['debug_info'] = self::validateStripePublicKey( $data_values[ $is_testing . 'public_key'], $result['valid_public_key']);
			}
		}

		return $result;
	}

	private static function get_payment_stripe(WP_REST_Request $request) {
		global $wpdb;

		$result = [];
		$results = $wpdb->get_results("SELECT * FROM tds_payment_stripe LIMIT 1", ARRAY_A);

		if ( null !== $results && count($results)) {
			$result['payment'] = $results;

			$is_testing = '';
            if (!empty($results[0]['is_sandbox'])) {
                $is_testing = 'sandbox_';
	        }

	        require_once TDS_PATH . '/includes/vendor/stripe/init.php';

            // check stripe secret key
			try {
                // try get the balance
                $stripeClient = new \Stripe\StripeClient($results[0][ $is_testing . 'secret_key']);
                $stripeClient->balance->retrieve();
                $result['valid_secret_key'] = true;

	        } catch (Exception $ex) {
                $result['valid_secret_key'] = false;
	        }

	        $result['valid_public_key'] = false;
			$result['debug_info'] = self::validateStripePublicKey($results[0][ $is_testing . 'public_key'], $result['valid_public_key']);
		}
		return $result;
	}

	private static function validateStripePublicKey($stripe_public_key, &$is_valid = false) {

		// check stripe public key
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, "https://api.stripe.com/v1/sources/src_?client_secret=src_client_secret_&key=" . $stripe_public_key);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);

		$response = json_decode(curl_exec($ch),true);

		curl_close ($ch);

		if ( substr($response["error"]["message"],0, 24 ) != "Invalid API Key provided" ) {
			$is_valid = true;
		}
		return $response;
	}

	private static function get_all_subscriptions(WP_REST_Request $request) {
		global $wpdb;

		$result  = [];

		$check = $request->get_param('check');
		if (!empty($check) && '1' === $check) {
			$result = tds_util::get_subscriptions();
		} else {

			$results = $wpdb->get_results( "SELECT 
					tds_subscriptions.*, 
					tds_plans.name AS 'plan_name' 
				FROM 
					tds_subscriptions 
					LEFT JOIN tds_plans
					ON tds_subscriptions.plan_id = tds_plans.id ORDER BY tds_subscriptions.ID desc", ARRAY_A );

			if ( null !== $results ) {
				foreach ( $results as &$item ) {
					if ( ! empty( $item[ 'user_id' ] ) ) {
						$item[ 'user_name' ] = get_user_meta( $item[ 'user_id' ], 'nickname', true );
						$item[ 'end_date' ]  = tds_util::get_end_date( $item[ 'start_date' ], $item[ 'last_months_in_cycle' ], $item[ 'start_day' ], $item['trial_days'] )->format( 'Y-m-d' );
						$item[ 'next_price' ] = empty($item[ 'next_price' ]) ? $item[ 'price' ] : $item[ 'next_price' ];

						if (empty($item['curr_name'])) {
							$item[ 'formatted_price' ] = tds_util::get_basic_currency( $item[ 'price' ] );
						} else {
							$item[ 'formatted_price' ] = tds_util::get_formatted_currency( $item[ 'price' ], $item[ 'curr_name' ], $item[ 'curr_pos' ], $item[ 'curr_th_sep' ], $item[ 'curr_dec_sep' ], $item[ 'curr_dec_no' ] );
						}
					}
				}
				$result[ 'subscriptions' ] = $results;
			}
		}
		return $result;
	}

	private static function get_latest_subscriptions(WP_REST_Request $request) {
		global $wpdb;

		$result = [];
		$results = $wpdb->get_results("SELECT 
					tds_subscriptions.*, 
					tds_plans.name AS 'plan_name' 
				FROM 
					tds_subscriptions 
					LEFT JOIN tds_plans
					ON tds_subscriptions.plan_id = tds_plans.id 
				WHERE tds_subscriptions.status IN ('waiting_payment', 'active', 'trial', 'free')
				ORDER BY tds_subscriptions.id DESC  
				LIMIT 10", ARRAY_A);

		if ( null !== $results) {
			foreach ($results as &$item) {
				if (!empty($item['user_id'])) {
					$item['user_name'] = get_user_meta($item['user_id'], 'nickname', true);
					$item['end_date'] = tds_util::get_end_date($item['start_date'], $item['last_months_in_cycle'], $item['start_day'])->format('Y-m-d');
					$item[ 'next_price' ] = empty($item[ 'next_price' ]) ? $item[ 'price' ] : $item[ 'next_price' ];

					if (empty($item['curr_name'])) {
						$item[ 'formatted_price' ] = tds_util::get_basic_currency( $item[ 'price' ] );
					} else {
						$item[ 'formatted_price' ] = tds_util::get_formatted_currency( $item[ 'price' ], $item[ 'curr_name' ], $item[ 'curr_pos' ], $item[ 'curr_th_sep' ], $item[ 'curr_dec_sep' ], $item[ 'curr_dec_no' ] );
					}
				}
			}
			$result['subscriptions'] = $results;
		}
		return $result;
	}

	private static function get_info_subscriptions(WP_REST_Request $request) {
		global $wpdb;

		$result = [
			'waiting_payment' => 0,
			'active' => 0,
		];

		foreach ($result as $key => $val ) {
			$status_counter = $wpdb->get_var( "SELECT count(*) FROM tds_subscriptions WHERE status = '" . $key . "'");
			if ( false !== $status_counter) {
				$result[$key] = $status_counter;
			}
		}

		return $result;
	}

	private static function get_current_user_subscriptions(WP_REST_Request $request) {
		global $wpdb;

		$result = [];
		$subscriptions = $wpdb->get_results($wpdb->prepare( "SELECT * FROM tds_subscriptions WHERE user_id = %s LIMIT 1", get_current_user_id()), ARRAY_A);

		if ( null !== $subscriptions) {

			if (count($subscriptions) &&
			    ( 'waiting_payment' === $subscriptions[0]['status'] || 'trial' === $subscriptions[0]['status'] ) &&
			    'direct' === $subscriptions[0]['payment_type']) {

				$payment_bank = $wpdb->get_results("SELECT * FROM tds_payment_bank LIMIT 1", ARRAY_A);
				if ( null !== $payment_bank && count($payment_bank) && 1 == $payment_bank[0]['is_active']) {
					foreach ($payment_bank[0] as $key => $val) {
						if (in_array($key, ['id', 'is_active', 'created_at'])) {
							continue;
						}
						$subscriptions[0][$key] = $val;
					}
				}
			}

			$result['subscriptions'] = $subscriptions;
		}
		return $result;
	}

	private static function modify_subscription(WP_REST_Request $request) {
		$result = [];
        $subs_id = $request->get_param('subsId');
        $subs_status = $request->get_param('subsStatus');
        $subs_start_date = $request->get_param('subsStartDate');

        $subs_bill_first_name = $request->get_param('subsBillFirstName');
        $subs_bill_last_name = $request->get_param('subsBillLastName');
        $subs_bill_company_name = $request->get_param('subsBillCompanyName');
        $subs_bill_email = $request->get_param('subsBillEmail');
        $subs_bill_cui = $request->get_param('subsBillCUI');
        $subs_bill_j = $request->get_param('subsBillJ');
        $subs_bill_vat_number = $request->get_param('subsBillVATNumber');
        $subs_bill_address = $request->get_param('subsBillAddress');
        $subs_bill_city = $request->get_param('subsBillCity');
        $subs_bill_country = $request->get_param('subsBillCountry');
        $subs_bill_bank_account = $request->get_param('subsBillBankAccount');
        $subs_bill_post_code = $request->get_param('subsBillPostCode');

        $subs_price = $request->get_param('subsPrice');
        $subs_plan_id = $request->get_param('subsPlanId');

        if (empty($subs_id)) {
			$result[ 'error' ][] = 'Invalid subscription id';
		}
		if (empty($subs_status)) {
			$result[ 'error' ][] = 'Invalid subscription status';
		}
		if (empty($subs_start_date)) {
			$result[ 'error' ][] = 'Invalid subscription start date';
		}
		if (!empty($result)) {
			// return error
		} else {
			global $wpdb;

			$update_result = $wpdb->update( 'tds_subscriptions',
				array(
					'status'     => $subs_status,
					'start_date' => $subs_start_date,
					'billing_first_name' => $subs_bill_first_name,
					'billing_last_name' => $subs_bill_last_name,
					'billing_company_name' => $subs_bill_company_name,
					'billing_email' => $subs_bill_email,
					'billing_cui' => $subs_bill_cui,
					'billing_j' => $subs_bill_j,
					'billing_vat_number' => $subs_bill_vat_number,
					'billing_address' => $subs_bill_address,
					'billing_city' => $subs_bill_city,
					'billing_country' => $subs_bill_country,
					'billing_bank_account' => $subs_bill_bank_account,
					'billing_post_code' => $subs_bill_post_code,
					'next_price' => $subs_price,
					'plan_id' => $subs_plan_id
				),
				array( 'id' => $subs_id ),
				array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'),
				array( '%d' )
			);

			if ( false !== $update_result ) {
				$result[ 'success' ] = true;
			}
		}

		return $result;
	}

	private static function create_wizard_locker(WP_REST_Request $request) {
		$result = [];
        $plan_id_free = $request->get_param('planIdFree');
        $plan_id_month = $request->get_param('planIdMonth');
        $plan_id_year = $request->get_param('planIdYear');

        if (empty($plan_id_free)) {
			$result[ 'error' ][] = 'Invalid plan id free';
		}
		if (empty($plan_id_month)) {
			$result[ 'error' ][] = 'Invalid plan id month';
		}
		if (empty($plan_id_year)) {
			$result[ 'error' ][] = 'Invalid plan id year';
		}
		if (!empty($result)) {
			// return error
		} else {

			$insert_result = wp_insert_post([
				'post_type' => 'tds_locker',
				'post_status' => 'publish',
				'post_title' => 'Wizard Locker (default)',
				'post_name' => 'tds_default_wizard_locker'
			]);

			if ( empty( $insert_result ) ) {
				$result[ 'error' ][] = 'Locker could not be created';
			} else if ( is_wp_error( $insert_result ) ) {
				$result[ 'error' ][] = $insert_result->get_error_message();
			} else {

                if ( TD_THEME_NAME === 'Newsmag' ) {
                    $post_content = '[tdc_zone type="tdc_content"][vc_row tdc_css="eyJhbGwiOnsibWFyZ2luLWJvdHRvbSI6IjQwIiwicGFkZGluZy10b3AiOiI0MCIsImRpc3BsYXkiOiIifSwicGhvbmUiOnsibWFyZ2luLWJvdHRvbSI6IjQwIiwiZGlzcGxheSI6IiJ9LCJwaG9uZV9tYXhfd2lkdGgiOjc2N30="][vc_column width="1/3" tdc_css="eyJwb3J0cmFpdCI6eyJkaXNwbGF5IjoiIn0sInBvcnRyYWl0X21heF93aWR0aCI6MTAyMywicG9ydHJhaXRfbWluX3dpZHRoIjo3NjgsInBob25lIjp7InBhZGRpbmctcmlnaHQiOiIxNCIsInBhZGRpbmctYm90dG9tIjoiMzAiLCJwYWRkaW5nLWxlZnQiOiIxNCIsImRpc3BsYXkiOiIifSwicGhvbmVfbWF4X3dpZHRoIjo3Njd9"][vc_column_text f_post_font_size="18" tdc_css="eyJhbGwiOnsicGFkZGluZy1ib3R0b20iOiIwIiwiYm9yZGVyLWNvbG9yIjoiI2U2ZTZlNiIsImRpc3BsYXkiOiIifSwicG9ydHJhaXQiOnsibWFyZ2luLXJpZ2h0IjoiMTAiLCJtYXJnaW4tbGVmdCI6IjEwIiwiZGlzcGxheSI6IiJ9LCJwb3J0cmFpdF9tYXhfd2lkdGgiOjEwMjMsInBvcnRyYWl0X21pbl93aWR0aCI6NzY4fQ=="]<span style="color: #888;">Unlock</span>[/vc_column_text][vc_column_text tdc_css="eyJhbGwiOnsibWFyZ2luLXRvcCI6IjAiLCJib3JkZXItdG9wLXdpZHRoIjoiMCIsInBhZGRpbmctdG9wIjoiMCIsImRpc3BsYXkiOiIifSwicG9ydHJhaXQiOnsibWFyZ2luLXJpZ2h0IjoiMTAiLCJtYXJnaW4tbGVmdCI6IjEwIiwiZGlzcGxheSI6IiJ9LCJwb3J0cmFpdF9tYXhfd2lkdGgiOjEwMjMsInBvcnRyYWl0X21pbl93aWR0aCI6NzY4fQ=="]<h3><strong>The Best Experience</strong></h3>[/vc_column_text][vc_column_text tdc_css="eyJhbGwiOnsiYm9yZGVyLXRvcC13aWR0aCI6IjAiLCJkaXNwbGF5IjoiIn0sInBvcnRyYWl0Ijp7Im1hcmdpbi1yaWdodCI6IjEwIiwibWFyZ2luLWxlZnQiOiIxMCIsImRpc3BsYXkiOiIifSwicG9ydHJhaXRfbWF4X3dpZHRoIjoxMDIzLCJwb3J0cmFpdF9taW5fd2lkdGgiOjc2OH0="]<p><span style="color: #888888;">Ut tempor suscipit justo a viverra. Etiam turpis erat, hendrerit quis molestie ut, vestibulum non diam.</span></p><p><span style="color: #888888;">Fusce at tortor tempor, porta elit ut, fringilla risus. Mauris ante ante, vulputate tincidunt eros at, scelerisque dictum justo.</span></p>[/vc_column_text][vc_raw_html tdc_css="eyJhbGwiOnsibWFyZ2luLWxlZnQiOiIyMCIsInBhZGRpbmctdG9wIjoiMjAiLCJwYWRkaW5nLXJpZ2h0IjoiMTYwIiwiZGlzcGxheSI6IiJ9LCJwaG9uZSI6eyJwYWRkaW5nLXJpZ2h0IjoiMjAwIiwiZGlzcGxheSI6IiJ9LCJwaG9uZV9tYXhfd2lkdGgiOjc2NywicG9ydHJhaXQiOnsibWFyZ2luLXJpZ2h0IjoiMTAiLCJtYXJnaW4tbGVmdCI6IjEwIiwicGFkZGluZy1yaWdodCI6IjgwIiwiZGlzcGxheSI6IiJ9LCJwb3J0cmFpdF9tYXhfd2lkdGgiOjEwMjMsInBvcnRyYWl0X21pbl93aWR0aCI6NzY4fQ=="]JTNDJTNGeG1sJTIwdmVyc2lvbiUzRCUyMjEuMCUyMiUyMCUzRiUzRSUzQ3N2ZyUyMHZlcnNpb24lM0QlMjIxLjElMjIlMjB2aWV3Qm94JTNEJTIyMCUyMDAlMjA2MCUyMDYwJTIyJTIweG1sbnMlM0QlMjJodHRwJTNBJTJGJTJGd3d3LnczLm9yZyUyRjIwMDAlMkZzdmclMjIlMjB4bWxucyUzQXhsaW5rJTNEJTIyaHR0cCUzQSUyRiUyRnd3dy53My5vcmclMkYxOTk5JTJGeGxpbmslMjIlM0UlM0N0aXRsZSUyRiUzRSUzQ2Rlc2MlMkYlM0UlM0NkZWZzJTJGJTNFJTNDZyUyMGZpbGwlM0QlMjJub25lJTIyJTIwZmlsbC1ydWxlJTNEJTIyZXZlbm9kZCUyMiUyMGlkJTNEJTIyUGVvcGxlJTIyJTIwc3Ryb2tlJTNEJTIybm9uZSUyMiUyMHN0cm9rZS13aWR0aCUzRCUyMjElMjIlM0UlM0NnJTIwZmlsbCUzRCUyMiUyMzAwMDAwMCUyMiUyMGlkJTNEJTIySWNvbi0xJTIyJTNFJTNDcGF0aCUyMGQlM0QlMjJNNSUyQzE0JTIwQzQuNDQ4JTJDMTQlMjA0JTJDMTQuNDQ4JTIwNCUyQzE1JTIwTDQlMkM0OSUyMEM0JTJDNDkuNTUyJTIwNC40NDglMkM1MCUyMDUlMkM1MCUyMEM1LjU1MiUyQzUwJTIwNiUyQzQ5LjU1MiUyMDYlMkM0OSUyMEw2JTJDMTUlMjBDNiUyQzE0LjQ0OCUyMDUuNTUyJTJDMTQlMjA1JTJDMTQlMjBMNSUyQzE0JTIwWiUyME0yMiUyQzIyJTIwQzIyJTJDMjMuMTAzJTIwMjIuODk3JTJDMjQlMjAyNCUyQzI0JTIwQzI1LjEwMyUyQzI0JTIwMjYlMkMyMy4xMDMlMjAyNiUyQzIyJTIwQzI2JTJDMjAuODk3JTIwMjUuMTAzJTJDMjAlMjAyNCUyQzIwJTIwQzIyLjg5NyUyQzIwJTIwMjIlMkMyMC44OTclMjAyMiUyQzIyJTIwTDIyJTJDMjIlMjBaJTIwTTE4JTJDMzElMjBDMTglMkMzMS41NTIlMjAxOC40NDglMkMzMiUyMDE5JTJDMzIlMjBMMjUlMkMzMiUyMEMyNS4yNjUlMkMzMiUyMDI1LjUyJTJDMzEuODk1JTIwMjUuNzA3JTJDMzEuNzA3JTIwTDMyJTJDMjUuNDE0JTIwTDM0LjI5MyUyQzI3LjcwNyUyMEMzNC42ODQlMkMyOC4wOTglMjAzNS4zMTYlMkMyOC4wOTglMjAzNS43MDclMkMyNy43MDclMjBMNDAlMkMyMy40MTQlMjBMNDQuMjkzJTJDMjcuNzA3JTIwQzQ0LjY4NCUyQzI4LjA5OCUyMDQ1LjMxNiUyQzI4LjA5OCUyMDQ1LjcwNyUyQzI3LjcwNyUyMEM0Ni4wOTglMkMyNy4zMTYlMjA0Ni4wOTglMkMyNi42ODQlMjA0NS43MDclMkMyNi4yOTMlMjBMNDAuNzA3JTJDMjEuMjkzJTIwQzQwLjMxNiUyQzIwLjkwMiUyMDM5LjY4NCUyQzIwLjkwMiUyMDM5LjI5MyUyQzIxLjI5MyUyMEwzNSUyQzI1LjU4NiUyMEwzMi43MDclMkMyMy4yOTMlMjBDMzIuMzE2JTJDMjIuOTAyJTIwMzEuNjg0JTJDMjIuOTAyJTIwMzEuMjkzJTJDMjMuMjkzJTIwTDI0LjU4NiUyQzMwJTIwTDIwJTJDMzAlMjBMMjAlMkMxOCUyMEw0OCUyQzE4JTIwTDQ4JTJDMzAlMjBMMzElMkMzMCUyMEMzMC40NDglMkMzMCUyMDMwJTJDMzAuNDQ4JTIwMzAlMkMzMSUyMEMzMCUyQzMxLjU1MiUyMDMwLjQ0OCUyQzMyJTIwMzElMkMzMiUyMEw0OSUyQzMyJTIwQzQ5LjU1MiUyQzMyJTIwNTAlMkMzMS41NTIlMjA1MCUyQzMxJTIwTDUwJTJDMTclMjBDNTAlMkMxNi40NDglMjA0OS41NTIlMkMxNiUyMDQ5JTJDMTYlMjBMMTklMkMxNiUyMEMxOC40NDglMkMxNiUyMDE4JTJDMTYuNDQ4JTIwMTglMkMxNyUyMEwxOCUyQzMxJTIwWiUyME00NyUyQzQ4JTIwTDM3JTJDNDglMjBDMzYuNDQ4JTJDNDglMjAzNiUyQzQ4LjQ0OCUyMDM2JTJDNDklMjBDMzYlMkM0OS41NTIlMjAzNi40NDglMkM1MCUyMDM3JTJDNTAlMjBMNDclMkM1MCUyMEM0Ny41NTIlMkM1MCUyMDQ4JTJDNDkuNTUyJTIwNDglMkM0OSUyMEM0OCUyQzQ4LjQ0OCUyMDQ3LjU1MiUyQzQ4JTIwNDclMkM0OCUyMEw0NyUyQzQ4JTIwWiUyME0xOSUyQzUwJTIwTDI5JTJDNTAlMjBDMjkuNTUyJTJDNTAlMjAzMCUyQzQ5LjU1MiUyMDMwJTJDNDklMjBDMzAlMkM0OC40NDglMjAyOS41NTIlMkM0OCUyMDI5JTJDNDglMjBMMTklMkM0OCUyMEMxOC40NDglMkM0OCUyMDE4JTJDNDguNDQ4JTIwMTglMkM0OSUyMEMxOCUyQzQ5LjU1MiUyMDE4LjQ0OCUyQzUwJTIwMTklMkM1MCUyMEwxOSUyQzUwJTIwWiUyME00NyUyQzQyJTIwTDM3JTJDNDIlMjBDMzYuNDQ4JTJDNDIlMjAzNiUyQzQyLjQ0OCUyMDM2JTJDNDMlMjBDMzYlMkM0My41NTIlMjAzNi40NDglMkM0NCUyMDM3JTJDNDQlMjBMNDclMkM0NCUyMEM0Ny41NTIlMkM0NCUyMDQ4JTJDNDMuNTUyJTIwNDglMkM0MyUyMEM0OCUyQzQyLjQ0OCUyMDQ3LjU1MiUyQzQyJTIwNDclMkM0MiUyMEw0NyUyQzQyJTIwWiUyME0xOSUyQzQ0JTIwTDMxJTJDNDQlMjBDMzEuNTUyJTJDNDQlMjAzMiUyQzQzLjU1MiUyMDMyJTJDNDMlMjBDMzIlMkM0Mi40NDglMjAzMS41NTIlMkM0MiUyMDMxJTJDNDIlMjBMMTklMkM0MiUyMEMxOC40NDglMkM0MiUyMDE4JTJDNDIuNDQ4JTIwMTglMkM0MyUyMEMxOCUyQzQzLjU1MiUyMDE4LjQ0OCUyQzQ0JTIwMTklMkM0NCUyMEwxOSUyQzQ0JTIwWiUyME01MCUyQzM3JTIwQzUwJTJDMzYuNDQ4JTIwNDkuNTUyJTJDMzYlMjA0OSUyQzM2JTIwTDM3JTJDMzYlMjBDMzYuNDQ4JTJDMzYlMjAzNiUyQzM2LjQ0OCUyMDM2JTJDMzclMjBDMzYlMkMzNy41NTIlMjAzNi40NDglMkMzOCUyMDM3JTJDMzglMjBMNDklMkMzOCUyMEM0OS41NTIlMkMzOCUyMDUwJTJDMzcuNTUyJTIwNTAlMkMzNyUyMEw1MCUyQzM3JTIwWiUyME00OSUyQzEyJTIwQzQ5LjU1MiUyQzEyJTIwNTAlMkMxMS41NTIlMjA1MCUyQzExJTIwQzUwJTJDMTAuNDQ4JTIwNDkuNTUyJTJDMTAlMjA0OSUyQzEwJTIwTDQ1JTJDMTAlMjBDNDQuNDQ4JTJDMTAlMjA0NCUyQzEwLjQ0OCUyMDQ0JTJDMTElMjBDNDQlMkMxMS41NTIlMjA0NC40NDglMkMxMiUyMDQ1JTJDMTIlMjBMNDklMkMxMiUyMFolMjBNMTklMkMxMiUyMEwzNSUyQzEyJTIwQzM1LjU1MiUyQzEyJTIwMzYlMkMxMS41NTIlMjAzNiUyQzExJTIwQzM2JTJDMTAuNDQ4JTIwMzUuNTUyJTJDMTAlMjAzNSUyQzEwJTIwTDE5JTJDMTAlMjBDMTguNDQ4JTJDMTAlMjAxOCUyQzEwLjQ0OCUyMDE4JTJDMTElMjBDMTglMkMxMS41NTIlMjAxOC40NDglMkMxMiUyMDE5JTJDMTIlMjBMMTklMkMxMiUyMFolMjBNMTklMkMzOCUyMEwyOSUyQzM4JTIwQzI5LjU1MiUyQzM4JTIwMzAlMkMzNy41NTIlMjAzMCUyQzM3JTIwQzMwJTJDMzYuNDQ4JTIwMjkuNTUyJTJDMzYlMjAyOSUyQzM2JTIwTDE5JTJDMzYlMjBDMTguNDQ4JTJDMzYlMjAxOCUyQzM2LjQ0OCUyMDE4JTJDMzclMjBDMTglMkMzNy41NTIlMjAxOC40NDglMkMzOCUyMDE5JTJDMzglMjBMMTklMkMzOCUyMFolMjBNNjAlMkM1JTIwTDYwJTJDNTElMjBDNjAlMkM1Ni41NTElMjA1Ni41NTElMkM2MCUyMDUxJTJDNjAlMjBMOSUyQzYwJTIwQzMuNDQ5JTJDNjAlMjAwJTJDNTYuNTUxJTIwMCUyQzUxJTIwTDAlMkMxMyUyMEMwJTJDMTAuMjQzJTIwMi4yNDMlMkM4JTIwNSUyQzglMjBDNS41NTIlMkM4JTIwNiUyQzguNDQ4JTIwNiUyQzklMjBDNiUyQzkuNTUyJTIwNS41NTIlMkMxMCUyMDUlMkMxMCUyMEMzLjM0NiUyQzEwJTIwMiUyQzExLjM0NiUyMDIlMkMxMyUyMEwyJTJDNTElMjBDMiUyQzU1LjQ0OSUyMDQuNTUxJTJDNTglMjA5JTJDNTglMjBMNTElMkM1OCUyMEM1NS40NDklMkM1OCUyMDU4JTJDNTUuNDQ5JTIwNTglMkM1MSUyMEw1OCUyQzUlMjBDNTglMkMzLjM0NiUyMDU2LjY1NCUyQzIlMjA1NSUyQzIlMjBMMTMlMkMyJTIwQzExLjQwMiUyQzIlMjAxMCUyQzMuNDAyJTIwMTAlMkM1JTIwTDEwJTJDNTMlMjBDMTAlMkM1My41NTIlMjA5LjU1MiUyQzU0JTIwOSUyQzU0JTIwQzguNDQ4JTJDNTQlMjA4JTJDNTMuNTUyJTIwOCUyQzUzJTIwTDglMkM1JTIwQzglMkMyLjI5JTIwMTAuMjklMkMwJTIwMTMlMkMwJTIwTDU1JTJDMCUyMEM1Ny43NTclMkMwJTIwNjAlMkMyLjI0MyUyMDYwJTJDNSUyMEw2MCUyQzUlMjBaJTIyJTIwaWQlM0QlMjJuZXdzcGFwZXIlMjIlMkYlM0UlM0MlMkZnJTNFJTNDJTJGZyUzRSUzQyUyRnN2ZyUzRQ==[/vc_raw_html][/vc_column][vc_column width="1/3" tdc_css="eyJhbGwiOnsiYmFja2dyb3VuZC1jb2xvciI6IiNmOGY4ZjgiLCJkaXNwbGF5IjoiIn0sInBob25lIjp7Im1hcmdpbi1ib3R0b20iOiIwIiwicGFkZGluZy1ib3R0b20iOiIxMCIsImRpc3BsYXkiOiIifSwicGhvbmVfbWF4X3dpZHRoIjo3NjcsInBvcnRyYWl0Ijp7ImRpc3BsYXkiOiIifSwicG9ydHJhaXRfbWF4X3dpZHRoIjoxMDIzLCJwb3J0cmFpdF9taW5fd2lkdGgiOjc2OH0=" column_height="eyJwb3J0cmFpdCI6IjYwMyIsImFsbCI6IjU1NnB4IiwicGhvbmUiOiIwIn0=" vertical_align=""][vc_column_text tdc_css="eyJhbGwiOnsiYm9yZGVyLWJvdHRvbS13aWR0aCI6IjEiLCJib3JkZXItY29sb3IiOiIjZTZlNmU2IiwiZGlzcGxheSI6ImJsb2NrIn19" f_h3_font_weight="700"]<h3>Free</h3>[/vc_column_text][tds_plans_price curr_txt="$" free_plan="' . $plan_id_free . '" inline="yes" f_price_font_size="36" vert_align="baseline" tdc_css="eyJhbGwiOnsiYm9yZGVyLXRvcC13aWR0aCI6IjAiLCJwYWRkaW5nLXJpZ2h0IjoiMTAiLCJkaXNwbGF5IjoiIn19"][tds_plans_description year_plan_desc="JTJGeWVhcg==" month_plan_desc="JTJGJTIwbW9udGg=" inline="yes" free_plan_desc="JTJGJTIwZm9yZXZlcg==" tdc_css="eyJhbGwiOnsicGFkZGluZy1sZWZ0IjoiMCIsImRpc3BsYXkiOiJpbmxpbmUtYmxvY2sifX0=" vert_align="baseline" f_descr_font_size="14" f_descr_font_line_height="1"][tds_plans_button display="full" all_border="2" text_color="#4db2ec" bg_color="#ffffff" all_border_color="#4db2ec" horiz_align="content-horiz-center" border_radius="5" padd="16px 24px 18px" free_plan="' . $plan_id_free . '" f_txt_font_weight="700" bg_color_h="#ffffff"][vc_column_text tdc_css="eyJhbGwiOnsiYm9yZGVyLXRvcC13aWR0aCI6IjAiLCJkaXNwbGF5IjoiIn0sInBvcnRyYWl0Ijp7Im1hcmdpbi1yaWdodCI6IjEwIiwibWFyZ2luLWxlZnQiOiIxMCIsImRpc3BsYXkiOiIifSwicG9ydHJhaXRfbWF4X3dpZHRoIjoxMDIzLCJwb3J0cmFpdF9taW5fd2lkdGgiOjc2OH0=" f_list_font_line_height="eyJwb3J0cmFpdCI6IjEuNyIsImFsbCI6IjIifQ=="]<span style="color: #888888;"><strong>What you\'ll get</strong></span>[/vc_column_text][vc_column_text f_list_font_line_height="eyJhbGwiOiIyIiwicG9ydHJhaXQiOiIxLjcifQ==" border_top="no_border_top" tdc_css="eyJhbGwiOnsibWFyZ2luLXRvcCI6Ii0xNSIsIm1hcmdpbi1sZWZ0IjoiMCIsInBhZGRpbmctdG9wIjoiMCIsImRpc3BsYXkiOiIifSwicG9ydHJhaXQiOnsiZGlzcGxheSI6IiJ9LCJwb3J0cmFpdF9tYXhfd2lkdGgiOjEwMjMsInBvcnRyYWl0X21pbl93aWR0aCI6NzY4fQ=="]<ul><li><strong><span style="color: #444444;">Etiam est nibh, lobortis sit</span></strong></li><li><strong><span style="color: #444444;">Praesent euismod ac</span></strong></li><li><strong><span style="color: #444444;">Ut mollis pellentesque tortor</span></strong></li><li><strong><span style="color: #444444;">Nullam eu erat condimentum</span></strong></li></ul>[/vc_column_text][/vc_column][vc_column width="1/3" tdc_css="eyJhbGwiOnsiYmFja2dyb3VuZC1jb2xvciI6IiNlNWYzZmYiLCJkaXNwbGF5IjoiIn0sInBob25lIjp7InBhZGRpbmctYm90dG9tIjoiMjAiLCJkaXNwbGF5IjoiIn0sInBob25lX21heF93aWR0aCI6NzY3fQ=="][vc_column_text f_h3_font_weight="700" tdc_css="eyJhbGwiOnsiYm9yZGVyLWJvdHRvbS13aWR0aCI6IjEiLCJib3JkZXItY29sb3IiOiIjZTZlNmU2IiwiZGlzcGxheSI6IiJ9fQ=="]<h3>Pro</h3>[/vc_column_text][tds_plans_price curr_txt="$" free_plan="" year_plan="' . $plan_id_year . '" month_plan="' . $plan_id_month . '" inline="yes" f_price_font_size="36" vert_align="baseline" tdc_css="eyJhbGwiOnsiYm9yZGVyLXRvcC13aWR0aCI6IjAiLCJwYWRkaW5nLXJpZ2h0IjoiMTAiLCJkaXNwbGF5IjoiIn19"][tds_plans_description inline="yes" tdc_css="eyJhbGwiOnsicGFkZGluZy1sZWZ0IjoiMCIsImRpc3BsYXkiOiJpbmxpbmUtYmxvY2sifX0=" vert_align="baseline" f_descr_font_size="14" f_descr_font_line_height="1" year_plan_desc="JTJGeWVhcg==" month_plan_desc="JTJGJTIwbW9udGg="][tds_plans_button display="full" horiz_align="content-horiz-center" border_radius="5" padd="16px 24px 18px" year_plan="' . $plan_id_year . '" month_plan="' . $plan_id_month . '" bg_color="#4db2ec" f_txt_font_weight="700"][vc_column_text tdc_css="eyJhbGwiOnsiYm9yZGVyLXRvcC13aWR0aCI6IjAiLCJkaXNwbGF5IjoiIn0sInBvcnRyYWl0Ijp7Im1hcmdpbi1yaWdodCI6IjEwIiwibWFyZ2luLWxlZnQiOiIxMCIsImRpc3BsYXkiOiIifSwicG9ydHJhaXRfbWF4X3dpZHRoIjoxMDIzLCJwb3J0cmFpdF9taW5fd2lkdGgiOjc2OH0=" f_list_font_line_height="eyJwb3J0cmFpdCI6IjEuNyIsImFsbCI6IjIifQ=="]<span style="color: #888888;"><strong>What you\'ll get</strong></span>[/vc_column_text][vc_column_text f_list_font_line_height="eyJhbGwiOiIyIiwicG9ydHJhaXQiOiIxLjcifQ==" border_top="no_border_top" tdc_css="eyJhbGwiOnsibWFyZ2luLXRvcCI6Ii0xNSIsIm1hcmdpbi1sZWZ0IjoiMCIsInBhZGRpbmctdG9wIjoiMCIsImRpc3BsYXkiOiIifX0="]<ul><li><strong><span style="color: #444444;">Etiam est nibh, lobortis sit</span></strong></li><li><strong><span style="color: #444444;">Praesent euismod ac</span></strong></li><li><strong><span style="color: #444444;">Ut mollis pellentesque tortor</span></strong></li><li><strong><span style="color: #444444;">Nullam eu erat condimentum</span></strong></li><li><strong><span style="color: #444444;">Donec quis est ac felis</span></strong></li><li><strong><span style="color: #444444;">Orci varius natoque dolor</span></strong></li></ul>[/vc_column_text][tds_plans_switcher tds_plans_switcher1-annual_txt="Yearly pricing" tds_plans_switcher1-monthly_txt="Monthly pricing" tdc_css="eyJwaG9uZSI6eyJwYWRkaW5nLXRvcCI6IjEwIiwicGFkZGluZy1ib3R0b20iOiIyMCIsImRpc3BsYXkiOiIifSwicGhvbmVfbWF4X3dpZHRoIjo3NjcsInBvcnRyYWl0Ijp7InBhZGRpbmctdG9wIjoiMTAiLCJkaXNwbGF5IjoiIn0sInBvcnRyYWl0X21heF93aWR0aCI6MTAyMywicG9ydHJhaXRfbWluX3dpZHRoIjo3Njh9" tds_plans_switcher="tds_plans_switcher1" tds_plans_switcher1-bg_color="#4db2ec"][/vc_column][/vc_row][/tdc_zone]';
                } else {
                    $post_content = '[tdc_zone type="tdc_content"][vc_row flex_layout="row" flex_vert_align="stretch" flex_wrap="yes" tdc_css="eyJwaG9uZSI6eyJtYXJnaW4tdG9wIjoiMzIiLCJtYXJnaW4tcmlnaHQiOiItMjAiLCJtYXJnaW4tYm90dG9tIjoiMzIiLCJtYXJnaW4tbGVmdCI6Ii0yMCIsIndpZHRoIjoiYXV0byIsImRpc3BsYXkiOiIifSwicGhvbmVfbWF4X3dpZHRoIjo3NjcsImFsbCI6eyJtYXJnaW4tdG9wIjoiNDgiLCJtYXJnaW4tYm90dG9tIjoiNDgiLCJkaXNwbGF5IjoiIn19"][vc_column width="1/3" flex_width="eyJwb3J0cmFpdCI6IjEwMCUifQ==" tdc_css="eyJhbGwiOnsicGFkZGluZy10b3AiOiIyOCIsInBhZGRpbmctcmlnaHQiOiI2MCIsInBhZGRpbmctYm90dG9tIjoiMzgiLCJwYWRkaW5nLWxlZnQiOiIzMCIsImRpc3BsYXkiOiIifSwicGhvbmUiOnsicGFkZGluZy10b3AiOiIyMiIsInBhZGRpbmctcmlnaHQiOiIyMCIsInBhZGRpbmctYm90dG9tIjoiNDIiLCJwYWRkaW5nLWxlZnQiOiIyMCIsImRpc3BsYXkiOiIifSwicGhvbmVfbWF4X3dpZHRoIjo3NjcsInBvcnRyYWl0Ijp7InBhZGRpbmctdG9wIjoiMjMiLCJwYWRkaW5nLXJpZ2h0IjoiMjUiLCJwYWRkaW5nLWJvdHRvbSI6IjQzIiwicGFkZGluZy1sZWZ0IjoiMjUiLCJkaXNwbGF5IjoiIn0sInBvcnRyYWl0X21heF93aWR0aCI6MTAxOCwicG9ydHJhaXRfbWluX3dpZHRoIjo3NjgsImxhbmRzY2FwZSI6eyJwYWRkaW5nLXRvcCI6IjIzIiwicGFkZGluZy1yaWdodCI6IjI1IiwicGFkZGluZy1ib3R0b20iOiIzMyIsInBhZGRpbmctbGVmdCI6IjI1IiwiZGlzcGxheSI6IiJ9LCJsYW5kc2NhcGVfbWF4X3dpZHRoIjoxMTQwLCJsYW5kc2NhcGVfbWluX3dpZHRoIjoxMDE5fQ=="][vc_row_inner flex_layout="eyJhbGwiOiJjb2x1bW4iLCJwb3J0cmFpdCI6InJvdyJ9" flex_vert_align="eyJhbGwiOiJmbGV4LXN0YXJ0IiwicG9ydHJhaXQiOiJjZW50ZXIifQ=="][vc_column_inner width="1/2" flex_width="100%" flex_grow="eyJhbGwiOiJkZWZhdWx0IiwicG9ydHJhaXQiOiJvbiJ9"][tdm_block_column_title title_text="VW5sb2Nr" title_tag="h3" title_size="tdm-title-sm" tds_title1-f_title_font_weight="400" tds_title1-f_title_font_line_height="1.2" tds_title1-f_title_font_size="eyJsYW5kc2NhcGUiOiIxNyIsInBvcnRyYWl0IjoiMTUiLCJwaG9uZSI6IjE3IiwiYWxsIjoiMTkifQ==" tds_title1-title_color="rgba(85,93,102,0.7)"][tdm_block_column_title title_text="VGhlJTIwQmVzdCUyMEV4cGVyaWVuY2U=" title_tag="h3" title_size="tdm-title-sm" tds_title1-f_title_font_weight="700" tds_title1-f_title_font_line_height="1.2" tds_title1-f_title_font_size="eyJsYW5kc2NhcGUiOiIyMSIsInBvcnRyYWl0IjoiMTkiLCJwaG9uZSI6IjIxIn0=" tdc_css="eyJhbGwiOnsibWFyZ2luLXRvcCI6Ii0xMiIsIm1hcmdpbi1ib3R0b20iOiIyNSIsImRpc3BsYXkiOiIifSwibGFuZHNjYXBlIjp7Im1hcmdpbi10b3AiOiItMTQiLCJtYXJnaW4tYm90dG9tIjoiMjMiLCJkaXNwbGF5IjoiIn0sImxhbmRzY2FwZV9tYXhfd2lkdGgiOjExNDAsImxhbmRzY2FwZV9taW5fd2lkdGgiOjEwMTksInBvcnRyYWl0Ijp7Im1hcmdpbi10b3AiOiItMTUiLCJtYXJnaW4tYm90dG9tIjoiMTgiLCJkaXNwbGF5IjoiIn0sInBvcnRyYWl0X21heF93aWR0aCI6MTAxOCwicG9ydHJhaXRfbWluX3dpZHRoIjo3NjgsInBob25lIjp7Im1hcmdpbi10b3AiOiItMTQiLCJtYXJnaW4tYm90dG9tIjoiMjAiLCJkaXNwbGF5IjoiIn0sInBob25lX21heF93aWR0aCI6NzY3fQ=="][tdm_block_inline_text description="VXQlMjB0ZW1wb3IlMjBzdXNjaXBpdCUyMGp1c3RvJTIwYSUyMHZpdmVycmEuJTIwRXRpYW0lMjB0dXJwaXMlMjBlcmF0JTJDJTIwaGVuZHJlcml0JTIwcXVpcyUyMG1vbGVzdGllJTIwdXQlMkMlMjB2ZXN0aWJ1bHVtJTIwbm9uJTIwZGlhbS4=" f_descr_font_size="eyJhbGwiOiIxNCIsImxhbmRzY2FwZSI6IjEzIiwicG9ydHJhaXQiOiIxMyIsInBob25lIjoiMTMifQ==" description_color="rgba(85,93,102,0.9)" tdc_css="eyJhbGwiOnsibWFyZ2luLWJvdHRvbSI6IjE0IiwiZGlzcGxheSI6IiJ9LCJsYW5kc2NhcGUiOnsibWFyZ2luLWJvdHRvbSI6IjEyIiwiZGlzcGxheSI6IiJ9LCJsYW5kc2NhcGVfbWF4X3dpZHRoIjoxMTQwLCJsYW5kc2NhcGVfbWluX3dpZHRoIjoxMDE5LCJwb3J0cmFpdCI6eyJtYXJnaW4tYm90dG9tIjoiMTAiLCJkaXNwbGF5IjoiIn0sInBvcnRyYWl0X21heF93aWR0aCI6MTAxOCwicG9ydHJhaXRfbWluX3dpZHRoIjo3NjgsInBob25lIjp7Im1hcmdpbi1ib3R0b20iOiIxMSIsImRpc3BsYXkiOiIifSwicGhvbmVfbWF4X3dpZHRoIjo3Njd9" f_descr_font_line_height="1.5"][tdm_block_inline_text description="RnVzY2UlMjBhdCUyMHRvcnRvciUyMHRlbXBvciUyQyUyMHBvcnRhJTIwZWxpdCUyMHV0JTJDJTIwZnJpbmdpbGxhJTIwcmlzdXMuJTIwTWF1cmlzJTIwYW50ZSUyMGFudGUlMkMlMjB2dWxwdXRhdGUlMjB0aW5jaWR1bnQlMjBlcm9zJTIwYXQlMkMlMjBzY2VsZXJpc3F1ZSUyMGRpY3R1bSUyMGp1c3RvLg==" f_descr_font_size="eyJhbGwiOiIxNCIsImxhbmRzY2FwZSI6IjEzIiwicG9ydHJhaXQiOiIxMyIsInBob25lIjoiMTMifQ==" description_color="rgba(85,93,102,0.9)" tdc_css="eyJhbGwiOnsiZGlzcGxheSI6IiJ9LCJsYW5kc2NhcGUiOnsiZGlzcGxheSI6IiJ9LCJsYW5kc2NhcGVfbWF4X3dpZHRoIjoxMTQwLCJsYW5kc2NhcGVfbWluX3dpZHRoIjoxMDE5LCJwb3J0cmFpdCI6eyJkaXNwbGF5IjoiIn0sInBvcnRyYWl0X21heF93aWR0aCI6MTAxOCwicG9ydHJhaXRfbWluX3dpZHRoIjo3NjgsInBob25lIjp7Im1hcmdpbi1ib3R0b20iOiI5IiwiZGlzcGxheSI6IiJ9LCJwaG9uZV9tYXhfd2lkdGgiOjc2N30=" f_descr_font_line_height="1.5"][/vc_column_inner][vc_column_inner width="1/2" flex_width="eyJhbGwiOiIxMDAlIiwicG9ydHJhaXQiOiJhdXRvIn0=" tdc_css="eyJhbGwiOnsicGFkZGluZy10b3AiOiI1MCIsImRpc3BsYXkiOiIifSwibGFuZHNjYXBlIjp7InBhZGRpbmctdG9wIjoiNDUiLCJkaXNwbGF5IjoiIn0sImxhbmRzY2FwZV9tYXhfd2lkdGgiOjExNDAsImxhbmRzY2FwZV9taW5fd2lkdGgiOjEwMTksInBvcnRyYWl0Ijp7InBhZGRpbmctdG9wIjoiMCIsImRpc3BsYXkiOiIifSwicG9ydHJhaXRfbWF4X3dpZHRoIjoxMDE4LCJwb3J0cmFpdF9taW5fd2lkdGgiOjc2OCwicGhvbmUiOnsicGFkZGluZy10b3AiOiIzMiIsImRpc3BsYXkiOiIifSwicGhvbmVfbWF4X3dpZHRoIjo3Njd9"][tdm_block_icon tdicon_id="tdc-font-fa tdc-font-fa-star-o" icon_size="eyJhbGwiOjE1MCwibGFuZHNjYXBlIjoiMTIwIiwicG9ydHJhaXQiOiIxMDAiLCJwaG9uZSI6IjEwMCJ9" icon_spacing="1" svg_code="JTNDJTNGeG1sJTIwdmVyc2lvbiUzRCUyMjEuMCUyMiUyMCUzRiUzRSUzQ3N2ZyUyMHZlcnNpb24lM0QlMjIxLjElMjIlMjB2aWV3Qm94JTNEJTIyMCUyMDAlMjA2MCUyMDYwJTIyJTIweG1sbnMlM0QlMjJodHRwJTNBJTJGJTJGd3d3LnczLm9yZyUyRjIwMDAlMkZzdmclMjIlMjB4bWxucyUzQXhsaW5rJTNEJTIyaHR0cCUzQSUyRiUyRnd3dy53My5vcmclMkYxOTk5JTJGeGxpbmslMjIlM0UlM0N0aXRsZSUyRiUzRSUzQ2Rlc2MlMkYlM0UlM0NkZWZzJTJGJTNFJTNDZyUyMGZpbGwlM0QlMjJub25lJTIyJTIwZmlsbC1ydWxlJTNEJTIyZXZlbm9kZCUyMiUyMGlkJTNEJTIyUGVvcGxlJTIyJTIwc3Ryb2tlJTNEJTIybm9uZSUyMiUyMHN0cm9rZS13aWR0aCUzRCUyMjElMjIlM0UlM0NnJTIwZmlsbCUzRCUyMiUyMzAwMDAwMCUyMiUyMGlkJTNEJTIySWNvbi0xJTIyJTNFJTNDcGF0aCUyMGQlM0QlMjJNNSUyQzE0JTIwQzQuNDQ4JTJDMTQlMjA0JTJDMTQuNDQ4JTIwNCUyQzE1JTIwTDQlMkM0OSUyMEM0JTJDNDkuNTUyJTIwNC40NDglMkM1MCUyMDUlMkM1MCUyMEM1LjU1MiUyQzUwJTIwNiUyQzQ5LjU1MiUyMDYlMkM0OSUyMEw2JTJDMTUlMjBDNiUyQzE0LjQ0OCUyMDUuNTUyJTJDMTQlMjA1JTJDMTQlMjBMNSUyQzE0JTIwWiUyME0yMiUyQzIyJTIwQzIyJTJDMjMuMTAzJTIwMjIuODk3JTJDMjQlMjAyNCUyQzI0JTIwQzI1LjEwMyUyQzI0JTIwMjYlMkMyMy4xMDMlMjAyNiUyQzIyJTIwQzI2JTJDMjAuODk3JTIwMjUuMTAzJTJDMjAlMjAyNCUyQzIwJTIwQzIyLjg5NyUyQzIwJTIwMjIlMkMyMC44OTclMjAyMiUyQzIyJTIwTDIyJTJDMjIlMjBaJTIwTTE4JTJDMzElMjBDMTglMkMzMS41NTIlMjAxOC40NDglMkMzMiUyMDE5JTJDMzIlMjBMMjUlMkMzMiUyMEMyNS4yNjUlMkMzMiUyMDI1LjUyJTJDMzEuODk1JTIwMjUuNzA3JTJDMzEuNzA3JTIwTDMyJTJDMjUuNDE0JTIwTDM0LjI5MyUyQzI3LjcwNyUyMEMzNC42ODQlMkMyOC4wOTglMjAzNS4zMTYlMkMyOC4wOTglMjAzNS43MDclMkMyNy43MDclMjBMNDAlMkMyMy40MTQlMjBMNDQuMjkzJTJDMjcuNzA3JTIwQzQ0LjY4NCUyQzI4LjA5OCUyMDQ1LjMxNiUyQzI4LjA5OCUyMDQ1LjcwNyUyQzI3LjcwNyUyMEM0Ni4wOTglMkMyNy4zMTYlMjA0Ni4wOTglMkMyNi42ODQlMjA0NS43MDclMkMyNi4yOTMlMjBMNDAuNzA3JTJDMjEuMjkzJTIwQzQwLjMxNiUyQzIwLjkwMiUyMDM5LjY4NCUyQzIwLjkwMiUyMDM5LjI5MyUyQzIxLjI5MyUyMEwzNSUyQzI1LjU4NiUyMEwzMi43MDclMkMyMy4yOTMlMjBDMzIuMzE2JTJDMjIuOTAyJTIwMzEuNjg0JTJDMjIuOTAyJTIwMzEuMjkzJTJDMjMuMjkzJTIwTDI0LjU4NiUyQzMwJTIwTDIwJTJDMzAlMjBMMjAlMkMxOCUyMEw0OCUyQzE4JTIwTDQ4JTJDMzAlMjBMMzElMkMzMCUyMEMzMC40NDglMkMzMCUyMDMwJTJDMzAuNDQ4JTIwMzAlMkMzMSUyMEMzMCUyQzMxLjU1MiUyMDMwLjQ0OCUyQzMyJTIwMzElMkMzMiUyMEw0OSUyQzMyJTIwQzQ5LjU1MiUyQzMyJTIwNTAlMkMzMS41NTIlMjA1MCUyQzMxJTIwTDUwJTJDMTclMjBDNTAlMkMxNi40NDglMjA0OS41NTIlMkMxNiUyMDQ5JTJDMTYlMjBMMTklMkMxNiUyMEMxOC40NDglMkMxNiUyMDE4JTJDMTYuNDQ4JTIwMTglMkMxNyUyMEwxOCUyQzMxJTIwWiUyME00NyUyQzQ4JTIwTDM3JTJDNDglMjBDMzYuNDQ4JTJDNDglMjAzNiUyQzQ4LjQ0OCUyMDM2JTJDNDklMjBDMzYlMkM0OS41NTIlMjAzNi40NDglMkM1MCUyMDM3JTJDNTAlMjBMNDclMkM1MCUyMEM0Ny41NTIlMkM1MCUyMDQ4JTJDNDkuNTUyJTIwNDglMkM0OSUyMEM0OCUyQzQ4LjQ0OCUyMDQ3LjU1MiUyQzQ4JTIwNDclMkM0OCUyMEw0NyUyQzQ4JTIwWiUyME0xOSUyQzUwJTIwTDI5JTJDNTAlMjBDMjkuNTUyJTJDNTAlMjAzMCUyQzQ5LjU1MiUyMDMwJTJDNDklMjBDMzAlMkM0OC40NDglMjAyOS41NTIlMkM0OCUyMDI5JTJDNDglMjBMMTklMkM0OCUyMEMxOC40NDglMkM0OCUyMDE4JTJDNDguNDQ4JTIwMTglMkM0OSUyMEMxOCUyQzQ5LjU1MiUyMDE4LjQ0OCUyQzUwJTIwMTklMkM1MCUyMEwxOSUyQzUwJTIwWiUyME00NyUyQzQyJTIwTDM3JTJDNDIlMjBDMzYuNDQ4JTJDNDIlMjAzNiUyQzQyLjQ0OCUyMDM2JTJDNDMlMjBDMzYlMkM0My41NTIlMjAzNi40NDglMkM0NCUyMDM3JTJDNDQlMjBMNDclMkM0NCUyMEM0Ny41NTIlMkM0NCUyMDQ4JTJDNDMuNTUyJTIwNDglMkM0MyUyMEM0OCUyQzQyLjQ0OCUyMDQ3LjU1MiUyQzQyJTIwNDclMkM0MiUyMEw0NyUyQzQyJTIwWiUyME0xOSUyQzQ0JTIwTDMxJTJDNDQlMjBDMzEuNTUyJTJDNDQlMjAzMiUyQzQzLjU1MiUyMDMyJTJDNDMlMjBDMzIlMkM0Mi40NDglMjAzMS41NTIlMkM0MiUyMDMxJTJDNDIlMjBMMTklMkM0MiUyMEMxOC40NDglMkM0MiUyMDE4JTJDNDIuNDQ4JTIwMTglMkM0MyUyMEMxOCUyQzQzLjU1MiUyMDE4LjQ0OCUyQzQ0JTIwMTklMkM0NCUyMEwxOSUyQzQ0JTIwWiUyME01MCUyQzM3JTIwQzUwJTJDMzYuNDQ4JTIwNDkuNTUyJTJDMzYlMjA0OSUyQzM2JTIwTDM3JTJDMzYlMjBDMzYuNDQ4JTJDMzYlMjAzNiUyQzM2LjQ0OCUyMDM2JTJDMzclMjBDMzYlMkMzNy41NTIlMjAzNi40NDglMkMzOCUyMDM3JTJDMzglMjBMNDklMkMzOCUyMEM0OS41NTIlMkMzOCUyMDUwJTJDMzcuNTUyJTIwNTAlMkMzNyUyMEw1MCUyQzM3JTIwWiUyME00OSUyQzEyJTIwQzQ5LjU1MiUyQzEyJTIwNTAlMkMxMS41NTIlMjA1MCUyQzExJTIwQzUwJTJDMTAuNDQ4JTIwNDkuNTUyJTJDMTAlMjA0OSUyQzEwJTIwTDQ1JTJDMTAlMjBDNDQuNDQ4JTJDMTAlMjA0NCUyQzEwLjQ0OCUyMDQ0JTJDMTElMjBDNDQlMkMxMS41NTIlMjA0NC40NDglMkMxMiUyMDQ1JTJDMTIlMjBMNDklMkMxMiUyMFolMjBNMTklMkMxMiUyMEwzNSUyQzEyJTIwQzM1LjU1MiUyQzEyJTIwMzYlMkMxMS41NTIlMjAzNiUyQzExJTIwQzM2JTJDMTAuNDQ4JTIwMzUuNTUyJTJDMTAlMjAzNSUyQzEwJTIwTDE5JTJDMTAlMjBDMTguNDQ4JTJDMTAlMjAxOCUyQzEwLjQ0OCUyMDE4JTJDMTElMjBDMTglMkMxMS41NTIlMjAxOC40NDglMkMxMiUyMDE5JTJDMTIlMjBMMTklMkMxMiUyMFolMjBNMTklMkMzOCUyMEwyOSUyQzM4JTIwQzI5LjU1MiUyQzM4JTIwMzAlMkMzNy41NTIlMjAzMCUyQzM3JTIwQzMwJTJDMzYuNDQ4JTIwMjkuNTUyJTJDMzYlMjAyOSUyQzM2JTIwTDE5JTJDMzYlMjBDMTguNDQ4JTJDMzYlMjAxOCUyQzM2LjQ0OCUyMDE4JTJDMzclMjBDMTglMkMzNy41NTIlMjAxOC40NDglMkMzOCUyMDE5JTJDMzglMjBMMTklMkMzOCUyMFolMjBNNjAlMkM1JTIwTDYwJTJDNTElMjBDNjAlMkM1Ni41NTElMjA1Ni41NTElMkM2MCUyMDUxJTJDNjAlMjBMOSUyQzYwJTIwQzMuNDQ5JTJDNjAlMjAwJTJDNTYuNTUxJTIwMCUyQzUxJTIwTDAlMkMxMyUyMEMwJTJDMTAuMjQzJTIwMi4yNDMlMkM4JTIwNSUyQzglMjBDNS41NTIlMkM4JTIwNiUyQzguNDQ4JTIwNiUyQzklMjBDNiUyQzkuNTUyJTIwNS41NTIlMkMxMCUyMDUlMkMxMCUyMEMzLjM0NiUyQzEwJTIwMiUyQzExLjM0NiUyMDIlMkMxMyUyMEwyJTJDNTElMjBDMiUyQzU1LjQ0OSUyMDQuNTUxJTJDNTglMjA5JTJDNTglMjBMNTElMkM1OCUyMEM1NS40NDklMkM1OCUyMDU4JTJDNTUuNDQ5JTIwNTglMkM1MSUyMEw1OCUyQzUlMjBDNTglMkMzLjM0NiUyMDU2LjY1NCUyQzIlMjA1NSUyQzIlMjBMMTMlMkMyJTIwQzExLjQwMiUyQzIlMjAxMCUyQzMuNDAyJTIwMTAlMkM1JTIwTDEwJTJDNTMlMjBDMTAlMkM1My41NTIlMjA5LjU1MiUyQzU0JTIwOSUyQzU0JTIwQzguNDQ4JTJDNTQlMjA4JTJDNTMuNTUyJTIwOCUyQzUzJTIwTDglMkM1JTIwQzglMkMyLjI5JTIwMTAuMjklMkMwJTIwMTMlMkMwJTIwTDU1JTJDMCUyMEM1Ny43NTclMkMwJTIwNjAlMkMyLjI0MyUyMDYwJTJDNSUyMEw2MCUyQzUlMjBaJTIyJTIwaWQlM0QlMjJuZXdzcGFwZXIlMjIlMkYlM0UlM0MlMkZnJTNFJTNDJTJGZyUzRSUzQyUyRnN2ZyUzRQ==" tds_icon1-color="rgba(144,156,175,0.2)" tdc_css="eyJhbGwiOnsiZGlzcGxheSI6IiJ9LCJsYW5kc2NhcGUiOnsiZGlzcGxheSI6IiJ9LCJsYW5kc2NhcGVfbWF4X3dpZHRoIjoxMTQwLCJsYW5kc2NhcGVfbWluX3dpZHRoIjoxMDE5LCJwb3J0cmFpdCI6eyJkaXNwbGF5IjoiIn0sInBvcnRyYWl0X21heF93aWR0aCI6MTAxOCwicG9ydHJhaXRfbWluX3dpZHRoIjo3Njh9"][/vc_column_inner][/vc_row_inner][/vc_column][vc_column tdc_css="eyJhbGwiOnsicGFkZGluZy10b3AiOiIyOCIsInBhZGRpbmctcmlnaHQiOiIzMCIsInBhZGRpbmctYm90dG9tIjoiMzgiLCJwYWRkaW5nLWxlZnQiOiIzMCIsImJhY2tncm91bmQtY29sb3IiOiIjZjhmOGY4IiwiZGlzcGxheSI6IiJ9LCJsYW5kc2NhcGUiOnsicGFkZGluZy10b3AiOiIyMyIsInBhZGRpbmctcmlnaHQiOiIyNSIsInBhZGRpbmctYm90dG9tIjoiMzMiLCJwYWRkaW5nLWxlZnQiOiIyNSIsImRpc3BsYXkiOiIifSwibGFuZHNjYXBlX21heF93aWR0aCI6MTE0MCwibGFuZHNjYXBlX21pbl93aWR0aCI6MTAxOSwicG9ydHJhaXQiOnsicGFkZGluZy10b3AiOiIyMyIsInBhZGRpbmctcmlnaHQiOiIyNSIsInBhZGRpbmctYm90dG9tIjoiMzMiLCJwYWRkaW5nLWxlZnQiOiIyNSIsImRpc3BsYXkiOiIifSwicG9ydHJhaXRfbWF4X3dpZHRoIjoxMDE4LCJwb3J0cmFpdF9taW5fd2lkdGgiOjc2OCwicGhvbmUiOnsicGFkZGluZy10b3AiOiIyMiIsInBhZGRpbmctcmlnaHQiOiIyMCIsInBhZGRpbmctYm90dG9tIjoiMzIiLCJwYWRkaW5nLWxlZnQiOiIyMCIsImRpc3BsYXkiOiIifSwicGhvbmVfbWF4X3dpZHRoIjo3Njd9" flex_width="eyJwb3J0cmFpdCI6IjUwJSJ9" width="1/3"][tdm_block_column_title title_size="tdm-title-sm" tds_title="tds_title2" tds_title2-f_title_font_line_height="1.2" tds_title2-f_title_font_weight="700" tds_title2-line_width="15" tds_title2-line_height="3" tds_title2-line_alignment="-100" tds_title2-line_space="15" tds_title2-line_color="eyJ0eXBlIjoiZ3JhZGllbnQiLCJjb2xvcjEiOiIjMTUyYmY3IiwiY29sb3IyIjoiIzE1MmJmNyIsIm1peGVkQ29sb3JzIjpbXSwiZGVncmVlIjoiLTkwIiwiY3NzIjoiYmFja2dyb3VuZC1jb2xvcjogIzE1MmJmNzsiLCJjc3NQYXJhbXMiOiIwZGVnLCMxNTJiZjcsIzE1MmJmNyJ9" tdc_css="eyJhbGwiOnsibWFyZ2luLWJvdHRvbSI6IjI0IiwiZGlzcGxheSI6IiJ9LCJsYW5kc2NhcGUiOnsibWFyZ2luLWJvdHRvbSI6IjIyIiwiZGlzcGxheSI6IiJ9LCJsYW5kc2NhcGVfbWF4X3dpZHRoIjoxMTQwLCJsYW5kc2NhcGVfbWluX3dpZHRoIjoxMDE5LCJwb3J0cmFpdCI6eyJtYXJnaW4tYm90dG9tIjoiMTkiLCJkaXNwbGF5IjoiIn0sInBvcnRyYWl0X21heF93aWR0aCI6MTAxOCwicG9ydHJhaXRfbWluX3dpZHRoIjo3NjgsInBob25lIjp7Im1hcmdpbi1ib3R0b20iOiIyMCIsImRpc3BsYXkiOiIifSwicGhvbmVfbWF4X3dpZHRoIjo3Njd9" tds_title2-title_color="#1d2327" tds_title2-f_title_font_size="eyJsYW5kc2NhcGUiOiIyMSIsInBvcnRyYWl0IjoiMTkiLCJwaG9uZSI6IjIxIn0=" title_text="RnJlZQ==" title_tag="h3"][tds_plans_price tdc_css="eyJhbGwiOnsibWFyZ2luLWJvdHRvbSI6IjAiLCJkaXNwbGF5IjoiIn19" f_price_font_size="eyJhbGwiOiIzNiIsImxhbmRzY2FwZSI6IjM0IiwicG9ydHJhaXQiOiIzMiIsInBob25lIjoiMzQifQ==" price_color="#1d2327" vert_align="baseline" inline="yes" free_plan="' . $plan_id_free . '" year_plan="' . $plan_id_year . '" month_plan="' . $plan_id_month . '"][tds_plans_description year_plan_desc="JTJGJTIweWVhcg==" month_plan_desc="JTJGJTIwbW9udGg=" inline="yes" tdc_css="eyJhbGwiOnsibWFyZ2luLWxlZnQiOiIxMCIsImRpc3BsYXkiOiIifSwibGFuZHNjYXBlIjp7Im1hcmdpbi1sZWZ0IjoiOSIsImRpc3BsYXkiOiIifSwibGFuZHNjYXBlX21heF93aWR0aCI6MTE0MCwibGFuZHNjYXBlX21pbl93aWR0aCI6MTAxOSwicG9ydHJhaXQiOnsibWFyZ2luLWxlZnQiOiI4IiwiZGlzcGxheSI6IiJ9LCJwb3J0cmFpdF9tYXhfd2lkdGgiOjEwMTgsInBvcnRyYWl0X21pbl93aWR0aCI6NzY4LCJwaG9uZSI6eyJtYXJnaW4tbGVmdCI6IjkiLCJkaXNwbGF5IjoiIn0sInBob25lX21heF93aWR0aCI6NzY3fQ==" color="#565f6d" f_descr_font_size="eyJhbGwiOiIxNCIsImxhbmRzY2FwZSI6IjEzIiwicG9ydHJhaXQiOiIxMiIsInBob25lIjoiMTMifQ==" f_descr_font_line_height="1" vert_align="baseline" free_plan_desc="JTJGJTIwZm9yZXZlcg=="][tds_plans_button month_plan="' . $plan_id_month . '" year_plan="' . $plan_id_year . '" tdc_css="eyJhbGwiOnsibWFyZ2luLXRvcCI6IjI4IiwibWFyZ2luLWJvdHRvbSI6IjMwIiwiZGlzcGxheSI6IiJ9LCJsYW5kc2NhcGUiOnsibWFyZ2luLXRvcCI6IjI2IiwibWFyZ2luLWJvdHRvbSI6IjI4IiwiZGlzcGxheSI6IiJ9LCJsYW5kc2NhcGVfbWF4X3dpZHRoIjoxMTQwLCJsYW5kc2NhcGVfbWluX3dpZHRoIjoxMDE5LCJwb3J0cmFpdCI6eyJtYXJnaW4tdG9wIjoiMjMiLCJtYXJnaW4tYm90dG9tIjoiMjUiLCJkaXNwbGF5IjoiIn0sInBvcnRyYWl0X21heF93aWR0aCI6MTAxOCwicG9ydHJhaXRfbWluX3dpZHRoIjo3NjgsInBob25lIjp7Im1hcmdpbi10b3AiOiIyNCIsIm1hcmdpbi1ib3R0b20iOiIyNiIsImRpc3BsYXkiOiIifSwicGhvbmVfbWF4X3dpZHRoIjo3Njd9" horiz_align="content-horiz-center" display="full" border_radius="5" f_txt_font_weight="600" padd="eyJhbGwiOiIxNHB4IDI0cHggMTZweCIsImxhbmRzY2FwZSI6IjEzcHggMjJweCAxNXB4IiwicG9ydHJhaXQiOiIxM3B4IDIycHggMTRweCIsInBob25lIjoiMTNweCAyMnB4IDE1cHgifQ==" all_border_color="#0489fc" text_color="#0489fc" bg_color="rgba(21,43,247,0)" all_border="2" bg_color_h="rgba(21,43,247,0)" free_plan="' . $plan_id_free . '" f_txt_font_size="eyJwb3J0cmFpdCI6IjEyIn0="][tdm_block_inline_text f_descr_font_size="eyJhbGwiOiIxNSIsImxhbmRzY2FwZSI6IjE0IiwicG9ydHJhaXQiOiIxMyIsInBob25lIjoiMTQifQ==" description_color="rgba(85,93,102,0.7)" tdc_css="eyJhbGwiOnsibWFyZ2luLWJvdHRvbSI6IjEyIiwiZGlzcGxheSI6IiJ9LCJsYW5kc2NhcGUiOnsibWFyZ2luLWJvdHRvbSI6IjEwIiwiZGlzcGxheSI6IiJ9LCJsYW5kc2NhcGVfbWF4X3dpZHRoIjoxMTQwLCJsYW5kc2NhcGVfbWluX3dpZHRoIjoxMDE5LCJwb3J0cmFpdCI6eyJtYXJnaW4tYm90dG9tIjoiNyIsImRpc3BsYXkiOiIifSwicG9ydHJhaXRfbWF4X3dpZHRoIjoxMDE4LCJwb3J0cmFpdF9taW5fd2lkdGgiOjc2OCwicGhvbmUiOnsibWFyZ2luLWJvdHRvbSI6IjkiLCJkaXNwbGF5IjoiIn0sInBob25lX21heF93aWR0aCI6NzY3fQ==" description="V2hhdCUyMHlvdSdsbCUyMGdldA=="][tdm_block_list content_align_horizontal="content-horiz-left" icon_color="#152bf7" text_color="#444444" f_list_font_weight="600" f_list_font_size="eyJhbGwiOiIxNSIsImxhbmRzY2FwZSI6IjE0IiwicG9ydHJhaXQiOiIxMyIsInBob25lIjoiMTQifQ==" f_list_font_line_height="eyJhbGwiOiIxLjUiLCJwb3J0cmFpdCI6IjEuMzUiLCJwaG9uZSI6IjEuNDUiLCJsYW5kc2NhcGUiOiIxLjQ1In0=" tdc_css="eyJhbGwiOnsibWFyZ2luLWJvdHRvbSI6IjAiLCJkaXNwbGF5IjoiIn19" icon_size="eyJsYW5kc2NhcGUiOiIxNCJ9" items="RXRpYW0lMjBlc3QlMjBuaWJoJTJDJTIwbG9ib3J0aXMlMjBzaXQlMEFQcmFlc2VudCUyMGV1aXNtb2QlMjBhYyUwQVV0JTIwbW9sbGlzJTIwcGVsbGVudGVzcXVlJTIwdG9ydG9yJTBBTnVsbGFtJTIwZXUlMjBlcmF0JTIwY29uZGltZW50dW0=" tdicon="tdc-font-fa tdc-font-fa-check-circle"][tdm_block_list items="RG9uZWMlMjBxdWlzJTIwZXN0JTIwYWMlMjBmZWxpcyUwQU9yY2klMjB2YXJpdXMlMjBuYXRvcXVlJTIwZG9sb3I=" tdicon="tdc-font-fa tdc-font-fa-minus-circle" content_align_horizontal="content-horiz-left" icon_color="#909caf" text_color="#909caf" f_list_font_weight="600" f_list_font_size="eyJhbGwiOiIxNSIsImxhbmRzY2FwZSI6IjE0IiwicG9ydHJhaXQiOiIxMyIsInBob25lIjoiMTQifQ==" f_list_font_line_height="eyJhbGwiOiIxLjUiLCJsYW5kc2NhcGUiOiIxLjQ1IiwicG9ydHJhaXQiOiIxLjM1IiwicGhvbmUiOiIxLjQ1In0=" tdc_css="eyJhbGwiOnsibWFyZ2luLWJvdHRvbSI6IjAiLCJkaXNwbGF5IjoiIn19"][/vc_column][vc_column width="1/3" tdc_css="eyJhbGwiOnsicGFkZGluZy10b3AiOiIyOCIsInBhZGRpbmctcmlnaHQiOiIzMCIsInBhZGRpbmctYm90dG9tIjoiMzgiLCJwYWRkaW5nLWxlZnQiOiIzMCIsImJhY2tncm91bmQtY29sb3IiOiIjZTVmM2ZmIiwiZGlzcGxheSI6IiJ9LCJsYW5kc2NhcGUiOnsicGFkZGluZy10b3AiOiIyMyIsInBhZGRpbmctcmlnaHQiOiIyNSIsInBhZGRpbmctYm90dG9tIjoiMzMiLCJwYWRkaW5nLWxlZnQiOiIyNSIsImRpc3BsYXkiOiIifSwibGFuZHNjYXBlX21heF93aWR0aCI6MTE0MCwibGFuZHNjYXBlX21pbl93aWR0aCI6MTAxOSwicG9ydHJhaXQiOnsicGFkZGluZy10b3AiOiIyMyIsInBhZGRpbmctcmlnaHQiOiIyNSIsInBhZGRpbmctYm90dG9tIjoiMzMiLCJwYWRkaW5nLWxlZnQiOiIyNSIsImRpc3BsYXkiOiIifSwicG9ydHJhaXRfbWF4X3dpZHRoIjoxMDE4LCJwb3J0cmFpdF9taW5fd2lkdGgiOjc2OCwicGhvbmUiOnsicGFkZGluZy10b3AiOiIyMiIsInBhZGRpbmctcmlnaHQiOiIyMCIsInBhZGRpbmctYm90dG9tIjoiMzIiLCJwYWRkaW5nLWxlZnQiOiIyMCIsImRpc3BsYXkiOiIifSwicGhvbmVfbWF4X3dpZHRoIjo3Njd9" flex_width="eyJwb3J0cmFpdCI6IjUwJSJ9"][tdm_block_column_title title_text="UHJv" title_tag="h3" title_size="tdm-title-sm" tds_title1-f_title_font_weight="700" tds_title1-f_title_font_line_height="1.2" tds_title="tds_title2" tds_title2-f_title_font_line_height="1.2" tds_title2-f_title_font_weight="700" tds_title2-line_width="15" tds_title2-line_height="3" tds_title2-line_alignment="-100" tds_title2-line_space="15" tds_title2-line_color="eyJ0eXBlIjoiZ3JhZGllbnQiLCJjb2xvcjEiOiIjMTUyYmY3IiwiY29sb3IyIjoiIzE1MmJmNyIsIm1peGVkQ29sb3JzIjpbXSwiZGVncmVlIjoiLTkwIiwiY3NzIjoiYmFja2dyb3VuZC1jb2xvcjogIzE1MmJmNzsiLCJjc3NQYXJhbXMiOiIwZGVnLCMxNTJiZjcsIzE1MmJmNyJ9" tdc_css="eyJhbGwiOnsibWFyZ2luLWJvdHRvbSI6IjI0IiwiZGlzcGxheSI6IiJ9LCJsYW5kc2NhcGUiOnsibWFyZ2luLWJvdHRvbSI6IjIyIiwiZGlzcGxheSI6IiJ9LCJsYW5kc2NhcGVfbWF4X3dpZHRoIjoxMTQwLCJsYW5kc2NhcGVfbWluX3dpZHRoIjoxMDE5LCJwb3J0cmFpdCI6eyJtYXJnaW4tYm90dG9tIjoiMTkiLCJkaXNwbGF5IjoiIn0sInBvcnRyYWl0X21heF93aWR0aCI6MTAxOCwicG9ydHJhaXRfbWluX3dpZHRoIjo3NjgsInBob25lIjp7Im1hcmdpbi1ib3R0b20iOiIyMCIsImRpc3BsYXkiOiIifSwicGhvbmVfbWF4X3dpZHRoIjo3Njd9" tds_title2-title_color="#1d2327" tds_title2-f_title_font_size="eyJsYW5kc2NhcGUiOiIyMSIsInBvcnRyYWl0IjoiMTkiLCJwaG9uZSI6IjIxIn0="][tds_plans_price month_plan="' . $plan_id_month . '" year_plan="' . $plan_id_year . '" tdc_css="eyJhbGwiOnsibWFyZ2luLWJvdHRvbSI6IjAiLCJkaXNwbGF5IjoiIn19" f_price_font_size="eyJhbGwiOiIzNiIsImxhbmRzY2FwZSI6IjM0IiwicG9ydHJhaXQiOiIzMiIsInBob25lIjoiMzQifQ==" curr_txt="$" price_color="#1d2327" vert_align="baseline" inline="yes" def_plan="" free_plan=""][tds_plans_description year_plan_desc="JTJGJTIweWVhcg==" month_plan_desc="JTJGJTIwbW9udGg=" inline="yes" tdc_css="eyJhbGwiOnsibWFyZ2luLWxlZnQiOiIxMCIsImRpc3BsYXkiOiIifSwibGFuZHNjYXBlIjp7Im1hcmdpbi1sZWZ0IjoiOSIsImRpc3BsYXkiOiIifSwibGFuZHNjYXBlX21heF93aWR0aCI6MTE0MCwibGFuZHNjYXBlX21pbl93aWR0aCI6MTAxOSwicG9ydHJhaXQiOnsibWFyZ2luLWxlZnQiOiI4IiwiZGlzcGxheSI6IiJ9LCJwb3J0cmFpdF9tYXhfd2lkdGgiOjEwMTgsInBvcnRyYWl0X21pbl93aWR0aCI6NzY4LCJwaG9uZSI6eyJtYXJnaW4tbGVmdCI6IjkiLCJkaXNwbGF5IjoiIn0sInBob25lX21heF93aWR0aCI6NzY3fQ==" color="#565f6d" f_descr_font_size="eyJhbGwiOiIxNCIsImxhbmRzY2FwZSI6IjEzIiwicG9ydHJhaXQiOiIxMiIsInBob25lIjoiMTMifQ==" f_descr_font_line_height="1" vert_align="baseline"][tds_plans_button month_plan="' . $plan_id_month . '" year_plan="' . $plan_id_year . '" tdc_css="eyJhbGwiOnsibWFyZ2luLXRvcCI6IjI4IiwibWFyZ2luLWJvdHRvbSI6IjMwIiwiZGlzcGxheSI6IiJ9LCJsYW5kc2NhcGUiOnsibWFyZ2luLXRvcCI6IjI2IiwibWFyZ2luLWJvdHRvbSI6IjI4IiwiZGlzcGxheSI6IiJ9LCJsYW5kc2NhcGVfbWF4X3dpZHRoIjoxMTQwLCJsYW5kc2NhcGVfbWluX3dpZHRoIjoxMDE5LCJwb3J0cmFpdCI6eyJtYXJnaW4tdG9wIjoiMjMiLCJtYXJnaW4tYm90dG9tIjoiMjUiLCJkaXNwbGF5IjoiIn0sInBvcnRyYWl0X21heF93aWR0aCI6MTAxOCwicG9ydHJhaXRfbWluX3dpZHRoIjo3NjgsInBob25lIjp7Im1hcmdpbi10b3AiOiIyNCIsIm1hcmdpbi1ib3R0b20iOiIyNiIsImRpc3BsYXkiOiIifSwicGhvbmVfbWF4X3dpZHRoIjo3Njd9" horiz_align="content-horiz-center" display="eyJsYW5kc2NhcGUiOiJmdWxsIiwiYWxsIjoiZnVsbCJ9" bg_color="#0489fc" border_radius="5" f_txt_font_weight="600" padd="eyJhbGwiOiIxNnB4IDI0cHggMThweCIsImxhbmRzY2FwZSI6IjE1cHggMjJweCAxN3B4IiwicG9ydHJhaXQiOiIxNXB4IDIycHggMTZweCIsInBob25lIjoiMTVweCAyMnB4IDE3cHgifQ==" bg_color_h="eyJ0eXBlIjoiZ3JhZGllbnQiLCJjb2xvcjEiOiIjMTUyYmY3IiwiY29sb3IyIjoiIzE1MmJmNyIsIm1peGVkQ29sb3JzIjpbXSwiZGVncmVlIjoiLTkwIiwiY3NzIjoiYmFja2dyb3VuZC1jb2xvcjogIzE1MmJmNzsiLCJjc3NQYXJhbXMiOiIwZGVnLCMxNTJiZjcsIzE1MmJmNyJ9" free_plan="" f_txt_font_size="eyJwb3J0cmFpdCI6IjEyIn0="][tdm_block_inline_text description="V2hhdCUyMHlvdSdsbCUyMGdldA==" f_descr_font_size="eyJhbGwiOiIxNSIsImxhbmRzY2FwZSI6IjE0IiwicG9ydHJhaXQiOiIxMyIsInBob25lIjoiMTQifQ==" description_color="rgba(85,93,102,0.7)" tdc_css="eyJhbGwiOnsibWFyZ2luLWJvdHRvbSI6IjEyIiwiZGlzcGxheSI6IiJ9LCJsYW5kc2NhcGUiOnsibWFyZ2luLWJvdHRvbSI6IjEwIiwiZGlzcGxheSI6IiJ9LCJsYW5kc2NhcGVfbWF4X3dpZHRoIjoxMTQwLCJsYW5kc2NhcGVfbWluX3dpZHRoIjoxMDE5LCJwb3J0cmFpdCI6eyJtYXJnaW4tYm90dG9tIjoiNyIsImRpc3BsYXkiOiIifSwicG9ydHJhaXRfbWF4X3dpZHRoIjoxMDE4LCJwb3J0cmFpdF9taW5fd2lkdGgiOjc2OCwicGhvbmUiOnsibWFyZ2luLWJvdHRvbSI6IjkiLCJkaXNwbGF5IjoiIn0sInBob25lX21heF93aWR0aCI6NzY3fQ=="][tdm_block_list items="RXRpYW0lMjBlc3QlMjBuaWJoJTJDJTIwbG9ib3J0aXMlMjBzaXQlMEFQcmFlc2VudCUyMGV1aXNtb2QlMjBhYyUwQVV0JTIwbW9sbGlzJTIwcGVsbGVudGVzcXVlJTIwdG9ydG9yJTBBTnVsbGFtJTIwZXUlMjBlcmF0JTIwY29uZGltZW50dW0lMEFEb25lYyUyMHF1aXMlMjBlc3QlMjBhYyUyMGZlbGlzJTBBT3JjaSUyMHZhcml1cyUyMG5hdG9xdWUlMjBkb2xvcg==" tdicon="tdc-font-fa tdc-font-fa-check-circle" content_align_horizontal="content-horiz-left" icon_color="#152bf7" text_color="#444444" f_list_font_weight="600" f_list_font_size="eyJhbGwiOiIxNSIsImxhbmRzY2FwZSI6IjE0IiwicG9ydHJhaXQiOiIxMyIsInBob25lIjoiMTQifQ==" f_list_font_line_height="eyJhbGwiOiIxLjUiLCJwb3J0cmFpdCI6IjEuMzUiLCJwaG9uZSI6IjEuNDUiLCJsYW5kc2NhcGUiOiIxLjQ1In0=" tdc_css="eyJhbGwiOnsibWFyZ2luLWJvdHRvbSI6IjAiLCJkaXNwbGF5IjoiIn19" icon_size="eyJsYW5kc2NhcGUiOiIxNCJ9"][tds_plans_switcher tds_plans_switcher1-annual_txt="Yearly pricing" tds_plans_switcher1-monthly_txt="Monthly pricing" def_plan="" tdc_css="eyJhbGwiOnsibWFyZ2luLXRvcCI6IjM1IiwibWFyZ2luLWJvdHRvbSI6IjAiLCJib3JkZXItdG9wLXdpZHRoIjoiMiIsInBhZGRpbmctdG9wIjoiMzAiLCJib3JkZXItc3R5bGUiOiJkYXNoZWQiLCJib3JkZXItY29sb3IiOiJyZ2JhKDIxLDQzLDI0NywwLjEpIiwiZGlzcGxheSI6IiJ9LCJsYW5kc2NhcGUiOnsibWFyZ2luLXRvcCI6IjMzIiwicGFkZGluZy10b3AiOiIyOCIsImRpc3BsYXkiOiIifSwibGFuZHNjYXBlX21heF93aWR0aCI6MTE0MCwibGFuZHNjYXBlX21pbl93aWR0aCI6MTAxOSwicG9ydHJhaXQiOnsibWFyZ2luLXRvcCI6IjMwIiwicGFkZGluZy10b3AiOiIyNSIsImRpc3BsYXkiOiIifSwicG9ydHJhaXRfbWF4X3dpZHRoIjoxMDE4LCJwb3J0cmFpdF9taW5fd2lkdGgiOjc2OCwicGhvbmUiOnsibWFyZ2luLXRvcCI6IjMxIiwicGFkZGluZy10b3AiOiIyNiIsImRpc3BsYXkiOiIifSwicGhvbmVfbWF4X3dpZHRoIjo3Njd9" tds_plans_switcher1-horiz_align="content-horiz-left" tds_plans_switcher="tds_plans_switcher1" tds_plans_switcher1-all_border="1" tds_plans_switcher1-bg_color="rgba(255,255,255,0)" tds_plans_switcher1-all_border_color="#152bf7" tds_plans_switcher1-dot_bg_color="eyJ0eXBlIjoiZ3JhZGllbnQiLCJjb2xvcjEiOiIjMTUyYmY3IiwiY29sb3IyIjoiIzE1MmJmNyIsIm1peGVkQ29sb3JzIjpbXSwiZGVncmVlIjoiLTkwIiwiY3NzIjoiYmFja2dyb3VuZC1jb2xvcjogIzE1MmJmNzsiLCJjc3NQYXJhbXMiOiIwZGVnLCMxNTJiZjcsIzE1MmJmNyJ9" tds_plans_switcher1-f_label_font_size="eyJhbGwiOiIxMyIsInBvcnRyYWl0IjoiMTIifQ==" tds_plans_switcher1-switch_size="1" tds_plans_switcher1-label_color="#909caf" tds_plans_switcher1-label_color_a="#565f6d" tds_plans_switcher1-label_space="10"][/vc_column][/vc_row][/tdc_zone]';
                }

				$inserted_page_id = wp_insert_post([
					'post_type' => 'page',
					'post_title' => 'Switching plans wizard',
					'post_name' => 'tds-switching-plans-wizard',
					'post_status' => 'publish',
					'post_content' => $post_content
                ]);

				if (!empty($inserted_page_id) && !is_wp_error($inserted_page_id)) {

                    update_post_meta( $insert_result, 'tds_locker_settings', [
                        'tds_title' => 'This Content Is Only For Subscribers',
                        'tds_message' => 'Please subscribe to unlock this content.',
                        'tds_input_placeholder' => '',
                        'tds_submit_btn_text' => 'Subscribe to unlock',
                        'tds_after_btn_text' => '',
                        'tds_pp_msg' => 'I consent to processing of my data according to <a href="#">Terms of Use</a> & <a href="#">Privacy Policy</a>'
                    ] );

					update_post_meta( $insert_result, 'tds_locker_types', [
						'tds_payable'            => 'paid_subscription',
						'tds_paid_subs_plan_ids' => [ $plan_id_free, $plan_id_month, $plan_id_year ],
						'tds_paid_subs_page_id' => $inserted_page_id
					] );

					$result[ 'inserted_id' ] = $insert_result;
					$last_post_permalink     = '';
					$last_post               = get_posts( [ 'numberposts' => 1, 'order' => 'ASC' ] );
					if ( ! empty( $last_post ) && is_array( $last_post ) ) {
						$last_post_permalink = get_permalink( $last_post[ 0 ] );

						$td_post_settings = td_util::get_post_meta_array( $last_post[ 0 ]->ID, 'td_post_theme_settings' );

						$td_post_settings[ 'tds_lock_content' ] = 1;
						$td_post_settings[ 'tds_locker' ]       = $insert_result;

						update_post_meta( $last_post[ 0 ]->ID, 'td_post_theme_settings', $td_post_settings );
					}
					$result[ 'last_post_permalink' ] = $last_post_permalink;
				}
			}
		}

		return $result;
	}

	static function create_wizard_pages() {
		$result = [];

		$items = tds_util::get_wizard_pages();

		$options = [];
		$result['pages'] = [];

		$db_options = tds_ajax::get_all_options();

		foreach ( $items as $name => $item ) {

			$create_page = true;
			if (!empty($db_options['options'])) {
				foreach ($db_options['options'] as $option) {
					if ( !empty($option['name']) && $item['db_id'] === $option['name'] && false !== get_permalink($option['value'])) {
						$create_page = false;
						break;
					}
				}
			}
			if (!$create_page) {
				continue;
			}

			$inserted_page_id = wp_insert_post($item['data']);

			if ( empty( $inserted_page_id ) ) {
				$result[ 'error' ][] = 'Page could not be created';
				break;
			} else if ( is_wp_error( $inserted_page_id ) ) {
				$result[ 'error' ][] = $inserted_page_id->get_error_message();
				break;
			} else {

				$permalink = esc_url(get_permalink($inserted_page_id));
				$result['pages'][] = ['name' => $name, 'id' => $inserted_page_id, 'permalink' => $permalink, 'title' => $item['data']['post_title']];
				$options[] = ['name' => $item['db_id'], 'value' => $inserted_page_id];
			}
		}

		if (!empty($options)) {
			self::set_db_options($options);
		}

		return $result;
	}

	static function create_general_settings() {
		self::set_db_options([
			['name' => 'curr_name', 'value' => 'USD'],
			['name' => 'curr_pos', 'value' => 'left_space'],
			['name' => 'curr_th_sep', 'value' => ','],
			['name' => 'curr_dec_sep', 'value' => '.'],
			['name' => 'curr_dec_no', 'value' => '0'],
		], true);
	}

	private static function set_options(WP_REST_Request $request) {
		$result = [];
        $options = $request->get_param('options');

        if (empty($options)) {
			$result[ 'error' ][] = 'Invalid options';
		}
		if (!empty($result)) {
			// return error
		} else {
			$result = self::set_db_options($options);
		}

		return $result;
	}

	private static function set_db_options($options, $soft_update = false) {
		$result = [];
		global $wpdb;

		foreach ($options as $option) {

			if (empty($option['name']) || !isset($option['value'])) {
				continue;
			}

			$get_result = $wpdb->get_var( $wpdb->prepare( "SELECT count(*) FROM tds_options WHERE name = %s", $option['name'] ) );
			if ( false !== $get_result ) {

				if (empty($option['value'])) {
					switch ($option['name']) {
						case 'curr_name': $option['value'] = 'USD'; break;
						case 'curr_pos': $option['value'] = 'left_space'; break;
						case 'curr_th_sep': $option['value'] = ','; break;
						case 'curr_dec_sep': $option['value'] = '.'; break;
						case 'curr_dec_no': $option['value'] = '0'; break;
					}
				}

				if ( '0' === $get_result || 0 === $get_result ) {

					$insert_result = $wpdb->insert( 'tds_options',
						array(
							'name'  => $option['name'],
							'value' => $option['value'],
						),
						array( '%s', '%s' ) );

					if ( false !== $insert_result ) {
						$result[ 'inserted_id' ] = $wpdb->insert_id;
					}
				} else if (! $soft_update ) {
					$update_result = $wpdb->update( 'tds_options',
						array(
							'name'  => $option['name'],
							'value' => $option['value'],
						),
						array( 'name' => $option['name'] ),
						array( '%s', '%s' ),
						array( '%s' )
					);

					if ( false !== $update_result ) {
						$result[ 'success' ] = true;
					}
				}
			}
		}
		return $result;
	}

	private static function get_page_info(WP_REST_Request $request) {
		$result = [];
        $page_ids = $request->get_param('page_ids');

        if (empty($page_ids)) {
			$result[ 'error' ][] = 'Invalid page ids';
		}
		if (!empty($result)) {
			// return error
		} else {
			$temp_pages = [];

			foreach ($page_ids as $page_id) {

				$page = get_post($page_id);

				if (!is_null($page)) {

					$temp_pages[] = [ 'id'        => $page_id,
					                  'title'     => $page->post_title,
					                  'permalink' => esc_url( get_permalink( $page_id ) )
					];
				}
			}

			$result['pages'] = $temp_pages;
		}

		return $result;
	}

	private static function generate_default_pages(WP_REST_Request $request) {
		global $wpdb;

		$result  = [];

		$check = $request->get_param('check');
		if (!empty($check) && '1' === $check) {
			$result = tds_util::get_subscriptions();
		} else {

			$results = $wpdb->get_results( "SELECT 
					tds_subscriptions.*, 
					tds_plans.name AS 'plan_name' 
				FROM 
					tds_subscriptions 
					INNER JOIN tds_plans
					ON tds_subscriptions.plan_id = tds_plans.id ORDER BY tds_subscriptions.ID desc", ARRAY_A );

			if ( null !== $results ) {
				foreach ( $results as &$item ) {
					if ( ! empty( $item[ 'user_id' ] ) ) {
						$item[ 'user_name' ] = get_user_meta( $item[ 'user_id' ], 'nickname', true );
						$item[ 'end_date' ]  = tds_util::get_end_date( $item[ 'start_date' ], $item[ 'last_months_in_cycle' ], $item[ 'start_day' ] )->format( 'Y-m-d' );
					}
				}
				$result[ 'subscriptions' ] = $results;
			}
		}
		return $result;
	}

	private static function get_list_pages(WP_REST_Request $request) {
		$result = [];
        $title = $request->get_param('title');

        $args = [
        	'post_type' => 'page',
	        'numberposts' => 30, // should be enough
	        'suppress_filters' => false,
        ];

        if (!empty($title)) {

        	global $keyword;
        	$keyword = $title;

        	add_filter( 'posts_where', 'tds_filter_list_pages');
        	function tds_filter_list_pages( $where ) {
				global $keyword;

			    global $wpdb;
			    $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( like_escape( $keyword ) ) . '%\'';

			    return $where;
			}
        }

        $result['pages'] = [];
        $pages = get_posts($args);
        remove_filter( 'posts_where', 'tds_filter_list_pages' );
        unset($keyword);

        foreach ($pages as $page) {
        	$result['pages'][] = ['id' => $page->ID, 'title' => $page->post_title, 'permalink' => esc_url(get_permalink($page->ID))];
        }

		return $result;
	}

	private static function get_dashboard_permalinks(WP_REST_Request $request) {
		$result = [];
        $dashboard_id = $request->get_param('dashboard_page_id');
        $args = $request->get_param('args');

        if (empty($dashboard_id)) {
			$result[ 'error' ][] = 'Invalid dashboard page id';
		}
		if (!empty($result)) {
			// return error
		} else {

			$dashboard = get_post($dashboard_id);
			if ($dashboard instanceof WP_Post) {
				$dashboard_permalink = get_permalink($dashboard_id);
				$result['dashboard'] = ['id' => $dashboard_id, 'title' => $dashboard->post_title, 'permalink' => esc_url($dashboard_permalink)];

				if (!empty($args)) {
					foreach ($args as $arg) {
						$result[$arg] = esc_url(add_query_arg($arg, '', $dashboard_permalink));
					}
				}
			}
		}

		return $result;
	}
}
