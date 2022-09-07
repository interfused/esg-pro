<h2>Edit Profile Dashboard</h2>
<p>profile editing content goes here</p>
<?php
/*
$settings = array(
    'media_buttons' => true
);

wp_editor('', 'test_id',  $settings);
*/
//echo do_shortcode('[frontend_admin form="591"]');
/*
acf_form();case_study_editor
*/

acf_form(array(
    'form' => true,
    'post_id'       => 'new_post',
    'new_post'      => array(
        'post_type'     => 'case_study',
        'post_status'   => 'pending'
    ),
    'fields' => ['case_study_editor'],

    'post_title'    => false,
    'post_content'  => false,

    'html_before_fields' =>  '<small>Edit your content below</small></h2>',
    'return' => $uri_parts[0] . '?mode=case_studies&edit_success=1&id=%post_id%',
    'updated_message' => __('this is the updated message', 'acf'),
    'html_updated_message'  => '<div id="message" class="alert alert-info"><p>%s</p></div>',
    'submit_value'  => __('Save')
));
?>