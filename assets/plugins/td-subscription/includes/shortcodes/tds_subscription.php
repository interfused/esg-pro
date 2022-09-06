<?php

/**
 * Class tds_subscription
 */

class tds_subscription extends td_block {

    private static $direct_payment_details = [];

	public function get_custom_css() {

        // $unique_block_class
        $unique_block_class = $this->block_uid;

		$compiled_css = '';

		/** @noinspection CssInvalidAtRule */
		$raw_css =
            "<style>

                /* @tds_subscription */
                body .tds_subscription {
                    margin-bottom: 0;                    
                }
                

            </style>";

		$td_css_res_compiler = new td_css_res_compiler( $raw_css );
		$td_css_res_compiler->load_settings( __CLASS__ . '::cssMedia', $this->get_all_atts() );

		$compiled_css .= $td_css_res_compiler->compile_css();

		return $compiled_css;

	}

	static function cssMedia( $res_ctx ) {

        $res_ctx->load_settings_raw( 'tds_subscription', 1 );
	}

	function __construct() {
		parent::disable_loop_block_features();
	}

	private function get_direct_payment_details() {

	    if (!empty(self::$direct_payment_details)) {
	        return self::$direct_payment_details;
        }

        global $wpdb;
        $payment_bank = $wpdb->get_results("SELECT * FROM tds_payment_bank LIMIT 1", ARRAY_A);

        if ( null !== $payment_bank && count($payment_bank) && 1 == $payment_bank[0]['is_active']) {
            self::$direct_payment_details['direct_payment_bank'] = $payment_bank[0]['bank_name'];
            self::$direct_payment_details['direct_payment_account_name'] = $payment_bank[0]['account_name'];
            self::$direct_payment_details['direct_payment_account_number'] = $payment_bank[0]['account_number'];
            self::$direct_payment_details['direct_payment_routing_number'] = $payment_bank[0]['routing_number'];
            self::$direct_payment_details['direct_payment_iban'] = $payment_bank[0]['iban'];
            self::$direct_payment_details['direct_payment_bic_swift'] = $payment_bank[0]['bic_swift'];
            self::$direct_payment_details['direct_payment_instruction'] = $payment_bank[0]['instruction'];
        }
        return self::$direct_payment_details;
    }

	function render( $atts, $content = null ) {

        parent::render( $atts );


        // flag to check if we are in composer
        $is_composer = false;
        if( td_util::tdc_is_live_editor_iframe() || td_util::tdc_is_live_editor_ajax() ) {
            $is_composer = true;
        }

        $dummy_subscriptions_data = array(
            'subscriptions' => array(
                array(
                    'id' => '1',
                    'plan_id' => '1',
                    'user_id' => '1',
                    'ref_id' => NULL,
                    'billing_first_name' => 'John',
                    'billing_last_name' => 'Doe',
                    'billing_company_name' => 'Demo company name',
                    'billing_cui' => NULL,
                    'billing_j' => NULL,
                    'billing_address' => 'Cross Roads 44',
                    'billing_county' => 'New York',
                    'billing_city' => 'New York',
                    'billing_country' => 'United States',
                    'billing_phone' => '+30 789546548',
                    'billing_email' => 'mail@example.com',
                    'billing_bank_account' => NULL,
                    'billing_post_code' => '123456',
                    'billing_vat_number' => '123456',
                    'price' => '10',
                    'payment_type' => 'direct',
                    'status' => 'waiting_payment',
                    'is_free' => '0',
                    'last_months_in_cycle' => '1',
                    'start_day' => '29',
                    'trial_days' => '0',
                    'start_date' => '2021-12-29 00:00:00',
                    'paypal_order_id' => '',
                    'paypal_order_intent' => '',
                    'paypal_order_status' => '',
                    'paypal_order_payer_id' => '',
                    'paypal_order_payer_given_name' => '',
                    'paypal_order_payer_surname' => '',
                    'paypal_order_payer_email' => '',
                    'paypal_order_payee_id' => '',
                    'paypal_order_payee_email' => '',
                    'paypal_order_amount_currency_code' => '',
                    'paypal_order_amount_value' => '',
                    'paypal_order_info' => '',
                    'paypal_order_create_time' => '',
                    'paypal_order_update_time' => '',
                    'paypal_order_capture_create_time' => '',
                    'paypal_order_capture_update_time' => '',
                    'confirm_key' => '161cc414e16f971',
                    'created_at' => '2021-12-29 11:06:54',
                    'plan_name' => 'Monthly Plan',
                    'user_name' => 'admin',
                    'end_date' => '2022-01-28',
                    'account_name' => 'Example bank name',
                    'account_number' => '123456',
                    'bank_name' => 'Example account name',
                    'routing_number' => '123456',
                    'iban' => 'NL43INGB4186520410',
                    'bic_swift' => '123456',
                    'description' => 'Sample payment method description.',
                    'instruction' => 'Sample payment method instructions.',
                    'options' => NULL,
                    'formatted_price' => 'USD 10'
                ),
                array(
                    'id' => '2',
                    'plan_id' => '1',
                    'user_id' => '1',
                    'ref_id' => NULL,
                    'billing_first_name' => 'John',
                    'billing_last_name' => 'Doe',
                    'billing_company_name' => 'Demo company name',
                    'billing_cui' => NULL,
                    'billing_j' => NULL,
                    'billing_address' => 'Cross Roads 44',
                    'billing_county' => 'New York',
                    'billing_city' => 'New York',
                    'billing_country' => 'United States',
                    'billing_phone' => '+30 789546548',
                    'billing_email' => 'mail@example.com',
                    'billing_bank_account' => NULL,
                    'billing_post_code' => '123456',
                    'billing_vat_number' => '123456',
                    'price' => '10',
                    'payment_type' => 'direct',
                    'status' => 'active',
                    'is_free' => '0',
                    'last_months_in_cycle' => '12',
                    'start_day' => '29',
                    'trial_days' => '0',
                    'start_date' => '2021-11-05 00:00:00',
                    'paypal_order_id' => '',
                    'paypal_order_intent' => '',
                    'paypal_order_status' => '',
                    'paypal_order_payer_id' => '',
                    'paypal_order_payer_given_name' => '',
                    'paypal_order_payer_surname' => '',
                    'paypal_order_payer_email' => '',
                    'paypal_order_payee_id' => '',
                    'paypal_order_payee_email' => '',
                    'paypal_order_amount_currency_code' => '',
                    'paypal_order_amount_value' => '',
                    'paypal_order_info' => '',
                    'paypal_order_create_time' => '',
                    'paypal_order_update_time' => '',
                    'paypal_order_capture_create_time' => '',
                    'paypal_order_capture_update_time' => '',
                    'confirm_key' => '161cc414e16f971',
                    'created_at' => '2021-12-29 11:06:54',
                    'plan_name' => 'Yearly Plan',
                    'user_name' => 'admin',
                    'end_date' => '2022-11-04',
                    'account_name' => 'Example bank name',
                    'account_number' => '123456',
                    'bank_name' => 'Example account name',
                    'routing_number' => '123456',
                    'iban' => 'NL43INGB4186520410',
                    'bic_swift' => '123456',
                    'description' => 'Sample payment method description.',
                    'instruction' => 'Sample payment method instructions.',
                    'options' => NULL,
                    'formatted_price' => 'USD 10'
                ),
                array(
                    'id' => '2',
                    'plan_id' => '1',
                    'user_id' => '1',
                    'ref_id' => NULL,
                    'billing_first_name' => 'John',
                    'billing_last_name' => 'Doe',
                    'billing_company_name' => 'Demo company name',
                    'billing_cui' => NULL,
                    'billing_j' => NULL,
                    'billing_address' => 'Cross Roads 44',
                    'billing_county' => 'New York',
                    'billing_city' => 'New York',
                    'billing_country' => 'United States',
                    'billing_phone' => '+30 789546548',
                    'billing_email' => 'mail@example.com',
                    'billing_bank_account' => NULL,
                    'billing_post_code' => '123456',
                    'billing_vat_number' => '',
                    'price' => '10',
                    'payment_type' => 'paypal',
                    'status' => 'closed',
                    'is_free' => '0',
                    'last_months_in_cycle' => '1',
                    'start_day' => '29',
                    'trial_days' => '0',
                    'start_date' => '2021-10-14 00:00:00',
                    'paypal_order_id' => '',
                    'paypal_order_intent' => '',
                    'paypal_order_status' => '',
                    'paypal_order_payer_id' => '',
                    'paypal_order_payer_given_name' => '',
                    'paypal_order_payer_surname' => '',
                    'paypal_order_payer_email' => '',
                    'paypal_order_payee_id' => '',
                    'paypal_order_payee_email' => '',
                    'paypal_order_amount_currency_code' => '',
                    'paypal_order_amount_value' => '',
                    'paypal_order_info' => '',
                    'paypal_order_create_time' => '',
                    'paypal_order_update_time' => '',
                    'paypal_order_capture_create_time' => '',
                    'paypal_order_capture_update_time' => '',
                    'confirm_key' => '161cc414e16f971',
                    'created_at' => '2021-12-29 11:06:54',
                    'plan_name' => 'Monthly Plan',
                    'user_name' => 'admin',
                    'end_date' => '2021-11-14',
                    'account_name' => 'Example bank name',
                    'account_number' => '123456',
                    'bank_name' => 'Example account name',
                    'routing_number' => '123456',
                    'iban' => 'NL43INGB4186520410',
                    'bic_swift' => '123456',
                    'description' => 'Sample payment method description.',
                    'instruction' => 'Sample payment method instructions.',
                    'options' => NULL,
                    'formatted_price' => 'USD 10'
                ),
            )
        );

        $dummy_direct_payment_details = array(
            'direct_payment_bank' => 'Example bank name',
            'direct_payment_account_name' => 'Example account name',
            'direct_payment_account_number' => '123456',
            'direct_payment_routing_number' => '123456',
            'direct_payment_iban' => 'NL43INGB4186520410',
            'direct_payment_bic_swift' => '123456',
            'direct_payment_instruction' => 'Sample payment methods instructions.'
        );

        // remove top border on Newsmag
        $block_classes = str_replace('td-pb-border-top', '', $this->get_block_classes());

		$buffy = '<div class="' . $block_classes . '" ' . $this->get_block_html_atts() . '>';

			$buffy .= $this->get_block_css(); // get block css
			$buffy .= $this->get_block_js(); // get block js


            if (is_user_logged_in()) {

                ob_start();
                ?>

	            <div class="tds-block-inner td-fix-index">
                    <div class="tds-s-page-sec tds-s-page-subsc">
                        <div class="tds-s-page-sec-header">
                            <h2 class="tds-spsh-title"><?php echo __td('Subscriptions', TD_THEME_NAME) ?></h2>
                            <div class="tds-spsh-descr"><?php echo __td('All your subscriptions.', TD_THEME_NAME) ?></div>
                        </div>

                        <div class="tds-s-page-sec-content">
                            <?php
                            $buffy .= ob_get_clean();

                            $result = $dummy_subscriptions_data;
                            if( !$is_composer ) {
                                $result = tds_util::get_subscriptions(get_current_user_id());
                            }

                            if ( ! empty( $result ) && ! empty( $result[ 'subscriptions' ] ) ) {

                                ob_start();
                                ?>

                                <table class="tds-s-table tds-s-table-subscr">
                                    <thead class="tds-s-table-header">
                                        <tr class="tds-s-table-row tds-s-table-row-h">
                                            <th class="tds-s-table-col"><?php echo __td('Plan', TD_THEME_NAME) ?></th>
                                            <th class="tds-s-table-col"><?php echo __td('Payment type', TD_THEME_NAME) ?></th>
                                            <th class="tds-s-table-col"><?php echo __td('Price', TD_THEME_NAME) ?></th>
                                            <th class="tds-s-table-col"><?php echo __td('Status', TD_THEME_NAME) ?></th>
                                            <th class="tds-s-table-col"><?php echo __td('Months', TD_THEME_NAME) ?></th>
                                            <th class="tds-s-table-col tds-s-table-col-start-date"><?php echo __td('Start date', TD_THEME_NAME) ?></th>
                                            <th class="tds-s-table-col tds-s-table-col-end-date"><?php echo __td('End date', TD_THEME_NAME) ?></th>
                                            <th class="tds-s-table-col"></th>
                                        </tr>
                                    </thead>

                                    <tbody class="tds-s-table-body">
                                        <?php

                                        $is_stripe = false;

                                        foreach ($result[ 'subscriptions' ] as $key => $subscription) {
                                            $buffy_payment_btn = '';
                                            $buffy_payment_stripe_form = '';

                                            if ('waiting_payment' === $subscription['status'] && !empty($subscription['price'])) {

                                                switch ($subscription['payment_type']) {
                                                    case 'paypal':

                                                        $payment_paypal       = td_subscription::get_payment_method_credentials();
                                                        $tds_paypal_client_id = $payment_paypal[ 'client_id' ];

                                                        if ( ! empty( $payment_paypal ) && '1' === $payment_paypal[ 'is_active' ] ) {

                                                            $price = $subscription[ 'price' ];
                                                            if ( 'waiting_payment' !== $subscription[ 'status' ] && ! empty( $subscription[ 'next_price' ] ) ) {
                                                                $price = $subscription[ 'next_price' ];
                                                            }

                                                            $currency = tds_util::get_tds_option( 'curr_name' );
                                                            if ( empty( $currency ) ) {
                                                                $currency = 'USD';
                                                            }
                                                            if ( ! empty( $subscription[ 'curr_name' ] ) ) {
                                                                $currency = $subscription[ 'curr_name' ];
                                                            }

                                                            $buffy_payment_btn =
                                                                '<script src="https://www.paypal.com/sdk/js?client-id=' . $tds_paypal_client_id . '&currency=' . $currency . '"></script>
                                                            <div class="tds-paypal-button" data-value="' . $price . '" data-currency="' . $currency . '"></div>';
                                                        }
                                                        break;

                                                    case 'stripe':

                                                        global $wpdb;
                                                        $tds = $wpdb->get_results("SELECT * FROM tds_payment_stripe LIMIT 1", ARRAY_A);

                                                        if (!empty($tds[0]) && '1' === $tds[0][ 'is_active' ]) {

                                                            $price = $subscription[ 'price' ];
                                                            if ( 'waiting_payment' !== $subscription[ 'status' ] && ! empty( $subscription[ 'next_price' ] ) ) {
                                                                $price = $subscription[ 'next_price' ];
                                                            }

                                                            $currency = tds_util::get_tds_option( 'curr_name' );
                                                            if ( empty( $currency ) ) {
                                                                $currency = 'USD';
                                                            }
                                                            if ( ! empty( $subscription[ 'curr_name' ] ) ) {
                                                                $currency = $subscription[ 'curr_name' ];
                                                            }

                                                            $in_testing = '';
                                                            if ($tds[ 0 ][ 'is_sandbox' ]) {
                                                                $in_testing = 'sandbox_';
                                                            }
                                                            $public_key = $tds[0][ $in_testing . 'public_key' ];

                                                            ob_start();
                                                            ?>

                                                                <div class="tds-stripe-button" data-subscription-id="<?php echo $subscription['id'] ?>" data-value="<? echo $price ?>" data-currency="<?php echo $currency ?>" data-public-key="<?php echo $public_key ?>">Stripe</div>

                                                            <?php
                                                            $buffy_payment_btn = ob_get_clean();

                                                            if (!$is_stripe) {
                                                                $is_stripe = true;
                                                                ob_start();
                                                                ?>

                                                                <script src="https://js.stripe.com/v3/"></script>
                                                                <div id="tds-stripe-container">
                                                                    <form id="tds-stripe-form">
                                                                        <div id="tds-payment-element" class="loading"></div>

                                                                        <div id="tds-payment-message" class="tds-s-notif tds-s-notif-xsm"><div class="tds-s-notif-descr"></div></div>

                                                                        <div class="tds-stripe-form-btns">
                                                                            <button id="tds-stripe-submit" class="tds-s-btn tds-s-btn-sm" disabled="disabled" data-public-key="<?php echo $public_key ?>">Pay with Stripe</button>
                                                                            <button class="tds-s-btn tds-s-btn-sm tds-s-btn-hollow tds-stripe-cancel" disabled="disabled">Cancel</button>
                                                                        </div>
                                                                    </form>
                                                                </div>

                                                                <?php
                                                                $buffy_payment_stripe_form .= ob_get_clean();
                                                            }
                                                        }
                                                        break;
                                                }
                                            }

                                            ?>
                                            <tr class="tds-s-table-row <?php echo ( $is_composer && $key == 0 ) ? 'tds-s-table-row-active tds-s-table-row-info-expanded' : '' ?>" data-subscription-id="<?php echo $subscription['id'] ?>">
                                                <td class="tds-s-table-col">
                                                    <div class="tds-s-table-col-label"><?php echo __td('Plan', TD_THEME_NAME) ?></div>
                                                    <?php echo $subscription['plan_name'] ? $subscription['plan_name'] : __td('missing plan', TD_THEME_NAME)  ?>
                                                </td>
                                                <td class="tds-s-table-col">
                                                    <div class="tds-s-table-col-label"><?php echo __td('Payment type', TD_THEME_NAME) ?></div>
                                                    <?php
                                                    $buffy .= ob_get_clean();

                                                    if( $subscription['is_free'] == 0 ) {
                                                        switch ($subscription['payment_type']) {
                                                            case 'direct':
                                                                $buffy .= 'Bank transfer';
                                                                break;
                                                            case 'paypal':
                                                                $buffy .= 'PayPal';
                                                                break;
                                                            case 'stripe':
                                                                $buffy .= 'Stripe';
                                                                break;
                                                            default:
                                                                $buffy .= $subscription['payment_type'];
                                                                break;
                                                        }
                                                    } else {
                                                        $buffy .= '-';
                                                    }

                                                    ob_start();
                                                    ?>
                                                </td>
                                                <td class="tds-s-table-col">
                                                    <div class="tds-s-table-col-label">Price</div>
                                                    <?php echo empty($subscription['price']) ? __td('Free', TD_THEME_NAME) : $subscription['formatted_price'] ?>
                                                </td>
                                                <td class="tds-s-table-col">
                                                    <div class="tds-s-table-col-label"><?php echo __td('Status', TD_THEME_NAME) ?></div>
                                                    <?php
                                                    $buffy .= ob_get_clean();

                                                    switch ($subscription['status']) {
                                                        case 'free':
                                                            $buffy .= '<div class="tds-s-table-status tds-s-table-status-free">' . __td('Free', TD_THEME_NAME) . '</div>';
                                                            break;
                                                        case 'active':
                                                            $buffy .= '<div class="tds-s-table-status tds-s-table-status-active">' . __td('Active', TD_THEME_NAME) . '</div>';
                                                            break;
                                                        case 'trial':
                                                            $buffy .= '<div class="tds-s-table-status tds-s-table-status-trial">' . __td('Trial', TD_THEME_NAME)  . $subscription['trial_days'] . ' ' . ((intval($subscription['trial_days']) > 1 ) ? 'days' : 'day') . '</div>';
                                                            break;
                                                        case 'blocked':
                                                            $buffy .= '<div class="tds-s-table-status tds-s-table-status-generic">' . __td('Blocked', TD_THEME_NAME) . '</div>';
                                                            break;
                                                        case 'closed':
                                                            $buffy .= '<div class="tds-s-table-status tds-s-table-status-canceled">' . __td('Closed', TD_THEME_NAME). '</div>';
                                                            break;
                                                        case 'closed_not_paid':
                                                            $buffy .= '<div class="tds-s-table-status tds-s-table-status-not-paid">' . __td('Not paid', TD_THEME_NAME) . '</div>';
                                                            break;
                                                        case 'waiting_payment':
                                                            $buffy .= '<div class="tds-s-table-status tds-s-table-status-waiting">' . __td('Awaiting payment', TD_THEME_NAME) . '</div>';
                                                            break;
                                                        default:
                                                            $buffy .= '<div class="tds-s-table-status tds-s-table-status-generic">' . $subscription['status'] . '</div>';
                                                            break;
                                                    }

                                                    ob_start();
                                                    ?>
                                                </td>
                                                <td class="tds-s-table-col">
                                                    <div class="tds-s-table-col-label">Months</div>
                                                    <?php echo $subscription['is_free'] == 0 ? $subscription['last_months_in_cycle'] : '' ?>
                                                </td>

                                                <td class="tds-s-table-col tds-s-table-col-start-date">
                                                    <div class="tds-s-table-col-label">Start date</div>
                                                    <?php echo tds_util::get_formatted_date($subscription['start_date']) ?>
                                                </td>
                                                <td class="tds-s-table-col tds-s-table-col-end-date">
                                                    <div class="tds-s-table-col-label">End date</div>
                                                    <?php echo $subscription['is_free'] == 0 ? tds_util::get_formatted_date($subscription['end_date']) : '' ?>
                                                </td>

                                                <td class="tds-s-table-col tds-s-table-col-pp-btn <?php echo $buffy_payment_btn == '' ? 'tds-s-table-col-pp-btn-none' : '' ?>">
                                                    <div class="tds-s-table-col-label">Pay now</div>
                                                    <?php echo $buffy_payment_btn ?>
                                                </td>

                                                <td class="tds-s-table-col tds-s-table-col-expand" title="Show more info">
                                                    <div class="tds-s-table-col-label">Show more info</div>
                                                    <svg class="tds-s-table-expand-toggle" xmlns="http://www.w3.org/2000/svg" width="12" height="7" viewBox="0 0 12 7"><path d="M11,15a1,1,0,0,1-.707-.293l-5-5A1,1,0,0,1,6.707,8.293L11,12.586l4.293-4.293a1,1,0,1,1,1.414,1.414l-5,5A1,1,0,0,1,11,15Z" transform="translate(-5 -8)"/></svg>
                                                </td>
                                            </tr>

                                            <?php if( $buffy_payment_stripe_form != '' ) { ?>
                                                <tr class="tds-s-table-row-extra-wrap tds-s-table-row-extra-wrap-stripe" style="display: none" data-belongs-to="<?php echo $subscription['id'] ?>">
                                                    <td class="tds-s-table-row-extra" colspan="9">
                                                        <div class="tds-s-table-row-extra-inner">
                                                            <div class="tds-s-tre-cols">
                                                                <div class="tds-s-tre-col tds-stripe-placeholder">
                                                                    <?php echo $buffy_payment_stripe_form ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php } ?>

                                            <tr class="tds-s-table-row-extra-wrap tds-s-table-row-extra-wrap-info" <?php echo ( $is_composer && $key == 0 ) ? '' : 'style="display: none"' ?> data-belongs-to="<?php echo $subscription['id'] ?>">
                                                <td class="tds-s-table-row-extra" colspan="9">
                                                    <div class="tds-s-table-row-extra-inner">
                                                        <div class="tds-s-tre-cols">
                                                            <div class="tds-s-tre-col tds-s-tre-subscr-info">
                                                                <div class="tds-s-list-wrap">
                                                                    <div class="tds-s-list-title"><?php echo __td('Subscription info', TD_THEME_NAME) ?></div>

                                                                    <ul class="tds-s-list">
                                                                        <li class="tds-s-list-item">
                                                                            <span class="tds-s-list-label"><?php echo __td('ID', TD_THEME_NAME) ?>:</span>
                                                                            <span class="tds-s-list-text">#<?php echo $subscription['id'] ?></span>
                                                                        </li>
                                                                        <li class="tds-s-list-item">
                                                                            <span class="tds-s-list-label"><?php echo __td('Name', TD_THEME_NAME) ?>:</span>
                                                                            <span class="tds-s-list-text"><?php echo $subscription['billing_first_name'] . ' ' . $subscription['billing_last_name'] ?></span>
                                                                        </li>
                                                                        <li class="tds-s-list-item">
                                                                            <span class="tds-s-list-label"><?php echo __td('Company name', TD_THEME_NAME) ?>:</span>
                                                                            <span class="tds-s-list-text"><?php echo $subscription['billing_company_name'] ?></span>
                                                                        </li>
                                                                        <li class="tds-s-list-item">
                                                                            <span class="tds-s-list-label"><?php echo __td('VAT', TD_THEME_NAME) ?>:</span>
                                                                            <span class="tds-s-list-text"><?php echo $subscription['billing_vat_number'] ?></span>
                                                                        </li>
                                                                        <li class="tds-s-list-item">
                                                                            <span class="tds-s-list-label"><?php echo __td('Address', TD_THEME_NAME) ?>:</span>
                                                                            <span class="tds-s-list-text"><?php echo $subscription['billing_address'] ?></span>
                                                                        </li>
                                                                        <li class="tds-s-list-item">
                                                                            <span class="tds-s-list-label"><?php echo __td('City', TD_THEME_NAME) ?>:</span>
                                                                            <span class="tds-s-list-text"><?php echo $subscription['billing_city'] ?></span>
                                                                        </li>
                                                                        <li class="tds-s-list-item">
                                                                            <span class="tds-s-list-label"><?php echo __td('Country/State', TD_THEME_NAME) ?>:</span>
                                                                            <span class="tds-s-list-text"><?php echo $subscription['billing_country'] ?></span>
                                                                        </li>
                                                                        <li class="tds-s-list-item">
                                                                            <span class="tds-s-list-label"><?php echo __td('Email', TD_THEME_NAME) ?>:</span>
                                                                            <span class="tds-s-list-text"><?php echo $subscription['billing_email'] ?></span>
                                                                        </li>
                                                                        <li class="tds-s-list-item">
                                                                            <span class="tds-s-list-label"><?php echo __td('Postal code', TD_THEME_NAME) ?>:</span>
                                                                            <span class="tds-s-list-text"><?php echo $subscription['billing_post_code'] ?></span>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                            <?php if ( $subscription['payment_type'] == 'direct' ) {
                                                                $direct_payment_details = self::get_direct_payment_details();
                                                                ?>
                                                                <div class="tds-s-tre-col tds-s-tre-pay-info tds-s-tre-pay-info-bank">
                                                                    <div class="tds-s-list-wrap">
                                                                        <div class="tds-s-list-title"><?php echo __td('Direct bank transfer details', TD_THEME_NAME) ?></div>

                                                                        <ul class="tds-s-list">
                                                                            <li class="tds-s-list-item">
                                                                                <span class="tds-s-list-label"><?php echo __td('Account name', TD_THEME_NAME) ?>:</span>
                                                                                <span class="tds-s-list-text"><?php echo $direct_payment_details['direct_payment_account_name'] ?></span>
                                                                            </li>
                                                                            <li class="tds-s-list-item">
                                                                                <span class="tds-s-list-label"><?php echo __td('Account number', TD_THEME_NAME) ?>:</span>
                                                                                <span class="tds-s-list-text"><?php echo $direct_payment_details['direct_payment_account_number'] ?></span>
                                                                            </li>
                                                                            <li class="tds-s-list-item">
                                                                                <span class="tds-s-list-label"><?php echo __td('Bank name', TD_THEME_NAME) ?>:</span>
                                                                                <span class="tds-s-list-text"><?php echo $direct_payment_details['direct_payment_bank'] ?></span>
                                                                            </li>
                                                                            <li class="tds-s-list-item">
                                                                                <span class="tds-s-list-label"><?php echo __td('Routing number', TD_THEME_NAME) ?>:</span>
                                                                                <span class="tds-s-list-text"><?php echo $direct_payment_details['direct_payment_routing_number'] ?></span>
                                                                            </li>
                                                                            <li class="tds-s-list-item">
                                                                                <span class="tds-s-list-label"><?php echo __td('IBAN', TD_THEME_NAME) ?>:</span>
                                                                                <span class="tds-s-list-text"><?php echo $direct_payment_details['direct_payment_iban'] ?></span>
                                                                            </li>
                                                                            <li class="tds-s-list-item">
                                                                                <span class="tds-s-list-label"><?php echo __td('Bic/Swift', TD_THEME_NAME) ?>:</span>
                                                                                <span class="tds-s-list-text"><?php echo $direct_payment_details['direct_payment_bic_swift'] ?></span>
                                                                            </li>
                                                                            <li class="tds-s-list-item">
                                                                                <span class="tds-s-list-label"><?php echo __td('Instructions', TD_THEME_NAME) ?>:</span>
                                                                                <span class="tds-s-list-text"><?php echo $direct_payment_details['direct_payment_instruction'] ?></span>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>

                                <?php
                                $buffy .= ob_get_clean();
                            } else {
                                $buffy .= '<div class="tds-s-notif tds-s-notif-info">';
                                    $buffy .= '<div class="tds-s-notif-descr">' . __td('No subscription created.', TD_THEME_NAME) . '</div>';
                                $buffy .= '</div>';
                            }

                        ob_start();
                        ?>

                        </div>
                    </div>
                </div>

                <?php
                $buffy .= ob_get_clean();

            } else {

                ob_start();

                wp_login_form();
                ?>

                <a href="<?php echo esc_url( add_query_arg('lost_password', '', get_permalink()) ); ?>"><?php echo __td('Lost Password', TD_THEME_NAME) ?></a>

                <?php
                $buffy .= ob_get_clean();
            }

		$buffy .= '</div>';

		return $buffy;
	}
}
