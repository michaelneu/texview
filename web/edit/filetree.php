<?php
require_once("../../php/administration/user.php");
require_once("../../php/administration/project.php");
require_once("../../php/security/bouncer.php");

if (!isset($_GET["project"]) or !isset($_GET["token"])) {
	die("{}");
}

$texview_id = $_GET["project"];
$project = new Project($texview_id);
$token = $project->get_token();
$name = $project->get_name();

if ($_GET["token"] != $token or strlen($name) == 0) {
	die("{}");
}

$tree = $project->get_directory_tree();
echo json_encode((object)$tree);

?>