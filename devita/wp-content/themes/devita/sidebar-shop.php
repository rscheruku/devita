<?php
/**
 * The sidebar for shop page
 *
 * If no active widgets are in the sidebar, hide it completely.
 *
 * @package WordPress
 * @subpackage Devita_Theme
 * @since Devita 1.0
 */
?>

<?php if ( is_active_sidebar( 'sidebar-shop' ) ) : ?>
<div id="secondary" class="col-12 col-lg-3 sidebar-shop">
	<div class="sidebar-inner">
		<?php dynamic_sidebar( 'sidebar-shop' ); ?>
	</div>
</div>
<?php endif; ?>