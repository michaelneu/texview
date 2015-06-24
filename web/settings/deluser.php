<?php
require_once("../../php/administration/user.php");
require_once("../../php/security/bouncer.php");

if (isset($_GET["user"])) {
	$id = $_GET["user"];
	User::delete_user($id);
	
	header("Location: index.php");
}

?>