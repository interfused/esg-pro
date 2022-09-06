<?php
$uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
$max_records_cnt  = 10;
?>
<form id="addNewSystemLocationMemberships" method="POST" action="<?php $uri_parts[0]; ?>?mode=basic_clinician_fields">
    <h3>Didn't find your professional membership(s)?<br /><small>Add up to <?php echo $max_records_cnt; ?> details below</small></h3>
    <hr>
    <?php
    for ($i = 1; $i <= $max_records_cnt; $i++) {
        esg_generate_input_field('text', 'Membership Title ' . $i, 'membership_title_' . $i, false);
    }
    $nonce = wp_create_nonce('addNewSystemLocationMemberships');
    ?>
    <input type="hidden" name="addNewSystemLocationMemberships" value="1" />
    <input type="hidden" name="_wpnonce" value="<?php echo $nonce; ?>" />

    <div class="btnActions">
        <button type="submit">Add Membership(s)</button>
    </div>
</form>