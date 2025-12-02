<?php
// Para usuarios autenticados.
add_action( 'wp_ajax_procesa_link', 'procesa_link' );
// Para usuarios NO autenticados.
add_action( 'wp_ajax_nopriv_procesa_link', 'procesa_link' );

function procesa_link() {
	$nonce = wp_create_nonce( "link_click_counter_".$_POST['post_id']);
	echo $nonce;
	wp_die();	
}
?>