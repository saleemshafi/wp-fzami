<?php
/*
Plugin Name: Fzami Prayer Time Plugin
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

add_action("widgets_init",
    function () { register_widget("PrayerTimeWidget"); });
