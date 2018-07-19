<?php
/**
 * Product Loop Start
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.3.0
 */

global $wp_query, $woocommerce_loop;

$devita_opt = get_option( 'devita_opt' );

$shoplayout = 'sidebar';
if(isset($devita_opt['shop_layout']) && $devita_opt['shop_layout']!=''){
	$shoplayout = $devita_opt['shop_layout'];
}
if(isset($_GET['layout']) && $_GET['layout']!=''){
	$shoplayout = $_GET['layout'];
}
$shopsidebar = 'left';
if(isset($devita_opt['sidebarshop_pos']) && $devita_opt['sidebarshop_pos']!=''){
	$shopsidebar = $devita_opt['sidebarshop_pos'];
}
if(isset($_GET['sidebar']) && $_GET['sidebar']!=''){
	$shopsidebar = $_GET['sidebar'];
}
switch($shoplayout) {
	case 'fullwidth':
		Devita_Class::devita_shop_class('shop-fullwidth');
		$shopcolclass = 12;
		$shopsidebar = 'none';
		$productcols = 4;
		break;
	default:
		Devita_Class::devita_shop_class('shop-sidebar');
		$shopcolclass = 9;
		$productcols = 3;
}

$devita_viewmode = Devita_Class::devita_show_view_mode();
?>
<div class="shop-products products row <?php echo esc_attr($devita_viewmode);?> <?php echo esc_attr($shoplayout);?>">