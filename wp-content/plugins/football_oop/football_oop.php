<?php
/*
Plugin Name: Football OOP
Plugin URI:  http://URI_Of_Page_Describing_Plugin_and_Updates
Description: This describes my plugin in a short sentence
Version:     1
Author:
Author URI:  http://URI_Of_The_Plugin_Author
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain:
*/


// Exit if accessed directly
if (!defined('ABSPATH')){
    exit;
}

    require_once(plugin_dir_path(__FILE__).'Classes/PHPExcel.php');
    require_once(plugin_dir_path(__FILE__).'Classes/PHPExcel/IOFactory.php');
    require_once(plugin_dir_path(__FILE__).'admin-notice-helper/admin-notice-helper.php');
    require_once(plugin_dir_path(__FILE__).'class.football.php');
    require_once(plugin_dir_path(__FILE__).'class.player.php');

    add_action('init',array('Football','init'));
    add_action('init',array('Player','init'));

?>