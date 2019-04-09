    <?php
        /**
        * The Template for displaying our faqs on the Faqs Archive page. 
        */
    defined( 'ABSPATH' ) || exit;
    get_header();

    ?>

    <section>
        <header class="faq-header">
            <h1 class="faq-header-title page-title"><?php dlwfq_echo_archive_title(); ?></h1>
        </header>
    </section>
    
    <div id="faqs-container">
    
        <?php
            $args = array(
                'post_type'       => 'dlw_wp_faq',
                'post_status'     => 'publish',
                'paged'           => get_query_var( 'paged' ),
                'posts_per_page'  => 3, //this has to sync with the default posts per page 
            );
            // the query
            $the_query = new WP_Query( $args ); 
        ?>
        
        <?php if ( $the_query->have_posts() ) : ?>

            <ul id="basics" class="dlwfq-fq-list">
            <!-- the loop -->
            <?php
                $counter = -1; 
                while ( $the_query->have_posts() ) : $the_query->the_post();  $counter++; 
            ?> 

            <li class="dlwfq-fq-target" data-content-status="closed" data-index="<?php echo $counter; ?>">
                <span class="dlwfq-fq-wrap">
                    <!-- TODO: make this into an action -->
                    <?php if(dlwfq_get_accordian_settings() !== true): ?>
                        <a class="dlwfq-fq-target" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    <?php else: the_title(); endif; ?>
                </span>

                <div class="dlwfq-fq-content">
                    <p><?php  the_content();  //TODO: remove all empty p tags.?></p>
                </div> <!-- dlwfq-fq-content-->
            </li> 
            <?php endwhile; ?>
            <!-- end of the loop -->
            </ul>

            <!-- pagination here -->
            <?php echo paginate_links( array( 'total' => $the_query->max_num_pages) ); ?>
            <?php wp_reset_postdata(); ?>

            <?php else : ?>

            <!-- TODO: ADD IN SOME STYLING HERE. -->
            <!-- ADD A FILTER OF BEFORE AN AFTER FOR DEVS. -->
            <!-- ADD A FILTER TO MAKE THIS TEXT MORE DYNAMIC FROM THE BACKEND OF THE SITE. -->

            <ul id="basics" class="dlwfq-fq-list">
                <li class="dlwfq-fq-target" data-content-status="closed" data-index="1"><span class="dlwfq-fq-target"><?php esc_html_e( 'Sorry, No FAQS To Display here' ); ?></span></li>
            </ul>
        <?php endif; ?>

    </div>

<?php get_footer(); ?>