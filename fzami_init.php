<?php
/*
Plugin Name: fzami Prayer Time Plugin
Plugin URI: http://www.fzami.com/wp
Description: A WordPress plugin to help display prayer times on your site
Version: 0.1
Author: Saleem Shafi
Author URI: http://www.fzami.com
License: GPL2

    Copyright 2014 Saleem Shafi

    This program is free software; you can redistribute it and/or
    modify it under the terms of the GNU General Public License,
    version 2, as published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA
    02110-1301  USA
*/

include("prayertime_widget.php");
include("jumah_widget.php");
include("settings.php");

register_activation_hook( __FILE__, 'fzami_setup_options' );
add_action( 'wp_enqueue_scripts', 'fzami_styles' );

function fzami_setup_options() {
    $options = get_option('fzami_options');
    if ($options === FALSE) {
        $options = array(
            'version' => '0.1',
            'calc_method' => '2',
            'latitude' => '30.3561811',
            'longitude' => '-97.74292609999999',
            'timezone' => '-5',
            'asr_format' => '1',
            'time_format' => '2',
        );
        add_option('fzami_options', $options);
    }
}

function fzami_styles() {
    wp_enqueue_style( 'fzami-styles', WP_PLUGIN_URL . "/wp-fzami/css/wp-fzami.css" );
    //wp_enqueue_script( 'script-name', get_template_directory_uri() . '/js/example.js', array(), '1.0.0', true );
}

function fzami_format_time($time) {
    $options = get_option('fzami_options');
    $timeFormat = $options['time_format'];
    if ($timeFormat == '1') {
        list($hours, $minutes) = explode(':', $time);
        return ((($hours+12-1)%12)+1).":".($minutes)." ".($hours >= 12 ? "pm" : "am");
    } else if ($timeFormat == '2') {
        list($hours, $minutes) = explode(':', $time);
        return ((($hours+12-1)%12)+1).":".($minutes);
    } else {
        return $time;
    }
}

