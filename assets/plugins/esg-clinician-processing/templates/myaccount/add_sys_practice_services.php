<?php
$referrer = $_SERVER['HTTP_REFERER'] ?? 'default';
?>
<form id="addNewSystemLocationServices" method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">

    <h3>Didn't find your practice services?<br /><small>Add the details below</small></h3>
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
        <input type="hidden" name="original_ref" value="<?php echo $referrer; ?>">
        <button type="submit">Add Service Option(s)</button>
    </div>
</form>