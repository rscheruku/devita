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
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div class="wrapper <?php if($devita_opt['page_layout']=='box'){echo 'box-layout';}?>">
	<div class="page-wrapper">