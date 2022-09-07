<?php
$max_records_cnt  = 10;
$referrer = $_SERVER['HTTP_REFERER'] ?? 'default';
?>
<form id="addNewSystemLocationServices" method="POST" action="<?php echo get_edit_profile_link('practice_locations'); ?>">
    <h3>Didn't find your practice services option(s)?<br /><small>Add up to <?php echo $max_records_cnt; ?> details below</small></h3>
    <?php
    for ($i = 1; $i <= 10; $i++) {
        esg_generate_input_field('text', 'Service Title ' . $i, 'service_' . $i, false);
    }
    $nonce = wp_create_nonce('add-system-practice-services');
    ?>
    <input type="hidden" name="addSystemPracticeServices" value="1" />
    <input type="hidden" name="_wpnonce" value="<?php echo $nonce; ?>" />

    <div class="btnActions">
        <input type="hidden" name="success_edit_url" value="<?php echo $referrer; ?>&edit_success=3">
        <button type="submit">Add Option(s)</button>
    </div>
</form>