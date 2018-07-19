<?php
/**
 * devita functions and definitions
 */

/**
* Require files
*/
	//TGM-Plugin-Activation
require_once( get_template_directory().'/class-tgm-plugin-activation.php' );
	//Init the Redux Framework
if ( class_exists( 'ReduxFramework' ) && !isset( $redux_demo ) && file_exists( get_template_directory().'/theme-config.php' ) ) {
	require_once( get_template_directory().'/theme-config.php' );
}
	// Theme files
if ( !class_exists( 'devita_widgets' ) && file_exists( get_template_directory().'/include/devitawidgets.php' ) ) {
	require_once( get_template_directory().'/include/devitawidgets.php' );
}

if ( !class_exists( 'vertical_menu_widgets' ) && file_exists( get_template_directory().'/include/vertical_menu_widgets.php' ) ) {
	require_once( get_template_directory().'/include/vertical_menu_widgets.php' );
}
if ( file_exists( get_template_directory().'/include/wooajax.php' ) ) {
	require_once( get_template_directory().'/include/wooajax.php' );
}
if ( file_exists( get_template_directory().'/include/map_shortcodes.php' ) ) {
	require_once( get_template_directory().'/include/map_shortcodes.php' );
}
if ( file_exists( get_template_directory().'/include/shortcodes.php' ) ) {
	require_once( get_template_directory().'/include/shortcodes.php' );
} 
Class Devita_Class {
	
	/**
	* Global values
	*/
	static function devita_post_odd_event(){
		global $wp_session;
		
		if(!isset($wp_session["devita_postcount"])){
			$wp_session["devita_postcount"] = 0;
		}
		
		$wp_session["devita_postcount"] = 1 - $wp_session["devita_postcount"];
		
		return $wp_session["devita_postcount"];
	}
	static function devita_post_thumbnail_size($size){
		global $wp_session;
		
		if($size!=''){
			$wp_session["devita_postthumb"] = $size;
		}
		
		return $wp_session["devita_postthumb"];
	}
	static function devita_shop_class($class){
		global $wp_session;
		
		if($class!=''){
			$wp_session["devita_shopclass"] = $class;
		}
		
		return $wp_session["devita_shopclass"];
	}
	static function devita_show_view_mode(){

		$devita_opt = get_option( 'devita_opt' );
		
		$devita_viewmode = 'grid-view'; //default value
		
		if(isset($devita_opt['default_view'])) {
			$devita_viewmode = $devita_opt['default_view'];
		}
		if(isset($_GET['view']) && $_GET['view']!=''){
			$devita_viewmode = $_GET['view'];
		}
		
		return $devita_viewmode;
	}
	static function devita_shortcode_products_count(){
		global $wp_session;
		
		$devita_productsfound = 0;
		if(isset($wp_session["devita_productsfound"])){
			$devita_productsfound = $wp_session["devita_productsfound"];
		}
		
		return $devita_productsfound;
	}
	
	static function devita_products_count() {
		global $wp_query;

		$devita_opt = get_option( 'devita_opt' );
		$perp = $devita_opt['product_per_page'];
		$max_page = $wp_query->max_num_pages;
		$total = devita_total_product_count();
		$number_products_page = (($perp*$max_page) - $total );
		$current_page = max( 1, get_query_var( 'paged' ) );
		$devita_products_count =0;
		if ($current_page == $max_page) {
			
			$devita_products_count = $perp - $number_products_page;
		}
		if ( wc_get_loop_prop( 'total' ) <= $perp || -1 === $perp ) {  $devita_products_count = wc_get_loop_prop( 'total' ); }
		return $devita_products_count;
	}
	/**
	* Constructor
	*/
	function __construct() {
		// Register action/filter callbacks
		
			//WooCommerce - action/filter
		add_theme_support( 'woocommerce' );
		remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
		remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
		add_filter( 'get_product_search_form', array($this, 'devita_woo_search_form'));
		add_filter( 'woocommerce_shortcode_products_query', array($this, 'devita_woocommerce_shortcode_count'));
		add_action( 'woocommerce_share', array($this, 'devita_woocommerce_social_share'), 35 );
		add_action( 'woocommerce_archive_description', array($this, 'devita_woocommerce_category_image'), 2 );
		
			//move message to top
		remove_action( 'woocommerce_before_shop_loop', 'wc_print_notices', 10 );
		add_action( 'woocommerce_show_message', 'wc_print_notices', 10 );

		add_action('woocommerce_after_shop_loop','woocommerce_result_count', 11);

			//remove cart total under cross sell
		remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cart_totals', 10 );
		add_action( 'cart_totals', 'woocommerce_cart_totals', 5 );

		//remove add to cart button after item
		remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
		
			//Single product organize   
		
		
			//WooProjects - Project organize
		remove_action( 'projects_before_single_project_summary', 'projects_template_single_title', 10 );
		add_action( 'projects_single_project_summary', 'projects_template_single_title', 5 );
		remove_action( 'projects_before_single_project_summary', 'projects_template_single_short_description', 20 );
		remove_action( 'projects_before_single_project_summary', 'projects_template_single_gallery', 40 );
		add_action( 'projects_single_project_gallery', 'projects_template_single_gallery', 40 );
		
			//WooProjects - projects list
		remove_action( 'projects_loop_item', 'projects_template_loop_project_title', 20 );
		
			//Theme actions
		add_action( 'after_setup_theme', array($this, 'devita_setup'));
		add_action( 'tgmpa_register', array($this, 'devita_register_required_plugins')); 
		add_action( 'widgets_init', array($this, 'devita_override_woocommerce_widgets'), 15 );
		
		add_action( 'wp_enqueue_scripts', array($this, 'devita_scripts_styles') );
		add_action( 'wp_head', array($this, 'devita_custom_code_header'));
		add_action( 'widgets_init', array($this, 'devita_widgets_init'));
		
		
		add_action('comment_form_before_fields', array($this, 'devita_before_comment_fields'));
		add_action('comment_form_after_fields', array($this, 'devita_after_comment_fields'));
		add_action( 'customize_register', array($this, 'devita_customize_register'));
		add_action( 'customize_preview_init', array($this, 'devita_customize_preview_js')); 
		add_action('admin_enqueue_scripts', array($this, 'devita_admin_style'));
		
			//Theme filters 
		add_filter( 'loop_shop_per_page', array($this, 'devita_woo_change_per_page'), 20 );
		add_filter( 'woocommerce_output_related_products_args', array($this, 'devita_woo_related_products_limit'));
		add_filter( 'get_search_form', array($this, 'devita_search_form'));
		add_filter('excerpt_more', array($this, 'devita_new_excerpt_more'));
		add_filter( 'excerpt_length', array($this, 'devita_change_excerpt_length'), 999 );
		add_filter('wp_nav_menu_objects', array($this, 'devita_first_and_last_menu_class'));
		add_filter( 'wp_page_menu_args', array($this, 'devita_page_menu_args'));
		add_filter('dynamic_sidebar_params', array($this, 'devita_widget_first_last_class'));
		add_filter('dynamic_sidebar_params', array($this, 'devita_mega_menu_widget_change'));
		add_filter( 'dynamic_sidebar_params', array($this, 'devita_put_widget_content'));
		
		//Adding theme support
		if ( ! isset( $content_width ) ) {
			$content_width = 625;
		}
	}
	
	/**
	* Filter callbacks
	* ----------------
	*/
	 
	// Change products per page
	function devita_woo_change_per_page() {
		$devita_opt = get_option( 'devita_opt' );
		
		return $devita_opt['product_per_page'];
	}
	//Change number of related products on product page. Set your own value for 'posts_per_page'
	function devita_woo_related_products_limit( $args ) {
		global $product;

		$devita_opt = get_option( 'devita_opt' );

		$args['posts_per_page'] = $devita_opt['related_amount'];

		return $args;
	}
	// Count number of products from shortcode
	function devita_woocommerce_shortcode_count( $args ) {
		$devita_productsfound = new WP_Query($args);
		$devita_productsfound = $devita_productsfound->post_count;
		
		global $wp_session;
		
		$wp_session["devita_productsfound"] = $devita_productsfound;
		
		return $args;
	}
	//Change search form
	function devita_search_form( $form ) {
		if(get_search_query()!=''){
			$search_str = get_search_query();
		} else {
			$search_str = esc_html__( 'Search...', 'devita' );
		}
		
		$form = '<form role="search" method="get" id="blogsearchform" class="searchform" action="' . esc_url(home_url( '/' ) ). '" >
		<div class="form-input">
			<input class="input_text" type="text" value="'.esc_attr($search_str).'" name="s" id="search_input" />
			<button class="button" type="submit" id="blogsearchsubmit">'.esc_html__('search','devita').'</button>
			</div>
		</form>';
		 
		return $form;
	}
	//Change woocommerce search form
	function devita_woo_search_form( $form ) {
		global $wpdb;
		
		if(get_search_query()!=''){
			$search_str = get_search_query();
		} else {
			$search_str = esc_html__( 'Search product...', 'devita' );
		}
		
		$form = '<form role="search" method="get" id="searchform" action="'.esc_url( home_url( '/'  ) ).'">';
			$form .= '<div class="form-input">';
				$form .= '<input type="text" value="'.esc_attr($search_str).'" name="s" id="ws" placeholder="'.esc_attr($search_str).'" />';
				$form .= '<button class="btn btn-primary" type="submit" id="wsearchsubmit">'.esc_html__('search','devita').'</button>';
				$form .= '<input type="hidden" name="post_type" value="product" />';
			$form .= '</div>';
		$form .= '</form>';
		  
		return $form;
	}
	// Replaces the excerpt "more" text by a link
	function devita_new_excerpt_more($more) {
		return '';
	}
	//Change excerpt length
	function devita_change_excerpt_length( $length ) {
		$devita_opt = get_option( 'devita_opt' );
		
		if(isset($devita_opt['excerpt_length'])){
			return $devita_opt['excerpt_length'];
		}
		
		return 22;
	}
	//Add 'first, last' class to menu
	function devita_first_and_last_menu_class($items) {
		$items[1]->classes[] = 'first';
		$items[count($items)]->classes[] = 'last';
		return $items;
	}
	/**
	 * Filter the page menu arguments.
	 *
	 * Makes our wp_nav_menu() fallback -- wp_page_menu() -- show a home link.
	 *
	 * @since Devita 1.0
	 */
	function devita_page_menu_args( $args ) {
		if ( ! isset( $args['show_home'] ) )
			$args['show_home'] = true;
		return $args;
	}
	//Add first, last class to widgets
	function devita_widget_first_last_class($params) {
		global $my_widget_num;
		
		$class = '';
		
		$this_id = $params[0]['id']; // Get the id for the current sidebar we're processing
		$arr_registered_widgets = wp_get_sidebars_widgets(); // Get an array of ALL registered widgets	

		if(!$my_widget_num) {// If the counter array doesn't exist, create it
			$my_widget_num = array();
		}

		if(!isset($arr_registered_widgets[$this_id]) || !is_array($arr_registered_widgets[$this_id])) { // Check if the current sidebar has no widgets
			return $params; // No widgets in this sidebar... bail early.
		}

		if(isset($my_widget_num[$this_id])) { // See if the counter array has an entry for this sidebar
			$my_widget_num[$this_id] ++;
		} else { // If not, create it starting with 1
			$my_widget_num[$this_id] = 1;
		}

		if($my_widget_num[$this_id] == 1) { // If this is the first widget
			$class .= ' widget-first ';
		} elseif($my_widget_num[$this_id] == count($arr_registered_widgets[$this_id])) { // If this is the last widget
			$class .= ' widget-last ';
		}
		
		$params[0]['before_widget'] = str_replace('first_last', ' '.$class.' ', $params[0]['before_widget']);
		
		return $params;
	}
	//Change mega menu widget from div to li tag
	function devita_mega_menu_widget_change($params) {
		
		$sidebar_id = $params[0]['id'];
		
		$pos = strpos($sidebar_id, '_menu_widgets_area_');
		
		if ( !$pos == false ) {
			$params[0]['before_widget'] = '<li class="widget_mega_menu">'.$params[0]['before_widget'];
			$params[0]['after_widget'] = $params[0]['after_widget'].'</li>';
		}
		
		return $params;
	}
	// Push sidebar widget content into a div
	function devita_put_widget_content( $params ) {
		global $wp_registered_widgets;

		if( $params[0]['id']=='sidebar-category' ){
			$settings_getter = $wp_registered_widgets[ $params[0]['widget_id'] ]['callback'][0];
			$settings = $settings_getter->get_settings();
			$settings = $settings[ $params[1]['number'] ];
			
			if($params[0]['widget_name']=="Text" && isset($settings['title']) && $settings['text']=="") { // if text widget and no content => don't push content
				return $params;
			}
			if( isset($settings['title']) && $settings['title']!='' ){
				$params[0][ 'after_title' ] .= '<div class="widget_content">';
				$params[0][ 'after_widget' ] = '</div>'.$params[0][ 'after_widget' ];
			} else {
				$params[0][ 'before_widget' ] .= '<div class="widget_content">';
				$params[0][ 'after_widget' ] = '</div>'.$params[0][ 'after_widget' ];
			}
		}
		
		return $params;
	}
	
	/**
	* Action hooks
	* ----------------
	*/
	/**
	 * devita setup.
	 *
	 * Sets up theme defaults and registers the various WordPress features that
	 * devita supports.
	 *
	 * @uses load_theme_textdomain() For translation/localization support.
	 * @uses add_editor_style() To add a Visual Editor stylesheet.
	 * @uses add_theme_support() To add support for post thumbnails, automatic feed links,
	 * 	custom background, and post formats.
	 * @uses register_nav_menu() To add support for navigation menus.
	 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
	 *
	 * @since Devita 1.0
	 */
	function devita_setup() {
		/*
		 * Makes devita available for translation.
		 *
		 * Translations can be added to the /languages/ directory.
		 * If you're building a theme based on devita, use a find and replace
		 * to change 'devita' to the name of your theme in all the template files.
		 */
		$devita_opt = get_option( 'devita_opt' );

		load_theme_textdomain( 'devita', get_template_directory() . '/languages' );

		// This theme styles the visual editor with editor-style.css to match the theme style.
		add_editor_style();

		// Adds RSS feed links to <head> for posts and comments.
		add_theme_support( 'automatic-feed-links' );

		// This theme supports a variety of post formats.
		add_theme_support( 'post-formats', array( 'image', 'gallery', 'video', 'audio' ) );

		// Register menus  
		register_nav_menu( 'primary', esc_html__( 'Primary Menu', 'devita' ) );
		register_nav_menu( 'mobilemenu', esc_html__( 'Mobile Menu', 'devita' ) );
		register_nav_menu( 'topmenu', esc_html__( 'Top Menu', 'devita' ) ); 
		register_nav_menu( 'categories', esc_html__( 'Categories Menu', 'devita' ) );  

		/*
		 * This theme supports custom background color and image,
		 * and here we also set up the default background color.
		 */
		add_theme_support( 'custom-background', array(
			'default-color' => 'e6e6e6',
		) );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );
		
		// This theme uses a custom image size for featured images, displayed on "standard" posts. 
		$width_image = "";
		if(isset($devita_opt['width_thumb']) && ($devita_opt['width_thumb'] != "" )) {
			$width_image = $devita_opt['width_thumb']; 
		} else {
			$width_image = 370;
		}
		$height_image ="";
		if(isset($devita_opt['height_thumb']) && ($devita_opt['height_thumb'] != "" )) {
			$height_image = $devita_opt['height_thumb'];
		} else {
			$width_image = 270;
		}
		$width_cate_image ="";
		if(isset($devita_opt['width_cate_thumb']) && ($devita_opt['width_cate_thumb'] != "" )) {
			$width_cate_image = $devita_opt['width_cate_thumb'];
		} else {
			$width_image = 1170;
		}
		$height_cate_image ="";
		if(isset($devita_opt['height_cate_thumb']) && ($devita_opt['height_cate_thumb'] != "" )) {
			$height_cate_image = $devita_opt['height_cate_thumb'];
		}  else {
			$width_image = 700;
		}
		
		add_theme_support( 'post-thumbnails' );

		set_post_thumbnail_size( 1170, 9999 ); // Unlimited height, soft crop
		add_image_size( 'devita-category-thumb', $width_cate_image, $height_cate_image, true ); // (cropped)
		add_image_size( 'devita-post-thumb', $width_image, $height_image, true ); // (cropped) 
	}
	//Override woocommerce widgets
	function devita_override_woocommerce_widgets() {
		//Show mini cart on all pages
		if ( class_exists( 'WC_Widget_Cart' ) ) {
			unregister_widget( 'WC_Widget_Cart' ); 
			include_once( get_template_directory().'/woocommerce/class-wc-widget-cart.php' );
			register_widget( 'Custom_WC_Widget_Cart' );
		}
	}
	// Add image to category description
	function devita_woocommerce_category_image() {
		if ( is_product_category() ){
			global $wp_query;
			
			$cat = $wp_query->get_queried_object();
			$thumbnail_id = get_woocommerce_term_meta( $cat->term_id, 'thumbnail_id', true );
			$image = wp_get_attachment_url( $thumbnail_id );
			
			if ( $image ) {
				echo '<p class="category-image-desc"><img src="' . esc_url($image) . '" alt="1" /></p>';
			}
		}
	}
	//Display social sharing on product page
	function devita_woocommerce_social_share(){
		$devita_opt = get_option( 'devita_opt' );
	?>
		<div class="share_buttons">
			<?php if ($devita_opt['share_code']!='') {
				echo wp_kses($devita_opt['share_code'], array(
					'div' => array(
						'class' => array()
					),
					'span' => array(
						'class' => array(),
						'displayText' => array()
					),
				));
			} ?>
		</div>
	<?php
	}
 

	/**
	 * Enqueue scripts and styles for front-end.
	 *
	 * @since Devita 1.0
	 */
	function devita_scripts_styles() {
		global $wp_styles, $wp_scripts;

		$devita_opt = get_option( 'devita_opt' );
		
		/*
		 * Adds JavaScript to pages with the comment form to support
		 * sites with threaded comments (when in use).
		*/
		
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
			wp_enqueue_script( 'comment-reply' ); 
 
		// Load bootstrap css
		wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/css/bootstrap.min.css', array(), '4.1.0' );
		// Add Bootstrap JavaScript
		wp_enqueue_script( 'bootstrap', get_template_directory_uri() . '/js/bootstrap.min.js', array('jquery'), '4.1.0', true ); 
 

		// Add owl-carousel file
		wp_enqueue_script( 'owl-carousel', get_template_directory_uri() . '/js/owl.carousel.js', array('jquery'), '2.3.4', true ); 
		wp_enqueue_script( 'owl-carousel-2', get_template_directory_uri() . '/js/owl.carousel.min.js', array('jquery'), '2.3.4', true );
		
		// Add Fancybox
		wp_enqueue_script( 'fancybox', get_template_directory_uri() . '/js/fancybox/jquery.fancybox.pack.js', array('jquery'), '2.1.5', true );
		wp_enqueue_script( 'fancybox-buttons', get_template_directory_uri() . '/js/fancybox/helpers/jquery.fancybox-buttons.js', array('jquery'), '1.0.5', true );
		wp_enqueue_script( 'fancybox-media', get_template_directory_uri() . '/js/fancybox/helpers/jquery.fancybox-media.js', array('jquery'), '1.0.6', true );
		wp_enqueue_script( 'fancybox-thumbs', get_template_directory_uri() . '/js/fancybox/helpers/jquery.fancybox-thumbs.js', array('jquery'), '1.0.7', true );

	 
		//Superfish
		wp_enqueue_script( 'superfish', get_template_directory_uri() . '/js/superfish/superfish.min.js', array('jquery'), '1.3.15', true );
		
		//Add Shuffle js
		wp_enqueue_script( 'modernizr', get_template_directory_uri() . '/js/modernizr.custom.min.js', array('jquery'), '2.6.2', true );
		wp_enqueue_script( 'shuffle', get_template_directory_uri() . '/js/jquery.shuffle.min.js', array('jquery'), '3.0.0', true );

		//Add mousewheel
		wp_enqueue_script( 'mousewheel', get_template_directory_uri() . '/js/jquery.mousewheel.min.js', array('jquery'), '3.1.12', true );
		
		// Add jQuery countdown file
		wp_enqueue_script( 'countdown', get_template_directory_uri() . '/js/jquery.countdown.min.js', array('jquery'), '2.0.4', true );
		
		// Add jQuery counter files 
		wp_enqueue_script( 'counterup', get_template_directory_uri() . '/js/jquery.counterup.min.js', array('jquery'), '1.0', true );   
		// Add variables.js file
		wp_enqueue_script( 'variables', get_template_directory_uri() . '/js/variables.js', array('jquery'), '20140826', true );
		
		// Add devita-theme.js file
		wp_enqueue_script( 'devita-theme', get_template_directory_uri() . '/js/devita-theme.js', array('jquery'), '20140826', true );


		$font_url = $this->devita_get_font_url();
		if ( ! empty( $font_url ) )
			wp_enqueue_style( 'devita-fonts', esc_url_raw( $font_url ), array(), null );
		
		// Loads our main stylesheet.
		wp_enqueue_style( 'devita-style', get_stylesheet_uri() ); 
		
		// Mega Main Menu
		wp_enqueue_style( 'megamenu-style', get_template_directory_uri() . '/css/megamenu_style.css', array(), '2.0.4' );
	
		// Load fontawesome css
		wp_enqueue_style( 'fontawesome', get_template_directory_uri() . '/css/font-awesome.min.css', array(), '4.7.0' );

		// Load animate css
		wp_enqueue_style( 'animate', get_template_directory_uri() . '/css/animate.css', array());
 
		// Load Plaza-font css
		wp_enqueue_style( 'Plaza-font', get_template_directory_uri() . '/css/plaza-font.css', array()); 

		// Load owl-carousel css
		wp_enqueue_style( 'owl-carousel', get_template_directory_uri() . '/css/owl.carousel.css', array(), '2.3.4');   
		
		// Add Fancybox Css
		wp_enqueue_style( 'fancybox', get_template_directory_uri() . '/js/fancybox/jquery.fancybox.css', array(), '2.1.5' );
		wp_enqueue_style( 'fancybox-buttons', get_template_directory_uri() . '/js/fancybox/helpers/jquery.fancybox-buttons.css', array(), '1.0.5' );
		wp_enqueue_style( 'fancybox-thumbs', get_template_directory_uri() . '/js/fancybox/helpers/jquery.fancybox-thumbs.css', array(), '1.0.7' );   
		// Compile Less to CSS
		$previewpreset = (isset($_REQUEST['preset']) ? $_REQUEST['preset'] : null);
			//get preset from url (only for demo/preview)
		if($previewpreset){
			$_SESSION["preset"] = $previewpreset;
		}
		$presetopt = 1;
		if(!isset($_SESSION["preset"])){
			$_SESSION["preset"] = 1;
		}
		if($_SESSION["preset"] != 1) {
			$presetopt = $_SESSION["preset"];
		} else { /* if no preset varialbe found in url, use from theme options */
			if(isset($devita_opt['preset_option'])){
				$presetopt = $devita_opt['preset_option'];
			}
		}
		if(!isset($presetopt)) $presetopt = 1; /* in case first time install theme, no options found */
		
		if(isset($devita_opt['enable_less'])){
			if($devita_opt['enable_less']){
				$themevariables = array(
					'body_font'=> $devita_opt['bodyfont']['font-family'],
					'text_color'=> $devita_opt['bodyfont']['color'],
					'text_selected_bg' => $devita_opt['text_selected_bg'],
					'text_selected_color' => $devita_opt['text_selected_color'],
					'text_size'=> $devita_opt['bodyfont']['font-size'],
					'border_color'=> $devita_opt['border_color']['border-color'],
					'background_opt'=> $devita_opt['background_opt']['background-color'],
					
					'heading_font'=> $devita_opt['headingfont']['font-family'],
					'heading_color'=> $devita_opt['headingfont']['color'],
					'heading_font_weight'=> $devita_opt['headingfont']['font-weight'],
					
					'menu_font'=> $devita_opt['menufont']['font-family'],
					'menu_color'=> $devita_opt['menufont']['color'],
					'menu_font_size'=> $devita_opt['menufont']['font-size'],
					'menu_font_weight'=> $devita_opt['menufont']['font-weight'],
					'sub_menu_bg' => $devita_opt['sub_menu_bg'],
					'sub_menu_color' => $devita_opt['sub_menu_color'],
					'vsub_menu_bg' => $devita_opt['vsub_menu_bg'],
					
					'link_color' => $devita_opt['link_color']['regular'],
					'link_hover_color' => $devita_opt['link_color']['hover'],
					'link_active_color' => $devita_opt['link_color']['active'],
					
					'primary_color' => $devita_opt['primary_color'],
					
					'sale_color' => $devita_opt['sale_color'],
					'saletext_color' => $devita_opt['saletext_color'],
					'rate_color' => $devita_opt['rate_color'],

					'topbar_color' => $devita_opt['topbar_color'],
					'topbar_link_color' => $devita_opt['topbar_link_color']['regular'],
					'topbar_link_hover_color' => $devita_opt['topbar_link_color']['hover'],
					'topbar_link_active_color' => $devita_opt['topbar_link_color']['active'],

					'header_color' => $devita_opt['header_color'],
					'header_link_color' => $devita_opt['header_link_color']['regular'],
					'header_link_hover_color' => $devita_opt['header_link_color']['hover'],
					'header_link_active_color' => $devita_opt['header_link_color']['active'], 
 
					'price_font'=> $devita_opt['pricefont']['font-family'],
					'price_color'=> $devita_opt['pricefont']['color'], 
					'price_size'=> $devita_opt['pricefont']['font-size'],
					'price_font_weight'=> $devita_opt['pricefont']['font-weight'],

					'footer_color' => $devita_opt['footer_color'],
					'footer_link_color' => $devita_opt['footer_link_color']['regular'],
					'footer_link_hover_color' => $devita_opt['footer_link_color']['hover'],
					'footer_link_active_color' => $devita_opt['footer_link_color']['active'],
				);
				
				if(isset($devita_opt['header_sticky_bg']['rgba']) && $devita_opt['header_sticky_bg']['rgba']!="") {
					$themevariables['header_sticky_bg'] = $devita_opt['header_sticky_bg']['rgba'];
				} else {
					$themevariables['header_sticky_bg'] = 'rgba(68,68,68,0.95)';
				}
				if(isset($devita_opt['header_bg']['background-color']) && $devita_opt['header_bg']['background-color']!="") {
					$themevariables['header_bg'] = $devita_opt['header_bg']['background-color'];
				} else {
					$themevariables['header_bg'] = '#fff';
				} 
				if(isset($devita_opt['header_border_color']['rgba']) && $devita_opt['header_border_color']['rgba']!="") {
					$themevariables['header_border_color'] = $devita_opt['header_border_color']['rgba'];
				} else {
					$themevariables['header_border_color'] = 'rgba(255,255,255,0.1)';
				}
				if(isset($devita_opt['footer_border_color']['rgba']) && $devita_opt['footer_border_color']['rgba']!="") {
					$themevariables['footer_border_color'] = $devita_opt['footer_border_color']['rgba'];
				} else {
					$themevariables['footer_border_color'] = 'rgba(255,255,255,0.1)';
				}
				if(isset($devita_opt['page_content_background']['background-color']) && $devita_opt['page_content_background']['background-color']!="") {
					$themevariables['page_content_background'] = $devita_opt['page_content_background']['background-color'];
				} else {
					$themevariables['page_content_background'] = '#fff';
				}
				if(isset($devita_opt['footer_bg']['background-color']) && $devita_opt['footer_bg']['background-color']!="") {
					$themevariables['footer_bg'] = $devita_opt['footer_bg']['background-color'];
				} else {
					$themevariables['footer_bg'] = '#282727';
				}
				switch ($presetopt) { 
					case 2:  
						$themevariables['header_bg'] = '#ffffff';
						$themevariables['header_color'] = '#242424';  
						$themevariables['header_link_color'] = '#242424';  
						$themevariables['topbar_color'] = '#242424';  
						$themevariables['topbar_link_color'] = '#242424'; 
						$themevariables['footer_bg'] = '#ffffff';  
						$themevariables['footer_color'] = '#757575';
						$themevariables['footer_link_color'] = '#757575';
						$themevariables['footer_border_color'] ="#ebebeb";
					break; 
					case 3:  
						$themevariables['header_bg'] = '#ffffff';
						$themevariables['header_color'] = '#242424';  
						$themevariables['header_link_color'] = '#242424';  
						$themevariables['topbar_color'] = '#242424';  
						$themevariables['topbar_link_color'] = '#242424';  
					break; 
					case 4:  
						$themevariables['header_bg'] = '#ffffff';
						$themevariables['header_color'] = '#242424';  
						$themevariables['header_link_color'] = '#242424';  
						$themevariables['topbar_color'] = '#242424';  
						$themevariables['topbar_link_color'] = '#242424';  
						$themevariables['header_border_color'] = '#ebebeb'; 
						$themevariables['footer_bg'] = '#f6f7f7';  
						$themevariables['footer_color'] = '#757575';
						$themevariables['footer_link_color'] = '#757575';
						$themevariables['footer_border_color'] ="#ebebeb";
					break;
				}

				if(function_exists('compileLessFile')){ 
					compileLessFile('theme.less', 'theme'.$presetopt.'.css', $themevariables);
				}
			}
		}
		
		// Load main theme css style files
		wp_enqueue_style( 'devita-theme-style', get_template_directory_uri() . '/css/theme'.$presetopt.'.css', array('bootstrap'), '1.0.0' ); 
		 wp_enqueue_style( 'devita-custom', get_template_directory_uri() . '/css/opt_css.css', array('devita-theme-style'), '1.0.0' );
		
		if(function_exists('WP_Filesystem')){
			if ( ! WP_Filesystem() ) {
				$url = wp_nonce_url();
				request_filesystem_credentials($url, '', true, false, null);
			}
			
			global $wp_filesystem;
			//add custom css, sharing code to header
			if($wp_filesystem->exists(get_template_directory(). '/css/opt_css.css')){
				$customcss = $wp_filesystem->get_contents(get_template_directory(). '/css/opt_css.css');
				
				if(isset($devita_opt['custom_css']) && $customcss!=$devita_opt['custom_css']){ //if new update, write file content
					$wp_filesystem->put_contents(
						get_template_directory(). '/css/opt_css.css',
						$devita_opt['custom_css'],
						FS_CHMOD_FILE // predefined mode settings for WP files
					);
				}
			} else {
				$wp_filesystem->put_contents(
					get_template_directory(). '/css/opt_css.css',
					$devita_opt['custom_css'],
					FS_CHMOD_FILE // predefined mode settings for WP files
				);
			}
		}
		
		//add javascript variables
		ob_start(); ?>  
		var devita_testipause = <?php if(isset($devita_opt['testipause'])) { echo esc_js($devita_opt['testipause']); } else { echo '3000'; } ?>,
			devita_testianimate = <?php if(isset($devita_opt['testianimate'])) { echo esc_js($devita_opt['testianimate']); } else { echo '700'; } ?>;
		var devita_testiscroll = false;
			<?php if(isset($devita_opt['testiscroll'])){ ?>
				devita_testiscroll = <?php echo esc_js($devita_opt['testiscroll'])==1 ? 'true': 'false'; ?>;
			<?php } ?>
		var devita_catenumber = <?php if(isset($devita_opt['catenumber'])) { echo esc_js($devita_opt['catenumber']); } else { echo '6'; } ?>,
			devita_catescrollnumber = <?php if(isset($devita_opt['catescrollnumber'])) { echo esc_js($devita_opt['catescrollnumber']); } else { echo '2';} ?>,
			devita_catepause = <?php if(isset($devita_opt['catepause'])) { echo esc_js($devita_opt['catepause']); } else { echo '3000'; } ?>,
			devita_cateanimate = <?php if(isset($devita_opt['cateanimate'])) { echo esc_js($devita_opt['cateanimate']); } else { echo '700';} ?>;
		var devita_catescroll = false;
			<?php if(isset($devita_opt['catescroll'])){ ?>
				devita_catescroll = <?php echo esc_js($devita_opt['catescroll'])==1 ? 'true': 'false'; ?>;
			<?php } ?>
		var devita_menu_number = <?php if(isset($devita_opt['categories_menu_items'])) { echo esc_js((int)$devita_opt['categories_menu_items']+1); } else { echo '9';} ?>;
		var devita_sticky_header = false;
			<?php if(isset($devita_opt['sticky_header'])){ ?>
				devita_sticky_header = <?php echo esc_js($devita_opt['sticky_header'])==1 ? 'true': 'false'; ?>;
			<?php } ?>

		jQuery(document).ready(function(){
			jQuery("#ws").focus(function(){
				if(jQuery(this).val()=="<?php echo esc_html__( 'Search product...', 'devita' )?>"){
					jQuery(this).val("");
				}
			});
			jQuery("#ws").focusout(function(){
				if(jQuery(this).val()==""){
					jQuery(this).val("<?php echo esc_html__( 'Search product...', 'devita' )?>");
				}
			});
			jQuery("#wsearchsubmit").on('click',function(){
				if(jQuery("#ws").val()=="<?php echo esc_html__( 'Search product...', 'devita' )?>" || jQuery("#ws").val()==""){
					jQuery("#ws").focus();
					return false;
				}
			});
			jQuery("#search_input").focus(function(){
				if(jQuery(this).val()=="<?php echo esc_html__( 'Search...', 'devita' )?>"){
					jQuery(this).val("");
				}
			});
			jQuery("#search_input").focusout(function(){
				if(jQuery(this).val()==""){
					jQuery(this).val("<?php echo esc_html__( 'Search...', 'devita' )?>");
				}
			});
			jQuery("#blogsearchsubmit").on('click',function(){
				if(jQuery("#search_input").val()=="<?php echo esc_html__( 'Search...', 'devita' )?>" || jQuery("#search_input").val()==""){
					jQuery("#search_input").focus();
					return false;
				}
			});
		});
		<?php
		$jsvars = ob_get_contents();
		ob_end_clean();
		
		if(function_exists('WP_Filesystem')){
			if($wp_filesystem->exists(get_template_directory(). '/js/variables.js')){
				$jsvariables = $wp_filesystem->get_contents(get_template_directory(). '/js/variables.js');
				
				if($jsvars!=$jsvariables){ //if new update, write file content
					$wp_filesystem->put_contents(
						get_template_directory(). '/js/variables.js',
						$jsvars,
						FS_CHMOD_FILE // predefined mode settings for WP files
					);
				}
			} else {
				$wp_filesystem->put_contents(
					get_template_directory(). '/js/variables.js',
					$jsvars,
					FS_CHMOD_FILE // predefined mode settings for WP files
				);
			}
		}
		//add css for footer, header templates
		$jscomposer_templates_args = array(
			'orderby'          => 'title',
			'order'            => 'ASC',
			'post_type'        => 'templatera',
			'post_status'      => 'publish',
			'posts_per_page'   => 30,
		);
		$jscomposer_templates = get_posts( $jscomposer_templates_args );

		if(count($jscomposer_templates) > 0) {
			foreach($jscomposer_templates as $jscomposer_template){
				if($jscomposer_template->post_title == $devita_opt['header_layout'] || $jscomposer_template->post_title == $devita_opt['footer_layout']){
					$jscomposer_template_css = get_post_meta ( $jscomposer_template->ID, '_wpb_shortcodes_custom_css', false );
					if(isset($jscomposer_template_css[0])) {
						wp_add_inline_style( 'devita-custom', $jscomposer_template_css[0] );
					} 
				}
			}
		}
		
		//page width
		wp_add_inline_style( 'devita-custom', '.wrapper.box-layout, .wrapper.box-layout .container, .wrapper.box-layout .row-container {max-width: '.$devita_opt['box_layout_width'].'px;}' );
	}
	 
	//add sharing code to header
	function devita_custom_code_header() {
		global $devita_opt;

		if ( isset($devita_opt['share_head_code']) && $devita_opt['share_head_code']!='') {
			echo wp_kses($devita_opt['share_head_code'], array(
				'script' => array(
					'type' => array(),
					'src' => array(),
					'async' => array()
				),
			));
		}
	}
	/**
	 * Register sidebars.
	 *
	 * Registers our main widget area and the front page widget areas.
	 *
	 * @since Devita 1.0
	 */
	function devita_widgets_init() {
		$devita_opt = get_option( 'devita_opt' );
		
		register_sidebar( array(
			'name' => esc_html__( 'Blog Sidebar', 'devita' ),
			'id' => 'sidebar-1',
			'description' => esc_html__( 'Sidebar on blog page', 'devita' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget' => '</aside>',
			'before_title' => '<h3 class="widget-title"><span>',
			'after_title' => '</span></h3>',
		) );
		
		register_sidebar( array(
			'name' => esc_html__( 'Shop Sidebar', 'devita' ),
			'id' => 'sidebar-shop',
			'description' => esc_html__( 'Sidebar on shop page (only sidebar shop layout)', 'devita' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget' => '</aside>',
			'before_title' => '<h3 class="widget-title"><span>',
			'after_title' => '</span></h3>',
		) );

		register_sidebar( array(
			'name' => esc_html__( 'Pages Sidebar', 'devita' ),
			'id' => 'sidebar-page',
			'description' => esc_html__( 'Sidebar on content pages', 'devita' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget' => '</aside>',
			'before_title' => '<h3 class="widget-title"><span>',
			'after_title' => '</span></h3>',
		) );
		
		if(isset($devita_opt['custom-sidebars']) && $devita_opt['custom-sidebars']!=""){
			foreach($devita_opt['custom-sidebars'] as $sidebar){
				$sidebar_id = str_replace(' ', '-', strtolower($sidebar));
				
				if($sidebar_id!='') {
					register_sidebar( array(
						'name' => $sidebar,
						'id' => $sidebar_id,
						'description' => $sidebar,
						'before_widget' => '<aside id="%1$s" class="widget %2$s">',
						'after_widget' => '</aside>',
						'before_title' => '<h3 class="widget-title"><span>',
						'after_title' => '</span></h3>',
					) );
				}
			}
		}
	} 
	//Change comment form
	function devita_before_comment_fields() {
		echo '<div class="comment-input">';
	}
	function devita_after_comment_fields() {
		echo '</div>';
	}
	/**
	 * Register postMessage support.
	 *
	 * Add postMessage support for site title and description for the Customizer.
	 *
	 * @since Devita 1.0
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer object.
	 */
	function devita_customize_register( $wp_customize ) {
		$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
		$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
		$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
	}
	/**
	 * Enqueue Javascript postMessage handlers for the Customizer.
	 *
	 * Binds JS handlers to make the Customizer preview reload changes asynchronously.
	 *
	 * @since Devita 1.0
	 */
	function devita_customize_preview_js() {
		wp_enqueue_script( 'devita-customizer', get_template_directory_uri() . '/js/theme-customizer.js', array( 'customize-preview' ), '20130301', true );
	}
	 
	function devita_admin_style() {
	  wp_enqueue_style('admin-styles', get_template_directory_uri().'/css/admin.css');
	}
	/**
	* Utility methods
	* ---------------
	*/
	
	//Add breadcrumbs
	static function devita_breadcrumb() {
		global $post;

		$devita_opt = get_option( 'devita_opt' );
		
		$brseparator = '<span class="separator">/</span>';
		if (!is_home()) {
			echo '<div class="breadcrumbs">';
			
			echo '<a href="';
			echo esc_url( home_url( '/' ));
			echo '">';
			echo esc_html__('Home', 'devita');
			echo '</a>'.$brseparator;
			if (is_category() && is_single()) {
				the_category($brseparator);
				echo ''.$brseparator;
				the_title();  
			} elseif (is_single()) {
				if (is_single()) { 
					the_title();
				}  
			} elseif (is_category()) {
				the_category($brseparator); 
			} elseif (is_page()) {
				if($post->post_parent){
					$anc = get_post_ancestors( $post->ID );
					$title = get_the_title();
					foreach ( $anc as $ancestor ) {
						$output = '<a href="'.get_permalink($ancestor).'" title="'.get_the_title($ancestor).'">'.get_the_title($ancestor).'</a>'.$brseparator;
					}
					echo wp_kses($output, array(
							'a'=>array(
								'href' => array(),
								'title' => array()
							),
							'span'=>array(
								'class'=>array()
							)
						)
					);
					echo '<span title="'.$title.'"> '.$title.'</span>';
				} else {
					echo '<span> '.get_the_title().'</span>';
				} 
			}

			elseif (is_tag()) {single_tag_title();}
			elseif (is_day()) {printf( esc_html__( 'Archive for: %s', 'devita' ), '<span>' . get_the_date() . '</span>' );}
			elseif (is_month()) {printf( esc_html__( 'Archive for: %s', 'devita' ), '<span>' . get_the_date( _x( 'F Y', 'monthly archives date format', 'devita' ) ) . '</span>' );}
			elseif (is_year()) {printf( esc_html__( 'Archive for: %s', 'devita' ), '<span>' . get_the_date( _x( 'Y', 'yearly archives date format', 'devita' ) ) . '</span>' );}
			elseif (is_author()) {echo "<span>".esc_html__('Archive for','devita'); echo'</span>';}
			elseif (isset($_GET['paged']) && !empty($_GET['paged'])) {echo "<span>".esc_html__('Blog Archives','devita'); echo'</span>';}
			elseif (is_search()) {echo "<span>".esc_html__('Search Results','devita'); echo'</span>';}
			
			echo '</div>';
		} else {
			echo '<div class="breadcrumbs">';
			
			echo '<a href="';
			echo esc_url( home_url( '/' ) );
			echo '">';
			echo esc_html__('Home', 'devita');
			echo '</a>'.$brseparator;
			
			if(isset($devita_opt['blog_header_text']) && $devita_opt['blog_header_text']!=""){
				echo esc_html($devita_opt['blog_header_text']);
			} else {
				echo esc_html__('Blog', 'devita');
			}
			
			echo '</div>';
		}
	}
	static function devita_limitStringByWord ($string, $maxlength, $suffix = '') {

		if(function_exists( 'mb_strlen' )) {
			// use multibyte functions by Iysov
			if(mb_strlen( $string )<=$maxlength) return $string;
			$string = mb_substr( $string, 0, $maxlength );
			$index = mb_strrpos( $string, ' ' );
			if($index === FALSE) {
				return $string;
			} else {
				return mb_substr( $string, 0, $index ).$suffix;
			}
		} else { // original code here
			if(strlen( $string )<=$maxlength) return $string;
			$string = substr( $string, 0, $maxlength );
			$index = strrpos( $string, ' ' );
			if($index === FALSE) {
				return $string;
			} else {
				return substr( $string, 0, $index ).$suffix;
			}
		}
	}
	static function devita_excerpt_by_id($post, $length = 10, $tags = '<a><em><strong>') {
 
		if(is_int($post)) {
			// get the post object of the passed ID
			$post = get_post($post);
		} elseif(!is_object($post)) {
			return false;
		}
	 
		if(has_excerpt($post->ID)) {
			$the_excerpt = $post->post_excerpt;
			return apply_filters('the_content', $the_excerpt);
		} else {
			$the_excerpt = $post->post_content;
		}
	 
		$the_excerpt = strip_shortcodes(strip_tags($the_excerpt), $tags);
		$the_excerpt = preg_split('/\b/', $the_excerpt, $length * 2+1);
		$excerpt_waste = array_pop($the_excerpt);
		$the_excerpt = implode($the_excerpt);
	 
		return apply_filters('the_content', $the_excerpt);
	}
	/**
	 * Return the Google font stylesheet URL if available.
	 *
	 * The use of Open Sans by default is localized. For languages that use
	 * characters not supported by the font, the font can be disabled.
	 *
	 * @since devita 1.2
	 *
	 * @return string Font stylesheet or empty string if disabled.
	 */
	function devita_get_font_url() {
		$fonts_url = '';
		 
		/* Translators: If there are characters in your language that are not
		* supported by Open Sans, translate this to 'off'. Do not translate
		* into your own language.
		*/
		$open_sans = _x( 'on', 'Open Sans font: on or off', 'devita' );
		 
		if ( 'off' !== $open_sans ) {
			$font_families = array();

			if ( 'off' !== $open_sans ) {
				$font_families[] = 'Rubik:300,400,500,700,900';
			}
			
			$query_args = array(
				'family' => urlencode( implode( '|', $font_families ) ),
				'subset' => urlencode( 'latin,latin-ext' ),
			);
			 
			$fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
		}
		 
		return esc_url_raw( $fonts_url );
	}
	/**
	 * Displays navigation to next/previous pages when applicable.
	 *
	 * @since Devita 1.0
	 */
	static function devita_content_nav( $html_id ) {
		global $wp_query;

		$html_id = esc_attr( $html_id );

		if ( $wp_query->max_num_pages > 1 ) : ?>
			<nav id="<?php echo esc_attr($html_id); ?>" class="navigation" role="navigation">
				<h3 class="assistive-text"><?php esc_html_e( 'Post navigation', 'devita' ); ?></h3>
				<div class="nav-previous"><?php next_posts_link( wp_kses(__( '<span class="meta-nav">&larr;</span> Older posts', 'devita' ),array('span'=>array('class'=>array())) )); ?></div>
				<div class="nav-next"><?php previous_posts_link( wp_kses(__( 'Newer posts <span class="meta-nav">&rarr;</span>', 'devita' ), array('span'=>array('class'=>array())) )); ?></div>
			</nav>
		<?php endif;
	}
	/* Pagination */
	static function devita_pagination() {
		global $wp_query;

		$big = 999999999; // need an unlikely integer
		if($wp_query->max_num_pages > 1) {
			echo '<div class="pagination-inner">';
		}
			echo paginate_links( array(
				'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
				'format' => '?paged=%#%',
				'current' => max( 1, get_query_var('paged') ),
				'total' => $wp_query->max_num_pages,
				'prev_text'    => esc_html__('Previous', 'devita'),
				'next_text'    =>esc_html__('Next', 'devita'),
			) );
		if($wp_query->max_num_pages > 1) {
			echo '</div>';
		}
	}
	/**
	 * Template for comments and pingbacks.
	 *
	 * To override this walker in a child theme without modifying the comments template
	 * simply create your own devita_comment(), and that function will be used instead.
	 *
	 * Used as a callback by wp_list_comments() for displaying the comments.
	 *
	 * @since Devita 1.0
	 */
	static function devita_comment( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment;
		switch ( $comment->comment_type ) :
			case 'pingback' :
			case 'trackback' :
			// Display trackbacks differently than normal comments.
		?>
		<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
			<p><?php esc_html_e( 'Pingback:', 'devita' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( esc_html__( '(Edit)', 'devita' ), '<span class="edit-link">', '</span>' ); ?></p>
		<?php
				break;
			default :
			// Proceed with normal comments.
			global $post;
		?>
		<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
			<article id="comment-<?php comment_ID(); ?>" class="comment">
				<div class="comment-avatar">
					<?php echo get_avatar( $comment, 50 ); ?>
				</div>
				<div class="comment-info">
					<header class="comment-meta comment-author vcard">
						<?php
							
							printf( '<cite><b class="fn">%1$s</b> %2$s</cite>',
								get_comment_author_link(),
								// If current post author is also comment author, make it known visually.
								( $comment->user_id === $post->post_author ) ? '<span>' . esc_html__( 'Post author', 'devita' ) . '</span>' : ''
							);
							printf( '<time datetime="%1$s">%2$s</time>',
								get_comment_time( 'c' ),
								/* translators: 1: date, 2: time */
								sprintf( esc_html__( '%1$s at %2$s', 'devita' ), get_comment_date(), get_comment_time() )
							);
						?>
						<div class="reply">
							<?php comment_reply_link( array_merge( $args, array( 'reply_text' => esc_html__( 'Reply', 'devita' ), 'after' => '', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
						</div><!-- .reply -->
					</header><!-- .comment-meta -->
					<?php if ( '0' == $comment->comment_approved ) : ?>
						<p class="comment-awaiting-moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', 'devita' ); ?></p>
					<?php endif; ?>

					<section class="comment-content comment">
						<?php comment_text(); ?>
						<?php edit_comment_link( esc_html__( 'Edit', 'devita' ), '<p class="edit-link">', '</p>' ); ?>
					</section><!-- .comment-content -->
				</div>
			</article><!-- #comment-## -->
		<?php
			break;
		endswitch; // end comment_type check
	}
	/**
	 * Set up post entry meta.
	 *
	 * Prints HTML with meta information for current post: categories, tags, permalink, author, and date.
	 *
	 * Create your own devita_entry_meta() to override in a child theme.
	 *
	 * @since Devita 1.0
	 */
	static function devita_entry_meta() {
		
		// Translators: used between list items, there is a space after the comma.
		$tag_list = get_the_tag_list( '', ', ' );

		$num_comments = (int)get_comments_number();
		$write_comments = '';
		if ( comments_open() ) {
			if ( $num_comments == 0 ) {
				$comments = esc_html__('0 comments', 'devita');
			} elseif ( $num_comments > 1 ) {
				$comments = $num_comments . esc_html__(' comments', 'devita');
			} else {
				$comments = esc_html__('1 comment', 'devita');
			}
			$write_comments = '<a href="' . get_comments_link() .'">'. $comments.'</a>';
		}

		$utility_text = null;
		if ( ( post_password_required() || !comments_open() ) && ($tag_list!=false && isset($tag_list) ) ) {
			$utility_text = esc_html__( 'Tags: %2$s', 'devita' );
		} elseif($tag_list!=false && isset($tag_list) && $num_comments !=0 ){
			$utility_text = esc_html__( '%1$s / Tags: %2$s', 'devita' );
		} elseif ( ($num_comments ==0 || !isset($num_comments) ) && $tag_list==true ) {
			$utility_text = esc_html__( 'Tags: %2$s', 'devita' );
		} else {
			$utility_text = esc_html__( '%1$s', 'devita' );
		}

		printf( $utility_text, $write_comments, $tag_list);
	}
	static function devita_entry_meta_small() {
		
		// Translators: used between list items, there is a space after the comma.
		$categories_list = get_the_category_list(', ');

		$author = sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>',
			esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
			esc_attr( sprintf( wp_kses(__( 'View all posts by %s', 'devita' ), array('a'=>array())), get_the_author() ) ),
			get_the_author()
		);
		
		$utility_text = esc_html__( 'Posted by %1$s  %2$s', 'devita' );

		printf( $utility_text, $author, $categories_list );
		
	}
	static function devita_entry_comments() {
		
		$date = sprintf( '<time class="entry-date" datetime="%3$s">%4$s</time>',
			esc_url( get_permalink() ),
			esc_attr( get_the_time() ),
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() )
		);

		$num_comments = (int)get_comments_number();
		$write_comments = '';
		if ( comments_open() ) {
			if ( $num_comments == 0 ) {
				$comments = wp_kses(__('<span>0</span> comments', 'devita'), array('span'=>array()));
			} elseif ( $num_comments > 1 ) {
				$comments = '<span>'.$num_comments .'</span>'. esc_html__(' comments', 'devita');
			} else {
				$comments = wp_kses(__('<span>1</span> comment', 'devita'), array('span'=>array()));
			}
			$write_comments = '<a href="' . get_comments_link() .'">'. $comments.'</a>';
		}
		
		$utility_text = esc_html__( '%1$s', 'devita' );
		
		printf( $utility_text, $write_comments );
	}
	/**
	* TGM-Plugin-Activation
	*/
	function devita_register_required_plugins() {

		$plugins = array(
			array(
				'name'               => esc_html__('Plazathemes Helper', 'devita'),
				'slug'               => 'roadthemes-helper',
				'source'             => get_template_directory() . '/plugins/roadthemes-helper.zip',
				'required'           => true,
				'version'            => '1.0.0',
				'force_activation'   => false,
				'force_deactivation' => false,
				'external_url'       => '',
			),
			array(
				'name'               => esc_html__('Mega Main Menu', 'devita'),
				'slug'               => 'mega_main_menu',
				'source'             => get_template_directory() . '/plugins/mega_main_menu.zip',
				'required'           => true,
				'external_url'       => '',
			),
			array(
				'name'               => esc_html__('Revolution Slider', 'devita'),
				'slug'               => 'revslider',
				'source'             => get_template_directory() . '/plugins/revslider.zip',
				'required'           => true,
				'external_url'       => '',
			),
			array(
				'name'               => 'Import Sample Data',
				'slug'               => 'road-importdata',
				'source'             => get_template_directory() . '/plugins/road-importdata.zip',
				'required'           => true,
				'external_url'       => '',
			),
			array(
				'name'               => esc_html__('Visual Composer', 'devita'),
				'slug'               => 'js_composer',
				'source'             => get_template_directory() . '/plugins/js_composer.zip',
				'required'           => true,
				'external_url'       => '',
			),
			array(
				'name'               => esc_html__('Templatera', 'devita'),
				'slug'               => 'templatera',
				'source'             => get_template_directory() . '/plugins/templatera.zip',
				'required'           => true,
				'external_url'       => '',
			),
			array(
				'name'               => esc_html__('Essential Grid', 'devita'),
				'slug'               => 'essential-grid',
				'source'             => get_template_directory() . '/plugins/essential-grid.zip',
				'required'           => true,
				'external_url'       => '',
			),
			 
			// Plugins from the WordPress Plugin Repository.
			array(
				'name'               => esc_html__('Redux Framework', 'devita'),
				'slug'               => 'redux-framework',
				'required'           => true,
				'force_activation'   => false,
				'force_deactivation' => false,
			),
			array(
				'name'      => esc_html__('Contact Form 7', 'devita'),
				'slug'      => 'contact-form-7',
				'required'  => true,
			),
			  
			array(
				'name'      => esc_html__('MailChimp for WordPress', 'devita'),
				'slug'      => 'mailchimp-for-wp',
				'required'  => true,
			),
			array(
				'name'      => esc_html__('Shortcodes Ultimate', 'devita'),
				'slug'      => 'shortcodes-ultimate',
				'required'  => true,
			),
			array(
				'name'      => esc_html__('Simple Local Avatars', 'devita'),
				'slug'      => 'simple-local-avatars',
				'required'  => false,
			),
			array(
				'name'      => esc_html__('Testimonials', 'devita'),
				'slug'      => 'testimonials-by-woothemes',
				'required'  => true,
			),
			array(
				'name'      => esc_html__('TinyMCE Advanced', 'devita'),
				'slug'      => 'tinymce-advanced',
				'required'  => false,
			),
			array(
				'name'      => esc_html__('Widget Importer & Exporter', 'devita'),
				'slug'      => 'widget-importer-exporter',
				'required'  => false,
			),
			array(
				'name'      => esc_html__('WooCommerce', 'devita'),
				'slug'      => 'woocommerce',
				'required'  => true,
			),
			array(
				'name'      => esc_html__('YITH WooCommerce Compare', 'devita'),
				'slug'      => 'yith-woocommerce-compare',
				'required'  => false,
			),
			array(
				'name'      => esc_html__('YITH WooCommerce Wishlist', 'devita'),
				'slug'      => 'yith-woocommerce-wishlist',
				'required'  => false,
			),
			array(
				'name'      => esc_html__('YITH WooCommerce Zoom Magnifier', 'devita'),
				'slug'      => 'yith-woocommerce-zoom-magnifier',
				'required'  => false,
			),
		);

		/**
		 * Array of configuration settings. Amend each line as needed.
		 * If you want the default strings to be available under your own theme domain,
		 * leave the strings uncommented.
		 * Some of the strings are added into a sprintf, so see the comments at the
		 * end of each line for what each argument will be.
		 */
		$config = array(
			'default_path' => '',                      // Default absolute path to pre-packaged plugins.
			'menu'         => 'tgmpa-install-plugins', // Menu slug.
			'has_notices'  => true,                    // Show admin notices or not.
			'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
			'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
			'is_automatic' => false,                   // Automatically activate plugins after installation or not.
			'message'      => '',                      // Message to output right before the plugins table.
			'strings'      => array(
				'page_title'                      => esc_html__( 'Install Required Plugins', 'devita' ),
				'menu_title'                      => esc_html__( 'Install Plugins', 'devita' ),
				'installing'                      => esc_html__( 'Installing Plugin: %s', 'devita' ), // %s = plugin name.
				'oops'                            => esc_html__( 'Something went wrong with the plugin API.', 'devita' ),
				'notice_can_install_required'     => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.', 'devita' ), // %1$s = plugin name(s).
				'notice_can_install_recommended'  => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.', 'devita' ), // %1$s = plugin name(s).
				'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.', 'devita' ), // %1$s = plugin name(s).
				'notice_can_activate_required'    => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.', 'devita' ), // %1$s = plugin name(s).
				'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.', 'devita' ), // %1$s = plugin name(s).
				'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.', 'devita' ), // %1$s = plugin name(s).
				'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.', 'devita' ), // %1$s = plugin name(s).
				'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.', 'devita' ), // %1$s = plugin name(s).
				'install_link'                    => _n_noop( 'Begin installing plugin', 'Begin installing plugins', 'devita' ),
				'activate_link'                   => _n_noop( 'Begin activating plugin', 'Begin activating plugins', 'devita' ),
				'return'                          => esc_html__( 'Return to Required Plugins Installer', 'devita' ),
				'plugin_activated'                => esc_html__( 'Plugin activated successfully.', 'devita' ),
				'complete'                        => esc_html__( 'All plugins installed and activated successfully. %s', 'devita' ), // %s = dashboard link.
				'nag_type'                        => 'updated' // Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
			)
		);

		tgmpa( $plugins, $config );

	}
}

// Instantiate theme
$Devita_Class = new Devita_Class();

//Fix duplicate id of mega menu
function devita_mega_menu_id_change($params) {
	ob_start('devita_mega_menu_id_change_call_back');
}
function devita_mega_menu_id_change_call_back($html){
	//$html = preg_replace('/id="primary"/', 'id="mega_main_menu_first"', $html, 1);
	//$html = preg_replace('/id="main_ul-primary"/', 'id="mega_main_menu_ul_first"', $html, 1);
	
	return $html;
}
function devita_total_product_count() {
    $args = array( 'post_type' => 'product', 'posts_per_page' => -1 );

    $products = new WP_Query( $args );

    return $products->found_posts;
}
add_action('wp_loaded', 'devita_mega_menu_id_change');

function devita_prefix_enqueue_script() {
  	wp_add_inline_script( 'devita-theme', 'var ajaxurl = "'.admin_url('admin-ajax.php').'";' ); 
}
add_action( 'wp_enqueue_scripts', 'devita_prefix_enqueue_script' ); 