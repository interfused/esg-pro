<?php
$edit_step = $_GET['add_step'] ?? "default";

?>
<section>
    <?php
    echo sprintf(esg_get_section_format('default'), 'Add New Case Study');

    if ($edit_step == 'default') {
        echo do_shortcode('[frontend_admin form="562"]');
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
        echo '<div class="cards-wrapper">';
        foreach ($posts as $post) {
            include('case_study/tpl-overview-case-study.php');
        }
        echo '</div>';
        echo '<hr />';
    }
    ?>
</section>



<?php
$form_tag = 'esg_new_case_study';
$form_tag1 = 'esg_new_case_study_setup';
$form_tag2 = 'esg_new_case_study_content';

$nonce = wp_create_nonce($form_tag);
$nonce1 = wp_create_nonce($form_tag1);
$nonce2 = wp_create_nonce($form_tag2);

//var_dump(wp_get_current_user());

//echo do_shortcode('[wpuf_form id="394"]');
?>
<?php
if (!isset($_GET['add_step'])) {
?>
    <section id="step1">
        <form id="<?php echo $form_tag1; ?>" method="POST" action="<?php $uri_parts[0]; ?>?mode=case_studies&add_step=2">

            <h2>Add New Case Study</h2>

            <?php
            echo esg_generate_input_field('text', 'Case Study Title', 'post_title');
            echo esg_generate_input_field('textarea', 'Give a brief excerpt of your case study (55 words)', 'post_excerpt');
            ?>
            <?php
            echo taxonomy_to_checkboxes('case_study_category', 'case_study_categories', 'Case study categories', 'case_study');
            ?>
            <p>Don't see appropriate case study category option(s)? <a class="btn" href="<?php echo $uri_parts[0] ?>?mode=add_system_case_study_category">Add Option(s)</a></p>

            <input type="hidden" name="<?php echo $form_tag1; ?>" value="1" />
            <input type="hidden" name="_wpnonce" value="<?php echo $nonce1; ?>" />

            <div class="btnActions">
                <button type="submit">Continue</button>
            </div>
        </form>

    </section>
<?php
}
?>

<?php
if (isset($_GET['add_step']) && $_GET['add_step'] == 2) {

    $edit_case_study_post_id = get_new_case_study_post_setup();
    include('case_study-edit.php');
} ?>