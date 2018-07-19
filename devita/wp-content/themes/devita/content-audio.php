<?php
/**
 * The template for displaying posts in the Audio post format
 *
 * Used for both single and index/archive/search.
 *
 * @package WordPress
 * @subpackage Devita_Theme
 * @since Devita 1.0
 */
$devita_opt = get_option( 'devita_opt' );

$devita_postthumb = Devita_Class::devita_post_thumbnail_size('');

if(Devita_Class::devita_post_odd_event() == 1){
	$devita_postclass='even';
} else {
	$devita_postclass='odd';
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class($devita_postclass); ?>> 
	<?php if ( ! post_password_required() && ! is_attachment() ) : ?>
	<?php 
		if ( is_single() ) { ?>
			<div class="post-thumbnail">
				<?php the_post_thumbnail(); ?> 
			</div>
			<div class="player"><?php echo do_shortcode(get_post_meta( $post->ID, '_devita_post_intro', true )); ?></div>
		<?php }
	?>
	<?php if ( !is_single() ) { ?>
		<?php if ( has_post_thumbnail() ) { ?>
		<div class="post-thumbnail">
			<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail($devita_postthumb); ?></a>
			 
		</div>
		<?php } ?>
	<?php } ?>
	<?php endif; ?>
	
	<div class="postinfo-wrapper <?php if ( !has_post_thumbnail() ) { echo 'no-thumbnail';} ?>">
		<header class="entry-header">
			<?php if ( is_single() ) : ?> 
				<h1 class="entry-title"><?php the_title(); ?></h1> 
			<?php else : ?> 
				<h2 class="entry-title">
					<a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
				</h2>
				 
			<?php endif; ?>
		</header>
		<div class="post-info"> 
			<?php if (is_home() && is_page_template('page-templates/front-page.php')){ ?>
				<header class="entry-header">  
					<h1 class="entry-title">
						<a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
					</h1> 
				</header>
			<?php }?>
			<?php if ( is_single() ) : ?>
				<div class="entry-content">
					<?php the_content( wp_kses(__( 'Continue reading <span class="meta-nav">&rarr;</span>', 'devita' ), array('span'=>array('class'=>array())) )); ?>
					<?php wp_link_pages( array( 'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'devita' ), 'after' => '</div>', 'pagelink' => '<span>%</span>' ) ); ?>
				</div>
			<?php else : ?>
				<div class="entry-summary">
					<div class="player"><?php echo do_shortcode(get_post_meta( $post->ID, '_devita_post_intro', true )); ?></div>
					<?php the_excerpt(); ?>
					<a class="readmore button" href="<?php the_permalink(); ?>"><?php if(isset($devita_opt['readmore_text']) && $devita_opt['readmore_text']!=''){ echo esc_html($devita_opt['readmore_text']); } else { esc_html_e('Read more', 'devita');}  ?></a>
					<?php if( function_exists('devita_blog_sharing') ) { ?>
						<div class="social-sharing"><?php devita_blog_sharing(); ?></div>
					<?php } ?>
					<div class="post-meta">
						<span class="post-date"> <?php echo get_the_date('', $post->ID);?> </span>
						<span class="post-author">
							<span class="post-by"><?php esc_html_e('Posts by', 'devita');?> : </span>
							<?php printf( get_the_author() ); ?>
						</span>
						<?php  $categories_list = get_the_category_list( ', ' ); 
							if($categories_list != '') { ?>
								<span class="post-category"> 
									<?php  echo wp_kses(($categories_list), array('a'=>array('href'=>array(), 'rel'=>array() ) ));?>
								</span>
						<?php } ?> 
					</div>
				</div>
			<?php endif; ?> 
			
			<?php if ( is_single() ) : ?>
				<div class="post-meta">
					<span class="post-date"> <?php echo get_the_date('', $post->ID);?> </span>
					<span class="post-author">
						<span class="post-by"><?php esc_html_e('Posts by', 'devita');?> : </span>
						<?php printf( get_the_author() ); ?>
					</span>
					<?php  $categories_list = get_the_category_list( ', ' ); 
						if($categories_list != '') { ?>
							<span class="post-category"> 
								<?php  echo wp_kses(($categories_list), array('a'=>array('href'=>array(), 'rel'=>array() ) ));?>
							</span>
					<?php } ?> 
				</div>
				<div class="entry-meta">
					<?php Devita_Class::devita_entry_meta(); ?>
				</div>
			
				<?php if( function_exists('devita_blog_sharing') ) { ?>
					<div class="social-sharing"><?php devita_blog_sharing(); ?></div>
				<?php } ?>
				<?php if(get_the_author_meta()!="") { ?>
				<div class="author-info">
					<div class="author-avatar">
						<?php
						$author_bio_avatar_size = apply_filters( 'devita_author_bio_avatar_size', 68 );
						echo get_avatar( get_the_author_meta( 'user_email' ), $author_bio_avatar_size );
						?>
					</div>
					<div class="author-description">
						<h2><?php esc_html_e( 'About the Author:', 'devita'); printf( '<a href="'.esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ).'" rel="author">%s</a>' , get_the_author()); ?></h2>
						<p><?php the_author_meta( 'description' ); ?></p>
					</div>
				</div>
				<?php } ?>
				<?php 
				//related posts
				$orig_post = $post;
				global $post;
				$tags = wp_get_post_tags($post->ID);

				if ($tags) { ?>
				<div class="relatedposts">
					<h3><?php esc_html_e('Related posts', 'devita');?></h3>
					<div class="row">
						<?php
						$tag_ids = array();
						foreach($tags as $individual_tag) $tag_ids[] = $individual_tag->term_id;
						$args=array(
						'tag__in' => $tag_ids,
						'post__not_in' => array($post->ID),
						'posts_per_page'=>3, // Number of related posts to display.
						'ignore_sticky_posts'=>1
						);
						 
						$my_query = new wp_query( $args );
					 
						while( $my_query->have_posts() ) {
							$my_query->the_post();
							?>
							<div class="relatedthumb col-md-4">
								<div class="image">
									<?php the_post_thumbnail('devita-post-thumb'); ?>
								</div> 
								<h4><a rel="external" href="<?php the_permalink()?>"><?php the_title(); ?></a></h4>
								<span class="post-date">
									<?php echo get_the_date(get_option('date_format'), $post->ID) ;?>
								</span>
							</div>
						<?php }
						
						$post = $orig_post;
						wp_reset_postdata();
						?>
					</div> 
				</div>
				<?php } ?>
			<?php endif; ?>
		</div>
	</div>
</article>