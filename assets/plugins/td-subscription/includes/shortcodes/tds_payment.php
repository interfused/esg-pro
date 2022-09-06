<?php

/**
 * Class tds_plans
 */

class tds_payment extends td_block {

	public function get_custom_css() {

        // $unique_block_class
        $unique_block_class = $this->block_uid;

		$compiled_css = '';

		/** @noinspection CssInvalidAtRule */
		$raw_css =
            "<style>

                /* @style_general_tds_page_block */
                .tds-page-block {
                    transform: translateZ(0);
                    font-family: -apple-system,BlinkMacSystemFont,\"Segoe UI\",Roboto,Oxygen-Sans,Ubuntu,Cantarell,\"Helvetica Neue\",sans-serif;
                    font-size: 14px;
                }
                .tds-page-block .td-element-style {
                    z-index: -1;
                }
                .tds-page-block a:not(.tds-s-btn) {
                    color: #0489FC;
                }
                .tds-page-block a:not(.tds-s-btn):hover {
                    color: #152BF7;
                }
                
                /* @style_general_tds_payment */
                .tds_payment .tds-checkout-wrap {
                    display: flex;
                }
                @media (max-width: 767px) {
                    .tds_payment .tds-checkout-wrap {
                        flex-direction: column;
                    }
                }
                .tds_payment .tds-payment-content {
                    flex: 1;
                    padding-right: 34px;
                }
                @media (min-width: 768px) and (max-width: 1018px) {
                    .tds_payment .tds-payment-content {
                        padding-right: 25px;
                    }
                }
                @media (max-width: 767px) {
                    .tds_payment .tds-payment-content {
                        padding-bottom: 35px;
                        padding-right: 0;
                    }
                }
                .tds_payment .tds-payment-sidebar {
                    width: 40%;
                }
                @media (max-width: 767px) {
                    .tds_payment .tds-payment-sidebar {
                        width: 100%;
                    }
                }
                .tds_payment .tds-s-notif-no-plan {
                    width: 100%;
                }
                .tds_payment .tds-s-page-billing-details .tds-s-page-sec-content {
                    background-color: #F8F8F8;
                    padding: 35px 25px;
                }
                @media (max-width: 767px) {
                    .tds_payment .tds-s-page-billing-details .tds-s-page-sec-content {
                        margin: 0 -20px;
                        padding: 30px 20px;
                    }
                }
                @media (min-width: 768px) {
                    .tds_payment .tds-s-billing-details-form .tds-s-form-group:nth-last-child(-n+2) {
                        margin-bottom: 0;
                    }
                    .tds_payment .tds-s-billing-details-form .tds-s-form-group:nth-last-child(-n+2),
                    .tds_payment .tds-s-billing-details-form .tds-s-form-group:nth-last-child(8),
                    .tds_payment .tds-s-billing-details-form .tds-s-form-group:nth-last-child(9),
                    .tds_payment .tds-s-billing-details-form .tds-s-form-group:nth-last-child(10),
                    .tds_payment .tds-s-billing-details-form .tds-s-form-group:nth-last-child(11) {
                        width: 50%;
                    }
                    .tds_payment .tds-s-billing-details-form .tds-s-form-group:nth-last-child(3) {
                        width: 35%;
                    }
                    .tds_payment .tds-s-billing-details-form .tds-s-form-group:nth-last-child(4) {
                        width: 65%;
                    }
                }
                .tds_payment .tds-s-page-sec:not(:last-child) {
                    border-bottom: none;
                }
                .tds_payment .tds-s-table-subscr-summary {
                    margin-bottom: 20px;
                }
                .tds_payment .tds-s-table-subscr-summary .tds-s-table-header {
                    background-color: #F8F8F8;
                    border-bottom: none;
                }
                .tds_payment .tds-s-table-subscr-summary .tds-s-table-header th {
                    padding-top: 13px;
                }
                .tds_payment .tds-s-table-subscr-summary .tds-s-table-body .tds-s-table-row:hover {
                    background-color: transparent;
                }
                @media (min-width: 1019px) {
                    .tds_payment .tds-s-table-subscr-summary-paid .tds-s-table-col:nth-child(2) {
                        text-align: center;
                    }
                    .tds_payment .tds-s-table-subscr-summary-paid .tds-s-table-col:nth-child(3) {
                        text-align: right;
                    }
                }
                @media (min-width: 1019px) {
                    .tds_payment .tds-s-table-subscr-summary-free .tds-s-table-col:nth-child(2) {
                        text-align: right;
                    }
                }
                .tds_payment .tds-s-subscr-summary-total {
                    display: flex;
                    align-items: baseline;
                    font-size: 1.143em;
                    line-height: 1;
                    font-weight: 700;
                    color: #1D2327;
                    padding-right: 10px;
                }
                @media (min-width: 767px) {
                    .tds_payment .tds-s-subscr-summary-total {
                        justify-content: flex-end;
                    }
                }
                .tds_payment .tds-s-subscr-summary-total .tds-s-sst-label {
                    margin-right: 20px;
                }
                .tds_payment .tds-s-pay-methods-form .tds-spm-content {
                    margin-top: 15px;
                }
                body .tds_payment .tds-s-pay-methods-form .tds-s-notif {
                    margin-left: 0;
                    margin-right: 0;
                }
                .tds_payment .tds-s-pay-methods-form .tds-s-notif:not(:last-child) {
                    margin-bottom: 15px;
                }
                .tds_payment .tds-s-pay-methods-form .tds-s-form-footer .tds-s-btn {
                    width: 100%;
                }
                .tds_payment .tds-s-pay-methods-form .tds-s-form-footer #paypal-button-container {
                    width: 100%;
                    height: 44px;
                }
                .tds_payment .tds-payment-confirmation {
                    width: 100%;
                }
                @media (min-width: 767px) {
                    .tds_payment .tds-s-psc-subscr-info,
                    .tds_payment .tds-s-psc-bank-info {
                        margin-bottom: 0;
                    }
                }
                .tds_payment .tds-s-psc-thank-you {
                    order: 1;
                }
                .tds_payment .tds-s-psc-subscr-info {
                    order: 2;
                }
                @media (min-width: 767px) {
                    .tds_payment .tds-s-psc-subscr-info {
                        flex: 1;
                    }
                }
                .tds_payment .tds-s-psc-bank-info {
                    order: 3;
                }
                @media (min-width: 767px) {
                    .tds_payment .tds-s-psc-bank-info {
                        width: 50%;
                    }
                }
                @media (max-width: 767px) {
                    .tds_payment .tds-s-psc-bank-info {
                        margin-top: 45px;
                        margin-bottom: 0;
                    }
                }
                .tds_payment .tds-s-checkout-confirm-btns .tds-s-btn:not(:last-of-type) {
                    margin-right: 26px;
                }
                .tds_payment #stripe-container {
                    width: 100%;
                }
                .tds_payment #tds-payment-element {
                    position: relative;
                    min-height: 40px;
                }
                .tds_payment #tds-payment-element:before,
                .tds_payment #tds-payment-element:after {
                    content: '';
                    position: absolute;
                    opacity: 0;
                    transition: opacity .2s ease-in-out;
                    pointer-events: none;
                }
                .tds_payment #tds-payment-element:before {
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(255, 255, 255, .8);
                    z-index: 1;
                }
                .tds_payment #tds-payment-element:after {
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    margin-top: -15px;
                    margin-left: -15px;
                    width: 30px;
                    height: 30px;
                    border: 2px solid #999;
                    border-left-color: transparent;
                    border-right-color: transparent;
                    border-radius: 50%;
                    -webkit-animation: fullspin 1s infinite ease-out;
                    animation: fullspin 1s infinite ease-out;
                    z-index: 2;
                }
                .tds_payment #tds-payment-element.loading:before,
                .tds_payment #tds-payment-element.loading:after {
                    opacity: 1;
                    pointer-events: auto;
                }
                .tds_payment #tds-payment-message {
                    display: none;
                    margin-top: 32px;
                }
                .tds_payment #tds-stripe-submit {
                    margin-top: 18px;
                }
                .tds_payment #tds-stripe-submit:disabled {
                    opacity: 0.5;
                    cursor: default;
                    pointer-events: none;
                }
                
                
                /* @bill_padd */
                body .$unique_block_class .tds-s-page-billing-details .tds-s-page-sec-content {
                    padding: @bill_padd;
                }
                
                /* @all_input_border */
                body .$unique_block_class .tds-s-form .tds-s-form-input {
                    border: @all_input_border @all_input_border_style @all_input_border_color;
                }
                body .$unique_block_class .tds-s-form .tds-s-form-check .tds-s-fc-check {
                    border: 2px solid @all_input_border_color;
                }
                /* @input_radius */
                body .$unique_block_class .tds-s-form .tds-s-form-input {
                    border-radius: @input_radius;
                }
                
                /* @btn_radius */
                body .$unique_block_class .tds-s-btn {
                    border-radius: @btn_radius;
                }
                
                /* @notif_radius */
                body .$unique_block_class .tds-s-notif {
                    border-radius: @notif_radius;
                }
                
                
                /* @accent_color */
                body .$unique_block_class .tds-s-btn,
                body .$unique_block_class .tds-s-form .tds-s-form-check .tds-s-fc-check:after {
                    background-color: @accent_color;
                }
                body .$unique_block_class .tds-s-form .tds-s-form-group:not(.tds-s-fg-error) .tds-s-form-input:focus:not([readonly]),
                body .$unique_block_class .tds-s-form .tds-s-form-check input:checked + .tds-s-fc-check {
                    border-color: @accent_color !important;
                }
                /* @input_outline_accent_color */
                body .$unique_block_class .tds-s-form .tds-s-form-group:not(.tds-s-fg-error) .tds-s-form-input:focus:not([readonly]),
                body .$unique_block_class .tds-s-form .tds-s-form-check input:checked + .tds-s-fc-check {
                    outline-color: @input_outline_accent_color;
                }
                
                /* @sec_color */
                body .$unique_block_class h2.tds-spsh-title {
                    color: @sec_color;
                }
                
                /* @bill_bg */
                body .$unique_block_class .tds-s-page-billing-details .tds-s-page-sec-content {
                    background-color: @bill_bg;
                }
                
                /* @label_color */
                body .$unique_block_class .tds-s-form .tds-s-form-label,
                body .$unique_block_class .tds-s-form .tds-s-form-check .tds-s-fc-title {
                    color: @label_color;
                }
                /* @input_color */
                body .$unique_block_class .tds-s-form .tds-s-form-input {
                    color: @input_color;
                }
                body .$unique_block_class .tds-s-form .tds-s-form-input:-webkit-autofill,
                body .$unique_block_class .tds-s-form .tds-s-form-input:-webkit-autofill:hover,
                body .$unique_block_class .tds-s-form .tds-s-form-input:-webkit-autofill:focus,
                body .$unique_block_class .tds-s-form .tds-s-form-input:-webkit-autofill:active {
                    -webkit-text-fill-color: @input_color;
                }
                /* @input_bg */
                body .$unique_block_class .tds-s-form .tds-s-form-input {
                    background-color: @input_bg;
                }
                body .$unique_block_class .tds-s-form .tds-s-form-input:-webkit-autofill,
                body .$unique_block_class .tds-s-form .tds-s-form-input:-webkit-autofill:hover,
                body .$unique_block_class .tds-s-form .tds-s-form-input:-webkit-autofill:focus,
                body .$unique_block_class .tds-s-form .tds-s-form-input:-webkit-autofill:active {
                    -webkit-box-shadow: 0 0 0 1000px @input_bg inset !important;
                }
                /* @input_err_accent */
                body .$unique_block_class .tds-s-form .tds-s-fg-error .tds-s-form-input {
                    border-color: @input_err_accent;
                }
                body .$unique_block_class .tds-s-form .tds-s-fg-error-msg {
                    color: @input_err_accent;
                }
                /* @input_err_accent_outline */
                body .$unique_block_class .tds-s-form .tds-s-fg-error .tds-s-form-input {
                    outline-color: @input_err_accent_outline;
                }
                
                /* @tabl_head_color */
                body .$unique_block_class .tds-s-table-subscr-summary .tds-s-table-header {
                    color: @tabl_head_color;
                }
                /* @tabl_head_bg */
                body .$unique_block_class .tds-s-table-subscr-summary .tds-s-table-header {
                    background-color: @tabl_head_bg;
                }
                /* @tabl_body_color */
                body .$unique_block_class .tds-s-table-subscr-summary .tds-s-table-body {
                    color: @tabl_body_color;
                }
                /* @tabl_foot_color */
                body .$unique_block_class .tds-s-subscr-summary-total {
                    color: @tabl_foot_color;
                }
                
                /* @list_label_color */
                body .$unique_block_class .tds-s-list-label {
                    color: @list_label_color;
                }
                /* @list_val_color */
                body .$unique_block_class .tds-s-list-text {
                    color: @list_val_color;
                }
                
                /* @btn_color */
                body .$unique_block_class .tds-s-btn {
                    color: @btn_color;
                }
                /* @btn_color_h */
                body .$unique_block_class .tds-s-btn:hover {
                    color: @btn_color_h;
                }
                /* @btn_bg_h */
                body .$unique_block_class .tds-s-btn:hover {
                    background-color: @btn_bg_h;
                }
                
                /* @notif_info_color */
                body .$unique_block_class .tds-s-notif-info {
                    color: @notif_info_color;
                }
                /* @notif_info_bg */
                body .$unique_block_class .tds-s-notif-info {
                    background-color: @notif_info_bg;
                }
                
                
                /* @f_text */
                body .$unique_block_class {
                    @f_text
                }
                    
            </style>";

		$td_css_res_compiler = new td_css_res_compiler( $raw_css );
		$td_css_res_compiler->load_settings( __CLASS__ . '::cssMedia', $this->get_all_atts() );

		$compiled_css .= $td_css_res_compiler->compile_css();

		return $compiled_css;

	}

	static function cssMedia( $res_ctx ) {

        /*-- GENERAL STYLES -- */
        $res_ctx->load_settings_raw( 'style_general_tds_page_block', 1 );
        $res_ctx->load_settings_raw( 'style_general_tds_payment', 1 );



        /*-- LAYOUT -- */
        // inputs border size
        $all_input_border = $res_ctx->get_shortcode_att('all_input_border');
        $res_ctx->load_settings_raw( 'all_input_border', $all_input_border );
        if( $all_input_border == '' ) {
            $res_ctx->load_settings_raw( 'all_input_border', '2px' );
        } else {
            if( is_numeric( $all_input_border ) ) {
                $res_ctx->load_settings_raw( 'all_input_border', $all_input_border . 'px' );
            }
        }

        // inputs border style
        $all_input_border_style = $res_ctx->get_shortcode_att('all_input_border_style');
        if( $all_input_border_style != '' ) {
            $res_ctx->load_settings_raw( 'all_input_border_style', $all_input_border_style );
        } else {
            $res_ctx->load_settings_raw( 'all_input_border_style', 'solid' );
        }

        // inputs border radius
        $input_radius = $res_ctx->get_shortcode_att('input_radius');
        $res_ctx->load_settings_raw( 'input_radius', $input_radius );
        if( $input_radius != '' && is_numeric( $input_radius ) ) {
            $res_ctx->load_settings_raw( 'input_radius', $input_radius . 'px' );
        }


        // billing details form padding
        $bill_padd = $res_ctx->get_shortcode_att('bill_padd');
        $res_ctx->load_settings_raw( 'bill_padd', $bill_padd );
        if( $bill_padd != '' && is_numeric( $bill_padd ) ) {
            $res_ctx->load_settings_raw( 'bill_padd', $bill_padd . 'px' );
        }


        // buttons border radius
        $btn_radius = $res_ctx->get_shortcode_att('btn_radius');
        $res_ctx->load_settings_raw( 'btn_radius', $btn_radius );
        if( $btn_radius != '' && is_numeric( $btn_radius ) ) {
            $res_ctx->load_settings_raw( 'btn_radius', $btn_radius . 'px' );
        }


        // notifications border radius
        $notif_radius = $res_ctx->get_shortcode_att('notif_radius');
        $res_ctx->load_settings_raw( 'notif_radius', $notif_radius );
        if( $notif_radius != '' && is_numeric( $notif_radius ) ) {
            $res_ctx->load_settings_raw( 'notif_radius', $notif_radius . 'px' );
        }



        /*-- COLORS -- */
        $accent_color = $res_ctx->get_shortcode_att('accent_color');
        $res_ctx->load_settings_raw( 'accent_color', $accent_color );
        if( !empty( $accent_color ) ) {
            $res_ctx->load_settings_raw('input_outline_accent_color', td_util::hex2rgba($accent_color, 0.1));
        }

        $res_ctx->load_settings_raw( 'sec_color', $res_ctx->get_shortcode_att('sec_color') );

        $res_ctx->load_settings_raw( 'bill_bg', $res_ctx->get_shortcode_att('bill_bg') );
        $res_ctx->load_settings_raw( 'label_color', $res_ctx->get_shortcode_att('label_color') );
        $res_ctx->load_settings_raw( 'input_color', $res_ctx->get_shortcode_att('input_color') );
        $res_ctx->load_settings_raw( 'input_bg', $res_ctx->get_shortcode_att('input_bg') );
        $all_input_border_color = $res_ctx->get_shortcode_att('all_input_border_color');
        if( $all_input_border_color != '' ) {
            $res_ctx->load_settings_raw( 'all_input_border_color', $all_input_border_color );
        } else {
            $res_ctx->load_settings_raw( 'all_input_border_color', '#D7D8DE' );
        }
        $input_err_accent = $res_ctx->get_shortcode_att('input_err_accent');
        $res_ctx->load_settings_raw( 'input_err_accent',$input_err_accent  );
        if( !empty( $input_err_accent ) ) {
            $res_ctx->load_settings_raw('input_err_accent_outline', td_util::hex2rgba($input_err_accent, 0.1));
        }

        $res_ctx->load_settings_raw( 'tabl_head_color', $res_ctx->get_shortcode_att('tabl_head_color') );
        $res_ctx->load_settings_raw( 'tabl_head_bg', $res_ctx->get_shortcode_att('tabl_head_bg') );
        $res_ctx->load_settings_raw( 'tabl_body_color', $res_ctx->get_shortcode_att('tabl_body_color') );
        $res_ctx->load_settings_raw( 'tabl_foot_color', $res_ctx->get_shortcode_att('tabl_foot_color') );

        $res_ctx->load_settings_raw( 'list_label_color', $res_ctx->get_shortcode_att('list_label_color') );
        $res_ctx->load_settings_raw( 'list_val_color', $res_ctx->get_shortcode_att('list_val_color') );

        $res_ctx->load_settings_raw( 'btn_color', $res_ctx->get_shortcode_att('btn_color') );
        $res_ctx->load_settings_raw( 'btn_color_h', $res_ctx->get_shortcode_att('btn_color_h') );
        $res_ctx->load_settings_raw( 'btn_bg_h', $res_ctx->get_shortcode_att('btn_bg_h') );

        $notif_info_color = $res_ctx->get_shortcode_att('notif_info_color');
        $res_ctx->load_settings_raw( 'notif_info_color', $notif_info_color );
        if( !empty( $notif_info_color ) ) {
            $res_ctx->load_settings_raw('notif_info_bg', td_util::hex2rgba($notif_info_color, 0.08));
        }



        /*-- FONTS -- */
        $res_ctx->load_font_settings( 'f_text' );

	}

	function __construct() {
		parent::disable_loop_block_features();
	}

	function render( $atts, $content = null ) {

        parent::render( $atts );

        $allow_subscribe = false;
		//$msg_confirm = $this->get_att('msg_confirm' );
        $msg_confirm =  __td('Thank you! We are delighted to see you here. Your subscription will be activated soon!', TD_THEME_NAME);// flag to check if we are in composer


        $is_composer = false;
        if( td_util::tdc_is_live_editor_iframe() || td_util::tdc_is_live_editor_ajax() ) {
            $is_composer = true;
        }

        // show a specific version of the shortcode in composer
        $show_version_in_composer = $this->get_att('show_version');

        $show_version_in_composer_confirm = false;
        if( $is_composer && $show_version_in_composer == 'confirm' ) {
            $show_version_in_composer_confirm = true;
        }

        // remove top border on Newsmag
        $block_classes = str_replace('td-pb-border-top', '', $this->get_block_classes());

		$buffy = '<div class="tds-page-block ' . $block_classes . '" ' . $this->get_block_html_atts() . '>';

			$buffy .= $this->get_block_css(); // get block css
			$buffy .= $this->get_block_js(); // get block js


            $my_account_page_id = tds_util::get_tds_option('my_account_page_id');

            if (!is_null($my_account_page_id) ) {
                $my_account_permalink = get_permalink( $my_account_page_id );
                if ( false !== $my_account_permalink ) {
                    $tds_subscription_url = esc_url( add_query_arg( 'subscriptions', '', $my_account_permalink ) );
                }
            }
            if ( !empty($tds_subscription_url) ) {
                if ( ! empty( $_REQUEST[ 'ref_url' ] ) ) {
	                $tds_subscription_url = add_query_arg( 'ref_url', $_REQUEST[ 'ref_url' ], $tds_subscription_url );
                }
                $buffy .= '<input type="hidden" id="tds-subscription-url" value="' . $tds_subscription_url . '">';
            }
            if ( ! empty( $_REQUEST[ 'ref_url' ] ) ) {
	            $buffy .= '<input type="hidden" id="tds-ref-url" value="' . $_REQUEST[ 'ref_url' ] . '">';
            }

            $buffy .= '<div class="tds-block-inner tds-checkout-wrap">';
                $payment_subscription = '';

                $subscription_id = $show_version_in_composer_confirm ? '123' : '';
                $subscription_months = $show_version_in_composer_confirm ? '12' : '';
                $subscription_value = $show_version_in_composer_confirm ? '100' : '';
                $subscription_payment_type = $show_version_in_composer_confirm ? 'Direct bank transfer' : '';
                $subscription_is_free = false;

                $subscription_curr_name = $show_version_in_composer_confirm ? '$' : '';
                $subscription_curr_pos = $show_version_in_composer_confirm ? 'left' : '';
                $subscription_curr_th_sep = $show_version_in_composer_confirm ? '.' : '';
                $subscription_curr_dec_sep = $show_version_in_composer_confirm ? ',' : '';
                $subscription_curr_dec_no = $show_version_in_composer_confirm ? '0' : '';

                $plan_id = '';
                $plan_name = '';

                $direct_payment_bank = $show_version_in_composer_confirm ? 'Example bank name' : '';
                $direct_payment_account_name = $show_version_in_composer_confirm ? 'Example account name' : '';
                $direct_payment_account_number = $show_version_in_composer_confirm ? '123456' : '';
                $direct_payment_routing_number = $show_version_in_composer_confirm ? '123456' : '';
                $direct_payment_iban = $show_version_in_composer_confirm ? 'NL43INGB4186520410' : '';
                $direct_payment_bic_swift = $show_version_in_composer_confirm ? '123456' : '';
                $direct_payment_instruction = $show_version_in_composer_confirm ? 'Sample playment instructions.' : '';;

                $show_detailed_info = false;

                if (!empty($_GET['subscription']) && !empty($_GET['key'])) {

                    global $wpdb;

            		$subscriptions = $wpdb->get_results($wpdb->prepare( "SELECT * FROM tds_subscriptions WHERE id = %s AND confirm_key = %s", $_GET['subscription'], $_GET['key']), ARRAY_A);

		            if ( null !== $subscriptions && count($subscriptions)) {

		                $subscription_id = $subscriptions[0]['id'];
                        $subscription_months = $subscriptions[0]['last_months_in_cycle'];
		                $subscription_value = $subscriptions[0]['price'];
		                $subscription_curr_name = $subscriptions[0]['curr_name'];
		                $subscription_curr_pos = $subscriptions[0]['curr_pos'];
		                $subscription_curr_th_sep = $subscriptions[0]['curr_th_sep'];
		                $subscription_curr_dec_sep = $subscriptions[0]['curr_dec_sep'];
		                $subscription_curr_dec_no = $subscriptions[0]['curr_dec_no'];

		                if ('direct' === $subscriptions[0]['payment_type']) {
		                    $subscription_payment_type = 'Direct bank transfer';

		                    $payment_bank = $wpdb->get_results("SELECT * FROM tds_payment_bank LIMIT 1", ARRAY_A);
                            if ( null !== $payment_bank && count($payment_bank) && 1 == $payment_bank[0]['is_active']) {

                                $direct_payment_bank = $payment_bank[0]['bank_name'];
                                $direct_payment_account_name = $payment_bank[0]['account_name'];
                                $direct_payment_account_number = $payment_bank[0]['account_number'];
                                $direct_payment_routing_number = $payment_bank[0]['routing_number'];
                                $direct_payment_iban = $payment_bank[0]['iban'];
                                $direct_payment_bic_swift = $payment_bank[0]['bic_swift'];
                                $direct_payment_instruction = $payment_bank[0]['instruction'];
                            }

                        } else if ('paypal' === $subscriptions[0]['payment_type']) {
		                    $subscription_payment_type = 'PayPal';

		                } else if ('stripe' === $subscriptions[0]['payment_type']) {
		                    $subscription_payment_type = 'Stripe';
                        }

                        if( $subscriptions[0]['is_free'] == 1 ) {
                            $subscription_is_free = true;
                        }

                        $plan_id = $subscriptions[0]['plan_id'];
                        $plans = $wpdb->get_results("SELECT * FROM tds_plans WHERE id = '$plan_id'", ARRAY_A );
                        if (false !== $plans && count($plans)) {
                            $plan_name = $plans[0]['name'];
                        }

                        $show_detailed_info = true;
		            }
                }

                ob_start();
                ?>

                <div class="tds-payment-confirmation"
                    <?php echo (
                                 isset($_REQUEST[ 'subscription' ])
                                 || $show_version_in_composer_confirm
                               ) ? '' : 'style="display: none"' ?>>
                    <div class="tds-s-page-sec tds-s-page-sec-cols tds-s-checkout-confirm-details">
                        <div class="tds-s-page-sec-col tds-s-psc-thank-you">
                            <div class="tds-s-page-sec-content">
                                <div class="tds-s-notif tds-s-notif-info">
                                    <div class="tds-s-notif-descr"><?php echo $msg_confirm ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="tds-s-page-sec-col tds-s-psc-bank-info"
                            <?php echo (
                                         ( $show_detailed_info && !$subscription_is_free && $subscription_payment_type == 'Direct bank transfer' )
                                         || $show_version_in_composer_confirm
                                       ) ? '' : 'style="display: none"' ?>>
                            <div class="tds-s-page-sec-header">
                                <h2 class="tds-spsh-title"><?php echo __td('Our bank details', TD_THEME_NAME) ?></h2>
                            </div>

                            <div class="tds-s-page-sec-content">
                                <ul class="tds-s-list">
                                    <li class="tds-s-list-item tds-s-bank-name" <?php echo !empty($direct_payment_bank) ? '' : 'style="display: none"' ?>>
                                        <span class="tds-s-list-label"><?php echo __td('Bank name', TD_THEME_NAME) ?>:</span>
                                        <span class="tds-s-list-text"><?php echo $direct_payment_bank ?></span>
                                    </li>
                                    <li class="tds-s-list-item tds-s-bank-acc-name" <?php echo !empty($direct_payment_account_name) ? '' : 'style="display: none"' ?>>
                                        <span class="tds-s-list-label"><?php echo __td('Account name', TD_THEME_NAME) ?>:</span>
                                        <span class="tds-s-list-text"><?php echo $direct_payment_account_name ?></span>
                                    </li>
                                    <li class="tds-s-list-item tds-s-bank-acc-number" <?php echo !empty($direct_payment_account_number) ? '' : 'style="display: none"' ?>>
                                        <span class="tds-s-list-label"><?php echo __td('Account number', TD_THEME_NAME) ?>:</span>
                                        <span class="tds-s-list-text"><?php echo $direct_payment_account_number ?></span>
                                    </li>
                                    <li class="tds-s-list-item tds-s-bank-routing" <?php echo !empty($direct_payment_routing_number) ? '' : 'style="display: none"' ?>>
                                        <span class="tds-s-list-label"><?php echo __td('Routing number', TD_THEME_NAME) ?>:</span>
                                        <span class="tds-s-list-text"><?php echo $direct_payment_routing_number ?></span>
                                    </li>
                                    <li class="tds-s-list-item tds-s-bank-iban" <?php echo !empty($direct_payment_iban) ? '' : 'style="display: none"' ?>>
                                        <span class="tds-s-list-label"><?php echo __td('IBAN', TD_THEME_NAME) ?>IBAN:</span>
                                        <span class="tds-s-list-text"><?php echo  $direct_payment_iban ?></span>
                                    </li>
                                    <li class="tds-s-list-item tds-s-bank-bic" <?php echo !empty($direct_payment_bic_swift) ? '' : 'style="display: none"' ?>>
                                        <span class="tds-s-list-label"><?php echo __td('Bic/Swift', TD_THEME_NAME) ?>:</span>
                                        <span class="tds-s-list-text"><?php echo $direct_payment_bic_swift ?></span>
                                    </li>
                                    <li class="tds-s-list-item tds-s-bank-instructions" <?php echo !empty($direct_payment_instruction) ? '' : 'style="display: none"' ?>>
                                        <span class="tds-s-list-label"><?php echo __td('Instructions', TD_THEME_NAME) ?>:</span>
                                        <span class="tds-s-list-text"><?php echo $direct_payment_instruction ?></span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="tds-s-page-sec-col tds-s-psc-subscr-info"
                            <?php echo (
                                         $show_detailed_info
                                         || $show_version_in_composer_confirm
                                       ) ? '' : 'style="display: none"' ?>>
                            <div class="tds-s-page-sec-header">
                                <h2 class="tds-spsh-title"><?php echo __td('Your subscription details', TD_THEME_NAME) ?></h2>
                            </div>

                            <div class="tds-s-page-sec-content">
                                <ul class="tds-s-list">
                                    <li class="tds-s-list-item tds-s-subscr-id">
                                        <span class="tds-s-list-label"><?php echo __td('ID', TD_THEME_NAME) ?>:</span>
                                        <span class="tds-s-list-text"><?php echo $subscription_id ?></span>
                                    </li>
                                    <li class="tds-s-list-item tds-s-subscr-plan">
                                        <span class="tds-s-list-label"><?php echo __td('Plan', TD_THEME_NAME) ?>:</span>
                                        <span class="tds-s-list-text"><?php echo $plan_name ?></span>
                                    </li>
                                    <li class="tds-s-list-item tds-s-subscr-months" <?php echo !$subscription_is_free ? '' : 'style="display: none"' ?>>
                                        <span class="tds-s-list-label"><?php echo __td('Months', TD_THEME_NAME) ?>:</span>
                                        <span class="tds-s-list-text"><?php echo $subscription_months ?></span>
                                    </li>
                                    <li class="tds-s-list-item tds-s-subscr-period">
                                        <span class="tds-s-list-label"><?php echo __td('Period', TD_THEME_NAME) ?>:</span>
                                        <span class="tds-s-list-text"><?php echo date('Y-m-d') ?> - <?php echo !empty($subscription_value) ? tds_util::get_end_date(date('Y-m-d'), $subscription_months, date('j'))->format('Y-m-d') : __td('unlimited', TD_THEME_NAME) ?></span>
                                    </li>
                                    <li class="tds-s-list-item tds-s-subscr-pay-method" <?php echo !$subscription_is_free ? '' : 'style="display: none"' ?>>
                                        <span class="tds-s-list-label"><?php echo __td('Payment method', TD_THEME_NAME) ?>:</span>
                                        <span class="tds-s-list-text"><?php echo $subscription_payment_type ?></span>
                                    </li>
                                    <li class="tds-s-list-item tds-s-subscr-total">
                                        <span class="tds-s-list-label"><?php echo __td('Total', TD_THEME_NAME) ?>:</span>
                                        <span class="tds-s-list-text"><?php echo $subscription_is_free ? __td('Free', TD_THEME_NAME) : tds_util::get_formatted_currency($subscription_value, $subscription_curr_name, $subscription_curr_pos, $subscription_curr_th_sep, $subscription_curr_dec_sep, $subscription_curr_dec_no) ?></span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="tds-s-page-sec tds-s-checkout-confirm-btns">
                        <div class="tds-s-page-sec-content">
                            <a href="<?php echo empty($tds_subscription_url) ? '#' : $tds_subscription_url ?>" class="tds-s-btn"><?php echo __td('View subscription', TD_THEME_NAME) ?></a>
                            <a href="<?php echo home_url() ?>" class="tds-s-btn"><?php echo __td('Home', TD_THEME_NAME) ?></a>
                        </div>
                    </div>
                </div>

                <?php

                $payment_subscription .= ob_get_clean();

                if ( isset( $_REQUEST[ 'subscription' ] ) || ( $is_composer && $show_version_in_composer == 'confirm' ) ) {
                            $buffy .= $payment_subscription;
                        $buffy .= '</div>';
                    $buffy .= '</div>';

                    return $buffy;
                }


                if ( is_user_logged_in() && ! empty( $_REQUEST[ 'plan_id' ] ) ) {
                    global $wpdb;

                    $plan_id = esc_sql($_REQUEST[ 'plan_id' ]);
                    $results = $wpdb->get_results("SELECT * FROM tds_plans WHERE id = '$plan_id'", ARRAY_A );
                    if (false !== $results && count($results)) {
                        $active_subscriptions = $wpdb->get_results($wpdb->prepare( "SELECT * FROM tds_subscriptions WHERE user_id = %s AND plan_id = %s AND status IN ('active')", get_current_user_id(), $plan_id), ARRAY_A);
                        if ( null !== $active_subscriptions && count($active_subscriptions) ) {

                            if (empty( $_REQUEST['ref_url'])) {
	                             $redirect_to_account_url = esc_url( add_query_arg( 'subscriptions', '', $my_account_permalink ) );
                            } else {
                                // redirect to the original locked post - to see it unlocked now
	                            $redirect_to_original_url = base64_decode( $_REQUEST[ 'ref_url' ] );
                            }

                        } else {
                            $waiting_payment_subscriptions = $wpdb->get_results($wpdb->prepare( "SELECT * FROM tds_subscriptions WHERE user_id = %d AND plan_id = %d AND status IN ('waiting_payment')", get_current_user_id(), $plan_id), ARRAY_A);
                            if ( null !== $waiting_payment_subscriptions && count($waiting_payment_subscriptions)) {
                                $my_account_page_id = tds_util::get_tds_option('my_account_page_id');
                                if (!is_null($my_account_page_id)) {
                                    $my_account_permalink = get_permalink( $my_account_page_id );
                                    if ( false !== $my_account_permalink ) {

                                        // redirect to subscription account - to inform user about the existing waiting in payment subscription
                                        $redirect_to_unpaid_account_url = esc_url( add_query_arg( 'subscriptions', '', $my_account_permalink ) );
                                    }
                                }
                            }
                        }
                    }

                    if (!empty($redirect_to_original_url)) {

                        ob_start();
                        ?>

                        <div class="tds-payment-confirmation">
                            <div class="tds-s-page-sec tds-s-page-sec-cols tds-s-checkout-confirm-details">
                                <div class="tds-s-page-sec-col tds-s-psc-thank-you">
                                    <div class="tds-s-page-sec-content">
                                        <div class="tds-s-notif tds-s-notif-info">
                                            <div class="tds-s-notif-descr"><?php _etd('You already have an active subscription. We will redirect you back to your post!', TD_THEME_NAME) ?></div>
                                            <script>
                                                (function() {
                                                    setTimeout(function() {
                                                        window.location = '<?php echo $redirect_to_original_url ?>';
                                                    }, 5000);
                                                })();
                                            </script>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php

                        $buffy .= ob_get_clean();

                            $buffy .= '</div>';
                        $buffy .= '</div>';

                        return $buffy;

                    } else if (!empty($redirect_to_unpaid_account_url)) {

	                    ob_start();
	                    ?>

                        <div class="tds-payment-confirmation">
                            <div class="tds-s-page-sec tds-s-page-sec-cols tds-s-checkout-confirm-details">
                                <div class="tds-s-page-sec-col tds-s-psc-thank-you">
                                    <div class="tds-s-page-sec-content">
                                        <div class="tds-s-notif tds-s-notif-info">
                                            <div class="tds-s-notif-descr"><?php _etd( 'You already have a subscription, but it\'s still in waiting to be paid!', TD_THEME_NAME ) ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tds-s-page-sec tds-s-checkout-confirm-btns">
                                <div class="tds-s-page-sec-content">
                                    <a href="<?php echo empty( $redirect_to_unpaid_account_url ) ? '#' : $redirect_to_unpaid_account_url ?>"
                                       class="tds-s-btn"><?php echo __td( 'View subscription', TD_THEME_NAME ) ?></a>
                                </div>
                            </div>
                        </div>

	                    <?php

	                    $buffy .= ob_get_clean();

	                        $buffy .= '</div>';
	                    $buffy .= '</div>';

	                    return $buffy;

                    } else if (!empty($redirect_to_account_url)) {

                        ob_start();
                        ?>

                        <div class="tds-payment-confirmation">
                            <div class="tds-s-page-sec tds-s-page-sec-cols tds-s-checkout-confirm-details">
                                <div class="tds-s-page-sec-col tds-s-psc-thank-you">
                                    <div class="tds-s-page-sec-content">
                                        <div class="tds-s-notif tds-s-notif-info">
                                            <div class="tds-s-notif-descr"><?php _etd('You already have an active subscription!', TD_THEME_NAME) ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tds-s-page-sec tds-s-checkout-confirm-btns">
                                <div class="tds-s-page-sec-content">
                                    <a href="<?php echo empty($redirect_to_account_url) ? '#' : $redirect_to_account_url ?>" class="tds-s-btn"><?php echo __td('View subscription', TD_THEME_NAME) ?></a>
                                </div>
                            </div>
                        </div>

                        <?php

                        $buffy .= ob_get_clean();

                            $buffy .= '</div>';
                        $buffy .= '</div>';

                        return $buffy;
                    }
                }



                global $wpdb;

                if ( ! empty( $_REQUEST[ 'plan_id' ] ) ) {
                    $plan_id = $_REQUEST[ 'plan_id' ];

                    $plan_id = esc_sql($plan_id);
                    $results = $wpdb->get_results("SELECT * FROM tds_plans WHERE id = '$plan_id'", ARRAY_A );
                    if (false !== $results && count($results)) {
                        $plan_price = $results[0]['price'];
                        $plan_months_in_cycle = $results[0]['months_in_cycle'];
                        $plan_name = $results[0]['name'];
                        $plan_free = $results[0]['is_free'];
                    }
                }


                if ( ( empty($plan_price) && empty($plan_free) && !$is_composer ) || ( $is_composer && $show_version_in_composer == 'no_plan' ) ) {
                    ob_start();
                    ?>

                            <div class="tds-s-notif tds-s-notif-info tds-s-notif-no-plan">
                                <div class="tds-s-notif-descr"><?php echo __td('You have not selected a valid subscription plan.', TD_THEME_NAME) ?></div>
                            </div>

                        <?php
                        $buffy .= ob_get_clean();
                        $buffy .= '</div>';
                    $buffy .= '</div>';

                    return $buffy;
                }


                if (empty($plan_id) && (td_util::tdc_is_live_editor_iframe() || td_util::tdc_is_live_editor_ajax())) {
                    $plan_price = 100; // fake plan price for composer
                    $plan_months_in_cycle = 12; // fake plan months in cycle for composer
                    $plan_name = 'Yearly Plan'; // fake plan name for composer
                }


                $curr_name = tds_util::get_tds_option('curr_name');
                if (empty($curr_name)) {
                    $curr_name = 'USD';
                }

                $payment_bank   = $wpdb->get_results( "SELECT * FROM tds_payment_bank LIMIT 1", ARRAY_A );
                $payment_paypal = td_subscription::get_payment_method_credentials();
                $payment_stripe   = $wpdb->get_results( "SELECT * FROM tds_payment_stripe LIMIT 1", ARRAY_A );

                if ( !empty($payment_paypal) && '1' === $payment_paypal['is_active'] ) {
	                $client_id = $payment_paypal[ 'client_id' ];
	                $buffy     .= '<script src="https://www.paypal.com/sdk/js?client-id=' . $client_id . '&currency=' . $curr_name . '"></script>';


	                ob_start();
	                ?>
                    <script>

                        var tdsValidation = {
                            fields: [{
                                billingFirstName: jQuery('#tds-billing-first-name'),
                                emptyMsg: '<?php echo __td( 'Field empty', TD_THEME_NAME ) ?>'
                            },
                                {
                                    billingLastName: jQuery('#tds-billing-last-name'),
                                    emptyMsg: '<?php echo __td( 'Field empty', TD_THEME_NAME ) ?>'
                                },
                                {
                                    billingCountry: jQuery('#tds-billing-country'),
                                    emptyMsg: '<?php echo __td( 'Field empty', TD_THEME_NAME ) ?>'
                                },
                                {
                                    billingAddress: jQuery('#tds-billing-address'),
                                    emptyMsg: '<?php echo __td( 'Field empty', TD_THEME_NAME ) ?>'
                                },
                                {
                                    billingCity: jQuery('#tds-billing-city'),
                                    emptyMsg: '<?php echo __td( 'Field empty', TD_THEME_NAME ) ?>'
                                },
                                {
                                    billingCounty: jQuery('#tds-billing-county'),
                                    emptyMsg: '<?php echo __td( 'Field empty', TD_THEME_NAME ) ?>'
                                },
                                {
                                    billingPostcode: jQuery('#tds-billing-postcode'),
                                    emptyMsg: '<?php echo __td( 'Field empty', TD_THEME_NAME ) ?>'
                                },
                                {
                                    billingPhone: jQuery('#tds-billing-phone'),
                                    emptyMsg: '<?php echo __td( 'Field empty', TD_THEME_NAME ) ?>'
                                },
                                {
                                    billingEmail: jQuery('#tds-billing-email'),
                                    emptyMsg: '<?php echo __td( 'Field empty', TD_THEME_NAME ) ?>'
                                }],

                            resetFields: function () {
                                tdsValidation.fields.forEach(el => {
                                    Object.values(el)[0].closest('.tds-s-form-group').removeClass('tds-s-fg-error').find('.tds-s-fg-error-msg').html('');
                                });
                            },

                            checkFields: function () {
                                let result = {
                                    params: {},
                                    checked: true
                                };

                                tdsValidation.fields.forEach(el => {

                                    result.params[Object.keys(el)[0]] = Object.values(el)[0].val().trim();

                                    if ('' === Object.values(el)[0].val().trim()) {
                                        result.checked = false;
                                        Object.values(el)[0].closest('.tds-s-form-group').addClass('tds-s-fg-error').find('.tds-s-fg-error-msg').html(Object.values(el)[1]);
                                    }
                                });

                                return result;
                            }
                        };

                        if ('undefined' !== typeof window.paypal) {

                            paypal.Buttons({
                                fundingSource: paypal.FUNDING.PAYPAL,
                                onClick: function (data, actions) {
                                    tdsValidation.resetFields();
                                    if (!tdsValidation.checkFields().checked) {
                                        return actions.reject();
                                    }
                                    return actions.resolve();
                                },
                                createOrder: function (data, actions) {
                                    return actions.order.create({
                                        purchase_units: [{
                                            amount: {
                                                value: '<?php echo $plan_price ?>',
                                                currency_code: '<?php echo $curr_name ?>'
                                            }
                                        }],
                                        application_context: {
                                            shipping_preference: "NO_SHIPPING"
                                        }
                                    });
                                },
                                onApprove: function (data, actions) {
                                    return actions.order.capture().then(function (orderData) {
                                        console.log(orderData);

                                        var $tdsSubscriptionUserId = jQuery('#tds-subscription-user-id'),
                                            $tdsSubscriptionPlanId = jQuery('#tds-subscription-plan-id'),
                                            $tdsBillingFirstName = jQuery('#tds-billing-first-name'),
                                            $tdsBillingLastName = jQuery('#tds-billing-last-name'),
                                            $tdsBillingCompanyName = jQuery('#tds-billing-company-name'),
                                            $tdsBillingVatNumber = jQuery('#tds-billing-vat'),
                                            $tdsBillingCountry = jQuery('#tds-billing-country'),
                                            $tdsBillingAddress = jQuery('#tds-billing-address'),
                                            $tdsBillingCity = jQuery('#tds-billing-city'),
                                            $tdsBillingCounty = jQuery('#tds-billing-county'),
                                            $tdsBillingPostcode = jQuery('#tds-billing-postcode'),
                                            $tdsBillingPhone = jQuery('#tds-billing-phone'),
                                            $tdsBillingEmail = jQuery('#tds-billing-email'),
                                            $tdsBillingPaymentMethod = jQuery('.tds-billing-payment-method:checked'),
                                            $tdsRefUrl = jQuery('#tds-ref-url');

                                        if ($tdsRefUrl.length) {
                                            refUrl = $tdsRefUrl.val();
                                        }

                                        jQuery.ajax({
                                            timeout: 10000,
                                            type: 'POST',
                                            url: tdsSubs.get_rest_endpoint(
                                                'tds_subscription/create_subscription',
                                                'uuid=' + tdsSubs.get_unique_id()
                                            ),
                                            beforeSend: function (xhr) {
                                                // add the nonce used for cookie authentication
                                                xhr.setRequestHeader('X-WP-Nonce', window.tds_js_globals.wpRestNonce);
                                            },
                                            cache: false,
                                            dataType: 'json',
                                            data: {
                                                subscriptionUserId: $tdsSubscriptionUserId.val(),
                                                subscriptionPlanId: $tdsSubscriptionPlanId.val(),
                                                billingPaymentMethod: $tdsBillingPaymentMethod.val(),
                                                billingFirstName: $tdsBillingFirstName.val(),
                                                billingLastName: $tdsBillingLastName.val(),
                                                billingCompanyName: $tdsBillingCompanyName.val(),
                                                billingVatNumber: $tdsBillingVatNumber.val(),
                                                billingCountry: $tdsBillingCountry.val(),
                                                billingAddress: $tdsBillingAddress.val(),
                                                billingCity: $tdsBillingCity.val(),
                                                billingCounty: $tdsBillingCounty.val(),
                                                billingPostcode: $tdsBillingPostcode.val(),
                                                billingPhone: $tdsBillingPhone.val(),
                                                billingEmail: $tdsBillingEmail.val(),
                                                paypalOrderId: orderData.id,
                                                paypalOrderIntent: orderData.intent,
                                                paypalOrderStatus: orderData.status,
                                                paypalOrderPayerId: orderData.payer.payer_id,
                                                paypalOrderPayerGivenName: orderData.payer.name.given_name,
                                                paypalOrderPayerSurname: orderData.payer.name.surname,
                                                paypalOrderPayerEmail: orderData.payer.email_address,
                                                paypalOrderPayeeId: orderData.purchase_units[0].payee.merchant_id,
                                                paypalOrderPayeeEmail: orderData.purchase_units[0].payee.email_address,
                                                paypalOrderAmountCurrencyCode: orderData.purchase_units[0].amount.currency_code,
                                                paypalOrderAmountValue: orderData.purchase_units[0].amount.value,
                                                paypalOrderInfo: JSON.stringify(orderData, null, 2),
                                                paypalOrderCreateTime: orderData.create_time,
                                                paypalOrderUpdateTime: orderData.update_time,
                                                paypalOrderCaptureCreateTime: orderData.purchase_units[0].payments.captures[0].create_time,
                                                paypalOrderCaptureUpdateTime: orderData.purchase_units[0].payments.captures[0].update_time,
                                            },
                                            success: function (data, textStatus, jqXHR) {

                                                console.log(data);

                                                if ('undefined' !== typeof data['error']) {
                                                    console.log(data['error']);

                                                } else if ('undefined' !== typeof data['response']) {

                                                    if ('undefined' !== typeof data['response']['confirm_url']) {
                                                        var response = data['response'];
                                                        redirectUrl = response['confirm_url'];
                                                        window.history.replaceState({}, 'TDS Confirmation Page', redirectUrl);

                                                        jQuery('.tds-payment-content').hide();
                                                        jQuery('.tds-payment-sidebar').hide();
                                                        jQuery('.tds-payment-confirmation').show();

                                                        // Subscription info section
                                                        var $tdsConfSubInfoWrap = jQuery('.tds-s-psc-subscr-info'),
                                                            $tdsConfSubInfoID = $tdsConfSubInfoWrap.find('.tds-s-subscr-id'),
                                                            $tdsConfSubInfoPlan = $tdsConfSubInfoWrap.find('.tds-s-subscr-plan'),
                                                            $tdsConfSubInfoMonths = $tdsConfSubInfoWrap.find('.tds-s-subscr-months'),
                                                            $tdsConfSubInfoPeriod = $tdsConfSubInfoWrap.find('.tds-s-subscr-period'),
                                                            $tdsConfSubInfoPayMethod = $tdsConfSubInfoWrap.find('.tds-s-subscr-pay-method'),
                                                            $tdsConfSubInfoTotal = $tdsConfSubInfoWrap.find('.tds-s-subscr-total');

                                                        $tdsConfSubInfoWrap.show();

                                                        if ('undefined' !== typeof response['local_subscription_id']) {
                                                            $tdsConfSubInfoID.find('.tds-s-list-text').html(response['local_subscription_id']);
                                                        }
                                                        if ('undefined' !== typeof response['local_plan_name']) {
                                                            $tdsConfSubInfoPlan.find('.tds-s-list-text').html(response['local_plan_name']);
                                                        }
                                                        if ('undefined' !== typeof response['last_months_in_cycle']) {
                                                            $tdsConfSubInfoMonths.find('.tds-s-list-text').html(response['last_months_in_cycle']);
                                                        }
                                                        if ('undefined' !== typeof response['start_date']) {
                                                            $tdsConfSubInfoPeriod.find('.tds-s-list-text').html(response['start_date'] + ' - ' + response['end_date']);
                                                        }
                                                        if ('undefined' !== typeof response['payment_type']) {
                                                            $tdsConfSubInfoPayMethod.find('.tds-s-list-text').html(response['payment_type']);
                                                        }
                                                        if ('undefined' !== typeof response['price']) {
                                                            $tdsConfSubInfoTotal.find('.tds-s-list-text').html(response['price'] + ' ' + response['curr_name']);
                                                        }
                                                    }
                                                }
                                            },

                                            error: function (MLHttpRequest, textStatus, errorThrown) {
                                                console.log('tds save settings - Error callback - textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
                                            }
                                        });


                                    });
                                }
                            }).render('#paypal-button-container');
                        } else {
                            jQuery().ready(function () {
                                var $el = jQuery('.tds-billing-payment-method[value="paypal"]');
                                if ($el.length) {
                                    $el.attr('disabled', true);
                                }
                            });
                        }

                    </script>

	                <?php
	                td_js_buffer::add_to_footer( "\n" . td_util::remove_script_tag( ob_get_clean() ) );
                }


                ob_start();
                ?>
                <script>
                    if ('undefined' === typeof tdsSubs) {

                        tdsSubs = {

                            init: function () {

                                jQuery().ready(function () {

                                    jQuery(document).on('click', '.tds-billing-complete', function (event) {

                                        event.preventDefault();

                                        var $this = jQuery(this),
                                            $tdsSubscriptionUserId = jQuery('#tds-subscription-user-id'),
                                            $tdsSubscriptionPlanId = jQuery('#tds-subscription-plan-id'),
                                            $tdsBillingFirstName = jQuery('#tds-billing-first-name'),
                                            $tdsBillingLastName = jQuery('#tds-billing-last-name'),
                                            $tdsBillingCompanyName = jQuery('#tds-billing-company-name'),
                                            $tdsBillingVatNumber = jQuery('#tds-billing-vat'),
                                            $tdsBillingCountry = jQuery('#tds-billing-country'),
                                            $tdsBillingAddress = jQuery('#tds-billing-address'),
                                            $tdsBillingCity = jQuery('#tds-billing-city'),
                                            $tdsBillingCounty = jQuery('#tds-billing-county'),
                                            $tdsBillingPostcode = jQuery('#tds-billing-postcode'),
                                            $tdsBillingPhone = jQuery('#tds-billing-phone'),
                                            $tdsBillingEmail = jQuery('#tds-billing-email'),
                                            $tdsBillingPaymentMethod = jQuery('.tds-billing-payment-method:checked'),
                                            $tdsSubscriptionUrl = jQuery('#tds-subscription-url');

                                        if ($tdsSubscriptionUrl.length) {
                                            subscriptionUrl = $tdsSubscriptionUrl.val();
                                        }

                                        $this.addClass('tds-s-btn-saving');

                                        jQuery.ajax({
                                            timeout: 10000,
                                            type: 'POST',
                                            url: tdsSubs.get_rest_endpoint(
                                                'tds_subscription/create_subscription',
                                                'uuid=' + tdsSubs.get_unique_id()
                                            ),
                                            beforeSend: function (xhr) {
                                                // add the nonce used for cookie authentication
                                                xhr.setRequestHeader('X-WP-Nonce', window.tds_js_globals.wpRestNonce);
                                            },
                                            cache: false,
                                            dataType: 'json',
                                            data: {
                                                subscriptionUserId: $tdsSubscriptionUserId.val(),
                                                subscriptionPlanId: $tdsSubscriptionPlanId.val(),
                                                billingPaymentMethod: $tdsBillingPaymentMethod.val(),
                                                billingFirstName: $tdsBillingFirstName.val(),
                                                billingLastName: $tdsBillingLastName.val(),
                                                billingCompanyName: $tdsBillingCompanyName.val(),
                                                billingVatNumber: $tdsBillingVatNumber.val(),
                                                billingCountry: $tdsBillingCountry.val(),
                                                billingAddress: $tdsBillingAddress.val(),
                                                billingCity: $tdsBillingCity.val(),
                                                billingCounty: $tdsBillingCounty.val(),
                                                billingPostcode: $tdsBillingPostcode.val(),
                                                billingPhone: $tdsBillingPhone.val(),
                                                billingEmail: $tdsBillingEmail.val(),
                                            },
                                            success: function (data, textStatus, jqXHR) {

                                                console.log(data);

                                                $this.removeClass('tds-s-btn-saving');

                                                var fields = [{ billingFirstName: $tdsBillingFirstName},
                                                    {billingLastName: $tdsBillingLastName},
                                                    {billingCountry: $tdsBillingCountry},
                                                    {billingAddress: $tdsBillingAddress},
                                                    {billingCity: $tdsBillingCity},
                                                    {billingCounty: $tdsBillingCounty},
                                                    {billingPostcode: $tdsBillingPostcode},
                                                    {billingPhone: $tdsBillingPhone},
                                                    {billingEmail: $tdsBillingEmail}];

                                                fields.forEach(el => {
                                                    Object.values(el)[0].closest('.tds-s-form-group').removeClass('tds-s-fg-error').find('.tds-s-fg-error-msg').html('')
                                                });

                                                if ('undefined' !== typeof data['error']) {
                                                    console.log(data['error']);

                                                    fields.forEach(el => {
                                                        if ('undefined' !== typeof data['error'][Object.keys(el)[0]]) {
                                                            Object.values(el)[0].closest('.tds-s-form-group').addClass('tds-s-fg-error').find('.tds-s-fg-error-msg').html(data['error'][Object.keys(el)[0]]);
                                                        }
                                                    });
                                                } else if ('undefined' !== typeof data['response'] && 'undefined' !== typeof data['response']['local_subscription_id'] ) {

                                                    if ('undefined' !== typeof data['response']['confirm_url']) {
                                                        var response = data['response'],
                                                            redirectUrl = response['confirm_url'];
                                                        window.history.replaceState({}, 'TDS Confirmation Page', redirectUrl);

                                                        jQuery('.tds-payment-content').hide();
                                                        jQuery('.tds-payment-sidebar').hide();
                                                        jQuery('.tds-payment-confirmation').show();


                                                        // Subscription info section
                                                        var $tdsConfSubInfoWrap = jQuery('.tds-s-psc-subscr-info'),
                                                            $tdsConfSubInfoID = $tdsConfSubInfoWrap.find('.tds-s-subscr-id'),
                                                            $tdsConfSubInfoPlan = $tdsConfSubInfoWrap.find('.tds-s-subscr-plan'),
                                                            $tdsConfSubInfoMonths = $tdsConfSubInfoWrap.find('.tds-s-subscr-months'),
                                                            $tdsConfSubInfoPeriod = $tdsConfSubInfoWrap.find('.tds-s-subscr-period'),
                                                            $tdsConfSubInfoPayMethod = $tdsConfSubInfoWrap.find('.tds-s-subscr-pay-method'),
                                                            $tdsConfSubInfoTotal = $tdsConfSubInfoWrap.find('.tds-s-subscr-total');

                                                        $tdsConfSubInfoWrap.show();

                                                        if ('undefined' !== typeof response['local_subscription_id']) {
                                                            $tdsConfSubInfoID.find('.tds-s-list-text').html(response['local_subscription_id']);
                                                        }
                                                        if ('undefined' !== typeof response['local_plan_name']) {
                                                            $tdsConfSubInfoPlan.find('.tds-s-list-text').html(response['local_plan_name']);
                                                        }
                                                        if ('undefined' !== typeof response['last_months_in_cycle']) {
                                                            $tdsConfSubInfoMonths.find('.tds-s-list-text').html(response['last_months_in_cycle']);
                                                            $tdsConfSubInfoMonths.show();
                                                        } else {
                                                            $tdsConfSubInfoMonths.hide();
                                                        }
                                                        if ('undefined' !== typeof response['start_date']) {
                                                            $tdsConfSubInfoPeriod.find('.tds-s-list-text').html(response['start_date'] + ' - ' + response['end_date']);
                                                        }
                                                        if ('undefined' !== typeof response['payment_type']) {
                                                            if( response['is_free'] === '0' ) {
                                                                $tdsConfSubInfoPayMethod.find('.tds-s-list-text').html(response['payment_type']);
                                                                $tdsConfSubInfoPayMethod.show();
                                                            }
                                                        } else {
                                                            $tdsConfSubInfoPayMethod.hide();
                                                        }
                                                        if ('undefined' !== typeof response['price']) {
                                                            $tdsConfSubInfoTotal.find('.tds-s-list-text').html(response['is_free'] === '0' ? ('$' + response['price']) : 'Free');
                                                        }


                                                        // Bank info section
                                                        if ('undefined' !== typeof response['payment_type']) {
                                                            var $tdsConfBankInfoWrap = jQuery('.tds-s-psc-bank-info'),
                                                                $tdsConfBankInfoName = $tdsConfBankInfoWrap.find('.tds-s-bank-name'),
                                                                $tdsConfBankInfoAccName = $tdsConfBankInfoWrap.find('.tds-s-bank-acc-name'),
                                                                $tdsConfBankInfoAccNumber = $tdsConfBankInfoWrap.find('.tds-s-bank-acc-number'),
                                                                $tdsConfBankInfoRouting = $tdsConfBankInfoWrap.find('.tds-s-bank-routing'),
                                                                $tdsConfBankInfoIBAN = $tdsConfBankInfoWrap.find('.tds-s-bank-iban'),
                                                                $tdsConfBankInfoBic = $tdsConfBankInfoWrap.find('.tds-s-bank-bic'),
                                                                $tdsConfBankInfoInstructions = $tdsConfBankInfoWrap.find('.tds-s-bank-instructions');

                                                            $tdsConfBankInfoWrap.show();

                                                            if ('undefined' !== typeof response['payment_bank']) {
                                                                $tdsConfBankInfoName.find('.tds-s-list-text').html(response['payment_bank']);
                                                                $tdsConfBankInfoName.show();
                                                            }
                                                            if ('undefined' !== typeof response['payment_account_name']) {
                                                                $tdsConfBankInfoAccName.find('.tds-s-list-text').html(response['payment_account_name']);
                                                                $tdsConfBankInfoAccName.show();
                                                            }
                                                            if ('undefined' !== typeof response['payment_account_number']) {
                                                                $tdsConfBankInfoAccNumber.find('.tds-s-list-text').html(response['payment_account_number']);
                                                                $tdsConfBankInfoAccNumber.show();
                                                            }
                                                            if ('undefined' !== typeof response['payment_routing_number']) {
                                                                $tdsConfBankInfoRouting.find('.tds-s-list-text').html(response['payment_routing_number']);
                                                                $tdsConfBankInfoRouting.show();
                                                            }
                                                            if ('undefined' !== typeof response['payment_iban']) {
                                                                $tdsConfBankInfoIBAN.find('.tds-s-list-text').html(response['payment_iban']);
                                                                $tdsConfBankInfoIBAN.show();
                                                            }
                                                            if ('undefined' !== typeof response['payment_bic_swift']) {
                                                                $tdsConfBankInfoBic.find('.tds-s-list-text').html(response['payment_bic_swift']);
                                                                $tdsConfBankInfoBic.show();
                                                            }
                                                            if ('undefined' !== typeof response['payment_instruction']) {
                                                                $tdsConfBankInfoInstructions.find('.tds-s-list-text').html(response['payment_instruction']);
                                                                $tdsConfBankInfoInstructions.show();
                                                            }
                                                        }
                                                    }
                                                }
                                            },

                                            error: function (MLHttpRequest, textStatus, errorThrown) {
                                                console.log('tds save settings - Error callback - textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
                                            }
                                        });

                                    }).on('change', '.tds-billing-payment-method', function(event) {
                                        var $this = jQuery(this),

                                            $paymentContentDirect = jQuery('.tds-spm-direct .tds-spm-content'),
                                            $paymentContentPaypal = jQuery('.tds-spm-paypal .tds-spm-content'),
                                            $paymentContentStripe = jQuery('.tds-spm-stripe .tds-spm-content'),

                                            $tdsBillingComplete = jQuery('.tds-billing-complete'),
                                            $paypalButtonContainer = jQuery('#paypal-button-container'),
                                            $stripeContainer = jQuery('#stripe-container');

                                        switch ($this.val()) {
                                            case 'direct':
                                                if ( $paymentContentDirect.length ) {
                                                    $paymentContentDirect.slideDown(200);
                                                }
                                                if ( $paymentContentPaypal.length ) {
                                                    $paymentContentPaypal.slideUp(200);
                                                }
                                                if ( $paymentContentStripe.length ) {
                                                    $paymentContentStripe.slideUp(200);
                                                }

                                                $tdsBillingComplete.show();
                                                $paypalButtonContainer.hide();
                                                $stripeContainer.hide();
                                                break;

                                            case 'paypal':
                                                if ( $paymentContentDirect.length ) {
                                                    $paymentContentDirect.slideUp(200);
                                                }
                                                if ( $paymentContentPaypal.length ) {
                                                    $paymentContentPaypal.slideDown(200);
                                                }
                                                if ( $paymentContentStripe.length ) {
                                                    $paymentContentStripe.slideUp(200);
                                                }

                                                $tdsBillingComplete.hide();
                                                $paypalButtonContainer.show();
                                                $stripeContainer.hide();
                                                break;

                                            case 'stripe':
                                                if ( $paymentContentDirect.length ) {
                                                    $paymentContentDirect.slideUp(200);
                                                }
                                                if ( $paymentContentPaypal.length ) {
                                                    $paymentContentPaypal.slideUp(200);
                                                }
                                                if ( $paymentContentStripe.length ) {
                                                    $paymentContentStripe.slideDown(200);
                                                }

                                                $tdsBillingComplete.hide();
                                                $paypalButtonContainer.hide();
                                                $stripeContainer.show();
                                                break;
                                        }
                                    });
                                });
                            },


                            // returns a full rest endpoint url..
                            get_rest_endpoint: function (restEndPoint, queryString) {

                                if (_.isEmpty(window.tds_js_globals.permalinkStructure)) {
                                    return window.tds_js_globals.wpRestUrl + restEndPoint + '&' + queryString; // no permalinks
                                } else {
                                    return window.tds_js_globals.wpRestUrl + restEndPoint + '?' + queryString; // we have permalinks enabled
                                }
                            },

                            // generates a unique ID
                            get_unique_id: function () {
                                function s4() {
                                    return Math.floor((1 + Math.random()) * 0x10000)
                                        .toString(16)
                                        .substring(1);
                                }

                                return s4() + s4() + '-' + s4() + '-' + s4() + '-' + s4() + '-' + s4() + s4() + s4();
                            }
                        };

                        tdsSubs.init();
                    }
                </script>

                <?php
                // js for frontend
                td_js_buffer::add_to_footer( "\n" . td_util::remove_script_tag( ob_get_clean() ) );


                if ( null !== $payment_stripe && count( $payment_stripe ) && $payment_stripe[ 0 ][ 'is_active' ] ) {
                    $in_testing = '';
                    if ($payment_stripe[ 0 ][ 'is_sandbox' ]) {
                        $in_testing = 'sandbox_';
                    }
                    $public_key = $payment_stripe[0][ $in_testing . 'public_key' ];
                    $buffy .= '<script src="https://js.stripe.com/v3/"></script>';

                    ob_start();
                    ?>
                    <script>

                        if ('undefined' === typeof tdsValidation) {
                            var tdsValidation = {
                                fields: [{
                                        billingFirstName: jQuery('#tds-billing-first-name'),
                                        emptyMsg: '<?php echo __td( 'Field empty', TD_THEME_NAME ) ?>'
                                    },
                                    {
                                        billingLastName: jQuery('#tds-billing-last-name'),
                                        emptyMsg: '<?php echo __td( 'Field empty', TD_THEME_NAME ) ?>'
                                    },
                                    {
                                        billingCountry: jQuery('#tds-billing-country'),
                                        emptyMsg: '<?php echo __td( 'Field empty', TD_THEME_NAME ) ?>'
                                    },
                                    {
                                        billingAddress: jQuery('#tds-billing-address'),
                                        emptyMsg: '<?php echo __td( 'Field empty', TD_THEME_NAME ) ?>'
                                    },
                                    {
                                        billingCity: jQuery('#tds-billing-city'),
                                        emptyMsg: '<?php echo __td( 'Field empty', TD_THEME_NAME ) ?>'
                                    },
                                    {
                                        billingCounty: jQuery('#tds-billing-county'),
                                        emptyMsg: '<?php echo __td( 'Field empty', TD_THEME_NAME ) ?>'
                                    },
                                    {
                                        billingPostcode: jQuery('#tds-billing-postcode'),
                                        emptyMsg: '<?php echo __td( 'Field empty', TD_THEME_NAME ) ?>'
                                    },
                                    {
                                        billingPhone: jQuery('#tds-billing-phone'),
                                        emptyMsg: '<?php echo __td( 'Field empty', TD_THEME_NAME ) ?>'
                                    },
                                    {
                                        billingEmail: jQuery('#tds-billing-email'),
                                        emptyMsg: '<?php echo __td( 'Field empty', TD_THEME_NAME ) ?>'
                                    }],

                                resetFields: function () {
                                    tdsValidation.fields.forEach(el => {
                                        Object.values(el)[0].closest('.tds-s-form-group').removeClass('tds-s-fg-error').find('.tds-s-fg-error-msg').html('');
                                    });
                                },

                                checkFields: function () {
                                    let result = {
                                        params: {},
                                        checked: true
                                    };

                                    tdsValidation.fields.forEach(el => {

                                        result.params[Object.keys(el)[0]] = Object.values(el)[0].val().trim();

                                        if ('' === Object.values(el)[0].val().trim()) {
                                            result.checked = false;
                                            Object.values(el)[0].closest('.tds-s-form-group').addClass('tds-s-fg-error').find('.tds-s-fg-error-msg').html(Object.values(el)[1]);
                                        }
                                    });

                                    return result;
                                }
                            }
                        }

                        const stripe = Stripe('<?php echo $public_key ?>');
                            $tdsStripeForm = jQuery('#tds-stripe-form');

                        if ($tdsStripeForm.length) {
                            $tdsStripeSubmit = jQuery('#tds-stripe-submit');

                            tdsCheckStripe();

                            $tdsStripeSubmit.on('click', tdsStripePayment);

                            jQuery.ajax({
                                timeout: 10000,
                                type: 'POST',
                                url: tdsSubs.get_rest_endpoint(
                                    'tds_subscription/get_stripe_info',
                                    'uuid=' + tdsSubs.get_unique_id()
                                ),
                                beforeSend: function (xhr) {
                                    // add the nonce used for cookie authentication
                                    xhr.setRequestHeader('X-WP-Nonce', window.tds_js_globals.wpRestNonce);
                                },
                                cache: false,
                                dataType: 'json',
                                data: {
                                    subscriptionPlanId: jQuery('#tds-subscription-plan-id').val(),
                                },
                                success: function (data, textStatus, jqXHR) {

                                    //console.log(data);

                                    if ('undefined' !== typeof data['error']) {
                                        //console.log(data['error']);

                                        tdsShowMessage(data['error'], 'error', true);

                                    } else if ('undefined' !== typeof data['stripe_info']) {

                                        elements = stripe.elements({clientSecret: data['stripe_info']});
                                        const paymentElement = elements.create("payment");

                                        paymentElement.mount("#tds-payment-element");

                                        setTimeout(function () {
                                            jQuery('#tds-payment-element').removeClass('loading');

                                            $tdsStripeSubmit.removeAttr('disabled');
                                        }, 200);
                                    }
                                },
                                error: function (MLHttpRequest, textStatus, errorThrown) {
                                    console.log('tds subscription stripe - Error callback - textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
                                }
                            });








                            // if ('undefined' === typeof stripe) {
                            //
                            //     alert(2);
                            //
                            // } else {
                            //
                            //     jQuery().ready(function () {
                            //         var $el = jQuery('.tds-billing-payment-method[value="stripe"]');
                            //         if ($el.length) {
                            //             $el.attr('disabled', true);
                            //         }
                            //     });
                            // }
                        }

                        async function tdsStripePayment(event) {
                            event.preventDefault();

                            let $this = jQuery(this);
                            $this.addClass('tds-s-btn-saving');
                            $this.attr('disabled', 'disabled');

                            tdsValidation.resetFields();
                            let checkFields = tdsValidation.checkFields();
                            if (!checkFields.checked) {
                                tdsShowMessage('Complete the required fields', 'warn');
                                $this.removeClass('tds-s-btn-saving');
                                $this.removeAttr('disabled');

                                return;
                            }

                            //console.log(checkFields.params);

                            checkFields.params = Object.assign(checkFields.params, {
                                subscriptionUserId: jQuery('#tds-subscription-user-id').val(),
                                subscriptionPlanId: jQuery('#tds-subscription-plan-id').val(),
                                billingCompanyName: jQuery('#tds-billing-company-name').val(),
                                billingVatNumber: jQuery('#tds-billing-vat').val(),
                            });

                            let current_url = new URL(window.location.href),
                                newSearchParams = new URLSearchParams([
                                    ...Array.from(current_url.searchParams.entries()),
                                    ...Object.entries(checkFields.params)
                                ]),
                                return_url = new URL(`${current_url.origin}${current_url.pathname}?${newSearchParams}`).toString();

                            const { error } = await stripe.confirmPayment({
                                elements,
                                confirmParams: {
                                    return_url: return_url,
                                },
                            });

                            console.log(error);

                            if ( -1 !== ["card_error", "validation_error"].indexOf('error.message')) {
                                tdsShowMessage(error.message, 'error');
                            } else {
                                tdsShowMessage('An unexpected error occurred!', 'error');
                            }

                            $this.removeClass('tds-s-btn-saving');
                            $this.removeAttr('disabled');
                        }

                        // Fetches the payment intent status after payment submission
                        async function tdsCheckStripe() {
                            const urlSearchParams = new URLSearchParams(window.location.search),
                                clientSecret = urlSearchParams.get("payment_intent_client_secret");

                            if (!clientSecret) {
                                return;
                            }

                            const subscriptionUserId = urlSearchParams.get("subscriptionUserId"),
                                subscriptionPlanId = urlSearchParams.get("subscriptionPlanId"),
                                billingFirstName = urlSearchParams.get("billingFirstName"),
                                billingLastName = urlSearchParams.get("billingLastName"),
                                billingCompanyName = urlSearchParams.get("billingCompanyName"),
                                billingVatNumber = urlSearchParams.get("billingVatNumber"),
                                billingCountry = urlSearchParams.get("billingCountry"),
                                billingAddress = urlSearchParams.get("billingAddress"),
                                billingCity = urlSearchParams.get("billingCity"),
                                billingCounty = urlSearchParams.get("billingCounty"),
                                billingPostcode = urlSearchParams.get("billingPostcode"),
                                billingPhone = urlSearchParams.get("billingPhone"),
                                billingEmail = urlSearchParams.get("billingEmail");

                            const { paymentIntent } = await stripe.retrievePaymentIntent(clientSecret);

                            //console.log(paymentIntent);

                            switch (paymentIntent.status) {

                                case 'succeeded':

                                    tdsShowMessage("Payment succeeded! The subscription is reqistered", 'success');

                                    jQuery.ajax({
                                        timeout: 10000,
                                        type: 'POST',
                                        url: tdsSubs.get_rest_endpoint(
                                            'tds_subscription/create_subscription',
                                            'uuid=' + tdsSubs.get_unique_id()
                                        ),
                                        beforeSend: function (xhr) {
                                            // add the nonce used for cookie authentication
                                            xhr.setRequestHeader('X-WP-Nonce', window.tds_js_globals.wpRestNonce);
                                        },
                                        cache: false,
                                        dataType: 'json',
                                        data: {
                                            subscriptionUserId: subscriptionUserId,
                                            subscriptionPlanId: subscriptionPlanId,
                                            billingPaymentMethod: 'stripe',
                                            billingFirstName: billingFirstName,
                                            billingLastName: billingLastName,
                                            billingCompanyName: billingCompanyName,
                                            billingVatNumber: billingVatNumber,
                                            billingCountry: billingCountry,
                                            billingAddress: billingAddress,
                                            billingCity: billingCity,
                                            billingCounty: billingCounty,
                                            billingPostcode: billingPostcode,
                                            billingPhone: billingPhone,
                                            billingEmail: billingEmail,
                                            stripePaymentIntent: paymentIntent.id,
                                            stripePaymentStatus: paymentIntent.status,
                                            stripePaymentInfo: JSON.stringify(paymentIntent),
                                        },
                                        success: function (data, textStatus, jqXHR) {

                                            console.log(data);

                                            if ('undefined' !== typeof data['error']) {
                                                console.log(data['error']);

                                            } else if ('undefined' !== typeof data['response']) {

                                                if ('undefined' !== typeof data['response']['confirm_url']) {
                                                    var response = data['response'];
                                                    redirectUrl = response['confirm_url'];
                                                    window.history.replaceState({}, 'TDS Confirmation Page', redirectUrl);

                                                    jQuery('.tds-payment-content').hide();
                                                    jQuery('.tds-payment-sidebar').hide();
                                                    jQuery('.tds-payment-confirmation').show();

                                                    // Subscription info section
                                                    var $tdsConfSubInfoWrap = jQuery('.tds-s-psc-subscr-info'),
                                                        $tdsConfSubInfoID = $tdsConfSubInfoWrap.find('.tds-s-subscr-id'),
                                                        $tdsConfSubInfoPlan = $tdsConfSubInfoWrap.find('.tds-s-subscr-plan'),
                                                        $tdsConfSubInfoMonths = $tdsConfSubInfoWrap.find('.tds-s-subscr-months'),
                                                        $tdsConfSubInfoPeriod = $tdsConfSubInfoWrap.find('.tds-s-subscr-period'),
                                                        $tdsConfSubInfoPayMethod = $tdsConfSubInfoWrap.find('.tds-s-subscr-pay-method'),
                                                        $tdsConfSubInfoTotal = $tdsConfSubInfoWrap.find('.tds-s-subscr-total');

                                                    $tdsConfSubInfoWrap.show();

                                                    if ('undefined' !== typeof response['local_subscription_id']) {
                                                        $tdsConfSubInfoID.find('.tds-s-list-text').html(response['local_subscription_id']);
                                                    }
                                                    if ('undefined' !== typeof response['local_plan_name']) {
                                                        $tdsConfSubInfoPlan.find('.tds-s-list-text').html(response['local_plan_name']);
                                                    }
                                                    if ('undefined' !== typeof response['last_months_in_cycle']) {
                                                        $tdsConfSubInfoMonths.find('.tds-s-list-text').html(response['last_months_in_cycle']);
                                                    }
                                                    if ('undefined' !== typeof response['start_date']) {
                                                        $tdsConfSubInfoPeriod.find('.tds-s-list-text').html(response['start_date'] + ' - ' + response['end_date']);
                                                    }
                                                    if ('undefined' !== typeof response['payment_type']) {
                                                        $tdsConfSubInfoPayMethod.find('.tds-s-list-text').html(response['payment_type']);
                                                    }
                                                    if ('undefined' !== typeof response['price']) {
                                                        $tdsConfSubInfoTotal.find('.tds-s-list-text').html(response['price'] + ' ' + response['curr_name']);
                                                    }
                                                }
                                            }
                                        },

                                        error: function (MLHttpRequest, textStatus, errorThrown) {
                                            console.log('tds save settings - Error callback - textStatus: ' + textStatus + ' errorThrown: ' + errorThrown);
                                        }
                                    });
                                    break;

                                case 'processing':
                                    tdsShowMessage("Your payment is processing.", 'info');
                                    break;

                                case 'requires_payment_method':
                                    tdsShowMessage("Your payment was not successful, please try again.", 'error');
                                    break;

                                default:
                                    tdsShowMessage("Something went wrong.", 'error');
                                    break;
                            }
                        }

                        function tdsShowMessage(messageText, type = '', permanent) {
                            const $tdsPaymentMessage = jQuery("#tds-payment-message"),
                                  $tdsPaymentMessageTxt = $tdsPaymentMessage.find('.tds-s-notif-descr');

                            $tdsPaymentMessage.show();
                            $tdsPaymentMessageTxt.html(messageText);

                            if ( type !== '' ) {
                                $tdsPaymentMessage.addClass('tds-s-notif-' + type);
                            }

                            if ('undefined' !== typeof permanent && true === permanent ) {
                                return;
                            }
                            setTimeout(function () {
                                $tdsPaymentMessage.hide();
                                $tdsPaymentMessageTxt.html('');

                                if ( type !== '' ) {
                                    $tdsPaymentMessage.removeClass('tds-s-notif-' + type);
                                }
                            }, 10000);
                        }

                    </script>

                    <?php
                    td_js_buffer::add_to_footer( "\n" . td_util::remove_script_tag( ob_get_clean() ) );

                }


                $plan_id = '';
                if ( ! empty( $_REQUEST[ 'plan_id' ] ) ) {
                    $plan_id = $_REQUEST[ 'plan_id' ];
                }

                ob_start();
                ?>

                <div class="tds-payment-content">
                    <div class="tds-s-page-sec tds-s-page-billing-details">
                        <div class="tds-s-page-sec-header">
                            <h2 class="tds-spsh-title"><?php echo (empty($plan_price)) ? __td('User information', TD_THEME_NAME) : __td('Billing details', TD_THEME_NAME) ?></h2>
                        </div>

                        <div class="tds-s-page-sec-content">
                            <div class="tds-s-form tds-s-billing-details-form">
                                <div class="tds-s-form-content">
                                    <div class="tds-s-fc-inner">
                                        <?php if ( ! empty( $_REQUEST[ 'ref_url' ] ) ) { ?>
                                            <input type="hidden" id="tds-ref-url" value="<?php echo $_REQUEST[ 'ref_url' ] ?>">
                                        <?php } ?>
                                        <input type="hidden" id="tds-subscription-user-id" value="<?php echo get_current_user_id() ?>">
                                        <input type="hidden" id="tds-subscription-plan-id" value="<?php echo $plan_id ?>">

                                        <div class="tds-s-form-group">
                                            <label class="tds-s-form-label" for="tds-billing-first-name"><?php echo __td('First name', TD_THEME_NAME) ?> *</label>
                                            <input class="tds-s-form-input tds-billing-first-name" type="text" id="tds-billing-first-name" <?php echo $is_composer ? 'value="John"' : '' ?>>
                                            <span class="tds-s-fg-error-msg"></span>
                                        </div>

                                        <div class="tds-s-form-group">
                                            <label class="tds-s-form-label" for="tds-billing-last-name"><?php echo __td('Last name', TD_THEME_NAME) ?> *</label>
                                            <input class="tds-s-form-input tds-billing-last-name" type="text" id="tds-billing-last-name" <?php echo $is_composer ? 'value="Doe"' : '' ?>>
                                            <span class="tds-s-fg-error-msg"></span>
                                        </div>

                                        <div class="tds-s-form-group">
                                            <label class="tds-s-form-label" for="tds-billing-company-name"><?php echo __td('Company name', TD_THEME_NAME) . ' ' . __td('(optional)', TD_THEME_NAME) ?> </label>
                                            <input class="tds-s-form-input tds-billing-company-name" type="text" id="tds-billing-company-name" <?php echo $is_composer ? 'value="Demo company name"' : '' ?>>
                                        </div>

                                        <div class="tds-s-form-group">
                                            <label class="tds-s-form-label" for=tds-billing-vat"><?php echo __td('VAT Number', TD_THEME_NAME) . ' ' . __td('(optional)', TD_THEME_NAME) ?> </label>
                                            <input class="tds-s-form-input tds-billing-vat" type="text" id="tds-billing-vat" <?php echo $is_composer ? 'value="123456"' : '' ?>>
                                        </div>

                                        <div class="tds-s-form-group">
                                            <label class="tds-s-form-label" for="tds-billing-country"><?php echo __td('Country/Region', TD_THEME_NAME) ?> *</label>
                                            <input class="tds-s-form-input tds-billing-country" type="text" id="tds-billing-country" <?php echo $is_composer ? 'value="United States"' : '' ?>>
                                            <span class="tds-s-fg-error-msg"></span>
                                        </div>

                                        <div class="tds-s-form-group <?php echo $is_composer ? 'tds-s-fg-error' : '' ?>">
                                            <label class="tds-s-form-label" for="tds-billing-address"><?php echo __td('Street address', TD_THEME_NAME) ?> *</label>
                                            <input class="tds-s-form-input tds-billing-address" type="text" id="tds-billing-address">
                                            <span class="tds-s-fg-error-msg"><?php echo $is_composer ? 'Empty address' : '' ?></span>
                                        </div>

                                        <div class="tds-s-form-group">
                                            <label class="tds-s-form-label" for="tds-billing-city"><?php echo __td('Town/City', TD_THEME_NAME) ?> *</label>
                                            <input class="tds-s-form-input tds-billing-city" type="text" id="tds-billing-city" <?php echo $is_composer ? 'value="New York"' : '' ?>>
                                            <span class="tds-s-fg-error-msg"></span>
                                        </div>

                                        <div class="tds-s-form-group">
                                            <label class="tds-s-form-label" for="tds-billing-county"><?php echo __td('County', TD_THEME_NAME) ?> *</label>
                                            <input class="tds-s-form-input tds-billing-county" type="text" id="tds-billing-county" <?php echo $is_composer ? 'value="New York"' : '' ?>>
                                            <span class="tds-s-fg-error-msg"></span>
                                        </div>

                                        <div class="tds-s-form-group">
                                            <label class="tds-s-form-label" for="tds-billing-postcode"><?php echo __td('Postcode', TD_THEME_NAME) ?> *</label>
                                            <input class="tds-s-form-input tds-billing-postcode" type="text" id="tds-billing-postcode" <?php echo $is_composer ? 'value="123456"' : '' ?>>
                                            <span class="tds-s-fg-error-msg"></span>
                                        </div>

                                        <div class="tds-s-form-group">
                                            <label class="tds-s-form-label" for="tds-billing-phone"><?php echo __td('Phone', TD_THEME_NAME) ?> *</label>
                                            <input class="tds-s-form-input tds-billing-phone" type="text" id="tds-billing-phone" <?php echo $is_composer ? 'value="+30 789546548"' : '' ?>>
                                            <span class="tds-s-fg-error-msg"></span>
                                        </div>

                                        <div class="tds-s-form-group">
                                            <label class="tds-s-form-label" for="tds-billing-email"><?php echo __td('Email', TD_THEME_NAME) ?> *</label>
                                            <input class="tds-s-form-input tds-billing-email" type="text" id="tds-billing-email" <?php echo $is_composer ? 'value="mail@example.com"' : '' ?>>
                                            <span class="tds-s-fg-error-msg"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tds-payment-sidebar">
                    <div class="tds-s-page-sec tds-s-page-subscr-summary">
                        <div class="tds-s-page-sec-header">
                            <h2 class="tds-spsh-title"><?php echo __td('Subscription summary', TD_THEME_NAME) ?></h2>
                        </div>

                        <div class="tds-s-page-sec-content">
                            <table class="tds-s-table tds-s-table-subscr-summary <?php echo (empty($plan_price)) ? 'tds-s-table-subscr-summary-free' : 'tds-s-table-subscr-summary-paid' ?>">
                                <thead class="tds-s-table-header">
                                    <tr class="tds-s-table-row tds-s-table-row-h">
                                        <th class="tds-s-table-col"><?php echo __td('Plan', TD_THEME_NAME) ?></th>
                                        <?php
                                        if (!empty($plan_price)) {
	                                        ?>
                                            <th class="tds-s-table-col"><?php echo __td('Months', TD_THEME_NAME) ?></th>
	                                        <?php
                                        }
                                        ?>
                                        <th class="tds-s-table-col"><?php echo __td('Period', TD_THEME_NAME) ?></th>
                                    </tr>
                                </thead>

                                <tbody class="tds-s-table-body">
                                    <tr class="tds-s-table-row">
                                        <td class="tds-s-table-col">
                                            <div class="tds-s-table-col-label"><?php echo __td('Plan', TD_THEME_NAME) ?></div>
                                            <?php echo $plan_name ?>
                                        </td>
                                        <?php
                                        if (!empty($plan_price)) {
	                                        ?>
                                            <td class="tds-s-table-col">
                                                <div class="tds-s-table-col-label"><?php echo __td('Months', TD_THEME_NAME) ?></div>
		                                        <?php echo $plan_months_in_cycle ?>
                                            </td>
	                                        <?php
                                        }
                                        ?>
                                        <td class="tds-s-table-col">
                                            <div class="tds-s-table-col-label"><?php echo __td('Period', TD_THEME_NAME) ?></div>
                                            <?php echo tds_util::get_formatted_date(date('Y-m-d')) ?> - <?php echo !empty($plan_price) ? tds_util::get_formatted_date(tds_util::get_end_date(date('Y-m-d'), $plan_months_in_cycle, date('j'))->format('Y-m-d')) : __td('unlimited', TD_THEME_NAME) ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="tds-s-subscr-summary-total">
                                <div class="tds-s-sst-label"><?php echo __td('Total', TD_THEME_NAME) ?>:</div>
                                <div class="tds-s-sst-val"><?php echo (empty($plan_price)) ? __td('Free', TD_THEME_NAME) : tds_util::get_basic_currency($plan_price) ?></div>
                            </div>
                        </div>
                    </div>

                    <?php

                    if (empty($plan_price)) {

                        ?>

                        <div class="tds-s-page-sec tds-s-page-pay-methods">
                            <div class="tds-s-page-sec-content">
			                    <div class="tds-s-form tds-s-pay-methods-form">
                                    <div class="tds-s-form-footer">
                                        <button class="tds-s-btn tds-billing-complete" <?php echo empty( $tds_subscription_url ) ? '' : 'data-url="' . esc_url( $tds_subscription_url ) . '"' ?>>
                                            <?php echo __td('Subscribe', TD_THEME_NAME) ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php

                    } else {

	                    ?>

                        <div class="tds-s-page-sec tds-s-page-pay-methods">
                            <div class="tds-s-page-sec-header">
                                <h2 class="tds-spsh-title"><?php echo __td('Payment methods', TD_THEME_NAME) ?></h2>
                            </div>

                            <div class="tds-s-page-sec-content">
			                    <?php

			                    if ( ( null !== $payment_bank && count( $payment_bank ) && $payment_bank[ 0 ][ 'is_active' ] )
                                     || ( ! empty( $payment_paypal ) && '1' === $payment_paypal[ 'is_active' ] )
                                     || ( null !== $payment_stripe && count( $payment_stripe ) && $payment_stripe[ 0 ][ 'is_active' ] ) ) {

				                    $payment_bank_checked   = false;
				                    $payment_paypal_checked = false;
				                    $payment_stripe_checked = false;

				                    ?>

                                    <div class="tds-s-form tds-s-pay-methods-form">
                                        <div class="tds-s-form-content">
                                            <div class="tds-s-fc-inner">
							                    <?php

							                    if ( null !== $payment_bank && count( $payment_bank ) && $payment_bank[ 0 ][ 'is_active' ] ) {
								                    $allow_subscribe      = true;
								                    $payment_bank_checked = true;

                                                    $payment_bank_description = $is_composer ? 'Sample bank payment description.' : $payment_bank[ 0 ][ 'description' ];
                                                    $payment_bank_instruction = $is_composer ? 'Sample bank payment instructions.' : $payment_bank[ 0 ][ 'instruction' ];

								                    ?>

                                                    <div class="tds-s-form-group tds-spm-direct">
                                                        <div class="tds-s-form-check">
                                                            <label>
                                                                <input type="radio"
                                                                       name="tds-payment-method"
                                                                       class="tds-billing-payment-method" value="direct"
                                                                       checked>

                                                                <span class="tds-s-fc-check"></span>
                                                                <span class="tds-s-fc-title">Direct Bank Transfer</span>
                                                            </label>
                                                        </div>

                                                        <div class="tds-spm-content">
                                                            <?php
                                                            if ( ! empty( $payment_bank_description ) ) {
                                                                ?>

                                                                <div class="tds-s-notif tds-s-notif-sm tds-s-notif-info">
                                                                    <div class="tds-s-notif-descr"><?php echo $payment_bank_description ?></div>
                                                                </div>

                                                                <?php
                                                            }

                                                            if ( ! empty( $payment_bank_instruction ) ) {
                                                                ?>

                                                                <div class="tds-s-notif tds-s-notif-sm tds-s-notif-info">
                                                                    <div class="tds-s-notif-descr"><?php echo $payment_bank_instruction ?></div>
                                                                </div>

                                                                <?php
                                                            }
                                                            ?>
                                                        </div>
                                                    </div>

								                    <?php
							                    }

							                    tds_util::check_paypal_currency($curr_name, $is_paypal, $is_digit );
							                    if (!$is_digit && ! preg_match('/^\d+$/', $plan_price)) {
							                        $is_paypal = false;
                                                }

							                    if ( ( $is_paypal && ! empty( $payment_paypal ) && '1' === $payment_paypal[ 'is_active' ] ) || $is_composer ) {
								                    if ( ! $payment_bank_checked ) {
									                    $payment_paypal_checked = true;
								                    }

								                    ?>

                                                    <div class="tds-s-form-group tds-spm-paypal">
                                                        <div class="tds-s-form-check">
                                                            <label>
                                                                <input type="radio"
                                                                       name="tds-payment-method"
                                                                       class="tds-billing-payment-method"
                                                                       value="paypal" <?php echo $payment_paypal_checked ? 'checked' : '' ?>>

                                                                <span class="tds-s-fc-check"></span>
                                                                <span class="tds-s-fc-title">PayPal</span>
                                                            </label>
                                                        </div>
                                                    </div>

								                    <?php
							                    }

							                    tds_util::check_stripe_currency($curr_name, $is_stripe, $is_digit );
							                    if (!$is_digit && ! preg_match('/^\d+$/', $plan_price)) {
							                        $is_stripe = false;
                                                }

							                    if (( $is_stripe && null !== $payment_stripe && count( $payment_stripe ) && $payment_stripe[ 0 ][ 'is_active' ] ) || $is_composer ) {
								                    if ( ! $payment_bank_checked && ! $payment_paypal_checked) {
									                    $payment_stripe_checked = true;
								                    }

								                    ?>

                                                    <div class="tds-s-form-group tds-spm-stripe">
                                                        <div class="tds-s-form-check">
                                                            <label>
                                                                <input type="radio"
                                                                       name="tds-payment-method"
                                                                       class="tds-billing-payment-method"
                                                                       value="stripe" <?php echo $payment_stripe_checked ? 'checked' : '' ?>>

                                                                <span class="tds-s-fc-check"></span>
                                                                <span class="tds-s-fc-title">Stripe</span>
                                                            </label>
                                                        </div>
                                                    </div>

								                    <?php
							                    }

							                    ?>
                                            </div>
                                        </div>

                                        <div class="tds-s-form-footer">
                                            <button class="tds-s-btn tds-billing-complete" <?php echo empty( $tds_subscription_url ) ? '' : 'data-url="' . esc_url( $tds_subscription_url ) . '"' ?> <?php echo $payment_paypal_checked ? 'style="display:none"' : '' ?>>
                                                <?php echo __td('Subscribe', TD_THEME_NAME) ?>
                                            </button>

						                    <?php

						                    if ( ! empty( $payment_paypal ) && '1' === $payment_paypal[ 'is_active' ] ) {
							                    ?>

                                                <div id="paypal-button-container" <?php echo ! $payment_paypal_checked ? 'style="display:none"' : '' ?>></div>

							                    <?php
						                    }

						                    if ( null !== $payment_stripe && count( $payment_stripe ) && $payment_stripe[ 0 ][ 'is_active' ] ) {
							                    ?>

                                                <div id="stripe-container" <?php echo ! $payment_stripe_checked ? 'style="display:none"' : '' ?>>
                                                    <form id="tds-stripe-form">
                                                        <div id="tds-payment-element" class="loading"></div>
                                                        <button id="tds-stripe-submit" class="tds-s-btn" disabled="disabled">Pay with Stripe</button>
                                                        <div id="tds-payment-message" class="tds-s-notif tds-s-notif-sm"><div class="tds-s-notif-descr"></div></div>
                                                    </form>
                                                </div>

							                    <?php
						                    }

						                    ?>
                                        </div>
                                    </div>

				                    <?php

			                    } else {
				                    ?>

                                    <div class="tds-s-notif tds-s-notif-sm tds-s-notif-info">
                                        <div class="tds-s-notif-descr"><?php echo __td('It seems that no available payment methods have
                                            been configured.', TD_THEME_NAME) ?>
                                        </div>
                                    </div>

				                    <?php
			                    }

			                    ?>
                            </div>
                        </div>

	                    <?php
                    }
                    ?>
                </div>

                <?php
                $buffy .= ob_get_clean();

                $buffy .= $payment_subscription;

            $buffy .= '</div>';

		$buffy .= '</div>';

		return $buffy;
	}
}
