<form id="basicFields" method="POST" action="<?php echo   $_SERVER['REQUEST_URI']; ?>">


    <h2>Practice Locations</h2>

    <?php echo get_hidden_fields_for_repeater('practice_locations_details'); ?>


    <div>
        <input type="hidden" name="updateProfile" value="1"><button type="submit">Update</button>
    </div>
</form>