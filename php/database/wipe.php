<?php

// double check if wipe is set to true
if (isset($GLOBALS["wipe_db"]) and $GLOBALS["wipe_db"]) {
	// exit if no root user is specified
	if (!isset($GLOBALS["root"])) {
		die("<b>Fatal error:</b> Wipe failed because no root user was specified\n");
	}

	// remove old database
	$database_path = path_join($GLOBALS["root_path"], "php/database.sqlite3");
	unlink($database_path);

	// create database structure
	$database = new Database();
	$database->exec("CREATE TABLE users (id INTEGER PRIMARY KEY, username TEXT, password TEXT);");
	$database->exec("CREATE TABLE projects (id INTEGER PRIMARY KEY, ref_owner INTEGER, directory TEXT, name TEXT, edit_token TEXT);");

	// create root user
	$root = $GLOBALS["root"];
	$root_username = base64_encode($root["username"]);
	$root_password = sha1($root["password"]);
	
	$database->exec("INSERT INTO users (username, password) VALUES ('$root_username', '$root_password');");
}

?>