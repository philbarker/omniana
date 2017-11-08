<?php
function register_omni_taxonomy($taxonomy, $type, $s_tag, $p_tag) {
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

function register_omni_taxonomies() {
	register_omni_taxonomy('people', 'chapter', 'person', 'people');
	register_omni_taxonomy('places', 'chapter', 'place', 'places');
	register_omni_taxonomy('events', 'chapter', 'event', 'events');
	register_omni_taxonomy('works', 'chapter', 'work', 'works');
}
add_action( 'init', 'register_omni_taxonomies');

function omni_add_taxonomy_admin_submenu($taxonomy, $title, $type) {
	add_submenu_page(  
			'taxonomies',
			'',
			$title,
			'',
			'edit-tags.php?taxonomy='.$taxonomy.'&post_type='.$type,
			null
		);
}

function omni_add_admin_menus() {
	$page_title = 'Taxonomies';
	$menu_title = 'Taxonomies';
	$capability = 'post';
	$menu_slug  = 'taxonomies';
	$function   = 'omni_display_admin_page';// Callback function which displays the page content.
	$icon_url   = 'dashicons-admin-page';
	$position   = 10;
	
	add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
	omni_add_taxonomy_admin_submenu('people', 'people mentioned', 'chapter');
	omni_add_taxonomy_admin_submenu('events', 'events mentioned', 'chapter');
	omni_add_taxonomy_admin_submenu('places', 'places mentioned', 'chapter');
	omni_add_taxonomy_admin_submenu('works',  'works mentioned', 'chapter');
}
add_action( 'admin_menu', 'omni_add_admin_menus', 1 );

function omni_display_admin_page() {
        # Display custom admin page content from newly added custom admin menu.
	echo '<div class="wrap">' . PHP_EOL;
	echo '<h2>Index Taxonomies</h2>' . PHP_EOL;
	echo '<p>Choose taxonomy to edit.</p>' . PHP_EOL;
	echo '</div><!-- end .wrap -->' . PHP_EOL;
	echo '<div class="clear"></div>' . PHP_EOL;
}


function omni_taxonomies_add_fields( $taxonomy ) {
    ?>
    <div class="form-field term-group">
        <label for="wd_id"><?php _e( 'Wikidata ID', 'omniana' ); ?></label>
        <input type="url" id="wd_id" name="wd_id" />
    </div>
    <?php
}
add_action( 'people_add_form_fields', 'omni_taxonomies_add_fields', 10, 2 );
add_action( 'places_add_form_fields', 'omni_taxonomies_add_fields', 10, 2 );
add_action( 'events_add_form_fields', 'omni_taxonomies_add_fields', 10, 2 );
add_action( 'works_add_form_fields',  'omni_taxonomies_add_fields', 10, 2 );

function omni_taxonomies_edit_fields( $term, $taxonomy ) {
    $wd_id = get_term_meta( $term->term_id, 'wd_id', true );
    ?>
    <tr class="form-field term-group-wrap">
        <th scope="row">
            <label for="wd_id"><?php _e( 'Wikidata ID', 'omniana' ); ?></label>
        </th>
        <td>
            <input type="text" id="wd_id" name="wd_id" value="<?php echo $wd_id; ?>" />
        </td>
    </tr>
    <?php
}
add_action( 'people_edit_form_fields', 'omni_taxonomies_edit_fields', 10, 2 );
add_action( 'places_edit_form_fields', 'omni_taxonomies_edit_fields', 10, 2 );
add_action( 'events_edit_form_fields', 'omni_taxonomies_edit_fields', 10, 2 );
add_action( 'works_edit_form_fields', 'omni_taxonomies_edit_fields', 10, 2 );

function omni_taxonomies_save_meta( $term_id, $tag_id ) {
    if( isset( $_POST['wd_id'] ) ) {
        update_term_meta( $term_id, 'wd_id', esc_attr( $_POST['wd_id'] ) );
    }
    print( $tag_id );
}
add_action( 'created_people', 'omni_taxonomies_save_meta', 10, 2 );
add_action( 'edited_people', 'omni_taxonomies_save_meta', 10, 2 );
add_action( 'created_places', 'omni_taxonomies_save_meta', 10, 2 );
add_action( 'edited_places', 'omni_taxonomies_save_meta', 10, 2 );
add_action( 'created_events', 'omni_taxonomies_save_meta', 10, 2 );
add_action( 'edited_events', 'omni_taxonomies_save_meta', 10, 2 );
add_action( 'created_works', 'omni_taxonomies_save_meta', 10, 2 );
add_action( 'edited_works', 'omni_taxonomies_save_meta', 10, 2 );

function omni_get_people_wikidata($term, $taxonomy) {
	$term_id = $term->term_id;
    $wd_id = get_term_meta( $term_id, 'wd_id', true );
    print('get wikidata');
   	$args = array();
    if ('' !== trim( $wd_id) ) {
	    $wd_api_uri = 'https://wikidata.org/entity/'.$wd_id.'.json';
    	$json = file_get_contents( $wd_api_uri );
    	$obj = json_decode($json);
    	$claims = $obj->entities->$wd_id->claims;
    	print_r( $claims->P569[0]->mainsnak->datavalue->value->time);
		$wd_birth_date = $claims->P569[0]->mainsnak->datavalue->value->time;
    	$wd_name = $obj->entities->$wd_id->labels->en->value;
    	$wd_description = $obj->entities->$wd_id->descriptions->en->value;
    	print_r($args);
    	update_term_meta( $term_id, 'wd_name', $wd_name );
    	$args['description'] = $wd_description;
    	$args['name'] = $wd_name;
    	wp_update_term( $term_id, 'people', $args );
	}
}
// add_action( 'people_pre_edit_form' 'omni_get_people_wikidata' )

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


