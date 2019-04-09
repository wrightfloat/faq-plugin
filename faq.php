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

if( !function_exists('dlwfq_plugin_activation') ){
    register_activation_hook( __FILE__, 'dlwfq_plugin_activation' );
    function dlwfq_plugin_activation() {
        global $wp_version;

        $php = '5.3';
        $wp  = '4.5';

        if ( version_compare( PHP_VERSION, $php, '<' ) ) {
            deactivate_plugins( basename( __FILE__ ) );
            //TODO: display an error message with wordpress's alert system.
            wp_die(
                '<p>' .
                sprintf(
                    __( 'This plugin can not be activated because it requires a PHP version greater than %1$s. Your PHP version can be updated by your hosting company.', 'my_plugin' ),
                    $php
                )
                . '</p> <a href="' . admin_url( 'plugins.php' ) . '">' . __( 'go back', 'my_plugin' ) . '</a>'
            );
        }

        if ( version_compare( $wp_version, $wp, '<' ) ) {
            deactivate_plugins( basename( __FILE__ ) );
            
            //TODO: display an error message with wordpress's alert system.
            wp_die(
                '<p>' .
                sprintf(
                    __( 'This plugin can not be activated because it requires a WordPress version greater than %1$s. Please go to Dashboard &#9656; Updates to gran the latest version of WordPress .', 'my_plugin' ),
                    $php
                )
                . '</p> <a href="' . admin_url( 'plugins.php' ) . '">' . __( 'go back', 'my_plugin' ) . '</a>'
            );
        }
    }
}

if( !function_exists('dlwfq_plugin_defaults') ){
    //setting up some default options for our faqs plugin's setting's page.
    //TODO: add in an deactivate hook an revert the rewrite rules back to what they where before the plugin was installed instead flushing rewrite rules. 
    //TODO: add some error handling here an put in a option that enables adding the erros to the log file if it's set. 

    register_activation_hook( __FILE__, 'dlwfq_plugin_defaults' );
    function dlwfq_plugin_defaults() {

        $dlwfq_plugin_version = 1.0; 

        //adds the accordian default here.
        if( get_option('dlwfq-archive-accordion') === false){
            add_option('dlwfq-archive-accordion', 0); //by default not displaying the accordian on the faq page.
        }

        //adds a default faq page title
        if( get_option('dlwfq-archive-title') === false){
            add_option('dlwfq-archive-title', 'Frequently Asked Questions'); //setting the default archive page title to: Frequently Asked Questions
        }

        //adds number of posts/faqs to display on the faq page 
        if( get_option('dlwfq-total-posts-on-archive-page') === false){
            add_option('dlwfq-total-posts-on-archive-page', 10); //setting the default post too display on the faq page to: 10 
        }

        //setting the slug to be used for the faq page's
        if( get_option('dlwfq-archive-options-slug') === false){
            add_option('dlwfq-archive-options-slug', 'faqs'); //setting the default to: faqs 
        }

        //adding a option in that will rewrite the slug only when it's differnet from the default one
        if( get_option('dlwfq-installation-options') === false){
            add_option('dlwfq-installation-options', array( 
                'dlwfq-plugin-version' => $dlwfq_plugin_version, 
                'default-slug' => 'faqs',
                'has-rewrite-rules-been-updated' => false, 
                'default-rewrite-rules' => get_option('rewrite_rules')  //stores the rewrite rules that site has before we change them. - will help if the clients site goes to a 404 page after installing the plugin. 
                ) 
            ); 
        }

    }
}

//defining our constant's that the plugin will use. 
if (! defined( 'DLWFAQ_PLUGIN_DIR' ) ) {
    define( 'DLWFAQ_PLUGIN_DIR', __DIR__ );
}

if(! defined( 'DLWFAQ_PLUGIN_DIR_URL' ) ){
    define( 'DLWFAQ_PLUGIN_DIR_URL', plugins_url() );
}

if(! defined( 'DLWFAQ_PLUGIN_NAME' ) ){
    define( 'DLWFAQ_PLUGIN_NAME', 'faqizer' );
}

if (! defined( 'DLWFAQ_FRONTEND_CSS_ASSETS' ) ){
    define( 'DLWFAQ_FRONTEND_CSS_ASSETS', plugins_url() . '/' . DLWFAQ_PLUGIN_NAME . '/assets/frontend/css/' );
}

if (! defined( 'DLWFAQ_BACKEND_CSS_ASSETS' ) ){
    define( 'DLWFAQ_BACKEND_CSS_ASSETS', plugins_url() . '/' . DLWFAQ_PLUGIN_NAME . '/assets/backend/css/' );
}

if( !function_exists('dlwfq_plugin_setup') ){
    //sets up the faq plugin. 
    function dlwfq_plugin_setup(){
        require_once(DLWFAQ_PLUGIN_DIR . '/includes/base/dlwfq_core_function.php');
        require_once(DLWFAQ_PLUGIN_DIR . '/includes/base/dlwfq_custom_actions.php'); //contains all of the custom actions an filters that we need access too. 
        require_once(DLWFAQ_PLUGIN_DIR . '/includes/base/class_dlwfq.php');
        new dlwfq_setup( dlwfq_get_the_slug(true) );
    }
    add_action('plugins_loaded', 'dlwfq_plugin_setup');
}

if( !function_exists('dlwfq_admin_style') ){
    //adds in admin style sheet. 
    function dlwfq_admin_style(){
        wp_register_style( 'dlwfq_admin_css', DLWFAQ_BACKEND_CSS_ASSETS . 'admin-setting.css', false);
        wp_enqueue_style( 'dlwfq_admin_css' );
    }
    add_action( 'admin_enqueue_scripts', 'dlwfq_admin_style' );
}