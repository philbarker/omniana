<?php 
	$args = array(
		'template' => __('<h4>%s:</h4> <ul>%l</ul>'),
		'term_template' => '<li rel="mentions" typeof="Thing" resource="%1$s">
				<a property="url"  href="%1$s">
					<span property="name">%2$s</span>
				</a>
			</li>'
	);
	$taxonomies = get_the_taxonomies(0, $args);
	unset($taxonomies['chapter-type']); 
	unset($taxonomies['back-matter-type']); 
	unset($taxonomies['front-matter-type']); 
	if ( count( $taxonomies ) > 0 ){ 
		print( '<div class="index-taxons">' );
		print( '<h3>Mentioned in this article</h3>' );
		foreach ( $taxonomies as $key => $value ) {
			$value = str_replace('> and <', '> <', $value);
			$value = str_replace('>, and <', '> <', $value);
			$value = str_replace('>, <', '> <', $value);
			if ('people' == $key) {
				$value = str_replace('Thing', 'Person', $value);
			} elseif ('works' == $key) {
				$value = str_replace('Thing', 'CreativeWork', $value);
			} elseif ('events' == $key) {
				$value = str_replace('Thing', 'Event', $value);
			} elseif ('places' == $key) {
				$value = str_replace('Thing', 'Place', $value);
			}
			echo $value;
		}
		print( '</div>' );
	}
?>

