<?php
require_once("../../php/administration/user.php");
require_once("../../php/administration/project.php");
require_once("../../php/security/bouncer.php");

if (!isset($_POST["project"]) or !isset($_POST["token"]) or !isset($_POST["file"]) or !isset($_POST["content"])) {
	die();
}

$texview_id = $_POST["project"];
$project = new Project($texview_id);
$token = $project->get_token();
$name  = $project->get_name();

$file    = realpath($_POST["file"]);
$src_dir = path_join($project->dirname, "src");

$file_in_src_dir = strpos($file, $src_dir) !== false;

if ($_POST["token"] != $token or strlen($name) == 0 or !$file_in_src_dir) {
	die();
}

file_put_contents($_POST["file"], $_POST["content"]);

?>