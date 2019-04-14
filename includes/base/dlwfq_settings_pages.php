<?php

/**
 * this sets up our settings pages 
 *
 * @package Faqizer
 * @since   0.1.0
 * Text Domain: dlwfq_faqizer
 */

defined( 'ABSPATH' ) || exit;

    // create custom plugin settings menu
    add_action('admin_menu', 'my_cool_plugin_create_menu');

    function my_cool_plugin_create_menu() {
        //create new top-level menu
        add_menu_page('Faqizer Setting\'s Page', 'Faqizer', 'manage_options', 'dlwfq-settings', 'my_cool_plugin_settings_page', 'dashicons-admin-settings', 101);
        //call register settings function
        add_action( 'admin_init', 'register_my_cool_plugin_settings' );
    }

    function register_my_cool_plugin_settings() {

        //used to create a check box field and have access too the checked function. 
        add_settings_field("dlwfq-archive-accordion", "Enable accordion on Faq page", "my_cool_plugin_settings_page", "dlwfq-archive-options-group", "default"); 
        
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
        register_setting( 'dlwfq-archive-options-group', 'dlwfq-archive-accordion');

    }

    /**
     * a sanitization function that will display errors if the admin is not entering in the correct info for the page title
     * at the moment it just makes sure that it's not empty.
     *
     * @return void
     */
    function dlwfq_sanitize_archive_page_title(){
        $archive_title = $_POST['dlwfq-archive-title'];
        $archive_data = new validate_our_data($archive_title);
        $archive_title_errors = array(
            'empty_value_error' => 
                array(
                    'id' => 'archive-title-empty-error',
                    'class' => 'archive-title-empty-error', 
                    'message' => __('Archive page title field was empty: default will be used until it\'s set.', 'dlwfq_faqizer'),
                    'type' => 'error'
                ),
            'invalid_input_type_error' => 
                array(
                    'id' => 'archive-title-invalid-input',
                    'class' => 'archive-title-invalid-input', 
                    'message' => __('Archive title field was not set correctly' , 'dlwfq_faqizer'),
                    'type' => 'error'            
                ),
        );

        $archive_data = $archive_data->set_input_errors($archive_title_errors)->set_input_type('none')->return_errors(true);
        
        //will update the data if there are no errors too display.
        if($archive_data['has_errors'] === false){
            return $archive_title;
        }

        //displays the error. 
        else{
            //create a js script that makes the invalid fields red. 
            add_settings_error($archive_data['the_error']['id'], $archive_data['the_error']['class'], $archive_data['the_error']['message'], $archive_data['the_error']['type']);
        }
    }
    
    /**
     * a sanitization function that will display errors if the admin is not entering in the correct info for the faq page slug
     * at the moment it just makes sure that it's not empty, and that it's a string.
     *
     * @return void
     */
    function dlwfq_sanitize_custom_post_slug(){
        $slug_input = $_POST['dlwfq-archive-options-slug'];
        $slug_data = new validate_our_data($slug_input);
        $slug_errors = array(
            'empty_value_error' => 
                array(
                    'id' => 'custom-slug-empty-error',
                    'class' => 'custom-slug-empty-error', 
                    'message' => __('Please provide a slug to be used for the faq Page.', 'dlwfq_faqizer'),
                    'type' => 'error'
                ),
            'invalid_input_type_error' => 
                array(
                    'id' => 'custom-slug-invalid-input',
                    'class' => 'custom-slug-invalid-input', 
                    'message' => __('Incorrect format for the custom slug on the faq pages.', 'dlwfq_faqizer'),
                    'type' => 'error'            
                ),
        );

        $slug_data = $slug_data->set_input_errors($slug_errors)->set_input_type('string')->return_errors(true);
        
        //will update the data if there are no errors too display.
        if($slug_data['has_errors'] === false){

            $current_slug = get_option('dlwfq-archive-options-slug');

            //make sure that the inputted slug does not match the one already within the database, and making sure the option is setup 
            if($current_slug !== false && $current_slug === $slug_input){

                $installation_settings = get_option('dlwfq-installation-options');
                
                if($installation_settings !== false){
                    //making sure to only flush rewrite rules on a new slug being added too the settings field. 
                    $update_current_installation_options = array('has-rewrite-rules-been-updated' => true); 
                    $installation_settings = array_replace( $installation_settings, $update_current_installation_options);
                    
                    //updates the has rewrite rules been updated array, and then flushes the cash. 
                    update_option('dlwfq-installation-options', $installation_settings);
                    flush_rewrite_rules();
                }

            } 

            //returns our post input. 
            return strtolower($slug_input);
        }

        //displays the error. 
        else{
            add_settings_error($slug_data['the_error']['id'], $slug_data['the_error']['class'], $slug_data['the_error']['message'], $slug_data['the_error']['type']);
        }
    }

    /**
     * a sanitization function that will display errors if the admin is not entering in the correct info within the number of post to display field.
     * at the moment it just makes sure that it's not empty, and that we have a number.
     *
     * @return void
     */ 
    function dlwfq_sanitize_total_posts_to_show(){
        $total_faqs = $_POST['dlwfq-total-posts-on-archive-page'];
        $total_faqs_data = new validate_our_data($total_faqs);

        //sets up the errors that we want to display 
        $total_post_errors = array(
            'empty_value_error' => 
                array(
                    'id' => 'total-posts-empty-error',
                    'class' => 'empty-value-error', 
                    'message' => __('Number of post to display on Archive page was empty: default will be used until it\'s set.', 'dlwfq_faqizer'),
                    'type' => 'error'
                ),
            'invalid_input_type_error' => 
                array(
                    'id' => 'total-posts-invalid-input',
                    'class' => 'total-posts-invalid-input', 
                    'message' => __('Only Numbers are allowed', 'dlwfq_faqizer'),
                    'type' => 'error'            
                ),
        );

        $total_faqs_data = $total_faqs_data->set_input_errors($total_post_errors)->set_input_type('integer')->return_errors(true);
        
        //will update the data if there are no errors too display.
        if($total_faqs_data['has_errors'] === false){
            return $total_faqs;
        }

        //displays the error. 
        else{
            //create a js script that makes the invalid fields red. 
            add_settings_error($total_faqs_data['the_error']['id'], $total_faqs_data['the_error']['class'], $total_faqs_data['the_error']['message'], $total_faqs_data['the_error']['type']);
        }

    }

    function my_cool_plugin_settings_page() { ?>
    
    <div class="wrap">
    <h1><?php _e('Main Faq Page Settings', 'dlwfq_faqizer'); ?></h1>
    <?php 
        settings_errors(); 
        // this will display a message on saving the options on our settings page.
    ?>

    <form method="post" action="options.php">
        <?php settings_fields( 'dlwfq-archive-options-group' );?>
        <?php do_settings_sections( 'dlwfq-archive-options-group' ); ?>
        <?php do_action('dlwfq-validate-plugin-option-entries'); ?>

        <?php 
            $plugin_prefix = 'dlw_frequently_asked_questions'; //the prefix of the plugin
        ?>

        <div class="input-wrap">

            <div class="input-group">
                <label for="dlwfq-archive-options-slug" ><?php _e('The Faq Page Slug', 'dlwfq_faqizer'); ?></label>
                <input type="text" id="dlwfq-archive-options-slug" name="dlwfq-archive-options-slug" value="<?php echo esc_attr( get_option('dlwfq-archive-options-slug') ); ?>" placeholder="Enter Faq Page slug"/>
                <?php 
                    //make into a function so it's easier too add.
                    $current_installation_options = get_option('dlwfq-installation-options');
                    if( $current_installation_options['has-rewrite-rules-been-updated'] === true  ): ?>
                        <a class="button preview-button" style="max-width: 130px" href="<?php echo get_post_type_archive_link('dlw_wp_faq');?>"><?php _e('View Faq Page', 'dlwfq_faqizer'); ?></a>
                    <?php endif; 
                ?>
            </div>
            
            <div class="input-group">
                <label for="dlwfq-archive-accordion" ><?php _e('Enable accordion on Faq Page', 'dlwfq_faqizer'); ?></label>
                <input type="checkbox" id="dlwfq-archive-accordion" name="dlwfq-archive-accordion" value="1" <?php checked(1, get_option('dlwfq-archive-accordion'), true); ?> />
            </div>
            
            <div class="input-group">
                <label for="dlwfq-archive-title"><?php _e('Archive Page Title', 'dlwfq_faqizer'); ?><span class="dlwfq-help-tip"></span></label> 
                <input type="text" id="dlwfq-archive-title" name="dlwfq-archive-title" value="<?php echo esc_attr( get_option('dlwfq-archive-title') ); ?>" placeholder="Enter Faq Page Title"/>
            </div>

            <div class="input-group">
                <label for="dlwfq-total-posts-on-archive-page" ><?php _e('Total Amount of faqs to display', 'dlwfq_faqizer'); ?><span class="dlwfq-help-tip"></span></label> 
                <input type="number" id="dlwfq-total-posts-on-archive-page" name="dlwfq-total-posts-on-archive-page" value="<?php echo esc_attr( get_option('dlwfq-total-posts-on-archive-page') ); ?>" max="999"/></td>
            </div>

        </div>
        
        <?php submit_button( __('Save Faq Settings', 'dlwfq_faqizer') ); ?>

    </form>
    </div>
    <?php } ?>