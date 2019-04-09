<?php
/**
 * Faqizer setup class
 *
 * @package Faqizer
 * @since   0.1.0
 */

defined( 'ABSPATH' ) || exit;

class dlwfq_setup{

    public $plugin_version = 0.1;
    public $plugin_prefix = 'dlw_frequently_asked_questions'; //the prefix of the plugin
    public $custom_post_type = 'dlw_wp_faq'; //the custom post type 
    public $custom_post_type_slug;
    

    public function __construct($slug) {
        $this->custom_post_type_slug = $slug; //adding our cutom slug or getting the default slug to use. 
        $this->set_up_custom_posttype(); //setting up the custom post type. 

        require_once 'class_dlwfq_validate_input.php'; //sanitization 
        
        require_once 'dlwfq_settings_pages.php'; //setting up our settings page. May make this into a class in the future if I add in more setting page's. 
        add_filter( 'archive_template', array($this, 'dlwfq_faq_archive_template' ) );
    }


    /**
     * adds our custom post type into our plugin 
     * @return void
     */
    public function set_up_custom_posttype(){      
        require_once 'class_dlwfq_custom_post_type.php'; //gets our custom post type class. 
        $FaqPosttype = new dlwfq_custom_post_type($this->plugin_prefix, $this->custom_post_type_slug, $this->custom_post_type);
        $FaqPosttype->setupLabels('Faqs', 'Faq', 'Enter frequently asked question\'s'); //setting up labels for our custom post type. 
    }


    /**
     * setting up templates pages for our custom post type too use. 
     *
     * @return void
     */
    public function dlwfq_faq_archive_template( $archive_template ) {

        //TODO: add in a way users can adjust the styling of the faqs.
        //Be able to add a container max-width property.
        //add the ability to select supported wordpress themes. This will add in-inline css for those spefic themes. 

        global $post;
        if ( is_post_type_archive ( 'dlw_wp_faq' ) ) { 

            $archive_template_name = 'archive-faqs.php';
            //loads the template from the current active theme if there is a template to use for the custom post type. 
            if( dlwfq_does_theme_have_a_template('has-template') ){
                $archive_template = dlwfq_does_theme_have_a_template('template-path');
            }

            //making sure that the theme did not have a template for the FAQ archive page. When one is not found the plugin uses the one it provides. 
            if( dlwfq_does_theme_have_a_template('has-template') === false && file_exists( dlwfq_get_file_paths(true) . '/templates/'. $archive_template_name) ){
                $archive_template = dlwfq_get_file_paths(true) . '/templates/'. $archive_template_name;

                function dlwfq_setup_custom_archive_style_sheet(){ 
                    //registering the custom style sheet too the site. 
                    do_action('dlwfq_register_frontend_style', 
                        array( 
                            'handle' => 'faq-archive-style',
                            'src' => plugins_url() . '/' . DLWFAQ_PLUGIN_NAME . '/assets/frontend/css/faq-archive.css',
                            'customA' => array(),
                            'version' => 0.1, 
                            'media-type' => 'all',
                        )
                    );
                    //requesting the stylesheet.
                    do_action('dlwfq_enqueue_frontend_style', 'faq-archive-style'); //second arg is the handle
                    
                    //enqueues the script if the user has the option checked within the settings panel. 
                    if(dlwfq_get_accordian_settings()){

                        //load in the script that we want too use for the site.
                        do_action('dlwfq_register_frontend_script', array( 
                            'handle' => 'faq-archive-script',
                            'src' => plugins_url() . '/' . DLWFAQ_PLUGIN_NAME . '/assets/frontend/js/dlwfq-faq-accordian.js',
                            'customA' => array(),
                            'version' => 0.1, 
                            'footer' => true,
                        ));
                        do_action('dlwfq_enqueue_frontend_script', 'faq-archive-script'); //second arg is the handle
                    }
                }
                add_action('wp_enqueue_scripts', 'dlwfq_setup_custom_archive_style_sheet');
            }
        }   
        return $archive_template;
    }
}
