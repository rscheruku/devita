<?php
$absolute_path = __FILE__;
$path_to_file = explode( 'wp-content', $absolute_path );
$path_to_wp = $path_to_file[0];	
require_once( $path_to_wp.'wp-content/plugins/woocommerce/woocommerce.php');	
require_once( $path_to_wp.'wp-content/plugins/woocommerce/includes/wc-formatting-functions.php');	
// require_once( $path_to_wp.'wp-content/plugins/woocommerce/includes/shortcodes/class-wc-shortcode-products.php');	
// require_once( $path_to_wp.'wp-content/plugins/js_composer/include/classes/vendors/plugins/class-vc-vendor-woocommerce.php');	

class RoadProducts {
	
	function __construct() {
		add_action( 'vc_before_init',  array($this,'road_vc_shortcodes'));
		add_action( 'wp_ajax_vc_get_autocomplete_suggestion',  array($this,'vc_get_autocomplete_suggestion'));
		add_shortcode( 'sale_products_r', __CLASS__ . '::sale_products_r' );
		add_shortcode( 'list_products_r', __CLASS__ . '::list_products_r' );
		add_shortcode( 'product_category_r', __CLASS__ . '::product_category_r' );
		add_shortcode( 'product_categories_r', __CLASS__ . '::product_categories_r' );
		add_shortcode( 'featured_products_r', __CLASS__ . '::featured_products_r' );
		add_shortcode( 'recent_products_r', __CLASS__ . '::recent_products_r' );
		add_shortcode( 'bestselling_products_r', __CLASS__ . '::bestselling_products_r' );

		//wp_enqueue_script('jquery');
		
	}
	
 
	public static function sale_products_r( $atts ) {
		$style= '';
		if ($atts["enable_slider"] == true) {
			$style = 'slide';
		} 
		wp_enqueue_script('product-options', plugins_url('roadthemes-helper').'/js/product_options.js');
		wp_localize_script('product-options', 'product_options_sale', array(
				'atts' => $atts
			)
		);		
		
		$atts = array_merge( array(
			'limit'        => '4',
			'columns'      => '4',
			'orderby'      => 'title',
			'order'        => 'ASC',
			'category'     => '',
			'cat_operator' => 'IN',
		), (array) $atts );
		
		$shortcode = new WC_Shortcode_Products( $atts, 'sale_products' );

		return '<div class="sale_product '.$style.'">'.$shortcode->get_content().'</div>';
	}
	
		/**
	 * List best selling products on sale.
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public static function bestselling_products_r( $atts ) {
		$style= '';
		if ($atts["enable_slider"] == true) {
			$style = 'slide';
		} 
		wp_enqueue_script('product-options', plugins_url('roadthemes-helper').'/js/product_options.js');
		wp_localize_script('product-options', 'product_options_selling', array(
				'atts' => $atts,
				'ajax_url' =>  admin_url( 'admin-ajax.php' )
			)
		);
		
		$atts = array_merge( array(
			'limit'        => '12',
			'columns'      => '4',
			'category'     => '',
			'cat_operator' => 'IN',
		), (array) $atts );

		$shortcode = new WC_Shortcode_Products( $atts, 'best_selling_products' );

			return '<div class="best_selling_products '.$style.'">'.$shortcode->get_content().'</div>' ;

	}
	
	
		/**
	 * Recent Products shortcode.
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public static function recent_products_r( $atts ) {
		$style= '';
		if ($atts["enable_slider"] == true) {
			$style = 'slide';
		} 
		wp_enqueue_script('product-options', plugins_url('roadthemes-helper').'/js/product_options.js');
		wp_localize_script('product-options', 'product_options_recent', array(
				'atts' => $atts,
				'ajax_url' =>  admin_url( 'admin-ajax.php' )
			)
		);
		$atts = array_merge( array(
			'limit'        => '12',
			'columns'      => '4',
			'orderby'      => 'date',
			'order'        => 'DESC',
			'category'     => '',
			'cat_operator' => 'IN',
		), (array) $atts );

		$shortcode = new WC_Shortcode_Products( $atts, 'recent_products' );

		return '<div class="recent_products '.$style.'">'.$shortcode->get_content().'</div>';
	}
	
	/**
	 * Output featured products.
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public static function featured_products_r( $atts ) {
		$style= '';
		if ($atts["enable_slider"] == true) {
			$style = 'slide';
		} 
		wp_enqueue_script('product-options', plugins_url('roadthemes-helper').'/js/product_options.js');
		wp_localize_script('product-options', 'product_options_featured', array(
				'atts' => $atts,
				'ajax_url' =>  admin_url( 'admin-ajax.php' )
			)
		);
		$atts = array_merge( array(
			'limit'        => '12',
			'columns'      => '4',
			'orderby'      => 'date',
			'order'        => 'DESC',
			'category'     => '',
			'cat_operator' => 'IN',
		), (array) $atts );

		$atts['visibility'] = 'featured';

		$shortcode = new WC_Shortcode_Products( $atts, 'featured_products' );

		return '<div class="featured_products '.$style.'">'.$shortcode->get_content().'</div>';
	}
	
		/**
	 * List multiple products shortcode.
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public static function list_products_r( $atts ) {
		$style= '';
		if ($atts["enable_slider"] == true) {
			$style = 'slide';
		} 
		wp_enqueue_script('product-options', plugins_url('roadthemes-helper').'/js/product_options.js');
		wp_localize_script('product-options', 'product_options_list', array(
				'atts' => $atts,
				'ajax_url' =>  admin_url( 'admin-ajax.php' )
			)
		);
		$atts = (array) $atts;
		$type = 'products';

		// Allow list product based on specific cases.
		if ( isset( $atts['on_sale'] ) && wc_string_to_bool( $atts['on_sale'] ) ) {
			$type = 'sale_products';
		} elseif ( isset( $atts['best_selling'] ) && wc_string_to_bool( $atts['best_selling'] ) ) {
			$type = 'best_selling_products';
		} elseif ( isset( $atts['top_rated'] ) && wc_string_to_bool( $atts['top_rated'] ) ) {
			$type = 'top_rated_products';
		}

		$shortcode = new WC_Shortcode_Products( $atts, $type );

		return '<div class="list_product '.$style.'">'.$shortcode->get_content().'</div>';
	}
	
	public static function product_category_r($atts) {
		$style= '';
		if ($atts["enable_slider"] == true) {
			$style = 'slide';
		} 
		wp_enqueue_script('product-options', plugins_url('roadthemes-helper').'/js/product_options.js');
		wp_localize_script('product-options', 'product_options_category', array(
				'atts' => $atts
			)
		);
		
		if ( empty( $atts['category'] ) ) {
			return '';
		}

		$atts = array_merge( array(
			'limit'        => '12',
			'columns'      => '4',
			'orderby'      => 'menu_order title',
			'order'        => 'ASC',
			'category'     => '',
			'cat_operator' => 'IN',
		), (array) $atts );

		$shortcode = new WC_Shortcode_Products( $atts, 'product_category' );

		return '<div class="category_product '.$style.'">'.$shortcode->get_content().'</div>';
		
	}
	
	/**
	 * List all (or limited) product categories.
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public static function product_categories_r( $atts ) {
		$style= '';
		if ($atts["enable_slider"] == true) {
			$style = 'slide';
		} 
		wp_enqueue_script('product-options', plugins_url('roadthemes-helper').'/js/product_options.js');
		wp_localize_script('product-options', 'product_options_categories', array(
				'atts' => $atts
			)
		);
		
		if ( isset( $atts['number'] ) ) {
			$atts['limit'] = $atts['number'];
		}

		$atts = shortcode_atts( array(
			'limit'      => '-1',
			'orderby'    => 'name',
			'order'      => 'ASC',
			'columns'    => '4',
			'hide_empty' => 1,
			'parent'     => '',
			'ids'        => '',
		), $atts, 'product_categories' );

		$ids        = array_filter( array_map( 'trim', explode( ',', $atts['ids'] ) ) );
		$hide_empty = ( true === $atts['hide_empty'] || 'true' === $atts['hide_empty'] || 1 === $atts['hide_empty'] || '1' === $atts['hide_empty'] ) ? 1 : 0;

		// Get terms and workaround WP bug with parents/pad counts.
		$args = array(
			'orderby'    => $atts['orderby'],
			'order'      => $atts['order'],
			'hide_empty' => $hide_empty,
			'include'    => $ids,
			'pad_counts' => true,
			'child_of'   => $atts['parent'],
		);

		$product_categories = get_terms( 'product_cat', $args );

		if ( '' !== $atts['parent'] ) {
			$product_categories = wp_list_filter( $product_categories, array(
				'parent' => $atts['parent'],
			) );
		}

		if ( $hide_empty ) {
			foreach ( $product_categories as $key => $category ) {
				if ( 0 === $category->count ) {
					unset( $product_categories[ $key ] );
				}
			}
		}

		$atts['limit'] = '-1' === $atts['limit'] ? null : intval( $atts['limit'] );
		if ( $atts['limit'] ) {
			$product_categories = array_slice( $product_categories, 0, $atts['limit'] );
		}

		$columns = absint( $atts['columns'] );

		wc_set_loop_prop( 'columns', $columns );
		wc_set_loop_prop( 'is_shortcode', true );

		ob_start();

		if ( $product_categories ) {
			woocommerce_product_loop_start();

			foreach ( $product_categories as $category ) {
				wc_get_template( 'content-product_cat.php', array(
					'category' => $category,
				) );
			}

			woocommerce_product_loop_end();
		}

		woocommerce_reset_loop();
		//echo "<pre>"; print_r($atts ); echo "</pre>";
		return '<div class="categories_product '.$style.'  woocommerce columns-' . $columns . '">' . ob_get_clean() . '</div>';
	}
	
	public function road_vc_shortcodes() {
		
		
		
		add_filter( 'vc_autocomplete_list_products_r_ids_callback', array(
			$this,
			'productIdAutocompleteSuggester',
		), 10, 1 ); // Render exact product. Must return an array (label,value)
		
		add_filter( 'vc_autocomplete_product_categories_r_ids_callback', array(
			$this,
			'productCategoryCategoryAutocompleteSuggester',
		), 10, 1 ); // Render exact product. Must return an array (label,value)
		
		$args = array(
			'type' => 'post',
			'child_of' => 0,
			'parent' => '',
			'orderby' => 'name',
			'order' => 'ASC',
			'hide_empty' => false,
			'hierarchical' => 1,
			'exclude' => '',
			'include' => '',
			'number' => '',
			'taxonomy' => 'product_cat',
			'pad_counts' => false,

		);
		$order_by_values = array(
			'',
			__( 'Date', 'devita' ) => 'date',
			__( 'ID', 'devita' ) => 'ID',
			__( 'Author', 'devita' ) => 'author',
			__( 'Title', 'devita' ) => 'title',
			__( 'Modified', 'devita' ) => 'modified',
			__( 'Random', 'devita' ) => 'rand',
			__( 'Comment count', 'devita' ) => 'comment_count',
			__( 'Menu order', 'devita' ) => 'menu_order',
		);

		$order_way_values = array(
			'',
			__( 'Descending', 'devita' ) => 'DESC',
			__( 'Ascending', 'devita' ) => 'ASC',
		); 
		 
	//Sale products Roadthemes
	vc_map( array(
		'name' => __( 'RT Sale products', 'devita' ),
		'base' => 'sale_products_r',
		'icon' => 'icon-wpb-woocommerce',
		'category' => __( 'Theme', 'devita' ),
		'description' => __( 'List all products on sale', 'devita' ),
		'params' => array_merge(
			array(
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Per page', 'devita' ),
					'value' => 12,
					'save_always' => true,
					'param_name' => 'per_page',
					'description' => esc_html__( 'How much items per page to show', 'devita' ),
				),
					array(
					'type' => 'textfield',
					'heading' => __( 'Columns', 'devita' ),
					'value' => 4,
					'save_always' => true,
					'param_name' => 'columns',
					'description' => __( 'How much columns grid', 'devita' ),
				),
				array(
					'type' => 'dropdown',
					'heading' => __( 'Order by', 'devita' ),
					'param_name' => 'orderby',
					'value' => $order_by_values,
					'save_always' => true,
					'description' => sprintf( __( 'Select how to sort retrieved products. More at %s.', 'devita' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
				),
				array(
					'type' => 'dropdown',
					'heading' => __( 'Sort order', 'devita' ),
					'param_name' => 'order',
					'value' => $order_way_values,
					'save_always' => true,
					'description' => sprintf( __( 'Designates the ascending or descending order. More at %s.', 'devita' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
				), 
			),road_get_slider_setting()
		)
	)  );
		
		
		
		//Featured products Roadthemes
	vc_map( array(
		'name' => __( 'RT Featured products', 'devita' ),
		'base' => 'featured_products_r',
		'icon' => 'icon-wpb-woocommerce',
		'category' => __( 'Theme', 'devita' ),
		'description' => __( 'List all products on Featured', 'devita' ),
		'params' => array_merge(
		array(
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Per page', 'devita' ),
				'value' => 12,
				'save_always' => true,
				'param_name' => 'per_page',
				'description' => esc_html__( 'How much items per page to show', 'devita' ),
			),
				array(
				'type' => 'textfield',
				'heading' => __( 'Columns', 'devita' ),
				'value' => 4,
				'save_always' => true,
				'param_name' => 'columns',
				'description' => __( 'How much columns grid', 'devita' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => __( 'Order by', 'devita' ),
				'param_name' => 'orderby',
				'value' => $order_by_values,
				'save_always' => true,
				'description' => sprintf( __( 'Select how to sort retrieved products. More at %s.', 'devita' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => __( 'Sort order', 'devita' ),
				'param_name' => 'order',
				'value' => $order_way_values,
				'save_always' => true,
				'description' => sprintf( __( 'Designates the ascending or descending order. More at %s.', 'devita' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
			), 
			),road_get_slider_setting()
		)
	)  );
		
		//Recent products Roadthemes
	vc_map( array(
	'name' => __( 'RT Recent products', 'devita' ),
	'base' => 'recent_products_r',
	'icon' => 'icon-wpb-woocommerce',
	'category' => __( 'Theme', 'devita' ),
	'description' => __( 'List all products on Recent', 'devita' ),
	'params' => array_merge( 
		array(
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Per page', 'devita' ),
				'value' => 12,
				'save_always' => true,
				'param_name' => 'per_page',
				'description' => esc_html__( 'How much items per page to show', 'devita' ),
			),
				array(
				'type' => 'textfield',
				'heading' => __( 'Columns', 'devita' ),
				'value' => 4,
				'save_always' => true,
				'param_name' => 'columns',
				'description' => __( 'How much columns grid', 'devita' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => __( 'Order by', 'devita' ),
				'param_name' => 'orderby',
				'value' => $order_by_values,
				'save_always' => true,
				'description' => sprintf( __( 'Select how to sort retrieved products. More at %s.', 'devita' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => __( 'Sort order', 'devita' ),
				'param_name' => 'order',
				'value' => $order_way_values,
				'save_always' => true,
				'description' => sprintf( __( 'Designates the ascending or descending order. More at %s.', 'devita' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
			), 
			),road_get_slider_setting()
		)
	)  );
		
	//Bestselling products Roadthemes
	vc_map( array(
	'name' => __( 'RT Bestselling products', 'devita' ),
	'base' => 'bestselling_products_r',
	'icon' => 'icon-wpb-woocommerce',
	'category' => __( 'Theme', 'devita' ),
	'description' => __( 'List all products on Recent', 'devita' ),
	'params' => array_merge( 
		array(
				array(
					'type' => 'textfield',
					'heading' => esc_html__( 'Per page', 'devita' ),
					'value' => 12,
					'save_always' => true,
					'param_name' => 'per_page',
					'description' => esc_html__( 'How much items per page to show', 'devita' ),
				),
					array(
					'type' => 'textfield',
					'heading' => __( 'Columns', 'devita' ),
					'value' => 4,
					'save_always' => true,
					'param_name' => 'columns',
					'description' => __( 'How much columns grid', 'devita' ),
				), 
			),road_get_slider_setting()
		)
	)  );
	
	//products Roadthemes
	vc_map( array(
	'name' => __( 'RT Products', 'devita' ),
	'base' => 'list_products_r',
	'icon' => 'icon-wpb-woocommerce',
	'category' => __( 'Theme', 'devita' ),
	'description' => __( 'List all products on New', 'devita' ),
	'params' => array_merge(
		array(
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Per page', 'devita' ),
				'value' => 12,
				'save_always' => true,
				'param_name' => 'per_page',
				'description' => esc_html__( 'How much items per page to show', 'devita' ),
			),
				array(
				'type' => 'textfield',
				'heading' => __( 'Columns', 'devita' ),
				'value' => 4,
				'save_always' => true,
				'param_name' => 'columns',
				'description' => __( 'How much columns grid', 'devita' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => __( 'Order by', 'devita' ),
				'param_name' => 'orderby',
				'value' => $order_by_values,
				'save_always' => true,
				'description' => sprintf( __( 'Select how to sort retrieved products. More at %s.', 'devita' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
			),
			array(
				'type' => 'dropdown',
				'heading' => __( 'Sort order', 'devita' ),
				'param_name' => 'order',
				'value' => $order_way_values,
				'save_always' => true,
				'description' => sprintf( __( 'Designates the ascending or descending order. More at %s.', 'devita' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
			), 
			array(
				'type' => 'autocomplete',
				'heading' => __( 'Products', 'js_composer' ),
				'param_name' => 'ids',
				'settings' => array(
					'multiple' => true,
					'sortable' => true,
					'unique_values' => true,
					// In UI show results except selected. NB! You should manually check values in backend
				),
				'save_always' => true,
				'description' => __( 'Enter List of Products', 'js_composer' ),
			),
			array(
				'type' => 'hidden',
				'param_name' => 'skus',
			),
		),road_get_slider_setting()
	)
	)  );
	
	
	//Product Categories Roadthemes
	vc_map( array(
	'name' => __( 'RT Product categories', 'devita' ),
	'base' => 'product_categories_r',
	'icon' => 'icon-wpb-woocommerce',
	'category' => __( 'Theme', 'devita' ),
	'description' => __( 'List all products on New', 'devita' ),
		'params' => array_merge( 
			array(
				array(
					'type' => 'textfield',
					'heading' => __( 'Number', 'devita' ),
					'param_name' => 'number',
					'description' => __( 'The `number` field is used to display the number of products.', 'devita' ),
				),
				array(
					'type' => 'dropdown',
					'heading' => __( 'Order by', 'devita' ),
					'param_name' => 'orderby',
					'value' => $order_by_values,
					'save_always' => true,
					'description' => sprintf( __( 'Select how to sort retrieved products. More at %s.', 'devita' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
				),
				array(
					'type' => 'dropdown',
					'heading' => __( 'Sort order', 'devita' ),
					'param_name' => 'order',
					'value' => $order_way_values,
					'save_always' => true,
					'description' => sprintf( __( 'Designates the ascending or descending order. More at %s.', 'devita' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
				),
					 
				array(
					'type' => 'textfield',
					'heading' => __( 'Columns', 'devita' ),
					'value' => 4,
					'param_name' => 'columns',
					'save_always' => true,
					'description' => __( 'How much columns grid', 'devita' ),
				),
				array(
					'type' => 'textfield',
					'heading' => __( 'Number', 'devita' ),
					'param_name' => 'hide_empty',
					'description' => __( 'Hide empty', 'devita' ),
				),
				array(
					'type' => 'autocomplete',
					'heading' => __( 'Categories', 'devita' ),
					'param_name' => 'ids',
					'settings' => array(
						'multiple' => true,
						'sortable' => true,
					),
					'save_always' => true,
					'description' => __( 'List of product categories', 'devita' ),
				), 
			),road_get_slider_setting()
		)
	)  );
		
		$categories = get_categories( $args );
		$product_categories_dropdown = array();
		$vc = new Vc_Vendor_Woocommerce();
		$this->getCategoryChildsFull( 0, $categories, 0, $product_categories_dropdown );
		
		//Product category Roadthemes
		vc_map( array(
		'name' => __( 'RT Product category', 'devita' ),
		'base' => 'product_category_r',
		'icon' => 'icon-wpb-woocommerce',
		'category' => __( 'Theme', 'devita' ),
		'description' => __( 'Show multiple products in a category', 'devita' ),
			'params' => array_merge(
			array(
				array(
					'type' => 'textfield',
					'heading' => __( 'Per page', 'js_composer' ),
					'value' => 12,
					'save_always' => true,
					'param_name' => 'per_page',
					'description' => __( 'How much items per page to show', 'js_composer' ),
				),
				array(
					'type' => 'textfield',
					'heading' => __( 'Columns', 'js_composer' ),
					'value' => 4,
					'save_always' => true,
					'param_name' => 'columns',
					'description' => __( 'How much columns grid', 'js_composer' ),
				),
				array(
					'type' => 'dropdown',
					'heading' => __( 'Order by', 'js_composer' ),
					'param_name' => 'orderby',
					'value' => $order_by_values,
					'save_always' => true,
					'description' => sprintf( __( 'Select how to sort retrieved products. More at %s.', 'js_composer' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
				),
				array(
					'type' => 'dropdown',
					'heading' => __( 'Sort order', 'js_composer' ),
					'param_name' => 'order',
					'value' => $order_way_values,
					'save_always' => true,
					'description' => sprintf( __( 'Designates the ascending or descending order. More at %s.', 'js_composer' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
				),
				 
				array(
					'type' => 'dropdown',
					'heading' => __( 'Category', 'js_composer' ),
					'value' => $product_categories_dropdown,
					'param_name' => 'category',
					'save_always' => true,
					'description' => __( 'Product category list', 'js_composer' ),
				),
			),road_get_slider_setting()
		)
	)  );
		
		
	}
	
	protected function getCategoryChildsFull( $parent_id, $array, $level, &$dropdown ) {
		$keys = array_keys( $array );
		$i = 0;
		while ( $i < count( $array ) ) {
			$key = $keys[ $i ];
			$item = $array[ $key ];
			$i ++;
			if ( $item->category_parent == $parent_id ) {
				$name = str_repeat( '- ', $level ) . $item->name;
				$value = $item->slug;
				$dropdown[] = array(
					'label' => $name . '(' . $item->term_id . ')',
					'value' => $value,
				);
				unset( $array[ $key ] );
				$array = $this->getCategoryChildsFull( $item->term_id, $array, $level + 1, $dropdown );
				$keys = array_keys( $array );
				$i = 0;
			}
		}

		return $array;
	}
	
	public function getCategoryChilds( $parent_id, $pos, $array, $level, &$dropdown ) {
		_deprecated_function( 'Vc_Vendor_Woocommerce::getCategoryChilds', '4.5.3  (will be removed in 5.3)', 'Vc_Vendor_Woocommerce::getCategoryChildsFull' );
		for ( $i = $pos; $i < count( $array ); $i ++ ) {
			if ( $array[ $i ]->category_parent == $parent_id ) {
				$data = array(
					str_repeat( '- ', $level ) . $array[ $i ]->name => $array[ $i ]->slug,
				);
				$dropdown = array_merge( $dropdown, $data );
				$this->getCategoryChilds( $array[ $i ]->term_id, $i, $array, $level + 1, $dropdown );
			}
		}
	}
	
	
		/**
	 * List all products on sale.
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	 function vc_get_autocomplete_suggestion() { 
	vc_user_access()
		->checkAdminNonce()
		->validateDie()
		->wpAny( 'edit_posts', 'edit_pages' )
		->validateDie();

	$query = vc_post_param( 'query' );
	
	$tag = strip_tags( vc_post_param( 'shortcode' ) );
	$param_name = vc_post_param( 'param' );

	$this->vc_render_suggestion( $query, $tag, $param_name );
	}
	
	function vc_render_suggestion( $query, $tag, $param_name ) {
		
		$suggestions = apply_filters( 'vc_autocomplete_' . stripslashes( $tag ) . '_' . stripslashes( $param_name ) . '_callback', $query, $tag, $param_name );
		
		if ( is_array( $suggestions ) && ! empty( $suggestions ) ) {
			die( json_encode( $suggestions ) );
		}
		die( 'No Thing' ); // if nothing found..
	}
	
	/**
	 * Find product by id
	 * @since 4.4
	 *
	 * @param $query
	 *
	 * @return bool|array
	 */
	public function productIdAutocompleteRender( $query ) {
		if(isset($query['value']))		
			$query = trim( $query['value'] ); // get value from requested
		if ( ! empty( $query ) ) {
			// get product
			$product_object = wc_get_product( (int) $query );
		
			if ( is_object( $product_object ) ) {
				$product_sku = $product_object->get_sku();
				$product_title = $product_object->get_title();
				$product_id = $product_object->get_id();

				$product_sku_display = '';
				if ( ! empty( $product_sku ) ) {
					$product_sku_display = ' - ' . __( 'Sku', 'js_composer' ) . ': ' . $product_sku;
				}

				$product_title_display = '';
				if ( ! empty( $product_title ) ) {
					$product_title_display = ' - ' . __( 'Title', 'js_composer' ) . ': ' . $product_title;
				}

				$product_id_display = __( 'Id', 'js_composer' ) . ': ' . $product_id;

				$data = array();
				$data['value'] = $product_id;
				$data['label'] = $product_id_display . $product_title_display . $product_sku_display;

				return ! empty( $data ) ? $data : false;
			}

			return false;
		}

		return false;
	}
	
		/**
	 * Suggester for autocomplete by id/name/title/sku
	 * @since 4.4
	 *
	 * @param $query
	 *
	 * @return array - id's from products with title/sku.
	 */
	public function productIdAutocompleteSuggester( $query ) {
		global $wpdb;
		$product_id = (int) $query;
		$post_meta_infos = $wpdb->get_results( $wpdb->prepare( "SELECT a.ID AS id, a.post_title AS title, b.meta_value AS sku
					FROM {$wpdb->posts} AS a
					LEFT JOIN ( SELECT meta_value, post_id  FROM {$wpdb->postmeta} WHERE `meta_key` = '_sku' ) AS b ON b.post_id = a.ID
					WHERE a.post_type = 'product' AND ( a.ID = '%d' OR b.meta_value LIKE '%%%s%%' OR a.post_title LIKE '%%%s%%' )", $product_id > 0 ? $product_id : - 1, stripslashes( $query ), stripslashes( $query ) ), ARRAY_A );

		$results = array();
		if ( is_array( $post_meta_infos ) && ! empty( $post_meta_infos ) ) {
			foreach ( $post_meta_infos as $value ) {
				$data = array();
				$data['value'] = $value['id'];
				$data['label'] = __( 'Id', 'js_composer' ) . ': ' . $value['id'] . ( ( strlen( $value['title'] ) > 0 ) ? ' - ' . __( 'Title', 'js_composer' ) . ': ' . $value['title'] : '' ) . ( ( strlen( $value['sku'] ) > 0 ) ? ' - ' . __( 'Sku', 'js_composer' ) . ': ' . $value['sku'] : '' );
				$results[] = $data;
			}
		}

		return $results;
	}
	
	function vc_autocomplete_form_field( $settings, $value, $tag ) {
		$auto_complete = new Vc_AutoComplete( $settings, $value, $tag );
		return apply_filters( 'vc_autocomplete_render_filter', $auto_complete->render() );
	}
	
	/**
	 * Autocomplete suggester to search product category by name/slug or id.
	 * @since 4.4
	 *
	 * @param $query
	 * @param bool $slug - determines what output is needed
	 *      default false - return id of product category
	 *      true - return slug of product category
	 *
	 * @return array
	 */
	public function productCategoryCategoryAutocompleteSuggester( $query, $slug = false ) {
		global $wpdb;
		$cat_id = (int) $query;
		$query = trim( $query );
		$post_meta_infos = $wpdb->get_results( $wpdb->prepare( "SELECT a.term_id AS id, b.name as name, b.slug AS slug
						FROM {$wpdb->term_taxonomy} AS a
						INNER JOIN {$wpdb->terms} AS b ON b.term_id = a.term_id
						WHERE a.taxonomy = 'product_cat' AND (a.term_id = '%d' OR b.slug LIKE '%%%s%%' OR b.name LIKE '%%%s%%' )", $cat_id > 0 ? $cat_id : - 1, stripslashes( $query ), stripslashes( $query ) ), ARRAY_A );

		$result = array();
		if ( is_array( $post_meta_infos ) && ! empty( $post_meta_infos ) ) {
			foreach ( $post_meta_infos as $value ) {
				$data = array();
				$data['value'] = $slug ? $value['slug'] : $value['id'];
				$data['label'] = __( 'Id', 'js_composer' ) . ': ' . $value['id'] . ( ( strlen( $value['name'] ) > 0 ) ? ' - ' . __( 'Name', 'js_composer' ) . ': ' . $value['name'] : '' ) . ( ( strlen( $value['slug'] ) > 0 ) ? ' - ' . __( 'Slug', 'js_composer' ) . ': ' . $value['slug'] : '' );
				$result[] = $data;
			}
		}

		return $result;
	}

	/**
	 * Search product category by id
	 * @since 4.4
	 *
	 * @param $query
	 *
	 * @return bool|array
	 */
	public function productCategoryCategoryRenderByIdExact( $query ) {
		$query = $query['value'];
		$cat_id = (int) $query;
		$term = get_term( $cat_id, 'product_cat' );

		return $this->productCategoryTermOutput( $term );
	}

	/**
	 * Suggester for autocomplete to find product category by id/name/slug but return found product category SLUG
	 * @since 4.4
	 *
	 * @param $query
	 *
	 * @return array - slug of products categories.
	 */
	public function productCategoryCategoryAutocompleteSuggesterBySlug( $query ) {
		$result = $this->productCategoryCategoryAutocompleteSuggester( $query, true );

		return $result;
	}

	/**
	 * Search product category by slug.
	 * @since 4.4
	 *
	 * @param $query
	 *
	 * @return bool|array
	 */
	public function productCategoryCategoryRenderBySlugExact( $query ) {
		$query = $query['value'];
		$query = trim( $query );
		$term = get_term_by( 'slug', $query, 'product_cat' );

		return $this->productCategoryTermOutput( $term );
	}

	/**
	 * Return product category value|label array
	 *
	 * @param $term
	 *
	 * @since 4.4
	 * @return array|bool
	 */
	protected function productCategoryTermOutput( $term ) {
		$term_slug = $term->slug;
		$term_title = $term->name;
		$term_id = $term->term_id;

		$term_slug_display = '';
		if ( ! empty( $term_slug ) ) {
			$term_slug_display = ' - ' . __( 'Sku', 'js_composer' ) . ': ' . $term_slug;
		}

		$term_title_display = '';
		if ( ! empty( $term_title ) ) {
			$term_title_display = ' - ' . __( 'Title', 'js_composer' ) . ': ' . $term_title;
		}

		$term_id_display = __( 'Id', 'js_composer' ) . ': ' . $term_id;

		$data = array();
		$data['value'] = $term_id;
		$data['label'] = $term_id_display . $term_title_display . $term_slug_display;

		return ! empty( $data ) ? $data : false;
	}

	public static function getProductsFieldsList() {
		return array(
			__( 'SKU', 'js_composer' ) => 'sku',
			__( 'ID', 'js_composer' ) => 'id',
			__( 'Price', 'js_composer' ) => 'price',
			__( 'Regular Price', 'js_composer' ) => 'regular_price',
			__( 'Sale Price', 'js_composer' ) => 'sale_price',
			__( 'Price html', 'js_composer' ) => 'price_html',
			__( 'Reviews count', 'js_composer' ) => 'reviews_count',
			__( 'Short description', 'js_composer' ) => 'short_description',
			__( 'Dimensions', 'js_composer' ) => 'dimensions',
			__( 'Rating count', 'js_composer' ) => 'rating_count',
			__( 'Weight', 'js_composer' ) => 'weight',
			__( 'Is on sale', 'js_composer' ) => 'on_sale',
			__( 'Custom field', 'js_composer' ) => '_custom_',
		);
	}

	public static function getProductFieldLabel( $key ) {
		if ( false === self::$product_fields_list ) {
			self::$product_fields_list = array_flip( self::getProductsFieldsList() );
		}

		return isset( self::$product_fields_list[ $key ] ) ? self::$product_fields_list[ $key ] : '';
	}

	public static function getOrderFieldsList() {
		return array(
			__( 'ID', 'js_composer' ) => 'id',
			__( 'Order number', 'js_composer' ) => 'order_number',
			__( 'Currency', 'js_composer' ) => 'order_currency',
			__( 'Total', 'js_composer' ) => 'total',
			__( 'Status', 'js_composer' ) => 'status',
			__( 'Payment method', 'js_composer' ) => 'payment_method',
			__( 'Billing address city', 'js_composer' ) => 'billing_address_city',
			__( 'Billing address country', 'js_composer' ) => 'billing_address_country',
			__( 'Shipping address city', 'js_composer' ) => 'shipping_address_city',
			__( 'Shipping address country', 'js_composer' ) => 'shipping_address_country',
			__( 'Customer Note', 'js_composer' ) => 'customer_note',
			__( 'Customer API', 'js_composer' ) => 'customer_api',
			__( 'Custom field', 'js_composer' ) => '_custom_',
		);
	}

	public static function getOrderFieldLabel( $key ) {
		if ( false === self::$order_fields_list ) {
			self::$order_fields_list = array_flip( self::getOrderFieldsList() );
		}

		return isset( self::$order_fields_list[ $key ] ) ? self::$order_fields_list[ $key ] : '';
	}

	public function yoastSeoCompatibility() {
		if ( function_exists( 'WC' ) ) {
			// WC()->frontend_includes();
			include_once( WC()->plugin_path() . '/includes/wc-template-functions.php' );
			// include_once WC()->plugin_path() . '';
		}
	}
	

}

$road_products = new RoadProducts();


