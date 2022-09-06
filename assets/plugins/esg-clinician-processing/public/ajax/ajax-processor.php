<?php
require '../../../../../wp-load.php';

function esg_get_zipcode_data($zipcode)
{
    global $wpdb;
    $sql = 'SELECT * FROM `zipcodes` WHERE `zipcode` = "' . $zipcode . '"';
    $results = $wpdb->get_results($sql, ARRAY_A);
    echo json_encode($results);
}

//echo 'stuff: ' . print_r($_POST, true);


switch ($_POST['action']) {
    case "esg_zip_table_search":
        esg_get_zipcode_data($_POST['data']['zipcode']);
        break;
}
