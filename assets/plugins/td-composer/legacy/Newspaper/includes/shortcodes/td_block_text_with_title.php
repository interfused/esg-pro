<?php

class td_block_text_with_title extends td_block {

    public function get_custom_css() {
        // $unique_block_class - the unique class that is on the block. use this to target the specific instance via css
        $in_composer = td_util::tdc_is_live_editor_iframe() || td_util::tdc_is_live_editor_ajax();
        $in_element = td_global::get_in_element();
        $unique_block_class_prefix = '';
        if( $in_element || $in_composer ) {
            $unique_block_class_prefix = 'tdc-row .';

            if( $in_element && $in_composer ) {
                $unique_block_class_prefix = 'tdc-row-composer .';
            }
        }
        $unique_block_class = $unique_block_class_prefix . $this->block_uid;

        $compiled_css = '';

        $raw_css =
            "<style>

				/* @style_general_text_with_title */
				.td_block_text_with_title {
                  margin-bottom: 44px;
                  -webkit-transform: translateZ(0);
                  transform: translateZ(0);
                }
                .td_block_text_with_title p:last-child {
                  margin-bottom: 0;
                }

				
				/* @f_post */
				.$unique_block_class,
                .$unique_block_class p {
			        @f_post
		        }
				/* @f_h1 */
				.$unique_block_class h1 {
			        @f_h1
		        }
				/* @f_h2 */
				.$unique_block_class h2 {
			        @f_h2
		        }
				/* @f_h3 */
				.$unique_block_class h3 {
			        @f_h3
		        }
				/* @f_h4 */
				.$unique_block_class h4 {
			        @f_h4
		        }
				/* @f_h5 */
				.$unique_block_class h5 {
			        @f_h5
		        }
				/* @f_h6 */
				.$unique_block_class h6 {
			        @f_h6
		        }
				/* @f_list */
				.$unique_block_class li {
			        @f_list
		        }
				/* @f_list_arrow */
				.$unique_block_class li:before {
				    margin-top: 1px;
			        line-height: @f_list_arrow !important;
		        }
				/* @f_bq */
				.$unique_block_class blockquote p {
			        @f_bq
		        }
		        
				/* @post_color */
				.$unique_block_class {
			        color: @post_color;
		        }
				/* @h_color */
				.$unique_block_class h1,
				.$unique_block_class h2,
				.$unique_block_class h3,
				.$unique_block_class h4,
				.$unique_block_class h5,
				.$unique_block_class h6 {
			        color: @h_color;
		        }
				/* @bq_color */
				.$unique_block_class blockquote p {
			        color: @bq_color;
		        }
				/* @a_color */
				.$unique_block_class a {
			        color: @a_color;
		        }
				/* @a_hover_color */
				.$unique_block_class a:hover {
			        color: @a_hover_color;
		        }

			</style>";


        $td_css_res_compiler = new td_css_res_compiler( $raw_css );
        $td_css_res_compiler->load_settings( __CLASS__ . '::cssMedia', $this->get_all_atts() );

        $compiled_css .= $td_css_res_compiler->compile_css();
        return $compiled_css;
    }

    static function cssMedia( $res_ctx ) {

        $res_ctx->load_settings_raw( 'style_general_text_with_title', 1 );

        /*-- fonts -- */
        $res_ctx->load_font_settings( 'f_post' );
        $res_ctx->load_font_settings( 'f_h1' );
        $res_ctx->load_font_settings( 'f_h2' );
        $res_ctx->load_font_settings( 'f_h3' );
        $res_ctx->load_font_settings( 'f_h4' );
        $res_ctx->load_font_settings( 'f_h5' );
        $res_ctx->load_font_settings( 'f_h6' );
        $res_ctx->load_font_settings( 'f_list' );
        $f_list_size = $res_ctx->get_shortcode_att('f_list_font_size');
        $f_list_lh = $res_ctx->get_shortcode_att('f_list_font_line_height');
        if( $f_list_size != '' && $f_list_lh == '' ) {
            if( is_numeric( $f_list_size ) ) {
                $res_ctx->load_settings_raw( 'f_list_arrow', $f_list_size . 'px' );
            } else {
                $res_ctx->load_settings_raw( 'f_list_arrow', $f_list_size );
            }
        }
        if( $f_list_size == '' && $f_list_lh != '' ) {
            if( is_numeric( $f_list_lh ) ) {
                $res_ctx->load_settings_raw( 'f_list_arrow', 15 * $f_list_lh . 'px' );
            } else {
                $res_ctx->load_settings_raw( 'f_list_arrow', $f_list_lh );
            }
        }
        if( $f_list_size != '' && $f_list_lh != '' ) {
            if( is_numeric( $f_list_lh ) ) {
                $res_ctx->load_settings_raw( 'f_list_arrow', $f_list_size * $f_list_lh . 'px' );
            } else {
                $res_ctx->load_settings_raw( 'f_list_arrow', $f_list_lh );
            }
        }
        $res_ctx->load_font_settings( 'f_bq' );


        // colors
        $res_ctx->load_settings_raw( 'post_color', $res_ctx->get_shortcode_att('post_color') );
        $res_ctx->load_settings_raw( 'h_color', $res_ctx->get_shortcode_att('h_color') );
        $res_ctx->load_settings_raw( 'bq_color', $res_ctx->get_shortcode_att('bq_color') );
        $res_ctx->load_settings_raw( 'a_color', $res_ctx->get_shortcode_att('a_color') );
        $res_ctx->load_settings_raw( 'a_hover_color', $res_ctx->get_shortcode_att('a_hover_color') );

    }

	/**
	 * Disable loop block features. This block does not use a loop and it dosn't need to run a query.
	 */
	function __construct() {
		parent::disable_loop_block_features();
	}


    function render($atts, $content = null) {
        parent::render($atts); // sets the live atts, $this->atts, $this->block_uid, $this->td_query (it runs the query

	    $atts = shortcode_atts(
			array(
				'content' => __('Html code here! Replace this with any non empty text and that\'s it.', TD_THEME_NAME ),
				'el_class' => '',
			), $atts, 'td_block_text_with_title' );

		if (td_util::tdc_is_live_editor_iframe() || td_util::tdc_is_live_editor_ajax() ) {
			if (is_null($content) || empty($content)) {
				$content = $atts['content'];
			}
		}

	    $buffy = '';
        $buffy .= '<div class="' . $this->get_block_classes(array('tagdiv-type')) . '" ' . $this->get_block_html_atts() . '>';

		    //get the block js
		    $buffy .= $this->get_block_css();

            // block title wrap
            $buffy .= '<div class="td-block-title-wrap">';
                $buffy .= $this->get_block_title();
                $buffy .= $this->get_pull_down_filter(); //get the sub category filter for this block
            $buffy .= '</div>';

			//td-fix-index class to fix background color z-index
            $buffy .= '<div class="td_mod_wrap td-fix-index">';
//                //only run the filter if we have visual composer
//	            if ( ! ( td_util::tdc_is_live_editor_iframe() || td_util::tdc_is_live_editor_ajax() ) ) {
//	                if (function_exists('wpb_js_remove_wpautop')) {
//			            $buffy .= wpb_js_remove_wpautop( $content );
//		            } else {
//			            $buffy .= do_shortcode( shortcode_unautop( $content ) );
//		            }
//	            } else {
//		            $buffy .= $content;   //no visual composer
//	            }

	    if ( base64_decode( $content, true ) && base64_encode( base64_decode( $content, true ) ) === $content ) {
			$content = base64_decode( $content );
		}

	    // As vc does
		$content = wpautop( preg_replace( '/<\/?p\>/', "\n", $content ) . "\n" );

	    $render_content = true;

		if ( tdc_state::is_live_editor_iframe() || tdc_state::is_live_editor_ajax() ) {
			$mapped_shortcodes = tdc_mapper::get_mapped_shortcodes();
			foreach ( $mapped_shortcodes as $base => $mapped_shortcode ) {
				if ( has_shortcode( $content, $base ) ) {
					$render_content = false;
					break;
				}
			}
		}

		if ( $render_content ) {
			$content = do_shortcode( shortcode_unautop( $content ) );
		}

        $buffy .= $content;


	    $buffy .= '</div>';
        $buffy .= '</div>';

        return $buffy;
    }
}