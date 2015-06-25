<?php
require_once("../../php/administration/user.php");
require_once("../../php/security/bouncer.php");
require_once("../../php/administration/project.php");

$projects = User::get_projects();

?>
<!doctype html>
<html>
	<head>
		<title>Dashboard &middot; TeXView</title>
		
		<link rel="stylesheet" href="../assets/libs/Semantic-UI/dist/semantic.min.css" />
		<link rel="stylesheet" href="../assets/css/semantic-fixes.css" />
		<link rel="stylesheet" href="../assets/css/dashboard.css" />
	</head>

	<body>
		<!-- <navbar> -->
		<div class="ui green inverted fixed menu">
			<div class="container">
				<a class="active item" href="index.php"><i class="align left icon"></i> Dashboard</a>
				<a class="item" href="../settings"><i class="align left icon"></i> Settings</a>

				<div class="right menu">
					<a class="item" href="?logout"><i class="share icon"></i> Logout</a>
				</div>
			</div>
		</div>
		<!-- </navbar> -->

		<!-- <main container> -->
		<div class="container">

			<!-- <grid> -->
			<div class="ui grid">
				<div class="row">

					<!-- <projects> -->
					<div class="ten wide column">
						<div class="ui segment">
							<h2>Projects</h2>

							<div class="projects">
<?php

foreach ($projects as $project) {
	$directory = $project["directory"];
	$token     = $project["token"];

	$project_information = new Project($directory);

	$name  = $project["name"];
	$color = $project_information->get_compile_status();

	$last_compile = $project_information->get_last_compile();

	if ($last_compile == 0) {
		$date = "&dash;";
		$time = "&dash;";
	} else {
		$date = date("Y-m-d", $last_compile);
		$time = date("H:i:s", $last_compile);
	}

	switch ($color) {
		case "SUCCESS": 
			$icon = "check circle";
			break;

		case "FAIL": 
			$icon = "remove circle";
			break;

		default: 
			$icon = "meh";
	}

	echo "
								<!-- <project> -->
								<a class=\"project\" href=\"../edit/?project=$directory&token=$token\">
									<div class=\"text\">
										<div class=\"$color compile\">
											<i class=\"ui $icon icon\"></i>
										</div>

										<b>$name</b>


										<!-- <compile information> -->
										<div class=\"time\">
											<span>
												<i class=\"calendar icon\"></i>
												$date
											</span>

											<span>
												<i class=\"time icon\"></i>
												$time
											</span>
										</div>
										<!-- </compile information> -->
									</div>
								</a>
								<!-- </project> -->
";
}

if (count($projects) == 0) {
	echo "
								<div class=\"center-aligned no projects\">
									It seems you <b>don't have any projects</b> yet. 
									<br>
									Go ahead and create one
								</div>
";
}

?>
							</div>
						</div>
					</div>
					<!-- </projects> -->

					<!-- <project management> -->
					<div class="six wide column">
						<div class="ui segment">

							<!-- <create project> -->
							<h2>New Project</h2>

							<form class="ui form" method="post" action="create-project.php">
								<div class="field">
									<input type="text" name="project-name" placeholder="Project title" required>
								</div>

								<div class="field">
									<button type="submit" class="ui right labeled green fluid icon button">
										<i class="plus icon"></i>
										Create
									</button>
								</div>
							</form>
							<!-- </create project> -->
						</div>

						<div class="ui segment">
							<!-- <delete project> -->
							<h2>Delete Project</h2>

							<form class="ui form" method="post" action="delete-project.php">
								<div class="field">
									<select class="ui search dropdown" name="project-id" required>
										<option value="">Project</option>
<?php

foreach ($projects as $project) {
	$id   = $project["id"];
	$name = $project["name"];

	echo "
										<option value=\"$id\">$name</option>";
}

?>
									</select>
								</div>

								<div class="field">
									<div class="ui checkbox">
										<input type="checkbox" required>
										<label>I'm completely aware that deleting the project will permanently delete all related files</label>
									</div>
								</div>

								<div class="field">
									<button type="submit" class="ui right labeled red fluid icon button">
										<i class="x icon"></i>
										Delete
									</button>
								</div>
							</form>
							<!-- </delete project> -->
							
						</div>
						<!-- </project management> -->

					</div>
				</div>
				<!-- <grid> -->

			</div>
		</div>
		<!-- </main container> -->

		<script type="text/javascript" src="../assets/libs/jquery/dist/jquery.min.js"></script>
		<script type="text/javascript" src="../assets/libs/Semantic-UI/dist/semantic.min.js"></script>
		<script type="text/javascript">
			$(function () {
				$(".dropdown").dropdown();
				$(".checkbox").checkbox();
			});
		</script>
	</body>
</html>