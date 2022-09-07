<?php
if (is_user_logged_in()) {
    require_once(ABSPATH . 'wp-admin/includes/post.php');
} else {
    die('Must be logged in: <a href="/login">Login</a>');
}
$max_terms_to_insert = 10;

function get_profile_alert()
{
    $message = $_GET['alert'] ?? "default";
    $mode = $_GET['mode'] ?? "default";
    $edit_success = $_GET['edit_success'] ?? "default";
    $delete_success = $_GET['delete_success'] ?? "default";


    $div_opening_tag = '<div class="alert alert-info">';
    $div_closing_tag = '</div>';

    if ('case_studies' === $mode && 1 == $edit_success) {
        $message = 'Case study has been edited';
    }

    if (1 == $delete_success) {
        $message = 'The post has been deleted';
    }


    if (in_array($mode, array('practice_locations', 'education', 'fellowship')) && 2 == $edit_success) {
        $message = 'New location has been added';
    }

    if (3 == $edit_success) {
        $message = 'New option(s) have been added';
    }

    if ($message !== 'default') {
        echo $div_opening_tag . $message . $div_closing_tag;
    }
}

function get_new_case_study_post_setup()
{
    echo 'attempt new case study post setup';
    pretty_print_r($_POST);

    $user_id = get_current_user_id();

    $meta_values = $_POST;
    foreach (array('post_title', 'post_excerpt', 'esg_new_case_study_setup') as $k) {
        unset($meta_values[$k]);
    }

    $csv = csv_taxonomy_terms_from_array('case_study_category', $_POST['case_study_categories']);
    $arr2 = explode(',', $csv);

    $custom_taxonomies = array();
    $custom_taxonomies['case_study_category'] = $arr2;


    $args = array(
        'post_title' => convert_to_title_case($_POST['post_title']),
        'post_type' => 'case_study',
        'post_excerpt' => $_POST['post_excerpt'],
        'post_author' => $user_id,
        'tax_input'   => $custom_taxonomies
    );

    $edit_case_study_post_id = wp_insert_post($args);

    $arg = array(
        'ID' => $edit_case_study_post_id,
        'post_author' => get_current_user_id(),
    );
    wp_update_post($arg);

    echo 'added case study post id: ' . $edit_case_study_post_id . ' for user id: ' . $user_id;
    return $edit_case_study_post_id;
}

function convert_to_title_case($str)
{
    $smallwordsarray = array(
        'of', 'a', 'the', 'and', 'an', 'or', 'nor', 'but', 'is', 'if', 'then', 'else', 'when',
        'at', 'from', 'by', 'on', 'off', 'for', 'in', 'out', 'over', 'to', 'into', 'with'
    );

    $words = explode(' ', $str);
    foreach ($words as $key => $word) {
        if (!$key or !in_array($word, $smallwordsarray))
            $words[$key] = ucwords($word);
    }



    $result = "";

    $arr = array();
    $pattern = '/([;:,-.\/ X])/';
    $array = preg_split($pattern, $str, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

    foreach ($array as $k => $v) {
        $result .= ucwords(strtolower($v));
    }

    //$result = str_replace("Mr.", "", $result); ->If you don't want Mr. in a String
    //return $result;


    $lower_case_words = array('the', 'a', 'of', 'an');
    $smallwordsarray = array(
        'of', 'a', 'the', 'and', 'an', 'or', 'nor', 'but', 'is', 'if', 'then', 'else', 'when',
        'at', 'from', 'by', 'on', 'off', 'for', 'in', 'out', 'over', 'to', 'into', 'with'
    );

    $pieces = explode(" ", $result);

    for ($i = 0; $i < count($pieces); $i++) {
        $piece = $pieces[$i];

        //first word should be capitalized regardless
        if ($i > 0 && in_array(strtolower($piece), $lower_case_words)) {

            $pieces[$i] = strtolower($piece);
        }
    }

    $result = implode(' ', $pieces);
    return $result;
}

function get_practice_address($post_id, $format = 'default')
{
    $meta = get_post_meta($post_id);
    $acf_post_reference = $post_id;

    $address_1 = get_field('address_1', $acf_post_reference);
    $address_2 = get_field('address_2', $acf_post_reference);
    $city = get_field('city', $acf_post_reference);
    $state = get_field('state', $acf_post_reference);
    $zipcode = get_field('zipcode', $acf_post_reference);

    switch ($format) {
        default:
            return "$address_1 $address_2 $city, $state $zipcode";
    }


    $html = pretty_print_r($meta, true);
    $html .= $address_1;
    return $html;
}

function esg_get_section_format($format = "default")
{
    switch ($format) {

        default:
            return '<h2 class="section_header">%s</h2>';
    }
}

function get_edit_profile_link($mode = "not_defined")
{
    $uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
    switch ($mode) {
        case "not_defined":
            return $uri_parts[0];
        default:
            return $uri_parts[0] . '?mode=' . $mode;
    }
}

function include_conditional_display_template($display_mode = "default")
{
    $display_template = 'dashboard-edit-profile.php';
    $is_acf_form = false;

    $acf_user_post_ref = 'user_' . get_current_user_id();
    $html_updated_message = '<div id="message" class="alert alert-info"><p>%s</p></div>';

    $format_section_header = '<h1 class="section_header">%s</h1>';

    $add_system_membership_options_html = '<p>Don\'t see your associated membership option(s)? ';
    $add_system_membership_options_html .= '<br/><a class="btn" href="' . get_edit_profile_link('add_sys_loc_memberships')  . '">Add option(s)</a></p>';

    $add_sys_practice_location_html = '<a href="' . get_edit_profile_link('add_sys_practice_location') . '" class="btn" >Add new location option</a>';
    $add_sys_practice_services_html = '<a href="' . get_edit_profile_link('add_sys_practice_services')  . '" class="btn" >Add new service option(s)</a>';

    //default
    $acf_form_args = array(
        'post_id' => $acf_user_post_ref,
        'post_title'    => false,
        'post_content'  => false,
        'html_before_fields' => '',
        'updated_message' => __("Updated default message", 'acf'),
        'html_updated_message'  => $html_updated_message,
        'submit_value'  => __('Save')
    );


    if (isset($_GET['mode'])) {
        switch ($_GET['mode']) {

            case "basic_clinician_fields":

                $html_before_fields = sprintf($format_section_header, 'Clinician Profile fields');
                $html_before_fields .= $add_system_membership_options_html;

                $is_acf_form = true;
                $acf_form_args['fields'] = ['npi_number', 'professional_memberships'];
                $acf_form_args['updated_message'] = __("Updated basic clinician fields", 'acf');
                $acf_form_args['html_before_fields'] = $html_before_fields;
                /*
                acf_form(array(
                    'post_id' => $acf_user_post_ref,
                    'fields'       => ['npi_number', 'professional_memberships'],
                    'post_title'    => false,
                    'post_content'  => false,
                    'html_before_fields' => $html_before_fields,
                    'updated_message' => __("Updated basic clinian fields", 'acf'),
                    'html_updated_message'  => $html_updated_message,
                    'submit_value'  => __('Save')
                ));
                */
                break;
            case "recognition_and_awards":
                $is_acf_form = true;

                $acf_form_args['fields'] = ['recognition_and_awards'];
                $acf_form_args['updated_message'] = __("Updated Recognition and Awards", 'acf');
                //$display_template = esg_get_template_file('/myaccount/add_sys_user_awards.php');
                break;
            case "education":
                $pre_html = "<p><strong>Don't see your institution or study options listed below?</strong><br />";
                $pre_html .= ' <a href="' . get_edit_profile_link('add_sys_education_location') . '" class="btn" >Add new institution</a>';
                $pre_html .= ' <a href="' . get_edit_profile_link('add_sys_edu_studies') . '" class="btn" >Add new field of study option(s)</a>';
                $pre_html .= ' <a href="' . get_edit_profile_link('add_sys_edu_degrees') . '" class="btn" >Add new degree option(s)</a>';
                $pre_html .= '</p>';
                $is_acf_form = true;
                $acf_form_args['fields'] = ['education'];
                $acf_form_args['html_before_fields'] = $pre_html;
                $acf_form_args['updated_message'] = __("Updated Education", 'acf');


                break;
            case "residency":
                $pre_html = "<p><strong>Don't see your residency institution listed in the options below?</strong><br />";
                $pre_html .= ' ' . $add_sys_practice_location_html;

                $is_acf_form = true;

                $acf_form_args['fields'] = ['residency'];
                $acf_form_args['html_before_fields'] = $pre_html;
                $acf_form_args['updated_message'] = __("Updated Residency Information", 'acf');

                break;
            case "fellowship":
                $pre_html = "<p><strong>Don't see your fellowship institution or service/practice listed in the options below?</strong><br />";
                $pre_html .= ' ' . $add_sys_practice_location_html;
                $pre_html .= ' <a href="' . get_edit_profile_link('add_system_service_category') . '" class="btn" >Add new service option(s)</a>';

                $is_acf_form = true;
                $acf_form_args['fields'] = ['fellowship'];
                $acf_form_args['html_before_fields'] = $pre_html;
                $acf_form_args['updated_message'] = __("Updated Fellowship Info", 'acf');
                $acf_form_args['return'] = get_edit_profile_link('fellowship&updated=true');
                break;


            case "case_studies":
                $is_acf_form = false;
                $display_template = esg_get_template_file('/myaccount/' . $_GET['mode'] . '.php');
                break;
            default:
                $display_template = esg_get_template_file('/myaccount/' . $_GET['mode'] . '.php');
        }
    }

    if (!$is_acf_form) {
        include($display_template);
    } else {
        acf_form($acf_form_args);
    }
}


function get_practice_locations()
{
    global $wp;
    $args = array(
        'post_type' => 'practice-location',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC'
    );
    $posts = get_posts($args);
    return $posts;
}
function taxonomy_to_checkboxes($taxonomy, $field_name, $label = 'test label', $post_type = 'practice-location')
{
    $taxonomy_terms = get_taxonomy_terms_except_uncategorized($post_type, $taxonomy);

    $terms = get_terms(array(
        'taxonomy' => $taxonomy,
        'hide_empty' => false,
    ));
    $html = print_r($terms, true);
    //return $html;
    $html = '<fieldset class="' . $field_name . '">      
    <legend>' . $label . '</legend>';
    $html .= '<ul class="bare columns-2">';
    foreach ($taxonomy_terms as $term) {
        $html .= '<li>';
        $html .= '<div class="formFieldWrapper checkbox">';
        $html .= '<input id="' . $term->slug . '" type="checkbox" name="' . $field_name . '[]" value="' . $term->term_id . '">';
        $html .= '<label for="' . $term->slug . '">' . $term->name . '</label>';
        $html .= '</div>';
        $html .= '</li>';
    }
    $html .= '</ul>';
    if (count($taxonomy_terms) <= 0) {

        $html .= '<p>No options found. <a  href="' . get_edit_profile_link('add_system_' . $taxonomy) . '">Add New Option(s)</a></p>';
    }
    $html .= ' </fieldset>';



    return $html;
}

function success_edit_url_redirect()
{
    $new_url = $_POST['success_edit_url'] ?? 'default';
    if ($new_url != 'default') {
        header('Location: ' . $new_url);
    }
}

function esg_add_system_practice_location($user_id = -1)
{
    /*
    echo '<h3>Attempt to update profile with</h3>';
    echo '<pre>' . print_r($_POST, true) . '</pre>';
    */
    $base_args = array(
        'post_title' => convert_to_title_case($_POST['title']),
        'post_type' => 'practice-location'
    );

    if (post_exists($base_args['post_title'],  '',  '', $base_args['post_type'])) {
        return -1;
    }

    $meta_values = $_POST;
    unset($meta_values['title']);
    unset($meta_values['addSystemPracticeLocation']);

    $post_id = wp_insert_post(
        array_merge($base_args, array(
            'post_status' => 'publish',
            'meta_input' => $meta_values

        ))
    );

    success_edit_url_redirect();
}



function esg_add_system_edu_location($user_id = -1)
{

    //echo '<h3>Attempt esg_add_system_edu_location</h3>';
    //echo '<pre>' . print_r($_POST, true) . '</pre>';

    $base_args = array(
        'post_title' => convert_to_title_case($_POST['title']),
        'post_type' => 'education-facility'
    );

    if (post_exists($base_args['post_title'],  '',  '', $base_args['post_type'])) {
        return -1;
    }

    $meta_values = $_POST;
    unset($meta_values['title']);
    unset($meta_values['add_sys_education_location']);
    unset($meta_values['_wpnonce']);

    $post_id = wp_insert_post(
        array_merge($base_args, array(
            'post_status' => 'publish',
            'meta_input' => $meta_values

        ))
    );

    //success_edit_url_redirect();
}

function esg_add_system_practice_services()
{

    for ($i = 1; $i <= 10; $i++) {
        if (isset($_POST['service_' . $i])) {
            $input = convert_to_title_case($_POST['service_' . $i]);
            wp_insert_term($input,  'service_category');
        }
    }

    success_edit_url_redirect();
}

function esg_add_system_taxonomy_options_from_post()
{
    $max_terms_to_insert = 10;


    /** GET NEW SYSTEM TAXONOMY OPTIONS FROM POST */
    $add_taxonomy_options = array();
    $add_taxonomy_options[] = array('post_key' => 'add_sys_edu_studies', 'field_input_pre' => 'studyfield_', 'taxonomy' => 'major_study');
    $add_taxonomy_options[] = array('post_key' => 'add_sys_edu_degrees', 'field_input_pre' => 'studydegree_', 'taxonomy' => 'education_level');
    $add_taxonomy_options[] = array('post_key' => 'addNewSystemLocationMemberships', 'field_input_pre' => 'membership_title_', 'taxonomy' => 'professional_membership');
    $add_taxonomy_options[] = array('post_key' => 'add_case_study_categories', 'field_input_pre' => 'add_case_study_categories_', 'taxonomy' => 'case_study_category');

    foreach ($add_taxonomy_options as $option) {
        $pre = $option['field_input_pre'];
        if (isset($_POST[$option['post_key']])) {
            for ($i = 1; $i <= $max_terms_to_insert; $i++) {
                if (isset($_POST[$pre . $i])) {
                    $input = convert_to_title_case($_POST[$pre . $i]);

                    wp_insert_term($input,  $option['taxonomy']);
                }
            }
        }
    }
    success_edit_url_redirect();
}

function esg_practice_details_to_my_list()
{
    /*
    echo 'esg_practice_details_to_my_list';
    pretty_print_r($_POST);
    */
    $user_id = get_current_user_id();
    $acf_repeater_group = 'practice_locations_details';
    $repeater_field1 = 'practice_location';
    $repeater_field2 = 'services_offered';

    $values = [];
    $values[$repeater_field1] = $_POST['practice_location_id'];
    if (isset($_POST['practice_service_categories'])) {
        $values[$repeater_field2] = $_POST['practice_service_categories'];
    }


    add_row($acf_repeater_group, $values, 'user_' . $user_id);
}


/**
 * 
 */
function esg_generate_input_field($input_type, $label_text, $input_name, $is_required = true)
{
    $required_str = ' ';
    if ($is_required) {
        $required_str = ' required ';
        $label_text .= ' *';
    } else {
    }


    $html = '<div class="formFieldWrapper">';
    $html .= '<label for="' . $input_name . '">' . $label_text . '</label>';

    switch ($input_type) {
        case "textarea":
            $html .= '<textarea name="' . $input_name . '"  rows="4"  ></textarea>';
            break;
        default:
            $html .= '<input name="' . $input_name . '" type="text" ' . $required_str . ' />';
    }


    $html .= '</div>';
    echo $html;
}

function get_city_state_zip_from_table()
{
    global $wpdb;
    $sql = 'SELECT id, zipcode, city, state, county FROM zipcodes';
    $results = $wpdb->get_results($sql, ARRAY_A);
    return $results;
}


function get_taxonomy_terms_except_uncategorized($post_type, $taxonomy_slug)
{
    $taxonomies = get_object_taxonomies($post_type);

    $exclude_ids   = array();
    $exclude_names = array("Uncategorized");

    foreach ($exclude_names as $name) {
        $excluded_term = get_term_by('name', $name, $taxonomy_slug);
        if (isset($excluded_term->term_id)) {
            $exclude_ids[] = (int) $excluded_term->term_id; // Get term_id (as a string), typcast to an INT
        }
    }

    $args = array(
        'exclude' => $exclude_ids,
        'taxonomy' => $taxonomy_slug,
        'hide_empty' => false
    );

    $taxonomy_terms = get_terms($args);
    return $taxonomy_terms;
}

function get_field_setup($input_name, $label = "")
{
    $label_text = ($label !== "") ? $label : $input_name;
    $html = '<div class="esg_field_wrapper"><label>' . $label_text . '</label><input type="text" name="' . $input_name . '" /></div>';
    return $html;
}

function csv_taxonomy_terms_from_array($taxonomy, $array)
{
    $terms = get_terms(array(
        'taxonomy' => $taxonomy,
        'hide_empty' => false,
    ));
    //$html = json_encode($terms);
    $values = [];
    foreach ($terms as $term) {
        if (in_array($term->term_id, $array)) {
            array_push($values, $term->name);
        }
    }
    return implode(', ', $values);
}
/**
 * returns html
 */
function get_hidden_fields_for_repeater($repeater_field)
{

    $html = '';
    $acf_post_ref = 'user_' . get_current_user_id();
    $data = get_field($repeater_field, $acf_post_ref);
    //pretty_print_r($data);
    if (!$data) {
        return $html;
    }
    $html .= '<div id="practice-location-cards" class="cards-wrapper">';
    foreach ($data as $row) {
        $html .= '<div class="card overview">';
        $html .= '<h4>' . $row['practice_location']->post_title . '</h4>';
        $html .= 'Address: ' . get_practice_address($row['practice_location']->ID);

        if ($row['services_offered']) {
            $html .= '<p><strong>Services Offered:</strong> ';
            $html .= csv_taxonomy_terms_from_array('service_category', $row['services_offered']);
        }
        $html .= '</p>';
        $html .= '</div>';
    }
    $html .= '</div>';
    return $html;
    if (have_rows($repeater_field, $acf_post_ref)) :
        while (have_rows($repeater_field)) : the_row();
            $location_id = get_sub_field('practice_location');
            $html .= 'location_id: ' . $location_id;

        // Do something...
        endwhile;
    else :
        // no rows found
        $html .= 'set it up: ' . $repeater_field;
        $test = get_field($repeater_field, false);
        $html .= '<pre>' . print_r($test, true) . '</pre>';
        $html .= get_sub_field('sub_field');
    endif;
    return $html;
}
?>

<?php
////// START PROCESSING
if (isset($_POST['addSystemPracticeLocation']) && isset($_REQUEST['_wpnonce']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'add-system-practice-location')) {
    esg_add_system_practice_location(get_current_user_id());
}

if (isset($_GET['delete_confirm'])) {
    echo 'ATETTEMPT DELTE';
    pretty_print_r($_POST);


    if (
        isset($_POST['esg_delete_post_nonce_field'])
        && wp_verify_nonce($_POST['esg_delete_post_nonce_field'], 'esg_delete_post')
    ) {
        echo 'proceed to detle';
        //FINAL CHECK
        if ($_POST['yes_confirm'] === 'YES') {
            wp_delete_post($_POST['delete_esg_post']);
            //
            $new_url = get_edit_profile_link($_POST['mode']) . '&delete_success=1';
            header('Location: ' . $new_url);
        }
    }
}


if (isset($_POST['add_sys_education_location'])) {
    esg_add_system_edu_location();
}

if (isset($_POST['addSystemPracticeServices']) && isset($_REQUEST['_wpnonce']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'add-system-practice-services')) {
    esg_add_system_practice_services();
}




esg_add_system_taxonomy_options_from_post();

if (isset($_POST['addPracticeDetailsToMyList']) && isset($_REQUEST['_wpnonce']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'addPracticeDetailsToMyList')) {
    esg_practice_details_to_my_list();
}




$esg_acf_keys = ['npi_number'];
$current_user = wp_get_current_user();

function esg_get_profile_edit_url($mode)
{
    $base_profile_rel_path = '/edit-profile';
    return $base_profile_rel_path . '?mode=' . $mode;
}

$display_mode = $_GET['mode'] ?? "default";
?>

<div class="row <?php echo $display_mode; ?>">
    <div class="col-md-3 swatch2">
        <nav class="sidebar <?php echo $display_mode; ?>">
            <ul class=" <?php echo $display_mode; ?>">
                <?php
                $nav_array = array();
                $nav_array[] = array('k' => 'basic_clinician_fields', 'display_txt' => 'Clinician Profile Fields');
                $nav_array[] = array('k' => 'practice_locations', 'display_txt' => 'My Practice Locations');
                $nav_array[] = array('k' => 'recognition_and_awards', 'display_txt' => 'Recognition / Awards');
                $nav_array[] = array('k' => 'education', 'display_txt' => 'Education');
                $nav_array[] = array('k' => 'residency', 'display_txt' => 'Residency');
                $nav_array[] = array('k' => 'fellowship', 'display_txt' => 'Fellowship');
                $nav_array[] = array('k' => 'case_studies', 'display_txt' => 'My Case Studies');

                foreach ($nav_array as $option) {
                    $active_class_text = $option['k'];
                    if ($display_mode == $option['k']) {
                        $active_class_text .= ' active';
                    }
                    echo '<li class="' . $active_class_text . '"><a href="' . esg_get_profile_edit_url($option['k']) . '">' . $option['display_txt'] . '</a></li>';
                }
                ?>

            </ul>
        </nav>
    </div>

    <div class="col-md-9">

        <?php
        get_profile_alert();
        include_conditional_display_template();
        ?>

        <p>extra content here</p>
    </div>

</div>


<?php
echo getEsgAjaxPathUrl('public');
?>
<style>
    .filteredLocations {
        margin-top: 1em;
    }

    .hidden {
        display: none;
    }
</style>