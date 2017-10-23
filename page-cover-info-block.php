<section id="post-<?php the_ID(); ?>" <?php post_class( [ 'top-block', 'clearfix', 'home-post' ] ); ?>>

	<?php pb_get_links( false ); ?>
	<?php $metadata = pb_get_book_information();?>
	<div class="log-wrap">	<!-- Login/Logout -->
		<?php if ( ! is_single() ) : ?>
			<?php if ( ! is_user_logged_in() ) : ?>
				<a href="<?php echo wp_login_url( get_permalink() ); ?>" class=""><?php _e( 'login', 'pressbooks-book' ); ?></a>
				<?php else : ?>
				<a href="<?php echo  wp_logout_url(); ?>" class=""><?php _e( 'logout', 'pressbooks-book' ); ?></a>
				<?php if ( is_super_admin() || is_user_member_of_blog() ) : ?>
				<a href="<?php echo get_option( 'home' ); ?>/wp-admin"><?php _e( 'Admin', 'pressbooks-book' ); ?></a>
				<?php endif; ?>
			<?php endif; ?>
		<?php endif; ?>
	</div>
	<div class="right-block">
		<?php do_action( 'pb_cover_promo' ); ?>
	</div>

			<div class="book-info">
				<!-- Book Title -->
				<h1 class="entry-title"><a href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>


				<?php if ( ! empty( $metadata['pb_author'] ) ) : ?>
				<p class="book-author vcard author"><span class="fn"><?php echo $metadata['pb_author']; ?></span></p>
					 <span class="stroke"></span>
				<?php endif; ?>

				<?php if ( ! empty( $metadata['pb_contributing_authors'] ) ) : ?>
					<p class="book-author"><?php echo $metadata['pb_contributing_authors']; ?> </p>
					<?php endif; ?>


				<?php if ( ! empty( $metadata['pb_subtitle'] ) ) : ?>
					<p class="sub-title"><?php echo $metadata['pb_subtitle']; ?></p>
					<span class="detail"></span>
				<?php endif; ?>

				<?php if ( ! empty( $metadata['pb_about_50'] ) ) : ?>
					<p><?php echo pb_decode( $metadata['pb_about_50'] ); ?></p>
				<?php endif; ?>

			</div> <!-- end .book-info -->

				<?php if ( ! empty( $metadata['pb_cover_image'] ) ) : ?>
				<div class="book-cover">

						<img src="<?php echo $metadata['pb_cover_image']; ?>" alt="book-cover" title="<?php bloginfo( 'name' ); ?> book cover" />

				</div>
				<?php endif; ?>

				<div class="third-block clearfix">
				<h2><?php _e( 'Contents', 'pressbooks-book' ); ?></h2>
				<?php $book = pb_get_book_structure(); ?>
					<ul class="table-of-content" id="table-of-content">
						<li>
							<ul class="front-matter">
								<?php foreach ( $book['front-matter'] as $fm ) : ?>
								<?php if ( $fm['post_status'] !== 'publish' ) {
									if ( ! current_user_can_for_blog( $blog_id, 'read_private_posts' ) ) {
										if ( current_user_can_for_blog( $blog_id, 'read' ) ) {
											if ( absint( get_option( 'permissive_private_content' ) ) !== 1 ) { continue; // Skip
											}
										} elseif ( ! current_user_can_for_blog( $blog_id, 'read' ) ) {
											 continue; // Skip
										}
									}
} ?>
								<li class="front-matter <?php echo pb_get_section_type( get_post( $fm['ID'] ) ) ?>"><a href="<?php echo get_permalink( $fm['ID'] ); ?>"><?php echo pb_strip_br( $fm['post_title'] );?></a>
					<?php if ( pb_should_parse_subsections() ) {
						$sections = pb_get_subsections( $fm['ID'] );
						if ( $sections ) {
							  $s = 1; ?>
							  <ul class="sections">
								<?php foreach ( $sections as $id => $name ) { ?>
						  <li class="section"><a href="<?php echo get_permalink( $fm['ID'] ); ?>#<?php echo $id; ?>"><?php echo $name; ?></a></li>
						<?php } ?>
							  </ul>
							<?php }
} ?>
									</li>
								<?php endforeach; ?>
							</ul>
						</li>
							<?php foreach ( $book['part'] as $part ) :?>
							<li><h4><?php if ( count( $book['part'] ) > 1  && get_post_meta( $part['ID'], 'pb_part_invisible', true ) !== 'on' ) { ?>
							<?php if ( $part['has_post_content'] ) { ?><a href="<?php echo get_permalink( $part['ID'] ); ?>"><?php } ?>
							<?php echo $part['post_title']; ?>
							<?php if ( $part['has_post_content'] ) { ?></a><?php } ?>
							<?php } ?></h4></li>
							<li>
								<ul>
									<?php foreach ( $part['chapters'] as $chapter ) : ?>
										<?php if ( $chapter['post_status'] !== 'publish' ) {
											if ( ! current_user_can_for_blog( $blog_id, 'read_private_posts' ) ) {
												if ( current_user_can_for_blog( $blog_id, 'read' ) ) {
													if ( absint( get_option( 'permissive_private_content' ) ) !== 1 ) { continue; // Skip
													}
												} elseif ( ! current_user_can_for_blog( $blog_id, 'read' ) ) {
													 continue; // Skip
												}
											}
} ?>
										<li class="chapter <?php echo pb_get_section_type( get_post( $chapter['ID'] ) ) ?>"><a href="<?php echo get_permalink( $chapter['ID'] ); ?>"><?php echo pb_strip_br( $chapter['post_title'] ); ?></a>
						<?php if ( pb_should_parse_subsections() ) {
							$sections = pb_get_subsections( $chapter['ID'] );
							if ( $sections ) {
								  $s = 1; ?>
								  <ul class="sections">
									<?php foreach ( $sections as $id => $name ) { ?>
							  <li class="section"><a href="<?php echo get_permalink( $chapter['ID'] ); ?>#<?php echo $id; ?>"><?php echo $name; ?></a></li>
							<?php } ?>
								  </ul>
								<?php }
} ?>
										</li>
									<?php endforeach; ?>
								</ul>
							</li>
							<?php endforeach; ?>
							<li><h4><!-- Back-matter --></h4></li>
							<li>
								<ul class="back-matter">
									<?php foreach ( $book['back-matter'] as $bm ) : ?>
									<?php if ( $bm['post_status'] !== 'publish' ) {
										if ( ! current_user_can_for_blog( $blog_id, 'read_private_posts' ) ) {
											if ( current_user_can_for_blog( $blog_id, 'read' ) ) {
												if ( absint( get_option( 'permissive_private_content' ) ) !== 1 ) { continue; // Skip
												}
											} elseif ( ! current_user_can_for_blog( $blog_id, 'read' ) ) {
												 continue; // Skip
											}
										}
} ?>
									<li class="back-matter <?php echo pb_get_section_type( get_post( $bm['ID'] ) ) ?>"><a href="<?php echo get_permalink( $bm['ID'] ); ?>"><?php echo pb_strip_br( $bm['post_title'] );?></a>
					<?php if ( pb_should_parse_subsections() ) {
						$sections = pb_get_subsections( $bm['ID'] );
						if ( $sections ) {
							$s = 1; ?>
							<ul class="sections">
								<?php foreach ( $sections as $id => $name ) { ?>
							<li class="section"><a href="<?php echo get_permalink( $bm['ID'] ); ?>#<?php echo $id; ?>"><?php echo $name; ?></a></li>
							<?php } ?>
							</ul>
							<?php }
} ?>
									</li>
									<?php endforeach; ?>
								</ul>
							</li>
					</ul><!-- end #toc -->

				</div><!-- end .third-block -->




				<?php
				 /**
					* @author Brad Payne <brad@bradpayne.ca>
					* @copyright 2014 Brad Payne
					* @since 3.8.0
					*/

					$files = \Pressbooks\Utility\latest_exports();
					$site_option = get_site_option( 'pressbooks_sharingandprivacy_options', [ 'allow_redistribution' => 0 ] );
					$option = get_option( 'pbt_redistribute_settings', [ 'latest_files_public' => 0 ] );
				if ( ! empty( $files ) && ( ! empty( $site_option['allow_redistribution'] ) ) && ( ! empty( $option['latest_files_public'] ) ) ) { ?>
						<div class="downloads">
							<h4><?php _e( 'Download in the following formats:', 'pressbooks-book' ); ?></h4>
							<?php foreach ( $files as $filetype => $filename ) :
								$filename = preg_replace( '/(-\d{10})(.*)/ui', '$1', $filename );

								// Rewrite rule
								$url = home_url( "/open/download?type={$filetype}" );

								// Tracking event defaults to Google Analytics (Universal). @codingStandardsIgnoreStart
								// Filter like so (for Piwik):
								// add_filter('pressbooks_download_tracking_code', function( $tracking, $filetype ) {
								//  return "_paq.push(['trackEvent','exportFiles','Downloads','{$filetype}']);";
								// }, 10, 2);
								// Or for Google Analytics (Classic):
								// add_filter('pressbooks_download_tracking_code', function( $tracking, $filetype ) {
								//  return "_gaq.push(['_trackEvent','exportFiles','Downloads','{$file_class}']);";
								// }, 10, 2); @codingStandardsIgnoreEnd
								$tracking = apply_filters( 'pressbooks_download_tracking_code', "ga('send','event','exportFiles','Downloads','{$filetype}');", $filetype );
							?>
								<link itemprop="bookFormat" href="http://schema.org/EBook">
									<a rel="nofollow" onclick="<?php echo $tracking; ?>" itemprop="offers" itemscope itemtype="http://schema.org/Offer" href="<?php echo $url; ?>">
										<span class="export-file-icon small <?php echo $filetype; ?>" title="<?php echo esc_attr( $filename ); ?>"></span>
										<meta itemprop="price" content="$0.00">
										<link itemprop="availability" href="http://schema.org/InStock">
									</a>
							<?php endforeach; ?>
						</div>
					<?php }
				?>


	</section> <!-- end .top-block -->
