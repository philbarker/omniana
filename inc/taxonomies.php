<?php
function register_omniana_taxonomy($taxonomy, $type, $s_tag, $p_tag) {
	$args = array(
    	'labels' => array (
	    	'name'          => $p_tag.' mentioned',
   			'singular name' => $s_tag.' mentioned',
    		'menu_name'     => $p_tag.' index'
    		),
    	'public'       => true,
    	'hierarchical' => false,
    	'show_admin_column' => true,
    	'show_in_menu' => false,
	    'rewrite'      => array( 'slug' => $p_tag ),
	    'description'  => 'Indexing '.$p_tag.' who are mentioned in articles',
	    'sort'         => true
	    );
	register_taxonomy( $taxonomy, $type, $args );
}

function register_omniana_taxonomies() {
	register_omniana_taxonomy('people', 'chapter', 'person', 'people');
	register_omniana_taxonomy('places', 'chapter', 'place', 'places');
	register_omniana_taxonomy('events', 'chapter', 'event', 'events');
	register_omniana_taxonomy('works', 'chapter', 'work', 'works');
}
add_action( 'init', 'register_omniana_taxonomies');

function omniana_add_taxonomy_admin_submenu($taxonomy, $title, $type) {
	add_submenu_page(  
			'taxonomies',
			'',
			$title,
			'',
			'edit-tags.php?taxonomy='.$taxonomy.'&post_type='.$type,
			null
		);
}

function omniana_add_admin_menus() {
	$page_title = 'Taxonmies';
	$menu_title = 'Taxonmies';
	$capability = 'post';
	$menu_slug  = 'taxonomies';
	$function   = 'omniana_display_admin_page';// Callback function which displays the page content.
	$icon_url   = 'dashicons-admin-page';
	$position   = 10;
	
	add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
	omniana_add_taxonomy_admin_submenu('people', 'people mentioned', 'chapter');
	omniana_add_taxonomy_admin_submenu('events', 'events mentioned', 'chapter');
	omniana_add_taxonomy_admin_submenu('places', 'places mentioned', 'chapter');
	omniana_add_taxonomy_admin_submenu('works',  'works mentioned', 'chapter');
}
add_action( 'admin_menu', 'omniana_add_admin_menus', 1 );

function omniana_display_admin_page() {
        # Display custom admin page content from newly added custom admin menu.
	echo '<div class="wrap">' . PHP_EOL;
	echo '<h2>Index Taxonomies</h2>' . PHP_EOL;
	echo '<p>Choose taxonomy to edit.</p>' . PHP_EOL;
	echo '</div><!-- end .wrap -->' . PHP_EOL;
	echo '<div class="clear"></div>' . PHP_EOL;
}


function omniana_taxonomies_add_fields( $taxonomy ) {
    ?>
    <div class="form-field term-group">
        <label for="wikidata_id"><?php _e( 'Wikidata ID', 'omniana' ); ?></label>
        <input type="url" id="wikidata_id" name="wikidata_id" />
    </div>
    <?php
}
add_action( 'people_add_form_fields', 'omniana_taxonomies_add_fields', 10, 2 );
add_action( 'places_add_form_fields', 'omniana_taxonomies_add_fields', 10, 2 );
add_action( 'events_add_form_fields', 'omniana_taxonomies_add_fields', 10, 2 );
add_action( 'works_add_form_fields',  'omniana_taxonomies_add_fields', 10, 2 );

function omniana_taxonomies_edit_meta_fields( $term, $taxonomy ) {
    $wikidata_id = get_term_meta( $term->term_id, 'wikidata_id', true );
   	$args = array();
    if ('' === $wikidata_id) {
    	$wikidata_id = 'Q';
    } else {
	    $wikidata_api_uri = 'https://wikidata.org/entity/'.$wikidata_id.'.json';
    	$json = file_get_contents( $wikidata_api_uri );
    	$obj = json_decode($json);
    	$claims = $obj->entities->$wikidata_id->claims;
    	print_r( $claims->P569[0]->mainsnak->datavalue->value->time);
		$args['birth_date'] = $obj->entities->$wikidata_id->claims->P569[0]->mainsnak->datavalue->value->time;
    	$args['name'] = $obj->entities->$wikidata_id->labels->en->value;
    	$args['description'] = $obj->entities->$wikidata_id->descriptions->en->value;
    	print('<p>Name and description are imported from wikidata</p>');
    	print_r( $obj->entities->$wikidata_id->descriptions->en->value );
	}

    ?>
    <tr class="form-field term-group-wrap">
        <th scope="row">
            <label for="wikidata_id"><?php _e( 'Wikidata ID', 'omniana' ); ?></label>
        </th>
        <td>
            <input type="text" id="wikidata_id" name="wikidata_id" value="<?php echo $wikidata_id; ?>" />
        </td>
    </tr>
    <?php
	wp_update_term( $term->term_id, $taxonomy, $args );

}
add_action( 'people_edit_form_fields', 'omniana_taxonomies_edit_meta_fields', 10, 2 );
add_action( 'places_edit_form_fields', 'omniana_taxonomies_edit_meta_fields', 10, 2 );
add_action( 'events_edit_form_fields', 'omniana_taxonomies_edit_meta_fields', 10, 2 );
add_action( 'works_edit_form_fields', 'omniana_taxonomies_edit_meta_fields', 10, 2 );

function omniana_taxonomies_save_taxonomy_meta( $term_id, $tag_id ) {
    if( isset( $_POST['wikidata_id'] ) ) {
        update_term_meta( $term_id, 'wikidata_id', esc_attr( $_POST['wikidata_id'] ) );
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


