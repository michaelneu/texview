<?php
set_time_limit(0);
$time_limit = 10;

require_once("../../php/administration/project.php");

if (isset($_GET["project"])) {
	$updateRequired = false;
	
	$project = new Project($_GET["project"]);
	$last_information = $project->get_information();

	if (isset($_GET["force"])) {
		$updateRequired = true;
	} else {
		for ($i = 0; $i < $time_limit; $i++) {
			$new_information = $project->get_information();

			# check information for differences
			$diff_compile_time = $new_information["time"] != $last_information["time"];
			$diff_status       = $new_information["status"] != $last_information["status"];

			# reassign the information
			$last_information = $new_information;

			if ($diff_compile_time || $diff_status) {
				$updateRequired = true;

				break;
			} else {
				sleep(1);
			}
		}
	}


	$information = array(
		"status"         => $last_information["status"],
		"reloadRequired" => $updateRequired
	);

	$json = json_encode((object)$information);
	echo $json;
}



?>