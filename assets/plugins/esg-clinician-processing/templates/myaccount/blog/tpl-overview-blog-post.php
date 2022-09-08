<?php
$featured_thumb_url = get_the_post_thumbnail_url($post->ID, 'medium');
if ($featured_thumb_url == '') {
    $featured_thumb_url = 'default.png';
}
?>
<div class="card overview">
    <div class="featured_image">
        <img src="<?php echo $featured_thumb_url; ?>" />
    </div>
    <div class="content">
        <h2><?php echo $post->post_title; ?></h2>
        <?php
        if ($post->post_excerpt) {
            echo '<p class="excerpt">' . $post->post_excerpt . '</p>';
        }
        ?>
        <div class="card_meta">
            Status: <span class="<?php echo $post->post_status; ?>"><?php echo $post->post_status; ?></span>
        </div>
        <div class="card_actions">
            <a class="view" href="<?php the_permalink($post->ID); ?>">View</a>
            <a class="edit" href="<?php echo get_edit_profile_link('blog_posts&action=edit&id=' . $post->ID); ?>">Edit</a>
            <a class="delete" href="<?php echo get_edit_profile_link('blog_posts&action=delete&id=' . $post->ID); ?>">Delete</a>

        </div>
    </div>

</div>