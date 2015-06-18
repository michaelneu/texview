<?php
require_once("../../php/administration/user.php");
require_once("../../php/administration/project.php");
require_once("../../php/security/bouncer.php");

if (!isset($_GET["project"]) or !isset($_GET["token"]) or !isset($_GET["file"])) {
	die();
}

$texview_id = $_GET["project"];
$project = new Project($texview_id);
$token = $project->get_token();
$name  = $project->get_name();

if ($_GET["token"] != $token or strlen($name) == 0) {
	die();
}

$filename = realpath($_GET["file"]);

# check if the given filename starts with the project directory
if (strpos($filename, $project->dirname) === 0) {
	header("Content-type: text/plain");
	readfile($filename);
}

?>