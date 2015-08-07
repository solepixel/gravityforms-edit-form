<?php

add_filter( 'gfef_schemas', 'gfef_ninjaforms_schema' );

function gfef_ninjaforms_schema( $schemas = array() ){
	$schemas['ninjaforms'] = array(
		'callback' => 'gfef_lookup_ninjaform',
		'menus' => array(
			'edit' => array(
				'url' => admin_url() . '?page=ninja-forms&tab=builder&form_id={0}',
				'label' => 'Edit Form'
			),
			'settings' => array(
				'url' => admin_url() . '?page=ninja-forms&tab=form_settings&form_id={0}',
				'label' => 'Form Settings'
			),
			'submissions' => array(
				'url' => admin_url() . 'edit.php?post_status=all&post_type=nf_sub&form_id={0}',
				'label' => 'Form Submissions'
			)
		)
	);
	return $schemas;
}

add_action( 'wp_ajax_gfef_lookup_ninjaform', 'gfef_lookup_ninjaform' );
add_action( 'wp_ajax_nopriv_gfef_lookup_ninjaform', 'gfef_lookup_ninjaform' );

function gfef_lookup_ninjaform(){
	$response = array();
	$form_id = isset( $_GET['form_id'] ) ? sanitize_text_field( $_GET['form_id'] ) : '';

	if( $form_id ){
		if( function_exists( 'ninja_forms_get_form_by_field_id' ) ){
			$form = ninja_forms_get_form_by_field_id( $form_id );
			if( isset( $form['data']['form_title'] ) && $form['data']['form_title'] )
				$response['form_title'] = $form['data']['form_title'];
		}
	}

	wp_send_json( $response );
}
