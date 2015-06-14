<?php
require_once("../../php/administration/project.php");

if (isset($_GET["project"])) {
	$project_directory = $_GET["project"];
	$project           = new Project($project_directory);

	$name = $project->get_name();
	$pdf  = $project->get_pdf();

	if ($pdf != false) {
		header("Content-Description: TeXView - PDF");
		header("Content-Type: application/pdf");
		header("Content-Length: " . filesize($pdf));

		if (isset($_GET["save"])) {
			header("Content-Disposition: attachment; filename=$name.pdf");
		}

		readfile($pdf);
	} else {
		echo "Your project isn't compiling. You can't download uncompiled projects. ";
	}
} 
?>