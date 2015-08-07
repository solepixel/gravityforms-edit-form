<?php

add_filter( 'gfef_schemas', 'gfef_formidable_schema' );

function gfef_formidable_schema( $schemas = array() ){
	$schemas['formidable'] = array(
		'callback' => 'gfef_lookup_formidable',
		'menus' => array(
			'edit' => array(
				'url' => admin_url() . 'admin.php?page=formidable&frm_action=edit&id={0}',
				'label' => 'Edit Form'
			),
			'settings' => array(
				'url' => admin_url() . 'admin.php?page=formidable&frm_action=settings&id={0}',
				'label' => 'Form Settings'
			),
			'entries' => array(
				'url' => admin_url() . 'admin.php?page=formidable-entries&frm_action=list&form={0}',
				'label' => 'Form Entries'
			)
		)
	);
	return $schemas;
}

add_action( 'wp_ajax_gfef_lookup_formidable', 'gfef_lookup_formidable' );
add_action( 'wp_ajax_nopriv_gfef_lookup_formidable', 'gfef_lookup_formidable' );

function gfef_lookup_formidable(){
	$response = array();
	$form_id = isset( $_GET['form_id'] ) ? sanitize_text_field( $_GET['form_id'] ) : '';

	if( $form_id ){
		if( class_exists( 'FrmField' ) ){
			$form = FrmField::get_all_for_form( $form_id );
			if( is_array( $form ) )
				$form = reset( $form );
			if( isset( $form->form_name ) && $form->form_name )
				$response['form_title'] = $form->form_name;
		}
	}

	wp_send_json( $response );
}
