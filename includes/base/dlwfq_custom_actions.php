<?php

/**
 * this sets up custom actions 
 *
 * @package Faqizer
 * @since   0.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// TODO: seprate the actions an filters into different files. 
// TODO: Make this a filter instead of an action.
// TODO: add in some validation for the filters. Then we make sure that this is actually set by users. 
// TODO: the custom action to register frontend stylesheets
add_action( 'dlwfq_register_frontend_style', 'dlwfq_register_styles_action', 10, 1);

function dlwfq_register_styles_action($register_styles) {
  wp_register_style($register_styles['handle'], $register_styles['src'], $register_styles['customA'], $register_styles['version'], $register_styles['media-type']);
}

// adding a cutom action to get registered style. 
add_action( 'dlwfq_enqueue_frontend_style', 'dlwfq_enqueue_style', 10, 1);
function dlwfq_enqueue_style($enqueue_frontend_styles){
  wp_enqueue_style($enqueue_frontend_styles);
}
/**
 *  A custom action to register frontend script.
 *
 * @param array $register_scripts this takes an array like this - $handle, $src, $deps, $ver, $in_footer
 * @return void
 */
function dlwfq_register_script_action($register_scripts) {
  wp_register_script($register_scripts['handle'], $register_scripts['src'], $register_scripts['customA'], $register_scripts['version'], $register_scripts['footer']);
}
add_action( 'dlwfq_register_frontend_script', 'dlwfq_register_script_action', 10, 1);

function dlwfq_enqueue_script($enqueue_frontend_script){
  wp_enqueue_script($enqueue_frontend_script);
}
// adding a cutom action to get registered script. 
add_action( 'dlwfq_enqueue_frontend_script', 'dlwfq_enqueue_script', 10, 1);


// will be used to generate the title for frequently asked questions for the main faq archive page and grab the title for any taxonmy that is created
// setting up the custom achive title filter. This will be displayed on the archive-faqs.php page.
function dlwfq_get_archive_title($archiveType) {

  // could also check to see if we are on the correct post type.
  if($archiveType === 'taxonomy') {
      $title = get_term_by( 'slug', get_query_var( 'faq-topics' ), 'dlwfq_topics' )->name;
      return $title;
  }
  
  else if($archiveType === 'archive') {
    //sets the archive title on the main archive page from the saved database value. 
    $archivetitle = get_option('dlwfq-archive-title');
    if($archivetitle !== false){
      $title = $archivetitle;
      return $title;
    }
    //if for some reason this runs an this field in the database does not have any data within it i may want to return the settings page link so they can update this value.

  }
  else{
    return; 
  }
} 
add_filter( 'dlwfq_set_archive_title', 'dlwfq_get_archive_title', 10, 1);

//to display the archive title
function dlwfq_echo_archive_title($archiveType){
  echo apply_filters('dlwfq_set_archive_title', $archiveType);
}

// returns the faqs slug for the Faq Posttype.  
function dlwfq_faq_slug($faqslughasvalues){
  $faqslughasvalues = get_option('dlwfq-archive-options-slug');
  if($faqslughasvalues !== false){
    return array('slug' => strtolower($faqslughasvalues) );
  }
}  
add_filter( 'dlwfq_get_faq_slug_filter', 'dlwfq_faq_slug', 10);

// grabs the users custom slug. 
function dlwfq_get_the_slug($return_as_array = false){
  $slug = apply_filters('dlwfq_get_faq_slug_filter', 'slug'); 
  if($return_as_array !== false){
    return $slug;
  }
}

// filter to get the option from the db that will display the accordian on the archive page.  
function dlwfq_display_accordian($accordianvalues){
  $accordianvalues = get_option('dlwfq-archive-accordion');
  //will get the option from the database.
  if( $accordianvalues !== false){ 
    // displays accordian
    if($accordianvalues == 1){
      $accordianvalues = true; 
    }
    //disabled accordian
    else if($accordianvalues == 0){
      $accordianvalues = false; 
    }
    return $accordianvalues;
  }
  else{
    return false; 
  }
}

add_filter( 'dlwfq_return_accordian_option_filter', 'dlwfq_display_accordian', 10);

// Setting up a easy function to use throught my plugin.   
function dlwfq_get_accordian_settings(){
  $accordian_settings = apply_filters('dlwfq_return_accordian_option_filter', 'get_accordian_settings'); 
  return $accordian_settings; 
}

// returns the amount of posts to show on the archive pages, grabs the value from the wp-options with the key of 'dlwfq-total-posts-on-archive-page'.  
function dlwfq_get_faq_loop_count($faqslughasvalues){
  $faqslughasvalues = get_option('dlwfq-total-posts-on-archive-page');
  // returns the total amount of posts to show when set on the settings page. 
  if($faqslughasvalues !== false){
    return absint($faqslughasvalues);
  }
}
add_filter( 'dlwfq_return_faq_loop_count_filter', 'dlwfq_get_faq_loop_count', 10);

//grab the amount of posts the user wants to display on the archive page. 
function dlwfq_get_the_archive_post_count(){

  $post_count = apply_filters('dlwfq_return_faq_loop_count_filter', 'post_count'); 
   
  if($post_count === 0){
    $post_count = get_option('posts_per_page'); // defaults to wordpress's posts per page option. 
  }

  return $post_count; 
}
 
/**
 * Setup up the query for wp too use for our custom post type. If this is not setup the custom pagination will not work as intended.
 *
 * @param [type] $query
 * @return void
 */
function dlwfq_pre_get_posts( $query ) {
  if ( !is_admin() && $query->is_post_type_archive('dlw_wp_faq') ) {
      // Modifing our posts per page with the value added too the database. 
      $query->set( 'posts_per_page', dlwfq_get_the_archive_post_count() ); 
  }
}

add_action( 'pre_get_posts', 'dlwfq_pre_get_posts' );

/**
 * filter for setting up the faq icons for the faqs.
 *
 * @param array $setup_icon_atts takes an array of values like the following ( 'width' => '48', 'height' => '48', 'style' => 'display: block;'); 
 * @param string $setup_icon_src the src of the icon that will be used for the faqs. 
 * @return void
 */

function dlw_setup_faq_icon($setup_icon_atts, $setup_icon_src){

    if($setup_icon_atts === false){
      $setup_icon_atts = array( 
        'width' => '48', 
        'height' => '48', 
        'style' => 'display: block;',
      );
    }

    if($setup_icon_src === false){
      $setup_icon_src =  plugins_url() . '/faqizer/assets/frontend/img/down-arrow-solid.png';
    }

    $dlw_icon_html = '<span class="dlwfq-fq-icons">' . '<img width="' . $setup_icon_atts['width'] . '" height="' . $setup_icon_atts['height'] . '" style="' . $setup_icon_atts['style'] . '" src="' . $setup_icon_src . '"></span>';
    return $dlw_icon_html; 
}

add_filter('dlw_setup_faq_icon', 'dlw_setup_faq_icon', 2, 10);