<?php

add_action("widgets_init", function () { register_widget("JumahWidget"); });


class JumahWidget extends WP_Widget
{
    public function __construct() {
        parent::__construct("jumah_widget", "Jumah Time Widget",
            array("description" => "A widget to display the jumah times and khateeb names"));
    }

    public function form($instance) {
        $default = array('title' => 'Jum\'ah Times');
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

        echo $before_widget;

        $title = apply_filters('widget_title', $title);
        if ($title) {
            echo $before_title . $title . $after_title;
        }

        echo $this->getJumahMarkup();

        echo $after_widget;
    }

    protected function getJumahMarkup() {
        $markup = "";
        $options = get_option('fzami_options');
        $markup .= "<table id=\"jumah-table\" class=\"jumah\">";
        if (isset($options["jumah_first_time"])) {
            $time = fzami_format_time($options['jumah_first_time']);
            $markup .= "<tr><th class=\"time\">$time</th><td class=\"khateeb\">{$options['jumah_first_khateeb']}</td></tr>";
        }
        if (isset($options["jumah_second_time"])) {
            $time = fzami_format_time($options['jumah_second_time']);
            $markup .= "<tr><th class=\"time\">$time</th><td class=\"khateeb\">{$options['jumah_second_khateeb']}</td></tr>";
        }
        if (isset($options["jumah_third_time"])) {
            $time = fzami_format_time($options['jumah_third_time']);
            $markup .= "<tr><th class=\"time\">$time</th><td class=\"khateeb\">{$options['jumah_third_khateeb']}</td></tr>";
        }
        $markup .= "</table>";
        return $markup;
    }
}