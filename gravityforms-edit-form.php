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
define( 'GFEF_PATH', plugin_dir_path( __FILE__ ) );

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

/**
 * Include some base adapters
 */
include_once( GFEF_PATH . 'adapters/gravityforms.php' );
include_once( GFEF_PATH . 'adapters/cf7.php' );
include_once( GFEF_PATH . 'adapters/ninjaforms.php' );
include_once( GFEF_PATH . 'adapters/formidable.php' );
