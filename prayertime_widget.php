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
        $default = ['title' => '',
                    'show_iqama' => TRUE];
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
        echo $this->getMarkup($pt, $show_iqama);

        echo $after_widget;
    }

    protected function getPrayerTimes($settings = null) {
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

        $pt = [];
        $pt["fajr"] = [ "azhan" => $times[0], "iqama" => "6:15"];
        $pt["zuhr"] = [ "azhan" => $times[2], "iqama" => "13:45"];
        $pt["asr"] = [ "azhan" => $times[3], "iqama" => "18:30"];
        $pt["maghrib"] = [ "azhan" => $times[5], "iqama" => "20:45"];
        $pt["isha"] = [ "azhan" => $times[6], "iqama" => "22:15"];

        return $pt;
    }

    protected function getMarkup($pt, $showIqama = FALSE) {
        $iqamas = [
            'fajr' => fzami_format_time($pt['fajr']['iqama']),
            'zuhr' => fzami_format_time($pt['zuhr']['iqama']),
            'asr' => fzami_format_time($pt['asr']['iqama']),
            'maghrib' => fzami_format_time($pt['maghrib']['iqama']),
            'isha' => fzami_format_time($pt['isha']['iqama']),
        ];
        return "<table class=\"prayertime\">".
            "<tr><th></th><th>Azhan</th>".($showIqama ? "<th>Iqama</th>" : "")."</tr>".
            "<tr><th>Fajr</th><td class=\"azhan\">{$pt['fajr']['azhan']}</td>".($showIqama ? "<td class=\"iqama\">{$iqamas['fajr']}</td>" : "")."</tr>".
            "<tr><th>Zuhr</th><td class=\"azhan\">{$pt["zuhr"]["azhan"]}</td>".($showIqama ? "<td class=\"iqama\">{$iqamas['zuhr']}</td>" : "")."</tr>".
            "<tr><th>Asr</th><td class=\"azhan\">{$pt["asr"]["azhan"]}</td>".($showIqama ? "<td class=\"iqama\">{$iqamas['asr']}</td>" : "")."</tr>".
            "<tr><th>Maghrib</th><td class=\"azhan\">{$pt["maghrib"]["azhan"]}</td>".($showIqama ? "<td class=\"iqama\">{$iqamas['maghrib']}</td>" : "")."</tr>".
            "<tr><th>Isha</th><td class=\"azhan\">{$pt["isha"]["azhan"]}</td>".($showIqama ? "<td class=\"iqama\">{$iqamas['isha']}</td>" : "")."</tr>".
            "</table>";
    }
}