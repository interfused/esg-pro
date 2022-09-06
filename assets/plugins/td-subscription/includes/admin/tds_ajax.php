<?php

/**
 * Returns a list of the lockers.
 */

// @todo maybe removed > not needed anymore ..
function tds_ajax_get_lockers() {

	$lockers = get_posts( array(
		'post_type' => 'tds_locker',
		'numberposts' => -1
	));

	$shortcode = isset( $_REQUEST['shortcode'] ) ? $_REQUEST['shortcode'] : false;

	// the default locker id
	$default_locker_id = (int) get_option( 'tds_default_locker_id' );

	$result = array();
	foreach ( $lockers as $locker ) {
		$result[] = array(
			'id' => $locker->ID,
			'title' => empty( $locker->post_title ) ? '(no titled, ID=' . $locker->ID . ')' : $locker->post_title,
			'isDefault' => ( $locker->ID === $default_locker_id )
		);
	}

	echo json_encode($result);
	die();
}

add_action( 'wp_ajax_get_tds_lockers', 'tds_ajax_get_lockers' );
