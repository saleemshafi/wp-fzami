<?php

add_action("widgets_init", function () { register_widget("JumahWidget"); });


class JumahWidget extends WP_Widget
{
    public function __construct() {
        parent::__construct("jumah_widget", "Jumah Time Widget",
            array("description" => "A widget to display the jumah times and khateeb names"));
    }

    public function form($instance) {
        $default = array('title' => 'Jum\'ah Times',
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
    <?php
    }

    public function update($newInstance, $oldInstance) {
        $values = array();
        $values["title"] = strip_tags($newInstance["title"]);
        $values["time_format"] = $newInstance["time_format"] != "" ? $newInstance["time_format"] : null;
        return $values;
    }

    public function widget($args, $instance) {
        $time_format = null;
        extract($args);
        extract($instance);

        echo $before_widget;

        $title = apply_filters('widget_title', $title);
        if ($title) {
            echo $before_title . $title . $after_title;
        }

        echo $this->getJumahMarkup($time_format);

        echo $after_widget;
    }

    protected function getJumahMarkup($format) {
        $markup = "";
        $options = get_option('fzami_options');
        $dateStr = $options['jumah_date'];
        $hasDate = $dateStr && trim($dateStr) != '';
        $markup .= "<table id=\"jumah-table\" class=\"jumah\">";
        if ($hasDate) {
            $markup .= "<tr class=\"jumah_date\"><td></td><td>".date(get_option('date_format'), strtotime($dateStr))."</td></tr>";
        }
        $formatter = fzami_get_time_formatter($format);
        if (isset($options["jumah_first_time"])) {
            $time = $formatter($options['jumah_first_time']);
            $markup .= "<tr><th class=\"time\">$time</th><td class=\"khateeb\">{$options['jumah_first_khateeb']}</td></tr>";
        }
        if (isset($options["jumah_second_time"])) {
            $time = $formatter($options['jumah_second_time']);
            $markup .= "<tr><th class=\"time\">$time</th><td class=\"khateeb\">{$options['jumah_second_khateeb']}</td></tr>";
        }
        if (isset($options["jumah_third_time"])) {
            $time = $formatter($options['jumah_third_time']);
            $markup .= "<tr><th class=\"time\">$time</th><td class=\"khateeb\">{$options['jumah_third_khateeb']}</td></tr>";
        }
        $markup .= "</table>";
        return $markup;
    }
}