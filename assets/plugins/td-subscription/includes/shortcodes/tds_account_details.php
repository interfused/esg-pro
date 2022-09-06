<?php

/**
 * Class tds_account_details
 */

class tds_account_details extends td_block {

	public function get_custom_css() {

        // $unique_block_class
        $unique_block_class = $this->block_uid;

		$compiled_css = '';

		/** @noinspection CssInvalidAtRule */
		$raw_css =
            "<style>

                /* @tds_account_details */
                body .tds_account_details {
                    margin-bottom: 0;                    
                }
                body .tds_logout .tds-block-inner {
                    margin: 0 auto;
                    padding: 55px 45px 60px;
                    max-width: 650px;
                    background-color: #fff;
                    text-align: center;
                }
                
            </style>";

		$td_css_res_compiler = new td_css_res_compiler( $raw_css );
		$td_css_res_compiler->load_settings( __CLASS__ . '::cssMedia', $this->get_all_atts() );

		$compiled_css .= $td_css_res_compiler->compile_css();

		return $compiled_css;

	}

	static function cssMedia( $res_ctx ) {
        $res_ctx->load_settings_raw( 'tds_account_details', 1 );
	}

	function __construct() {
		parent::disable_loop_block_features();
	}

	function render( $atts, $content = null ) {

        parent::render( $atts );


        $show_notif = $this->get_att('show_notif');
        //$show_notif = false;

		$buffy = '';

        // remove top border on Newsmag
        $block_classes = str_replace('td-pb-border-top', '', $this->get_block_classes());

        $buffy = '<div class="' . $block_classes . '" ' . $this->get_block_html_atts() . '>';

			$buffy .= $this->get_block_css(); // get block css
			$buffy .= $this->get_block_js(); // get block js


            $buffy .= '<div class="tds-block-inner td-fix-index">';

                if (is_user_logged_in()) {

                    $current_user = wp_get_current_user();
                    $first_name = get_user_meta( $current_user->ID, 'first_name', true );
                    $last_name = get_user_meta( $current_user->ID, 'last_name', true );

                    //if (!headers_sent()) {
                    //    session_start();
                    //}

	                if (!headers_sent()) {
		                if (!session_id()) {
			                try {
				                @session_start();
			                } catch (Exception $e) {
				                //***
			                }
		                }
	                }

                    if( $show_notif != '' ) {
                        $buffy .= '<div class="tds-s-notif tds-s-notif-sm tds-s-notif-error">';
                            $buffy .= '<ul class="tds-s-notif-list">';
                                $buffy .= '<li>Sample error message.</li>';
                            $buffy .= '</ul>';
                        $buffy .= '</div>';

                        $buffy .= '<div class="tds-s-notif tds-s-notif-sm tds-s-notif-success">';
                            $buffy .= '<ul class="tds-s-notif-list">';
                                $buffy .= '<li>Sample success message.</li>';
                            $buffy .= '</ul>';
                        $buffy .= '</div>';
                    }

                    if (!empty($_SESSION['tds_errors']) && is_array($_SESSION['tds_errors'])) {
                        $buffy .= '<div class="tds-s-notif tds-s-notif-sm tds-s-notif-error">';
                            $buffy .= '<ul class="tds-s-notif-list">';
                                foreach ($_SESSION['tds_errors'] as $error ) {
                                    $buffy .= '<li>' . $error . '</li>';
                                }
                            $buffy .= '</ul>';
                        $buffy .= '</div>';

                        unset($_SESSION['tds_errors']);

                    } else if (!empty($_SESSION['tds_msg'])) {
                        $buffy .= '<div class="tds-s-notif tds-s-notif-sm tds-s-notif-success">';
                            $buffy .= '<div class="tds-s-notif-descr">' . $_SESSION['tds_msg'] . '</div>';
                        $buffy .= '</div>';

                        unset($_SESSION['tds_msg']);
                    }

                    ob_start();
                    ?>

                    <div class="tds-s-page-sec tds-s-page-acc-details">
                        <div class="tds-s-page-sec-header">
                            <h2 class="tds-spsh-title"><?php echo __td('Account settings', TD_THEME_NAME) ?></h2>
                            <div class="tds-spsh-descr"><?php echo __td('Manage your account details.', TD_THEME_NAME) ?></div>
                        </div>

                        <div class="tds-s-page-sec-content">
                            <form action="" method="post" class="tds-s-form tds-s-acc-info-form">
                                <div class="tds-s-form-content">
                                    <div class="tds-s-fc-inner">
                                        <?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ); ?>
                                        <input type="hidden" name="action" value="save_account_details" />

                                        <div class="tds-s-form-group">
                                            <label class="tds-s-form-label" for="tds-first-name"><?php echo __td('First name', TD_THEME_NAME) ?> *</label>
                                            <input class="tds-s-form-input" type="text" id="tds-first-name" name="tds_first_name" value="<?php echo $first_name ?>" required>
                                        </div>

                                        <div class="tds-s-form-group">
                                            <label class="tds-s-form-label" for="tds-last-name"><?php echo __td('Last name', TD_THEME_NAME) ?> *</label>
                                            <input class="tds-s-form-input" type="text" id="tds-last-name" name="tds_last_name" value="<?php echo $last_name ?>" required>
                                        </div>

                                        <div class="tds-s-form-group">
                                            <label class="tds-s-form-label" for="tds-display-name"><?php echo __td('Display name', TD_THEME_NAME) ?> *</label>
                                            <input class="tds-s-form-input" type="text" id="tds-display-name" name="tds_display_name" value="<?php echo $current_user->display_name ?>" required>
                                        </div>

                                        <div class="tds-s-form-group">
                                            <label class="tds-s-form-label" for="tds-email"><?php echo __td('Email address', TD_THEME_NAME) ?> *</label>
                                            <input class="tds-s-form-input" type="text" id="tds-email" name="tds_email" value="<?php echo $current_user->user_email ?>" required>
                                        </div>

                                        <div class="tds-s-form-group">
                                            <label class="tds-s-form-label" for="tds-current-pass"><?php echo __td('Current password', TD_THEME_NAME) ?>
                                                <span class="tds-s-form-tip"><span class="tds-s-form-tip-txt"><?php echo __td('(leave blank to leave unchanged)', TD_THEME_NAME) ?></span></span></label>
                                            <input class="tds-s-form-input" type="password" id="tds-current-pass" name="tds_current_pass" value="" autocomplete="off">
                                        </div>

                                        <div class="tds-s-form-group">
                                            <label class="tds-s-form-label" for="tds-new-pass"><?php echo __td('New password', TD_THEME_NAME) ?>
                                                <span class="tds-s-form-tip"><span class="tds-s-form-tip-txt"><?php echo __td('(leave blank to leave unchanged)', TD_THEME_NAME) ?></span></span></label>
                                            <input class="tds-s-form-input" type="password" id="tds-new-pass" name="tds_new_pass" value="" autocomplete="off">
                                        </div>

                                        <div class="tds-s-form-group">
                                            <label class="tds-s-form-label" for="tds-retype-new-pass"><?php echo __td('Confirm new password', TD_THEME_NAME) ?></label>
                                            <input class="tds-s-form-input" type="password" id="tds-retype-new-pass" name="tds_retype_new_pass" value="" autocomplete="off">
                                        </div>
                                    </div>
                                </div>

                                <div class="tds-s-form-footer">
                                    <button class="tds-s-btn" type="submit" name="save_account_details"><?php echo __td('Save changes', TD_THEME_NAME) ?></button>
                                </div>
                            </form>
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
		$buffy .= '</div>';

		return $buffy;
	}
}
