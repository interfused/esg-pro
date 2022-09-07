<form id="addSystemPracticeLocation" method="POST" action="<?php echo get_edit_profile_link('basic_clinician_fields'); ?>">
    <h3>Clinician Profile fields</h3>
    <?php
    esg_generate_input_field('text', 'NPI Number', 'npi_number2');
    echo taxonomy_to_checkboxes('professional_membership', 'professional_membership', 'Your associated professional membership(s):');
    ?>
    <p>Don't see your associated membership(s)?
        <a href="<?php echo esg_get_profile_edit_url('add_sys_loc_memberships'); ?>">Add New</a>
    </p>

    <div class="btnActions">
        <button type="submit">Update</button>
    </div>
</form>