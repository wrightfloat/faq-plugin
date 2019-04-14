<?php 

defined( 'ABSPATH' ) || exit;

/**
 * This function is reponsible for returning file paths for the plugin too use. 
 *
 * @param boolean $return_specfic_file_path returns a specfic file path when this value is set too true, if it's false it returns the whole array. 
 * @param string $file This returns the plugin path by defualt. It Has these options (theme-path: themes file path, theme-name: current theme name).
 * @return void
 */
function dlwfq_get_file_paths($return_specfic_file_path = false , $file = 'plugin-file-path'){

    //getting the current file path of our website. 
    $sitefile_array = explode( "/", DLWFAQ_PLUGIN_DIR );
    $sitefile_index = array_search( 'wp-content', $sitefile_array, true );
    array_splice($sitefile_array, $sitefile_index);
    $sitefilepath = implode( "/", $sitefile_array );

    //getting the template name and allowing us to format our 
    $current_template_array = explode( "/", get_template_directory_uri() ); //using the template directory uri to always grab the correct template even when a child theme is being used.
    $current_template_index = count($current_template_array);
    $current_template_name = $current_template_array[$current_template_index - 1];

    $current_template_details = array( 
        'current-theme-file-path' => $sitefilepath . '/wp-content/themes/' . $current_template_name, 
        'current-template-name' => $current_template_name,
        'plugin-file-path' => DLWFAQ_PLUGIN_DIR,   
    );

    // returning a specfic file path too users
    if($return_specfic_file_path){

        switch ($file) {
            case 'theme-path':
                $path = $current_template_details['current-theme-file-path'];
                break;

            case 'theme-name':
                $path = $current_template_details['current-template-name'];
                break;
            
            default:
                $path = $current_template_details['plugin-file-path'];
                break;
        }
        return $path; 
    }

    //returning all file paths too users. 
    else{
        return $current_template_details;
    } 
    
}

function dlwfq_does_theme_have_a_template($return_type, $return_array = false){


    $archive_template_name = 'archive-faqs.php';  //the custom template that i am looking for. 
    if( empty(locate_template( $archive_template_name, false )) ){
        $does_theme_have_template = array('has-template' => false); 
    }
    //runs when we have a template within the active theme
    elseif( !empty(locate_template( $archive_template_name, false )) ) {
        $does_theme_have_template = array('has-template' => true, 'template-path' => locate_template( $archive_template_name, false ) ); 
    }

    if($return_array){
        return $does_theme_have_template;
    }

    if($return_array === false) {
        switch ($return_type) {
            case 'has-template':
                $does_theme_have_template = $does_theme_have_template['has-template'];
                break;
            
            case 'template-path':
                if( isset($does_theme_have_template['template-path']) ){
                    $does_theme_have_template = $does_theme_have_template['template-path'];
                }
            break;
        }

        return $does_theme_have_template;
    }
    
} 

if ( ! function_exists( 'dlwfq_the_posts_navigation' ) ) :
	/**
	 * Documentation for function.
	 */
	function dlwfq_the_posts_navigation() {
		the_posts_pagination(

			array(
				'mid_size'  => 2,
				'prev_text' => sprintf(
					'%s <span class="nav-prev-text">%s</span>',
					twentynineteen_get_icon_svg( 'chevron_left', 22 ),
					__( 'Newer posts', 'dlwfq_faqizer' )
				),
				'next_text' => sprintf(
					'<span class="nav-next-text">%s</span> %s',
					__( 'Older posts', 'dlwfq_faqizer' ),
					twentynineteen_get_icon_svg( 'chevron_right', 22 )
				),
            )
            
        );
        
	}
endif;



//grab the amount of posts the user wants to display on the archive page. 
function dlwfq_get_the_archive_post_count(){
    $post_count = apply_filters('dlwfq_return_faq_loop_count', 'post_count'); 
    return $post_count; 
}

//setup the the total number of posts to display on the site for our custom post type.  
function dlwfq_pre_get_posts( $query ) {
    if ( !is_admin() && $query->is_post_type_archive('dlw_wp_faq') ) {
        // Modify posts per page
        $query->set( 'posts_per_page', dlwfq_get_the_archive_post_count() ); 
    }
}
add_action( 'pre_get_posts', 'dlwfq_pre_get_posts' );