<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * For example, it puts together the home page when no home.php file exists.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Devita_Theme
 * @since Devita 1.0
 */

$devita_opt = get_option( 'devita_opt' );

get_header();

$devita_bloglayout = 'sidebar';

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
		Devita_Class::devita_post_thumbnail_size('devita-category-thumb');
		break;
	case 'grid':
		$devita_blogclass = 'grid';
		$devita_blogcolclass = 9;
		Devita_Class::devita_post_thumbnail_size('devita-category-thumb');
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

						<?php /* Start the Loop */ ?>
						<?php while ( have_posts() ) : the_post(); ?>
							
							<?php get_template_part( 'content', get_post_format() ); ?>
							
						<?php endwhile; ?> 
						<div class="pagination">
							<?php Devita_Class::devita_pagination(); ?>
						</div>
					<?php else : ?>

						<article id="post-0" class="post no-results not-found">

						<?php if ( current_user_can( 'edit_posts' ) ) :
							// Show a different message to a logged-in user who can add posts.
						?>
							<header class="entry-header">
								<h1 class="entry-title"><?php esc_html_e( 'No posts to display', 'devita' ); ?></h1>
							</header>

							<div class="entry-content">
								<p><?php printf( wp_kses(__( 'Ready to publish your first post? <a href="%s">Get started here</a>.', 'devita' ), array('a'=>array('href'=>array()))), admin_url( 'post-new.php' ) ); ?></p>
							</div><!-- .entry-content -->

						<?php else :
							// Show the default message to everyone else.
						?>
							<header class="entry-header">
								<h1 class="entry-title"><?php esc_html_e( 'Nothing Found', 'devita' ); ?></h1>
							</header>

							<div class="entry-content">
								<p><?php esc_html_e( 'Apologies, but no results were found. Perhaps searching will help find a related post.', 'devita' ); ?></p>
								<?php get_search_form(); ?>
							</div><!-- .entry-content -->
						<?php endif; // end current_user_can() check ?>

						</article><!-- #post-0 -->

					<?php endif; // end have_posts() check ?>
				</div> 
			</div>
			<?php if( $devita_blogsidebar=='right') : ?>
				<?php get_sidebar(); ?>
			<?php endif; ?>
		</div>
	</div> 
</div>
<?php get_footer(); ?>