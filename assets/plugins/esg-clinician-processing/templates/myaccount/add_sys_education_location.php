<?php
$form_tag = 'add_sys_education_location';
$referrer = $_SERVER['HTTP_REFERER'] ?? 'default';
?>
<form id="<?php echo $form_tag; ?>" method="POST" action="<?php echo get_edit_profile_link('education'); ?>">
    <h3>Didn't find your eduational institution?<br /><small>Add the details below</small></h3>
    <?php
    esg_generate_input_field('text', 'Name of facility', 'title');
    esg_generate_input_field('text', 'Address', 'address_1');
    esg_generate_input_field('text', 'Suite/Building/etc', 'address_2', false);
    esg_generate_input_field('text', 'Zipcode', 'zipcode');
    ?>

    <div class="filteredLocations">
        <h3>Choose the appropriate location below</h3>
        <div class="dynamicContentWrapper"></div>
        <?php
        esg_generate_input_field('text', 'City', 'city');
        esg_generate_input_field('text', 'State', 'state');
        //wp_nonce_field('add-system-practice-location');
        $nonce = wp_create_nonce('add-system-practice-location');
        ?>
        <input type="hidden" name="zipTableRecId" />
        <input type="hidden" name="<?php echo $form_tag; ?>" value="1" />
        <input type="hidden" name="_wpnonce" value="<?php echo $nonce; ?>" />
    </div>
    <div class="btnActions">
        <input type="hidden" name="success_edit_url" value="<?php echo $referrer; ?>&edit_success=2">
        <button type="submit">Add Location</button>
    </div>
</form>