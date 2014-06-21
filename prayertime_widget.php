<?php
include("lib/PrayTime.php");

add_action("widgets_init", function () { register_widget("PrayerTimeWidget"); });


class PrayerTimeWidget extends WP_Widget
{
    public function __construct() {
        parent::__construct("prayertime_widget", "Prayer Time Widget",
            array("description" => "A widget to display the prayer times"));
    }

    public function form($instance) {
        $default = array('title' => 'Prayer Times',
                    'show_iqama' => TRUE);
        $instance = wp_parse_args( (array) $instance, $default );
        $title = $instance['title'];
        $show_iqama = $instance['show_iqama'];
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' );?>">Title:</label>
            <input class="widefat"
                   id="<?php echo $this->get_field_id( 'title' );?>"
                   name="<?php echo $this->get_field_name( 'title' );?>"
                   type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'show_iqama' );?>">Show Iqama Times:</label>
            <input class="widefat"
                   id="<?php echo $this->get_field_id( 'show_iqama' );?>"
                   name="<?php echo $this->get_field_name( 'show_iqama' );?>"
                   type="checkbox" value="true" <?php echo $show_iqama ? 'checked="checked"' : "" ?>"/>
        </p>
    <?php
    }

    public function update($newInstance, $oldInstance) {
        $values = array();
        $values["title"] = strip_tags($newInstance["title"]);
        $values["show_iqama"] = $newInstance["show_iqama"] == "true";
        return $values;
    }

    public function widget($args, $instance) {
        extract($args);
        extract($instance);

        $title = apply_filters('widget_title', $title);

        echo $before_widget;
        echo $before_title . $title . $after_title;

        $pt = $this->getPrayerTimes();
        $it = null;
        if ($show_iqama) {
            $it = $this->getIqamaTimes();
        }
        echo $this->getMarkup($pt, $it);

        echo $after_widget;
    }

    protected function getPrayerTimes() {
        $options = get_option('fzami_options');
        $latitude = $options['latitude'];
        $longitude = $options['longitude'];
        $method = $options['calc_method'];
        $asrMethod = $options['asr_method'];
        $timeZone = $options['timezone'];
        $timeFormat = $options['time_format'];

        $prayTime = new PrayTime($method);
        $prayTime->setAsrMethod($asrMethod);
        $prayTime->setTimeFormat($timeFormat);
        $times = $prayTime->getPrayerTimes(time(), $latitude, $longitude, $timeZone);

        return array(
            "fajr" => $times[0],
            "zuhr" => $times[2],
            "asr" => $times[3],
            "maghrib" => $times[5],
            "isha" => $times[6],
        );
    }

    protected function getIqamaTimes() {
        $options = get_option('fzami_options');

        return array(
            "fajr" => $options['iqama_fajr'],
            "zuhr" => $options['iqama_zuhr'],
            "asr"=> $options['iqama_asr'],
            "maghrib"=> $options['iqama_maghrib'],
            "isha" => $options['iqama_isha'],
        );
    }

    protected function getMarkup($pt, $it) {
        $showIqama = !is_null($it);
        if ($showIqama) {
            $iqamas = array_map(function($time) { return fzami_format_time($time); }, $it);
        }
        return "<table class=\"prayertime\">".
            "<tr><th></th><th>Azhan</th>".($showIqama ? "<th>Iqama</th>" : "")."</tr>".
            "<tr><th>Fajr</th><td class=\"azhan\">{$pt['fajr']}</td>".($showIqama ? "<td class=\"iqama\">{$iqamas['fajr']}</td>" : "")."</tr>".
            "<tr><th>Zuhr</th><td class=\"azhan\">{$pt["zuhr"]}</td>".($showIqama ? "<td class=\"iqama\">{$iqamas['zuhr']}</td>" : "")."</tr>".
            "<tr><th>Asr</th><td class=\"azhan\">{$pt["asr"]}</td>".($showIqama ? "<td class=\"iqama\">{$iqamas['asr']}</td>" : "")."</tr>".
            "<tr><th>Maghrib</th><td class=\"azhan\">{$pt["maghrib"]}</td>".($showIqama ? "<td class=\"iqama\">{$iqamas['maghrib']}</td>" : "")."</tr>".
            "<tr><th>Isha</th><td class=\"azhan\">{$pt["isha"]}</td>".($showIqama ? "<td class=\"iqama\">{$iqamas['isha']}</td>" : "")."</tr>".
            "</table>";
    }
}