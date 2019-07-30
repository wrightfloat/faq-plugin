    <?php
        /**
        * The Template for displaying our faqs on the Faqs Archive page. 
        */
        if ( ! defined( 'ABSPATH' ) ) {
            exit;
        }
    ?>
    
    <?php get_header(); ?>
        
    <div id="faqs-container" class="faq-plugin-template">
        <section>
            <header class="faq-header">
                <h1 class="faq-header-title page-title">
                    <?php esc_html( dlwfq_echo_archive_title('archive') ); ?>
                </h1>
            </header>
        </section>

        <?php
            $args = array(
                'post_type'       => 'dlw_wp_faq',
                'post_status'     => 'publish',
                'paged'           => get_query_var( 'paged' ),
                'posts_per_page'  => esc_attr( dlwfq_get_the_archive_post_count() ), //this has to sync with the default posts per page 
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
                    
                    <?php 
                        //if the user does not have accordian enabled a link will be clickable, which will take the user to the single faq page. 
                        if( dlwfq_get_accordian_settings() !== true){ 
                    ?>
                    <a class="dlwfq-fq-target" href="<?php the_permalink(); ?>">
                        <?php the_title(); ?>
                    </a>

                    <?php }
                        //When the user has the accordian enabled no link will appear in the title area of the faq.
                    else{
                        the_title();
                    ?>
                        <span class="dlwfq-fq-icons">
                            <img width="48px" height="48px" style="display: block;" src="<?php echo get_site_url() .  '/wp-content/plugins/faqizer/assets/frontend/img/down-arrow-solid.png'; ?> ">
                        </span>
                        
                    <?php } ?>
                </span>

                <div class="dlwfq-fq-content">
                    <p><?php esc_html( the_content() ) ;?></p>
                </div> <!-- dlwfq-fq-content-->
            </li> 
            <?php endwhile; ?>
            <!-- end of the loop -->
            </ul>
            <div id="dlwfq-pagination">
                <!-- pagination here -->
                <?php echo paginate_links( array( 'total' => $the_query->max_num_pages) ); ?>
            </div>
            <?php wp_reset_postdata(); ?>
            
        <?php else: ?>
            <ul id="basics" class="dlwfq-fq-list">
                <li class="dlwfq-fq-target" data-content-status="closed" data-index="1"><span class="dlwfq-fq-target"><?php esc_html_e( __('Sorry, No FAQS To Display here', 'dlwfq_faqizer') ); ?></span></li>
            </ul>
        <?php endif; ?>
    </div>

<?php get_footer(); ?>