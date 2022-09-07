<?php
$form_id = 'delete_esg_post';
$referrer = $_SERVER['HTTP_REFERER'] ?? 'default';
$post_id = $_GET['id'] ?? 0;
$action = $_GET['mode'] ?? 'default';
?>

<form id="<?php echo $form_id; ?>" class="text-center" method="POST" action="<?php echo get_edit_profile_link($action); ?>&delete_confirm=1">
    <h2>Are you sure you wish to delete your post?</h2>
    <h4><?php echo get_the_title($post_id); ?></h4>
    <p>NOTE: This CANNOT be reversed</p>
    <?php
    esg_generate_input_field('text', 'Type "YES" to confirm ', 'yes_confirm', false);
    $nonce = wp_create_nonce($form_id);
    ?>
    <input type="hidden" name="<?php echo $form_id; ?>" value="<?php echo $post_id; ?>" />
    <input type="hidden" name="mode" value="<?php echo $_GET['mode']; ?>" />

    <?php
    wp_nonce_field('esg_delete_post', 'esg_delete_post_nonce_field');
    ?>
    <div class="btnActions">
        <a class="btn" href="<?php echo $referrer; ?>">Cancel</a> <button type="submit">Confirm</button>
    </div>
</form>