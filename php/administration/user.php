<?php
require_once("../../php/database/database.php");

/**
 * Provides methods for user management
 */
class User {
	/**
	 * Check if the given credentials are correct
	 * 
	 * @param  string    $username The username in plain text
	 * @param  string    $password The password in plain text
	 * @return int|bool            The user's id if the credentials are correct, otherwise false
	 */
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

		return false;
	}

	/**
	 * Set the login flag
	 * 
	 * @param string $username The user's name to log in
	 * @param int    $id       The user's id to use for login
	 */
	static function login($username, $id) {
		$_SESSION["login"] = array(
			"username" => $username,
			"id" => $id,
			"time" => time(),
			"ip" => $_SERVER["REMOTE_ADDR"]
		);
	}

	/**
	 * Unset the login flag
	 */
	static function logout() {
		unset($_SESSION["login"]);
	}

	/**
	 * Get if the user is logged in
	 * 
	 * @return bool true if the user is logged in, false otherwise
	 */
	static function is_logged_in() {
		return isset($_SESSION["login"]);
	}

	/**
	 * Get all projects owned by the current user
	 * 
	 * @return array An array with the user's projects
	 */
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


	/**
	 * Get the current user's id
	 * 
	 * @return int The current user's id
	 */
	static function get_id() {
		return $_SESSION["login"]["id"];
	}

	/**
	 * Check if the current user has root privileges
	 * @return bool If the user is root (uid = 1)
	 */
	static function is_root() {
		return User::get_id() == 1;
	}

	/**
	 * Create a new user with the given username and returns the pseudo-random password
	 * 
	 * @param  string $username The new username
	 * @return string The generated password
	 */
	static function create_user($username) {
		if (User::is_root()) {
			$username = base64_encode($username);
			$password = User::generate_random_password();
			$password_hash = sha1($password);

			$database = new Database();
			$database->exec("
				INSERT INTO 
					users

				(
					username, 
					password
				)

				VALUES (
					'$username',
					'$password_hash'
				);");

			return $password;
		} else {
			return false;
		}
	}

	/**
	 * Delete a user
	 * 
	 * @param  int $id The id of the user which should be deleted
	 */
	static function delete_user($id) {
		if (User::is_root()) {
			$id += 0;

			$database = new Database();
			$database->exec("
				DELETE FROM
					users

				WHERE
					id = $id;");
		}
	}

	/**
	 * Update a user's password
	 * 
	 * @param int    $id       The user's id whose password should be updated
	 * @param string $password The new password in plain text
	 */
	static function update_password($id, $password) {
		$id += 0;
		$password = sha1($password);

		$database = new Database();
		$database->exec("
			UPDATE
				users

			SET 
				password = '$password'

			WHERE 
				id = $id;");
	}

	/**
	 * Generate a pseudo-random password
	 * 
	 * @return string A pseudo-random password in plain text
	 */
	static function generate_random_password() {
		return base_convert(microtime(), 10, 36);
	}

	/**
	 * Randomize a given user's password
	 * 
	 * @param  int    $id The user's id whose password should be randomized
	 * @return string The pseudo-random password in plain text
	 */
	static function randomize_password($id) {
		if (User::is_root()) {
			$id += 0;

			$password = User::generate_random_password();
			User::update_password($id, $password);

			return $password;
		} else {
			return false;
		}
	}


	/**
	 * Get all known users
	 * 
	 * @return array The known users as an array
	 */
	static function get_all_users() {
		$users = array();

		$database = new Database();
		$iterator = $database->query("
			SELECT
				username,
				id

			FROM 
				users;");

		while ($iterator->has_next()) {
			$row = $iterator->next();

			$name = base64_decode($row["username"]);
			$id   = $row["id"];

			if ($id != 1) {
				$users[] = array(
					"username" => $name,
					"id"       => $id
				);
			}
		}

		return $users;
	}
}

?>