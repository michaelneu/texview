<?php
require_once("../../php/administration/project.php");

$title = "TeXView";

if (isset($_GET["project"])) {
	$project_directory = $_GET["project"];
	$download_template = "../download/get%s.php?project=$project_directory";

	$texview_id = sprintf("\"%s\"", $project_directory);
	$project    = new Project($project_directory);

	$title = $project->get_name() . " &middot; " . $title;
} else {
	$texview_id = "undefined";
	$download_template = "";
}


?>
<!doctype html>
<html>
	<head>
		<link rel="stylesheet" href="../assets/libs/semantic-ui/semantic.min.css" />
		<link rel="stylesheet" href="../assets/css/semantic-fixes.css" />
		<link rel="stylesheet" href="../assets/css/preview-page.css" />

		<title><?php echo $title; ?></title>
	</head>

	<body>
		<!-- <menu bar> -->
		<div class="ui gray inverted animated fixed menu">
			<div class="container">
				<a class="item" target="_blank" href="<?php printf($download_template . "&save", "pdf"); ?>"><i class="file pdf outline icon"></i> PDF</a>
				<a class="item" target="_blank" href="<?php printf($download_template, "log"); ?>"><i class="code icon"></i> LaTeX log</a>
			</div>
		</div>
		<!-- </menu bar> -->

		<!-- <menu bar> -->
		<div class="preview-container">

			<!-- <pdf preview> -->
			<div class="container" id="pdf-preview-container">
			</div>
			<!-- <pdf preview> -->

			<!-- <not found message> -->
			<div class="center-wrapper" id="not-found-message">
				<div class="vertically center-aligned muted text">
					<h1>
						<i class="history icon"></i>... wait for it
					</h1>

					It seems we're having trouble displaying your file
					<br>
					Just come back in a couple of minutes and try again
				</div>
			</div>
			<!-- </not found message> -->

		</div>

		<script type="text/javascript" src="../assets/libs/jquery/jquery.min.js"></script>

		<script type="text/javascript" src="../assets/libs/pdfjs/pdf.js"></script>

		<script type="text/javascript" src="../assets/js/texview-pdf.js"></script>
		<script type="text/javascript" src="../assets/js/texview-reader.js"></script>

		<script type="text/javascript">
			$(function () {
				var reader = new TeXViewReader(<?php echo $texview_id; ?>);

				reader.poll();
			});
		</script>
	</body>
</html>
