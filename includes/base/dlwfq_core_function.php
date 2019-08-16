<?php 

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Undocumented function
 *
 * @param [type] $file_name
 * @return void
 */
function dlwfq_get_faq_archive_template($file_name) {	
	
	// $file_name = 'archive-faqs.php';  
	//the custom template file that i am looking for. 

	//to remove errors from future versions of php
	if( defined('DLWFQ_PLUGIN_DIR_PATH') ){

		//https://developer.wordpress.org/reference/functions/locate_template/
		$archive_template = locate_template($file_name, false);

		//load spefic assets based on the template that is loaded.
		if(!empty($archive_template)){

			$archive_template = array('template_loaded_via_theme' => true, 'template_loaded_via_plugin_true' => false, 'file' => $archive_template); 
			return $archive_template; 
		}

		//check if we got anything back from the locate_template function so we can load our plugins template if we have nothing being retured.
		if( empty($archive_template) ){
			
			$archive_template = DLWFQ_PLUGIN_DIR_PATH . 'templates/' . $file_name; //grabs the plugins faq template
			$archive_template = array('template_loaded_via_theme' => false, 'template_loaded_via_plugin_true' => true, 'file' => $archive_template); 
		
		}
		return $archive_template;
	}

}

/**
 * A function to setup our post naviagtion. 
 *
 * @return void
 */
function dlwfq_the_posts_navigation() {
	the_posts_pagination(

		array(
			'mid_size'  => 2,
			'prev_text' => sprintf(
				'%s <span class="nav-prev-text">%s</span>',
				_e( 'Newer posts', 'dlwfq_faqizer' )
			),
			'next_text' => sprintf(
				'<span class="nav-next-text">%s</span> %s',
				_e( 'Older posts', 'dlwfq_faqizer' )
			),
		)
		
	);
}

//shortcode for displaying our faq topics to users
function dlwfq_get_faq_topics_shortcode( $atts ) {
	
	$atts = shortcode_atts( 
		array(
			'category_to_return' => 'pixie',
			'number_to_return' => '2',
		), 
		$atts,
		'dlwfq_get_faq_topics' 
	);
	
	$args = array(
        'post_type'       => 'dlw_wp_faq',
        'post_status'     => 'publish',
        'tax_query' => array(
            array(
                'taxonomy' => 'dlwfq_topics',
                'field'    => 'slug',
                'terms'    => $atts['category_to_return'], //grabs the current term for our custom taxonomy.
            ),
        ),
		'posts_per_page'  => esc_attr( $atts['number_to_return'] ), //this has to sync with the default posts per page 
		
	);
		
			// the query
			$the_query = new WP_Query( $args ); 
			
			ob_start();
			while ( $the_query->have_posts() ) : $the_query->the_post();
				?>
					<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li> 
				<?php 
			endwhile;

			// Save output and stop output buffering
			wp_reset_postdata();
			$output = ob_get_contents();
			ob_clean();

			return $output;
			//var_dump($the_query);
	}

add_shortcode( 'dlwfq_get_faq_topics', 'dlwfq_get_faq_topics_shortcode' );