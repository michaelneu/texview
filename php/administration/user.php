<?php
require_once("../../php/database/database.php");

# Provides methods for user management
class User {
	# returns whether the user's credentials are correct
	static function check_credentials($username, $password) {
		$username = base64_encode($username);
		$password = sha1($password);

		$database = new Database();
		$iterator = $database->query("
			SELECT 
				password

			FROM 
				users

			WHERE 
				username = '$username';");

		while ($iterator->has_next()) {
			$row = $iterator->next();

			if ($row["password"] == $password) {
				return true;
			}
		}

		return false;
	}

	# sets the login flag
	static function login($username) {
		$_SESSION["login"] = array(
			"username" => $username,
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
		$username = base64_encode($_SESSION["login"]["username"]);
		$projects = array();

		$database = new Database();
		$iterator = $database->query("
			SELECT 
				projects.*

			FROM 
				projects 

			JOIN (users)
				ON users.id = projects.ref_owner

			WHERE 
				users.name = '$username';");

		while ($iterator->has_next()) {
			$row = $iterator->next();

			$projects[] = array(
				"directory" => $row["directory"],
				"name" => $row["name"]
			);
		}

		return $projects;
	}
}

?>