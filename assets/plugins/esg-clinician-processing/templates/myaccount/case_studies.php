<?php
$edit_action = $_GET['action'] ?? "default";

?>
<section>
    <?php


    if ($edit_action == 'default') {
        echo sprintf(esg_get_section_format('default'), 'Add New Case Study');
        echo do_shortcode('[frontend_admin form="562"]');
    }

    if ($edit_action == 'edit') {
        echo sprintf(esg_get_section_format('default'), 'Edit Case Study');
        echo do_shortcode('[frontend_admin form="591"]');
    }

    if ($edit_action == 'delete') {
        include('delete_post_confirmation.php');
    }
    ?>
</section>


<section>
    <?php
    $posts = get_posts(
        array(
            'posts_per_page' => -1,
            'post_status' => 'any',
            'author'    => get_current_user_id(),
            'post_type' => 'case_study'
        )
    );
    pretty_print_r($posts);
    if ($posts) {
        echo sprintf(esg_get_section_format('default'), 'My Case Studies');
        echo '<div id="case-study-cards" class="cards-wrapper">';
        foreach ($posts as $post) {
            include('case_study/tpl-overview-case-study.php');
        }
        echo '</div>';
    }
    ?>
</section>