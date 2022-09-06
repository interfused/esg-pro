<?php
//$edit_case_study_post_id = get_new_case_study_post_setup();
?>
<section>


    <?php
    $updated_message = "Your submission is pending review";
    if ('publish' === get_post_status($edit_case_study_post_id)) {
        $updated_message = "Your submission has been saved";
    }
    echo 'attempt case study edit edit_case_study_post_id: ' . $edit_case_study_post_id;
    acf_form(array(
        'form' => true,
        'post_id'       => $edit_case_study_post_id,

        'post_title'    => false,
        'post_content'  => true,

        'html_before_fields' => '<h2>' . $_POST['post_title'] . '<br /><small>Edit your content below</small></h2>',
        'return' => $uri_parts[0] . '?mode=case_studies&edit_success=1&id=%post_id%',
        'updated_message' => __($updated_message, 'acf'),
        'html_updated_message'  => '<div id="message" class="alert alert-info"><p>%s</p></div>',
        'submit_value'  => __('Save')
    ));
    ?>

</section>