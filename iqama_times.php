<?php

add_action('admin_menu', 'fzami_add_iqama_menu');
add_action('admin_init', 'fzami_init_register_iqama_times');

function fzami_add_iqama_menu() {
    add_options_page('Iqama Times', 'Iqama Times', 'administrator', __FILE__, 'fzami_display_iqama_times');
}

function fzami_init_register_iqama_times() {
    register_setting( 'fzami_plugin_iqama_times', 'fzami_iqama_times', 'fzami_validate_iqama_times');
//------------------------ Iqama Settings -------------------------//
    add_settings_section('iqama_times', // Unique ID
        '', // Name for this section
        'fzami_iqama_times_section', // Function to call
        __FILE__ // Page
    );
    add_settings_field('maghrib_offset',// Unique ID
        'Maghrib Offset', // Name for this field
        'fzami_iqama_maghrib_field', //Function to call
        __FILE__, // Page
        'iqama_times' // Section to belong to
    );

    wp_enqueue_style( 'fzami-styles', WP_PLUGIN_URL . "/wp-fzami/css/wp-fzami.css" );
    wp_enqueue_script( 'iqama_times', WP_PLUGIN_URL . '/wp-fzami/js/iqama_times.js', array(), '1.0.0', true );
}

function fzami_display_iqama_times() {
    $iqama_times = get_option('fzami_iqama_times');
    $next_num = 0;
    ?>
    <div class="wrap">
        <?php screen_icon(); ?>
        <h2>Iqama Times</h2>
        <form method="post" action="options.php">
            <?php settings_fields('fzami_plugin_iqama_times'); ?>

            <?php do_settings_sections(__FILE__); ?>
            <table>
                <tr><th>Date</th><th>Fajr</th><th>Zuhr</th><th>Asr</th><th class="maghrib">Maghrib</th><th>Isha</th></tr>

                <?php
                    $dates = array();
                    if (isset($iqama_times['dates'])) {
                        $dates = $iqama_times['dates'];
                    }
                    foreach(array_keys($dates) as $date) {
                        $iqama_time = $dates[$date];
                        ?>
                <tr id="iqama_<?=$next_num ?>">
                    <td><input name="iqama[]" value="<?=$next_num ?>" type="hidden"/>
                        <input name="fzami_iqama_times[date_<?=$next_num ?>]" size="10" value="<?=$date?>"/></td>
                    <td><input name="fzami_iqama_times[fajr_<?=$next_num ?>]" size="6"  value="<?=fzami_only_explicit_time($iqama_time['fajr'])?>" placeholder="<?=fzami_only_implicit_time($iqama_time['fajr'])?>"/></td>
                    <td><input name="fzami_iqama_times[zuhr_<?=$next_num ?>]" size="6"  value="<?=fzami_only_explicit_time($iqama_time['zuhr'])?>" placeholder="<?=fzami_only_implicit_time($iqama_time['zuhr'])?>"/></td>
                    <td><input name="fzami_iqama_times[asr_<?=$next_num ?>]" size="6"  value="<?=fzami_only_explicit_time($iqama_time['asr'])?>" placeholder="<?=fzami_only_implicit_time($iqama_time['asr'])?>"/></td>
                    <td class="maghrib"><input name="fzami_iqama_times[maghrib_<?=$next_num ?>]" size="6"  value="<?=fzami_only_explicit_time($iqama_time['maghrib'])?>" placeholder="<?=fzami_only_implicit_time($iqama_time['maghrib'])?>"/></td>
                    <td><input name="fzami_iqama_times[isha_<?=$next_num ?>]" size="6"  value="<?=fzami_only_explicit_time($iqama_time['isha'])?>" placeholder="<?=fzami_only_implicit_time($iqama_time['isha'])?>"/></td>
                </tr>
                <?php
                        $next_num++;
                    }
                ?>

                <tr id="iqama_<?=$next_num ?>">
                    <td><input name="iqama[]" value="<?=$next_num ?>" type="hidden"/>
                        <input name="fzami_iqama_times[date_<?=$next_num ?>]" size="10"/></td>
                    <td><input name="fzami_iqama_times[fajr_<?=$next_num ?>]" size="6"/></td>
                    <td><input name="fzami_iqama_times[zuhr_<?=$next_num ?>]" size="6"/></td>
                    <td><input name="fzami_iqama_times[asr_<?=$next_num ?>]" size="6"/></td>
                    <td class="maghrib"><input name="fzami_iqama_times[maghrib_<?=$next_num ?>]" size="6"/></td>
                    <td><input name="fzami_iqama_times[isha_<?=$next_num ?>]" size="6"/></td>
                </tr>
            </table>

            <?php submit_button();?>

        </form>
    </div>
<?php
}

function fzami_iqama_times_section() {
    echo '<p>You can change your iqama times here.</p>';
}

function fzami_iqama_maghrib_field() {
    $options = get_option('fzami_iqama_times');
    echo "<input id='fzami_iqama_maghrib_input' name='fzami_iqama_times[maghrib_offset]' type='text' value='" . $options['maghrib_offset'] . "' />".
         "<br/><sub>If this field is set, then the maghrib iqama will be X minutes after the azan.  Otherwise, maghrib iqama needs to be set explicitly.</sub>";
}


function fzami_validate_iqama_times($input) {
    $iqama_dates = array();
    $previous_iqama = array(
        'fajr' => '',
        'zuhr' => '',
        'asr' => '',
        'maghrib' => '',
        'isha' => '',
    );
    foreach(fzami_sorted_iqama_numbers($_POST['iqama'], $input) as $iqamaNum) {
        $iqamas = array();
        foreach($previous_iqama as $prayer => $iqama) {
            if ($input[$prayer.'_'.$iqamaNum] != '' &&
                ($time = date('H:i', strtotime($input[$prayer.'_'.$iqamaNum]))) != '00:00')
            {
                $previous_iqama[$prayer] = $iqamas[$prayer] = $time;
            } else {
                $iqamas[$prayer] = '?'.$previous_iqama[$prayer];
            }
        }
        $iqama_dates[$input['date_'.$iqamaNum]] = $iqamas;
    }
    return array(
        'maghrib_offset' => (int)$input['maghrib_offset'],
        'dates' => $iqama_dates,
    );
}

function fzami_sorted_iqama_numbers($iqama_nums, &$input) {
    $dateNumMap = array();
    $dates = array();
    foreach($iqama_nums as $num) {
        $date = $input['date_'.$num];
        if (($time = strtotime($date)) != 0) {
            $date = date('Y-m-d', $time);
            $input['date_'.$num] = $date;
            $dateNumMap[$date] = $num;
            array_push($dates, $date);
        }
    }
    sort($dates);
    return array_map(function($date) use ($dateNumMap) { return $dateNumMap[$date]; }, $dates);
}

function fzami_only_explicit_time($time) {
    if (!$time || $time[0] == '?') {
        return '';
    } else {
        return $time;
    }
}

function fzami_only_implicit_time($time) {
    if (!$time || $time[0] != '?') {
        return '';
    } else {
        return substr($time, 1);
    }
}

function fzami_any_time($time) {
    if ($time && $time[0] == '?') {
        return substr($time, 1);
    } else {
        return $time;
    }
}