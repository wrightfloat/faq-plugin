<?php

/**
 * this sets up custom actions 
 *
 * @package Simplefaqs
 * @since   0.1.0
 */

defined( 'ABSPATH' ) || exit;

// TODO: Make this a filter instead of an action.
//the custom action to register frontend stylesheets
add_action( 'dlwfq_register_frontend_style', 'dlwfq_register_styles_action', 10, 1);
function dlwfq_register_styles_action($register_styles) {
  wp_register_style($register_styles['handle'], $register_styles['src'], $register_styles['customA'], $register_styles['version'], $register_styles['media-type']);
}

//adding a cutom action to get registered style. 
add_action( 'dlwfq_enqueue_frontend_style', 'dlwfq_enqueue_style', 10, 1);
function dlwfq_enqueue_style($enqueue_frontend_styles){
  wp_enqueue_style($enqueue_frontend_styles);
}

// TODO: Make this a filter instead of an action. 
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
//adding a cutom action to get registered script. 
add_action( 'dlwfq_enqueue_frontend_script', 'dlwfq_enqueue_script', 10, 1);

//setting up a custom achive title filter to retrive the archive title. 
add_filter( 'dlwfq_archive_title', 'dlwfq_return_archive_title', 10);
function dlwfq_return_archive_title(){
  $archivetitle = get_option('dlwfq-archive-title');
  if($archivetitle !== false){
    return $archivetitle; 
  }
}

//getting the archive title 
function dlwfq_get_archive_title(){
  $title = apply_filters('dlwfq_archive_title', 'title'); 
  return $title;
}


//returns the faqs slug for the Faq Posttype.  
add_filter( 'dlwfq_get_faq_slug', 'dlwfq_faq_slug', 10);
function dlwfq_faq_slug($faqslughasvalues){
  $faqslughasvalues = get_option('dlwfq-archive-options-slug');
  if( $faqslughasvalues !== false ){
      return array('slug' => strtolower($faqslughasvalues) );
  }
}  

//grab the users custom slug. 
function dlwfq_get_the_slug($return_as_array = false){

  $slug = apply_filters('dlwfq_get_faq_slug', 'slug'); 
  if($return_as_array){
    return $slug;
  }
  else{
    return $slug['slug'];
  }

}


//returns the amount of posts to show on the archive page.  
add_filter( 'dlwfq_return_faq_loop_count', 'dlwfq_return_faq_loop_count', 10);

function dlwfq_return_faq_loop_count($faqslughasvalues){

  $faqslughasvalues = get_option('dlwfq-total-posts-on-archive-page');

  //returns the total amount of posts to show when set on the settings page. 
  if( $faqslughasvalues !== false ){
      return $faqslughasvalues;
  }

  //returns the post count from the default posts_per_page option table. 
  else{
      return $faqslughasvalues = get_option('posts_per_page');
  }

}  

//grab the users custom slug. 
function dlwfq_get_the_archive_post_count(){
  $post_count = apply_filters('dlwfq_return_faq_loop_count', 'post_count'); 
  return $post_count; 
}