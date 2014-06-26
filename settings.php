<?php

add_action('admin_menu', 'fzami_add_menu');
add_action('admin_init', 'fzami_init_register_settings');

function fzami_add_menu() {
    add_options_page('fzami Settings', 'fzami Settings', 'administrator', __FILE__, 'fzami_display_settings');
}

function fzami_init_register_settings() {
    register_setting( 'fzami_plugin_options', 'fzami_options', 'fzami_validate_settings');
//------------------------ Location Settings -------------------------//
    add_settings_section('location_settings', // Unique ID
        'Location Settings', // Name for this section
        'fzami_location_section', // Function to call
        __FILE__ // Page
    );
    add_settings_field('location_latitude',// Unique ID
        'Latitude', // Name for this field
        'fzami_location_latitude_field', //Function to call
        __FILE__, // Page
        'location_settings' // Section to belong to
    );
    add_settings_field('location_longitude',// Unique ID
        'Longitude', // Name for this field
        'fzami_location_longitude_field', //Function to call
        __FILE__, // Page
        'location_settings' // Section to belong to
    );
//------------------------ Calculation Settings -------------------------//
    add_settings_section('calculation_settings', // Unique ID
        'Calculation Settings', // Name for this section
        'fzami_calculation_section', // Function to call
        __FILE__ // Page
    );
    add_settings_field('calculate_method',// Unique ID
        'Calculation Method', // Name for this field
        'fzami_calculate_method_field', //Function to call
        __FILE__, // Page
        'calculation_settings' // Section to belong to
    );
    add_settings_field('calculate_asr_method',// Unique ID
        'Asr Method', // Name for this field
        'fzami_calculate_asr_method_field', //Function to call
        __FILE__, // Page
        'calculation_settings' // Section to belong to
    );
//------------------------ Display Settings -------------------------//
    add_settings_section('display_settings', // Unique ID
        'Display Settings', // Name for this section
        'fzami_display_section', // Function to call
        __FILE__ // Page
    );
    add_settings_field('display_timezone',// Unique ID
        'Timezone', // Name for this field
        'fzami_display_timezone_field', //Function to call
        __FILE__, // Page
        'display_settings' // Section to belong to
    );
    add_settings_field('display_time_format',// Unique ID
        'Time Format', // Name for this field
        'fzami_display_time_format_field', //Function to call
        __FILE__, // Page
        'display_settings' // Section to belong to
    );
//------------------------ Jumah Settings -------------------------//
    add_settings_section('jumah_settings', // Unique ID
        'Friday Prayer Settings', // Name for this section
        'fzami_jumah_section', // Function to call
        __FILE__ // Page
    );
    add_settings_field('jumah_first_data',// Unique ID
        'First Jumah', // Name for this field
        'fzami_jumah_first_data_field', //Function to call
        __FILE__, // Page
        'jumah_settings' // Section to belong to
    );
    add_settings_field('jumah_second_data',// Unique ID
        'Second Jumah', // Name for this field
        'fzami_jumah_second_data_field', //Function to call
        __FILE__, // Page
        'jumah_settings' // Section to belong to
    );
    add_settings_field('jumah_third_data',// Unique ID
        'Third Jumah', // Name for this field
        'fzami_jumah_third_data_field', //Function to call
        __FILE__, // Page
        'jumah_settings' // Section to belong to
    );

}

function fzami_display_settings() {
    ?>
    <div class="wrap">
        <?php screen_icon(); ?>
        <h2>fzami Prayer Time Settings</h2>
        <form method="post" action="options.php">

            <?php settings_fields('fzami_plugin_options'); ?>
            <?php do_settings_sections(__FILE__); ?>
            <?php submit_button();?>

        </form>
    </div>
<?php
}

function fzami_location_section() {
    echo '<p>You can change your location settings here.</p>';
}

function fzami_calculation_section() {
    echo '<p>You can change your prayer calculation settings here.</p>';
}

function fzami_display_section() {
    echo '<p>You can change your display settings here.</p>';
}

function fzami_jumah_section() {
    echo '<p>You can change your Friday prayer settings here.</p>';
}

function fzami_location_latitude_field() {
    $options = get_option('fzami_options');
    echo "<input id='fzami_latitude_input' name='fzami_options[latitude]' type='text' value='" . $options['latitude'] . "' />";
}

function fzami_location_longitude_field() {
    $options = get_option('fzami_options');
    echo "<input id='fzami_longitude_input' name='fzami_options[longitude]' type='text' value='" . $options['longitude'] . "' />";
}

function fzami_display_timezone_field() {
    $options = get_option('fzami_options');
    echo "<input id='fzami_timezone_input' name='fzami_options[timezone]' type='text' value='" . $options['timezone'] . "' />";
}

function fzami_display_time_format_field() {
    $options = get_option('fzami_options');
    echo "<select id='fzami_time_format_input' name='fzami_options[time_format]'>".
        "<option value=\"0\" ".($options["time_format"] == "0" ? 'selected="true"': '').">24 hour</option>".
        "<option value=\"1\" ".($options["time_format"] == "1" ? 'selected="true"': '').">12 hour (am/pm)</option>".
        "<option value=\"2\" ".($options["time_format"] == "2" ? 'selected="true"': '').">12 hour</option>".
        "</select>";
}

function fzami_calculate_method_field() {
    $options = get_option('fzami_options');
    echo "<select id='fzami_calc_method_input' name='fzami_options[calc_method]'>".
        "<option value=\"0\" ".($options["calc_method"] == "0" ? 'selected="true"': '').">Ithna Ashari</option>".
        "<option value=\"1\" ".($options["calc_method"] == "1" ? 'selected="true"': '').">University of Islamic Sciences, Karachi</option>".
        "<option value=\"2\" ".($options["calc_method"] == "2" ? 'selected="true"': '').">Islamic Society of North America (ISNA)</option>".
        "<option value=\"3\" ".($options["calc_method"] == "3" ? 'selected="true"': '').">Muslim World League (MWL)</option>".
        "<option value=\"4\" ".($options["calc_method"] == "4" ? 'selected="true"': '').">Umm al-Qura, Makkah</option>".
        "<option value=\"5\" ".($options["calc_method"] == "5" ? 'selected="true"': '').">Egyptian General Authority of Survey</option>".
        "<option value=\"7\" ".($options["calc_method"] == "7" ? 'selected="true"': '').">Institute of Geophysics, University of Tehran</option>".
        "</select>";
}

function fzami_calculate_asr_method_field() {
    $options = get_option('fzami_options');
    echo "<select id='fzami_asr_method_input' name='fzami_options[asr_method]'>".
        "<option value=\"0\" ".($options["asr_method"] == "0" ? 'selected="true"': '').">Standard</option>".
        "<option value=\"1\" ".($options["asr_method"] == "1" ? 'selected="true"': '').">Hanafi</option>".
        "</select>";
}

function fzami_jumah_first_data_field() {
    fzami_jumah_data_field("first");
}

function fzami_jumah_second_data_field() {
    fzami_jumah_data_field("second");
}

function fzami_jumah_third_data_field() {
    fzami_jumah_data_field("third");
}

function fzami_jumah_data_field($number) {
    $options = get_option('fzami_options');
    echo "<label for=\"fzami_jumah_{$number}_time_input\">Time:</label><input id='fzami_jumah_{$number}_time_input' name='fzami_options[jumah_{$number}_time]' type='text' value='" . $options["jumah_{$number}_time"] . "' />";
    echo "<label for=\"fzami_jumah_{$number}_khateeb_input\">Khateeb:</label><input id='fzami_jumah_{$number}_khateeb_input' name='fzami_options[jumah_{$number}_khateeb]' type='text' value='" . $options["jumah_{$number}_khateeb"] . "' />";
}

function fzami_validate_settings($input) {
    if ($input['timezone'] < -12 || $input['timezone'] > 12) {
        $input['timezone'] = "-5";
    }
    return $input;
}