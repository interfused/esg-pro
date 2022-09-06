<?php
$uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
?>
<form id="addNewSystemLocationServices" method="POST" action="<?php $uri_parts[0]; ?>?mode=practice_locations">
    <h3>Didn't find your practice services?<br /><small>Add the details below</small></h3>
    <?php
    for ($i = 1; $i <= 10; $i++) {
        esg_generate_input_field('text', 'Service Title ' . $i, 'service_' . $i, false);
    }
    $nonce = wp_create_nonce('add-system-practice-services');
    ?>
    <input type="hidden" name="addSystemPracticeServices" value="1" />
    <input type="hidden" name="_wpnonce" value="<?php echo $nonce; ?>" />

    <div class="btnActions">
        <button type="submit">Add Service(s)</button>
    </div>
</form>