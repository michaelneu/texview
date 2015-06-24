<?php
require_once("../../php/administration/user.php");
require_once("../../php/security/bouncer.php");

if (isset($_POST["username"])) {
	$username = $_POST["username"];
	$password = User::create_user($username);
	
	header("Location: index.php");
}

?>