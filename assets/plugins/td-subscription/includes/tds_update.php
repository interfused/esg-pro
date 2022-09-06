<?php

class tds_update {

	private static $versions = ['1.2', '1.3'];

	static function update_settings($to_version) {

		if (empty($to_version)) {
			return;
		}

		$versions = self::get_upper_versions($to_version);

		foreach ($versions as $version) {
		    $method_name = '_to_' . str_replace('.', 'p', $version );
		    if (method_exists(__CLASS__, $method_name)) {
		        call_user_func( array( __CLASS__, $method_name ) );
            }
		}
	}

	static function get_upper_versions($current_version) {
		$upper_versions = [];
		foreach (self::$versions as $version) {
			if (1 === version_compare($version, $current_version)) {
				$upper_versions[] = $version;
			}
		}

		return $upper_versions;
	}

	static function _to_1p2 () {
		global $wpdb;

		try {

			$wpdb->query( "ALTER TABLE `tds_subscriptions` MODIFY paypal_order_info TEXT;" );
			if ( '' !== $wpdb->last_error ) {
				throw new Exception($wpdb->print_error());
			}

			$wpdb->query( "ALTER TABLE `tds_subscriptions` ADD `curr_dec_no` VARCHAR(30) DEFAULT NULL AFTER `price`" );
			if ( '' !== $wpdb->last_error ) {
				throw new Exception($wpdb->print_error());
			}

			$wpdb->query( "ALTER TABLE `tds_subscriptions` ADD `curr_dec_sep` VARCHAR(30) NULL DEFAULT NULL AFTER `price`" );
			if ( '' !== $wpdb->last_error ) {
				throw new Exception($wpdb->print_error());
			}

			$wpdb->query( "ALTER TABLE `tds_subscriptions` ADD `curr_th_sep` VARCHAR(30) NULL DEFAULT NULL AFTER `price`" );
			if ( '' !== $wpdb->last_error ) {
				throw new Exception($wpdb->print_error());
			}

			$wpdb->query( "ALTER TABLE `tds_subscriptions` ADD `curr_pos` VARCHAR(30) NULL DEFAULT NULL AFTER `price`" );
			if ( '' !== $wpdb->last_error ) {
				throw new Exception($wpdb->print_error());
			}

			$wpdb->query( "ALTER TABLE `tds_subscriptions` ADD `curr_name` VARCHAR(50) NULL DEFAULT NULL AFTER `price`" );
			if ( '' !== $wpdb->last_error ) {
				throw new Exception($wpdb->print_error());
			}

			$wpdb->query( "ALTER TABLE `tds_subscriptions` ADD `next_price` VARCHAR(50) DEFAULT NULL AFTER `price`" );
			if ( '' !== $wpdb->last_error ) {
				throw new Exception($wpdb->print_error());
			}

		} catch (Exception $ex) {
			// $ex
			return;
		}

		$wpdb->query( 'SET autocommit = 0;' );

		$wpdb->query('START TRANSACTION;');

		$wpdb->query("LOCK TABLES tds_subscriptions WRITE, tds_options WRITE;");

		try {

			$wpdb->query( "UPDATE `tds_subscriptions` SET `curr_name` = 'USD' WHERE `curr_name` IS NULL" );
			if ( '' !== $wpdb->last_error ) {
				throw new Exception($wpdb->print_error());
			}

			$wpdb->query( "UPDATE `tds_subscriptions` SET `curr_pos` = 'left_space' WHERE `curr_pos` IS NULL" );
			if ( '' !== $wpdb->last_error ) {
				throw new Exception($wpdb->print_error());
			}

			$wpdb->query( "UPDATE `tds_subscriptions` SET `curr_th_sep` = ',' WHERE `curr_th_sep` IS NULL" );
			if ( '' !== $wpdb->last_error ) {
				throw new Exception($wpdb->print_error());
			}

			$wpdb->query( "UPDATE `tds_subscriptions` SET `curr_dec_sep` = '.' WHERE `curr_dec_sep` IS NULL" );
			if ( '' !== $wpdb->last_error ) {
				throw new Exception($wpdb->print_error());
			}

			$wpdb->query( "UPDATE `tds_subscriptions` SET `curr_dec_no` = '0' WHERE `curr_dec_no` IS NULL" );
			if ( '' !== $wpdb->last_error ) {
				throw new Exception($wpdb->print_error());
			}

			foreach ( [ 'curr_name'    => 'USD',
			            'curr_pos'     => 'left',
			            'curr_th_sep'   => '.',
			            'curr_dec_sep' => ',',
			            'curr_dec_no'  => '0'
			] as $key => $val
			) {
				$wpdb->insert( 'tds_options',
					array(
						'name'  => $key,
						'value' => $val
					),
					array( '%s', '%s' ) );

				if ( '' !== $wpdb->last_error ) {
					throw new Exception($wpdb->print_error());
				}
			}

			tds_util::set_tds_option('version', '1.2');
			$wpdb->query('COMMIT');

		} catch (Exception $ex) {
			// $ex
			$wpdb->query( 'ROLLBACK' );
		} finally {
			$wpdb->query('UNLOCK TABLES');
			$wpdb->query( 'SET autocommit = 1;' );
		}
	}


	static function _to_1p3 () {
		global $wpdb;

		try {

			$wpdb->query( "ALTER TABLE `tds_subscriptions` ADD `stripe_payment_info` TEXT DEFAULT NULL AFTER `paypal_order_capture_update_time`" );
			if ( '' !== $wpdb->last_error ) {
				throw new Exception($wpdb->print_error());
			}

			$wpdb->query( "ALTER TABLE `tds_subscriptions` ADD `stripe_payment_status` VARCHAR(40) DEFAULT NULL AFTER `paypal_order_capture_update_time`" );
			if ( '' !== $wpdb->last_error ) {
				throw new Exception($wpdb->print_error());
			}

			$wpdb->query( "ALTER TABLE `tds_subscriptions` ADD `stripe_payment_intent` VARCHAR(40) DEFAULT NULL AFTER `paypal_order_capture_update_time`" );
			if ( '' !== $wpdb->last_error ) {
				throw new Exception($wpdb->print_error());
			}

			tds_util::set_tds_option('version', '1.3');

		} catch (Exception $ex) {
			// $ex
			return;
		}
	}
}
