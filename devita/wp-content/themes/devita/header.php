<?php
/**
 * The Header template for our theme
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage Devita_Theme
 * @since Devita 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<?php $devita_opt = get_option( 'devita_opt' ); ?>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php if (isset($devita_opt['menu_sidebar']) && ($devita_opt['menu_sidebar'] != "")) {
	$class_menu = $devita_opt['menu_sidebar'];
} else {
$class_menu = "";
}?>
<div class="wrapper <?php if($devita_opt['page_layout']=='box'){echo 'box-layout';} echo esc_attr($class_menu); ?>">
	<div class="page-wrapper">

			

		<?php if(isset($devita_opt['header_layout']) && $devita_opt['header_layout']!=''){
			$header_class = str_replace(' ', '-', strtolower($devita_opt['header_layout']));
		} else {
			$header_class = '';
		} ?>
		<div class="header-container <?php echo esc_attr($header_class);?>">
			<div class="header">
				<div class="header-content">
					<?php
					if ( isset($devita_opt['header_layout']) && $devita_opt['header_layout']!="") {
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
								if($jscomposer_template->post_title == $devita_opt['header_layout']){
									echo do_shortcode($jscomposer_template->post_content);
								}
							}
						}
					} else {
						?>
						<div class="header-default">  
							<div class="header-middle">
								<div class="container"> 
									<div class="row">
										<div class="col-12 col-md-3"> 
											<h1 class="logo site-title"><span class="logo-inner"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></span></h1> 
										</div>
										<div class="col-12 col-md-6">
											<?php get_search_form(); ?>
										</div>
									</div> 
								</div>
							</div>
							<div class="header-bottom"> 
								<div class="container"> 
									<?php if ( has_nav_menu( 'primary' ) ) : ?>
										<div class="nav-container">
											<div class="horizontal-menu visible-large">
												<?php wp_nav_menu( array( 'theme_location' => 'primary', 'container_class' => 'primary-menu-container', 'menu_class' => 'nav-menu' ) ); ?>
											</div>
										</div>  
									<?php endif; ?>
									<?php if ( has_nav_menu( 'mobilemenu' ) ) : ?>
										<div class="visible-small mobile-menu"> 
											<div class="mbmenu-toggler">
												<?php if(isset($devita_opt['mobile_menu_label']) && ($devita_opt['mobile_menu_label'] != '') ) { 
													echo esc_html($devita_opt['mobile_menu_label']); 
												} else { 
													echo esc_html__('menu', 'devita'); 
												}?>
												<span class="mbmenu-icon"><i class="fa fa-bars"></i></span>
											</div>
											<?php wp_nav_menu( array( 'theme_location' => 'mobilemenu', 'container_class' => 'mobile-menu-container', 'menu_class' => 'nav-menu' ) ); ?>
										</div> 
									<?php endif; ?>   
								</div> 
							</div>   
						</div>  
						<?php
					} 
					?>
				</div> 
			</div>
			<div class="clearfix"></div>
		</div>