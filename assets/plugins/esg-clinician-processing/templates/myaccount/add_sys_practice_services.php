<?php
$referrer = $_SERVER['HTTP_REFERER'] ?? 'default';
?>
<form id="addNewSystemLocationServices" method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">

    <h3>Didn't find your practice service option(s)?<br /><small>Add up to 10 details below</small></h3>
    <div clas="formFieldsWrapper columns-2">
        <?php
        for ($i = 1; $i <= 10; $i++) {
            esg_generate_input_field('text', 'Service Title ' . $i, 'service_' . $i, false);
        }
        $nonce = wp_create_nonce('add-system-practice-services');
        ?>
    </div>
    <input type="hidden" name="addSystemPracticeServices" value="1" />
    <input type="hidden" name="_wpnonce" value="<?php echo $nonce; ?>" />

    <div class="btnActions">
        <input type="hidden" name="success_edit_url" value="<?php echo $referrer; ?>&edit_success=3">
        <button type="submit">Add Service Option(s)</button>
    </div>
</form>