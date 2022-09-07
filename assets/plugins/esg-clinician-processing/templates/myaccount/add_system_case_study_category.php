<?php
$form_tag = 'add_case_study_categories';
?>
<form id="<?php echo $form_tag; ?>" method="POST" action="<?php echo get_edit_profile_link('case_studies') ?>">
    <h3>Didn't find your case study categories?<br /><small>Add the details below</small></h3>
    <hr>
    <?php
    for ($i = 1; $i <= 10; $i++) {
        esg_generate_input_field('text', 'Case Study Category Title ' . $i, $form_tag . '_' . $i, false);
    }
    $nonce = wp_create_nonce($form_tag);
    ?>
    <input type="hidden" name="<?php echo $form_tag; ?>" value="1" />
    <input type="hidden" name="_wpnonce" value="<?php echo $nonce; ?>" />

    <div class="btnActions">
        <button type="submit">Add Option(s)</button>
    </div>
</form>