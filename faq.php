<?php
/*
* Plugin Name: Faqizer
* Plugin URI: http://wrightfloat.com
* Description: Add Frequently Asked Questions to your website, No more depending on muiltiple short codes to display each faq on your site.   
* Author: Daniel Wright
* Author URI: http://wrightfloat.com
* Version: 0.2
* Text Domain: dlwfq_faqizer
* Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if(! defined( 'DLWFQ_PLUGIN_DIR_PATH' ) ){
    define( 'DLWFQ_PLUGIN_DIR_PATH', plugin_dir_path(__FILE__) );
}

if(! defined( 'DLWFQ_PLUGIN_NAME' ) ){
    //plugins main directory folder name 
    define( 'DLWFQ_PLUGIN_NAME', 'faqizer' );
}

if (! defined( 'DLWFQ_PLUGIN_DIR' ) ) {

    //a constant to be used to get the directory with my plugin name 
    define( 'DLWFQ_PLUGIN_DIR',  WP_PLUGIN_DIR . '/' . DLWFQ_PLUGIN_NAME );
}

if (! defined( 'DLWFQ_FRONTEND_CSS_ASSETS' ) ){

    //a constant to be used for reffering too my frontend folder location for css assets.
    define( 'DLWFQ_FRONTEND_CSS_ASSETS', '/assets/frontend/css/' );
}

if (! defined( 'DLWFQ_FRONTEND_JS_ASSETS' ) ){

    //a constant to be used for reffering too my frontend folder location for js assets.
    define( 'DLWFQ_FRONTEND_JS_ASSETS',  '/assets/frontend/js/' );
}

if (! defined( 'DLWFQ_BACKEND_CSS_ASSETS' ) ){

    //a constant to be used for reffering too my backend folder location for css assets.
    define( 'DLWFQ_BACKEND_CSS_ASSETS',  '/assets/backend/css/' ); 
}

function dlwfq_plugin_activation() {
    // doing a check to make sure the user has the miniumn requirements to use the plugin.
    global $wp_version;
    $php = '5.3';
    $wp  = '4.5'; 
    if ( version_compare( PHP_VERSION, $php, '<' ) ) {
        deactivate_plugins( basename( __FILE__ ) );
        wp_die(
            '<p>' .
            sprintf(
                __( 'This plugin can not be activated because it requires a PHP version greater than %1$s.', 'dlwfq_faqizer' ), $php)
            . '</p> <a href="' . admin_url( 'plugins.php' ) . '">' . __( 'go back', 'dlwfq_faqizer' ) . '</a>'
        );
    }
    if ( version_compare( $wp_version, $wp, '<' ) ) {
        deactivate_plugins( basename( __FILE__ ) );
        wp_die(
            '<p>' .
            sprintf(
                __( 'This plugin can not be activated because it requires a WordPress version greater than %1$s .', 'dlwfq_faqizer' ),
                $php
            )
            . '</p> <a href="' . admin_url( 'plugins.php' ) . '">' . __( 'go back', 'dlwfq_faqizer' ) . '</a>'
        );
    }
    if( get_option('dlwfq-archive-title') === false){
        update_option('dlwfq-archive-title',  __('Frequently Asked Questions' , 'dlwfq_faqizer')); 
    }
    if( get_option('dlwfq-total-posts-on-archive-page') === false){
        update_option('dlwfq-total-posts-on-archive-page', 10); 
    }
    if( get_option('dlwfq-archive-options-slug') === false){
        update_option('dlwfq-archive-options-slug', 'faqs'); 
    }
    if( get_option('dlwfq-plugin-v') === false ){ 
        update_option('dlwfq-plugin-v', '0.2' ); 
    }
    if( get_option('dlwfq-archive-accordion') === false){
        update_option('dlwfq-archive-accordion', 1); 
    }

    set_transient( 'dlwfq_faqizer_activated', 1 );
    set_transient( 'dlwfq_faqizer_activated_reset_permalinks', 1 );
}

register_activation_hook( __FILE__,  'dlwfq_plugin_activation' );

//Actually setup the faq plugin. 
function dlwfq_plugin_setup(){

    // adding translation
    load_plugin_textdomain( 'dlwfq_faqizer', false, __DIR__ ); 

    //getting our required files. 
    require_once( DLWFQ_PLUGIN_DIR_PATH . '/includes/base/dlwfq_core_function.php'  );
    require_once( DLWFQ_PLUGIN_DIR_PATH . '/includes/base/dlwfq_custom_actions.php' ); //contains all of the custom actions an filters that we need access too. 
    require_once( DLWFQ_PLUGIN_DIR_PATH . '/includes/base/class_dlwfq.php' );

    //grabing the custom slug setup from our database 
    new dlwfq_setup( dlwfq_get_the_slug(true) ); 

    function dlwfq_plugin_add_settings_link( $links ) {
        $settings_link = '<a href="admin.php?page=dlwfq-settings">' . __( 'Settings' ) . '</a>';
        array_push( $links, $settings_link );
        return $links;
    }

    // adds the settings link too the plugins installed admin page.
    $plugin = plugin_basename( __FILE__ );
    add_filter( "plugin_action_links_$plugin", 'dlwfq_plugin_add_settings_link' );

    //faqs custom taxonomy called faq topics. 
    function dlwfq_create_topics_tax() {
        register_taxonomy(
            'dlwfq_topics',
            'dlw_wp_faq',
            array(
                'label' => __( 'Faq Topics' ),
                'public' => true,
                'show_ui' => true, 
                'show_in_menu' => true,
                'show_in_nav_menus' => true,
                'show_in_rest' => false, //(boolean) (optional) Whether to include the taxonomy in the REST API. Default: false
                'show_tagcloud' => true, 
                'show_in_quick_edit' => true,
                'show_admin_column' => true,
                'rewrite' => array( 'slug' => 'faq-topics' ),
                'hierarchical' => false,
                'query_var' => 'faq-topics',
                'sort' => true, 
            )
        );
    }
    add_action( 'init', 'dlwfq_create_topics_tax' );
}

add_action('plugins_loaded', 'dlwfq_plugin_setup');
/**
 * removing plugin data from the database on deactivation.
 *
 * @return void
 */ 
function dlwfq_plugin_deactivation() {
    if( get_option('dlwfq-archive-accordion') !== false){
        delete_option('dlwfq-archive-accordion'); 
    }
    if( get_option('dlwfq-archive-title') !== false){
        delete_option('dlwfq-archive-title'); 
    } 
    if( get_option('dlwfq-total-posts-on-archive-page') !== false){
        delete_option('dlwfq-total-posts-on-archive-page'); 
    }
    if( get_option('dlwfq-archive-options-slug') !== false){
        delete_option('dlwfq-archive-options-slug');  
    }
    if( get_option('dlwfq-plugin-v') !== false ){ 
        delete_option('dlwfq-plugin-v'); 
    }

    //flushing the rewrite rules to remove uneeded settings.
    flush_rewrite_rules();
}

register_deactivation_hook(__FILE__, 'dlwfq_plugin_deactivation');