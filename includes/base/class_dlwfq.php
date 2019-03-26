<?php
/**
 * Simplefaqs setup class
 *
 * @package Simplefaqs
 * @since   0.1.0
 */

defined( 'ABSPATH' ) || exit;

class dlwfq_setup{

    public $plugin_version = 0.1;
    public $plugin_prefix = 'dlw_frequently_asked_questions'; //the prefix of the plugin
    public $custom_post_type = 'dlw_wp_faq'; //the custom post type 
    public $custom_post_type_slug;
    

    public function __construct($slug) {
        
        $this->custom_post_type_slug = $slug; 

        $this->set_up_custom_posttype();
        require_once 'dlwfq_settings_pages.php';  
        
        add_filter( 'archive_template', array($this, 'dlwfq_faq_archive_template' ) );
        
    }


    //adds our custom post type into our plugin 
    public function set_up_custom_posttype(){      
        require_once 'class_dlwfq_custom_post_type.php';
        // adding a custom posttype to this plugin
        $FaqPosttype = new dlwfq_custom_post_type($this->plugin_prefix, $this->custom_post_type_slug, $this->custom_post_type);
        $FaqPosttype->setupLabels('Faqs', 'Faq', 'Enter frequently asked question\'s');
    }


    /**
     * setting up templates pages for our custom post type too use. 
     *
     * @return void
     */

    //setting up the custom templates
    public function dlwfq_faq_archive_template( $archive_template ) {
        global $post;

        // TODO: get the template name via the settings menu. 
        $archive_template_name = 'archive-faqs.php';

        if ( is_post_type_archive ( 'dlw_wp_faq' ) ) {

            //TODO: add in a way users can adjust the styling of the faqs. 

            //loads the template from the current active theme if there is a template to use for the custom post type. 
            if( dlwfq_does_theme_have_a_template('has-template') ){
                $archive_template = dlwfq_does_theme_have_a_template('template-path');
            }

            //making sure that the theme did not have a template an using the one provided by the plugin. 
            if( dlwfq_does_theme_have_a_template('has-template') === false && file_exists( dlwfq_get_file_paths(true) . '/templates/'. $archive_template_name) ){
                $archive_template = dlwfq_get_file_paths(true) . '/templates/'. $archive_template_name;

                function dlwfq_setup_custom_archive_style_sheet(){
                    
                    //TODO: add in a option to select the actual theme the user wants to use for the faqs. 

                    //registering the custom style sheet too the site. 
                    do_action('dlwfq_register_frontend_style', 
                        array( 
                            'handle' => 'faq-archive-style',
                            'src' => plugins_url() . '/' . DLWFAQ_PLUGIN_NAME . '/assets/css/faq-archive.css',
                            'customA' => array(),
                            'version' => 0.1, 
                            'media-type' => 'all',
                        )
                    );
                    //requesting the stylesheet.
                    do_action('dlwfq_enqueue_frontend_style', 'faq-archive-style'); //second arg is the handle
                    
                    //load in the script that we want too use for the site.

                    do_action('dlwfq_register_frontend_script', array( 
                        'handle' => 'faq-archive-script',
                        'src' => plugins_url() . '/' . DLWFAQ_PLUGIN_NAME . '/assets/js/dlwfq-faq-accordian.js',
                        'customA' => array(),
                        'version' => 0.1, 
                        'footer' => true,
                    ));

                    do_action('dlwfq_enqueue_frontend_script', 'faq-archive-script'); //second arg is the handle
                }
                add_action('wp_enqueue_scripts', 'dlwfq_setup_custom_archive_style_sheet');
            }

        }
        
        return $archive_template;

    }

}
