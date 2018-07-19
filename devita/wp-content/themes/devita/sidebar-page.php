<?php
/**
 * The sidebar for content page
 *
 * If no active widgets are in the sidebar, hide it completely.
 *
 * @package WordPress
 * @subpackage Devita_Theme
 * @since Devita 1.0
 */
?>

<?php if ( is_active_sidebar( 'sidebar-page' ) ) : ?>
<div id="secondary" class="col-12 col-lg-3">
	<div class="sidebar-inner">
		<?php dynamic_sidebar( 'sidebar-page' ); ?>
	</div>
</div>
<?php endif; ?>