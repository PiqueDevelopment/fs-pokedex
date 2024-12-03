<?php
	/* Display Errors */
	//ini_set('display_errors', 1);
	//ini_set('display_startup_errors', 1);
	//error_reporting(E_ALL);
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	mysqli_report(MYSQLI_REPORT_ERROR );


	/* Change for your username and password for phpMyAdmin*/
	define('DB_SERVER', 'classmysql.engr.oregonstate.edu');
	define('DB_USERNAME', 'cs340_bernamic');
	define('DB_PASSWORD', '8105');
	define('DB_NAME', 'cs340_bernamic');
	 
	/* Attempt to connect to MySQL database */
	$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
	 
	// Check connection
	if($link === false){
		die("ERROR: Could not connect. " . mysqli_connect_error());
	}
?>
