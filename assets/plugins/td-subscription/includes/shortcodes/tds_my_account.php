<?php

/**
 * Class tds_my_account
 */

class tds_my_account extends td_block {

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
                
                /* @style_general_tds_my_account */
                .tds_my_account .tds-my-account-wrap {
                    display: flex;
                    align-items: stretch;
                }
                @media (max-width: 767px) {
                    .tds_my_account .tds-my-account-wrap {
                        flex-direction: column;
                    }
                }
                .tds_my_account .tds-s-acc-sidebar {
                    width: 200px;
                    padding-right: 25px;
                    border-width: 0 1px 0 0;
                    border-style: solid;
                    border-color: #D0D4FE;
                }
                @media (min-width: 768px) and (max-width: 1018px) {
                    .tds_my_account .tds-s-acc-sidebar {
                        width: 170px;
                        padding-right: 15px;
                    }
                }
                @media (max-width: 767px) {
                    .tds_my_account .tds-s-acc-sidebar {
                        width: 100%;
                        padding-bottom: 35px;
                        padding-right: 0;
                        border-width: 0 0 1px;
                    }
                }
                .tds_my_account .tds-s-acc-content {
                    flex: 1;
                    padding-left: 35px;
                }
                @media (min-width: 768px) and (max-width: 1018px) {
                    .tds_my_account .tds-s-acc-content {
                        padding-left: 25px;
                    }
                }
                @media (max-width: 767px) {
                    .tds_my_account .tds-s-acc-content {
                        padding-top: 35px;
                        padding-left: 0;
                    }
                }
                .tds_my_account .tds-s-acc-content .tds-s-notif:not(:last-child) {
                    margin-bottom: 40px;
                }
                .tds_my_account .tds-s-acc-user {
                    display: flex;
                    align-items: center;
                    margin-bottom: 25px;
                }
                @media (min-width: 768px) and (max-width: 1018px) {
                    .tds_my_account .tds-s-acc-user {
                        margin-bottom: 18px;
                    }
                }
                .tds_my_account .tds-sau-avatar {
                    margin-right: 12px;
                    background-size: cover;
                    background-position: center;
                    width: 32px;
                    height: 32px;
                    border-radius: 100%;
                }
                @media (min-width: 768px) and (max-width: 1018px) {
                    .tds_my_account .tds-sau-avatar {
                        width: 26px;
                        height: 26px;
                        margin-right: 10px;
                    }
                }
                .tds_my_account .tds-sau-name {
                    flex: 1;
                    font-size: 1.286em;
                    line-height: 1.2;
                    font-weight: 600;
                    color: #1D2327;
                }
                @media (min-width: 768px) and (max-width: 1018px) {
                    .tds_my_account .tds-sau-name {
                        font-size: 1.143em;
                    }
                }
                .tds_my_account a.tds-san-item {
                    display: flex;
                    align-items: center;
                    position: relative;
                    font-size: 1em;
                    line-height: 1.3;
                    font-weight: 600;
                    color: #1D2327;
                    transition: color .2s ease-in-out;
                }
                .tds_my_account a.tds-san-item:not(:last-child) {
                    margin-bottom: 18px;
                }
                @media (min-width: 768px) and (max-width: 1018px) {
                    .tds_my_account a.tds-san-item {
                        font-size: .857em;
                    }
                    .tds_my_account a.tds-san-item:not(:last-child) {
                        margin-bottom: 14px;
                    }
                }
                .tds_my_account a.tds-san-item:after {
                    content: '';
                    position: absolute;
                    top: 0;
                    right: -26px;
                    width: 3px;
                    height: 100%;
                    background-color: transparent;
                    transition: background-color .2s ease-in-out;
                }
                @media (min-width: 768px) and (max-width: 1018px) {
                    .tds_my_account a.tds-san-item:after {
                        right: -16px;
                    }
                }
                @media (max-width: 767px) {
                    .tds_my_account a.tds-san-item:after {
                        display: none;
                    }
                }
                .tds_my_account a.tds-san-item:hover {
                    color: #152BF7;
                }
                .tds_my_account a.tds-san-item:hover:after {
                    background-color: #D0D4FE;
                }
                .tds_my_account a.tds-san-item.tds-san-item-active {
                    color: #152BF7;
                }
                .tds_my_account a.tds-san-item.tds-san-item-active:after {
                    background-color: #152BF7;
                }
                .tds_my_account .tds-san-item-icon {
                    position: relative;
                    width: 26px;
                    height: 26px;
                    margin-right: 14px;
                    background-color: #F3F4FF;
                    border-radius: 2px;
                    transition: background-color .2s ease-in-out;
                }
                @media (min-width: 768px) and (max-width: 1018px) {
                    .tds_my_account .tds-san-item-icon {
                        width: 22px;
                        height: 22px;
                        margin-right: 10px;
                    }
                }
                .tds_my_account .tds-san-item-icon svg {
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    stroke: #7b81b9;
                    transition: stroke .2s ease-in-out;
                }
                @media (min-width: 768px) and (max-width: 1018px) {
                    .tds_my_account .tds-san-item-icon svg {
                        width: 14px;
                        height: auto;
                    }
                }
                .tds_my_account .tds-san-item:hover .tds-san-item-icon,
                .tds_my_account .tds-san-item-active .tds-san-item-icon {
                    background-color: #E5F3FF;
                }
                .tds_my_account .tds-san-item:hover .tds-san-item-icon svg,
                .tds_my_account .tds-san-item-active .tds-san-item-icon svg {
                    stroke: #152BF7;
                }
                .tds_my_account .tds-s-notif-acc-activation {
                    display: flex;
                    align-items: center;
                    margin-bottom: 40px;
                }
                .tds_my_account .tds-s-notif-acc-activation .tds-s-notif-descr {
                    flex: 1;
                    margin-bottom: 0;
                }
                @media (min-width: 768px) {
                    .tds_my_account .tds-s-acc-info-form .tds-s-form-group:nth-last-of-type(-n+3) {
                        margin-bottom: 0;
                    }
                    .tds_my_account .tds-s-acc-info-form .tds-s-form-group:nth-of-type(-n+4) {
                        width: 50%;
                    }
                    .tds_my_account .tds-s-acc-info-form .tds-s-form-group:nth-last-of-type(-n+3) {
                        width: 33.333%;
                    }
                }
                @media (min-width: 1019px) {
                    .tds_my_account .tds-s-table-subscr .tds-s-table-col-pp-btn-none {
                        padding-right: 0;
                    }
                    .tds_my_account .tds-s-table-subscr .tds-s-table-col-pp-btn .tds-paypal-button > div {
                        min-width: 75px !important;
                    }
                }
                @media (max-width: 1018px) {
                    .tds_my_account .tds-s-table-subscr .tds-s-table-col-pp-btn {
                        align-items: flex-start;
                    }
                    .tds_my_account .tds-s-table-subscr .tds-s-table-col-pp-btn-none {
                        display: none;
                    }
                }
                .tds_my_account .tds-s-table-subscr .tds-s-table-status-active {
                    background-color: #E2F3DF;
                    color: #317A25;
                }
                .tds_my_account .tds-s-table-subscr .tds-s-table-status-free,
                .tds_my_account .tds-s-table-subscr .tds-s-table-status-trial {
                    background-color: #FFF1B4;
                    color: #ee8302;
                }
                .tds_my_account .tds-s-table-subscr .tds-s-table-status-canceled,
                .tds_my_account .tds-s-table-subscr .tds-s-table-status-not-paid {
                    background-color: #FCE8E8;
                    color: #FF0000;
                }
                .tds_my_account .tds-s-table-subscr .tds-s-table-status-waiting {
                    background-color: #E5F3FF;
                    color: #152BF7;
                }
                .tds_my_account .tds-s-table-subscr .tds-s-table-row-extra .tds-s-list-item {
                    font-size: 1em;
                    line-height: 1.2;
                }
                .tds_my_account .tds-s-table-subscr .tds-s-table-row-extra .tds-s-list-item:not(:last-child) {
                    margin-bottom: 8px;
                }
                @media (min-width: 1019px) {
                    .tds_my_account .tds-s-table-subscr .tds-s-table-row-extra .tds-s-tre-subscr-info {
                        flex: 1;
                    }
                    .tds_my_account .tds-s-table-subscr .tds-s-table-row-extra .tds-s-tre-pay-info {
                        width: 50%;
                    }
                }
                .tds_my_account .tds-s-table-subscr .tds-s-table-row-extra .tds-s-list-label {
                    font-weight: 400;
                }
                @media (max-width: 1018px) {
                    .tds_my_account .tds-s-table-subscr .tds-s-tre-pay-info {
                        margin-top: 24px;
                    }
                }
                .tds_my_account .tds-stripe-button {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    width: 100%;
                    height: 25px;
                    padding-bottom: 1px;
                    background-color: #635bff;
                    font-size: .923em;
                    color: #fff;
                    border-radius: 4px;
                    cursor: pointer;
                    padding: 0 10px 1px;
                }
                .tds_my_account .tds-stripe-button:hover,
                .tds_my_account .tds-s-table-row-stripe-expanded .tds-stripe-button {
                    background-color: #0a2540;
                }
                .tds_my_account .tds-s-table-row-stripe-expanded  {
                    border-bottom: none !important;
                }
                .tds_my_account .tds-s-table-row-stripe-expanded .tds-stripe-button {
                    pointer-events: none;
                    opacity: .5;
                }
                .tds_my_account #tds-payment-element {
                    position: relative;
                    min-height: 40px;
                }
                .tds_my_account #tds-payment-element:before,
                .tds_my_account #tds-payment-element:after {
                    content: '';
                    position: absolute;
                    opacity: 0;
                    transition: opacity .2s ease-in-out;
                    pointer-events: none;
                }
                .tds_my_account #tds-payment-element:before {
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(255, 255, 255, .8);
                    z-index: 1;
                }
                .tds_my_account #tds-payment-element:after {
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
                .tds_my_account #tds-payment-element.loading:before,
                .tds_my_account #tds-payment-element.loading:after {
                    opacity: 1;
                    pointer-events: auto;
                }
                .tds_my_account #tds-payment-message {
                    display: none;
                    margin-top: 18px;
                    margin-bottom: 0;
                }
                .tds_my_account .tds-stripe-form-btns {
                    display: flex;
                    margin-top: 18px;
                }
                .tds_my_account .tds-stripe-form-btns button:not(:last-child) {
                    margin-right: 16px;
                }
                
                
                /* @tip_radius */
                body .$unique_block_class .tds-s-form .tds-s-form-tip-txt {
                    border-radius: @tip_radius;
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
                body .$unique_block_class.tds-page-block a:not(.tds-s-btn):not(.tds-san-item),
                body .$unique_block_class a.tds-san-item:hover,
                body .$unique_block_class a.tds-san-item.tds-san-item-active,
                body .$unique_block_class .tds-s-btn-hollow {
                    color: @accent_color;
                }
                body .$unique_block_class .tds-s-btn:not(.tds-s-btn-hollow),
                body .$unique_block_class a.tds-san-item.tds-san-item-active:after {
                    background-color: @accent_color;
                }
                body .$unique_block_class .tds-s-btn-hollow {
                    border-color: @accent_color;
                }
                body .$unique_block_class .tds-s-form .tds-s-form-group:not(.tds-s-fg-error) .tds-s-form-input:focus:not([readonly]) {
                    border-color: @accent_color !important;
                }
                body .$unique_block_class  .tds-san-item:hover .tds-san-item-icon svg,
                body .$unique_block_class  .tds-san-item-active .tds-san-item-icon svg {
                    stroke: @accent_color;
                }
                /* @input_outline_accent_color */
                body .$unique_block_class .tds-s-form .tds-s-form-group:not(.tds-s-fg-error) .tds-s-form-input:focus:not([readonly]) {
                    outline-color: @input_outline_accent_color;
                }
                
                /* @a_color_h */
                body .$unique_block_class.tds-page-block a:not(.tds-s-btn):not(.tds-san-item):hover {
                    color: @a_color_h;
                }
                
                /* @sec_color */
                body .$unique_block_class h2.tds-spsh-title,
                body .$unique_block_class .tds-sau-name {
                    color: @sec_color;
                }
                /* @sec_descr_color */
                body .$unique_block_class .tds-spsh-descr {
                    color: @sec_descr_color;
                }
                
                /* @sep_color */
                body .$unique_block_class .tds-s-acc-sidebar {
                    border-color: @sep_color;
                }
                body .$unique_block_class a.tds-san-item:hover:after {
                    background-color: @sep_color;
                }
                /* @nav_color */
                body .$unique_block_class a.tds-san-item {
                    color: @nav_color;
                }
                /* @nav_ico_color */
                body .$unique_block_class .tds-san-item-icon svg {
                    stroke: @nav_ico_color;
                }
                /* @nav_ico_bg */
                body .$unique_block_class .tds-san-item-icon {
                    background-color: @nav_ico_bg;
                }
                /* @nav_ico_bg_h */
                body .$unique_block_class .tds-san-item:hover .tds-san-item-icon,
                body .$unique_block_class .tds-san-item-active .tds-san-item-icon {
                    background-color: @nav_ico_bg_h;
                }
                
                /* @label_color */
                body .$unique_block_class .tds-s-form .tds-s-form-label,
                body .$unique_block_class .tds-s-form .tds-s-form-check .tds-s-fc-title {
                    color: @label_color;
                }
                /* @tip_color */
                body .$unique_block_class .tds-s-form .tds-s-form-content .tds-s-form-tip-txt {
                    color: @tip_color;
                }
                /* @tip_bg */
                body .$unique_block_class .tds-s-form .tds-s-form-content .tds-s-form-tip-txt {
                    background-color: @tip_bg;
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
                
                /* @tabl_border_color */
                body .$unique_block_class .tds-s-table-header, 
                body .$unique_block_class .tds-s-table-row:not(:last-child),
                body .$unique_block_class .tds-s-table-row-extra-wrap:not(:last-child) {
                    border-bottom-color: @tabl_border_color;
                }
                /* @tabl_head_color */
                body .$unique_block_class .tds-s-table-header {
                    color: @tabl_head_color;
                }
                /* @tabl_body_color */
                body .$unique_block_class .tds-s-table-body {
                    color: @tabl_body_color;
                }
                body .$unique_block_class .tds-s-table-expand-toggle {
                    fill: @tabl_body_color;
                }
                /* @tabl_hover_bg */
                body .$unique_block_class .tds-s-table-row.tds-s-table-row-active,
                body .$unique_block_class .tds-s-table-body .tds-s-table-row:hover,
                body .$unique_block_class .tds-s-table-row-extra {
                    background-color: @tabl_hover_bg;
                }
                body .$unique_block_class .tds-s-table-row-extra {
                    background-color: @tabl_hover_bg !important;
                }
                /* @tabl_info_bg */
                body .$unique_block_class .tds-s-table-row-extra-inner {
                    background-color: @tabl_info_bg;
                }
                
                /* @list_label_color */
                body .$unique_block_class .tds-s-list-label {
                    color: @list_label_color;
                }
                /* @list_val_color */
                body .$unique_block_class .tds-s-list-title,
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
                body .$unique_block_class .tds-s-btn:not(.tds-s-btn-hollow):hover {
                    background-color: @btn_bg_h;
                }
                body .$unique_block_class .tds-s-btn-hollow:hover {
                    border-color: @btn_bg_h;
                }
                
                /* @notif_info_color */
                body .$unique_block_class .tds-s-notif-info {
                    color: @notif_info_color;
                }
                /* @notif_info_bg */
                body .$unique_block_class .tds-s-notif-info {
                    background-color: @notif_info_bg;
                }
                /* @notif_succ_color */
                body .$unique_block_class .tds-s-notif-success {
                    color: @notif_succ_color;
                }
                /* @notif_succ_bg */
                body .$unique_block_class .tds-s-notif-success {
                    background-color: @notif_succ_bg;
                }
                /* @notif_error_color */
                body .$unique_block_class .tds-s-notif-error {
                    color: @notif_error_color;
                }
                /* @notif_error_bg */
                body .$unique_block_class .tds-s-notif-error {
                    background-color: @notif_error_bg;
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
        $res_ctx->load_settings_raw( 'style_general_tds_my_account', 1 );



        /*-- LAYOUT -- */
        // label tooltips border radius
        $tip_radius = $res_ctx->get_shortcode_att('tip_radius');
        $res_ctx->load_settings_raw( 'tip_radius', $tip_radius );
        if( $tip_radius != '' && is_numeric( $tip_radius ) ) {
            $res_ctx->load_settings_raw( 'tip_radius', $tip_radius . 'px' );
        }


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
        $res_ctx->load_settings_raw( 'a_color_h', $res_ctx->get_shortcode_att('a_color_h') );

        $res_ctx->load_settings_raw( 'sec_color', $res_ctx->get_shortcode_att('sec_color') );
        $res_ctx->load_settings_raw( 'sec_descr_color', $res_ctx->get_shortcode_att('sec_descr_color') );

        $res_ctx->load_settings_raw( 'sep_color', $res_ctx->get_shortcode_att('sep_color') );
        $res_ctx->load_settings_raw( 'nav_color', $res_ctx->get_shortcode_att('nav_color') );
        $res_ctx->load_settings_raw( 'nav_ico_color', $res_ctx->get_shortcode_att('nav_ico_color') );
        $res_ctx->load_settings_raw( 'nav_ico_bg', $res_ctx->get_shortcode_att('nav_ico_bg') );
        $res_ctx->load_settings_raw( 'nav_ico_bg_h', $res_ctx->get_shortcode_att('nav_ico_bg_h') );

        $res_ctx->load_settings_raw( 'label_color', $res_ctx->get_shortcode_att('label_color') );
        $res_ctx->load_settings_raw( 'tip_color', $res_ctx->get_shortcode_att('tip_color') );
        $res_ctx->load_settings_raw( 'tip_bg', $res_ctx->get_shortcode_att('tip_bg') );
        $res_ctx->load_settings_raw( 'input_color', $res_ctx->get_shortcode_att('input_color') );
        $res_ctx->load_settings_raw( 'input_bg', $res_ctx->get_shortcode_att('input_bg') );
        $all_input_border_color = $res_ctx->get_shortcode_att('all_input_border_color');
        if( $all_input_border_color != '' ) {
            $res_ctx->load_settings_raw( 'all_input_border_color', $all_input_border_color );
        } else {
            $res_ctx->load_settings_raw( 'all_input_border_color', '#D7D8DE' );
        }

        $res_ctx->load_settings_raw( 'tabl_border_color', $res_ctx->get_shortcode_att('tabl_border_color') );
        $res_ctx->load_settings_raw( 'tabl_head_color', $res_ctx->get_shortcode_att('tabl_head_color') );
        $res_ctx->load_settings_raw( 'tabl_body_color', $res_ctx->get_shortcode_att('tabl_body_color') );
        $res_ctx->load_settings_raw( 'tabl_hover_bg', $res_ctx->get_shortcode_att('tabl_hover_bg') );
        $res_ctx->load_settings_raw( 'tabl_info_bg', $res_ctx->get_shortcode_att('tabl_info_bg') );

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

        $notif_succ_color = $res_ctx->get_shortcode_att('notif_succ_color');
        $res_ctx->load_settings_raw( 'notif_succ_color', $notif_succ_color );
        if( !empty( $notif_succ_color ) ) {
            $res_ctx->load_settings_raw('notif_succ_bg', td_util::hex2rgba($notif_succ_color, 0.1));
        }

        $notif_error_color = $res_ctx->get_shortcode_att('notif_error_color');
        $res_ctx->load_settings_raw( 'notif_error_color', $notif_error_color );
        if( !empty( $notif_error_color ) ) {
            $res_ctx->load_settings_raw('notif_error_bg', td_util::hex2rgba($notif_error_color, 0.12));
        }



        /*-- FONTS -- */
        $res_ctx->load_font_settings( 'f_text' );

	}

	function __construct() {
		parent::disable_loop_block_features();
	}

	function render( $atts, $content = null ) {

        parent::render( $atts );


        if (is_user_logged_in()) {
	        $show_validation = false;
	        $tds_validate    = get_user_meta( get_current_user_id(), 'tds_validate', true );
	        if ( ! empty( $tds_validate ) && is_array( $tds_validate ) && empty( $tds_validate[ 'validation_time' ] ) ) {
		        $show_validation = true;
	        }
        }


        // flag to check if we are in composer
        $is_composer = false;
        if( td_util::tdc_is_live_editor_iframe() || td_util::tdc_is_live_editor_ajax() ) {
            $is_composer = true;
        }

        // show a specific version of the shortcode in composer
        $show_version_in_composer = $this->get_att('show_version');

        // show notifications in composer
        $show_notif_in_composer = $this->get_att('show_notif');

        // remove top border on Newsmag
        $block_classes = str_replace('td-pb-border-top', '', $this->get_block_classes());

		$buffy = '<div class="tds-page-block ' . (empty($show_validation) ? '' : ' tds-invalid-account ')  . $block_classes . '" ' . $this->get_block_html_atts() . '>';

			$buffy .= $this->get_block_css(); // get block css
			$buffy .= $this->get_block_js(); // get block js


            $buffy .= '<div class="tds-block-inner tds-my-account-wrap">';
                if ( is_user_logged_in() || $is_composer ) {

                    $tds_dashboard_url = '#tds_dashboard';
                    $tds_subscription_url = '#tds_subscription';
                    $tds_account_details_url = '#tds_account_details';
                    $tds_logout_url = '#tds_logout';

                    $my_account_page_id = tds_util::get_tds_option('my_account_page_id');

                    if (!is_null($my_account_page_id) && is_page($my_account_page_id)) {

                        $my_account_permalink = get_permalink($my_account_page_id);
                        if (false !== $my_account_permalink) {
                            $tds_dashboard_url = esc_url($my_account_permalink);
                            $tds_subscription_url = esc_url(add_query_arg('subscriptions', '', $my_account_permalink));
                            $tds_account_details_url = esc_url(add_query_arg('account_details', '', $my_account_permalink));
                            $tds_logout_url = esc_url(wp_logout_url($my_account_permalink));
                        }
                    }

                    ob_start();
                    ?>

                    <div class="tds-s-acc-sidebar">
                        <div class="tds-s-acc-user">
                            <div class="tds-sau-avatar" style="background-image:url(<?php echo get_avatar_url(wp_get_current_user()->ID, ['size' => 32]) ?>)" title="<?php echo wp_get_current_user()->display_name ?>"></div>
                            <div class="tds-sau-name"><?php echo wp_get_current_user()->display_name ?></div>
                        </div>

                        <nav class="tds-s-acc-nav">
                            <a class="tds-san-item
                                <?php echo (
                                            ( !isset($_GET['account_details']) && !isset($_GET['subscriptions']) && !$is_composer )
                                            || ( $is_composer && $show_version_in_composer == '' )
                                           ) ? 'tds-san-item-active' : '' ?>"
                                href="<?php echo $tds_dashboard_url ?>">
                                <span class="tds-san-item-icon"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg></span>
                                <span class="tds-san-item-txt"><?php echo __td('Dashboard', TD_THEME_NAME) ?></span>
                            </a>
                            <a class="tds-san-item
                                <?php echo (
                                            ( isset($_GET['account_details']) )
                                            || ( $is_composer && $show_version_in_composer == 'settings' )
                                           ) ? 'tds-san-item-active' : '' ?>"
                                href="<?php echo $tds_account_details_url ?>">
                                <span class="tds-san-item-icon"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#A0A5D7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg></span>
                                <span class="tds-san-item-txt"><?php echo __td('Account settings', TD_THEME_NAME) ?></span>
                            </a>
                            <a class="tds-san-item
                                <?php echo (
                                            ( isset($_GET['subscriptions']) )
                                            || ( $is_composer && $show_version_in_composer == 'subscriptions' )
                                           ) ? 'tds-san-item-active' : '' ?>"
                                href="<?php echo $tds_subscription_url ?>">
                                <span class="tds-san-item-icon"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#A0A5D7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-package"><line x1="16.5" y1="9.4" x2="7.5" y2="4.21"></line><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg></span>
                                <span class="tds-san-item-txt"><?php echo __td('Subscriptions', TD_THEME_NAME) ?></span>
                            </a>
                            <a class="tds-san-item" href="<?php echo $tds_logout_url ?>">
                                <span class="tds-san-item-icon"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#A0A5D7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-out"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg></span>
                                <span class="tds-san-item-txt"><?php echo __td('Logout', TD_THEME_NAME) ?></span>
                            </a>
                        </nav>
                    </div>

                    <div class="tds-s-acc-content">
                        <?php
                        if ( !empty($show_validation) ) {
                            ?>

                            <div class="tds-s-notif tds-s-notif-sm tds-s-notif-error tds-s-notif-acc-activation">
                                <div class="tds-s-notif-descr"><?php echo __td('Please activate your account by following the link sent to your email address.', TD_THEME_NAME) ?></div>
                            <!-- tds-s-notif-js was added only for translation-->
                                <div class="tds-s-notif-js" style="display: none;"><?php echo __td('A new activation link has been sent to your email address!', TD_THEME_NAME) ?></div>
                                <button class="tds-s-btn tds-s-btn-xsm tds-s-btn-red tds-resend-activation-link" data-user="<?php echo get_current_user_id() ?>"><?php echo __td('Resend activation link', TD_THEME_NAME) ?></button>
                            </div>

                            <?php
                        }

                        if ( isset($_GET['subscriptions']) || ( $is_composer && $show_version_in_composer == 'subscriptions' ) ) {
                            if (empty($show_validation)) {
                                td_global::set_in_element( true );
	                            echo do_shortcode( '[tds_subscription ' . ( $is_composer ? 'show_dummy="yes"' : '' ) . ']' );
                                td_global::set_in_element( false );
                            } else {
                                ?>

                                <div class="tds-s-notif tds-s-notif-info">
                                    <div class="tds-s-notif-descr">
                                        <?php echo __td('In order to have access to this section, you have to activate your account.', TD_THEME_NAME) ?>
                                    </div>
                                </div>

                                <?php
                            }
                        } else if ( isset($_GET['account_details']) || ( $is_composer && $show_version_in_composer == 'settings' ) ) {
                            td_global::set_in_element( true );
                            echo do_shortcode('[tds_account_details ' . ( ( $is_composer && $show_notif_in_composer ) ? 'show_notif="yes"' : '' )  . ']');
                            td_global::set_in_element( false );
                        } else {
                            echo do_shortcode('[tds_dashboard]');
                        }

                        ?>
                    </div>

                    <?php
                    $buffy .= ob_get_clean();

                }
		    $buffy .= '</div>';
		$buffy .= '</div>';

		return $buffy;
	}
}
