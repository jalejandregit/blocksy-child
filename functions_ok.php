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


require_once 'include/procesaid.php';

add_action( 'wp_ajax_link_click_counter', 'link_click_counter' );
add_action( 'wp_ajax_nopriv_link_click_counter', 'link_click_counter' );
add_action( 'wp_head', 'link_click_head' );
add_action('wp_enqueue_scripts', 'theme_scripts');
add_action( 'wp_enqueue_scripts', 'pm_add_font_awesome' );
add_action('save_post', 'desa_post_type_Excerpt', 50);
//add_action( 'widgets_init', 'orientacio_register_widget' );
add_action( 'pre_get_posts', 'search_filter' );
//add_filter( 'orientacio_tags', 'migas_de_pan_OK' );
//add_shortcode( 'tags-orientacio-old', 'tags_orientacio_old' );
//add_shortcode( 'tags-orientacio', 'tags_orientacio' );
//add_shortcode( 'get_queried_object2', 'get_queried_object2' );


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

/*
function my_scripts_and_styles(){
$cache_buster = date("YmdHi", filemtime( get_stylesheet_directory() . '/style.css'));
wp_enqueue_style( 'main', get_stylesheet_directory_uri() . '/style.css', array(), $cache_buster, 'all' );
}
*/
//add_action( 'wp_enqueue_scripts', 'my_scripts_and_styles', 1);

function theme_scripts() {
  wp_enqueue_script('jquery');
}



// awesome 
function pm_add_font_awesome() {
 	wp_enqueue_style( 'pm-font-awesome', 'https://use.fontawesome.com/releases/v6.2.0/css/all.css' );
} 
// end awesome


/*------------ https://www.chillicon.co.uk/divi-blog-module-show-acf-advanced-custom-fields/*/

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
	$post_lat = get_field( 'latitud', $post_id ); // ACF field
	$post_lon = get_field( 'longitud', $post_id ); // ACF field
	$name_location = getAddress($post_lat,$post_lon);
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

	wp_update_post( $post_array );

	add_action( 'save_post', 'desa_post_type_Excerpt', 50); // Re-hook this function

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
function getAddress($latitude,$longitude){
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

/***** OK tag_cloud_by_category ***/

/*
function tag_cloud_by_category($category_ID){
    // Get our tag array
    $tags = get_tags_in_use($category_ID, 'id');

    // Start our output variable
    echo '<div class="tag-cloud">';

    // Cycle through each tag and set it up
    foreach ($tags as $tag):
        // Get our count
        $term = get_term_by('id', $tag, 'post_tag');
        $count = $term->count;

        // Get tag name
        $tag_info = get_tag($tag);
        $tag_name = $tag_info->name;

        // Get tag link
        $tag_link = get_tag_link($tag);

        // Set up our font size based on count
        $size = 8 + $count;

        echo '<span style="font-size:'.$size.'px;">';
        echo '<a href="'.$tag_link.'">'.$tag_name.'</a>';
        echo ' </span>';

    endforeach;

    echo '</div>';
}
*/

/***** OK show_posts_from_process_cat_tree ***/

/*
function show_posts_from_process_cat_tree( $cat ) {
 $args = array('category__in' => array( $cat ), 'numberposts' => -1);
 $cat_posts = get_posts( $args );
 echo '<ul>';
	 if( $cat_posts ) :
		 foreach( $cat_posts as $post ) :
			 echo '<li>';
			 echo '<a href="' . get_permalink( $post->ID ) . '">' . $post->post_title . '</a>';
			 echo '</li>';
		 endforeach;
	 endif;

 	$next = get_categories('hide_empty=0&parent=' . $cat);

	 if( $next ) :
		 foreach( $next as $cat ) :
		 	echo '<ul><li><strong>' . $cat->name . '</strong></li>';
		 	show_posts_from_process_cat_tree( $cat->term_id );
		 endforeach;
	 endif;

 echo '</ul>';
}
*/


/***** OK getCrumbsTags for Categories ****/

function migas_de_pan_OK(){
	//echo getCrumbsTags(  get_query_var('cat'),  get_query_var('category_name')  );
	$category_ID =  get_query_var('cat');
	$current = get_query_var('category_name');
	$i = 0;
	$link ='';	
  	if ( $term_ids = get_ancestors( get_queried_object_id(), 'category', 'taxonomy' ) ) {
    $crumbs = [];

	foreach ( $term_ids as $term_id ) {
        $term = get_term( $term_id, 'category' );

         if ($i == 0){
				$link = esc_url( get_term_link( $term ));
        	}
		$i++;
    	}
	}
	
	$link .=$current.'?tag=';
	
	/* tags */
	 $tags = get_tags_in_use($category_ID, 'id');
	$lineCrumbs = '<div class="div-tag">';	
	$lineCrumbs .= '<ul class="tags-tp">';
		// Cycle through each tag and set it up
    foreach ($tags as $tag){
		// Get tag name
        $tag_info = get_tag($tag);
        $tag_name = $tag_info->name;

        // Get tag link
        $tag_link = get_tag_link($tag);
		$size = 10;
		$lineCrumbs .= '<li><a class="tag-tp" href="'.$link.$tag_name.'">'.$tag_name.'</a></li>';
	}
	$lineCrumbs .='</ul></div>';
	echo $lineCrumbs;
}

//**** OK getCrumbsCategoryAncestors() ***/

/*function getCrumbsCategoryAncestors(){
	if ( $term_ids = get_ancestors( get_queried_object_id(), 'category', 'taxonomy' ) ) {
    $crumbs = [];
	$links =[];
    foreach ( $term_ids as $term_id ) {
        $term = get_term( $term_id, 'category' );

        if ( $term && ! is_wp_error( $term ) ) {
            	 $crumbs[] = sprintf( '<a href="%s">%s</a>', esc_url( get_term_link( $term ) ), esc_html( $term->name ) );
			$links[]= esc_url( get_term_link( $term ) );
        }
    }
	//echo array_reverse( $crumbs );
    echo implode( '</a>', array_reverse( $crumbs ) );
	
	}
}

*/




function get_tags_in_use($category_ID, $type = 'name'){
    // Set up the query for our posts
    $my_posts = new WP_Query(array(
      'cat' => $category_ID, // Your category id
      'posts_per_page' => -1 // All posts from that category
    ));

    // Initialize our tag arrays
    $tags_by_id = array();
    $tags_by_name = array();
    $tags_by_slug = array();

    // If there are posts in this category, loop through them
    if ($my_posts->have_posts()): while ($my_posts->have_posts()): $my_posts->the_post();

      // Get all tags of current post
      $post_tags = wp_get_post_tags($my_posts->post->ID);

      // Loop through each tag
      foreach ($post_tags as $tag):

        // Set up our tags by id, name, and/or slug
        $tag_id = $tag->term_id;
        $tag_name = $tag->name;
        $tag_slug = $tag->slug;

        // Push each tag into our main array if not already in it
        if (!in_array($tag_id, $tags_by_id))
          array_push($tags_by_id, $tag_id);

        if (!in_array($tag_name, $tags_by_name))
          array_push($tags_by_name, $tag_name);

        if (!in_array($tag_slug, $tags_by_slug))
          array_push($tags_by_slug, $tag_slug);

      endforeach;
    endwhile; endif;

    // Return value specified
    if ($type == 'id')
        return $tags_by_id;

    if ($type == 'name')
        return $tags_by_name;

    if ($type == 'slug')
        return $tags_by_slug;
}
/***** END tag_cloud_by_category ***/


//add_action( 'widgets_init', 'orientacio_register_widget' );
/*
function orientacio_register_widget() {
	register_widget( 'orientacions_widget' );
}


class orientacions_widget extends WP_Widget {
	function __construct() {
		parent::__construct(
		 // widget ID
		 'orientacions_widget',
		 // widget name
		 __('Tags Orientacio', 'orientacions_widget_domain'),
		 // widget description
		 array( 'description' => __( 'Tags Orientanció', 'orientacions_widget_domain' ), )
	 );
	}	
	
	
	 public function widget( $args, $instance ) {
	 $title = apply_filters( 'widget_title', $instance['title'] );
	 echo $args['before_widget'];
	 //if title is present
	 if ( ! empty( $title ) )
		echo $args['before_title'] . $title . $args['after_title'];
		//output
		$orientacio = tags_orientacio();
	 	echo __( $orientacio, 'orientacions_widget_domain' );
	 	echo $args['after_widget'];
	}
	
	
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) )
			$title = $instance[ 'title' ];
		else
			$title = __( 'Default Title', 'orientacions_widget_domain' );
	}
}
*/


//add_shortcode( 'tags-orientacio-old', 'tags_orientacio_old' );
/*
function tags_orientacio_old(){
	//echo getCrumbsTags(  get_query_var('cat'),  get_query_var('category_name')  );
	$category_ID =  get_query_var('cat');
	$current = get_query_var('category_name');
	$i = 0;
	$link ='';	
  	if ( $term_ids = get_ancestors( get_queried_object_id(), 'category', 'taxonomy' ) ) {
    $crumbs = [];

	foreach ( $term_ids as $term_id ) {
        $term = get_term( $term_id, 'category' );

        //if ( $term && ! is_wp_error( $term ) ) {
         if ($i == 0){
            //$crumbs[] = sprintf( '<a href="%s">%s</a>', esc_url( get_term_link( $term ) ), esc_html( $term->name ) );
			//$links[]= esc_url( get_term_link( $term ) );
            //------<a href="https://triapedres.com/category/catalunya/barcelona/bages/">Bages</a>
            //------<a href="https://triapedres.com/tag/s/">S</a>
            //echo esc_url( get_term_link( $term )).'-->'.esc_html( $term->name );
			//if ($term_id === array_key_first($term_ids )) {
					//$link = sprintf( '<a href="%s">%s</a>', esc_url( get_term_link( $term ) ), esc_html( $term->name ) );
					$link = esc_url( get_term_link( $term ));
			//	}
        	}
		
		$i++;
    	}
	//return $lineCrumbs;
	
	}
	
	$link .=$current.'?tag=';
	
	/// tags 
	 $tags = get_tags_in_use($category_ID, 'id');
	$lineCrumbs = '<div class="div-tag">';	
	$lineCrumbs .= '<ul class="tags-tp">';
		// Cycle through each tag and set it up
    foreach ($tags as $tag){
		// Get tag name
        $tag_info = get_tag($tag);
        $tag_name = $tag_info->name;

        // Get tag link
        $tag_link = get_tag_link($tag);
		$size = 10;
		//$lineCrumbs .= '<span style="font-size:'.$size.'px;">';
		//$lineCrumbs .=<li><a class="tag-tp"
        //$lineCrumbs .= '<a href="'.$tag_link.'">'.$tag_name.'</a>';
		$lineCrumbs .= '<li><a class="tag-tp" href="'.$link.$tag_name.'">'.$tag_name.'</a></li>';
        //$lineCrumbs .= ' </span>';
	}
	$lineCrumbs .='</ul></div>';
	return $lineCrumbs;
}
*/


/*
function tags_orientacio(){
	$category_ID =  get_query_var('cat');
	$current = get_query_var('category_name');
	$i = 0;
	$link ='';	
  	if ( $term_ids = get_ancestors( get_queried_object_id(), 'category', 'taxonomy' ) ) {
    $crumbs = [];
	foreach ( $term_ids as $term_id ) {
		$term = get_term( $term_id, 'category' );
		if ($i == 0){
			$link = esc_url( get_term_link( $term ));
			}
		$i++;
		}
	} //end if ( $term_ids = get_ancestors
	
	$link .=$current.'?tag=';
	/-- tags 
	$tags = get_tags_in_use($category_ID, 'id');
	
	//--ordenar l'array
	$array_orden =  array(3,7,2,4,0,6,1,5);
	$tags_ordered = orderArrayTags($tags, $array_orden);
	
	
	
	$lineCrumbs = '<div class="div-tag">';	
	$lineCrumbs .= '<ul class="tags-tp">';
	$tag_selected = $tag = get_query_var('tag'); //get_queried_object_id();
		// Cycle through each tag and set it up
    foreach ($tags_ordered as $tag){
		// Get tag name
		$selected='';
        $tag_info = get_tag($tag);
        $tag_name = $tag_info->name;
		//if($tag_info->term_id === $tag_selected){
			 ($tag_name === strtoupper($tag_selected)) ? $selected="tag-cube selected": $selected="tag-cube";
		//}

        // Get tag link
        $tag_link = get_tag_link($tag);
		//$lineCrumbs .= '<li><a class="tag-cube" data-value="'.$tag_name.'"  href="'.$link.$tag_name.'">'.$tag_name.'</a></li>';
		$lineCrumbs .= '<li><a class="'.$selected.'" data-value="'.$tag_name.'"  href="'.$link.$tag_name.'">'.$tag_name.'</a></li>';
		//<a href="#" class="cube" data-value="N" >N</a>
	}
	$lineCrumbs .='</ul></div>';
	return $lineCrumbs;
		

}
*/

/*
function orderArrayTags($arrayToOrder, $keys) {
    $ordered = array();
    foreach ($keys as $key) {
        if (isset($arrayToOrder[$key])) {
             $ordered[$key] = $arrayToOrder[$key];
        }
    }
    return $ordered;
}
*/



// Custom_Walker_Category - in functions.php
// https://wordpress.org/support/topic/how-to-automatically-add-categories-sub-categories-in-wordpress-nav-menu/
// https://www.smashingmagazine.com/2015/10/customize-tree-like-data-structures-wordpress-walker-class/
class Custom_Walker_Category extends Walker_Category {
	
		function start_lvl(&$output, $depth=0, $args=array()) {
			$output .= "\n<ul class=\"sub-menu\">\n";
		}
 
		function end_lvl(&$output, $depth=0, $args=array()) {
			$output .= "</ul>\n";
		}
		
        function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
                extract($args);
                $cat_name = esc_attr( $category->name );
                $cat_name = apply_filters( 'list-cats', $cat_name, $category );
				$span = '<span class="ct-toggle-dropdown-desktop"><svg class="ct-icon" width="8" height="8" viewBox="0 0 15 15"><path d="M2.1,3.2l5.4,5.4l5.4-5.4L15,4.3l-7.5,7.5L0,4.3L2.1,3.2z"></path></svg></span>';
			
                $link = '<a href="' . esc_url( get_term_link($category) ) . '" ';
                if ( $use_desc_for_title == 0 || empty($category->description) )
                        $link .= 'class="ct-menu-link" title="' . esc_attr( sprintf(__( 'Veure totes les publicacions arxivades a %s' ), $cat_name) ) . '"';

				
                else
                        $link .= 'title="' . esc_attr( strip_tags( apply_filters( 'category_description', $category->description, $category ) ) ) . '"';
                $link .= '>';
				$button = '<button class="ct-toggle-dropdown-desktop-ghost" aria-label="Tancar el menú desplegable" aria-expanded="true"></button>';
                $link .= $cat_name . $span. '</a>'.$button ;
                if ( !empty($feed_image) || !empty($feed) ) {
                        $link .= ' ';
                        if ( empty($feed_image) )
                                $link .= '(';
                        $link .= '<a href="' . esc_url( get_term_feed_link( $category->term_id, $category->taxonomy, $feed_type ) ) . '"';
                        if ( empty($feed) ) {
                                $alt = ' alt="' . sprintf(__( 'Feed per a totes les publicacions arxivades a %s' ), $cat_name ) . '"';
                        } else {
                                $title = ' title="' . $feed . '"';
                                $alt = ' alt="' . $feed . '"';
                                $name = $feed;
                                $link .= $title;
                        }
                        $link .= '>';
                        if ( empty($feed_image) )
                                $link .= $name;
                        else
                                $link .= "<img src='$feed_image'$alt$title" . ' />';
                        $link .= '</a>';
                        if ( empty($feed_image) )
                                $link .= ')';
                }
                if ( !empty($show_count) )
                        $link .= ' (' . intval($category->count) . ')';
                if ( 'list' == $args['style'] ) {
                       $idterm = $category->term_id;
					   $output .= '<li id="menu-item-'.$idterm.'"';
					   //$un_class='menu-item menu-item-type-aau_ahcm menu-item-object-aau_ahcm';
					   $mes_class='menu-item menu-item-type-taxonomy menu-item-object-category menu-item-has-children';
						
                       $class = $mes_class. ' menu-item-' . $category->term_id. ' animated-submenu';
						
                        // YOUR CUSTOM CLASS
                        $termchildren = get_term_children( $category->term_id, $category->taxonomy );
                        if(count($termchildren)>0){
                            //$class .=  ' ct-active" data-submenu="left"';
                            $class .=  ' " data-submenu="left"';
                        } 
						else {
							 $class .=  '"';
						}
					
                        if ( !empty($current_category) ) {
                                $_current_category = get_term( $current_category, $category->taxonomy );
                                if ( $category->term_id == $current_category )
                                        $class .=  ' current-cat';
                                elseif ( $category->term_id == $_current_category->parent )
                                        $class .=  ' current-cat-parent';
                        }

                        $output .=  ' class="' . $class ;
                        $output .= ">$link\n";
                } else {
                        $output .= "\t$link<br />\n";
                }
        } // function start_el

} // class Custom_Walker_Category

//add_shortcode( 'get_menus_categories', 'get_menus_categories' );

/*
function get_queried_object2(){
	$excat1 = get_term_by( 'slug', 'highlight', 'category' );
	$exid1 = $excat1->term_id;
	$excat2 = get_term_by( 'slug', 'general', 'category' );
	$exid2 = $excat2->term_id;
	
	$args = array(
		'orderby' => 'slug',
		'show_count' => 0,
		'hierarchical' => 1, 
		'depth' => 5,
		'exclude' => $exid1.','.$exid2,
		'hide_empty' => 0, 
		'title_li' => '', 
		'hide_title_if_empty' => true,
		'walker' => new Custom_Walker_Category(),
	);

	echo '<ul id="menu-primary" class="menu">';
  		wp_list_categories($args);
	echo '</ul>';
	
}
*/