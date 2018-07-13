<?php
/*
Plugin Name: Cotelco Plugin
Plugin URI:  https://github.com/japhfortin/cotelco-plugin
Description: Additional codes for cotelco website
Version:     1.0
Author:      Japh Fortin
Author URI:  http://codezenthia.com
License:     GPL2
 
{Cotelco Plugin} is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
{Cotelco Plugin} is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with {Cotelco Plugin}. If not, see {License URI}.
*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
define('CP_PLUGIN_URL', plugin_dir_url( __FILE__ ));

// on install
register_activation_hook( __FILE__, 'cot_create_tables' );

function cotelco_plugin_css() {
	wp_enqueue_style( 'cp-shortcodes',  CP_PLUGIN_URL . "css/shortcodes.css");
}
add_action( 'wp_enqueue_scripts', 'cotelco_plugin_css', 999 );

global $cot_db_version;	
$cot_db_version = '1.0';

function cot_create_tables() {
	global $wpdb;
	global $cot_db_version;

	$tbl_accounts = $wpdb->prefix . 'cotelco_accounts';
	
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $tbl_accounts (  
		`account_no` VARCHAR(20) NOT NULL , 
		`name` TEXT NULL , 
		`address` TEXT NULL , 
		`meter` VARCHAR(250) NULL , 
		`type` VARCHAR(250) NULL , 
		`status` VARCHAR(20) NULL , 
		`mid` VARCHAR(20) NULL , 
		`district` VARCHAR(250) NULL , 
		`bapa` BOOLEAN NULL , 
		PRIMARY KEY (`account_no`)) $charset_collate;";

	$tbl_ledger = $wpdb->prefix . 'cotelco_ledger';
	$sql .= "CREATE TABLE $tbl_ledger ( 
		`account_no` VARCHAR(20) NOT NULL , 
		`date` DATE NOT NULL , 
		`reference` VARCHAR(100) NOT NULL , 
		`kwhused` INT(10) NOT NULL DEFAULT '0' , 
		`debit` VARCHAR(20) NOT NULL DEFAULT '0' , 
		`credit` VARCHAR(20) NOT NULL DEFAULT '0' , 
		`balance` VARCHAR(20) NOT NULL DEFAULT '0' ,
		FOREIGN KEY (`account_no`) REFERENCES $tbl_accounts(`account_no`)) $charset_collate;";
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	dbDelta( $sql );
	add_option( 'cot_db_version', $cot_db_version );
}

// include files
include_once('includes/functions.php');
include_once('includes/form-registration.php');
include_once('includes/shortcodes.php');