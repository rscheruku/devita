<?php
/**
 * The sidebar for product category page
 *
 * If no active widgets are in the sidebar, hide it completely.
 *
 * @package WordPress
 * @subpackage Devita_Theme
 * @since Devita 1.0
 */
?>

<?php if ( is_active_sidebar( 'sidebar-category' ) ) : ?>
<div id="secondary" class="col-12 col-lg-3 sidebar-category">
	<div class="sidebar-inner">
		<?php dynamic_sidebar( 'sidebar-category' ); ?>
	</div>
</div>
<?php endif; ?>