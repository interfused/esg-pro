<div class="card overview">
    <div class="featured_image">featured image</div>
    <div class="content">
        <h2><?php echo $post->post_title; ?></h2>
        <?php
        if ($post->post_excerpt) {
            echo '<p class="excerpt">' . $post->post_excerpt . '</p>';
        }
        ?>

        <div class="btnActions">
            Status: <span class="<?php echo $post->post_status; ?>"><?php echo $post->post_status; ?></span>
        </div>
    </div>

    tpl-overview-case-study.php
</div>