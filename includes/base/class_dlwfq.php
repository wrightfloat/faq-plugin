<?php
/**
 * Faqizer setup class
 *
 * @package Faqizer
 * @since   0.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class dlwfq_setup{

    public $plugin_version = 1.0;
    public $plugin_prefix = 'dlw_frequently_asked_questions'; //the prefix of the plugin
    public $custom_post_type = 'dlw_wp_faq'; //the custom post type 
    public $custom_post_type_slug;
    public $wp_content_dir_name; 
    public $archive_template; 
    public $plugin_template;

    public function __construct( $slug  ) {
        $this->custom_post_type_slug = $slug; //adding our cutom slug or getting the default slug to use. 
        $this->set_up_custom_posttype(); //setting up the custom post type.
        require_once( DLWFQ_PLUGIN_DIR_PATH . 'includes/base/class_dlwfq_clean_our_data.php'); //require only when it's actually needed within the settings class
        require_once( DLWFQ_PLUGIN_DIR_PATH . 'includes/base/dlwfq_settings_pages.php'); //setting up our settings page. 
        
        //May make this into a class in the future if I add in more setting page's. 
        //setting up the custom archive template.
        add_filter( 'archive_template', array($this, 'setup_faq_archive_page' ) );

        //add in a taxonmony template here
        add_filter( 'taxonomy_template', array($this, 'setup_faq_topics_archive_template' ) );
    }

    /**
     * adds our custom post type into our plugin 
     * @return void
     */
    public function set_up_custom_posttype(){      

        require_once( DLWFQ_PLUGIN_DIR_PATH . 'includes/base/class_dlwfq_custom_post_type.php'); //gets our custom post type class. 
        $FaqPosttype = new dlwfq_custom_post_type( $this->custom_post_type_slug, $this->custom_post_type );
        $FaqPosttype->setupLabels('Faqs', 'Faq', __('Enter frequently asked question\'s', 'dlwfq_faqizer') ); //setting up labels for our custom post type. 
    }

    /**
     * Function is reponsible for setting up the custom archive-faqs page for our plugin. 
     *
     * @param [type] $archive_template
     * @return void
     */
    
    public function setup_faq_archive_page($archive_template) {

        if( is_post_type_archive( $this->custom_post_type ) ){
            $file_name = 'archive-faqs.php'; // make sure too let users know that the archive faq page must be defined as this if they add a custom archive page too there theme. 

            $archive_template = dlwfq_get_faq_archive_template($file_name); //From - includes/base/dlwfq_core_function.php

            //run this when the user has a template that was added too it's theme. 
            if($archive_template['template_loaded_via_theme'] === true && $archive_template['template_loaded_via_plugin_true'] === false ){
                $archive_template = $archive_template['file'];
                
                // sets up the required stylesheet and js files.  
                function dlwfq_setup_custom_archive_style_sheet(){ 

                    //registering the custom style sheet too the site. 
                    do_action('dlwfq_register_frontend_style', 
                        array( 
                            'handle' => 'faq-archive-style',
                            'src' => plugins_url( DLWFQ_PLUGIN_NAME . DLWFQ_FRONTEND_CSS_ASSETS .'/faq-archive.css'),
                            'customA' => array(),
                            'version' => 0.1, 
                            'media-type' => 'all',
                        )
                    );

                    //requesting the stylesheet.
                    do_action('dlwfq_enqueue_frontend_style', 'faq-archive-style'); //second arg is the handle
                    
                    // //enqueues the script if the user has the option checked within the settings panel. 
                    if(dlwfq_get_accordian_settings()){

                        //load in the script that we want too use for the site.
                        do_action('dlwfq_register_frontend_script', array( 
                            'handle' => 'faq-archive-script',
                            'src' => plugins_url(DLWFQ_PLUGIN_NAME . DLWFQ_FRONTEND_JS_ASSETS . '/dlwfq-faq-accordian.js'),
                            'customA' => array(),
                            'version' => 0.1, 
                            'footer' => true,
                        ));

                        do_action('dlwfq_enqueue_frontend_script', 'faq-archive-script'); //second arg is the handle
                    }
                }
                add_action('wp_enqueue_scripts', 'dlwfq_setup_custom_archive_style_sheet');
            }

            //run this when the user has a template that was added too it's theme.
            else if($archive_template['template_loaded_via_theme'] === false && $archive_template['template_loaded_via_plugin_true'] === true ){
                $archive_template = $archive_template['file']; 

                // sets up the required stylesheet and js files.  
                function dlwfq_setup_custom_archive_style_sheet(){ 

                    //registering the custom style sheet too the site. 
                    do_action('dlwfq_register_frontend_style', 
                        array( 
                            'handle' => 'faq-archive-style',
                            'src' => plugins_url( DLWFQ_PLUGIN_NAME . DLWFQ_FRONTEND_CSS_ASSETS .'/faq-archive.css'),
                            'customA' => array(),
                            'version' => 0.1, 
                            'media-type' => 'all',
                        )
                    );

                    //requesting the stylesheet.
                    do_action('dlwfq_enqueue_frontend_style', 'faq-archive-style'); //second arg is the handle
                    
                    // enqueues the js script if the user has the option checked within the settings panel. 
                    if(dlwfq_get_accordian_settings()){

                        //load in the script that we want too use for the site.
                        do_action('dlwfq_register_frontend_script', array( 
                            'handle' => 'faq-archive-script',
                            'src' => plugins_url(DLWFQ_PLUGIN_NAME . DLWFQ_FRONTEND_JS_ASSETS . '/dlwfq-faq-accordian.js'),
                            'customA' => array(),
                            'version' => 0.1, 
                            'footer' => true,
                        ));
                        
                        do_action('dlwfq_enqueue_frontend_script', 'faq-archive-script'); //second arg is the handle
                    }
                }

                add_action('wp_enqueue_scripts', 'dlwfq_setup_custom_archive_style_sheet');
            }

            //TODO: in the result that both of our checks fail default to the themes archive file.
            return $archive_template;
        }
        else{
            return; 
        }

    }
    /**
     * Function is reponsible for setting up the custom archive-faqs page for our plugin. 
     *
     * @param [type] $archive_template
     * @return void
     */
    
    public function setup_faq_topics_archive_template($archive_template) {
        
        if( is_post_type_archive( $this->custom_post_type ) ){
            $file_name = 'taxonomy-dlwfq_topics.php';
            $archive_template = dlwfq_get_faq_archive_template($file_name); 

            //run this when the user has a template that was added too it's theme. 
            if($archive_template['template_loaded_via_theme'] === true && $archive_template['template_loaded_via_plugin_true'] === false ){
                $archive_template = $archive_template['file'];
                
                // sets up the required stylesheet and js files.  
                function dlwfq_setup_custom_archive_style_sheet(){ 

                    //registering the custom style sheet too the site. 
                    do_action('dlwfq_register_frontend_style', 
                        array( 
                            'handle' => 'faq-archive-style',
                            'src' => plugins_url( DLWFQ_PLUGIN_NAME . DLWFQ_FRONTEND_CSS_ASSETS .'/faq-archive.css'),
                            'customA' => array(),
                            'version' => 0.1, 
                            'media-type' => 'all',
                        )
                    );

                    //requesting the stylesheet.
                    do_action('dlwfq_enqueue_frontend_style', 'faq-archive-style'); 
                    
                    //enqueues the acordian js script if the user has the option checked within the settings panel. 
                    if(dlwfq_get_accordian_settings()){
                        //load in the script that we want too use for the site.
                        do_action('dlwfq_register_frontend_script', array( 
                            'handle' => 'faq-archive-script',
                            'src' => plugins_url(DLWFQ_PLUGIN_NAME . DLWFQ_FRONTEND_JS_ASSETS . '/dlwfq-faq-accordian.js'),
                            'customA' => array(),
                            'version' => 0.1, 
                            'footer' => true,
                        ));

                        do_action('dlwfq_enqueue_frontend_script', 'faq-archive-script'); 
                    }
                }
                add_action('wp_enqueue_scripts', 'dlwfq_setup_custom_archive_style_sheet');
            }

            //run this when the user has a template that was added too it's theme.
            else if($archive_template['template_loaded_via_theme'] === false && $archive_template['template_loaded_via_plugin_true'] === true ){
                
                $archive_template = $archive_template['file']; 
                // sets up the required stylesheet and js files.  
                function dlwfq_setup_custom_archive_style_sheet(){ 

                    //registering the custom style sheet too the site. 
                    do_action('dlwfq_register_frontend_style', 
                        array( 
                            'handle' => 'faq-archive-style',
                            'src' => plugins_url( DLWFQ_PLUGIN_NAME . DLWFQ_FRONTEND_CSS_ASSETS .'/faq-archive.css'),
                            'customA' => array(),
                            'version' => 0.1, 
                            'media-type' => 'all',
                        )
                    );

                    //requesting the stylesheet.
                    do_action('dlwfq_enqueue_frontend_style', 'faq-archive-style'); //second arg is the handle
                    
                    // enqueues the js script if the user has the option checked within the settings panel. 
                    if(dlwfq_get_accordian_settings()){

                        //load in the script that we want too use for the site.
                        do_action('dlwfq_register_frontend_script', array( 
                            'handle' => 'faq-archive-script',
                            'src' => plugins_url(DLWFQ_PLUGIN_NAME . DLWFQ_FRONTEND_JS_ASSETS . '/dlwfq-faq-accordian.js'),
                            'customA' => array(),
                            'version' => 0.1, 
                            'footer' => true,
                        ));
                        
                        do_action('dlwfq_enqueue_frontend_script', 'faq-archive-script'); //second arg is the handle
                    }
                }

                add_action('wp_enqueue_scripts', 'dlwfq_setup_custom_archive_style_sheet');
            }

            return $archive_template;
        }

        else{
            return; 
        }
    }
}
