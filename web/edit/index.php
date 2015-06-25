<?php
require_once("../../php/administration/user.php");
require_once("../../php/administration/project.php");
require_once("../../php/security/bouncer.php");

if (!isset($_GET["project"]) or !isset($_GET["token"])) {
	header("Location: ../dash/");
}

$texview_id = $_GET["project"];
$project = new Project($texview_id);
$token = $project->get_token();
$name = $project->get_name();

if ($_GET["token"] != $token or strlen($name) == 0) {
	header("Location: ../dash/");
}

?>
<!doctype html>
<html>
	<head>
		<title><?php echo $name ?> &middot; TeXView</title>

		<link rel="stylesheet" href="../assets/libs/codemirror/lib/codemirror.css" />
		<link rel="stylesheet" href="../assets/libs/codemirror/theme/base16-light.css" />
		<link rel="stylesheet" href="../assets/libs/Semantic-UI/dist/semantic.min.css" />
		<link rel="stylesheet" href="../assets/css/semantic-fixes.css" />
		<link rel="stylesheet" href="../assets/css/editor.css" />
	</head>

	<body>
		<!-- <navbar> -->
		<div class="ui green inverted fixed menu">
			<div class="container">
				<a class="item" target="_blank" href="../read?project=<?php echo $texview_id; ?>"><i class="file pdf outline icon"></i> Preview</a>
				<a class="item" href="index.php"><i class="file archive outline icon"></i> Project</a>

				<div class="right menu">
					<a class="item" href="../dash/"><i class="align left icon"></i> Dashboard</a>
				</div>
			</div>
		</div>
		<!-- </navbar> -->

		<!-- <editor frame> -->
		<div class="editor">
			<div class="file-list">
			</div>

			<div class="codemirror">
				<textarea id="code" autofocus></textarea>
			</div>

			<!-- <toast notification> -->
			<div id="notification" class="toast">
				<div class="ui black message">
					
				</div>
			</div>
			<!-- </toast notification> -->
		</div>
		<!-- <editor frame> -->


		<script type="text/javascript" src="../assets/libs/jquery/dist/jquery.min.js"></script>
		<script type="text/javascript" src="../assets/libs/codemirror/lib/codemirror.js"></script>
		<script type="text/javascript" src="../assets/libs/codemirror/mode/stex/stex.js"></script>
		<script type="text/javascript" src="../assets/libs/codemirror/addon/edit/matchbrackets.js"></script>
		<script type="text/javascript" src="../assets/js/texview-editor.js"></script>
		<script type="text/javascript">
			$(function () {
				var editor = CodeMirror.fromTextArea(document.getElementById("code"), {
					lineNumbers: true,
					mode: "stex",
					theme: "base16-light",
					lineWrapping: true,
					matchBrackets: true,
					extraKeys: {
						"Ctrl-S": function (cm) {
							console.log("Saving automatically, just ignore this one. ");
						}
					}
				});

				var notfication = $("#notification");

				var texview = new TeXViewEditor("<?php echo $texview_id; ?>", "<?php echo $token; ?>", editor, notification);
			});
		</script>
	</body>
</html>