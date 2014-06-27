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
            'show_iqama' => TRUE,
            'show_date' => TRUE,
            'show_hijri_date' => TRUE,
            'time_format' => null,
        );
        $instance = wp_parse_args( (array) $instance, $default );
        extract($instance);
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' );?>">Title:</label>
            <input class="widefat"
                   id="<?php echo $this->get_field_id( 'title' );?>"
                   name="<?php echo $this->get_field_name( 'title' );?>"
                   type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'time_format' );?>">Time Format:</label>
            <input class="widefat"
                   id="<?php echo $this->get_field_id( 'time_format' );?>"
                   name="<?php echo $this->get_field_name( 'time_format' );?>"
                   type="text" value="<?php echo $time_format ? esc_attr($time_format) : ""; ?>"/>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'show_iqama' );?>">Show Iqama Times:</label>
            <input class="widefat"
                   id="<?php echo $this->get_field_id( 'show_iqama' );?>"
                   name="<?php echo $this->get_field_name( 'show_iqama' );?>"
                   type="checkbox" value="true" <?php echo $show_iqama ? 'checked="checked"' : "" ?>"/>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'show_date' );?>">Show Today's Date:</label>
            <input class="widefat"
                   id="<?php echo $this->get_field_id( 'show_date' );?>"
                   name="<?php echo $this->get_field_name( 'show_date' );?>"
                   type="checkbox" value="true" <?php echo $show_date ? 'checked="checked"' : "" ?>"/>
        </p>
        <?php
        if (function_exists('en_hijri_date')) {
        ?>
            <p>
                <label for="<?php echo $this->get_field_id( 'show_hijri_date' );?>">Show Hijri Date:</label>
                <input class="widefat"
                       id="<?php echo $this->get_field_id( 'show_hijri_date' );?>"
                       name="<?php echo $this->get_field_name( 'show_hijri_date' );?>"
                       type="checkbox" value="true" <?php echo $show_hijri_date ? 'checked="checked"' : "" ?>"/>
            </p>
        <?php } else { ?>
            <p>If you would like to be able to display Hijri dates, please install <a href="http://wordpress.org/plugins/hijri-calendar/">the Hijri Calendar plugin</a>.
            </p>
        <?php
        }
        ?>
    <?php
    }

    public function update($newInstance, $oldInstance) {
        $values = array();
        $values["title"] = strip_tags($newInstance["title"]);
        $values["show_iqama"] = $newInstance["show_iqama"] == "true";
        $values["show_date"] = $newInstance["show_date"] == "true";
        $values["show_hijri_date"] = $newInstance["show_hijri_date"] == "true";
        $values["time_format"] = $newInstance["time_format"] != "" ? $newInstance["time_format"] : null;
        return $values;
    }

    public function widget($args, $instance) {
        $time_format = null;
        extract($args);
        extract($instance);

        $title = apply_filters('widget_title', $title);

        echo $before_widget;
        echo $before_title . $title . $after_title;

        if ($show_date) {
            echo '<div class="pt_date">'.date(get_option('date_format'), current_time( 'timestamp' )).'</div>';
        }
        if ($show_hijri_date && function_exists('en_hijri_date')) {
            echo '<div class="pt_hijri_date">';
            echo en_hijri_date();
            echo '</div>';
        }
        $pto = new Fzami_PrayerTimes();

        $now = current_time( 'timestamp' );
        $pt = $pto->getAzanAndIqamaTimes($now, $time_format);
        echo $this->getMarkup($pt['azan'], $show_iqama ? $pt['iqama'] : null);

        echo $after_widget;
    }

    protected function getMarkup($pt, $it) {
        $showIqama = !is_null($it);
        return "<table class=\"prayertime\">".
            "<tr><th></th><th>Azhan</th>".($showIqama ? "<th>Iqama</th>" : "")."</tr>".
            "<tr><th>Fajr</th><td class=\"azhan\">{$pt['fajr']}</td>".($showIqama ? "<td class=\"iqama\">{$it['fajr']}</td>" : "")."</tr>".
            "<tr><th>Zuhr</th><td class=\"azhan\">{$pt["zuhr"]}</td>".($showIqama ? "<td class=\"iqama\">{$it['zuhr']}</td>" : "")."</tr>".
            "<tr><th>Asr</th><td class=\"azhan\">{$pt["asr"]}</td>".($showIqama ? "<td class=\"iqama\">{$it['asr']}</td>" : "")."</tr>".
            "<tr><th>Maghrib</th><td class=\"azhan\">{$pt["maghrib"]}</td>".($showIqama ? "<td class=\"iqama\">{$it['maghrib']}</td>" : "")."</tr>".
            "<tr><th>Isha</th><td class=\"azhan\">{$pt["isha"]}</td>".($showIqama ? "<td class=\"iqama\">{$it['isha']}</td>" : "")."</tr>".
            "</table>";
    }
}