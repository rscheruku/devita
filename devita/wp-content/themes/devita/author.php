<?php
/**
 * The template for displaying Author Archive pages
 *
 * Used to display archive-type pages for posts by an author.
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

						<?php
							/* Queue the first post, that way we know
							 * what author we're dealing with (if that is the case).
							 *
							 * We reset this later so we can run the loop
							 * properly with a call to rewind_posts().
							 */
							the_post();
						?>

						<header class="archive-header">
							<h1 class="archive-title"><?php printf( esc_html__( 'Author Archives: %s', 'devita' ), '<span class="vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( "ID" ) ) ) . '" title="' . esc_attr( get_the_author() ) . '" rel="me">' . get_the_author() . '</a></span>' ); ?></h1>
						</header><!-- .archive-header -->

						<?php
							/* Since we called the_post() above, we need to
							 * rewind the loop back to the beginning that way
							 * we can run the loop properly, in full.
							 */
							rewind_posts();
						?>

						<?php
						// If a user has filled out their description, show a bio on their entries.
						if ( get_the_author_meta( 'description' ) ) : ?>
						<div class="author-info archives">
							<div class="author-avatar">
								<?php
								/**
								 * Filter the author bio avatar size.
								 *
								 * @since Devita 1.0
								 *
								 * @param int $size The height and width of the avatar in pixels.
								 */
								$author_bio_avatar_size = apply_filters( 'devita_author_bio_avatar_size', 68 );
								echo get_avatar( get_the_author_meta( 'user_email' ), $author_bio_avatar_size );
								?>
							</div><!-- .author-avatar -->
							<div class="author-description">
								<h2><?php printf( esc_html__( 'About %s', 'devita' ), get_the_author() ); ?></h2>
								<p><?php the_author_meta( 'description' ); ?></p>
							</div><!-- .author-description	-->
						</div><!-- .author-info -->
						
						<?php endif; ?>

						<?php /* Start the Loop */ ?>
						<?php while ( have_posts() ) : the_post(); ?>
							<?php get_template_part( 'content', get_post_format() ); ?>
						<?php endwhile; ?> 
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