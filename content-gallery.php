	<article id="post-<?php the_ID(); ?>" <?php post_class('gallery'); ?>>
		<div class="entry-content">
			<h1 class="entry-title">
				<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'via' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark">
					<?php the_title(); ?>
				</a>
			</h1>
			<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'via' ) ); ?>
		</div><!-- .entry-content .image-post -->

		<footer class="entry-meta">
			<span class="format-link">
				<a href="<?php echo esc_url( get_post_format_link( get_post_format() ) ); ?>" title="<?php echo esc_attr( sprintf( __( 'All %s posts', 'cleanretina' ), get_post_format_string( get_post_format() ) ) ); ?>">
					<?php echo get_post_format_string( get_post_format() ); ?>
				</a>
			</span>

			<?php if ( 'post' == get_post_type() ) : // Hide category and tag text for pages on Search ?>
				<?php
					/* translators: used between list items, there is a space after the comma */
					$categories_list = get_the_category_list( __( ', ', 'via' ) );
					if ( $categories_list && via_categorized_blog() ) :
				?>
				<span class="cat-links">
					<?php printf( __( 'Posted in %1$s', 'via' ), $categories_list ); ?>
				</span>
				<?php endif; // End if categories ?>

				<?php
					/* translators: used between list items, there is a space after the comma */
					$tags_list = get_the_tag_list( '', __( ', ', 'via' ) );
					if ( $tags_list ) :
				?>
				<span class="tag-links">
					<?php printf( __( 'Tagged %1$s', 'via' ), $tags_list ); ?>
				</span>
				<?php endif; // End if $tags_list ?>
			<?php endif; // End if 'post' == get_post_type() ?>

			<?php if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ) : ?>
				<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'via' ), __( '1 Comment', 'via' ), __( '% Comments', 'via' ) ); ?></span>
			<?php endif; ?>
		</footer><!-- .entry-meta -->
	</article><!-- #post -->