<?php
require_once("../../php/administration/user.php");
require_once("../../php/security/bouncer.php");

if (isset($_POST["new-password"])) {
	$password = $_POST["new-password"];
	$id       = User::get_id();

	User::update_password($id, $password);
	header("Location: index.php");
} else if (isset($_GET["user"])) {
	$user = $_GET["user"];
	$password = User::randomize_password($user);

	$data = array(
		"password" => $password
	);
	
	print json_encode((object)$data);
}

?>