<?php
/**
 * Created by PhpStorm. tr
 * User: tagdiv
 * Date: 30.12.2014
 * Time: 13:27
 */


require_once(TDC_PATH . '/legacy/common/wp_booster/td_video_playlist_render.php');


//class for vimeo playlist shortcode
class td_block_video_vimeo extends td_block {

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
                /* @display_row */
                body .$unique_block_class .td_wrapper_video_playlist {
                    flex-direction: column;
                }
                body .$unique_block_class .td_wrapper_player {
                    flex: auto;
                }
                body .$unique_block_class .td_container_video_playlist {
                    min-width: 100%;
                    max-width: 100%;
                }
                body .$unique_block_class .td_video_playlist_column_1 .td_container_video_playlist,
                body .$unique_block_class .td_video_playlist_column_2 .td_container_video_playlist {
                    width: 100%;
                }
                body .$unique_block_class .td_video_playlist_column_1 .td_container_video_playlist {
                    height: 412px;
                }
                body .$unique_block_class .td_video_playlist_column_2 .td_container_video_playlist {
                    height: 305px;
                }
                @media (max-width: 480px) {
                    body .$unique_block_class .td_video_playlist_column_1 .td_container_video_playlist,
                    body .$unique_block_class .td_video_playlist_column_2 .td_container_video_playlist {
                        height: 245px;
                    }
                }
                /* @display_column */
                body .$unique_block_class .td_wrapper_video_playlist {
                    flex-direction: row;
                }
                body .$unique_block_class .td_wrapper_player {
                    flex: 1;
                }
                body .$unique_block_class .td_container_video_playlist {
                    min-width: 0;
                    max-width: none;
                }
                body .$unique_block_class .td_video_playlist_column_1 .td_container_video_playlist,
                body .$unique_block_class .td_video_playlist_column_2 .td_container_video_playlist {
                    height: auto;
                }
                body .$unique_block_class .td_video_playlist_column_1 .td_container_video_playlist {
                    width: 150px;
                }
                body .$unique_block_class .td_video_playlist_column_2 .td_container_video_playlist {
                    width: 250px;
                }
            
            
                /* @v_height */
				body .$unique_block_class .td_wrapper_player {
					height: @v_height;
				}
				/* @v_height2 */
				@media (min-width: 1019px) {
                    body .$unique_block_class .td_video_playlist_column_3 .td_container_video_playlist {
                        height: @v_height2;
                    }
				}
            
            
                /* @p_width */
				body .$unique_block_class .td_container_video_playlist {
					width: @p_width !important;
				}
                /* @p_height */
				body .$unique_block_class .td_container_video_playlist {
					height: @p_height !important;
				}
				
				/* @p_horiz_align_center */
				body .$unique_block_class .td_container_video_playlist {
				    text-align: center;
				}
				/* @p_horiz_align_right */
				body .$unique_block_class .td_container_video_playlist {
				    text-align: right;
				}
				
				
				/* @title_color */
				body .$unique_block_class .td_video_playlist_title .td_video_title_text {
				    color: @title_color;
				}
				/* @title_bg_color */
				body .$unique_block_class .td_video_playlist_title {
				    background-color: @title_bg_color;
				}
				
				/* @vid_color */
				body .$unique_block_class .td_video_title_and_time .td_video_title {
				    color: @vid_color;
				}
				/* @vid_color_h */
				body .$unique_block_class .td_click_video:hover .td_video_title_and_time .td_video_title {
				    color: @vid_color_h;
				}
				/* @timestamp_color */
				body .$unique_block_class .td_wrapper_video_playlist .td_video_time {
				    color: @timestamp_color;
				}
				
				/* @curr_vid_color */
				body .$unique_block_class .td_wrapper_video_playlist .td_video_title_playing {
				    color: @curr_vid_color;
				}
				/* @curr_timestamp_color */
				body .$unique_block_class .td_wrapper_video_playlist .td_video_time_playing {
				    color: @curr_timestamp_color;
				}
				/* @curr_bg_color */
				body .$unique_block_class .td_wrapper_video_playlist .td_video_controls_playlist_wrapper {
				    background-color: @curr_bg_color;
				}
				body .$unique_block_class .td_wrapper_video_playlist .td_video_currently_playing:after {
				    border-left-color: @curr_bg_color !important;
				}
				

                /* @f_title */
				body .$unique_block_class .td_video_playlist_title .td_video_title_text {
				    @f_title
				}
				/* @f_vid */
				body .$unique_block_class .td_video_title_and_time .td_video_title {
					@f_vid
				}
				/* @f_curr_vid */
				body .$unique_block_class .td_wrapper_video_playlist .td_video_title_playing {
					@f_curr_vid
				}
				/* @f_timestamp */
				body .$unique_block_class .td_wrapper_video_playlist .td_video_time,
				body .$unique_block_class .td_wrapper_video_playlist .td_video_time_playing {
					@f_timestamp
				}

			</style>";


        $td_css_res_compiler = new td_css_res_compiler( $raw_css );
        $td_css_res_compiler->load_settings( __CLASS__ . '::cssMedia', $this->get_all_atts() );

        $compiled_css .= $td_css_res_compiler->compile_css();
        return $compiled_css;
    }

    static function cssMedia( $res_ctx ) {

        /*-- GENERAL -- */
        $res_ctx->load_settings_raw( 'style_general_video', 1 );

        // display
        $display = $res_ctx->get_shortcode_att('display');
        if( $display == 'row' ) {
            $res_ctx->load_settings_raw('display_row', 1);
        } else if ( $display == 'column' ) {
            $res_ctx->load_settings_raw('display_column', 1);
        }



        /*-- MAIN VIDEO -- */
        // width
        $p_width = $res_ctx->get_shortcode_att('p_width');
        $res_ctx->load_settings_raw('p_width', $p_width);
        if( $p_width != '' && is_numeric($p_width) ) {
            $res_ctx->load_settings_raw('p_width', $p_width . 'px');
        }

        // height
        $v_height = $res_ctx->get_shortcode_att('v_height');
        $res_ctx->load_settings_raw('v_height', $v_height);
        if( $v_height != '' && is_numeric($v_height) ) {
            $res_ctx->load_settings_raw('v_height', $v_height . 'px');
        }
        if ( $display == 'column' ) {
            $res_ctx->load_settings_raw('v_height2', $v_height);
            if( $v_height != '' && is_numeric($v_height) ) {
                $res_ctx->load_settings_raw('v_height2', $v_height . 'px');
            }
        }



        /*-- PLAYLIST -- */
        // height
        $p_height = $res_ctx->get_shortcode_att('p_height');
        $res_ctx->load_settings_raw('p_height', $p_height);
        if( $p_height != '' && is_numeric($p_height) ) {
            $res_ctx->load_settings_raw('p_height', $p_height . 'px');
        }

        // horizontal align
        $p_horiz_align = $res_ctx->get_shortcode_att('p_horiz_align');
        if( $p_horiz_align == 'content-horiz-center' ) {
            $res_ctx->load_settings_raw('p_horiz_align_center', 1);
        } else if ( $p_horiz_align == 'content-horiz-right' ) {
            $res_ctx->load_settings_raw('p_horiz_align_right', 1);
        }



        /*-- COLORS -- */
        $res_ctx->load_settings_raw('title_color', $res_ctx->get_shortcode_att('title_color'));
        $res_ctx->load_settings_raw('title_bg_color', $res_ctx->get_shortcode_att('title_bg_color'));

        $res_ctx->load_settings_raw('vid_color', $res_ctx->get_shortcode_att('vid_color'));
        $res_ctx->load_settings_raw('vid_color_h', $res_ctx->get_shortcode_att('vid_color_h'));
        $res_ctx->load_settings_raw('timestamp_color', $res_ctx->get_shortcode_att('timestamp_color'));

        $res_ctx->load_settings_raw('curr_vid_color', $res_ctx->get_shortcode_att('curr_vid_color'));
        $res_ctx->load_settings_raw('curr_timestamp_color', $res_ctx->get_shortcode_att('curr_timestamp_color'));
        $res_ctx->load_settings_raw('curr_bg_color', $res_ctx->get_shortcode_att('curr_bg_color'));



        /*-- FONTS -- */
        $res_ctx->load_font_settings( 'f_title' );
        $res_ctx->load_font_settings( 'f_vid' );
        $res_ctx->load_font_settings( 'f_curr_vid' );
        $res_ctx->load_font_settings( 'f_timestamp' );

    }

	/**
	 * Disable loop block features. This block does not use a loop and it dosn't need to run a query.
	 */
	function __construct() {
		parent::disable_loop_block_features();
	}



    function render($atts, $content = null) {

        parent::render($atts); // sets the live atts, $this->atts, $this->block_uid, $this->td_query (it runs the query)

        // get the playlist
        return td_video_playlist_render::render_generic( $atts, 'vimeo', $this->get_wrapper_class(), $this->block_uid, $this->get_block_css() );

    }
}