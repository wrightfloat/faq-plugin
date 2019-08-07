<?php
/*
* Plugin Name: Faqizer
* Plugin URI: http://wrightfloat.com
* Description: A plugin to add Frequently Asked Questions to your website. The Plugin is in the early stages, an will have new features added.  
* Author: Daniel Wright
* Author URI: http://wrightfloat.com
* Version: 1.1
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

    /**
     * setting up some default options for our faqs plugin's setting's page on that will occur only on plugin activation.
     *
     * @return void
     */ 

    //adds the accordian default here.
    if( get_option('dlwfq-archive-accordion') === false){
        add_option('dlwfq-archive-accordion', 0);
    }

    //adds a default faq page title
    if( get_option('dlwfq-archive-title') === false){
        add_option('dlwfq-archive-title',  __('Frequently Asked Questions' , 'dlwfq_faqizer')); 
    }

    //adds number of posts/faqs to display on the faq page 
    if( get_option('dlwfq-total-posts-on-archive-page') === false){
        add_option('dlwfq-total-posts-on-archive-page', 10); 
    }

    //setting the slug to be used for the faq page's
    if( get_option('dlwfq-archive-options-slug') === false){
        add_option('dlwfq-archive-options-slug', 'faqs'); 
    }

    //setting plugin version
    if( get_option('dlwfq-plugin-v') === false ){ 
        add_option('dlwfq-plugin-v', '0.2' ); 
    }

    //registering our post type in the activation hook, so the user has a faq page setup right away. 
    //always make sure this is exaclty the same as what's in the plugin post type class. 
    register_post_type( 'dlw_wp_faq', 
        array(
            'labels' => array('name'=> 'faqs', 'singular_name' => 'faq'), 
            'description' => 'Enter a FAQ',
            'public' => true,
            'exclude_from_search' => false, //Whether to exclude posts with this post type from front end search results.
            'publicly_queryable' => true, //Whether queries can be performed on the front end as part of parse_request.
            'show_ui' => true, //Whether to generate a default UI for managing this post type in the admin
            'show_in_nav_menus' => true, //Whether to generate a default UI for managing this post type in the admin
            'show_in_menu' => true, //Where to show the post type in the admin menu. show_ui must be true.
            'show_in_admin_bar' => true, //Whether to make this post type available in the WordPress admin bar.
            'menu_position' => 102, //The position in the menu order the post type should appear. show_in_menu must be true.
            'menu_icon' => 'dashicons-flag', 
            'hierarchical' => false, //Whether the post type is hierarchical (e.g. page). Allows Parent to be specified. The 'supports' parameter should contain 'page-attributes' to show the parent select box on the editor page.
            'supports' => array('title', 'editor', 'author'),   //title, editor, author, thumbnail, excerpt, trackbacks, custom-fields, comments, revisions, page-attributes, post-formats
            'has_archive' => true, //Enables post type archives. Will use $post_type as archive slug by default.
            'rewrite' => array('slug' => faqs, 'with_front' => false ),// Triggers the handling of rewrites for this post type. To prevent rewrites, set to false.
            'can_export' => true,  //allows users to export a csv file of this post type
            'delete_with_user' => false, //Whether to delete posts of this type when deleting a user. If true, posts of this type belonging to the user will be moved to trash when then user is deleted. If false, posts of this type belonging to the user will not be trashed or deleted. If not set (the default), posts are trashed if post_type_supports('author'). Otherwise posts are not trashed or deleted.
            'show_in_rest' => false, //Whether to expose this post type in the REST API. 
        )
    );

    flush_rewrite_rules();

    // Run this on activation, so that we know we've just activated the plugin.
    set_transient( 'dlwfq_faqizer_activated', 1 );

}
register_activation_hook( __FILE__,  'dlwfq_plugin_activation' );

/**
* will display an activation notice when the plugin is installed.
*/
function dlwfq_plugin_activation_notice() {
    // Check the transient to see if we've just activated the plugin
    if( get_transient( 'dlwfq_faqizer_activated' ) ){
        echo '<div class="notice notice-success">' . __( 'Thanks for installing and activating the Faqizer plugin.', 'dlwfq_faqizer' ) . '</div>';
        // Delete the transient so we don't keep displaying the activation message
        delete_transient( 'dlwfq_faqizer_activated' );
    }
}

add_action( 'admin_notices', 'dlwfq_plugin_activation_notice' );

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