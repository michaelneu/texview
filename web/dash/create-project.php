<?php
require_once("../../php/administration/user.php");
require_once("../../php/administration/project.php");
require_once("../../php/security/bouncer.php");

if (isset($_POST["project-name"])) {
	$user_id = $_SESSION["login"]["id"];
	$name = $_POST["project-name"];

	$project_information = Project::create($user_id, $name);

	$directory = $project_information["directory"];
	$token     = $project_information["token"];
	$url       = sprintf("../edit/?projects=%s&token=%s", $directory, $token);
	
	header("Location: $url");
}
?>