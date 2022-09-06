<?php
$uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
$max_records_cnt  = 10;
$form_id = 'add_sys_edu_studies';
?>

<form id="<?php echo $form_id; ?>" method="POST" action="<?php $uri_parts[0]; ?>?mode=education">
    <h3>Didn't find your field(s) of study listed previously?<br /><small>Add up to <?php echo $max_records_cnt; ?> details below</small></h3>
    <hr>
    <?php
    for ($i = 1; $i <= $max_records_cnt; $i++) {
        esg_generate_input_field('text', 'Field of Study Title ' . $i, 'studyfield_' . $i, false);
    }
    $nonce = wp_create_nonce($form_id);
    ?>
    <input type="hidden" name="<?php echo $form_id; ?>" value="1" />
    <input type="hidden" name="_wpnonce" value="<?php echo $nonce; ?>" />

    <div class="btnActions">
        <button type="submit">Add Field(s) of Study</button>
    </div>
</form>