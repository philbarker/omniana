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
//add_action( 'init', 'register_omni_taxonomies');

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
	$function   = 'wdtax_display_admin_page';// Callback function which displays the page content.
	$icon_url   = 'dashicons-admin-page';
	$position   = 10;
	
	add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
	omni_add_taxonomy_admin_submenu('people', 'people mentioned', 'chapter');
	omni_add_taxonomy_admin_submenu('events', 'events mentioned', 'chapter');
	omni_add_taxonomy_admin_submenu('places', 'places mentioned', 'chapter');
	omni_add_taxonomy_admin_submenu('works',  'works mentioned', 'chapter');
}
add_action( 'admin_menu', 'omni_add_admin_menus', 1 );

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
    $wd_id = ucfirst( get_term_meta( $term->term_id, 'wd_id', true ) );
    $wd_name = get_term_meta( $term->term_id, 'wd_name', true ); 
    $wd_description = get_term_meta( $term->term_id, 'wd_description', true ); 
    ?>
    <tr class="form-field term-group-wrap">
        <th scope="row">
            <label for="wd_id"><?php _e( 'Wikidata ID', 'omniana' ); ?></label>
        </th>
        <td>
            <input type="text" id="wd_id"  name="wd_id" 
            	   value="<?php echo ucfirst($wd_id); ?>" />
        </td>
    </tr>

<!--JavaScript required so that name and description fields are updated-->
    <script>
	  var f = document.getElementById("edittag");
  	  function updateFields() {
	  	var i = document.getElementById("wd_id");
	  	var n = document.getElementById("name");
  	  	var d = document.getElementById("description");
  		if (i.value.charAt(0) == "Q") {
	  		n.value = "<?php echo($wd_name) ?>";
  			d.innerHTML = "<?php echo($wd_description) ?>";
  		}
  	  }
	  f.onsubmit=updateFields();
	</script>

    <?php
}
add_action( 'people_edit_form_fields', 'omni_taxonomies_edit_fields', 10, 2 );
add_action( 'places_edit_form_fields', 'omni_taxonomies_edit_fields', 10, 2 );
add_action( 'events_edit_form_fields', 'omni_taxonomies_edit_fields', 10, 2 );
add_action( 'works_edit_form_fields', 'omni_taxonomies_edit_fields', 10, 2 );

function omni_people_edit_fields( $term, $taxonomy ) {
    $wd_id = ucfirst( get_term_meta( $term->term_id, 'wd_id', true ) );
    $wd_name = get_term_meta( $term->term_id, 'wd_name', true ); 
    $wd_description = get_term_meta( $term->term_id, 'wd_description', true ); 
    $wd_birth_year  = get_term_meta( $term->term_id, 'wd_birth_year', true );
    $wd_birth_place  = get_term_meta( $term->term_id, 'wd_birth_place', true );
    $wd_death_year  = get_term_meta( $term->term_id, 'wd_death_year', true );
    $wd_death_place  = get_term_meta( $term->term_id, 'wd_death_place', true );
    $wd_VIAF  = get_term_meta( $term->term_id, 'wd_VIAF', true );
    $wd_ISNI  = get_term_meta( $term->term_id, 'wd_ISNI', true );
    ?>
    <tr class="form-field term-group-wrap">
    	<th>From Wikidata</th>
        <td><strong>Born: </strong><?php echo $wd_birth_year; ?>.
        	                     <?php echo $wd_birth_place ?>.
        </td>
    </tr>
    <tr class="form-field term-group-wrap">
    	<td></td>
        <td><strong>Died: </strong><?php echo $wd_death_year; ?>.
        	                     <?php echo $wd_death_place; ?>.
        </td>
    </tr>
    <tr class="form-field term-group-wrap">
    	<td></td>
        <td><strong>Identifiers: </strong> VIAF <?php echo $wd_VIAF; ?>;
        	                     ISNI <?php echo $wd_ISNI; ?>.
        </td>
    </tr>


    <?php
}
add_action( 'people_edit_form_fields', 'omni_people_edit_fields', 20, 2 );

function omni_works_edit_fields( $term, $taxonomy ) {
    $wd_id = ucfirst( get_term_meta( $term->term_id, 'wd_id', true ) );
    $wd_name = get_term_meta( $term->term_id, 'wd_name', true ); 
    $wd_description = get_term_meta( $term->term_id, 'wd_description', true );
    $wd_author = get_term_meta( $term->term_id, 'wd_author', true ); 
    ?>
    <?php if (! empty($wd_author)): ?>
    <tr class="form-field term-group-wrap">
    	<th>From Wikidata</th>
        <td><strong>Author: </strong><?php echo $wd_author; ?>.
        </td>
    </tr>
    <?php endif; ?>
<?php
}
add_action( 'works_edit_form_fields', 'omni_works_edit_fields', 20, 2 );

function omni_taxonomies_save_meta( $term_id, $tag_id ) {
// there is no need to save metadata that comes from wikidata here
// as it is saved when fetched, not edited in form.
    if( isset( $_POST['wd_id'] ) ) {
        update_term_meta( 
        	$term_id, 'wd_id', 
        	ucfirst( esc_attr( $_POST['wd_id'] ) ) 
        );
    }
}
add_action( 'created_people', 'omni_taxonomies_save_meta', 10, 2 );
add_action( 'edited_people', 'omni_taxonomies_save_meta', 10, 2 );
add_action( 'created_places', 'omni_taxonomies_save_meta', 10, 2 );
add_action( 'edited_places', 'omni_taxonomies_save_meta', 10, 2 );
add_action( 'created_events', 'omni_taxonomies_save_meta', 10, 2 );
add_action( 'edited_events', 'omni_taxonomies_save_meta', 10, 2 );
add_action( 'created_works', 'omni_taxonomies_save_meta', 10, 2 );
add_action( 'edited_works', 'omni_taxonomies_save_meta', 10, 2 );


