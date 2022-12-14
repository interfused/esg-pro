<?php
/**
 * If you are looking for the loop that's handling the single post page (single.php), check out loop-single.php
 **/


// $global_flag_to_hide_no_post_to_display - comes from page-category-big-grid.php and is a flag to hide the 'No posts to display' message if on category page there are between 1 and 5  posts
global $loop_sidebar_position, $global_flag_to_hide_no_post_to_display;

///if we are in wordpress loop; used by quotes in blocks to check if the blocks are displayed in blocks or in loop
td_global::$is_wordpress_loop = true;

$td_template_layout = new td_template_layout($loop_sidebar_position);


if (have_posts()) {
    while ( have_posts() ) : the_post(); ?>
        <div <?php post_class('td_module_15 td_module_wrap clearfix') ?> >
            <div class="item-details">
                <h3 class="entry-title td-module-title">
                    <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute() ?>">
                        <?php the_title() ?>
                    </a>
                </h3>

                <div class="td-module-meta-info">
                    <ul class="td-category">
                        <?php
                        $categories = get_the_category();
                        if( !empty( $categories ) ) {
                            foreach($categories as $category) {
                                $cat_link = get_category_link($category->cat_ID);
                                $cat_name = $category->name; ?>
                                <li class="entry-category"><a href="<?php echo esc_url($cat_link) ?>"><?php echo esc_html($cat_name) ?></a></li>
                            <?php }
                        } ?>
                    </ul>

                    <div class="td-post-author-name">
                        <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta( 'ID' ))) ?>"><?php the_author() ?></a>
                        <div class="td-author-line"> - </div>
                    </div>

                    <span class="td-post-date">
                        <time class="entry-date updated td-module-date" datetime="<?php echo esc_html(date(DATE_W3C, get_the_time('U'))) ?>" ><?php the_time(get_option('date_format')) ?></time>
                    </span>

                    <div class="td-post-comments">
                        <a href="<?php comments_link() ?>">
                            <i class="td-icon-comments"></i>
                            <?php comments_number('0','1','%') ?>
                        </a>
                    </div>
                </div>

                <?php
                if( get_the_post_thumbnail_url(null, 'medium_large') != false ) { ?>
                    <div class="td-module-thumb">
                        <?php if ( current_user_can('edit_published_posts') ) {
                            edit_post_link('Edit', '', '', '', 'td-admin-edit');
                        }
                        ?>

                        <a href="<?php the_permalink() ?>" rel="bookmark" class="td-image-wrap" title="<?php the_title_attribute() ?>">
                            <img class="entry-thumb" src="<?php echo esc_url(get_the_post_thumbnail_url(null, 'medium_large')) ?>" alt="<?php the_title() ?>" title="<?php echo esc_attr(strip_tags(the_title())) ?>" />
                        </a>
                    </div>
                <?php } ?>

                <div class="td-post-text-content td-post-content tagdiv-type">
                    <?php

                    if ( is_plugin_active('td-subscription/td-subscription.php') ) {

                        // run locker init
	                    tds_email_locker::instance()->locker_init();

                        // get content
	                    the_content( esc_html( 'Continue' ) );

                        // remove the content filter
                        if ( has_filter( 'the_content', array( tds_email_locker::instance(), 'lock_content' ) ) ) {
	                        remove_filter( 'the_content', array( tds_email_locker::instance(), 'lock_content' ) );
                        }

                    } else {
	                    the_content( esc_html( 'Continue' ) );
                    }

                    ?>
                </div>
            </div>

        </div>
    <?php endwhile; //end loop


} else {
    /**
     * no posts to display. This function generates the __td('No posts to display').
     * the text can be overwritten by the template using the global @see td_global::$custom_no_posts_message
     */

    echo td_page_generator::no_posts();

}
