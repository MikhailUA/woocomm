<?php
require_once( get_template_directory() . '/classes/CustomWalkerNavMenu.php');
if ( ! function_exists( 'base_theme_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
function base_theme_setup() {

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on base_theme, use a find and replace
	 * to change 'base_theme' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'base_theme', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * See: https://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 825, 510, true );
        add_image_size( 'default-items', 350, 350, true );

	// This theme uses wp_nav_menu() in two locations.
	register_nav_menus( array(
		'primary' => __( 'Primary Menu',      'base_theme' ),
        'footer-menu' => __( 'Footer Menu',      'base_theme' ),
		'social'  => __( 'Social Links Menu', 'base_theme' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
	) );

	/*
	 * Enable support for Post Formats.
	 *
	 * See: https://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array(
		'aside', 'image', 'video', 'quote', 'link', 'gallery', 'status', 'audio', 'chat'
	) );

}
endif; // base_theme_setup
add_action( 'after_setup_theme', 'base_theme_setup' );

/**
 * Register widget area.
 */
function base_theme_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Widget Area', 'base_theme' ),
		'id'            => 'sidebar-default',
		'description'   => __( 'Add widgets here to appear in your sidebar.', 'base_theme' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'base_theme_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function base_theme_scripts() {

        /* remove 
        for removing scripts use this construction
         * 
            wp_deregister_style( 'some' );
            wp_dequeue_style( 'some' ); 
         */

        // Register Styles
        wp_register_style( 'main-stylesheet', get_stylesheet_uri() );
        wp_register_style( 'all', get_template_directory_uri() .'/css/all.css' );

        if ( is_child_theme() ) {
                wp_register_style( 'child-stylesheet', get_stylesheet_uri() .'/style.css' );
        }

        // Enqueue Styles
        wp_enqueue_style('main-stylesheet');
        wp_enqueue_style('all');

        // Register Scripts

        wp_register_script( 'main',  get_template_directory_uri() .'/js/main.js', array('jquery'), false, true );

        // Enqueue Scripts
        wp_enqueue_script('jquery');
        wp_enqueue_script('main');
        if ( is_singular() && comments_open() && get_option('thread_comments')) {
                wp_enqueue_script( 'comment-reply' );
        }

        //Localizate Scripts
        wp_localize_script( 'main', 'objectName', array(
                'someTextForTranslate'   => '<span class="screen-reader-text">' . __( 'localization text', 'base_theme' ) . '</span>',
        ) );
        // Send AjaxUrl to our script (in script params.ajax_url)
        wp_localize_script( 'main', 'params', array(
                'ajax_url' => admin_url( 'admin-ajax.php' )
        ) );
}
add_action( 'wp_enqueue_scripts', 'base_theme_scripts' );

// Customithation Search form for comments form use same function
function base_theme_search_form_modify( $html ) {
	return str_replace( 'class="search-submit"', 'class="search-submit screen-reader-text"', $html );
}
add_filter( 'get_search_form', 'base_theme_search_form_modify' );

/*Register tag [template-url]*/
function filter_template_url($text) {
	return str_replace('[template-url]',get_bloginfo('template_url'), $text);
}
add_filter('the_content', 'filter_template_url');
add_filter('get_the_content', 'filter_template_url');
add_filter('widget_text', 'filter_template_url');

/* Ajax handler
 * in js file, ajax call parametrs:
 *   action: loadmore (becouse name wp_ajax_[..loadmore..])
 *   url: ajaxurl) */
function right_ajax_call(){
	exit;
}
 
add_action('wp_ajax_loadmore', 'right_ajax_call');
add_action('wp_ajax_nopriv_loadmore', 'right_ajax_call');

/* ACF functions */
//theme options tab in appearance
if(function_exists('acf_add_options_sub_page')) {
	acf_add_options_sub_page(array(
		'title' => 'Theme Options',
		'parent' => 'themes.php',
	));
}

//acf theme functions placeholders
if(!class_exists('acf') && !is_admin()) {
	function get_field_reference( $field_name, $post_id ) {return '';}
	function get_field_objects( $post_id = false, $options = array() ) {return false;}
	function get_fields( $post_id = false) {return false;}
	function get_field( $field_key, $post_id = false, $format_value = true )  {return false;}
	function get_field_object( $field_key, $post_id = false, $options = array() ) {return false;}
	function the_field( $field_name, $post_id = false ) {}
	function have_rows( $field_name, $post_id = false ) {return false;}
	function the_row() {}
	function reset_rows( $hard_reset = false ) {}
	function has_sub_field( $field_name, $post_id = false ) {return false;}
	function get_sub_field( $field_name ) {return false;}
	function the_sub_field($field_name) {}
	function get_sub_field_object( $child_name ) {return false;}
	function acf_get_child_field_from_parent_field( $child_name, $parent ) {return false;}
	function register_field_group( $array ) {}
	function get_row_layout() {return false;}
	function acf_form_head() {}
	function acf_form( $options = array() ) {}
	function update_field( $field_key, $value, $post_id = false ) {return false;}
	function delete_field( $field_name, $post_id ) {}
	function create_field( $field ) {}
	function reset_the_repeater_field() {}
	function the_repeater_field($field_name, $post_id = false) {return false;}
	function the_flexible_field($field_name, $post_id = false) {return false;}
	function acf_filter_post_id( $post_id ) {return $post_id;}
}

add_action( 'related_woocommerce_template_loop_add_to_cart', 'woocommerce_template_loop_add_to_cart', 10 );