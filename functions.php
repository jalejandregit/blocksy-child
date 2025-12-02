<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
    function chld_thm_cfg_locale_css( $uri ){
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );
         
if ( !function_exists( 'child_theme_configurator_css' ) ):
    function child_theme_configurator_css() {
        wp_enqueue_style( 'chld_thm_cfg_child', trailingslashit( get_stylesheet_directory_uri() ) . 'style.css', array( 'ct-main-styles','ct-admin-frontend-styles' ) );
    }
endif;
add_action( 'wp_enqueue_scripts', 'child_theme_configurator_css', 10 );

// END ENQUEUE PARENT ACTION

// ******** CUSTOMS *******//
// 

/*
	hierarchical_category_tree_09hy7( 0 ); // the function call; 0 for all categories; or cat ID  
	function hierarchical_category_tree_09hy7( $cat ) {
		$next = get_categories('hide_empty=0&orderby=name&order=ASC&parent=' . $cat);
		if( $next ){   
			foreach( $next as $cat ){
				echo '<ul><li>' . $cat->name . '';
				hierarchical_category_tree_09hy7( $cat->term_id );
			}	
		}
		echo '</li></ul>'; echo "\n";
	}  

*/








/* https://developer.wordpress.org/reference/functions/get_categories */

function wpdocs_get_child_categories( $parent_category_id ) {
    $html = '';
    $child_categories = get_categories( array( 'parent' => $parent_category_id, 'hide_empty' => false, 'taxonomy' => $taxonomy ) );
    if ( ! empty( $child_categories ) ) {
        $html .= '<ul>';
        foreach ( $child_categories as $child_category ) {
            $html .= '<li>'.$child_category->name;
            $html .= get_child_categories( $child_category->term_id );
            $html .= '</li>';
        }
        $html .= '</ul>';
    }
    return $html;
}

function wpdocs_list_categories() {
    $html = '';
    $parent_categories = get_categories( array( 'parent' => 0, 'hide_empty' => false, 'taxonomy' => $taxonomy ) );
    $html .= '<ul>';
    foreach ( $parent_categories as $parent_category ) {
        $html .= '<li>';
        $html .= $parent_category->name;
        $html .= wpdocs_get_child_categories( $parent_category->term_id  );
        $html .= '</li>';
    }
    $html.= '</ul>';
    return $html;
}

function wp_llistats_categories(){
	wp_list_categories("echo=0&orderby=term_taxonomy_id");
}

add_action( 'pre_get_posts',  'wp_llistats_categories' );


/*
function only_search_for_full_phrase( $query ) {
    if ( $query->is_search() && $query->is_main_query() ) {
        $query->set( 'sentence', true );
		$query->set( 'post_type', 'post' );
		$query->set( 'date_query', array(
				array(
				'after' => 'May 17, 2019',
				)
				) );
    }
}
add_action( 'pre_get_posts', 'only_search_for_full_phrase' );


*/
function theme_scripts() {
  wp_enqueue_script('jquery');
}
add_action('wp_enqueue_scripts', 'theme_scripts');

// awesome 
function pm_add_font_awesome() {
 	wp_enqueue_style( 'pm-font-awesome', 'https://use.fontawesome.com/releases/v6.2.0/css/all.css' );
} 
add_action( 'wp_enqueue_scripts', 'pm_add_font_awesome' );
// end awesome

/**** ordre entrades*/
function search_filter($query) {
	//ordenar només si estem a la pàgina principal, en altres casos dona 404
	if (is_home()){
		if ($query->is_main_query() ) {
				$query->set( 'post_type', 'post' );
				$query->set('orderby', 'meta_value_num');    
				$query->set('meta_key', 'link_click_counter');    
				$query->set('order', 'DESC'); 
		}
	}
}





/* excloure categoria General 
 * no funciona, se oculta desde CSS 
function exclude_category( $query ) {
    if ( $query->is_home() && $query->is_main_query() ) {
        $query->set( 'cat', '-1' ); 
    }
}
*/
//add_action( 'pre_get_posts', 'exclude_category' );


function ordenar_category_ct_term( $query ) {
	 if ( $query->is_home() && $query->is_main_query() ) {
        $query->set( 'taxonomy', 'category', );
		$query->set('orderby', 'meta_value_num');    
		$query->set('meta_key', 'term_taxonomy_id');    
		$query->set('order', 'ASC'); 
    }
}
add_action( 'pre_get_posts', 'ordenar_category_ct_term' );




add_filter( 'get_terms_orderby', 'my_taxonomy_terms_order_orderby', 10, 3 );
function my_taxonomy_terms_order_orderby( $orderby, $args, $term_args ) {
  if ( ! empty( $term_args['orderby'] ) && 'term_order' === $term_args['orderby'] ) {
    return 'term_order';
  }
  return $orderby;
}




// Afegir status=Scraped al editor de post
// https://mangcoding.com/how-to-add-custom-post-status-to-wordpress-quick-edit/
function add_custom_status(){
    register_post_status('scraped', array(
        'label'         => _x('Scaped', 'post'),
        'label_count'   => _n_noop('Scraped <span class="count">(%s)</span>','Scraped <span class="count">(%s)</span>'),
        'public'        => true
    ));
}
add_action('init', 'add_custom_status');

function add_custom_status_inline(){
    echo "<script>
    jQuery(document).ready( function(){
        jQuery( 'select[name=\"_status\"]' ).append( '<option value=\"scraped\">Scraped</option>' );
    });
    </script>";
}
add_action('admin_footer-edit.php','add_custom_status_inline');
//END Afegir status=Scraped al editor de post

//******************************************************//
//*************** INI SavePost *************************//
//******************************************************//

require_once 'include/procesaid.php';

add_action( 'wp_ajax_link_click_counter', 'link_click_counter' );
add_action( 'wp_ajax_nopriv_link_click_counter', 'link_click_counter' );
add_action( 'wp_head', 'link_click_head' );

add_action('save_post', 'desa_post_type_Excerpt', 50);
add_action( 'pre_get_posts', 'search_filter' );





/****** CLICKCOUNT  *****
/* clicks links link_click_counter
	https://stackoverflow.com/questions/20812532/click-count-of-a-certain-link-in-wordpress-post
*/
function link_click_counter() {
	//$nonce =  wp_verify_nonce( $_POST['nonce'], 'link_click_counter_'.$_POST['post_id'] ) ;
	//$resultado= $_POST['post_id'].' nonce:'.$_POST['nonce'].' verificacion:'.$verifica;

   if ( isset( $_POST['nonce'] ) &&  isset( $_POST['post_id'] ) && wp_verify_nonce( $_POST['nonce'], 'link_click_counter_'.$_POST['post_id'] ) ) {
        $count = get_post_meta( $_POST['post_id'], 'link_click_counter', true );
        update_post_meta( $_POST['post_id'], 'link_click_counter', ( $count === '' ? 1 : $count + 1 ) );
		//$resultado='ok';
    }else{
		//$resultado='no se ha actualizado nada';
	}
   // echo $resultado;
}


/*------------ link_click_head ------------------*/
function link_click_head(){
	//$file_procesaID = get_stylesheet_directory_uri().'/include/procesaid.php';
	$url_admin  = admin_url( 'admin-ajax.php' );  
	$nonce = wp_create_nonce( 'link_click_counter_' );
	echo "<script type='text/javascript'>
	jQuery(function ($) {
	   $('a.count').on('click', function (e) {	
			var post1='';
			var post='';
			var id = $(this).attr('id');
			//adaptacio countable_link_249648 i countable_link_249648-2
			post1 =id.replace('countable_link_', '');
			post =post1.replace('-2', '');

			var ajax_options = {
							action: 'procesa_link',
            				post_id: post,
							nonce: '$nonce'
						};
			console.log('post:' + post);
			$.post( '$url_admin', ajax_options, function(data, status) {
				//console.log('status: ' + status + ', data: ' + data);
				var ajax_options2 = {
							action: 'link_click_counter',
            				post_id: post,
							nonce: data
						};
				var self = $( this );
				$.post( '$url_admin', ajax_options2, function(result) {
							//console.log('end:' + result);
							//window.open(self.attr( 'href' ), '_blank');
						});
						return false;		
			});
		});	
	
	});	
	
    </script>";
}
/******** end  link_click_head ***/



/*--- Guardar els valors de coordenades per comprovar si han canviat*/

function getValuesCoordenades($post_id){
	//$post_id = get_the_ID() ;
	$post_tipo = get_post_type($post_id);
	$post_lat_old  = get_field( 'latitud', $post_id ); // ACF field
	$post_lon_old = get_field( 'longitud', $post_id ); // ACF field
	if($post_tipo==='post'){
		error_log("Latitud-Longitud : " . print_r($post_lat_old.'-'.$post_lon_old, true));
		wp_cache_set( 'post_lat_old', $post_lat_old );
		wp_cache_set( 'post_lon_old', $post_lon_old );
	}
	remove_action('pre_post_update', 'getValuesCoordenades');
}
add_action('pre_post_update', 'getValuesCoordenades');


//

function desa_post_type_Excerpt() {
	$post_id = get_the_ID() ;
	$post_tipo = get_post_type($post_id);
	
	if($post_tipo==='post'){
		desa_post_Custom_Excerpt();
	}

	
}

function desa_post_Custom_Excerpt() {
global $post;

/* $post_id = ( $post->ID ); // Current post ID*/

	$post_id = get_the_ID() ;
	$autor= get_field( 'autor', $post_id ); // ACF field
	$autorurl= get_field( 'autor_url', $post_id ); // ACF field
	$post_title= get_field( 'title', $post_id ); // ACF field
		if( empty( $post_title ) ){
		  $post_title = get_the_title( $post_id );
		}
	$post_link = get_field( 'link_post', $post_id ); // ACF field
	$post_orient = get_field( 'orientacio', $post_id ); // ACF field
	
	//$post_lat_old =wp_cache_get( 'post_lat_old');
	$post_lat_old = (string) wp_cache_get( 'post_lat_old') ?? '';
	$post_lon_old = (string) wp_cache_get( 'post_lon_old') ?? '';
	
	$post_lat = (string) get_field( 'latitud', $post_id ); // ACF field
	$post_lon = (string) get_field( 'longitud', $post_id ); // ACF field
	//error_log("OLD Latitud-Longitud : " . print_r($post_lat_old.'-'.$post_lon_old,true) );
	//error_log("NEW Latitud-Longitud : " . print_r($post_lat.'-'.$post_lon, true) );
	if ( !strcmp($post_lat, $post_lat_old) or !strcmp($post_lon, $post_lon_old)  ){	
		remove_parent_category($post_id);
	}
	
	
    // Reverse geocode
     $geo = array();
     if (!empty($post_lat) && !empty($post_lon)) {
        $geo = reverse_geocode($post_lat, $post_lon);
        if (!empty($geo['location'])) {
            $name_location = $geo['location'];
         }
     }

	//$name_location = reverse_geocode($post_lat,$post_lon);
	$link_location = openMaps($post_lat,$post_lon).' - '.openOSMaps($post_lat,$post_lon);
	$rosavents = getOrientacio($post_orient);
	$href = get_field( 'urlImagen', $post_id ); // ACF field
	$thumb = get_field( '_thumbnail_id', $post_id ); // ACF field
	$post_link_title = '<p class="list-post-meta"><span class="published"><a class="count" id="countable_link_'.$post_id.'"  href="'.$post_link.'" target="_blank">'.$post_title.'</a></span></p>';
	$taula_dades= getTableDades($autor,$autorurl, $rosavents,$link_location);
	$post_excerpt = '<strong>'.$post_link_title.'</strong>'.$taula_dades.'<div class="thumb"><a class="count" id="countable_link_'.$post_id.'-2"  href="'.$href.'" target="_blank"><img src="'.$thumb.'" ></a></div>';

	if ( ( $post_id ) AND ( $post_excerpt ) ) {

	$post_array = array(
		'ID' => $post_id,
		'post_excerpt' => $post_excerpt
		);
	update_post_meta($post_id, 'location', $name_location);
	/***** save tag orientacio ***/
	wp_set_post_tags( $post_id, $post_orient );	
	remove_action('save_post', 'desa_post_type_Excerpt', 50); // Unhook this function so it doesn't loop infinitely
	remove_action('save_post', 'desa_post_Custom_Excerpt', 50); // Unhook this function so it doesn't loop infinitely
	remove_action('save_post', 'remove_parent_category');
		
	wp_update_post( $post_array );
    add_Categories($post_id, $geo);

	add_action( 'save_post', 'desa_post_type_Excerpt', 50); // Re-hook this function

	}
}



  function reverse_geocode($lat, $lon) {
        $url = esc_url_raw(
            "https://nominatim.openstreetmap.org/reverse?format=jsonv2&accept-language=ca&lat="
            . rawurlencode($lat) . "&lon=" . rawurlencode($lon)
        );

        $resp = wp_remote_get($url, [
            'headers' => ['User-Agent' => 'garbellPlugin/1.0'],
            'timeout' => 10
        ]);

        if (is_wp_error($resp)) return [];

        $body = wp_remote_retrieve_body($resp);
        $json = json_decode($body, true);

        return [
            'location' => $json['display_name']         ?? '',
            'state'    => $json['address']['state']     ?? '',
            'county'   => $json['address']['county']    ?? '',
            'village'  => $json['address']['village']   ?? ''
        ];
    }

function add_Categories($post_id, $geo){
    // ---------------------- CATEGORÍAS GEO ---------------------- //
                $state   = $geo['state']   ?? '';
                $county  = $geo['county']  ?? '';
                $village = $geo['village'] ?? '';

                $cat_state_id   = ensure_category($state, 0);
                $cat_county_id  = ensure_category($county, $cat_state_id);
                $cat_village_id = ensure_category($village, $cat_county_id);

                $categories = array_filter([$cat_state_id, $cat_county_id, $cat_village_id]);
                if (!empty($categories)) {
                    wp_set_post_terms($post_id, $categories, 'category', true);
                }

                // ---------------------------------------------------------
                //   AÑADIR TÉRMINO "orientacio" → wp_term_relationships
                // ---------------------------------------------------------
                if (!empty($orientacio)) {

                    $term_taxonomy_id = $wpdb->get_var(
                        $wpdb->prepare("
                            SELECT tt.term_taxonomy_id
                            FROM {$wpdb->terms} t
                            INNER JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id
                            WHERE t.name = %s
                            LIMIT 1
                        ", $orientacio)
                    );

                    if ($term_taxonomy_id) {
                        $wpdb->insert(
                            $wpdb->term_relationships,
                            [
                                'object_id'        => $post_id,
                                'term_taxonomy_id' => $term_taxonomy_id,
                                'term_order'       => 0,
                            ],
                            ['%d','%d','%d']
                        );
                    }

                }

}

function ensure_category($name, $parent_id = 0) {
	if (empty($name)) return 0;
    $term = term_exists($name, 'category');
    if ($term) return $term['term_id'];
    $new = wp_insert_term($name, 'category', ['parent' => $parent_id]);
    return is_wp_error($new) ? 0 : $new['term_id'];
}

function remove_parent_category($post_id) {
	//error_log( '$term_ancestors: ' . print_r($term, true ) );
	// Evitar loops infinitos
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (wp_is_post_revision($post_id)) return;

    $categories = get_the_category($post_id);
    if (empty($categories)) return;

    $to_remove = array();

    foreach ($categories as $cat) {
        // Eliminar tanto padres como hijos
        $to_remove[] = $cat->term_id;
    }

    if (!empty($to_remove)) {
        wp_remove_object_terms($post_id, $to_remove, 'category');
    }

}



function getTableDades($autor, $autorurl ,$orientacio_html,$location_html){
		$table1 = '<table style="width:100%" class="table_dades"><tbody><tr><td style="width:5%"><i class="fas fa-user-pen"></i></td><td><a href="'.$autorurl.'" target="_blank">'.$autor.'</a></td><td style="width:15%" rowspan="2">'.$orientacio_html.'</td></tr><tr>';
	
	$table2= '<td style="width:5%"><i class="fas fa-location-dot"></i></td><td>'.$location_html.'</td></tr></tbody></table>';
	$table = $table1.$table2;	
return $table;	
}

function openMaps($post_lat,$post_lon){
	return '<a href=https://www.google.com/maps/search/?api=1&query='.$post_lat.','.$post_lon.' target="_blank" title="Obrir amb Google Maps">GMaps</a>';
}
function openOSMaps($post_lat,$post_lon){
	return '<a href="http://www.openstreetmap.org/?lat='.$post_lat.'&lon='.$post_lon.'&zoom=17&layers=Y" target="_blank" title="Obrir amb Open Street Map">OSMaps</a>';
}

/***** Address(lat + long) **/
function reverse_geocode_OLD($latitude,$longitude){
  //$json = "https://nominatim.openstreetmap.org/reverse?format=json&lat=".$latitude."&lon=".$longitude."&zoom=27&addressdetails=1";
  $json = "https://nominatim.openstreetmap.org/reverse?format=jsonv2&accept-language=ca&lat=".$latitude."&lon=".$longitude."&zoom=27&addressdetails=1";
  $ch = curl_init($json);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:59.0) Gecko/20100101 Firefox/59.0");
  $jsonfile = curl_exec($ch);
  curl_close($ch);

  $RG_array = json_decode($jsonfile,true);

  return $RG_array['display_name'];
}
/*****End Address(lat + long) **/

/******** Orientacio ****/
function getOrientacio($arg_o){

$o_11 = '<div dades="" datao="" title="E" class="control">';
$o_22 = '<svg dades="" xmlns="http://www.w3.org/2000/svg" width="40" height="40" version="1.1" viewBox="0 0 454.00715 454.00714" class="orientacio is-unselectable is-read-only">';
$o_nn = '<g dades="" class=""><path dades="" d="m285.19 83.727-58.18 142.14-58.19-142.14v-0.005l58.19-83.725z"></path> <text dades="" y="105.007141" x="205.0424" font-size="150">.</text></g>';
$o_ne = '<g dades="" class=""><path dades="" d="m369.46 166.83-141.65 59.371 59.368-141.65 0.002-0.002 100.34-18.058z"></path><text dades="" y="135.007141" x="306.6721" font-size="150">.</text></g>';
$o_ee = '<g dades="" class=""><path dades="" d="m370.28 168.82-142.14 58.18 142.14 58.185h0.005l83.722-58.185z"></path> <text dades="" y="247.58344" x="350" font-size="150">.</text></g>';
$o_se = '<g dades="" class=""><path dades="" d="m369.46 287.17-141.65-59.371 59.368 141.65 0.002 0.002 100.34 18.058z"></path><text dades="" y="345.58344" x="300.10295" font-size="150">.</text></g>';
$o_ss = '<g dades="" class=""><path dades="" d="m285.19 370.28-58.18-142.14-58.185 142.14v0.005l58.185 83.722z"></path> <text dades="" y="390.00714" x="206.10295" font-size="150">.</text></g>';
$o_sw = '<g dades="" class=""><path dades="" d="m166.83 369.46 59.371-141.65-141.65 59.368-0.0032 0.002-18.058 100.34z"></path><text dades="" y="345.58344" x="106.6721" font-size="150">.</text></g>';
$o_ww = '<g dades="" class=""><path dades="" d="m83.727 168.82 142.14 58.18-142.14 58.185h-0.0046l-83.725-58.18z"></path> <text dades="" y="247.58344" x="56" font-size="150">.</text></g>';
$o_nw = '<g dades="" class=""><path dades="" d="m166.83 84.55 59.371 141.65-141.65-59.368-0.0032-0.002l-18.058-100.34z"></path><text dades="" y="135.007141" x="106.6721" font-size="150">.</text></g> </svg>';
$o_33 = '</div>';

switch ($arg_o) { 
    case 'N':
		$o_11 = '<div dades="" datao="" title="N" class="control">';	
        $o_nn = '<g dades="" class="orientacio-selected"><path dades="" d="m285.19 83.727-58.18 142.14-58.19-142.14v-0.005l58.19-83.725z"></path> <text dades="" y="105.007141" x="205.0424" font-size="150">.</text></g>';
		break;
	case 'NE':
		$o_11 = '<div dades="" datao="" title="NE" class="control">';
        $o_ne = '<g dades="" class="orientacio-selected"><path dades="" d="m369.46 166.83-141.65 59.371 59.368-141.65 0.002-0.002 100.34-18.058z"></path><text dades="" y="135.007141" x="306.6721" font-size="150">.</text></g>';
		break;
	case 'E':
		$o_11 = '<div dades="" datao="" title="E" class="control">';
        $o_ee = '<g dades="" class="orientacio-selected"><path dades="" d="m370.28 168.82-142.14 58.18 142.14 58.185h0.005l83.722-58.185z"></path> <text dades="" y="247.58344" x="350" font-size="150">.</text></g>';
		break;
	case 'SE':
		$o_11 = '<div dades="" datao="" title="SE" class="control">';
        $o_se = '<g dades="" class="orientacio-selected"><path dades="" d="m369.46 287.17-141.65-59.371 59.368 141.65 0.002 0.002 100.34 18.058z"></path><text dades="" y="345.58344" x="300.10295" font-size="150">.</text></g>';
		break;
	case 'S': 
		$o_11 = '<div dades="" datao="" title="S" class="control">';
        $o_ss = '<g dades="" class="orientacio-selected"><path dades="" d="m285.19 370.28-58.18-142.14-58.185 142.14v0.005l58.185 83.722z"></path> <text dades="" y="390.00714" x="206.10295" font-size="150">.</text></g>';
		break;
	case 'SW':
		$o_11 = '<div dades="" datao="" title="SW" class="control">';
        $o_sw = '<g dades="" class="orientacio-selected"><path dades="" d="m166.83 369.46 59.371-141.65-141.65 59.368-0.0032 0.002-18.058 100.34z"></path><text dades="" y="345.58344" x="106.6721" font-size="150">.</text></g>';
		break;
	case 'W':
		$o_11 = '<div dades="" datao="" title="W" class="control">';
        $o_ww = '<g dades="" class="orientacio-selected"><path dades="" d="m83.727 168.82 142.14 58.18-142.14 58.185h-0.0046l-83.725-58.18z"></path> <text dades="" y="247.58344" x="56" font-size="150">.</text></g>';
		break;
	case 'NW': 
		$o_11 = '<div dades="" datao="" title="NW" class="control">';
        $o_nw = '<g dades="" class="orientacio-selected"><path dades="" d="m166.83 84.55 59.371 141.65-141.65-59.368-0.0032-0.002l-18.058-100.34z"></path><text dades="" y="135.007141" x="106.6721" font-size="150">.</text></g> </svg>';
		break;		
} 
	$return_orientacio = $o_11.$o_22.$o_nn.$o_ne.$o_ee.$o_se.$o_ss.$o_sw.$o_ww.$o_nw.$o_33;
    return $return_orientacio;
}
/********End Orientacio ****/

