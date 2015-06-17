<?php
require_once("../../php/database/database.php");

# Provides methods for user management
class User {
	# returns the user's id if the given credentials are correct
	static function check_credentials($username, $password) {
		$username = base64_encode($username);
		$password = sha1($password);

		$database = new Database();
		$iterator = $database->query("
			SELECT 
				password, id

			FROM 
				users

			WHERE 
				username = '$username';");

		while ($iterator->has_next()) {
			$row = $iterator->next();

			if ($row["password"] == $password) {
				return $row["id"];
			}
		}

		return -1;
	}

	# sets the login flag
	static function login($username, $id) {
		$_SESSION["login"] = array(
			"username" => $username,
			"id" => $id,
			"time" => time(),
			"ip" => $_SERVER["REMOTE_ADDR"]
		);
	}

	# unsets the login flag
	static function logout() {
		unset($_SESSION["login"]);
	}

	# returns whether the login flag is set
	static function is_logged_in() {
		return isset($_SESSION["login"]);
	}

	# returns the projects owned by the logged in user
	static function get_projects() {
		$id = $_SESSION["login"]["id"];
		$projects = array();

		$database = new Database();
		$iterator = $database->query("
			SELECT 
				*

			FROM 
				projects 

			WHERE 
				ref_owner = $id;");

		while ($iterator->has_next()) {
			$row = $iterator->next();

			$projects[] = array(
				"directory" => $row["directory"],
				"name" => base64_decode($row["name"]),
				"id" => $row["id"],
				"token" => $row["edit_token"]
			);
		}

		return $projects;
	}
}

?>