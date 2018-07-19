<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woothemes.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.6.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $woocommerce_loop;

$devita_opt = get_option( 'devita_opt' );

$devita_viewmode = Devita_Class::devita_show_view_mode();
$devita_products_count = Devita_Class::devita_products_count();

// Ensure visibility
if ( ! $product || ! $product->is_visible() )
	return;

//substring description
$shortdecript = get_the_excerpt($post);
$productintro = substr($shortdecript, 0, $devita_opt['product_excerpt_length']);

// Extra post classes
$classes = array();

$count   = $product->get_rating_count();

$devita_shopclass = Devita_Class::devita_shop_class('');

$colwidth = 3;

if($devita_shopclass=='shop-fullwidth') {
	if(isset($devita_opt)){
		$woocommerce_loop['columns'] = $devita_opt['product_per_row_fw'];
		if($woocommerce_loop['columns'] > 0){
			$colwidth = round(12/$woocommerce_loop['columns']);
		}
	}
	$classes[] = ' item-col col-12 col-sm-6 col-md-4 col-lg-3 col-xl-'.$colwidth ;
} else {
	if(isset($devita_opt)){
		$woocommerce_loop['columns'] = $devita_opt['product_per_row'];
		if($woocommerce_loop['columns'] > 0){
			$colwidth = round(12/$woocommerce_loop['columns']);
		}
	}
	$classes[] = ' item-col col-12 col-sm-6 col-md-4 col-xl-'.$colwidth ;
}
?>

 

<div <?php post_class( $classes ); ?>>
	<div class="product-wrapper">
		
		<div class="list-col4 <?php if($devita_viewmode=='list-view'){ echo ' col-12 col-md-4';} ?>">
			<div class="product-image">
				<?php if ( $product->is_on_sale() ) : ?> 
					<?php if($product->get_type()=="variable") {
						$salep = $product->get_variation_regular_price() - $product->get_variation_sale_price();
						$salepercent = round(($salep*100)/($product->get_variation_regular_price()));
						echo '<span class="onsale"><span class="sale-percent">-'.$salepercent.'%</span></span>';
					}
					elseif ( $product->get_price() > 0 ) {
						$salep = $product->get_regular_price() - $product->get_price();
						$salepercent = round(($salep*100)/($product->get_regular_price()));
						echo '<span class="onsale"> <span class="sale-percent">-'.$salepercent.'%</span></span>';
					} ?>
				<?php endif; ?> 

				<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>
					<?php 
					echo wp_kses($product->get_image('shop_catalog', array('class'=>'primary_image')), array(
						'a'=>array(
							'href'=>array(),
							'title'=>array(),
							'class'=>array(),
						),
						'img'=>array(
							'src'=>array(),
							'height'=>array(),
							'width'=>array(),
							'class'=>array(),
							'alt'=>array(),
						)
					));
					
					if(isset($devita_opt['second_image'])){
						if($devita_opt['second_image']){
							$attachment_ids = $product->get_gallery_image_ids();
							if ( $attachment_ids ) {
								echo wp_get_attachment_image( $attachment_ids[0], apply_filters( 'single_product_small_thumbnail_size', 'shop_catalog' ), false, array('class'=>'secondary_image') );
							}
						}
					}
					?>
					<span class="shadow"></span>
				<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>  
				<?php if ( isset($devita_opt['quickview']) && $devita_opt['quickview'] ) { ?>
					<div class="quickview-inner">
						<div class="quickviewbtn">
							<a class="detail-link quickview" data-quick-id="<?php the_ID();?>" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php echo esc_html($devita_opt['detail_link_text']);?></a>
						</div>
					</div>
				<?php } ?> 
			</div>
		</div>
		<div class="list-col8 <?php if($devita_viewmode=='list-view'){ echo ' col-12 col-md-8';} ?>">
			<div class="gridview">
				<div class="count-down">
					<?php
					$countdown = false;
					$sale_end = get_post_meta( $product->get_id(), '_sale_price_dates_to', true );
					/* simple product */
					if($sale_end){
						$countdown = true;
						$sale_end = date('Y/m/d', (int)$sale_end);
						?>
						<div class="countbox hastime" data-time="<?php echo esc_attr($sale_end); ?>"></div>
					<?php } ?>
					<?php /* variable product */
					if($product->has_child()){
						$vsale_end = array();
						
						$pvariables = $product->get_children();
						foreach($pvariables as $pvariable){
							$vsale_end[] = (int)get_post_meta( $pvariable, '_sale_price_dates_to', true );
							
							if( get_post_meta( $pvariable, '_sale_price_dates_to', true ) ){
								$countdown = true;
							}
						}
						if($countdown){
							/* get the latest time */
							$vsale_end_date = max($vsale_end);
							$vsale_end_date = date('Y/m/d', $vsale_end_date);
							?>
							<div class="countbox hastime" data-time="<?php echo esc_attr($vsale_end_date); ?>"></div>
						<?php
						}
					}
					?>
				</div>
				<?php  $categ = wc_get_product_category_list($product->get_id()); 
    				if ($categ != '') { ?>
	    				<div class="tag-cate">
	    					<?php echo ''.$categ;  ?>
	    				</div>
    			<?php } ?>
				<h2 class="product-name">
					<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				</h2>  
				<div class="price-box"><?php echo ''.$product->get_price_html(); ?></div> 
				<div class="box-hover">
					<?php if (wc_get_rating_html( $product->get_average_rating() )) { ?>
						<div class="ratings"><?php echo ''.wc_get_rating_html( $product->get_average_rating() ); ?></div> 
					<?php } ?>
					<div class="product-desc"><?php echo ''.$productintro; ?></div> 
					 
					<div class="actions clearfix"> 
						<ul class="add-to-links">
							<li> 
								<?php if ( class_exists( 'YITH_WCWL' ) ) {
									echo preg_replace("/<img[^>]+\>/i", " ", do_shortcode('[yith_wcwl_add_to_wishlist]'));
								} ?>
							</li> 
							<li class="add-to-cart">
								<?php echo do_shortcode('[add_to_cart id="'.$product->get_id().'"]') ?>
							</li>
							<li>
								<?php if( class_exists( 'YITH_Woocompare' ) ) {
									echo do_shortcode('[yith_compare_button]');
								} ?>
							</li>   
						</ul>
					</div>
				</div>
			</div>
			<div class="listview">
				<?php  $categ = wc_get_product_category_list($product->get_id()); 
    				if ($categ != '') { ?>
	    				<div class="tag-cate">
	    					<?php echo ''.$categ;  ?>
	    				</div>
    			<?php } ?>
				<h2 class="product-name">
					<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				</h2> 
				<?php if (wc_get_rating_html( $product->get_average_rating() )) { ?>
					<div class="ratings"><?php echo ''.wc_get_rating_html( $product->get_average_rating() ); ?></div>
				<?php } ?>
				<div class="price-box"><?php echo ''.$product->get_price_html(); ?></div>
				<div class="product-desc"><?php the_excerpt(); ?></div> 
				<div class="actions clearfix">
					
					<ul class="add-to-links"> 
						<li class="add-to-cart">
							<?php echo do_shortcode('[add_to_cart id="'.$product->get_id().'"]') ?>
						</li>  
						<li> 
							<?php if ( class_exists( 'YITH_WCWL' ) ) {
								echo preg_replace("/<img[^>]+\>/i", " ", do_shortcode('[yith_wcwl_add_to_wishlist]'));
							} ?>
						</li> 
						<li>
							<?php if( class_exists( 'YITH_Woocompare' ) ) {
								echo do_shortcode('[yith_compare_button]');
							} ?>
						</li>
						
					</ul>
				</div>
			</div>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
 