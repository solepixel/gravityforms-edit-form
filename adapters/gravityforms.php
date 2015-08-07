<?php

add_filter( 'gfef_schemas', 'gfef_gravityforms_schema' );

function gfef_gravityforms_schema( $schemas = array() ){
	$schemas['gravityforms'] = array(
		'callback' => 'gfef_lookup_gravityform',
		'menus' => array(
			'edit' => array(
				'url' => admin_url() . '?page=gf_edit_forms&id={0}',
				'label' => 'Edit Form'
			),
			'settings' => array(
				'url' => admin_url() . '?page=gf_edit_forms&view=settings&id={0}',
				'label' => 'Form Settings'
			),
			'entries' => array(
				'url' => admin_url() . '?page=gf_entries&id={0}',
				'label' => 'Form Entries'
			)
		)
	);
	return $schemas;
}

add_action( 'wp_ajax_gfef_lookup_gravityform', 'gfef_lookup_gravityform' );
add_action( 'wp_ajax_nopriv_gfef_lookup_gravityform', 'gfef_lookup_gravityform' );

function gfef_lookup_gravityform(){
	$response = array();
	$form_id = isset( $_GET['form_id'] ) ? sanitize_text_field( $_GET['form_id'] ) : '';

	if( $form_id ){
		if( class_exists( 'GFAPI' ) ){
			$form = GFAPI::get_form( $form_id );
			if( $form['title'] )
				$response['form_title'] = $form['title'];
		}
	}

	wp_send_json( $response );
}
