<?php
define('PB_HIDE_COVER_PROMO', true);

function omniana_enqueue_styles() {

    $parent_style = 'parent-style'; // This is 'twentyfifteen-style' for the Twenty Fifteen theme.

    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get('Version')
    );
}
add_action( 'wp_enqueue_scripts', 'omniana_enqueue_styles' );

function register_omniana_taxonomies() {
	$people_args = array(
    	'labels' => array (
	    	'name'          => 'people mentioned',
   			'singular name' => 'person mentioned',
    		'menu_name'     => 'people index'
    		),
    	'public'       => true,
    	'hierarchical' => false,
    	'show_admin_column' => true,
	    'rewrite'      => array( 'slug' => 'people' ),
	    'description'  => 'Indexing people who are mentioned in articles',
	    'sort'         => true
	    );
	register_taxonomy( 'people', 'chapter', $people_args );

	$place_args = array(
    	'labels' => array (
	    	'name'          => 'place mentioned',
   			'singular name' => 'place mentioned',
    		'menu_name'     => 'places index'
    		),
    	'public'       => true,
    	'hierarchical' => false,
    	'show_admin_column' => true,
	    'rewrite'      => array( 'slug' => 'places' ),
	    'description'  => 'Indexing places who are mentioned in articles',
	    'sort'         => true
	    );
	register_taxonomy( 'places', 'chapter', $place_args );

	$event_args = array(
    	'labels' => array (
	    	'name'          => 'event mentioned',
   			'singular name' => 'event mentioned',
    		'menu_name'     => 'events index'
    		),
    	'public'       => true,
    	'hierarchical' => false,
    	'show_admin_column' => true,
	    'rewrite'      => array( 'slug' => 'events' ),
	    'description'  => 'Indexing events who are mentioned in articles',
	    'sort'         => true
	    );
	register_taxonomy( 'events', 'chapter', $event_args );

	$work_args = array(
    	'labels' => array (
	    	'name'          => 'works mentioned',
   			'singular name' => 'work mentioned',
    		'menu_name'     => 'works index'
    		),
    	'public'       => true,
    	'hierarchical' => false,
    	'show_admin_column' => true,
	    'rewrite'      => array( 'slug' => 'works' ),
	    'description'  => 'Indexing works who are mentioned in articles',
	    'sort'         => true
	    );
	register_taxonomy( 'works', 'chapter', $work_args );

}
add_action( 'init', 'register_omniana_taxonomies');

function omniana_taxonomies_add_fields( $taxonomy ) {
    ?>
    <div class="form-field term-group">
        <label for="wikidata_uri"><?php _e( 'Wikidata URI', 'omniana' ); ?></label>
        <input type="url" id="wikidata_uri" name="wikidata_uri" />
    </div>
    <?php
}
add_action( 'people_add_form_fields', 'omniana_taxonomies_add_fields', 10, 2 );
add_action( 'places_add_form_fields', 'omniana_taxonomies_add_fields', 10, 2 );
add_action( 'events_add_form_fields', 'omniana_taxonomies_add_fields', 10, 2 );
add_action( 'works_add_form_fields', 'omniana_taxonomies_add_fields', 10, 2 );

function omniana_taxonomies_edit_meta_fields( $term, $taxonomy ) {
    $wikidata_uri = get_term_meta( $term->term_id, 'wikidata_uri', true );
    if ('' === $wikidata_uri) {
    	$wikidata_uri = 'https://www.wikidata.org/wiki/Q';
    }
    ?>
    <tr class="form-field term-group-wrap">
        <th scope="row">
            <label for="wikidata_uri"><?php _e( 'Wikidata URI', 'omniana' ); ?></label>
        </th>
        <td>
            <input type="url" id="wikidata_uri" name="wikidata_uri" value="<?php echo $wikidata_uri; ?>" />
        </td>
    </tr>
    <?php
}
add_action( 'people_edit_form_fields', 'omniana_taxonomies_edit_meta_fields', 10, 2 );
add_action( 'places_edit_form_fields', 'omniana_taxonomies_edit_meta_fields', 10, 2 );
add_action( 'events_edit_form_fields', 'omniana_taxonomies_edit_meta_fields', 10, 2 );
add_action( 'works_edit_form_fields', 'omniana_taxonomies_edit_meta_fields', 10, 2 );

function omniana_taxonomies_save_taxonomy_meta( $term_id, $tag_id ) {
    if( isset( $_POST['wikidata_uri'] ) ) {
        update_term_meta( $term_id, 'wikidata_uri', esc_attr( $_POST['wikidata_uri'] ) );
    }
}
add_action( 'created_people', 'omniana_taxonomies_save_taxonomy_meta', 10, 2 );
add_action( 'edited_people', 'omniana_taxonomies_save_taxonomy_meta', 10, 2 );
add_action( 'created_places', 'omniana_taxonomies_save_taxonomy_meta', 10, 2 );
add_action( 'edited_places', 'omniana_taxonomies_save_taxonomy_meta', 10, 2 );
add_action( 'created_events', 'omniana_taxonomies_save_taxonomy_meta', 10, 2 );
add_action( 'edited_events', 'omniana_taxonomies_save_taxonomy_meta', 10, 2 );
add_action( 'created_works', 'omniana_taxonomies_save_taxonomy_meta', 10, 2 );
add_action( 'edited_works', 'omniana_taxonomies_save_taxonomy_meta', 10, 2 );

#flush_rewrite_rules(); 
?>
