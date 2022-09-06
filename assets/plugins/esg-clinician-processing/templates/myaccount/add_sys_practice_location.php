<?php
$uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
?>
<form id="addSystemPracticeLocation" method="POST" action="<?php $uri_parts[0]; ?>?mode=practice_locations">
    <h3>Add Practice Location</h3>
    <hr>
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
        <input type="hidden" name="addSystemPracticeLocation" value="1" />
        <input type="hidden" name="_wpnonce" value="<?php echo $nonce; ?>" />
    </div>
    <div class="btnActions">
        <button type="submit">Add Location</button>
    </div>
</form>