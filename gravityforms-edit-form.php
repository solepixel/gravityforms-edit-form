<?php
/*
Plugin Name: Gravity Forms Edit Form
Plugin URI: https://briandichiara.com
Description: Adds a simple "Edit Form" link to admin bar
Version: 0.1.1
Author: Brian DiChiara
Author URI: http://briandichiara.com
*/

define( 'GFEF_VERSION', '0.1.1' );
define( 'GFEF_URL', plugin_dir_url( __FILE__ ) );

add_action( 'admin_bar_init', 'gfef_enqueue_script' );
#add_action( 'wp_enqueue_scripts', 'gfef_enqueue_script' );

function gfef_enqueue_script(){
	wp_register_script( 'gfef-admin-bar', GFEF_URL . 'js/admin-bar.js', array( 'admin-bar', 'jquery' ), GFEF_VERSION );
	wp_localize_script( 'gfef-admin-bar', 'gfef_vars', array(
		'schemas' => json_encode( apply_filters( 'gfef_schemas', array() ), JSON_FORCE_OBJECT ),
		'ajax_url' => admin_url() . 'admin-ajax.php'
	));

	wp_enqueue_script( 'gfef-admin-bar' );
}

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
