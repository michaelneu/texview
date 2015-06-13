<?php
require_once("sqlite.php");
require_once("../../config.php");
require_once("../..//php/util/io.php");

# Wraps the actual database connection to avoid multiple PDO instances when a single one is sufficient
class Database {
	static $__connection;

	function __construct() {
		if (self::$__connection == null) {
			$database = path_join($GLOBALS["root_path"], "php/database.sqlite3");

			self::$__connection = new SQLite($database);
		}
	}

	function query($query) {
		if (self::$__connection == null) {
			die("<b>Fatal error:</b> <i>Database::query</i> failed, no database connection\n");
		}

		return self::$__connection->query($query);
	}

	function exec($query) {
		if (self::$__connection == null) {
			die("<b>Fatal error:</b> <i>Database::exec</i> failed, no database connection\n");
		}

		self::$__connection->exec($query);
	}
}

# optionally wipe the database
if (isset($GLOBALS["wipe_db"]) and $GLOBALS["wipe_db"]) {
	require("wipe.php");
}

?>