<?php
/**
 * Faqizer setup custom posttype setup class
 * @package Faqizer
 * @since   0.2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class dlwfq_custom_post_type {
    
    private $single_page_faq_slug; 
    private $single;
    private $plural;
    public  $custom_post_type_slug;
    private $custom_title; 

    public function __construct($custom_post_type_slug, $custom_post_type) {
        // Set post type name
        $this->custom_post_type_slug = $custom_post_type_slug;
        $this->custom_post_type = $custom_post_type;
        $this->init();
    }

    private function init(){
        add_action('init', array($this, 'create_our_custom_posttype' ) );

        //updates the title placeholder on custom post type
        add_filter( 'enter_title_here', array( $this, 'change_enter_title_here_text' ) );
    }

    protected function updateCase($string, $updateCase, $addSpace = false){
        //make sure that this is not empty.
        if( isset($string) ){
            if($updateCase !== false){
                switch ($updateCase) {
                    case 'lcfirst':
                        $string = lcfirst($string); // converts the first character of a string to lowercase.
                        break;
                    case 'ucfirst':
                        $string = ucfirst($string); // converts the first character of a string to uppercase.
                        break;
                    
                }
            }
            if($addSpace){
                $string = str_repeat('&nbsp;', 1) . $string . str_repeat('&nbsp;', 1);
            }
            else{
                $string = $string;
            }
        }
        return $string; 
    }

    /**
     * Add in custom post type labels
     * https://codex.wordpress.org/Function_Reference/register_post_type#Parameters
     *
     * @param string $plural a plural label for the custom posttype.
     * @param string $single a single label for the custom posttype.
     * @param string $custom_title Used as the place holder for the title of wordpress
     * @return void
     */

    public function setupLabels($plural, $single, $custom_title = false){
        $this->plural = $plural;
        $this->single = $single;
        $this->custom_title = $custom_title; 
        $labels = array(
            'name'                      => _x( $this->updateCase($single, 'Ucfirst') , 'post type general name', 'dlwfq_faqizer' ), //single name
            'singular_name'             => _x( $this->updateCase($single, 'Ucfirst') , 'post type singular name', 'dlwfq_faqizer' ),
            'menu_name'                 => _x( $this->updateCase($plural, 'Ucfirst') , 'admin menu', 'dlwfq_faqizer' ),
            'add_new'                   => _x( 'Add New' . $this->updateCase($single, 'Ucfirst', true) , 'dlwfq_faqizer' ), //Add New -item // this is the text that is displayed on the add button on the edit.php page and replaces the add new text within the sidebar menu.
            'add_new_item'              => __( 'Add New' . $this->updateCase($single, 'Ucfirst', true) , 'dlwfq_faqizer' ), 
            'new_item'                  => __( 'New' . $this->updateCase($single, 'Ucfirst', true) , 'dlwfq_faqizer' ),
            'edit_item'                 => __( 'Edit' . $this->updateCase($single, 'Ucfirst', true) , 'dlwfq_faqizer' ),
            'view_item'                 => __( 'View' . $this->updateCase($single, 'Ucfirst', true) , 'dlwfq_faqizer' ),
            'view_items'                => __( 'View' . $this->updateCase($plural, 'Ucfirst', true) , 'dlwfq_faqizer' ),
            'all_items'                 => __( 'All' . $this->updateCase($plural, 'Ucfirst', true) , 'dlwfq_faqizer' ),
            'search_items'              => __( 'Search'. $this->updateCase($plural, 'Ucfirst', true) , 'dlwfq_faqizer' ),
            'parent_item_colon'         => __( 'Parent' . $this->updateCase($plural, 'Ucfirst', true) . ':', 'dlwfq_faqizer' ),
            'not_found'                 => __( 'No' . $this->updateCase($plural, 'Ucfirst', true) . 'found.', 'dlwfq_faqizer' ),
            'not_found_in_trash'        => __( 'No' . $this->updateCase($plural, 'Ucfirst', true) . 'found in Trash.', 'dlwfq_faqizer' ),
            'items_list_navigation'     => __( 'items_list_navigation' . $this->updateCase($plural, 'Ucfirst', true), 'dlwfq_faqizer' ), //- String for the table pagination hidden heading.
            'items_list'                => __( 'items_list' . $this->updateCase($plural, 'Ucfirst', true), 'dlwfq_faqizer' ), //- String for the table hidden heading.
            'name_admin_bar'            => _x( $this->updateCase($single, 'Ucfirst') , 'add new on admin bar', 'dlwfq_faqizer' ),
        );
        return $this->labels = $labels;
    }

    /**
     * function that actually creates the posttype. 
     * https://codex.wordpress.org/Function_Reference/register_post_type
     *
     * @return void
     */
    
    public function create_our_custom_posttype() {
        $labels = $this->labels;
        register_post_type(  $this->custom_post_type,
            array(
                'labels' => $labels,
                'description' => 'Enter a FAQ',
                'public' => true,
                'exclude_from_search' => false, //Whether to exclude posts with this post type from front end search results.
                'publicly_queryable' => true, //Whether queries can be performed on the front end as part of parse_request.
                'show_ui' => true, //Whether to generate a default UI for managing this post type in the admin
                'show_in_nav_menus' => true, //Whether to generate a default UI for managing this post type in the admin
                'show_in_menu' => true, //Where to show the post type in the admin menu. show_ui must be true.
                'show_in_admin_bar' => true, //Whether to make this post type available in the WordPress admin bar.
                'menu_position' => 102, //The position in the menu order the post type should appear. show_in_menu must be true.
                'menu_icon' => 'dashicons-flag', 
                'hierarchical' => false, //Whether the post type is hierarchical (e.g. page). Allows Parent to be specified. The 'supports' parameter should contain 'page-attributes' to show the parent select box on the editor page.
                'supports' => array('title', 'editor', 'author'),   //title, editor, author, thumbnail, excerpt, trackbacks, custom-fields, comments, revisions, page-attributes, post-formats
                'has_archive' => true, //Enables post type archives. Will use $post_type as archive slug by default.
                'rewrite' => array('slug' => $this->custom_post_type_slug['slug'], 'with_front' => false ),// Triggers the handling of rewrites for this post type. To prevent rewrites, set to false.
                'can_export' => true,  //allows users to export csv files
                'delete_with_user' => false, //Whether to delete posts of this type when deleting a user. If true, posts of this type belonging to the user will be moved to trash when then user is deleted. If false, posts of this type belonging to the user will not be trashed or deleted. If not set (the default), posts are trashed if post_type_supports('author'). Otherwise posts are not trashed or deleted.
                'show_in_rest' => false, //Whether to expose this post type in the REST API.
            )
        );
    }

    /**
     * edits the h1 with the class of .wp-heading-inline on the new and edit screens for the custom post type. 
     *
     * @param string $title the placeholder for the post title on the edit screens
     * @return void
     */
    public function change_enter_title_here_text( $title ){
        $screen = get_current_screen();
        if  ( $this->custom_post_type === $screen->post_type ) {
            
            $title = $this->updateCase($this->custom_title, 'Ucfirst'); 
        }
        return $title;   
    }


}