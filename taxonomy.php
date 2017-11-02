<?php 
/**
 * The template for displaying Archive pages
 *
 * used to display archives for custom taxonomy terms, e.g. 
 * people, places, works.
 *
 * @package WordPress
 * @subpackage omniana
 * @since Omniana 0.1
 * @version 0.1
 */
 
get_header(); ?>
<?php if ( have_posts() ) : ?>
	<header class="archive-header">
	<?php
		the_archive_title( '<h1 class="page-title">', '</h1>' );
		the_archive_description( '<p class="page-description">', '</p>' );
		$tid = get_queried_object_id();
		$tax = get_term_meta( $tid );
		if (! empty($tax['wikidata_uri'][0])) : 
			echo( '<p>Wikidata URI: '.$tax['wikidata_uri'][0].'</p>' );
		endif;
		echo( '<p>Mentioned in the following articles.</p>' );
	?>
	</header>
	<main id="main" class="taxonomy-main">
	<?php
		/* start the loop */
		while ( have_posts() ) : the_post();
			$before = '<h2 class="entry-title"><a href="'.
				esc_url(get_permalink() ).
				'">';
			$after = '</a></h2>';
			the_title($before, $after);
			the_excerpt();
		endwhile;
	?>
	</main>
<?php else : ?>
	<header class="archive-header">
	<?php
		the_archive_title( '<h1 class="page-title">', '</h1>' );
		the_archive_description( '<p class="page-description">', '</p>' );
		$tid = get_queried_object_id();
		$tax = get_term_meta( $tid );
		if (! empty($tax['wikidata_uri'][0])) : 
			echo( '<p>Wikidata URI: '.$tax['wikidata_uri'][0].'</p>' );
		endif;
	?>
	</header>
	<main id="main" class="taxonomy-main">
		<p>Sorry, there is no content for this archive</p>
	</main>	
<?php endif; ?>
<?php get_footer(); ?>
