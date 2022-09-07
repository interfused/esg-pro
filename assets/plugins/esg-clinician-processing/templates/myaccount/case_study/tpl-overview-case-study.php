<div class="card overview">
    <div class="featured_image">featured image</div>
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
            <a class="edit" href="#">Edit</a> <a class="delete" href="#">Delete</a>

        </div>
    </div>

    tpl-overview-case-study.php
</div>