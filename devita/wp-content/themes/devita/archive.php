<?php
/**
 * The template for displaying Archive pages
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * If you'd like to further customize these archive views, you may create a
 * new template file for each specific one. For example, devita already
 * has tag.php for Tag archives, category.php for Category archives, and
 * author.php for Author archives.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Devita_Theme
 * @since Devita 1.0
 */

$devita_opt = get_option( 'devita_opt' );

get_header();

$devita_bloglayout = 'nosidebar';
if(isset($devita_opt['blog_layout']) && $devita_opt['blog_layout']!=''){
	$devita_bloglayout = $devita_opt['blog_layout'];
}
if(isset($_GET['layout']) && $_GET['layout']!=''){
	$devita_bloglayout = $_GET['layout'];
}
$devita_blogsidebar = 'right';
if(isset($devita_opt['sidebarblog_pos']) && $devita_opt['sidebarblog_pos']!=''){
	$devita_blogsidebar = $devita_opt['sidebarblog_pos'];
}
if(isset($_GET['sidebar']) && $_GET['sidebar']!=''){
	$devita_blogsidebar = $_GET['sidebar'];
}
switch($devita_bloglayout) {
	case 'sidebar':
		$devita_blogclass = 'blog-sidebar';
		$devita_blogcolclass = 9;
		Devita_Class::devita_post_thumbnail_size('devita-category-thumb');
		break;
	case 'largeimage':
		$devita_blogclass = 'blog-large';
		$devita_blogcolclass = 9;
		$devita_postthumb = '';
		break;
	default:
		$devita_blogclass = 'blog-nosidebar';
		$devita_blogcolclass = 12;
		$devita_blogsidebar = 'none';
		Devita_Class::devita_post_thumbnail_size('devita-post-thumb');
}
?>
<div class="main-container">
	<div class="blog-header-title">
		<div class="container">
			<div class="title-breadcrumb-inner">
				<?php Devita_Class::devita_breadcrumb(); ?>
				<header class="entry-header">
					<h1 class="entry-title"><?php if(isset($devita_opt)) { echo esc_html($devita_opt['blog_header_text']); } else { esc_html_e('Blog', 'devita');}  ?></h1>
				</header> 
			</div>
		</div>
	</div>
	<div class="container">
		<div class="row">
			
			<?php if($devita_blogsidebar=='left') : ?>
				<?php get_sidebar(); ?>
			<?php endif; ?>
			
			<div class="col-12 <?php if ( is_active_sidebar( 'sidebar-1' ) ) { echo 'col-lg-'.esc_attr($devita_blogcolclass);} ?>">
				<div class="page-content blog-page <?php echo esc_attr($devita_blogclass); if($devita_blogsidebar=='left') {echo ' left-sidebar'; } if($devita_blogsidebar=='right') {echo ' right-sidebar'; } ?>">
					<?php if ( have_posts() ) : ?>
						<header class="archive-header">
							<?php
								the_archive_title( '<h1 class="archive-title">', '</h1>' );
								the_archive_description( '<div class="archive-description">', '</div>' );
							?>
						</header>

						<?php
						/* Start the Loop */
						while ( have_posts() ) : the_post();

							/* Include the post format-specific template for the content. If you want to
							 * this in a child theme then include a file called called content-___.php
							 * (where ___ is the post format) and that will be used instead.
							 */
							get_template_part( 'content', get_post_format() );

						endwhile;
						?> 
					<div class="pagination">
						<?php Devita_Class::devita_pagination(); ?>
					</div>	
					<?php else : ?>
						<?php get_template_part( 'content', 'none' ); ?>
					<?php endif; ?>
				</div> 
			</div>
			<?php if( $devita_blogsidebar=='right') : ?>
				<?php get_sidebar(); ?>
			<?php endif; ?>
		</div>
	</div> 
</div>
<?php get_footer(); ?>