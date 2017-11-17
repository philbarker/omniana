<?php 
/**
 * The template for displaying description of a creative work
 *
 * used in archives for  people custom taxonomy terms
 *
 * @package WordPress
 * @subpackage omniana
 * @since Omniana 0.1
 * @version 0.1
 */
 
$term_id = get_queried_object_id();
$schema_terms = schema_thing_terms( $term_id );
$schema_name = $schema_terms['schema_name'];
$schema_description = $schema_terms['schema_description'];

// schema properties for Person
$schema_type = 'CreativeWork';
$term_md = get_term_meta( $term_id );
// output title and description of person
if ( $schema_name ) {
	echo( '<h1 class="page-title">People Mentioned: '.$schema_name.'</h1>' );
} else {
	the_archive_title( '<h1 class="page-title">', '</h1>' );
}
if ( $schema_description ) {
	echo( '<p class="page-description">' );
	echo $schema_description;
	echo( '.</p>' );
} else {
	the_archive_description( '<p class="page-description">', '</p>' );
}
?>
