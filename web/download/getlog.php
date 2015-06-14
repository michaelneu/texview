<?php
require_once("../../php/administration/project.php");

if (isset($_GET["project"])) {
	$project_directory = $_GET["project"];
	$project           = new Project($project_directory);

	$name = $project->get_name();
	$log  = $project->logfile;

	if (file_exists($log)) {
		header("Content-Description: TeXView - LaTeX log file");
		header("Content-Type: text/plain");
		header("Content-Length: " . filesize($log));

		readfile($log);
	}
} 
?>