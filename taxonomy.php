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
$schema_type = 'Thing';
$term_id = get_queried_object_id();
$wd_url = schema_thing_terms( $term_id )['wd_url'];
// schema properties for each specific type
$taxonomy= get_query_var( 'taxonomy' );
if ( 'people' === $taxonomy ) $schema_type = 'Person';
if ( 'places' === $taxonomy ) $schema_type = 'Place';
if ( 'events' === $taxonomy ) $schema_type = 'Event';
if ( 'works' === $taxonomy ) $schema_type = 'CreativeWork';

get_header(); ?>
<?php if ( have_posts() ) : ?>
<div vocab="http://schema.org/" 
	 <?php if ($wd_url) : ?>resource="<?php echo($wd_url); ?>"<?php endif; ?>
     typeof="<?php echo($schema_type); ?>">
	<header class="archive-header">
	<?php
	if ( 'people' === $taxonomy ) {
		get_template_part('content', 'person-description');
	} elseif ( 'works' === $taxonomy ) {
		get_template_part('content', 'work-description');
	} else {
		the_archive_title( '<h1 class="page-title">', '</h1>' );
		the_archive_description( '<p class="page-description">', '</p>' );
	}
	?>
	</header>
	<main id="main" class="taxonomy-main">
		<p>Mentioned in the following articles.</p>
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
</div>
<?php else : // no posts
?> 
	<header class="archive-header">
	<?php
		the_archive_title( '<h1 class="page-title">', '</h1>' );
		the_archive_description( '<p class="page-description">', '</p>' );
	?>
	</header>
	<main id="main" class="taxonomy-main">
		<p>Sorry, there is no content for this archive</p>
	</main>	
<?php endif; ?>
<?php get_footer(); ?>
