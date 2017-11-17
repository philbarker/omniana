<?php 
/**
 * The template for displaying description of a person
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
$schema_type = 'Person';
$VIAF_base = 'http://viaf.org/viaf/';
$ISNI_base = 'http://isni.org/isni/';
$term_md = get_term_meta( $term_id );
if (! empty($term_md['wd_birth_year'][0]) )
	$schema_birthyear = schema_prop('span', 'birthDate', 
	                                $term_md['wd_birth_year'][0]);
if (! empty($term_md['wd_birth_place'][0]) )
	$schema_birthplace = schema_prop('span', 'birthPlace', 
	                                $term_md['wd_birth_place'][0]);
if (! empty($term_md['wd_death_year'][0]) )
	$schema_deathyear = schema_prop('span', 'deathDate', 
	                                $term_md['wd_death_year'][0]);
if (! empty($term_md['wd_death_place'][0]) )
	$schema_deathplace = schema_prop('span', 'deathPlace', 
	                                $term_md['wd_death_place'][0]);
if (! empty($term_md['wd_VIAF'][0]) )
	$schema_VIAF = schema_prop('a', 'sameAs', $term_md['wd_VIAF'][0],
	                            $url = $VIAF_base.$term_md['wd_VIAF'][0]);
if (! empty($term_md['wd_ISNI'][0]) ) {
	$url =$ISNI_base.str_replace(' ', '', $term_md['wd_ISNI'][0]);
	$schema_ISNI = schema_prop('a', 'sameAs', $term_md['wd_ISNI'][0], $url);
}
	                               
// output title and description of person
if ( $schema_name ) {
	echo( '<h1 class="page-title">People Mentioned: '.$schema_name.'</h1>' );
} else {
	the_archive_title( '<h1 class="page-title">', '</h1>' );
}
if ( $schema_description ) {
	echo( '<p class="page-description">' );
	echo $schema_description;
	if ( $schema_birthyear || $schema_birthplace ) {
		echo '. Born ';
		echo $schema_birthyear;
		echo ' '.$schema_birthplace;
	}
	if ( $schema_deathyear || $schema_deathplace ) {
		echo '; died ';
		echo $schema_deathyear;
		echo ' '.$schema_deathplace;
	}
	echo( '.</p>' );
	if ( $schema_VIAF || $schame_ISNI) {
		echo '<ul>';
			if ( $schema_VIAF ) {
				echo '<li>VAIF: '; 
				echo $schema_VIAF.'. </li>';
			}
			if ( $schema_ISNI ) {
				echo '<li> ISNI: '; 
				echo $schema_ISNI.'. </li>';
			}
		echo '</ul>';
	}
} else {
	the_archive_description( '<p class="page-description">', '</p>' );
}
?>
