<?php
require_once("../../php/administration/user.php");
require_once("../../php/administration/project.php");
require_once("../../php/security/bouncer.php");

if (isset($_POST["project-id"])) {
	$user_id = $_SESSION["login"]["id"];
	$project_id = $_POST["project-id"];
	Project::delete($user_id, $project_id);

	header("Location: index.php");
}
?>