<?php
function omni_get_wikidata($wd_id) {
	if (WP_DEBUG) print('getting wikidata<br />');
    if ('' !== trim( $wd_id) ) {
	    $wd_api_uri = 'https://wikidata.org/entity/'.$wd_id.'.json';
    	$json = file_get_contents( $wd_api_uri );
    	$obj = json_decode($json);
    	return $obj;
    } else {
    	return false;
	}
}

function get_wikidata_value($claim, $datatype=false) {
	if ($datatype) {
		if ( isset( $claim->mainsnak->datavalue->value->$datatype ) ) {
			return $claim->mainsnak->datavalue->value->$datatype;
		} else {
			return false;
		}
	} else {
		if ( isset( $claim->mainsnak->datavalue->value ) ) {	
			return $claim->mainsnak->datavalue->value;
		} else {
			return false;
		}
	}
}

function strip_zero($year_in) {
	if ('0' !== substr( $year_in, 0, 1 ) ) {
		return $year_in;
	} else {
		$year = substr( $year_in, 1, strlen($year_in) );
		return strip_zero( $year) ;
	}
}

function omni_wd_year($wd_time) {
	$BCE = '';
	$wd_year = get_wikidata_value( $wd_time, 'time');
	if ( substr($wd_year, 0, 1) == '-' ) {
		$BCE = ' BCE';
	}
	$year = strip_zero(substr($wd_year, 1, 4) );
	return $year.$BCE;
}

function omni_wd_place( $place_claim, $c=false) {
	//$c is used to stop recursion for countries
	$wd_place_id = get_wikidata_value( $place_claim, 'id' );
	// this is wikidata Qcode for place, 
	// get name of place from Qcode
   	$wikidata = omni_get_wikidata($wd_place_id);
	$wd_place_name=$wikidata->entities->$wd_place_id->labels->en->value;
	$place_claims = $wikidata->entities->$wd_place_id->claims;
	// get country of place	where available, & where place is not a country
	if ( isset ($place_claims->P17[0]) && ($c) ) {   //P17 is country
		$wd_country = omni_wd_place( $place_claims->P17[0], $c=false );
		$wd_place_name = $wd_place_name.', '.$wd_country;
	}
	//idea: return array of Qcode, town & country
	return $wd_place_name;
	
}

function omni_get_people_wikidata( $term ) {
	$term_id = $term->term_id;
    $wd_id = ucfirst( get_term_meta( $term_id, 'wd_id', true ) );
   	$args = array();
   	$wikidata = omni_get_wikidata($wd_id);
   	if ( $wikidata ) {
    	$wd_name = $wikidata->entities->$wd_id->labels->en->value;
    	$wd_description = $wikidata->entities->$wd_id->descriptions->en->value;
    	$claims = $wikidata->entities->$wd_id->claims;
   		$type = get_wikidata_value($claims->P31[0], 'id');
   		// check if human
   		if ( 'Q5' === $type ) {
			if ( isset ($claims->P569[0] ) ) {   //P569 is date of birth
				$wd_birth_year = omni_wd_year( $claims->P569[0] );
			}
			if ( isset ($claims->P569[0] ) ) {   //P570 is date of death
				$wd_death_year = omni_wd_year( $claims->P570[0] );
			}
			if ( isset ($claims->P19[0] ) ) {   //P19 is place of birth
				$wd_birth_place = omni_wd_place( $claims->P19[0], true );
			}
			if ( isset ($claims->P20[0] ) ) {   //P20 is place of birth
				$wd_death_place = omni_wd_place( $claims->P20[0], true );
			}
			if ( isset ($claims->P213[0] ) ) {   //P213 is ISNI ID
				$wd_ISNI = get_wikidata_value( $claims->P213[0] );
			}
			if ( isset ($claims->P214[0] ) ) {   //P213 is VIAF ID
				$wd_VIAF = get_wikidata_value( $claims->P214[0] );
				echo $wd_VIAF;
			}
			$args['description'] = $wd_description;
    		$args['name'] = $wd_name;
			if (WP_DEBUG) {
				print_r( $args );print('<br />');
				print( $wd_birth_year.'<br/>' );
				print( $wd_death_year.'<br/>' );
			}
    		update_term_meta( $term_id, 'wd_name', $wd_name );
    		update_term_meta( $term_id, 'wd_description', $wd_description );
    		update_term_meta( $term_id, 'wd_birth_year',  $wd_birth_year );
    		update_term_meta( $term_id, 'wd_birth_place', $wd_birth_place );
    		update_term_meta( $term_id, 'wd_death_year',  $wd_death_year );
    		update_term_meta( $term_id, 'wd_death_place', $wd_death_place );
    		update_term_meta( $term_id, 'wd_ISNI', $wd_ISNI );
    		update_term_meta( $term_id, 'wd_VIAF', $wd_VIAF );
    	
    		wp_update_term( $term_id, 'people', $args );

   		} else { // not human
	   		echo(' Warning: that wikidata is not for a human, check the ID. ');
	   		echo(' <br /> ');
   		} 
    	
   	} else {
   		echo(' Warning: no wikidata for you, check the Wikidata ID. ');
   	}
}
add_action( 'people_pre_edit_form', 'omni_get_people_wikidata' );


