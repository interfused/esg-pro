<?php
$max_records_cnt  = 5;
$form_id = 'add_sys_edu_degrees';
$referrer = $_SERVER['HTTP_REFERER'] ?? 'default';
?>

<form id="<?php echo $form_id; ?>" method="POST" action="<?php echo get_edit_profile_link('education');  ?>">
    <h3>Didn't find your education degree option(s) listed previously?<br /><small>Add up to <?php echo $max_records_cnt; ?> details below</small></h3>
    <hr>
    <?php
    for ($i = 1; $i <= $max_records_cnt; $i++) {
        esg_generate_input_field('text', 'Education Degree Title ' . $i, 'studydegree_' . $i, false);
    }
    $nonce = wp_create_nonce($form_id);
    ?>
    <input type="hidden" name="<?php echo $form_id; ?>" value="1" />
    <input type="hidden" name="_wpnonce" value="<?php echo $nonce; ?>" />

    <div class="btnActions">
        <input type="hidden" name="success_edit_url" value="<?php echo $referrer; ?>&edit_success=3">
        <button type="submit">Add Degree Option(s)</button>
    </div>
</form>