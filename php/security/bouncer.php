<?php
session_start();

$logged_in = User::is_logged_in();

if (!$logged_in) {
	header("Location: ../login");
} else if ($logged_in and isset($_GET["logout"])) {
	User::logout();
	header("Location: ../");
}


?>