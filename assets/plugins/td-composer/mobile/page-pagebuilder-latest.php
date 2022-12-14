<?php
/* Template Name: Mobile Theme - Pagebuilder + latest articles + pagination */
if ( td_util::is_amp() ) {
    get_header('amp');
} else {
    get_header();
}

td_global::set_current_template('page-homepage-loop' );

global $paged, $post;

$td_page = get_query_var('page') ? get_query_var('page') : 1; //rewrite the global var
$td_paged = get_query_var('paged') ? get_query_var('paged') : 1; //rewrite the global var

//paged works on single pages, page - works on homepage
$paged = max( $td_page, $td_paged );

/*
    read the settings for the loop
---------------------------------------------------------------------------------------- */
td_global::load_single_post($post);

//read the metadata for the post

$td_homepage_loop = td_util::get_post_meta_array($post->ID, 'td_homepage_loop');

$td_list_custom_title =__td('LATEST ARTICLES', TD_THEME_NAME);
if (!empty($td_homepage_loop['list_custom_title'])) {
	$td_list_custom_title = $td_homepage_loop['list_custom_title'];
}

$list_custom_title_show = true; //show the article list title by default
if (!empty($td_homepage_loop['list_custom_title_show'])) {
	$list_custom_title_show = false;
}

?>

<div class="td-main-content-wrap td-main-page-wrap">

	<?php
	/*
	the first part of the page (built with the page builder)  - empty($paged) or $paged < 2 = first page
	---------------------------------------------------------------------------------------- */
	//td_global::$cur_single_template_sidebar_pos = 'no_sidebar';

	if ( empty( $paged ) or $paged < 2 ) { //show this only on the first page
		if ( have_posts() ) { ?>
			<?php while ( have_posts() ) : the_post(); ?>

				<div class="td-container">
					<?php

					// get the mob theme status from post meta
					$status = get_post_meta( get_the_ID(), 'tdm_grid_status', true );

					if ( $status && $status === 'enabled' ) {
						echo td_global_blocks::get_instance( 'td_block_big_grid_mob_1' )->render( array() );
					}

					?>
                    <?php
                    $the_content = apply_filters('the_content', get_the_content());
                    if ( current_user_can( 'publish_posts' ) && empty($the_content) ) {?>
                        <div class="td-mobile-missing-content">
                            <?php echo 'No mobile content! - ' ?><a href="<?php echo get_edit_post_link() . '#td_mobile_editor'?>"> Edit mobile content </a>
                        </div>
                        <?php
                    } else {
                        the_content();
                    } ?>
				</div>

			<?php endwhile; ?>
		<?php }
	}

	// set the $cur_single_template_sidebar_pos - for gallery and video playlist
	td_global::$cur_single_template_sidebar_pos = 'no_sidebar';
	//the default template

	//td_global::$load_featured_img_from_template = 'full';

	?>
	<div class="td-container td-pb-article-list td-main-content" role="main">
		<?php if ( ( empty( $paged ) or $paged < 2 ) and true === $list_custom_title_show ) { ?>
			<h4 class="block-title"><span><?php echo $td_list_custom_title; ?></span></h4>
		<?php }

		query_posts( td_data_source::metabox_to_args( $td_homepage_loop, $paged ) );
		locate_template( 'loop.php', true );
		td_page_generator_mob::get_pagination();
		wp_reset_query();
		?>
	</div>

</div> <!-- /.td-main-content-wrap -->

<?php

if ( td_util::is_amp() ) {
    get_footer('amp');
} else {
    get_footer();
}