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
include('iqama_times.php');
include('month_table_shortcode.php');

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
            'asr_format' => '1',
        );
        add_option('fzami_options', $options);
    }
    $iqama_times = get_option('fzami_iqama_times');
    if ($iqama_times === FALSE) {
        $iqama_dates = array(
            '2000-01-01' => array(
                'fajr' => '06:00',
                'zuhr' => '14:00',
                'asr' => '18:00',
                'maghrib' => '20:30',
                'isha' => '22:00',
            )
        );
        add_option('fzami_iqama_times', array(
            'maghrib_offset' => 5,
            'dates' => $iqama_dates,
        ));
    }
}

function fzami_styles() {
    wp_enqueue_style( 'fzami-styles', WP_PLUGIN_URL . "/wp-fzami/css/wp-fzami.css" );
    //wp_enqueue_script( 'script-name', get_template_directory_uri() . '/js/example.js', array(), '1.0.0', true );
}

function fzami_get_time_formatter($format = null) {
    if ($format == null) {
        $format = get_option('time_format');
    }
    return function($time) use ($format) {
        return date($format, strtotime($time));
    };
}


class Fzami_PrayerTimes {
    protected $prayTime;
    protected $longitude;
    protected $latitude;
    protected $timeFormat;

    public function __construct() {
        $options = get_option('fzami_options');
        $method = $options['calc_method'];
        $asrMethod = $options['asr_method'];
        $this->prayTime = new PrayTime($method);
        $this->prayTime->setAsrMethod($asrMethod);
        $this->prayTime->setTimeFormat('0');

        $this->latitude = $options['latitude'];
        $this->longitude = $options['longitude'];
    }

    public function getAzanAndIqamaTimes($d, $format = null) {
        $pt = $this->getPrayerTimes($d, $format);
        $it = $this->getIqamaTimes($pt['maghrib'], $d, $format);
        return array("azan" => $pt, "iqama" => $it);
    }

    protected function getTimeZoneOffset($date) {
        $tzs = get_option('timezone_string');
        if (!$tzs || strlen($tzs) < 1) {
            $tzs = "UTC+0";
        }
        $dtz = new DateTimeZone($tzs);
        $tz = $dtz->getOffset(new DateTime(date('Y-m-d', $date)));
        return $tz / (60 * 60);
    }

    protected function getPrayerTimes($d, $format) {
        $times = $this->prayTime->getPrayerTimes($d, $this->latitude, $this->longitude, $this->getTimeZoneOffset($d));
        $formatter = fzami_get_time_formatter($format);

        return array(
            "fajr" => $formatter($times[0]),
            "shuruq" => $formatter($times[1]),
            "zuhr" => $formatter($times[2]),
            "asr" => $formatter($times[3]),
            "maghrib" => $formatter($times[5]),
            "isha" => $formatter($times[6]),
        );
    }

    protected function getIqamaTimes($maghrib_time, $d, $format) {
        $iqama_times = get_option('fzami_iqama_times');
        $date_times = isset($iqama_times['dates']) ? $iqama_times['dates'] : array();
        $today = date('Y-m-d', $d);
        $bestDate = null;
        $dates = array_keys($date_times);
        sort($dates);
        foreach($dates as $date) {
            if ($date < $today) {
                $bestDate = $date;
            }
        }
        $times = $bestDate != null ? $date_times[$bestDate] : null;
        $realTimes = array_map('fzami_any_time', $times);
        if (ctype_digit($iqama_times['maghrib_offset'])) {
            $realTimes['maghrib'] = date('H:i', strtotime($maghrib_time) + ($iqama_times['maghrib_offset'] * 60));
        }

        $formatter = fzami_get_time_formatter($format);
        return array(
            "fajr" => $realTimes ? $formatter($realTimes['fajr']) : null,
            "zuhr" => $realTimes ? $formatter($realTimes['zuhr']) : null,
            "asr"=> $realTimes ? $formatter($realTimes['asr']) : null,
            "maghrib"=> $realTimes ? $formatter($realTimes['maghrib']) : null,
            "isha" => $realTimes ? $formatter($realTimes['isha']) : null,
        );
    }
}