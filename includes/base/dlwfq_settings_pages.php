<?php

// create custom plugin settings menu
add_action('admin_menu', 'my_cool_plugin_create_menu');

function my_cool_plugin_create_menu() {

	//create new top-level menu
	add_menu_page('Faqizer Setting\'s Page', 'Faqizer', 'manage_options', 'dlwfq-settings', 'my_cool_plugin_settings_page', 'dashicons-admin-settings', 26);

	//call register settings function
	add_action( 'admin_init', 'register_my_cool_plugin_settings' );
}


function register_my_cool_plugin_settings() {
    //TODO: options that i would like too add.
    //will need to setup default options on plugin's installation. 
    //add_option('dlwfq-archive-options-slug', 'Faqs'); //sets the default option of the slug to Faqs
    //add_option('dlwfq-archive-accordion', false);
    //add_option('dlwfq-archive-title', 'Frequently Asked Questions');

    //used to create a check box field and have access too the checked function. 
    add_settings_field("dlwfq-archive-accordion", "Enable accordion on Faq page", "my_cool_plugin_settings_page", "dlwfq-archive-options-group", "default"); 
    
    //A option to change the 'faqs' slug 
    register_setting( 'dlwfq-archive-options-group', 'dlwfq-archive-options-slug' );

    //A option to add an accordian to the faq's on the archive page.
    register_setting( 'dlwfq-archive-options-group', 'dlwfq-archive-accordion' );
    
    //update_option('dlwfq-archive-title', 'Frequently Asked Questions');

    //A option to setup the title on the archive faq page. 
    register_setting( 'dlwfq-archive-options-group', 'dlwfq-archive-title' );
    
    //A the below option will only ever have any info within it if this option gets set.
    register_setting( 'dlwfq-archive-options-group', 'dlwfq-show-a-custom-amount-posts');

    //Add a option to show a differnet amount of faqs then what is shown on the blog, make sure to note that this will only be the same if the theme uses the get_option('posts_per_page');
    //Make this defualt to what the blog pages are supposed to show at most. The default option will be grabed from get_option('posts_per_page');
    register_setting( 'dlwfq-archive-options-group', 'dlwfq-total-posts-on-archive-page' );

}

function my_cool_plugin_settings_page() {
?>
<div class="wrap">
<h1>Main Faq Page Settings</h1>

<form method="post" action="options.php">
    <?php settings_fields( 'dlwfq-archive-options-group' );?>
    <?php do_settings_sections( 'dlwfq-archive-options-group' ); ?>

    <?php 
        $plugin_prefix = 'dlw_frequently_asked_questions'; //the prefix of the plugin
    ?>
    <!-- TODO: make things look a little better -->
    <table class="form-table">

        <tr valign="top">
            <th scope="row">
                <label for="dlwfq-archive-options-slug"><?php e_('The Archive Slug', $plugin_prefix); ?></label>
            </th>

            <td><input type="text" id="dlwfq-archive-options-slug" name="dlwfq-archive-options-slug" value="<?php echo esc_attr( get_option('dlwfq-archive-options-slug') ); ?>" /></td>
            
            <!-- TODO: Output page where the FAQs will be displayed.  So the user can preview the page -->
        </tr>
         
        <tr valign="top">

            <th scope="row">
                <label for="dlwfq-archive-accordion"><?php e_('Enable accordion on Faq page', $plugin_prefix); ?></label>
            </th>
            <td><input type="checkbox" id="dlwfq-archive-accordion" name="dlwfq-archive-accordion" value="1" <?php checked(1, get_option('dlwfq-archive-accordion'), true); ?>/></td>
        
        </tr>
        
        <tr valign="top">

            <th scope="row">
                <label for="dlwfq-archive-title"><?php e_('Archive Page Title', $plugin_prefix); ?><span class="dlwfq-help-tip"></span></label> 
            </th>
            <td><input type="text" id="dlwfq-archive-title" name="dlwfq-archive-title" value="<?php echo esc_attr( get_option('dlwfq-archive-title') ); ?>" /></td>
        </tr>

        <tr valign="top">
            <th scope="row">
                <label for="dlwfq-total-posts-on-archive-page"><?php e_('Number of post to display on Archive page', $plugin_prefix); ?><span class="dlwfq-help-tip"></span></label> 
            </th>
            <td><input type="number" id="dlwfq-total-posts-on-archive-page" name="dlwfq-total-posts-on-archive-page" value="<?php echo esc_attr( get_option('dlwfq-total-posts-on-archive-page') ); ?>" /></td>
        </tr>
        
    </table> 
    
    <?php submit_button(); ?>

</form>
</div>
<?php } ?>