<?php
/*
Plugin Name: BlockBuddy
Plugin URI: https://wordpress.org/plugins/blockbuddy/
Description: Gutenberg Block to easily query and display content from any post type!
Version: 0.1
Author: Mateusz Michalik & Aaron Rutley
Author URI: https://wordpress.org/plugins/blockbuddy/
License: GPL2
*/

defined('ABSPATH') or die('No script kiddies please!');

//define block buddy globals
define('BLOCK_BUDDY_VERSION', '0.1');
define('BLOCK_BUDDY_PLUGIN_DIR', plugin_dir_path(__FILE__));

//define custom block globals
define('CUSTOM_QUERY_BLOCK_NAMESPACE', 'custom-query-block/v1');

if (!function_exists('add_action')) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

//load in our block libraries (only custom-query-block for now)
require_once(plugin_dir_path(__FILE__) . 'class.custom-query-block.php');
add_action('init', array('Custom_Query_Block', 'cqb_init'));

//load in our admin related functions and asssets
if (is_admin()) {
    require_once(plugin_dir_path(__FILE__) . 'class.block-buddy-admin.php');
    add_action('init', array('Block_Buddy_Admin', 'block_buddy_admin_init'));
    register_activation_hook(__FILE__, array('Block_Buddy_Admin', 'block_buddy_admin_plugin_activation'));
    register_deactivation_hook(__FILE__, array('Block_Buddy_Admin', 'block_buddy_admin_plugin_deactivation'));
}
