<h2>Edit Profile Dashboard</h2>

<section id="marketing">
    <?php
    $marketing_page = get_page_by_title('Case Study Benefits');
    if ($marketing_page) {
        echo '';
        echo sprintf(esg_get_section_format('default'), $marketing_page->post_title);
        echo '<div class="overview">' . $marketing_page->post_content . '</div>';
    }
    ?>
</section>