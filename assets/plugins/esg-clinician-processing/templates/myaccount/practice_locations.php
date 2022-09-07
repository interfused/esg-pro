<form id="practiceLocations" method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">


    <section>

        <?php
        $html = get_hidden_fields_for_repeater('practice_locations_details');
        if ($html != '') {
            echo sprintf(esg_get_section_format('default'), 'My Current Practice Location(s)');
            echo $html;
            echo '<hr />';
        }
        ?>

    </section>
    <section id="addNew">
        <?php echo sprintf(esg_get_section_format('default'), 'Add Your New Practice Location & Services'); ?>

        <p>Don't see your practice location / service option(s) listed below?<br>
            <?php
            echo $add_sys_practice_location_html;
            echo ' ' . $add_sys_practice_services_html;
            ?>

        </p>
        <div class="search formFieldWrapper">

            <?php
            $locations = get_practice_locations();
            ?>
            <div class="">
                <label for='practice_location_id'>Practice Name & Location</label>
                <select name="practice_location_id" required class="select2Convert">
                    <option></option>
                    <?php
                    foreach ($locations as $location) {
                        $display_text = $location->post_title;

                        $display_text .= ' : ' . get_field("address_1", $location->ID);
                        $display_text .= get_field("address_2", $location->ID);
                        $display_text .= ' ' . get_field("city", $location->ID);
                        $display_text .= ', ' . get_field("state", $location->ID);
                        $display_text .= ' ' . get_field("zip", $location->ID);

                        echo '<option value="' . $location->ID . '">' . $display_text . '</option>';
                    }
                    ?>
                </select>

            </div>
        </div>
        <div class="formFieldWrapper">

            <?php
            echo taxonomy_to_checkboxes('service_category', 'practice_service_categories', 'Service(s) you provide at this location:');
            ?>


        </div>



        <?php
        $nonce = wp_create_nonce('addPracticeDetailsToMyList');
        ?>

        <div class="btnActions">
            <input type="hidden" name="addPracticeDetailsToMyList" value="1" />
            <input type="hidden" name="_wpnonce" value="<?php echo $nonce; ?>" />
            <button type="submit">Add to my list</button>
        </div>
    </section>
</form>
</div>