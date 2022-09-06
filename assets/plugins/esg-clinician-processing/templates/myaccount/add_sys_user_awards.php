<?php
acf_form(array(
    'post_id' => 'user_' . get_current_user_id(),
    'fields'       => ['recognition_and_awards'],
    'post_title'    => false,
    'post_content'  => false,
    'updated_message' => __("Updated Recognition and Awards", 'acf'),
    'html_updated_message'  => '<div id="message" class="alert alert-info"><p>%s</p></div>',
    'submit_value'  => __('Save')
));
?>
add_sys_user_awards template