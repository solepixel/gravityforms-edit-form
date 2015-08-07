<?php

add_filter( 'gfef_schemas', 'gfef_cf7_schema' );

function gfef_cf7_schema( $schemas = array() ){
	$schemas['cf7'] = array(
		'callback' => 'gfef_lookup_cf7',
		'menus' => array(
			'edit' => array(
				'url' => admin_url() . 'admin.php?page=wpcf7&post={0}&action=edit',
				'label' => 'Edit Form'
			)
		)
	);
	return $schemas;
}

add_action( 'wp_ajax_gfef_lookup_cf7', 'gfef_lookup_cf7' );
add_action( 'wp_ajax_nopriv_gfef_lookup_cf7', 'gfef_lookup_cf7' );

function gfef_lookup_cf7(){
	$response = array();
	$form_id = isset( $_GET['form_id'] ) ? sanitize_text_field( $_GET['form_id'] ) : '';

	if( $form_id ){
		$form_title = get_the_title( $form_id );
		if( $form_title )
			$response['form_title'] = $form_title;
	}

	wp_send_json( $response );
}
