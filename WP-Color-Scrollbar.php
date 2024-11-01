<?php
/*
  Plugin Name: WP Color Scrollbar
  Plugin URI: http://wordpress.org/plugins/wp-color-scrollbar/
  Description: This plugin will enable custom scrollbar in your wordpress site. You can change color & other setting from <a href="options-general.php?page=soocolor-settings">Option Panel</a>
  Author: babyskill
  Author URI: http://taizalo.biz
  Version: 1.0
 */
include('inc/color.class.php');
define('WPCOLOR_PLUGIN_URL', WP_PLUGIN_URL . '/' . plugin_basename(dirname(__FILE__)) . '/');
new wp_color_scrollbar();