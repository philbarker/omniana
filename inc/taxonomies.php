<?php
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
    	'show_in_menu' => false,
	    'rewrite'      => array( 'slug' => 'people' ),
	    'description'  => 'Indexing people who are mentioned in articles',
	    'sort'         => true
	    );
	register_taxonomy( 'people', 'chapter', $people_args );

	$place_args = array(
    	'labels' => array (
	    	'name'          => 'places mentioned',
   			'singular name' => 'place mentioned',
    		'menu_name'     => 'places index'
    		),
    	'public'       => true,
    	'hierarchical' => false,
    	'show_admin_column' => true,
    	'show_in_menu' => false,
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
    	'show_in_menu' => false,
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
    	'show_in_menu' => false,
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

function omniana_add_admin_menus() {
	$page_title = 'Taxonmies';
	$menu_title = 'Taxonmies';
	$capability = 'post';
	$menu_slug  = 'taxonomies';
	$function   = 'omniana_display_admin_page';// Callback function which displays the page content.
	$icon_url   = 'dashicons-admin-page';
	$position   = 10;
	
	add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );

	$submenu_pages = array(
		array(
			'parent_slug' => $menu_slug,
			'page_title'  => '',
			'menu_title'  => 'people mentioned',
			'capability'  => '',
			'menu_slug'   => 'edit-tags.php?taxonomy=people&post_type=chapter',
			'function'    => null,
		),
		array(
			'parent_slug' => $menu_slug,
			'page_title'  => '',
			'menu_title'  => 'events mentioned',
			'capability'  => '',
			'menu_slug'   => 'edit-tags.php?taxonomy=events&post_type=chapter',
			'function'    => null,
		),
		array(
			'parent_slug' => $menu_slug,
			'page_title'  => '',
			'menu_title'  => 'places mentioned',
			'capability'  => '',
			'menu_slug'   => 'edit-tags.php?taxonomy=places&post_type=chapter',
			'function'    => null,
		),
		array(
			'parent_slug' => $menu_slug,
			'page_title'  => '',
			'menu_title'  => 'works mentioned',
			'capability'  => '',
			'menu_slug'   => 'edit-tags.php?taxonomy=works&post_type=chapter',
			'function'    => null,
		),
	);
	foreach ( $submenu_pages as $submenu) {
		add_submenu_page(  
			$submenu['parent_slug'],
			$submenu['page_title'],
			$submenu['menu_title'],
			$submenu['capability'],
			$submenu['menu_slug'],
			$submenu['function']
		);
	}
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


