<?php
/*
Plugin Name: Faqizer
Plugin URI: http://wrightfloat.com
Description: A plugin to easily add Frequently asked questions to your website. 
Author: Daniel Wright
Version: 1.0
Author URI: http://wrightfloat.com
*/

if( ! defined( 'ABSPATH' ) ) {
    return;
}

//TODO: do a check to see if any of our define statements are alreay defined, and if so get the plugins that are using the statements.
//use this to alert the user what is wrong with the plugin, an why it did not install the plugin properly. 

//our settings page will not work if wordpress version is lower than 4.7; 

//defining our constant's that the plugin will use. 
if (! defined( 'DLWFAQ_PLUGIN_DIR' ) ) {
	define( 'DLWFAQ_PLUGIN_DIR', __DIR__ );
}

//
if (! defined( 'DLWFAQ_PLUGIN_CSS_ASSETS_URL' ) ){
    define( 'DLWFAQ_PLUGIN_CSS_ASSETS_URL', plugins_url() . '/faq/assets/css/' );
}


if(! defined( 'DLWFAQ_PLUGIN_DIR_URL' ) ){
    define( 'DLWFAQ_PLUGIN_DIR_URL', plugins_url() );
}

//sets up the faq plugin. 
function dlwfq_plugin_setup(){
    require_once(DLWFAQ_PLUGIN_DIR . '/includes/base/dlwfq_core_function.php');
    require_once(DLWFAQ_PLUGIN_DIR . '/includes/base/dlwfq_custom_actions.php'); //contains all of the custom actions an filters that we need access too. 
    require_once(DLWFAQ_PLUGIN_DIR . '/includes/base/class_dlwfq.php');
    new dlwfq_setup( dlwfq_get_the_slug(true) );
}
add_action('plugins_loaded', 'dlwfq_plugin_setup');