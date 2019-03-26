<?php
/**
 * The Template for displaying our faqs on the Faqs Archive page. 
 */

defined( 'ABSPATH' ) || exit;

    get_header();

    ?>
    
    <?php $custom_css = "margin: 0 calc(10% + 60px);"; ?>

    <section>
        <header class="faq-header" style="<?php echo $custom_css; ?>" >
            <h1 class="faq-header-title page-title"><?php echo dlwfq_get_archive_title(); ?></h1>
        </header>
    </section>
    
    <div id="faqs-container" style="<?php echo $custom_css; ?>" >
    
    <?php 
    
        $args = array(
            'posts_per_page'          => dlwfq_get_the_archive_post_count(),                 //(int) - number of post to show per page (available with Version 2.1). Use 'posts_per_page'=1 to show all posts (the 'offset' parameter is ignored with a -1 value). Note if the query is in a feed, wordpress overwrites this parameter with the stored 'posts_per_rss' option. Treimpose the limit, try using the 'post_limits' filter, or filter 'pre_option_posts_per_rss' and return -1
            'posts_per_archive_page'  => dlwfq_get_the_archive_post_count(),
            'post_type'    => 'dlw_wp_faq', 
            'post_status'  => 'publish',
            'paged'        => get_query_var( 'paged' ),  

        );

        // the query
        $the_query = new WP_Query( $args ); ?>
        <?php if ( $the_query->have_posts() ) : ?>
            <ul id="basics" class="dlwfq-fq-list">
            <!-- the loop -->
            
            <?php
            $counter = -1; 
            while ( $the_query->have_posts() ) : $the_query->the_post();  $counter++?> 

            <li class="dlwfq-fq-target" data-content-status="closed" data-index="<?php echo $counter; ?>">
                <!-- FIXME: when the accordian is not set as an option, link too the post page. -->
                <!-- <a class="dlwfq-fq-target" href="<?php //the_permalink(); ?>"><?php //the_title(); ?></a> -->

                <span class="dlwfq-fq-target">
                    <?php the_title(); ?>
                    <span class="icon dlwfq-pull-right" style="">
                    <!-- TODO: Make the this editable in the backend of the website -->
                    <!-- Need to edit the width -->
                    <!-- Need to edit the stroke within both the trianle and the square   -->
                    <!-- Add fallback support -->

                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="28px" viewBox="0 0 100 67.516" style="enable-background:new 0 0 100 67.516;" xml:space="preserve">
                        <g id="square">
                            <path class="square-icon" style="fill:none;stroke:#00304f;stroke-width:4;stroke-miterlimit:10;" d="M81.109,52.867c0,6.627-5.373,12-12,12H30.891c-6.628,0-12-5.373-12-12V14.648c0-6.628,5.372-12,12-12h38.219c6.627,0,12,5.372,12,12V52.867z"/>
                        </g>
                        <g id="triangle">
                            <polygon class="dlwfq-open-icon" style="display:none;fill:none;stroke:#00304f;stroke-width:5;stroke-miterlimit:10;" points="66.453,27.787 50,46.397 33.659,27.838"/>
                            <polygon class="dlwfq-close-icon" style="fill:none;stroke:#00304f;stroke-width:5;stroke-miterlimit:10;" points="33.659,46.397 50.113,27.786 66.453,46.346"/>
                        </g>
                    </svg>

                    </span>
                </span>

                <div class="dlwfq-fq-content">
                    <p><?php  the_content();  //TODO: remove all empty p tags.?></p>
                </div> <!-- dlwfq-fq-content-->
            </li> 
            <?php endwhile; ?>
            <!-- end of the loop -->
            </ul>

            <!-- pagination here -->
            <?php dlwfq_the_posts_navigation(); ?>
            <?php wp_reset_postdata(); ?>

            <?php else : ?>

            <!-- TODO: ADD IN SOME STYLING HERE. -->
            <!-- ADD A FILTER OF BEFORE AN AFTER FOR DEVS. -->
            <!-- ADD A FILTER TO MAKE THIS TEXT MORE DYNAMIC FROM THE BACKEND OF THE SITE. -->

            <ul id="basics" class="dlwfq-fq-list">
                <li class="dlwfq-fq-target" data-content-status="closed" data-index="1"><span class="dlwfq-fq-target"><?php esc_html_e( 'Sorry, No FAQS To Display here' ); ?></span></li>
            </ul>

            <?php dlwfq_the_posts_navigation(); ?>
        <?php endif; ?>

        

        

    </div>

<?php get_footer(); ?>