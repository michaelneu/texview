<?php
require_once("../../php/administration/user.php");
require_once("../../php/administration/project.php");
require_once("../../php/security/bouncer.php");

if (isset($_POST["project-name"])) {
	$user_id = $_SESSION["login"]["id"];
	$name = $_POST["project-name"];

	$project_directory = Project::create($user_id, $name);
	header("Location: ../edit/?project=" . $project_directory);
}
?>