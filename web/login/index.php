<?php
session_start();
require_once("../../php/administration/user.php");

$username = "";
$error = false;

if (isset($_POST["username"]) and isset($_POST["password"])) {
	$username = $_POST["username"];
	$password = $_POST["password"];

	$id = User::check_credentials($username, $password);
	if ($id !== false) {
		User::login($username, $id);
	} else {
		$error = true;
	}
}


if (User::is_logged_in()) {
	header("Location: ../dash/");
}

?>
<!doctype html>
<html>
	<head>
		<link rel="stylesheet" href="../assets/libs/semantic-ui/semantic.min.css" />
		<link rel="stylesheet" href="../assets/css/semantic-fixes.css" />
	</head>

	<body>
		<!-- <vcenter> -->
		<div class="center-wrapper">
			<div class="vertically center-aligned text">

				<!-- <grid> -->
				<div class="ui centered grid">
<?php
if ($error) {
?>
					<div class="row">
						<div class="four wide column">

							<!-- <error message> -->
							<div class="ui negative message">
								<div class="header">
									Login failed
								</div>

								<p>It seems your login credentials were invalid. Please try again</p>
							</div>
							<!-- <error message> -->

						</div>
					</div>
<?php
}
?>
					<div class="row">
						<div class="four wide column">

							<!-- <form> -->
							<form method="post" class="ui form">
								<div class="field">
									<div class="ui icon input">
										<input type="text" name="username" placeholder="Username" value="<?= $username ?>" required autofocus>
										<i class="user icon"></i>
									</div>
								</div>

								<div class="field">
									<div class="ui icon input">
										<input type="password" name="password" placeholder="Password" required>
										<i class="lock icon"></i>
									</div>
								</div>

								<div class="field">
									<button type="submit" class="ui right labeled green fluid icon button">
										<i class="right arrow icon"></i>
										Login
									</button>
								</div>
							</form>
							<!-- </form> -->

						</div>
					</div>
				</div>
				<!-- </grid> -->

			</div>
		</div>
		<!-- </vcenter> -->
	</body>
</html>