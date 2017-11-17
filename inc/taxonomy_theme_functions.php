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

function schema_prop($tag, $property, $value='', $url='') {
// return a value wrapped in a HTML tag as a schema property
	$acceptable_tags = array (
			'a', 'abbr', 'addr', 'article', 'aside', 'b', 'blockquote', 'body', 
			'caption', 'cite', 'dd', 'dfn', 'div', 'dl', 'dt', 'em', 'embed',
			'figcaption', 'figure', 'footer', 'h1', 'h2', 'h3', 'h4','h5','h6', 
			'header', 'i', 'img', 'li', 'link', 'main', 'map', 'mark', 'menu',
			'menuitem', 'nav', 'object', 'ol', 'output', 'p', 'param','picture',
			'pre', 'q', 'samp', 'section', 'small', 'source', 'span', 'strong',
			'sub', 'summary', 'sup', 'table', 'tbody', 'td', 'textarea','tfoot',
			'th', 'thead', 'time', 'title', 'tr', 'tracks', 'u', 'ul', 'var',
			'video'
		);
	if ( !( $property  &&  ($value || $url) ) ) 
		return 'must provide values for property and value or url';
	if ( in_array($tag, $acceptable_tags) ) {
		$tag = strtolower( $tag );
		if ('link' === $tag) {
			if (!$url) 
				return ' Warning: need a url for a link. ';
			else 
				$url = esc_url( $url );
			return '<link property="'.$property.'" href="'.$url.'"/>';
		} elseif ('a' === $tag) {
			$url = esc_url( $url );
			return '<a property="'.$property.'" href="'.$url.'">'.$value.'</a>';
		} else {
			return '<'.$tag.' property="'.$property.'">'.$value.'</'.$tag.'>';
		}
	} else { 
		return '$tag not suitable HTML element tag';
	}
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

function posts_by_taxon ( $atts ) {
	$results = '<div><p>';

	$taxonomies = get_taxonomies();
	
	if ( array_key_exists( 'debug', $atts ) )
	{
		$debug = ('true' === $atts['debug']);
	} else {
		$debug = false;
	}

	$tax_queries = array();
	foreach ($taxonomies as $taxonomy) {
		if ( array_key_exists( $taxonomy, $atts ) ) {
			if ($debug) {
				$results = $results.'Posts by taxon: '.$taxonomy.' = '.$atts[$taxonomy];
			}
			$tax_query = array(
				'taxonomy' => $taxonomy,
				'field' => 'slug',
				'terms' => $atts[$taxonomy]
			);
			$tax_queries[] = $tax_query;
		}
	}
	$results = $results.'</p>';

	if ( array_key_exists( 'type', $atts ) )
	{
		$post_type = $atts['type'];
	} else {
		$post_type = get_post_types();
	}		

	if ( array_key_exists( 'orderby', $atts ) )
	{
		$orderby = $atts['orderby'];
		if ( array_key_exists( 'order', $atts ) )
		{
			$order = strtoupper( $atts['order'] );
		} else {
			$order = 'ASC';
		}
	} else {
		$orderby = null;
		$order = null;
	}
	$query = array(
		'tax_query'   => $tax_queries,
		'posts_per_page' => -1,
		'post_type'   => $post_type,
		'orderby'     => $orderby,
		'order'       => $order,
		'post_status' => 'publish'
		);
	$posts_array = get_posts( $query );
//	print_r( $posts_array );
	foreach ( $posts_array as $post ) 	{
		$url = esc_url( get_permalink( $post->ID ) );
		$title = $post->post_title;
		$linkitem = '<li><a href="'.$url.'">'.$title.'</a></li>';
		$results = $results.$linkitem;
	}
	$results = $results.'</ul></div>';
	return $results;
}
add_shortcode( 'posts_by_taxon', 'posts_by_taxon' );

function list_taxa( $atts ) {
	if ( array_key_exists( 'debug', $atts ) ) {
		$debug = ('true' === $atts['debug']);
	} else {
		$debug = false;
	}
	if ( array_key_exists( 'taxonomy', $atts ) ) {
		$taxonomy = $atts['taxonomy'];
	} else {
		return('error: use taxonomy="taxonomyname" parameter');
	}
	if ( array_key_exists( 'orderby', $atts ) )	{
		$orderby = $atts['orderby'];
		if ( array_key_exists( 'order', $atts ) )
		{
			$order = strtoupper( $atts['order'] );
		} else {
			$order = 'ASC';
		}
	} else {
		$orderby = 'name';
		$order = 'ASC';
	}
	if ( get_taxonomy( $taxonomy ) ) {
		$query = array(
			'taxonomy' => $taxonomy,
			'orderby' => $orderby,
			'order' => $order,
		);
		if ($debug) print_r( $query );
		$terms = get_terms( $query);
	} else {
		return('<p>error: taxonomy "'.$taxonomy.'" does not exist</p>');
	}
	if (empty ( $terms ) ) {
		return('<p>Taxonomy "'.$taxonomy.'" has no members</p>');
	} else {
		$results = "<ul>";
		foreach ( $terms as $term ) {
			if ($debug) {
				echo('<p>');
				print_r( $term );
				echo('<\p>');
			}
			$name = $term->name;
			$slug = $term->slug;
			$url = get_term_link( $slug, $taxonomy );
			$link = '<a href="'.$url.'">'.$name.'</a>';
			$results = $results.'<li>'.$link.'</li>';
		}
		$results = $results.'</ul>';		
		return( $results );
	}
}
add_shortcode( 'list_taxa', 'list_taxa' );
