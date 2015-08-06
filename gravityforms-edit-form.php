<?php
/*
Plugin Name: Gravity Forms Edit Form
Plugin URI: https://briandichiara.com
Description: Adds a simple "Edit Form" link to admin bar
Version: 0.0.1
Author: Brian DiChiara
Author URI: http://briandichiara.com
*/

/**
 * Insert all the menu bar items
 * @param  string $form_id Numeric form ID
 * @return void
 */
function gfef_add_gf_admin_bar_node( $form_id ){
	global $wp_admin_bar;

	// Edit Form
	$args = array(
		'id' => 'gfef',
		'title' => __( 'Edit Form', 'gfef' ),
		'href' => admin_url() . 'admin.php?page=gf_edit_forms&id=' . $form_id
	);

	$wp_admin_bar->add_node( $args );

	// Form Settings
	$args = array(
		'id' => 'gfef-settings',
		'title' => __( 'Form Settings', 'gfef' ),
		'href' => admin_url() . 'admin.php?page=gf_edit_forms&view=settings&id=' . $form_id,
		'parent' => 'gfef'
	);

	$wp_admin_bar->add_node( $args );

	// Form Entries
	$args = array(
		'id' => 'gfef-entries',
		'title' => __( 'Form Entries', 'gfef' ),
		'href' => admin_url() . 'admin.php?page=gf_entries&id=' . $form_id,
		'parent' => 'gfef'
	);

	$wp_admin_bar->add_node( $args );
}

add_action( 'admin_bar_menu', 'gfef_detect_gravityform_shortcode', 85 );

/**
 * Add Admin Menu node if GravityForm is detected
 * action: admin_bar_menu
 * @return void
 */
function gfef_detect_gravityform_shortcode(){
	global $wp_admin_bar;
	// bail if there is no wp_admin_bar
	if( ! $wp_admin_bar )
		return;

	$form_id = false;

	$content = get_the_content();

	if( has_shortcode( $content, 'gravityform' ) ){
		$form_id = gfef_get_form_id_from_content( $content );
	} else {
		$form_id = gfef_get_form_id_from_postmeta();
	}

	if( $form_id === false )
		return;

	gfef_add_gf_admin_bar_node( $form_id );
}

/**
 * Retrieve the form ID from the content
 * @param  string $content HTML post content
 * @return mixed          String Form ID if found, false if not
 */
function gfef_get_form_id_from_content( $content ){
	$form_id = false;
	$atts = shortcode_parse_atts( $content );
	$found = false;

	foreach( $atts as $key => $att ):
		if( ! $found && strpos( $att, '[gravityform' ) !== false )
			$found = true;

		if( ! $found )
			continue;

		if( is_string( $key ) && $key == 'id' ){
			$form_id = $att;
			break;
		}
	endforeach;

	return $form_id;
}

/**
 * Retrieve a Gravity Form ID from Post Meta
 * @return mixed 	String Form ID if found, otherwise false
 */
function gfef_get_form_id_from_postmeta(){
	$post_meta = get_post_meta( get_the_ID() );
	return gfef_get_form_id_from_array( $post_meta );
}

/**
 * Search an array for gravityform shortcode
 * @param  array $array
 * @return mixed        String Form ID if found, otherwise false
 */
function gfef_get_form_id_from_array( $array ){
	$form_id = false;

	foreach( $array as $key => $value ):
		if( is_array( $value ) ){
			$form_id = gfef_get_form_id_from_array( $value );
		} elseif( is_string( $value ) ) {
			if( has_shortcode( $value, 'gravityform' ) )
				$form_id = gfef_get_form_id_from_content( $value );
		}
		if( $form_id ) // let's quit when we find one.
			break;
	endforeach;

	return $form_id;
}
