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
 */
 
get_header(); ?>
<?php if ( have_posts() ) : ?>
	<header class="archive-header">
	<?php
		the_archive_title( '<h1 class="page-title">', '</h1>' ); 
		the_archive_description( '<p class="page-description">', '</p>' );
		$tid = get_queried_object_id();
		$tax = get_term_meta( $tid );
		echo( '<p>Wikidata URI: '.$tax['wikidata_uri'][0].'</p>' );
	?>
	</header>
<?php else : ?>
	<?php get_template_part( 'content', 'none' ); ?>
<?php endif; ?>
<?php get_footer(); ?>
