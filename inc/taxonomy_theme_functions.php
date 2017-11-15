<?php
/**
 * Helper functions used in themes to display taxonomies & terms
 *
 *
 * @package WordPress
 * @subpackage omniana
 * @since Omniana 0.1
 * @version 0.1
 */
defined( 'ABSPATH' ) or die( 'Be good. If you can\'t be good be careful' );

function schema_prop($tag, $property, $value) {
// return a value wrapped in a HTML tag as a schema property
	if (('div' !== $tag) && ('span' !== $tag) )
		return '$tag must be div or span';
	if ( !( $property  &&  $value ) ) 
		return 'must provide values for property and value';
	return '<'.$tag.' property="'.$property.'">'.$value.'</'.$tag.'>';
}

function schema_thing_terms( $term_id ) {
// schema properties for any Thing
	if ( empty($term_id) ) return 'no Thing to work with';
	$term_md = get_term_meta( $term_id );
	$wd_base = 'https://www.wikidata.org/entity/';
	if (! empty($term_md['wd_id'][0])) { 
		$wd_url = $wd_base.$term_md['wd_id'][0];
	} else {
		$wd_url = false;
	}
	if (! empty($term_md['wd_description'][0]) ) {
		$schema_description = schema_prop('span', 'description', 
	                                  $term_md['wd_description'][0]);
	} else {
		$schema_description = false;
	}
	if (! empty($term_md['wd_name'][0]) ) {
		$schema_name = schema_prop('span', 'name', $term_md['wd_name'][0]);
	} else {
		$schema_name = false;
	}
	$terms = array(
				'wd_url' => $wd_url,
				'schema_name' => $schema_name,
				'schema_description' => $schema_description
			);
	return $terms;
}
