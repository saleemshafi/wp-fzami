<?php
class PrayerTimeWidget extends WP_Widget
{
    public function __construct() {
        parent::__construct("prayertime_widget", "Prayer Time Widget",
            array("description" => "A widget to display the prayer times"));
    }

    public function form($instance) {
        $default = ['title' => ''];
        $instance = wp_parse_args( (array) $instance, $default );
        $title = $instance['title'];
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' );?>">Title:</label>
            <input class="widefat"
                   id="<?php echo $this->get_field_id( 'title' );?>"
                   name="<?php echo $this->get_field_name( 'title' );?>"
                   type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
    <?php
    }

    public function update($newInstance, $oldInstance) {
        $values = array();
        $values["title"] = strip_tags($newInstance["title"]);
        return $values;
    }

    public function widget($args, $instance) {
        extract($args);
        extract($instance);

        $title = apply_filters('widget_title', $title);

        echo $before_widget;
        echo $before_title . $title . $after_title;

        $pt = [];
        $pt["fajr"] = [ "azhan" => "6:00", "iqama" => "6:15"];
        $pt["zuhr"] = [ "azhan" => "1:30", "iqama" => "1:45"];
        $pt["asr"] = [ "azhan" => "5:00", "iqama" => "6:00"];
        $pt["maghrib"] = [ "azhan" => "7:35", "iqama" => "7:40"];
        $pt["isha"] = [ "azhan" => "9:00", "iqama" => "9:15"];
        echo $this->getMarkup($pt);

        echo $after_widget;
    }

    protected function getMarkup($pt) {
        return "<table class=\"prayertime\">".
            "<tr><th>Fajr</th><td class=\"azhan\">{$pt['fajr']['azhan']}</td><td class=\"iqama\">{$pt['fajr']['iqama']}</td></tr>".
            "<tr><th>Zuhr</th><td class=\"azhan\">{$pt["zuhr"]["azhan"]}</td><td class=\"iqama\">{$pt["zuhr"]["iqama"]}</td></tr>".
            "<tr><th>Asr</th><td class=\"azhan\">{$pt["asr"]["azhan"]}</td><td class=\"iqama\">{$pt["asr"]["iqama"]}</td></tr>".
            "<tr><th>Maghrib</th><td class=\"azhan\">{$pt["maghrib"]["azhan"]}</td><td class=\"iqama\">{$pt["maghrib"]["iqama"]}</td></tr>".
            "<tr><th>Isha</th><td class=\"azhan\">{$pt["isha"]["azhan"]}</td><td class=\"iqama\">{$pt["isha"]["iqama"]}</td></tr>".
            "</table>";
    }
}