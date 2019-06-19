<?php

/**
 * this sets up our settings page.
 *
 * @package Faqizer
 * @since   0.1.1
 * Text Domain: dlwfq_faqizer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


// create custom plugin settings menu
add_action('admin_menu', 'dlwfq_create_menu');

function dlwfq_create_menu() {

    add_menu_page('Faqizer Setting\'s Page', 'Faqizer', 'manage_options', 'dlwfq-settings', 'dlwfq_plugin_settings_page', 'dashicons-admin-settings', 101);
    add_action( 'admin_init', 'dlwfq_plugin_settings' ); 

}


function dlwfq_plugin_settings() {

    
    //used to create a check box field and have access too the checked function. 
    add_settings_field("dlwfq-archive-accordion", "Enable accordion on Faq page", "dlwfq_plugin_settings_page", "dlwfq-archive-options-group", "default"); 


    //A option to change the 'faqs' slug 
    register_setting( 'dlwfq-archive-options-group', 'dlwfq-archive-options-slug', 
        array( 
            'type' => 'string', 
            'description' => __('Enter Custom Slug', 'dlwfq_faqizer'),
            'sanitize_callback' => 'dlwfq_sanitize_custom_post_slug',
            'show_in_rest' =>  false,
            'default' => false,
        )
    );

    //Add a option to show a differnet amount of faqs then what is shown on the blog, make sure to note that this will only be the same if the theme uses the get_option('posts_per_page');
    //Make this defualt to what the blog pages are supposed to show at most. The default option will be grabed from get_option('posts_per_page');
    register_setting( 'dlwfq-archive-options-group', 'dlwfq-total-posts-on-archive-page', 
        array( 
            'type' => 'integer', 
            'description' => __('total posts to display on our custom faq page', 'dlwfq_faqizer'),
            'sanitize_callback' => 'dlwfq_sanitize_total_posts_to_show',
            'show_in_rest' =>  false,
            'default' => false,
        ) 
    );



    //A option to setup the title on the archive faq page. 
    register_setting( 'dlwfq-archive-options-group', 'dlwfq-archive-title' ,
        array(
            'type' => 'string', 
            'description' => __('the title for the archive page', 'dlwfq_faqizer'),
            'sanitize_callback' => 'dlwfq_sanitize_archive_page_title',
            'show_in_rest' =>  false,
            'default' => false,
        )
    );

    //A option to add an accordian to the faq's on the archive page.
    register_setting( 'dlwfq-archive-options-group', 'dlwfq-archive-accordion', 
        array( 
            'type' => 'integer', 
            'description' => __('total posts to display on our custom faq page', 'dlwfq_faqizer'),
            'sanitize_callback' => 'dlwfq_sanitize_archive_accordion',
            'show_in_rest' =>  false,
            'default' => false,
        ) 
    );

    add_action( 'current_screen', 'dlwfq_check_settings_page' );

    
    /**
     * Making sure that the following code is only exicuted on the faqizer plugin settings page. 
     *
     * @return void
     */
    function dlwfq_check_settings_page() {

        $current_screen = get_current_screen();
        // only setting things up when they are actually needed.  
        if($current_screen->id === 'toplevel_page_dlwfq-settings'){

            //Adds in our admin stylesheet.
            function dlwfq_admin_style(){
                wp_register_style( 'dlwfq_admin_css', plugins_url( DLWFQ_PLUGIN_NAME . '/' . DLWFQ_BACKEND_CSS_ASSETS . 'admin-setting.css') );
                wp_enqueue_style( 'dlwfq_admin_css' );
            }
            add_action( 'admin_enqueue_scripts', 'dlwfq_admin_style' );

            // Do a check to see if we need to flush permalinks. 
            // deletes our transient after reseting the permalinks so they are not reset over and over when the user navigates too this admin page. 
            if( get_transient('dlwfq-page-slug-updated') == 1){
                flush_rewrite_rules();  //flushes the rewrite rules - https://developer.wordpress.org/reference/functions/flush_rewrite_rules/
                delete_transient( 'dlwfq-page-slug-updated' ); // deletes the transient. 
            }

        }

        else{
            return; 
        }
        
    }

}

/**
 * Responsible for sanitizing the archive page title for faq pages. 
 *
 * @return void
 */
function dlwfq_sanitize_archive_page_title(){

    //validating that we have data, and sanitizing the data.
    $archive_title = new dlwfq_clean_our_data( $_POST['dlwfq-archive-title'] , 'strings_only' );
    // display an error if the input that they entered was empty, an let the user know that a default value will be used. 
    if( $archive_title->return_our_data['has_an_error'] ){ 
        add_settings_error('dlwfq-archive-title-empty-value', 'dlwfq-archive-title-empty-value', 'The Archive Page Title input field is not allowed to be empty. The Default "'.  $archive_title->return_our_data['value'] . '" will be used for now.', 'update-nag');
        return $archive_title->return_our_data['value']; // adds default data.
    }
    else{
        return $archive_title->return_our_data['value']; // adds the data
    }

}

/**
 * Responsible for sanitizing the amount of posts too show on the archive faq page, on each page before pagination occurs. 
 *
 * @return void
 */
function dlwfq_sanitize_total_posts_to_show(){
    
    //validating that we have data, and sanitizing the data.
    $faq_count_input = new dlwfq_clean_our_data( $_POST['dlwfq-total-posts-on-archive-page'] , 'post_count' );

    // display an error if the input that they entered was empty, an let the user know that a default value will be used. 
    if( $faq_count_input->return_our_data['has_an_error']){
        add_settings_error('dlwfq-total-posts-on-archive-page-empty-value', 'dlwfq-total-posts-on-archive-page-empty-value', 'The Faqs to Display Per Page input field is not allowed to be empty. The Faqs page will display only '.  $faq_count_input->return_our_data['value'] . ' faqs until a new value is added here.', 'update-nag'); 
        return $faq_count_input->return_our_data['value']; // adds default data.
    }
    else{
        return $faq_count_input->return_our_data['value']; // adds the data
    }

}

function dlwfq_sanitize_custom_post_slug(){

    //validating that we have data, and sanitizing the data.
    $slug_input = new dlwfq_clean_our_data( $_POST['dlwfq-archive-options-slug'] , 'slug' ); 

    // display an error if the input that they entered was empty, an let the user know that a default value will be used. 
    if($slug_input->return_our_data['has_an_error']){
        //create a js script that makes the invalid fields red. 
        add_settings_error('dlwfq-archive-options-slug-empty-value', 'dlwfq-archive-options-slug-empty-value', 'The Custom page slug input field is not allowed to be empty. The Default '. $slug_input->return_our_data['value'] . ' will be used for now.', 'update-nag');
        return $slug_input->return_our_data['value'];
    }

    else{

        // adding a transient when the slug is changed by the user, so we can flush permalinks when the transient exists.  
        if( get_option('dlwfq-archive-options-slug') !== $slug_input->return_our_data['value'] ){
            // deletes an exsisting transient. 
            if( get_transient( 'dlwfq-page-slug-updated' ) !== false){
                delete_transient( 'dlwfq-page-slug-updated' );
            }
            // creating the transient. 
            set_transient( 'dlwfq-page-slug-updated', 1, 60 );
        } 

        //returning the users cleaned input.
        return $slug_input->return_our_data['value'];
    }

}

function dlwfq_sanitize_archive_accordion(){
    //validating that we have data, and sanitizing the data.
    $display_accordian = new dlwfq_clean_our_data( $_POST['dlwfq-archive-accordion'] , 'checkbox' );
    return $display_accordian->return_our_data['value']; // adds the data
}

function dlwfq_plugin_settings_page() { ?>

<div id="dlwfq-wrap" class="dlwfq-wrap wrap">
<h1><?php _e('Main Faq Page Settings', 'dlwfq_faqizer'); ?></h1>
<?php 
    //displaying settings errors 
    settings_errors(); 
?>
<form method="post" action="options.php">
    <?php settings_fields( 'dlwfq-archive-options-group' );?>
    <?php do_settings_sections( 'dlwfq-archive-options-group' ); ?>
    <?php do_action('dlwfq-validate-plugin-option-entries'); ?>

    <div class="input-wrap">

        <div class="input-group">
            <label for="dlwfq-archive-options-slug" ><?php _e('The Faq Page Slug', 'dlwfq_faqizer'); ?></label>
            <input type="text" id="dlwfq-archive-options-slug" name="dlwfq-archive-options-slug" value="<?php echo esc_attr( get_option('dlwfq-archive-options-slug') ); ?>" placeholder="<?php _e('Enter Faq Page slug', 'dlwfq_faqizer'); ?>"/>
            <!-- adds faq link to the admin area. -->
            <a class="button preview-button" style="max-width: 130px" href="<?php echo esc_url( get_post_type_archive_link('dlw_wp_faq') ); ?>"><?php _e('View Faq Page', 'dlwfq_faqizer'); ?></a>
        </div>
        
        <div class="input-group">
            <label for="dlwfq-archive-accordion" ><?php _e('Enable accordion on Faq Page', 'dlwfq_faqizer'); ?></label>
            <input type="checkbox" id="dlwfq-archive-accordion" name="dlwfq-archive-accordion" value="1" <?php checked(1 , esc_attr( get_option('dlwfq-archive-accordion') ) , true); ?> />
        </div>
        
        <div class="input-group">
            <label for="dlwfq-archive-title"><?php _e('Archive Page Title', 'dlwfq_faqizer'); ?><span class="dlwfq-help-tip"></span></label> 
            <input type="text" id="dlwfq-archive-title" name="dlwfq-archive-title" value="<?php echo esc_attr( get_option('dlwfq-archive-title') ); ?>" placeholder="<?php _e('Enter Faq Page Title', 'dlwfq_faqizer'); ?>"/>
        </div>

        <div class="input-group">
            <label for="dlwfq-total-posts-on-archive-page" ><?php _e('Faqs To Display Per Page', 'dlwfq_faqizer'); ?><span class="dlwfq-help-tip"></span></label> 
            <input type="number" id="dlwfq-total-posts-on-archive-page" name="dlwfq-total-posts-on-archive-page" value="<?php echo esc_attr( get_option('dlwfq-total-posts-on-archive-page') ); ?>" max="999"/></td>
        </div>

    </div>
    
    <?php submit_button( __('Save Faq Settings', 'dlwfq_faqizer') ); ?>

</form>
</div>
<?php } ?>