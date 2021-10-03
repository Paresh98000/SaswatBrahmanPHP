<?php

/**
 * Create data-structure
 * 
 * -- Calendar festival List Calendar Patrika
 * This table store festival information
 * this is also for dynamic users
 * -- Relationship table for user and calender festivals
 * 
 * -- Appointments Table
 * Appointment Request from one user to another stored here
 * 
 * -- Services Table
 * users created services will stored here
 * 
 * -- Samgari List Table
 * Lists that are required for Yajaman
 * We will store userids with this list to view only on that user
 * 
 * Show default lists
 * 
 * need other table to store lists
 * -- lists names table (names may be same but not an id of it)
 * -- relationship table of user and list names id (that are added after default)
 * -- list items with list name id
 * 
 * -- Schedule
 *  user schedules for future
 *  if user confirms any appointment than that will be added in schedule
 * 
 */


function install() {

	$Calendar_Festival_List = "ex_ps_calendar_festivals";
	$Relation_Calendar_Festival_List = "ex_ps_calendar_relation";
	$Service = 'ex_ps_service'; // service info
	$Service_Type = 'ex_ps_service_type';
	$Relation_Service = "ex_ps_service_relation"; // author of service
	$Appointment = 'ex_ps_appointment'; // appointment-serviceid info
	$Relation_Appointment = ''; // appointment requester user

	$ex_ps_db_version = '1.0';

	global $wpdb;

	// table 1 Calendar Festival Details
	$table_name = $wpdb->prefix . $Calendar_Festival_List;
	
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		id bigint NOT NULL AUTO_INCREMENT,
		festival_date date DEFAULT '0000-00-00' NOT NULL,
		festival_name tinytext NOT NULL,
		guj_month int(2) Not Null,
		festival_category tinytext default '',
		PRIMARY KEY  (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	// table 2 Relationship Calendar
	$table_name = $wpdb->prefix . $Relation_Calendar_Festival_List;

	$sql = "CREATE TABLE $table_name (
		id bigint NOT NULL AUTO_INCREMENT,
		userid bigint NOT NULL,
		festivalid bigint NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

	dbDelta( $sql );

	// table 3 Service
	$table_name = $wpdb->prefix . $Service;

	$sql = "CREATE TABLE $table_name (
		id bigint NOT NULL AUTO_INCREMENT,
		service_title tinytext NOT NULL,
		service_type int NOT NULL,
		service_details text NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

	dbDelta( $sql );

	// table 4 Service Type
	$table_name = $wpdb->prefix . $Service_Type;

	$sql = "CREATE TABLE $table_name (
		id int NOT NULL AUTO_INCREMENT,
		service_type_name tinytext NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

	dbDelta( $sql );

	// table 5 Relation Service
	$table_name = $wpdb->prefix . $Relation_Service;

	$sql = "CREATE TABLE $table_name (
		id int NOT NULL AUTO_INCREMENT,
		userid tinytext NOT NULL,
		serviceid int NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

	dbDelta( $sql );

	add_option( 'ex_ps_db_version', $ex_ps_db_version );
}

function install_data() {
	// global $wpdb;
	
	// table 1
	$table_name = $wpdb->prefix . $Calendar_Festival_List;
	
	// $wpdb->insert( 
	// 	$table_name, 
	// 	array( 
	// 		'festival_date' => current_time( 'mysql' ), 
	// 		'festival_name' => $welcome_name, 
	// 		'guj_month' => $welcome_text, 
	// 	) 
	// );

	// table 2
	$table_name = $wpdb->prefix . $Calendar_Festival_List;
	
	// $wpdb->insert( 
	// 	$table_name, 
	// 	array( 
	// 		'time' => current_time( 'mysql' ), 
	// 		'name' => $welcome_name, 
	// 		'text' => $welcome_text, 
	// 	) 
	// );
}